@extends('professional.layout.layout')

@section('title', 'Additional Services')

@section('styles')

@vite('resources/css/tazen.css')
<style>.filters-content{display:none}.modal{display:none}.modal.show{display:flex}</style>
@endsection

@section('content')
<div id="additional-services-page" class="additional-services-page min-h-full bg-slate-50 px-4 py-6 text-slate-900 sm:px-6 lg:px-8">
    <div class="additional-services-shell mx-auto max-w-7xl space-y-6">
        <section class="services-hero flex flex-col gap-5 rounded-2xl border border-brand-100 bg-gradient-to-br from-white via-brand-50 to-white p-6 shadow-card sm:flex-row sm:items-center sm:justify-between">
            <div class="hero-meta">
                <span class="hero-eyebrow"><i class="fas fa-layer-group"></i>Additional Services</span>
                <h1 class="mt-2 text-2xl font-bold tracking-tight text-brand-950 sm:text-3xl">Manage Additional Services</h1>
                <ul class="hero-breadcrumb">
                    <li><a href="{{ route('professional.dashboard') }}">Home</a></li>
                    <li class="active" aria-current="page">Additional Services</li>
                </ul>
            </div>
            <div class="hero-actions">
                <a href="{{ route('professional.additional-services.create') }}" class="btn-primary-pill inline-flex items-center gap-2 rounded-lg bg-brand-700 px-4 py-3 font-semibold text-white shadow-sm transition hover:bg-brand-800">
                    <i class="fas fa-plus"></i>
                    Add New Service
                </a>
            </div>
        </section>

        <section class="filters-card overflow-hidden rounded-2xl border border-brand-100 bg-white shadow-card">
            <div class="filters-card__head flex items-center justify-between border-b border-slate-100 px-5 py-4">
                <h3 class="text-lg font-semibold text-brand-950"><i class="fas fa-filter"></i>Filter Services</h3>
                <button type="button" id="toggleFiltersBtn" class="filters-toggle inline-flex items-center gap-2 rounded-lg border border-brand-200 px-3 py-2 text-sm font-medium text-brand-800 hover:bg-brand-50">
                    <i class="fas fa-chevron-down" id="filterIcon"></i>
                    Toggle Filters
                </button>
            </div>

            <div id="filtersContent" class="filters-content p-5">
                <div class="filters-grid grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    <div>
                        <label for="filterCustomer"><i class="fas fa-user"></i> Customer</label>
                        <select class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm focus:border-brand-500 focus:outline-none" id="filterCustomer">
                            <option value="">All Customers</option>
                            @php
                                $customers = $additionalServices->unique('user_id')->pluck('user')->sortBy('name');
                            @endphp
                            @foreach($customers as $customer)
                                @if($customer)
                                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="filterPaymentStatus"><i class="fas fa-credit-card"></i> Payment Status</label>
                        <select class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm focus:border-brand-500 focus:outline-none" id="filterPaymentStatus">
                            <option value="">All Payment Status</option>
                            <option value="pending">Pending</option>
                            <option value="paid">Paid</option>
                            <option value="failed">Failed</option>
                        </select>
                    </div>

                    <div>
                        <label for="filterServiceStatus"><i class="fas fa-tasks"></i> Service Status</label>
                        <select class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm focus:border-brand-500 focus:outline-none" id="filterServiceStatus">
                            <option value="">All Status</option>
                            <option value="completed">Completed</option>
                            <option value="in_progress">In Progress</option>
                            <option value="ready_to_start">Ready to Start</option>
                            <option value="awaiting_delivery_date">Awaiting Delivery Date</option>
                            <option value="awaiting_payment">Awaiting Payment</option>
                            <option value="rejected">Rejected</option>
                            <option value="under_negotiation">Under Negotiation</option>
                            <option value="price_updated">Price Updated</option>
                            <option value="pending_review">Pending Review</option>
                            <option value="awaiting_admin">Awaiting Admin</option>
                            <option value="pending">Pending</option>
                        </select>
                    </div>

                    <div>
                        <label for="filterDateRange"><i class="fas fa-calendar"></i> Date Range</label>
                        <select class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm focus:border-brand-500 focus:outline-none" id="filterDateRange">
                            <option value="">All Time</option>
                            <option value="today">Today</option>
                            <option value="week">This Week</option>
                            <option value="month">This Month</option>
                            <option value="custom">Custom Range</option>
                        </select>
                    </div>
                </div>

                <div id="customDateRange" class="filters-grid grid gap-4 sm:grid-cols-2" style="display: none">
                    <div>
                        <label for="filterDateFrom">From Date</label>
                        <input class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none" type="date" id="filterDateFrom">
                    </div>
                    <div>
                        <label for="filterDateTo">To Date</label>
                        <input class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none" type="date" id="filterDateTo">
                    </div>
                </div>

                <div class="filters-actions mt-4 flex flex-wrap items-center gap-3">
                    <button type="button" id="applyFilters" class="btn-filter-primary inline-flex items-center gap-2 rounded-lg bg-brand-700 px-4 py-2 font-semibold text-white hover:bg-brand-800">
                        <i class="fas fa-check"></i>
                        Apply Filters
                    </button>
                    <button type="button" id="clearFilters" class="btn-filter-secondary inline-flex items-center gap-2 rounded-lg border border-slate-300 px-4 py-2 font-semibold text-slate-700 hover:bg-slate-50">
                        <i class="fas fa-times"></i>
                        Clear All
                    </button>
                    <span id="filterResultsCount" class="filters-count"></span>
                </div>
            </div>
        </section>

        <section class="services-card overflow-hidden rounded-2xl border border-brand-100 bg-white shadow-card">
            <header class="services-card__head border-b border-slate-100 px-5 py-4">
                <h2 class="text-lg font-semibold text-brand-950">Additional Services List</h2>
                <p>Monitor service requests, update delivery schedules, and manage consultation status without changing any underlying logic.</p>
            </header>
            <div class="services-card__body p-5">
                @if($additionalServices->count() > 0)
                    <div class="table-controls table-controls--top">
                        <div class="table-control table-control--length"></div>
                        <div class="table-control table-control--search"></div>
                    </div>
                    <div class="table-wrapper overflow-x-auto">
                        <table class="data-table w-full min-w-[1000px] border-collapse text-left text-sm" id="additional-services-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Service Name</th>
                                    <th>Customer</th>
                                    <th>Booking Ref</th>
                                    <th>Total Price</th>
                                    <th>Status</th>
                                    <th>Payment Status</th>
                                    <th>Created Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($additionalServices as $service)
                                    <tr>
                                        <td>#{{ $service->id }}</td>
                                        <td><strong>{{ $service->service_name }}</strong></td>
                                        <td>{{ $service->user->name }}</td>
                                        <td>
                                            <a href="#" class="text-primary">
                                                #{{ $service->booking_id }}
                                            </a>
                                        </td>
                                        <td>₹{{ number_format($service->final_price, 2) }}</td>
                                        <td>
                                            @php
                                                $statusBadge = '';
                                                $statusText = '';
                                                $subStatusText = '';

                                                if ($service->consulting_status === 'done' && $service->customer_confirmed_at) {
                                                    $statusBadge = 'badge-success';
                                                    $statusText = 'Completed';
                                                } elseif ($service->consulting_status === 'in_progress') {
                                                    $statusBadge = 'badge-success';
                                                    $statusText = 'In Progress';
                                                } elseif ($service->admin_status === 'approved') {
                                                    if ($service->payment_status === 'paid') {
                                                        if ($service->delivery_date_set) {
                                                            $statusBadge = 'badge-success';
                                                            $statusText = 'Ready to Start';
                                                            $subStatusText = '📅 Delivery Date Set';
                                                        } else {
                                                            $statusBadge = 'badge-warning';
                                                            $statusText = 'Awaiting Delivery Date';
                                                            $subStatusText = '⏰ Set delivery date to proceed';
                                                        }
                                                    } else {
                                                        $statusBadge = 'badge-warning';
                                                        $statusText = 'Awaiting Payment';
                                                        $subStatusText = '💳 Customer needs to pay';
                                                    }
                                                } elseif ($service->admin_status === 'rejected') {
                                                    $statusBadge = 'badge-danger';
                                                    $statusText = 'Rejected';
                                                    if ($service->admin_reason) {
                                                        $subStatusText = '❌ ' . $service->admin_reason;
                                                    }
                                                } elseif ($service->admin_status === 'pending') {
                                                    if ($service->negotiation_status === 'user_negotiated') {
                                                        $statusBadge = 'badge-warning';
                                                        $statusText = 'Under Negotiation';
                                                        $subStatusText = '💬 Awaiting admin review';
                                                    } elseif ($service->negotiation_status === 'admin_responded') {
                                                        $statusBadge = 'badge-warning';
                                                        $statusText = 'Price Updated';
                                                        $subStatusText = '✅ Awaiting customer response';
                                                    } else {
                                                        if ($service->professional_status === 'pending') {
                                                            $statusBadge = 'badge-warning';
                                                            $statusText = 'Pending Review';
                                                            $subStatusText = '⏳ Awaiting admin approval';
                                                        } elseif ($service->professional_status === 'accepted') {
                                                            $statusBadge = 'badge-warning';
                                                            $statusText = 'Awaiting Admin';
                                                            $subStatusText = '👨‍💼 Under admin review';
                                                        } else {
                                                            $statusBadge = 'badge-warning';
                                                            $statusText = 'Pending';
                                                        }
                                                    }
                                                } else {
                                                    $statusBadge = 'badge-warning';
                                                    $statusText = 'Pending';
                                                }
                                            @endphp

                                            <span class="status-pill {{ $statusBadge }}">{{ $statusText }}</span>

                                            @if($subStatusText)
                                                <small class="status-subtext">{{ $subStatusText }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($service->payment_status === 'pending')
                                                <span class="badge badge-warning">Pending</span>
                                            @elseif($service->payment_status === 'paid')
                                                <span class="badge badge-success">Paid</span>
                                            @elseif($service->payment_status === 'failed')
                                                <span class="badge badge-danger">Failed</span>
                                            @endif
                                        </td>
                                        <td>{{ $service->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <div class="action-buttons">
                                                <a href="{{ route('professional.additional-services.show', $service->id) }}" class="btn-action view">
                                                    <i class="fas fa-eye"></i>
                                                    View
                                                </a>
                                                @if(!$service->delivery_date_set)
                                                    <button type="button" class="btn-action delivery set-delivery-date" data-id="{{ $service->id }}">
                                                        <i class="fas fa-calendar-plus"></i>
                                                        Set Delivery
                                                    </button>
                                                @endif
                                                @if($service->canBeCompletedByProfessional())
                                                    <button type="button" class="btn-action complete mark-completed" data-id="{{ $service->id }}">
                                                        <i class="fas fa-check"></i>
                                                        Mark Completed
                                                    </button>
                                                @endif
                                            </div>
                                            @if($service->consulting_status === 'done' && $service->payment_status === 'paid')
                                                <div class="dropdown">
                                                    <button class="actions-trigger" type="button" onclick="toggleDropdown(this)">
                                                        <i class="fas fa-file-invoice"></i>
                                                        Invoice
                                                        <i class="fas fa-chevron-down caret-icon"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li>
                                                            <a href="{{ route('professional.additional-services.invoice', $service->id) }}" class="dropdown-item">
                                                                <i class="fas fa-file-alt"></i> View Invoice
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a href="{{ route('professional.additional-services.invoice.pdf', $service->id) }}" class="dropdown-item" target="_blank">
                                                                <i class="fas fa-download"></i> Download PDF Invoice
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="table-controls table-controls--bottom">
                        <div class="table-control table-control--info"></div>
                        <div class="table-control table-control--pagination"></div>
                    </div>

                    <div>
                        {{ $additionalServices->links() }}
                    </div>
                @else
                    <div class="empty-state">
                        <i class="fas fa-plus-circle"></i>
                        <h4>No Additional Services Found</h4>
                        <p>You haven't created any additional services yet.</p>
                        <a href="{{ route('professional.additional-services.create') }}">
                            <i class="fas fa-plus"></i>
                            Create Your First Additional Service
                        </a>
                    </div>
                @endif
            </div>
        </section>
    </div>
</div>

<!-- Mark Completed Modal -->
<div class="modal" id="markCompletedModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Mark Consultation Completed</h5>
                <button type="button" class="btn-close" onclick="closeModal('markCompletedModal')">&times;</button>
            </div>
            <div class="modal-body">
                Are you sure you want to mark this consultation as completed? The customer will be notified to confirm the completion.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('markCompletedModal')">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button type="button" class="btn btn-success" id="confirmComplete">
                    <i class="fas fa-check"></i> Mark Completed
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Set Delivery Date Modal -->
<div class="modal" id="deliveryDateModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Set Delivery Date</h5>
                <button type="button" class="btn-close" onclick="closeModal('deliveryDateModal')">&times;</button>
            </div>
            <form id="deliveryDateForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="delivery_date" class="form-label">Delivery Date *</label>
                        <input type="date" class="form-control" id="delivery_date" name="delivery_date" min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="delivery_reason" class="form-label">Delivery Reason *</label>
                        <textarea class="form-control" id="delivery_reason" name="delivery_reason" rows="3" placeholder="Please provide a reason for the delivery date..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('deliveryDateModal')">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-calendar-check"></i> Set Date
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

<script>
let currentServiceId = null;

function toggleDropdown(button) {
    const dropdown = button.closest('.dropdown');
    const menu = dropdown.querySelector('.dropdown-menu');

    document.querySelectorAll('.dropdown-menu.show').forEach(otherMenu => {
        if (otherMenu !== menu) {
            otherMenu.classList.remove('show');
        }
    });

    menu.classList.toggle('show');

    if (menu.classList.contains('show')) {
        setTimeout(() => {
            document.addEventListener('click', function closeDropdown(e) {
                if (!dropdown.contains(e.target)) {
                    menu.classList.remove('show');
                    document.removeEventListener('click', closeDropdown);
                }
            });
        }, 0);
    }
}

function showModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('show');
        document.body.style.overflow = '';
    }
}

document.querySelectorAll('.modal').forEach(modal => {
    modal.addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal(this.id);
        }
    });
});

