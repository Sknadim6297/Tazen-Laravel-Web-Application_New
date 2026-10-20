<div data-yuktione-feedback class="yk-feedback">
    <div class="yk-toast-region" data-yuktione-toast-region aria-label="Notifications"></div>
    <div data-yuktione-flashes hidden>
        @if (session()->has('password_reset_requested'))
            <span data-yuktione-flash="info" data-yuktione-flash-title="Password reset requested">{{ session('password_reset_requested') }}</span>
        @endif
        @foreach (['success', 'error', 'warning', 'info'] as $type)
            @if (session()->has($type))
                <span data-yuktione-flash="{{ $type }}">{{ session($type) }}</span>
            @endif
        @endforeach
    </div>
</div>
