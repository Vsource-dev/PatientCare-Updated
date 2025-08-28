
{{-- resources/views/laboratory/show.blade.php --}}

@extends('layouts.billing')

@section('content')
<div class="container-fluid py-4">

  {{-- Summary Card --}}
  <div class="card shadow-sm border-0 mb-4">

    <div class="card-header bg-white border-bottom">
      <h4 class="mb-0 fw-bold">
        <i class="fa-solid fa-clipboard-list me-2 text-primary"></i>Summary
      </h4>
      <p class="text-muted small mb-0">Kindly review the summary details below before approval</p>
    </div>

    <div class="card-body">

      {{-- Info Row --}}
      <div class="row mb-4">
        <div class="col-4">
          <label class="fw-semibold text-muted"># Reference No.</label>
          <div class="p-2 border rounded">{{ $charge->reference_no }}</div>
        </div>
        <div class="col-4">
          <label class="fw-semibold text-muted">
            <i class="fa-solid fa-bed me-1"></i>Patient
          </label>
          <div class="p-2 border rounded">{{ $charge->patient->full_name }}</div>
        </div>
        <div class="col-4">
          <label class="fw-semibold text-muted">
            <i class="fa-solid fa-user-doctor me-1"></i>Doctor Assigned
          </label>
          <div class="p-2 border rounded">{{ $charge->doctor->full_name }}</div>
        </div>
      </div>

      {{-- Item Table --}}
      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead class="table-light">
            <tr>
              <th>#</th>
              <th>Item Name</th>
              <th>Quantity</th>
              <th>Unit Price</th>
              <th>Subtotal</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($charge->items as $i => $item)
              <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $item->name }}</td>
                <td>{{ $item->quantity }}</td>
                <td>₱{{ number_format($item->unit_price, 2) }}</td>
                <td>₱{{ number_format($item->quantity * $item->unit_price, 2) }}</td>
              </tr>
            @empty
              <tr class="text-center text-muted">
                <td colspan="5"><em>**Nothing Follows**</em></td>
              </tr>
            @endforelse
          </tbody>
          <tfoot>
            <tr class="bg-light fw-bold">
              <td colspan="4" class="text-end">Total</td>
              <td class="text-start text-danger">₱{{ number_format($charge->total, 2) }}</td>
            </tr>
          </tfoot>
        </table>
      </div>

    </div>
  </div>

  {{-- Action Buttons --}}
  <div class="d-flex justify-content-end mt-auto gap-2">
    <a href="{{ route('billing.panel') }}" class="btn btn-secondary">
      <i class="fa-solid fa-arrow-left me-1"></i>Back
    </a>
    <form method="POST" action="{{ route('billing.process', $charge->id) }}">
      @csrf
      <button type="submit" class="btn btn-primary">
        <i class="fa-solid fa-circle-check me-1"></i>Process
      </button>
    </form>
  </div>

</div>
@endsection
