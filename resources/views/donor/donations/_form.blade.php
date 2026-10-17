@php
    $listing = $listing ?? null;
    $defaults = $defaults ?? auth()->user();
    $lat = old('latitude', $listing->latitude ?? $defaults->latitude);
    $lng = old('longitude', $listing->longitude ?? $defaults->longitude);
    $expiryValue = old('expiry_at');
    if (! $expiryValue && $listing?->expiry_at) {
        $expiryValue = $listing->expiry_at->format('Y-m-d\TH:i');
    }
@endphp

<div class="mb-3">
    <label for="title" class="form-label required">Food title</label>
    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $listing->title ?? '') }}" required>
    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
    <label for="description" class="form-label">Short description</label>
    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="2" maxlength="1000">{{ old('description', $listing->description ?? '') }}</textarea>
    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
    <label for="category_id" class="form-label required">Category</label>
    <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
        <option value="">Choose category</option>
        @foreach ($categories as $category)
            <option value="{{ $category->id }}" @selected((string) old('category_id', $listing->category_id ?? '') === (string) $category->id)>
                {{ $category->name }}
            </option>
        @endforeach
    </select>
    @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="row g-3 mb-3">
    <div class="col-12 col-md-6">
        <label for="quantity_value" class="form-label required">Quantity</label>
        <input type="number" step="0.01" min="0.01" class="form-control @error('quantity_value') is-invalid @enderror" id="quantity_value" name="quantity_value" value="{{ old('quantity_value', $listing->quantity_value ?? '') }}" required>
        @error('quantity_value')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-12 col-md-6">
        <label for="quantity_unit" class="form-label required">Unit</label>
        <select class="form-select @error('quantity_unit') is-invalid @enderror" id="quantity_unit" name="quantity_unit" required>
            <option value="kg" @selected(old('quantity_unit', $listing->quantity_unit ?? 'servings') === 'kg')>kg</option>
            <option value="servings" @selected(old('quantity_unit', $listing->quantity_unit ?? 'servings') === 'servings')>servings</option>
        </select>
        @error('quantity_unit')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>

<div class="mb-3">
    <label for="expiry_at" class="form-label required">Best before (date &amp; time)</label>
    <input type="datetime-local" class="form-control @error('expiry_at') is-invalid @enderror" id="expiry_at" name="expiry_at" value="{{ $expiryValue }}" required>
    @error('expiry_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
    <label for="photo" class="form-label">Photo (optional)</label>
    @if ($listing?->photo_path)
        <div class="mb-2">
            @include('partials.listing_photo', ['path' => $listing->photo_path, 'size' => 80])
            <p class="small text-muted mb-0">Upload a new file to replace this photo.</p>
        </div>
    @endif
    <input type="file" class="form-control @error('photo') is-invalid @enderror" id="photo" name="photo" accept=".jpg,.jpeg,.png">
    @error('photo')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<p class="form-section-title">Pickup location for this listing</p>
<div class="row g-3 mb-3">
    <div class="col-12 col-md-6">
        <label for="city" class="form-label required">City</label>
        <input type="text" class="form-control @error('city') is-invalid @enderror" id="city" name="city" value="{{ old('city', $listing->city ?? $defaults->city) }}" required>
        @error('city')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-12">
        <label for="pickup_address" class="form-label required">Pickup address</label>
        <input type="text" class="form-control @error('pickup_address') is-invalid @enderror" id="pickup_address" name="pickup_address" value="{{ old('pickup_address', $listing->pickup_address ?? $defaults->address) }}" required>
        @error('pickup_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-12">
        @include('partials.map_picker', [
            'label' => 'Pin on map',
            'required' => true,
            'latitude' => $lat,
            'longitude' => $lng,
        ])
        @error('latitude')<div class="text-danger small">{{ $message }}</div>@enderror
        @error('longitude')<div class="text-danger small">{{ $message }}</div>@enderror
    </div>
</div>
