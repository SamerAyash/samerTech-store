<html lang="en" >
<!--begin::Head-->
<head>
    <meta charset="utf-8"/>
    <title>{{config('app.name')}}</title>
    <meta name="description" content="Singin page example"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>

    <!--begin::Fonts-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700"/>        <!--end::Fonts-->


    <!--begin::Page Custom Styles(used by this page)-->
    <link href="{{asset("/")}}cp_assets/css/pages/login/login-4.css" rel="stylesheet" type="text/css"/>
    <!--end::Page Custom Styles-->

    <!--begin::Global Theme Styles(used by all pages)-->
    <link href="{{asset("/")}}cp_assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css"/>
    <link href="{{asset("/")}}cp_assets/plugins/custom/prismjs/prismjs.bundle.css" rel="stylesheet" type="text/css"/>
    <link href="{{asset("/")}}cp_assets/css/style.bundle.css" rel="stylesheet" type="text/css"/>
    <!--end::Global Theme Styles-->

    <!--begin::Layout Themes(used by all pages)-->
    <!--end::Layout Themes-->

    <link rel="shortcut icon" href="{{asset("/")}}cp_assets/logos/ROMANO-WHITE-BIEGE.svg"/>
    <style>
        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus,
        input[type="text"]::placeholder,
        input[type="email"]::placeholder,
        input[type="password"]::placeholder {
            border: 0px solid #D7CEC4;
            background: rgba(209, 199, 188, 1);
            color: rgba(255, 255, 255, 1);
        }
        .custom-button{
            background: rgba(184, 174, 162, 1) !important;
            border-color: rgba(184, 174, 162, 1) !important;
            color: white !important;
        }
    </style>
</head>
<!--end::Head-->

<!--begin::Body-->
<body  id="kt_body"  class="header-fixed header-mobile-fixed subheader-enabled subheader-fixed aside-enabled aside-fixed page-loading"  >

<!--begin::Main-->
<div class="d-flex flex-column flex-root">
    <!--begin::Login-->
    <div class="login login-4 wizard d-flex flex-column flex-lg-row flex-column-fluid">
        <!--begin::Content-->
        <div class="login-container order-2 order-lg-1 d-flex flex-center flex-row-fluid px-7 pt-lg-0 pb-lg-0 pt-4 pb-6 bg-white">
            <!--begin::Wrapper-->
            <div class="login-content d-flex flex-column pt-lg-0 pt-12">
                <!--begin::Logo-->
                <h3 href="#" class="pb-xl-20 pb-15 font-boldest display2 text-black text-uppercase">
                    Romano    
                </h3>               
                <!--end::Logo-->
                @yield("content")
            </div>
            <!--end::Wrapper-->
        </div>
        <!--begin::Content-->

        <!--begin::Aside-->
        <div class="login-aside order-1 order-lg-2 bgi-no-repeat bgi-position-x-right">
            <div class="login-conteiner bgi-no-repeat bgi-position-x-right bgi-position-y-bottom bgi-" style="background-image: url({{asset("/")}}cp_assets/logos/Romano-TopHeader-Logo.png);">
                <!--begin::Aside title-->
                <h3 class="pt-lg-40 pl-lg-20 pb-lg-0 pl-10 pt-20 m-0 d-flex justify-content-lg-start font-weight-boldest display5 display2-lg text-white">
                    Welcome Back! <br/>
                </h3>
                <p class="pl-lg-20 pb-lg-0 pl-10 pb-20 m-0 d-flex justify-content-md-start display5 text-white">
                    Manage Everything <br/> from One Place
                </p>
                <!--end::Aside title-->
            </div>
        </div>
        <!--end::Aside-->
    </div>
    <!--end::Login-->
</div>
<!--end::Main-->

<!--begin::Global Config(global config for global JS scripts)-->
<script>
    var KTAppSettings = {
        "breakpoints": {
            "sm": 576,
            "md": 768,
            "lg": 992,
            "xl": 1200,
            "xxl": 1400
        },
        "colors": {
            "theme": {
                "base": {
                    "white": "#ffffff",
                    "primary": "#3699FF",
                    "secondary": "#E5EAEE",
                    "success": "#1BC5BD",
                    "info": "#8950FC",
                    "warning": "#FFA800",
                    "danger": "#F64E60",
                    "light": "#E4E6EF",
                    "dark": "#181C32"
                },
                "light": {
                    "white": "#ffffff",
                    "primary": "#E1F0FF",
                    "secondary": "#EBEDF3",
                    "success": "#C9F7F5",
                    "info": "#EEE5FF",
                    "warning": "#FFF4DE",
                    "danger": "#FFE2E5",
                    "light": "#F3F6F9",
                    "dark": "#D6D6E0"
                },
                "inverse": {
                    "white": "#ffffff",
                    "primary": "#ffffff",
                    "secondary": "#3F4254",
                    "success": "#ffffff",
                    "info": "#ffffff",
                    "warning": "#ffffff",
                    "danger": "#ffffff",
                    "light": "#464E5F",
                    "dark": "#ffffff"
                }
            },
            "gray": {
                "gray-100": "#F3F6F9",
                "gray-200": "#EBEDF3",
                "gray-300": "#E4E6EF",
                "gray-400": "#D1D3E0",
                "gray-500": "#B5B5C3",
                "gray-600": "#7E8299",
                "gray-700": "#5E6278",
                "gray-800": "#3F4254",
                "gray-900": "#181C32"
            }
        },
        "font-family": "Poppins"
    };
</script>
<!--end::Global Config-->

<!--begin::Global Theme Bundle(used by all pages)-->
<script src="{{asset("/")}}cp_assets/plugins/global/plugins.bundle.js"></script>
<script src="{{asset("/")}}cp_assets/plugins/custom/prismjs/prismjs.bundle.js"></script>
<script src="{{asset("/")}}cp_assets/js/scripts.bundle.js"></script>
<!--end::Global Theme Bundle-->

<!--begin::Page Scripts(used by this page)-->
<script src="{{asset("/")}}cp_assets/js/pages/custom/login/login-4.js"></script>
<!--end::Page Scripts-->
</body>
<!--end::Body-->
</html>
