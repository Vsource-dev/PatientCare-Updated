{{--resources/views/patient/billing/charge/audit.blade.php--}}

@extends('layouts.billing')

@section('content')
<div class="container-fluid p-4">
  <h4 class="mb-4">Audit Trail for Charge #{{ $item->billing_item_id }}</h4>

  @if($item->logs->isEmpty())
    <div class="alert alert-secondary">
      No audit records found for this charge.
    </div>
  @else
    <ul class="timeline list-unstyled">
      @foreach($item->logs as $log)
        <li class="timeline-item mb-4 pb-2 border-bottom">
          <div class="d-flex align-items-center mb-1">
            <i class="fa-solid {{ $log->icon }} fa-lg text-primary me-3"></i>
            <div>
              <strong>{{ $log->action }}</strong>
              <div class="small text-muted">
                {{ $log->created_at->format('Y-m-d H:i') }} by {{ $log->actor }}
              </div>
            </div>
          </div>
          <p class="mb-0">{{ $log->message }}</p>
        </li>
      @endforeach
    </ul>
  @endif

  <a href="{{ url()->previous() }}" class="btn btn-secondary mt-3">
    <i class="fa-solid fa-arrow-left me-1"></i> Back
  </a>
</div>
@endsection
