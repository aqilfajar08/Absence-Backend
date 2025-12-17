@extends('layouts.app')

@section('title', 'Posts')

@push('style')
    <!-- CSS Libraries -->
    <link rel="stylesheet"
        href="{{ asset('library/selectric/public/selectric.css') }}">
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Attendance</h1>
                <div class="section-header-button">
                    <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteModal">
                        <i class="fas fa-trash"></i> Delete Data by Month
                    </button>
                </div>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
                    <div class="breadcrumb-item"><a href="#">Attendance</a></div>
                    <div class="breadcrumb-item">All Attendance</div>
                </div>
            </div>
            @include('layouts.alert')
            <div class="section-body">
                <h2 class="section-title">Attendance</h2>
                <p class="section-lead">
                    You can manage all posts, such as editing, deleting and more.
                </p>
                </div>
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>All Attendance</h4>
                            </div>
                            <div class="card-body">
                                <div class="float-left">
                                    <select class="form-control selectric">
                                        <option>Action For Selected</option>
                                        <option>Move to Draft</option>
                                        <option>Move to Pending</option>
                                        <option>Delete Pemanently</option>
                                    </select>
                                </div>
                                <div class="float-right">
                                    <form>
                                        <div class="input-group">
                                            <input type="text"
                                                name="name"
                                                class="form-control"
                                                placeholder="Search">
                                            <div class="input-group-append">
                                                <button class="btn btn-primary"><i class="fas fa-search"></i></button>
                                            </div>
                                        </div>
                                    </form>
                                </div>

                                <div class="clearfix mb-3"></div>

                                <div class="table-responsive">
                                    <table class="table-striped table">
                                        <tr>
                                            <th>Name</th>
                                            <th>Date</th>
                                            <th>Time In</th>
                                            <th>Time Out</th>
                                            <th>Latlon In</th>
                                            <th>Latlon Out</th>
                                        </tr>
                                        @foreach ($attendances as $attendance)
                                        <tr>                                            
                                            <td>
                                                {{ $attendance->user->name }}
                                            </td>
                                            <td>
                                                {{ $attendance->date_attendance }}
                                            </td>
                                            <td>
                                                {{ $attendance->time_in }}
                                            </td>
                                            <td>
                                                {{ $attendance->time_out }}
                                            </td>
                                            <td>
                                                {{ $attendance->latlon_in }}
                                            </td>
                                            <td>
                                                {{ $attendance->latlon_out }}
                                            </td>
                                        </tr>
                                        @endforeach
                                    </table>
                                </div>
                                <div class="float-right">
                                    <nav>
                                        {{ $attendances->links() }}
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

<!-- Delete Attendance Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="fas fa-trash-alt text-danger"></i> Delete Attendance Data by Month
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('attendance.deleteByMonth') }}" method="POST" onsubmit="return confirm('Are you sure you want to delete ALL attendance data for the selected month? This action cannot be undone!')">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Warning!</strong> This action cannot be undone. All attendance records for the selected month across ALL users will be permanently deleted.
                    </div>
                    <div class="form-group">
                        <label for="deleteMonth">Select Month to Delete</label>
                        <input type="month" class="form-control" id="deleteMonth" name="month" required min="2020-01" max="2030-12">
                        <small class="form-text text-muted">Format: YYYY-MM (e.g., 2025-12 for December 2025)</small>
                    </div>
                    <p class="text-muted mb-0">
                        This will delete <strong>ALL attendance records</strong> for the selected month from <strong>ALL users</strong>.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Delete All Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
    <!-- JS Libraies -->
    <script src="{{ asset('library/selectric/public/jquery.selectric.min.js') }}"></script>

    <!-- Page Specific JS File -->
    <script src="{{ asset('js/page/features-posts.js') }}"></script>
@endpush
