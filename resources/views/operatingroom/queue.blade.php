{{-- resources/views/operatingroom/queue.blade.php --}}
@php use Carbon\Carbon; @endphp

@extends('layouts.operatingroom')

@section('content')
<div class="container-fluid h-100 border p-4 d-flex flex-column" style="background-color: #fafafa;">

    {{-- Header --}}
    <div>
        <h5 class="hdng">Operating Room Services Management</h5>
        <p class="lead">Manage OR Charges and Service Completion</p>
    </div>

    {{-- Filters --}}
    <div class="card mb-3">
        <div class="card-body py-4">
            <div class="row">
                <div class="col-md-3 col-sm-6">
                    <select id="status-filter" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="pending">Pending</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>
                <div class="col-md-3 col-sm-6">
                    <select id="or-filter" class="form-select">
                        <option value="">All OR Nos.</option>
                        @foreach($procedures->pluck('room')->unique()->filter()->sort() as $orNo)
                            <option value="{{ $orNo }}">OR {{ $orNo }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 col-sm-12">
                    <div class="input-group w-100">
                        <input type="text" id="search-input" class="form-control" placeholder="Search by Name or MRN">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="card" style="height: 70vh;">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0 fw-semibold">
                    <i class="fa-solid fa-procedures me-2 text-secondary"></i>OR Queue
                </h5>
                <a href="{{ route('operating.queue') }}" class="btn btn-primary">
                    <i class="fa-solid fa-clock-rotate-left me-2"></i>Refresh
                </a>
            </div>

            <div class="table-responsive rounded shadow-sm p-1">
                <table class="table align-middle table-hover mb-0" id="or-queue-table">
                    <thead class="table-light">
                        <tr>
                            <th>Date Assigned</th>
                            <th>Patient</th>
                            <th>Procedure</th>
                            <th>OR No.</th>
                            <th>Assigned By</th>
                            <th>Status</th>
                            <th class="text-end">Amount</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($procedures as $procedure)
                        <tr data-status="{{ $procedure->service_status }}" data-or="{{ $procedure->room }}" data-name="{{ strtolower($procedure->patient->patient_first_name.' '.$procedure->patient->patient_last_name) }}" data-mrn="{{ $procedure->patient->patient_id }}">
                        <td>
  {{ $procedure->datetime
      ? Carbon::parse($procedure->datetime)->format('M j, Y g:i A')
      : '–' }}
</td>

                            <td>
                                <div class="fw-semibold">
                                    {{ $procedure->patient->patient_first_name }}
                                    {{ $procedure->patient->patient_last_name }}
                                </div>
                                <small class="text-muted">P-{{ $procedure->patient->patient_id }}</small>
                            </td>
                            <td>{{ $procedure->service->description ?? $procedure->service->service_name }}</td>
                            <td>{{ $procedure->room ?? '–' }}</td>
                            <td>{{ $procedure->doctor->doctor_name ?? 'N/A' }}</td>
                            <td>
                                <span class="badge {{ $procedure->service_status === 'pending'
                                           ? 'bg-warning text-dark'
                                           : 'bg-success text-white' }}">
                                    {{ ucfirst($procedure->service_status) }}
                                </span>
                            </td>
                            <td class="text-end">₱{{ number_format($procedure->amount, 2) }}</td>
                            <td class="text-center">
                                <a href="#" class="btn btn-sm btn-success add-charge-btn"
   data-bs-toggle="modal"
   data-bs-target="#addChargeModal"
   data-patient-id="{{ $procedure->patient->patient_id }}"
   data-patient-name="{{ $procedure->patient->patient_first_name }} {{ $procedure->patient->patient_last_name }}">
   <i class="fa-solid fa-plus me-1"></i>Add Charge
</a>

                                @if($procedure->service_status === 'pending')
                                    <a href="{{ route('operating.details', $procedure) }}"
                                       class="btn btn-sm btn-outline-secondary confirm-btn">
                                        <i class="fa-solid fa-file-circle-question me-1"></i>
                                        Details
                                    </a>
                                @else
                                    <span class="text-muted">Completed</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-3">
                                <i class="fa-solid fa-puzzle-piece me-2"></i>No Data Available
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3 d-flex justify-content-center">
  {{ $procedures->links() }}
</div>

        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Confirmation for marking completed
    document.querySelectorAll('.confirm-btn').forEach(btn => {
        btn.addEventListener('click', e => {
            e.preventDefault();
            const row = btn.closest('tr');
            Swal.fire({
                title: "Mark this procedure complete?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#00529A",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, complete it"
            }).then(result => {
                if (result.isConfirmed) {
                    // Update badge
                    const badge = row.querySelector('.badge');
                    badge.classList.remove('bg-warning','text-dark');
                    badge.classList.add('bg-success','text-white');
                    badge.textContent = 'Completed';
                    // Remove button
                    btn.remove();
                    Swal.fire("Completed!", "Procedure has been marked completed.", "success");
                    // TODO: AJAX call to update DB
                }
            });
        });
    });

    // Client-side filtering
    const statusFilter = document.getElementById('status-filter');
    const orFilter     = document.getElementById('or-filter');
    const searchInput  = document.getElementById('search-input');
    const tableRows    = document.querySelectorAll('#or-queue-table tbody tr');

    function applyFilters() {
        const statusVal = statusFilter.value;
        const orVal     = orFilter.value.toLowerCase();
        const searchVal = searchInput.value.toLowerCase();

        tableRows.forEach(row => {
            const matchesStatus = !statusVal || row.dataset.status === statusVal;
            const matchesOr     = !orVal     || row.dataset.or === orVal.replace('or ', '');
            const matchesSearch = !searchVal || row.dataset.name.includes(searchVal) || row.dataset.mrn.includes(searchVal);
            row.style.display = (matchesStatus && matchesOr && matchesSearch) ? '' : 'none';
        });
    }

    statusFilter.addEventListener('change', applyFilters);
    orFilter.addEventListener('change', applyFilters);
    searchInput.addEventListener('input', applyFilters);
});
</script>
@endpush
