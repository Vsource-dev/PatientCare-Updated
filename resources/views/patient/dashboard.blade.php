{{-- resources/views/patient/dashboard.blade.php --}}
@extends('layouts.patients')

@section('content')
<div class="container-fluid py-4">

  {{-- Greeting --}}
  <div class="mb-4">
    <h1 class="h4 fw-bold">Welcome back, {{ $patient->patient_first_name }} 👋</h1>
    <p class="text-muted mb-0">Your health hub — quick access to records, bills & schedules.</p>
  </div>

  {{-- Quick Info Stats --}}
  <div class="row g-3 mb-4">
    @php
      $stats = [
        ['label' => 'Patient ID', 'value' => str_pad($user->patient_id, 8, '0', STR_PAD_LEFT)],
        ['label' => 'Room Number', 'value' => $admission->room_number ?? '—'],
        ['label' => 'Latest Admit Date', 'value' => $admission?->admission_date?->format('M d, Y') ?? '—'],
        ['label' => 'Amount Due', 'value' => '₱'.number_format($amountDue, 2), 'highlight' => true]
      ];
    @endphp
    @foreach($stats as $stat)
      <div class="col-md-3">
        <div class="card shadow-sm h-100 border-0 rounded-3">
          <div class="card-body">
            <small class="text-muted d-block">{{ $stat['label'] }}</small>
            <div class="fs-5 fw-semibold {{ $stat['highlight'] ?? false ? 'text-danger' : '' }}">
              {{ $stat['value'] }}
            </div>
          </div>
        </div>
      </div>
    @endforeach
  </div>

  {{-- Prescriptions & Schedule --}}
  <div class="row g-3 mb-4">
    <div class="col-md-6">
      <div class="card shadow-sm h-100 border-0 rounded-3">
        <div class="card-header bg-white border-0 fw-semibold">Prescriptions</div>
        <div class="card-body" style="max-height: 30vh; overflow-y:auto;">
          @forelse($prescriptions as $item)
            <div class="mb-3">
              <div class="fw-semibold">{{ $item->service->service_name }}</div>
              <small class="text-muted">
                {{ $item->dosage ?? 'Qty: '.$item->quantity_asked }}  
                · {{ \Carbon\Carbon::parse($item->datetime)->format('M d, Y h:i A') }}
              </small>
            </div>
          @empty
            <p class="text-muted">No prescriptions yet.</p>
          @endforelse
        </div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="card shadow-sm h-100 border-0 rounded-3">
        <div class="card-header bg-white border-0 fw-semibold">Today's Schedule</div>
        <div class="card-body" style="max-height: 30vh; overflow-y:auto;">
          @forelse($todaySchedule as $sched)
            <div class="mb-3">
              <div class="fw-semibold">{{ $sched->service->service_name }}</div>
              <small class="text-muted">
                {{ \Carbon\Carbon::parse($sched->datetime)->format('h:i A') }} ·
                <span class="badge bg-{{ $sched->service_status === 'confirmed' ? 'success' : 'secondary' }}">
                  {{ ucfirst($sched->service_status) }}
                </span>
              </small>
            </div>
          @empty
            <p class="text-muted">No appointments today.</p>
          @endforelse
        </div>
      </div>
    </div>
  </div>

  {{-- Doctors & Pharmacy --}}
  <div class="row g-3 mb-4">
    <div class="col-md-6">
      <div class="card shadow-sm h-100 border-0 rounded-3">
        <div class="card-header bg-white border-0 fw-semibold">Assigned Doctors</div>
        <div class="card-body" style="max-height: 30vh; overflow-y:auto;">
          @forelse($assignedDoctors as $doc)
            <p class="mb-2">
              Dr. {{ $doc->doctor_name ?? 'No Doctor' }}
              <small class="text-muted">({{ $doc->department->department_name ?? '—' }})</small>
            </p>
          @empty
            <p class="text-muted">No doctors assigned.</p>
          @endforelse
        </div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="card shadow-sm h-100 border-0 rounded-3">
        <div class="card-header bg-white border-0 fw-semibold">Pharmacy Charges</div>
        <div class="card-body" style="max-height: 30vh; overflow-y:auto;">
          @forelse($pharmacyCharges as $c)
            <div class="mb-2">
              <div class="fw-semibold">{{ $c->service->service_name }}</div>
              <small class="text-muted">
                ₱{{ number_format($c->quantity_asked * $c->service->price, 2) }} ·
                {{ \Carbon\Carbon::parse($c->datetime)->format('M d, Y h:i A') }}
              </small>
            </div>
          @empty
            <p class="text-muted">No pharmacy charges yet.</p>
          @endforelse
        </div>
        <div class="card-footer bg-light fw-semibold">
          Total: ₱{{ number_format($pharmacyTotal, 2) }}
        </div>
      </div>
    </div>
  </div>

  {{-- Hospital Services --}}
  <div class="card shadow-sm border-0 rounded-3">
    <div class="card-header bg-white border-0 fw-semibold">
      Hospital Services <span class="badge bg-primary">{{ $serviceAssignments->count() }}</span>
    </div>
    <div class="card-body p-0" style="max-height: 40vh; overflow-y:auto;">
      @if($serviceAssignments->isEmpty())
        <p class="text-muted p-3 mb-0">No services ordered yet.</p>
      @else
        <div class="table-responsive">
          <table class="table table-sm mb-0 align-middle">
            <thead class="table-light">
              <tr>
                <th>Date</th>
                <th>Service</th>
                <th>Dept.</th>
                <th class="text-end">Price</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              @foreach($serviceAssignments as $sa)
                <tr>
                  <td>{{ \Carbon\Carbon::parse($sa->datetime)->format('M d, Y') }}</td>
                  <td>{{ $sa->service->service_name }}</td>
                  <td>{{ $sa->service->department->department_name ?? '—' }}</td>
                  <td class="text-end">₱{{ number_format($sa->service->price, 2) }}</td>
                  <td>
                    <span class="badge bg-{{ $sa->service_status === 'confirmed' ? 'success' : 'secondary' }}">
                      {{ ucfirst($sa->service_status) }}
                    </span>
                  </td>
                </tr>
              @endforeach
            </tbody>
            <tfoot class="table-light">
              <tr class="fw-semibold">
                <td colspan="3" class="text-end">Total</td>
                <td class="text-end">₱{{ number_format($servicesTotal, 2) }}</td>
                <td></td>
              </tr>
            </tfoot>
          </table>
        </div>
      @endif
    </div>
  </div>

</div>
@endsection
