<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Authorisation Certificate</title>
  <style>
   @page {
    margin: 0cm;
}

body {
    margin: 0cm;
    font-family: Arial, sans-serif;
    position: relative;
}

.bg-image {
    position: fixed;
    top: 0;
    left: 0;
    z-index: -1;
    width: 100%;
    height: 100vh;
    object-fit: cover;
}

.content {
    position: relative;
    top: 100px;
    text-align: center;
    padding: 0 40px;
    /*margin-top:80px;*/
    color: #000;
}

.signature-section {
    margin-top: 80px;
    display: flex;
    justify-content: space-between;
    padding: 0 60px;
}

.signature {
    text-align: center;
    font-size: 14px;
}

.footer {
    margin-top: 50px;
    font-size: 12px;
}

</style>
</head>
<body>
   
    <img class="bg-image" src="{{ public_path('backend/images/cert-2.png') }}" alt="Background">

   
    <div class="content">
         <div class="title" style="font-size:40px; font-weight:900; color: #00bfff;">AUTHORISATION</div>
         <span>LETTER</span>
        <div class="company" style="font-size:40px; font-weight:900;">L<span style="color: orange;">O</span>AN LENDERS</div>
        <span> Authorise</span>
       
        <div class="name" style="margin-top:10px;font-size:20px;">
           
           <strong>{{"TRADE".$name }} {{" ".$number}} {{" ".$name ." ".$last_name}}</strong>
          
        </div>

        <div class="profile-id" style="margin-top:12px;font-size:16px;">
            <strong>Profile ID: {{ $profileId ?? 'N/A' }}</strong>
        </div>

        <div class="desc" style="margin-top:30px">
            Has the responsibility to promote, distribute, sale and provide all services of Loan Lenders <br>(Bewins Advisory Private Limited)
        </div>

       <table style="width: 100%; margin-top: 80px;">
    <tr>
        <td style="text-align: left; padding-left: 200px;">
            
            <img style="height:90px; width:130px" src="{{ public_path('backend/images/sign.png') }}" alt="Director signature"><br><br>
            OM PRAKASH<br>
            Director
        </td>
        <td>
             <img style="height:110px; width:100px; object-fit:fill" src="{{ public_path('backend/images/bewins_seal.png') }}" alt="Company seal"><br><br>
        </td>
        <td style="text-align: right; padding-right: 200px;">
           <img style="height:90px; width:110px" src="{{ public_path('backend/images/manorma_sign.png') }}" alt="Director signature"><br><br>
           Manorma Devi<br>
            Director
        </td>
    </tr>
</table>


        <div class="footer">
            
             Bewins Advisory Private Limited | CIN: U69100BR2025PTC074362
        </div>
    </div>
</body>
</html>
