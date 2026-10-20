@extends('admin.layouts.layout')

@section('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css" rel="stylesheet">
<style>
    .form-section {
        background: white;
        border-radius: 12px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .section-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 1.5rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        display: block;
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.5rem;
    }

    .form-control {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 1rem;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }

    .form-control:focus {
        outline: none;
        border-color: #4f46e5;
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
    }

    .btn {
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        cursor: pointer;
        border: none;
        transition: all 0.2s ease;
    }

    .btn-primary {
        background: #4f46e5;
        color: white;
    }

    .btn-primary:hover {
        background: #4338ca;
    }

    .btn-secondary {
        background: #6b7280;
        color: white;
    }

    .btn-secondary:hover {
        background: #4b5563;
    }

    .file-upload-area {
        border: 2px dashed #d1d5db;
        border-radius: 8px;
        padding: 2rem;
        text-align: center;
        background: #f9fafb;
        transition: border-color 0.2s ease;
    }

    .file-upload-area:hover {
        border-color: #4f46e5;
    }

    .current-image {
        max-width: 150px;
        border-radius: 8px;
        margin-bottom: 1rem;
    }

    .actions {
        display: flex;
        gap: 1rem;
        margin-top: 2rem;
        padding-top: 2rem;
        border-top: 1px solid #e5e7eb;
    }

    /* Photo Cropping Styles */
    .photo-crop-modal {
        display: none;
        position: fixed;
        z-index: 9999;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.8);
        backdrop-filter: blur(5px);
    }
    
    .crop-modal-content {
        background-color: white;
        margin: 2% auto;
        padding: 0;
        border-radius: 12px;
        width: 90%;
        max-width: 800px;
        max-height: 90vh;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        position: relative;
        display: flex;
        flex-direction: column;
    }
    
    .crop-modal-header {
        padding: 1.5rem;
        border-bottom: 1px solid #e5e7eb;
        background: linear-gradient(135deg, #4f46e5, #4338ca);
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .crop-modal-header h4 {
        margin: 0;
        font-size: 1.25rem;
        font-weight: 600;
    }
    
    .crop-close {
        background: none;
        border: none;
        color: white;
        font-size: 1.5rem;
        cursor: pointer;
        padding: 0;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .crop-close:hover {
        background-color: rgba(255, 255, 255, 0.2);
        transform: rotate(90deg);
    }
    
    .crop-modal-body {
        padding: 1.5rem;
        flex: 1;
        overflow: auto;
    }
    
    .crop-container {
        width: 100%;
        max-height: 400px;
        margin-bottom: 1rem;
    }
    
    .crop-container img {
        max-width: 100%;
        height: auto;
        display: block;
    }
    
    .crop-controls {
        display: flex;
        gap: 1rem;
        margin-bottom: 1rem;
        flex-wrap: wrap;
        align-items: center;
    }
    
    .aspect-ratio-buttons {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }
    
    .aspect-btn {
        padding: 0.5rem 1rem;
        border: 2px solid #d1d5db;
        background: white;
        border-radius: 20px;
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 0.875rem;
        font-weight: 500;
    }
    
    .aspect-btn.active,
    .aspect-btn:hover {
        border-color: #4f46e5;
        background-color: #4f46e5;
        color: white;
    }
    
    .crop-preview {
        display: flex;
        gap: 1rem;
        margin-bottom: 1rem;
        flex-wrap: wrap;
    }
    
    .preview-box {
        text-align: center;
    }
    
    .preview-box h6 {
        margin-bottom: 0.5rem;
        font-size: 0.875rem;
        color: #6b7280;
        font-weight: 600;
    }
    
    .preview-circle,
    .preview-square {
        border: 2px solid #d1d5db;
        overflow: hidden;
        background-color: #f8f9fa;
        margin: 0 auto;
    }
    
    .preview-circle {
        width: 80px;
        height: 80px;
        border-radius: 50%;
    }
    
    .preview-square {
        width: 100px;
        height: 80px;
        border-radius: 8px;
    }
    
    .crop-modal-footer {
        padding: 1.5rem;
        border-top: 1px solid #e5e7eb;
        background-color: #f8f9fa;
        display: flex;
        justify-content: space-between;
        gap: 1rem;
    }
    
    .crop-buttons {
        display: flex;
        gap: 1rem;
    }
    
    .btn-crop {
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.2s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .btn-crop-cancel {
        background-color: #6c757d;
        color: white;
    }
    
    .btn-crop-cancel:hover {
        background-color: #5a6268;
    }
    
    .btn-crop-save {
        background: linear-gradient(135deg, #22c55e, #27ae60);
        color: white;
    }
    
    .btn-crop-save:hover {
        background: linear-gradient(135deg, #27ae60, #2ecc71);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(46, 204, 113, 0.3);
    }
    
    .crop-info {
        font-size: 0.875rem;
        color: #6b7280;
        margin-bottom: 1rem;
        padding: 1rem;
        background-color: #e8f4f8;
        border-radius: 6px;
        border-left: 4px solid #4f46e5;
    }
    
    .crop-info i {
        color: #4f46e5;
        margin-right: 0.5rem;
    }
    
    .current-photo-preview {
        position: relative;
        display: inline-block;
    }
    
    .photo-preview-img {
        max-width: 150px;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .photo-preview-img:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    
    .edit-photo-btn {
        position: absolute;
        bottom: -10px;
        right: -10px;
        width: 35px;
        height: 35px;
        border-radius: 50%;
        background: #4f46e5;
        color: white;
        border: 3px solid white;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.875rem;
        transition: all 0.2s ease;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    }
    
    .edit-photo-btn:hover {
        background: #4338ca;
        transform: scale(1.1);
    }
    
    /* Responsive Design for Crop Modal */
    @media (max-width: 768px) {
        .crop-modal-content {
            width: 95%;
            margin: 5% auto;
        }
        
        .crop-controls {
            flex-direction: column;
            align-items: stretch;
        }
        
        .aspect-ratio-buttons {
            justify-content: center;
        }
        
        .crop-preview {
            justify-content: center;
        }
        
        .crop-modal-footer {
            flex-direction: column;
        }
    }
</style>
@endsection

@section('content')
<div class="main-content app-content">
    <div class="container-fluid mt-4">
        <h2 class="mb-4">Edit Professional Details</h2>

        <!-- Display Success/Error Messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <h5><i class="fas fa-exclamation-triangle"></i> There were some errors with your submission:</h5>
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form action="{{ route('admin.manage-professional.update', $professional->id) }}" method="POST" enctype="multipart/form-data" onsubmit="return confirmUpdate()">
            @csrf
            @method('PUT')

            <!-- Basic Information -->
            <div class="form-section">
                <h3 class="section-title">
                    <i class="fas fa-user"></i>
                    Basic Information
                </h3>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="name" name="name" 
                               value="{{ old('name', $professional->name) }}" required>
                    </div>

                    <div class="form-group">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="{{ old('email', $professional->email) }}" required>
                    </div>

                    <div class="form-group">
                        <label for="phone" class="form-label">Phone Number</label>
                        <input type="text" class="form-control" id="phone" name="phone" 
                               value="{{ old('phone', optional($professional->profile)->phone) }}">
                    </div>

                    <div class="form-group">
                        <label for="specialization" class="form-label">Specialization</label>
                        <input type="text" class="form-control" id="specialization" name="specialization" 
                               value="{{ old('specialization', optional($professional->profile)->specialization) }}">
                    </div>

                    <div class="form-group">
                        <label for="experience" class="form-label">Experience</label>
                        <input type="text" class="form-control" id="experience" name="experience" 
                               placeholder="e.g., 5 years, Beginner, Expert, etc."
                               value="{{ old('experience', optional($professional->profile)->experience) }}">
                    </div>

                    <div class="form-group">
                        <label for="starting_price" class="form-label">Starting Price (₹)</label>
                        <input type="number" class="form-control" id="starting_price" name="starting_price" 
                               value="{{ old('starting_price', optional($professional->profile)->starting_price) }}">
                    </div>
                </div>

                <div class="form-group">
                    <label for="address" class="form-label">Address</label>
                    <textarea class="form-control" id="address" name="address" rows="3">{{ old('address', optional($professional->profile)->address) }}</textarea>
                </div>
            </div>

            <!-- Profile Photo -->
            <div class="form-section">
                <h3 class="section-title">
                    <i class="fas fa-camera"></i>
                    Profile Photo
                </h3>
                
                <div class="crop-info">
                    <i class="fas fa-info-circle"></i>
                    Click on the current photo or upload a new one to crop and adjust it perfectly. The cropper allows you to resize and position the image.
                </div>
                
                @if(optional($professional->profile)->photo)
                    <div class="mb-3">
                        <label class="form-label">Current Photo</label><br>
                        <div class="current-photo-preview">
                            <img src="{{ asset('storage/'.optional($professional->profile)->photo) }}" 
                                 class="current-image photo-preview-img" 
                                 alt="Current Photo"
                                 id="currentPhotoPreview"
                                 onclick="openCropperForExisting()">
                            <button type="button" class="edit-photo-btn" onclick="openCropperForExisting()" title="Edit Photo">
                                <i class="fas fa-crop-alt"></i>
                            </button>
                        </div>
                        <small class="text-muted d-block mt-2">Click on the image to crop/adjust it</small>
                    </div>
                @endif

                <div class="form-group">
                    <label for="photo" class="form-label">Upload New Photo (Optional)</label>
                    <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
                    <input type="hidden" id="cropped_photo" name="cropped_photo">
                    <small class="text-muted">Upload a new photo to replace the current one. The cropper will open automatically.</small>
                </div>
            </div>

            <!-- GST Information -->
            <div class="form-section">
                <h3 class="section-title">
                    <i class="fas fa-file-invoice-dollar"></i>
                    GST Information
                </h3>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="gst_number" class="form-label">GST Number</label>
                        <input type="text" class="form-control @error('gst_number') is-invalid @enderror" id="gst_number" name="gst_number" 
                               value="{{ old('gst_number', optional($professional->profile)->gst_number) }}">
                        @error('gst_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="state_code" class="form-label">State Code</label>
                        <input type="text" class="form-control" id="state_code" name="state_code" 
                               value="{{ old('state_code', optional($professional->profile)->state_code) }}">
                    </div>

                    <div class="form-group">
                        <label for="state_name" class="form-label">State Name</label>
                        <input type="text" class="form-control" id="state_name" name="state_name" 
                               value="{{ old('state_name', optional($professional->profile)->state_name) }}">
                    </div>
                </div>

                <div class="form-group">
                    <label for="gst_address" class="form-label">GST Address</label>
                    <textarea class="form-control" id="gst_address" name="gst_address" rows="3">{{ old('gst_address', optional($professional->profile)->gst_address) }}</textarea>
                </div>
            </div>

            <!-- Bank Account Details -->
            <div class="form-section">
                <h3 class="section-title">
                    <i class="fas fa-university"></i>
                    Bank Account Details
                </h3>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="account_holder_name" class="form-label">Account Holder Name</label>
                        <input type="text" class="form-control" id="account_holder_name" name="account_holder_name" 
                               value="{{ old('account_holder_name', optional($professional->profile)->account_holder_name) }}">
                    </div>

                    <div class="form-group">
                        <label for="bank_name" class="form-label">Bank Name</label>
                        <input type="text" class="form-control" id="bank_name" name="bank_name" 
                               value="{{ old('bank_name', optional($professional->profile)->bank_name) }}">
                    </div>

                    <div class="form-group">
                        <label for="account_number" class="form-label">Account Number</label>
                        <input type="text" class="form-control" id="account_number" name="account_number" 
                               value="{{ old('account_number', optional($professional->profile)->account_number) }}">
                    </div>

                    <div class="form-group">
                        <label for="ifsc_code" class="form-label">IFSC Code</label>
                        <input type="text" class="form-control" id="ifsc_code" name="ifsc_code" 
                               value="{{ old('ifsc_code', optional($professional->profile)->ifsc_code) }}">
                    </div>

                    <div class="form-group">
                        <label for="account_type" class="form-label">Account Type</label>
                        <select class="form-control" id="account_type" name="account_type">
                            <option value="">Select Account Type</option>
                            <option value="savings" {{ old('account_type', optional($professional->profile)->account_type) == 'savings' ? 'selected' : '' }}>Savings</option>
                            <option value="current" {{ old('account_type', optional($professional->profile)->account_type) == 'current' ? 'selected' : '' }}>Current</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="bank_branch" class="form-label">Bank Branch</label>
                        <input type="text" class="form-control" id="bank_branch" name="bank_branch" 
                               value="{{ old('bank_branch', optional($professional->profile)->bank_branch) }}">
                    </div>
                </div>
            </div>

            <!-- Document Uploads -->
            <div class="form-section">
                <h3 class="section-title">
                    <i class="fas fa-file-alt"></i>
                    Documents
                </h3>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="qualification_document" class="form-label">Qualification Certificate</label>
                        @if(optional($professional->profile)->qualification_document)
                            <div class="mb-2">
                                <a href="{{ asset('storage/'.optional($professional->profile)->qualification_document) }}" target="_blank" class="btn btn-sm btn-secondary">
                                    <i class="fas fa-eye"></i> View Current
                                </a>
                            </div>
                        @endif
                        <input type="file" class="form-control" id="qualification_document" name="qualification_document" accept=".pdf,.jpg,.jpeg,.png">
                        <small class="text-muted">Leave empty to keep current document</small>
                    </div>

                    <div class="form-group">
                        <label for="id_proof_document" class="form-label">ID Proof Document</label>
                        @if(optional($professional->profile)->id_proof_document)
                            <div class="mb-2">
                                <a href="{{ asset('storage/'.optional($professional->profile)->id_proof_document) }}" target="_blank" class="btn btn-sm btn-secondary">
                                    <i class="fas fa-eye"></i> View Current
                                </a>
                            </div>
                        @endif
                        <input type="file" class="form-control" id="id_proof_document" name="id_proof_document" accept=".pdf,.jpg,.jpeg,.png">
                        <small class="text-muted">Leave empty to keep current document</small>
                    </div>

                    <div class="form-group">
                        <label for="gst_certificate" class="form-label">GST Certificate</label>
                        @if(optional($professional->profile)->gst_certificate)
                            <div class="mb-2">
                                <a href="{{ asset('storage/'.optional($professional->profile)->gst_certificate) }}" target="_blank" class="btn btn-sm btn-secondary">
                                    <i class="fas fa-eye"></i> View Current
                                </a>
                            </div>
                        @endif
                        <input type="file" class="form-control" id="gst_certificate" name="gst_certificate" accept=".pdf,.jpg,.jpeg,.png">
                        <small class="text-muted">Leave empty to keep current document</small>
                    </div>

                    <div class="form-group">
                        <label for="bank_document" class="form-label">Bank Account Proof</label>
                        @if(optional($professional->profile)->bank_document)
                            <div class="mb-2">
                                <a href="{{ asset('storage/'.optional($professional->profile)->bank_document) }}" target="_blank" class="btn btn-sm btn-secondary">
                                    <i class="fas fa-eye"></i> View Current
                                </a>
                            </div>
                        @endif
                        <input type="file" class="form-control" id="bank_document" name="bank_document" accept=".pdf,.jpg,.jpeg,.png">
                        <small class="text-muted">Leave empty to keep current document</small>
                    </div>
                </div>
            </div>

            <!-- Gallery Images -->
            <div class="form-section">
                <h3 class="section-title">
                    <i class="fas fa-images"></i>
                    Gallery Images
                </h3>
                
                <!-- Current Gallery Images -->
                @if(optional($professional->profile)->gallery_array && count(optional($professional->profile)->gallery_array) > 0)
                    <div class="mb-4">
                        <label class="form-label">Current Gallery Images</label>
                        <div class="gallery-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1rem;">
                            @foreach(optional($professional->profile)->gallery_array as $index => $image)
                                <div class="gallery-item" style="position: relative; border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden;">
                                    @php
                                        // Handle different image path formats
                                        if (str_starts_with($image, 'gallery/')) {
                                            $imagePath = asset('storage/'.$image);
                                        } elseif (str_starts_with($image, 'uploads/')) {
                                            $imagePath = asset($image);
                                        } elseif (str_starts_with($image, 'storage/')) {
                                            $imagePath = asset($image);
                                        } else {
                                            // Default fallback - try both possibilities
                                            if (file_exists(public_path('uploads/professionals/gallery/'.$image))) {
                                                $imagePath = asset('uploads/professionals/gallery/'.$image);
                                            } elseif (file_exists(public_path('storage/gallery/'.$image))) {
                                                $imagePath = asset('storage/gallery/'.$image);
                                            } else {
                                                $imagePath = asset('uploads/professionals/gallery/'.$image);
                                            }
                                        }
                                    @endphp
                                    <img src="{{ $imagePath }}" 
                                         style="width: 100%; height: 150px; object-fit: cover;" 
                                         alt="Gallery Image {{ $index + 1 }}">
                                    <div style="position: absolute; top: 5px; right: 5px;">
                                        <button type="button" class="btn btn-sm btn-danger" 
                                                onclick="removeGalleryImage('{{ $image }}', this)" 
                                                style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    <div style="padding: 0.5rem; background: #f9fafb; font-size: 0.75rem; color: #6b7280;">
                                        Image {{ $index + 1 }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="mb-4">
                        <div style="text-align: center; padding: 2rem; background: #f9fafb; border-radius: 8px; color: #6b7280;">
                            <i class="fas fa-images" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5;"></i>
                            <p style="margin: 0; font-size: 1.1rem;">No gallery images have been uploaded yet.</p>
                        </div>
                    </div>
                @endif

                <!-- Upload New Gallery Images -->
                <div class="form-group">
                    <label for="gallery_images" class="form-label">Upload New Gallery Images</label>
                    <input type="file" class="form-control" id="gallery_images" name="gallery_images[]" 
                           accept="image/*" multiple>
                    <small class="text-muted">
                        You can select multiple images. Supported formats: JPG, JPEG, PNG. Max 5 images at once.
                    </small>
                </div>

                <!-- Hidden field for removed images -->
                <input type="hidden" id="removed_images" name="removed_images" value="">
            </div>

            <!-- Form Actions -->
            <div class="actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Update Professional Details
                </button>
                <a href="{{ route('admin.manage-professional.show', $professional->id) }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i>
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Photo Crop Modal -->
<div id="photoCropModal" class="photo-crop-modal">
    <div class="crop-modal-content">
        <div class="crop-modal-header">
            <h4><i class="fas fa-crop-alt"></i> Crop Profile Photo</h4>
            <button type="button" class="crop-close" id="closeCropModal">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="crop-modal-body">
            <div class="crop-info">
                <i class="fas fa-info-circle"></i>
                Crop and adjust the photo to ensure it looks perfect. Choose an aspect ratio and position the crop area.
            </div>
            
            <div class="crop-controls">
                <div>
                    <label style="margin-bottom: 0.5rem; font-weight: 600;">Aspect Ratio:</label>
                    <div class="aspect-ratio-buttons">
                        <button type="button" class="aspect-btn" data-ratio="1" title="Square (1:1) - Perfect for profile pictures">
                            <i class="fas fa-square"></i> Square
                        </button>
                        <button type="button" class="aspect-btn" data-ratio="4/3" title="Landscape (4:3) - Good for headers">
                            <i class="fas fa-rectangle-landscape"></i> 4:3
                        </button>
                        <button type="button" class="aspect-btn" data-ratio="16/9" title="Widescreen (16:9) - For banners">
                            <i class="fas fa-rectangle-wide"></i> 16:9
                        </button>
                        <button type="button" class="aspect-btn active" data-ratio="NaN" title="Free crop - Any size">
                            <i class="fas fa-crop"></i> Free
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="crop-container">
                <img id="cropImage" src="" alt="Image to crop">
            </div>
            
            <div class="crop-preview">
                <div class="preview-box">
                    <h6>Dashboard Preview</h6>
                    <div class="preview-circle" id="previewCircle"></div>
                </div>
                <div class="preview-box">
                    <h6>Listing Preview</h6>
                    <div class="preview-square" id="previewSquare"></div>
                </div>
            </div>
        </div>
        <div class="crop-modal-footer">
            <div class="crop-info" style="margin: 0; padding: 0.5rem 1rem; background: none; border: none; font-size: 0.8rem;">
                <i class="fas fa-mouse"></i> Drag to move • <i class="fas fa-expand-arrows-alt"></i> Drag corners to resize
            </div>
            <div class="crop-buttons">
                <button type="button" class="btn-crop btn-crop-cancel" id="cancelCrop">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button type="button" class="btn-crop btn-crop-save" id="saveCrop">
                    <i class="fas fa-check"></i> Apply Crop
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>
<script>
// Photo Cropping Functionality
let cropper = null;
let currentFile = null;
let isEditingExisting = false;

// Photo Cropping Functions
function initializeCropper(imageFile, isExisting = false) {
    const modal = document.getElementById('photoCropModal');
    const cropImage = document.getElementById('cropImage');
    
    isEditingExisting = isExisting;
    
    let imageUrl;
    if (isExisting) {
        // Use the existing image source
        imageUrl = imageFile;
    } else {
        // Create object URL for the selected image
        imageUrl = URL.createObjectURL(imageFile);
        currentFile = imageFile;
    }
    
    cropImage.src = imageUrl;
    
    // Show modal
    modal.style.display = 'block';
    document.body.style.overflow = 'hidden';
    
    // Initialize cropper after image loads
    cropImage.onload = function() {
        if (cropper) {
            cropper.destroy();
        }
        
        cropper = new Cropper(cropImage, {
            aspectRatio: NaN, // Free crop by default
            viewMode: 1,
            dragMode: 'move',
            autoCropArea: 0.8,
            restore: false,
            guides: true,
            center: true,
            highlight: false,
            cropBoxMovable: true,
            cropBoxResizable: true,
            toggleDragModeOnDblclick: false,
            responsive: true,
            modal: true,
            background: true,
            ready: function () {
                updatePreviews();
            },
            cropend: function () {
                updatePreviews();
            }
        });
    };
}

function updatePreviews() {
    if (!cropper) return;
    
    const circleCanvas = cropper.getCroppedCanvas({
        width: 80,
        height: 80,
        imageSmoothingQuality: 'high'
    });
    
    const squareCanvas = cropper.getCroppedCanvas({
        width: 100,
        height: 80,
        imageSmoothingQuality: 'high'
    });
    
    if (circleCanvas && squareCanvas) {
        const previewCircle = document.getElementById('previewCircle');
        const previewSquare = document.getElementById('previewSquare');
        
        // Clear previous previews
        previewCircle.innerHTML = '';
        previewSquare.innerHTML = '';
        
        // Add new previews
        const circleImg = circleCanvas.toDataURL();
        const squareImg = squareCanvas.toDataURL();
        
        previewCircle.style.backgroundImage = `url(${circleImg})`;
        previewCircle.style.backgroundSize = 'cover';
        previewCircle.style.backgroundPosition = 'center';
        
        previewSquare.style.backgroundImage = `url(${squareImg})`;
        previewSquare.style.backgroundSize = 'cover';
        previewSquare.style.backgroundPosition = 'center';
    }
}

function closeCropModal() {
    const modal = document.getElementById('photoCropModal');
    modal.style.display = 'none';
    document.body.style.overflow = 'auto';
    
    if (cropper) {
        cropper.destroy();
        cropper = null;
    }
    
    if (!isEditingExisting) {
        // Reset file input only if not editing existing
        const photoInput = document.getElementById('photo');
        photoInput.value = '';
    }
    
    currentFile = null;
    isEditingExisting = false;
}

function saveCroppedImage() {
    if (!cropper) return;
    
    // Get cropped canvas with high quality
    const canvas = cropper.getCroppedCanvas({
        width: 800,
        height: 800,
        imageSmoothingQuality: 'high',
        fillColor: '#fff'
    });
    
    if (canvas) {
        // Convert to blob
        canvas.toBlob(function(blob) {
            // Store the cropped image data
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('cropped_photo').value = e.target.result;
                
                // Update preview
                const previewImg = document.getElementById('currentPhotoPreview');
                if (previewImg) {
                    previewImg.src = e.target.result;
                }
            };
            reader.readAsDataURL(blob);
            
            // Close modal
            closeCropModal();
            
            // Show success message
            showToast('Photo cropped successfully! Remember to save the form to apply changes.', 'success');
            
        }, 'image/jpeg', 0.9);
    }
}

function openCropperForExisting() {
    const currentPhoto = document.getElementById('currentPhotoPreview');
    if (currentPhoto && currentPhoto.src) {
        initializeCropper(currentPhoto.src, true);
    }
}

// Event Listeners
document.addEventListener('DOMContentLoaded', function() {
    // Photo input change
    const photoInput = document.getElementById('photo');
    if (photoInput) {
        photoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file && file.type.startsWith('image/')) {
                // Validate file size (5MB max)
                if (file.size > 5 * 1024 * 1024) {
                    showToast('File size must be less than 5MB', 'error');
                    this.value = '';
                    return;
                }
                
                currentFile = file;
                initializeCropper(file, false);
            } else if (file) {
                showToast('Please select a valid image file', 'error');
                this.value = '';
            }
        });
    }
    
    // Aspect ratio buttons
    document.querySelectorAll('.aspect-btn').forEach(button => {
        button.addEventListener('click', function() {
            // Update active state
            document.querySelectorAll('.aspect-btn').forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            // Update cropper aspect ratio
            if (cropper) {
                const ratio = parseFloat(this.dataset.ratio);
                cropper.setAspectRatio(ratio);
                setTimeout(updatePreviews, 100);
            }
        });
    });
    
    // Close modal buttons
    const closeCropBtn = document.getElementById('closeCropModal');
    if (closeCropBtn) {
        closeCropBtn.addEventListener('click', closeCropModal);
    }
    
    const cancelCropBtn = document.getElementById('cancelCrop');
    if (cancelCropBtn) {
        cancelCropBtn.addEventListener('click', closeCropModal);
    }
    
    // Save crop button
    const saveCropBtn = document.getElementById('saveCrop');
    if (saveCropBtn) {
        saveCropBtn.addEventListener('click', saveCroppedImage);
    }
    
    // Close modal when clicking outside
    const cropModal = document.getElementById('photoCropModal');
    if (cropModal) {
        cropModal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeCropModal();
            }
        });
    }
});

// Toast notification function
function showToast(message, type = 'info') {
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${type === 'success' ? '#22c55e' : type === 'error' ? '#ef4444' : '#4f46e5'};
        color: white;
        padding: 12px 20px;
        border-radius: 8px;
        z-index: 10000;
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        transform: translateX(400px);
        transition: transform 0.3s ease;
        max-width: 300px;
    `;
    toast.textContent = message;
    
    document.body.appendChild(toast);
    
    // Trigger animation
    setTimeout(() => {
        toast.style.transform = 'translateX(0)';
    }, 10);
    
    // Remove toast after 4 seconds
    setTimeout(() => {
        toast.style.transform = 'translateX(400px)';
        setTimeout(() => {
            document.body.removeChild(toast);
        }, 300);
    }, 4000);
}

function confirmUpdate() {
    console.log('Form submission - Removed images:', removedImages);
    console.log('Form data being submitted');
    return confirm("Are you sure you want to edit this professional's details?");
}

// Gallery image removal functionality
let removedImages = [];
console.log('Gallery management script loaded');

function removeGalleryImage(imagePath, button) {
    if (confirm('Are you sure you want to remove this image?')) {
        // Add to removed images list
        removedImages.push(imagePath);
        document.getElementById('removed_images').value = JSON.stringify(removedImages);
        
        // Remove the gallery item from DOM
        button.closest('.gallery-item').remove();
        
        // Show message if no images left
        const galleryGrid = document.querySelector('.gallery-grid');
        if (galleryGrid && galleryGrid.children.length === 0) {
            galleryGrid.parentElement.innerHTML = `
                <div style="text-align: center; padding: 2rem; background: #f9fafb; border-radius: 8px; color: #6b7280;">
                    <i class="fas fa-images" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5;"></i>
                    <p style="margin: 0; font-size: 1.1rem;">No gallery images have been uploaded yet.</p>
                </div>
            `;
        }
    }
}

// File upload preview (optional enhancement)
document.getElementById('gallery_images').addEventListener('change', function(e) {
    const files = e.target.files;
    if (files.length > 5) {
        alert('You can upload maximum 5 images at once.');
        e.target.value = '';
        return;
    }
    
    // Optional: Show preview of selected files
    console.log(`Selected ${files.length} images for upload`);
});
</script>
@endsection