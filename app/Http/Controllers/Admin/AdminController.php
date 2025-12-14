<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Doctor;
use App\Models\Hospital;
use App\Models\Customer;
use App\Models\Review;
use App\Models\Service;
use App\Models\Tag;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    /**
     * Display the admin dashboard with key metrics.
     *
     * @return \Illuminate\View\View
     */
    public function dashboard()
    {
        // Get counts for dashboard metrics
        $totalCustomers = User::where('role', 'customer')->count();
        $activeDoctors = Doctor::where('is_verified', true)->count();
        $totalHospitals = Hospital::count();
        
        // Count pending verifications
        $pendingDoctors = Doctor::where('is_verified', false)
            ->whereNull('rejection_reason')
            ->count();
        
        // Get most requested services (based on tags)
        $popularTags = Tag::withCount('services')
            ->orderByDesc('services_count')
            ->limit(8)
            ->get();
        
        // Get recent doctors and customers for activity section
        $recentDoctors = Doctor::orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
            
        $recentCustomers = User::where('role', 'customer')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        return view('admin.dashboard', compact(
            'totalCustomers',
            'activeDoctors', 
            'totalHospitals',
            'pendingDoctors',
            'popularTags',
            'recentDoctors',
            'recentCustomers'
        ));
    }
    
    /**
     * Display the admin profile page.
     *
     * @return \Illuminate\View\View
     */
    public function profile()
    {
        $user = Auth::user();
        return view('admin.profile', compact('user'));
    }
    
    /**
     * Update the admin profile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        // Debug logging
        Log::info('Admin Profile Update Request', [
            'user_id' => $user->id,
            'method' => $request->method(),
            'has_name' => $request->has('name'),
            'has_email' => $request->has('email'),
            'has_file' => $request->hasFile('profile_photo'),
            'request_data' => $request->except(['profile_photo'])
        ]);
        
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => [
                    'required',
                    'string',
                    'email',
                    'max:255',
                    Rule::unique('users')->ignore($user->id)
                ],
                'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);
            
            // Update basic profile information
            $user->name = $validated['name'];
            $user->email = $validated['email'];
            
            // Handle profile photo upload
            if ($request->hasFile('profile_photo')) {
                // Validate file exists and is valid
                $file = $request->file('profile_photo');
                if ($file->isValid()) {
                    // Delete old photo if exists
                    if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
                        Storage::disk('public')->delete($user->profile_photo);
                    }
                    
                    // Store new photo
                    $path = $file->store('profile-photos', 'public');
                    $user->profile_photo = $path;
                    
                    Log::info('Admin profile photo updated', [
                        'user_id' => $user->id,
                        'photo_path' => $path
                    ]);
                } else {
                    return redirect()->route('admin.profile')
                        ->with('error', 'The uploaded file is not valid. Please try again.');
                }
            }
            
            $user->save();
            
            return redirect()->route('admin.profile')
                ->with('success', 'Profile updated successfully!');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->route('admin.profile')
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            Log::error('Admin profile update failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->route('admin.profile')
                ->with('error', 'An error occurred while updating your profile. Please try again.');
        }
    }
    
    /**
     * Update the admin password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePassword(Request $request)
    {
        // Debug logging
        Log::info('Admin Password Update Request', [
            'user_id' => Auth::id(),
            'method' => $request->method(),
            'has_current_password' => $request->has('current_password'),
            'has_new_password' => $request->has('password'),
            'has_password_confirmation' => $request->has('password_confirmation')
        ]);
        
        try {
            $validated = $request->validate([
                'current_password' => 'required|string',
                'password' => 'required|string|min:8|confirmed',
            ]);
            
            $user = Auth::user();
            
            // Verify current password
            if (!Hash::check($validated['current_password'], $user->password)) {
                return redirect()->route('admin.profile')
                    ->withErrors(['current_password' => 'The current password is incorrect.'])
                    ->withInput();
            }
            
            // Update password
            $user->password = Hash::make($validated['password']);
            $user->save();
            
            Log::info('Admin password updated', ['user_id' => $user->id]);
            
            return redirect()->route('admin.profile')
                ->with('success', 'Password updated successfully!');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->route('admin.profile')
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            Log::error('Admin password update failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            
            return redirect()->route('admin.profile')
                ->with('error', 'An error occurred while updating your password. Please try again.');
        }
    }
}