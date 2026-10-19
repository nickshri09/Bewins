<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>BCS Closure Certificate</title>
    <style>
        @page { margin: 42px; }
        body { font-family: DejaVu Sans, sans-serif; color: #24334a; font-size: 12px; }
        .top { border-bottom: 4px solid #2457c5; padding-bottom: 16px; }
        .brand { color: #2457c5; font-size: 22px; font-weight: bold; }
        .subtitle { color: #6c7788; margin-top: 4px; }
        .title { margin: 44px 0 10px; text-align: center; color: #1d3e82; font-size: 24px; font-weight: bold; }
        .intro { text-align: center; color: #606d7c; line-height: 1.7; margin-bottom: 28px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        td { border: 1px solid #dce2ec; padding: 12px; }
        td:first-child { width: 36%; color: #57657a; font-weight: bold; background: #f4f7fc; }
        .notice { margin-top: 24px; border-left: 4px solid #2457c5; background: #f4f7fc; padding: 14px; line-height: 1.7; }
        .sign { margin-top: 70px; text-align: right; color: #566276; }
        .footer { margin-top: 46px; border-top: 1px solid #dce2ec; padding-top: 10px; color: #7b8593; font-size: 10px; text-align: center; }
    </style>
</head>
<body>
    <div class="top">
        <div class="brand">Bewin Services Private Limited</div>
        <div class="subtitle">Bewin Collateral Security (BCS)</div>
    </div>

    <div class="title">BCS Closure Certificate</div>
    <div class="intro">
        This certificate confirms that the following Bewin Collateral Security record<br>
        has been marked closed in our system.
    </div>

    <table>
        <tr><td>Certificate / BCS Reference</td><td>{{ $investment->investment_code ?? ('BCS-' . $investment->id) }}</td></tr>
        <tr><td>Client Name</td><td>{{ $member->name }}</td></tr>
        <tr><td>Client Number</td><td>{{ $member->member_no ?? '—' }}</td></tr>
        <tr><td>Client Email</td><td>{{ $member->email ?? '—' }}</td></tr>
        <tr><td>BCS Type</td><td>{{ ucwords(str_replace('_', ' ', (string) $investment->investment_type)) }}</td></tr>
        <tr><td>Contribution Type</td><td>{{ ucfirst((string) $investment->contribution_type) }}</td></tr>
        <tr><td>Principal Amount</td><td>{{ number_format((float) $investment->investment_amount, 2) }} {{ $investment->currency->name ?? '' }}</td></tr>
        <tr><td>Start Date</td><td>{{ $investment->start_date?->format('d M Y') ?? '—' }}</td></tr>
        <tr><td>Closure Date</td><td>{{ $investment->closed_at?->format('d M Y') ?? now()->format('d M Y') }}</td></tr>
    </table>

    <div class="notice">
        This document records the closure status of the BCS entry identified above. Please retain it for your records.
        For questions, contact Bewin Services Private Limited through your registered service channel.
    </div>

    <div class="sign">Authorised Signatory<br>Bewin Services Private Limited</div>
    <div class="footer">Generated electronically on {{ now()->format('d M Y, h:i A') }}</div>
</body>
</html>
