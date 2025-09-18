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
                <h1>Users</h1>
                <div class="section-header-button">
                    <button type="button" class="btn btn-success mr-2" data-toggle="modal" data-target="#exportModal">
                        <i class="fas fa-file-excel"></i> Ekspor Absensi
                    </button>
                    <a href="{{ route('user.create') }}"
                        class="btn btn-primary">Add New</a>
                </div>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
                    <div class="breadcrumb-item"><a href="#">Users</a></div>
                    <div class="breadcrumb-item">All Users</div>
                </div>
            </div>
            @include('layouts.alert')
            <div class="section-body">
                <h2 class="section-title">Users</h2>
                <p class="section-lead">
                    You can manage all posts, such as editing, deleting and more.
                </p>
                </div>
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>All Users</h4>
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
                                            <th>Email</th>
                                            <th>Position</th>
                                            <th>Department</th>
                                        </tr>
                                        @foreach ($users as $user)
                                        <tr>
                                            <td> {{ $user->name }}
                                                <div class="table-links">
                                                    <a href="{{route('user.show', $user->id)}}">View</a>
                                                    <div class="bullet"></div>
                                                    <a href="{{route('user.edit', $user->id)}}">Edit</a>
                                                    <div class="bullet"></div>
                                                    <form action="{{ route('user.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure want to delete this user?')">@csrf @method('DELETE')
                                                        <button type="submit" class="btn btn-danger"><i class="fa fa-trash"></i> Delete</button>
                                                    </form>
                                                </div>
                                            </td>
                                            <td>
                                                 {{ $user->email }}
                                            </td>
                                            <td>
                                                {{ $user->position }}
                                            </td>
                                            <td>
                                                {{ $user->department }}
                                            </td>
                                        </tr>
                                        @endforeach
                                    </table>
                                </div>
                                <div class="float-right">
                                    <nav>
                                        {{ $users->links() }}
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Export Modal -->
    <div class="modal fade" id="exportModal" tabindex="-1" role="dialog" aria-labelledby="exportModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exportModalLabel">
                        <i class="fas fa-file-excel text-success"></i> Ekspor Laporan Absensi
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('user.export.attendance') }}" method="GET">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="selected_month"><i class="fas fa-calendar-alt"></i> Pilih Bulan</label>
                            <select class="form-control" id="selected_month" name="selected_month" required>
                                @php
                                    $currentMonth = \Carbon\Carbon::now();
                                    $months = [];
                                    
                                    // Generate last 12 months and next 6 months
                                    for ($i = -12; $i <= 6; $i++) {
                                        $month = $currentMonth->copy()->addMonths($i);
                                        $months[] = [
                                            'value' => $month->format('Y-m'),
                                            'label' => $month->locale('id')->translatedFormat('F Y'),
                                            'current' => $i === 0
                                        ];
                                    }
                                @endphp
                                
                                @foreach($months as $month)
                                    <option value="{{ $month['value'] }}" {{ $month['current'] ? 'selected' : '' }}>
                                        {{ $month['label'] }}{{ $month['current'] ? ' (Bulan Ini)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Pilih bulan untuk laporan absensi</small>
                        </div>
                        
                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle"></i> Informasi Laporan Bulanan:</h6>
                            <ul class="mb-0">
                                <li><strong>Cakupan:</strong> Laporan lengkap satu bulan dengan kolom harian</li>
                                <li><strong>Kode Warna:</strong> 
                                    <span style="background-color: #d4edda; padding: 2px 6px; border-radius: 3px; border: 1px solid #c3e6cb;">Hijau (Tepat Waktu 08:01-08:30)</span>, 
                                    <span style="background-color: #fff3cd; padding: 2px 6px; border-radius: 3px; border: 1px solid #fdbf47;">Orange (Terlambat 08:31-09:00)</span>, 
                                    <span style="background-color: #f8d7da; padding: 2px 6px; border-radius: 3px; border: 1px solid #f5c6cb;">Merah (Sangat Terlambat > 09:00)</span>
                                </li>
                                <li><strong>Keterangan:</strong> 
                                    <span style="background-color: #f8d7da; padding: 2px 6px; border-radius: 3px;">T (Tidak Hadir)</span>, 
                                    <span style="background-color: #e2e3e5; padding: 2px 6px; border-radius: 3px;">L (Libur)</span>
                                </li>
                                <li><strong>Data Termasuk:</strong> Jumlah izin, waktu absensi harian</li>
                                <li><strong>Zona Waktu:</strong> Semua waktu dalam WITA (Waktu Indonesia Tengah)</li>
                            </ul>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-download"></i> Unduh Excel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- JS Libraies -->
    <script src="{{ asset('library/selectric/public/jquery.selectric.min.js') }}"></script>

    <!-- Page Specific JS File -->
    <script src="{{ asset('js/page/features-posts.js') }}"></script>
@endpush
