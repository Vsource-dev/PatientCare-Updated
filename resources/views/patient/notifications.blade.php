{{-- resources/views/patient/notifications.blade.php --}}
@extends('layouts.patients')

@section('content')
<div class="container-fluid h-100 border p-4" style="background-color:#fafafa">
  <div class="mb-2">
    <h4>Notifications</h4>
    <p>Welcome to Notifications! Stay updated on your billing.</p>
  </div>

  <div class="alert alert-warning">
    <i class="fa-solid fa-circle-info me-2"></i>
    <strong>Important!</strong> Do not share this information.
  </div>

  {{-- Filter & Mark-All --}}
  <div class="row mb-3 align-items-center">
    <div class="col-auto">
      <form method="GET" class="d-flex align-items-center">
        <label class="me-2 mb-0">Show:</label>
        <select name="filter" class="form-select form-select-sm" onchange="this.form.submit()">
          <option value="all"   {{ $filter==='all'    ? 'selected' : '' }}>All</option>
          <option value="read"  {{ $filter==='read'   ? 'selected' : '' }}>Read</option>
          <option value="unread"{{ $filter==='unread' ? 'selected' : '' }}>Unread</option>
        </select>
      </form>
    </div>
    <div class="col text-end">
      <form action="{{ route('notifications.markAllRead') }}" method="POST">
        @csrf
        <button class="btn btn-sm btn-outline-secondary">Mark All as Read</button>
      </form>
    </div>
  </div>

  <div class="card">
    <div class="card-body table-responsive" style="max-height:70vh">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light sticky-top">
          <tr>
            <th>Type</th>
            <th>Message</th>
            <th>Date</th>
            <th>Time</th>
            <th>Status</th>
            <th class="text-center">Action</th>
          </tr>
        </thead>
        <tbody>
          @forelse($notifications as $n)
            <tr class="{{ $n->read_at ? '' : 'table-warning' }}">
              <td>{{ $n->data['type'] ?? 'Notification' }}</td>
              <td>{{ $n->data['message'] ?? '—' }}</td>
              <td>{{ $n->created_at->format('M d, Y') }}</td>
              <td>{{ $n->created_at->format('g:i A') }}</td>
              <td>
                <span class="badge bg-{{ $n->read_at ? 'success' : 'danger' }}">
                  {{ $n->read_at ? 'Read' : 'Unread' }}
                </span>
              </td>
              <td class="text-center">
                @if(! empty($n->data['assignment_id']))
                  <button
                    type="button"
                    class="btn btn-sm btn-outline-primary btn-details"
                    data-url="{{ route('operating.details', $n->data['assignment_id']) }}">
                    View OR Charge
                  </button>
                @elseif(! empty($n->data['billing_item_id']))
                  <button
                    type="button"
                    class="btn btn-sm btn-outline-primary btn-details"
                    data-url="{{ route('patient.billing.chargeTrace', $n->data['billing_item_id']) }}">
                    View Charge
                  </button>
                @else
                  <span class="text-muted">—</span>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-center text-muted">No notifications found.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

{{-- DETAILS / TIMELINE MODAL --}}
<div class="modal fade" id="detailsModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold">Charge Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <ul class="nav nav-tabs mb-3">
          <li class="nav-item">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-timeline" type="button">
              Timeline
            </button>
          </li>
          <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-details" type="button">
              Details
            </button>
          </li>
        </ul>
        <div class="tab-content">
          <div class="tab-pane fade show active" id="tab-timeline">
            <p class="text-muted">Loading…</p>
          </div>
          <div class="tab-pane fade" id="tab-details">
            <p class="text-muted">Loading…</p>
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
  const modalEl = document.getElementById('detailsModal');
  const bsModal = new bootstrap.Modal(modalEl);

  document.querySelectorAll('.btn-details').forEach(btn => {
    btn.addEventListener('click', async () => {
      // show the modal immediately
      bsModal.show();

      // loading placeholders
      document.getElementById('tab-timeline').innerHTML = '<p class="text-muted">Loading…</p>';
      document.getElementById('tab-details').innerHTML  = '<p class="text-muted">Loading…</p>';

      try {
        const res  = await fetch(btn.dataset.url);
        const html = await res.text();
        const doc  = new DOMParser().parseFromString(html, 'text/html');

        // pull out the two sections from the fetched page
        const tl = doc.querySelector('#charge-trace-timeline');
        const dt = doc.querySelector('#charge-trace-details');

        document.getElementById('tab-timeline').innerHTML = tl
          ? tl.innerHTML
          : '<p class="text-muted">No timeline available.</p>';

        document.getElementById('tab-details').innerHTML = dt
          ? dt.innerHTML
          : '<p class="text-muted">No details available.</p>';

      } catch (err) {
        document.getElementById('tab-timeline').innerHTML = '<p class="text-danger">Error loading timeline.</p>';
        document.getElementById('tab-details').innerHTML  = '<p class="text-danger">Error loading details.</p>';
      }
    });
  });
});
</script>
@endpush
