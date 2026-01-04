<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt #{{ $receipt->receipt_number }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #333;
            line-height: 1.6;
        }
        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #eee;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #eee;
            padding-bottom: 20px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        .address {
            font-size: 12px;
            color: #7f8c8d;
        }
        .receipt-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .receipt-title {
            font-size: 28px;
            color: #2c3e50;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }
        .label {
            font-weight: bold;
            color: #7f8c8d;
            font-size: 12px;
            text-transform: uppercase;
        }
        .value {
            font-size: 16px;
            margin-bottom: 10px;
        }
        .amount-box {
            background-color: #f8f9fa;
            padding: 15px;
            text-align: center;
            border-radius: 5px;
            margin-bottom: 30px;
        }
        .amount-label {
            font-size: 14px;
            color: #7f8c8d;
        }
        .amount-value {
            font-size: 32px;
            font-weight: bold;
            color: #27ae60;
        }
        .footer {
            text-align: center;
            font-size: 12px;
            color: #95a5a6;
            margin-top: 50px;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 100px;
            color: rgba(0,0,0,0.03);
            z-index: -1;
            pointer-events: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="watermark">PAID</div>
        
        <div class="header">
            <div class="logo">KIBIHAS</div>
            <div class="address">
                Kigoma Ujiji College of Health and Allied Sciences<br>
                P.O. Box 123, Kigoma, Tanzania<br>
                Tel: +255 123 456 789 | Email: info@kibihas.ac.tz
            </div>
        </div>

        <div class="receipt-info">
            <div>
                <div class="label">Receipt Number</div>
                <div class="value">#{{ $receipt->receipt_number }}</div>
            </div>
            <div style="text-align: right;">
                <div class="label">Date Issued</div>
                <div class="value">{{ $receipt->issued_at->format('d M Y') }}</div>
            </div>
        </div>

        <div style="text-align: center; margin-bottom: 30px;">
            <div class="receipt-title">Payment Receipt</div>
        </div>

        <div class="details-grid">
            <div>
                <div class="label">Received From</div>
                <div class="value">
                    {{ $payment->student->first_name }} {{ $payment->student->last_name }}<br>
                    <span style="font-size: 14px; color: #666;">ID: {{ $payment->student->admission_number }}</span>
                </div>
            </div>
            <div>
                <div class="label">Payment Method</div>
                <div class="value">
                    {{ ucfirst($payment->method) }}<br>
                    <span style="font-size: 14px; color: #666;">Ref: {{ $payment->transaction_ref ?? 'N/A' }}</span>
                </div>
            </div>
        </div>

        <div class="amount-box">
            <div class="amount-label">Amount Received</div>
            <div class="amount-value">{{ number_format($payment->amount, 2) }} TZS</div>
        </div>

        <div class="details-grid">
            <div>
                <div class="label">Applied To Invoice</div>
                <div class="value">#{{ $payment->invoice->invoice_number }}</div>
            </div>
            <div>
                <div class="label">Remaining Balance</div>
                <div class="value">{{ number_format($payment->invoice->balance, 2) }} TZS</div>
            </div>
        </div>

        <div class="footer">
            <p>This is a computer-generated receipt and does not require a physical signature.</p>
            <p>Thank you for your payment.</p>
        </div>
    </div>
    
    <script>
        window.print();
    </script>
</body>
</html>
