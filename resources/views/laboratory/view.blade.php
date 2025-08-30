{{-- filepath: resources/views/laboratory/view.blade.php --}}
@extends('layouts.laboratory')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h3 class="fw-bold">🧪 Lab Order Details</h3>
        <a href="{{ route('laboratory.history') }}" class="btn btn-light btn-sm mb-3">
            <i class="fa fa-arrow-left"></i> Back to History
        </a>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="mb-3">Order #{{ str_pad($assignment->assignment_id, 6, '0', STR_PAD_LEFT) }}</h5>
            <ul class="list-group list-group-flush">
                <li class="list-group-item"><strong>Patient:</strong> {{ $assignment->patient->full_name ?? $assignment->patient->patient_first_name . ' ' . $assignment->patient->patient_last_name }}</li>
                <li class="list-group-item"><strong>Physician:</strong> {{ $assignment->doctor->doctor_name ?? '—' }}</li>
                <li class="list-group-item"><strong>Service:</strong> {{ $assignment->service->service_name ?? 'N/A' }}</li>
                <li class="list-group-item"><strong>Status:</strong> {{ ucfirst($assignment->service_status) }}</li>
                <li class="list-group-item"><strong>Ordered At:</strong> {{ $assignment->created_at->format('M d, Y h:i A') }}</li>
                <li class="list-group-item"><strong>Completed At:</strong> {{ $assignment->updated_at->format('M d, Y h:i A') }}</li>
                @if($assignment->notes)
                <li class="list-group-item"><strong>Notes:</strong> {{ $assignment->notes }}</li>
                @endif
            </ul>
        </div>
    </div>
</div>
@endsection