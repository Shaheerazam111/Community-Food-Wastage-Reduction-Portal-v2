<div class="api-panel" data-api-id="{{ $api['id'] }}">
    <div class="api-header" onclick="toggleApi(this.parentElement)">
        <span class="badge badge-{{ strtolower($api['method']) }}">{{ $api['method'] }}</span>
        <span class="api-title">{{ $api['name'] }}</span>
        <code style="font-size:.78rem;color:var(--muted)">{{ $api['path'] }}</code>
        <span class="chevron">▶</span>
    </div>
    <div class="api-body">
        <p class="api-desc">{{ $api['description'] }}</p>
        <div class="api-meta">
            <span class="badge badge-task">{{ $api['task'] }}</span>
            @if (!empty($api['challenge']))
                <span class="badge badge-challenge">{{ $api['challenge'] }}</span>
            @endif
            @if (!empty($api['auth']))
                · Requires login @if(!empty($api['role'])) ({{ $api['role'] }}) @endif
            @else
                · Public
            @endif
            · Expected: <code>{{ $api['expects'] }}</code>
        </div>

        @if (!empty($api['fields']))
            <form class="api-form" data-api="{{ $api['id'] }}"
                  data-method="{{ $api['method'] }}"
                  data-path="{{ $api['path'] }}"
                  @if(!empty($api['capture'])) data-capture="{{ $api['capture'] }}" @endif
                  @if(!empty($api['usesListingId'])) data-uses-listing-id="1" @endif
                  @if(!empty($api['showMap'])) data-show-map="1" @endif
                  @if(!empty($api['mapPicker'])) data-map-picker="1" @endif>
                @foreach ($api['fields'] as $field)
                    <div>
                        <label>{{ $field['label'] }}</label>
                        @if (($field['type'] ?? '') === 'hidden')
                            <input type="hidden" name="{{ $field['name'] }}" value="{{ $field['default'] }}">
                        @elseif (($field['type'] ?? '') === 'select')
                            <select name="{{ $field['name'] }}">
                                @foreach ($field['options'] as $val => $label)
                                    <option value="{{ $val }}" @if(($field['default'] ?? '') === $val) selected @endif>{{ $label }}</option>
                                @endforeach
                            </select>
                        @else
                            <input type="{{ $field['type'] ?? 'text' }}"
                                   name="{{ $field['name'] }}"
                                   value="{{ ($field['default'] ?? '') !== '+1hour' ? ($field['default'] ?? '') : '' }}"
                                   @if(!empty($field['step'])) step="{{ $field['step'] }}" @endif
                                   @if(!empty($field['required'])) required @endif
                                   @if(($field['default'] ?? '') === '+1hour') data-default-offset="3600000" @endif
                                   @if(!empty($api['mapPicker']) && in_array($field['name'], ['latitude', 'longitude'])) readonly @endif>
                        @endif
                    </div>
                @endforeach

                @include('test-apis.partials.donor-location', ['api' => $api])
                @include('test-apis.partials.map-blocks', ['api' => $api])
                @include('test-apis.partials.listing-picker', ['api' => $api])

                <div style="display:flex;align-items:flex-end">
                    <button type="submit" class="btn btn-primary">Send request</button>
                </div>
            </form>
        @else
            @include('test-apis.partials.listing-picker', ['api' => $api])
            <div style="margin-bottom:.75rem">
                <button type="button" class="btn btn-primary btn-send-no-form"
                        data-api="{{ $api['id'] }}"
                        data-method="{{ $api['method'] }}"
                        data-path="{{ $api['path'] }}"
                        @if(!empty($api['usesListingId'])) data-uses-listing-id="1" @endif>Send request</button>
            </div>
        @endif

        <div class="response-box" id="response-{{ $api['id'] }}">No request sent yet.</div>
    </div>
</div>
