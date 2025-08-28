<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statement of Account</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 12px; color: #333; }
        .container { width: 100%; margin: 0 auto; }
        .header, .footer { text-align: center; }
        .header h1 { margin: 0; font-size: 24px; }
        .header p { margin: 5px 0; }
        .patient-info { margin: 20px 0; border-collapse: collapse; width: 100%; }
        .patient-info td { padding: 5px; }
        .charges-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .charges-table th, .charges-table td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        .charges-table th { background-color: #f2f2f2; }
        .text-right { text-align: right; }
        .summary { margin-top: 20px; width: 40%; float: right; }
        .summary td { padding: 5px; }
        .summary .total { font-weight: bold; border-top: 2px solid #333; }
    </style>
</head>
<body>
    <div class="container">
   

        <table class="patient-info">
            <tr>
                <td><strong>Patient Name:</strong> {{ $patient->patient_first_name }} {{ $patient->patient_last_name }}</td>
                <td><strong>Patient ID:</strong> {{ $patient->patient_id }}</td>
            </tr>
            <tr>
                <td><strong>Admission Date:</strong> {{ optional($patient->admissionDetail->admission_date)->format('F d, Y') ?? 'N/A' }}</td>
                <td><strong>Statement Date:</strong> {{ now()->format('F d, Y') }}</td>
            </tr>
        </table>

        <table class="charges-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Description</th>
                    <th class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                @forelse($all_charges as $charge)
                    <tr>
                        <td>{{ $charge->billing_date->format('Y-m-d') }}</td>
                        <td>{{ $charge->service->service_name ?? 'N/A' }} ({{ $charge->quantity ?? 1 }}x)</td>
                        <td class="text-right">₱{{ number_format($charge->amount, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" style="text-align: center;">No charges found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <table class="summary">
            <tr>
                <td>Total Charges:</td>
                <td class="text-right">₱{{ number_format($totals['charges'], 2) }}</td>
            </tr>
            <tr>
                <td>Total Payments/Deposits:</td>
                <td class="text-right">₱{{ number_format($totals['deposits'], 2) }}</td>
            </tr>
            <tr class="total">
                <td>Balance Due:</td>
                <td class="text-right">₱{{ number_format($totals['balance'], 2) }}</td>
            </tr>
        </table>
    </div>
</body>
</html>