{{-- filepath: resources/views/pharmacy/queue.blade.php --}}
@extends('layouts.pharmacy')

@section('content')

<div class="container-fluid">

    {{-- Header --}}
    <div class="mb-4">
        <h3 class="fw-bold hdng mb-1">💊 Pending Approvals Queue</h3>
        <p class="text-muted">Review and dispense prescriptions awaiting approval</p>
    </div>

    {{-- Metrics --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-muted mb-1">Total Pending</h6>
                        <h4 class="fw-bold mb-0">{{ $pendingCharges->count() }}</h4>
                    </div>
                    <div class="ms-3 text-warning">
                        <i class="fas fa-clock fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        {{-- Add more if needed: today’s approvals, dispensed count, etc. --}}
    </div>

    {{-- Queue Table --}}
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>RX Number</th>
                        <th>Patient</th>
                        <th>Physician</th>
                        <th>Date</th>
                        <th>Medications</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($pendingCharges as $charge)
                    @if($charge->items->where('status', 'pending')->count())
                    @php
                        $pendingItems = $charge->items->where('status', 'pending')->values()->map(function($i){
                            return [
                                'id' => $i->id,
                                'name' => $i->service->service_name,
                                'qty' => $i->quantity
                            ];
                        });
                    @endphp
                    <tr>
                        <td>
                            <span class="badge bg-light text-dark">
                                {{ $charge->rx_number }}
                            </span>
                        </td>
                        <td>
                            <i class="fas fa-user text-primary me-1"></i>
                            {{ $charge->patient->full_name 
                                ?? $charge->patient->patient_first_name . ' ' . $charge->patient->patient_last_name }}
                        </td>
                        <td>
                            <i class="fas fa-user text-secondary me-1"></i>
                            {{ $charge->prescribing_doctor }}
                        </td>
                        <td class="text-muted">
                            <span class="created-at" 
                                style="cursor:pointer"
                                data-date="{{ $charge->created_at->format('M d, Y h:i A') }}">
                                {{ $charge->created_at->diffForHumans() }}
                            </span>
                        </td>
                        <td>
                            <ul class="mb-0">
                            @foreach($charge->items->where('status', 'pending') as $item)
                                <li>
                                    {{ $item->service->service_name ?? 'N/A' }} 
                                    <span class="badge bg-secondary">{{ $item->quantity }}</span>
                                </li>
                            @endforeach
                            </ul>
                        </td>
                        <td class="text-center">
                              <button 
                                    type="button"
                                    class="btn btn-success btn-sm open-dispense-modal"
                                    data-items='@json($pendingItems)'
                                    data-action="{{ route('pharmacy.charges.partial-dispense', $charge) }}"
                                    data-rx="{{ $charge->rx_number }}"
                                >
                                    <i class="fas fa-check me-1"></i> Dispense
                                </button>
                        </td>
                    </tr>
                    @endif
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            <i class="fas fa-info-circle me-1"></i> No pending approvals.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal --}}
    {{-- Modal for selecting items to dispense --}}
    <div class="modal fade" id="dispenseModal" tabindex="-1" aria-labelledby="dispenseModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" id="dispenseForm">
        @csrf
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="dispenseModalLabel">Dispense Prescription</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="dispenseModalBody">
            {{-- Items will be loaded here by JS --}}
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-success">Dispense Selected</button>
            </div>
        </div>
        </form>
    </div>
    </div>

</div>

{{-- SweetAlert Confirmation --}}
@push('scripts')

<script>
document.addEventListener('DOMContentLoaded', function() {
    // When a dispense button is clicked
    document.querySelectorAll('.open-dispense-modal').forEach(btn => {
        btn.addEventListener('click', function() {
            const items = JSON.parse(this.dataset.items);
            const action = this.dataset.action;
            const rx = this.dataset.rx;

            // Set form action
            document.getElementById('dispenseForm').action = action;
            // Set modal title
            document.getElementById('dispenseModalLabel').textContent = 'Dispense Prescription: ' + rx;

            // Build the checkboxes
            let html = '<ul>';
            items.forEach(item => {
                html += `<li>
                    <label>
                        <input type="checkbox" name="items[]" value="${item.id}" checked>
                        ${item.name ?? 'N/A'} (${item.qty})
                    </label>
                </li>`;
            });
            html += '</ul>';
            document.getElementById('dispenseModalBody').innerHTML = html;

            // Show the modal
            let modal = new bootstrap.Modal(document.getElementById('dispenseModal'));
            modal.show();
        });
    });


    // Toggle between relative and absolute date on click
    document.querySelectorAll('.created-at').forEach(function(span) {
        span.addEventListener('click', function() {
            // If already showing absolute, revert to relative
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
