<!-- resources/views/billing/index.blade.php -->
@extends('layouts.billing')

@section('content')
<div class="container-fluid min-vh-100 p-4 d-flex flex-column" style="background-color: #fafafa;">

    <h4 class="hdng">Patient Billing Management</h4>
    <p class="text-muted mb-5">Manage patient billing records and disputes.</p>

    <!-- Display total revenue -->
    <div>
        <h5>Total Revenue: ₱{{ number_format($totals['total'], 2) }}</h5>
        <h5>Outstanding Balance: ₱{{ number_format($totals['balance'], 2) }}</h5>
    </div>

    <!-- Paginated list of billing items -->
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead>
                <tr>
                    <th>Ref No</th>
                    <th>Description</th>
                    <th>Amount</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                    <tr>
                        <td>{{ $item->ref_no }}</td>
                        <td>{{ $item->description }}</td>
                        <td>₱{{ number_format($item->amount, 2) }}</td>
                        <td>{{ $item->status }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="d-flex justify-content-end">
            {{ $items->links() }}
        </div>
    </div>

</div>
@endsection
