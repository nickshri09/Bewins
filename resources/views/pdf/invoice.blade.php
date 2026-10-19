<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $order->id }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            color: #000;
            font-size: 12px;
            margin: 0;
            padding: 0;
        }
        .container {
            padding: 20px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        .header-left img {
            height: 60px;
        }
        .header-right {
            text-align: right;
            font-size: 12px;
            margin-top:-90px;
        }
        .header-right .title {
            font-weight: bold;
            font-size: 14px;
        }
        .badge {
            display: inline-block;
            background: #a64ce0;
            color: #fff;
            padding: 3px 8px;
            font-size: 11px;
            font-weight: bold;
            border-radius: 3px;
            margin-top: 6px;
        }
       .details {
    margin-top: 10px;
    display: flex;
    justify-content: space-between;
    align-items: flex-start; /* यह दोनों को top align रखेगा */
    font-size: 12px;
    gap: 20px; /* left और right block के बीच gap */
}

.details div {
    flex: 1; /* दोनों बराबर space लें */
}

.details div:first-child {
    max-width: 50%; /* left वाला 50% तक */
}

.details div:last-child {
    text-align: right; /* right वाला text right align */
}

        .box {
            border: 1px solid #000;
            padding: 8px;
            margin-top: 10px;
            font-size: 12px;
        }
        .box strong {
            font-weight: bold;
        }
        .terms {
            margin-top: 10px;
            border: 1px solid #000;
            padding: 8px;
            font-size: 12px;
        }
        .terms ol {
            margin: 0;
            padding-left: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }
        th, td {
            border: 1px solid #000;
            padding: 6px;
            vertical-align: top;
            font-size: 12px;
        }
        th {
            background: #f2f2f2;
            font-weight: bold;
            text-align: left;
        }
        .amount {
            text-align: right;
        }
        .totals {
            width: 40%;
            margin-left: auto;
            margin-top: 8px;
            font-size: 12px;
        }
        .totals td {
            border: none;
            padding: 4px 0;
        }
        .totals td:first-child {
            text-align: right;
            padding-right: 8px;
        }
        .totals td:last-child {
            text-align: right;
            font-weight: bold;
        }


    </style>
</head>
<body>
<div class="container">

    <!-- Header -->
    <div class="header">
        <div class="header-left">
            <img src="{{ public_path('backend/images/company-logo.png') }}" alt="Logo">

        </div>
        <div class="header-right">
            <!--<div class="title">BEWINS SOFTWARE PRIVATE LIMITED</div>-->
            <!--H.NO-9610 9/F, C-1 BLK RANA PARK<br>-->
            <!--UTTAM NAGAR NEAR METRO STATION, NEW DELHI, DELHI-110059 INDIA<br>-->
            <div class="title">BEWIN SERVICES PRIVATE LIMITED</div>
            C/O. SMT. SONAPATI DEVI, WARD-02,<br>
            TEXTILE COLONY, Dumraon Textiles,<br>
            Dumraon, Buxar- 802136, Bihar<br>
            <!--GSTIN: 07AAECH7810K1ZF<br>-->
            GSTIN: 10AANCB3455G1ZY<br>
            {{-- CIN: U74999DL2019PTC348331<br> --}}
            CIN: U69100BR2025PTC074362<br>
            Email: bewinservicespvtLtd@gmail.com
        </div>
    </div>


    <!-- Details -->
    <div class="details">
        <div>
            <strong>Name:</strong> {{ $order->member->name ?? ($order->user->name ?? 'N/A') }}<br>
            <strong>Address:</strong> {{ $order->user->residence_address ?? ($order->member->address ?? 'N/A') }}<br>
            <strong>Mobile:</strong> {{ $order->user->mobile_number ?? ($order->member->mobile ?? 'N/A') }}<br>
            <strong>Email:</strong> {{ $order->user->email ?? ($order->member->email ?? 'N/A') }}<br>
            <strong>GSTIN:</strong> {{ $order->user->gst_number ?? 'N/A' }}
        </div>
        <div>
            <strong>Invoice Number:</strong> {{ $invoiceNo }}<br>
            <strong>Invoice Date:</strong> {{ $issuedAt }}<br>
            <strong>Order Number:</strong> {{ $orderNo }}<br>
            <strong>Order Date:</strong> {{ optional($order->created_at)->format('F d, Y') }}<br>
            <strong>Relationship Manager:</strong> {{ optional($order->user)->id ?? 'N/A' }} - {{ optional($order->user)->name ?? optional($order->member)->name ?? 'N/A' }}
        </div>
    </div>


    <!-- Bank Details -->
    {{-- <div class="box">
        <strong>Bank Name:</strong> {{ $order->user->bank_name ?? 'ICICI Bank Limited' }}<br>
        <!--<strong>Account Name:</strong> {{ $order->user->account_name ?? 'Hoog Software Private Limited' }}<br>-->
        <strong>Account Name:</strong>{{ ' Bewin Services Private Limited'}}<br
        <strong>Account Number:</strong> {{ $order->user->account_number ?? '0007150050637' }}<br>
        <strong>IFSC Code:</strong> {{ $order->user->ifsc_code ?? 'ICIC0000071' }}<br>
        <strong>Account Type:</strong> {{ $order->user->account_type ?? 'Current Account' }}<br>
        <strong>Branch:</strong> {{ optional($order->user->branch)->name ?? 'Green Park' }}<br>
        1. The above amount is deducted from wallet.<br>
        2. If refund initiated then it will be transferred to the main wallet only and can be further used for other services.<br>
        3. The amount is in Indian Rupee.
    </div> --}}
    <div class="box">
        <strong>Bank Name:</strong> {{'Punjab National Bank' }}<br>
        <strong>Account Name:</strong> {{ ' Bewin Services Private Limited'}}<br>
        <strong>Account Number:</strong> {{ '6006002100001329' }}<br>
        <strong>IFSC Code:</strong> {{ 'PUNB0600600 BRANCH- Chaugain' }}<br>
        <strong>Account Type:</strong> {{ optional($order->user)->account_type ?? 'Current Account' }}<br>
        <strong>Branch:</strong> {{ optional(optional($order->user)->branch)->name ?? optional($order->member)->branch->name ?? 'Green Park' }}<br>
        1. The above amount is deducted from wallet.<br>
        2. If refund initiated then it will be transferred to the main wallet only and can be further used for other services.<br>
        3. The amount is in Indian Rupee.
    </div>

     <!-- Terms -->
    <div class="terms">
        <strong>Terms and Conditions</strong>
        <ol>
            <li>Bewin does not guarantee company registration, trademark registration, or any similar activities. We solely assist with applications in accordance with the rules and regulations of the Government of India. If an application is rejected by the government, the amount paid will not be refunded.</li>
            <li>Approval is entirely at the discretion of the government or relevant authority. Our role is limited to acting as a service provider.</li>
            <li>Bewin and its staff do not charge any fees for loan disbursal. Loan approval and disbursal are at the sole discretion of the Bank or NBFC.</li>
            <li>We may delegate work to third parties to ensure timely and quality completion of tasks. Cancellation by the client will result in no refund, except wallet credit in special cases.</li>
            <li>We operate in the B2B sector; our terms apply accordingly.</li>
            <li>Any legal jurisdiction will be limited to Delhi or Dehradun.</li>
            <li>This is a computer-generated invoice and does not require a signature.</li>
        </ol>
    </div>


   <!-- Items section ko page break ke saath start karte hain -->
<div style="page-break-before: always;">
    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th style="width: 120px;" class="amount">Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Main Service: {{ $order->main_service_name ?? ($order->service->service_name ?? 'N/A') }}</td>
                <td class="amount">{{ number_format((float)($order->main_service_fee ?? 0), 2) }}</td>
            </tr>
            @if(!empty($order->scope_service_name) || !empty($order->scope_service_fee))
            <tr>
                <td>Scope Service: {{ $order->scope_service_name ?? 'N/A' }}</td>
                <td class="amount">{{ number_format((float)($order->scope_service_fee ?? 0), 2) }}</td>
            </tr>
            @endif
        </tbody>
    </table>
</div>




    <?php
        $main = (float)($order->main_service_fee ?? 0);
        $scope = (float)($order->scope_service_fee ?? 0);
         $subtotal = $main + $scope;
        // $taxRate = 18.0;
        // $taxAmount = $subtotal * $taxRate;
        // $total = $subtotal + $taxAmount;


        // (10%)
        $taxRate = 18.0;
        $taxAmount = ($subtotal * $taxRate) / 100;

       // Total amount
         $total = $subtotal + $taxAmount;

        // Optional: GST breakdown (CGST + SGST, 50%-50%)
        $cgst = $taxAmount / 2;
        $sgst = $taxAmount / 2;
  //dd($subtotal);
  //dd($taxAmount);
    ?>

    <!-- Totals -->
    <table class="totals">
        <tr>
            <td>Subtotal</td>
            <td>{{ number_format($subtotal, 2) }}</td>
        </tr>
        <tr>
            <td>Tax</td>
            <td>{{ number_format($taxAmount, 2) }}</td>
        </tr>
        <tr>
            <td>Total</td>
            <td>{{ number_format($total, 2) }}</td>
        </tr>
    </table>



</div>
</body>
</html>
