@extends('admin.layouts.layout')

@section('content')
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="my-4 page-header-breadcrumb">
            <h1 class="page-title fw-medium fs-18 mb-2">Tool Settings</h1>
            <p class="text-muted mb-0">Visibility, order, and Free/Paid labels. This does not create prices or payments.</p>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        <div class="card custom-card">
            <div class="card-body">
                @if ($tools->isEmpty())
                    <p class="mb-0">No tools are registered.</p>
                @else
                    <form method="post" action="{{ route('admin.tool-settings.update') }}">
                        @csrf
                        @method('PUT')
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th>Tool</th>
                                        <th>Order</th>
                                        <th>Free/Paid</th>
                                        <th>Availability</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($tools as $index => $tool)
                                        <tr>
                                            <td>
                                                <input type="hidden" name="tools[{{ $index }}][id]" value="{{ $tool->id }}">
                                                {{ $tool->name }}
                                                <div class="text-muted small">{{ $tool->module_key }}</div>
                                            </td>
                                            <td style="max-width: 8rem;">
                                                <input class="form-control" type="number" name="tools[{{ $index }}][display_order]" value="{{ $tool->display_order }}" min="0" max="9999" required>
                                            </td>
                                            <td>
                                                <select class="form-select" name="tools[{{ $index }}][access_type]">
                                                    <option value="free" @selected($tool->access_type === 'free')>Free</option>
                                                    <option value="paid" @selected($tool->access_type === 'paid')>Paid</option>
                                                </select>
                                            </td>
                                            <td>
                                                @if ($tool->is_implemented)
                                                    <select class="form-select" name="tools[{{ $index }}][status]">
                                                        @foreach (['active', 'inactive', 'unavailable'] as $status)
                                                            <option value="{{ $status }}" @selected($tool->status === $status)>{{ ucfirst($status) }}</option>
                                                        @endforeach
                                                    </select>
                                                @else
                                                    <span>{{ $tool->statusLabel() }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <button class="btn btn-primary" type="submit">Save settings</button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
