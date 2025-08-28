{{resources/views/billing/charge/edit.blade.php}}

@extends('layouts.billing')

@section('content')
<div class="container-fluid p-4">
  <h4 class="mb-4">Edit Charge #{{ $item->billing_item_id }}</h4>

  <form method="POST" action="{{ route('billing.charges.update', $item->billing_item_id) }}">
    @csrf
    @method('PUT')

    {{-- Patient --}}
    <div class="mb-3">
      <label for="patient_id" class="form-label">Patient</label>
      <select
        id="patient_id"
        name="patient_id"
        class="form-select @error('patient_id') is-invalid @enderror"
        required
      >
        <option value="">— select patient —</option>
        @foreach($patients as $p)
          <option
            value="{{ $p->patient_id }}"
            {{ $item->bill->patient_id == $p->patient_id ? 'selected' : '' }}
          >
            {{ $p->patient_last_name }}, {{ $p->patient_first_name }}
            (ID: {{ str_pad($p->patient_id,6,'0',STR_PAD_LEFT) }})
          </option>
        @endforeach
      </select>
      @error('patient_id')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>

    {{-- Service --}}
    <div class="mb-3">
      <label for="service_id" class="form-label">Service</label>
      <select
        id="service_id"
        name="service_id"
        class="form-select @error('service_id') is-invalid @enderror"
        required
      >
        <option value="">— select service —</option>
        @foreach($services as $s)
          <option
            value="{{ $s->service_id }}"
            {{ $item->service_id == $s->service_id ? 'selected' : '' }}
          >
            {{ $s->service_name }} — ₱{{ number_format($s->price,2) }}
          </option>
        @endforeach
      </select>
      @error('service_id')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>

    <div class="row g-3">
      {{-- Quantity --}}
      <div class="col-md-6">
        <label for="quantity" class="form-label">Quantity</label>
        <input
          type="number"
          id="quantity"
          name="quantity"
          class="form-control @error('quantity') is-invalid @enderror"
          value="{{ old('quantity', $item->quantity) }}"
          min="1"
          required
        >
        @error('quantity')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>

      {{-- Amount --}}
      <div class="col-md-6">
        <label for="amount" class="form-label">Amount (₱)</label>
        <input
          type="number"
          id="amount"
          name="amount"
          step="0.01"
          class="form-control @error('amount') is-invalid @enderror"
          value="{{ old('amount', $item->amount) }}"
          min="0"
          required
        >
        @error('amount')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>
    </div>

    <div class="mt-4">
      <button type="submit" class="btn btn-primary">
        <i class="fa-solid fa-save me-1"></i> Update Charge
      </button>
      <a href="{{ route('billing.dashboard') }}" class="btn btn-secondary ms-2">
        Cancel
      </a>
    </div>
  </form>
</div>
@endsection
