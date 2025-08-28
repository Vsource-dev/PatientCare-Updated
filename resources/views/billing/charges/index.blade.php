{{-- resources/views/billing/charges/index.blade.php --}}
@extends('layouts.billing')

@section('content')
<div class="container-fluid p-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Manual Charges</h4>
    <a href="{{ route('billing.charges.create') }}" class="btn btn-primary">
      <i class="fa-solid fa-plus me-1"></i> New Charge
    </a>
  </div>

  @if($items->isEmpty())
    <div class="alert alert-info">No manual charges have been posted yet.</div>
  @else
    <table class="table table-striped align-middle">
      <thead>
        <tr>
          <th>#</th>
          <th>Patient</th>
          <th>Service</th>
          <th>Qty</th>
          <th class="text-end">Amount</th>
          <th>Date</th>
          <th class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach($items as $item)
          <tr>
            <td>{{ $item->billing_item_id }}</td>
            <td>
              {{ optional($item->bill->patient)->patient_first_name }}
              {{ optional($item->bill->patient)->patient_last_name }}
            </td>
            <td>{{ optional($item->service)->service_name }}</td>
            <td>{{ $item->quantity }}</td>
            <td class="text-end">₱{{ number_format($item->amount,2) }}</td>
            <td>{{ optional($item->bill)->billing_date?->format('Y-m-d') }}</td>
            <td class="text-end">
              <a href="{{ route('billing.charges.show', $item->billing_item_id) }}"
                 class="btn btn-sm btn-outline-secondary me-1">
                View
              </a>
              <a href="{{ route('billing.charges.edit', $item->billing_item_id) }}"
                 class="btn btn-sm btn-outline-primary me-1">
                Edit
              </a>
              <a href="{{ route('billing.charges.audit', $item->billing_item_id) }}"
                 class="btn btn-sm btn-outline-info me-1">
                Audit
              </a>
              <form method="POST"
                    action="{{ route('billing.charges.destroy', $item->billing_item_id) }}"
                    class="d-inline"
                    onsubmit="return confirm('Delete this charge?')">
                @csrf
                @method('DELETE')
                <button class="btn btn-sm btn-outline-danger">Delete</button>
              </form>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>

    {{ $items->links() }}
  @endif
</div>
@endsection
