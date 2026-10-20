@props([
    'errors' => $errors,
    'title' => 'Please correct the following errors:',
])

@if ($errors->any())
    <div {{ $attributes->class(['yk-inline-alert', 'yk-inline-alert--error']) }} role="alert" aria-labelledby="yk-validation-summary-title">
        <div class="yk-inline-alert__content">
            <p id="yk-validation-summary-title" class="yk-inline-alert__title">{{ $title }}</p>
            <ul class="yk-validation-summary__list">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif
