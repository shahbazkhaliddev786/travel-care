<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Doctor;
use App\Models\Customer;
use App\Models\Laboratory;
use App\Models\Translator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CustomersExport;
use App\Exports\DoctorsExport;
use App\Exports\LaboratoriesExport;
use App\Exports\TranslatorsExport;

class UserManagementController extends Controller
{
    /**
     * Display a listing of customers/patients.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function customers(Request $request)
    {
        $query = User::where('role', 'customer')->with('customerProfile');
        
        // Calculate stats
        $stats = [
            'active' => User::where('role', 'customer')->where('is_active', true)->count(),
            'inactive' => User::where('role', 'customer')->where('is_active', false)->count(),
            'total' => User::where('role', 'customer')->count(),
            'new' => User::where('role', 'customer')
                ->where('created_at', '>=', now()->startOfMonth())
                ->count(),
        ];
        
        // Apply filters if provided
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }
        
        // Apply date filter if provided
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $dateFrom = $request->date_from . ' 00:00:00';
            $dateTo = $request->date_to . ' 23:59:59';
            $query->whereBetween('created_at', [$dateFrom, $dateTo]);
        }
        
        $customers = $query->orderBy('created_at', 'desc')->paginate(10);
        
        return view('admin.customers.index', compact('customers', 'stats'));
    }
    
    /**
     * Export customers to Excel.
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportCustomers()
    {
        return Excel::download(new CustomersExport, 'customers_' . date('Y-m-d_H-i-s') . '.xlsx');
    }
    
    /**
     * Display details for a specific customer.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function showCustomer($id)
    {
        $customer = User::with('customerProfile')->findOrFail($id);
        
        if ($customer->role !== 'customer') {
            return redirect()->route('admin.customers')
                ->with('error', 'The specified user is not a customer.');
        }
        
        return view('admin.customers.show', compact('customer'));
    }
    
    /**
     * Show the form for creating a new customer.
     *
     * @return \Illuminate\View\View
     */
    public function createCustomer()
    {
        return view('admin.customers.create');
    }
    
