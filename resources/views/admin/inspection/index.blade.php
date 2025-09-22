@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')

<div class="container-fluid py-4">

    <div class="card mb-4">
        <div id="alert">
            @include('components.alert')
        </div>
        <div class="card-header pb-0 d-flex justify-content-between align-items-center">
            <h6>Inspection History</h6>
            <a href="{{route('inspection.create')}}" class="btn btn-primary btn-sm">New Inspection</a>
        </div>

        <div class="card-body px-0 pt-0 pb-2">
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
                                Action </th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($inspections as $in)
                        <tr class="text-center">

                            {{-- checked date --}}
                            <td class="text-xs font-weight-bold">
                                {{ \Carbon\Carbon::parse($in->checked_date)->format('d M Y') }}
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

                            {{-- actions --}}
                            <td class="text-end">
                                <a href="{{route('inspection.edit',$in->id)}}" class="btn btn-dark btn-sm mt-1">Edit</a>

                               
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


</div>
@endsection
@section('scripts')
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

@endsection