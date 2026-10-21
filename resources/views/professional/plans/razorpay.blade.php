@extends('professional.layout.layout')

@section('styles')
@vite('resources/css/tazen.css')
@endsection

@section('content')
<div class="payment-page flex min-h-full items-center justify-center bg-slate-50 px-4 py-10 text-slate-900">
    <div class="payment-shell w-full max-w-xl">
        <div class="payment-card space-y-6 rounded-2xl border border-brand-100 bg-white p-6 text-center shadow-card sm:p-9">
            <div class="payment-icon mx-auto grid h-16 w-16 place-items-center rounded-2xl bg-brand-50 text-2xl text-brand-700">
                <i class="fas fa-credit-card"></i>
            </div>

            <h1 class="payment-title text-2xl font-bold text-brand-950">Complete Your Payment</h1>
            <p class="payment-subtitle text-slate-600">You're just one step away from activating your plan</p>

            <div class="payment-details space-y-3 rounded-xl border border-brand-100 bg-brand-50/50 p-5 text-left">
                <div class="payment-row">
                    <span class="payment-label">Plan Name:</span>
                    <span class="payment-value">{{ $purchase->plan_name }}</span>
                </div>
                <div class="payment-row">
                    <span class="payment-label">Lead Limit:</span>
                    <span class="payment-value">{{ $purchase->lead_limit }} leads</span>
                </div>
                @if($purchase->end_date)
                <div class="payment-row">
                    <span class="payment-label">Validity:</span>
                    <span class="payment-value">{{ \Carbon\Carbon::parse($purchase->start_date)->diffInDays($purchase->end_date) }} days</span>
                </div>
                @endif
                <div class="payment-row">
                    <span class="payment-label">Amount:</span>
                    <span class="payment-value">{{ $purchase->formatted_price }}</span>
                </div>
            </div>

            <button type="button" id="razorpay-button" class="payment-button">
                <i class="fas fa-lock"></i> Pay Securely
            </button>

            <a href="{{ route('professional.plans.index') }}" class="cancel-link">
                <i class="fas fa-arrow-left"></i> Cancel Payment
            </a>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const button = document.getElementById('razorpay-button');
    
    const options = {
        key: '{{ $razorpayKey }}',
        amount: {{ $order['amount'] }},
        currency: '{{ $order['currency'] }}',
        name: 'Professional Plan',
        description: '{{ $purchase->plan_name }} Plan Purchase',
        order_id: '{{ $order['id'] }}',
        handler: function (response) {
            // Show loading state
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verifying Payment...';
            
            // Send payment details to server for verification
            fetch('{{ route("professional.plans.razorpay-callback") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    razorpay_payment_id: response.razorpay_payment_id,
                    razorpay_order_id: response.razorpay_order_id,
                    razorpay_signature: response.razorpay_signature,
                    purchase_id: {{ $purchase->id }}
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success || data.status === 'success') {
                    // Redirect to success page
                    window.location.href = '{{ route("professional.plans.success", $purchase->id) }}';
                } else {
                    alert('Payment verification failed. Please contact support.');
                    button.disabled = false;
                    button.innerHTML = '<i class="fas fa-lock"></i> Pay Securely';
                }
            })
            .catch(error => {
                console.error('Payment verification error:', error);
                alert('An error occurred during payment verification. Please contact support.');
                button.disabled = false;
                button.innerHTML = '<i class="fas fa-lock"></i> Pay Securely';
            });
        },
        prefill: {
            name: '{{ Auth::user()->name }}',
            email: '{{ Auth::user()->email }}',
            contact: '{{ Auth::user()->phone ?? '' }}'
        },
        notes: {
            purchase_id: '{{ $purchase->id }}',
            plan_name: '{{ $purchase->plan_name }}'
        },
        theme: {
            color: '#168a80'
        },
        modal: {
            ondismiss: function() {
                button.disabled = false;
                console.log('Payment cancelled by user');
            }
        }
    };
    
    const rzp = new Razorpay(options);
    
    rzp.on('payment.failed', function (response){
        console.error('Payment failed:', response.error);
        alert('Payment failed: ' + response.error.description);
        button.disabled = false;
    });
    
    button.addEventListener('click', function(e) {
        e.preventDefault();
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Opening Payment Gateway...';
        
        try {
            rzp.open();
        } catch (error) {
            console.error('Razorpay error:', error);
            alert('Failed to open payment gateway. Please try again.');
            button.disabled = false;
            button.innerHTML = '<i class="fas fa-lock"></i> Pay Securely';
        }
    });
    
    // Auto-trigger payment after 1 second
    setTimeout(function() {
        if (!button.disabled) {
            button.click();
        }
    }, 1000);
});
</script>
@endsection
