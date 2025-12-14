<div class="modal" id="editProfileModal">
    <div class="modal-content">
        <span class="close-modal">&times;</span>
        <div class="modal-body">
            <form action="{{ route('profile.update-profile') }}" method="POST" enctype="multipart/form-data" id="edit-profile-form" class="edit-profile-form">
                @csrf
                @method('PUT')
                
                <h2 class="form-title">Edit Profile</h2>

                <div class="profile-photo-section">
                    <div class="profile-photo-container">
                        <div class="profile-photo">
                            <img src="{{ $doctor->profile_image ? asset('storage/' . $doctor->profile_image) . '?v=' . time() : asset('assets/icons/profile.svg') }}" alt="{{ $doctor->name }}" id="profile-preview">
                            <label for="profile_image" class="edit-photo-btn">
                                <i class="fas fa-camera"></i>
                            </label>
                            <input type="file" name="profile_image" id="profile_image" class="d-none hidden" accept="image/*">
                        </div>
                        <button type="button" id="delete-photo-btn" class="delete-photo-btn">Delete Photo</button>
                        <!-- Hidden input to track if photo should be deleted -->
                        <input type="hidden" name="delete_profile_image" id="delete_profile_image" value="0">
                    </div>
                    
                    <div class="profile-gallery">
                        @if(isset($doctor->gallery_images) && is_array($doctor->gallery_images) && count($doctor->gallery_images) > 0)
                            @foreach($doctor->gallery_images as $image)
                            <div class="gallery-item">
                                <img src="{{ asset('storage/' . $image) . '?v=' . time() }}" alt="Gallery Image">
                                <div class="delete-gallery-image" data-path="{{ $image }}">
                                    <i class="fas fa-trash"></i>
                                </div>
                            </div>
                            @endforeach
                            
                            @if(count($doctor->gallery_images) < 6)
                            <div class="gallery-item add-more">
                                <div class="add-more-btn">
                                    <i class="fas fa-plus"></i>
                                    <span>Add More</span>
                                </div>
                                <input type="file" name="gallery_images[]" id="gallery_image_input" class="d-none hidden" accept="image/*" multiple>
                            </div>
                            @endif
                        @else
                            
                            <div class="gallery-item add-more">
                                <div class="add-more-btn">
                                    <i class="fas fa-plus"></i>
                                    <span>Add More</span>
                                </div>
                                <input type="file" name="gallery_images[]" id="gallery_image_input" class="d-none hidden" accept="image/*" multiple>
                            </div>
                        @endif
                    </div>
                </div>
                
                <div class="data-fields">
                    <div class="form-group">
                        <label for="full_name">Full Name</label>
                        <input type="text" id="full_name" name="name" placeholder="Enter Full Name" value="{{ $doctor->name ?? '' }}" class="form-input">
                    </div>
                    
                    <div class="form-group">
                        <label for="city">City Or Village</label>
                        <input type="text" id="city" name="city" placeholder="Enter City Or Village" value="{{ $doctor->city ?? '' }}" class="form-input">
                    </div>
                    
                    <div class="form-group">
                        <label for="experience">Years Of Experience</label>
                        <input type="number" id="experience" name="years_of_experience" placeholder="Enter Years of Experience" value="{{ $doctor->years_of_experience ?? '' }}" class="form-input">
                    </div>
                    
                    <div class="form-group">
                        <label for="location">Working Location</label>
                        <input type="text" id="location" name="working_location" placeholder="Enter Location" value="{{ $doctor->working_location ?? '' }}" class="form-input">
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" placeholder="Add Description" class="form-textarea">{{ $doctor->description ?? '' }}</textarea>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>