<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>{{ get_option('site_title', config('app.name')) }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- App favicon -->
    <!--<link rel="shortcut icon" href="{{ get_favicon() }}">-->

    <!-- DataTables -->
    <link href="{{ asset('backend/plugins/datatable/datatables.min.css') }}" rel="stylesheet" type="text/css" />

    <link href="{{ asset('backend/plugins/dropify/css/dropify.min.css') }}" rel="stylesheet">
    <link href="{{ asset('backend/plugins/sweet-alert2/css/sweetalert2.min.css') }}" rel="stylesheet"
        type="text/css">
    <link href="{{ asset('backend/plugins/animate/animate.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('backend/plugins/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('backend/plugins/daterangepicker/daterangepicker.css') }}" rel="stylesheet" />
    <link href="{{ asset('backend/plugins/jquery-toast-plugin/jquery.toast.min.css') }}" rel="stylesheet" />

    <!-- App Css -->
    <link rel="stylesheet" href="{{ asset('backend/plugins/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/assets/css/fontawesome.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/assets/css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/plugins/metisMenu/metisMenu.css') }}">

    <!-- Others css -->
    <link rel="stylesheet" href="{{ asset('backend/assets/css/typography.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/assets/css/default-css.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/assets/css/styles.css?v=1.3') }}">
    <link rel="stylesheet" href="{{ asset('backend/assets/css/responsive.css?v=1.0') }}">

    <!-- Modernizr -->
    <script src="{{ asset('backend/assets/js/vendor/modernizr-3.6.0.min.js') }}"></script>

    @if (get_option('backend_direction') == 'rtl')
        <link rel="stylesheet" href="{{ asset('backend/plugins/bootstrap/css/bootstrap-rtl.min.css') }}">
        <link rel="stylesheet" href="{{ asset('backend/assets/css/rtl/style.css?v=1.0') }}">
    @endif

    @include('layouts.others.languages')

    {{-- ============================================================ --}}
    {{-- ✅ BRANCH DROPDOWN: SCROLLER + SEARCH (NEWLY ADDED - SAFE)   --}}
    {{-- ============================================================ --}}
    <style>
        /* Branch dropdown: scrollable + search */
        .branch-dropdown-menu {
            max-height: 320px;
            overflow-y: auto;
            min-width: 260px;
            padding: 0;
            border-radius: 8px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        }

        .branch-search-wrapper {
            position: sticky;
            top: 0;
            background: #fff;
            padding: 8px 10px;
            border-bottom: 1px solid #e9ecef;
            z-index: 10;
        }

        .branch-search-wrapper input {
            width: 100%;
            padding: 6px 10px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            font-size: 13px;
            outline: none;
        }

        .branch-search-wrapper input:focus {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, .25);
        }

        .branch-dropdown-menu .dropdown-item {
            padding: 6px 14px;
            font-size: 13px;
            white-space: nowrap;
        }

        .branch-dropdown-menu .dropdown-item:hover {
            background-color: #f1f3f5;
        }

        /* Custom scrollbar (Chrome / Edge / Safari) */
        .branch-dropdown-menu::-webkit-scrollbar {
            width: 6px;
        }
        .branch-dropdown-menu::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }
        .branch-dropdown-menu::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 3px;
        }
        .branch-dropdown-menu::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        /* Firefox scrollbar */
        .branch-dropdown-menu {
            scrollbar-width: thin;
            scrollbar-color: #c1c1c1 #f1f1f1;
        }
    </style>
</head>

<body>
    <!-- Main Modal -->
    <div id="main_modal" class="modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title mt-0 text-white"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="alert alert-danger d-none m-3"></div>
                <div class="alert alert-secondary d-none m-3"></div>
                <div class="modal-body overflow-hidden"></div>
            </div>
        </div>
    </div>


    <!-- Secondary Modal -->
    <div id="secondary_modal" class="modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-dark">
                    <h5 class="modal-title mt-0 text-white"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="alert alert-danger d-none m-3"></div>
                <div class="alert alert-secondary d-none m-3"></div>
                <div class="modal-body overflow-hidden"></div>
            </div>
        </div>
    </div>

    <!-- Preloader area start -->
    <div id="preloader"></div>
    <!-- Preloader area end -->

    <div class="page-container">
        <!-- sidebar menu area start -->
        <div class="sidebar-menu">

            <div class="sidebar-header text-center">
                <a href="{{ route('dashboard.index') }}">
                    <img src="{{ get_logo() }}" style="mix-blend-mode: plus-lighter;" />
                    <!--<h4 class="text-white ml-1 d-inline-block">{{ get_option('site_title', 'Bewin Services') }}</h4>-->
                </a>
            </div>

            <div class="main-menu">
                <div class="menu-inner">
                    <nav>
                        <ul class="metismenu" id="menu">
                            @include('layouts.menus.' . Auth::user()->user_type)
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
        <!-- sidebar menu area end -->


        <!-- main content area start -->
        <div class="main-content">
            <!-- header area start -->
            <div class="header-area">
                <div class="row align-items-center">
                    <!-- nav and search button -->
                    <div class="col-lg-6 col-4 clearfix rtl-2">
                        <button type="button" class="nav-btn float-left" aria-label="Toggle sidebar" aria-expanded="true">
                            <span aria-hidden="true"></span>
                            <span aria-hidden="true"></span>
                            <span aria-hidden="true"></span>
                        </button>
                    </div>


                    <!-- profile info & task notification -->
                    <div class="col-lg-6 col-8 clearfix rtl-1">
                        <ul class="notification-area float-right">
                         {{--   <li class="d-none d-md-inline-block">
                                <div class="dropdown">
                                    <a class="dropdown-toggle" type="button" id="selectLanguage"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        {{ session('language') == '' ? get_option('language') : session('language') }}
                                        <i class="fa fa-angle-down"></i>
                                    </a>
                                    <div class="dropdown-menu" aria-labelledby="selectLanguage">
                                        @foreach (get_language_list() as $language)
                                            <a class="dropdown-item"
                                                href="{{ route('switch_language') }}?language={{ $language }}">{{ $language }}</a>
                                        @endforeach
                                    </div>
                                </div>
                            </li> --}}

                            @if (Auth::user()->user_type == 'customer')
                                @php $notificatioCount = Auth::user()->member->unreadNotifications->count(); @endphp
                                <li class="dropdown d-none d-sm-inline-block">
                                    <i class="ti-bell dropdown-toggle" data-toggle="dropdown">
                                        <span>{{ $notificatioCount }}</span>
                                    </i>
                                    <div class="dropdown-menu bell-notify-box notify-box">
                                        <span
                                            class="notify-title">{{ _lang('You have') . ' ' . $notificatioCount . ' ' . _lang('new notifications') }}</span>
                                        <div class="nofity-list">
                                            @foreach (Auth::user()->member->notifications->take(15) as $notification)
                                                <a href="{{ route('profile.show_notification', $notification->id) }}"
                                                    class="ajax-modal-2 notify-item {{ $notification->read_at == null ? 'unread-notification' : '' }}"
                                                    data-title="{{ _lang('Notification Details') }}">
                                                    <div class="notify-thumb">
                                                        <img src="{{ profile_picture() }}">
                                                    </div>
                                                    <div class="notify-text">
                                                        <span>{{ $notification->data['message'] }}</span><br>
                                                        <span>{{ $notification->created_at->diffForHumans() }}</span>
                                                    </div>
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                </li>
                            @endif


                            <li class="nav-item dropdown" style="margin-right: 20px;">
                                <div class="user-profile dropdown" style="background-color: white;">
                                    <h4 class="user-name dropdown-toggle d-flex align-items-center"
                                        data-toggle="dropdown" role="button" aria-haspopup="true"
                                        aria-expanded="false">
                                        <img class="avatar user-thumb rounded-circle mr-2"
                                            src="{{ profile_picture() }}" alt="avatar" width="40"
                                            height="40">
                                        <i class="fa fa-angle-down text-dark"></i>
                                    </h4>

                                    <div class="dropdown-menu p-4 profile-dropdown-panel shadow-lg "
                                        style="min-width: 320px; right: 0; border:1px solid black;">
                                        {{-- Profile Header --}}
                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                            <div>
                                                <small class="text-muted d-block mb-1">Joined on <br>
                                                    {{ \Carbon\Carbon::parse(Auth::user()->created_at)->format('d M Y') }}</small>
                                                <img src="{{ profile_picture() }}" class="rounded-circle"
                                                    width="60" height="60">
                                            </div>


                                            {{-- Wallet Section --}}
                                            <div class="text-right">
                                                <span class="d-block text-muted mb-1" style="font-weight: 500;">
                                                    Wallet <span class="text-dark">₹
                                                        {{ Auth::user()->wallet_amount ?? '0.00' }}</span>
                                                </span>
                                                <a href="{{ route('walle.showwww') }}"
                                                    class="badge badge-primary p-2" style="font-size: 13px;">
                                                    <span style="color: black;">Main</span> <i
                                                        class="fa fa-arrow-right ml-1"></i>
                                                </a>
                                            </div>
                                        </div>

                                        <hr>

                                        {{-- User Info --}}
                                        <div class="text-center mb-3">
                                            <h6 class="mb-1">{{ Auth::user()->membership_id }}
                                                {{ Auth::user()->name }} <small
                                                    class="text-muted">{{ ucfirst(Auth::user()->mobile_number) }}
                                                </small></h6>

                                        </div>

                                        <hr>
                                        {{-- RM Info --}}
                                        @if (Auth::user()->assigned_manager_id)
                                            <div class="text-center mb-3">
                                                <strong>RM</strong><br>
                                                <small class="text-muted">
                                                    {{-- <strong>ID :</strong> {{ Auth::user()->assigned_manager_id }} <br> --}}
                                                    <strong>ID :</strong> {{ Auth::user()->assignedManagerDetails->user_id ?? 'N/A' }} <br>

                                                    {{ Auth::user()->assignedManagerDetails->name ?? 'N/A' }} <br>
                                                    {{-- @php
                                                  dd(Auth::user()->assignedManagerDetails);
                                                  @endphp --}}
                                                    {{ Auth::user()->assignedManagerDetails->mobile_number
                                                        ? '+91' . Auth::user()->assignedManagerDetails->mobile_number
                                                        : 'N/A' }}
                                                </small>
                                            </div>
                                        @endif
                                        <hr>

                                        {{-- Menu Links --}}


                                        <a class="dropdown-item" href="{{ url('/user_profile') }}">
                                            <i class="fas fa-user-circle"></i> Profile
                                        </a>
                                        <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                            <i class="fas fa-user-cog"></i> Profile Settings
                                        </a>

                                        <a class="dropdown-item" href="{{ route('agreement.view') }}"
                                            target="_blank">
                                            <i class="fa fa-file-pdf"></i> Agreement
                                        </a>


                                        <a class="dropdown-item" href="{{ route('certificate.view') }}"
                                            target="_blank">
                                            <i class="fa fa-file-pdf"></i> Certificate
                                        </a>

                                        <a class="dropdown-item" href="#" target="_blank">
                                            <i class="fa fa-file-pdf"></i> Loan Agreement
                                        </a>

                                        <a class="dropdown-item" href="{{ route('loan_agreement.view') }}"
                                            target="_blank">
                                            <i class="fa fa-file-pdf"></i> Letter Of Authorisation

                                        </a>

                                        <a class="dropdown-item" href="{{ route('profile.change_password') }}">
                                            <i class="ti-exchange-vertical mr-2"></i> Change Password
                                        </a>

                                        @if (auth()->user()->user_type == 'admin')
                                            <a class="dropdown-item" href="{{ route('settings.update_settings') }}">
                                                <i class="ti-settings mr-2"></i> System Settings
                                            </a>
                                        @endif

                                        {{-- Logout --}}
                                        <a class="btn btn-outline-danger btn-sm btn-block mt-3"
                                            href="{{ route('logout') }}">
                                            <i class="ti-power-off mr-1"></i> Logout
                                        </a>

                                        {{-- Footer --}}
                                        <div class="text-center mt-3">
                                            <small><a href="#">Privacy Policy</a> | <a href="#">Terms of
                                                    Use</a></small>
                                        </div>
                                    </div>
                                </div>
                            </li>

                        </ul>

                    </div>
                </div>
            </div><!-- header area end -->

            <!-- page title area start -->
            @if (Request::is('dashboard'))
                <div class="page-title-area mb-3">
                    <div class="row align-items-center py-3">
                        <div class="col-sm-12">
                            <div class="breadcrumbs-area clearfix">
                                <h6 class="page-title float-left">{{ _lang('Dashboard') }}</h6>

                                {{-- ============================================================ --}}
                                {{-- ✅ BRANCH SWITCHER (UPDATED: id + class + search box added) --}}
                                {{-- ============================================================ --}}
                                @if (auth()->user()->user_type == 'admin')
                                    <div class="dropdown float-right">
                                        <a class="dropdown-toggle btn btn-dark btn-xs" type="button"
                                            id="branchDropdown" data-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false">
                                            {{ session('branch') == '' ? _lang('All Branch') : session('branch') }}
                                        </a>
                                        <div class="dropdown-menu branch-dropdown-menu" aria-labelledby="branchDropdown">
                                            <div class="branch-search-wrapper">
                                                <input type="text" class="branch-search-input" placeholder="🔍 Search branch..." autocomplete="off">
                                            </div>
                                            <a class="dropdown-item"
                                                href="{{ route('switch_branch') }}">{{ _lang('All Branch') }}</a>
                                            <!--<a class="dropdown-item" href="{{ route('switch_branch') }}?branch_id=default&branch={{ get_option('default_branch_name', 'Main Branch') }}">{{ get_option('default_branch_name', 'Main Branch') }}</a>-->
                                            @foreach (\App\Models\Branch::all() as $branch)
                                                <a class="dropdown-item"
                                                    href="{{ route('switch_branch') }}?branch_id={{ $branch->id }}&branch={{ $branch->name }}">{{ $branch->name }}</a>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                                <!--@include('layouts.others.breadcrumbs')-->
                            </div>
                        </div>
                    </div>
                </div><!-- page title area end -->
            @endif

            <div class="main-content-inner {{ !Request::is('dashboard') ? 'mt-4' : '' }}">
                <div class="row">
                    <div class="{{ isset($alert_col) ? $alert_col : 'col-lg-12' }}">
                        <div class="alert alert-success alert-dismissible" id="main_alert" role="alert">
                            <button type="button" id="close_alert" class="close">
                                <span aria-hidden="true"><i class="far fa-times-circle"></i></span>
                            </button>
                            <span class="msg"></span>
                        </div>
                    </div>
                </div>

                @yield('content')
            </div><!--End main content Inner-->

        </div><!--End main content-->

    </div><!--End Page Container-->

    <!-- jQuery  -->
    <script src="{{ asset('backend/assets/js/vendor/jquery-3.6.1.min.js') }}"></script>
    <script src="{{ asset('backend/assets/js/popper.min.js') }}"></script>
    <script src="{{ asset('backend/plugins/bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('backend/plugins/metisMenu/metisMenu.min.js') }}"></script>
    <script src="{{ asset('backend/plugins/slimscroll/jquery.slimscroll.min.js') }}"></script>

    <script src="{{ asset('backend/assets/js/print.js') }}"></script>
    <script src="{{ asset('backend/plugins/pace/pace.min.js') }}"></script>
    <script src="{{ asset('backend/plugins/moment/moment.js') }}"></script>

    <!-- Datatable js -->
    <script src="{{ asset('backend/plugins/datatable/datatables.min.js') }}"></script>

    <script src="{{ asset('backend/plugins/dropify/js/dropify.min.js') }}"></script>
    <script src="{{ asset('backend/plugins/sweet-alert2/js/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('backend/plugins/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('backend/plugins/daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{ asset('backend/plugins/tinymce/tinymce.min.js') }}"></script>
    <script src="{{ asset('backend/plugins/parsleyjs/parsley.min.js') }}"></script>
    <script src="{{ asset('backend/plugins/jquery-toast-plugin/jquery.toast.min.js') }}"></script>

    <!-- App js -->
    <script src="{{ asset('backend/assets/js/scripts.js?v=1.4') }}"></script>

    <script type="text/javascript">
        (function($) {

            "use strict";

            //Show Success Message
            @if (Session::has('success'))
                $("#main_alert > span.msg").html(" {{ session('success') }} ");
                $("#main_alert").addClass("alert-success").removeClass("alert-danger");
                $("#main_alert").css('display', 'block');
            @endif

            //Show Single Error Message
            @if (Session::has('error'))
                $("#main_alert > span.msg").html(" {{ session('error') }} ");
                $("#main_alert").addClass("alert-danger").removeClass("alert-success");
                $("#main_alert").css('display', 'block');
            @endif


            @php $i = 0; @endphp

            @foreach ($errors->all() as $error)
                @if ($loop->first)
                    $("#main_alert > span.msg").html("<i class='ti-alert'></i>&nbsp;{{ $error }} ");
                    $("#main_alert").addClass("alert-danger").removeClass("alert-success");
                @else
                    $("#main_alert > span.msg").append("<br><i class='ti-alert'></i>&nbsp;{{ $error }} ");
                @endif

                @if ($loop->last)
                    $("#main_alert").css('display', 'block');
                @endif

                @if (isset($errors->keys()[$i]))
                    var name = "{{ $errors->keys()[$i] }}";

                    $("input[name='" + name + "']").addClass('error is-invalid');
                    $("select[name='" + name + "'] + span").addClass('error is-invalid');

                    $("input[name='" + name + "'], select[name='" + name + "']").parent().append(
                        "<span class='v-error'>{{ $error }}</span>");
                @endif
                @php $i++; @endphp
            @endforeach

        })(jQuery);
    </script>

    {{-- ============================================================ --}}
    {{-- ✅ BRANCH DROPDOWN: SEARCH SCRIPT (NEWLY ADDED - SAFE)       --}}
    {{-- ============================================================ --}}
    <script>
        (function($) {
            "use strict";

            $(document).ready(function() {
                var $menu = $('.branch-dropdown-menu');
                if (!$menu.length) return;

                // Filter as you type
                $menu.on('keyup', '.branch-search-input', function() {
                    var v = $(this).val().toLowerCase().trim();
                    $menu.find('.dropdown-item').each(function() {
                        var t = $(this).text().toLowerCase();
                        $(this).toggle(v === '' || t.indexOf(v) > -1);
                    });
                });

                // Reset when dropdown closes
                $menu.closest('.dropdown').on('hidden.bs.dropdown', function() {
                    $menu.find('.branch-search-input').val('');
                    $menu.find('.dropdown-item').show();
                });

                // Autofocus search input when dropdown opens
                $menu.closest('.dropdown').on('shown.bs.dropdown', function() {
                    $menu.find('.branch-search-input').focus();
                });

                // Prevent dropdown from closing when clicking inside search box
                $menu.on('click', '.branch-search-wrapper', function(e) {
                    e.stopPropagation();
                });
            });

        })(jQuery);
    </script>

    <!-- Custom JS -->
    @yield('js-script')

    @stack('scripts')

</body>

</html>
