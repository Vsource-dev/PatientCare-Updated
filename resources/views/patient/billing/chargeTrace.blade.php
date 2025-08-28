{{-- timeline -----------------------------------------------------------------}}
<div id="charge-trace-timeline">
  <ul class="list-group mb-3">
    @forelse($charge->logs as $log)
      <li class="list-group-item">
        <span class="badge bg-secondary rounded-circle me-2">
          {{ strtoupper(substr($log->action,0,1)) }}
        </span>
        {{ ucfirst($log->action) }} –
        {{ $charge->service?->service_name ?? '—' }}
        <div class="small text-muted">
          {{ $log->created_at->format('Y-m-d H:i') }}
          by {{ $log->actor ?? 'System' }}
        </div>
      </li>
    @empty
      <li class="list-group-item text-center text-muted">
        <em>No trace records found.</em>
      </li>
    @endforelse
  </ul>
</div>

{{-- charge details -----------------------------------------------------------}}
<div id="charge-trace-details">
  <table class="table table-sm">
      <tr><th>Description</th><td>{{ $charge->service?->service_name ?? '—' }}</td></tr>
      <tr><th>ID</th><td>{{ $charge->billing_item_id }}</td></tr>
      <tr><th>Amount</th><td>₱{{ number_format($charge->amount,2) }}</td></tr>
      <tr><th>Status</th><td class="text-capitalize">{{ $charge->status }}</td></tr>
      <tr><th>Department</th>
          <td>{{ $charge->service?->department?->department_name ?? '—' }}</td></tr>
      <tr><th>Date</th>
          <td>{{ $charge->billing_date?->format('Y-m-d') ?? '—' }}</td></tr>
  </table>
</div>
