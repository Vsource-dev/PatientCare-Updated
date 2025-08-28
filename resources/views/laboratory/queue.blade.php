{{-- resources/views/laboratory/queue.blade.php --}}

@extends('layouts.laboratory')

@section('content')
    {{-- Header --}}
    <div>
        <h5 class="hdng">Laboratory Services Management</h5>
        <p class="lead">Welcome to Laboratory! Manage Lab Charges and Service Completion</p>
    </div>

    {{-- Filters --}}
    <div class="card mb-3">
        <div class="card-body py-4">
            <div class="row">
                <div class="col col-md-3 col-sm-6">
                    <select class="form-select">
                        <option value="">All</option>
                        <option value="pending">Pending</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>
                <div class="col col-md-3 col-sm-6">
                    <select class="form-select">
                        <option value="">All</option>
                        <option value="pending">Pending</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>
                <div class="col col-md-6 col-sm-12">
                    <div class="input-group w-100">
                        <input type="text" class="form-control" placeholder="Search by Name or MRN">
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
                    <i class="fa-solid fa-users-line me-2 text-secondary"></i>Patient Queue
                </h5>
                <a href="{{ route('laboratory.queue') }}" class="btn btn-primary">
                    <i class="fa-solid fa-clock-rotate-left me-2"></i>Refresh
                </a>
            </div>

            <div class="table-responsive rounded shadow-sm p-1">
                <table class="table align-middle table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Date Assigned</th>
                            <th>Patient</th>
                            <th>Description</th>
                            <th>Assigned By</th>
                            <th>Status</th>
                            <th class="text-end">Amount</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                  <tbody>
  @forelse($labRequests as $request)
    <tr>
    <td>
  {{ $request->created_at->format('M j, Y g:i A') }}
</td>


      {{-- Patient --}}
      <td>
        <div class="fw-semibold">
          {{ $request->patient->patient_first_name }}
          {{ $request->patient->patient_last_name }}
        </div>
        <small class="text-muted">
          ID: {{ $request->patient->patient_id }}
        </small>
      </td>

      {{-- Description (from the service) --}}
      <td>
        {{ $request->service->description
           ?? $request->service->service_name }}
      </td>

      {{-- Assigned By (doctor_name) --}}
      <td>
        {{ $request->doctor->doctor_name ?? 'N/A' }}
      </td>

      {{-- Status --}}
      <td>
        <span
          class="badge {{ $request->service_status === 'pending'
                     ? 'bg-warning text-dark'
                     : 'bg-success text-white' }}">
          {{ ucfirst($request->service_status) }}
        </span>
      </td>

      {{-- Amount --}}
      <td class="text-end">
        ₱{{ number_format($request->amount, 2) }}
      </td>

      {{-- Actions --}}
   <td class="text-center">
  @if($request->service_status === 'pending')
    <a href="{{ route('laboratory.details', $request) }}"
       class="btn btn-sm btn-outline-secondary">
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
      <td colspan="7" class="text-center text-muted py-3">
        <i class="fa-solid fa-puzzle-piece me-2"></i>No Data Available
      </td>
    </tr>
  @endforelse
</tbody>

                </table>
            </div>

        </div>
    </div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const buttons = document.querySelectorAll('.confirm-btn');

    buttons.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();

            const row = btn.closest('tr');

            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#00529A",
                cancelButtonColor: "#d33",
                confirmButtonText: "Confirm"
            }).then((result) => {
                if (result.isConfirmed) {
                    // Update status badge
                    const badge = row.querySelector('.badge');
                    badge.classList.remove('bg-warning', 'text-dark');
                    badge.classList.add('bg-success', 'text-white');
                    badge.textContent = 'Completed';

                    // Remove button
                    btn.remove();

                    Swal.fire({
                        title: "Confirmed",
                        text: "Request marked as completed.",
                        icon: "success"
                    });

                    // Optionally: AJAX call to update status in DB here
                }
            });
        });
    });
});
</script>
@endpush
