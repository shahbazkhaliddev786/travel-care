<div class="profile-right">
    <div class="services-section">
        
        <h2 class="services-title">My Services</h2>
        
        @if(isset($doctor) && $doctor && isset($doctor->services) && $doctor->services->count() > 0)
            @foreach($doctor->services as $service)
                <div class="service-item" id="service-item-{{ $service->id }}">
                    <div class="service-info">
                        <h3>{{ $service->name }} <span class="service-price">${{ number_format($service->price, 0) }}</span></h3>
                        <p class="service-description">{{ $service->description }}</p>
                    </div>
                    <button class="edit-button" data-id="{{ $service->id }}">
                        Edit
                    </button>
                </div>
                
                <!-- Inline Edit Form (hidden by default) -->
                <div class="service-edit-form" id="edit-form-{{ $service->id }}" style="display: none;">
                    <form action="{{ route('profile.update-service', $service->id) }}" method="POST">
                        @csrf
                        <div class="edit-form-fields">
                            <div class="edit-form-group">
                                <label>Title</label>
                                <div class="edit-input-wrapper">
                                    <input type="text" name="name" value="{{ $service->name }}" class="edit-input">
                                </div>
                            </div>
                            
                            <div class="edit-form-group">
                                <label>Price</label>
                                <div class="edit-input-wrapper price-input">
                                    <input type="number" name="price" value="{{ $service->price }}" class="edit-input">
                                    <span class="currency-symbol">$</span>
                                </div>
                            </div>

                            <div class="edit-form-group">
                                <label>Short description</label>
                                <div class="edit-input-wrapper">
                                    <input type="text" name="description" value="{{ $service->description }}" class="edit-input">
                                </div>
                            </div>
                            
                            <div class="edit-form-actions">
                                <button type="submit" class="save-button">Save</button>
                            </div>
                        </div>
                    </form>
                </div>
                
            @endforeach
        @else
            <div class="no-services-message">
                <p>No Services Added</p>
            </div>
        @endif
        
        <!-- Add Service Button -->
        <div class="add-service">
            <i class="fas fa-plus"></i> Add One More Service
        </div>
        
        <!-- Add Service Form (hidden by default) -->
        <div class="service-edit-form no-border" id="add-service-form" style="display: none;">
            <form action="{{ route('profile.add-service') }}" method="POST">
                @csrf
                <div class="edit-form-fields">
                    <div class="edit-form-group">
                        <label>Title</label>
                        <div class="edit-input-wrapper">
                            <input type="text" name="name" placeholder="Title" class="edit-input">
                        </div>
                    </div>
                    
                    <div class="edit-form-group">
                        <label>Price</label>
                        <div class="edit-input-wrapper price-input">
                            <input type="number" name="price" placeholder="Price" class="edit-input">
                            <span class="currency-symbol">$</span>
                        </div>
                    </div>
                    
                    <div class="edit-form-group">
                        <label>Short description</label>
                        <div class="edit-input-wrapper">
                            <input type="text" name="description" placeholder="Description" class="edit-input">
                        </div>
                    </div>
                    
                    <div class="edit-form-actions">
                        <button type="submit" class="save-button">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>