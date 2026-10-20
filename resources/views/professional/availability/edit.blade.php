@extends('professional.layout.layout')

@section('styles')
@vite('resources/css/tazen.css')
@endsection

@section('title', 'Edit Availability')

@section('content')
<div class="content-wrapper w-full text-text-primary">
    <div class="availability-page mx-auto w-full max-w-7xl px-4 py-8 text-text-primary sm:px-6 lg:px-8">
        <div class="availability-shell mx-auto w-full space-y-6">
            <header class="availability-header flex flex-col gap-5 rounded-panel border border-border bg-gradient-to-br from-white to-brand-50 p-6 shadow-card sm:flex-row sm:items-center sm:justify-between">
                <div class="header-content">
                    <h1 class="text-2xl font-bold tracking-tight text-text-primary sm:text-3xl">Edit Availability</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 flex flex-wrap items-center gap-2 text-sm text-text-muted">
                            <li class="breadcrumb-item"><a href="{{ route('professional.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('professional.availability.index') }}">Availability</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Edit</li>
                        </ol>
                    </nav>
                </div>
                <div class="header-actions">
                    <a href="{{ route('professional.availability.index') }}" class="btn inline-flex items-center justify-center gap-2 rounded-control border border-border bg-white px-4 py-2.5 text-sm font-semibold text-text-secondary transition hover:bg-surface-muted">
                        <i class="ri-arrow-left-line"></i>
                        Back to Availability
                    </a>
                </div>
            </header>

            <form id="editAvailabilityForm" class="availability-form">
                @csrf
                @method('PUT')

                <section class="form-card rounded-panel border border-border bg-white p-5 shadow-card sm:p-7">
                    <header>
                        <i class="ri-calendar-line"></i>
                        Editing Month
                    </header>
                    <p class="description my-4 text-sm leading-6 text-text-secondary">You are editing availability for <strong>{{ date('F Y', strtotime($availability->month . '-01')) }}</strong>. This will only update this specific month.</p>
                    <input type="hidden" name="months[]" value="{{ $availability->month }}">
                </section>

                <section class="form-card rounded-panel border border-border bg-white p-5 shadow-card sm:p-7">
                    <header>
                        <i class="ri-settings-line"></i>
                        Session Settings
                    </header>
                    <div class="form-grid grid gap-5 md:grid-cols-2">
                        <div class="form-group mb-5 space-y-2">
                            <label for="session_duration" class="block text-sm font-medium text-text-secondary">Session Duration (minutes)</label>
                            <select class="form-select w-full rounded-control border border-border bg-white px-3 py-2.5 text-sm text-text-primary shadow-sm focus:border-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-200" id="session_duration" name="session_duration" required>
                                @foreach([30, 45, 60, 90, 120] as $duration)
                                    <option value="{{ $duration }}" {{ $availability->session_duration == $duration ? 'selected' : '' }}>
                                        {{ $duration }} {{ $duration == 120 ? 'minutes (2 hours)' : 'minutes' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </section>

                <section class="form-card schedule-card rounded-panel border border-border bg-white p-5 shadow-card sm:p-7">
                    <header>
                        <i class="ri-time-line"></i>
                        Weekly Schedule
                    </header>
                    <p class="description my-4 text-sm leading-6 text-text-secondary">Adjust your available time slots for each day. Add or remove slots as needed.</p>
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
                                <div class="day-slots" id="slots_{{ $dayCode }}"></div>
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

                        <div class="add-for-all-wrap">
                            <button type="button" class="add-for-all-btn" onclick="showAddForAllModal()">
                                <i class="ri-calendar-event-line"></i>
                                Add Slot To All Days
                            </button>
                        </div>
                    </div>
                </section>

                <section class="form-actions flex flex-col-reverse gap-3 border-t border-divider pt-5 sm:flex-row sm:justify-end">
                    <button type="submit" class="btn btn-primary inline-flex items-center justify-center gap-2 rounded-control bg-brand-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-800 focus:outline-none focus:ring-2 focus:ring-brand-300">
                        <i class="ri-save-line"></i> Update Availability
                    </button>
                    <a href="{{ route('professional.availability.index') }}" class="btn btn-secondary inline-flex items-center justify-center gap-2 rounded-control border border-border bg-white px-4 py-2.5 text-sm font-semibold text-text-secondary transition hover:bg-surface-muted">
                        <i class="ri-close-line"></i> Cancel
                    </a>
                </section>
            </form>
        </div>
    </div>
</div>

<!-- Add for All Days Modal -->
<div class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/40 p-4" id="addForAllModal" tabindex="-1" role="dialog" aria-modal="true" aria-labelledby="addForAllModalLabel" aria-hidden="true">
    <div class="mx-auto w-full max-w-lg">
        <div class="overflow-hidden rounded-panel border border-border bg-white shadow-popover">
            <div class="flex items-center justify-between border-b border-divider p-5">
                <h5 class="modal-title text-lg font-semibold text-text-primary" id="addForAllModalLabel">Add Time Slot for All Days</h5>
                <button type="button" class="inline-flex h-8 w-8 items-center justify-center rounded-full text-text-muted hover:bg-surface-muted after:content-['×'] after:text-xl" data-modal-dismiss aria-label="Close"></button>
            </div>
            <div class="p-5">
                <div class="form-group mb-5 space-y-2">
                    <label for="allDaysTime" class="block text-sm font-medium text-text-secondary">Start Time</label>
                    <input type="time" class="form-control w-full rounded-control border border-border bg-white px-3 py-2.5 text-sm text-text-primary shadow-sm placeholder:text-text-muted focus:border-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-200" id="allDaysTime" required>
                </div>
                <p class="description my-4 text-sm leading-6 text-text-secondary">End time will be calculated automatically based on session duration.</p>
            </div>
            <div class="flex flex-wrap justify-end gap-3 border-t border-divider p-5">
                <button type="button" class="btn btn-secondary inline-flex items-center justify-center gap-2 rounded-control border border-border bg-white px-4 py-2.5 text-sm font-semibold text-text-secondary transition hover:bg-surface-muted" data-modal-dismiss>Cancel</button>
                <button type="button" class="btn btn-primary inline-flex items-center justify-center gap-2 rounded-control bg-brand-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-800 focus:outline-none focus:ring-2 focus:ring-brand-300" onclick="applyToAllDays()">Add to All Days</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let weeklySlots = { mon: [], tue: [], wed: [], thu: [], fri: [], sat: [], sun: [] };
    let slotIdCounter = 0;
    let selectedMonths = [];
    const addForAllModal = document.getElementById('addForAllModal');
    let addForAllOpener = null;

    function hideAddForAllModal() {
        addForAllModal.classList.add('hidden');
        addForAllModal.classList.remove('flex');
        addForAllModal.setAttribute('aria-hidden', 'true');
        if (addForAllOpener) addForAllOpener.focus();
    }

    addForAllModal.querySelectorAll('[data-modal-dismiss]').forEach(button => {
        button.addEventListener('click', hideAddForAllModal);
    });

    addForAllModal.addEventListener('click', function(event) {
        if (event.target === addForAllModal) hideAddForAllModal();
    });

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && !addForAllModal.classList.contains('hidden')) {
            hideAddForAllModal();
        }
    });

    const availabilityData = @json($availability);
    selectedMonths = availabilityData.month ? [availabilityData.month] : [];

    loadExistingSlots();

    window.calculateEndTime = function(day) {
        const startInput = document.getElementById('start-' + day);
        const endInput = document.getElementById('end-' + day);
        const sessionDuration = document.getElementById('session_duration').value;
        if (!startInput.value || !sessionDuration) {
            endInput.value = '';
            return;
        }
        const [hours, minutes] = startInput.value.split(':').map(Number);
        const startDate = new Date();
        startDate.setHours(hours, minutes, 0, 0);
        const endDate = new Date(startDate.getTime() + parseInt(sessionDuration) * 60000);
        endInput.value = `${String(endDate.getHours()).padStart(2, '0')}:${String(endDate.getMinutes()).padStart(2, '0')}`;
    };

    document.getElementById('session_duration').addEventListener('change', function() {
        ['mon','tue','wed','thu','fri','sat','sun'].forEach(day => {
            const startInput = document.getElementById('start-' + day);
            if (startInput.value) calculateEndTime(day);
        });
    });

    window.addSlot = function(day) {
        const startInput = document.getElementById('start-' + day);
        const endInput = document.getElementById('end-' + day);
        const sessionDuration = document.getElementById('session_duration').value;
        if (!startInput.value || !sessionDuration) {
            alert('Please select start time and session duration');
            return;
        }
        const startTime = formatTimeToHHMM(startInput.value);
        const endTime = formatTimeToHHMM(endInput.value);
        if (!startTime || !endTime) {
            alert('Invalid time format');
            return;
        }
        const hasOverlap = weeklySlots[day].some(slot => {
            const newStart = new Date(`1/1/2023 ${startTime}`);
            const newEnd = new Date(`1/1/2023 ${endTime}`);
            const existingStart = new Date(`1/1/2023 ${slot.start_time}`);
            const existingEnd = new Date(`1/1/2023 ${slot.end_time}`);
            return (newStart < existingEnd && newEnd > existingStart);
        });
        if (hasOverlap) {
            alert('This time slot overlaps with an existing slot');
            return;
        }
        const newSlot = {
            id: ++slotIdCounter,
            start_time: startTime,
            end_time: endTime,
            weekday: day
        };
        weeklySlots[day].push(newSlot);
        weeklySlots[day].sort((a, b) => new Date(`1/1/2023 ${a.start_time}`) - new Date(`1/1/2023 ${b.start_time}`));
        renderDaySlots(day);
        startInput.value = '';
        endInput.value = '';
    };

    window.deleteSlot = function(day, slotId) {
        if (confirm('Are you sure you want to delete this time slot?')) {
            weeklySlots[day] = weeklySlots[day].filter(slot => slot.id !== slotId);
            renderDaySlots(day);
        }
    };

    window.showAddForAllModal = function() {
        document.getElementById('allDaysTime').value = '';
        addForAllOpener = document.activeElement;
        addForAllModal.classList.remove('hidden');
        addForAllModal.classList.add('flex');
        addForAllModal.setAttribute('aria-hidden', 'false');
        document.getElementById('allDaysTime').focus();
    };

    window.applyToAllDays = function() {
        const timeValue = document.getElementById('allDaysTime').value.trim();
        const sessionDuration = document.getElementById('session_duration').value;
        if (!timeValue || !sessionDuration) {
            alert('Please select start time and session duration');
            return;
        }
        const startTime = formatTimeToHHMM(timeValue);
        const [hours, minutes] = startTime.split(':').map(Number);
        const startDate = new Date();
        startDate.setHours(hours, minutes, 0, 0);
        const endDate = new Date(startDate.getTime() + parseInt(sessionDuration) * 60000);
        const endTime = `${String(endDate.getHours()).padStart(2, '0')}:${String(endDate.getMinutes()).padStart(2, '0')}`;
        const days = ['mon','tue','wed','thu','fri','sat','sun'];
        let addedCount = 0;
        let skippedCount = 0;
        days.forEach(day => {
            const hasOverlap = weeklySlots[day].some(slot => {
                const newStart = new Date(`1/1/2023 ${startTime}`);
                const newEnd = new Date(`1/1/2023 ${endTime}`);
                const existingStart = new Date(`1/1/2023 ${slot.start_time}`);
                const existingEnd = new Date(`1/1/2023 ${slot.end_time}`);
                return (newStart < existingEnd && newEnd > existingStart);
            });
            if (!hasOverlap) {
                const newSlot = {
                    id: ++slotIdCounter,
                    start_time: startTime,
                    end_time: endTime,
                    weekday: day
                };
                weeklySlots[day].push(newSlot);
                weeklySlots[day].sort((a, b) => new Date(`1/1/2023 ${a.start_time}`) - new Date(`1/1/2023 ${b.start_time}`));
                renderDaySlots(day);
                addedCount++;
            } else {
                skippedCount++;
            }
        });
        hideAddForAllModal();
        if (addedCount > 0) alert(`Added slot to ${addedCount} days`);
        if (skippedCount > 0) alert(`Skipped ${skippedCount} days due to overlapping slots`);
    };

    function renderDaySlots(day) {
        const container = document.getElementById(`slots_${day}`);
        const slots = weeklySlots[day];
        if (slots.length === 0) {
            container.innerHTML = '<div class="text-muted small text-text-muted">No slots added</div>';
            return;
        }
        container.innerHTML = slots.map(slot => `
            <div class="slot-item flex items-center justify-between gap-3 rounded-control border border-border bg-brand-50 px-3 py-2">
                <span class="slot-time font-medium text-text-primary">${slot.start_time} - ${slot.end_time}</span>
                <button type="button" class="delete-slot inline-flex h-8 w-8 items-center justify-center rounded-full text-error hover:bg-error-surface" onclick="deleteSlot('${day}', ${slot.id})">
                    <i class="ri-delete-bin-line"></i>
                </button>
            </div>
        `).join('');
    }

    function loadExistingSlots() {
        if (availabilityData.availability_slots) {
            availabilityData.availability_slots.forEach(slot => {
                const startTime = formatTimeToHHMM(slot.start_time);
                const endTime = formatTimeToHHMM(slot.end_time);
                const newSlot = {
                    id: ++slotIdCounter,
                    start_time: startTime,
                    end_time: endTime,
                    weekday: slot.weekday
                };
                weeklySlots[slot.weekday].push(newSlot);
            });
            ['mon','tue','wed','thu','fri','sat','sun'].forEach(day => renderDaySlots(day));
        }
    }

    function formatTimeToHHMM(timeString) {
        if (!timeString) return '';
        const cleanTime = timeString.toString().trim();
        if (/^\d{1,2}:\d{2}$/.test(cleanTime)) {
            const [hours, minutes] = cleanTime.split(':');
            return `${String(hours).padStart(2, '0')}:${minutes}`;
        }
        if (/^\d{1,2}:\d{2}:\d{2}$/.test(cleanTime)) {
            const [hours, minutes] = cleanTime.split(':');
            return `${String(hours).padStart(2, '0')}:${minutes}`;
        }
        const date = new Date(`1970-01-01T${cleanTime}`);
        if (!isNaN(date.getTime())) {
            return `${String(date.getHours()).padStart(2,'0')}:${String(date.getMinutes()).padStart(2,'0')}`;
        }
        return cleanTime;
    }

    function showLoading() {
        if (!document.getElementById('loadingOverlay')) {
            const overlay = document.createElement('div');
            overlay.id = 'loadingOverlay';
            overlay.className = 'loading-overlay';
            overlay.innerHTML = `
                <div class="loading-spinner mx-auto h-10 w-10 animate-spin rounded-full border-4 border-brand-200 border-t-brand-700">
                    <div class="spinner-border text-primary text-brand-700" role="status"><span class="visually-hidden">Loading...</span></div>
                    <div class="mt-2">Updating availability...</div>
                </div>`;
            document.body.appendChild(overlay);
        }
        document.getElementById('loadingOverlay').style.display = 'flex';
    }

    function hideLoading() {
        const overlay = document.getElementById('loadingOverlay');
        if (overlay) overlay.style.display = 'none';
    }

    document.getElementById('editAvailabilityForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const totalSlots = Object.values(weeklySlots).reduce((sum, daySlots) => sum + daySlots.length, 0);
        if (totalSlots === 0) {
            alert('Please add at least one time slot');
            return;
        }
        const sessionDuration = document.getElementById('session_duration').value;
        if (!sessionDuration) {
            alert('Please select session duration');
            return;
        }
        const formData = new FormData();
        formData.append('_token', document.querySelector('input[name="_token"]').value);
        formData.append('_method', 'PUT');
        formData.append('session_duration', sessionDuration);
        formData.append('months[]', '{{ $availability->month }}');
        const slotsArray = [];
        Object.keys(weeklySlots).forEach(day => {
            weeklySlots[day].forEach(slot => {
                slotsArray.push({
                    weekday: day,
                    start_time: formatTimeToHHMM(slot.start_time),
                    end_time: formatTimeToHHMM(slot.end_time)
                });
            });
        });
        formData.append('weekly_slots', JSON.stringify(slotsArray));
        showLoading();
        fetch(`{{ route('professional.availability.update', $availability->id) }}`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            hideLoading();
            if (data.success) {
                alert(data.message);
                window.location.href = "{{ route('professional.availability.index') }}";
            } else {
                alert(data.message || 'An error occurred. Please try again.');
            }
        })
        .catch(error => {
            hideLoading();
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
    });
});
</script>
@endsection