<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        .header { margin-bottom: 30px; border-bottom: 2px solid #eee; padding-bottom: 20px; }
        .logo { float: left; max-height: 60px; }
        .company-info { float: right; text-align: right; }
        .clearfix::after { content: ""; clear: both; display: table; }
        
        .invoice-title { font-size: 24px; font-weight: bold; color: #2563eb; margin-top: 20px; }
        
        .bill-to { margin-top: 30px; margin-bottom: 30px; }
        .label { font-weight: bold; color: #777; font-size: 10px; text-transform: uppercase; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th { background-color: #f3f4f6; padding: 10px; text-align: left; font-weight: bold; border-bottom: 1px solid #ddd; }
        td { padding: 10px; border-bottom: 1px solid #eee; }
        .amount { text-align: right; }
        
        .totals { float: right; width: 40%; }
        .total-row { display: flex; justify-content: space-between; padding: 5px 0; }
        .grand-total { font-size: 16px; font-weight: bold; border-top: 2px solid #333; padding-top: 10px; margin-top: 5px; }
        
        .status-stamp { 
            position: absolute; top: 200px; right: 50px; 
            border: 3px solid; padding: 10px 20px; 
            font-size: 20px; font-weight: bold; text-transform: uppercase; 
            transform: rotate(-15deg); opacity: 0.3;
        }
        .status-paid { color: green; border-color: green; }
        .status-unpaid { color: red; border-color: red; }
        .status-partial { color: orange; border-color: orange; }
    </style>
</head>
<body>
    <div class="header clearfix">
        <div class="logo">
            <!-- <img src="path/to/logo.png" alt="Logo"> -->
            <h2>KIBIHAS</h2>
        </div>
        <div class="company-info">
            <strong>KIBIHAS Institute</strong><br>
            P.O. Box 123, Kibaha<br>
            Tanzania<br>
            finance@kibihas.ac.tz
        </div>
    </div>

    <div class="invoice-title">INVOICE</div>
    
    <div class="clearfix">
        <div style="float: left; width: 50%;">
            <div class="bill-to">
                <div class="label">Bill To:</div>
                <strong>{{ $student->first_name }} {{ $student->last_name }}</strong><br>
                Reg No: {{ $student->registration_number }}<br>
                {{ $student->email }}
            </div>
        </div>
        <div style="float: right; width: 40%;">
            <div class="bill-to">
                <div class="label">Invoice Details:</div>
                <strong>Number:</strong> {{ $invoice->invoice_number }}<br>
                <strong>Date:</strong> {{ $invoice->created_at->format('d M Y') }}<br>
                <strong>Due Date:</strong> {{ $invoice->due_date ? \Carbon\Carbon::parse($invoice->due_date)->format('d M Y') : 'Immediate' }}
            </div>
        </div>
    </div>

    <div class="status-stamp status-{{ strtolower($invoice->status) }}">
        {{ $invoice->status }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th class="amount">Amount (TZS)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $item)
                <tr>
                    <td>
                        {{ $item->feeItem->name ?? 'Fee Item' }}
                        @if($item->description) <br><small class="text-gray-500">{{ $item->description }}</small> @endif
                    </td>
                    <td class="amount">{{ number_format($item->amount, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="clearfix">
        <div class="totals">
            <div class="total-row">
                <span>Subtotal:</span>
                <span style="float: right;">{{ number_format($invoice->subtotal, 2) }}</span>
            </div>
            <div class="total-row">
                <span>Paid to Date:</span>
                <span style="float: right;">{{ number_format($invoice->total_paid, 2) }}</span>
            </div>
            <div class="total-row grand-total">
                <span>Balance Due:</span>
                <span style="float: right;">{{ number_format($invoice->balance, 2) }} TZS</span>
            </div>
        </div>
    </div>

    <div style="margin-top: 50px; font-size: 11px; color: #666; border-top: 1px solid #eee; padding-top: 20px;">
        <p><strong>Payment Instructions:</strong></p>
        <p>Please pay using Control Number provided in the student portal or at the finance office.</p>
        <p>Bank: NMB Bank | Account Name: KIBIHAS | Account No: 1234567890</p>
    </div>
</body>
</html>
