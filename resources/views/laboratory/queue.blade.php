{{-- resources/views/laboratory/queue.blade.php --}}

@extends('layouts.laboratory')

@section('content')

<div class="container-fluid">

    {{-- Header --}}
    <div class="mb-4">
        <h3 class="fw-bold hdng mb-1">🧪 Laboratory Queue</h3>
        <p class="text-muted">Manage lab requests and mark services as completed</p>
    </div>

    {{-- Metrics --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-muted mb-1">Total Pending</h6>
                        <h4 class="fw-bold mb-0">
                            {{ $labRequests->where('service_status', 'pending')->count() }}
                        </h4>
                    </div>
                    <div class="ms-3 text-warning">
                        <i class="fas fa-clock fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        {{-- Add more metrics if needed --}}
    </div>

    {{-- Queue Table --}}
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Date Assigned</th>
                        <th>Patient</th>
                        <th>Description</th>
                        <th>Assigned By</th>
                        <th>Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($labRequests as $request)
                    <tr>
                        <td>
                            {{ $request->created_at ? $request->created_at->format('M j, Y g:i A') : '-' }}
                        </td>
                        <td>
                            <div class="fw-semibold">
                                {{ $request->patient->patient_first_name }} {{ $request->patient->patient_last_name }}
                            </div>
                            <small class="text-muted">
                                ID: {{ $request->patient->patient_id }}
                            </small>
                        </td>
                        <td>
                            <span class="badge rounded-pill bg-light text-dark border me-1 mb-1">
                                {{ $request->service->service_name ?? 'N/A' }}
                            </span>
                        </td>
                        <td>
                            {{ $request->doctor->doctor_name ?? 'N/A' }}
                        </td>
                        <td>
                            <span class="badge {{ $request->service_status === 'pending' ? 'bg-warning text-dark' : 'bg-success text-white' }}">
                                {{ ucfirst($request->service_status) }}
                            </span>
                          </td>
                        <td class="text-center">
                            @if($request->service_status === 'pending')
                                <form action="{{ route('laboratory.details.complete', $request) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success confirm-btn">
                                        <i class="fa fa-check me-1"></i> Mark as Completed
                                    </button>
                                </form>
                            @else
                                <span class="text-muted">Completed</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="fas fa-info-circle me-1"></i> No lab requests in queue.
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
<script>
document.addEventListener('DOMContentLoaded', () => {
    const buttons = document.querySelectorAll('.confirm-btn');

    buttons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const form = btn.closest('form');
            Swal.fire({
                title: "Are you sure?",
                text: "This will mark the lab request as completed.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#00529A",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, complete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
});
</script>
@endpush
