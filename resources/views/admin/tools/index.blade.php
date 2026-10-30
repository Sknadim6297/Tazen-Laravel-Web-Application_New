@extends('admin.layouts.layout')

@section('content')
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="my-4 page-header-breadcrumb d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <h1 class="page-title fw-medium fs-18 mb-2">All Tools</h1>
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Tools Management</li>
                    </ol>
                </nav>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        <div class="card custom-card">
            <div class="card-header">
                <form class="row g-2 align-items-end" method="get" action="{{ route('admin.tools.index') }}">
                    <div class="col-md-4">
                        <label class="form-label" for="tool-search">Search</label>
                        <input id="tool-search" class="form-control" type="search" name="q" value="{{ $filters['q'] ?? '' }}" maxlength="100">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" for="tool-category">Category</label>
                        <select id="tool-category" class="form-select" name="category">
                            <option value="">All categories</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected(($filters['category'] ?? null) == $category->id)>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" for="tool-status">Status</label>
                        <select id="tool-status" class="form-select" name="status">
                            <option value="">All statuses</option>
                            @foreach ($statuses as $status)
                                <option value="{{ $status }}" @selected(($filters['status'] ?? null) === $status)>{{ str_replace('_', ' ', ucfirst($status)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary w-100" type="submit">Filter</button>
                    </div>
                </form>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Icon</th>
                                <th>Tool</th>
                                <th>Category</th>
                                <th>Module</th>
                                <th>Access</th>
                                <th>Status</th>
                                <th>Order</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($tools as $tool)
                                <tr>
                                    <td>@if ($tool->icon)<i class="{{ $tool->icon }}" aria-hidden="true"></i>@endif</td>
                                    <td>{{ $tool->name }}</td>
                                    <td>{{ $tool->category->name ?? '—' }}</td>
                                    <td>{{ $tool->module_key }}</td>
                                    <td>{{ ucfirst($tool->access_type) }}</td>
                                    <td>{{ $tool->statusLabel() }}</td>
                                    <td>{{ $tool->display_order }}</td>
                                    <td class="text-nowrap">
                                        <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.tools.edit', $tool) }}">View/Edit</a>
                                        @if ($tool->is_implemented)
                                            <form class="d-inline" method="post" action="{{ route('admin.tools.toggle', $tool) }}">
                                                @csrf
                                                <button class="btn btn-sm btn-outline-secondary" type="submit">{{ $tool->status === 'active' ? 'Disable' : 'Enable' }}</button>
                                            </form>
                                        @else
                                            <button class="btn btn-sm btn-outline-secondary" type="button" disabled>Unavailable</button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">No tools match these filters.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if ($tools->hasPages())
                <div class="card-footer">{{ $tools->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection
