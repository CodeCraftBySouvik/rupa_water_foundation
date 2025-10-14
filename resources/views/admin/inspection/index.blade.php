@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')

<div class="container-fluid py-4">

    <div class="card mb-4">
        <div id="alert">
            @include('components.alert')
        </div>
        <div
            class="card-header pb-0 d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
            <h6 class="mb-2 mb-md-0">Inspection History (Total {{$inspections->count()}})</h6>
            <div class="d-flex flex-wrap align-items-end gap-2">
                <form id="filterForm" action="{{route('inspection.index')}}" method="GET"
                    class="d-flex gap-2 flex-wrap align-items-end">

                    <div class="d-flex flex-column" style="margin-bottom: 15px;">
                        <label for="search">Search</label>
                        <input type="text" class="form-control form-control-sm" id="search" name="search"
                            placeholder="Search by location, checker" style="width: 200px;"
                            value="{{ request('search') }}">
                    </div>
                    <div class="d-flex flex-column" style="margin-bottom: 15px;">
                        <!-- Start Date -->
                        <label for="start_date">Start Date</label>
                        <input type="date" class="form-control form-control-sm" id="start_date" name="start_date"
                            placeholder="Start Date" style="width: 150px;" value="{{ request('start_date') }}">
                    </div>
                    <div class="d-flex flex-column" style="margin-bottom: 15px;">
                        <!-- End Date -->
                        <label for="end_date">End Date</label>
                        <input type="date" class="form-control form-control-sm" id="end_date" name="end_date"
                            placeholder="End Date" style="width: 150px;" value="{{ request('end_date') }}">
                    </div>
                    <div class="d-flex flex-column">
                        <label>&nbsp;</label>
                        <a href="{{route('inspection.index')}}" class="btn btn-primary btn-sm" id="refreshBtn"> <i
                                class="fa fa-refresh"></i></a>
                    </div>
                    @if(!\App\Helpers\Helpers::isSupervisor())
                    <div class="d-flex flex-column">
                        <label>&nbsp;</label>
                        <button type="submit" formaction="{{ route('inspection.export') }}"
                            class="btn btn-success btn-sm" id="exportBtn"> <i class="fa fa-download"></i>
                            Export</button>
                    </div>
                    <a href="{{route('inspection.create')}}" class="btn btn-primary btn-sm">New Inspection</a>
                    @endif
                </form>
            </div>
        </div>

        <div class="card-body px-0 pt-0 pb-2 mt-2">
            <div class="table-responsive p-0">

                <table class="table align-items-center mb-0">
                    <thead>
                        <tr class="text-center">
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Date</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                Location</th>

                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">
                                Report</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">
                                Checked By</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">
                                GeoLocation</th>
                           
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">
                                Action </th>
                        </tr>
                    </thead>

                    <tbody id="inspectionTableBody">
                        @forelse($inspections as $in)
                        <tr class="text-center">

                            {{-- checked date --}}
                            <td class="text-xs font-weight-bold" data-date="{{ $in->checked_date }}">
                                {{ \Carbon\Carbon::parse($in->checked_date)->format('d M Y') }}
                            </td>

                            {{-- location title --}}
                            <td>
                                <p class="text-xs font-weight-bold mb-0">
                                    {{ $in->location->title ?? '-' }}
                                </p>
                                <span class="text-xs text-muted">{{ $in->location->location_id ?? '' }}</span>
                            </td>
                            <td>
                                <button class="btn btn-outline-primary btn-sm mt-1 show-report" data-id="{{ $in->id }}"
                                    data-location="{{ $in->location->location_id ?? '' }}"
                                    data-checked-by="{{ $in->checker->name ?? '—' }}"
                                    data-checked-date="{{ $in->checked_date }}" data-repairing="{{ $in->repairing }}"
                                    data-water-quality="{{ $in->water_quality }}"
                                    data-electric="{{ $in->electric_available }}"
                                    data-cooling="{{ $in->cooling_system }}" data-cleanliness="{{ $in->cleanliness }}"
                                    data-tap="{{ $in->tap_condition }}" data-meter="{{ $in->electric_meter_working }}"
                                    data-compressor="{{ $in->compressor_condition }}"
                                    data-light="{{ $in->light_availability }}" data-filter="{{ $in->filter_condition }}"
                                    data-usage="{{ $in->electric_usage_method }}" data-notes="{{ $in->notes }}"
                                    data-created="{{ $in->created_at }}" data-updated="{{ $in->updated_at }}">
                                    Report
                                </button>
                            </td>

                            {{-- checker --}}
                            <td class="text-center">
                                <p class="text-xs mb-0">{{ $in->checker->name ?? '—' }}</p>
                            </td>

                            {{-- Geolocation --}}
                            <td class="text-center">
                                <p class="text-xs mb-0">
                                    <button class="btn btn-outline-primary btn-sm mt-1 show-location"
                                        data-id="{{ $in->id }}" data-lat="{{$in->latitude}}"
                                        data-lng="{{$in->longitude}}" data-address="{{$in->address}}">
                                        Location
                                    </button>
                                </p>
                            </td>
                            
                            {{-- actions --}}
                            <td class="text-end">
                                 @if(!\App\Helpers\Helpers::isSupervisor())
                                -1">Edit</a>
                                @endif

                                <a href="{{ route('inspection.galleries.list', ['inspection_id' => $in->id]) }}"
                                    class="btn btn-info btn-sm mt-1">
                                    Gallery</a>
                            </td>
                            
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">No inspection records yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

            </div>
        </div>
    </div>

    <!-- Report Modal -->
    <div class="modal fade" id="reportModal" tabindex="-1" aria-labelledby="reportModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="reportModalLabel">Inspection Report</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <table class="table table-bordered table-sm">
                        <tbody class="text-center">
                            {{-- <tr>
                                <th>ID</th>
                                <td id="report-id"></td>
                            </tr> --}}
                            <tr>
                                <th>Location ID</th>
                                <td id="report-location"></td>
                            </tr>
                            <tr>
                                <th>Checked By</th>
                                <td id="report-checked-by"></td>
                            </tr>
                            <tr>
                                <th>Checked Date</th>
                                <td id="report-checked-date"></td>
                            </tr>
                            <tr>
                                <th>Repairing</th>
                                <td id="report-repairing"></td>
                            </tr>
                            <tr>
                                <th>Water Quality</th>
                                <td id="report-water-quality"></td>
                            </tr>
                            <tr>
                                <th>Electric Available</th>
                                <td id="report-electric"></td>
                            </tr>
                            <tr>
                                <th>Cooling System</th>
                                <td id="report-cooling"></td>
                            </tr>
                            <tr>
                                <th>Cleanliness</th>
                                <td id="report-cleanliness"></td>
                            </tr>
                            <tr>
                                <th>Tap Condition</th>
                                <td id="report-tap"></td>
                            </tr>
                            <tr>
                                <th>Electric Meter</th>
                                <td id="report-meter"></td>
                            </tr>
                            <tr>
                                <th>Compressor</th>
                                <td id="report-compressor"></td>
                            </tr>
                            <tr>
                                <th>Light</th>
                                <td id="report-light"></td>
                            </tr>
                            <tr>
                                <th>Filter</th>
                                <td id="report-filter"></td>
                            </tr>
                            <tr>
                                <th>Electric Usage Method</th>
                                <td id="report-usage"></td>
                            </tr>
                            <tr>
                                <th>Notes</th>
                                <td id="report-notes"></td>
                            </tr>

                        </tbody>
                    </table>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>

            </div>
        </div>
    </div>

    {{-- Geo Location Modal --}}
    <!-- Location Modal -->
    <div class="modal fade" id="locationModal" tabindex="-1" aria-labelledby="locationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="locationModalLabel">GeoLocation Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered table-sm text-center">
                        <tr>
                            <th>Latitude</th>
                            <td id="geo-latitude"></td>
                        </tr>
                        <tr>
                            <th>Longitude</th>
                            <td id="geo-longitude"></td>
                        </tr>
                        <tr>
                            <th>Address</th>
                            <td id="geo-address"></td>
                        </tr>

                    </table>
                </div>
                <div class="modal-footer">

                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


</div>
@endsection
@section('scripts')

{{-- <script>
    document.addEventListener('DOMContentLoaded', function () {
    const startDate = document.getElementById('start_date');
    const endDate = document.getElementById('end_date');
    const table = document.querySelector('table tbody');

    function filterRows() {
    const start = startDate.value ? new Date(startDate.value) : null;
    const end = endDate.value ? new Date(endDate.value) : null;

    const rows = table.querySelectorAll('tr');

    rows.forEach(row => {
        const dateCell = row.querySelector('td:first-child');
        if (!dateCell) return;

        const rowDate = new Date(dateCell.dataset.date); // use data-date in YYYY-MM-DD
        let show = true;

        if (start && rowDate < start) show = false;
        if (end && rowDate > end) show = false;

        row.style.display = show ? '' : 'none';
    });
}


    startDate.addEventListener('change', filterRows);
    endDate.addEventListener('change', filterRows);
});
</script> --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
    const startDate = document.getElementById('start_date');
    const endDate   = document.getElementById('end_date');
    const search    = document.getElementById('search'); // 🔎 add search input
    const table     = document.querySelector('table tbody');

    function filterRows() {
        const start = startDate.value ? new Date(startDate.value) : null;
        const end   = endDate.value ? new Date(endDate.value) : null;
        const keyword = search ? search.value.toLowerCase() : "";

        const rows = table.querySelectorAll('tr');

        rows.forEach(row => {
            const dateCell = row.querySelector('td:first-child');
            if (!dateCell) return;

            const rowDate = new Date(dateCell.dataset.date); // data-date="YYYY-MM-DD"
            const rowText = row.innerText.toLowerCase();     // full row text

            let show = true;

            if (start && rowDate < start) show = false;
            if (end && rowDate > end) show = false;
            if (keyword && !rowText.includes(keyword)) show = false;

            row.style.display = show ? '' : 'none';
        });
    }

    // Events
    startDate.addEventListener('change', filterRows);
    endDate.addEventListener('change', filterRows);
    if (search) {
        search.addEventListener('keyup', filterRows); // 🔎 live search
    }
});
</script>


<script>
    document.addEventListener('DOMContentLoaded', function () {
    const reportButtons = document.querySelectorAll('.show-report');
    const modal = new bootstrap.Modal(document.getElementById('reportModal'));

    reportButtons.forEach(btn => {
        btn.addEventListener('click', function () {
            // document.getElementById('report-id').innerText = this.dataset.id;
            document.getElementById('report-location').innerText = this.dataset.location;
            document.getElementById('report-checked-by').innerText = this.dataset['checkedBy'];
            document.getElementById('report-checked-date').innerText = this.dataset['checkedDate'];
            document.getElementById('report-repairing').innerText = this.dataset.repairing;
            document.getElementById('report-water-quality').innerText = this.dataset['waterQuality'];
            document.getElementById('report-electric').innerText = this.dataset.electric;
            document.getElementById('report-cooling').innerText = this.dataset.cooling;
            document.getElementById('report-cleanliness').innerText = this.dataset.cleanliness;
            document.getElementById('report-tap').innerText = this.dataset.tap;
            document.getElementById('report-meter').innerText = this.dataset.meter;
            document.getElementById('report-compressor').innerText = this.dataset.compressor;
            document.getElementById('report-light').innerText = this.dataset.light;
            document.getElementById('report-filter').innerText = this.dataset.filter;
            document.getElementById('report-usage').innerText = this.dataset.usage;
            document.getElementById('report-notes').innerText = this.dataset.notes;

            modal.show();
        });
    });
});
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
    const locationButtons = document.querySelectorAll('.show-location');
    const locationModal = new bootstrap.Modal(document.getElementById('locationModal'));

    locationButtons.forEach(btn => {
        btn.addEventListener('click', function () {
            const lat = this.dataset.lat || '—';
            const lng = this.dataset.lng || '—';
            const address = this.dataset.address || '—';
            

            document.getElementById('geo-latitude').innerText = lat;
            document.getElementById('geo-longitude').innerText = lng;
            document.getElementById('geo-address').innerText = address;


            locationModal.show();
        });
    });
});
</script>


@endsection