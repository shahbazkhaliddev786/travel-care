<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Doctor;
use App\Models\Service;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class DoctorProfileController extends Controller
{
    public function index()
    {
        // Get the authenticated user's doctor profile
        $doctor = Auth::user()->doctor;
        
        if (!$doctor) {
            return redirect()->route('home')->with('error', 'Doctor profile not found.');
        }
        
        // Load the services relation
        $doctor->load('services');
        
        return view('profile.doctor-profile', compact('doctor'));
    }
    
    public function updateService(Request $request, $id)
    {
        $service = Service::findOrFail($id);
        $doctor = Auth::user()->doctor;
        
        // Check if the service belongs to the authenticated doctor
        if (!$doctor || $service->doctor_id !== $doctor->id) {
            return redirect()->route('profile.index')->with('error', 'Unauthorized action.');
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'required|string|max:255',
        ]);
        
        $service->update($validated);
        
        return redirect()->route('profile.index')->with('success', 'Service updated successfully');
    }
    
    public function addService(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'required|string|max:255',
        ]);
        
        $doctor = Auth::user()->doctor;
        
        if (!$doctor) {
            return redirect()->route('profile.index')->with('error', 'Doctor profile not found.');
        }
        
        $service = new Service($validated);
        $service->doctor_id = $doctor->id;
        $service->save();
        
        return redirect()->route('profile.index')->with('success', 'Service added successfully');
    }
    
    public function updateFees(Request $request)
    {
        $validated = $request->validate([
            'messaging_fee' => 'nullable|numeric|min:0',
            'video_call_fee' => 'nullable|numeric|min:0',
            'house_visit_fee' => 'nullable|numeric|min:0',
            'voice_call_fee' => 'nullable|numeric|min:0',
        ]);
        
        $doctor = Auth::user()->doctor;
        
        if (!$doctor) {
            return redirect()->route('profile.index')->with('error', 'Doctor profile not found.');
        }
        
        // Update doctor profile
        $doctor->update($validated);
        
        return redirect()->route('profile.index')->with('success', 'Fees updated successfully');
    }
    
    public function updateWorkingHours(Request $request)
    {
        $validated = $request->validate([
            'working_hours_from' => 'required|string',
            'working_hours_to' => 'required|string',
            'working_days' => 'required|array',
            'working_days.*' => 'string|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
        ]);
        
        $doctor = Auth::user()->doctor;
        
        if (!$doctor) {
            return redirect()->route('profile.index')->with('error', 'Doctor profile not found.');
        }
        
        // Update doctor profile
        $doctor->update($validated);
        
        return redirect()->route('profile.index')->with('success', 'Working hours updated successfully');
    }
    
    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'city' => 'nullable|string|max:255',
            'years_of_experience' => 'nullable|integer|min:0',
            'working_location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'profile_image' => 'nullable|image|max:2048',
            'delete_profile_image' => 'nullable|string',
            'gallery_images' => 'nullable|array',
            'gallery_images.*' => 'image|max:2048',
        ]);
        
        $doctor = Auth::user()->doctor;
        
        if (!$doctor) {
            return redirect()->route('profile.index')->with('error', 'Doctor profile not found.');
        }
        
        // Handle profile image upload or deletion
        if ($request->input('delete_profile_image') == '1') {
            // Delete the profile image
            if ($doctor->profile_image && Storage::disk('public')->exists($doctor->profile_image)) {
                Storage::disk('public')->delete($doctor->profile_image);
            }
            $validated['profile_image'] = null;
        } elseif ($request->hasFile('profile_image')) {
            // Delete old image if exists
            if ($doctor->profile_image && Storage::disk('public')->exists($doctor->profile_image)) {
                Storage::disk('public')->delete($doctor->profile_image);
            }
            
            // Store new image
            $path = $request->file('profile_image')->store('profile-images', 'public');
            $validated['profile_image'] = $path;
        } else {
            // No new image uploaded and not deleting - don't update the profile_image field
            unset($validated['profile_image']);
        }
        
        // Handle gallery images
        if ($request->hasFile('gallery_images')) {
            $gallery = $doctor->gallery_images ?? [];
            
            foreach ($request->file('gallery_images') as $image) {
                $path = $image->store('gallery-images', 'public');
                $gallery[] = $path;
            }
            
            $validated['gallery_images'] = $gallery;
        }
        
        // Remove the delete_profile_image field as it's not part of the model
        unset($validated['delete_profile_image']);
        
        // Update doctor profile
        $doctor->update($validated);
        
        // Sync profile image with user's profile_photo for navbar display
        if (isset($validated['profile_image'])) {
            $doctor->user->update(['profile_photo' => $validated['profile_image']]);
        }
        
        return redirect()->route('profile.index')->with('success', 'Profile updated successfully');
    }
    
    public function deleteProfileImage()
    {
        $doctor = Auth::user()->doctor;
        
        if (!$doctor) {
            return redirect()->route('profile.index')->with('error', 'Doctor profile not found.');
        }
        
        // Delete profile image
        if ($doctor->profile_image && Storage::disk('public')->exists($doctor->profile_image)) {
            Storage::disk('public')->delete($doctor->profile_image);
        }
        
        $doctor->profile_image = null;
        $doctor->save();
        
        // Sync with user's profile_photo
        $doctor->user->update(['profile_photo' => null]);
        
        return redirect()->route('profile.index')->with('success', 'Profile image deleted successfully');
    }
    
    public function addPaymentMethod(Request $request)
    {
        $validated = $request->validate([
            'card_type' => 'required|string|in:visa,mastercard,amex,discover',
            'holder_name' => 'required|string|max:255',
            'card_number' => 'required|string|max:16',
            'expiry_month' => 'required|string|max:2',
            'expiry_year' => 'required|string|max:4',
            'cvv' => 'required|string|max:4',
        ]);
        
        $doctor = Auth::user()->doctor;
        
        if (!$doctor) {
            return redirect()->route('profile.index')->with('error', 'Doctor profile not found.');
        }
        
        // In a real app, you would use a payment gateway to tokenize the card
        // For demo purposes, we'll just store the last 4 digits
        $paymentMethods = $doctor->payment_methods ?? [];
        $paymentMethods[] = [
            'id' => count($paymentMethods) + 1,
            'type' => $validated['card_type'],
            'holder_name' => $validated['holder_name'],
            'last_four' => substr($validated['card_number'], -4),
        ];
        
        $doctor->payment_methods = $paymentMethods;
        $doctor->save();
        
        return redirect()->route('profile.index')->with('success', 'Payment method added successfully');
    }
    
    public function deletePaymentMethod(Request $request, $id)
    {
        $doctor = Auth::user()->doctor;
        
        if (!$doctor) {
            return redirect()->route('profile.index')->with('error', 'Doctor profile not found.');
        }
        
        $paymentMethods = $doctor->payment_methods ?? [];
        $paymentMethods = array_filter($paymentMethods, function($method) use ($id) {
            return $method['id'] != $id;
        });
        
        $doctor->payment_methods = array_values($paymentMethods);
        $doctor->save();
        
        return redirect()->route('profile.index')->with('success', 'Payment method deleted successfully');
    }
    
    public function updatePaypalEmail(Request $request)
    {
        $validated = $request->validate([
            'paypal_email' => 'required|email|max:255',
        ]);
        
        $doctor = Auth::user()->doctor;
        
        if (!$doctor) {
            return redirect()->route('profile.index')->with('error', 'Doctor profile not found.');
        }
        
        $doctor->paypal_email = $validated['paypal_email'];
        $doctor->save();
        
        return redirect()->route('profile.index')->with('success', 'PayPal email updated successfully');
    }

    /**
     * Upload gallery image
     */
    public function uploadGalleryImage(Request $request)
    {
        $request->validate([
            'gallery_image' => 'required|image|max:2048',
        ]);
        
        $doctor = Auth::user()->doctor;
        
        if (!$doctor) {
            return response()->json(['success' => false, 'message' => 'Doctor profile not found.']);
        }
        
        if ($request->hasFile('gallery_image')) {
            $gallery = $doctor->gallery_images ?? [];
            
            $path = $request->file('gallery_image')->store('gallery-images', 'public');
            $gallery[] = $path;
            
            $doctor->gallery_images = $gallery;
            $doctor->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Gallery image uploaded successfully',
                'image_path' => $path,
                'url' => asset('storage/' . $path),
                'path' => $path,
                'gallery_count' => count($gallery)
            ]);
        }
        
        return response()->json(['success' => false, 'message' => 'No file uploaded']);
    }

    /**
     * Delete gallery image
     */
    public function deleteGalleryImage(Request $request)
    {
        $imagePath = $request->input('image_path');
        
        $doctor = Auth::user()->doctor;
        
        if (!$doctor) {
            return response()->json(['success' => false, 'message' => 'Doctor profile not found.']);
        }
        
        $gallery = $doctor->gallery_images ?? [];
        
        // Remove the image from the gallery array
        $gallery = array_filter($gallery, function($image) use ($imagePath) {
            return $image !== $imagePath;
        });
        
        // Delete the file from storage
        if (Storage::disk('public')->exists($imagePath)) {
            Storage::disk('public')->delete($imagePath);
        }
        
        // Update the gallery
        $doctor->gallery_images = array_values($gallery);
        $doctor->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Gallery image deleted successfully',
            'gallery_count' => count($gallery)
        ]);
    }

    /**
     * Show public doctor profile (used by public pages)  
     */
    public function showPublicProfile($id = null)
    {
        // If no ID is provided, show the current user's profile
        if (!$id) {
            $doctor = Auth::user()->doctor;
            if (!$doctor) {
                return redirect()->route('home')->with('error', 'Doctor profile not found.');
            }
        } else {
            $doctor = Doctor::with(['services'])->find($id);
            if (!$doctor) {
                return redirect()->route('home')->with('error', 'Doctor profile not found.');
            }
        }

        $reviews = $doctor->reviews()
            ->with('user')
            ->orderByDesc('created_at')
            ->paginate(8);
        $reviews->appends(request()->query());

        return view('public-dlp', compact('doctor', 'reviews'));
    }
}