    /**
     * Store a newly created customer in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeCustomer(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone_number' => 'required|string|max:20',
            'country_code' => 'required|string|max:5',
            'password' => 'required|string|min:8|confirmed',
            'country' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'biological_sex' => 'nullable|in:male,female,other',
            'age' => 'nullable|integer|min:1|max:120',
            'weight' => 'nullable|numeric|min:0',
            'chronic_pathologies' => 'nullable|string|max:1000',
            'allergies' => 'nullable|string|max:1000',
            'chronic_medications' => 'nullable|string|max:1000',
            'medical_info' => 'nullable|string|max:2000',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'nullable|boolean',
        ]);

        try {
            // Handle profile photo upload
            $profilePhotoPath = null;
            if ($request->hasFile('profile_photo')) {
                $profilePhotoPath = $request->file('profile_photo')->store('profile-photos', 'public');
            }

            // Process array fields - convert comma-separated strings to JSON arrays
            $chronicPathologies = $validated['chronic_pathologies'] ? 
                json_encode(array_values(array_filter(array_map('trim', explode(',', $validated['chronic_pathologies'])), function($value) {
                    return !empty($value);
                }))) : null;
                
            $allergies = $validated['allergies'] ? 
                json_encode(array_values(array_filter(array_map('trim', explode(',', $validated['allergies'])), function($value) {
                    return !empty($value);
                }))) : null;
                
            $chronicMedications = $validated['chronic_medications'] ? 
                json_encode(array_values(array_filter(array_map('trim', explode(',', $validated['chronic_medications'])), function($value) {
                    return !empty($value);
                }))) : null;

            // Create the user first (only basic auth fields)
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone_number' => $validated['phone_number'],
                'country_code' => $validated['country_code'],
                'password' => bcrypt($validated['password']),
                'role' => 'customer',
                'profile_photo' => $profilePhotoPath,
                'is_active' => $validated['is_active'] ?? true,
            ]);

            // Create the customer profile
            Customer::create([
                'user_id' => $user->id,
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone_number'],
                'country' => $validated['country'],
                'city' => $validated['city'],
                'gender' => $validated['biological_sex'] ? ucfirst($validated['biological_sex']) : null,
                'age' => $validated['age'],
                'weight' => $validated['weight'],
                'chronic_pathologies' => $chronicPathologies,
                'allergies' => $allergies,
                'chronic_medications' => $chronicMedications,
                'medical_info' => $validated['medical_info'] ?? null,
                'country_code' => $validated['country_code'],
                'phone_number' => $validated['phone_number'],
                'is_verified' => true, // Admin created customers are pre-verified
            ]);

            return redirect()->route('admin.customers.index')
                ->with('success', 'Customer created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error creating customer: ' . $e->getMessage());
        }
    }
    
    /**
     * Show the form for editing the specified customer.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function editCustomer($id)
    {
        $customer = User::with('customerProfile')->findOrFail($id);
        
        if ($customer->role !== 'customer') {
            return redirect()->route('admin.customers')
                ->with('error', 'The specified user is not a customer.');
        }
        
        return view('admin.customers.edit', compact('customer'));
    }
    
    /**
     * Update the specified customer in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateCustomer(Request $request, $id)
    {
        $customer = User::with('customerProfile')->findOrFail($id);
        
        if ($customer->role !== 'customer') {
            return redirect()->route('admin.customers')
                ->with('error', 'The specified user is not a customer.');
        }

        // Handle profile creation if requested
        if ($request->has('create_profile') && !$customer->customerProfile) {
            try {
                Customer::create([
                    'user_id' => $customer->id,
                    'name' => $customer->name,
                    'email' => $customer->email,
                    'phone' => $customer->phone_number ?? '',
                    'country' => $customer->country,
                    'city' => $customer->city,
                    'gender' => $customer->biological_sex ? ucfirst($customer->biological_sex) : null,
                    'age' => $customer->age,
                    'weight' => $customer->weight,
                    'chronic_pathologies' => $customer->chronic_pathologies ?? null,
                    'allergies' => $customer->allergies,
                    'chronic_medications' => $customer->chronic_medications ?? null,
                    'country_code' => $customer->country_code,
                    'phone_number' => $customer->phone_number,
                    'is_verified' => true,
                ]);

                return redirect()->route('admin.customers.show', $customer->id)
                    ->with('success', 'Customer profile created successfully.');
            } catch (\Exception $e) {
                return redirect()->back()
                    ->with('error', 'Error creating customer profile: ' . $e->getMessage());
            }
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone_number' => 'required|string|max:20',
            'country_code' => 'required|string|max:5',
            'password' => 'nullable|string|min:8|confirmed',
            'country' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'biological_sex' => 'nullable|in:male,female,other',
            'age' => 'nullable|integer|min:1|max:120',
            'weight' => 'nullable|numeric|min:0',
            'chronic_pathologies' => 'nullable|string|max:1000',
            'allergies' => 'nullable|string|max:1000',
            'chronic_medications' => 'nullable|string|max:1000',
            'medical_info' => 'nullable|string|max:2000',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'nullable|boolean',
        ]);

        try {
            // Handle profile photo upload
            if ($request->hasFile('profile_photo')) {
                // Delete old photo if exists
                if ($customer->profile_photo) {
                    Storage::disk('public')->delete($customer->profile_photo);
                }
                $validated['profile_photo'] = $request->file('profile_photo')->store('profile-photos', 'public');
            }

            // Update password only if provided
            if (!empty($validated['password'])) {
                $validated['password'] = bcrypt($validated['password']);
            } else {
                unset($validated['password']);
            }

            // Update the user record (only basic auth fields)
            $userUpdateData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone_number' => $validated['phone_number'],
                'country_code' => $validated['country_code'],
                'is_active' => $validated['is_active'] ?? $customer->is_active,
            ];
            
            if (isset($validated['profile_photo'])) {
                $userUpdateData['profile_photo'] = $validated['profile_photo'];
            }
            
            if (isset($validated['password'])) {
                $userUpdateData['password'] = $validated['password'];
            }
            
            $customer->update($userUpdateData);

            // Find or create the customer profile
            $customerProfile = Customer::where('user_id', $customer->id)->first();
            
            // Process array fields - convert comma-separated strings to JSON arrays
            $chronicPathologies = $validated['chronic_pathologies'] ? 
                json_encode(array_values(array_filter(array_map('trim', explode(',', $validated['chronic_pathologies'])), function($value) {
                    return !empty($value);
                }))) : null;
                
            $allergies = $validated['allergies'] ? 
                json_encode(array_values(array_filter(array_map('trim', explode(',', $validated['allergies'])), function($value) {
                    return !empty($value);
                }))) : null;
                
            $chronicMedications = $validated['chronic_medications'] ? 
                json_encode(array_values(array_filter(array_map('trim', explode(',', $validated['chronic_medications'])), function($value) {
                    return !empty($value);
                }))) : null;
            
            $profileData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone_number'],
                'country' => $validated['country'],
                'city' => $validated['city'],
                'gender' => $validated['biological_sex'] ? ucfirst($validated['biological_sex']) : null,
                'age' => $validated['age'],
                'weight' => $validated['weight'],
                'chronic_pathologies' => $chronicPathologies,
                'allergies' => $allergies,
                'chronic_medications' => $chronicMedications,
                'medical_info' => $validated['medical_info'] ?? null,
                'country_code' => $validated['country_code'],
                'phone_number' => $validated['phone_number'],
            ];

            if ($customerProfile) {
                // Update existing customer profile
                $customerProfile->update($profileData);
            } else {
                // Create new customer profile if it doesn't exist
                Customer::create([
                    'user_id' => $customer->id,
                    ...$profileData,
                    'is_verified' => true,
                ]);
            }

            return redirect()->route('admin.customers.index')
                ->with('success', 'Customer updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error updating customer: ' . $e->getMessage());
        }
    }
    
    /**
     * Remove the specified customer from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroyCustomer($id)
    {
        try {
            $customer = User::findOrFail($id);
            
            if ($customer->role !== 'customer') {
                return redirect()->route('admin.customers')
                    ->with('error', 'The specified user is not a customer.');
            }

            // Delete profile photo if exists
            if ($customer->profile_photo) {
                Storage::disk('public')->delete($customer->profile_photo);
            }

            // Delete customer profile first (if exists)
            $customerProfile = Customer::where('user_id', $customer->id)->first();
            if ($customerProfile) {
                $customerProfile->delete();
            }

            // Delete the user (this will cascade delete the customer profile due to foreign key constraint)
            $customer->delete();

            return redirect()->route('admin.customers.index')
                ->with('success', 'Customer deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('admin.customers.index')
                ->with('error', 'Error deleting customer: ' . $e->getMessage());
        }
    }
    
    /**
     * Toggle customer status (active/inactive).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleCustomerStatus(Request $request, $id)
    {
        try {
            $customer = User::findOrFail($id);
            
            if ($customer->role !== 'customer') {
                return response()->json([
                    'success' => false,
                    'message' => 'The specified user is not a customer.'
                ], 400);
            }

            // Get the status from request data
            $isActive = $request->input('is_active', !$customer->is_active);
            $customer->is_active = $isActive;
            $customer->save();

            return response()->json([
                'success' => true,
                'message' => 'Customer status updated successfully.',
                'is_active' => $customer->is_active
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating customer status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle bulk actions for customers.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function bulkCustomerAction(Request $request)
    {
        $validated = $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:users,id'
        ]);

        try {
            $customerIds = $validated['ids'];
            $action = $validated['action'];
            
            // Verify all IDs are customers
            $customers = User::whereIn('id', $customerIds)
                ->where('role', 'customer')
                ->get();
                
            if ($customers->count() !== count($customerIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Some selected users are not customers.'
                ], 400);
            }

            $count = 0;
            switch ($action) {
                case 'activate':
                    $count = User::whereIn('id', $customerIds)
                        ->where('role', 'customer')
                        ->update(['is_active' => true]);
                    $message = "$count customers activated successfully.";
                    break;
                    
                case 'deactivate':
                    $count = User::whereIn('id', $customerIds)
                        ->where('role', 'customer')
                        ->update(['is_active' => false]);
                    $message = "$count customers deactivated successfully.";
                    break;
                    
                case 'delete':
                    // Delete profile photos first
                    $customersToDelete = User::whereIn('id', $customerIds)
                        ->where('role', 'customer')
                        ->get();
                        
                    foreach ($customersToDelete as $customer) {
                        if ($customer->profile_photo) {
                            Storage::disk('public')->delete($customer->profile_photo);
                        }
                    }
                    
                    $count = User::whereIn('id', $customerIds)
                        ->where('role', 'customer')
                        ->delete();
                    $message = "$count customers deleted successfully.";
                    break;
            }

            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error performing bulk action: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Handle bulk actions for doctors.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function bulkDoctorAction(Request $request)
    {
        $validated = $request->validate([
            'action' => 'required|in:enable_video,disable_video,delete',
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:doctors,id'
        ]);

        try {
            $doctorIds = $validated['ids'];
            $action = $validated['action'];
            
            $count = 0;
            switch ($action) {
                case 'enable_video':
                    $count = Doctor::whereIn('id', $doctorIds)
                        ->update(['can_video_consult' => true]);
                    $message = "$count doctors enabled for video consultation.";
                    break;
                    
                case 'disable_video':
                    $count = Doctor::whereIn('id', $doctorIds)
                        ->update(['can_video_consult' => false]);
                    $message = "$count doctors disabled for video consultation.";
                    break;
                    
                case 'delete':
                    // Delete profile images first
                    $doctorsToDelete = Doctor::whereIn('id', $doctorIds)->get();
                        
                    foreach ($doctorsToDelete as $doctor) {
                        if ($doctor->profile_image) {
                            Storage::disk('public')->delete($doctor->profile_image);
                        }
                        if ($doctor->license_scan) {
                            Storage::disk('public')->delete($doctor->license_scan);
                        }
                    }
                    
                    $count = Doctor::whereIn('id', $doctorIds)->delete();
                    $message = "$count doctors deleted successfully.";
                    break;
            }

            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error performing bulk action: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Handle bulk actions for laboratories.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function bulkLaboratoryAction(Request $request)
    {
        $validated = $request->validate([
            'action' => 'required|in:enable_video,disable_video,delete',
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:laboratories,id'
        ]);

        try {
            $laboratoryIds = $validated['ids'];
            $action = $validated['action'];
            
            $count = 0;
            switch ($action) {
                case 'enable_video':
                    // Only verified laboratories can be enabled for video consultation
                    $count = Laboratory::whereIn('id', $laboratoryIds)
                        ->where('is_verified', true)
                        ->update(['can_video_consult' => true]);
                    $message = "$count laboratories enabled for video consultation.";
                    break;
                    
                case 'disable_video':
                    $count = Laboratory::whereIn('id', $laboratoryIds)
                        ->update(['can_video_consult' => false]);
                    $message = "$count laboratories disabled for video consultation.";
                    break;
                    
                case 'delete':
                    $labsToDelete = Laboratory::with('user')->whereIn('id', $laboratoryIds)->get();
                    
                    foreach ($labsToDelete as $laboratory) {
                        if ($laboratory->profile_image) {
                            Storage::disk('public')->delete($laboratory->profile_image);
                        }
                        if ($laboratory->gallery_images) {
                            foreach ($laboratory->gallery_images as $image) {
                                Storage::disk('public')->delete($image);
                            }
                        }
                        if ($laboratory->user) {
                            $laboratory->user->delete();
                        }
                        // Delete the laboratory record
                        $laboratory->delete();
                    }
                    
                    $count = count($labsToDelete);
                    $message = "$count laboratories deleted successfully.";
                    break;
            }

            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error performing bulk action: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Display a listing of doctors.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function doctors(Request $request)
    {
        $query = Doctor::with('user');
        
        // Calculate stats for filter tabs
        $stats = [
            'total' => Doctor::count(),
            'approved' => Doctor::where('is_verified', true)->count(),
            'pending' => Doctor::where('is_verified', false)->whereNull('rejection_reason')->count(),
            'rejected' => Doctor::where('is_verified', false)->whereNotNull('rejection_reason')->count(),
            'video_enabled' => Doctor::where('can_video_consult', true)->count(),
        ];
        
        // Apply filters if provided
        if ($request->filled('status')) {
            if ($request->status === 'pending') {
                $query->where('is_verified', false)
                      ->whereNull('rejection_reason');
            } elseif ($request->status === 'approved') {
                $query->where('is_verified', true);
            } elseif ($request->status === 'rejected') {
                $query->where('is_verified', false)
                      ->whereNotNull('rejection_reason');
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('specialization', 'like', "%{$search}%");
            });
        }

        // Apply video consultation filter
        if ($request->filled('video_consult')) {
            if ($request->video_consult === 'yes') {
                $query->where('can_video_consult', true);
            } elseif ($request->video_consult === 'no') {
                $query->where('can_video_consult', false);
            }
        }

        // Apply date filters
        if ($request->filled('date_from')) {
            $dateFrom = $request->date_from . ' 00:00:00';
            $query->where('created_at', '>=', $dateFrom);
        }
        
        if ($request->filled('date_to')) {
            $dateTo = $request->date_to . ' 23:59:59';
            $query->where('created_at', '<=', $dateTo);
        }
        
        $doctors = $query->orderBy('created_at', 'desc')->paginate(15);
        
        // Handle AJAX requests for status updates
        if ($request->ajax() || $request->has('ajax')) {
            return response()->json(['stats' => $stats]);
        }
        
        return view('admin.doctors.index', compact('doctors', 'stats'));
    }
    
    /**
     * Export doctors to Excel.
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportDoctors()
    {
        return Excel::download(new DoctorsExport, 'doctors_' . date('Y-m-d_H-i-s') . '.xlsx');
    }
    
    /**
     * Display details for a specific doctor.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function showDoctor($id)
    {
        $doctor = Doctor::with(['user', 'services'])->findOrFail($id);
        
        return view('admin.doctors.show', compact('doctor'));
    }
    
    /**
     * Show form to create a new doctor
     *
     * @return \Illuminate\View\View
     */
    public function createDoctor()
    {
        return view('admin.doctors.create');
    }
    
