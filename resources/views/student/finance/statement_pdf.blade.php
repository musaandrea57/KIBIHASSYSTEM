<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Financial Statement</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; }
        .logo { max-height: 80px; margin-bottom: 10px; }
        .title { font-size: 18px; font-weight: bold; text-transform: uppercase; margin-bottom: 5px; }
        .subtitle { font-size: 14px; color: #666; }
        
        .info-grid { display: table; width: 100%; margin-bottom: 20px; }
        .info-col { display: table-cell; width: 50%; vertical-align: top; }
        .label { font-weight: bold; color: #555; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { padding: 8px; border-bottom: 1px solid #ddd; text-align: left; }
        th { background-color: #f8f9fa; font-weight: bold; text-transform: uppercase; font-size: 10px; }
        .amount { text-align: right; }
        .total-row td { border-top: 2px solid #333; font-weight: bold; font-size: 14px; }
        
        .footer { position: fixed; bottom: 0; left: 0; right: 0; text-align: center; font-size: 10px; color: #999; border-top: 1px solid #eee; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">KIBIHAS Institute</div>
        <div class="subtitle">Student Financial Statement</div>
        <div>Generated on: {{ now()->format('d M Y H:i') }}</div>
    </div>

    <div class="info-grid">
        <div class="info-col">
            <div class="label">Student Details:</div>
            <div>{{ $student->first_name }} {{ $student->last_name }}</div>
            <div>Reg No: {{ $student->registration_number }}</div>
            <div>Program: {{ $student->program->name ?? 'N/A' }}</div>
        </div>
        <div class="info-col" style="text-align: right;">
            <div class="label">Summary:</div>
            <div>Total Invoiced: {{ number_format($totals['invoiced'], 2) }} TZS</div>
            <div>Total Paid: {{ number_format($totals['paid'], 2) }} TZS</div>
            <div>Outstanding: {{ number_format($totals['outstanding'], 2) }} TZS</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Description</th>
                <th>Reference</th>
                <th class="amount">Debit (Invoiced)</th>
                <th class="amount">Credit (Paid)</th>
                <th class="amount">Balance</th>
            </tr>
        </thead>
        <tbody>
            @php $runningBalance = 0; @endphp
            @foreach($transactions as $txn)
                @php
                    if($txn['type'] == 'invoice') {
                        $runningBalance += $txn['amount'];
                    } else {
                        $runningBalance -= $txn['amount'];
                    }
                @endphp
                <tr>
                    <td>{{ $txn['date']->format('d M Y') }}</td>
                    <td>{{ $txn['description'] }}</td>
                    <td>{{ $txn['reference'] }}</td>
                    <td class="amount">{{ $txn['type'] == 'invoice' ? number_format($txn['amount'], 2) : '-' }}</td>
                    <td class="amount">{{ $txn['type'] == 'payment' ? number_format($txn['amount'], 2) : '-' }}</td>
                    <td class="amount">{{ number_format($runningBalance, 2) }}</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="5" style="text-align: right;">Closing Balance</td>
                <td class="amount">{{ number_format($runningBalance, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        This is a computer-generated document. No signature is required.
    </div>
</body>
</html>
