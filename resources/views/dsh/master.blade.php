<!doctype html>
<html class="no-js" lang="en">

<head>
  <title>QBIC</title>
  <!-- HTML5 Shim and Respond.js IE11 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 11]>

  <![endif]-->
  <!-- Meta -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalhable=0, minimal-ui">
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="description" content="" />
  <meta name="keywords" content="">
  <meta name="author" content="Phoenixcoded" />
  <!-- Favicon icon -->
  <link rel="icon" href="/dsh/assets/images/favicon.ico" type="image/x-icon">

  <!-- vendor css -->
  <link rel="stylesheet" href="/dsh/assets/css/style.css">
  <link rel="stylesheet" href="/dsh/assets/css/layout-rtl.css">
  <link rel="stylesheet" href="/bkoodak/style.css">
  <link rel="stylesheet" href="{{asset('dsh/assets/css/font/fonts.css')}}" />



  <link rel="stylesheet" href="{{asset('dsh/assets/select2/css/select2.min.css')}}">

  <!-- Added for New Dashboards -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    :root {
      --navy-blue: #0A192F;
      --emerald-green: #10B981;
      --gold-accent: #F59E0B;
    }

    .row-gap {
      margin-bottom: 20px;
    }

    .col-gap {
      padding-bottom: 20px;
    }

    .glass-card {
      background: #ffffff;
      background: rgba(255, 255, 255, 0.9);
      border: 1px solid rgba(0, 0, 0, 0.1);
      border-radius: 16px;
      box-shadow: 0 4px 30px rgba(0, 0, 0, 0.05);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .glass-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
    }

    .kpi-title {
      font-size: 0.9rem;
      text-transform: uppercase;
      letter-spacing: 1px;
      color: #6c757d;
      font-family: Tahoma, Arial, sans-serif;
    }

    .kpi-value {
      font-size: 1.8rem;
      font-weight: 700;
      color: #0A192F;
      font-family: Tahoma, Arial, sans-serif;
    }

    .kpi-icon {
      font-size: 2.5rem;
      opacity: 0.8;
    }

    .pipeline-step {
      flex: 1;
    }

    .pipeline-divider {
      align-self: center;
      padding: 0 10px;
    }

    /* Global Search Styles */
    .global-search-input::placeholder {
      color: rgba(255, 255, 255, 0.6);
    }

    .global-search-input:focus {
      background: rgba(255, 255, 255, 0.25);
      border-color: rgba(255, 255, 255, 0.6);
      box-shadow: none;
      outline: none;
    }

    .search-result-item {
      padding: 12px 15px;
      border-bottom: 1px solid #f1f5f9;
      transition: background 0.2s;
      cursor: pointer;
      display: block;
      text-decoration: none;
      color: inherit;
    }

    .search-result-item:hover {
      background: #f8fafc;
      text-decoration: none;
    }

    .search-result-item:last-child {
      border-bottom: none;
    }
  </style>

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
              @elseif(Auth::user()->role == 'FI')
                <p>کارمند مالی</p>
              @endif

            </div>
          </div>
          <div class="collapse" id="nav-user-link">
            <ul class="list-inline">

              <li class="list-inline-item"><a href="/logout" data-toggle="tooltip" title="Logout" class="text-danger"><i
                    class="feather icon-power"></i></a></li>
            </ul>
          </div>
        </div>

        <ul class="nav pcoded-inner-navbar ">

          @if(auth()->user()->hasAnyPermission(['view_production_dashboard', 'view_inventory_dashboard', 'view_finance_dashboard', 'view_sales_dashboard', 'view_purchases_dashboard', 'view_cost_analytics']))

          <li
            class="nav-item pcoded-hasmenu {{ request()->is('dashboard', 'dashboard/production*', 'dashboard/inventory*', 'dashboard/accounting*', 'dashboard/purchases*', 'dashboard/sales*', 'dashboard/cost-analytics*') ? 'active pcoded-trigger' : '' }}">
            <a href="#" class="nav-link"><span class="pcoded-micon"><i class="feather icon-home"></i></span><span
                class="pcoded-mtext"><b>داشبورد</b></span></a>
            <ul class="pcoded-submenu">
              <!-- <li><a href="/dashboard">داشبورد اجرایی (Executive)</a></li> -->
              @can('view_production_dashboard')
              <li><a href="/dashboard/production">داشبورد تولید (Production)</a></li>
              @endcan
              @can('view_inventory_dashboard')
              <li><a href="/dashboard/inventory">داشبورد گدام (Inventory)</a></li>
              @endcan
              @can('view_finance_dashboard')
              <li><a href="/dashboard/accounting">داشبورد مالی (Finance)</a></li>
              @endcan
              @can('view_sales_dashboard')
              <li><a href="/dashboard/sales-dashboard">داشبورد فروشات (Sales)</a></li>
              @endcan
              @can('view_purchases_dashboard')
              <li><a href="/dashboard/purchases">داشبورد خرید (Purchases)</a></li>
              @endcan
              @can('view_cost_analytics')
              <li><a href="/dashboard/cost-analytics">تحلیل مصارف (Cost Analytics)</a></li>
              @endcan
            </ul>
          </li>
          @endif

          @if(auth()->user()->hasAnyPermission([
              'view_coa', 'view_journals', 'view_mapping_rules', 'view_currencies',
              'view_pl_report', 'view_balance_sheet', 'view_trial_balance', 'view_comparative_pl', 'view_cash_flow_report', 'view_fx_exposure_report',
              'view_inventory_valuation', 'view_cost_center_performance', 'view_audit_corrections', 'view_account_ledger', 'view_customer_statement', 'view_agent_statement', 'view_different_account_statement', 'view_kachaee_team_statement', 'view_washing_team_statement', 'view_finishing_team_statement', 'view_seller_statement', 'view_employee_statement'
          ]))
            <li
              class="nav-item pcoded-hasmenu {{ (request()->is('dashboard/accounting*') && !request()->is('dashboard/accounting/warehouses*') && !request()->is('dashboard/accounting/transfers*')) ? 'active pcoded-trigger' : '' }}">
              <a href="#" class="nav-link">
                <span class="pcoded-micon"><i class="feather icon-book"></i></span>
                <span class="pcoded-mtext"><b>سیستم حسابداری</b></span>
              </a>
              <ul class="pcoded-submenu">
                @can('view_coa')
                <li><a href="{{ route('accounting.coa.index') }}">لایحه حسابات (COA)</a></li>
                @endcan
                @can('view_journals')
                <li><a href="{{ route('accounting.journals.index') }}">روزنامچه عمومی (GL)</a></li>
                @endcan
                @can('view_mapping_rules')
                <li><a href="{{ route('accounting.mappings.index') }}">تنظیمات محاسباتی</a></li>
                @endcan
                @can('view_currencies')
                <li><a href="{{ route('accounting.currencies.index') }}">مدیریت اسعار (Forensic FX)</a></li>
                @endcan

                <!-- Advanced Financial Reports (Dari Afghanistan) -->
                @if(auth()->user()->hasAnyPermission(['view_pl_report', 'view_balance_sheet', 'view_trial_balance', 'view_comparative_pl', 'view_cash_flow_report', 'view_fx_exposure_report']))
                <li class="nav-item pcoded-hasmenu">
                  <a href="#!" class="nav-link"><span class="pcoded-mtext" style="color: #4caf50;">گزارشات مالی و
                      تحلیلی</span></a>
                  <ul class="pcoded-submenu">
                    @can('view_pl_report')
                    <li><a href="{{ route('accounting.reports.profit_loss') }}">مفاد و ضرر (P&L)</a></li>
                    @endcan
                    @can('view_balance_sheet')
                    <li><a href="{{ route('accounting.reports.balance_sheet') }}">ترازنامه (بیلانس شیت)</a></li>
                    @endcan
                    @can('view_trial_balance')
                    <li><a href="{{ route('accounting.reports.trial_balance') }}" style="color: #4caf50;">بیلان آزمایشی
                        (Trial
                        Balance)</a></li>
                    @endcan
                    @can('view_comparative_pl')
                    <li><a href="{{ route('accounting.reports.comparative_pl') }}">تحلیل مقایسوی عملکرد</a></li>
                    @endcan
                    @can('view_cash_flow_report')
                    <li><a href="{{ route('accounting.reports.cash_flow') }}">جریان وجوه نقد (Cash Flow)</a></li>
                    @endcan
                    @can('view_fx_exposure_report')
                    <li><a href="{{ route('accounting.reports.fx_exposure') }}">تحلیل اسعار و نقدینگی</a></li>
                    @endcan
                  </ul>
                </li>
                @endif

                <!-- Operational & Audit Reports -->
                @if(auth()->user()->hasAnyPermission(['view_inventory_valuation', 'view_cost_center_performance', 'view_audit_corrections', 'view_account_ledger', 'view_customer_statement', 'view_agent_statement', 'view_different_account_statement', 'view_kachaee_team_statement', 'view_washing_team_statement', 'view_finishing_team_statement', 'view_seller_statement', 'view_employee_statement']))
                <li class="nav-item pcoded-hasmenu">
                  <a href="#!" class="nav-link"><span class="pcoded-mtext" style="color: #00acc1;">گزارشات عملیاتی و
                      تفتیش</span></a>
                  <ul class="pcoded-submenu">
                    @can('view_inventory_valuation')
                    <li><a href="{{ route('accounting.reports.inventory_valuation') }}">ارزش پولی موجودی گدام</a></li>
                    @endcan
                    @can('view_cost_center_performance')
                    <li><a href="{{ route('accounting.reports.cost_center_performance') }}">عملکرد دیپارتمنت‌ها</a></li>
                    @endcan
                    @can('view_audit_corrections')
                    <li><a href="{{ route('accounting.reports.audit_corrections') }}">تفتیش اصلاحات و ریورس</a></li>
                    @endcan
                    @can('view_account_ledger')
                    <li><a href="{{ route('accounting.reports.account_ledger') }}">دفتر تفصیلی حساب</a></li>
                    @endcan
                    @can('view_customer_statement')
                    <li><a href="{{ route('accounting.reports.customer_statement') }}">صورت حساب مشتری</a></li>
                    @endcan
                    @can('view_agent_statement')
                    <li><a href="{{ route('accounting.reports.agent_statement') }}">صورت حساب نماینده</a></li>
                    @endcan
                    @can('view_different_account_statement')
                    <li><a href="{{ route('accounting.reports.different_account_statement') }}">صورت حساب متفرقه</a></li>
                    @endcan
                    @can('view_kachaee_team_statement')
                    <li><a href="{{ route('accounting.reports.repair_team_statement') }}">صورت حساب تیم کچایی</a></li>
                    @endcan
                    @can('view_washing_team_statement')
                    <li><a href="{{ route('accounting.reports.washing_team_statement') }}">صورت حساب تیم شست</a></li>
                    @endcan
                    @can('view_finishing_team_statement')
                    <li><a href="{{ route('accounting.reports.finishing_team_statement') }}">صورت حساب تیم تیاری</a></li>
                    @endcan
                    @can('view_seller_statement')
                    <li><a href="{{ route('accounting.reports.string_seller_statement') }}">صورت حساب فروشندگان مواد
                        خام</a></li>
                    @endcan
                    @can('view_employee_statement')
                    <li><a href="{{ route('accounting.reports.employee_statement') }}">صورت حساب کارمندان</a></li>
                    @endcan
                  </ul>
                </li>
                @endif
              </ul>
            </li>
          @endif

          @if(auth()->user()->role != 'AO')
            @if(auth()->user()->hasAnyPermission(['view_agents', 'view_agent_statement', 'view_agent_money_requests']))

              <li
                class="nav-item pcoded-hasmenu {{ request()->is('dashboard/agents*') || request()->is('dashboard/accounting/reports/agent-statement*') ? 'active' : '' }}">
                <a href="#" class="nav-link"><span class="pcoded-micon"><i class="feather icon-layout"></i></span><span
                    class="pcoded-mtext"><b>نماینده</b></span></a>
                <ul class="pcoded-submenu">
                  @can('view_agents')
                    <li><a href="/dashboard/agents">لیست نماینده ها</a></li>
                  @endcan
                  @can('view_agent_statement')
                    <li><a href="{{ route('accounting.reports.agent_statement') }}">صورت حساب نماینده (حسابداری)</a></li>
                  @endcan

                  @can('view_agent_money_requests')
                    <li><a href="/dashboard/agent-money-request-list"> لیست درخواست های پول</a></li>
                  @endcan

                </ul>
              </li>

            @endif
          @endif

          @if(auth()->user()->hasAnyPermission(['view_buy_carpets']))
            <li
              class="nav-item pcoded-hasmenu {{ request()->is(['dashboard/contract-carpet*', 'dashboard/list-weight', 'list-buy-carpet', 'check-book']) ? 'active' : '' }}">
              <a href="#" class="nav-link"><span class="pcoded-micon"><i class="feather icon-layout"></i></span><span
                  class="pcoded-mtext"><b>قالین</b></span></a>
              <ul class="pcoded-submenu">
                @can('view_purchased_carpets_report')
                  <li><a href="{{ route('purchased.carpets.index') }}" style="color: #00acc1; font-weight: bold;">گزارش
                      قالین های خرید شده</a></li>
                @endcan
                <!-- <li><a href="/dashboard/contract-carpet">قالین های قراردادی</a></li> -->
                <!-- <li><a href="/dashboard/list-weight">قالین های وزنی</a></li> -->
                @can('view_buy_carpets')
                  <li><a href="/dashboard/list-buy-carpet">لیست قالین های خرید شده</a></li>
                @endcan
                @can('view_purchase_bills')
                  <li><a href="/dashboard/check-book">بل‌های خرید (Purchase Bills)</a></li>
                @endcan
                @can('view_carpet_types')
                  <li><a href="/dashboard/carpet-types">نوعیت قالین</a></li>
                @endcan
                @can('view_carpet_qualities')
                  <li><a href="/dashboard/carpet-qualities">کوالتی قالین</a></li>
                @endcan

              </ul>
            </li>
          @endif

          @if(auth()->user()->hasAnyPermission(['view_material_purchases', 'view_material_stock']))



            <li class="nav-item pcoded-hasmenu">
              <a href="#" class="nav-link "><span class="pcoded-micon"><i class="feather icon-layout"></i></span><span
                  class="pcoded-mtext"><b>مواد خام</b></span></a>
              <ul class="pcoded-submenu">
                @can('view_material_purchases')
                  <li><a href="/dashboard/material-purchase">خرید مواد خام</a></li>
                @endcan
                @can('view_raw_material_bills')
                  <li><a href="/dashboard/raw-material-purchase-bills">بل‌های خرید مواد خام</a></li>
                @endcan
                @can('view_purchase_material_requests')
                  <li><a href="/dashboard/purchase-material-request-list"> لیست درخواست خرید مواد خام</a></li>
                @endcan
                @can('view_material_sales')
                  <li><a href="/dashboard/material-sales">فروش مواد خام</a></li>
                @endcan
                @can('view_material_sale_requests')
                  <li><a href="/dashboard/material-sale-request-list"> لیست درخواست فروش مواد خام</a></li>
                @endcan
                @can('view_material_stock')
                  <li><a href="/dashboard/material-stock">گدام مواد خام</a></li>
                @endcan
                @can('view_string_sellers')
                  <li><a href="/dashboard/string-seller">فروشنده مواد خام</a></li>
                @endcan
                @can('view_seller_statement')
                  <li><a href="{{ route('accounting.reports.string_seller_statement') }}">صورت حساب فروشنده خام</a></li>
                @endcan
                @can('view_seller_money_requests')
                  <li><a href="/dashboard/string-seller-request-list"> لیست درخواست پول فروشنده مواد خام</a></li>
                @endcan

                @can('view_material_categories')
                  <li><a href="/dashboard/material-category">کتگوری مواد</a></li>
                @endcan
                @can('view_material_types')
                  <li><a href="/dashboard/materialtypes">نوعیت مواد</a></li>
                @endcan

                <!-- <li><a href="/dashboard/material-accounts">دخل و خرچ مواد خام</a></li> -->
                <!-- @if(auth()->user()->can('approve_material_account_requests')) -->
                <!-- <li><a href="/dashboard/material-account-request-list"> لیست درخواست اکونت های مواد خام</a></li> -->
                <!-- @endif -->
              </ul>
            </li>
          @endif

          @if(auth()->user()->hasAnyPermission(['view_different_accounts', 'view_different_account_statement', 'view_different_account_money_requests']))
            <li
              class="nav-item pcoded-hasmenu {{ request()->is('dashboard/different-account*', 'dashboard/accounting/reports/different-account-statement*') ? 'active' : '' }}">
              <a href="#" class="nav-link"><span class="pcoded-micon"><i class="feather icon-home"></i></span><span
                  class="pcoded-mtext"><b>حساب متفرقه</b></span></a>
              <ul class="pcoded-submenu">
                @can('view_different_accounts')
                  <li><a href="/dashboard/different-account">حساب متفرقه</a></li>
                @endcan
                @can('view_different_account_statement')
                  <li><a href="{{ route('accounting.reports.different_account_statement') }}">صورت حساب متفرقه</a></li>
                @endcan
                @can('view_different_account_money_requests')
                  <li><a href="/dashboard/different-account-money-request-list"> لیست درخواست های پول</a></li>
                @endcan
              </ul>

            </li>

          @endif

          @if(auth()->user()->hasAnyPermission(['view_carpet_repairs', 'view_kachaee_teams', 'view_kachaee_team_statement', 'view_kachaee_batches', 'create_kachaee_batch', 'view_kachaee_money_requests']))
            <li
              class="nav-item pcoded-hasmenu {{ request()->is('carpet-repair*', 'kachaee-team', 'dashboard/accounting/reports/repair-team-statement*') ? 'active' : '' }}">
              <a href="#" class="nav-link "><span class="pcoded-micon"><i class="feather icon-layout"></i></span><span
                  class="pcoded-mtext"><b>کچایی</b></span></a>
              <ul class="pcoded-submenu">
                @can('view_carpet_repairs')
                  <li><a href="/dashboard/carpet-repair">کچای قالین ها</a></li>
                @endcan
                @can('view_kachaee_teams')
                  <li><a href="/dashboard/kachaee-team">تیم کچایی</a></li>
                @endcan
                @can('view_kachaee_team_statement')
                  <li><a href="{{ route('accounting.reports.repair_team_statement') }}">صورت حساب کچایی</a></li>
                @endcan
                @canany(['view_kachaee_batches', 'create_kachaee_batch'])
                  <li><a href="/dashboard/batches/kachaee">نمبرهای کچایی (KCH)</a></li>
                @endcanany
                @if(auth()->user()->can('view_kachaee_money_requests'))
                  <li><a href="/dashboard/kachaee-money-request-list"> لیست درخواست های پول</a></li>
                @endif
              </ul>
            </li>
          @endif

          @if(auth()->user()->hasAnyPermission(['view_carpet_washes', 'view_washing_teams', 'view_washing_team_statement', 'view_washing_batches', 'create_washing_batch', 'view_washing_money_requests']))

            <li
              class="nav-item pcoded-hasmenu {{ request()->is('carpet-wash*', 'washing-team', 'dashboard/accounting/reports/washing-team-statement*') ? 'active' : '' }}">
              <a href="#" class="nav-link "><span class="pcoded-micon"><i class="feather icon-layout"></i></span><span
                  class="pcoded-mtext"><b>شست</b></span></a>
              <ul class="pcoded-submenu">

                @can('view_carpet_washes')
                  <li><a href="/dashboard/carpet-wash">شست قالین ها</a></li>
                @endcan
                @can('view_washing_teams')
                  <li><a href="/dashboard/washing-team"> تیم شست</a></li>
                @endcan
                @can('view_washing_team_statement')
                  <li><a href="{{ route('accounting.reports.washing_team_statement') }}">صورت حساب شست</a></li>
                @endcan
                @canany(['view_washing_batches', 'create_washing_batch'])
                  <li><a href="/dashboard/batches/wash">نمبرهای شست (Wash)</a></li>
                @endcanany
                @if(auth()->user()->can('view_washing_money_requests'))
                  <li><a href="/dashboard/washing-money-request-list"> لیست درخواست های پول</a></li>
                @endif
              </ul>
            </li>
          @endif

          @if(auth()->user()->hasAnyPermission(['view_finishing_centers', 'view_finishing_teams', 'view_finishing_team_statement', 'view_finishing_batches', 'create_finishing_batch', 'view_refinish_requests', 'view_finishing_money_requests']))
            <li
              class="nav-item pcoded-hasmenu {{ request()->is('finishing-center*', 'finish-team', 'finish-team-category', 'dashboard/accounting/reports/finishing-team-statement*') ? 'active' : '' }}">
              <a href="#" class="nav-link "><span class="pcoded-micon"><i class="feather icon-layout"></i></span><span
                  class="pcoded-mtext"><b>تیاری</b></span></a>
              <ul class="pcoded-submenu">
                @can('view_finishing_centers')
                  <li><a href="/dashboard/finishing-center">بخش های تیاری</a></li>
                @endcan
                @can('view_finishing_teams')
                  <li><a href="/dashboard/finish-team"> تیم تیاری</a></li>
                @endcan
                @can('view_finishing_team_statement')
                  <li><a href="{{ route('accounting.reports.finishing_team_statement') }}">صورت حساب تیاری</a></li>
                @endcan
                @canany(['view_finishing_batches', 'create_finishing_batch'])
                  <li><a href="/dashboard/batches/finish">نمبرهای تیاری (TA)</a></li>
                @endcanany

                @can('view_refinish_requests')
                  <li><a href="/dashboard/refinish-request-list"> لیست درخواست های دوباره تیاری</a></li>
                @endcan
                @can('view_finishing_money_requests')
                  <li><a href="/dashboard/finishing-money-request-list"> لیست درخواست های پول</a></li>
                @endcan
                {{--<li><a href="/dashboard/finish-team-category"> دسته بندی تیاری</a></li>--}}
              </ul>
            </li>
          @endif

          @if(auth()->user()->hasAnyPermission(['view_carpet_stock']))
            <li class="nav-item pcoded-hasmenu {{ request()->is('carpet-stock*') ? 'active' : '' }}">
              <a href="/dashboard/carpet-stock" class="nav-link "><span class="pcoded-micon"><i
                    class="feather icon-home"></i></span><span class="pcoded-mtext"><b> قالین آماده به
                    فروش</b></span></a>

            </li>
          @endif

          @if(auth()->user()->hasAnyPermission(['view_sales', 'view_invoices', 'create_invoice']))
            <li class="nav-item pcoded-hasmenu {{ request()->is('sales*') || request()->is('invoices*') ? 'active' : '' }}">
              <a href="#" class="nav-link "><span class="pcoded-micon"><i class="feather icon-layout"></i></span><span
                  class="pcoded-mtext"><b>فروشات</b></span></a>
              <ul class="pcoded-submenu">
                @can('view_sales')
                <li><a href="/dashboard/sales"> لیست فروشات</a></li>
                @endcan
                <!-- <li><a href="/dashboard/packing-list">پکینگ لیست</a></li> -->
                @canany(['view_invoices', 'create_invoice'])
                <li><a href="/dashboard/invoices">لیست انوایس ها</a></li>
                @endcanany
              </ul>
            </li>

          @endif

          @if(auth()->user()->hasAnyPermission(['view_customer_orders']))
            <li class="nav-item pcoded-hasmenu {{ request()->is('dashboard/customer-orders*') ? 'active' : '' }}">
              <a href="/dashboard/customer-orders" class="nav-link"><span class="pcoded-micon"><i
                    class="feather icon-home"></i></span><span class="pcoded-mtext"><b>سفارشات مشتریان</b></span></a>
            </li>
          @endif

          @if(auth()->user()->hasAnyPermission(['view_assets_accounts', 'view_assets_report']))
            <li
              class="nav-item pcoded-hasmenu {{ request()->is('dashboard/assets-accounts*') || request()->is('dashboard/assets-report*') ? 'active pcoded-trigger' : '' }}">
              <a href="#" class="nav-link"><span class="pcoded-micon"><i class="feather icon-home"></i></span><span
                  class="pcoded-mtext"><b>اجناس ثابت شرکت</b></span></a>
              <ul class="pcoded-submenu">
                @can('view_assets_accounts')
                <li><a href="/dashboard/assets-accounts">حسابات اجناس</a></li>
                @endcan
                @can('view_assets_report')
                <li><a href="{{ route('assets.report') }}" style="color: #00acc1; font-weight: bold;">گزارش اجناس ثابت</a>
                </li>
                @endcan
              </ul>
            </li>
          @endif

          @if(auth()->user()->hasAnyPermission(['view_warehouse_inventory_report', 'view_carpet_stock_report', 'view_raw_material_stock_report', 'view_warehouses', 'view_inventory_transfers', 'view_warehouse_movements']))
              <li
                class="nav-item pcoded-hasmenu {{ (request()->is('dashboard/accounting/warehouses*') || request()->is('dashboard/accounting/transfers*') || request()->is('dashboard/accounting/warehouse-movements*') || request()->is('dashboard/inventory/reports*')) ? 'active pcoded-trigger' : '' }}">
                <a href="#" class="nav-link">
                  <span class="pcoded-micon"><i class="feather icon-package"></i></span>
                  <span class="pcoded-mtext"><b>مدیریت گدام‌ها (Warehouse)</b></span>
                </a>
                <ul class="pcoded-submenu">
                  @can('view_warehouse_inventory_report')
                    <li><a href="{{ route('inventory.reports.index') }}" style="color: #00acc1;">گزارش موجودی گدام (ERP)</a></li>
                  @endcan
                  @can('view_carpet_stock_report')
                    <li><a href="{{ route('accounting.warehouses.carpet_stock') }}" style="color: #10b981;">موجودی قالین‌ها (Carpet Stock)</a></li>
                  @endcan
                  @can('view_raw_material_stock_report')
                    <li><a href="{{ route('accounting.warehouses.raw_material_stock') }}" style="color: #6366f1;">موجودی مواد خام (Raw Material Stock)</a></li>
                  @endcan
                  @can('view_warehouses')
                    <li><a href="{{ route('accounting.warehouses.index') }}">مدیریت گدام‌ها (Locations)</a></li>
                  @endcan
                  @can('view_inventory_transfers')
                    <li><a href="{{ route('accounting.transfers.index') }}">انتقال جنس بین گدام‌ها</a></li>
                  @endcan
                  @can('view_warehouse_movements')
                    <li><a href="{{ route('accounting.warehouses.movements') }}">گزارش ورودی و خروجی (IN/OUT)</a></li>
                  @endcan
                </ul>
              </li>
          @endif

        @if(auth()->user()->hasAnyPermission(['view_customers']))
          <li class="nav-item pcoded-hasmenu">
            <a href="#" class="nav-link "><span class="pcoded-micon"><i class="feather icon-home"></i></span><span
                class="pcoded-mtext"><b>مشتری ها</b></span></a>
            <ul class="pcoded-submenu">
              <li><a href="/dashboard/customers">لیست مشتریان</a></li>
              @can('view_customer_statement')
              <li><a href="{{ route('accounting.reports.customer_statement') }}">صورت حساب مشتری</a></li>
              @endcan
              @can('view_customer_money_requests')
                <li><a href="/dashboard/customer-request-list"> لیست درخواست پول مشتریان</a></li>
              @endcan
              @can('view_ar_aging_report')
              <li><a href="{{ route('accounting.reports.ar-aging') }}" style="color: #4caf50;">تحلیل بدهی مشتریان
                  (Aging)</a></li>
              @endcan

            </ul>

          </li>
        @endif

        <!-- @if(auth()->user()->hasAnyPermission(['view_contract_carpets']))
            <li
              class="nav-item pcoded-hasmenu {{ request()->is('add-office-credit*', 'money-request-list', 'office-cash-book') ? 'active' : '' }}">
              <a href="#" class="nav-link "><span class="pcoded-micon"><i class="feather icon-layout"></i></span><span
                  class="pcoded-mtext"><b>دخل و خرچ پول</b></span></a>
              <ul class="pcoded-submenu">
                <li><a href="/dashboard/add-office-credit">اضافه کردن پول به دخل</a></li>

                @if(auth()->user()->can('approve_carpet_requests'))
                  <li><a href="/dashboard/money-request-list"> لیست درخواست های پول</a></li>
                @endif
                <li><a href="/dashboard/office-cash-book">مصارف</a></li>

              </ul>
            </li>
          @endif -->

        @if(auth()->user()->hasAnyPermission(['view_monthly_expenses']))
          <li class="nav-item pcoded-hasmenu {{ request()->is('dashboard/monthly-expense-accounts*') ? 'active' : '' }}">
            <a href="/dashboard/monthly-expense-accounts" class="nav-link"><span class="pcoded-micon"><i
                  class="feather icon-home"></i></span><span class="pcoded-mtext"><b>مصارف ماهانه</b></span></a>
          </li>
        @endif

        @if(auth()->user()->hasAnyPermission(['view_employees', 'view_employee_statement', 'view_payroll', 'view_employee_money_requests', 'view_employee_departments']))
          <li
            class="nav-item pcoded-hasmenu {{ request()->is('dashboard/office-employee*', 'dashboard/accounting/reports/employee-statement*', 'dashboard/payroll*', 'dashboard/employee-request-list*', 'dashboard/employee-department*') ? 'active pcoded-trigger' : '' }}">
            <a href="#" class="nav-link "><span class="pcoded-micon"><i class="feather icon-users"></i></span><span
                class="pcoded-mtext"><b>کارمندان دفتر</b></span></a>
            <ul class="pcoded-submenu">
              @can('view_employees')
              <li><a href="/dashboard/office-employee">لیست کارمندان</a></li>
              @endcan
              @can('view_employee_statement')
              <li><a href="{{ route('accounting.reports.employee_statement') }}">صورت حساب کارمندان</a></li>
              @endcan
              @can('view_payroll')
              <li><a href="/dashboard/payroll">اجرای معاشات (Payroll)</a></li>
              @endcan
              @if(auth()->user()->can('view_employee_money_requests'))
                <li><a href="/dashboard/employee-request-list"> لیست درخواست پول کارمندان</a></li>
              @endif
              @can('view_employee_departments')
              <li><a href="/dashboard/employee-department">دیپارتمنت کارمندان</a></li>
              @endcan
            </ul>
          </li>
        @endif

        @if(auth()->user()->hasAnyPermission(['view_trial_balance', 'view_inventory_valuation']))
          <li class="nav-item pcoded-hasmenu">
            <a href="#" class="nav-link "><span class="pcoded-micon"><i class="feather icon-layout"></i></span><span
                class="pcoded-mtext"><b>گزارشات</b></span></a>
            <ul class="pcoded-submenu">

              <!-- <li><a href="/dashboard/agent_balance_report">صورت حساب نماینده ها</a></li>
                                    <li><a href="/dashboard/different_account_balance_report">صورت حساب متفرقه جدید </a></li>
                                    <li><a href="/dashboard/kachaee_team_balance_report">صورت حساب تیم کچایی </a></li>
                                    <li><a href="/dashboard/washing_team_balance_report">صورت حساب تیم شست </a></li>
                                    <li><a href="/dashboard/finishing_team_balance_report">صورت حساب تیم تیاری </a></li>
                                    <li><a href="/dashboard/string_seller_balance_report">صورت حساب فروشندگان مواد خام </a></li>
                                    <li><a href="/dashboard/customer_balance_report">صورت حساب مشتری ها </a></li>
                                    <li><a href="/dashboard/expense_report">گزارش مصارف</a></li>
                                    <li><a href="/dashboard/purchase_carpet_report">گزارش خرید قالین</a></li>
                                    <li><a href="/dashboard/sales_report">گزارش فروشات</a></li> -->
              @can('view_trial_balance')
              <li><a href="{{ route('accounting.reports.trial-balance') }}" style="color: #4caf50;">بیلان آزمایشی (Trial
                  Balance)</a></li>
              @endcan
              
              @can('view_inventory_valuation')
              <hr style="margin: 5px 0; border-top: 1px solid rgba(255,255,255,0.1);">
              <li><a href="{{ route('inventory.reports.wip') }}" style="color: #00acc1;">گزارش سرمایه در حال کار
                  (WIP)</a></li>
              @endcan
              <!--<li><a href="/admin/benefit_lose_report">گزارش مفاد و نقص</a></li>-->


              <!--<li><a href="/admin/customer_demand">طلبات مردم</a></li>-->

              <!--<li><a href="/admin/customer_debtor">باقیات مردم</a></li>-->

            </ul>
          </li>

        @endif

        @if(auth()->user()->hasAnyPermission(['manage_roles_and_permissions', 'view_users', 'view_phone_book', 'view_provinces', 'view_agent_employees', 'view_orders', 'view_activities']))
          <li class="nav-item pcoded-hasmenu">
            <a href="#" class="nav-link "><span class="pcoded-micon"><i class="feather icon-layout"></i></span><span
                class="pcoded-mtext"><b>تنظیمات</b></span></a>
            <ul class="pcoded-submenu">
              @can('view_users')
                <li><a href="/dashboard/users">کاربران سیستم</a></li>
              @endcan
              @can('manage_roles_and_permissions')
                <li><a href="/dashboard/roles">نقش ها و دسترسی ها</a></li>
              @endcan

              @can('view_phone_book')
                <li><a href="/dashboard/phone-book">دفترچه تلفون</a></li>
              @endcan
              @can('view_provinces')
                <li><a href="/dashboard/provinces">ولایات</a></li>
              @endcan
              @can('view_agent_employees')
                <li><a href="/dashboard/agent-employees">کارگرها</a></li>
              @endcan
              @can('view_orders')
                <li><a href="/dashboard/carpet-orders">شماره فرمایش</a></li>
              @endcan
              @can('view_activities')
                <li><a href="/dashboard/activities">نمایش فعالیت ها</a></li>
              @endcan

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
        QBIC

      </a>
      <a href="#!" class="mob-toggler">
        <i class="feather icon-more-vertical"></i>
      </a>
    </div>

    <!-- Global Search -->
    <div class="global-search-container d-none d-md-block"
      style="position: relative; margin-right: 20px; width: 450px; z-index: 1050; align-self: center;">
      <div class="input-group" style="position: relative;">
        <input type="text" class="form-control global-search-input"
          placeholder="جستجوی قالین بر اساس ID یا نقشه (مثال: 1000 یا QB1000)" autocomplete="off"
          style="border-radius: 20px; padding-right: 40px; background: rgba(255,255,255,0.15); color: white; border: 1px solid rgba(255,255,255,0.3); backdrop-filter: blur(5px); transition: all 0.3s ease; height: 40px;">
        <div
          style="position: absolute; right: 15px; top: 10px; color: rgba(255,255,255,0.8); z-index: 10; pointer-events: none;">
          <i class="feather icon-search"></i>
        </div>
      </div>
      <div class="global-search-results"
        style="position: absolute; top: 100%; left: 0; right: 0; background: white; z-index: 1060; width: 100%; max-height: 400px; overflow-y: auto; border-radius: 12px; margin-top: 10px; box-shadow: 0 10px 40px rgba(0,0,0,0.2); display: none; padding: 0; border: none;">
        <!-- Results will be injected here -->
      </div>
    </div>

    <div style="position: absolute; left: 55px;">
      <a href="/dashboard/close-to-end-customer-order" title="لیست سفارشات رو به اتمام">

        @php
            $unreadCount = auth()->check() ? auth()->user()->unreadNotifications->count() : 0;
        @endphp

        @if($unreadCount > 0)
          <span class="badge badge-danger" style="position: absolute; top: 20px; left: 15px; border-radius: 50%; padding: 4px 6px; font-size: 12px; font-weight: bold; box-shadow: 0 0 5px rgba(0,0,0,0.3);">{{$unreadCount}}</span>
        @endif
        <i class="fa fa-bell" style="font-size: 30px;margin-top: 30px;color: white;">
        </i></a>
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
              <img src="/printStyle/header.png" height="330px" width="1110px" style="margin-right: 5px;"
                alt="Header Logo">
            </div>
            <div id="SC" style="min-height: 990px;background-color: white;"></div>
            <div id="footerimage" style="background-color: white;">
              {{--<img src="/printStyle/Footer.png" alt="Footer Logo">--}}
            </div>
          </div>
        </div>
        @if ($errors->any())
          <div class="container-fluid pt-3 hideOnPrint">
            <div class="alert alert-danger alert-dismissible fade show border-0 rounded-lg p-3 mb-0" role="alert"
              style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">
              <h6 class="font-weight-bold mb-2"><i class="fa fa-times-circle mr-2"></i> لطفا خطاهای زیر را برطرف کنید:
              </h6>
              <ul class="mb-0 pl-3">
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
              <button type="button" class="close" data-dismiss="alert" aria-label="Close"
                style="color: #ef4444; opacity: 0.8; line-height: 1.5; padding: 0.75rem 1.25rem;">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
          </div>
        @endif
        @if(session("status"))
          <div class="container-fluid pt-3 hideOnPrint">
            <div class="alert alert-success alert-dismissible fade show border-0 rounded-lg p-3 mb-0" role="alert"
              style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
              <p class="mb-0 font-weight-bold"><i class="fa fa-check-circle mr-2"></i> {{session('status')}}</p>
              <button type="button" class="close" data-dismiss="alert" aria-label="Close"
                style="color: #10b981; opacity: 0.8; line-height: 1.5; padding: 0.75rem 1.25rem;">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
          </div>
        @endif
        @if(session("error"))
          <div class="container-fluid pt-3 hideOnPrint">
            <div class="alert alert-danger alert-dismissible fade show border-0 rounded-lg p-3 mb-0" role="alert"
              style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">
              <p class="mb-0 font-weight-bold"><i class="fa fa-times-circle mr-2"></i> {{session('error')}}</p>
              <button type="button" class="close" data-dismiss="alert" aria-label="Close"
                style="color: #ef4444; opacity: 0.8; line-height: 1.5; padding: 0.75rem 1.25rem;">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
          </div>
        @endif
        @yield('content')

        {{--
      </div>--}}
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
  {{--
  <script src="/dsh/jquery-1.12.4.min.js"></script>--}}
  {{--<!-- Warning Section Ends -->--}}
  <script src="/dsh/toastr/toastr.min.js"></script>
  <!-- Required Js -->
  <script src="/dsh/assets/js/vendor-all.min.js"></script>
  <script src="/dsh//assets/js/plugins/bootstrap.min.js"></script>
  <script src="/dsh/assets/js/ripple.js"></script>
  <script src="/dsh/assets/js/pcoded.min.js"></script>
  <script src="/dsh/assets/js/menu-setting.min.js"></script>

  <!-- Apex Chart -->
  {{--
  <script src="/dsh/assets/js/plugins/apexcharts.min.js"></script>--}}
  <!-- custom-chart js -->
  {{--
  <script src="/dsh/assets/js/pages/dashboard-main.js"></script>--}}
  <script src="/dsh/swal/swal.min.js"></script>
  <script src="/dsh/parsleyjs/dist/parsley.min.js"></script>

  <!-- Added for New Dashboards -->
  <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

  <!--sabeqa-->
  <script src="{{asset('dsh/assets/select2/js/select2.js')}}"></script>


  <!-- sweet alert Js -->
  <script src="/dsh/assets/js/plugins/sweetalert.min.js"></script>
  <script src="/dsh/assets/js/pages/ac-alert.js"></script>

  <script src="{{\Illuminate\Support\Facades\URL::asset('dsh/assets/xlsx/dist/xlsx.core.min.js')}}"></script>
  <script src="{{\Illuminate\Support\Facades\URL::asset('dsh/assets/file-saverjs/FileSaver.min.js')}}"></script>
  <script src="{{\Illuminate\Support\Facades\URL::asset('dsh/assets/blobjs/Blob.min.js')}}"></script>

    <script
      src="{{\Illuminate\Support\Facades\URL::asset('dsh/assets/tableexport/dist/js/tableexport.min.js')}}"></script>
  <script src="{{asset('dsh/assets/js/pages/form-select-custom.js')}}"></script>
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

    // Global Search AJAX
    $(document).ready(function () {
      var searchTimeout;

      $(document).on('input', '.global-search-input', function () {
        clearTimeout(searchTimeout);
        var query = $(this).val().trim();
        var parentContainer = $(this).closest('.global-search-container').find('.global-search-results');

        console.log("Global search triggered for: " + query);

        if (query.length < 1) {
          parentContainer.css('display', 'none').empty();
          return;
        }

        // Show loading
        parentContainer.css('display', 'block').html('<div class="p-3 text-center text-muted"><i class="feather icon-loader" style="animation: spin 1s linear infinite; display: inline-block;"></i> در حال جستجو...</div>');

        searchTimeout = setTimeout(function () {
          $.ajax({
            url: '/dashboard/global-search/carpets',
            type: 'GET',
            dataType: 'json',
            data: { q: query },
            success: function (data) {
              parentContainer.empty();
              console.log("Search results:", data);

              if (!data || data.length === 0) {
                var emptyHtml = '<div class="p-4 text-center">' +
                  '<i class="feather icon-search text-muted" style="font-size: 2rem; opacity: 0.5;"></i>' +
                  '<p class="mt-2 mb-0 text-muted" style="font-weight: bold;">هیچ قالینی با این مشخصات یافت نشد!</p>' +
                  '<small class="text-muted">شماره نقشه یا ID دیگری را امتحان کنید.</small>' +
                  '</div>';
                parentContainer.html(emptyHtml);
                return;
              }

              var html = '<div class="px-3 py-2 bg-light border-bottom" style="font-size: 11px; font-weight: bold; color: #64748b;">نتایج جستجو</div>';

              for (var i = 0; i < data.length; i++) {
                var item = data[i];
                html += '<a href="' + item.url + '" class="search-result-item">' +
                  '<div class="d-flex justify-content-between align-items-center">' +
                  '<div>' +
                  '<div style="font-weight: 800; color: #1e40af; font-size: 14px; margin-bottom: 2px;">' +
                  '<i class="feather icon-target mr-1"></i> ' + item.carpet_no +
                  '</div>' +
                  '<div style="font-size: 11px; color: #64748b;">' +
                  'نقشه: <span style="color: #0f172a; font-weight: bold;">' + item.map_number + '</span> | ' +
                  'نوع: ' + item.type + ' | ' +
                  'نماینده: ' + item.agent +
                  '</div>' +
                  '</div>' +
                  '<div class="text-right">' +
                  '<span class="badge badge-light-primary" style="font-size: 10px; margin-bottom: 4px;">' + item.status + '</span>' +
                  '<div style="font-size: 11px; font-weight: bold; color: #334155; direction: ltr;">' + item.area + ' m²</div>' +
                  '</div>' +
                  '</div>' +
                  '</a>';
              }

              parentContainer.html(html);
            },
            error: function (xhr, status, error) {
              console.error("Search AJAX Error:", error);
              parentContainer.html('<div class="p-3 text-center text-danger">خطا در برقراری ارتباط</div>');
            }
          });
        }, 300);
      });

      // Hide when clicked outside
      $(document).on('click', function (e) {
        if (!$(e.target).closest('.global-search-container').length) {
          $('.global-search-results').css('display', 'none');
        }
      });

      // Show again when input is focused if there's a value
      $(document).on('focus', '.global-search-input', function () {
        var parentContainer = $(this).closest('.global-search-container').find('.global-search-results');
        if ($(this).val().trim().length > 0 && parentContainer.children().length > 0) {
          parentContainer.css('display', 'block');
        }
      });
    });

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
      // WASHING TOTAL PRICE (ERP INTEGRATED)
      $("#wprice,#wheight,#wwidth,#exchange_rate,#currency_code").on('blur change', function () {
        var unitPrice = parseFloat($('#wprice').val()) || 0;
        var area = parseFloat($('#warea').val()) || 0;
        var exchangeRate = parseFloat($('#exchange_rate').val()) || 1;
        var currencyCode = $('#currency_code').val();

        if (unitPrice > 0 && area > 0) {
          var totalPriceInSelectedCurrency = unitPrice * area;
          var baseAmountUSD = 0;

          if (currencyCode === 'AFN') {
            baseAmountUSD = totalPriceInSelectedCurrency / exchangeRate;
          } else {
            baseAmountUSD = totalPriceInSelectedCurrency;
          }

          $("#af_total_price").val(totalPriceInSelectedCurrency.toFixed(2));
          $("#total_price").val(baseAmountUSD.toFixed(2));
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
    $('.error').show();
    window.setTimeout(function () {
      $(".error").fadeTo(200, 0).slideUp(200, function () {

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