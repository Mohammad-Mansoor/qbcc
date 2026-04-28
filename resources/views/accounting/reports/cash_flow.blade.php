@extends('dsh.master')

@section('content')
<div class="container-fluid no-print-padding">
    <br class="no-print">
    
    <!-- Header Section -->
    <div class="row mb-4 no-print">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm" style="border-radius: 15px; background: linear-gradient(45deg, #006064, #00838f);">
                <div class="card-body p-4 text-white">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h3 class="text-white font-weight-bold mb-1">گزارش جریان وجوه نقد (Cash Flow)</h3>
                            <p class="mb-0 opacity-80">روش غیر مستقیم - تحلیل منابع و مصارف نقدینگی شرکت</p>
                        </div>
                        <div class="col-md-4 text-right">
                            <button type="button" onclick="window.print()" class="btn btn-white text-info font-weight-bold px-4 rounded-pill shadow">
                                <i class="feather icon-printer mr-2"></i> چاپ گزارش (Print)
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Period Filter -->
    <div class="row mb-4 no-print">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                <div class="card-body py-3">
                    <form action="{{ route('accounting.reports.cash_flow') }}" method="GET">
                        <div class="row align-items-center">
                            <div class="col-md-4">
                                <div class="text-muted small"><i class="feather icon-calendar mr-1"></i> دوره گزارش: <strong>{{ $startDate }}</strong> الی <strong>{{ $endDate }}</strong></div>
                            </div>
                            <div class="col-md-8 d-flex justify-content-end">
                                <div class="col-md-4 px-1">
                                    <input type="date" name="start_date" value="{{ $startDate }}" class="form-control form-control-sm rounded-pill">
                                </div>
                                <div class="col-md-4 px-1">
                                    <input type="date" name="end_date" value="{{ $endDate }}" class="form-control form-control-sm rounded-pill">
                                </div>
                                <button type="submit" class="btn btn-info btn-sm rounded-pill px-4 shadow-sm">بروزرسانی</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row justify-content-center">
        <div class="col-lg-10 col-md-12">
            <div class="printable-document">
                
                <!-- Print Branding -->
                <div class="d-none d-print-block text-center mb-5">
                    <h1 class="font-weight-bold text-dark mb-1">QASIMI BROTHERS CARPET CO.</h1>
                    <h3 class="text-muted">صورت جریان وجوه نقد (Cash Flow Statement)</h3>
                    <p class="font-weight-bold border-bottom pb-2">بازه زمانی: {{ $startDate }} الی {{ $endDate }}</p>
                </div>

                <!-- 1. OPERATING ACTIVITIES -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px; border-right: 6px solid #00acc1 !important;">
                    <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                        <h5 class="font-weight-bold text-dark mb-0">۱. فعالیت‌های عملیاتی (Operating Activities)</h5>
                        <div class="badge badge-light-info p-2 rounded">بخش عملیات اصلی شرکت</div>
                    </div>
                    <div class="card-body px-4 pb-4">
                        <p class="text-muted small mb-4">این بخش نشان می‌دهد که عملیات اصلی فروش و خرید چقدر نقدینگی تولید کرده است.</p>
                        
                        <table class="table table-hover">
                            <tbody>
                                <tr>
                                    <td class="py-3 font-weight-bold">سود خالص دوره (Net Profit)</td>
                                    <td class="py-3 text-right font-weight-bold text-dark" style="font-size: 1.1rem;">{{ number_format($net_profit, 2) }}</td>
                                </tr>
                                <tr><td colspan="2" class="bg-light py-1 small font-weight-bold text-muted px-3 text-right">تعدیلات مربوط به سرمایه در گردش:</td></tr>
                                
                                <tr>
                                    <td class="py-3">تغییر در حساب‌های دریافتنی (Account Receivables)</td>
                                    <td class="py-3 text-right {{ $adjustments['receivables'] < 0 ? 'text-danger' : 'text-success' }}">
                                        {{ $adjustments['receivables'] > 0 ? '+' : '' }}{{ number_format($adjustments['receivables'], 2) }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-3">تغییر در موجودی کالا (Inventory)</td>
                                    <td class="py-3 text-right {{ $adjustments['inventory'] < 0 ? 'text-danger' : 'text-success' }}">
                                        {{ $adjustments['inventory'] > 0 ? '+' : '' }}{{ number_format($adjustments['inventory'], 2) }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-3 border-bottom">تغییر در حساب‌های پرداختنی (Account Payables)</td>
                                    <td class="py-3 text-right border-bottom {{ $adjustments['payables'] < 0 ? 'text-danger' : 'text-success' }}">
                                        {{ $adjustments['payables'] > 0 ? '+' : '' }}{{ number_format($adjustments['payables'], 2) }}
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr class="font-weight-bold" style="background: #f0fbfc;">
                                    <td class="py-3">خالص جریان نقد از فعالیت‌های عملیاتی</td>
                                    <td class="py-3 text-right text-info" style="font-size: 1.2rem;">{{ number_format($net_cash_operating, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>

                        <!-- Instructional Tip -->
                        <div class="mt-3 p-3 bg-light rounded border-left" style="border-left: 4px solid #00acc1 !important;">
                            <small class="text-dark d-block mb-1 font-weight-bold"><i class="feather icon-info mr-1"></i> نکته آموزشی (Accounting Tip):</small>
                            <small class="text-muted">در روش غیر مستقیم، افزایش در طلبات (Assets) به معنای مصرف نقد است (منفی) و افزایش در بدهی‌ها (Liabilities) به معنای باقی ماندن نقد در شرکت است (مثبت).</small>
                        </div>
                    </div>
                </div>

                <!-- 2. INVESTING ACTIVITIES -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px; border-right: 6px solid #fbc02d !important;">
                    <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                        <h5 class="font-weight-bold text-dark mb-0">۲. فعالیت‌های سرمایه‌گذاری (Investing Activities)</h5>
                        <div class="badge badge-light-warning p-2 rounded">خرید و فروش دارایی‌های ثابت</div>
                    </div>
                    <div class="card-body px-4 pb-4">
                        <table class="table table-hover">
                            <tbody>
                                <tr>
                                    <td class="py-3">تغییر در دارایی‌های ثابت (Fixed Assets)</td>
                                    <td class="py-3 text-right {{ $investing['fixed_assets'] < 0 ? 'text-danger' : 'text-success' }}">
                                        {{ $investing['fixed_assets'] > 0 ? '+' : '' }}{{ number_format($investing['fixed_assets'], 2) }}
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr class="font-weight-bold" style="background: #fffde7;">
                                    <td class="py-3">خالص جریان نقد از فعالیت‌های سرمایه‌گذاری</td>
                                    <td class="py-3 text-right text-warning" style="font-size: 1.1rem;">{{ number_format($net_cash_investing, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- 3. FINANCING ACTIVITIES -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px; border-right: 6px solid #43a047 !important;">
                    <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                        <h5 class="font-weight-bold text-dark mb-0">۳. فعالیت‌های تأمین مالی (Financing Activities)</h5>
                        <div class="badge badge-light-success p-2 rounded">تغییر در سرمایه و وام‌ها</div>
                    </div>
                    <div class="card-body px-4 pb-4">
                        <table class="table table-hover">
                            <tbody>
                                <tr>
                                    <td class="py-3">تغییر در حقوق مالکانه (Equity / Investment)</td>
                                    <td class="py-3 text-right {{ $financing['equity'] < 0 ? 'text-danger' : 'text-success' }}">
                                        {{ $financing['equity'] > 0 ? '+' : '' }}{{ number_format($financing['equity'], 2) }}
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr class="font-weight-bold" style="background: #e8f5e9;">
                                    <td class="py-3">خالص جریان نقد از فعالیت‌های تأمین مالی</td>
                                    <td class="py-3 text-right text-success" style="font-size: 1.1rem;">{{ number_format($net_cash_financing, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- TOTAL SUMMARY -->
                <div class="card border-0 shadow-lg mb-5 overflow-hidden" style="border-radius: 15px;">
                    <div class="row no-gutters">
                        <div class="col-md-8 bg-dark text-white p-4">
                            <h4 class="text-white font-weight-bold mb-1">خالص تغییر در نقدینگی (کل)</h4>
                            <p class="mb-0 opacity-75">Net Change in Cash and Equivalents</p>
                        </div>
                        <div class="col-md-4 bg-info text-white p-4 text-center d-flex align-items-center justify-content-center">
                            <h2 class="text-white font-weight-bold mb-0">{{ number_format($net_change_in_cash, 2) }}</h2>
                        </div>
                    </div>
                </div>

                <!-- Final Message -->
                <div class="text-center text-muted small no-print">
                    این گزارش بر اساس استانداردهای حسابداری (IFRS) با روش غیر مستقیم تهیه شده است.
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .badge-light-info { background: #e0f7fa; color: #00838f; }
    .badge-light-warning { background: #fffde7; color: #fbc02d; }
    .badge-light-success { background: #e8f5e9; color: #43a047; }
    .border-left { border-left: 4px solid #00acc1; }
    @media print {
        body { background: white !important; }
        .no-print, .pcoded-navbar, .pcoded-header { display: none !important; }
        .pcoded-main-container { margin-left: 0 !important; margin-top: 0 !important; }
        .no-print-padding { padding: 0 !important; }
        .card { border: 1px solid #eee !important; box-shadow: none !important; }
        .bg-dark, .bg-info, .badge-light-info, .badge-light-warning, .badge-light-success { -webkit-print-color-adjust: exact; }
    }
</style>
@endsection
