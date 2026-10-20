@extends('professional.layout.layout')

@section('styles')
@vite('resources/css/tazen.css')
<style>.custom-modal,
    .upload-modal{display:none}</style>
@endsection


@section('content')
<div class="content-wrapper bookings-page min-h-full bg-slate-50 px-4 py-6 text-slate-900 sm:px-6 lg:px-8">
    <div class="bookings-shell mx-auto max-w-7xl space-y-6">
        <header class="bookings-header flex flex-col gap-5 rounded-2xl border border-brand-100 bg-gradient-to-br from-white via-brand-50 to-white p-6 shadow-card sm:flex-row sm:items-center sm:justify-between">
            <div class="hero-meta">
                <span class="hero-eyebrow"><i class="fas fa-calendar-check"></i>Bookings</span>
                <h1 class="mt-2 text-2xl font-bold tracking-tight text-brand-950 sm:text-3xl">All Bookings</h1>
                <ul class="hero-breadcrumb">
                    <li><a href="{{ route('professional.dashboard') }}">Home</a></li>
                    <li class="active" aria-current="page">Bookings</li>
                </ul>
            </div>
            <div class="header-actions">
                <a href="{{ route('professional.dashboard') }}" class="btn">
                    <i class="fas fa-arrow-left"></i>
                    Back to Dashboard
                </a>
            </div>
        </header>

        <section class="filter-card overflow-hidden rounded-2xl border border-brand-100 bg-white p-5 shadow-card">
            <h3 class="mb-4 text-lg font-semibold text-brand-950">Filter Bookings</h3>
            <form action="{{ route('professional.booking.index') }}" method="GET" class="search-form grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                <div class="form-group space-y-1">
                    <label for="plan_type">Plan Type</label>
                    <select class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-800 shadow-sm focus:border-brand-500 focus:outline-none" name="plan_type" id="plan_type">
                        <option value="">-- Select Plan --</option>
                        @foreach($planTypes as $type)
                            <option value="{{ $type }}" {{ request('plan_type') == $type ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $type)) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group space-y-1">
                    <label for="status">Status</label>
                    <select class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-800 shadow-sm focus:border-brand-500 focus:outline-none" name="status" id="status">
                        <option value="">All Status</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    </select>
                </div>

                <div class="form-group space-y-1">
                    <label for="search_name">Search</label>
                    <input class="w-full rounded-lg border border-slate-300 px-3 py-2 text-slate-800 shadow-sm placeholder:text-slate-400 focus:border-brand-500 focus:outline-none" type="text" name="search_name" id="search_name" value="{{ request('search_name') }}" placeholder="Customer or Service Name">
                </div>

                <div class="form-group space-y-1">
                    <label for="search_date_from">From Date</label>
                    <input class="w-full rounded-lg border border-slate-300 px-3 py-2 text-slate-800 shadow-sm focus:border-brand-500 focus:outline-none" type="date" name="search_date_from" value="{{ request('search_date_from') }}">
                </div>

                <div class="form-group space-y-1">
                    <label for="search_date_to">To Date</label>
                    <input class="w-full rounded-lg border border-slate-300 px-3 py-2 text-slate-800 shadow-sm focus:border-brand-500 focus:outline-none" type="date" name="search_date_to" value="{{ request('search_date_to') }}">
                </div>

                <div class="search-buttons flex flex-wrap items-end gap-3">
                    <button type="submit" class="btn btn-primary inline-flex items-center gap-2 rounded-lg bg-brand-700 px-4 py-2 font-semibold text-white hover:bg-brand-800">
                        <i class="fas fa-search"></i>
                        Search
                    </button>
                    <a href="{{ route('professional.booking.index') }}" class="btn btn-secondary inline-flex items-center gap-2 rounded-lg border border-slate-300 px-4 py-2 font-semibold text-slate-700 hover:bg-slate-50">
                        <i class="fas fa-undo"></i>
                        Reset
                    </a>
                </div>
            </form>
        </section>

        <section class="bookings-card overflow-hidden rounded-2xl border border-brand-100 bg-white shadow-card">
            <div class="bookings-card__head border-b border-slate-100 px-5 py-4">
                <h3 class="text-lg font-semibold text-brand-950">Bookings Overview</h3>
                <span class="text-muted">Monitor customer bookings, manage slots, and access documents from a single place.</span>
            </div>
            <div class="bookings-card__body p-5">
                <div class="table-wrapper overflow-x-auto">
                    @if($bookings->isEmpty())
                        <div class="empty-state">
                            <i class="fas fa-calendar-times"></i>
                            <p>No bookings available at the moment.</p>
                        </div>
                    @else
                        <table class="data-table w-full min-w-[1100px] border-collapse text-left text-sm">
                            <thead>
                                <tr>
                                    <th>Customer Name</th>
                                    <th>Plan Type</th>
                                    <th>Service Name</th>
                                    <th>Sub-Service</th>
                                    <th>Time Slot</th>
                                    <th>Booking Date</th>
                                    <th>Meeting Link</th>
                                    <th>Details</th>
                                    <th>Status</th>
                                    <th>Admin Remarks</th>
                                    <th>Upload Documents (PDF)</th>
                                    <th>Customer Document</th>
                                    <th>Chat</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($bookings as $booking)
                                    @php
                                        $earliestTimedate = $booking->timedates && $booking->timedates->count() > 0 
                                            ? $booking->timedates
                                                ->filter(function($timedate) {
                                                    return \Carbon\Carbon::parse($timedate->date)->isFuture();
                                                })
                                                ->sortBy('date')
                                                ->first()
                                            : null;
                                        $allSlots = $booking->timedates;
                                        $allCompleted = $allSlots->count() > 0 && $allSlots->every(function($timedate) {
                                            return $timedate->status === 'completed';
                                        });
                                    @endphp
                                    <tr>
                                        <td>
                                            {{ $booking->customer_name }}
                       				        <button class="btn btn-info btn-sm questionnaire-info" data-booking-id="{{ $booking->id }}" title="View Questionnaire Answers">
                                                <i class="fas fa-info-circle"></i>
                                            </button>
                                        </td>
                                        <td>{{ $booking->plan_type }}</td>
                                        <td>{{ $booking->service_name }}</td>
                                        <td>
                                            @if($booking->sub_service_name)
                                                <span class="badge bg-info">{{ $booking->sub_service_name }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>{!! $earliestTimedate ? str_replace(',', '<br>', $earliestTimedate->time_slot) : '-' !!}</td>
                                        <td>{{ \Carbon\Carbon::parse($booking->booking_date)->format('d-m-Y') }}</td>
                                        <td>
                                            @if($booking->meeting_link)
                                                <a href="{{ $booking->meeting_link }}" class="btn btn-link" target="_blank">
                                                    <i class="fas fa-video"></i> Join Meeting
                                                </a>
                                            @elseif($booking->timedates && $booking->timedates->count() > 0 && $booking->timedates->first()->meeting_link)
                                                <a href="{{ $booking->timedates->first()->meeting_link }}" class="btn btn-link" target="_blank">
                                                    <i class="fas fa-video"></i> Join Meeting
                                                </a>
                                            @else
                                                <span class="badge bg-warning">Meeting link not given by admin</span>
                                            @endif
                                        </td>
                                        <td>
                                            <button class="btn btn-info btn-sm view-details" data-booking-id="{{ $booking->id }}">
                                                View
                                            </button>
                                        </td>
                                        <td>
                                            <span class="badge {{ $allCompleted ? 'bg-success' : 'bg-warning' }}">
                                                {{ $allCompleted ? 'Completed' : 'Pending' }}
                                            </span>
                                        </td>
                                        <td>{{ $booking->remarks ?? 'No remarks' }}</td>
                                        <td>
                                            <div class="d-flex flex-wrap gap-2">
                                                @if ($booking->professional_documents)
                                                    @php
                                                        $doc = $booking->professional_documents;
                                                        $extension = pathinfo($doc, PATHINFO_EXTENSION);
                                                        $fileName = basename($doc);
                                                        $icon = match(strtolower($extension)) {
                                                            'pdf' => 'pdf',
                                                            'doc', 'docx' => 'word',
                                                            'jpg', 'jpeg', 'png' => 'image',
                                                            default => 'file'
                                                        };
                                                    @endphp
                                                    <a href="{{ asset('storage/' . $doc) }}" 
                                                       class="document-preview" 
                                                       target="_blank"
                                                       title="View {{ $fileName }}">
                                                        <img src="{{ asset('images/' . $icon . '-icon.png') }}" 
                                                             alt="{{ strtoupper($extension) }}">
                                                        <div class="doc-info">
                                                            <span class="doc-name">{{ $fileName }}</span>
                                                            <span class="doc-type">{{ strtoupper($extension) }} Document</span>
                                                        </div>
                                                    </a>
                                                @endif
                                                <button class="btn btn-sm btn-primary upload-btn" 
                                                        onclick="openUploadModal('{{ $booking->id }}')"
                                                        title="Upload/Update Document">
                                                    <i class="fas fa-{{ $booking->professional_documents ? 'sync' : 'upload' }}"></i>
                                                    {{ $booking->professional_documents ? 'Update' : 'Upload' }}
                                                </button>
                                            </div>
                                        </td>
                                        <td>
                                            @if ($booking->customer_document)
                                                @php
                                                    $customerDocs = explode(',', $booking->customer_document);
                                                @endphp
                                                <div class="d-flex flex-wrap gap-2">
                                                    @foreach ($customerDocs as $doc)
                                                        @php
                                                            $extension = pathinfo($doc, PATHINFO_EXTENSION);
                                                            $fileName = basename($doc);
                                                            $icon = match(strtolower($extension)) {
                                                                'pdf' => 'pdf',
                                                                'doc', 'docx' => 'word',
                                                                'jpg', 'jpeg', 'png' => 'image',
                                                                default => 'file'
                                                            };
                                                        @endphp
                                                        <a href="{{ asset('storage/' . $doc) }}" 
                                                           class="document-preview" 
                                                           target="_blank"
                                                           title="View {{ $fileName }}">
                                                            <img src="{{ asset('images/' . $icon . '-icon.png') }}" 
                                                                 alt="{{ strtoupper($extension) }}">
                                                            <div class="doc-info">
                                                                <span class="doc-name">{{ $fileName }}</span>
                                                                <span class="doc-type">{{ strtoupper($extension) }} Document</span>
                                                            </div>
                                                        </a>
                                                    @endforeach
                                                </div>
                                            @else
                                                <div class="no-doc-message">
                                                    <i class="fas fa-file-alt"></i>
                                                    No documents provided
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('professional.chat.open', $booking->id) }}" 
                                               class="chat-button position-relative chat-btn-{{ $booking->id }}" 
                                               target="_blank" 
                                               title="Chat with Customer"
                                               data-booking-id="{{ $booking->id }}">
                                                <i class="fas fa-comments chat-icon"></i>
                                                <span class="chat-label">Chat</span>
                                                <span class="chat-badge badge bg-danger d-none" id="chat-badge-{{ $booking->id }}">
                                                    0
                                                </span>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </section>
    </div>
