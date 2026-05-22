@extends('dsh.master')

@section('content')
<div class="container-fluid no-print-padding">
    <br class="no-print">
    
    <!-- Header -->
    <div class="row mb-4 no-print">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-7">
                            <h3 class="font-weight-bold mb-1">تفتیش اصلاحات و ریورس (Audit Corrections)</h3>
                            <p class="text-muted mb-0">نظارت بر تمامی معاملات اصلاحی و ریورس شده در سیستم</p>
                        </div>
                        <div class="col-md-5">
                            <form action="{{ route('accounting.reports.audit_corrections') }}" method="GET">
                                <div class="row no-gutters align-items-end justify-content-end">
                                    <div class="col-md-5 px-1">
                                        <label class="small font-weight-bold text-muted mb-1">از تاریخ:</label>
                                        <input type="date" name="start_date" value="{{ $startDate }}" class="form-control bg-light border-0 rounded-pill">
                                    </div>
                                    <div class="col-md-5 px-1">
                                        <label class="small font-weight-bold text-muted mb-1">تا تاریخ:</label>
                                        <input type="date" name="end_date" value="{{ $endDate }}" class="form-control bg-light border-0 rounded-pill">
                                    </div>
                                    <div class="col-md-2 px-1">
                                        <button type="submit" class="btn btn-danger btn-block rounded-pill shadow-sm"><i class="feather icon-filter"></i></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Audit List -->
    <div class="row">
        <div class="col-md-12">
            <div class="card border-0 shadow-lg printable-document" style="border-radius: 20px;">
                <div class="card-body p-5">
                    
                    <div class="text-center mb-5 d-none d-print-block">
                        <h2 class="font-weight-bold text-danger">گزارش تفتیش معاملات اصلاحی</h2>
                        <p>دوره: {{ $startDate }} الی {{ $endDate }}</p>
                    </div>

                    <table class="table table-hover border">
                        <thead class="bg-light">
                            <tr>
                                <th class="py-3 px-4">نمبر معامله (Trans ID)</th>
                                <th class="py-3 px-4">تاریخ</th>
                                <th class="py-3 px-4">مرجع (Reference)</th>
                                <th class="py-3 px-4">دلیل و توضیحات (Reason)</th>
                                <th class="py-3 text-right px-4">مبلغ اصلاحی (Amount)</th>
                                <th class="py-3 px-4">کاربر مسئول (Operator)</th>
                                <th class="py-3 text-center px-4 no-print">جزئیات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($report as $row)
                            <tr>
                                <td class="py-3 px-4 font-weight-bold text-danger">#{{ $row->id }}</td>
                                <td class="py-3 px-4">{{ $row->date }}</td>
                                <td class="py-3 px-4"><span class="badge badge-light-danger">{{ $row->reference }}</span></td>
                                <td class="py-3 px-4">{{ $row->description }}</td>
                                <td class="py-3 text-right px-4 font-weight-bold text-dark">{{ number_format($row->total_amount, 2) }} {{ $row->currency_code }}</td>
                                <td class="py-3 px-4"><span class="badge badge-light-secondary font-weight-bold">{{ $row->operator_name }}</span></td>
                                <td class="py-3 text-center px-4 no-print">
                                    <button class="btn btn-sm btn-outline-danger rounded-pill px-3" data-toggle="modal" data-target="#modal-tx-{{ $row->id }}">
                                        <i class="feather icon-eye mr-1"></i> مشاهده جزئیات
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="py-5 text-center text-muted">هیچ معامله اصلاحی در این دوره یافت نشد.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <!-- Modals container (rendered outside the table to prevent HTML parsing errors) -->
                    @foreach($report as $row)
                    <!-- Modal for Transaction details -->
                    <div class="modal fade" id="modal-tx-{{ $row->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel-{{ $row->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-xl" role="document">
                            <div class="modal-content" style="border-radius: 20px; text-align: right; direction: rtl;">
                                <div class="modal-header bg-danger text-white py-3">
                                    <h5 class="modal-title font-weight-bold text-white" id="modalLabel-{{ $row->id }}">
                                        تفتیش معامله اصلاحی و ریورس #{{ $row->id }}
                                    </h5>
                                    <button type="button" class="close text-white ml-0 mr-auto" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body p-4 text-dark text-right">
                                    <div class="row mb-4">
                                        <div class="col-md-6 border-left">
                                            <h6 class="font-weight-bold text-danger mb-3"><i class="feather icon-rotate-ccw mr-1"></i> معامله معکوس‌کننده (Reversal Entry)</h6>
                                            <table class="table table-sm table-borderless text-dark">
                                                <tr><td><strong>نمبر معامله:</strong> #{{ $row->id }}</td></tr>
                                                <tr><td><strong>تاریخ ریورس:</strong> {{ $row->date }}</td></tr>
                                                <tr><td><strong>مرجع:</strong> {{ $row->reference }}</td></tr>
                                                <tr><td><strong>توضیحات:</strong> {{ $row->description }}</td></tr>
                                                <tr><td><strong>کاربر صادرکننده:</strong> {{ $row->operator_name }}</td></tr>
                                            </table>
                                        </div>
                                        <div class="col-md-6">
                                            <h6 class="font-weight-bold text-muted mb-3"><i class="feather icon-file-text mr-1"></i> معامله اصلی معکوس شده (Original Transaction)</h6>
                                            @if($row->original_tx)
                                                <table class="table table-sm table-borderless text-dark">
                                                    <tr><td><strong>نمبر معامله اصلی:</strong> #{{ $row->original_tx->id }}</td></tr>
                                                    <tr><td><strong>تاریخ ثبت اصلی:</strong> {{ $row->original_tx->date }}</td></tr>
                                                    <tr><td><strong>مرجع اصلی:</strong> {{ $row->original_tx->reference }}</td></tr>
                                                    <tr><td><strong>توضیحات اصلی:</strong> {{ $row->original_tx->description }}</td></tr>
                                                </table>
                                            @else
                                                <p class="text-warning">سند معامله اصلی یافت نشد یا در دوره خارج از محدوده است.</p>
                                            @endif
                                        </div>
                                    </div>

                                    <hr>

                                    <!-- Comparison tables -->
                                    <div class="row">
                                        <!-- Reversal Entries Table -->
                                        <div class="col-md-6">
                                            <h6 class="font-weight-bold text-dark mb-2">تراکنش‌های دفتر کل ریورس (Reversal Journal Entries)</h6>
                                            <table class="table table-striped table-sm border text-dark">
                                                <thead class="bg-light">
                                                    <tr>
                                                        <th>حساب (Account)</th>
                                                        <th class="text-right">بدهکار (Debit)</th>
                                                        <th class="text-right">بستانکار (Credit)</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($row->entries as $entry)
                                                        <tr>
                                                            <td>{{ $entry->account->account_code }} - {{ $entry->account->account_name }}</td>
                                                            <td class="text-right text-success font-weight-bold">{{ $entry->base_debit > 0 ? number_format($entry->base_debit, 2) : '-' }}</td>
                                                            <td class="text-right text-danger font-weight-bold">{{ $entry->base_credit > 0 ? number_format($entry->base_credit, 2) : '-' }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>

                                        <!-- Original Entries Table -->
                                        <div class="col-md-6">
                                            <h6 class="font-weight-bold text-dark mb-2">تراکنش‌های دفتر کل معامله اصلی (Original Journal Entries)</h6>
                                            @if($row->original_tx)
                                                <table class="table table-striped table-sm border text-dark">
                                                    <thead class="bg-light">
                                                        <tr>
                                                            <th>حساب (Account)</th>
                                                            <th class="text-right">بدهکار (Debit)</th>
                                                            <th class="text-right">بستانکار (Credit)</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($row->original_tx->entries as $entry)
                                                            <tr>
                                                                <td>{{ $entry->account->account_code }} - {{ $entry->account->account_name }}</td>
                                                                <td class="text-right text-success font-weight-bold">{{ $entry->base_debit > 0 ? number_format($entry->base_debit, 2) : '-' }}</td>
                                                                <td class="text-right text-danger font-weight-bold">{{ $entry->base_credit > 0 ? number_format($entry->base_credit, 2) : '-' }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            @else
                                                <p class="text-muted text-center py-4">سند معامله اصلی موجود نیست.</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer bg-light py-2">
                                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-dismiss="modal">بستن</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach

                    <div class="mt-5 no-print text-right">
                        <button onclick="window.print()" class="btn btn-danger btn-lg px-5 rounded-pill shadow-lg"><i class="feather icon-alert-triangle mr-2"></i> چاپ لیست تفتیش</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .badge-light-danger { background: #fbe9e7; color: #d50000; font-weight: bold; }
    @media print {
        .no-print { display: none !important; }
        .printable-document { border: none !important; box-shadow: none !important; }
    }
</style>
@endsection
