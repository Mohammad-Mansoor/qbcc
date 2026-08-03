@extends('dsh.master')

@section('content')
<div class="container-fluid no-print-padding">
    <br class="no-print">
    
    <!-- Premium Header & Filter Section -->
    <div class="row mb-4 no-print">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                <div class="card-body p-4">
                    <div class="row align-items-center mb-4">
                        <div class="col-md-6">
                            <h3 class="font-weight-bold mb-1">دفتر تفصیلی حساب (Account Ledger)</h3>
                            <p class="text-muted mb-0">تحلیل و بررسی دقیق تمامی تراکنش‌های یک حساب بانکی یا بودجه‌ای</p>
                        </div>
                    </div>
                    
                    <form action="{{ route('accounting.reports.account_ledger') }}" method="GET">
                        <div class="row align-items-end">
                            <div class="col-md-4">
                                <label class="small font-weight-bold text-muted mb-1">انتخاب حساب (Select Account):</label>
                                <select name="account_id" class="form-control select2 shadow-sm" required>
                                    <option value="">-- برای جستجو تایپ کنید --</option>
                                    @foreach($accounts as $acc)
                                        <option value="{{ $acc->id }}" {{ isset($account) && $account->id == $acc->id ? 'selected' : '' }}>
                                            {{ $acc->account_code }} - {{ $acc->account_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="small font-weight-bold text-muted mb-1">از تاریخ:</label>
                                <input type="date" name="start_date" value="{{ $startDate }}" class="form-control border-0 bg-light rounded-pill px-3">
                            </div>
                            <div class="col-md-2">
                                <label class="small font-weight-bold text-muted mb-1">الی تاریخ:</label>
                                <input type="date" name="end_date" value="{{ $endDate }}" class="form-control border-0 bg-light rounded-pill px-3">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary btn-block rounded-pill shadow-sm">
                                    <i class="feather icon-search mr-1"></i> مشاهده جزئیات
                                </button>
                            </div>
                            <div class="col-md-2">
                                <a href="{{ route('accounting.reports.account_ledger') }}" class="btn btn-light btn-block rounded-pill text-muted">پاکسازی</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if(isset($account) || count($entries) > 0)
    @php
        $accCurrency = isset($account) ? $account->currency : 'USD';
        $accRate = (isset($currencies) && isset($currencies[$accCurrency]) && $currencies[$accCurrency]->exchange_rate > 0) ? $currencies[$accCurrency]->exchange_rate : 1.0;
    @endphp
    <!-- Analytical Snapshot Cards -->
    <div class="row mb-4 no-print">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-4 text-center" style="border-radius: 15px; background: #fff;">
                <span class="text-muted small font-weight-bold">بیلانس افتتاحی (Opening)</span>
                <h4 class="font-weight-bold text-dark mt-2 mb-0">{{ number_format($openingBalance, 2) }} <small class="text-muted" style="font-size: 0.75rem;">USD</small></h4>
                @if($accCurrency !== 'USD')
                <div class="text-muted small mt-1">
                    {{ number_format($openingBalance / $accRate, 2) }} <small>{{ $accCurrency }}</small>
                </div>
                @endif
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-4 text-center" style="border-radius: 15px; background: #fff; border-bottom: 4px solid #4099ff !important;">
                <span class="text-primary small font-weight-bold">مجموع دیبت (Total Debit)</span>
                <h4 class="font-weight-bold text-primary mt-2 mb-0">+ {{ number_format($entries->sum('debit'), 2) }} <small class="text-muted" style="font-size: 0.75rem;">USD</small></h4>
                @if($accCurrency !== 'USD')
                <div class="text-muted small mt-1">
                    + {{ number_format($entries->sum('debit') / $accRate, 2) }} <small>{{ $accCurrency }}</small>
                </div>
                @endif
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-4 text-center" style="border-radius: 15px; background: #fff; border-bottom: 4px solid #ff5370 !important;">
                <span class="text-danger small font-weight-bold">مجموع کریدت (Total Credit)</span>
                <h4 class="font-weight-bold text-danger mt-2 mb-0">- {{ number_format($entries->sum('credit'), 2) }} <small class="text-muted" style="font-size: 0.75rem;">USD</small></h4>
                @if($accCurrency !== 'USD')
                <div class="text-muted small mt-1">
                    - {{ number_format($entries->sum('credit') / $accRate, 2) }} <small>{{ $accCurrency }}</small>
                </div>
                @endif
            </div>
        </div>
        @php 
            $runningBalance = $openingBalance;
            foreach($entries as $entry) {
                $normal = (isset($account) && $account->normal_balance == 'credit') ? 'credit' : 'debit';
                if ($normal == 'debit') { $runningBalance += ($entry->debit - $entry->credit); } 
                else { $runningBalance += ($entry->credit - $entry->debit); }
            }
        @endphp
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-4 text-center" style="border-radius: 15px; background: linear-gradient(45deg, #1a237e, #3949ab);">
                <span class="text-white opacity-75 small font-weight-bold">بیلانس نهایی (Closing Balance)</span>
                <h4 class="text-white font-weight-bold mt-2 mb-0">{{ number_format($runningBalance, 2) }} <small class="text-white-50" style="font-size: 0.75rem;">USD</small></h4>
                @if($accCurrency !== 'USD')
                <div class="text-white opacity-75 small mt-1">
                    {{ number_format($runningBalance / $accRate, 2) }} <small>{{ $accCurrency }}</small>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Main Ledger Card -->
    <div class="row">
        <div class="col-md-12">
            <div class="card border-0 shadow-lg printable-document" style="border-radius: 20px; overflow: hidden;">
                
                <!-- Print Header -->
                <div class="d-none d-print-block text-center p-5 border-bottom">
                    <h1 class="font-weight-bold text-dark mb-1">QASIMI BROTHERS CARPET CO.</h1>
                    <h3 class="text-muted mb-2">دفتر تفصیلی حساب (Account Ledger)</h3>
                    <div class="row mt-4">
                        <div class="col-6 text-right">
                            حساب: <strong>{{ isset($account) ? $account->account_name . ' (' . $account->account_code . ')' : 'گزارش تفتیش (Audit Filter)' }}</strong>
                        </div>
                        <div class="col-6 text-left">دوره: <strong>{{ $startDate }} الی {{ $endDate }}</strong></div>
                    </div>
                </div>

                <div class="card-body p-5">
                    <!-- Web-Only Export Bar -->
                    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
                        <div class="text-muted">
                            @if(isset($account))
                                لیست تراکنش‌ها برای: <span class="badge badge-primary px-3">{{ $account->account_name }}</span>
                            @else
                                لیست تراکنش‌ها بر اساس: <span class="badge badge-info px-3">منبع انتخابی (Audit Filter)</span>
                            @endif
                        </div>
                        <div>
                            @if(isset($account))
                            <a href="{{ route('accounting.reports.account_ledger', ['account_id' => $account->id, 'start_date' => $startDate, 'end_date' => $endDate, 'export' => 'excel']) }}" class="btn btn-success rounded-pill px-4 mr-2 shadow-sm" style="font-weight: 500;">
                                <i class="feather icon-file-text mr-1"></i> EXCEL
                            </a>
                            <a href="{{ route('accounting.reports.account_ledger', ['account_id' => $account->id, 'start_date' => $startDate, 'end_date' => $endDate, 'export' => 'pdf']) }}" target="_blank" class="btn btn-danger rounded-pill px-4 mr-2 shadow-sm" style="font-weight: 500; background-color: #dc2626; border-color: #dc2626;">
                                <i class="feather icon-file mr-1"></i> PDF
                            </a>
                            @endif
                            <button onclick="window.print()" class="btn btn-dark rounded-pill px-4 shadow-sm" style="font-weight: 500;">
                                <i class="feather icon-printer mr-1"></i> PRINT
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover border" id="ledger-table">
                            <thead class="bg-dark text-white">
                                <tr>
                                    <th class="py-3 px-4">تاریخ</th>
                                    <th class="py-3">سند (Ref)</th>
                                    <th class="py-3" style="width: 35%">شرح تراکنش (Description)</th>
                                    <th class="py-3 text-right">دیبت ({{ \App\Currency::getBase()->code }})</th>
                                    <th class="py-3 text-right">کریدت ({{ \App\Currency::getBase()->code }})</th>
                                    <th class="py-3 text-right px-4">بیلانس ({{ \App\Currency::getBase()->code }})</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $currentRunning = $openingBalance; @endphp
                                <!-- Opening Balance Row -->
                                <tr class="bg-lightest">
                                    <td colspan="5" class="py-3 px-4 text-muted font-weight-bold">بیلانس انتقالی (Opening Balance Forwarded)</td>
                                    <td class="py-3 text-right px-4 font-weight-bold text-dark">
                                        {{ number_format($openingBalance, 2) }} <small class="text-muted">USD</small>
                                        @if($accCurrency !== 'USD')
                                            <div class="small text-muted font-weight-normal" style="font-size: 0.75rem;">
                                                {{ number_format($openingBalance / $accRate, 2) }} {{ $accCurrency }}
                                            </div>
                                        @endif
                                    </td>
                                </tr>

                                @foreach($entries as $entry)
                                    @php 
                                        $normal = (isset($account) && $account->normal_balance == 'credit') ? 'credit' : 'debit';
                                        if ($normal == 'debit') { $currentRunning += ($entry->debit - $entry->credit); } 
                                        else { $currentRunning += ($entry->credit - $entry->debit); }
                                    @endphp
                                <tr>
                                    <td class="py-3 px-4 small">{{ $entry->date }}</td>
                                    <td class="py-3 font-weight-bold">
                                        <a href="{{ route('accounting.journals.show', $entry->transaction_id) }}" target="_blank" class="text-primary">{{ $entry->reference }}</a>
                                    </td>
                                    <td class="py-3 text-muted small">{{ $entry->description }}</td>
                                    <td class="py-3 text-right text-primary font-weight-bold">
                                        {{ $entry->debit > 0 ? number_format($entry->debit, 2) : '-' }}
                                        @if($entry->debit > 0 && $entry->currency_code != \App\Currency::getBase()->code)
                                            <div class="small text-muted font-weight-normal" dir="ltr">
                                                <i class="feather icon-repeat x-small"></i> {{ number_format($entry->original_amount, 2) }} {{ $entry->currency_code }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="py-3 text-right text-danger font-weight-bold">
                                        {{ $entry->credit > 0 ? number_format($entry->credit, 2) : '-' }}
                                        @if($entry->credit > 0 && $entry->currency_code != \App\Currency::getBase()->code)
                                            <div class="small text-muted font-weight-normal" dir="ltr">
                                                <i class="feather icon-repeat x-small"></i> {{ number_format($entry->original_amount, 2) }} {{ $entry->currency_code }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="py-3 text-right px-4 font-weight-bold" style="background: #fcfcfc;">
                                        {{ number_format($currentRunning, 2) }} <small class="text-muted">USD</small>
                                        @if($accCurrency !== 'USD')
                                            <div class="small text-muted font-weight-normal" style="font-size: 0.75rem;">
                                                {{ number_format($currentRunning / $accRate, 2) }} {{ $accCurrency }}
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-light">
                                <tr class="font-weight-bold">
                                    <td colspan="3" class="py-3 px-4 text-right border-0">مجموع دوره (Period Total):</td>
                                    <td class="py-3 text-right text-primary border-0">
                                        {{ number_format($entries->sum('debit'), 2) }} <small class="text-muted">USD</small>
                                        @if($accCurrency !== 'USD')
                                            <div class="small text-muted font-weight-normal" style="font-size: 0.75rem;">
                                                {{ number_format($entries->sum('debit') / $accRate, 2) }} {{ $accCurrency }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="py-3 text-right text-danger border-0">
                                        {{ number_format($entries->sum('credit'), 2) }} <small class="text-muted">USD</small>
                                        @if($accCurrency !== 'USD')
                                            <div class="small text-muted font-weight-normal" style="font-size: 0.75rem;">
                                                {{ number_format($entries->sum('credit') / $accRate, 2) }} {{ $accCurrency }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="py-3 text-right px-4 border-0 text-dark" style="font-size: 1.1rem;">
                                        {{ number_format($currentRunning, 2) }} <small class="text-muted">USD</small>
                                        @if($accCurrency !== 'USD')
                                            <div class="small text-muted font-weight-normal" style="font-size: 0.85rem;">
                                                {{ number_format($currentRunning / $accRate, 2) }} {{ $accCurrency }}
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Print Footer -->
                    <div class="mt-5 pt-5 d-none d-print-block">
                        <div class="row text-center">
                            <div class="col-4"><div class="border-top pt-2">ترتیب کننده</div></div>
                            <div class="col-4"><div class="border-top pt-2">کنترل کننده</div></div>
                            <div class="col-4"><div class="border-top pt-2">مدیریت مالی</div></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
    <!-- Empty State -->
    <div class="row justify-content-center mt-5">
        <div class="col-md-6 text-center">
            <div class="p-5 bg-white shadow-sm rounded-lg" style="border-radius: 20px;">
                <i class="feather icon-search f-60 text-muted mb-4 opacity-20"></i>
                <h5 class="text-muted">لطفاً ابتدا یک حساب را از لیست بالا انتخاب کنید.</h5>
                <p class="text-muted small">برای مشاهده گزارش تفصیلی، حساب و بازه زمانی را تعیین نمایید.</p>
            </div>
        </div>
    </div>
    @endif
</div>

<style>
    .bg-lightest { background: #fafafa; }
    .select2-container--default .select2-selection--single { background-color: #f8f9fa; border: none; height: 45px; line-height: 45px; border-radius: 10px; }
    .select2-container { width: 100% !important; }
    @media print {
        body { background: white !important; }
        .no-print, .pcoded-navbar, .pcoded-header { display: none !important; }
        .pcoded-main-container { margin-left: 0 !important; margin-top: 0 !important; }
        .printable-document { box-shadow: none !important; border: 1px solid #eee !important; width: 100% !important; }
        .no-print-padding { padding: 0 !important; }
        .bg-dark { -webkit-print-color-adjust: exact; color: white !important; }
        .bg-light { -webkit-print-color-adjust: exact; }
    }
</style>
@endsection

@section('footer-plugins')
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
<script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.print.min.js"></script>

<script>
    $(document).ready(function() {
        $('.select2').select2();
        
        var table = $('#ledger-table').DataTable({
            paging: false,
            searching: true
        });
    });
</script>
@endsection