</div>

<!-- Custom Modal -->
<div id="bookingDetailModal" class="custom-modal">
    <div class="custom-modal-content">
        <span class="close-modal">&times;</span>
        <h4>Booking Details</h4>
        <table class="table table-bordered w-full border-collapse text-sm" id="details-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Time Slot</th>
                    <th>Remarks</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<!-- Questionnaire Modal -->
<div id="questionnaireModal" class="custom-modal">
    <div class="custom-modal-content">
        <span class="close-modal" id="closeQuestionnaireModal">&times;</span>
        <h4>Questionnaire Answers</h4>
        <div id="questionnaireContent">
            <!-- Content will be loaded here -->
        </div>
    </div>
</div>

<!-- Booking Chat Modal -->
<div id="bookingChatModal" class="custom-modal" style="display: none">
    <div class="custom-modal-content">
        <div>
            <div id="chatModalTitle">Chat with Customer</div>
            <span class="close-modal" id="closeChatModal" onclick="closeChatModal()">&times;</span>
        </div>
        <div id="chatMessages">
            <!-- Messages will be loaded here -->
        </div>
        <div>
            <div id="selectedFileName"></div>
            <div>
            </div>
        </div>
    </div>
</div>

<!-- Upload Modal -->
<div id="uploadModal" class="upload-modal" style="display: none">
    <div class="upload-modal-content">
        <div class="modal-header">
            <h3>Upload Document</h3>
            <span class="close-upload-modal">&times;</span>
        </div>
        <div class="modal-body">
            <form id="uploadForm" class="upload-form" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="booking_id" name="booking_id" value="">
                
                <div class="form-group">
                    <label for="document">Select PDF Document:</label>
                    <input type="file" id="document" name="document" accept=".pdf" required>
                    <small class="form-text text-muted">Only PDF files are allowed. Max size: 10MB</small>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn-upload">
                        <i class="fas fa-upload"></i> Upload Document
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('bookingDetailModal');
    const questionnaireModal = document.getElementById('questionnaireModal');
    const closeModal = document.querySelector('.close-modal');
    const closeQuestionnaireModal = document.getElementById('closeQuestionnaireModal');
    const tableBody = document.querySelector('#details-table tbody');
    const questionnaireContent = document.getElementById('questionnaireContent');

    // View details button click
    document.querySelectorAll('.view-details').forEach(button => {
        button.addEventListener('click', () => {
            const bookingId = button.dataset.bookingId;
            fetchBookingDetails(bookingId);
        });
    });

    // Fetch booking details from server
    function fetchBookingDetails(bookingId) {
        fetch(`/professional/bookings/${bookingId}/details`)
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                populateTable(data.dates, bookingId);
                modal.style.display = 'block';
            })
            .catch(error => {
                console.error('Error fetching booking details:', error);
                alert('Something went wrong while fetching booking details.');
            });
    }

    // Populate the modal table
    function populateTable(dates, bookingId) {
        tableBody.innerHTML = '';

        dates.forEach(item => {
            item.time_slot.forEach(slot => {
                const isCompleted = item.status === 'completed';
                const isChecked = isCompleted ? 'checked' : '';
                const sliderClass = 'status-slider';
                const remarks = item.remarks || '';

                const row = `
                    <tr>
                        <td>${item.date}</td>
                        <td>${slot}</td>
                        <td>
                            <input type="text" class="form-control remark-input" value="${remarks}" 
                                   placeholder="Remarks">
                        </td>
                        <td>
                            <label class="${sliderClass}"
                                   data-booking-id="${bookingId}" 
                                   data-date="${item.date}" 
                                   data-slot="${slot}">
                                <input type="checkbox" class="status-checkbox" ${isChecked}>
                                <span class="slider"></span>
                            </label>
                            <span class="status-text">${isCompleted ? 'Completed' : 'Pending'}</span>
                        </td>
                    </tr>
                `;

                tableBody.insertAdjacentHTML('beforeend', row);
            });
        });

        attachCheckboxListeners();
    }

    // Attach change event to status checkboxes
    function attachCheckboxListeners() {
        document.querySelectorAll('.status-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', async (e) => {
                const label = checkbox.closest('.status-slider');
                const bookingId = label.dataset.bookingId;
                const date = label.dataset.date;
                const slot = label.dataset.slot;
                const remarks = checkbox.closest('tr').querySelector('.remark-input').value.trim();
                const status = checkbox.checked ? 'completed' : 'pending';

                // Check if remarks are empty and show confirmation
                if (!remarks) {
                    if (!confirm('Are you sure you want to proceed without adding any remarks?')) {
                        // If user cancels, revert the checkbox state
                        checkbox.checked = !checkbox.checked;
                        return;
                    }
                }

                // Send AJAX request to update status and remarks
                fetch('/professional/bookings/update-status', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        booking_id: bookingId,
                        date: date,
                        slot: slot,
                        remarks: remarks,
                        status: status
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        toastr.success(data.message);
                        // Update the UI to reflect the new status
                        const statusText = checkbox.closest('td').querySelector('.status-text');
                        statusText.textContent = status === 'completed' ? 'Completed' : 'Pending';
                        
                        // Keep remarks input always enabled
                        const remarksInput = checkbox.closest('tr').querySelector('.remark-input');
                        remarksInput.readOnly = false;
                        
                        // Keep slider always enabled
                        label.className = 'status-slider';
                    } else {
                        toastr.error(data.message);
                        // Revert the checkbox state
                        checkbox.checked = !checkbox.checked;
                    }
                })
                .catch(error => {
                    console.error('Error updating status:', error);
                    toastr.error('Failed to update status.');
                    // Revert the checkbox state
                    checkbox.checked = !checkbox.checked;
                });
            });
        });
    }

    // Handle questionnaire info button clicks
    document.querySelectorAll('.questionnaire-info').forEach(button => {
        button.addEventListener('click', () => {
            const bookingId = button.dataset.bookingId;
            fetchQuestionnaireAnswers(bookingId);
        });
    });

    // Fetch questionnaire answers
    function fetchQuestionnaireAnswers(bookingId) {
        fetch(`/professional/bookings/${bookingId}/questionnaire`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    let html = `<div class="questionnaire-details">
                        <h5>Customer: ${data.booking_details.customer_name}</h5>
                        <h6>Service: ${data.booking_details.service_name}</h6>
                        ${data.booking_details.sub_service_name ? '<h6>Sub-Service: <span class="badge bg-info">' + data.booking_details.sub_service_name + '</span></h6>' : ''}
                        <div class="answers-list">`;
                    
                    data.answers.forEach(item => {
                        html += `
                            <div class="answer-item">
                                <p class="question"><strong>Q:</strong> ${item.question}</p>
                                <p class="answer"><strong>A:</strong> ${item.answer}</p>
                            </div>
                        `;
                    });
                    
                    html += `</div></div>`;
                    questionnaireContent.innerHTML = html;
                } else {
                    questionnaireContent.innerHTML = `<p class="text-center text-muted">${data.message}</p>`;
                }
                questionnaireModal.style.display = 'block';
            })
            .catch(error => {
                console.error('Error fetching questionnaire answers:', error);
                questionnaireContent.innerHTML = '<p class="text-center text-danger">Error loading questionnaire answers.</p>';
                questionnaireModal.style.display = 'block';
            });
    }

    // Modal close handlers
    closeModal.addEventListener('click', () => modal.style.display = 'none');
    closeQuestionnaireModal.addEventListener('click', () => questionnaireModal.style.display = 'none');
    
    window.addEventListener('click', event => {
        if (event.target === modal) modal.style.display = 'none';
        if (event.target === questionnaireModal) questionnaireModal.style.display = 'none';
    });
});