$(document).ready(function() {
    if (typeof $.fn.DataTable === 'undefined') {
        console.error('DataTables library not loaded');
        return;
    }

    var table = $('#additional-services-table').DataTable({
        "order": [[ 7, "desc" ]],
        "pageLength": 10,
        "responsive": true,
        "language": {
            "search": "Search services:",
            "lengthMenu": "Show _MENU_ services per page",
            "info": "Showing _START_ to _END_ of _TOTAL_ services",
            "infoEmpty": "No services found",
            "infoFiltered": "(filtered from _MAX_ total services)",
            "zeroRecords": "No matching services found"
        }
    });

    var dataTableWrapper = $('#additional-services-table').closest('.dataTables_wrapper');
    if (dataTableWrapper.length) {
        dataTableWrapper.find('.dataTables_length').appendTo('.table-control--length');
        dataTableWrapper.find('.dataTables_filter').appendTo('.table-control--search');
        dataTableWrapper.find('.dataTables_info').appendTo('.table-control--info');
        dataTableWrapper.find('.dataTables_paginate').appendTo('.table-control--pagination');
    }

    $('#toggle_filters_btn, #toggleFiltersBtn').on('click', function() {
        var content = document.getElementById('filtersContent');
        var icon = document.getElementById('filterIcon');

        if (content.style.display === 'none' || content.style.display === '') {
            content.style.display = 'block';
            icon.classList.remove('fa-chevron-down');
            icon.classList.add('fa-chevron-up');
        } else {
            content.style.display = 'none';
            icon.classList.remove('fa-chevron-up');
            icon.classList.add('fa-chevron-down');
        }
    });

    $('#filterDateRange').on('change', function() {
        var customRange = document.getElementById('customDateRange');
        if ($(this).val() === 'custom') {
            customRange.style.display = 'grid';
        } else {
            customRange.style.display = 'none';
        }
    });

    $('#applyFilters').on('click', function() {
        applyFilters();
    });

    $('#clearFilters').on('click', function() {
        $('#filterCustomer').val('');
        $('#filterPaymentStatus').val('');
        $('#filterServiceStatus').val('');
        $('#filterDateRange').val('');
        $('#filterDateFrom').val('');
        $('#filterDateTo').val('');
        document.getElementById('customDateRange').style.display = 'none';

        $.fn.dataTable.ext.search = [];

        table.columns().search('').draw();

        table.search('').draw();

        updateFilterCount();

        if (typeof toastr !== 'undefined') {
            toastr.success('All filters cleared');
        }
    });

    function applyFilters() {
        var customerId = $('#filterCustomer').val();
        var paymentStatus = $('#filterPaymentStatus').val();
        var serviceStatus = $('#filterServiceStatus').val();
        var dateRange = $('#filterDateRange').val();
        var dateFrom = $('#filterDateFrom').val();
        var dateTo = $('#filterDateTo').val();

        $.fn.dataTable.ext.search = [];
        table.columns().search('');

        if (customerId) {
            var customerName = $('#filterCustomer option:selected').text();
            table.column(2).search(customerName, false, false);
        }

        if (paymentStatus) {
            var paymentText = paymentStatus.charAt(0).toUpperCase() + paymentStatus.slice(1);
            table.column(6).search(paymentText, false, false);
        }

        if (serviceStatus) {
            var statusMapping = {
                'completed': 'Completed',
                'in_progress': 'In Progress',
                'ready_to_start': 'Ready to Start',
                'awaiting_delivery_date': 'Awaiting Delivery Date',
                'awaiting_payment': 'Awaiting Payment',
                'rejected': 'Rejected',
                'under_negotiation': 'Under Negotiation',
                'price_updated': 'Price Updated',
                'pending_review': 'Pending Review',
                'awaiting_admin': 'Awaiting Admin',
                'pending': 'Pending'
            };
            var statusText = statusMapping[serviceStatus] || serviceStatus;
            table.column(5).search(statusText, false, false);
        }

        if (dateRange && dateRange !== 'custom') {
            var today = new Date();
            var filterDate = new Date();

            switch(dateRange) {
                case 'today':
                    filterDate.setHours(0, 0, 0, 0);
                    break;
                case 'week':
                    filterDate.setDate(today.getDate() - 7);
                    break;
                case 'month':
                    filterDate.setMonth(today.getMonth() - 1);
                    break;
            }

            $.fn.dataTable.ext.search.push(
                function(settings, data, dataIndex) {
                    var dateStr = data[7];
                    if (!dateStr) return true;

                    var rowDate = new Date(dateStr);

                    if (dateRange === 'today') {
                        return rowDate.toDateString() === today.toDateString();
                    } else {
                        return rowDate >= filterDate;
                    }
                }
            );
        } else if (dateRange === 'custom' && dateFrom && dateTo) {
            var fromDate = new Date(dateFrom);
            var toDate = new Date(dateTo);
            toDate.setHours(23, 59, 59, 999);

            $.fn.dataTable.ext.search.push(
                function(settings, data, dataIndex) {
                    var dateStr = data[7];
                    if (!dateStr) return true;

                    var rowDate = new Date(dateStr);
                    return rowDate >= fromDate && rowDate <= toDate;
                }
            );
        }

        table.draw();

        updateFilterCount();

        var activeFilters = 0;
        if (customerId) activeFilters++;
        if (paymentStatus) activeFilters++;
        if (serviceStatus) activeFilters++;
        if (dateRange) activeFilters++;

        if (activeFilters > 0 && typeof toastr !== 'undefined') {
            toastr.success(activeFilters + ' filter(s) applied');
        }
    }

    function updateFilterCount() {
        var info = table.page.info();
        var countText = '';

        if (info.recordsDisplay < info.recordsTotal) {
            countText = 'Showing ' + info.recordsDisplay + ' of ' + info.recordsTotal + ' services';
        } else {
            countText = 'Showing all ' + info.recordsTotal + ' services';
        }

        $('#filterResultsCount').text(countText);
    }

    updateFilterCount();

    table.on('draw', function() {
        updateFilterCount();
    });

    $(document).on('click', '.mark-completed', function(e) {
        e.preventDefault();
        e.stopPropagation();
        currentServiceId = $(this).data('id');
        showModal('markCompletedModal');
    });

    $('#confirmComplete').click(function() {
        if (currentServiceId) {
            $.ajax({
                url: `/professional/additional-services/${currentServiceId}/mark-completed`,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        location.reload();
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function() {
                    toastr.error('An error occurred. Please try again.');
                }
            });
        }
        closeModal('markCompletedModal');
    });

    $(document).on('click', '.set-delivery-date', function(e) {
        e.preventDefault();
        e.stopPropagation();
        currentServiceId = $(this).data('id');
        showModal('deliveryDateModal');
    });

    $('#deliveryDateForm').submit(function(e) {
        e.preventDefault();

        if (currentServiceId) {
            $.ajax({
                url: `/professional/additional-services/${currentServiceId}/set-delivery-date`,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    delivery_date: $('#delivery_date').val(),
                    delivery_reason: $('#delivery_reason').val()
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        location.reload();
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function(xhr) {
                    const errors = xhr.responseJSON?.errors;
                    if (errors) {
                        Object.values(errors).forEach(function(error) {
                            toastr.error(error[0]);
                        });
                    } else {
                        toastr.error('An error occurred. Please try again.');
                    }
                }
            });
        }
        closeModal('deliveryDateModal');
    });
});
</script>
@endsection