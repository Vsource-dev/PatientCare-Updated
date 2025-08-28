{{-- resources/views/supplies/queue.blade.php --}}

@extends('layouts.supplies')

@section('content')
<div class="container-fluid h-100 border p-4 d-flex flex-column" style="background-color: #fafafa;">

  {{-- Heading --}}
  <div class="mb-3">
    <h1 class="hdng">Patient Supply Requests Queue</h1>
    <p>Confirm request completion</p>
  </div>

  {{-- Filter Form --}}
  <form method="GET" action="{{ route('supplies.queue') }}">
    <div class="row mb-3 g-2">
      <div class="col-md-2">
        <button type="submit" class="btn btn-outline-primary w-100">
          <i class="fa-solid fa-filter me-2"></i>Filter
        </button>
      </div>
      <div class="col-md-2">
        <select name="status" class="form-select">
          <option value="">All Statuses</option>
          <option value="pending"   {{ request('status')=='pending'   ? 'selected' : '' }}>Pending</option>
          <option value="completed" {{ request('status')=='completed' ? 'selected' : '' }}>Completed</option>
        </select>
      </div>
      <div class="col-md-2">
        <select name="date_range" class="form-select">
          <option value="">Default</option>
          <option value="asc"  {{ request('date_range')=='asc'  ? 'selected' : '' }}>Oldest first</option>
          <option value="desc" {{ request('date_range')=='desc' ? 'selected' : '' }}>Newest first</option>
        </select>
      </div>
      <div class="col-md-6">
        <div class="input-group">
          <input type="text"
                 name="search"
                 class="form-control"
                 placeholder="Search by Name or MRN"
                 value="{{ request('search') }}">
          <span class="input-group-text"><i class="fas fa-search"></i></span>
        </div>
      </div>
    </div>
  </form>

  {{-- Table --}}
  <div class="table-responsive flex-grow-1" style="max-height: 500px; overflow-y: auto;">
    <table class="table table-hover table-sm mb-0">
      <thead class="table-light">
        <tr>
          <th>Entry Date</th>
          <th>MRN</th>
          <th>Patient Name</th>
          <th>Item Name</th>
          <th>Quantity</th>
          <th>Assigned By</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
       @forelse($miscReq as $misc)
<tr>
  <td>{{ $misc->created_at->format('Y-m-d') }}</td>
  <td>{{ $misc->patient->patient_id }}</td>
  <td>{{ $misc->patient->patient_first_name }} {{ $misc->patient->patient_last_name }}</td>
  <td>{{ optional($misc->service)->service_name ?? '—' }}</td>
  <td>{{ $misc->quantity }}</td>
  <td>
  {{ optional($misc->creator)->username ?? '—' }}
</td>
  <td>
    <span class="badge bg-{{ $misc->status==='pending'? 'warning text-dark':'success' }}">
      {{ ucfirst($misc->status) }}
    </span>
  </td>
  <td class="d-flex gap-1">
    <a href="{{ route('supplies.show', $misc->id) }}" class="btn btn-sm btn-outline-info">
      <i class="fa-solid fa-eye me-1"></i>View
    </a>
    @if($misc->status === 'pending')
      <form action="{{ route('supplies.complete', $misc->id) }}" method="POST" class="m-0">
        @csrf
        <button class="btn btn-sm btn-outline-success confirm-btn">
    <i class="fa-solid fa-check me-1"></i>Confirm
</button>

      </form>
    @else
      <span class="btn btn-sm btn-outline-secondary disabled">
        <i class="fa-solid fa-check-double me-1"></i>Done
      </span>
    @endif
  </td>
</tr>
        @empty
          <tr>
            <td colspan="8" class="text-center text-muted py-3">
              No supply requests found.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
    <div class="mt-3">
    {{ $miscReq->links() }}
</div>

  </div>
</div>
@endsection

@push('scripts')
<script>
  // (your existing SweetAlert or other JS can remain here)
</script>
@endpush
