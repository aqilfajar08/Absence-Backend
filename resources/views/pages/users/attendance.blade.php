@extends('layouts.app')

@section('title', 'User Attendance')

@push('style')
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{ asset('library/selectric/public/selectric.css') }}">
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>{{ $user->name }} - Attendance Records</h1>
                <div class="section-header-button">
                    <a href="{{ route('user.show', $user->id) }}" class="btn btn-secondary mr-2">
                        <i class="fas fa-arrow-left"></i> Back to User Details
                    </a>
                    <a href="{{ route('user.index') }}" class="btn btn-secondary">
                        <i class="fas fa-users"></i> All Users
                    </a>
                </div>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
                    <div class="breadcrumb-item"><a href="{{ route('user.index') }}">Users</a></div>
                    <div class="breadcrumb-item"><a href="{{ route('user.show', $user->id) }}">{{ $user->name }}</a></div>
                    <div class="breadcrumb-item">Attendance</div>
                </div>
            </div>
            @include('layouts.alert')
            
            <div class="section-body">
                <h2 class="section-title">Attendance Records - {{ $currentMonth->format('F Y') }}</h2>
                <p class="section-lead">
                    All attendance records for {{ $user->name }} in {{ $currentMonth->format('F Y') }}.
                </p>

                <!-- User Info Summary -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card card-primary">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <h5 class="mb-1 font-weight-bold">{{ $user->name }}</h5>
                                        <p class="text-muted mb-0 small">{{ $user->department }} - {{ $user->position }}</p>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-center bg-primary rounded-circle" style="width: 50px; height: 50px;">
                                        <i class="fas fa-user fa-lg text-white"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card card-success">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <h4 class="text-success mb-1">{{ $attendances->total() }}</h4>
                                        <p class="text-muted mb-0 small">Total Attendance Days</p>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-center bg-success rounded-circle" style="width: 50px; height: 50px;">
                                        <i class="fas fa-calendar-check fa-lg text-white"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card card-info">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <h5 class="text-info mb-1">{{ $currentMonth->format('F Y') }}</h5>
                                        <p class="text-muted mb-0 small">Current Period</p>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-center bg-info rounded-circle" style="width: 50px; height: 50px;">
                                        <i class="fas fa-calendar-alt fa-lg text-white"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Attendance Table -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4><i class="fas fa-clock"></i> Attendance Records</h4>
                            </div>
                            <div class="card-body">
                                @if($attendances->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Day</th>
                                                    <th>Time In</th>
                                                    <th>Time Out</th>
                                                    <th>Working Hours</th>
                                                    <th>Status</th>
                                                    <th>Location</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($attendances as $attendance)
                                                    <tr>
                                                        <td>
                                                            <div class="font-weight-600">
                                                                {{ \Carbon\Carbon::parse($attendance->date_attendance)->format('d M Y') }}
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <span class="badge badge-light">
                                                                {{ \Carbon\Carbon::parse($attendance->date_attendance)->format('l') }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            @if($attendance->time_in)
                                                                <div class="d-flex align-items-center justify-content-center">
                                                                    <div class="d-flex align-items-center bg-success rounded px-2 py-1">
                                                                        <i class="fas fa-sign-in-alt text-white mr-2"></i>
                                                                        <span class="font-weight-600 text-white">{{ $attendance->time_in_formatted }}</span>
                                                                    </div>
                                                                </div>
                                                            @else
                                                                <div class="text-center">
                                                                    <span class="text-muted">-</span>
                                                                </div>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($attendance->time_out)
                                                                <div class="d-flex align-items-center justify-content-center">
                                                                    <div class="d-flex align-items-center bg-danger rounded px-2 py-1">
                                                                        <i class="fas fa-sign-out-alt text-white mr-2"></i>
                                                                        <span class="font-weight-600 text-white">{{ $attendance->time_out_formatted }}</span>
                                                                    </div>
                                                                </div>
                                                            @else
                                                                <div class="text-center">
                                                                    <span class="badge badge-warning px-3 py-2">
                                                                        <i class="fas fa-clock mr-1"></i>Still Working
                                                                    </span>
                                                                </div>
                                                            @endif
                                                        </td>
                                                        <td class="text-center">
                                                            @if($attendance->time_in && $attendance->time_out)
                                                                @php
                                                                    $timeIn = \Carbon\Carbon::parse($attendance->time_in);
                                                                    $timeOut = \Carbon\Carbon::parse($attendance->time_out);
                                                                    $workingHours = $timeOut->diff($timeIn);
                                                                @endphp
                                                                <span class="badge badge-primary px-3 py-2">
                                                                    <i class="fas fa-clock mr-1"></i>{{ $workingHours->format('%H:%I') }} hours
                                                                </span>
                                                            @else
                                                                <span class="text-muted">-</span>
                                                            @endif
                                                        </td>
                                                        <td class="text-center">
                                                            @if($attendance->time_in)
                                                                @php
                                                                    $timeIn = \Carbon\Carbon::parse($attendance->time_in);
                                                                    $standardTime = \Carbon\Carbon::parse('08:30');
                                                                    $lateTime = \Carbon\Carbon::parse('09:00');
                                                                @endphp
                                                                @if($timeIn->lte($standardTime))
                                                                    <span class="badge badge-success px-3 py-2">
                                                                        <i class="fas fa-check mr-1"></i>On Time
                                                                    </span>
                                                                @elseif($timeIn->lte($lateTime))
                                                                    <span class="badge badge-warning px-3 py-2">
                                                                        <i class="fas fa-exclamation mr-1"></i>Late
                                                                    </span>
                                                                @else
                                                                    <span class="badge badge-danger px-3 py-2">
                                                                        <i class="fas fa-times mr-1"></i>Very Late
                                                                    </span>
                                                                @endif
                                                            @else
                                                                <span class="badge badge-secondary px-3 py-2">
                                                                    <i class="fas fa-question mr-1"></i>No Check-in
                                                                </span>
                                                            @endif
                                                        </td>
                                                        <td class="text-center">
                                                            @if($attendance->latlon_in)
                                                                <div class="d-flex justify-content-center">
                                                                    <div class="btn-group">
                                                                        <button type="button" class="btn btn-sm btn-outline-primary d-flex align-items-center" 
                                                                                onclick="showLocation('{{ $attendance->latlon_in }}', 'Check-in Location')"
                                                                                title="Check-in Location">
                                                                            <i class="fas fa-map-marker-alt mr-1"></i>In
                                                                        </button>
                                                                        @if($attendance->latlon_out)
                                                                            <button type="button" class="btn btn-sm btn-outline-danger d-flex align-items-center" 
                                                                                    onclick="showLocation('{{ $attendance->latlon_out }}', 'Check-out Location')"
                                                                                    title="Check-out Location">
                                                                                <i class="fas fa-map-marker-alt mr-1"></i>Out
                                                                            </button>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            @else
                                                                <span class="text-muted">-</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    <!-- Pagination -->
                                    <div class="d-flex justify-content-center mt-4">
                                        {{ $attendances->links() }}
                                    </div>
                                @else
                                    <div class="empty-state" data-height="400">
                                        <div class="empty-state-icon">
                                            <i class="fas fa-calendar-times"></i>
                                        </div>
                                        <h2>No Attendance Records</h2>
                                        <p class="lead">
                                            {{ $user->name }} has no attendance records for {{ $currentMonth->format('F Y') }}.
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Location Modal -->
    <div class="modal fade" id="locationModal" tabindex="-1" role="dialog" aria-labelledby="locationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="locationModalLabel">Location Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="locationInfo"></div>
                    <div id="mapContainer" style="height: 300px; margin-top: 15px;">
                        <div class="text-center py-5">
                            <i class="fas fa-map-marked-alt fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Click "View on Google Maps" to see the exact location</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <a href="#" id="googleMapsLink" class="btn btn-primary" target="_blank">
                        <i class="fas fa-external-link-alt"></i> View on Google Maps
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- JS Libraries -->
    <script src="{{ asset('library/selectric/public/jquery.selectric.min.js') }}"></script>
    
    <script>
        function showLocation(latlon, title) {
            // Parse latitude and longitude
            const coords = latlon.split(',');
            const lat = parseFloat(coords[0]);
            const lng = parseFloat(coords[1]);
            
            // Update modal title and content
            document.getElementById('locationModalLabel').textContent = title;
            document.getElementById('locationInfo').innerHTML = `
                <div class="row">
                    <div class="col-6">
                        <strong>Latitude:</strong><br>
                        <span class="text-primary">${lat}</span>
                    </div>
                    <div class="col-6">
                        <strong>Longitude:</strong><br>
                        <span class="text-primary">${lng}</span>
                    </div>
                </div>
            `;
            
            // Update Google Maps link
            const googleMapsUrl = `https://www.google.com/maps?q=${lat},${lng}&z=15`;
            document.getElementById('googleMapsLink').href = googleMapsUrl;
            
            // Show modal
            $('#locationModal').modal('show');
        }
    </script>
@endpush
