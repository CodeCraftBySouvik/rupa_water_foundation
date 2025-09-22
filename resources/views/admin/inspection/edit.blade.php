@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])
@section('content')

<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow-sm">

                <form method="POST" action="{{ route('inspection.update') }}">
                    @csrf
                    <input type="hidden" name="id" value="{{ $inspection->id }}">

                    <div class="card-header bg-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Edit Inspection</h6>
                            <a href="{{ route('inspection.index') }}" class="btn btn-primary btn-sm">Back</a>
                        </div>
                    </div>

                    <div class="card-body">
                        {{-- Top fields --}}
                        <div class="row g-3 mb-4">
                            <div class="col-4">
                                <label class="form-label">Check‑up&nbsp;Date</label>
                                <input type="date" name="checked_date" value="{{ old('checked_date', $inspection->checked_date) }}" class="form-control">
                            </div>

                            <div class="col-4">
                                <label class="form-label">Checked&nbsp;By</label>
                                <select name="checked_by" id="userSelect" class="form-select js-single-select-user">
                                    <option value="">-- Select User --</option>
                                    @foreach($checkUser as $id => $name)
                                        <option value="{{ $id }}" {{ old('checked_by', $inspection->checked_by) == $id ? 'selected' : '' }}>
                                            {{ ucwords($name) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('checked_by') <p class="text-danger small">{{ $message }}</p> @enderror
                            </div>

                            <div class="col-4">
                                <label class="form-label">Location</label>
                                <select name="location_id" id="locationSelect" class="form-select js-single-select">
                                    <option value="">-- Select Location --</option>
                                    @foreach($locations as $id => $title)
                                        <option value="{{ $id }}" {{ old('location_id', $inspection->location_id) == $id ? 'selected' : '' }}>
                                            {{ $title }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('location_id') <p class="text-danger small">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        {{-- pill radio helper --}}
                        @php
                        function pill($name, $value, $label, $current) {
                            $checked = $current == $value ? 'checked' : '';
                            return "
                                <input type='radio' class='btn-check' name='{$name}' id='{$name}_{$value}' value='{$value}' {$checked}>
                                <label class='btn btn-sm btn-outline-primary me-1 mb-1' for='{$name}_{$value}'>{$label}</label>
                            ";
                        }
                        @endphp

                        <div class="row row-cols-1 row-cols-md-4 g-3">
                            {{-- Each field --}}
                            <div class="mb-3">
                                <label class="form-label small text-muted">Repairing</label><br>
                                {!! pill('repairing','Floor','Floor', old('repairing', $inspection->repairing)) !!}
                                {!! pill('repairing','Machine','Machine', old('repairing', $inspection->repairing)) !!}
                                @error('repairing') <p class="text-danger small">{{ $message }}</p> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label small text-muted">Water quality</label><br>
                                {!! pill('water_quality','good','Good', old('water_quality', $inspection->water_quality)) !!}
                                {!! pill('water_quality','poor','Poor', old('water_quality', $inspection->water_quality)) !!}
                                @error('water_quality') <p class="text-danger small">{{ $message }}</p> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label small text-muted">Electric available</label><br>
                                {!! pill('electric_available','yes','Yes', old('electric_available', $inspection->electric_available)) !!}
                                {!! pill('electric_available','no','No', old('electric_available', $inspection->electric_available)) !!}
                                @error('electric_available') <p class="text-danger small">{{ $message }}</p> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label small text-muted">Cooling system</label><br>
                                {!! pill('cooling_system','working','Working', old('cooling_system', $inspection->cooling_system)) !!}
                                {!! pill('cooling_system','not working','Not Working', old('cooling_system', $inspection->cooling_system)) !!}
                                @error('cooling_system') <p class="text-danger small">{{ $message }}</p> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label small text-muted">Cleanliness</label><br>
                                {!! pill('cleanliness','clean','Clean', old('cleanliness', $inspection->cleanliness)) !!}
                                {!! pill('cleanliness','dirty','Dirty', old('cleanliness', $inspection->cleanliness)) !!}
                                @error('cleanliness') <p class="text-danger small">{{ $message }}</p> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label small text-muted">Tap condition</label><br>
                                {!! pill('tap_condition','present','Present', old('tap_condition', $inspection->tap_condition)) !!}
                                {!! pill('tap_condition','not present','Not Present', old('tap_condition', $inspection->tap_condition)) !!}
                                @error('tap_condition') <p class="text-danger small">{{ $message }}</p> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label small text-muted">Electric meter working</label><br>
                                {!! pill('electric_meter_working','yes','Yes', old('electric_meter_working', $inspection->electric_meter_working)) !!}
                                {!! pill('electric_meter_working','no','No', old('electric_meter_working', $inspection->electric_meter_working)) !!}
                                @error('electric_meter_working') <p class="text-danger small">{{ $message }}</p> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label small text-muted">Compressor condition</label><br>
                                {!! pill('compressor_condition','ok','OK', old('compressor_condition', $inspection->compressor_condition)) !!}
                                {!! pill('compressor_condition','not ok','Not OK', old('compressor_condition', $inspection->compressor_condition)) !!}
                                @error('compressor_condition') <p class="text-danger small">{{ $message }}</p> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label small text-muted">Light availability</label><br>
                                {!! pill('light_availability','yes','Yes', old('light_availability', $inspection->light_availability)) !!}
                                {!! pill('light_availability','no','No', old('light_availability', $inspection->light_availability)) !!}
                                @error('light_availability') <p class="text-danger small">{{ $message }}</p> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label small text-muted">Filter condition</label><br>
                                {!! pill('filter_condition','ok','OK', old('filter_condition', $inspection->filter_condition)) !!}
                                {!! pill('filter_condition','not ok','Not OK', old('filter_condition', $inspection->filter_condition)) !!}
                                @error('filter_condition') <p class="text-danger small">{{ $message }}</p> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label small text-muted">Electric usage method</label><br>
                                {!! pill('electric_usage_method','hooking','Hooking', old('electric_usage_method', $inspection->electric_usage_method)) !!}
                                {!! pill('electric_usage_method','proper','Proper', old('electric_usage_method', $inspection->electric_usage_method)) !!}
                                @error('electric_usage_method') <p class="text-danger small">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        {{-- Notes --}}
                        <div class="mb-4">
                            <label class="form-label small text-muted">Report notes</label>
                            <textarea name="notes" rows="3" class="form-control">{{ old('notes', $inspection->notes) }}</textarea>
                        </div>

                        <button class="btn btn-primary btn-sm">Update Inspection</button>
                       
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

@endsection
@section('scripts')
<script>
    // for location
$(document).ready(function(){
    $('.js-single-select').select2({
        placeholder: "-- Select Location --",
        allowClear: true,
        width: '100%' 
    });
});

// for user
$(document).ready(function(){
    $('.js-single-select-user').select2({
        placeholder: "-- Select User --",
        allowClear: true,
        width: '100%' 
    });
});
</script>
@endsection