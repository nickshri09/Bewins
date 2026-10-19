<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Service;
use App\Models\MainService;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $blocks = [
            '7140||DIN Activation (Company/LLP)' => [
                ['7161','Government Fees',5000.00],
            ],
            '7414||Change in Object Clause of the Company' => [
                ['7421','Governemnt Fees',400.00],
            ],
            '7447||APEDA Registration' => [
                ['7454','Government Fees',5900.00],
            ],
            '7473||Maharashtra Gumasta (Shop Act)' => [
                ['7480','Government Fees',30.00],
            ],
           
            '8327||DSC Token for Downloading Purpose Only' => [
                ['8394','Class 3 (Type = General | Term = 2 Years)',1.00],
                ['8395','Class 3 (Type = Combo | Term = 2 Years)',2.00],
                ['8396','Class 0 (Type = DGFT | Term = 2 Years)',3.00],
            ],
            '8417||NIDHI Registration (20 Lacs Authorized Capital)' => [
                ['8519','UTTAR PRADESH',69069.00],
                ['8510','NAGALAND',68319.00],
                ['8511','ORISSA',68669.00],
                ['8512','PONDICHERRY',68569.00],
                ['8513','PUNJAB',83084.00],
                ['8514','RAJASTHAN',73569.00],
                ['8515','SIKKIM',68059.00],
                ['8516','TAMIL NADU',69279.00],
                ['8517','TELANGANA',71579.00],
                ['8518','TRIPURA',68319.00],
                ['8509','MIZORAM',68319.00],
                ['8520','UTTARAKHAND',69069.00],
                ['8521','WEST BENGAL',68429.00],
                ['8710','LADAKH',68059.00],
                ['8730','ANDAMAN AND NICOBAR ISLAND',68579.00],
                ['8731','ANDHRA PRADESH',71579.00],
                ['8732','ARUNACHAL PRADESH',68769.00],
                ['8733','ASSAM',68584.00],
                ['8734','BIHAR',71579.00],
                ['8500','JAMMU AND KASHMIR',68519.00],
                ['8492','CHHATISGARH',71569.00],
                ['8493','DADAR NAGAR HAVELI',68100.00],
                ['8494','DAMAN AND DIU',72229.00],
                ['8495','DELHI',71269.00],
                ['8496','GOA',72259.00],
                ['8497','GUJARAT',78179.00],
                ['8498','HARYANA',68254.00],
                ['8499','HIMACHAL PRADESH',68242.00],
                ['8491','CHANDIGARH',69562.00],
                ['8501','JHARKHAND',68232.00],
                ['8502','KARNATAKA',83079.00],
                ['8503','KERALA',74084.00],
                ['8504','LAKSHADWEEP',69584.00],
                ['8505','MADHYA PRADESH',75609.00],
                ['8506','MAHARASTRA',72359.00],
                ['8507','MANIPUR',68319.00],
                ['8508','MEGHALAYA',68469.00],
            ],
            '1945||FSSAI And FOSCOS - Central' => [
                ['1949','Government Fees',1180.00],
            ],
            '5465||FSSAI And FOSCOS - Central Licence(Amendment)' => [
                ['5473','Government Fees',1000.00],
            ],
            '5438||FSSAI And FOSCOS - Basic Licence (Amendment)' => [
                ['5444','Government Fees',100.00],
            ],
            '5451||FSSAI And FOSCOS - State Licence (Amendment)' => [
                ['5458','Government Fees',1000.00],
                ['7050','Government Fees',1000.00],
            ],
            '5709||FSSAI And FOSCOS - State Licence' => [
                ['7277','ALL FOOD SERVICE PROVIDER (only 1 Year) (kindly add the remaining government fees with reference order)',1180.00],
                ['7278','MANUFACTURER / MILLER (BELOW 1 MT) (1 Year) (kindly add the remaining government fees with reference order )',1180.00],
                ['7279','HOTELS (UPTO 4 STARS) (1 Year) (kindly add the remaining government fees with reference order)',1180.00],
                ['7280','MANUFACTURER / MILLER (ABOVE 1 MT) (1 Year) (kindly add the remaining government fees with reference order)',1180.00],
            ],
            '5716||FSSAI And FOSCOS - Basic Licence' => [
                ['7327','1 Year',100.00],
                ['7328','2 Years',200.00],
                ['7329','3 Years',300.00],
                ['7330','4 Years',400.00],
                ['7331','5 Years',500.00],
            ],
            '386||Import - Export Services (IEC) - IEC (Import Export Code) Amendment' => [
                ['7244','Government Fees',200.00],
            ],
            '362||Import - Export Services (IEC) - IEC (IMPORT EXPORT CODE) NEW' => [
                ['7466','Government Fees',500.00],
            ],
            '1414||Income Tax Related Services (Itr & Other Direct Tax Compliance) - ITR-4 (Turnover Upto ₹ 50Lacs)' => [
                ['9317','Government Fees',1.00],
                ['9318','Government Fees',1000.00],
                ['9319','Government Fees',5000.00],
            ],
            '1420||Income Tax Related Services (Itr & Other Direct Tax Compliance) - ITR-4 (Turnover Upto ₹ 1 Crore)' => [
                ['8939','Government Fees',1000.00],
                ['9285','Government Fees',5000.00],
                ['9286','Government Fees',1.00],
            ],
            '1429||Income Tax Related Services (Itr & Other Direct Tax Compliance) - ITR-2 (Upto ₹ 25 Lacs)' => [
                ['9180','Government Fees',1.00],
                ['9181','Government Fees',1000.00],
                ['9182','Government Fees',5000.00],
            ],
            '1432||Income Tax Related Services (Itr & Other Direct Tax Compliance) - ITR-2 (Upto ₹ 50 Lacs)' => [
                ['9219','Government Fees',1.00],
                ['9220','Government Fees',1000.00],
                ['9221','Government Fees',5000.00],
            ],
            '1437||Income Tax Related Services (Itr & Other Direct Tax Compliance) - ITR-2 (Above ₹ 50 Lacs)' => [
                ['9258','Government Fees',1.00],
                ['9259','Government Fees',1000.00],
                ['9260','Government Fees',5000.00],
            ],
            '1534||Income Tax Related Services (Itr & Other Direct Tax Compliance) - ITR-3 (Turnover upto ₹ 20 Lacs)' => [
                ['8958','Government Fees',1000.00],
                ['9117','Government Fees',1.00],
                ['9118','Government Fees',5000.00],
            ],
            '1540||Income Tax Related Services (Itr & Other Direct Tax Compliance) - ITR-3 (Turnover upto ₹ 50 Lacs)' => [
                ['9084','Government Fees',1.00],
                ['9097','Government Fees',1000.00],
                ['9098','Government Fees',5000.00],
            ],
            '1546||Income Tax Related Services (Itr & Other Direct Tax Compliance) - ITR-3 (Turnover upto ₹ 75 Lacs)' => [
                ['9051','Government Fees',1000.00],
                ['9052','Government Fees',5000.00],
                ['9053','Government Fees',1.00],
            ],
            '1552||Income Tax Related Services (Itr & Other Direct Tax Compliance) - ITR-3 (Turnover upto ₹ 1 Crore)' => [
                ['9141','Government Fees',1.00],
                ['9142','Government Fees',1000.00],
                ['9143','Government Fees',5000.00],
            ],
            '1675||Income Tax Related Services (Itr & Other Direct Tax Compliance) - PAN AND AADHAAR CARD Link on Income Tax Portal' => [
                ['1676','Government Fees',1000.00],
            ],
            '5487||Income Tax Related Services (Itr & Other Direct Tax Compliance) - ITR- U (Last Year ITR or Missed ITR)' => [
                ['5494','Government Fees',1000.00],
            ],
            '1830||Miscellaneous Services - DPCC (Delhi Pollution Control) Certificate' => [
                ['1837','DPCC Government Fees',10000.00],
            ],
            '2083||Miscellaneous Services - Labour Card Odisha per Labour (1 Labour)' => [
                ['2836','Government Fees for Labour Card Odisha (1 Labour)',25.00],
            ],
            '1820||Trademark and Intellectual Rights - Trademark Registration without DSC (for Single (1) Class) only for Individual' => [
                ['1821','Government Fees (For Single Class)',4500.00],
            ],
            '1903||Trademark and Intellectual Rights - Trademark Registration without DSC (for Single (1) Class) for other than Individual' => [
                ['1904','Government Fees for Trademark (only for (1) class) for other than Individual',9000.00],
            ],
            '1954||Trademark and Intellectual Rights - Trademark Registration without DSC (for Single (1) Class) for other than Individual (with MSME Certificate)' => [
                ['1959','Government Fees (For Single Class)',4500.00],
            ],
            '365||ROC Event Compliance - Event compliance-Appointment of Director or partner in company or LLP (Excluding DSC)' => [
                ['5480','DIR-3 Fees and DIR-12',800.00],
            ],
            '366||ROC Event Compliance - Event compliance-Resignation/Removal of Director or partner in company or LLP(Excluding DSC)' => [
                ['5501','Government Fees',400.00],
            ],
            '9340||EPCH- Export Promotion Council for Handicrafts' => [
                ['9349','Government Fees',2950.00],
            ],
            '9358||Renewal Export Promotion Council for Handicrafts (EPCH)' => [
                ['9367','Government Fees',5900.00],
            ],
            '9383||PUBLIC LIMITED COMPANY REGISTRATION' => [
                ['9489','PUNJAB',11156.00],
                ['9481','MADHYA PRADESH',8681.00],
                ['9482','MAHARASTRA',2431.00],
                ['9483','MANIPUR',1391.00],
                ['9484','MEGHALAYA',1541.00],
                ['9485','MIZORAM',1391.00],
                ['9486','NAGALAND',1391.00],
                ['9487','ORISSA',1741.00],
                ['9488','PONDICHERRY',1641.00],
                ['9480','LAKSHADWEEP',2656.00],
                ['9490','RAJASTHAN',6641.00],
                ['9491','SIKKIM',1131.00],
                ['9492','TAMIL NADU',1651.00],
                ['9493','TELANGANA',2651.00],
                ['9494','TRIPURA',1391.00],
                ['9495','UTTAR PRADESH',2141.00],
                ['9496','UTTARAKHAND',2141.00],
                ['9497','WEST BENGAL',1501.00],
                ['9471','DELHI',1491.00],
                ['9463','ANDHRA PRADESH',2651.00],
                ['9464','ARUNACHAL PRADESH',1841.00],
                ['9465','ASSAM',1656.00],
                ['9466','BIHAR',2731.00],
                ['9467','CHANDIGARH',2634.00],
                ['9468','CHHATISGARH',2641.00],
                ['9469','DADAR NAGAR HAVELI',1172.00],
                ['9470','DAMAN AND DIU',2301.00],
                ['9462','ANDAMAN AND NICOBAR ISLAND',1651.00],
                ['9472','GOA',2331.00],
                ['9473','GUJARAT',1751.00],
                ['9474','HARYANA',1266.00],
                ['9475','HIMACHAL PRADESH',1254.00],
                ['9476','JAMMU AND KASHMIR',1441.00],
                ['9477','JHARKHAND',1304.00],
                ['9478','KARNATAKA',10179.00],
                ['9479','KERALA',4156.00],
            ],
            '358||Company And LLP And NGO Incorporation - PRIVATE LIMITED COMPANY REGISTRATION' => [
                ['1114','PUNJAB',11156.00],
                ['1106','MADHYA PRADESH',8681.00],
                ['1107','MAHARASTRA',2431.00],
                ['1108','MANIPUR',1391.00],
                ['1109','MEGHALAYA',1541.00],
                ['1110','MIZORAM',1391.00],
                ['1111','NAGALAND',1391.00],
                ['1112','ORISSA',1741.00],
                ['1113','PONDICHERRY',1641.00],
                ['1105','LAKSHADWEEP',2656.00],
                ['1115','RAJASTHAN',6641.00],
                ['1116','SIKKIM',1131.00],
                ['1117','TAMIL NADU',1651.00],
                ['1118','TELANGANA',2651.00],
                ['1119','TRIPURA',1391.00],
                ['1120','UTTAR PRADESH',2141.00],
                ['1121','UTTARAKHAND',2141.00],
                ['1122','WEST BENGAL',1501.00],
                ['1096','DELHI',1491.00],
                ['1088','ANDHRA PRADESH',2651.00],
                ['1089','ARUNACHAL PRADESH',1841.00],
                ['1090','ASSAM',1656.00],
                ['1091','BIHAR',2731.00],
                ['1092','CHANDIGARH',2634.00],
                ['1093','CHHATISGARH',2641.00],
                ['1094','DADAR NAGAR HAVELI',1172.00],
                ['1095','DAMAN AND DIU',2301.00],
                ['1087','ANDAMAN AND NICOBAR ISLAND',1651.00],
                ['1097','GOA',2331.00],
                ['1098','GUJARAT',1751.00],
                ['1099','HARYANA',1266.00],
                ['1100','HIMACHAL PRADESH',1254.00],
                ['1101','JAMMU AND KASHMIR',1441.00],
                ['1102','JHARKHAND',1304.00],
                ['1103','KARNATAKA',10179.00],
                ['1104','KERALA',4156.00],
            ],
            '359||Company And LLP And NGO Incorporation - LLP (Limited liability partnership) registration up to 1Lac Contribution (excluding Stamp Duty)' => [
                ['7521','Government Fees',661.00],
            ],
            '360||Company And LLP And NGO Incorporation - OPC REGISTRATION' => [
                ['1150','PUNJAB',11156.00],
                ['1142','MADHYA PRADESH',8681.00],
                ['1143','MAHARASTRA',2431.00],
                ['1144','MANIPUR',1391.00],
                ['1145','MEGHALAYA',1541.00],
                ['1146','MIZORAM',1391.00],
                ['1147','NAGALAND',1391.00],
                ['1148','ORISSA',1741.00],
                ['1149','PONDICHERRY',1641.00],
                ['1141','LAKSHADWEEP',2656.00],
                ['1151','RAJASTHAN',6641.00],
                ['1152','SIKKIM',1131.00],
                ['1153','TAMIL NADU',1651.00],
                ['1154','TELANGANA',2651.00],
                ['1155','TRIPURA',1391.00],
                ['1156','UTTAR PRADESH',2141.00],
                ['1157','UTTARAKHAND',2141.00],
                ['1158','WEST BENGAL',1501.00],
                ['1132','DELHI',1491.00],
                ['1124','ANDHRA PRADESH',2651.00],
                ['1125','ARUNACHAL PRADESH',1841.00],
                ['1126','ASSAM',1656.00],
                ['1127','BIHAR',2731.00],
                ['1128','CHANDIGARH',2634.00],
                ['1129','CHHATISGARH',2641.00],
                ['1130','DADAR NAGAR HAVELI',1172.00],
                ['1131','DAMAN AND DIU',2301.00],
                ['1123','ANDAMAN AND NICOBAR ISLAND',1651.00],
                ['1133','GOA',2331.00],
                ['1134','GUJARAT',1751.00],
                ['1135','HARYANA',1266.00],
                ['1136','HIMACHAL PRADESH',1254.00],
                ['1137','JAMMU AND KASHMIR',1441.00],
                ['1138','JHARKHAND',1304.00],
                ['1139','KARNATAKA',10179.00],
                ['1140','KERALA',4156.00],
            ],
            '361||Company And LLP And NGO Incorporation - SECTION 8 | NGO | Trust REGISTRATION' => [
                ['1186','PUNJAB',1131.00],
                ['1178','MADHYA PRADESH',8550.00],
                ['1179','MAHARASTRA',1131.00],
                ['1180','MANIPUR',1131.00],
                ['1181','MEGHALAYA',1131.00],
                ['1182','MIZORAM',1131.00],
                ['1183','NAGALAND',1131.00],
                ['1184','ORISSA',1131.00],
                ['1185','PONDICHERRY',1131.00],
                ['1177','LAKSHADWEEP',2530.00],
                ['1187','RAJASTHAN',6641.00],
                ['1188','SIKKIM',1131.00],
                ['1189','TAMIL NADU',1131.00],
                ['1190','TELANGANA',2530.00],
                ['1191','TRIPURA',1131.00],
                ['1192','UTTAR PRADESH',2100.00],
                ['1193','UTTARAKHAND',2100.00],
                ['1194','WEST BENGAL',1131.00],
                ['1168','DELHI',1131.00],
                ['1160','ANDHRA PRADESH',1530.00],
                ['1161','ARUNACHAL PRADESH',2530.00],
                ['1162','ASSAM',1131.00],
                ['1163','BIHAR',1131.00],
                ['1164','CHANDIGARH',1131.00],
                ['1165','CHHATISGARH',1131.00],
                ['1166','DADAR NAGAR HAVELI',1131.00],
                ['1167','DAMAN AND DIU',1131.00],
                ['1159','ANDAMAN AND NICOBAR ISLAND',1131.00],
                ['1169','GOA',1131.00],
                ['1170','GUJARAT',1131.00],
                ['1171','HARYANA',1131.00],
                ['1172','HIMACHAL PRADESH',1131.00],
                ['1173','JAMMU AND KASHMIR',1131.00],
                ['1174','JHARKHAND',1131.00],
                ['1175','KARNATAKA',1131.00],
                ['1176','KERALA',4025.00],
            ],
            '1880||Company And LLP And NGO Incorporation - NIDHI Registration (10 Lacs Authorized Capital)' => [
                ['8082','PUNJAB',11156.00],
                ['8074','MADHYA PRADESH',8681.00],
                ['8075','MAHARASTRA',2431.00],
                ['8076','MANIPUR',1391.00],
                ['8077','MEGHALAYA',1541.00],
                ['8078','MIZORAM',1391.00],
                ['8079','NAGALAND',1391.00],
                ['8080','ORISSA',1741.00],
                ['8081','PONDICHERRY',1641.00],
                ['8073','LAKSHADWEEP',2656.00],
                ['8083','RAJASTHAN',6641.00],
                ['8084','SIKKIM',1131.00],
                ['8085','TAMIL NADU',1651.00],
                ['8086','TELANGANA',2651.00],
                ['8087','TRIPURA',1391.00],
                ['8088','UTTAR PRADESH',2141.00],
                ['8089','UTTARAKHAND',2141.00],
                ['8090','WEST BENGAL',1501.00],
                ['8064','DELHI',1491.00],
                ['8056','ANDHRA PRADESH',2651.00],
                ['8057','ARUNACHAL PRADESH',1841.00],
                ['8058','ASSAM',1656.00],
                ['8059','BIHAR',2731.00],
                ['8060','CHANDIGARH',2634.00],
                ['8061','CHHATISGARH',2641.00],
                ['8062','DADAR NAGAR HAVELI',1172.00],
                ['8063','DAMAN AND DIU',2301.00],
                ['8055','ANDAMAN AND NICOBAR ISLAND',1651.00],
                ['8065','GOA',2331.00],
                ['8066','GUJARAT',1751.00],
                ['8067','HARYANA',1266.00],
                ['8068','HIMACHAL PRADESH',1254.00],
                ['8069','JAMMU AND KASHMIR',1441.00],
                ['8070','JHARKHAND',1304.00],
                ['8071','KARNATAKA',3151.00],
                ['8072','KERALA',4156.00],
            ],
            '1887||Company And LLP And NGO Incorporation - Name Approval Only for Company' => [
                ['1888','Government Fees for Name Approval (Company)',1000.00],
            ],
            '1899||Company And LLP And NGO Incorporation - Name Approval Only for LLP' => [
                ['1900','Government Fees for Name Approval (LLP)',200.00],
            ],
            '1965||Company And LLP And NGO Incorporation - Surrender of Private Limited and Section 8 (Only Surrender or Strike off)' => [
                ['1968','Government Fees of ROC',10000.00],
            ],
            '5662||Company And LLP And NGO Incorporation - FPO (AUTHORIZED CAPITAL 10.00 LACS)' => [
                ['7712','PUNJAB',11156.00],
                ['7704','MADHYA PRADESH',8681.00],
                ['7705','MAHARASTRA',2431.00],
                ['7706','MANIPUR',1391.00],
                ['7707','MEGHALAYA',1541.00],
                ['7708','MIZORAM',1391.00],
                ['7709','NAGALAND',1391.00],
                ['7710','ORISSA',1741.00],
                ['7711','PONDICHERRY',1641.00],
                ['7703','LAKSHADWEEP',2656.00],
                ['7713','RAJASTHAN',6641.00],
                ['7714','SIKKIM',1131.00],
                ['7715','TAMIL NADU',1651.00],
                ['7716','TELANGANA',2651.00],
                ['7717','TRIPURA',1391.00],
                ['7718','UTTAR PRADESH',2141.00],
                ['7719','UTTARAKHAND',2141.00],
                ['7720','WEST BENGAL',1501.00],
                ['7694','DELHI',1491.00],
                ['7686','ANDHRA PRADESH',2651.00],
                ['7687','ARUNACHAL PRADESH',1841.00],
                ['7688','ASSAM',1656.00],
                ['7689','BIHAR',2731.00],
                ['7690','CHANDIGARH',2634.00],
                ['7691','CHHATISGARH',2641.00],
                ['7692','DADAR NAGAR HAVELI',1172.00],
                ['7693','DAMAN AND DIU',2301.00],
                ['7685','ANDAMAN AND NICOBAR ISLAND',1651.00],
                ['7695','GOA',2331.00],
                ['7696','GUJARAT',1751.00],
                ['7697','HARYANA',1266.00],
                ['7698','HIMACHAL PRADESH',1254.00],
                ['7699','JAMMU AND KASHMIR',1441.00],
                ['7700','JHARKHAND',1304.00],
                ['7701','KARNATAKA',3151.00],
                ['7702','KERALA',4156.00],
            ],
        ];

        $batchId = Str::uuid();

        $insertedTotal = 0;
        $skippedBlocks = [];

        foreach ($blocks as $mainServiceKey => $children) {
            // Resolve main service either by code or by name (case-insensitive)
            $mainService = null;
            if (strpos($mainServiceKey, '||') !== false) {
                [$serviceCode, $serviceName] = explode('||', $mainServiceKey, 2);
                $serviceCode = trim($serviceCode);
                $serviceName = trim($serviceName);
                if ($serviceCode !== '') {
                    $mainService = MainService::where('service_code', $serviceCode)->first();
                }
                if (!$mainService && $serviceName !== '') {
                    $mainService = MainService::whereRaw('LOWER(name) = ?', [mb_strtolower($serviceName)])->first();
                }
            } else {
                $mainService = MainService::whereRaw('LOWER(name) = ?', [mb_strtolower(trim((string) $mainServiceKey))])->first();
            }

            if (!$mainService) {
                // Skip if the main service is not present; user seeded main_services already
                $skippedBlocks[] = is_array($mainServiceKey)
                    ? ($mainServiceKey['code'] ?? '?') . ' | ' . ($mainServiceKey['name'] ?? '?')
                    : (string) $mainServiceKey;
                continue;
            }

            $insertedForBlock = 0;
            foreach ($children as [$uniqueId, $serviceName, $fee]) {
                Service::updateOrCreate(
                    ['unique_id' => (string) $uniqueId],
                    [
                        'service_name'     => $serviceName,
                        'fee'              => (float) $fee,
                        'batch_id'         => $batchId,
                        'product_category' => null,
                        'obligation'       => '',
                        'frequency'        => '',
                        'maximum_tat'      => '',
                        'main_service_id'  => $mainService->id,
                        'main_service_name'=> $mainService->name,
                    ]
                );
                $insertedForBlock++;
                $insertedTotal++;
            }

            if (isset($this->command)) {
                $this->command->info("Seeded services for main: {$mainService->service_code} | {$mainService->name} -> {$insertedForBlock} rows");
            }
        }

        if (isset($this->command)) {
            $this->command->info("Total services upserted: {$insertedTotal}");
            if (!empty($skippedBlocks)) {
                $this->command->warn('Skipped blocks (main service not found):');
                foreach ($skippedBlocks as $sk) {
                    $this->command->line("- {$sk}");
                }
            }
        }
    }
}


