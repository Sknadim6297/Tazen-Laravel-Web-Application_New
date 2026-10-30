<svg viewBox="0 0 64 64" focusable="false">
    <circle cx="32" cy="32" r="22"></circle>
    <path d="M22 28h.1M42 28h.1"></path>
    @if ($mood === 'very-happy')
        <path d="M20 36c3.2 6 8 9 12 9s8.8-3 12-9"></path>
    @elseif ($mood === 'good')
        <path d="M22 38c2.6 4 6 6 10 6s7.4-2 10-6"></path>
    @elseif ($mood === 'neutral')
        <path d="M22 40h20"></path>
    @elseif ($mood === 'sad')
        <path d="M22 44c2.8-4 6.4-6 10-6s7.2 2 10 6"></path>
    @else
        <path d="M22 42c3-2 5 2 8 0s5-2 8 0"></path>
        <path d="M20 22l4 3M44 22l-4 3"></path>
    @endif
</svg>
