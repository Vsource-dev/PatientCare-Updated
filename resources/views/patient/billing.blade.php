{{-- resources/views/patient/billing.blade.php --}}
@extends('layouts.patients')

@section('content')
<div class="card shadow-sm">
  <div class="card-body p-4">

    {{-- Heading & Admission selector --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
      <div>
        <h4 class="fw-bold text-primary mb-1">Billing and Transactions</h4>
        <small class="text-muted">
          Welcome! Here you can monitor an itemized version of your bill.
        </small>
      </div>
    </div>

    {{-- Info banner --}}
    <div class="alert alert-warning d-flex align-items-center py-2 mb-4">
      <i class="fas fa-info-circle me-2"></i>
      <div>
        <strong>Important:</strong> Disputed items will appear with a red badge until resolved.
      </div>
    </div>

    {{-- KPI Tiles --}}
    <div class="row g-3 mb-4">
      @foreach([
        ['label'=>'Total Amount','value'=>$totals['total']],
        ['label'=>'Balance','value'=>$totals['balance']],
        ['label'=>'Discount Applied','value'=>$totals['discount']],
        ['label'=>'Bed/Room Rate','value'=>$bedRate],
        ['label'=>'Doctor Fee','value'=>$doctorFee],
        ['label'=>'Pharmacy Charges','value'=>$pharmacyTotal],
        ['label'=>'Deposits Paid','value'=>$paymentsMade],  {{-- NEW: show deposits --}}
      ] as $tile)
        <div class="col-md-4">
          <div class="bg-light rounded p-3 h-100">
            <div class="text-muted">{{ $tile['label'] }}</div>
            <div class="fs-5 fw-bold">₱{{ number_format($tile['value'],2) }}</div>
          </div>
        </div>
      @endforeach
    </div>

    {{-- Filters --}}
    <form method="GET" class="row g-2 mb-3">
      <input type="hidden" name="admission_id" value="{{ $admissionId }}">
      <div class="col-md-4">
        <input type="text" name="q" class="form-control form-control-sm"
               placeholder="Search Billing Items" value="{{ request('q') }}">
      </div>
      <div class="col-md-3">
        <select name="order" class="form-select form-select-sm">
          <option value="desc" {{ request('order','desc')==='desc'?'selected':'' }}>
            Date (Newest)
          </option>
          <option value="asc" {{ request('order')==='asc'?'selected':'' }}>
            Date (Oldest)
          </option>
        </select>
      </div>
      <div class="col-md-1 d-grid">
        <button class="btn btn-sm btn-outline-secondary">
          <i class="fas fa-filter"></i>
        </button>
      </div>
    </form>

    {{-- Table --}}
    <div class="table-responsive">
      <table class="table table-sm align-middle">
        <thead class="table-light">
          <tr>
            <th>Date</th><th>Ref no.</th><th>Description</th>
            <th>Provider</th><th class="text-end">Amount</th>
            <th>Status</th><th class="text-center">Actions</th>
          </tr>
        </thead>
        <tbody>
        @forelse($items as $row)
  @php
    $itemId = data_get($row, 'billing_item_id', optional($row->children->first())->billing_item_id);
    $badge  = [
      'complete'=>'success','completed'=>'success',
      'pending'=>'warning','disputed'=>'danger','mixed'=>'secondary',
    ][$row->status] ?? 'secondary';
  @endphp
          <tr>
            <td>{{ \Carbon\Carbon::parse($row->billing_date)->format('Y-m-d') }}</td>
            <td>{{ $row->ref_no }}</td>
            <td>{{ $row->description }}</td>
            <td>{{ $row->provider_label ?? $row->provider }}</td>
            <td class="text-end">₱{{ number_format($row->amount,2) }}</td>
            <td><span class="badge bg-{{ $badge }} text-capitalize">{{ $row->status }}</span></td>
            <td class="text-center">
              <button type="button"
        class="btn btn-outline-secondary btn-sm btn-details"
        data-bs-toggle="modal"
        data-bs-target="#detailsModal"
        data-items='@json($row->children)'
        data-provider="{{ $row->provider }}"
        data-timeline='@json($row->children->flatMap(fn($c)=>$c->timeline)->sortBy("stamp")->values())'>
  Details
</button>
@if($itemId)
        <button type="button"
                class="btn btn-outline-danger btn-sm btn-dispute"
                data-bs-toggle="modal"
                data-bs-target="#disputeModal"
                data-item-id="{{ $itemId }}"
                data-date="{{ \Carbon\Carbon::parse($row->billing_date)->format('Y-m-d') }}"
                data-time="{{ \Carbon\Carbon::parse($row->billing_date)->format('h:ia') }}"
                data-ref="{{ $row->ref_no }}"
                data-description="{{ $row->description }}"
                data-provider="{{ $row->provider }}"
                data-amount="₱{{ number_format($row->amount,2) }}">
          Request Review
        </button>
      @else
      <span class="text-muted">No details available</span>
      @endif
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="7" class="text-center text-muted">No billing items found.</td>
          </tr>
        @endforelse
        </tbody>
      </table>
    </div>

    {{-- Download Statement --}}
    <div class="text-end mt-3">
      <a href="{{ route('patient.billing.statement',['admission_id'=>$admissionId]) }}"
         class="btn btn-primary btn-sm">
        <i class="fas fa-download me-1"></i> Download Statement
      </a>
    </div>
  </div>
</div>



{{-- DISPUTE MODAL --}}
<div class="modal fade" id="disputeModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-primary fw-bold">Request for Clarification</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="disputeForm"
            method="POST"
            action="{{ route('patient.disputes.store') }}"
            enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="bill_item_id" id="d-item-id">
        <div class="modal-body">
          <div class="bg-light rounded p-3 mb-4">
            <div class="row">
              <div class="col-4 small"><strong>Date:</strong> <span id="d-date"></span></div>
              <div class="col-4 small"><strong>Reference:</strong> <span id="d-ref"></span></div>
              <div class="col-4 small"><strong>Amount:</strong> <span id="d-amount"></span></div>
              <div class="col-4 small"><strong>Time:</strong> <span id="d-time"></span></div>
              <div class="col-4 small"><strong>Description:</strong> <span id="d-desc"></span></div>
              <div class="col-4 small"><strong>Provider:</strong> <span id="d-prov"></span></div>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Reason (Dispute Type)</label>
            <input type="text" name="reason" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Additional Details</label>
            <textarea name="details" rows="3" class="form-control"></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Supporting Documents</label>
            <input type="file" name="documents[]" class="form-control" multiple>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Submit</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- DETAILS / TIMELINE MODAL --}}
