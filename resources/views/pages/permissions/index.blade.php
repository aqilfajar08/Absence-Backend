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
                                            <th>Position</th>
                                            <th>Department</th>
                                            <th>Time</th>
                                            <th>Status</th>
                                        </tr>
                                        @foreach ($permissions as $permission)
                                        <tr>
                                            <td> {{ $permission->user->name }}
                                                <div class="table-links">
                                                    <a href="{{ route('permission.show', $permission->id) }}">View</a>
                                                    {{-- <form action="{{ route('user.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure want to delete this user?')">@csrf @method('DELETE')
                                                        <button type="submit" class="btn btn-danger"><i class="fa fa-trash"></i> Delete</button>
                                                    </form> --}}
                                                </div>
                                            </td>
                                            <td>
                                                {{ $permission->date_permission }}
                                            </td>
                                            <td>
                                                {{ $permission->user->position }}
                                            </td>
                                            <td>
                                                {{ $permission->user->department }}
                                            </td>
                                            <td>
                                                {{ $permission->created_at->diffForHumans() }}
                                            </td>
                                            <td>
                                                @if ($permission->is_approved == 'approved')
                                                    <div class="badge badge-success">Approved</div>
                                                @elseif ($permission->is_approved == 'rejected')
                                                    <div class="badge badge-danger">Rejected</div>
                                                @else
                                                    <div class="badge badge-warning">Pending</div>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </table>
                                </div>
                                <div class="float-right">
                                    <nav>
                                        {{ $permissions->links() }}
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

@push('scripts')
    <!-- JS Libraies -->
    <script src="{{ asset('library/selectric/public/jquery.selectric.min.js') }}"></script>

    <!-- Page Specific JS File -->
    <script src="{{ asset('js/page/features-posts.js') }}"></script>
@endpush
