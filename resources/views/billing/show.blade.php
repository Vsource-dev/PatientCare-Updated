@extends('layouts.billing')

@section('content')
<main class="p-4" style="margin-left: 240px;">
    <div class="container-fluid min-vh-100 p-4 d-flex flex-column">

        <div class="card border mb-3">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fa-solid fa-bed me-2"></i>Patient Information</h5>
            </div>
            <div class="card-body d-flex align-items-center gap-4">
                <div class="flex-shrink-0">
                    <img src="{{ asset($patient->profile_picture ?? 'https://via.placeholder.com/64') }}" alt="Patient Avatar" class="avatar rounded-circle bg-light border" style="width: 64px; height: 64px; object-fit: cover;">
                </div>
                <div>
                    {{-- Note: Add a getFullNameAttribute accessor to your Patient model for cleaner code --}}
                    <strong class="fs-5">{{ $patient->patient_first_name }} {{ $patient->patient_last_name }}</strong>
                    <div class="text-muted small">Patient ID: {{ $patient->patient_id }}</div>
                    <div class="text-muted small">Room: {{ optional($patient->admissionDetail)->room_number ?? 'N/A' }}</div>
                    <div class="text-muted small">
                        Admitted: {{ optional(optional($patient->admissionDetail)->admission_date)->format('F d, Y') ?? '—' }}
                    </div>
                </div>
            </div>
        </div>
        <div class="card border-danger mb-4">
            <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fa-solid fa-flag me-2"></i>Review Dispute Request</h5>
                <span class="badge bg-white text-danger text-capitalize">{{ $dispute->status }}</span>
            </div>
            <div class="card-body">
                <blockquote class="blockquote">
                    <p class="mb-2">"{{ $dispute->reason }}"</p>
                    <footer class="blockquote-footer">Filed by {{ $patient->patient_first_name }} on <cite title="Source Title">{{ $dispute->datetime->format('F d, Y h:i A') }}</cite></footer>
                </blockquote>
                <hr>
                <p><strong>Disputed Item:</strong> {{ $disputed_charge->service->service_name ?? 'N/A' }} (Amount: ₱{{ number_format($disputed_charge->amount ?? $disputed_charge->total ?? 0, 2) }})</p>
            </div>
            <div class="card-footer bg-white">
                <form action="{{ route('billing.disputes.update', $dispute) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="mb-3">
                        <label for="notes" class="form-label fw-bold">Billing Notes (Optional)</label>
                        <textarea name="notes" id="notes" rows="2" class="form-control" placeholder="Add internal notes about the resolution..."></textarea>
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <button type="submit" name="action" value="reject" class="btn btn-danger">
                            <i class="fa-solid fa-times me-1"></i> Reject Dispute
                        </button>
                        <button type="submit" name="action" value="approve" class="btn btn-success">
                            <i class="fa-solid fa-check me-1"></i> Approve & Waive Charge
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <div class="card border mb-3">
            <div class="card-header d-flex justify-content-between align-items-center bg-white">
                <h5 class="mb-0 fw-semibold"><i class="fa-solid fa-receipt me-2 text-primary"></i>Transaction History</h5>
                <a href="{{ url()->current() }}" class="btn btn-primary"><i class="fa-solid fa-arrows-rotate me-2"></i>Refresh</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive rounded shadow-sm" style="height: 450px; overflow-y: auto;">
                    <table class="table align-middle table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Description</th>
                                <th>Source/Origin</th>
                                <th>Qty</th>
                                <th>Unit Price</th>
                                <th class="text-end">Total</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Loop through all charges for the patient --}}
                            @forelse($all_charges as $charge)
                                {{-- Highlight the charge that is being disputed --}}
                                <tr class="{{ $charge->is($disputed_charge) ? 'table-warning' : '' }}">
                                    <td>{{ $charge->billing_date->format('M d, Y') }}</td>
                                    <td>
                                        {{ $charge->service->service_name ?? '—' }}
                                        @if($charge->is($disputed_charge))
                                            <span class="badge bg-danger ms-2">Disputed</span>
                                        @endif
                                    </td>
                                    <td>{{ $charge->service->department->department_name ?? 'Manual' }}</td>
                                    <td>{{ $charge->quantity ?? 1 }}</td>
                                    <td>₱{{ number_format($charge->unit_price ?? $charge->amount, 2) }}</td>
                                    <td class="text-end">₱{{ number_format($charge->amount - ($charge->discount_amount ?? 0), 2) }}</td>
                                    <td class="text-center">
                                        {{-- Actions only apply to BillItem models --}}
                                        @if($charge instanceof \App\Models\BillItem)
                                            <a href="{{ route('billing.charges.edit', $charge) }}" class="btn btn-sm btn-outline-secondary" title="Edit"><i class="fa-solid fa-pen"></i></a>
                                            <form method="POST" action="{{ route('billing.charges.destroy', $charge) }}" class="d-inline" onsubmit="return confirm('Really delete this charge?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete"><i class="fa-solid fa-trash"></i></button>
                                            </form>
                                            <a href="{{ route('billing.charges.audit', $charge) }}" class="btn btn-sm btn-outline-info" title="Audit Trail"><i class="fa-solid fa-clock-rotate-left"></i></a>
                                        @else
                                            <span class="text-muted small">N/A</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted p-5">No transactions found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="card p-3 border shadow-sm">
            <div class="d-flex justify-content-between">
                <strong>Total Charges:</strong>
                <span class="fw-bold text-danger">₱{{ number_format($totalCharges, 2) }}</span>
            </div>
            <div class="d-flex justify-content-between">
                <strong>Total Deposits:</strong>
                <span class="fw-bold text-success">₱{{ number_format($totalDeposits, 2) }}</span>
            </div>
            <div class="d-flex justify-content-between fs-5">
                <strong>Balance:</strong>
                <span class="fw-bold {{ $balance <= 0 ? 'text-success' : 'text-danger' }}">
                    ₱{{ number_format($balance, 2) }}
                </span>
            </div>
        </div>

    </div>
