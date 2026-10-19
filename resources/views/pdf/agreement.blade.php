<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Agreement</title>
    <style>
        @page {
            margin: 120px 50px 120px 50px;
        }

        header {
            position: fixed;
            top: -100px;
            left: 0px;
            right: 0px;
            height: 80px;
            text-align: center;
            line-height: 70px;
            border-bottom: 1px solid #ccc;
        }

        footer {
            position: fixed;
            bottom: -100px;
            left: 0px;
            right: 0px;
            height: 100px;
            font-size: 12px;
            text-align: center;
            line-height: 1.5;
            border-top: 1px solid #ccc;
        }

        .content {
            font-size: 14px;
            line-height: 1.6;
        }

        .page-break {
            page-break-after: always;
        }

        img.logo {
            height: 60px;
        }

        .partner-info {
            background: #f5f5f5;
            padding: 15px;
            margin: 20px 0;
            border-left: 4px solid #333;
        }

        .company-info {
            background: #f5f5f5;
            padding: 15px;
            margin: 20px 0;
            border-left: 4px solid #333;
        }

        h1, h2, h3 {
            color: #333;
            margin-top: 25px;
            margin-bottom: 15px;
        }

        h3 {
            font-size: 16px;
            font-weight: bold;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }

        h4 {
            color: #333;
            font-size: 14px;
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 10px;
        }

        .signature-section {
            margin-top: 50px;
            page-break-inside: avoid;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .signature, .company-seal, .user-name {
            text-align: center;
            flex: 1;
        }

        .company-seal img {
            width: 100px;
            height: auto;
        }

        p {
            margin-bottom: 12px;
            text-align: justify;
        }

        ol, ul {
            margin-bottom: 15px;
            padding-left: 20px;
        }

        li {
            margin-bottom: 8px;
        }

        .section-break {
            margin-top: 30px;
            margin-bottom: 20px;
        }

        .clause {
            margin-bottom: 20px;
        }

        .clause-title {
            font-weight: bold;
            color: #333;
            margin-bottom: 8px;
        }

        .clause-content {
            padding-left: 15px;
            border-left: 2px solid #eee;
        }
    </style>
</head>
<body>

    <!-- Header -->
    <header>
        <img src="{{ public_path('backend/images/company-logo.png') }}" class="logo" alt="Company Logo">
    </header>

    <!-- Footer -->
    <footer>
        <p><strong>Address:</strong> Textile Colony Dumraon, Dist- Buxar - 802119</p>
        <p><strong>Email:</strong>bewinservicespvtltd@gmail.com | <strong>Website:</strong> www.Bewins.in | <strong>Official No.:</strong> 18008906652</p>
    </footer>

    <!-- Content -->
    <main class="content">
        <h1 style="text-align: center; margin-bottom: 30px;">AGREEMENT</h1>

        <div class="company-info">
            <strong>COMPANY NAME:</strong> BEWINS ADVISORY PRIVATE LIMITED
        </div>

        <p>This agreement signed on <strong>{{ $date }}</strong> and is for <strong>PARTNER</strong></p>

        <div class="partner-info">
            <table width="100%" cellpadding="5">
                <tr>
                    <td width="25%"><strong>Partner Name:</strong></td>
                    <td width="25%">{{ $name }}</td>
                    <td width="25%"><strong>ID No.:</strong></td>
                    <td width="25%">#DSA-{{ $id }}</td>
                </tr>
                <tr>
                    <td><strong>Aadhar Number:</strong></td>
                    <td>{{ $aadhar ?? '209079584483' }}</td>
                    <td><strong>PAN Number:</strong></td>
                    <td>{{ $pan ?? 'CHIPR9131E' }}</td>
                </tr>
            </table>
        </div>

        <p>The consideration price (non-refundable) and the above agreement will be valid on full and final payment received.</p>

        <p>This Agreement comes into force on the date of signing, and the tenure will be as licence validity (Renewal) from the signing date. This agreement may be extended on the agreement of both parties unless earlier terminated.</p>

        <div class="section-break">
            <h3>BETWEEN</h3>
        </div>

        <p><strong>BEWINS ADVISORY PRIVATE LIMITED</strong> (from this point will be known as Loan Lenders, a private limited company incorporated under the company's act 2013, and having its corporate office at Textile Colony Dumraon, Dist- Buxar - 802119, hereinafter referred to as the "Franchiser" which expression shall unless repugnant to the context or meaning thereof include its successors and assigns of ONE PART.</p>

        <p><strong>And</strong></p>

        <p><strong>{{ $name }}</strong> is a proprietor and having his/her office at {{ $address ?? 'JAWAHI DIYAR PO.BALLUA BUXAR BIHAR MO 8252042652, Buxar, Bihar: 802130, India' }} and residence address at {{ $residence_address ?? 'Dadan prasad jawahi diyar, Balua, Buxar, Bihar 802130, Buxar, Bihar: 802130, India' }} from now on referred to as the "Partner" and "BCP" which expression unless repugnant to the context or meaning thereof be deemed to include, legal representative, executors, administrators, successors and permitted assigns of the other PART, each a party and collectively referred to as parties.</p>

        <div class="section-break">
            <h3>1. Definitions</h3>
        </div>
        <p>As used herein, the following terms shall have the meanings set forth below:</p>
        <ol>
            <li><strong>"Services"</strong> shall mean the Company's services to be sold by Partner and such services as may be communicated by the Company in writing to the Partner from time to time.</li>
            <li><strong>"Territory"</strong> shall be allocated during time of engagement by the Company in writing to the Partner. Any change in "Territory" shall be communicated by the Company in writing to the Partner from time to time.</li>
            <li>Partner will have the title of <strong>"Partner."</strong></li>
        </ol>

        <div class="section-break">
            <h3>2. Appointment</h3>
        </div>
        <p>Company hereby appoints Partner as its non-exclusive selling agent for the services in the territory, and Partner hereby accepts such appointment. Partner's sole authority shall be to solicit customers for the services in the territory in accordance with the terms of this agreement. Partner shall not have the authority to make any commitments whatsoever on behalf of Company.</p>

        <div class="section-break">
            <h3>3. General Duties</h3>
        </div>
        <p>Partner shall use his best efforts to promote the services and maximize the sale of the services in the territory. Partner shall also provide reasonable assistance to Company in promotional activities in the territory. Partner will assist the company by taking part in all promotional events, use the marketing inputs judiciously for maximizing orders for the company.</p>

        <div class="section-break">
            <h3>4. Reserved Rights</h3>
        </div>
        <p>Company reserves the right to solicit/engage other Agents, Partners directly from businesses within the territory. Partner's task is to solicit customers from all potential businesses in the territory.</p>

        <div class="section-break">
            <h3>5. Conflict of Interest</h3>
        </div>
        <p>Partner warrants to Company that it does not currently represent or promote any Services that compete with the Company's Services. During the term of this Agreement, Partner shall not represent, promote or otherwise try to sell within the Territory any Services that, in Company's judgment, compete with the Services covered by this Agreement.</p>

        <div class="section-break">
            <h3>6. Independent Contractor</h3>
        </div>
        <p>Partner is an independent contractor, and nothing contained in this Agreement shall be construed to (i) give either party the power to direct and control the day-to-day activities of the other, (ii) constitute the parties as partners, joint ventures, co-owners or otherwise, or (iii) allow Partner to create or assume any obligation on behalf of Company for any purpose whatsoever. Partner is not an employee of Company and is not entitled to any employee benefits. Partner shall be responsible for paying all income taxes and other taxes charged to Partner on amounts earned hereunder. All financial and other obligations associated with Partner's business are the sole responsibility of Partner.</p>

        <div class="section-break">
            <h3>7. Indemnification by Partner</h3>
        </div>
        <p>Partner shall indemnify and hold Company free and harmless from any and all claims, damages or lawsuits (including reasonable attorney's fees) arising out of negligence or malfeasant acts of Partner or misrepresentation or breach of any obligations under this agreement.</p>

        <div class="section-break">
            <h3>8. Commission</h3>
        </div>

        <div class="clause">
            <div class="clause-title">A. Sole Compensation</div>
            <div class="clause-content">
                <p>The Company shall pay the Partner a commission at such rate as may be communicated by the Company in writing to the Partner, for whole or part of the services hereto, based on the Maximum Retailing Price of the product as fixed by the company on every new order. This commission will be subjected to the relevant taxes as applicable. The Company reserves its right to revise the rate of commission from time to time and the same shall be intimated to the Partner in writing by the Company. (Through any mode verbal or written)</p>
            </div>
        </div>

        <div class="clause">
            <div class="clause-title">B. Basis of Commission</div>
            <div class="clause-content">
                <p>The Commission shall apply to all sales orders from customers solicited by Partner. (Customers defined as an individual or a company who have bought the product/services from the Partner for their own use.) No commissions shall be paid on (i) orders solicited directly by Company within the Territory; (ii) orders received from outside the Territory unless otherwise agreed in writing by Company. (iii) No commission will be paid to the Partner until 100% payment pertaining to the order is received. The company reserves the right to change the commission / prices on products as and when required.</p>
            </div>
        </div>

        <div class="clause">
            <div class="clause-title">C. Time of Payment</div>
            <div class="clause-content">
                <p>The commission on all PAID ORDERS shall be due and payable within ten (10) working days after the Partner raises invoice.</p>
            </div>
        </div>

        <div class="clause">
            <div class="clause-title">D. Monthly Statements</div>
            <div class="clause-content">
                <p>The Partner shall submit to the company the monthly statements of commissions due and payable to Partner under the terms of this Agreement.</p>
            </div>
        </div>

        <div class="section-break">
            <h3>9. Sale of the Services</h3>
        </div>

        <div class="clause">
            <div class="clause-title">A. Prices and Terms of Sale</div>
            <div class="clause-content">
                <p>Company shall provide Partner with copies of its current market price and this is subject to change and the sole discretion of the same lies with the company, its payment schedules (may very month to month), and all Rules and Regulations and other material available for sales presentation and customer's information. Partner shall quote to Customers only those authorized prices, payment schedules, and terms and conditions as informed by Company. The services will be activated only after receipt of 100% payment pertaining to the order. The company will not refund any money in part or in full after payment on order is once received.</p>
            </div>
        </div>

        <div class="clause">
            <div class="clause-title">B. Acceptance</div>
            <div class="clause-content">
                <p>All requests for service obtained by Partner shall be subject to acceptance by Company and all quotations by Partners shall contain a statement to that effect. Partners shall have no authority to make any acceptance or commitments to customers. Company specifically reserves the right to reject any request for service or any part thereof for any reason, Company shall inform Partner of any written acceptances on commissionable applications/orders.</p>
            </div>
        </div>

        <div class="clause">
            <div class="clause-title">C. Collection</div>
            <div class="clause-content">
                <p>Full responsibility for collection of payment from customers rests with Partner. (If found guilty or at any time it came to notice that documents are uploaded at the time of lead or at any time in between the process were manipulated or are not OSV verified.)</p>
            </div>
        </div>

        <div class="clause">
            <div class="clause-title">D. Documentation</div>
            <div class="clause-content">
                <p>All documents after disbursal and before disbursal of loan id lie to the PARTNER Agent and his responsibility is to complete the papers demanded by bank or NBFC or any other institution at the time of filing the application or after sanction or after disbursal or at the time of pay-out.</p>
            </div>
        </div>

        <div class="clause">
            <div class="clause-title">E. Verification</div>
            <div class="clause-content">
                <p>The file/lead which is generated by the PARTNER should be genuine and must be best to the knowledge of PARTNER and all documents like KYC should be originally seen and verified.</p>
            </div>
        </div>

        <div class="clause">
            <div class="clause-title">F. Whistle Blower</div>
            <div class="clause-content">
                <p>If at any time during the file/lead login or in process he came to know the intention of customer which is doubtful in any respect he must blow the whistle to company and must inform in written to the company.</p>
            </div>
        </div>

        <div class="section-break">
            <h3>10. Additional Responsibilities of Partner</h3>
        </div>

        <div class="clause">
            <div class="clause-title">A. Expense of Doing Business</div>
            <div class="clause-content">
                <p>Partner shall bear the cost and expense of conducting its business in accordance with the terms of this Agreement. This would include salaries for the staff of the Partner who are engaged in the business of selling the products of the Company, expenses related to communications, telecommunication, mailing, conveyance and business entertainment if required. The company will not entertain any reimbursement on any expense made by the Partner other than the commissions.</p>
            </div>
        </div>

        <div class="clause">
            <div class="clause-title">B. Promotion of the Products</div>
            <div class="clause-content">
                <p>Partner shall make efforts to promote the sale of and stimulate demand for the Services within the Territory by direct solicitation. In no event shall Partner make any representation, guarantee or warranty concerning the Services except as expressly authorized by Company. The Company will take care of all online promotions on their website and ensure lead generations. Use of company logo, product logo, any advertising / promotion / marketing activity conceived originally by the Partner should be first approved in writing by [Company Name] before being implemented.</p>
            </div>
        </div>

        <div class="clause">
            <div class="clause-title">C. Agents & Customer Service</div>
            <div class="clause-content">
                <p>Partner shall inform and assist customers on Company's Services, and shall perform such additional customer services by e-mail, phone and fax, whenever needed, as good salesmanship requires and as Company may reasonably request.</p>
            </div>
        </div>

        <div class="clause">
            <div class="clause-title">D. Books and Records</div>
            <div class="clause-content">
                <p>Partner shall notify Company of any Customer's complaints regarding either the Services or Company and immediately forward to Company the information regarding those complaints.</p>
            </div>
        </div>

        <div class="clause">
            <div class="clause-title">E. Documents Original Seen and Verified</div>
            <div class="clause-content">
                <p>Partner shall and will perform duties while seeing all the documents of the customer and must believe to his knowledge that all documents presented by the customer are showing the true and fair view. Partner shall originally see and verify all documents presented by the customer, if at any point the partner or BCP came to know that documents are manipulated then he/she must inform to the company in written format and should take necessary actions to stop the lead. The Original seen and Verified (OSV) duty is of the partner and BCP and should be performed for the good cause and within the business ethics.</p>
            </div>
        </div>

        <div class="section-break">
            <h3>11. Additional Obligations of Company</h3>
        </div>

        <div class="clause">
            <div class="clause-title">A. Assistance in Promotion</div>
            <div class="clause-content">
                <p>Company shall, at its own expense, promptly provide Partner with marketing and technical information, training concerning the Services, brochures, instructional material, advertising literature, and other product data. The portal for file login will be provided by the company and all training part will be take care by the company.</p>
            </div>
        </div>

        <div class="clause">
            <div class="clause-title">B. Assistance in Technical Problems</div>
            <div class="clause-content">
                <p>Company shall, at its own expense, assist Partner and customers of the Services in all ways deemed reasonable by Company in the solution of any problems relating to the Services.</p>
            </div>
        </div>

        <div class="clause">
            <div class="clause-title">C. New Developments</div>
            <div class="clause-content">
                <p>Company shall inform Partner of new Products or Services that are competitive with Company's Products Services and other market information and competitive information as discovered from time to time.</p>
            </div>
        </div>

        <div class="section-break">
            <h3>12. Trademarks and Tradenames</h3>
        </div>

        <div class="clause">
            <div class="clause-title">A. Use</div>
            <div class="clause-content">
                <p>During the term of this Agreement, Partner shall have the right to indicate to the public that it is an authorized Partner of Company's Services. Nothing herein shall grant Partner any right, title, or interest in Company's Trademarks. At no time during or after the term of this Agreement shall Partner challenge or assist others to challenge Company's Trademarks or the registration thereof or attempt to register any trademarks, marks or trade names confusingly similar to those of Company.</p>
            </div>
        </div>

        <div class="section-break">
            <h3>13. Non-Compete</h3>
        </div>
        <p>For a period of refer page 1 Table after the Partner is no longer in agreement with the Company, the Partner will not, directly or indirectly, either as proprietor, stockholder, partner, officer, employee or otherwise, distribute, sell, offer to sell, or solicit any orders for the purchase or distribution of any products or services which are similar to those distributed, sold or provided by the Company.</p>

        <div class="section-break">
            <h3>14. Term and Termination</h3>
        </div>

        <div class="clause">
            <div class="clause-title">A. Term</div>
            <div class="clause-content">
                <p>This Agreement shall commence on the date first written above.</p>
            </div>
        </div>

        <div class="clause">
            <div class="clause-title">B. Termination</div>
            <div class="clause-content">
                <p>Either party to this agreement shall have the right to terminate this agreement with or without cause with a thirty (30) days written notice to the other party.</p>
            </div>
        </div>

        <div class="clause">
            <div class="clause-title">C. Return of Materials</div>
            <div class="clause-content">
                <p>All of Company's trademarks, trade names, data, photographs, literature, and sales aids, customer related database of every kind shall remain the property of Company. Within five (5) days after the termination of this Agreement, Partner shall return all such items to company. Partner shall not make or retain any copies of any confidential items or information that may have been entrusted to it. Effective upon the termination of this Agreement, Partner shall cease to use all trademarks, marks and trade name of Company.</p>
            </div>
        </div>

        <div class="clause">
            <div class="clause-title">D. Review</div>
            <div class="clause-content">
                <p>This agreement will be reviewed by the company after a period of 12 months* (as per company BOD). Any Partner not performing to the full satisfaction of the company in terms of securing new orders and company's policies is liable to be terminated. Bewins Advisory Private Limited reserved to right to change any of the terms and condition without any prior notice.</p>
            </div>
        </div>

        <div class="section-break">
            <h3>15. Limitation on Liability</h3>
        </div>
        <p>In the event of termination by either party in accordance with any of the provisions of this Agreement, neither party shall be liable to the other, because of the termination for compensation, reimbursement or damages on account of the loss of prospective profits or anticipated sales or on account of expenditures or commitments in connection with the business or goodwill of Company or Partner.</p>

        <div class="section-break">
            <h3>16. Confidentiality</h3>
        </div>
        <p>Partner acknowledges that by reason of its relationship to Company hereunder it will have access to certain information and materials concerning Company's business plans, customers, technology, and products/services that is confidential and of substantial value to Company, which value would be impaired if such information were disclosed to third parties. Partner agrees that it shall not use in any way for its own account or the account of any third party, nor disclose to any third party, any such confidential information revealed to it by the Company. Company shall advise Partner whether or not it considers any particular information or materials to be confidential. Partner shall not publish any description of the Products/Services beyond the description published by Company and without the prior written consent of the Company. In the event of termination of this Agreement, there shall be no use or disclosure by Partner of any confidential information of Company.</p>

        <div class="section-break">
            <h3>17. Governing Law and Jurisdiction</h3>
        </div>
        <p>This Agreement will be governed by and construed in accordance with the laws of Republic of India. Each Party irrevocably and unconditionally submits to the exclusive jurisdiction of the High Court.</p>

        <div class="section-break">
            <h3>18. Entire Agreement</h3>
        </div>
        <p>This Agreement sets forth the entire agreement and understanding of the parties relating to the subject matter herein and supersedes any prior discussions or agreements between them. No modification of or amendment to neither this Agreement, nor any waiver of any rights under this Agreement to be done unilaterally and it shall be effective unless in writing signed by the party to be charged.</p>

        <div class="section-break">
            <h3>19. Notices</h3>
        </div>
        <p>Any notices required or permitted by this Agreement shall be deemed given if sent by certified mail, postage prepaid, return receipt requested or by recognized overnight delivery service:</p>
        <p>If to Company: Textile Colony Dumraon, Dist- Buxar - 802119</p>

        <div class="section-break">
            <h3>20. Non-Assignability and Binding Effect</h3>
        </div>
        <p>A mutually agreed consideration for Company's entering into this Agreement is the reputation, business standing, and goodwill already honoured and enjoyed by Company under its present ownership, and, accordingly, Partner agrees that its rights and obligations under this Agreement may not be transferred or assigned directly or indirectly. Subject to the foregoing, this Agreement shall be binding upon and insure to the benefit of the parties hereto, their successors and assigns.</p>

        <div class="section-break">
            <h3>21. Severability</h3>
        </div>
        <p>If any provision of this Agreement is held to be invalid by a court of competent jurisdiction, then the remaining provisions shall nevertheless remain in full force and effect.</p>

        <div class="section-break">
            <h3>22. Applicable Law</h3>
        </div>
        <p>This agreement is governed by and constructed in accordance with the laws of India.</p>

        <div class="section-break">
            <h3>23. BCP Terms and Conditions</h3>
        </div>
        <p>This point will be only applicable to BCP Channel and the BCP agreement is signed separately otherwise it will be null and void.</p>
        <ol>
            <li>BCP stands for Business Channel Partner</li>
            <li>If you are BCP, the commission will be paid by Loan Lenders as per the chart applicable at the time of sale of a particular PARTNER by Loan Lenders</li>
            <li>Loan Lenders will allot the portal for tracking you your sales and orders deducted by another PARTNER.</li>
            <li>In case of any addition of channel partner made by you will be informed to Loan Lenders and all payments will be made by another channel partner in Loan Lenders Advisory Private Limited bank account and after that Loan Lenders will allot other Partner portal and sign the agreement with him/her.</li>
            <li>Loan Lenders Team will provide you leads by the mode which is applicable at that time, any commitment or offer given for the sale will be first approved by Loan Lenders staff in written</li>
            <li>Any commission will be transferred to your bank account (if any) on or before 7th of the following month.</li>
        </ol>

        <div class="section-break">
            <h3>24. Dispute Resolution and Jurisdiction</h3>
        </div>
        <p>Any dispute, controversy or claims arising out of or relating to this Agreement or the breach, termination or invalidity thereof, shall be settled by arbitration in accordance with the provisions of the Indian Arbitration and Conciliation Act, 1996.</p>
        <ol>
            <li>The arbitral tribunal shall be composed of three arbitrators, one arbitrator appointed by Loan Lenders a second arbitrator appointed by "Franchisee" and a third arbitrator to be appointed by such arbitrators.</li>
            <li>The place of arbitration shall be at Delhi for any award whether interim or final.</li>
            <li>The arbitral procedure shall be conducted in the English language, and any award or awards shall be rendered in English. The procedural law of the arbitration shall be Indian.</li>
            <li>The award of the arbitral tribunal shall be final, conclusive and binding upon the Parties, and the provisions of the Indian Arbitration and Conciliation Act, 1996 shall apply.</li>
            <li>The rights and obligations of the Parties under, or under, this Clause, including the arbitration agreement in this Clause, shall be governed by and be subject to Indian law, and the agreement shall be subject to the exclusive jurisdiction of the courts at Delhi.</li>
            <li>This Agreement has been executed on the date set forth herein in two (2) copies of which the Parties have taken one each.</li>
        </ol>

        <p><strong>Note:</strong> This agreement is digitally signed via E-mail OTP.</p>

       
        
<!--         <table style="width: 100%; ">-->
<!--    <tr>-->
<!--        <td style="text-align: left; margin-left:70px !important ">-->
            
<!--            <img  style="height:40px; width:80px" src="https://tse4.mm.bing.net/th/id/OIP.zhF1-kLPbEt2oEk85FhTTAHaDQ?pid=Api&P=0&h=180" alt="sign1"><br>-->
<!--            <p>Director</p> -->
<!--                <p>OM PRAKASH</p>-->
<!--        </td>-->
<!--        <td>-->
<!--             <img  style="height:80px; width:80px" src="https://tse1.mm.bing.net/th/id/OIP.NEZe_EjZivkv0-eo4xxMtAHaHZ?pid=Api&P=0&h=180" alt="sign1"><br><br>-->
<!--        </td>-->
<!--        <td style="text-align: right; ">-->
            
            
<!--                <p> <strong>{{"TRADE".$name }} {{" ".$number}} {{" ".$name ." ".$last_name}}</strong></p><br>-->
               
<!--        </td>-->
<!--    </tr>-->
<!--</table>-->

  <table style="width: 100%; margin-top: 80px;">
    <tr>
        <td style="text-align: left; padding-left: 120px;">
            
            <img  style="height:90px; width:130px" src={{ asset('public/backend/images/sign.png')}} TTAHaDQ?pid=Api&P=0&h=180"alt="sign1"><br><br>
            OM PRAKASH<br>
            Director
        </td>
        <td>
             <img  style="height:110px; width:100px; object-fit:fill" src={{ asset('public/backend/images/bewins_seal.png')}} " alt="sign1"><br><br>
        </td>
       <td style="text-align: right; ">
            
            
            <p> <strong>{{"TRADE".$name }} {{" ".$number}} {{" ".$name ." ".$last_name}}</strong></p><br>
               
       </td>
    </tr>
</table>
    </main>
</body>
</html>