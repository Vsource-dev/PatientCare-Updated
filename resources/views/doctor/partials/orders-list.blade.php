{{-- resources/views/doctor/partials/orders-list.blade.php --}}
@if($serviceOrders->isEmpty() && $medOrders->isEmpty())
  <p class="text-muted">No orders found for this patient.</p>
@else

  @if($serviceOrders->isNotEmpty())
    <h6 class="mt-2">Service & Lab Orders</h6>
    <ul class="list-group mb-3">
      @foreach($serviceOrders as $so)
        <li class="list-group-item d-flex justify-content-between align-items-center">
          {{ $so->service->service_name }}
          <span class="badge bg-secondary">{{ ucfirst($so->service_status) }}</span>
          <small class="text-muted">{{ 
             \Carbon\Carbon::parse($so->created_at)->format('M j, Y H:i') 
          }}</small>
        </li>
      @endforeach
    </ul>
  @endif

  @if($medOrders->isNotEmpty())
    <h6 class="mt-2">Medication Orders</h6>
    <ul class="list-group">
      @foreach($medOrders as $mo)
        <li class="list-group-item">
          {{ $mo->service->service_name }} &times;{{ $mo->quantity_asked }}
          <br>
          <small class="text-muted">
            Ordered on {{ \Carbon\Carbon::parse($mo->datetime)->format('M j, Y H:i') }}
          </small>
        </li>
      @endforeach
    </ul>
  @endif

@endif
