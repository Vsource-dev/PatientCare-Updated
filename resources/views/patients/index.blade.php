{{-- resources/views/patients/index.blade.php --}}
@extends('layouts.admission')

@section('content')
    {{-- Header & action button --}}
    <div class="row align-items-center justify-content-between mb-3">
        <div class="col">
            <h4 class="fw-bold">🏥 Patient Admission Management</h4>
            <p class="text-muted">Welcome to Admission! Manage admissions, beds, and doctor assignments.</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('admission.patients.create') }}" class="btn btn-outline-primary">
                <i class="fa-solid fa-user-plus me-2"></i>Admit new Patient
            </a>
        </div>
    </div>

    {{-- Search form --}}
    <form method="GET" action="{{ route('admission.patients.index') }}">
        @csrf
        <div class="row mb-3 g-2">
            <div class="col-md-10 col-sm-12">
                <input
                    type="text"
                    name="q"
                    class="form-control"
                    placeholder="MRN or Patient Name"
                    value="{{ request('q') }}"
                >
            </div>
            <div class="col-md-2 col-sm-12">
                <button type="submit" class="btn btn-outline-primary w-100">
                    <i class="fa-solid fa-magnifying-glass me-2"></i>Search
                </button>
            </div>
        </div>
    </form>

    {{-- Success message --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Patients table --}}
    <div class="table-responsive" style="max-height: 60vh; overflow-y: scroll;">
        <table class="table align-middle table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>MRN</th>
                    <th>Name</th>
                    <th>Room</th>
                    <th>Admission Date</th>
                    <th>Assigned Doctor</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($patients as $patient)
                    <tr>
                        <td>{{ $patient->patient_id }}</td>
                        <td>{{ $patient->patient_first_name }} {{ $patient->patient_last_name }}</td>
                        <td>{{ $patient->admissionDetail?->room_number ?? '—' }}</td>
                        <td>{{ $patient->admissionDetail?->admission_date?->format('Y-m-d') ?? '—' }}</td>
                        <td>{{ $patient->admissionDetail?->doctor?->doctor_name ?? '—' }}</td>
                        <td>
                            @php
                                $badge = match(strtolower($patient->status)) {
                                    'active'    => 'bg-success',
                                    'completed' => 'bg-primary',
                                    'pending'   => 'bg-warning',
                                    default     => 'bg-secondary',
                                };
                            @endphp
                            <span class="badge text-white {{ $badge }}">
                                {{ ucfirst($patient->status) }}
                            </span>
                        </td>
                        <td class="text-end">
                            {{-- FIXED: use $patient, not $adm --}}
                            <a href="{{ route('admission.patients.show', $patient->patient_id) }}" class="btn btn-sm btn-primary">
                                <i class="fa-solid fa-eye me-2"></i>View
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted">No patients found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{-- Pagination --}}
    <div class="d-flex justify-content-end">
        {{ $patients->withQueryString()->links('pagination::bootstrap-5') }}
    </div>
@endsection
