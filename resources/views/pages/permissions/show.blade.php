@extends('layouts.app')

@section('title', 'Profile')

@push('style')
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{ asset('library/summernote/dist/summernote-bs4.css') }}">
    <link rel="stylesheet" href="{{ asset('library/bootstrap-social/assets/css/bootstrap.css') }}">
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Profile Perusahaan</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
                    <div class="breadcrumb-item">Profile Perusahaan</div>
                </div>
            </div>
            <div class="section-body">
                <h2 class="section-title">Profil Perusahaan</h2>
                <p class="section-lead">
                    Informasi tentang perusahaan Anda.
                </p>

                <div class="row mt-sm-4">
                    <div class="col-12 col-md-12 col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="form-group col-md-6 col-12">
                                        <label>Name</label>
                                        <p>{{ $permission->user->name }}</p>
                                    </div>
                                    <div class="form-group col-md-6 col-12">
                                        <label>Position</label>
                                        <p>{{ $permission->user->position }}</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-6 col-12">
                                        <label>department</label>
                                        <p>{{ $permission->user->department }}</p>
                                    </div>
                                    <div class="form-group col-md-6 col-12">
                                        <label>date</label>
                                        <p>{{ $permission->date_permission }}</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-6 col-12">
                                        <label>reason</label>
                                        <p>{{ $permission->reason }}</p>
                                    </div>
                                    <div class="form-group col-md-6 col-12">
                                        <label>email</label>
                                        <p>{{ $permission->user->email }}</p>
                                    </div>
                                </div>
                                <div class="row">

                                    <div class="form-group col-md-6 col-12">
                                        <label>Bukti dukung</label>
                                        <div>
                                            @if ($permission->image)
                                                <img src="{{ asset('storage/permissions/' . $permission->image) }}" alt="image surat izin" style="width: 200px; height: 200px; object-fit: cover; border: 1px solid #ddd; border-radius: 4px;">
                                            @else
                                                tidak ada bukti dukung 
                                            @endif
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6 col-12">
                                        <label>status</label>
                                        <p>
                                            @if($permission->is_approved === 1)
                                                Approved
                                            @elseif($permission->is_approved === 0)
                                                Pending
                                            @else
                                                Rejected
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer text-right">
                                <a href="{{ route('permission.edit', $permission->id) }}" class="btn btn-primary">Edit Permission
                                    for approve</a>
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
    <script src="{{ asset('library/summernote/dist/summernote-bs4.js') }}"></script>

    <!-- Page Specific JS File -->
@endpush