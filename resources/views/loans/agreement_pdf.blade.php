<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Loan Agreement</title>
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
        th { background: #edf2fb; width: 36%; }
        ol { margin: 5px 0; padding-left: 20px; }
        li { margin: 3px 0; }
        .signature { margin-top: 24px; width: 100%; }
        .signature td { border: 0; padding: 8px 18px 8px 0; width: 50%; }
        .line { border-bottom: 1px solid #637083; height: 28px; }
        .notice { background: #fff8e6; border: 1px solid #e8d49b; margin-top: 18px; padding: 9px; }
        .footer { border-top: 1px solid #d8deea; color: #667085; font-size: 8px; margin-top: 18px; padding-top: 6px; }
    </style>
</head>
<body>
    <div class="company">
        <h1>BEWINS SERVICES PRIVATE LIMITED</h1>
        <h2>LOAN AGREEMENT</h2>
        <div class="muted">https://bewins.in</div>
    </div>

    <div class="meta">
        <span><strong>Agreement ID:</strong> {{ $agreement_id }}</span>
        <span><strong>Loan ID:</strong> {{ $loan_id }}</span>
        <span><strong>Agreement Date:</strong> {{ $agreement_date }}</span>
    </div>

    <h3>1. Lender and Borrower Details</h3>
    <table>
        <tr><th>Lender / Financing Partner</th><td>{{ $lender_name }}</td></tr>
        <tr><th>Lender Address</th><td>{{ $lender_address }}</td></tr>
        <tr><th>Platform</th><td>{{ $platform_name }}</td></tr>
        <tr><th>Borrower Name</th><td>{{ $borrower_name }}</td></tr>
        <tr><th>Customer ID</th><td>{{ $customer_id }}</td></tr>
        <tr><th>Registered Email</th><td>{{ $borrower_email }}</td></tr>
        <tr><th>Borrower Address</th><td>{{ $borrower_address }}</td></tr>
    </table>

    <h3>2. Loan Details</h3>
    <table>
        <tr><th>Loan ID</th><td>{{ $loan_id }}</td></tr>
        <tr><th>Loan Type</th><td>{{ $loan_type }}</td></tr>
        <tr><th>Loan Purpose</th><td>{{ $loan_purpose }}</td></tr>
        <tr><th>Sanctioned Loan Amount</th><td>₹{{ $sanctioned_amount }}</td></tr>
        <tr><th>Amount to be Disbursed</th><td>{{ is_numeric($disbursement_amount) ? '₹'.$disbursement_amount : $disbursement_amount }}</td></tr>
        <tr><th>Interest Rate</th><td>{{ $interest_rate }}% per year</td></tr>
        <tr><th>Processing Fee</th><td>{{ is_numeric($processing_fee) ? '₹'.$processing_fee : $processing_fee }}</td></tr>
        <tr><th>Applicable Taxes</th><td>{{ is_numeric($applicable_taxes) ? '₹'.$applicable_taxes : $applicable_taxes }}</td></tr>
        <tr><th>Repayment Frequency</th><td>{{ $repayment_frequency }}</td></tr>
        <tr><th>Number of Instalments</th><td>{{ $installment_count }}</td></tr>
        <tr><th>Instalment Amount</th><td>{{ is_numeric($monthly_emi) ? '₹'.$monthly_emi : $monthly_emi }}</td></tr>
        <tr><th>First Payment Date</th><td>{{ $first_payment_date }}</td></tr>
        <tr><th>Final Payment Date</th><td>{{ $final_payment_date }}</td></tr>
        <tr><th>Security / Collateral</th><td>{{ $collateral_details }}</td></tr>
        <tr><th>Loan Status</th><td>{{ $loan_status }}</td></tr>
    </table>

    <h3>3. Purpose of the Agreement</h3>
    <p>This document records the loan details of the borrower identified above. The final loan terms shall be governed by the agreement and disclosures approved by the authorized lender.</p>

    <h3>4. Repayment Terms</h3>
    <ol>
        <li>The borrower shall repay the loan according to the lender-approved repayment schedule.</li>
        <li>Interest, processing fees, taxes, and other applicable charges shall be as stated in the final approved loan documents.</li>
        <li>Late-payment charges, prepayment conditions, and default consequences must be specified in the final agreement.</li>
        <li>The borrower should retain payment receipts and transaction confirmations.</li>
    </ol>

    <h3>5. Loan Approval and Disbursement</h3>
    <p>Loan approval and actual disbursement are separate stages. Disbursement is subject to the lender's approval, required verification, completion of documentation, and applicable conditions. The loan amount and disbursement date must match verified lender or bank records.</p>

    <h3>6. Electronic Agreement Generation</h3>
    <p>This agreement is generated electronically by the Bewins platform after the loan is approved. A copy is sent to the borrower's registered email address and made available in the loan account documents.</p>

    <h3>7. Borrower Acknowledgement</h3>
    <p>I acknowledge that I have reviewed the final loan terms, applicable disclosures, and repayment schedule provided by the authorized lender.</p>
    <p><strong>Borrower Name:</strong> {{ $borrower_name }}</p>
    <table class="signature">
        <tr>
            <td>Signature / Approved Electronic Signature:<div class="line"></div></td>
            <td>Date: {{ $agreement_date }}<div class="line"></div></td>
        </tr>
    </table>

    <h3>8. Contact and Verification</h3>
    <table>
        <tr><th>Lender Support</th><td>{{ $support_email }}</td></tr>
        <tr><th>Bewins Support Email</th><td>{{ $support_email }}</td></tr>
        <tr><th>Website</th><td>https://bewins.in</td></tr>
        <tr><th>Loan Agreement ID</th><td>{{ $agreement_id }}</td></tr>
        <tr><th>Loan ID</th><td>{{ $loan_id }}</td></tr>
    </table>

    <div class="notice"><strong>Draft notice:</strong> This sample is not a final legal contract. The lender's legal identity, applicable terms, disclosures, grievance details, and other mandatory clauses must be reviewed and approved before it is issued to customers.</div>
    <div class="footer">Generated electronically by the Bewins platform. Refer to the authorized lender's final terms and disclosures for binding loan conditions.</div>
</body>
</html>
