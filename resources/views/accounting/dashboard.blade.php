@extends('dsh.master')

@section('title', 'داشبورد مالی - Financial Dashboard')

@section('content')
<!-- Include ApexCharts -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<div class="container-fluid no-print-padding">
    <!-- Dashboard Header -->
    <div class="row align-items-center mb-4 no-print">
        <div class="col-md-5">
            <h2 class="font-weight-bold text-dark mb-1">داشبورد مدیریت مالی (Executive Dashboard)</h2>
            <p class="text-muted mb-0">تحلیل لحظه‌ای وضعیت نقدینگی، سودآوری و عملکرد گدام</p>
        </div>
        <div class="col-md-7 text-right">
            <!-- Currency Toggle -->
            <div class="d-inline-block mr-3 text-right" style="vertical-align: middle;">
                <label class="small text-muted d-block mb-0">واحد پولی نمایش</label>
                <div class="btn-group btn-group-sm shadow-sm rounded-pill overflow-hidden bg-white border">
                    @foreach($currencies as $c)
                        <button type="button" onclick="setCurrency('{{ $c->code }}')" id="btn{{ $c->code }}" class="btn btn-white px-3 {{ $c->code == 'USD' ? 'active btn-primary text-white' : '' }}">{{ $c->code }}</button>
                    @endforeach
                </div>
            </div>

            <!-- Quick Action -->
            <button type="button" class="btn btn-info rounded-pill px-4 shadow-sm" data-toggle="modal" data-target="#quickJournalModal">
                <i class="fa fa-plus-circle mr-2"></i> ثبت روزنامچه سریع
            </button>

            <button onclick="window.print()" class="btn btn-primary rounded-pill px-4 ml-2 shadow-sm">
                <i class="fa fa-print mr-2"></i> چاپ داشبورد
            </button>
        </div>
    </div>

    <!-- 1. Top Metrics Row -->
    <div class="row mb-4 no-print">
        <!-- Cash Position -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3 text-white h-100" style="border-radius: 20px; background: linear-gradient(135deg, #1a237e, #3949ab);">
                <div class="d-flex justify-content-between mb-3">
                    <div class="bg-white-10 p-2 rounded-circle"><i class="feather icon-briefcase f-24"></i></div>
                    <span class="badge badge-light-success">+{{ $kpis['cash']['change'] }}%</span>
                </div>
                <span class="opacity-75 d-block small font-weight-bold">مجموع نقدینگی (Cash Balance)</span>
                <h3 class="font-weight-bold mt-1 text-white">{{ number_format($metrics['cash_on_hand'], 2) }} <small class="f-12">USD</small></h3>
            </div>
        </div>
        <!-- Receivables -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3 h-100" style="border-radius: 20px; background: #fff;">
                <div class="d-flex justify-content-between mb-3">
                    <div class="bg-light-success p-2 rounded-circle"><i class="feather icon-users f-24 text-success"></i></div>
                    <span class="badge badge-success">{{ $kpis['receivables']['change'] }}%</span>
                </div>
                <span class="text-muted d-block small font-weight-bold">طلبات مشتریان (Accounts Receivable)</span>
                <h3 class="font-weight-bold mt-1 text-dark">{{ number_format($metrics['total_receivables'], 2) }} <small class="f-12">USD</small></h3>
            </div>
        </div>
        <!-- Inventory Value -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3 h-100" style="border-radius: 20px; background: #fff;">
                <div class="d-flex justify-content-between mb-3">
                    <div class="bg-light-warning p-2 rounded-circle"><i class="feather icon-archive f-24 text-warning"></i></div>
                    <span class="text-warning small font-weight-bold">ارزش گدام</span>
                </div>
                <span class="text-muted d-block small font-weight-bold">ارزش کل موجودی (Inventory Value)</span>
                <h3 class="font-weight-bold mt-1 text-dark">{{ number_format($metrics['inventory_value'], 2) }} <small class="f-12">USD</small></h3>
            </div>
        </div>
        <!-- Net Profit -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3 text-white h-100" style="border-radius: 20px; background: linear-gradient(135deg, #00c853, #2e7d32);">
                <div class="d-flex justify-content-between mb-3">
                    <div class="bg-white-10 p-2 rounded-circle"><i class="feather icon-trending-up f-24"></i></div>
                    <span class="badge badge-light-primary">MTD Profit</span>
                </div>
                <span class="opacity-75 d-block small font-weight-bold">مفاد ماه جاری (Net Profit)</span>
                <h3 class="font-weight-bold mt-1 text-white">{{ number_format($kpis['profit']['value'], 2) }} <small class="f-12">USD</small></h3>
            </div>
        </div>
    </div>

    <!-- 2. Main Analytics Row (Charts) -->
    <div class="row mb-4">
        <!-- Revenue vs Expense Chart -->
        <div class="col-md-8 mb-4">
            <div class="card border-0 shadow-sm p-4 h-100" style="border-radius: 20px;">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="font-weight-bold mb-0">تحلیل عواید و مصارف (Revenue vs Expenses)</h5>
                    <div class="small text-muted">روند ۳۰ روز گذشته</div>
                </div>
                <div id="revenueExpenseChart" style="min-height: 350px;"></div>
            </div>
        </div>
        <!-- Inventory Distribution -->
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm p-4 h-100" style="border-radius: 20px;">
                <h5 class="font-weight-bold mb-4">توزیع دارایی‌های گدام</h5>
                <div id="inventoryDonutChart" style="min-height: 300px;"></div>
                <div class="mt-4 pt-3 border-top">
                    <div class="d-flex justify-content-between mb-2 small">
                        <span class="text-muted">مواد خام (Raw Materials)</span>
                        <span class="font-weight-bold">{{ number_format($inventoryDist['raw'], 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between small">
                        <span class="text-muted">قالین آماده (Finished Carpets)</span>
                        <span class="font-weight-bold">{{ number_format($inventoryDist['finished'], 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Secondary Row (Cashflow & Aging) -->
    <div class="row mb-4">
        <!-- Cash Flow Bar -->
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm p-4 h-100" style="border-radius: 20px;">
                <h5 class="font-weight-bold mb-4">تحلیل ورود و خروج نقدینگی (Cash Flow)</h5>
                <div id="cashFlowChart" style="min-height: 250px;"></div>
            </div>
        </div>
        <!-- Aging Analysis -->
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm p-4 h-100" style="border-radius: 20px;">
                <h5 class="font-weight-bold mb-4">تحلیل سررسید طلبات (AR Aging)</h5>
                <div id="agingChart" style="min-height: 250px;"></div>
            </div>
        </div>
    </div>

    <!-- 4. Intelligence & Quick Actions Row -->
    <div class="row no-print">
        <!-- Top Expenses -->
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 20px;">
                <div class="card-header bg-white border-0 py-4 px-4">
                    <h5 class="font-weight-bold mb-0">بیشترین مصارف (Top Expenses)</h5>
                </div>
                <div class="card-body p-0 px-4 pb-4">
                    @foreach($topExpenses as $expense)
                    <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                        <span class="text-muted small">{{ $expense->account_name }}</span>
                        <span class="font-weight-bold text-danger currency-val" data-usd="{{ $expense->total }}">{{ number_format($expense->total, 2) }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        <!-- Profitability by Type -->
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 20px;">
                <div class="card-header bg-white border-0 py-4 px-4">
                    <h5 class="font-weight-bold mb-0">سودآوری به تفکیک نوعیت</h5>
                </div>
                <div class="card-body p-0 px-4 pb-4">
                    @foreach($profitability as $profit)
                    <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                        <span class="text-muted small">{{ $profit->type }}</span>
                        <span class="font-weight-bold text-success currency-val" data-usd="{{ $profit->total_profit }}">{{ number_format($profit->total_profit, 2) }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        <!-- Period Closing & Cash Balances -->
        <div class="col-md-4 mb-4">
            <!-- Cash Balances Breakdown Card -->
            <div class="card border-0 shadow-sm mb-3" style="border-radius: 20px; background: #fff;">
                <div class="card-header bg-white border-0 py-3 px-4">
                    <h5 class="font-weight-bold mb-0">موجودی صندوق‌ها (Cash Balances)</h5>
                </div>
                <div class="card-body p-0 px-4 pb-3">
                    @foreach($cashAccounts as $acc)
                    <div class="d-flex justify-content-between align-items-center mb-2 border-bottom pb-2">
                        <span class="text-muted small">{{ $acc['name'] }}</span>
                        <div class="text-right">
                            <span class="font-weight-bold text-primary" style="font-size: 0.9rem;">
                                {{ number_format($acc['balance'], 2) }} <small class="text-muted">{{ $acc['currency'] }}</small>
                            </span>
                            @if($acc['currency'] !== 'USD')
                            <div class="text-muted" style="font-size: 0.7rem;">
                                Equivalent: ${{ number_format($acc['base_balance'], 2) }}
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Quick Reports Card -->
            <div class="card border-0 shadow-sm" style="border-radius: 20px; background: #fff;">
                <div class="card-body p-4 text-center">
                    <h5 class="font-weight-bold mb-3">دسترسی سریع به گزارشات</h5>
                    <div class="row">
                        <div class="col-6 mb-2"><a href="{{ route('accounting.reports.balance_sheet') }}" class="btn btn-outline-primary btn-block rounded py-3 small font-weight-bold">ترازنامه</a></div>
                        <div class="col-6 mb-2"><a href="{{ route('accounting.reports.profit_loss') }}" class="btn btn-outline-success btn-block rounded py-3 small font-weight-bold">مفاد و ضرر</a></div>
                        <div class="col-6 mb-2"><a href="{{ route('accounting.reports.cash_flow') }}" class="btn btn-outline-info btn-block rounded py-3 small font-weight-bold">جریان نقد</a></div>
                        <div class="col-6 mb-2"><a href="{{ route('accounting.reports.comparative_pl') }}" class="btn btn-outline-dark btn-block rounded py-3 small font-weight-bold">تحلیل مقایسوی</a></div>
                    </div>
                </div>
            </div>

            <!-- Financial Period Lock Card -->
            <div class="card border-0 shadow-sm mt-3" style="border-radius: 20px; background: #fff;">
                <div class="card-header bg-white border-0 py-3 px-4">
                    <h5 class="font-weight-bold text-danger mb-0">بستن دوره مالی (Lock Period)</h5>
                </div>
                <div class="card-body p-4 pt-0">
                    <form action="{{ route('accounting.close_period') }}" method="POST">
                        @csrf
                        <div class="form-group mb-3">
                            <label class="small font-weight-bold text-muted">تاریخ بسته شدن حسابات</label>
                            <input type="date" name="lock_date" class="form-control rounded-pill bg-light border-0" value="{{ $lockDate ?? '' }}" required>
                        </div>
                        <button type="submit" class="btn btn-danger btn-block rounded-pill shadow-sm">
                            <i class="fa fa-lock mr-2"></i> قفل کردن حسابات
                        </button>
                    </form>
                    <p class="small text-muted mt-3 text-center mb-0">
                        معاملات ثبت شده قبل از این تاریخ قابل ویرایش نخواهند بود.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- 5. Recent Transactions Row -->
    <div class="row no-print">
        <div class="col-md-12 mb-4">
            <div class="card border-0 shadow-sm" style="border-radius: 20px;">
                <div class="card-header bg-white border-0 py-4 px-4 d-flex justify-content-between align-items-center">
                    <h5 class="font-weight-bold mb-0">آخرین معاملات دفتر روزنامچه (Recent Ledger)</h5>
                    <a href="{{ route('accounting.journals.index') }}" class="btn btn-light rounded-pill px-3 py-1 small">مشاهده همه</a>
                </div>
                <div class="table-responsive px-4 pb-4">
                    <table class="table table-hover border-top">
                        <thead>
                            <tr class="text-muted small uppercase">
                                <th>تاریخ</th>
                                <th>نمبر معامله</th>
                                <th>شرح</th>
                                <th class="text-right">مبلغ</th>
                                <th class="text-center">حالت</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentTransactions as $tx)
                            <tr>
                                <td class="small">{{ $tx->date }}</td>
                                <td class="font-weight-bold">#{{ $tx->id }}</td>
                                <td class="small">{{ $tx->description }}</td>
                                <td class="text-right">
                                    <span class="font-weight-bold currency-val" data-usd="{{ $tx->entries->sum('base_debit') }}">{{ number_format($tx->entries->sum('base_debit'), 2) }}</span>
                                    <div class="small text-muted" style="font-size: 0.75rem;">
                                        @php
                                            $originalDetails = $tx->entries->filter(function($e) { return $e->debit > 0; })->map(function($e) {
                                                return number_format($e->debit, 2) . ' ' . ($e->currency_code ?? 'USD');
                                            })->unique()->implode(', ');
                                        @endphp
                                        {{ $originalDetails }}
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-pill badge-light-success px-3">ثبت شده</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Journal Modal -->
<div class="modal fade" id="quickJournalModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content border-0" style="border-radius: 20px;">
            <div class="modal-header border-0 p-4">
                <h5 class="modal-title font-weight-bold">ثبت روزنامچه سریع (Quick Journal Entry)</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <form action="{{ route('accounting.journals.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="small font-weight-bold text-muted">تاریخ</label>
                            <input type="date" name="date" class="form-control rounded-pill" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="small font-weight-bold text-muted">شرح معامله</label>
                            <input type="text" name="description" class="form-control rounded-pill" placeholder="مثلاً: هزینه حمل و نقل" required>
                        </div>
                    </div>
                    <p class="small text-muted mb-2">برای ثبت کامل و حرفه‌ای از صفحه <a href="{{ route('accounting.journals.create') }}">ثبت روزنامچه</a> استفاده کنید.</p>
                </div>
                <div class="modal-footer border-0 p-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-dismiss="modal">انصراف</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">تایید و ادامه</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const CURRENCY_RATES = {
        @foreach($currencies as $c)
            '{{ $c->code }}': {{ $c->exchange_rate }},
        @endforeach
    };
    let currentCurrency = 'USD';

    function setCurrency(cur) {
        currentCurrency = cur;
        const rate = CURRENCY_RATES[cur] || 1.0;

        $('.currency-val').each(function() {
            let usdVal = parseFloat($(this).data('usd'));
            if (isNaN(usdVal)) return;

            let displayVal = usdVal;
            if (cur !== 'USD') {
                displayVal = (rate > 0) ? (usdVal / rate) : 0;
            }

            if ($(this).find('.currency-label').length > 0 || $(this).html().toLowerCase().includes('small')) {
                $(this).html(number_format(displayVal, 2) + ' <small class="f-12 currency-label">' + cur + '</small>');
            } else {
                $(this).text(number_format(displayVal, 2));
            }
        });

        // Toggle active button class for all currency buttons
        $('.btn-group button').removeClass('active btn-primary text-white').addClass('btn-white');
        $('#btn' + cur).addClass('active btn-primary text-white').removeClass('btn-white');

        // Update any other currency labels
        $('.currency-label').text(cur);
    }
    
    // Auto-update USD labels on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Find all H3 and other financial values and wrap them in currency-val if not already
        $('h3.text-white, h3.text-dark').each(function() {
            if (!$(this).hasClass('currency-val')) {
                let text = $(this).text().replace(/,/g, '').trim();
                let val = parseFloat(text);
                if (!isNaN(val)) {
                    $(this).addClass('currency-val').data('usd', val);
                    $(this).html(number_format(val, 2) + ' <small class="f-12 currency-label">USD</small>');
                }
            }
        });
    });

    function number_format(number, decimals) {
        return number.toLocaleString(undefined, {minimumFractionDigits: decimals, maximumFractionDigits: decimals});
    }
</script>

<!-- Chart Scripts -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // 1. Revenue vs Expense Chart
        var revExpOptions = {
            series: [{
                name: 'عواید (Revenue)',
                data: {!! json_encode(collect($profit_loss)->pluck('revenue')) !!}
            }, {
                name: 'مصارف (Expenses)',
                data: {!! json_encode(collect($profit_loss)->pluck('expense')) !!}
            }],
            chart: { height: 350, type: 'area', toolbar: { show: false }, fontFamily: 'Iran, sans-serif' },
            colors: ['#00c853', '#d50000'],
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 3 },
            xaxis: { categories: {!! json_encode(collect($profit_loss)->pluck('date')) !!} },
            tooltip: { theme: 'dark', x: { show: true } },
            fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.1 } }
        };
        new ApexCharts(document.querySelector("#revenueExpenseChart"), revExpOptions).render();

        // 2. Inventory Donut
        var invOptions = {
            series: [{{ $inventoryDist['finished'] }}, {{ $inventoryDist['raw'] }}],
            chart: { type: 'donut', height: 300 },
            labels: {!! json_encode($inventoryDist['labels']) !!},
            colors: ['#1a237e', '#4caf50'],
            legend: { position: 'bottom' },
            plotOptions: { pie: { donut: { size: '75%' } } }
        };
        new ApexCharts(document.querySelector("#inventoryDonutChart"), invOptions).render();

        // 3. Cash Flow Bar
        var cfOptions = {
            series: [{
                name: 'ورودی (Inflow)',
                data: {!! json_encode(collect($cash_flow)->pluck('inflow')) !!}
            }, {
                name: 'خروجی (Outflow)',
                data: {!! json_encode(collect($cash_flow)->pluck('outflow')) !!}
            }],
            chart: { type: 'bar', height: 250, toolbar: { show: false } },
            colors: ['#00b0ff', '#f50057'],
            plotOptions: { bar: { horizontal: false, columnWidth: '55%', borderRadius: 5 } },
            dataLabels: { enabled: false },
            xaxis: { categories: {!! json_encode(collect($cash_flow)->pluck('date')) !!} }
        };
        new ApexCharts(document.querySelector("#cashFlowChart"), cfOptions).render();

        // 4. Aging Analysis
        var agingOptions = {
            series: [{{ $aging['0-30'] }}, {{ $aging['31-60'] }}, {{ $aging['61-90'] }}, {{ $aging['90+'] }}],
            chart: { type: 'polarArea', height: 250 },
            labels: ['0-30 روز', '31-60 روز', '61-90 روز', '90+ روز'],
            colors: ['#4caf50', '#ffeb3b', '#ff9800', '#f44336'],
            legend: { show: false }
        };
        new ApexCharts(document.querySelector("#agingChart"), agingOptions).render();
    });
</script>

<style>
    .bg-white-10 { background: rgba(255,255,255,0.1); }
    .bg-light-success { background: #e8f5e9; }
    .bg-light-warning { background: #fff8e1; }
    .badge-light-success { background: #e8f5e9; color: #2e7d32; font-weight: bold; }
    .f-12 { font-size: 12px; }
    .f-18 { font-size: 18px; }
    .f-24 { font-size: 24px; }
    @media print {
        .no-print { display: none !important; }
        body { background: white !important; }
        .card { border: 1px solid #eee !important; box-shadow: none !important; }
    }
</style>
@endsection
