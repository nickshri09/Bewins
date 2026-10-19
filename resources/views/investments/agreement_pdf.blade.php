<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>BCS Investment Agreement</title>
    <style>
        @page { margin: 34px 42px; }
        body { color: #202938; font: 10px DejaVu Sans, sans-serif; line-height: 1.5; }
        h1 { color: #17366f; font-size: 19px; margin: 0 0 3px; }
        h2 { color: #17366f; font-size: 15px; margin: 0 0 18px; }
        h3 { border-bottom: 1px solid #d8deea; color: #263c65; font-size: 11px; margin: 17px 0 7px; padding-bottom: 3px; }
        .company { border-bottom: 3px solid #274a91; margin-bottom: 14px; padding-bottom: 10px; }
        .muted { color: #586579; }
        .meta { margin: 10px 0 14px; }
        .meta span { display: inline-block; margin-right: 22px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #cbd3df; padding: 6px 8px; text-align: left; vertical-align: top; }
        th { background: #edf2fb; width: 34%; }
        ol { margin: 5px 0; padding-left: 20px; }
        li { margin: 3px 0; }
        .signature { margin-top: 27px; width: 100%; }
        .signature td { border: 0; padding: 8px 18px 8px 0; width: 50%; }
        .line { border-bottom: 1px solid #637083; height: 28px; }
        .footer { border-top: 1px solid #d8deea; color: #667085; font-size: 8px; margin-top: 18px; padding-top: 6px; }
    </style>
</head>
<body>
    <div class="company">
        <h1>BEWINS SERVICES PRIVATE LIMITED</h1>
        <h2>BCS INVESTMENT AGREEMENT</h2>
        @if($company_address)<div>{{ $company_address }}</div>@endif
        <div class="muted">https://bewins.in</div>
    </div>

    <div class="meta">
        <span><strong>Agreement ID:</strong> {{ $agreement_id }}</span>
        <span><strong>BCS Investment ID:</strong> {{ $investment_code }}</span>
        <span><strong>Agreement Date:</strong> {{ $agreement_date }}</span>
    </div>

    <h3>1. Company Details</h3>
    <table>
        <tr><th>Company Name</th><td>Bewins Services Private Limited</td></tr>
        @if($company_address)<tr><th>Registered Office</th><td>{{ $company_address }}</td></tr>@endif
        <tr><th>Website</th><td>https://bewins.in</td></tr>
    </table>

    <h3>2. Customer Details</h3>
    <table>
        <tr><th>Customer Name</th><td>{{ $customer_name }}</td></tr>
        <tr><th>Customer ID</th><td>{{ $customer_id }}</td></tr>
        <tr><th>Registered Email</th><td>{{ $customer_email }}</td></tr>
        @if($customer_address)<tr><th>Customer Address</th><td>{{ $customer_address }}</td></tr>@endif
    </table>

    <h3>3. Investment Details</h3>
    <table>
        <tr><th>Investment Product</th><td>Bewin Collateral Security (BCS)</td></tr>
        <tr><th>Investment ID</th><td>{{ $investment_code }}</td></tr>
        <tr><th>Investment Amount</th><td>₹{{ $investment_amount }}</td></tr>
        <tr><th>Contribution Type</th><td>{{ $contribution_type }}</td></tr>
        <tr><th>Investment Date</th><td>{{ $investment_date }}</td></tr>
        <tr><th>Applicable TDS</th><td>₹{{ $applicable_tds }}</td></tr>
        <tr><th>Applicable Charges</th><td>₹{{ $applicable_charges }}</td></tr>
        <tr><th>Total Amount</th><td>₹{{ $total_amount }}</td></tr>
        <tr><th>Investment Status</th><td>{{ $investment_status }}</td></tr>
    </table>

    <h3>4. Purpose of the Agreement</h3>
    <p>This agreement records the investment transaction made by the customer through the Bewins platform under the Bewin Collateral Security (BCS) product. The investment details stated in this document correspond to the approved transaction record maintained by the Company.</p>

    <h3>5. Terms and Conditions</h3>
    <ol>
        <li>The investment amount, investment date, and other transaction details are based on the Company's approved records.</li>
        <li>Any applicable returns, benefits, maturity amounts, or withdrawal conditions are governed by the final approved product terms and customer disclosures.</li>
        <li>Withdrawals, where applicable, are subject to eligibility criteria, verification, compliance requirements, and applicable transaction rules.</li>
        <li>Applicable taxes, deductions, and charges are calculated according to relevant rules and approved product terms.</li>
        <li>The customer should contact the Company's official support channel if any information in this agreement appears incorrect.</li>
        <li>This agreement must be read together with the final product terms and disclosures approved by the Company.</li>
    </ol>

    <h3>6. Electronic Agreement and Delivery</h3>
    <p>This agreement is generated electronically by the Bewins platform after the investment is recorded as successfully completed, according to the configured workflow. A copy is sent to the customer's registered email address and made available in the customer's investment account.</p>

    <h3>7. Customer Acknowledgement</h3>
    <p>I acknowledge that the investment details stated in this document should be reviewed along with the applicable product terms and disclosures.</p>
    <p><strong>Customer Name:</strong> {{ $customer_name }}</p>
    <table class="signature">
        <tr>
            <td>Customer Signature, if required:<div class="line"></div></td>
            <td>Date: {{ $agreement_date }}<div class="line"></div></td>
        </tr>
    </table>

    <h3>8. Company Verification</h3>
    <table>
        <tr><th>Agreement ID</th><td>{{ $agreement_id }}</td></tr>
        <tr><th>BCS Investment ID</th><td>{{ $investment_code }}</td></tr>
        @if($support_email)<tr><th>Official Support Email</th><td>{{ $support_email }}</td></tr>@endif
    </table>

    <div class="footer">Document generated electronically by the Bewins platform. This document does not independently establish guaranteed returns or payout obligations.</div>
</body>
</html>