    /**
     * Store a new doctor
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeDoctor(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|unique:doctors,email',
            'phone' => 'required|string|max:20',
            'country_code' => 'required|string|max:5',
            'password' => 'required|string|min:8|confirmed',
            'professional_id' => 'required|string|max:50',
            'license_scan' => 'nullable|string|max:100',
            'address' => 'required|string',
            'city' => 'required|string|max:100',
            'state' => 'nullable|string|max:100',
            'specialization' => 'nullable|string',
            'description' => 'nullable|string',
            'consultation_fee' => 'nullable|numeric|min:0',
            'messaging_fee' => 'nullable|numeric|min:0',
            'video_call_fee' => 'nullable|numeric|min:0',
            'house_visit_fee' => 'nullable|numeric|min:0',
            'voice_call_fee' => 'nullable|numeric|min:0',
            'working_hours_from' => 'nullable|string',
            'working_hours_to' => 'nullable|string',
            'working_days' => 'nullable|array',
            'working_days.*' => 'string|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'years_of_experience' => 'nullable|integer|min:0',
            'working_location' => 'nullable|string|max:255',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            // Create the user account first
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone_number' => $validated['phone'],
                'country_code' => $validated['country_code'],
                'password' => bcrypt($validated['password']),
                'role' => 'doctor',
                'is_active' => true, // Admin-created accounts are active by default
            ]);

            // Handle license number
            $licenseNumber = $validated['license_scan'];

            // Handle profile image upload
            $profileImagePath = null;
            if ($request->hasFile('profile_image')) {
                $profileImagePath = $request->file('profile_image')->store('profile-images', 'public');
            }

            // Create the doctor profile
            $doctor = Doctor::create([
                'user_id' => $user->id,
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'country_code' => $validated['country_code'],
                'professional_id' => $validated['professional_id'],
                'license_scan' => $licenseNumber,
                'address' => $validated['address'],
                'city' => $validated['city'],
                'state' => $validated['state'] ?? null,
                'specialization' => $validated['specialization'],
                'description' => $validated['description'],
                'consultation_fee' => $validated['consultation_fee'],
                'messaging_fee' => $validated['messaging_fee'],
                'video_call_fee' => $validated['video_call_fee'],
                'house_visit_fee' => $validated['house_visit_fee'],
                'voice_call_fee' => $validated['voice_call_fee'],
                'working_hours_from' => $validated['working_hours_from'] ?? '08:00:00',
                'working_hours_to' => $validated['working_hours_to'] ?? '17:00:00',
                'working_days' => $validated['working_days'] ?? ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
                'years_of_experience' => $validated['years_of_experience'],
                'working_location' => $validated['working_location'],
                'type' => 'Doctor',
                'profile_image' => $profileImagePath,
                'is_verified' => true, // Admin-created doctors are automatically verified
                'can_video_consult' => false, // Can be enabled later
            ]);

            return redirect()->route('admin.doctors.index')
                ->with('success', 'Doctor created successfully!');

        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => 'Failed to create doctor: ' . $e->getMessage()
            ])->withInput();
        }
    }
    
    /**
     * Update doctor verification status.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateDoctorStatus(Request $request, $id)
    {
        $doctor = Doctor::with('user')->findOrFail($id);
        
        $validated = $request->validate([
            'status' => 'required|in:approve,reject',
            'rejection_reason' => 'nullable|string|max:500',
        ]);
        
        if ($validated['status'] === 'approve') {
            $doctor->is_verified = true;
            $doctor->rejection_reason = null;
            
            // Activate the user as well
            if ($doctor->user) {
                $doctor->user->is_active = true;
                $doctor->user->save();
            }
            
            $message = 'Doctor approved successfully';
        } else {
            $doctor->is_verified = false;
            $doctor->can_video_consult = false;
            $doctor->rejection_reason = $validated['rejection_reason'] ?? 'Rejected by admin';
            
            // Deactivate the user as well
            if ($doctor->user) {
                $doctor->user->is_active = false;
                $doctor->user->save();
            }
            
            $message = 'Doctor rejected successfully';
        }
        
        $doctor->save();
        
        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }
    
    /**
     * Toggle video consultation access for a doctor.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggleVideoConsultation(Request $request, $id)
    {
        $doctor = Doctor::findOrFail($id);
        
        // Only verified doctors can have video consultation access
        if (!$doctor->is_verified) {
            return response()->json([
                'success' => false, 
                'message' => 'Doctor must be verified before enabling video consultation.'
            ]);
        }
        
        $validated = $request->validate([
            'enabled' => 'required|boolean',
        ]);
        
        $doctor->can_video_consult = $validated['enabled'];
        $doctor->save();
        
        return response()->json(['success' => true]);
    }
    
    /**
     * Show the form for editing the specified doctor.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function editDoctor($id)
    {
        try {
            $doctor = Doctor::with('user')->findOrFail($id);
            return view('admin.doctors.edit', compact('doctor'));
        } catch (\Exception $e) {
            return redirect()->route('admin.doctors.index')
                ->with('error', 'Doctor not found.');
        }
    }
    
    /**
     * Update the specified doctor in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateDoctor(Request $request, $id)
    {
        $doctor = Doctor::with('user')->findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $doctor->user_id . '|unique:doctors,email,' . $id,
            'phone' => 'required|string|max:20',
            'country_code' => 'required|string|max:5',
            'password' => 'nullable|string|min:8|confirmed',
            'professional_id' => 'required|string|max:50',
            'license_scan' => 'nullable|string|max:100',
            'address' => 'required|string',
            'city' => 'required|string|max:100',
            'specialization' => 'nullable|string',
            'description' => 'nullable|string',
            'consultation_fee' => 'nullable|numeric|min:0',
            'messaging_fee' => 'nullable|numeric|min:0',
            'video_call_fee' => 'nullable|numeric|min:0',
            'house_visit_fee' => 'nullable|numeric|min:0',
            'voice_call_fee' => 'nullable|numeric|min:0',
            'working_hours_from' => 'nullable|string',
            'working_hours_to' => 'nullable|string',
            'working_days' => 'nullable|array',
            'working_days.*' => 'string|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'years_of_experience' => 'nullable|integer|min:0',
            'working_location' => 'nullable|string|max:255',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'delete_profile_image' => 'nullable|string',
            'is_verified' => 'nullable|boolean',
            'can_video_consult' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        try {
            // Update the user account
            $userUpdates = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone_number' => $validated['phone'],
                'country_code' => $validated['country_code'],
                'is_active' => $validated['is_active'] ?? $doctor->user->is_active,
            ];

            // Only update password if provided
            if (!empty($validated['password'])) {
                $userUpdates['password'] = bcrypt($validated['password']);
            }

            $doctor->user->update($userUpdates);

            // Handle license number
            $licenseNumber = $validated['license_scan'];

            // Handle profile image upload/deletion
            $profileImagePath = $doctor->profile_image;
            if ($request->input('delete_profile_image') == '1') {
                // Delete existing profile image
                if ($profileImagePath && \Storage::disk('public')->exists($profileImagePath)) {
                    \Storage::disk('public')->delete($profileImagePath);
                }
                $profileImagePath = null;
            } elseif ($request->hasFile('profile_image')) {
                // Delete old profile image if exists
                if ($profileImagePath && \Storage::disk('public')->exists($profileImagePath)) {
                    \Storage::disk('public')->delete($profileImagePath);
                }
                // Upload new profile image
                $profileImagePath = $request->file('profile_image')->store('profile-images', 'public');
            }

            // Update the doctor profile
            $doctor->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'country_code' => $validated['country_code'],
                'professional_id' => $validated['professional_id'],
                'license_scan' => $licenseNumber,
                'address' => $validated['address'],
                'city' => $validated['city'],
                'specialization' => $validated['specialization'],
                'description' => $validated['description'],
                'consultation_fee' => $validated['consultation_fee'],
                'messaging_fee' => $validated['messaging_fee'],
                'video_call_fee' => $validated['video_call_fee'],
                'house_visit_fee' => $validated['house_visit_fee'],
                'voice_call_fee' => $validated['voice_call_fee'],
                'working_hours_from' => $validated['working_hours_from'] ?? $doctor->working_hours_from,
                'working_hours_to' => $validated['working_hours_to'] ?? $doctor->working_hours_to,
                'working_days' => $validated['working_days'] ?? $doctor->working_days,
                'years_of_experience' => $validated['years_of_experience'],
                'working_location' => $validated['working_location'],
                'profile_image' => $profileImagePath,
                'is_verified' => $validated['is_verified'] ?? $doctor->is_verified,
                'can_video_consult' => ($validated['can_video_consult'] ?? false) && ($validated['is_verified'] ?? $doctor->is_verified), // Only allow video consult if verified
            ]);

            return redirect()->route('admin.doctors.index')
                ->with('success', 'Doctor updated successfully!');

        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => 'Failed to update doctor: ' . $e->getMessage()
            ])->withInput();
        }
    }
    
    /**
     * Remove the specified doctor from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroyDoctor($id)
    {
        try {
            $doctor = Doctor::with('user')->findOrFail($id);
            

            
            // Delete profile image if exists
            if ($doctor->profile_image && \Storage::disk('public')->exists($doctor->profile_image)) {
                \Storage::disk('public')->delete($doctor->profile_image);
            }
            
            // Delete gallery images if exists
            if ($doctor->gallery_images) {
                foreach ($doctor->gallery_images as $image) {
                    if (\Storage::disk('public')->exists($image)) {
                        \Storage::disk('public')->delete($image);
                    }
                }
            }
            
            // Delete the user account (this will cascade delete the doctor due to foreign key)
            if ($doctor->user) {
                $doctor->user->delete();
            }
            
            // Delete the doctor record if it still exists
            $doctor->delete();
            
            return redirect()->route('admin.doctors.index')
                ->with('success', 'Doctor deleted successfully!');
                
        } catch (\Exception $e) {
            return redirect()->route('admin.doctors.index')
                ->with('error', 'Failed to delete doctor: ' . $e->getMessage());
        }
    }
    
    /**
     * Display a listing of laboratories.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function laboratories(Request $request)
    {
        $query = Laboratory::with('user');
        
        // Calculate stats for filter tabs
        $stats = [
            'total' => Laboratory::count(),
            'approved' => Laboratory::where('is_verified', true)->count(),
            'pending' => Laboratory::where('is_verified', false)->whereNull('rejection_reason')->count(),
            'rejected' => Laboratory::where('is_verified', false)->whereNotNull('rejection_reason')->count(),
            'video_enabled' => Laboratory::where('can_video_consult', true)->count(),
        ];
        
        // Handle AJAX requests for status updates
        if ($request->ajax() || $request->has('ajax')) {
            return response()->json(['stats' => $stats]);
        }
        
        // Apply filters if provided
        if ($request->filled('status')) {
            if ($request->status === 'pending') {
                $query->where('is_verified', false)
                      ->whereNull('rejection_reason');
            } elseif ($request->status === 'approved') {
                $query->where('is_verified', true);
            } elseif ($request->status === 'rejected') {
                $query->where('is_verified', false)
                      ->whereNotNull('rejection_reason');
            }
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }
        
        // Apply video consultation filter
        if ($request->filled('video_consult')) {
            if ($request->video_consult === '1') {
                $query->where('can_video_consult', true);
            } elseif ($request->video_consult === '0') {
                $query->where('can_video_consult', false);
            }
        }
        
        // Apply date filters
        if ($request->filled('date_from')) {
            $dateFrom = $request->date_from . ' 00:00:00';
            $query->where('created_at', '>=', $dateFrom);
        }
        
        if ($request->filled('date_to')) {
            $dateTo = $request->date_to . ' 23:59:59';
            $query->where('created_at', '<=', $dateTo);
        }
        
        $laboratories = $query->orderBy('created_at', 'desc')->paginate(15);
        
        return view('admin.laboratories.index', compact('laboratories', 'stats'));
    }
    
    /**
     * Display details for a specific laboratory.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function showLaboratory($id)
    {
        $laboratory = Laboratory::with(['user', 'services', 'paymentMethods', 'galleryImages', 'reviews'])->findOrFail($id);
        
        return view('admin.laboratories.show', compact('laboratory'));
    }
    
    /**
     * Show form to create a new laboratory
     *
     * @return \Illuminate\View\View
     */
    public function createLaboratory()
    {
        return view('admin.laboratories.create');
    }
    
