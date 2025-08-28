{{--resources/views/billing/dashboard.blade.php--}}
@extends('layouts.billing')

@section('content')
<div class="container-fluid min-vh-100 p-4 d-flex flex-column" style="background-color: #fafafa;">

  <!-- Header -->
  <header class="mb-3">
    <h4 class="hdng">Patient Billing Management</h4>
    <p class="text-muted mb-0">Manage patient billing records and disputes.</p>
  </header>

  <!-- Metrics -->
  <div class="row g-3 mb-4">
    <div class="col-lg-3 col-md-6">
      <div class="card border">
        <div class="card-body d-flex align-items-center">
          <div class="me-3"><i class="fa-solid fa-chart-simple fa-2x text-secondary"></i></div>
          <div>
            <div class="text-muted small">Total Revenue</div>
            <h5 class="mb-0">₱{{ number_format($totalRevenue, 2) }}</h5>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-6">
      <div class="card border">
        <div class="card-body d-flex align-items-center">
          <div class="me-3"><i class="fa-solid fa-peso-sign fa-2x text-secondary"></i></div>
          <div>
            <div class="text-muted small">Outstanding Balance</div>
            <h5 class="mb-0">₱{{ number_format($outstandingBalance, 2) }}</h5>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-6">
      <div class="card border">
        <div class="card-body d-flex align-items-center">
          <div class="me-3"><i class="fa-solid fa-bed fa-2x text-secondary"></i></div>
          <div>
            <div class="text-muted small">Active Patients</div>
            <h5 class="mb-0">{{ $activePatients }}</h5>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-6">
      <div class="card border">
        <div class="card-body d-flex align-items-center">
          <div class="me-3"><i class="fa-solid fa-person-circle-question fa-2x text-secondary"></i></div>
          <div>
            <div class="text-muted small">Pending Disputes</div>
            <h5 class="mb-0">{{ $pendingDisputes }}</h5>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Recent Billing Items -->
  <div class="card mb-5 flex-grow-1">
    <div class="card-body d-flex flex-column">
      <h5 class="fw-semibold mb-3">
        <i class="fa-solid fa-calendar-plus me-2 text-primary"></i>
        Recent Billing Activity
      </h5>

      <div class="table-responsive rounded shadow-sm p-2 overflow-auto">
        <table class="table table-hover mb-0 align-middle">
          <thead class="table-light">
            <tr>
              <th>MRN</th>
              <th>Patient</th>
              <th>Description</th>
              <th>Origin</th>
              <th class="text-end">Amount</th>
            </tr>
          </thead>
          <tbody>
            @forelse($recentBillItems as $item)
              @php
                $p    = optional($item->bill->patient);
                $svc  = optional($item->service);
                $dept = optional($svc->department);
              @endphp
              <tr>
                <td>{{ $p->patient_id ?? '—' }}</td>
                <td>{{ trim(($p->patient_first_name ?? '').' '.($p->patient_last_name ?? '')) ?: '—' }}</td>
                <td>{{ $svc->service_name ?? 'N/A' }}</td>
                <td>
                  <span class="badge bg-info">
                    {{ $dept->department_name ?? '—' }}
                  </span>
                </td>
                <td class="text-end">
                  ₱{{ number_format($item->amount - ($item->discount_amount ?? 0), 2) }}
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="text-center text-muted py-3">
                  No recent billing activity.
                </td>
              </tr>
            @endforelse
          </tbody>
          @if($recentBillItems->isNotEmpty())
            <tfoot>
              <tr class="fw-semibold">
                <td colspan="4" class="text-end">Subtotal</td>
                <td class="text-end">₱{{ number_format($billItemsTotal,2) }}</td>
              </tr>
            </tfoot>
          @endif
        </table>
      </div>
    </div>
  </div>

  <!-- Recent Service Assignments -->
  <div class="card flex-grow-1">
    <div class="card-body d-flex flex-column">
      <h5 class="fw-semibold mb-3">
        <i class="fa-solid fa-tools me-2 text-primary"></i>
        Recent Service Assignments
      </h5>

      <div class="table-responsive rounded shadow-sm p-2 overflow-auto">
        <table class="table table-hover mb-0 align-middle">
          <thead class="table-light">
            <tr>
              <th>MRN</th>
              <th>Patient</th>
              <th>Service</th>
              <th>Dept.</th>
              <th class="text-end">Price</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            @forelse($recentServiceAssignments as $sa)
              @php
                $p    = optional($sa->patient);
                $svc  = optional($sa->service);
                $dept = optional($svc->department);
              @endphp
              <tr>
                <td>{{ $p->patient_id ?? '—' }}</td>
                <td>{{ trim(($p->patient_first_name ?? '').' '.($p->patient_last_name ?? '')) ?: '—' }}</td>
                <td>{{ $svc->service_name ?? 'N/A' }}</td>
                <td>{{ $dept->department_name ?? '—' }}</td>
                <td class="text-end">₱{{ number_format($svc->price ?? 0,2) }}</td>
                <td>
                  <span class="badge bg-{{ $sa->service_status==='confirmed'?'success':'secondary' }}">
                    {{ ucfirst($sa->service_status) }}
                  </span>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="text-center text-muted py-3">
                  No recent services.
                </td>
              </tr>
            @endforelse
          </tbody>
          @if($recentServiceAssignments->isNotEmpty())
            <tfoot>
              <tr class="fw-semibold">
                <td colspan="4" class="text-end">Subtotal</td>
                <td class="text-end">₱{{ number_format($servicesTotal,2) }}</td>
                <td></td>
              </tr>
            </tfoot>
          @endif
        </table>
      </div>
    </div>
  </div>

</div>
@endsection
