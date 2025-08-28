{{-- resources/views/patient/billing/disputeRequest.blade.php --}}
@extends('layouts.patients')

@section('content')
<main class="p-4" style="margin-left: 240px; background-color: #f9fafe;">
  <div class="container-fluid min-vh-100 d-flex flex-column">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h4 class="hdng mb-0">Billing &amp; Transactions</h4>
        <p class="text-muted mb-0">Monitor your charges and submit billing disputes</p>
      </div>
    </div>

    {{-- SUCCESS FLASH --}}
    @if(session('success'))
      <div class="alert alert-success">
        <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
      </div>
    @endif

    {{-- VALIDATION ERRORS --}}
    @if($errors->any())
      <div class="alert alert-danger">
        <ul class="mb-0">
          @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    {{-- CHARGE SUMMARY --}}
    <div class="card mb-4">
      <div class="card-body">
        <h6 class="mb-3 text-secondary"><i class="fa fa-receipt me-1"></i> Charge Summary</h6>
        <div class="row">
          <x-summary-col label="Entry Date/Time"
                         :value="$charge->billing_date->format('M d, Y H:i')" />
          <x-summary-col label="Prescribed By"
                         :value="optional($charge->bill->doctor)->doctor_name ?? '—'" />
          <x-summary-col label="Charge Origin"
                         :value="optional($charge->service->department)->department_name ?? '—'" />
          <x-summary-col label="Item"
                         :value="$charge->service?->service_name ?? '—'" />
          <x-summary-col label="Amount"
                         :value="'₱'.number_format($charge->amount,2)"
                         class="text-success" />
        </div>
      </div>
    </div>

    {{-- DISPUTE FORM --}}
    <div class="card mb-4">
      <div class="card-body">
        <h6 class="mb-3 text-secondary"><i class="fa fa-comment-dots me-1"></i> Submit a Review Request</h6>

        <form method="POST"
              action="{{ route('patient.disputes.store') }}"
              enctype="multipart/form-data">
          @csrf

          {{-- hidden --}}
          <input type="hidden" name="bill_item_id" value="{{ $charge->billing_item_id }}">

          {{-- reason code --}}
          <div class="mb-3">
            <label class="form-label">Reason for Dispute</label>
            <select name="reason_code"
                    class="form-select @error('reason_code') is-invalid @enderror"
                    required>
              <option value="">🔍 Select reason…</option>
              @foreach([
                'not_received' => '❌ Service not received',
                'coding_error' => '🔢 Incorrect code',
                'duplicate'    => '📄 Duplicate',
                'wrong_qty'    => '🔁 Wrong quantity',
                'cancelled'    => '🚫 Cancelled service',
                'other'        => '📝 Others',
              ] as $k=>$v)
                <option value="{{ $k }}" @selected(old('reason_code')===$k)>
                  {{ $v }}
                </option>
              @endforeach
            </select>
            @error('reason_code')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          {{-- explanation --}}
          <div class="mb-3">
            <label class="form-label">Explanation</label>
            <textarea name="reason"
                      rows="4"
                      class="form-control @error('reason') is-invalid @enderror"
                      required
                      placeholder="Explain why you are disputing this charge…">{{ old('reason') }}</textarea>
            @error('reason')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          {{-- file upload --}}
          <div class="mb-3">
            <label class="form-label">Upload Evidence (Optional)</label>
            <input type="file"
                   name="documents[]"
                   multiple
                   class="form-control @error('documents.*') is-invalid @enderror"
                   accept="image/*,.pdf">
            @error('documents.*')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          {{-- submit --}}
          <div class="text-end">
            <button type="submit" class="btn btn-primary px-4">
              <i class="fa fa-paper-plane me-1"></i> Submit Request
            </button>
          </div>
        </form>
      </div>
    </div>

    {{-- back link --}}
    <div class="d-flex justify-content-start">
      <a href="{{ route('patient.billing') }}" class="btn btn-outline-secondary">
        <i class="fa-solid fa-arrow-left me-1"></i> Back to Billing
      </a>
    </div>

  </div>
</main>
@endsection
