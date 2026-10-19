<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cash Collection Receipt</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 14px;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            border: 1px solid #eee;
            padding: 30px;
        }
        .header {
            display: table;
            width: 100%;
            margin-bottom: 30px;
            border-bottom: 2px solid #ed1c24;
            padding-bottom: 10px;
        }
        .header-left {
            display: table-cell;
            width: 50%;
            vertical-align: middle;
        }
        .header-right {
            display: table-cell;
            width: 50%;
            text-align: right;
            vertical-align: middle;
        }
        .logo {
            max-width: 150px;
            height: auto;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #ed1c24;
            margin-bottom: 5px;
        }
        .receipt-title {
            font-size: 20px;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 20px;
            color: #555;
        }
        .details-section {
            width: 100%;
            margin-bottom: 20px;
            display: table;
        }
        .details-box {
            display: table-cell;
            width: 50%;
        }
        .details-box h3 {
            font-size: 16px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
            margin-bottom: 10px;
            color: #ed1c24;
        }
        .details-item {
            margin-bottom: 5px;
        }
        .details-label {
            font-weight: bold;
            display: inline-block;
            width: 100px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            margin-bottom: 30px;
        }
        .table th {
            background-color: #f8f8f8;
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        .table td {
            border: 1px solid #ddd;
            padding: 10px;
        }
        .amount-words {
            font-style: italic;
            margin-bottom: 20px;
        }
        .footer {
            margin-top: 50px;
            display: table;
            width: 100%;
        }
        .signature-box {
            display: table-cell;
            width: 50%;
            text-align: center;
        }
        .signature-line {
            border-top: 1px solid #333;
            width: 150px;
            margin: 50px auto 10px;
        }
        .footer-note {
            font-size: 12px;
            color: #777;
            text-align: center;
            margin-top: 40px;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }
        /* Utility for currency symbol */
        .currency {
            font-family: 'DejaVu Sans', sans-serif;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="header-left">
                @php $logo = get_option('logo'); @endphp
                @if($logo)
                    <img src="{{ public_path('uploads/media/' . $logo) }}" class="logo" alt="Logo">
                @else
                    <div class="company-name">{{ get_option('site_title', 'BEWINS') }}</div>
                @endif
            </div>
            <div class="header-right">
                <div class="receipt-title">Payment Receipt</div>
                <div><strong>Receipt No:</strong> #{{ str_pad($collection->id, 6, '0', STR_PAD_LEFT) }}</div>
                <div><strong>Date:</strong> {{ date('d M Y', strtotime($collection->admin_received_at)) }}</div>
            </div>
        </div>

        <div class="details-section">
            <div class="details-box" style="padding-right: 20px;">
                <h3>Investor Details</h3>
                <div class="details-item"><span class="details-label">Name:</span> {{ $collection->investor_name }}</div>
                <div class="details-item"><span class="details-label">Mobile:</span> {{ $collection->investor_mobile }}</div>
                <div class="details-item"><span class="details-label">Address:</span> {{ $collection->investor_address }}</div>
            </div>
            <div class="details-box">
                <h3>Collection Info</h3>
                <div class="details-item"><span class="details-label">Employee:</span> {{ $collection->employee_name }}</div>
                <div class="details-item"><span class="details-label">Contact:</span> {{ $collection->employee_mobile }}</div>
                <div class="details-item"><span class="details-label">Status:</span> Verified by Admin</div>
            </div>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th style="text-align: right;">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <strong>Investment Cash Collection</strong><br>
                        <small>Collection Date: {{ date('d M Y', strtotime($collection->paid_date)) }}</small><br>
                        <small>Payment Mode: Cash</small>
                    </td>
                    <td style="text-align: right; vertical-align: middle; font-size: 18px; font-weight: bold;">
                        <span class="currency">{{ '₹' }}</span> {{ number_format($collection->total_amount, 2) }}
                    </td>
                </tr>
            </tbody>
        </table>

        @php
            // $f = new \NumberFormatter("en", \NumberFormatter::SPELLOUT);
            $amountInWords = ucwords($collection->total_amount);
        @endphp
        <div class="amount-words">
            <strong>Amount in Words:</strong> {{ $amountInWords }} {{ $collection->currency_name }} Only
        </div>

        <div class="footer">
            <div class="signature-box">
                <div class="signature-line"></div>
                <div>Investor Signature</div>
            </div>
            <div class="signature-box">
                <div class="signature-line"></div>
                <div>Authorized Signatory</div>
            </div>
        </div>

        <div class="footer-note">
            {{ get_option('address') }}<br>
            Email: {{ get_option('email') }} | Phone: {{ get_option('phone') }}<br>
            This is a computer generated receipt and does not require a physical seal.
        </div>
    </div>
</body>
</html>
