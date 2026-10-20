@extends('professional.layout.layout')

@section('styles')
@vite('resources/css/tazen.css')
<style>/* Custom Modal Styles */
    .custom-modal-overlay{display:none}.custom-modal-overlay.active{display:flex;opacity:1}.image-preview-custom{display:none}</style>
@endsection

@section('content')
<div class="content-wrapper purchase-page min-h-full bg-slate-50 px-4 py-6 text-slate-900 sm:px-6 lg:px-8">
    <div class="purchase-shell mx-auto max-w-5xl space-y-6">
        <!-- Page Header -->
        <div class="purchase-header rounded-2xl border border-brand-100 bg-gradient-to-br from-white via-brand-50 to-white p-6 shadow-card">
            <h1 class="text-2xl font-bold tracking-tight text-brand-950 sm:text-3xl"><i class="fas fa-shopping-cart"></i> Purchase Confirmation</h1>
        </div>

        <div class="purchase-card space-y-6 rounded-2xl border border-brand-100 bg-white p-5 shadow-card sm:p-7">
            <div class="row">
                <!-- Plan Details -->
                <div class="col-md-6 detail-section">
                    <h5>Plan Details</h5>
                    <table class="detail-table">
                        <tr>
                            <td><strong>Plan Name:</strong></td>
                            <td>{{ $plan->name }}</td>
                        </tr>
                        <tr>
                            <td><strong>Price:</strong></td>
                            <td><h4>{{ $plan->formatted_price }}</h4></td>
                        </tr>
                        <tr>
                            <td><strong>Lead Limit:</strong></td>
                            <td>{{ $plan->lead_limit }} leads</td>
                        </tr>
                        @if($plan->validity_days)
                            <tr>
                                <td><strong>Validity:</strong></td>
                                <td>{{ $plan->validity_days }} days</td>
                            </tr>
                        @endif
                    </table>
                </div>

                <!-- Features -->
                <div class="col-md-6 detail-section">
                    <h5>Features Included:</h5>
                    <ul class="feature-list">
                        @if($plan->features && is_array($plan->features))
                            @foreach($plan->features as $feature)
                                <li>
                                    <i class="fas fa-check-circle"></i>
                                    {{ $feature }}
                                </li>
                            @endforeach
                        @endif
                    </ul>
                </div>
            </div>

            <hr>

            <!-- Error Messages -->
            @if($errors->any())
                <div class="alert alert-danger">
                    <strong><i class="fas fa-exclamation-circle"></i> Error!</strong>
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                </div>
            @endif

            <!-- Payment Form -->
            <form class="space-y-5" action="{{ route('professional.plans.process-purchase', $plan->id) }}" method="POST" enctype="multipart/form-data" id="purchaseForm">
                @csrf
                <div class="payment-options space-y-3">
                    <h5 class="font-semibold text-brand-950">Select Payment Method:</h5>
                    
                    <label class="payment-option flex cursor-pointer items-center gap-4 rounded-xl border border-brand-100 p-4 transition hover:border-brand-300 hover:bg-brand-50/60">
                        <input class="form-check-input accent-brand-700" type="radio" name="payment_method" id="razorpay" value="razorpay" checked required>
                        <div>
                            <i class="fas fa-credit-card"></i>
                        </div>
                        <label class="form-check-label" for="razorpay">
                            <strong>Razorpay</strong><br>
                            <small>Pay with Cards, UPI, or Net Banking</small>
                        </label>
                    </label>
                    
                    <label class="payment-option flex cursor-pointer items-center gap-4 rounded-xl border border-brand-100 p-4 transition hover:border-brand-300 hover:bg-brand-50/60">
                        <input class="form-check-input accent-brand-700" type="radio" name="payment_method" id="manual" value="manual" required>
                        <div>
                            <i class="fas fa-money-bill"></i>
                        </div>
                        <label class="form-check-label" for="manual">
                            <strong>Manual Payment</strong><br>
                            <small>Admin approval required</small>
                        </label>
                    </label>
                </div>

                <!-- Hidden file input - will be populated from modal -->
                <input type="file" name="payment_screenshot" id="payment_screenshot_hidden" style="display: none" accept="image/*">

                <div class="info-alert">
                    <i class="fas fa-info-circle"></i>
                    <span>By clicking "Proceed to Payment", you agree to purchase this plan.</span>
                </div>

                <div class="action-buttons">
                    <button type="button" class="btn-action primary" id="proceedBtn">
                        <i class="fas fa-lock"></i>
                        Proceed to Payment
                    </button>
                    <a href="{{ route('professional.plans.index') }}" class="btn-action secondary">
                        <i class="fas fa-arrow-left"></i>
                        Back to Plans
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Custom Modal for Screenshot Upload -->
<div class="custom-modal-overlay fixed inset-0 z-50 hidden items-center justify-center bg-brand-950/60 p-4 backdrop-blur-sm" id="screenshotModal">
    <div class="custom-modal w-full max-w-lg overflow-hidden rounded-2xl border border-brand-100 bg-white shadow-popover">
        <div class="custom-modal-header flex items-center justify-between border-b border-slate-100 px-5 py-4">
            <h5>
                <i class="fas fa-file-upload"></i> Upload Payment Screenshot
            </h5>
            <button type="button" class="custom-modal-close" id="closeModal">
                <span>&times;</span>
            </button>
        </div>
        <div class="custom-modal-body space-y-4 p-5">
            <div class="alert-info-custom">
                <i class="fas fa-info-circle"></i> (Optional) Upload a screenshot or photo of your payment transaction for verification.
            </div>
            
            <div class="form-group-custom">
                <label for="payment_screenshot">
                    Payment Screenshot <span class="text-muted">(Optional)</span>
                </label>
                <input class="block w-full rounded-lg border border-slate-300 p-2 text-sm file:mr-4 file:rounded-md file:border-0 file:bg-brand-50 file:px-3 file:py-2 file:font-medium file:text-brand-800" type="file" id="payment_screenshot" accept="image/*">
                <small>Accepted formats: JPG, PNG, JPEG (Max: 5MB)</small>
            </div>

            <div id="imagePreview" class="image-preview-custom">
                <label>Preview:</label>
                <img id="previewImage" src="" alt="Preview">
            </div>
        </div>
        <div class="custom-modal-footer flex justify-end gap-3 border-t border-slate-100 px-5 py-4">
            <button type="button" class="modal-btn modal-btn-secondary" id="cancelModal">Cancel</button>
            <button type="button" class="modal-btn modal-btn-primary" id="submitWithScreenshot">
                <i class="fas fa-check"></i> Submit
            </button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    const form = $('#purchaseForm');
    const proceedBtn = $('#proceedBtn');
    const manualRadio = $('#manual');
    const razorpayRadio = $('#razorpay');
    const modal = $('#screenshotModal');
    const screenshotInput = $('#payment_screenshot');
    const hiddenScreenshotInput = $('#payment_screenshot_hidden');
    const previewImage = $('#previewImage');
    const imagePreview = $('#imagePreview');
    const closeModalBtn = $('#closeModal');
    const cancelModalBtn = $('#cancelModal');
    const submitBtn = $('#submitWithScreenshot');

    // Open modal function
    function openModal() {
        modal.addClass('active');
        $('body').css('overflow', 'hidden');
    }

    // Close modal function
    function closeModal() {
        modal.removeClass('active');
        $('body').css('overflow', '');
        // Reset modal
        screenshotInput.val('');
        imagePreview.hide();
        previewImage.attr('src', '');
    }

    // Preview image when selected in modal
    screenshotInput.on('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImage.attr('src', e.target.result);
                imagePreview.show();
            };
            reader.readAsDataURL(file);
        }
    });

    // Handle proceed button click
    proceedBtn.on('click', function() {
        if (manualRadio.is(':checked')) {
            console.log('Manual payment selected - opening modal');
            openModal();
        } else {
            console.log('Razorpay payment selected - submitting form');
            form.submit();
        }
    });

    // Close modal events
    closeModalBtn.on('click', closeModal);
    cancelModalBtn.on('click', closeModal);
    
    // Close on overlay click
    modal.on('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });

    // Close on Escape key
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape' && modal.hasClass('active')) {
            closeModal();
        }
    });

    // Handle submit with screenshot
    submitBtn.on('click', function() {
        const files = screenshotInput[0].files;
        
        // Only validate if a file is selected
        if (files.length > 0) {
            // Validate file size (5MB max)
            const maxSize = 5 * 1024 * 1024; // 5MB in bytes
            if (files[0].size > maxSize) {
                alert('File size must be less than 5MB');
                return;
            }

            // Validate file type
            const validTypes = ['image/jpeg', 'image/jpg', 'image/png'];
            if (!validTypes.includes(files[0].type)) {
                alert('Please upload a valid image file (JPG, JPEG, or PNG)');
                return;
            }
        }

        // Close modal
        closeModal();
        
        // Add a loading state to prevent double submission
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Submitting...');
        proceedBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Submitting...');
        
        console.log('Preparing to submit manual payment');
        
        // Always use FormData for reliable file upload
        const formData = new FormData(form[0]);
        // Replace or add the screenshot file only if one is selected
        if (files.length > 0) {
            formData.set('payment_screenshot', files[0]);
            console.log('Form data prepared with file:', files[0].name);
        } else {
            formData.delete('payment_screenshot');
            console.log('Form data prepared without screenshot');
        }
        
        console.log('Payment method:', formData.get('payment_method'));
        console.log('CSRF Token:', formData.get('_token'));
        
        // Submit using AJAX
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                console.log('Form submitted successfully', response);
                // Redirect to success page
                if (response.redirect) {
                    window.location.href = response.redirect;
                } else if (response.success) {
                    // For non-AJAX responses, reload or redirect
                    window.location.href = '{{ route("professional.plans.index") }}';
                }
            },
            error: function(xhr) {
                console.error('Form submission error:', xhr);
                submitBtn.prop('disabled', false).html('<i class="fas fa-check"></i> Submit');
                proceedBtn.prop('disabled', false).html('<i class="fas fa-lock"></i> Proceed to Payment');
                
                let errorMessage = 'Error submitting form. Please try again.';
                
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = xhr.responseJSON.errors;
                    errorMessage = Object.values(errors).flat().join('\n');
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.status === 419) {
                    errorMessage = 'Your session has expired. Please refresh the page and try again.';
                }
                
                alert(errorMessage);
            }
        });
    });
});
</script>
@endsection
