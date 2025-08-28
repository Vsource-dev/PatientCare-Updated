@extends('layouts.billing')

@section('content')
<div class="container-fluid min-vh-100 p-4" style="background-color:#fafafa">

  <!-- Header -->
  <header class="mb-3">
    <h4 class="hdng">Patient Billing Management</h4>
    <p>Manage your billing records and disputes</p>
  </header>

  <!-- Search Menu -->
  <div class="card border mb-4">
    <div class="card-body">
      <form method="GET" action="{{ route('billing.main') }}">
        <div class="row g-3 align-items-end">
          <div class="col-md-6">
            <label class="form-label">Search Description</label>
            <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="e.g. Paracetamol">
          </div>
          <div class="col-md-3">
            <label class="form-label">Admission</label>
            <select name="admission_id" class="form-select" onchange="this.form.submit()">
              <option value="">All Admissions</option>
              @foreach($admissions as $adm)
                <option value="{{ $adm->admission_id }}"
                  {{ $adm->admission_id == $admissionId ? 'selected' : '' }}>
                  {{ $adm->admission_date->format('M d, Y') }}
                </option>
              @endforeach
            </select>
          </div>
          <div class="col-md-3 text-end">
            <button class="btn btn-primary">
              <i class="fa-solid fa-magnifying-glass me-1"></i>Filter
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- Transactions Table -->
  <div class="card shadow-sm">
    <div class="card-body p-0">
      <div class="table-responsive" style="max-height:500px; overflow-y:auto;">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>Date</th>
              <th>Ref #</th>
              <th>Description</th>
              <th>Provider</th>
              <th class="text-end">Amount</th>
              <th class="text-center">Status</th>
            </tr>
          </thead>
          <tbody>
            @forelse($items as $row)
              <tr>
                <td>{{ \Carbon\Carbon::parse($row->billing_date)->format('M d, Y') }}</td>
                <td>{{ $row->ref_no }}</td>
                <td>{{ $row->description }}</td>
                <td>{{ $row->provider }}</td>
                <td class="text-end">₱{{ number_format($row->amount,2) }}</td>
                <td class="text-center">
                  <span class="badge {{
                    $row->status==='pending'  ? 'bg-warning' 
                  : ($row->status==='approved' ? 'bg-success' : 'bg-secondary')
                  }}">
                    {{ ucfirst($row->status) }}
                  </span>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="text-center text-muted py-3">
                  No records found.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Totals -->
  <div class="card border mt-4 p-3">
    <div class="d-flex justify-content-between">
      <strong>Total Charges:</strong>
      <span>₱{{ number_format($totals['total'],2) }}</span>
    </div>
    <div class="d-flex justify-content-between">
      <strong>Balance:</strong>
      <span>₱{{ number_format($totals['balance'],2) }}</span>
    </div>
  </div>

</div>
@endsection
