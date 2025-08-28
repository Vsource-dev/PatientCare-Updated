{{-- resources/views/billing/dispute/queue.blade.php --}}
@extends('layouts.billing')

@section('content')
<div class="container-fluid min-vh-100 p-4 d-flex flex-column">

  <header class="mb-3">
    <h4 class="hdng"><i class="fa-solid fa-ticket me-2"></i>Dispute Tickets</h4>
    <p class="text-muted">Manage billing disputes that require attention and resolution.</p>
  </header>

  <div class="card border mb-4">
    <div class="card-body">
      <form method="GET" action="{{ route('billing.dispute.queue') }}">
        <div class="row g-3 align-items-center">
          <div class="col-md-8">
            <div class="input-group">
              <input type="text"
                     name="q"
                     class="form-control"
                     placeholder="Enter MRN or Patient Name"
                     value="{{ request('q') }}">
              <button class="btn btn-outline-primary" type="submit">
                <i class="fa-solid fa-magnifying-glass me-2"></i>Search
              </button>
            </div>
          </div>
          <div class="col-md-4">
            <select name="status"
                    class="form-select"
                    onchange="this.form.submit()">
              <option value="all"      {{ request('status')=='all'      ? 'selected':'' }}>All</option>
              <option value="pending"  {{ request('status')=='pending'  ? 'selected':'' }}>Pending</option>
              <option value="approved" {{ request('status')=='approved' ? 'selected':'' }}>Approved</option>
              <option value="rejected" {{ request('status')=='rejected' ? 'selected':'' }}>Rejected</option>
            </select>
          </div>
        </div>
      </form>
    </div>
  </div>

  <div class="card shadow-sm">
    <div class="card-body p-0">
      <div class="table-responsive rounded" style="max-height: 500px;">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light sticky-top">
            <tr>
              <th>#</th>
              <th>Patient</th>
              <th>Description</th>
              <th>Origin</th>
              <th class="text-end">Amount</th>
              <th class="text-center">Actions</th>
            </tr>
          </thead>
          <tbody>
    @forelse($disputes as $dispute)
      <tr>
        <td>{{ $loop->iteration + ($disputes->currentPage()-1)*$disputes->perPage() }}</td>
        <td>
          <div class="fw-semibold">
            {{ optional($dispute->patient)->patient_first_name }}
            {{ optional($dispute->patient)->patient_last_name }}
          </div>
          <small class="text-muted">
            ID: {{ str_pad(optional($dispute->patient)->patient_id, 8, '0', STR_PAD_LEFT) }}
          </small>
        </td>
        <td>{{ Str::limit($dispute->reason, 40) }}</td>
        <td>
          {{-- ✅ FIX: Use the new 'disputable' relationship --}}
          <span class="badge bg-info">
            {{ $dispute->disputable->service->department->department_name ?? 'N/A' }}
          </span>
        </td>
        <td class="text-end text-danger">
          {{-- ✅ FIX: Check for 'amount' or 'total' property --}}
          ₱{{ number_format($dispute->disputable->amount ?? $dispute->disputable->total ?? 0, 2) }}
        </td>
        <td class="text-center">
          <a href="{{ route('billing.dispute.show', $dispute) }}"
             class="btn btn-sm btn-outline-success">
            <i class="fa-solid fa-expand me-1"></i> Review
          </a>
        </td>
      </tr>
    @empty
      <tr>
        <td colspan="6" class="text-center text-muted">No disputes found.</td>
      </tr>
    @endforelse
</tbody>
        </table>
      </div>

      <div class="p-3">
        {{ $disputes->withQueryString()->links() }}
      </div>
    </div>
  </div>
</div>
@endsection
