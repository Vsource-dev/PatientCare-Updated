{{-- resources/views/patient/billing/partials/chargeTrace.blade.php --}}
{{-- no @extends or @section — just these two fragments --}}

{{-- timeline fragment --}}
<div id="charge-trace-timeline">
  <ul class="timeline list-unstyled mb-4">
    @forelse($charge->logs as $log)
      <li class="timeline-item mb-3 border-bottom pb-2">
        <h6 class="mb-1">
          {{ ucfirst($log->action) }}
          <span class="badge bg-light text-dark ms-2">
            {{ $log->created_at->format('Y-m-d H:i') }}
          </span>
        </h6>
        <p class="text-muted mb-1">{{ $log->message }}</p>
        <small class="text-muted">
          <i class="fa-solid {{ $log->icon }} me-1"></i>
          {{ $log->actor }}
        </small>
      </li>
    @empty
      <li class="text-center text-muted"><em>No trace records found.</em></li>
    @endforelse
  </ul>
</div>

{{-- details fragment --}}
<div id="charge-trace-details">
  <table class="table table-sm">
    <tbody>
      <tr><th>Description</th>
          <td>{{ $charge->service?->service_name ?? '—' }}</td></tr>
      <tr><th>Bill Item ID</th>
          <td>{{ $charge->billing_item_id }}</td></tr>
      <tr><th>Amount</th>
          <td>₱{{ number_format($charge->amount,2) }}</td></tr>
      <tr><th>Status</th>
          <td class="text-capitalize">{{ $charge->status }}</td></tr>
      <tr><th>Department</th>
          <td>{{ $charge->service?->department?->department_name ?? '—' }}</td></tr>
      <tr><th>Charged On</th>
          <td>{{ $charge->billing_date->format('Y-m-d') }}</td></tr>
    </tbody>
  </table>
</div>
