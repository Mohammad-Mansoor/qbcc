@extends('dsh.master')

@section('content')
<div class="container-fluid no-print-padding">
    <br class="no-print">
    
    <!-- Top Filter Section -->
    <div class="row mb-4 no-print">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                <div class="card-body p-4">
                    <div class="row align-items-center mb-4">
                        <div class="col-md-6">
                            <h3 class="font-weight-bold mb-1">صورت حساب {{ $config['title'] }}</h3>
                            <p class="text-muted mb-0">گزارش تفصیلی تراکنش‌ها و بیلانس نهایی</p>
                        </div>
                    </div>
                    
                    <form action="{{ route('accounting.reports.entity_statement', ['entity' => $entityKey]) }}" method="GET">
                        <div class="row align-items-end">
                            <div class="col-md-3">
                                <label class="small font-weight-bold text-muted mb-1">انتخاب (Select):</label>
                                <select name="entity_id" class="form-control select2 shadow-sm" required>
                                    <option value="">-- نام یا نمبر مربوطه را انتخاب کنید --</option>
                                    @foreach($entities as $e)
                                        <option value="{{ $e->id }}" {{ isset($selectedEntity) && $selectedEntity->id == $e->id ? 'selected' : '' }}>
                                            {{ $e->id }} - {{ $e->display_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="small font-weight-bold text-muted mb-1">جستجو در شرح یا سند (Search):</label>
                                <input type="text" name="search" value="{{ $search ?? '' }}" class="form-control border-0 bg-light rounded-pill px-3" placeholder="جستجو (شرح، سند)...">
                            </div>
                            <div class="col-md-2">
                                <label class="small font-weight-bold text-muted mb-1">از تاریخ:</label>
                                <input type="date" name="start_date" value="{{ $startDate }}" class="form-control border-0 bg-light rounded-pill px-3">
                            </div>
                            <div class="col-md-2">
                                <label class="small font-weight-bold text-muted mb-1">الی تاریخ:</label>
                                <input type="date" name="end_date" value="{{ $endDate }}" class="form-control border-0 bg-light rounded-pill px-3">
                            </div>
                            <div class="col-md-2 d-flex">
                                <button type="submit" class="btn btn-indigo rounded-pill shadow-sm text-white mr-2 flex-fill" style="background: #3f51b5; padding: 10px 15px;">
                                    <i class="feather icon-search"></i> فیلتر
                                </button>
                                <a href="{{ route('accounting.reports.entity_statement', ['entity' => $entityKey]) }}" class="btn btn-light rounded-pill text-muted flex-fill" style="padding: 10px 15px;">پاکسازی</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if(isset($selectedEntity))
    <!-- Summary Snapshot -->
    <div class="row mb-4 no-print">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-4 text-center" style="border-radius: 15px; background: #fff; border-bottom: 4px solid #3f51b5 !important;">
                <span class="text-muted small font-weight-bold text-uppercase">مجموع کل مصارف / دیبت (Total Debits)</span>
                <h3 class="font-weight-bold text-indigo mt-2 mb-0" style="color: #3f51b5;">{{ number_format($entries->sum('debit'), 2) }}</h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-4 text-center" style="border-radius: 15px; background: #fff; border-bottom: 4px solid #2ed8b6 !important;">
                <span class="text-muted small font-weight-bold text-uppercase">مجموع کل رسید / کریدت (Total Credits)</span>
                <h3 class="font-weight-bold text-success mt-2 mb-0">{{ number_format($entries->sum('credit'), 2) }}</h3>
            </div>
        </div>
        @php 
            $isCustomer = ($entityKey === 'customer');
            $runningBalance = $openingBalance;
            foreach($entries as $entry) { 
                if ($isCustomer) {
                    $runningBalance += ($entry->debit - $entry->credit);
                } else {
                    $runningBalance += ($entry->credit - $entry->debit);
                }
            }
        @endphp
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-4 text-center text-white" style="border-radius: 15px; background: linear-gradient(45deg, #3f51b5, #5c6bc0);">
                <span class="opacity-75 small font-weight-bold text-uppercase">بیلانس نهایی (Outstanding Balance)</span>
                <h3 class="text-white font-weight-bold mt-2 mb-0">{{ number_format($runningBalance, 2) }}</h3>
            </div>
        </div>
    </div>

    <!-- Main Statement Document -->
    <div class="row">
        <div class="col-md-12">
            <div class="card border-0 shadow-lg printable-document" style="border-radius: 20px; overflow: hidden;">
                
                <!-- Print-Only Statement Header -->
                <div class="d-none d-print-block p-5 bg-light">
                    <div class="row align-items-center">
                        <div class="col-6">
                            <h1 class="font-weight-bold text-dark mb-1">QASIMI BROTHERS</h1>
                            <p class="text-muted mb-0">شرکت تولیدی قاسمی برادران</p>
                        </div>
                        <div class="col-6 text-right">
                            <h2 class="font-weight-bold text-indigo mb-1" style="color: #3f51b5;">صورت حساب (Statement)</h2>
                            <p class="text-muted mb-0">تاریخ گزارش: {{ date('Y-m-d') }}</p>
                        </div>
                    </div>
                    <hr class="my-4">
                    <div class="row">
                        <div class="col-6">
                            <span class="small text-muted font-weight-bold d-block text-uppercase">نام شخص/حساب (Account Party):</span>
                            <h4 class="font-weight-bold text-dark mb-0">{{ $selectedEntity->display_name }}</h4>
                            <p class="text-muted">کد حساب: {{ $selectedEntity->id }}</p>
                        </div>
                        <div class="col-6 text-right">
                            <span class="small text-muted font-weight-bold d-block text-uppercase">دوره مالی (Period):</span>
                            <p class="mb-0">از: <strong>{{ $startDate }}</strong></p>
                            <p class="mb-0">الی: <strong>{{ $endDate }}</strong></p>
                        </div>
                    </div>
                </div>

                <div class="card-body p-5">
                    <!-- Web Export Tools -->
                    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
                        <div class="text-muted">تاریخچه تراکنش‌های <span class="badge badge-indigo text-white px-3" style="background: #3f51b5;">{{ $selectedEntity->display_name }}</span></div>
                        <div id="export-buttons">
                            <a href="{{ route('accounting.reports.entity_statement', array_merge(request()->all(), ['entity' => $entityKey, 'export' => 'excel'])) }}" class="btn btn-success rounded-pill px-4 mr-2">
                                <i class="feather icon-file-text"></i> EXCEL
                            </a>
                            <a href="{{ route('accounting.reports.entity_statement', array_merge(request()->all(), ['entity' => $entityKey, 'export' => 'pdf'])) }}" target="_blank" class="btn btn-danger rounded-pill px-4 mr-2">
                                <i class="feather icon-file"></i> PDF
                            </a>
                            <button onclick="window.print()" class="btn btn-dark rounded-pill px-4">
                                <i class="feather icon-printer"></i> PRINT
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover border" id="statement-table">
                            <thead class="bg-indigo text-white" style="background: #3f51b5;">
                                <tr>
                                    <th class="py-3 px-4">تاریخ (Date)</th>
                                    <th class="py-3">سند (Ref)</th>
                                    <th class="py-3" style="width: 40%">تفصیلات (Description)</th>
                                    <th class="py-3 text-right">دیبت ({{ \App\Currency::getBase()->code }})</th>
                                    <th class="py-3 text-right">کریدت ({{ \App\Currency::getBase()->code }})</th>
                                    <th class="py-3 text-right px-4">بیلانس نهایی ({{ \App\Currency::getBase()->code }})</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $currentRunning = $openingBalance; @endphp
                                <tr class="bg-lightest font-weight-bold">
                                    <td colspan="5" class="py-3 px-4 text-muted">بیلانس قبلی (Opening Balance Forwarded)</td>
                                    <td class="py-3 text-right px-4 text-dark">{{ number_format($openingBalance, 2) }}</td>
                                </tr>

                                @foreach($entries as $entry)
                                    @php 
                                        if ($isCustomer) {
                                            $currentRunning += ($entry->debit - $entry->credit);
                                        } else {
                                            $currentRunning += ($entry->credit - $entry->debit);
                                        }
                                    @endphp
                                <tr>
                                    <td class="py-3 px-4 small">{{ $entry->date }}</td>
                                    <td class="py-3 font-weight-bold">
                                        <a href="{{ route('accounting.journals.show', $entry->transaction_id) }}" target="_blank" class="text-indigo">{{ $entry->reference }}</a>
                                    </td>
                                    <td class="py-3 text-muted small">{{ $entry->description }}</td>
                                    <td class="py-3 text-right text-dark">
                                        {{ $entry->debit > 0 ? number_format($entry->debit, 2) : '-' }}
                                        @if($entry->debit > 0 && $entry->currency_code != \App\Currency::getBase()->code)
                                            <div class="small text-muted font-weight-normal" dir="ltr">
                                                <i class="feather icon-repeat x-small"></i> {{ number_format($entry->base_currency_amount / $entry->exchange_rate, 2) }} {{ $entry->currency_code }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="py-3 text-right text-success font-weight-bold">
                                        {{ $entry->credit > 0 ? number_format($entry->credit, 2) : '-' }}
                                        @if($entry->credit > 0 && $entry->currency_code != \App\Currency::getBase()->code)
                                            <div class="small text-muted font-weight-normal" dir="ltr">
                                                <i class="feather icon-repeat x-small"></i> {{ number_format($entry->base_currency_amount / $entry->exchange_rate, 2) }} {{ $entry->currency_code }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="py-3 text-right px-4 font-weight-bold {{ $currentRunning >= 0 ? 'text-dark' : 'text-danger' }}">
                                        {{ number_format(abs($currentRunning), 2) }} {{ $currentRunning >= 0 ? ($isCustomer ? '(Dr)' : '(Cr)') : ($isCustomer ? '(Cr)' : '(Dr)') }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-light font-weight-bold">
                                <tr>
                                    <td colspan="3" class="py-4 px-4 text-right border-0">خلاصه این دوره:</td>
                                    <td class="py-4 text-right border-0">{{ number_format($entries->sum('debit'), 2) }}</td>
                                    <td class="py-4 text-right text-success border-0">{{ number_format($entries->sum('credit'), 2) }}</td>
                                    <td class="py-4 text-right px-4 border-0 text-indigo" style="font-size: 1.2rem; color: #3f51b5;">
                                        {{ number_format($currentRunning, 2) }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Print Footer -->
                    <div class="mt-5 pt-5 d-none d-print-block">
                        <div class="row">
                            <div class="col-8">
                                <div class="p-3 bg-light rounded small text-muted border">
                                    <span class="d-block font-weight-bold mb-1">نکات مهم (Statement Notes):</span>
                                    1. این صورت حساب صرفاً جهت اطلاع رسانی وضعیت مالی می‌باشد.<br>
                                    2. لطفاً در صورت وجود هرگونه مغایرت، حداکثر ظرف مدت 7 روز اطلاع دهید.<br>
                                    3. تسویه حساب به موقع موجب تداوم همکاری‌های فی‌مابین خواهد بود.
                                </div>
                            </div>
                            <div class="col-4 text-center">
                                <div class="border-top pt-2 mt-4 font-weight-bold">امضاء و مهر شرکت</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="row justify-content-center mt-5">
        <div class="col-md-6 text-center">
            <div class="p-5 bg-white shadow-sm rounded-lg" style="border-radius: 20px;">
                <i class="feather icon-users f-60 text-muted mb-4 opacity-20"></i>
                <h5 class="text-muted">لطفاً ابتدا یک مورد را انتخاب کنید.</h5>
                <p class="text-muted small">برای مشاهده صورت حساب، از فیلترهای بالا استفاده نمایید.</p>
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
        .bg-indigo { -webkit-print-color-adjust: exact; background: #3f51b5 !important; color: white !important; }
        .bg-light { -webkit-print-color-adjust: exact; background: #f8f9fa !important; }
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
        
        var table = $('#statement-table').DataTable({
            paging: false,
            searching: true
        });
    });
</script>
@endsection
