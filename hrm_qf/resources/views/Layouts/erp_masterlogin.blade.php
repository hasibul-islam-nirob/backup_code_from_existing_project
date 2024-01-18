<!DOCTYPE html>
<html>

<head>
    <title>Login Page</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="apple-touch-icon" href="{{ asset('assets/images/apple-touch-icon.png') }}">
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">

    <!-- Stylesheets -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-extend.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/site.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">

    <!-- Plugins -->
    <link rel="stylesheet" href="{{ asset('assets/css/pages/login-v2.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ url('style.css') }}">
    <!-- Scripts -->
    <script src="{{ asset('assets/vendor/breakpoints/breakpoints.min.js') }}"></script>
    <script>
        Breakpoints();
    </script>
</head>

@php
    $companyInfo = DB::table('gnl_companies')->where([['is_delete', 0], ['is_active', 1]])->first();

    $productLogo = DB::table('gnl_dynamic_form_value')
        ->where([['is_delete', 0], ['is_active', 1], ['type_id', 5], ['form_id', 'GCONF.1']])
        ->first();

    $productTitle = DB::table('gnl_dynamic_form_value')
        ->where([['is_delete', 0], ['is_active', 1], ['type_id', 5], ['form_id', 'GCONF.2']])
        ->first();

    $productTitle = isset($productTitle->name) && !empty($productTitle->name) ? $productTitle->name : null;

    $loginCoverImage = isset($companyInfo->cover_image_lp) && !empty($companyInfo->cover_image_lp) ? $companyInfo->cover_image_lp : null;

    // dd($loginCoverImage);
@endphp

@if (!empty($loginCoverImage) && file_exists($loginCoverImage))
<style>
    .page-dark.page-login-v2::before {
        background-image: url("{{ asset($companyInfo->cover_image_lp) }}");
        /* background-size: cover; */
        background-position: center;
    }

    /* .page-dark.layout-full::before {
        background-position: center;
    } */
</style>
@endif

<body class="animsition page-login-v2 layout-full page-dark">

    <div class="page" data-animsition-in="fade-in" data-animsition-out="fade-out">
        <div class="page-content">
            {{-- ## Client Logo & Name for Laptop Screen --}}
            <div class="page-brand-info">
                <div class="brand">
                    @if(isset($companyInfo->logo_view_lp) && $companyInfo->logo_view_lp == 1)
                        @if (isset($companyInfo->comp_logo) && !empty($companyInfo->comp_logo) && file_exists($companyInfo->comp_logo))
                            <a href="{{ $companyInfo->comp_web_add }}" target="_blank">
                                <img class="brand-img" src="{{ asset($companyInfo->comp_logo) }}" width="{{$companyInfo->logo_lp_width > 0 ? $companyInfo->logo_lp_width : '15'}}%">
                            </a>
                        @endif
                    @endif

                    @if (isset($companyInfo->name_view_lp) && $companyInfo->name_view_lp == 1 && isset($companyInfo->comp_name) && !empty($companyInfo->comp_name))
                        <a href="{{ $companyInfo->comp_web_add }}" target="_blank">
                            <h2 class="brand-text font-size-40" style="margin-left: 0%; padding-top: 0%;">{{ $companyInfo->comp_name }}</h2>
                        </a>
                    @endif
                </div>
            </div>
            {{-- ## Client Logo & Name for Laptop Screen End --}}

            <div class="page-login-main animation-slide-right animation-duration-1">

                {{-- ## For Mobile Responsive --}}
                <div class="brand hidden-md-up row text-center">

                    {{-- ## Client Logo & Name for Mobile Screen --}}
                    @if((isset($companyInfo->logo_view_lp) && $companyInfo->logo_view_lp == 1) || (isset($companyInfo->name_view_lp) && $companyInfo->name_view_lp == 1))
                        <div class="col-xs-5 col-sm-5">
                            @if(isset($companyInfo->logo_view_lp) && $companyInfo->logo_view_lp == 1)
                                @if (isset($companyInfo->comp_logo) && !empty($companyInfo->comp_logo) && file_exists($companyInfo->comp_logo))
                                    <a href="{{ $companyInfo->comp_web_add }}" target="_blank">
                                        <img class="brand-img" src="{{ asset($companyInfo->comp_logo) }}" width="{{$companyInfo->logo_lp_width > 0 ? $companyInfo->logo_lp_width : '15'}}%">
                                    </a>
                                @endif
                            @endif

                            @if (isset($companyInfo->name_view_lp) && $companyInfo->name_view_lp == 1 && isset($companyInfo->comp_name) && !empty($companyInfo->comp_name))
                                <a href="{{ $companyInfo->comp_web_add }}" target="_blank">
                                    <h3 class="brand-text" style="font-size:60%;">{{ $companyInfo->comp_name }}</h3>
                                </a>
                            @endif
                        </div>
                        <div class="col-xs-1 col-sm-1" style="border-left: 1px solid #ccc; margin-left:5%;">&nbsp;</div>
                    @endif
                    {{-- ## Client Logo & Name for Mobile Screen End --}}

                    {{-- ## Advance product logo for Mobile Screen --}}
                    @if((isset($companyInfo->logo_view_lp) && $companyInfo->logo_view_lp == 1) || (isset($companyInfo->name_view_lp) && $companyInfo->name_view_lp == 1))
                        <div class="col-xs-5 col-sm-5">
                    @else
                        <div class="col-xs-12 col-sm-12">
                    @endif

                    @if (isset($productLogo->name) && !empty($productLogo->name) && file_exists($productLogo->name))
                            <img src="{{ asset($productLogo->name) }}" width="60%;">
                    @endif

                    @if(!empty($productTitle))
                            <br>
                            <span class="float-right" style="padding-right: 23%;">
                                <h3 class="brand-text p-0 m-0" style="font-weight: bolder; font-size:90%;">
                                    {{ $productTitle }}
                                </h3>
                            </span>
                    @endif
                        </div>
                    {{-- ## Advance product logo for Mobile Screen end--}}
                </div>

                {{-- ## Advance product logo for Laptop Screen --}}
                <div class="hidden-sm-down text-center">

                    @if (isset($productLogo->name) && !empty($productLogo->name) && file_exists($productLogo->name))
                        <img src="{{ asset($productLogo->name) }}" style="width: 200px; padding:0px; margin:0px;">
                    @endif

                    @if(!empty($productTitle))
                    <br>
                    <span style="padding-right: 26%;" class="float-right">
                        <h2 class="brand-text font-size-18 font-weight-600 p-0 m-0">
                            {{ $productTitle }}
                        </h2>
                    </span>
                    @endif
                </div>
                {{-- ## Advance product logo for Laptop Screen End--}}

                <div class="card pt-10 mb-0">
                    {{-- <div class="card-header">Reset Password</div> --}}
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div class="card-body">
                        @yield('content')
                    </div>
                </div>

                <footer class="page-copyright">
                    <a href="https://taratechltd.com" target="_blank">
                        <img class="brand-img" src="{{ asset('assets/images/ttl_logo.png') }}" width="15%" alt="Tara Tech Ltd.">
                    </a>
                    <p>&copy; {{ date('Y') }}. All RIGHT RESERVED.</p>
                </footer>
            </div>

        </div>
    </div>

    <!-- Core  -->
    <script src="{{ asset('assets/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/babel-external-helpers/babel-external-helpers.min.js') }}"></script>

    <script src="{{ asset('assets/vendor/popper-js/umd/popper.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/bootstrap/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/animsition/animsition.min.js') }}"></script>

    <!-- Scripts -->
    <script src="{{ asset('assets/js/Component.min.js') }}"></script>
    <script src="{{ asset('assets/js/Plugin.min.js') }}"></script>
    <script src="{{ asset('assets/js/Base.min.js') }}"></script>
    <script src="{{ asset('assets/js/Config.min.js') }}"></script>

    <script src="{{ asset('assets/js/Section/Menubar.min.js') }}"></script>
    <script src="{{ asset('assets/js/Section/Sidebar.min.js') }}"></script>


    <!-- Page -->
    <script src="{{ asset('assets/js/Site.min.js') }}"></script>

    <script src="{{ asset('assets/js/dashboard/team.min.js') }}"></script>

    <!-------------------- toastr start ---------------------->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/toastr.min.css') }}">
    <script type="text/javascript" src="{{ asset('assets/js/toastr.min.js') }}"></script>
    <!-------------------- toastr end ---------------------->

    <script type="text/javascript">
        // toastr js \
        @if (Session::has('message'))

            var type = "{{ Session::get('alert-type', 'info') }}";

            switch (type) {

                case 'info':
                    toastr.info("{{ Session::get('message') }}");
                    break;
                case 'success':
                    toastr.success("{{ Session::get('message') }}");
                    break;
                case 'warning':
                    toastr.warning("{{ Session::get('message') }}");
                    break;
                case 'error':
                    toastr.error("{{ Session::get('message') }}");
                    break;
            }
        @endif
    </script>
</body>

</html>
