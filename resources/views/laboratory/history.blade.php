{{-- filepath: resources/views/laboratory/history.blade.php --}}
@extends('layouts.laboratory')

@section('content')

<div class="container-fluid">

    {{-- Header --}}
    <div class="mb-4">
        <h3 class="fw-bold hdng mb-1">🧪 Laboratory History</h3>
        <p class="text-muted">History of completed and pending laboratory orders</p>
    </div>

    {{-- Metrics --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-muted mb-1">Total Completed Services</h6>
                        <h4 class="fw-bold mb-0">{{ $completedOrders->count() }}</h4>
                    </div>
                    <div class="ms-3 text-success">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>

    {{-- Unified Table --}}
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Order No</th>
                        <th>Patient</th>
                        <th>Physician</th>
                        <th>Completion Date</th>
                        <th>Tests</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                {{-- Completed --}}
                @forelse($completedOrders as $order)
                    <tr>
                        <td>
                            <span class="badge bg-light text-dark">
                                {{ str_pad($order->assignment_id, 6, '0', STR_PAD_LEFT) }}
                            </span>
                        </td>
                        <td>
                            <i class="fas fa-user text-primary me-1"></i>
                            {{ $order->patient->full_name 
                                ?? $order->patient->patient_first_name . ' ' . $order->patient->patient_last_name }}
                        </td>
                        <td>
                            <i class="fas fa-user-md text-secondary me-1"></i>
                            {{ $order->doctor->doctor_name ?? '—' }}
                        </td>
                        <td class="text-muted">
                            <span class="completed-at" style="cursor: pointer"
                                data-date="{{ $order->updated_at->format('M d, Y h:i A') }}">
                                {{ $order->updated_at->diffForHumans() }}
                            </span>
                        </td>
                        <td>
                            <span class="badge rounded-pill bg-light text-dark border me-1 mb-1">
                                {{ $order->service->service_name ?? 'N/A' }}
                            </span>
                        </td>
                        <td><span class="badge bg-success">Completed</span></td>
                        <td>
                            <a href="{{ route('laboratory.history.show', $order->assignment_id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fa fa-eye"></i> View
                            </a>
                        </td>
                    </tr>
                @empty
                @endforelse

                {{-- If none --}}
                @if($completedOrders->isEmpty() && $pendingOrders->isEmpty())
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            <i class="fas fa-info-circle me-1"></i> No laboratory orders yet.
                        </td>
                    </tr>
                @endif
                </tbody>
            </table>
        </div>
    </div>

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.completed-at').forEach(function(span) {
        span.addEventListener('click', function() {
            if (span.dataset.toggled === "1") {
                span.textContent = span.dataset.relative;
                span.dataset.toggled = "0";
            } else {
                // Save current (relative) text for toggling back
                span.dataset.relative = span.textContent;
                span.textContent = span.dataset.date;
                span.dataset.toggled = "1";
            }
        });
    });
});
</script>
@endpush

@endsection
