@extends('layouts.pharmacy')

@section('content')
<div class="container">
    <h3>Dispense Prescription: {{ $charge->rx_number }}</h3>
    <form method="POST" action="{{ route('pharmacy.charges.partial-dispense', $charge) }}">
        @csrf
        <ul>
            @foreach($charge->items as $item)
                <li>
                    <label>
                        <input type="checkbox" name="items[]" value="{{ $item->id }}" checked>
                        {{ $item->service->service_name ?? 'N/A' }} ({{ $item->quantity }})
                    </label>
                </li>
            @endforeach
        </ul>
        <button class="btn btn-success">Dispense Selected</button>
    </form>
</div>
@endsection