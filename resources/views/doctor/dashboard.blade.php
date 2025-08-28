{{-- resources/views/doctor/dashboard.blade.php --}}
@extends('layouts.doctor')

@section('content')
<div class="container-fluid">
    {{-- Header --}}
  <div class="mb-4">
    <h4 class="fw-bold">🏥 Order Entry</h4>
    <p class="text-muted">Create Prescriptions and Order services for patients.</p>
  </div>

    {{-- Metrics / Quick Stats --}}
    <div class="row g-3 mb-4">
        <div class="col-md-6 col-sm-12">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-muted mb-1">Total Patients</h6>
                        <h4 class="fw-bold mb-0">{{ $patients->total() }}</h4>
                    </div>
                    <div class="ms-3 text-primary">
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-sm-12">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-muted mb-1">Recent Admissions</h6>
                        <h4 class="fw-bold mb-0">{{ $recentAdmissions->count() }}</h4>
                    </div>
                    <div class="ms-3 text-success">
                        <i class="fas fa-hospital-user fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        {{-- Add more metrics if needed, e.g., male/female split, occupied rooms --}}
    </div>

    {{-- Recent Admissions --}}
    @if($recentAdmissions->count())
    <div class="card mb-4 shadow-sm border-0 rounded-3">
        <div class="card-header bg-primary text-white fw-semibold">
            Recently Admitted — {{ now()->format('M d, Y') }}
        </div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Room</th>
                        <th class="text-end pe-4">Time</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentAdmissions as $admit)
                        <tr>
                            <td>
                                <i class="fas fa-user text-secondary me-1"></i>
                                {{ $admit->patient->patient_first_name }}
                                {{ $admit->patient->patient_last_name }}
                            </td>
                            <td>
                                <span class="badge bg-light text-dark">
                                    {{ $admit->room?->room_number ?? '—' }}
                                </span>
                            </td>
                            <td class="text-end pe-4 text-muted">
                                {{ $admit->admission_date->format('h:i A') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Search --}}
    <div class="card mb-4 shadow-sm border-0 rounded-3">
        <div class="card-body">
            <form method="GET" class="row g-2 align-items-center">
                <div class="col">
                    <input 
                        type="text" 
                        name="q" 
                        class="form-control" 
                        placeholder="🔍 Search by Name or MRN"
                        value="{{ $q }}"
                    >
                </div>
                <div class="col-auto">
                    <button class="btn btn-primary">
                        <i class="fas fa-search me-1"></i> Search
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Patients List --}}
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Sex</th>
                        <th>Room</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($patients as $patient)
                        <tr>
                            <td>
                                <i class="fas fa-user-circle text-secondary me-1"></i>
                                {{ $patient->patient_first_name }}
                                {{ $patient->patient_last_name }}
                            </td>
                            <td>{{ $patient->sex ?? '—' }}</td>
                            <td>
                                <span class="badge bg-light text-dark">
                                    {{ $patient->admissionDetail?->room?->room_number ?? '—' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <a 
                                  href="{{ route('doctor.order', $patient) }}" 
                                  class="btn btn-outline-primary btn-sm"
                                >
                                    <i class="fas fa-file-alt me-1"></i> Details
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">
                                <i class="fas fa-info-circle me-1"></i> No patients found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    <div class="d-flex justify-content-end mt-3">
        {{ $patients->withQueryString()->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