    /**
     * Store a new laboratory
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeLaboratory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|unique:laboratories,email',
            'phone' => 'required|string|max:20',
            'country_code' => 'required|string|max:5',
            'password' => 'required|string|min:8|confirmed',
            'professional_mexican_id' => 'required|string|max:50',
            'mexican_voting_license_scan' => 'nullable|string|max:100',
            'address' => 'required|string',
            'city' => 'required|string|max:100',
            'state' => 'nullable|string|max:100',
            'operating_hours_from' => 'nullable|string',
            'operating_hours_to' => 'nullable|string',
            'operating_days' => 'nullable|array',
            'operating_days.*' => 'string|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            // Create the user account first
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone_number' => $validated['phone'],
                'country_code' => $validated['country_code'],
                'password' => bcrypt($validated['password']),
                'role' => 'laboratory',
                'is_active' => true, // Admin-created accounts are active by default
            ]);

            // Handle profile image upload
            $profileImagePath = null;
            if ($request->hasFile('profile_image')) {
                $profileImagePath = $request->file('profile_image')->store('profile-images', 'public');
            }

            // Create the laboratory profile
            Laboratory::create([
                'user_id' => $user->id,
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'country_code' => $validated['country_code'],
                'license_number' => $validated['professional_mexican_id'], // Map professional_mexican_id to license_number
                'license_scan' => $validated['mexican_voting_license_scan'], // Map mexican_voting_license_scan to license_scan
                'address' => $validated['address'],
                'city' => $validated['city'],
                'state' => $validated['state'] ?? null,
                'specialization' => null, // Not in form, set to null
                'bio' => null, // Not in form, set to null
                'consultation_fee' => null, // Not in form, set to null
                'messaging_fee' => null, // Not in form, set to null
                'video_call_fee' => null, // Not in form, set to null
                'house_visit_fee' => null, // Not in form, set to null
                'voice_call_fee' => null, // Not in form, set to null
                'working_hours_from' => $validated['operating_hours_from'] ?? null, // Map operating_hours_from to working_hours_from
                'working_hours_to' => $validated['operating_hours_to'] ?? null, // Map operating_hours_to to working_hours_to
                'working_days' => $validated['operating_days'] ?? null, // Map operating_days to working_days
                'years_of_experience' => null, // Not in form, set to null
                'working_location' => null, // Not in form, set to null
                'profile_image' => $profileImagePath,
                'is_verified' => true, // Admin-created laboratories are approved by default
                'verification_date' => now(), // Set verification date to current timestamp
                'can_video_consult' => false, // Disabled by default
            ]);

            return redirect()->route('admin.laboratories.index')
                ->with('success', 'Laboratory created successfully!');

        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => 'Failed to create laboratory: ' . $e->getMessage()
            ])->withInput();
        }
    }
    
    /**
     * Show the form for editing the specified laboratory.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function editLaboratory($id)
    {
        try {
            $laboratory = Laboratory::with('user')->findOrFail($id);
            return view('admin.laboratories.edit', compact('laboratory'));
        } catch (\Exception $e) {
            return redirect()->route('admin.laboratories')
                ->with('error', 'Laboratory not found.');
        }
    }
    
    /**
     * Update the specified laboratory in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateLaboratory(Request $request, $id)
    {
        $laboratory = Laboratory::with('user')->findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $laboratory->user_id . '|unique:laboratories,email,' . $id,
            'phone' => 'required|string|max:20',
            'country_code' => 'required|string|max:5',
            'password' => 'nullable|string|min:8|confirmed',
            'license_number' => 'required|string|max:50',
            'license_scan' => 'nullable|string|max:100',
            'address' => 'required|string',
            'city' => 'required|string|max:100',
            'specialization' => 'nullable|string',
            'bio' => 'nullable|string',
            'consultation_fee' => 'nullable|numeric|min:0',
            'messaging_fee' => 'nullable|numeric|min:0',
            'video_call_fee' => 'nullable|numeric|min:0',
            'house_visit_fee' => 'nullable|numeric|min:0',
            'voice_call_fee' => 'nullable|numeric|min:0',
            'working_hours_from' => 'nullable|string',
            'working_hours_to' => 'nullable|string',
            'working_days' => 'nullable|array',
            'working_days.*' => 'string|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'years_of_experience' => 'nullable|integer|min:0',
            'working_location' => 'nullable|string|max:255',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'delete_profile_image' => 'nullable|string',
            'is_verified' => 'nullable|boolean',
            'can_video_consult' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        try {
            // Update the user account
            $userUpdates = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone_number' => $validated['phone'],
                'country_code' => $validated['country_code'],
                'is_active' => $validated['is_active'] ?? $laboratory->user->is_active,
            ];

            // Only update password if provided
            if (!empty($validated['password'])) {
                $userUpdates['password'] = bcrypt($validated['password']);
            }

            $laboratory->user->update($userUpdates);

            // Handle profile image upload/deletion
            $profileImagePath = $laboratory->profile_image;
            if ($request->input('delete_profile_image') == '1') {
                // Delete existing profile image
                if ($profileImagePath && \Storage::disk('public')->exists($profileImagePath)) {
                    \Storage::disk('public')->delete($profileImagePath);
                }
                $profileImagePath = null;
            } elseif ($request->hasFile('profile_image')) {
                // Delete old profile image if exists
                if ($profileImagePath && \Storage::disk('public')->exists($profileImagePath)) {
                    \Storage::disk('public')->delete($profileImagePath);
                }
                // Upload new profile image
                $profileImagePath = $request->file('profile_image')->store('profile-images', 'public');
            }

            // Update the laboratory profile
            $laboratory->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'country_code' => $validated['country_code'],
                'license_number' => $validated['license_number'],
                'license_scan' => $validated['license_scan'],
                'address' => $validated['address'],
                'city' => $validated['city'],
                'specialization' => $validated['specialization'] ?? $laboratory->specialization,
                'bio' => $validated['bio'] ?? $laboratory->bio,
                'consultation_fee' => $validated['consultation_fee'] ?? $laboratory->consultation_fee,
                'messaging_fee' => $validated['messaging_fee'] ?? $laboratory->messaging_fee,
                'video_call_fee' => $validated['video_call_fee'] ?? $laboratory->video_call_fee,
                'house_visit_fee' => $validated['house_visit_fee'] ?? $laboratory->house_visit_fee,
                'voice_call_fee' => $validated['voice_call_fee'] ?? $laboratory->voice_call_fee,
                'working_hours_from' => $validated['working_hours_from'] ?? $laboratory->working_hours_from,
                'working_hours_to' => $validated['working_hours_to'] ?? $laboratory->working_hours_to,
                'working_days' => $validated['working_days'] ?? $laboratory->working_days,
                'years_of_experience' => $validated['years_of_experience'] ?? $laboratory->years_of_experience,
                'working_location' => $validated['working_location'] ?? $laboratory->working_location,
                'profile_image' => $profileImagePath,
                'is_verified' => $validated['is_verified'] ?? $laboratory->is_verified,
                'can_video_consult' => ($validated['can_video_consult'] ?? false) && ($validated['is_verified'] ?? $laboratory->is_verified),
            ]);

            return redirect()->route('admin.laboratories.index')
                ->with('success', 'Laboratory updated successfully!');

        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => 'Failed to update laboratory: ' . $e->getMessage()
            ])->withInput();
        }
    }
    
    /**
     * Remove the specified laboratory from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroyLaboratory($id)
    {
        try {
            $laboratory = Laboratory::with('user')->findOrFail($id);
            
            // Delete profile image if exists
            if ($laboratory->profile_image && \Storage::disk('public')->exists($laboratory->profile_image)) {
                \Storage::disk('public')->delete($laboratory->profile_image);
            }
            
            // Delete gallery images if exists
            if ($laboratory->gallery_images) {
                foreach ($laboratory->gallery_images as $image) {
                    if (\Storage::disk('public')->exists($image)) {
                        \Storage::disk('public')->delete($image);
                    }
                }
            }
            
            // Delete the user account (this will cascade delete the laboratory due to foreign key)
            if ($laboratory->user) {
                $laboratory->user->delete();
            }
            
            // Delete the laboratory record if it still exists
            $laboratory->delete();
            
            return redirect()->route('admin.laboratories.index')
                ->with('success', 'Laboratory deleted successfully!');
                
        } catch (\Exception $e) {
            return redirect()->route('admin.laboratories.index')
                ->with('error', 'Failed to delete laboratory: ' . $e->getMessage());
        }
    }
    
    /**
     * Update laboratory status (verification)
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateLaboratoryStatus(Request $request, $id)
    {
        $laboratory = Laboratory::with('user')->findOrFail($id);

        if ($request->has('status')) {
            $validated = $request->validate([
                'status' => 'required|in:approve,reject',
                'rejection_reason' => 'nullable|string|max:500',
            ]);

            if ($validated['status'] === 'approve') {
                $laboratory->is_verified = true;
                $laboratory->rejection_reason = null;
                $laboratory->verification_date = now();

                if ($laboratory->user) {
                    $laboratory->user->is_active = true;
                    $laboratory->user->save();
                }

                $message = 'Laboratory approved successfully';
            } else {
                $laboratory->is_verified = false;
                $laboratory->can_video_consult = false;
                $laboratory->rejection_reason = $validated['rejection_reason'] ?? 'Rejected by admin';
                $laboratory->verification_date = null;

                if ($laboratory->user) {
                    $laboratory->user->is_active = false;
                    $laboratory->user->save();
                }

                $message = 'Laboratory rejected successfully';
            }
        } else {
            $validated = $request->validate([
                'is_verified' => 'required|boolean',
                'rejection_reason' => 'nullable|string|max:500',
            ]);

            $laboratory->is_verified = $validated['is_verified'];
            $laboratory->rejection_reason = $validated['is_verified'] ? null : ($validated['rejection_reason'] ?? 'Rejected by admin');
            $laboratory->verification_date = $validated['is_verified'] ? now() : null;

            if (!$validated['is_verified']) {
                $laboratory->can_video_consult = false;
            }

            if ($laboratory->user) {
                $laboratory->user->is_active = $laboratory->is_verified;
                $laboratory->user->save();
            }

            $message = $laboratory->is_verified ? 'Laboratory approved successfully' : 'Laboratory rejected successfully';
        }

        $laboratory->save();

        return response()->json([
            'success' => true,
            'message' => $message,
        ]);
    }
    
    /**
     * Toggle video consultation for laboratory
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleLaboratoryVideo(Request $request, $id)
    {
        $laboratory = Laboratory::findOrFail($id);
        
        // Only verified laboratories can have video consultation access
        if (!$laboratory->is_verified) {
            return response()->json([
                'success' => false, 
                'message' => 'Laboratory must be verified before enabling video consultation.'
            ]);
        }
        
        $validated = $request->validate([
            'enabled' => 'required|boolean',
        ]);
        
        $laboratory->can_video_consult = $validated['enabled'];
        $laboratory->save();
        
        return response()->json(['success' => true]);
    }
    
    /**
     * Display a listing of translators.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function translators(Request $request)
    {
        $query = Translator::with('user');
        
        // Calculate stats for filter tabs
        $stats = [
            'total' => Translator::count(),
            'approved' => Translator::where('is_verified', true)->count(),
            'pending' => Translator::where('is_verified', false)->whereNull('rejection_reason')->count(),
            'rejected' => Translator::where('is_verified', false)->whereNotNull('rejection_reason')->count(),
            'available' => Translator::where('is_available', true)->count(),
        ];
        
        // Apply filters if provided
        if ($request->filled('status')) {
            if ($request->status === 'pending') {
                $query->where('is_verified', false)
                      ->whereNull('rejection_reason');
            } elseif ($request->status === 'approved') {
                $query->where('is_verified', true);
            } elseif ($request->status === 'rejected') {
                $query->where('is_verified', false)
                      ->whereNotNull('rejection_reason');
            }
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }
        
        // Apply availability filter
        if ($request->filled('availability')) {
            if ($request->availability === '1') {
                $query->where('is_available', true);
            } elseif ($request->availability === '0') {
                $query->where('is_available', false);
            }
        }
        
        // Apply date filters
        if ($request->filled('date_from')) {
            $dateFrom = $request->date_from . ' 00:00:00';
            $query->where('created_at', '>=', $dateFrom);
        }
        
        if ($request->filled('date_to')) {
            $dateTo = $request->date_to . ' 23:59:59';
            $query->where('created_at', '<=', $dateTo);
        }
        
        $translators = $query->orderBy('created_at', 'desc')->paginate(15);
        
        return view('admin.translators.index', compact('translators', 'stats'));
    }
    
    /**
     * Display details for a specific translator.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function showTranslator($id)
    {
        $translator = Translator::with(['user', 'paymentMethods', 'reviews.user'])->findOrFail($id);
        
        return view('admin.translators.show', compact('translator'));
    }
    
    /**
     * Show form to create a new translator
     *
     * @return \Illuminate\View\View
     */
    public function createTranslator()
    {
        return view('admin.translators.create');
    }
    
