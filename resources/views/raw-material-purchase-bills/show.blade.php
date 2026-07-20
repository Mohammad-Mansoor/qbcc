@extends('dsh.master')
@section('title', 'بل خرید مواد خام - ' . $bill->bill_number)
@section('content')
<style>
    :root {
        --QBIC-primary: #0f172a;
        --QBIC-secondary: #334155;
        --QBIC-accent: #2563eb;
        --QBIC-border: #cbd5e1;
        --radius-lg: 16px;
        --shadow-soft: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
    }

    .invoice-card {
        background: #ffffff;
        border: 1px solid var(--QBIC-border);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-soft);
        padding: 40px;
        margin-bottom: 30px;
        direction: rtl;
        font-family: 'Inter', 'Segoe UI', Tahoma, sans-serif;
    }

    .invoice-header {
        border-bottom: 2px solid var(--QBIC-primary);
        padding-bottom: 20px;
        margin-bottom: 30px;
    }

    .company-logo-section {
        text-align: right;
    }

    .company-name {
        font-size: 24px;
        font-weight: 800;
        color: var(--QBIC-primary);
        margin-bottom: 5px;
    }

    .company-details {
        font-size: 12px;
        color: #64748b;
        line-height: 1.6;
    }

    .invoice-title-section {
        text-align: left;
    }

    .invoice-title {
        font-size: 28px;
        font-weight: 900;
        color: var(--QBIC-accent);
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 10px;
    }

    .invoice-meta-table {
        font-size: 13px;
        width: 100%;
        border-collapse: collapse;
    }

    .invoice-meta-table td {
        padding: 4px 8px;
    }

    .invoice-meta-table td.label {
        font-weight: 700;
        color: #64748b;
        text-align: left;
        padding-left: 15px;
    }

    .invoice-meta-table td.value {
        font-weight: 700;
        color: var(--QBIC-primary);
        text-align: right;
    }

    .billing-section {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 30px;
    }

    .billing-title {
        font-size: 14px;
        font-weight: 800;
        color: var(--QBIC-primary);
        border-bottom: 1px solid #e2e8f0;
        padding-bottom: 8px;
        margin-bottom: 12px;
    }

    .billing-details {
        font-size: 13px;
        color: var(--QBIC-secondary);
        line-height: 1.8;
    }

    .table-invoice {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 30px;
    }

    .table-invoice th {
        background: var(--QBIC-primary);
        color: #ffffff;
        font-weight: 700;
        font-size: 12px;
        padding: 12px 10px;
        text-align: center;
        border: 1px solid var(--QBIC-primary);
    }

    .table-invoice td {
        padding: 12px 10px;
        border: 1px solid #e2e8f0;
        font-size: 13px;
        text-align: center;
        color: var(--QBIC-secondary);
    }

    .table-invoice tr:nth-child(even) {
        background: #f8fafc;
    }

    .totals-section {
        margin-bottom: 40px;
    }

    .totals-table {
        width: 100%;
        border-collapse: collapse;
    }

    .totals-table td {
        padding: 10px 15px;
        font-size: 14px;
        border-bottom: 1px solid #e2e8f0;
    }

    .totals-table tr.grand-total td {
        font-size: 18px;
        font-weight: 800;
        color: var(--QBIC-accent);
        border-top: 2px solid var(--QBIC-primary);
        border-bottom: 2px solid var(--QBIC-primary);
        background: #eff6ff;
    }

    .status-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        display: inline-block;
    }

    .badge-open {
        background: #dcfce7;
        color: #166534;
        border: 1px solid #bbf7d0;
    }

    .badge-closed {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }

    .signature-row {
        margin-top: 60px;
        border-top: 1px dashed #cbd5e1;
        padding-top: 30px;
    }

    .signature-box {
        text-align: center;
        font-size: 13px;
        color: #64748b;
    }

    .signature-line {
        border-top: 1px solid #94a3b8;
        width: 80%;
        margin: 40px auto 10px auto;
    }

    /* PRINT STYLES */
    @media print {
        body * {
            visibility: hidden;
        }

        #print-area,
        #print-area * {
            visibility: visible;
        }

        #print-area {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            padding: 0;
            margin: 0;
            border: none;
            box-shadow: none;
            direction: rtl;
        }

        .hide-on-print {
            display: none !important;
        }

        .table-invoice th {
            background-color: #0f172a !important;
            color: #ffffff !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .totals-table tr.grand-total td {
            background-color: #eff6ff !important;
            color: #2563eb !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
    }
