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
                        <div class="col-md-9">
                            <h3 class="font-weight-bold mb-1">صورت حساب مشتری (Customer Statement)</h3>
                            <p class="text-muted mb-0">گزارش تفصیلی فاکتورها، رسیدات و بیلانس نهایی مشتری</p>
                        </div>
                        @if($logoBase64)
                        <div class="col-md-3 text-right">
                            <img src="{{ $logoBase64 }}" style="max-height: 60px; object-fit: contain;">
                        </div>
                        @endif
                    </div>
                    
                    <form action="{{ route('accounting.reports.customer_statement') }}" method="GET">
                        <div class="row align-items-end">
                            <div class="col-md-4">
                                <label class="small font-weight-bold text-muted mb-1">انتخاب مشتری (Search Customer):</label>
                                <select name="customer_id" class="form-control select2 shadow-sm" required>
                                    <option value="">-- نام یا نمبر مشتری را وارد کنید --</option>
                                    @foreach($customers as $c)
                                        <option value="{{ $c->id }}" {{ isset($customer) && $customer->id == $c->id ? 'selected' : '' }}>
                                            {{ $c->id }} - {{ $c->name }}
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
                                <button type="submit" class="btn btn-indigo btn-block rounded-pill shadow-sm text-white" style="background: #3f51b5;">
                                    <i class="feather icon-search mr-1"></i> مشاهده صورت حساب
                                </button>
                            </div>
                            <div class="col-md-2">
                                <a href="{{ route('accounting.reports.customer_statement') }}" class="btn btn-light btn-block rounded-pill text-muted">پاکسازی</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if(isset($customer))
    <!-- Summary Snapshot -->
    <div class="row mb-4 no-print">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-4 text-center" style="border-radius: 15px; background: #fff; border-bottom: 4px solid #3f51b5 !important;">
                <span class="text-muted small font-weight-bold text-uppercase">مجموع فروشات (Total Charges)</span>
                <h3 class="font-weight-bold text-indigo mt-2 mb-0" style="color: #3f51b5;">{{ number_format($entries->sum('debit'), 2) }}</h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-4 text-center" style="border-radius: 15px; background: #fff; border-bottom: 4px solid #2ed8b6 !important;">
                <span class="text-muted small font-weight-bold text-uppercase">مجموع پرداختی (Total Payments)</span>
                <h3 class="font-weight-bold text-success mt-2 mb-0">{{ number_format($entries->sum('credit'), 2) }}</h3>
            </div>
        </div>
        @php 
            $runningBalance = $openingBalance;
            foreach($entries as $entry) { $runningBalance += ($entry->debit - $entry->credit); }
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
                
                <div class="card-body p-5">
                    <!-- Web Export Tools -->
                    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
                        <div class="text-muted">تاریخچه تراکنش‌های <span class="badge badge-indigo text-white px-3" style="background: #3f51b5;">{{ $customer->name }}</span></div>
                        <div class="d-flex align-items-center">
                            @can('export_customer_statement_excel')
                            <a href="{{ route('accounting.reports.customer_statement', ['customer_id' => $customer->id, 'start_date' => $startDate, 'end_date' => $endDate, 'export' => 'excel']) }}" class="btn btn-success rounded-pill px-4 mr-2" style="font-weight: 500;">
                                <i class="feather icon-file-text mr-1"></i> EXCEL EXPORT (اکسل)
                            </a>
                            @endcan
                            @can('export_customer_statement_pdf')
                            <a href="{{ route('accounting.reports.customer_statement', ['customer_id' => $customer->id, 'start_date' => $startDate, 'end_date' => $endDate, 'export' => 'pdf']) }}" target="_blank" class="btn btn-danger rounded-pill px-4" style="font-weight: 500; background-color: #dc2626; border-color: #dc2626;">
                                <i class="feather icon-printer mr-1"></i> PDF / PRINT (پی‌دی‌اف)
                            </a>
                            @endcan
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover border" id="statement-table">
                            <thead class="bg-indigo text-white" style="background: #3f51b5;">
                                <tr>
                                    <th class="py-3 px-4">تاریخ (Date)</th>
                                    <th class="py-3">سند (Ref)</th>
                                    <th class="py-3" style="width: 40%">تفصیلات (Description)</th>
                                    <th class="py-3 text-right">دیبت / فروش ({{ \App\Currency::getBase()->code }})</th>
                                    <th class="py-3 text-right">کریدت / رسید ({{ \App\Currency::getBase()->code }})</th>
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
                                    @php $currentRunning += ($entry->debit - $entry->credit); @endphp
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
                                                <i class="feather icon-repeat x-small"></i> {{ number_format($entry->original_amount, 2) }} {{ $entry->currency_code }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="py-3 text-right text-success font-weight-bold">
                                        {{ $entry->credit > 0 ? number_format($entry->credit, 2) : '-' }}
                                        @if($entry->credit > 0 && $entry->currency_code != \App\Currency::getBase()->code)
                                            <div class="small text-muted font-weight-normal" dir="ltr">
                                                <i class="feather icon-repeat x-small"></i> {{ number_format($entry->original_amount, 2) }} {{ $entry->currency_code }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="py-3 text-right px-4 font-weight-bold {{ $currentRunning >= 0 ? 'text-dark' : 'text-danger' }}">
                                        {{ number_format(abs($currentRunning), 2) }} {{ $currentRunning >= 0 ? '(Dr)' : '(Cr)' }}
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
                <h5 class="text-muted">لطفاً ابتدا یک مشتری را انتخاب کنید.</h5>
                <p class="text-muted small">برای مشاهده صورت حساب مشتری، از فیلترهای بالا استفاده نمایید.</p>
            </div>
        </div>
    </div>
    @endif
</div>

<style>
    .bg-lightest { background: #fafafa; }
    .select2-container--default .select2-selection--single { background-color: #f8f9fa; border: none; height: 45px; line-height: 45px; border-radius: 10px; }
    .select2-container { width: 100% !important; }
</style>
@endsection

@section('footer-plugins')
<script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>

<script>
    $(document).ready(function() {
        $('.select2').select2();
        
        $('#statement-table').DataTable({
            paging: false,
            searching: true,
            info: false
        });
    });
</script>
@endsection