    /**
     * Store a new translator
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeTranslator(Request $request)
    {
        // Convert comma-separated languages string to array
        if ($request->has('languages') && is_string($request->languages)) {
            $languagesArray = array_map('trim', explode(',', $request->languages));
            $languagesArray = array_filter($languagesArray); // Remove empty values
            $request->merge(['languages' => $languagesArray]);
        }
        
        // Convert comma-separated availability_hours string to array for availability field
        if ($request->has('availability_hours') && is_string($request->availability_hours)) {
            $availabilityArray = array_map('trim', explode(',', $request->availability_hours));
            $availabilityArray = array_filter($availabilityArray); // Remove empty values
            $request->merge(['availability' => $availabilityArray]);
        }

        // Map singular specialization input to the expected specializations array
        if ($request->has('specialization') && ! $request->has('specializations') && is_string($request->specialization)) {
            $specs = array_map('trim', explode(',', $request->specialization));
            $specs = array_filter($specs);
            $request->merge(['specializations' => array_values($specs)]);
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|unique:translators,email',
            'phone' => 'required|string|max:20',
            'country_code' => 'required|string|max:5',
            'password' => 'required|string|min:8|confirmed',
            // Location Information
            'country' => 'nullable|string|max:100',
            'city' => 'required|string|max:100',
            'address' => 'nullable|string',
            // Other fields
            'languages' => 'required|array|min:1',
            'languages.*' => 'string|max:100',
            'specializations' => 'nullable|array',
            'specializations.*' => 'string|max:100',
            'bio' => 'nullable|string',
            'hourly_rate' => 'nullable|numeric|min:0',
            'experience_years' => 'nullable|integer|min:0',

            'availability_hours' => 'nullable|string',
            'availability' => 'nullable|array',
            'availability.*' => 'string|max:100',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_available' => 'nullable|boolean',
        ]);

        try {
            // Create the user account first
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone_number' => $validated['phone'],
                'country_code' => $validated['country_code'],
                'password' => bcrypt($validated['password']),
                'role' => 'translator',
                'is_active' => true, // Admin-created accounts are active by default
            ]);

            // Handle profile image upload
            $profileImagePath = null;
            if ($request->hasFile('profile_image')) {
                $profileImagePath = $request->file('profile_image')->store('profile-images', 'public');
            }

            // Create the translator profile
            Translator::create([
                'user_id' => $user->id,
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'country_code' => $validated['country_code'],
                // Location Information
                'country' => $validated['country'] ?? null,
                'city' => $validated['city'] ?? null,
                'address' => $validated['address'] ?? null,
                // Other fields
                'languages' => $validated['languages'],
                'specializations' => $validated['specializations'] ?? [],
                'bio' => $validated['bio'],
                'hourly_rate' => $validated['hourly_rate'],
                'availability' => $validated['availability'] ?? [],
                'experience_years' => $validated['experience_years'] ?? null,

                'profile_image' => $profileImagePath,
                'is_verified' => true, // Approved by default for admin-created translators
                'verification_date' => now(),
                'is_available' => $request->boolean('is_available'),
            ]);

            return redirect()->route('admin.translators.index')
                ->with('success', 'Translator created successfully!');

        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => 'Failed to create translator: ' . $e->getMessage()
            ])->withInput();
        }
    }
    
    /**
     * Show the form for editing the specified translator.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function editTranslator($id)
    {
        try {
            $translator = Translator::with('user')->findOrFail($id);
            return view('admin.translators.edit', compact('translator'));
        } catch (\Exception $e) {
            return redirect()->route('admin.translators.index')
                ->with('error', 'Translator not found.');
        }
    }
    
    /**
     * Update the specified translator in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateTranslator(Request $request, $id)
    {
        $translator = Translator::with('user')->findOrFail($id);
        
        // Convert comma-separated languages string to array
        if ($request->has('languages') && is_string($request->languages)) {
            $languagesArray = array_map('trim', explode(',', $request->languages));
            $languagesArray = array_filter($languagesArray); // Remove empty values
            $request->merge(['languages' => $languagesArray]);
        }

        // Map singular specialization input to the expected specializations array
        if ($request->has('specialization') && ! $request->has('specializations') && is_string($request->specialization)) {
            $specs = array_map('trim', explode(',', $request->specialization));
            $specs = array_filter($specs);
            $request->merge(['specializations' => array_values($specs)]);
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $translator->user_id . '|unique:translators,email,' . $id,
            'phone' => 'required|string|max:20',
            'country_code' => 'required|string|max:5',
            'password' => 'nullable|string|min:8|confirmed',
            // Location Information
            'country' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'address' => 'nullable|string',
            // Other fields
            'languages' => 'required|array|min:1',
            'languages.*' => 'string|max:100',
            'specializations' => 'nullable|array',
            'specializations.*' => 'string|max:100',
            'bio' => 'nullable|string',
            'hourly_rate' => 'nullable|numeric|min:0',
            'experience_years' => 'nullable|integer|min:0',

            'availability_hours' => 'nullable|string',
            'availability' => 'nullable|array',
            'availability.*' => 'string|max:100',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'delete_profile_image' => 'nullable|string',
            'is_verified' => 'nullable|boolean',
            'is_available' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        try {
            // Update the user account
            $userUpdates = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone_number' => $validated['phone'],
                'country_code' => $validated['country_code'],
                'is_active' => $validated['is_active'] ?? $translator->user->is_active,
            ];

            // Only update password if provided
            if (!empty($validated['password'])) {
                $userUpdates['password'] = bcrypt($validated['password']);
            }

            $translator->user->update($userUpdates);

            // Handle profile image upload/deletion
            $profileImagePath = $translator->profile_image;
            if ($request->input('delete_profile_image') == '1') {
                // Delete existing profile image
                if ($profileImagePath && \Storage::disk('public')->exists($profileImagePath)) {
                    \Storage::disk('public')->delete($profileImagePath);
                }
                $profileImagePath = null;
            } elseif ($request->hasFile('profile_image')) {
                // Delete old profile image if exists
                if ($profileImagePath && \Storage::disk('public')->exists($profileImagePath)) {
                    \Storage::disk('public')->delete($profileImagePath);
                }
                // Upload new profile image
                $profileImagePath = $request->file('profile_image')->store('profile-images', 'public');
            }

            // Update the translator profile
            $translator->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'country_code' => $validated['country_code'],
                // Location Information
                'country' => $validated['country'] ?? $translator->country,
                'city' => $validated['city'] ?? $translator->city,
                'address' => $validated['address'] ?? $translator->address,
                // Other fields
                'languages' => $validated['languages'],
                'specializations' => $validated['specializations'] ?? [],
                'bio' => $validated['bio'],
                'hourly_rate' => $validated['hourly_rate'],
                'availability' => $validated['availability'] ?? [],
                'experience_years' => $validated['experience_years'] ?? null,

                'profile_image' => $profileImagePath,
                'is_verified' => $validated['is_verified'] ?? $translator->is_verified,
                'is_available' => $validated['is_available'] ?? $translator->is_available,
            ]);

            return redirect()->route('admin.translators.index')
                ->with('success', 'Translator updated successfully!');

        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => 'Failed to update translator: ' . $e->getMessage()
            ])->withInput();
        }
    }
    
    /**
     * Remove the specified translator from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroyTranslator(Request $request, $id)
    {
        try {
            $translator = Translator::with('user')->findOrFail($id);
            
            // Delete profile image if exists
            if ($translator->profile_image && \Storage::disk('public')->exists($translator->profile_image)) {
                \Storage::disk('public')->delete($translator->profile_image);
            }
            
            // Delete the user account (this will cascade delete the translator due to foreign key)
            if ($translator->user) {
                $translator->user->delete();
            }
            
            // Delete the translator record if it still exists
            $translator->delete();
            
            if ($request->expectsJson() || $request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Translator deleted successfully.'
                ]);
            }
            
            return redirect()->route('admin.translators.index')
                ->with('success', 'Translator deleted successfully!');
                
        } catch (\Exception $e) {
            if (isset($request) && ($request->expectsJson() || $request->wantsJson() || $request->ajax())) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete translator: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->route('admin.translators.index')
                ->with('error', 'Failed to delete translator: ' . $e->getMessage());
        }
    }
    
    /**
     * Update translator status (verification)
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateTranslatorStatus(Request $request, $id)
    {
        $translator = Translator::findOrFail($id);
        
        $validated = $request->validate([
            'is_verified' => 'required|boolean',
            'rejection_reason' => 'nullable|string|max:500',
        ]);
        
        $translator->is_verified = $validated['is_verified'];
        $translator->rejection_reason = $validated['is_verified'] ? null : $validated['rejection_reason'];
        $translator->verification_date = $validated['is_verified'] ? now() : null;
        
        $translator->save();
        
        return response()->json(['success' => true]);
    }
    
    /**
     * Toggle translator availability
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleTranslatorAvailability(Request $request, $id)
    {
        $translator = Translator::findOrFail($id);
        
        $validated = $request->validate([
            'available' => 'required|boolean',
        ]);
        
        $translator->is_available = $validated['available'];
        $translator->save();
        
        if ($request->expectsJson() || $request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true]);
        }
        return redirect()->back()->with('success', 'Availability updated successfully');
    }

    /**
     * Export laboratories to Excel.
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportLaboratories()
    {
        return Excel::download(new LaboratoriesExport, 'laboratories_' . date('Y-m-d_H-i-s') . '.xlsx');
    }

    /**
     * Export translators to Excel.
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportTranslators()
    {
        return Excel::download(new TranslatorsExport, 'translators_' . date('Y-m-d_H-i-s') . '.xlsx');
    }

    /**
     * Handle bulk actions for translators.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function bulkTranslatorAction(Request $request)
    {
        $validated = $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:translators,id'
        ]);

        try {
            $translatorIds = $validated['ids'];
            $action = $validated['action'];

            $count = 0;
            switch ($action) {
                case 'activate':
                    $count = Translator::whereIn('id', $translatorIds)
                        ->update([
                            'is_verified' => true,
                            'rejection_reason' => null,
                            'verification_date' => now(),
                        ]);
                    $message = "$count translators approved successfully.";
                    break;

                case 'deactivate':
                    $count = Translator::whereIn('id', $translatorIds)
                        ->update([
                            'is_verified' => false,
                            'rejection_reason' => 'Rejected by admin',
                            'verification_date' => null,
                        ]);
                    $message = "$count translators rejected successfully.";
                    break;

                case 'delete':
                    $translators = Translator::with('user')->whereIn('id', $translatorIds)->get();
                    foreach ($translators as $translator) {
                        if ($translator->profile_image && Storage::disk('public')->exists($translator->profile_image)) {
                            Storage::disk('public')->delete($translator->profile_image);
                        }
                        if ($translator->user) {
                            $translator->user->delete();
                        }
                        $translator->delete();
                        $count++;
                    }
                    $message = "$count translators deleted successfully.";
                    break;
            }

            return response()->json([
                'success' => true,
                'message' => $message,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Bulk action failed: ' . $e->getMessage(),
            ], 500);
        }
    }

}