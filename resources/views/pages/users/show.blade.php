@extends('layouts.app')

@section('title', 'User Details')

@push('style')
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{ asset('library/selectric/public/selectric.css') }}">
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>User Details</h1>
                <div class="section-header-button">
                    <a href="{{ route('user.edit', $user->id) }}" class="btn btn-warning mr-2">
                        <i class="fas fa-edit"></i> Edit User
                    </a>
                    <a href="{{ route('user.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Users
                    </a>
                </div>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
                    <div class="breadcrumb-item"><a href="{{ route('user.index') }}">Users</a></div>
                    <div class="breadcrumb-item">{{ $user->name }}</div>
                </div>
            </div>
            @include('layouts.alert')
            
            <div class="section-body">
                <h2 class="section-title">{{ $user->name }}</h2>
                <p class="section-lead">
                    Detailed information about {{ $user->name }} including attendance and permission records.
                </p>

                <div class="row">
                    <!-- User Information Card -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h4><i class="fas fa-user"></i> Personal Information</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">
                                        <table class="table table-borderless">
                                            <tbody>
                                                <tr>
                                                    <td><strong>Name:</strong></td>
                                                    <td>{{ $user->name }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Email:</strong></td>
                                                    <td>{{ $user->email }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Department:</strong></td>
                                                    <td>{{ $user->department ?? 'Not specified' }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Position:</strong></td>
                                                    <td>{{ $user->position ?? 'Not specified' }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Salary Information Card -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h4><i class="fas fa-money-bill-wave"></i> Salary Information</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">
                                        <table class="table table-borderless">
                                            <tbody>
                                                <tr>
                                                    <td><strong>GP (Gaji Pokok):</strong></td>
                                                    <td>
                                                        @if($user->gaji_pokok)
                                                            Rp {{ number_format($user->gaji_pokok, 0, ',', '.') }}
                                                        @else
                                                            <span class="text-muted">Not set</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong>TJ (Tunjangan):</strong></td>
                                                    <td>
                                                        @if($user->tunjangan)
                                                            Rp {{ number_format($user->tunjangan, 0, ',', '.') }}
                                                        @else
                                                            <span class="text-muted">Not set</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Joined Date:</strong></td>
                                                    <td>{{ $user->created_at->format('d M Y') }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Monthly Statistics -->
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card card-primary">
                            <div class="card-header">
                                <h4><i class="fas fa-clock"></i> Attendance This Month</h4>
                                <div class="card-header-action">
                                    <span class="badge badge-primary">{{ $currentMonth->format('F Y') }}</span>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <h3 class="text-primary mb-1">{{ $attendanceCount }}</h3>
                                        <p class="text-muted mb-0">Total Attendance Days</p>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-center bg-primary rounded-circle" style="width: 60px; height: 60px;">
                                        <i class="fas fa-calendar-check fa-2x text-white"></i>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <a href="{{ route('user.attendance', $user->id) }}" class="btn btn-primary btn-block">
                                        <i class="fas fa-eye"></i> View Detailed Attendance
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card card-warning">
                            <div class="card-header">
                                <h4><i class="fas fa-file-alt"></i> Permissions This Month</h4>
                                <div class="card-header-action">
                                    <span class="badge badge-warning">{{ $currentMonth->format('F Y') }}</span>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <h3 class="text-warning mb-1">{{ $permissionCount }}</h3>
                                        <p class="text-muted mb-0">Total Permission Requests</p>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-center bg-warning rounded-circle" style="width: 60px; height: 60px;">
                                        <i class="fas fa-clipboard-list fa-2x text-white"></i>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <a href="{{ route('user.permission', $user->id) }}" class="btn btn-warning btn-block">
                                        <i class="fas fa-eye"></i> View Detailed Permissions
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4><i class="fas fa-tools"></i> Quick Actions</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3 col-6 mb-3">
                                        <a href="{{ route('user.edit', $user->id) }}" class="btn btn-warning btn-lg btn-block d-flex flex-column align-items-center justify-content-center" style="height: 80px;">
                                            <i class="fas fa-edit fa-2x mb-2"></i>
                                            <span>Edit User</span>
                                        </a>
                                    </div>
                                    <div class="col-md-3 col-6 mb-3">
                                        <a href="{{ route('user.attendance', $user->id) }}" class="btn btn-primary btn-lg btn-block d-flex flex-column align-items-center justify-content-center" style="height: 80px;">
                                            <i class="fas fa-clock fa-2x mb-2"></i>
                                            <span>View Attendance</span>
                                        </a>
                                    </div>
                                    <div class="col-md-3 col-6 mb-3">
                                        <a href="{{ route('user.permission', $user->id) }}" class="btn btn-info btn-lg btn-block d-flex flex-column align-items-center justify-content-center" style="height: 80px;">
                                            <i class="fas fa-clipboard-list fa-2x mb-2"></i>
                                            <span>View Permissions</span>
                                        </a>
                                    </div>
                                    <div class="col-md-3 col-6 mb-3">
                                        <form action="{{ route('user.destroy', $user->id) }}" method="POST" class="w-100" onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-lg btn-block d-flex flex-column align-items-center justify-content-center" style="height: 80px;">
                                                <i class="fas fa-trash fa-2x mb-2"></i>
                                                <span>Delete User</span>
                                            </button>
                                        </form>
                                    </div>
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
    <!-- JS Libraries -->
    <script src="{{ asset('library/selectric/public/jquery.selectric.min.js') }}"></script>
@endpush
