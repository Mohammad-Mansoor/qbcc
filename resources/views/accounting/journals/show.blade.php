@extends('dsh.master')

@section('content')
<div class="container-fluid no-print-padding">
    <br class="no-print">
    
    <div class="row justify-content-center">
        <div class="col-lg-10">
            
            <!-- Printing Document Container -->
            <div class="printable-document card border-0 shadow-sm" style="border-radius: 15px;">
                
                <!-- Print-Only Header (Company Branding) -->
                <div class="d-none d-print-block print-header text-center mb-5">
                    <h1 class="font-weight-bold text-dark mb-1" style="letter-spacing: 2px;">QASIMI BROTHERS CARPET CO.</h1>
                    <p class="text-muted mb-4">شرکت تولیدی قـاسمی بـرادران - بخش امور مالی</p>
                    <div style="border-bottom: 2px solid #333; width: 100%; margin-bottom: 5px;"></div>
                    <div style="border-bottom: 1px solid #333; width: 100%;"></div>
                </div>

                <div class="card-body p-5">
                    <!-- Voucher Title Section -->
                    <div class="row align-items-center mb-5">
                        <div class="col-7">
                            <h2 class="font-weight-bold text-dark mb-1" id="voucher-title">
                                @php
                                    $titles = [
                                        'payment' => 'سند تادیاتی (Payment Voucher)',
                                        'receipt' => 'سند رسید (Receipt Voucher)',
                                        'journal' => 'سند روزنامچه (Journal Voucher)',
                                        'sales' => 'سند فروشات (Sales Voucher)',
                                        'adjustment' => 'سند تعدیلی (Adjustment Voucher)',
                                    ];
                                    echo $titles[$transaction->journal_type] ?? 'سند حسابداری (Accounting Voucher)';
                                @endphp
                            </h2>
                            <p class="text-muted mb-0 no-print">جزئیات کامل تراکنش ثبت شده در دفتر کل</p>
                        </div>
                        <div class="col-5 text-right">
                            <div class="d-inline-block text-left mr-4">
                                <span class="d-block small text-muted font-weight-bold">نمبر سند (Ref):</span>
                                <h4 class="mb-0 font-weight-bold text-primary">{{ $transaction->reference }}</h4>
                            </div>
                            <div class="d-inline-block text-left">
                                <span class="d-block small text-muted font-weight-bold">تاریخ (Date):</span>
                                <h4 class="mb-0 font-weight-bold text-dark">{{ $transaction->date }}</h4>
                            </div>
                        </div>
                    </div>

                    <!-- Description Box -->
                    <div class="voucher-description mb-5 p-4" style="background: #f9f9f9; border-right: 5px solid #4099ff; border-radius: 8px;">
                        <h6 class="font-weight-bold text-muted mb-2 small text-uppercase">شرح سند (Narrative):</h6>
                        <p class="mb-0 text-dark" style="font-size: 1.15rem; line-height: 1.6;">{{ $transaction->description }}</p>
                    </div>

                    <!-- Entries Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0" style="border: 2px solid #333 !important;">
                            <thead class="bg-dark text-white">
                                <tr>
                                    <th class="py-3 px-4 border-dark" style="width: 15%;">کد حساب</th>
                                    <th class="py-3 border-dark" style="width: 45%;">تفصیلات حساب (Account Detail)</th>
                                    <th class="py-3 text-right border-dark" style="width: 20%;">دیبت (Debit)</th>
                                    <th class="py-3 text-right border-dark px-4" style="width: 20%;">کریدت (Credit)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transaction->entries as $entry)
                                <tr class="{{ $entry->debit > 0 ? 'debit-row' : 'credit-row' }}">
                                    <td class="py-3 px-4 border-dark font-weight-bold">{{ $entry->account->account_code }}</td>
                                    <td class="py-3 border-dark">
                                        <div class="{{ $entry->credit > 0 ? 'pl-5' : '' }}">
                                            <span class="font-weight-bold text-dark">{{ $entry->account->account_name }}</span>
                                            @if($entry->party_id)
                                                <small class="d-block text-muted">طرف حساب: {{ $entry->party_type == 'App\Customer' ? 'مشتری' : 'فروشنده' }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="py-3 text-right border-dark font-weight-bold" style="font-size: 1.1rem;">
                                        @if($entry->debit > 0)
                                            {{ number_format($entry->debit, 2) }} <small class="text-muted font-weight-bold">{{ $entry->currency_code }}</small>
                                            @if($entry->currency_code !== 'USD')
                                                <small class="d-block text-muted font-weight-normal" style="font-size: 0.8rem;">(${{ number_format($entry->base_debit, 2) }})</small>
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="py-3 text-right border-dark px-4 font-weight-bold" style="font-size: 1.1rem;">
                                        @if($entry->credit > 0)
                                            {{ number_format($entry->credit, 2) }} <small class="text-muted font-weight-bold">{{ $entry->currency_code }}</small>
                                            @if($entry->currency_code !== 'USD')
                                                <small class="d-block text-muted font-weight-normal" style="font-size: 0.8rem;">(${{ number_format($entry->base_credit, 2) }})</small>
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-light">
                                <tr class="font-weight-bold">
                                    <td colspan="2" class="text-right py-4 px-4 border-dark" style="font-size: 1.2rem;">مجموع (Total Amount):</td>
                                    <td class="text-right py-4 border-dark text-primary font-weight-bold border-top-double" style="font-size: 1.2rem;">
                                        ${{ number_format($transaction->entries->sum('base_debit'), 2) }}
                                    </td>
                                    <td class="text-right py-4 px-4 border-dark text-primary font-weight-bold border-top-double" style="font-size: 1.2rem;">
                                        ${{ number_format($transaction->entries->sum('base_credit'), 2) }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Footer / Authentication Section -->
                    <div class="mt-5 pt-5 d-print-block d-none">
                        <div class="row text-center">
                            <div class="col-4">
                                <div style="border-top: 1px solid #333; padding-top: 10px;">
                                    <p class="font-weight-bold mb-0">ترتیب کننده (Prepared By)</p>
                                    <small class="text-muted">Accountant Signature</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div style="border-top: 1px solid #333; padding-top: 10px;">
                                    <p class="font-weight-bold mb-0">کنترل کننده (Checked By)</p>
                                    <small class="text-muted">Finance Manager</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div style="border-top: 1px solid #333; padding-top: 10px;">
                                    <p class="font-weight-bold mb-0">تایید نهایی (Approved By)</p>
                                    <small class="text-muted">CEO / Director</small>
                                </div>
                            </div>
                        </div>
                        <div class="mt-5 text-right small text-muted">
                            <p>پرینت شده توسط سیستم در تاریخ: {{ date('Y-m-d H:i') }}</p>
                        </div>
                    </div>

                    <!-- Web Actions -->
                    <div class="row mt-5 no-print">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <div class="mr-3">
                                    <span class="small d-block text-muted">حالت سند:</span>
                                    @if($transaction->status == 'posted')
                                        <span class="badge badge-success px-4 py-2 rounded-pill shadow-sm">تایید شده (POSTED)</span>
                                    @else
                                        <span class="badge badge-danger px-4 py-2 rounded-pill shadow-sm">ابطال شده (REVERSED)</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 text-right">
                             @if($transaction->status == 'posted')
                                <button type="button" class="btn btn-outline-danger px-4 rounded-pill mr-2" data-toggle="modal" data-target="#reverseModal">
                                    <i class="feather icon-rotate-ccw mr-1"></i> ابطال سند
                                </button>
                             @endif
                             <a href="{{ route('accounting.journals.print', $transaction->id) }}" target="_blank" class="btn btn-dark rounded-pill px-4 shadow">
                                <i class="feather icon-printer mr-2"></i> چاپ سند (Print)
                             </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reverse Modal (Keep existing) -->
<div class="modal fade" id="reverseModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0" style="border-radius: 15px;">
            <form action="{{ route('accounting.journals.reverse', $transaction->id) }}" method="POST">
                @csrf
                <div class="modal-header border-0 p-4">
                    <h5 class="modal-title font-weight-bold">تایید ابطال سند</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4 pt-0">
                    <p>آیا مطمئن هستید که می‌خواهید این سند را ابطال کنید؟ این عمل اثر مالی این سند را خنثی خواهد کرد.</p>
                    <div class="form-group mb-0">
                        <label class="font-weight-bold small text-muted">دلیل ابطال:</label>
                        <textarea name="reason" class="form-control bg-light border-0" rows="3" placeholder="مثلاً: اشتباه در مبلغ..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-dismiss="modal">انصراف</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4">تایید ابطال</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .border-top-double { border-top: 4px double #333 !important; }
    
    @media print {
        @page { size: A4; margin: 20mm; }
        body { background: #fff !important; }
        .no-print, .pcoded-navbar, .pcoded-header, .btn, .modal { display: none !important; }
        .pcoded-main-container { margin-left: 0 !important; margin-top: 0 !important; }
        .no-print-padding { padding: 0 !important; }
        .printable-document { box-shadow: none !important; border: none !important; }
        .card-body { padding: 0 !important; }
        table { width: 100% !important; border: 2px solid #000 !important; color: #000 !important; }
        th { background-color: #000 !important; color: #fff !important; -webkit-print-color-adjust: exact; }
        td, th { border: 1px solid #000 !important; }
        .bg-light { background-color: #f8f9fa !important; -webkit-print-color-adjust: exact; }
    }
</style>
@endsection
