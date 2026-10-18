@extends('layouts.app')

@section('title', 'Food Categories')

@section('nav')
@include('partials.admin_nav')
@endsection

@section('content')
<div class="page-header">
    <h1 class="page-title h4 mb-0">Food categories</h1>
    <a href="{{ url('/admin/dashboard') }}" class="btn btn-outline-secondary btn-sm">Back to dashboard</a>
</div>

<p class="small text-muted">
    Donors pick from <strong>active</strong> categories when posting food.
    Categories with listings are not deleted — deactivate them instead.
</p>

<details class="ui-disclose mb-4" @if ($errors->has('name')) open @endif>
    <summary>Add a category</summary>
    <div class="ui-disclose-body">
        <form method="POST" action="{{ url('/admin/categories') }}" class="row g-3 align-items-end mt-3">
            @csrf
            <div class="col-12 col-md-8">
                <label for="name" class="form-label required">Name</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" maxlength="100" required placeholder="e.g. Packaged">
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12 col-md-4">
                <button type="submit" class="btn btn-brand w-100">Add category</button>
            </div>
        </form>
    </div>
</details>

<div class="card page-card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped mb-0 align-middle">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Listings</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($categories as $category)
                        <tr>
                            <td>{{ $category->name }}</td>
                            <td>{{ $category->listings_count }}</td>
                            <td>
                                @if ($category->is_active)
                                    <span class="badge badge-status badge-available">Active</span>
                                @else
                                    <span class="badge badge-status badge-closed">Inactive</span>
                                @endif
                            </td>
                            <td class="text-end">
                                @if ($category->is_active)
                                    <form method="POST" action="{{ url('/admin/categories/'.$category->id) }}" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="action" value="deactivate">
                                        <button type="submit" class="btn btn-sm btn-outline-secondary" data-confirm="Deactivate {{ $category->name }}? Existing listings keep this category.">Deactivate</button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ url('/admin/categories/'.$category->id) }}" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="action" value="activate">
                                        <button type="submit" class="btn btn-sm btn-brand">Activate</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-muted p-3">No categories yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
