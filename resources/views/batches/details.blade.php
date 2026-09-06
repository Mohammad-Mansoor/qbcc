@extends('dsh.master')
@section('title', 'جزئیات نمبر مسلسل تولید')
@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Action Header -->
    <div class="row mb-4 hideOnPrint">
        <div class="col-md-6 text-right d-flex align-items-center">
            <h3 class="mb-0 font-weight-bold text-dark">
                <i class="fa fa-file-text-o text-primary mr-2"></i> 
                جزئیات نمبر مسلسل #{{ $batch->reference_number }}
            </h3>
            @if($batch->status === 'open')
                <span class="badge badge-success ml-3 font-weight-bold px-3 py-2 rounded-pill shadow-sm" style="font-size: 0.85rem;">
                    <i class="fa fa-unlock-alt mr-1"></i> فعال / باز (Open)
                </span>
            @else
                <span class="badge badge-danger ml-3 font-weight-bold px-3 py-2 rounded-pill shadow-sm" style="font-size: 0.85rem;">
                    <i class="fa fa-lock mr-1"></i> غیرفعال / بسته (Closed)
                </span>
            @endif
        </div>
        <div class="col-md-7 text-left d-flex align-items-center justify-content-end">
            <!-- Date Filter Form -->
            <form method="GET" action="{{ route('batches.details', $batch->id) }}" class="form-inline ml-3 hideOnPrint" style="border: 1px solid #ddd; padding: 5px; border-radius: 12px; background: #fff;">
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control form-control-sm border-0" title="از تاریخ">
                <span class="mx-1 text-muted">-</span>
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control form-control-sm border-0" title="تا تاریخ">
                <button type="submit" class="btn btn-sm btn-info rounded px-3 ml-2 shadow-sm font-weight-bold">
                    <i class="fa fa-filter"></i> فیلتر
                </button>
                @if(request('start_date') || request('end_date'))
                    <a href="{{ route('batches.details', $batch->id) }}" class="btn btn-sm btn-light text-danger ml-1 shadow-sm rounded px-2" title="پاک کردن فیلتر"><i class="fa fa-times"></i></a>
                @endif
            </form>

            <div class="dropdown">
                <button class="btn btn-primary rounded-lg shadow px-4 dropdown-toggle font-weight-bold" type="button" id="printExportDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-print mr-1"></i> خروجی و چاپ
                </button>
                <div class="dropdown-menu dropdown-menu-right shadow-lg border-0" aria-labelledby="printExportDropdown" style="border-radius: 12px; z-index: 10000;">
                    @php
                        $pdfPermission = 'export_' . ($batch->type == 'wash' ? 'washing' : ($batch->type == 'finish' ? 'finishing' : 'kachaee')) . '_batches_pdf';
                        $excelPermission = 'export_' . ($batch->type == 'wash' ? 'washing' : ($batch->type == 'finish' ? 'finishing' : 'kachaee')) . '_batches_excel';
                    @endphp
                    @can($pdfPermission)
                    <a class="dropdown-item py-2" href="{{ route('batches.details', $batch->id) }}?export=pdf&start_date={{ request('start_date') }}&end_date={{ request('end_date') }}" target="_blank">
                        <i class="fa fa-file-pdf-o mr-2 text-danger"></i> خروجی PDF
                    </a>
                    @endcan
                    @can($excelPermission)
                    <a class="dropdown-item py-2" href="{{ route('batches.details', $batch->id) }}?export=excel&start_date={{ request('start_date') }}&end_date={{ request('end_date') }}">
                        <i class="fa fa-file-excel-o mr-2 text-success"></i> خروجی Excel
                    </a>
                    @endcan
                </div>
            </div>
            <a href="/dashboard/batches/{{ $batch->type }}" class="btn btn-light shadow-sm px-4 ml-2">بازگشت به لیست</a>
        </div>
    </div>

    <!-- Invoice Card -->
    <div class="card border-0 shadow-lg rounded-lg overflow-hidden" id="premiumBatchInvoice">
        <!-- Invoice Header -->
        <div class="card-header bg-white border-bottom p-5 text-center">
            <h2 class="font-weight-bold text-dark mb-2" style="font-size: 2.2rem; color: #1e3a8a !important;">{{ config('company.name') }}</h2>
            <p class="text-muted mb-4" style="font-size: 1.1rem;">{{ config('company.description') }}</p>
            
            <div class="d-inline-block px-5 py-2 mt-2 rounded-pill shadow-sm" style="background-color: #eff6ff; border: 1px solid #bfdbfe;">
                <h4 class="font-weight-bold mb-0" style="color: #1d4ed8;">
                    @if($batch->type == 'kachaee')
                        صورتحساب کچایی (Kachaee Payment Bill)
                    @elseif($batch->type == 'wash')
                        صورتحساب شستشو (Washing Payment Bill)
                    @elseif($batch->type == 'finish')
                        صورتحساب تیاری (Finishing Payment Bill)
                    @else
                        صورتحساب تولید (Production Payment Bill)
                    @endif
                </h4>
            </div>
        </div>
        
        <!-- Meta Details Section -->
        <div class="card-body px-5 py-4 bg-light border-bottom">
            <div class="row">
                <div class="col-md-6 text-right border-left">
                    <table class="table table-borderless table-sm mb-0">
                        <tr>
                            <td class="text-muted font-weight-bold align-middle" style="width: 160px;">تیم کاری / بخش مربوطه:</td>
                            <td class="font-weight-bold text-dark align-middle" style="font-size: 1.1rem;">{{ $team->name ?? '--- ثبت نشده ---' }}</td>
                        </tr>
                        @if(isset($team->phone))
                        <tr>
                            <td class="text-muted font-weight-bold align-middle">شماره تماس:</td>
                            <td class="align-middle">{{ $team->phone }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td class="text-muted font-weight-bold align-middle">نوعیت مرحله:</td>
                            <td class="align-middle">
                                <span class="badge badge-info px-3 py-1 shadow-sm" style="font-size: 0.95rem;">
                                    @if($batch->type == 'kachaee') کچایی (Kachaee)
                                    @elseif($batch->type == 'wash') شستشو (Washing)
                                    @elseif($batch->type == 'finish') تیاری (Finishing)
                                    @endif
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6 text-right">
                    <table class="table table-borderless table-sm mb-0 pl-md-4">
                        <tr>
                            <td class="text-muted font-weight-bold align-middle" style="width: 150px;">نمبر مسلسل / بل:</td>
                            <td class="font-weight-bold text-primary align-middle" style="font-size: 1.4rem; font-family: monospace;">{{ $batch->reference_number }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted font-weight-bold align-middle">تاریخ ایجاد:</td>
                            <td class="align-middle" style="direction: ltr; text-align: right; font-weight: 500;">{{ $batch->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted font-weight-bold align-middle">خلاصه کارکرد:</td>
                            <td class="align-middle font-weight-bold text-dark">
                                <span class="badge badge-light border mr-2 px-3 py-2" style="direction: ltr; display: inline-flex; align-items: center; font-size: 0.95rem; background-color: #f8fafc; color: #1e293b;">
                                    <i class="fa fa-cubes text-primary mr-2"></i> {{ $carpets->count() }} Pcs
                                </span>
                                <span class="badge badge-light border px-3 py-2" style="direction: ltr; display: inline-flex; align-items: center; font-size: 0.95rem; background-color: #f8fafc; color: #1e293b;">
                                    <i class="fa fa-map-o text-info mr-2"></i> {{ number_format($carpets->sum(function($c) { return $c->carpet->area ?? 0; }), 2) }} m²
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Metric Cards -->
        <div class="px-5 pb-4 hideOnPrint">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="card bg-light-primary border-0 rounded-lg p-4 shadow-sm text-right" style="border-right: 5px solid #3b82f6 !important; background-color: #eff6ff;">
                        <h6 class="text-muted font-weight-bold small mb-1">مجموع کل هزینه (Total Cost)</h6>
                        <h3 class="text-primary font-weight-bold mb-0">${{ number_format($totalCost, 2) }}</h3>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card bg-light-success border-0 rounded-lg p-4 shadow-sm text-right" style="border-right: 5px solid #10b981 !important; background-color: #ecfdf5;">
                        <h6 class="text-muted font-weight-bold small mb-1">مجموع پرداخت شده (Paid)</h6>
                        <h3 class="text-success font-weight-bold mb-0">${{ number_format($totalPaid, 2) }}</h3>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card bg-light-danger border-0 rounded-lg p-4 shadow-sm text-right" style="border-right: 5px solid #ef4444 !important; background-color: #fef2f2;">
                        <h6 class="text-muted font-weight-bold small mb-1">باقی‌مانده طلب (Remaining)</h6>
                        <h3 class="{{ $remaining > 0 ? 'text-danger' : 'text-primary' }} font-weight-bold mb-0">${{ number_format($remaining, 2) }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <!-- Table of Carpets -->
            <div class="px-5 pt-3">
                <h5 class="font-weight-bold text-dark mb-3"><i class="fa fa-th-list text-primary mr-1"></i> لیست قالین‌های شامل این نمبر</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-5 text-right">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th class="border-0 px-5 py-3">نمبر قالین</th>
                            <th class="border-0 py-3 text-center">شماره نقشه (Map No)</th>
                            <th class="border-0 py-3 text-center">نوعیت (Type)</th>
                            <th class="border-0 py-3 text-center">کیفیت (Quality)</th>
                            <th class="border-0 py-3 text-center">طول (m)</th>
                            <th class="border-0 py-3 text-center">عرض (m)</th>
                            <th class="border-0 py-3 text-center">مساحت (m²)</th>
                            <th class="border-0 py-3 text-center">گدام فعلی (Warehouse)</th>
                            @if($batch->type == 'finish')
                                <th class="border-0 py-3 text-center">کتگوری تیاری</th>
                            @endif
                            <th class="border-0 py-3 text-center">هزینه فی متر ($)</th>
                            <th class="border-0 py-3 text-left px-5">قیمت کل ($)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($carpets as $item)
                            <tr class="border-bottom">
                                <td class="px-5 py-3 font-weight-bold text-primary">
                                    {{ $item->carpet->carpet_no ?? 'N/A' }}
                                </td>
                                <td class="text-center font-weight-bold text-dark">
                                    {{ $item->carpet->map_number ?? '---' }}
                                </td>
                                <td class="text-center">
                                    {{ $item->carpet->type->carpet_type ?? '---' }}
                                </td>
                                <td class="text-center">
                                    {{ $item->carpet->quality->quality ?? '---' }}
                                </td>
                                <td class="text-center" style="direction: ltr;">
                                    {{ $item->carpet->height ?? '---' }}
                                </td>
                                <td class="text-center" style="direction: ltr;">
                                    {{ $item->carpet->width ?? '---' }}
                                </td>
                                <td class="text-center font-weight-bold">
                                    {{ number_format($item->carpet->area ?? 0, 2) }}
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-light font-weight-bold border px-2 py-1" style="background-color: #f1f5f9; color: #1e293b;">
                                        <i class="fa fa-building-o text-info mr-1"></i> {{ $item->carpet->warehouse->name ?? '---' }}
                                    </span>
                                </td>
                                @php
                                    $area = isset($item->carpet->area) && $item->carpet->area > 0 ? $item->carpet->area : 1;
                                    $price = 0;
                                    if($batch->type == 'finish') $price = $item->price;
                                    elseif($batch->type == 'kachaee') $price = $item->total_price;
                                    elseif($batch->type == 'wash') $price = $item->total_price;

                                    if (isset($item->price) && $item->price > 0 && ($item->currency_code ?? 'USD') === 'USD') {
                                        $unitRate = $item->price;
                                    } else {
                                        $unitRate = round($price / $area, 2);
                                    }
                                @endphp
                                @if($batch->type == 'finish')
                                    <td class="text-center">
                                        <span class="badge badge-light font-weight-bold border">
                                            {{ $item->category->category ?? '---' }}
                                        </span>
                                    </td>
                                @endif
                                <td class="text-center font-weight-bold text-dark" style="direction: ltr;">
                                    ${{ number_format($unitRate, 2) }}
                                </td>
                                <td class="text-left px-5 font-weight-bold text-dark" style="direction: ltr;">
                                    ${{ number_format($price, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $batch->type == 'finish' ? 11 : 10 }}" class="text-center text-muted py-5">
                                    <i class="fa fa-folder-open-o fa-2x mb-2 d-block"></i>
                                    هیچ قالینی تحت این نمبر مسلسل به ثبت نرسیده است.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Payment Transactions History -->
            <div class="px-5 pt-4 border-top" style="border-top: 2px solid #eee !important;">
                <h5 class="font-weight-bold text-dark mb-3"><i class="fa fa-credit-card text-success mr-1"></i> تاریخچه تادیات و پرداخت‌های مستقیم (Payment History)</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-right">
                    <thead class="bg-dark text-white">
                        <tr>
                            <th class="border-0 px-5 py-3">تاریخ پرداخت</th>
                            <th class="border-0 py-3">تفصیلات و بابت</th>
                            <th class="border-0 py-3 text-center">نوعیت</th>
                            <th class="border-0 py-3 text-center">ارز اصلی</th>
                            <th class="border-0 py-3 text-center">نرخ تسعیر</th>
                            <th class="border-0 py-3 text-left px-5">معادل دالر ($)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $pay)
                            <tr class="border-bottom">
                                <td class="px-5 py-3">{{ $pay->date }}</td>
                                <td class="font-weight-bold text-dark">{{ $pay->description }}</td>
                                <td class="text-center">
                                    @if($pay->type == 'گرفت')
                                        <span class="badge badge-success px-2 py-1"><i class="fa fa-arrow-down mr-1"></i> پرداخت به تیم (Outflow)</span>
                                    @else
                                        <span class="badge badge-warning px-2 py-1"><i class="fa fa-arrow-up mr-1"></i> برگشت/رسید (Inflow)</span>
                                    @endif
                                </td>
                                <td class="text-center">{{ number_format($pay->original_amount, 2) }} {{ $pay->currency_code }}</td>
                                <td class="text-center font-weight-bold" style="direction: ltr;">{{ number_format($pay->exchange_rate, 4) }}</td>
                                <td class="text-left px-5 font-weight-bold text-success">
                                    ${{ number_format($pay->base_amount ?? ($pay->original_amount * ($pay->exchange_rate > 0 ? $pay->exchange_rate : 1)), 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-5">
                                    <i class="fa fa-money fa-2x mb-2 d-block"></i>
                                    هیچ پرداخت یا پیش‌پرداختی برای این نمبر مسلسل به ثبت نرسیده است.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Invoice Footer -->
        <div class="card-footer bg-white border-0 p-5 mt-5">
            <div class="row">
                <div class="col-md-7 text-right">
                    <div class="p-3 rounded-lg border border-dashed text-muted small" style="border-style: dashed !important; border-width: 1px !important;">
                        <h6 class="font-weight-bold text-dark small mb-2">توضیحات و شرایط تسویه:</h6>
                        <ul class="mb-0 pr-3">
                            <li>این صورتحساب جهت تصفیه حساب با تیم‌های تولیدی بر اساس مبالغ توافقی فوق تنظیم گردیده است.</li>
                            <li>تمامی پرداخت‌های مستقیم در جدول تاریخچه تادیات منعکس گردیده و از مانده بدهی کسر شده است.</li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="bg-light p-4 rounded-lg shadow-sm" style="direction: ltr;">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Total Cost:</span>
                            <span class="text-dark font-weight-bold">${{ number_format($totalCost, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Total Paid:</span>
                            <span class="text-success font-weight-bold">${{ number_format($totalPaid, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3 border-top pt-2 mt-2">
                            <h5 class="text-primary font-weight-bold mb-0">BALANCE DUE:</h5>
                            <h5 class="{{ $remaining > 0 ? 'text-danger' : 'text-primary' }} font-weight-bold mb-0">${{ number_format($remaining, 2) }}</h5>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Signature Placeholders -->
            <div class="row mt-5 pt-5 text-center">
                <div class="col-4">
                    <div class="border-top pt-2 mx-4 text-muted tiny font-weight-bold">تنظیم کننده صورتحساب</div>
                </div>
                <div class="col-4">
                    <div class="border-top pt-2 mx-4 text-muted tiny font-weight-bold">تایید و مهر مدیریت</div>
                </div>
                <div class="col-4">
                    <div class="border-top pt-2 mx-4 text-muted tiny font-weight-bold">امضا و تایید نماینده تیم</div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    #premiumBatchInvoice { 
        font-family: 'Inter', 'Outfit', sans-serif; 
    }
    .rounded-lg { 
        border-radius: 1rem !important; 
    }
    .tiny { 
        font-size: 11px; 
    }
    @media print {
        body { 
            background: white !important; 
        }
        .hideOnPrint { 
            display: none !important; 
        }
        .card { 
            box-shadow: none !important; 
            border: none !important; 
        }
        .card-header { 
            padding: 0 !important; 
        }
        .container-fluid { 
            padding: 0 !important; 
        }
        .bg-primary { 
            background-color: #007bff !important; 
            -webkit-print-color-adjust: exact; 
        }
        .text-white { 
            color: white !important; 
            -webkit-print-color-adjust: exact; 
        }
    }
</style>

<script>
    function printPage(divName) {
        var printContents = document.getElementById(divName).innerHTML;
        var originalContents = document.body.innerHTML;
        
        // Temporarily style for print
        document.body.innerHTML = printContents;
        window.print();
        
        // Restore page
        document.body.innerHTML = originalContents;
        window.location.reload();
    }
</script>
@endsection
