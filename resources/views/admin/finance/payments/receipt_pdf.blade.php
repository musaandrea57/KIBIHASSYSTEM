<!DOCTYPE html>
<html>
<head>
    <title>Receipt</title>
    <style>
        body { font-family: sans-serif; font-size: 14px; }
        .header { text-align: center; margin-bottom: 30px; }
        .logo { width: 80px; height: auto; margin-bottom: 10px; }
        .details { margin-bottom: 20px; width: 100%; }
        .details td { padding: 5px; vertical-align: top; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .table th, .table td { border: 1px solid #ddd; padding: 8px; }
        .table th { background-color: #f2f2f2; text-align: left; }
        .footer { margin-top: 50px; text-align: center; font-size: 12px; color: #666; }
        .watermark { position: fixed; top: 30%; left: 30%; opacity: 0.1; font-size: 50px; transform: rotate(-45deg); }
    </style>
</head>
<body>
    @if($payment->status === 'reversed')
    <div class="watermark">REVERSED</div>
    @endif

    <div class="header">
        <h2>KIBIHA COLLEGE</h2>
        <p>OFFICIAL RECEIPT</p>
    </div>

    <table class="details">
        <tr>
            <td width="50%">
                <strong>Receipt No:</strong> {{ $payment->receipt->receipt_number }}<br>
                <strong>Date:</strong> {{ $payment->payment_date->format('d M Y') }}<br>
                <strong>Payment Ref:</strong> {{ $payment->payment_reference }}
            </td>
            <td width="50%">
                <strong>Student:</strong> {{ $payment->student->first_name }} {{ $payment->student->last_name }}<br>
                <strong>Admission No:</strong> {{ $payment->student->admission_number }}<br>
                <strong>Program:</strong> {{ $payment->student->program->code ?? 'N/A' }}
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <strong>Payment Method:</strong> {{ ucfirst($payment->method) }}<br>
                <strong>Transaction Ref:</strong> {{ $payment->transaction_ref ?? 'N/A' }}
            </td>
        </tr>
    </table>

    <table class="table">
        <thead>
            <tr>
                <th>Description</th>
                <th style="text-align: right;">Amount Paid (TZS)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payment->allocations as $allocation)
            <tr>
                <td>{{ $allocation->invoiceItem->description }}</td>
                <td style="text-align: right;">{{ number_format($allocation->amount, 2) }}</td>
            </tr>
            @endforeach
            <tr>
                <td style="text-align: right; font-weight: bold;">Total Paid</td>
                <td style="text-align: right; font-weight: bold;">{{ number_format($payment->amount, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p>Received By: {{ $payment->receiver->name ?? 'System' }}</p>
        <p>Generated on {{ now()->format('d M Y H:i:s') }}</p>
        <p>This is a computer-generated receipt.</p>
    </div>
</body>
</html>
