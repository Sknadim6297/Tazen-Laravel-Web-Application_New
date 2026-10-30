@extends('admin.layouts.layout')

@section('content')
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="my-4 page-header-breadcrumb">
            <h1 class="page-title fw-medium fs-18 mb-2">Edit {{ $tool->name }}</h1>
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.tools.index') }}">All Tools</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        <div class="card custom-card">
            <div class="card-body">
                @unless ($tool->is_implemented)
                    <p class="alert alert-warning">This tool is {{ $tool->statusLabel() }}. Its workflow is not built, so it cannot be launched or made public.</p>
                @endunless
                <form method="post" action="{{ route('admin.tools.update', $tool) }}" class="row g-3">
                    @csrf
                    @method('PUT')
                    <div class="col-md-6">
                        <label class="form-label" for="name">Name</label>
                        <input id="name" class="form-control" name="name" value="{{ old('name', $tool->name) }}" required maxlength="120">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="slug">Slug</label>
                        <input id="slug" class="form-control" name="slug" value="{{ old('slug', $tool->slug) }}" required maxlength="120">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="module_key">Module</label>
                        <input id="module_key" class="form-control" name="module_key" value="{{ old('module_key', $tool->module_key) }}" required maxlength="60">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="tool_category_id">Category</label>
                        <select id="tool_category_id" class="form-select" name="tool_category_id">
                            <option value="">None</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected(old('tool_category_id', $tool->tool_category_id) == $category->id)>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="icon">Icon class</label>
                        <input id="icon" class="form-control" name="icon" value="{{ old('icon', $tool->icon) }}" maxlength="80">
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="description">Description</label>
                        <textarea id="description" class="form-control" name="description" rows="3" maxlength="1000">{{ old('description', $tool->description) }}</textarea>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" for="access_type">Free/Paid</label>
                        <select id="access_type" class="form-select" name="access_type">
                            <option value="free" @selected(old('access_type', $tool->access_type) === 'free')>Free</option>
                            <option value="paid" @selected(old('access_type', $tool->access_type) === 'paid')>Paid</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" for="status">Status</label>
                        <select id="status" class="form-select" name="status">
                            @foreach (['coming_soon', 'unavailable', 'active', 'inactive'] as $status)
                                <option value="{{ $status }}" @selected(old('status', $tool->status) === $status) @disabled(!$tool->is_implemented && !in_array($status, ['coming_soon', 'unavailable'], true))>{{ \Illuminate\Support\Str::headline($status) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" for="display_order">Display order</label>
                        <input id="display_order" class="form-control" type="number" name="display_order" min="0" max="9999" value="{{ old('display_order', $tool->display_order) }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" for="route_name">Route name</label>
                        <input id="route_name" class="form-control" name="route_name" value="{{ old('route_name', $tool->route_name) }}" maxlength="120" @disabled(!$tool->is_implemented)>
                    </div>
                    <div class="col-12 d-flex gap-2">
                        <button class="btn btn-primary" type="submit">Save</button>
                        <a class="btn btn-light" href="{{ route('admin.tools.index') }}">Back</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
