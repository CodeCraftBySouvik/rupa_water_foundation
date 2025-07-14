@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
@include('layouts.navbars.auth.topnav', ['title' => 'Create Inspection'])

<div class="container-fluid py-4">

    {{-- @isset($inspection) @method('PUT') @endisset --}}

    <div class="row justify-content-center">
        <div class="col-md-12">

            <div class="card shadow-sm">
              
                <form role="form" method="POST" action="{{route('inspection.store') }}">
                    @csrf
                    <div class="card-header bg-white align-items-center">
                        <h6 class="mb-0">Inspection</h6>
                        <a href="{{route('inspection.index')}}" class="btn btn-primary btn-sm ms-auto">Back</a>
                    </div>
                    <div class="card-body">

                        {{-- Top two fields ---------------------------------------------------- --}}
                        <div class="row g-3 mb-4">
                            <div class="col-4">
                                <label class="form-label">Check‑up&nbsp;Date</label>
                                <input type="date" name="checked_date" value="{{ old('checked_date') }}"
                                    class="form-control">
                            </div>
                            <div class="col-4">
                                @error('checked_by') <p class="text-danger small">{{ $message }}</p> @enderror
                            </div>
                            <div class="col-4">
                                <label class="form-label">Location</label>
                                <select name="location_id" id="locationSelect" class="form-select js-single-select">
                                    <option value="">-- Select Location --</option>
                                    @foreach($locations as $id => $title)
                                        <option value="{{ $id }}" {{ old('location_id') == $id ? 'selected' : '' }}>
                                            {{ $title }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('location_id') <p class="text-danger small">{{ $message }}</p> @enderror
                        </div>

                        {{-- field helper for pill radios --}}
                        @php
                        function pill($name,$value,$label,$current) {
                        $checked = $current == $value ? 'checked' : '';
                        return "
                        <input type='radio' class='btn-check' name='{$name}' id='{$name}_{$value}' value='{$value}'
                            {$checked}>
                        <label class='btn btn-sm btn-outline-primary me-1 mb-1' for='{$name}_{$value}'>{$label}</label>
                        ";
                        }
                        @endphp

                        <div class="row row-cols-1 row-cols-md-4 g-3">
                            {{-- Water quality ---------------------------------------------------- --}}
                            <div class="mb-3">
                                <label class="form-label small text-muted">Water quality</label><br>
                                {!! pill('water_quality','good','Good',old('water_quality')) !!}
                                {!! pill('water_quality','poor','Poor',old('water_quality')) !!}
                                @error('water_quality')
                                <p class="text-danger small">{{$message}}</p>
                                @enderror
                            </div>

                            {{-- Electric available ------------------------------------------------ --}}
                            <div class="mb-3">
                                <label class="form-label small text-muted">Electric available</label><br>
                                {!! pill('electric_available','yes','Yes',old('electric_available')) !!}
                                {!! pill('electric_available','no','No',old('electric_available')) !!}
                                @error('electric_available')
                                <p class="text-danger small">{{$message}}</p>
                                @enderror
                            </div>

                            {{-- Cooling system ----------------------------------------------------- --}}
                            <div class="mb-3">
                                <label class="form-label small text-muted">Cooling system</label><br>
                                {!! pill('cooling_system','working','Working',old('cooling_system')) !!}
                                {!! pill('cooling_system','not working','Not Working',old('cooling_system')) !!}
                                @error('cooling_system')
                                <p class="text-danger small">{{$message}}</p>
                                @enderror
                            </div>

                            {{-- Cleanliness -------------------------------------------------------- --}}
                            <div class="mb-3">
                                <label class="form-label small text-muted">Cleanliness</label><br>
                                {!! pill('cleanliness','clean','Clean',old('cleanliness')) !!}
                                {!! pill('cleanliness','dirty','Dirty',old('cleanliness')) !!}
                                @error('cleanliness')
                                <p class="text-danger small">{{$message}}</p>
                                @enderror
                            </div>

                            {{-- Tap & glass condition --------------------------------------------- --}}
                            <div class="mb-3">
                                <label class="form-label small text-muted">Tap &amp; glass condition</label><br>
                                {!! pill('tap_glass_condition','present','Present',old('tap_glass_condition')) !!}
                                {!! pill('tap_glass_condition','not
                                present','Not&nbsp;Present',old('tap_glass_condition')) !!}
                                @error('tap_glass_condition')
                                <p class="text-danger small">{{$message}}</p>
                                @enderror
                            </div>

                            {{-- Electric meter working -------------------------------------------- --}}
                            <div class="mb-3">
                                <label class="form-label small text-muted">Electric meter working</label><br>
                                {!! pill('electric_meter_working','yes','Yes',old('electric_meter_working')) !!}
                                {!! pill('electric_meter_working','no','No',old('electric_meter_working')) !!}
                                 @error('electric_meter_working')
                                  <p class="text-danger small">{{$message}}</p>
                                @enderror
                            </div>

                            {{-- Compressor condition ---------------------------------------------- --}}
                            <div class="mb-3">
                                <label class="form-label small text-muted">Compressor condition</label><br>
                                {!! pill('compressor_condition','ok','OK',old('compressor_condition')) !!}
                                {!! pill('compressor_condition','not ok','Not&nbsp;OK',old('compressor_condition')) !!}
                                 @error('compressor_condition')
                                  <p class="text-danger small">{{$message}}</p>
                                @enderror
                            </div>

                            {{-- Light availability ------------------------------------------------ --}}
                            <div class="mb-3">
                                <label class="form-label small text-muted">Light availability</label><br>
                                {!! pill('light_availability','yes','Yes',old('light_availability')) !!}
                                {!! pill('light_availability','no','No',old('light_availability')) !!}
                                 @error('light_availability')
                                  <p class="text-danger small">{{$message}}</p>
                                @enderror
                            </div>

                            {{-- Filter condition --------------------------------------------------- --}}
                            <div class="mb-3">
                                <label class="form-label small text-muted">Filter condition</label><br>
                                {!! pill('filter_condition','ok','OK',old('filter_condition')) !!}
                                {!! pill('filter_condition','not ok','Not&nbsp;OK',old('filter_condition')) !!}
                                 @error('filter_condition')
                                  <p class="text-danger small">{{$message}}</p>
                                @enderror
                            </div>

                            {{-- Electric usage method --------------------------------------------- --}}
                            <div class="mb-3">
                                <label class="form-label small text-muted">Electric usage method</label><br>
                                {!! pill('electric_usage_method','hooking','Hooking',old('electric_usage_method')) !!}
                                {!! pill('electric_usage_method','proper','Proper',old('electric_usage_method')) !!}
                                @error('electric_usage_method')
                                  <p class="text-danger small">{{$message}}</p>
                                @enderror
                            </div>
                        </div>
                        {{-- Notes -------------------------------------------------------------- --}}
                        <div class="mb-4">
                            <label class="form-label small text-muted">Report notes</label>
                            <textarea name="notes" rows="3" class="form-control">{{ old('notes') }}</textarea>
                        </div>

                        <button class="btn btn-danger btn-sm">Save and Continue</button>

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

//for user
$(document).ready(function(){
    $('.js-single-select-user').select2({
        placeholder: "-- Select User --",
        allowClear: true,
        width: '100%' 
    });
});
</script>
@endsection