@extends('layouts.admin')

@section('content')
    {{-- Header --}}
    <div class="mb-4">
        <h4 class="fw-bold">🏥 Users and Resources Management</h4>
        <p class="text-muted">Welcome to Admin! Manage Users and Resources for Hospital Operations.</p>
    </div>
    
    {{-- Stats / Metrics --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-4 col-md-6 col-sm-12">
            <div class="card border-100 h-100">
                <div class="card-body d-flex align-items-center">
                    <i class="fa-solid fa-users fa-2x text-primary me-4"></i>
                    <div>
                        <div class="text-muted small">Total Active Users</div>
                        <h4 class="fw-semibold mb-0">{{ $totalActiveUsers }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 col-sm-12">
            <div class="card border h-100">
                <div class="card-body d-flex align-items-center">
                    <i class="fa-solid fa-door-open fa-2x text-success me-4"></i>
                    <div>
                        <div class="text-muted small">Total Created Rooms</div>
                        <h4 class="fw-semibold mb-0">{{ $totalCreatedRooms }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 col-sm-12">
            <div class="card border h-100">
                <div class="card-body d-flex align-items-center">
                    <i class="fa-solid fa-bed fa-2x text-warning me-4"></i>
                    <div>
                        <div class="text-muted small">Total Created Beds</div>
                        <h4 class="fw-semibold mb-0">{{  $totalCreatedBeds }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Recently Created Users --}}
    <div class="card border-0 mb-4">
        <div class="card-header bg-white d-flex align-items-center">
            <i class="fa-solid fa-clock-rotate-left me-2 text-secondary"></i>
            <h6 class="mb-0">Recently Created Users</h6>
        </div>
        <div class="table-responsive p-3">
            <table class="table align-middle table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentUsers as $i => $user)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td><strong>{{ $user->username }}</strong></td>
                            <td>{{ $user->email }}</td>
                            <td>
                                {{ $user->role}}
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-3">
                                <i class="fa-solid fa-circle-info me-1"></i> No users found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