<div class="modal fade" id="detailsModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold">Complete Charge History</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <ul class="nav nav-tabs mb-3">
          <li class="nav-item">
            <button class="nav-link active" data-bs-toggle="tab"
                    data-bs-target="#tab-timeline" type="button">Timeline</button>
          </li>
          <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab"
                    data-bs-target="#tab-details" type="button">Charge Details</button>
          </li>
        </ul>
        <div class="tab-content">
          <div class="tab-pane fade show active" id="tab-timeline">
            <div id="timeline-content">
              <p class="text-muted">Loading timeline…</p>
            </div>
          </div>
          <div class="tab-pane fade" id="tab-details">
            <div id="charge-details-content">
              <p class="text-muted">Loading details…</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  // Dispute modal population
  document.querySelectorAll('.btn-dispute').forEach(btn => {
    btn.addEventListener('click', e => {
      const d = e.currentTarget.dataset;
      document.getElementById('d-item-id').value     = d.itemId;
      document.getElementById('d-date').textContent   = d.date;
      document.getElementById('d-ref').textContent    = d.ref;
      document.getElementById('d-amount').textContent = d.amount;
      document.getElementById('d-time').textContent   = d.time;
      document.getElementById('d-desc').textContent   = d.description;
      document.getElementById('d-prov').textContent   = d.provider;
    });
  });

  // Details modal population
  document.querySelectorAll('.btn-details').forEach(btn => {
    btn.addEventListener('click', () => {
      const items    = JSON.parse(btn.dataset.items || '[]');
      const provider = btn.dataset.provider;

      // Timeline
      const tlHtml = items.map(it => `
        <li class="list-group-item d-flex justify-content-between">
          <div>
            <strong>${it.description}</strong><br>
            <small class="text-muted">
              ${provider} • ${it.status} •
              ${new Date(it.billing_date).toLocaleString()}
            </small>
          </div>
          <div class="fw-semibold">
            ₱${Number(it.amount).toLocaleString(undefined,{minimumFractionDigits:2})}
          </div>
        </li>
      `).join('');
      document.getElementById('timeline-content').innerHTML = `
        <ul class="list-group list-group-flush">${tlHtml}</ul>
      `;

      // Charge Details table
      const detailsRows = items.map(it => `
        <tr>
          <td>${new Date(it.billing_date).toLocaleDateString()}</td>
          <td>${it.description}</td>
          <td>${provider}</td>
          <td class="text-end">
            ₱${Number(it.amount).toLocaleString(undefined,{minimumFractionDigits:2})}
          </td>
          <td>${it.status}</td>
        </tr>
      `).join('');
      document.getElementById('charge-details-content').innerHTML = `
        <table class="table table-sm mb-0">
          <thead>
            <tr>
              <th>Date</th><th>Description</th><th>Provider</th>
              <th class="text-end">Amount</th><th>Status</th>
            </tr>
          </thead>
          <tbody>${detailsRows}</tbody>
        </table>
      `;
    });
    // build audit timeline
const timeline = JSON.parse(btn.dataset.timeline || '[]');
const auditHtml = timeline.map(t => `
  <li class="list-group-item d-flex justify-content-between">
    <div>
      <i class="fas fa-circle-notch me-2 text-primary"></i>
      <strong>${t.actor}</strong>
      <small class="text-muted">(${t.dept})</small> – ${t.text}
    </div>
    <small class="text-muted">
      ${new Date(t.stamp).toLocaleString()}
    </small>
  </li>
`).join('');

document.getElementById('timeline-content').innerHTML = `
  <ul class="list-group list-group-flush">${auditHtml}</ul>
`;

  });
});
</script>
@endpush
