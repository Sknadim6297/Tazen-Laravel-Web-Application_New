@extends('professional.layout.layout')

@section('styles')
@vite('resources/css/tazen.css')
@endsection

@section('content')
<div class="content-wrapper w-full text-text-primary">
    <div class="availability-page mx-auto w-full max-w-7xl px-4 py-8 text-text-primary sm:px-6 lg:px-8">
        <div class="availability-shell mx-auto w-full space-y-6">
            <header class="availability-header flex flex-col gap-5 rounded-panel border border-border bg-gradient-to-br from-white to-brand-50 p-6 shadow-card sm:flex-row sm:items-center sm:justify-between">
                <div class="hero-meta">
                    <span class="hero-eyebrow inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.16em] text-brand-700"><i class="ri-calendar-line"></i>Availability</span>
                    <h1 class="text-2xl font-bold tracking-tight text-text-primary sm:text-3xl">Add New Availability</h1>
                    <ul class="hero-breadcrumb mt-3 flex flex-wrap items-center gap-2 text-sm text-text-muted">
                        <li><a href="{{ route('professional.dashboard') }}">Home</a></li>
                        <li><a href="{{ route('professional.availability.index') }}">Availability</a></li>
                        <li class="active" aria-current="page">Add New</li>
                    </ul>
                </div>
                <a href="{{ route('professional.availability.index') }}" class="btn inline-flex items-center justify-center gap-2 rounded-control border border-border bg-white px-4 py-2.5 text-sm font-semibold text-text-secondary transition hover:bg-surface-muted">
                    <i class="ri-arrow-left-line"></i>
                    Back to Availability
                </a>
            </header>

            <div id="error-messages"></div>

            <form id="availability-form" class="availability-form">
                @csrf

                <section class="form-card rounded-panel border border-border bg-white p-5 shadow-card sm:p-7">
                    <header>
                        <i class="ri-settings-line"></i>
                        Basic Information
                    </header>
                    <div class="form-grid grid gap-5 md:grid-cols-2">
                        <div class="form-group mb-5 space-y-2">
                            <label for="session_duration" class="block text-sm font-medium text-text-secondary">Session Duration (in minutes)</label>
                            <select name="session_duration" id="session_duration" class="form-control w-full rounded-control border border-border bg-white px-3 py-2.5 text-sm text-text-primary shadow-sm placeholder:text-text-muted focus:border-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-200" required>
                                @foreach([30, 45, 60, 90, 120] as $duration)
                                    <option value="{{ $duration }}" {{ $duration == 30 ? 'selected' : '' }}>
                                        {{ $duration }} {{ $duration == 120 ? 'minutes (2 hours)' : 'minutes' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </section>

                <section class="form-card rounded-panel border border-border bg-white p-5 shadow-card sm:p-7">
                    <header>
                        <i class="ri-calendar-line"></i>
                        Select Months
                    </header>
                    <p class="description my-4 text-sm leading-6 text-text-secondary">Choose the months for which you want to set this availability schedule.</p>
                    <div class="months-grid grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
                        @php
                            $currentYear = date('Y');
                            $currentMonth = date('n');
                            $months = [
                                1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
                                5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
                                9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
                            ];
                            // Calculate 6 months ahead from current date
                            $endDate = strtotime('+6 months');
                            $endYear = date('Y', $endDate);
                            $endMonth = date('n', $endDate);
                        @endphp

                        @for($year = $currentYear; $year <= $endYear; $year++)
                            @foreach($months as $monthNum => $monthName)
                                @php
                                    // Skip past months
                                    if($year == $currentYear && $monthNum < $currentMonth) {
                                        continue;
                                    }
                                    // Skip months beyond 6 months from now
                                    if($year == $endYear && $monthNum > $endMonth) {
                                        continue;
                                    }
                                    // Skip if year is beyond end year
                                    if($year > $endYear) {
                                        continue;
                                    }
                                    $monthValue = sprintf('%04d-%02d', $year, $monthNum);
                                @endphp
                                <div class="month-card flex cursor-pointer items-center gap-3 rounded-control border border-border bg-white p-4 text-sm font-medium text-text-secondary transition hover:border-brand-400 hover:bg-brand-50" onclick="toggleMonth('{{ $monthValue }}')">
                                    <input type="checkbox" name="months[]" value="{{ $monthValue }}" id="month_{{ $monthValue }}" class="h-4 w-4 rounded border-border text-brand-600 focus:ring-brand-500">
                                    <label for="month_{{ $monthValue }}" class="mb-0 block text-sm font-medium text-text-secondary">
                                        {{ $monthName }} {{ $year }}
                                    </label>
                                </div>
                            @endforeach
                        @endfor
                    </div>
                </section>

                <section class="form-card schedule-card rounded-panel border border-border bg-white p-5 shadow-card sm:p-7">
                    <header>
                        <i class="ri-time-line"></i>
                        Weekly Schedule
                    </header>
                    <p class="description my-4 text-sm leading-6 text-text-secondary">Set your available time slots for each day of the week. You can add multiple time slots per day.</p>
                    <div class="weekly-scheduler grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                        @php
                            $days = [
                                'mon' => 'Monday',
                                'tue' => 'Tuesday',
                                'wed' => 'Wednesday',
                                'thu' => 'Thursday',
                                'fri' => 'Friday',
                                'sat' => 'Saturday',
                                'sun' => 'Sunday'
                            ];
                        @endphp

                        @foreach($days as $dayCode => $dayName)
                            <div class="day-column space-y-4 rounded-control border border-border bg-white p-4">
                                <div class="day-header border-b border-divider pb-3 font-semibold text-text-primary">{{ $dayName }}</div>
                                <div class="day-slots" id="slots-{{ $dayCode }}"></div>
                                <div class="add-slot-form space-y-3 rounded-control bg-surface-muted p-3">
                                    <div class="time-inputs grid grid-cols-[1fr_auto_1fr] items-center gap-2">
                                        <input type="time" id="start-{{ $dayCode }}" onchange="calculateEndTime('{{ $dayCode }}')" class="w-full rounded-control border border-border bg-white px-3 py-2.5 text-sm text-text-primary shadow-sm placeholder:text-text-muted focus:border-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-200">
                                        <span>to</span>
                                        <input type="time" id="end-{{ $dayCode }}" readonly class="w-full rounded-control border border-border bg-white px-3 py-2.5 text-sm text-text-primary shadow-sm placeholder:text-text-muted focus:border-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-200">
                                    </div>
                                    <button type="button" class="add-slot-btn inline-flex items-center justify-center gap-2 rounded-control bg-brand-700 px-3 py-2 text-sm font-semibold text-white hover:bg-brand-800" onclick="addSlot('{{ $dayCode }}')">
                                        <i class="ri-add-line"></i>
                                        Add Slot
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>

                <section class="form-actions flex flex-col-reverse gap-3 border-t border-divider pt-5 sm:flex-row sm:justify-end">
                    <button type="submit" class="btn btn-primary inline-flex items-center justify-center gap-2 rounded-control bg-brand-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-800 focus:outline-none focus:ring-2 focus:ring-brand-300">
                        <i class="ri-save-line"></i>Save Availability
                    </button>
                    <a href="{{ route('professional.availability.index') }}" class="btn btn-secondary inline-flex items-center justify-center gap-2 rounded-control border border-border bg-white px-4 py-2.5 text-sm font-semibold text-text-secondary transition hover:bg-surface-muted">
                        <i class="ri-arrow-left-line"></i>Cancel
                    </a>
                </section>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Store weekly slots data
    let weeklySlots = {
        mon: [], tue: [], wed: [], thu: [], fri: [], sat: [], sun: []
    };

    // Toggle month selection
    window.toggleMonth = function(monthValue) {
        const checkbox = document.getElementById('month_' + monthValue);
        const card = checkbox.closest('.month-card');
        
        checkbox.checked = !checkbox.checked;
        card.classList.toggle('selected', checkbox.checked);
        card.classList.toggle('border-brand-600', checkbox.checked);
        card.classList.toggle('bg-brand-50', checkbox.checked);
        card.classList.toggle('bg-white', !checkbox.checked);
        card.classList.toggle('border-border', !checkbox.checked);
        card.classList.toggle('ring-2', checkbox.checked);
    };

    // Calculate end time based on start time and session duration
    window.calculateEndTime = function(day) {
        const startInput = document.getElementById('start-' + day);
        const endInput = document.getElementById('end-' + day);
        const sessionDuration = document.getElementById('session_duration').value;
        
        if (!startInput.value || !sessionDuration) {
            endInput.value = '';
            return;
        }
        
        // Parse start time
        const [hours, minutes] = startInput.value.split(':').map(Number);
        const startDate = new Date();
        startDate.setHours(hours, minutes, 0, 0);
        
        // Add session duration
        const endDate = new Date(startDate.getTime() + parseInt(sessionDuration) * 60000);
        
        // Format end time
        const endHours = String(endDate.getHours()).padStart(2, '0');
        const endMinutes = String(endDate.getMinutes()).padStart(2, '0');
        endInput.value = `${endHours}:${endMinutes}`;
    };

    // Update all end times when session duration changes
    document.getElementById('session_duration').addEventListener('change', function() {
        // Update all start time inputs that have values
        const days = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'];
        days.forEach(day => {
            const startInput = document.getElementById('start-' + day);
            if (startInput.value) {
                calculateEndTime(day);
            }
        });
    });

    // Add time slot to a specific day
    window.addSlot = function(day) {
        const startInput = document.getElementById('start-' + day);
        const endInput = document.getElementById('end-' + day);
        
        const startTime = startInput.value;
        const endTime = endInput.value;
        
        if (!startTime || !endTime) {
            alert('Please select both start and end times');
            return;
        }
        
        if (startTime >= endTime) {
            alert('Start time must be before end time');
            return;
        }
        
        // Add to data structure
        weeklySlots[day].push({
            start_time: startTime,
            end_time: endTime
        });
        
        // Update UI
        renderDaySlots(day);
        
        // Clear inputs
        startInput.value = '';
        endInput.value = '';
    };

    // Remove slot from a specific day
    window.deleteSlot = function(day, index) {
        weeklySlots[day].splice(index, 1);
        renderDaySlots(day);
    };

    // Render slots for a specific day
    function renderDaySlots(day) {
        const container = document.getElementById('slots-' + day);
        container.innerHTML = '';
        
        weeklySlots[day].forEach((slot, index) => {
            const slotDiv = document.createElement('div');
            slotDiv.className = 'slot-item';
            slotDiv.innerHTML = `
                <span class="slot-time font-medium text-text-primary">${formatTime(slot.start_time)} - ${formatTime(slot.end_time)}</span>
                <button type="button" class="delete-slot inline-flex h-8 w-8 items-center justify-center rounded-full text-error hover:bg-error-surface" onclick="deleteSlot('${day}', ${index})">
                    <i class="ri-delete-bin-line"></i>
                </button>
            `;
            container.appendChild(slotDiv);
        });
    }

    // Format time for display
    function formatTime(time) {
        return new Date('2000-01-01 ' + time).toLocaleTimeString('en-US', {
            hour: 'numeric',
            minute: '2-digit',
            hour12: true
        });
    }

    // Form submission
    document.getElementById('availability-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Validate form
        const selectedMonths = document.querySelectorAll('input[name="months[]"]:checked');
        if (selectedMonths.length === 0) {
            showError('Please select at least one month');
            return;
        }
        
        // Check if any slots are added
        const totalSlots = Object.values(weeklySlots).reduce((total, daySlots) => total + daySlots.length, 0);
        if (totalSlots === 0) {
            showError('Please add at least one time slot');
            return;
        }
        
        // Prepare data
        const formData = new FormData(this);
        
        // Convert weeklySlots to the format expected by backend
        const slotsArray = [];
        Object.keys(weeklySlots).forEach(day => {
            weeklySlots[day].forEach(slot => {
                slotsArray.push({
                    weekday: day,
                    start_time: slot.start_time,
                    end_time: slot.end_time
                });
            });
        });
        
        formData.append('weekly_slots', JSON.stringify(slotsArray));
        
        // Submit form
        fetch('{{ route("professional.availability.store") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccess(data.message);
                setTimeout(() => {
                    window.location.href = data.redirect || '{{ route("professional.availability.index") }}';
                }, 1500);
            } else {
                if (data.errors) {
                    showError(data.errors.join('<br>'));
                } else {
                    showError(data.message || 'An error occurred');
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showError('An error occurred while saving availability');
        });
    });

    function showError(message) {
        const errorContainer = document.getElementById('error-messages');
        errorContainer.innerHTML = `
            <div class="alert alert-danger alert-dismissible fade show rounded-control border border-error/20 bg-error-surface p-4 text-sm text-error relative" role="alert">
                ${message}
                <button type="button" class="btn-close inline-flex h-8 w-8 items-center justify-center rounded-full text-text-muted hover:bg-surface-muted after:content-['×'] after:text-xl" data-bs-dismiss="alert"></button>
            </div>
        `;
        errorContainer.scrollIntoView({ behavior: 'smooth' });
    }

    function showSuccess(message) {
        const errorContainer = document.getElementById('error-messages');
        errorContainer.innerHTML = `
            <div class="alert alert-success alert-dismissible fade show rounded-control border border-success/20 bg-success-surface p-4 text-sm text-success relative" role="alert">
                ${message}
                <button type="button" class="btn-close inline-flex h-8 w-8 items-center justify-center rounded-full text-text-muted hover:bg-surface-muted after:content-['×'] after:text-xl" data-bs-dismiss="alert"></button>
            </div>
        `;
        errorContainer.scrollIntoView({ behavior: 'smooth' });
    }
});
</script>
@endsection