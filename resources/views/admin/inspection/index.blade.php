@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
@include('layouts.navbars.auth.topnav', ['title' => 'Inspection History'])

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
                        <tr>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Date</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                Location</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                Water Quality</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                Electric</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Cooling
                            </th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                Cleanliness</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">
                                Checked By</th>
                            <th class="text-secondary opacity-7 text-end pe-3"> </th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($inspections as $in)
                        <tr>

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

                            {{-- water quality badge --}}
                            <td>
                                <span
                                    class="badge badge-sm {{ $in->water_quality == 'good' ? 'bg-gradient-success' : 'bg-gradient-danger' }}">
                                    {{ ucfirst($in->water_quality) }}
                                </span>
                            </td>

                            {{-- electric available --}}
                            <td>
                                <span
                                    class="badge badge-sm {{ $in->electric_available == 'yes' ? 'bg-gradient-success' : 'bg-gradient-danger' }}">
                                    {{ ucfirst($in->electric_available) }}
                                </span>
                            </td>

                            {{-- cooling system --}}
                            <td>
                                <span
                                    class="badge badge-sm {{ $in->cooling_system == 'working' ? 'bg-gradient-success' : 'bg-gradient-warning' }}">
                                    {{ ucfirst($in->cooling_system) }}
                                </span>
                            </td>

                            {{-- cleanliness --}}
                            <td>
                                <span
                                    class="badge badge-sm {{ $in->cleanliness == 'clean' ? 'bg-gradient-success' : 'bg-gradient-danger' }}">
                                    {{ ucfirst($in->cleanliness) }}
                                </span>
                            </td>

                            {{-- checker --}}
                            <td class="text-center">
                                <p class="text-xs mb-0">{{ $in->checker->name ?? '—' }}</p>
                            </td>

                            {{-- actions --}}
                            <td class="text-end">
                                <a href="" class="btn btn-sm btn-secondary">Edit</a>

                                <form method="POST" action="" class="d-inline"
                                    onsubmit="return confirm('Delete this inspection?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger">Del</button>
                                </form>
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

    {{-- Laravel pagination links (if you used paginate()) --}}
    {{-- {{ $inspections->links() }} --}}

</div>
@endsection