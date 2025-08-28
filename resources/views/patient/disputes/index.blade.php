{{-- resources/views/patient/disputes/index.blade.php --}}
@extends('layouts.patients')

@section('content')
<main class="p-4" style="margin-left: 240px; background-color: #f9fafe;">
<div class="container-fluid min-vh-100 d-flex flex-column">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="hdng mb-0">My Dispute Requests</h4>
            <p class="text-muted mb-0">Here is the history of your submitted billing disputes.</p>
        </div>
        <a href="{{ route('patient.billing') }}" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Billing
        </a>
    </div>

    @if(session('success'))
      <div class="alert alert-success">
        <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
      </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Date Filed</th>
                            <th>Item Disputed</th>
                            <th>Reason</th>
                            <th class="text-end">Amount</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
    @forelse($disputes as $dispute)
        <tr>
            <td>{{ \Carbon\Carbon::parse($dispute->datetime)->format('M d, Y') }}</td>
            
            {{-- ✅ FIX: Use the new 'disputable' relationship --}}
            <td>{{ $dispute->disputable->service->service_name ?? 'Item not found' }}</td>

            <td>{{ Str::limit($dispute->reason, 50) }}</td>

            {{-- ✅ FIX: Check for 'amount' or 'total' property for different item types --}}
            <td class="text-end">₱{{ number_format($dispute->disputable->amount ?? $dispute->disputable->total ?? 0, 2) }}</td>

            <td class="text-center">
                @php
                    $badge = [
                        'pending'  => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                    ][$dispute->status] ?? 'secondary';
                @endphp
                <span class="badge bg-{{$badge}} text-capitalize">{{ $dispute->status }}</span>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="5" class="text-center text-muted p-4">You have not filed any disputes.</td>
        </tr>
    @endforelse
</tbody>
                </table>
            </div>
             @if($disputes->hasPages())
                <div class="p-3">
                    {{ $disputes->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
</main>
@endsection