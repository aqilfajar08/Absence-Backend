@extends('layouts.app')

@section('title', 'User Permissions')

@push('style')
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{ asset('library/selectric/public/selectric.css') }}">
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>{{ $user->name }} - Permission Records</h1>
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
                    <div class="breadcrumb-item">Permissions</div>
                </div>
            </div>
            @include('layouts.alert')
            
            <div class="section-body">
                <h2 class="section-title">Permission Records - {{ $currentMonth->format('F Y') }}</h2>
                <p class="section-lead">
                    All permission requests for {{ $user->name }} in {{ $currentMonth->format('F Y') }}.
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
                        <div class="card card-warning">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <h4 class="text-warning mb-1">{{ $permissions->total() }}</h4>
                                        <p class="text-muted mb-0 small">Total Permission Requests</p>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-center bg-warning rounded-circle" style="width: 50px; height: 50px;">
                                        <i class="fas fa-clipboard-list fa-lg text-white"></i>
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

                <!-- Permission Statistics -->
                @if($permissions->count() > 0)
                    @php
                        $approvedCount = $permissions->where('is_approved', 1)->count();
                        $pendingCount = $permissions->where('is_approved', 0)->count();
                        $rejectedCount = $permissions->where('is_approved', -1)->count();
                    @endphp
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card card-success">
                                <div class="card-body">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <h3 class="text-success mb-1">{{ $approvedCount }}</h3>
                                            <p class="text-muted mb-0">Approved</p>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-center bg-success rounded-circle" style="width: 45px; height: 45px;">
                                            <i class="fas fa-check fa-lg text-white"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card card-warning">
                                <div class="card-body">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <h3 class="text-warning mb-1">{{ $pendingCount }}</h3>
                                            <p class="text-muted mb-0">Pending</p>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-center bg-warning rounded-circle" style="width: 45px; height: 45px;">
                                            <i class="fas fa-clock fa-lg text-white"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card card-danger">
                                <div class="card-body">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <h3 class="text-danger mb-1">{{ $rejectedCount }}</h3>
                                            <p class="text-muted mb-0">Rejected</p>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-center bg-danger rounded-circle" style="width: 45px; height: 45px;">
                                            <i class="fas fa-times fa-lg text-white"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Permission Table -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4><i class="fas fa-clipboard-list"></i> Permission Records</h4>
                            </div>
                            <div class="card-body">
                                @if($permissions->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Day</th>
                                                    <th>Reason</th>
                                                    <th>Status</th>
                                                    <th>Attachment</th>
                                                    <th>Submitted</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($permissions as $permission)
                                                    <tr>
                                                        <td>
                                                            <div class="font-weight-600">
                                                                {{ \Carbon\Carbon::parse($permission->date_permission)->format('d M Y') }}
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <span class="badge badge-light">
                                                                {{ \Carbon\Carbon::parse($permission->date_permission)->format('l') }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <div class="text-truncate" style="max-width: 200px;" title="{{ $permission->reason }}">
                                                                {{ $permission->reason }}
                                                            </div>
                                                        </td>
                                                        <td class="text-center">
                                                            @if($permission->is_approved === 1)
                                                                <span class="badge badge-success px-3 py-2">
                                                                    <i class="fas fa-check mr-1"></i>Approved
                                                                </span>
                                                            @elseif($permission->is_approved === 0)
                                                                <span class="badge badge-warning px-3 py-2">
                                                                    <i class="fas fa-clock mr-1"></i>Pending
                                                                </span>
                                                            @else
                                                                <span class="badge badge-danger px-3 py-2">
                                                                    <i class="fas fa-times mr-1"></i>Rejected
                                                                </span>
                                                            @endif
                                                        </td>
                                                        <td class="text-center">
                                                            @if($permission->image)
                                                                <div class="d-flex flex-column align-items-center">
                                                                    <button type="button" class="btn btn-sm btn-outline-primary mb-1 d-flex align-items-center" 
                                                                            onclick="showImage('{{ asset('storage/permissions/' . $permission->image) }}', '{{ $permission->reason }}')"
                                                                            title="View Attachment">
                                                                        <i class="fas fa-image mr-1"></i>View
                                                                    </button>
                                                                    <small class="text-muted text-truncate" style="max-width: 80px;">{{ $permission->image }}</small>
                                                                </div>
                                                            @else
                                                                <span class="text-muted">-</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <div class="text-muted small">
                                                                {{ $permission->created_at->format('d M Y, H:i') }}
                                                            </div>
                                                        </td>
                                                        <td class="text-center">
                                                            @if($permission->is_approved === 0)
                                                                <div class="d-flex justify-content-center">
                                                                    <div class="btn-group">
                                                                        <form action="{{ route('permission.update', $permission->id) }}" method="POST" class="d-inline">
                                                                            @csrf
                                                                            @method('PATCH')
                                                                            <input type="hidden" name="is_approved" value="1">
                                                                            <button type="submit" class="btn btn-sm btn-success d-flex align-items-center" 
                                                                                    onclick="return confirm('Approve this permission request?')"
                                                                                    title="Approve">
                                                                                <i class="fas fa-check"></i>
                                                                            </button>
                                                                        </form>
                                                                        <form action="{{ route('permission.update', $permission->id) }}" method="POST" class="d-inline">
                                                                            @csrf
                                                                            @method('PATCH')
                                                                            <input type="hidden" name="is_approved" value="-1">
                                                                            <button type="submit" class="btn btn-sm btn-danger d-flex align-items-center" 
                                                                                    onclick="return confirm('Reject this permission request?')"
                                                                                    title="Reject">
                                                                                <i class="fas fa-times"></i>
                                                                            </button>
                                                                        </form>
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
                                        {{ $permissions->links() }}
                                    </div>
                                @else
                                    <div class="empty-state" data-height="400">
                                        <div class="empty-state-icon">
                                            <i class="fas fa-clipboard"></i>
                                        </div>
                                        <h2>No Permission Records</h2>
                                        <p class="lead">
                                            {{ $user->name }} has no permission requests for {{ $currentMonth->format('F Y') }}.
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

    <!-- Image Modal -->
    <div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageModalLabel">Permission Attachment</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="image-container text-center mb-3" style="min-height: 300px; display: flex; align-items: center; justify-content: center;">
                        <img id="modalImage" src="" alt="Permission Attachment" class="img-fluid" 
                             style="max-height: 500px; max-width: 100%; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); display: none;"
                             onload="this.style.display='block'" 
                             onerror="this.style.display='none'">
                    </div>
                    <div id="imageDescription" class="mt-3 text-center"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <a href="#" id="downloadImageLink" class="btn btn-primary" download>
                        <i class="fas fa-download"></i> Download
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
        function showImage(imageUrl, description) {
            // Update modal content
            const modalImage = document.getElementById('modalImage');
            const imageDescription = document.getElementById('imageDescription');
            const downloadLink = document.getElementById('downloadImageLink');
            
            // Reset image and show loading state
            modalImage.src = '';
            modalImage.style.display = 'none';
            imageDescription.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin fa-2x text-primary"></i><br><small class="text-muted mt-2">Loading image...</small></div>';
            
            // Create new image to test loading
            console.log('Attempting to load image:', imageUrl);
            const testImage = new Image();
            testImage.onload = function() {
                // Image loaded successfully
                console.log('Image loaded successfully:', imageUrl);
                modalImage.src = imageUrl;
                modalImage.style.display = 'block';
                imageDescription.innerHTML = `
                    <strong>Reason:</strong><br>
                    <p class="text-muted">${description}</p>
                `;
                downloadLink.href = imageUrl;
            };
            testImage.onerror = function() {
                // Image failed to load
                console.error('Failed to load image:', imageUrl);
                modalImage.src = '';
                modalImage.style.display = 'none';
                imageDescription.innerHTML = `
                    <div class="text-center">
                        <div class="alert alert-danger d-inline-block">
                            <i class="fas fa-exclamation-triangle fa-2x mb-2"></i><br>
                            <strong>Error:</strong> Unable to load image<br>
                            <small class="text-muted">The file may be missing or corrupted</small><br>
                            <small class="text-muted">Path: ${imageUrl}</small>
                        </div>
                        <div class="mt-3">
                            <strong>Reason:</strong><br>
                            <p class="text-muted">${description}</p>
                        </div>
                    </div>
                `;
                downloadLink.href = imageUrl;
            };
            testImage.src = imageUrl;
            
            // Show modal
            $('#imageModal').modal('show');
        }
    </script>
@endpush
