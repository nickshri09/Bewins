<!DOCTYPE html>
<html>
<head>
    <title>{{ _lang('Wallet Transaction History') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f5f5f5;
        }
        .credit {
            color: green;
        }
        .debit {
            color: red;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>{{ _lang('Wallet Transaction History') }}</h2>
        <p>{{ _lang('Generated on') }}: {{ Carbon\Carbon::now()->format('d M, Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>{{ _lang('Date') }}</th>
                <th>{{ _lang('User') }}</th>
                <th>{{ _lang('Amount') }}</th>
                <th>{{ _lang('Type') }}</th>
                <th>{{ _lang('Note') }}</th>
                <th>{{ _lang('Added By') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $transaction)
            <tr>
                <td>{{ $transaction->created_at->setTimezone('Asia/Kolkata')->format('d M, Y H:i') }}</td>
                <td>{{ $transaction->user->name ?? 'Deleted User' }}</td>
                <td class="{{ $transaction->type == 'credit' ? 'credit' : 'debit' }}">
                    {{ $transaction->type == 'credit' ? '+' : '-' }}₹ {{ number_format($transaction->amount, 2) }}
                </td>
                <td>{{ _lang(ucfirst($transaction->type)) }}</td>
                <td>{{ $transaction->note }}</td>
                <td>{{ $transaction->createdBy->name ?? 'Deleted User' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html> 