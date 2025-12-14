<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\PaymentMethod;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user() ?? User::find(2); // Fallback for demo
        
        // Eager load the customer profile
        if(!$user->customerProfile) {
            // If no customer profile exists, create one
            if($user->isCustomer()) {
                Customer::create([
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email
                ]);
                // Refresh user to get the newly created relationship
                $user = User::with('customerProfile')->find($user->id);
            }
        } else {
            // Load the relationship if it exists
            $user->load('customerProfile');
        }
        
        $paymentMethods = PaymentMethod::where('user_id', $user->id)->get();
        
        return view('profile.user-profile', compact('user', 'paymentMethods'));
    }
    
    public function update(Request $request)
    {
        $user = Auth::user() ?? User::find(1); // Fallback for demo
        
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'biological_sex' => 'required|in:male,female',
            'age' => 'required|numeric|min:1',
            'weight' => 'required|numeric|min:1',
            'chronic_pathologies' => 'nullable|array',
            'allergies' => 'nullable|array',
            'chronic_medication' => 'nullable|array',
            'medical_info' => 'nullable|string',
        ]);
        
        // Process chronic pathologies - filter out empty values
        $chronicPathologies = array_filter($validated['chronic_pathologies'] ?? [], function($item) {
            return !empty(trim($item));
        });
        
        // Process allergies - filter out empty values
        $allergies = array_filter($validated['allergies'] ?? [], function($item) {
            return !empty(trim($item));
        });
        
        // Process chronic medications - filter out empty values
        $chronicMedications = array_filter($validated['chronic_medication'] ?? [], function($item) {
            return !empty(trim($item));
        });
        
        // Update user table
        $user->update([
            'name' => $validated['full_name'],
            'country' => $validated['country'],
            'city' => $validated['city'],
            'biological_sex' => $validated['biological_sex'],
            'age' => $validated['age'],
            'weight' => $validated['weight'],
            'chronic_pathologies' => json_encode(array_values($chronicPathologies)),
            'allergies' => json_encode(array_values($allergies)),
            'chronic_medication' => json_encode(array_values($chronicMedications)),
            'medical_info' => $validated['medical_info'],
        ]);
        
        // Update customer profile if it exists
        if($user->customerProfile) {
            $user->customerProfile->update([
                'name' => $validated['full_name'],
                'country' => $validated['country'],
                'city' => $validated['city'],
                'gender' => $validated['biological_sex'],
                'age' => $validated['age'],
                'weight' => $validated['weight'],
                'chronic_pathologies' => json_encode(array_values($chronicPathologies)),
                'allergies' => json_encode(array_values($allergies)),
                'chronic_medications' => json_encode(array_values($chronicMedications)),
            ]);
        }
        
        return redirect()->route('profile')->with('success', 'Profile updated successfully!');
    }
    
    public function uploadPhoto(Request $request)
    {
        $user = Auth::user() ?? User::find(1); // Fallback for demo
        
        $request->validate([
            'profile_photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        if ($request->hasFile('profile_photo')) {
            // Delete old photo if exists
            if ($user->profile_photo) {
                Storage::disk('public')->delete($user->profile_photo);
            }
            
            // Store the new photo
            $path = $request->file('profile_photo')->store('profile-photos', 'public');
            $user->update(['profile_photo' => $path]);
            
            return redirect()->route('profile')->with('success', 'Profile photo updated successfully!');
        }
        
        return redirect()->route('profile')->with('error', 'Failed to upload profile photo.');
    }
    
    public function deletePhoto(Request $request)
    {
        $user = Auth::user() ?? User::find(1); // Fallback for demo
        
        if ($user->profile_photo) {
            Storage::disk('public')->delete($user->profile_photo);
            $user->update(['profile_photo' => null]);
        }
        
        return redirect()->route('profile')->with('success', 'Profile photo deleted successfully!');
    }
}
