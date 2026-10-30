@extends('admin.layouts.layout')

@section('content')
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="my-4 page-header-breadcrumb">
            <h1 class="page-title fw-medium fs-18 mb-2">Tool Categories</h1>
            <p class="text-muted mb-0">These categories belong to the tools registry. They are separate from service categories.</p>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        <div class="card custom-card mb-3">
            <div class="card-header"><div class="card-title">Add category</div></div>
            <div class="card-body">
                <form method="post" action="{{ route('admin.tool-categories.store') }}" class="row g-2 align-items-end">
                    @csrf
                    <div class="col-md-3">
                        <label class="form-label" for="new-name">Name</label>
                        <input id="new-name" class="form-control" name="name" required maxlength="120">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label" for="new-slug">Slug</label>
                        <input id="new-slug" class="form-control" name="slug" maxlength="120">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label" for="new-icon">Icon</label>
                        <input id="new-icon" class="form-control" name="icon" maxlength="80">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label" for="new-order">Order</label>
                        <input id="new-order" class="form-control" type="number" name="display_order" value="0" min="0" max="9999" required>
                    </div>
                    <div class="col-md-2">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="new-active" checked>
                            <label class="form-check-label" for="new-active">Active</label>
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="new-description">Description</label>
                        <textarea id="new-description" class="form-control" name="description" rows="2" maxlength="1000"></textarea>
                    </div>
                    <div class="col-12">
                        <button class="btn btn-primary" type="submit">Save category</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card custom-card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0 align-middle">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Slug</th>
                                <th>Tools</th>
                                <th>Status</th>
                                <th>Order</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($categories as $category)
                                <tr>
                                    <td colspan="6">
                                        <form method="post" action="{{ route('admin.tool-categories.update', $category) }}" class="row g-2 align-items-center">
                                            @csrf
                                            @method('PUT')
                                            <div class="col-md-3"><input class="form-control" name="name" value="{{ $category->name }}" required maxlength="120"></div>
                                            <div class="col-md-2"><input class="form-control" name="slug" value="{{ $category->slug }}" maxlength="120"></div>
                                            <div class="col-md-1">{{ $category->tools_count }}</div>
                                            <div class="col-md-2">
                                                <input type="hidden" name="is_active" value="0">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" @checked($category->is_active)>
                                                    <label class="form-check-label">Active</label>
                                                </div>
                                            </div>
                                            <div class="col-md-1"><input class="form-control" type="number" name="display_order" value="{{ $category->display_order }}" min="0" max="9999" required></div>
                                            <div class="col-md-3 d-flex gap-2">
                                                <button class="btn btn-sm btn-primary" type="submit">Save</button>
                                                <button class="btn btn-sm btn-outline-danger" type="submit" form="delete-category-{{ $category->id }}">Remove</button>
                                            </div>
                                            <div class="col-12"><textarea class="form-control" name="description" rows="2" maxlength="1000">{{ $category->description }}</textarea></div>
                                            <div class="col-md-3"><input class="form-control" name="icon" value="{{ $category->icon }}" placeholder="Icon class" maxlength="80"></div>
                                        </form>
                                        <form id="delete-category-{{ $category->id }}" method="post" action="{{ route('admin.tool-categories.destroy', $category) }}">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center py-4">No tool categories yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if ($categories->hasPages())
                <div class="card-footer">{{ $categories->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection
