<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Letter of Authorisation</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }
        .page-wrapper {
            position: relative;
            width: 100%;
            max-width: 1000px;
            margin: 0 auto;
        }

        .bg-image {
            width: 100%;
            display: block;
        }

        .content-wrapper {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            padding: 40px;
            box-sizing: border-box;
        }

        /* Header Banner */
        .header-banner {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            padding: 30px 40px;
            position: relative;
            margin-bottom: 40px;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo-section {
            display: flex;
            flex-direction: column;
        }

        .company-logo {
            width: 120px;
            margin-bottom: 10px;
        }

        .company-name {
            color: #333;
            font-size: 18px;
            font-weight: bold;
            margin: 0;
        }

        .service-text {
            color: #666;
            font-size: 14px;
            margin: 5px 0 0 0;
        }

        .urls {
            color: #28a745;
            font-size: 12px;
            margin: 5px 0 0 0;
        }

        .main-title {
            color: white;
            font-size: 32px;
            font-weight: bold;
            margin: 0;
            text-align: center;
        }

        /* Main Content */
        .document-title {
            font-size: 28px;
            font-weight: bold;
            text-align: center;
            margin: 0 0 40px 0;
            color: #333;
            text-transform: uppercase;
        }

        .intro-text {
            font-size: 16px;
            color: #333;
            margin-bottom: 20px;
        }

        .applicant-name {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin-bottom: 15px;
        }

        .applicant-details {
            font-size: 14px;
            color: #333;
            line-height: 1.6;
            margin-bottom: 15px;
        }

        .authorization-text {
            font-size: 16px;
            color: #333;
            line-height: 1.6;
            margin: 30px 0;
        }

        /* Signature Section */
        .signature-table {
            margin-top: 60px;
            width: 100%;
            text-align: center;
        }

        .signature-table td {
            padding: 20px;
            vertical-align: top;
        }

        .signature-image {
            width: 120px;
            margin-bottom: 10px;
        }

        .signature-name {
            font-size: 16px;
            font-weight: bold;
            color: #333;
            margin: 5px 0;
        }

        .signature-title {
            font-size: 14px;
            color: #333;
            margin: 5px 0;
        }

        /* Footer */
        .footer {
            margin-top: 60px;
            text-align: center;
            font-size: 12px;
            color: #666;
            line-height: 1.4;
        }

        .footer p {
            margin: 5px 0;
        }

        /* Left sidebar contact info */
        .contact-sidebar {
            position: fixed;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            writing-mode: vertical-rl;
            text-orientation: mixed;
            font-size: 12px;
            color: #333;
            line-height: 2;
        }
    </style>
</head>
<body>

<div class="page-wrapper">
    {{-- Background Image --}}
    <img class="bg-image" src="{{ asset('public/backend/images/cert-1.png') }}" alt="Background">

    {{-- All Content Over Image --}}
    <div class="content-wrapper">
        <!-- Header Banner -->
        <div class="header-banner">
            <div class="header-content">
                <div class="logo-section">
                    <img src="{{ asset('backend/images/company-logo.png') }}" alt="Company Logo" class="company-logo"style="width: 260px;height: 120px; margin-left:-30px;">
                    <!--<p class="company-name">Hoogmatic</p>-->
                    <!--<p class="service-text">Bond of Services</p>-->
                    <!--<p class="urls">www.hoogmatic.in | www.loanlenders.in</p>-->
                   
                </div>
                <div class="main-title">
                    LOAN LENDERS
                </div>
            </div>
        </div>

        <h1 class="document-title">LETTER OF AUTHORISATION</h1>
    
        <p class="intro-text" style="margin-left:30px">This is in reference to the application made by</p>

        <p class="applicant-name" style="margin-left:30px">{{"TRADE".$name }} {{" ".$number}} {{" ".$name ." ".$last_name}}</p>

        <p class="applicant-details" style="margin-left:30px">Office at: Textile Colony Dumraon, Dist- Buxar - 802119 India</p>
        <p class="applicant-details" style="margin-left:30px">{{"Aadhar No.- " . $aadhar}}</p>
        <p class="applicant-details" style="margin-left:30px">{{ "Pan No.- ".$pan }}</p>

        <p class="authorization-text" style="margin-left:30px">This letter authorizes you to act as Sub-DSA for (Loan Lenders) Bewins Advisory Private Limited to market products categorized and identified under the Guidelines only.</p>

        <!-- Signature Section -->
        <table class="signature-table">
            <tr>
                <td>
                    <img src="{{ asset('public/backend/images/sign.png') }}" alt="Signature" class="signature-image">
                    <p class="signature-name">OM PRAKASH</p>
                    <p class="signature-title">Director</p>
                </td>
                <td>
                    <img src="{{ asset('public/backend/images/manorma_sign.png') }}" alt="Signature" class="signature-image">
                    <p class="signature-name">MANORMA DEVI</p>
                    <p class="signature-title">Director</p>
                </td>
            </tr>
        </table>

        <!-- Footer -->
        <div class="footer" style="color:orange; font-size:12">
            <p>Regd. & Corporate Office: Textile Colony Dumraon, Dist- Buxar - 802119</p>
            <p>Bewins Advisory Private Limited | CIN: U69100BR2025PTC074362</p>
        </div>
    </div>
</div>

<!-- Contact Sidebar -->
<!--<div class="contact-sidebar">-->
<!--    +91-7669914776<br>-->
<!--    support@hoogmatic.in<br>-->
<!--    +91-9354072574-->
<!--</div>-->

</body>
</html>
