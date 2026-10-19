<style>
    /* Scoped menu UI refinements */
    .options {
        padding-left: 25px !important;
        background-color: #02010a !important;
        border-radius: 10px !important;
    }

    .options1 {
        padding-left: 20px !important;
        background-color: #04052e !important;
        border-radius: 10px !important;
    }

    .options2 {
        padding-left: 20px !important;
        background-color: #140152 !important;
        border-radius: 10px !important;
    }

    /* Make chevron smaller and add spacing */
    li>a .menu-arrow {
        margin-left: 0px;
        display: inline-flex;
        align-items: center;
    }

    li>a .menu-arrow i {
        font-size: 12px;
        opacity: 0.9;
    }

    /* Aggressive fix for active submenus visibility */
    .metismenu li.active > ul,
    .metismenu ul.in,
    .metismenu ul.show,
    .metismenu ul.mm-show {
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
    }

    /* Keep submenus hidden by default */
    .metismenu ul.collapse:not(.in):not(.show):not(.mm-show):not(.active) {
        display: none;
    }

    /* Ensure parent doesn't clip content during/after expansion */
    .metismenu li.active {
        overflow: visible !important;
    }
</style>

@php
    // $sidebarPerms is set in admin.blade.php; fallback for safety
    if (!isset($sidebarPerms)) {
        $sidebarPerms = auth()->user()->getSidebarPermissions();
    }
    $isAdmin = in_array('*', $sidebarPerms);
@endphp