// Add this new JavaScript code
const questionnaireModal = document.getElementById('questionnaireModal');
const closeQuestionnaireModal = document.getElementById('closeQuestionnaireModal');
const questionnaireContent = document.getElementById('questionnaireContent');

// Handle questionnaire info button clicks
document.querySelectorAll('.questionnaire-info').forEach(button => {
    button.addEventListener('click', () => {
        const bookingId = button.dataset.bookingId;
        fetchQuestionnaireAnswers(bookingId);
    });
});

// Fetch questionnaire answers
function fetchQuestionnaireAnswers(bookingId) {
    // Show loading indicator
    questionnaireContent.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading questionnaire answers...</div>';
    questionnaireModal.style.display = 'block';
    
    fetch(`/professional/bookings/${bookingId}/questionnaire`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                let html = `<div class="questionnaire-details">
                    <!-- Header Section -->
                    <div class="questionnaire-header mb-4 p-3 bg-light rounded">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h5 class="mb-1"><i class="fas fa-user text-primary"></i> ${data.booking_details.customer_name}</h5>
                                <p class="mb-1"><i class="fas fa-cog text-info"></i> <strong>Service:</strong> ${data.booking_details.service_name}</p>
                                ${data.booking_details.sub_service_name ? '<p class="mb-1"><i class="fas fa-puzzle-piece text-warning"></i> <strong>Sub-Service:</strong> <span class="badge bg-info">' + data.booking_details.sub_service_name + '</span></p>' : ''}
                            </div>
                            <div class="col-md-4 text-end">
                                ${data.booking_details.booking_date ? '<p class="mb-1"><i class="fas fa-calendar text-success"></i> ' + new Date(data.booking_details.booking_date).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }) + '</p>' : ''}
                                <span class="badge bg-primary fs-6"><i class="fas fa-question-circle"></i> ${data.booking_details.answers_count || data.answers.length} Answers</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Answers Section -->
                    <div class="answers-list">`;
                
                if (data.answers && data.answers.length > 0) {
                    data.answers.forEach((item, index) => {
                        // Format date nicely
                        let formattedDate = '';
                        if (item.created_at) {
                            const date = new Date(item.created_at);
                            formattedDate = date.toLocaleDateString('en-US', { 
                                month: 'numeric', 
                                day: 'numeric', 
                                year: 'numeric' 
                            });
                        }
                        
                        html += `
                            <div class="answer-card mb-3 border rounded">
                                <div class="card-body p-3">
                                    <div class="d-flex align-items-start">
                                        <div class="question-number me-3 mt-1">
                                            <span class="badge bg-gradient-primary rounded-pill fs-6">${index + 1}</span>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="question-text mb-3">
                                                <h6 class="text-dark mb-2"><i class="fas fa-question-circle text-primary me-2"></i>Question:</h6>
                                                <p class="question-content mb-0 text-muted">${item.question}</p>
                                            </div>
                                            <div class="answer-text mb-3">
                                                <h6 class="text-dark mb-2"><i class="fas fa-check-circle text-success me-2"></i>Answer:</h6>
                                                <div class="answer-content p-2 bg-light rounded border-start border-success border-3">
                                                    <strong class="text-success">${item.answer}</strong>
                                                </div>
                                            </div>
                                            <div class="metadata d-flex justify-content-between align-items-center">
                                                <div>
                                                    ${item.question_type ? '<span class="badge bg-secondary"><i class="fas fa-tag me-1"></i>Type: ' + item.question_type + '</span>' : ''}
                                                </div>
                                                <div>
                                                    ${formattedDate ? '<small class="text-muted"><i class="fas fa-clock me-1"></i>Answered on: ' + formattedDate + '</small>' : ''}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                } else {
                    html += `
                        <div class="text-center py-5">
                            <i class="fas fa-exclamation-triangle text-warning fs-1 mb-3"></i>
                            <h5 class="text-muted">No MCQ Answers Found</h5>
                            <p class="text-muted">The customer hasn't filled out the questionnaire yet.</p>
                        </div>
                    `;
                }
                
                html += `</div></div>`;
                questionnaireContent.innerHTML = html;
            } else {
                let errorHtml = `
                    <div class="alert alert-warning">
                        <h6><i class="fas fa-exclamation-triangle"></i> No MCQ Answers Found</h6>
                        <p>${data.message}</p>
                `;
                
                if (data.debug_info) {
                    errorHtml += `
                        <details class="mt-3">
                            <summary>Debug Information</summary>
                            <small class="text-muted">
                                <br>Booking ID: ${data.debug_info.booking_id}
                                <br>Customer: ${data.debug_info.customer_name || 'N/A'}
                                <br>Service: ${data.debug_info.service_name || 'N/A'}
                                <br>User ID: ${data.debug_info.user_id || 'N/A'}
                                <br>Service ID: ${data.debug_info.service_id || 'N/A'}
                                ${data.debug_info.sub_service_id ? '<br>Sub-Service ID: ' + data.debug_info.sub_service_id : ''}
                            </small>
                        </details>
                    `;
                }
                
                errorHtml += '</div>';
                questionnaireContent.innerHTML = errorHtml;
            }
        })
        .catch(error => {
            console.error('Error fetching questionnaire answers:', error);
            questionnaireContent.innerHTML = `
                <div class="alert alert-danger">
                    <h6><i class="fas fa-exclamation-circle"></i> Error Loading Questionnaire</h6>
                    <p>There was an error loading the questionnaire answers. Please try again.</p>
                    <small class="text-muted">Error: ${error.message}</small>
                </div>
            `;
        });
}

// Close questionnaire modal
closeQuestionnaireModal.addEventListener('click', () => {
    questionnaireModal.style.display = 'none';
});

// Close modal when clicking outside
window.addEventListener('click', event => {
    if (event.target === questionnaireModal) {
        questionnaireModal.style.display = 'none';
    }
});

// Upload Modal Functionality
document.addEventListener('DOMContentLoaded', function() {
    const uploadModal = document.getElementById('uploadModal');
    const closeUploadModal = document.querySelector('.close-upload-modal');
    const uploadForm = document.getElementById('uploadForm');
    const bookingIdInput = document.getElementById('booking_id');

    // Check if elements exist before adding event listeners
    if (!uploadModal || !closeUploadModal || !uploadForm || !bookingIdInput) {
        console.error('Upload modal elements not found');
        return;
    }

    window.openUploadModal = function(bookingId) {
        bookingIdInput.value = bookingId;
        uploadModal.style.display = 'block';
    }

    closeUploadModal.onclick = function() {
        uploadModal.style.display = 'none';
    }

    window.onclick = function(event) {
        if (event.target == uploadModal) {
            uploadModal.style.display = 'none';
        }
    }

    uploadForm.onsubmit = function(e) {
        e.preventDefault();
        const formData = new FormData(uploadForm);
        const bookingId = bookingIdInput.value;
        
        // Get the file input and check if a file is selected
        const fileInput = document.getElementById('document');
        if (fileInput.files.length === 0) {
            toastr.error('Please select a file to upload');
            return;
        }

        // Add the single file to formData
        formData.append('document', fileInput.files[0]);

        fetch(`/professional/bookings/${bookingId}/upload-documents`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                toastr.success(data.message);
                uploadModal.style.display = 'none';
                location.reload(); 
            } else {
                toastr.error(data.message || 'Upload failed');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            toastr.error('An error occurred while uploading');
        });
    };
});

// Load unread chat counts for all bookings
function loadUnreadChatCounts() {
    $('[data-booking-id]').each(function() {
        const bookingId = $(this).data('booking-id');
        const badgeElement = $(`#chat-badge-${bookingId}`);
        
        $.ajax({
            url: `/professional/chat/booking/${bookingId}/unread-count`,
            method: 'GET',
            success: function(response) {
                if(response.success && response.unread_count > 0) {
                    badgeElement.text(response.unread_count);
                    badgeElement.removeClass('d-none');
                } else {
                    badgeElement.addClass('d-none');
                }
            },
            error: function() {
                // Silently fail
            }
        });
    });
}



// Load counts on page load
$(document).ready(function() {
    loadUnreadChatCounts();
    
    // Refresh counts every 30 seconds
    setInterval(loadUnreadChatCounts, 30000);
});

</script>
@endsection