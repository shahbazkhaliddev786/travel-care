<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hospital;
use App\Models\GalleryImage;
use App\Models\SealOfQuality;
use App\Models\PaymentMethod;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\HospitalsExport;

class HospitalManagementController extends Controller
{
    /**
     * Display a listing of the hospitals.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Hospital::query();
        
        // Apply search filter
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%");
            });
        }
        
        // Apply status filter
        if ($request->has('status') && $request->status !== '') {
            if ($request->status === 'approved') {
                $query->where('is_verified', 1);
            } elseif ($request->status === 'pending') {
                $query->where('is_verified', 0);
            } elseif ($request->status === 'rejected') {
                $query->where('is_verified', -1);
            }
        }
        
        // Apply date filters
        if ($request->has('date_from') && !empty($request->date_from)) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && !empty($request->date_to)) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        // Get paginated results
        $hospitals = $query->orderBy('created_at', 'desc')->paginate(10);
        
        // Calculate stats
        $stats = [
            'approved' => Hospital::where('is_verified', 1)->count(),
            'pending' => Hospital::where('is_verified', 0)->count(),
            'rejected' => Hospital::where('is_verified', -1)->count(),
            'total' => Hospital::count(),
        ];
        
        return view('admin.hospitals.index', compact('hospitals', 'stats'));
    }
    
    /**
     * Show the form for creating a new hospital.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.hospitals.create');
    }
    
    /**
     * Store a newly created hospital in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:general,specialized,clinic,emergency,rehabilitation',
            'email' => 'required|email|unique:hospitals,email',
            'country_code' => 'required|string|max:5',
            'phone' => 'required|string|max:20',
            'website' => 'nullable|url',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description' => 'nullable|string',
            'country' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'address' => 'required|string',
            'specialties' => 'nullable|string',
            'facilities' => 'nullable|string',
            'bed_count' => 'nullable|integer|min:0',
            'emergency_services' => 'nullable|boolean',
            'pharmacy' => 'nullable|boolean',
            'operating_hours_from' => 'nullable|string',
            'operating_hours_to' => 'nullable|string',
            'operating_days' => 'nullable|array',
            'operating_days.*' => 'string|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        try {
            // Handle logo upload
            $logoPath = null;
            if ($request->hasFile('logo')) {
                $logoPath = $request->file('logo')->store('hospitals/logos', 'public');
            }
            
            // Create hospital with all the new fields
            $hospital = Hospital::create([
                'name' => $request->name,
                'type' => $request->type,
                'email' => $request->email,
                'country_code' => $request->country_code,
                'phone' => $request->phone,
                'website' => $request->website,
                'logo' => $logoPath,
                'description' => $request->description,
                'country' => $request->country,
                'city' => $request->city,
                'state' => $request->state,
                'postal_code' => $request->postal_code,
                'address' => $request->address,
                'specialties' => $request->specialties,
                'facilities' => $request->facilities,
                'bed_count' => $request->bed_count,
                'emergency_services' => $request->has('emergency_services') ? (bool)$request->emergency_services : false,
                'pharmacy' => $request->has('pharmacy') ? (bool)$request->pharmacy : false,
                'operating_hours_from' => $request->operating_hours_from,
                'operating_hours_to' => $request->operating_hours_to,
                'operating_days' => $request->operating_days ? json_encode($request->operating_days) : null,
                'is_verified' => 1, // Auto-approve hospitals created by admin
            ]);
            
            return redirect()->route('admin.hospitals.index')
                ->with('success', 'Hospital created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Failed to create hospital: ' . $e->getMessage()])
                ->withInput();
        }
    }
    
    /**
     * Display the specified hospital.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $hospital = Hospital::with(['galleryImages', 'sealsOfQuality', 'paymentMethods'])
            ->findOrFail($id);
        
        $paymentMethods = PaymentMethod::all();
        
        return view('admin.hospitals.show', compact('hospital', 'paymentMethods'));
    }
    
    /**
     * Show the form for editing the specified hospital.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        try {
            $hospital = Hospital::findOrFail($id);
            return view('admin.hospitals.edit', compact('hospital'));
        } catch (\Exception $e) {
            return redirect()->route('admin.hospitals.index')
                ->with('error', 'Hospital not found.');
        }
    }
    
    /**
     * Update the specified hospital in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $hospital = Hospital::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:general,specialized,clinic,emergency,rehabilitation',
            'email' => 'required|email|unique:hospitals,email,' . $id,
            'country_code' => 'required|string|max:5',
            'phone' => 'required|string|max:20',
            'website' => 'nullable|url',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description' => 'nullable|string',
            'country' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'address' => 'required|string',
            'specialties' => 'nullable|string',
            'facilities' => 'nullable|string',
            'bed_count' => 'nullable|integer|min:0',
            'emergency_services' => 'nullable|boolean',
            'pharmacy' => 'nullable|boolean',
            'operating_hours_from' => 'nullable|string',
            'operating_hours_to' => 'nullable|string',
            'operating_days' => 'nullable|array',
            'operating_days.*' => 'string|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'is_verified' => 'nullable|in:-1,0,1',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        try {
            // Handle logo upload
            $logoPath = $hospital->logo;
            if ($request->hasFile('logo')) {
                // Delete old logo if exists
                if ($logoPath && Storage::disk('public')->exists($logoPath)) {
                    Storage::disk('public')->delete($logoPath);
                }
                $logoPath = $request->file('logo')->store('hospitals/logos', 'public');
            }
            
            // Update hospital with all the new fields
            $hospital->update([
                'name' => $request->name,
                'type' => $request->type,
                'email' => $request->email,
                'country_code' => $request->country_code,
                'phone' => $request->phone,
                'website' => $request->website,
                'logo' => $logoPath,
                'description' => $request->description,
                'country' => $request->country,
                'city' => $request->city,
                'state' => $request->state,
                'postal_code' => $request->postal_code,
                'address' => $request->address,
                'specialties' => $request->specialties,
                'facilities' => $request->facilities,
                'bed_count' => $request->bed_count,
                'emergency_services' => $request->has('emergency_services') ? (bool)$request->emergency_services : false,
                'pharmacy' => $request->has('pharmacy') ? (bool)$request->pharmacy : false,
                'operating_hours_from' => $request->operating_hours_from,
                'operating_hours_to' => $request->operating_hours_to,
                'operating_days' => $request->operating_days ? json_encode($request->operating_days) : null,
                'is_verified' => $request->is_verified ?? $hospital->is_verified,
            ]);
            
            return redirect()->route('admin.hospitals.index')
                ->with('success', 'Hospital updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Failed to update hospital: ' . $e->getMessage()])
                ->withInput();
        }
    }
    
    /**
     * Remove the specified hospital from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        try {
            $hospital = Hospital::findOrFail($id);
            
            // Delete profile image if exists
            if ($hospital->profile_image && Storage::disk('public')->exists($hospital->profile_image)) {
                Storage::disk('public')->delete($hospital->profile_image);
            }
            
            // Delete license scan if exists
            if ($hospital->license_scan && Storage::disk('public')->exists($hospital->license_scan)) {
                Storage::disk('public')->delete($hospital->license_scan);
            }
            
            // Delete gallery images if exists
            if ($hospital->galleryImages) {
                foreach ($hospital->galleryImages as $galleryImage) {
                    if ($galleryImage->image_path && Storage::disk('public')->exists($galleryImage->image_path)) {
                        Storage::disk('public')->delete($galleryImage->image_path);
                    }
                    $galleryImage->delete();
                }
            }
            
            // Delete seals of quality
            if ($hospital->sealsOfQuality) {
                $hospital->sealsOfQuality()->delete();
            }
            
            // Delete the hospital
            $hospital->delete();
            
            return redirect()->route('admin.hospitals.index')
                ->with('success', 'Hospital deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('admin.hospitals.index')
                ->with('error', 'Failed to delete hospital: ' . $e->getMessage());
        }
    }
    
    /**
     * Update the hospital profile from the show modal.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateProfile(Request $request, $id)
    {
        $hospital = Hospital::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:hospitals,email,' . $id,
            'country_code' => 'required|string|max:5',
            'phone' => 'required|string|max:20',
            'city' => 'required|string|max:100',
            'address' => 'required|string',
            'description' => 'nullable|string',
            'professional_id' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'license_scan' => 'nullable|file|mimes:pdf,jpeg,png,jpg|max:5120',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        try {
            // Handle logo upload
            $logoPath = $hospital->logo;
            if ($request->hasFile('logo')) {
                // Delete old logo if exists
                if ($logoPath && Storage::disk('public')->exists($logoPath)) {
                    Storage::disk('public')->delete($logoPath);
                }
                $logoPath = $request->file('logo')->store('hospitals/logos', 'public');
            }
            
            // Handle license scan upload
            $licenseScanPath = $hospital->license_scan;
            if ($request->hasFile('license_scan')) {
                // Delete old scan if exists
                if ($licenseScanPath && Storage::disk('public')->exists($licenseScanPath)) {
                    Storage::disk('public')->delete($licenseScanPath);
                }
                $licenseScanPath = $request->file('license_scan')->store('hospitals/licenses', 'public');
            }
            
            // Update hospital
            $hospital->update([
                'name' => $request->name,
                'email' => $request->email,
                'country_code' => $request->country_code,
                'phone' => $request->phone,
                'city' => $request->city,
                'address' => $request->address,
                'description' => $request->description,
                'professional_id' => $request->professional_id,
                'logo' => $logoPath,
                'license_scan' => $licenseScanPath,
            ]);
            
            return redirect()->route('admin.hospitals.show', $hospital->id)
                ->with('success', 'Hospital profile updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Failed to update hospital profile: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Update the specified hospital's status.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:-1,0,1',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid status value',
            ], 400);
        }
        
        $hospital = Hospital::findOrFail($id);
        $hospital->is_verified = $request->status;
        $hospital->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Hospital status updated successfully',
        ]);
    }
    
    /**
     * Upload a gallery image for the specified hospital.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function uploadGalleryImage(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'gallery_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        $hospital = Hospital::findOrFail($id);
        
        // Handle image upload
        $imagePath = $request->file('gallery_image')->store('hospitals/gallery', 'public');
        
        // Create gallery image
        $hospital->galleryImages()->create([
            'image_path' => $imagePath,
            'title' => $request->title,
            'description' => $request->description,
        ]);
        
        return redirect()->route('admin.hospitals.show', $hospital->id)
            ->with('success', 'Gallery image uploaded successfully.');
    }
    
    /**
     * Delete a gallery image.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteGalleryImage(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'image_id' => 'required|integer',
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid image ID: ' . $validator->errors()->first(),
                ], 400);
            }
            
            $hospital = Hospital::findOrFail($id);
            $image = GalleryImage::where('id', $request->image_id)
                ->where('imageable_id', $hospital->id)
                ->where('imageable_type', Hospital::class)
                ->first();
            
            if (!$image) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gallery image not found',
                ], 404);
            }
            
            // Delete image file if exists
            if ($image->image_path && Storage::disk('public')->exists($image->image_path)) {
                Storage::disk('public')->delete($image->image_path);
            }
            
            // Delete image record
            $image->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Gallery image deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting gallery image: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * Add a seal of quality to the specified hospital.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addSealOfQuality(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'issuing_authority' => 'nullable|string|max:255',
            'issue_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after_or_equal:issue_date',
            'description' => 'nullable|string',
            'seal_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        $hospital = Hospital::findOrFail($id);
        
        // Handle image upload
        $imagePath = null;
        if ($request->hasFile('seal_image')) {
            $imagePath = $request->file('seal_image')->store('hospitals/seals', 'public');
        }
        
        // Create seal of quality
        $hospital->sealsOfQuality()->create([
            'name' => $request->name,
            'image_path' => $imagePath,
            'issuing_authority' => $request->issuing_authority,
            'issue_date' => $request->issue_date,
            'expiry_date' => $request->expiry_date,
            'description' => $request->description,
        ]);
        
        return redirect()->route('admin.hospitals.show', $hospital->id)
            ->with('success', 'Seal of quality added successfully.');
    }
    
    /**
     * Remove a seal of quality.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeSealOfQuality(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'seal_id' => 'required|integer',
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid seal ID: ' . $validator->errors()->first(),
                ], 400);
            }
            
            $hospital = Hospital::findOrFail($id);
            $seal = SealOfQuality::where('id', $request->seal_id)
                ->where('qualifiable_id', $hospital->id)
                ->where('qualifiable_type', Hospital::class)
                ->first();
            
            if (!$seal) {
                return response()->json([
                    'success' => false,
                    'message' => 'Seal of quality not found',
                ], 404);
            }
            
            // Delete image file if exists
            if ($seal->image_path && Storage::disk('public')->exists($seal->image_path)) {
                Storage::disk('public')->delete($seal->image_path);
            }
            
            // Delete seal record
            $seal->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Seal of quality removed successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error removing seal: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * Update hospital payment methods.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePaymentMethods(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'payment_methods' => 'nullable|array',
            'payment_methods.*' => 'exists:payment_methods,id',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        $hospital = Hospital::findOrFail($id);
        
        // Sync payment methods
        $hospital->paymentMethods()->sync($request->payment_methods ?? []);
        
        return redirect()->route('admin.hospitals.show', $hospital->id)
            ->with('success', 'Payment methods updated successfully.');
    }
    
    /**
     * Export hospitals data to Excel.
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export()
    {
        return Excel::download(new HospitalsExport, 'hospitals_' . date('Y-m-d_H-i-s') . '.xlsx');
    }
}