@if ($isAdmin || in_array('taxation', $sidebarPerms))
    <li>
        <a href="javascript: void(0);">
            <i class="fas fa-landmark"></i>
            <span>Taxation</span>
            <span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span>
        </a>
        <ul class="nav-second-level options collapse">

            @if ($isAdmin || in_array('taxation.orders', $sidebarPerms))
                <li>
                    <a href="javascript: void(0);">
                        <span>Orders</span>
                        <span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span>
                    </a>
                    <ul class="nav-second-level options1 collapse">
                        <li>
                            <a href="javascript: void(0);">
                                New Order
                                <span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span>
                            </a>
                            <ul class="nav-second-level options2 collapse">
                                <!-- Amendment -->
                                <li>
                                    <a href="javascript: void(0);">
                                        Amendment
                                        <span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span>
                                    </a>
                                    <ul class="nav-second-level options collapse">
                                        <li><a
                                                href="{{ route('services.index', ['category' => 'amendment', 'subCategory' => 'gst']) }}">GST</a>
                                        </li>
                                        <li><a
                                                href="{{ route('services.index', ['category' => 'amendment', 'subCategory' => 'iec']) }}">IEC</a>
                                        </li>
                                        <li><a
                                                href="{{ route('services.index', ['category' => 'amendment', 'subCategory' => 'msme']) }}">MSME</a>
                                        </li>
                                        <li><a
                                                href="{{ route('services.index', ['category' => 'amendment', 'subCategory' => 'fssai']) }}">FSSAI/FOSCOS</a>
                                        </li>
                                    </ul>
                                </li>

                                <!-- Audit -->
                                <li>
                                    <a href="javascript: void(0);">
                                        Audit
                                        <span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span>
                                    </a>
                                    <ul class="nav-second-level options2 collapse">
                                        <li><a
                                                href="{{ route('services.index', ['category' => 'audit', 'subCategory' => 'gst']) }}">GST</a>
                                        </li>
                                        <li><a
                                                href="{{ route('services.index', ['category' => 'audit', 'subCategory' => 'income_tax']) }}">Income
                                                Tax</a></li>
                                        <li><a
                                                href="{{ route('services.index', ['category' => 'audit', 'subCategory' => 'trust']) }}">Trust</a>
                                        </li>
                                    </ul>
                                </li>

                                <!-- Company -->
                                <li>
                                    <a href="javascript: void(0);">
                                        Company
                                        <span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span>
                                    </a>
                                    <ul class="nav-second-level options2 collapse">
                                        <li><a
                                                href="{{ route('services.index', ['category' => 'company', 'subCategory' => 'private_limited']) }}">Private
                                                Limited</a></li>
                                        <li><a
                                                href="{{ route('services.index', ['category' => 'company', 'subCategory' => 'public_limited']) }}">Public
                                                Limited</a></li>
                                        <li><a
                                                href="{{ route('services.index', ['category' => 'company', 'subCategory' => 'fpo']) }}">Farmer
                                                Producer Organizations(FPO)</a></li>
                                        <li><a
                                                href="{{ route('services.index', ['category' => 'company', 'subCategory' => 'llp']) }}">Limited
                                                Liability Partnership (LLP)</a></li>
                                        <li><a
                                                href="{{ route('services.index', ['category' => 'company', 'subCategory' => 'opc']) }}">One
                                                Person Company (OPC)</a></li>
                                        <li><a
                                                href="{{ route('services.index', ['category' => 'company', 'subCategory' => 'nidhi']) }}">National
                                                Initiative For Developing and Harnessing innovation/ NIDHI</a></li>
                                        <li><a
                                                href="{{ route('services.index', ['category' => 'company', 'subCategory' => 'section_8']) }}">Section
                                                8 Company</a></li>
                                        <li><a
                                                href="{{ route('services.index', ['category' => 'company', 'subCategory' => 'insurance_broker']) }}">Insurance
                                                Broker/DB</a></li>
                                        <li><a
                                                href="{{ route('services.index', ['category' => 'company', 'subCategory' => 'insurance_marketing_firm']) }}">Insurance
                                                Marketing Firm/IMF</a></li>
                                    </ul>
                                </li>

                                <!-- Intellectual Property -->
                                <li>
                                    <a href="javascript: void(0);">
                                        Intellectual Property
                                        <span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span>
                                    </a>
                                    <ul class="nav-second-level options collapse">
                                        <li><a
                                                href="{{ route('services.index', ['category' => 'IntellectualProperty', 'subCategory' => 'trademark']) }}">Trademark</a>
                                        </li>
                                    </ul>
                                </li>

                                <!-- Licences -->
                                <li>
                                    <a href="javascript: void(0);">
                                        Licences
                                        <span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span>
                                    </a>
                                    <ul class="nav-second-level options2 collapse">
                                        <li><a
                                                href="{{ route('services.index', ['category' => 'Licences', 'subCategory' => 'fssai']) }}">FSSAI
                                                OR FOSCOS</a></li>
                                        <li><a
                                                href="{{ route('services.index', ['category' => 'Licences', 'subCategory' => 'health']) }}">HEALTH
                                                LICENCE</a></li>
                                        <li><a
                                                href="{{ route('services.index', ['category' => 'Licences', 'subCategory' => 'labour']) }}">LABOUR
                                                LICENCE</a></li>
                                        <li><a
                                                href="{{ route('services.index', ['category' => 'Licences', 'subCategory' => 'trade']) }}">TRADE
                                                LICENCE</a></li>
                                    </ul>
                                </li>

                                <!-- Marketing Material -->
                                <li>
                                    <a href="javascript: void(0);">
                                        Marketing Material
                                        <span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span>
                                    </a>
                                    <ul class="nav-second-level options collapse">
                                        <li><a
                                                href="{{ route('services.index', ['category' => 'MarketingMaterial', 'subCategory' => 'designing']) }}">Designing</a>
                                        </li>
                                    </ul>
                                </li>

                                <!-- Miscellaneous -->
                                <li>
                                    <a href="javascript: void(0);">
                                        Miscellaneous
                                        <span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span>
                                    </a>
                                    <ul class="nav-second-level options collapse">
                                        <li><a
                                                href="{{ route('services.index', ['category' => 'Miscellaneous', 'subCategory' => 'advance_tax']) }}">ADVANCE
                                                TAX</a></li>
                                        <li><a
                                                href="{{ route('services.index', ['category' => 'Miscellaneous', 'subCategory' => 'ca_assistance']) }}">CA
                                                ASSISTANCE</a></li>
                                        <li><a
                                                href="{{ route('services.index', ['category' => 'Miscellaneous', 'subCategory' => 'financials']) }}">FINANCIALS</a>
                                        </li>
                                        <li><a
                                                href="{{ route('services.index', ['category' => 'Miscellaneous', 'subCategory' => 'gst_composition']) }}">GST
                                                COMPOSITION TO REGULAR</a></li>
                                        <li><a
                                                href="{{ route('services.index', ['category' => 'Miscellaneous', 'subCategory' => 'labour_card']) }}">LABOUR
                                                CARD</a></li>
                                        <li><a
                                                href="{{ route('services.index', ['category' => 'Miscellaneous', 'subCategory' => 'logo']) }}">LOGO</a>
                                        </li>
                                        <li><a
                                                href="{{ route('services.index', ['category' => 'Miscellaneous', 'subCategory' => 'pan_aadhaar']) }}">PAN
                                                AND AADHAAR LINK</a></li>
                                        <li><a
                                                href="{{ route('services.index', ['category' => 'Miscellaneous', 'subCategory' => 'partnership']) }}">PARTNERSHIP</a>
                                        </li>
                                        <li><a
                                                href="{{ route('services.index', ['category' => 'Miscellaneous', 'subCategory' => 'pps_services']) }}">PPS
                                                Services (Plug and Play Services)</a></li>
                                        <li><a
                                                href="{{ route('services.index', ['category' => 'Miscellaneous', 'subCategory' => 'recall_plan']) }}">RECALL
                                                PLAN FOR FSSAI OR FOSCOS</a></li>
                                        <li><a
                                                href="{{ route('services.index', ['category' => 'Miscellaneous', 'subCategory' => 'revocation_of_gst']) }}">REVOCATION
                                                OF GST</a></li>
                                        <li><a
                                                href="{{ route('services.index', ['category' => 'Miscellaneous', 'subCategory' => 'tds']) }}">TDS</a>
                                        </li>
                                    </ul>
                                </li>

                                <!-- Net Worth -->
                                <li>
                                    <a href="javascript: void(0);">
                                        Net Worth
                                        <span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span>
                                    </a>
                                    <ul class="nav-second-level options collapse">
                                        <li><a
                                                href="{{ route('services.index', ['category' => 'NetWorth', 'subCategory' => 'net_worth']) }}">NET
                                                WORTH</a></li>
                                    </ul>
                                </li>

                                <!-- Notices & Litigations -->
                                <li>
                                    <a href="javascript: void(0);">
                                        Notices & Litigations
                                        <span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span>
                                    </a>
                                    <ul class="nav-second-level options collapse">
                                        <li><a
                                                href="{{ route('services.index', ['category' => 'NoticesLitigations', 'subCategory' => 'income_tax']) }}">INCOME
                                                TAX</a></li>
                                    </ul>
                                </li>

                                <!-- Project Report & CMA -->
                                <li>
                                    <a href="javascript: void(0);">
                                        Project Report & CMA
                                        <span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span>
                                    </a>
                                    <ul class="nav-second-level options collapse">
                                        <li><a
                                                href="{{ route('services.index', ['category' => 'ProjectReport', 'subCategory' => 'project_report_cma']) }}">PROJECT
                                                REPORT AND/OR CMA</a></li>
                                    </ul>
                                </li>

                                <!-- Registration & Certification -->
                                <li>
                                    <a href="javascript: void(0);">
                                        Registration & Certification
                                        <span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span>
                                    </a>
                                    <ul class="nav-second-level options collapse">
                                        <li><a
                                                href="{{ route('services.index', ['category' => 'Registration', 'subCategory' => '12a_80g']) }}">12A
                                                and 80G</a></li>
                                        <li><a
                                                href="{{ route('services.index', ['category' => 'Registration', 'subCategory' => '80g']) }}">80G</a>
                                        </li>
                                        <li><a
                                                href="{{ route('services.index', ['category' => 'Registration', 'subCategory' => 'apeda']) }}">APEDA</a>
                                        </li>
                                        <li><a
                                                href="{{ route('services.index', ['category' => 'Registration', 'subCategory' => 'brn']) }}">Business
                                                Registration Number(BRN)</a></li>
                                        <li><a
                                                href="{{ route('services.index', ['category' => 'Registration', 'subCategory' => 'darpan']) }}">DARPAN</a>
                                        </li>
                                        <li><a
                                                href="{{ route('services.index', ['category' => 'Registration', 'subCategory' => 'dsc']) }}">Digital
                                                Signature Certificate(DSC)</a></li>
                                        <li><a
                                                href="{{ route('services.index', ['category' => 'Registration', 'subCategory' => 'dpcc']) }}">DPCC(Delhi
                                                Pollution Control)</a></li>
                                        <li><a
                                                href="{{ route('services.index', ['category' => 'Registration', 'subCategory' => 'eway']) }}">E-WAY</a>
                                        </li>
                                        <li><a
                                                href="{{ route('services.index', ['category' => 'Registration', 'subCategory' => 'epch']) }}">EPCH</a>
                                        </li>
                                        <li><a
                                                href="{{ route('services.index', ['category' => 'Registration', 'subCategory' => 'fassai_or_foscos']) }}">FSSAI
                                                OR FOSCOSS</a></li>
                                        <li><a
                                                href="{{ route('services.index', ['category' => 'Registration', 'subCategory' => 'gem']) }}">GEM</a>
                                        </li>
                                        <li><a
                                                href="{{ route('services.index', ['category' => 'Registration', 'subCategory' => 'goods_and_services_tax']) }}">GOODS
                                                AND SERVICES TAX (GST)</a></li>
                                        <li><a
                                                href="{{ route('services.index', ['category' => 'Registration', 'subCategory' => 'gumasta']) }}">GUMASTA</a>
                                        </li>
                                        <li><a
                                                href="{{ route('services.index', ['category' => 'Registration', 'subCategory' => 'import_export_code']) }}">IMPORT
                                                EXPORT CODE (IEC)</a></li>
                                        <li><a
                                                href="{{ route('services.index', ['category' => 'Registration', 'subCategory' => 'iso']) }}">ISO</a>
                                        </li>
                                        <li><a
                                                href="{{ route('services.index', ['category' => 'Registration', 'subCategory' => 'lut']) }}">LUT</a>
                                        </li>
                                        <li><a
                                                href="{{ route('services.index', ['category' => 'Registration', 'subCategory' => 'micro_small_medium_enterprise']) }}">MICRO,
                                                SMALL AND MEDIUM ENTERPRISE (MSME)</a></li>
                                        <li><a
                                                href="{{ route('services.index', ['category' => 'Registration', 'subCategory' => 'niti_ayog']) }}">NITI
                                                AYOG</a></li>
                                        <li><a
                                                href="{{ route('services.index', ['category' => 'Registration', 'subCategory' => 'pan_card']) }}">PAN
                                                CARD</a></li>
                                        <li><a
                                                href="{{ route('services.index', ['category' => 'Registration', 'subCategory' => 'pf_esi']) }}">PF
                                                AND ESI</a></li>
                                        <li><a
                                                href="{{ route('services.index', ['category' => 'Registration', 'subCategory' => 'ptax_prtc']) }}">PTAX
                                                OR PRTC</a></li>
                                        <li><a
                                                href="{{ route('services.index', ['category' => 'Registration', 'subCategory' => 'shop']) }}">SHOP</a>
                                        </li>
                                        <li><a
                                                href="{{ route('services.index', ['category' => 'Registration', 'subCategory' => 'startup']) }}">STARTUP</a>
                                        </li>
                                        <li><a
                                                href="{{ route('services.index', ['category' => 'Registration', 'subCategory' => 'tan_card']) }}">TAN
                                                CARD</a></li>
                                    </ul>
                                </li>

                                <!-- Renewals -->
                                <li>
                                    <a href="javascript: void(0);">
                                        Renewals
                                        <span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span>
                                    </a>
                                    <ul class="nav-second-level options collapse">
                                        <li><a
                                                href="{{ route('services.index', ['category' => 'Renewals', 'subCategory' => 'epch_renewal']) }}">EPCH
                                                Renewal</a></li>
                                        <li><a
                                                href="{{ route('services.index', ['category' => 'Renewals', 'subCategory' => 'fssai_renewal']) }}">FSSAI
                                                OR FOSCOS</a></li>
                                    </ul>
                                </li>

                                <!-- Returns -->
                                <li>
                                    <a href="javascript: void(0);">
                                        Returns
                                        <span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span>
                                    </a>
                                    <ul class="nav-second-level options collapse">
                                        <li><a
                                                href="{{ route('services.index', ['category' => 'Returns', 'subCategory' => 'esi']) }}">ESI</a>
                                        </li>
                                        <li><a
                                                href="{{ route('services.index', ['category' => 'Returns', 'subCategory' => 'gst']) }}">GST</a>
                                        </li>
                                        <li><a
                                                href="{{ route('services.index', ['category' => 'Returns', 'subCategory' => 'itr']) }}">ITR</a>
                                        </li>
                                        <li><a
                                                href="{{ route('services.index', ['category' => 'Returns', 'subCategory' => 'itr_u']) }}">ITR-U</a>
                                        </li>
                                        <li><a
                                                href="{{ route('services.index', ['category' => 'Returns', 'subCategory' => 'pf']) }}">PF</a>
                                        </li>
                                        <li><a
                                                href="{{ route('services.index', ['category' => 'Returns', 'subCategory' => 'pf_esi']) }}">PF
                                                AND ESI</a></li>
                                        <li><a
                                                href="{{ route('services.index', ['category' => 'Returns', 'subCategory' => 'tds']) }}">TDS</a>
                                        </li>
                                    </ul>
                                </li>

                                <!-- ROC -->
                                <li>
                                    <a href="javascript: void(0);">
                                        ROC
                                        <span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span>
                                    </a>
                                    <ul class="nav-second-level options collapse">
                                        <li><a
                                                href="{{ route('services.index', ['category' => 'Roc', 'subCategory' => 'compliance']) }}">COMPLIANCE</a>
                                        </li>
                                        <li><a
                                                href="{{ route('services.index', ['category' => 'Roc', 'subCategory' => 'conversion']) }}">CONVERSION</a>
                                        </li>
                                        <li><a
                                                href="{{ route('services.index', ['category' => 'Roc', 'subCategory' => 'name_approval']) }}">NAME
                                                APPROVAL</a></li>
                                    </ul>
                                </li>

                                <!-- Surrender & Closure -->
                                <li>
                                    <a href="javascript: void(0);">
                                        Surrender & Closure
                                        <span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span>
                                    </a>
                                    <ul class="nav-second-level options collapse">
                                        <li><a
                                                href="{{ route('services.index', ['category' => 'Surrender', 'subCategory' => 'company']) }}">COMPANY</a>
                                        </li>
                                        <li><a
                                                href="{{ route('services.index', ['category' => 'Surrender', 'subCategory' => 'gst']) }}">GST</a>
                                        </li>
                                        <li><a
                                                href="{{ route('services.index', ['category' => 'Surrender', 'subCategory' => 'pan_card']) }}">PAN
                                                CARD</a></li>
                                        <li><a
                                                href="{{ route('services.index', ['category' => 'Surrender', 'subCategory' => 'pf']) }}">PF</a>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                        <li><a href="{{ route('orders.index') }}">History</a></li>
                        <li><a href="{{ route('orders.index.simple') }}">Leads</a></li>
                        <li><a href="#">Download All Orders History</a></li>
                    </ul>
                </li>
            @endif {{-- end taxation.orders --}}

            @if ($isAdmin || in_array('taxation.service_list', $sidebarPerms))
                <li>
                    <a href="javascript: void(0);">
                        <span>Service List</span>
                        <span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span>
                    </a>
                    <ul class="nav-second-level options collapse">
                        <li><a href="{{ url('admin/main-services') }}">Main Service</a></li>


                        <!--<li><a href="{{ url('/addservice') }}">Add Service</a></li>-->
                        @php
                            $user = Auth::user();
                        @endphp

                        @if ($user && $user->user_type === 'admin')
                            <li><a href="{{ url('/addservice') }}">Add Service</a></li>
                        @endif


                        <li><a href="{{ url('/viewservice') }}">View Service</a></li>
                        {{-- <li><a href="{{ url('/importservice') }}">Import Service</a></li> --}}
                        <li><a href="{{ url('/download') }}">Download</a></li>
                    </ul>
                </li>
            @endif {{-- end taxation.service_list --}}

            @if ($isAdmin || in_array('taxation.action_required', $sidebarPerms))
                <li>
                    <a href="javascript: void(0);">
                        <span>Action Required</span>
                        <span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span>
                    </a>
                    <ul class="nav-second-level options collapse">
                        <li><a href="#">Approval Pending</a></li>
                        <li><a href="#">Document Pending</a></li>
                    </ul>
                </li>
            @endif {{-- end taxation.action_required --}}

        </ul>
    </li>
@endif {{-- end taxation main --}}
