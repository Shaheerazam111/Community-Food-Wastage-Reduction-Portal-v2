@if (!empty($api['usesDonorLocation']))
    <div class="donor-location-box" id="donor-location-{{ $api['id'] }}">
        Pickup location will be taken from your logged-in donor profile.
    </div>
@endif
