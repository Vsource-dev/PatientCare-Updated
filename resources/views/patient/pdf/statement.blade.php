{{-- resources/views/patient/pdf/statement.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Billing Statement</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        h2 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 6px; }
        th { background: #f5f5f5; }
        .text-right { text-align: right; }
        .totals { margin-top: 20px; width: 100%; }
        .totals td { padding: 4px; }
        .label { width: 80%; text-align: right; }
    </style>
</head>
<body>

    <h2>Patient Billing Statement</h2>

    <p>
        <strong>Patient:</strong>
        {{ $patient->patient_first_name }} {{ $patient->patient_last_name }}
    </p>
    <p>
        <strong>Admission Date:</strong>
        {{ optional($admission->admission_date)->format('Y-m-d') }}
    </p>
    <p>
        <strong>Generated:</strong>
        {{ \Carbon\Carbon::now()->format('Y-m-d') }}
    </p>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Ref No.</th>
                <th>Description</th>
                <th>Provider</th>
                <th class="text-right">Amount</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
            <tr>
                <td>{{ $item['date'] }}</td>
                <td>{{ $item['ref_no'] }}</td>
                <td>{{ $item['description'] }}</td>
                <td>{{ $item['provider'] }}</td>
                <td class="text-right">₱{{ number_format($item['amount'],2) }}</td>
                <td>{{ ucfirst($item['status']) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals">
        <tr>
            <td class="label"><strong>Total:</strong></td>
            <td class="text-right">₱{{ number_format($totals['total'],2) }}</td>
        </tr>
        <tr>
            <td class="label"><strong>Balance:</strong></td>
            <td class="text-right">₱{{ number_format($totals['balance'],2) }}</td>
        </tr>
        <tr>
            <td class="label"><strong>Discount:</strong></td>
            <td class="text-right">₱{{ number_format($totals['discount'],2) }}</td>
        </tr>
    </table>

</body>
</html>