</main>

<div class="position-sticky bottom-0 start-0 end-0 bg-white border-top p-3 z-3" style="margin-left: 240px;">
    {{-- This section remains mostly the same, as it's already dynamic --}}
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <a href="{{ route('billing.dispute.queue') }}" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left me-2"></i>Back to Dispute Queue
            </a>
        </div>
        <div class="d-flex flex-wrap gap-2 justify-content-end">
            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#manualChargeModal">
                <i class="fa-solid fa-plus me-1"></i> Manual Charge
            </button>
            <button type="button" class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#depositModal">
                <i class="fa-solid fa-money-bill-wave me-1"></i> Post Deposit
            </button>
            <a href="{{ route('billing.print', $patient) }}" class="btn btn-outline-secondary">
                <i class="fa-solid fa-print me-1"></i> Print SOA
            </a>
            @if ($patient->billing_closed_at)
                {{-- Show UNLOCK button if bill is already locked --}}
                <form method="POST" action="{{ route('billing.patients.toggleLock', $patient) }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-success">
                        <i class="fa-solid fa-lock-open me-1"></i> Unlock Bill
                    </button>
                </form>
            @else
                {{-- Show LOCK button if bill is open --}}
                <form method="POST" action="{{ route('billing.patients.toggleLock', $patient) }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to lock this bill? No new charges can be added.');">
                        <i class="fa-solid fa-lock me-1"></i> Lock Bill
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>

{{-- MODALS remain the same, they use the $patient and $services variables --}}
{{-- MODAL: Manual Charge --}}
<form id="manualChargeModal" class="modal fade" tabindex="-1" method="POST" action="{{ route('billing.charges.store') }}">
    @csrf
    <input type="hidden" name="patient_id" value="{{ $patient->patient_id }}">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Post Manual Charges</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table align-middle mb-0" id="mcTable">
                        <thead class="table-light">
                            <tr>
                                <th>Item</th>
                                <th style="width:120px" class="text-end">Unit ₱</th>
                                <th style="width:100px">Qty</th>
                                <th style="width:140px" class="text-end">Subtotal ₱</th>
                                <th style="width:60px"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $byType = $services->groupBy('service_type'); @endphp
                            <tr class="mc-row">
                                <td>
                                    <select name="charges[0][service_id]" class="form-select serviceSel" required>
                                        <option value="">— select item —</option>
                                        @foreach($byType as $type=>$grp)
                                            <optgroup label="{{ ucfirst($type) }}">
                                                @foreach($grp as $s)
                                                    <option value="{{ $s->service_id }}" data-price="{{ $s->price }}">
                                                        {{ $s->service_name }} — ₱{{ number_format($s->price,2) }}
                                                    </option>
                                                @endforeach
                                            </optgroup>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="text-end">
                                    <input class="form-control-plaintext unitP" readonly value="₱0.00">
                                </td>
                                <td>
                                    <input type="number" min="1" value="1" name="charges[0][quantity]" class="form-control qty" required>
                                </td>
                                <td class="text-end">
                                    <input class="form-control-plaintext lineTot" readonly value="₱0.00">
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-outline-danger delRow"><i class="fa-solid fa-trash"></i></button>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="3" class="text-end">Grand Total</th>
                                <th class="text-end" id="mcGrand">₱0.00</th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <button type="button" class="btn btn-sm btn-outline-primary mt-3" id="mcAddRow"><i class="fa-solid fa-plus me-1"></i> Add Row</button>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-primary"><i class="fa-solid fa-paper-plane me-1"></i> Submit</button>
            </div>
        </div>
    </div>
</form>

{{-- MODAL: Post Deposit --}}
<form id="depositModal" class="modal fade" tabindex="-1" method="POST" action="{{ route('billing.deposits.store') }}">
    @csrf
    <input type="hidden" name="patient_id" value="{{ $patient->patient_id }}">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Record Deposit</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Amount (₱)</label>
                    <input type="number" name="amount" min="0" step="0.01" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Deposit Date</label>
                    <input type="date" name="deposited_at" class="form-control" value="{{ now()->toDateString() }}" required>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-primary"><i class="fa-solid fa-save me-1"></i> Save</button>
            </div>
        </div>
    </div>
</form>

@endsection

@push('scripts')
{{-- The existing JavaScript for modals will work correctly with this dynamic data --}}
<script>
(() => {
    const modal = document.getElementById('manualChargeModal');
    if (!modal) return;
    const table   = modal.querySelector('#mcTable tbody');
    const totalEl = modal.querySelector('#mcGrand');
    let idx = 1;

    modal.addEventListener('shown.bs.modal', recalc);
    modal.querySelector('#mcAddRow').addEventListener('click', () => {
        const row = table.querySelector('.mc-row').cloneNode(true);
        row.querySelector('.serviceSel').name = `charges[${idx}][service_id]`;
        row.querySelector('.qty').name       = `charges[${idx}][quantity]`;
        row.querySelector('.serviceSel').value = '';
        row.querySelector('.qty').value = 1;
        row.querySelector('.unitP').value    = '₱0.00';
        row.querySelector('.lineTot').value  = '₱0.00';
        table.appendChild(row);
        idx++;
    });

    table.addEventListener('input', e => {
        if (e.target.matches('.serviceSel, .qty')) updateRow(e.target.closest('tr'));
    });

    table.addEventListener('click', e => {
        if (e.target.closest('.delRow') && table.rows.length > 1) {
            e.target.closest('tr').remove();
            recalc();
        }
    });

    function updateRow(tr) {
        const price = parseFloat(tr.querySelector('.serviceSel').selectedOptions[0]?.dataset.price || 0);
        const qty   = parseInt(tr.querySelector('.qty').value) || 0;
        tr.querySelector('.unitP').value   = `₱${price.toFixed(2)}`;
        tr.querySelector('.lineTot').value = `₱${(price * qty).toFixed(2)}`;
        recalc();
    }

    function recalc() {
        let sum = 0;
        table.querySelectorAll('.lineTot').forEach(i => {
            sum += parseFloat(i.value.replace(/[₱,]/g, '')) || 0;
        });
        totalEl.textContent = `₱${sum.toFixed(2)}`;
    }
})();
</script>
@endpush