</style>

<div class="row hide-on-print mb-4">
    <div class="col-sm-12 d-flex justify-content-between align-items-center bg-white p-3 rounded-lg shadow-sm">
        <div>
            <h4 class="font-weight-bold mb-1">مشاهده و چاپ بل خرید مواد خام</h4>
            <span class="text-muted small">مدیریت فاکتورهای رسمی و چاپ استاندارد</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            @if($bill->status == 'open')
                @can('edit_raw_material_bill')
                    <a href="{{ route('raw-material-purchase-bills.edit', $bill->id) }}"
                        class="btn btn-outline-info rounded-lg px-4 mr-2" style="font-weight: 600;">
                        <i class="fa fa-edit mr-1"></i> ویرایش بل (Edit)
                    </a>
                @endcan
                @can('close_raw_material_bill')
                    <form action="{{ route('raw-material-purchase-bills.close', $bill->id) }}" method="post"
                        class="d-inline ml-2"
                        onsubmit="return confirm('آیا از بستن این بل خرید مطمئن هستید؟ پس از بستن دیگر قادر به ویرایش آن نخواهید بود.');">
                        @csrf
                        <button type="submit" class="btn btn-danger rounded-lg px-4 shadow-sm" style="font-weight: 600;">
                            <i class="fa fa-lock mr-1"></i> بستن بل خرید
                        </button>
                    </form>
                @endcan
            @endif
            <div class="dropdown">
                <button class="btn btn-primary rounded-lg shadow px-4 dropdown-toggle" type="button"
                    id="printExportDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                    style="font-weight: 600;">
                    <i class="fa fa-print mr-1"></i> خروجی و چاپ
                </button>
                <div class="dropdown-menu dropdown-menu-right shadow-lg border-0" aria-labelledby="printExportDropdown"
                    style="border-radius: 12px; z-index: 10000;">
                    @can('print_raw_material_bill_pdf')
                        <a class="dropdown-item py-2"
                            href="{{ route('raw-material-purchase-bills.show', $bill->id) }}?export=pdf" target="_blank">
                            <i class="fa fa-file-pdf-o mr-2 text-danger"></i> خروجی PDF
                        </a>
                    @endcan
                    @can('export_raw_material_bill_excel')
                        <a class="dropdown-item py-2"
                            href="{{ route('raw-material-purchase-bills.show', $bill->id) }}?export=excel">
                            <i class="fa fa-file-excel-o mr-2 text-success"></i> خروجی Excel
                        </a>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="invoice-card" id="print-area">
            <!-- INVOICE HEADER -->
            <div class="invoice-header">
                <div class="row align-items-center">
                    <div class="col-md-6 col-sm-6 company-logo-section">
                        <div class="company-name">شرکت تولیدی قالین QBIC</div>
                        <div class="company-details">
                            آدرس: کابل، افغانستان<br>
                            ایمیل: info@QBIC.com | تلفن: +93 (0) 700 000 000<br>
                            سیستم مدیریت مالی Forensic ERP
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-6 invoice-title-section">
                        <div class="invoice-title">بل خرید مواد خام</div>
                        <table class="invoice-meta-table">
                            <tr>
                                <td class="value" style="font-family: monospace; font-size: 1.1rem; color: #2563eb;">
                                    {{ $bill->bill_number }}</td>
                                <td class="label">نمبر فاکتور / بل:</td>
                            </tr>
                            <tr>
                                <td class="value">{{ \Carbon\Carbon::parse($bill->date)->format('d M Y') }}</td>
                                <td class="label">تاریخ صدور:</td>
                            </tr>
                            <tr>
                                <td class="value">
                                    <span
                                        class="status-badge {{ $bill->status == 'open' ? 'badge-open' : 'badge-closed' }}">
                                        {{ $bill->status == 'open' ? 'باز (Open)' : 'بسته (Closed)' }}
                                    </span>
                                </td>
                                <td class="label">وضعیت بل:</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <!-- SUPPLIER INFO -->
            <div class="billing-section">
                <div class="row">
                    <div class="col-md-6 text-right">
                        <div class="billing-title"><i class="fa fa-user mr-1"></i> مشخصات فروشنده مواد خام</div>
                        <div class="billing-details">
                            <strong>نام تامین کننده:</strong> {{ $bill->seller->name ?? 'N/A' }}<br>
                            <strong>شماره تماس:</strong> {{ $bill->seller->phone ?? 'N/A' }}<br>
                            <strong>آدرس:</strong> {{ $bill->seller->address ?? 'N/A' }}
                        </div>
                    </div>
                    <div class="col-md-6 text-right">
                        <div class="billing-title"><i class="fa fa-home mr-1"></i> مشخصات تحویل‌گیرنده</div>
                        <div class="billing-details">
                            <strong>نام سازمان:</strong> دفتر مرکزی QBIC<br>
                            <strong>بخش تحویل‌گیرنده:</strong> مدیریت انبار و گدام مرکزی الیاف و رنگ<br>
                            <strong>آدرس دفتر:</strong> چهارراهی صدارت، کابل، افغانستان<br>
                            <strong>سیستم مالی:</strong> حسابداری دوبانده (Double Entry Ledger)
                        </div>
                    </div>
                </div>
            </div>

            <!-- MATERIALS LIST TABLE -->
            <table class="table-invoice">
                <thead>
                    <tr>
                        <th style="width: 5%">ردیف</th>
                        <th style="width: 15%">تاریخ خرید</th>
                        <th style="width: 20%">نوعیت مواد</th>
                        <th style="width: 20%">دسته‌بندی (Subtype)</th>
                        <th style="width: 15%">گدام (Warehouse)</th>
                        <th style="width: 10%">مقدار (KG)</th>
                        <th style="width: 10%">نرخ فی کیلو (ارز خرید)</th>
                        <th style="width: 15%">مجموع قیمت (Base USD)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchases as $index => $purchase)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $purchase->purchase_date }}</td>
                            <td class="font-weight-bold text-dark">{{ $purchase->materialType->material_type ?? 'N/A' }}
                            </td>
                            <td>
                                @if($purchase->materialCategory)
                                    {{ $purchase->materialCategory->material_category }}
                                @else
                                    —
                                @endif
                            </td>
                            <td>{{ $purchase->warehouse->name ?? 'N/A' }}</td>
                            <td class="font-weight-bold" style="direction: ltr;">{{ number_format($purchase->quantity, 2) }}
                                kg</td>
                            <td style="direction: ltr;">{{ number_format($purchase->price_per_kilo, 2) }} {{ $purchase->currency_code ?? 'USD' }}</td>
                            <td class="font-weight-bold text-dark" style="direction: ltr;">
                                ${{ number_format($purchase->total, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">هیچ خرید مواد خامی به این بل متصل نشده است.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- AGGREGATE TOTALS -->
            <div class="row totals-section">
                <div class="col-md-7"></div>
                <div class="col-md-5">
                    <table class="totals-table text-right">
                        <tr>
                            <td class="text-left font-weight-bold text-dark">{{ $purchases->count() }} فاکتور</td>
                            <td class="font-weight-bold text-muted">تعداد اقلام خرید (Items):</td>
                        </tr>
                        <tr>
                            <td class="text-left font-weight-bold text-dark" style="direction: ltr;">
                                {{ number_format($purchases->sum('quantity'), 2) }} kg</td>
                            <td class="font-weight-bold text-muted">مجموع کل وزن (Total Weight):</td>
                        </tr>
                        <tr class="grand-total">
                            <td class="text-left font-weight-bold" style="direction: ltr;">
                                ${{ number_format($bill->total_amount, 2) }}</td>
                            <td class="font-weight-bold">مبلغ کل قابل تادیه (Grand Total USD):</td>
                        </tr>
                        <tr>
                            <td class="text-left font-weight-bold text-success" style="direction: ltr;">
                                ${{ number_format($bill->paid_amount, 2) }}</td>
                            <td class="font-weight-bold text-muted">مجموع پرداخت شده (Total Paid USD):</td>
                        </tr>
                        <tr>
                            <td class="text-left font-weight-bold text-danger" style="direction: ltr;">
                                ${{ number_format($bill->remaining_balance, 2) }}</td>
                            <td class="font-weight-bold text-muted">باقیمانده (Remaining Balance USD):</td>
                        </tr>
                        <tr>
                            <td class="text-left">
                                <span
                                    class="status-badge {{ $bill->payment_status === 'paid' ? 'bg-success text-white' : ($bill->payment_status === 'partially_paid' ? 'bg-info text-white' : 'bg-warning text-dark') }}">
                                    {{ $bill->payment_status === 'paid' ? 'تصفیه شده (Paid)' : ($bill->payment_status === 'partially_paid' ? 'تادیه قسمتی (Partially Paid)' : 'پرداخت نشده (Unpaid)') }}
                                </span>
                            </td>
                            <td class="font-weight-bold text-muted">وضعیت پرداخت بل (Payment Status):</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- PAYMENT TRANSACTIONS / ALLOCATIONS HISTORY -->
            @if($bill->allocations && $bill->allocations->count() > 0)
            <div class="row mt-4 pt-4 border-top text-right"
                style="margin-top: 30px; border-top: 2px solid #ddd; padding-top: 20px;">
                <div class="col-12">
                    <h5 class="font-weight-bold text-dark mb-3" style="font-size: 15px; margin-bottom: 15px;"><i
                            class="fa fa-credit-card text-success mr-1"></i> تاریخچه تادیات و پرداخت‌های بل خرید
                        (Payment History)</h5>
                    <div class="table-responsive">
                        <table class="table-invoice"
                            style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                            <thead>
                                <tr style="background-color: #f8f9fa;">
                                    <th
                                        style="padding: 10px; font-size: 12px; border: 1px solid #dee2e6; text-align: center;">
                                        تاریخ پرداخت (Date)</th>
                                    <th
                                        style="padding: 10px; font-size: 12px; border: 1px solid #dee2e6; text-align: right;">
                                        توضیحات تراکنش (Description)</th>
                                    <th
                                        style="padding: 10px; font-size: 12px; border: 1px solid #dee2e6; text-align: center;">
                                        نوعیت پرداخت</th>
                                    <th
                                        style="padding: 10px; font-size: 12px; border: 1px solid #dee2e6; text-align: center;">
                                        مقدار پرداختی ارز اصلی (Amount)</th>
                                    <th
                                        style="padding: 10px; font-size: 12px; border: 1px solid #dee2e6; text-align: center;">
                                        نرخ تسعیر (FX Rate)</th>
                                    <th
                                        style="padding: 10px; font-size: 12px; border: 1px solid #dee2e6; text-align: center;">
                                        معادل دالر (USD Amount)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bill->allocations as $pay)
                                @php($sp = $pay->seller_payment)
                                @if($sp)
                                    <tr>
                                        <td
                                            style="padding: 10px; font-size: 12px; border: 1px solid #dee2e6; text-align: center;">
                                            {{ $sp->date }}</td>
                                        <td
                                            style="padding: 10px; font-size: 12px; border: 1px solid #dee2e6; text-align: right;">
                                            {{ $sp->description }}
                                        </td>
                                        <td
                                            style="padding: 10px; font-size: 12px; border: 1px solid #dee2e6; text-align: center;">
                                            <span
                                                class="status-badge {{ $sp->type == 'رسید' ? 'bg-success text-white' : 'bg-danger text-white' }}"
                                                style="padding: 3px 8px; border-radius: 4px; font-size: 11px;">
                                                {{ $sp->type == 'رسید' ? 'رسید (Inflow)' : 'گرفت (Outflow)' }}
                                            </span>
                                        </td>
                                        <td
                                            style="padding: 10px; font-size: 12px; border: 1px solid #dee2e6; text-align: center;">
                                            {{ number_format($pay->allocated_amount, 2) }} {{ $sp->currency_code }}</td>
                                        <td
                                            style="padding: 10px; font-size: 12px; border: 1px solid #dee2e6; text-align: center; direction: ltr;">
                                            {{ number_format($pay->exchange_rate, 8) }}</td>
                                        <td
                                            style="padding: 10px; font-size: 12px; border: 1px solid #dee2e6; text-align: center; font-weight: bold; color: #166534;">
                                            ${{ number_format($pay->base_allocated_amount, 2) }}</td>
                                    </tr>
                                @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            <!-- SIGNATURE SECTION -->
            <div class="row signature-row">
                <div class="col-md-4 col-sm-4 signature-box">
                    <div>امضا و تایید تحویل‌دهنده (فروشنده)</div>
                    <div class="signature-line"></div>
                </div>
                <div class="col-md-4 col-sm-4 signature-box">
                    <div>امضا و تایید مدیر گدام (رسیور)</div>
                    <div class="signature-line"></div>
                </div>
                <div class="col-md-4 col-sm-4 signature-box">
                    <div>امضای نهایی مدیریت مالی / خزانه</div>
                    <div class="signature-line"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection