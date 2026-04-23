
<!doctype html>
<html class="no-js" lang="en">

<head>
  <title>QBCC</title>
  <!-- HTML5 Shim and Respond.js IE11 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 11]>

  <![endif]-->
  <!-- Meta -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
  <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
  <meta name="description" content=""/>
  <meta name="keywords" content="">
  <meta name="author" content="Phoenixcoded"/>
  <!-- Favicon icon -->
  <link rel="icon" href="/dsh/assets/images/favicon.ico" type="image/x-icon">

  <!-- vendor css -->
  <link rel="stylesheet" href="/dsh/assets/css/style.css">
  <link rel="stylesheet" href="/dsh/assets/css/layout-rtl.css">
  <link rel="stylesheet" href="/bkoodak/style.css">
  <link rel="stylesheet" href="{{asset('dsh/assets/css/font/fonts.css')}}"/>
  


  <!--<link rel="stylesheet" href="{{asset('dsh/assets/select2/css/select2.min.css')}}">-->
  
 
</head>
<body dir="rtl" style="font-family: Iran!important;">
<!-- [ Pre-loader ] start -->
<div class="loader-bg">
  <div class="loader-track">
    <div class="loader-fill"></div>
  </div>
</div>
<!-- [ Pre-loader ] End -->
<!-- [ navigation menu ] start -->
<nav class="pcoded-navbar menu-light ">
  <div class="navbar-wrapper  ">
    <div class="navbar-content scroll-div ">

      <div class="">
        <div class="main-menu-header">
          <img class="img-radius" src="/printStyle/logo.png" height="40px" alt="User-Profile-Image">
          <div class="user-details">
            <div id="more-details">&nbsp;{{Auth::user()->name}}<i class="fa fa-caret-down"></i></div>
            @if(Auth::user()->role == 'SP')
              <p>سوپر ادمین</p>
            @elseif(Auth::user()->role == 'CO')
              <p>دفتر مرکزی</p>
            @elseif(Auth::user()->role == 'CCO')
              <p>دفتر مرکزی و مشتریان</p>
            @elseif(Auth::user()->role == 'SO')
              <p>دفتر فروشات</p>
            @elseif(Auth::user()->role == 'SCO')
              <p>دفتر فروشات و مشتریان</p>
            @elseif(Auth::user()->role == 'MO')
              <p>حساب متفرقه</p>
            @elseif(Auth::user()->role == 'PH')
              <p>حساب عکاس</p>
            @endif

          </div>
        </div>
        <div class="collapse" id="nav-user-link">
          <ul class="list-inline">

            <li class="list-inline-item"><a href="/logout" data-toggle="tooltip" title="Logout"
                                            class="text-danger"><i class="feather icon-power"></i></a></li>
          </ul>
        </div>
      </div>

      <ul class="nav pcoded-inner-navbar ">

        <li class="nav-item pcoded-hasmenu">
          <a href="/dashboard" class="nav-link"><span class="pcoded-micon"><i
                      class="feather icon-home"></i></span><span
                    class="pcoded-mtext"><b>داشبورد</b></span></a>
        </li>
    
        @if(auth()->user()->role != 'AO')
          @if(auth()->user()->role == 'SP'  ||  auth()->user()->role == 'CCO')
           
      
            <li class="nav-item pcoded-hasmenu {{ request()->is('dashboard/agents*')  ? 'active' : '' }}">
              <a href="#" class="nav-link"><span class="pcoded-micon"><i
                          class="feather icon-layout"></i></span><span
                        class="pcoded-mtext"><b>نماینده</b></span></a>
              <ul class="pcoded-submenu">
                <li><a href="/dashboard/agents">لیست نماینده ها</a></li>
                @if(auth()->user()->role == 'SP')
                  <li><a href="/dashboard/agent-money-request-list"> لیست درخواست های پول</a></li>
                @endif
        
              </ul>
            </li>

          @endif
          
        @endif


        @if(auth()->user()->role == 'SP' || auth()->user()->role == 'SO'||  auth()->user()->role == 'SCO'||  auth()->user()->role == 'CCO'|| auth()->user()->role == 'MO' )
          <li class="nav-item pcoded-hasmenu {{ request()->is('dashboard/different-account*')  ? 'active' : '' }}">
            <a href="#" class="nav-link"><span class="pcoded-micon"><i
                        class="feather icon-home"></i></span><span
                      class="pcoded-mtext"><b>حساب متفرقه</b></span></a>
            <ul class="pcoded-submenu">
              <li><a href="/dashboard/different-account">حساب متفرقه</a></li>
              @if(auth()->user()->role == 'SP')
                <li><a href="/dashboard/different-account-money-request-list"> لیست درخواست های پول</a></li>
              @endif
  
            </ul>

          </li>

        @endif
        
        @if(auth()->user()->role == 'SP' || auth()->user()->role == 'SO'||  auth()->user()->role == 'CO'||  auth()->user()->role == 'SCO'||  auth()->user()->role == 'CCO'|| auth()->user()->role == 'MO' )
          <li class="nav-item pcoded-hasmenu {{ request()->is('dashboard/new-different-account*')  ? 'active' : '' }}">
            <a href="#" class="nav-link"><span class="pcoded-micon"><i
                        class="feather icon-home"></i></span><span
                      class="pcoded-mtext"><b>حساب متفرقه جدید</b></span></a>
            <ul class="pcoded-submenu">
              <li><a href="/dashboard/new-different-account"> حساب متفرقه جدید</a></li>
              @if(auth()->user()->role == 'SP')
                <li><a href="/dashboard/new-different-account-money-request-list"> لیست درخواست های پول</a></li>
              @endif
      
            </ul>
    
          </li>
  
        @endif

        @if(auth()->user()->role == 'SP'  ||  auth()->user()->role == 'CCO')
          <li class="nav-item pcoded-hasmenu {{ request()->is(['dashboard/contract-carpet*','dashboard/list-weight','list-buy-carpet','check-book'])  ? 'active' : '' }}">
            <a href="#" class="nav-link"><span class="pcoded-micon"><i
                        class="feather icon-layout"></i></span><span
                      class="pcoded-mtext"><b>قالین</b></span></a>
            <ul class="pcoded-submenu">
              <li><a href="/dashboard/contract-carpet">قالین های قراردادی</a></li>
              <li><a href="/dashboard/list-weight">قالین های وزنی</a></li>
              <li><a href="/dashboard/list-buy-carpet">قالین های خرید شده</a></li>
              <li><a href="/dashboard/check-book">چک بٌک شده ها</a></li>

            </ul>
          </li>
          <li class="nav-item pcoded-hasmenu {{ request()->is('carpet-repair*','kachaee-team')  ? 'active' : '' }}">
            <a href="#" class="nav-link "><span class="pcoded-micon"><i
                        class="feather icon-layout"></i></span><span
                      class="pcoded-mtext"><b>کچایی</b></span></a>
            <ul class="pcoded-submenu">
              <li><a href="/dashboard/carpet-repair">کچای قالین ها</a></li>
              <li><a href="/dashboard/kachaee-team">تیم کچایی</a></li>
              @if(auth()->user()->role == 'SP')
                <li><a href="/dashboard/kachaee-money-request-list"> لیست درخواست های پول</a></li>
              @endif
            </ul>
          </li>
        @endif
        @if(auth()->user()->role == 'SP' || auth()->user()->role == 'SO'|| auth()->user()->role == 'SCO' || auth()->user()->role == 'CCO' || auth()->user()->role == 'DE' )

          <li class="nav-item pcoded-hasmenu {{ request()->is('carpet-wash*','washing-team')  ? 'active' : '' }}">
            <a href="#" class="nav-link "><span class="pcoded-micon"><i
                        class="feather icon-layout"></i></span><span
                      class="pcoded-mtext"><b>شست</b></span></a>
            <ul class="pcoded-submenu">

              <li><a href="/dashboard/carpet-wash">شست قالین ها</a></li>

              <li><a href="/dashboard/washing-team"> تیم شست</a></li>
              @if(auth()->user()->role == 'SP')
                <li><a href="/dashboard/washing-money-request-list"> لیست درخواست های پول</a></li>
              @endif
            </ul>
          </li>
        @endif
        @if(auth()->user()->role == 'SP' || auth()->user()->role == 'SO'|| auth()->user()->role == 'SCO' || auth()->user()->role == 'DE' || auth()->user()->role == 'CCO')
          <li class="nav-item pcoded-hasmenu {{ request()->is('finishing-center*','finish-team','finish-team-category')  ? 'active' : '' }}">
            <a href="#" class="nav-link "><span class="pcoded-micon"><i
                        class="feather icon-layout"></i></span><span
                      class="pcoded-mtext"><b>تیاری</b></span></a>
            <ul class="pcoded-submenu">
              <li><a href="/dashboard/finishing-center">بخش های تیاری</a></li>
              <li><a href="/dashboard/finish-team"> تیم تیاری</a></li>
  
              @if(auth()->user()->role == 'SP')
                <li><a href="/dashboard/refinish-request-list"> لیست درخواست های دوباره تیاری</a></li>
                <li><a href="/dashboard/finishing-money-request-list"> لیست درخواست های پول</a></li>
              @endif
              {{--<li><a href="/dashboard/finish-team-category"> دسته بندی تیاری</a></li>--}}
            </ul>
          </li>
        @endif
        @if(auth()->user()->role == 'SP' || auth()->user()->role == 'SO'|| auth()->user()->role == 'SCO' || auth()->user()->role == 'OM' || auth()->user()->role == 'DE')
          <li class="nav-item pcoded-hasmenu {{ request()->is('carpet-stock*')  ? 'active' : '' }}">
            <a href="/dashboard/carpet-stock" class="nav-link "><span class="pcoded-micon"><i
                        class="feather icon-home"></i></span><span
                      class="pcoded-mtext"><b>گدام قالین آماده به فروش</b></span></a>

          </li>
        @endif
        @if(auth()->user()->role == 'SO' || auth()->user()->role == 'SCO' || auth()->user()->role == 'CCO' || auth()->user()->role == 'SP' || auth()->user()->role == 'OM' || auth()->user()->role == 'DE')
          <li class="nav-item pcoded-hasmenu {{ request()->is('sales*')  ? 'active' : '' }}">
            <a href="#" class="nav-link "><span class="pcoded-micon"><i
                        class="feather icon-layout"></i></span><span
                      class="pcoded-mtext"><b>فروشات</b></span></a>
            <ul class="pcoded-submenu">
              <li><a href="/dashboard/sales"> لیست فروشات</a></li>
              <li><a href="/dashboard/packing-list">پکینگ لیست</a></li>
              <li><a href="/dashboard/invoices">لیست انوایس ها</a></li>
            </ul>
          </li>

        @endif

        @if(auth()->user()->role == 'SP' || auth()->user()->role == 'SO' )
          <li class="nav-item pcoded-hasmenu {{ request()->is('add-office-credit*','money-request-list','office-cash-book')  ? 'active' : '' }}">
            <a href="#" class="nav-link "><span class="pcoded-micon"><i
                        class="feather icon-layout"></i></span><span
                      class="pcoded-mtext"><b>دخل و خرچ پول</b></span></a>
            <ul class="pcoded-submenu">
              <li><a href="/dashboard/add-office-credit">اضافه کردن پول به دخل</a></li>

              @if(auth()->user()->role == 'SP')
                <li><a href="/dashboard/money-request-list"> لیست درخواست های پول</a></li>
              @endif
              <li><a href="/dashboard/office-cash-book">مصارف</a></li>
             
            </ul>
          </li>
          <li class="nav-item pcoded-hasmenu">
          <a href="/dashboard/monthly-expenses" class="nav-link"><span class="pcoded-micon"><i
                      class="feather icon-home"></i></span><span
                    class="pcoded-mtext"><b>مصارف ماهانه</b></span></a>
        </li>
        @endif
        
        @if(auth()->user()->role == 'SP' || auth()->user()->role == 'CO' ||  auth()->user()->role == 'CCO')
        
      
         
          <li class="nav-item pcoded-hasmenu">
            <a href="#" class="nav-link "><span class="pcoded-micon"><i
                        class="feather icon-layout"></i></span><span
                      class="pcoded-mtext"><b>تار</b></span></a>
            <ul class="pcoded-submenu">
              <li><a href="/dashboard/material-purchase">خرید تار</a></li>
              @if(auth()->user()->role == 'SP')
                <li><a href="/dashboard/purchase-material-request-list"> لیست درخواست خرید تار</a></li>
              @endif
              <li><a href="/dashboard/material-sales">فروش تار</a></li>
              @if(auth()->user()->role == 'SP')
                <li><a href="/dashboard/material-sale-request-list"> لیست درخواست فروش تار</a></li>
              @endif
              <li><a href="/dashboard/material-stock">گدام تار</a></li>
              <li><a href="/dashboard/string-seller">فروشنده تار</a></li>
              @if(auth()->user()->role == 'SP')
                <li><a href="/dashboard/string-seller-request-list"> لیست درخواست پول فروشنده تار</a></li>
              @endif
             
              <!--<li><a href="/dashboard/material-category">کتگوری مواد</a></li> material category works on carpets controler search contract and weight carpets by agent function--> >
              <li><a href="/dashboard/materialtypes">نوعیت مواد</a></li>
              
                       <li><a href="/dashboard/material-accounts">دخل و خرچ تار</a></li>
                        @if(auth()->user()->role == 'SP')
              <li><a href="/dashboard/material-account-request-list"> لیست درخواست اکونت های تار</a></li>
                    @endif
            </ul>
          </li>
        @endif
        
        @if(auth()->user()->role == 'SP' || auth()->user()->role == 'SO' || auth()->user()->role == 'CO')
          <li class="nav-item pcoded-hasmenu">
            <a href="#" class="nav-link "><span class="pcoded-micon"><i
                        class="feather icon-layout"></i></span><span
                      class="pcoded-mtext"><b>کارمندان دفتر</b></span></a>
            <ul class="pcoded-submenu">
              <li><a href="/dashboard/office-employee">لیست کارمندان</a></li>
              @if(auth()->user()->role == 'SP')
                <li><a href="/dashboard/employee-request-list"> لیست درخواست پول کارمندان</a></li>
              @endif
              <li><a href="/dashboard/employee-department">دیپارتمنت کارمندان</a></li>
            </ul>
          </li>
        @endif
        @if(auth()->user()->role == 'SCO' || auth()->user()->role == 'CCO' || auth()->user()->role == 'SP')
          <li class="nav-item pcoded-hasmenu">
            <a href="#" class="nav-link "><span class="pcoded-micon"><i
                        class="feather icon-home"></i></span><span
                      class="pcoded-mtext"><b>مشتری ها</b></span></a>
            <ul class="pcoded-submenu">
              <li><a href="/dashboard/customers">لیست مشتریان</a></li>
              @if(auth()->user()->role == 'SP')
                <li><a href="/dashboard/customer-request-list"> لیست درخواست پول مشتریان</a></li>
              @endif
             
            </ul>

          </li>
        @endif
        @if(auth()->user()->role != 'MO' && auth()->user()->role != 'PH' && auth()->user()->role != 'AO' && auth()->user()->role != 'OM' && auth()->user()->role != 'DE')
          <li class="nav-item pcoded-hasmenu">
            <a href="#" class="nav-link "><span class="pcoded-micon"><i
                        class="feather icon-layout"></i></span><span
                      class="pcoded-mtext"><b>تنظیمات</b></span></a>
            <ul class="pcoded-submenu">
              @if(auth()->user()->role == 'SP')
                <li><a href="/dashboard/users">کاربران سیستم</a></li>
              @endif

              @if(auth()->user()->role != 'AO')
                <li><a href="/dashboard/phone-book">دفترچه تلفون</a></li>
                <li><a href="/dashboard/carpet-types">نوعیت قالین</a></li>
                <li><a href="/dashboard/carpet-qualities">کوالتی قالین</a></li>
                <li><a href="/dashboard/provinces">ولایات</a></li>
              @endif
              @if(auth()->user()->role == 'AO' || auth()->user()->role == 'SP')
                <li><a href="/dashboard/agent-employees">کارگرها</a></li>
              @endif
              <li><a href="/dashboard/carpet-orders">شماره فرمایش</a></li>
              @if(auth()->user()->role == 'SP')
                <li><a href="/dashboard/activities">نمایش فعالیت ها</a></li>
              @endif

            </ul>
          </li>


        @endif

      </ul>


    </div>
  </div>
</nav>
<!-- [ navigation menu ] end -->
<!-- [ Header ] start -->
<header class="navbar pcoded-header navbar-expand-lg navbar-light header-blue">


  <div class="m-header">
    <a class="mobile-menu" id="mobile-collapse" href="#!"><span></span></a>
    <a href="#!" class="b-brand">
      <!-- ========   change your logo hear   ============ -->
      {{--<img src="/dsh/assets/images/logo.png" alt="" class="logo">--}}
      QBCC

    </a>
    <a href="#!" class="mob-toggler">
      <i class="feather icon-more-vertical"></i>
    </a>
  </div>


</header>
<!-- [ Header ] end -->


<!-- [ Main Content ] start -->
<div class="pcoded-main-container">
  <div class="pcoded-content">
    <!-- [ breadcrumb ] start -->
    <div class="page-header">
      <div class="page-block">
        <div class="row align-items-center">
          <div class="col-md-12">
            <div class="page-header-title">

            </div>
            <ul class="breadcrumb">
              <li class="breadcrumb-item"><a href="index.html"><i class="feather icon-home"></i></a></li>
              <li class="breadcrumb-item"><a href="#!"></a></li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <!-- [ breadcrumb ] end -->
    <!-- [ Main Content ] start -->
    {{--<div class="container-fluid ">--}}
    {{--<div id="PC" style="display: none;overflow-y:scroll;">--}}
      {{--<div class="middleContainer" style="position: relative;background-color: white">--}}
        {{--<div id="headerimage" style="margin-bottom: 100px;background-color: white;">--}}
          {{--<img src="/printStyle/header.png" height="230px" width="1200px;" style="" alt="Header Logo">--}}
        {{--</div>--}}
        {{--<div id="SC" style="min-height: 790px;background-color: white;"></div>--}}
        {{--<div id="footerimage">--}}
          {{--<img src="/printStyle/footer1.png" height="250px" width="1200px" alt="Footer Logo">--}}
        {{--</div>--}}
      {{--</div>--}}
    {{--</div>--}}

    <div id="PC" style="display: none;overflow-y:scroll">
      <div class="middleContainer" style="position: relative;background-color: white">
        <div id="headerimage">
          <img src="/printStyle/header.png" height="230px" width="960px" style="margin-right: 30px;" alt="Header Logo">
        </div>
        <div id="SC" style="min-height: 790px;background-color: white;"></div>
        <div id="footerimage" style="background-color: white;">
          {{--<img src="/printStyle/Footer.png" alt="Footer Logo">--}}
        </div>
      </div>
    </div>
  @yield('content')

  {{--</div>--}}
  <!-- [ Main Content ] end -->
  </div>
</div>
<!-- [ Main Content ] end -->
{{--<!-- Warning Section start -->--}}
{{--<!-- Older IE warning message -->--}}
{{--<!--[if lt IE 11]>--}}
{{--<div class="ie-warning">-->--}}
{{--<!--<h1>Warning!!</h1>-->--}}
{{--<!--<p>You are using an outdated version of Internet Explorer, please upgrade-->--}}
{{--<!--<br/>to any of the following web browsers to access this website.-->--}}
{{--<!--</p>-->--}}
{{--<!--<div class="iew-container">-->--}}
{{--<!--<ul class="iew-download">-->--}}
{{--<!--<li>-->--}}
{{--<!--<a href="http://www.google.com/chrome/">-->--}}
{{--<!--<img src="assets/images/browser/chrome.png" alt="Chrome">-->--}}
{{--<!--<div>Chrome</div>-->--}}
{{--<!--</a>-->--}}
{{--<!--</li>-->--}}
{{--<!--<li>-->--}}
{{--<!--<a href="https://www.mozilla.org/en-US/firefox/new/">-->--}}
{{--<!--<img src="assets/images/browser/firefox.png" alt="Firefox">-->--}}
{{--<!--<div>Firefox</div>-->--}}
{{--<!--</a>-->--}}
{{--<!--</li>-->--}}
{{--<!--<li>-->--}}
{{--<!--<a href="http://www.opera.com">-->--}}
{{--<!--<img src="assets/images/browser/opera.png" alt="Opera">-->--}}
{{--<!--<div>Opera</div>-->--}}
{{--<!--</a>-->--}}
{{--<!--</li>-->--}}
{{--<!--<li>-->--}}
{{--<!--<a href="https://www.apple.com/safari/">-->--}}
{{--<!--<img src="assets/images/browser/safari.png" alt="Safari">-->--}}
{{--<!--<div>Safari</div>-->--}}
{{--<!--</a>-->--}}
{{--<!--</li>-->--}}
{{--<!--<li>-->--}}
{{--<!--<a href="http://windows.microsoft.com/en-us/internet-explorer/download-ie">-->--}}
{{--<!--<img src="assets/images/browser/ie.png" alt="">-->--}}
{{--<!--<div>IE (11 & above)</div>-->--}}
{{--<!--</a>-->--}}
{{--<!--</li>-->--}}
{{--<!--</ul>-->--}}
{{--<!--</div>-->--}}
{{--<!--<p>Sorry for the inconvenience!</p>-->--}}
{{--<!--</div>-->--}}
{{--<script src="/dsh/jquery-1.12.4.min.js"></script>--}}
{{--<!-- Warning Section Ends -->--}}
<script src="/dsh/toastr/toastr.min.js"></script>
<!-- Required Js -->
<script src="/dsh/assets/js/vendor-all.min.js"></script>
<script src="/dsh//assets/js/plugins/bootstrap.min.js"></script>
<script src="/dsh/assets/js/ripple.js"></script>
<script src="/dsh/assets/js/pcoded.min.js"></script>
<script src="/dsh/assets/js/menu-setting.min.js"></script>

<!-- Apex Chart -->
{{--<script src="/dsh/assets/js/plugins/apexcharts.min.js"></script>--}}
<!-- custom-chart js -->
{{--<script src="/dsh/assets/js/pages/dashboard-main.js"></script>--}}
<script src="/dsh/swal/swal.min.js"></script>
<script src="/dsh/parsleyjs/dist/parsley.min.js"></script>




<!--sabeqa-->
<script src="{{asset('dsh/assets/select2/js/select2.js')}}"></script>


<!-- sweet alert Js -->
<script src="/dsh/assets/js/plugins/sweetalert.min.js"></script>
<script src="/dsh/assets/js/pages/ac-alert.js"></script>

<!--<script src="{{\Illuminate\Support\Facades\URL::asset('dsh/assets/xlsx/dist/xlsx.core.min.js')}}"></script>-->
<!--<script src="{{\Illuminate\Support\Facades\URL::asset('dsh/assets/file-saverjs/FileSaver.min.js')}}"></script>-->
<!--<script src="{{\Illuminate\Support\Facades\URL::asset('dsh/assets/blobjs/Blob.min.js')}}"></script>-->
<!--<script src="{{\Illuminate\Support\Facades\URL::asset('dsh/assets/tableexport/dist/js/tableexport.min.js')}}"></script>-->
<!--<script src="{{asset('dsh/assets/js/pages/form-select-custom.js')}}"></script>-->
<script>
    $(document).ready(function () {
        checkCookie();
    });

    function setCookie(cname, cvalue, exdays) {
        var d = new Date();
        d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
        var expires = "expires=" + d.toGMTString();
        document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
    }

    function getCookie(cname) {
        var name = cname + "=";
        var decodedCookie = decodeURIComponent(document.cookie);
        var ca = decodedCookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ') {
                c = c.substring(1);
            }
            if (c.indexOf(name) == 0) {
                return c.substring(name.length, c.length);
            }
        }
        return "";
    }

    function checkCookie() {
        var ticks = getCookie("modelopen");
        if (ticks != "") {
            ticks++;
            setCookie("modelopen", ticks, 1);
            if (ticks == "2" || ticks == "1" || ticks == "0") {
                $('#exampleModalCenter').modal();
            }
        } else {
            // user = prompt("Please enter your name:", "");
            $('#exampleModalCenter').modal();
            ticks = 1;
            setCookie("modelopen", ticks, 1);
        }
    }
</script>

<script>
    function printPage(sectionID) {
        var page = document.body.innerHTML;
        var section = document.getElementById(sectionID).innerHTML;
        document.getElementById("SC").innerHTML = section;
        var contain = document.getElementById("PC").innerHTML;
        document.body.innerHTML = contain;
        window.print();
        document.body.innerHTML = page;
    }

    $(document).ready(function () {

        // CONVERTING FORM DATA TO DECIMAL
        // FINISHING CENTER
        $('#fp').blur(function () {
            var fp = $('#fp').val();
            var fprice = parseFloat(fp).toFixed(2);
            if (isNaN(fprice)) {
                $('#fp').val();
                $('#mainPq').val();
            } else {
                $('#fp').val(fprice);
                var us = $('#currency').val();
                var midPrice = fprice / us;
                var mainPrice = parseFloat(midPrice).toFixed(2);
                $('#mainP').val(mainPrice);
            }
        });
        $('#fpq').blur(function () {
            var fp = $('#fpq').val();
            var fprice = parseFloat(fp).toFixed(2);
            if (isNaN(fprice)) {
                $('#fpq').val();
                $('#mainPq').val();
            } else {
                $('#fpq').val(fprice);
                var us = $('#currencyq').val();
                var midPrice = fprice / us;
                var mainPrice = parseFloat(midPrice).toFixed(2);
                $('#mainPq').val(mainPrice);
            }
        });
        $('#fpr').blur(function () {
            var fp = $('#fpr').val();
            var fprice = parseFloat(fp).toFixed(2);
            if (isNaN(fprice)) {
                $('#fpr').val();
                $('#mainPr').val();
            } else {
                $('#fpr').val(fprice);
                var us = $('#currencyr').val();
                var midPrice = fprice / us;
                var mainPrice = parseFloat(midPrice).toFixed(2);
                $('#mainPr').val(mainPrice);
            }
        });

        $('#fpc').blur(function () {
            var fp = $('#fpc').val();
            var fprice = parseFloat(fp).toFixed(2);
            if (isNaN(fprice)) {
                $('#fpc').val();
                $('#mainPc').val();
            } else {
                $('#fpc').val(fprice);
                var us = $('#currencyc').val();
                var midPrice = fprice / us;
                var mainPrice = parseFloat(midPrice).toFixed(2);
                $('#mainPc').val(mainPrice);
            }
        });
        $('#fpl').blur(function () {
            var fp = $('#fpl').val();
            var fprice = parseFloat(fp).toFixed(2);
            if (isNaN(fprice)) {
                $('#fpl').val();
                $('#mainPl').val();
            } else {
                $('#fpl').val(fprice);
                var us = $('#currencyl').val();
                var midPrice = fprice / us;
                var mainPrice = parseFloat(midPrice).toFixed(2);
                $('#mainPl').val(mainPrice);
            }
        });
        $('#fppo').blur(function () {
            var fp = $('#fppo').val();
            var fprice = parseFloat(fp).toFixed(2);
            if (isNaN(fprice)) {
                $('#fppo').val();
                $('#mainPpo').val();
            } else {
                $('#fppo').val(fprice);
                var us = $('#currencypo').val();
                var midPrice = fprice / us;
                var mainPrice = parseFloat(midPrice).toFixed(2);
                $('#mainPpo').val(mainPrice);
            }
        });
        $('#fpk').blur(function () {
            var fp = $('#fpk').val();
            var fprice = parseFloat(fp).toFixed(2);
            if (isNaN(fprice)) {
                $('#fpk').val();
                $('#mainPk').val();
            } else {
                $('#fpk').val(fprice);
                var us = $('#currencyk').val();
                var midPrice = fprice / us;
                var mainPrice = parseFloat(midPrice).toFixed(2);
                $('#mainPk').val(mainPrice);
            }
        });
        $('#fpra').blur(function () {
            var fp = $('#fpra').val();
            var fprice = parseFloat(fp).toFixed(2);
            if (isNaN(fprice)) {
                $('#fpra').val();
                $('#mainPra').val();
            } else {
                $('#fpra').val(fprice);
                var us = $('#currencyra').val();
                var midPrice = fprice / us;
                var mainPrice = parseFloat(midPrice).toFixed(2);
                $('#mainPra').val(mainPrice);
            }
        });
        //end finishing center
        // price per killo
        $("#ppm").blur(function () {
            var price = $('#ppm').val();
            var mainPrice = parseFloat(price).toFixed(2);
            if (isNaN(mainPrice)) {
                $("#ppm").val();
            } else {
                $("#ppm").val(mainPrice);
            }
        });
        // WASHING METRAZH
        $("#wheight").blur(function () {
            var height = $('#wheight').val();
            var mainHeight = parseFloat(height).toFixed(2);
            if (isNaN(mainHeight)) {
                $("#wheight").val();
            } else {
                $("#wheight").val(mainHeight);
            }

            // Area
            var mainWidth = $('#wwidth').val();
            if (mainHeight != '' && mainWidth != '') {
                var area = mainHeight * mainWidth;
                var mainArea = parseFloat(area).toFixed(2);
                $('#warea').val(mainArea);
            }
        });
        $("#wwidth").blur(function () {
            var width = $('#wwidth').val();
            var mainWidth = parseFloat(width).toFixed(2);
            if (isNaN(mainWidth)) {
                $("#wwidth").val();
            } else {
                $("#wwidth").val(mainWidth);
            }
            // Area
            var mainHeight = $('#wheight').val();
            if (mainHeight != '' && mainWidth != '') {
                var area = mainHeight * mainWidth;
                var mainArea = parseFloat(area).toFixed(2);
                $('#warea').val(mainArea);
            }
        });
        // KACHAEE TOTAL PRICE
        $("#wprice,#wheight,#wwidth").blur(function () {
            var af = $('#wprice').val();
            var afg = parseFloat(af).toFixed(2);
            if (isNaN(afg)) {
                $("#wprice").val();
            } else {
                $("#wprice").val(afg);
            }
            var us = $('#currency').val();
            var price = afg / us;
            var area = $('#warea').val();
            if (price != '' && area != '') {
                var mul = price * area;
                var af_mul = afg * area;
                var total = parseFloat(mul).toFixed(2);
                var af_total = parseFloat(af_mul).toFixed(2);
                $("#total_price").val(total);
                $("#af_total_price").val(af_total);
            }
        });
        // END OF WASHING
        // height
        $("#height").blur(function () {
            var height = $('#height').val();
            var mainHeight = parseFloat(height).toFixed(2);
            if (isNaN(mainHeight)) {
                $("#height").val();
            } else {
                $("#height").val(mainHeight);
            }


            // Area
            var mainWidth = $('#width').val();
            if (mainHeight != '' && mainWidth != '') {
                var area = mainHeight * mainWidth;
                var mainArea = parseFloat(area).toFixed(2);
                $('#area').val(mainArea);
            }

        });
        //width
        $("#width").blur(function () {
            var width = $('#width').val();
            var mainWidth = parseFloat(width).toFixed(2);
            if (isNaN(mainWidth)) {
                $("#width").val();
            } else {
                $("#width").val(mainWidth);
            }

            // Area
            var mainHeight = $('#height').val();
            if (mainHeight != '' && mainWidth != '') {
                var area = mainHeight * mainWidth;
                var mainArea = parseFloat(area).toFixed(2);
                $('#area').val(mainArea);
            }


        });
        //total price
        $("#ppm,#width,#height").blur(function () {
            var price_per_meter = $('#ppm').val();

            var area = $('#area').val();
            var curr = $('#currency_fo_af_convert').val();
            if (price_per_meter != '' && area != '') {
                var mul = price_per_meter * area;
                var afmul = price_per_meter * curr * area;
                var total = parseFloat(mul).toFixed(2);
                var aftotal = parseFloat(afmul).toFixed(2);
                $("#total_price").val(total);
                $("#total_price_af").val(aftotal);
                $("#carpet_price").val(total);
                $("#carpet_price_us").val(total);

            }
            // var afg = $('#ppm').val();
            // var us = $('#currency').val();
            // if (us != null && us != '') {
            //     var price = afg / us;
            //     var area = $('#area').val();
            //     if (price != '' && area != '') {
            //         var mul = price * area;
            //         var afmul = afg * area;
            //         var total = parseFloat(mul).toFixed(2);
            //         var aftotal = parseFloat(afmul).toFixed(2);
            //         $("#total_price").val(total);
            //         $("#total_price_af").val(aftotal);
            //         $("#carpet_price").val(total);
            //         $("#carpet_price_us").val(total);
            //
            //     }
            // } else {
            //
            //     var price = afg;
            //     var area = $('#area').val();
            //     var curr = $('#currency_fo_af_convert').val();
            //     if (price != '' && area != '') {
            //         var mul = price * area;
            //         var afmul = afg * curr * area;
            //         var total = parseFloat(mul).toFixed(2);
            //         var aftotal = parseFloat(afmul).toFixed(2);
            //         $("#total_price").val(total);
            //         $("#total_price_af").val(aftotal);
            //         $("#carpet_price").val(total);
            //         $("#carpet_price_us").val(total);
            //
            //     }
            // }
        });

        // KACHAEE TOTAL PRICE
        $("#price").blur(function () {
            var af = $('#price').val();
            var afg = parseFloat(af).toFixed(2);
            if (isNaN(afg)) {
                $("#price").val();
            } else {
                $("#price").val(afg);
            }
            var us = $('#currency').val();
            var price = afg / us;
            var area = $('#area').val();
            if (price != '' && area != '') {
                var mul = price * area;
                var af_mul = afg * area;
                var total = parseFloat(mul).toFixed(2);
                var af_total = parseFloat(af_mul).toFixed(2);
                $("#total_price").val(total);
                $("#af_total_price").val(af_total);
            }
        });
    });
</script>
<script>
    $('.status').show();
    window.setTimeout(function () {
        $(".status").fadeTo(200, 0).slideUp(200, function () {

            $(this).remove();
        });
    }, 5000);
    

    // FORM SCRIPTS
    $(function () {

        // CONVERTING FORM DATA TO DECIMAL

        //credit amount
        $("#creditAmount").blur(function () {
            var amount = $("#creditAmount").val();
            var mainAmount = parseFloat(amount).toFixed(2);
            $("#creditAmount").val(mainAmount);
        })
        // // heightWaste
        // $("#heightWaste").blur(function () {
        //     var heightWaste = $('#heightWaste').val();
        //     var mainHeightWaste = parseFloat(heightWaste).toFixed(2);
        //     if (isNaN(mainHeightWaste)) {
        //         $("#heightWaste").val();
        //     } else {
        //         $("#heightWaste").val(mainHeightWaste);
        //     }
        // });
        // // widthWaste
        // $("#widthWaste").blur(function () {
        //     var widthWaste = $('#widthWaste').val();
        //     var mainWidthWaste = parseFloat(widthWaste).toFixed(2);
        //     if (isNaN(mainWidthWaste)) {
        //         $("#widthWaste").val();
        //     } else {
        //         $("#widthWaste").val(mainWidthWaste);
        //     }
        // });


        //ALL RECEIVEDS
        $("#ar").blur(function () {
            var c = $('#currency').val();
            var ar = $('#ar').val();
            var mainar = parseFloat(ar).toFixed(2);
            if (isNaN(mainar)) {
                $("#ar").val();
            } else {
                $("#ar").val(mainar);
                var mar = mainar / c;
                var mainarr = parseFloat(mar).toFixed(2);
                $('#arr').val(mainarr);
            }
        });

        $("#material_paid").blur(function () {
            var c = $('#currency').val();
            var total_price = $("#material-total-price").val();
            var main_total_price = parseFloat(total_price).toFixed(2);

            var paid = $("#material_paid").val();
            var mainPaid = parseFloat(paid).toFixed(2);
            var pa = mainPaid / c;
            $("#main_paid").val(pa);
            $("#material_remain").val(main_total_price - pa);

        });

    });
</script>
{{--this function prevent multiple submitting--}}

@yield('footer-plugins')
@yield('scripts')

<script type="text/javascript">
    $(document).ready(function () {
        $("form").on("submit", function () {
            $(this).submit(function () {
                return false;
            });
            return true;
        });
    });


</script>
{{--this function prevent multiple submitting--}}

</body>


</html>
