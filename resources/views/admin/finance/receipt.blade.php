<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Receipt #{{ $payment->receipt->receipt_number ?? $payment->payment_reference }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .logo { width: 80px; height: auto; }
        .title { font-size: 18px; font-weight: bold; margin: 5px 0; }
        .subtitle { font-size: 14px; color: #555; }
        .details-box { border: 1px solid #ccc; padding: 10px; margin-bottom: 20px; }
        .row { display: flex; justify-content: space-between; margin-bottom: 5px; }
        .label { font-weight: bold; width: 150px; display: inline-block; }
        .amount { font-size: 16px; font-weight: bold; }
        .footer { text-align: center; margin-top: 30px; font-size: 10px; color: #777; border-top: 1px solid #eee; padding-top: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">KIBOSHO INSTITUTE OF HEALTH AND ALLIED SCIENCES</div>
        <div class="subtitle">P.O. Box 123, Moshi, Kilimanjaro | Tel: +255 123 456 789</div>
        <div class="title" style="margin-top: 15px; text-decoration: underline;">PAYMENT RECEIPT</div>
    </div>

    <div class="details-box">
        <div><span class="label">Receipt No:</span> {{ $payment->receipt->receipt_number ?? 'N/A' }}</div>
        <div><span class="label">Payment Ref:</span> {{ $payment->payment_reference }}</div>
        <div><span class="label">Date:</span> {{ $payment->payment_date->format('d M Y') }}</div>
        <div><span class="label">Received From:</span> {{ $payment->student->first_name }} {{ $payment->student->last_name }}</div>
        <div><span class="label">Admission No:</span> {{ $payment->student->admission_number }}</div>
        <div><span class="label">Payment Method:</span> {{ ucfirst(str_replace('_', ' ', $payment->method)) }}</div>
        @if($payment->transaction_ref)
        <div><span class="label">Reference:</span> {{ $payment->transaction_ref }}</div>
        @endif
    </div>

    <div style="margin-bottom: 20px;">
        <span class="label">Amount Paid:</span>
        <span class="amount">TZS {{ number_format($payment->amount, 2) }}</span>
    </div>

    @if($payment->allocations->count() > 0)
        <h3>Payment Allocation</h3>
        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Amount Allocated</th>
                </tr>
            </thead>
            <tbody>
                @foreach($payment->allocations as $allocation)
                <tr>
                    <td>{{ $allocation->invoiceItem->feeItem->name ?? $allocation->invoiceItem->description }}</td>
                    <td>{{ number_format($allocation->amount, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div style="margin-top: 40px;">
        <div>Received By: __________________________</div>
        <div style="margin-top: 5px;">{{ $payment->receivedBy->name ?? 'System' }}</div>
    </div>

    <div class="footer">
        <p>This is a computer-generated receipt. Signature is optional.</p>
        <p>&copy; {{ date('Y') }} KIBIHAS. All rights reserved.</p>
    </div>
</body>
</html>