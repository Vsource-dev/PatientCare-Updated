@extends('layouts.billing')

@section('content')
<div class="container-fluid p-4">
  <h4 class="mb-3">Settle / Discharge Patients</h4>

  {{-- ── Search & Sort ───────────────────────────────────────────────── --}}
  <form method="GET" action="{{ route('billing.discharge.index') }}"
        class="row g-2 mb-4 align-items-end">
    <div class="col-auto">
      <label class="form-label small mb-1">Search</label>
      <input type="text" name="search"
             value="{{ request('search') }}"
             class="form-control form-control-sm"
             placeholder="MRN or Name">
    </div>
    <div class="col-auto">
      <label class="form-label small mb-1">Sort by</label>
      <select name="sort_by" class="form-select form-select-sm">
        <option value="patient_id"        @selected(request('sort_by')=='patient_id')       >MRN</option>
        <option value="patient_last_name" @selected(request('sort_by')=='patient_last_name')>Name</option>
        <option value="balance"           @selected(request('sort_by')=='balance')          >Outstanding</option>
      </select>
    </div>
    <div class="col-auto">
      <label class="form-label small mb-1">Direction</label>
      <select name="sort_dir" class="form-select form-select-sm">
        <option value="asc"  @selected(request('sort_dir')=='asc') >Asc</option>
        <option value="desc" @selected(request('sort_dir')=='desc')>Desc</option>
      </select>
    </div>
    <div class="col-auto">
      <button class="btn btn-primary btn-sm">Apply</button>
    </div>
  </form>

  {{-- ── Patients Table ─────────────────────────────────────────────── --}}
  <div class="table-responsive rounded shadow-sm">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th>MRN</th>
          <th>Name</th>
          <th class="text-end">Outstanding ₱</th>
          <th class="text-center">Action</th>
        </tr>
      </thead>
      <tbody>
      @forelse($patients as $p)
        @php $modalId = 'depositModal-'.$p->patient_id; @endphp

        <tr>
          <td>{{ str_pad($p->patient_id,8,'0',STR_PAD_LEFT) }}</td>
          <td>{{ $p->patient_first_name }} {{ $p->patient_last_name }}</td>
          <td class="text-end">{{ number_format($p->balance,2) }}</td>
          <td class="text-center">
            {{-- History button --}}
            <button class="btn btn-outline-secondary btn-sm me-1"
                    data-bs-toggle="modal" data-bs-target="#{{ $modalId }}">
              <i class="fa-solid fa-clock-rotate-left me-1"></i> History
            </button>

            @if($p->balance == 0)
              {{-- Settle --}}
            <form method="POST" action="{{ route('billing.discharge.settle', $p->patient_id) }}"
                    class="d-inline">
                @csrf
                <button class="btn btn-success btn-sm">
                  <i class="fa-solid fa-circle-check me-1"></i> Settle
                </button>
              </form>
            @else
              {{-- Deposit --}}
              <button class="btn btn-primary btn-sm"
                      data-bs-toggle="modal" data-bs-target="#{{ $modalId }}">
                <i class="fa-solid fa-money-bill-wave me-1"></i> Deposit
              </button>
            @endif
          </td>
        </tr>

        {{-- Push each modal to a stack so it's OUTSIDE the table --}}
        @push('modals')
          <div class="modal fade" id="{{ $modalId }}" tabindex="-1"
               aria-labelledby="{{ $modalId }}Label" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
              <div class="modal-content">
                <form method="POST" action="{{ route('billing.deposits.store') }}">
                  @csrf
                  <input type="hidden" name="patient_id" value="{{ $p->patient_id }}">

                  <div class="modal-header">
                    <h5 class="modal-title" id="{{ $modalId }}Label">
                      Deposits – {{ $p->patient_first_name }} {{ $p->patient_last_name }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                  </div>

                  <div class="modal-body">
                    {{-- New Deposit --}}
                    <div class="row g-3 mb-4">
                      <div class="col-md-6">
                        <label class="form-label">Amount (₱)</label>
                        <input type="number" name="amount" step="0.01" min="0"
                               class="form-control" required autofocus>
                      </div>
                      <div class="col-md-6">
                        <label class="form-label">Deposit Date</label>
                        <input type="date" name="deposited_at"
                               class="form-control"
                               value="{{ now()->toDateString() }}" required>
                      </div>
                    </div>

                    {{-- History --}}
                    <h6 class="fw-semibold mb-2">Last 5 Deposits</h6>
                    <div class="table-responsive rounded shadow-sm">
                      <table class="table table-sm table-bordered mb-0">
                        <thead class="table-light">
                          <tr><th>Date</th><th class="text-end">Amount ₱</th></tr>
                        </thead>
                        <tbody>
                          @forelse($p->deposits->take(5) as $d)
                            <tr>
                              <td>{{ $d->deposited_at->format('Y-m-d') }}</td>
                              <td class="text-end">{{ number_format($d->amount,2) }}</td>
                            </tr>
                          @empty
                            <tr>
                              <td colspan="2" class="text-center text-muted">No deposits yet.</td>
                            </tr>
                          @endforelse
                        </tbody>
                      </table>
                    </div>
                  </div>

                  <div class="modal-footer">
                    <button class="btn btn-primary">Save Deposit</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                      Close
                    </button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        @endpush
      @empty
        <tr>
          <td colspan="4" class="text-center py-3 text-muted">No active patients.</td>
        </tr>
      @endforelse
      </tbody>
    </table>
  </div>

  {{-- Pagination --}}
  <div class="mt-3">
    {{ $patients->links() }}
  </div>
</div>

{{-- Render all the modals collected above --}}
@stack('modals')
@endsection
