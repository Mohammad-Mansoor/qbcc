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
                <h4 class="font-weight-bold mb-0" style="color: #1d4ed8;">صورتحساب تیاری (Finishing Payment Bill)</h4>
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
                                <span class="badge badge-info px-3 py-1 shadow-sm" style="font-size: 0.95rem;">تیاری (Finishing)</span>
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
                                    <i class="fa fa-cubes text-primary mr-2"></i> {{ $groupedCarpets->count() }} Pcs
                                </span>
                                <span class="badge badge-light border px-3 py-2" style="direction: ltr; display: inline-flex; align-items: center; font-size: 0.95rem; background-color: #f8fafc; color: #1e293b;">
                                    <i class="fa fa-map-o text-info mr-2"></i> {{ number_format($groupedCarpets->sum(function($group) { return $group->first()->carpet->area ?? 0; }), 2) }} m²
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Metric Cards -->
        <div class="px-5 pt-4 pb-2 hideOnPrint">
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
                <h5 class="font-weight-bold text-dark mb-3"><i class="fa fa-th-list text-primary mr-1"></i> لیست قالین‌ها و مصارف تیاری</h5>
            </div>
            <div class="table-responsive px-4">
                <table class="table table-hover table-bordered align-middle mb-5 text-center" style="font-size: 0.9rem;">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th class="border-0 align-middle px-3 py-3" style="width: 5%;">ردیف</th>
                            <th class="border-0 align-middle py-3">نمبر قالین</th>
                            <th class="border-0 align-middle py-3">نقشه</th>
                            <th class="border-0 align-middle py-3">نوعیت</th>
                            <th class="border-0 align-middle py-3">کیفیت</th>
                            <th class="border-0 align-middle py-3">طول (m)</th>
                            <th class="border-0 align-middle py-3">عرض (m)</th>
                            <th class="border-0 align-middle py-3">مساحت (m²)</th>
                            <th class="border-0 align-middle py-3">گدام فعلی (Warehouse)</th>
                            @foreach($batchCategories as $cat)
                                <th class="border-0 align-middle py-3" style="background-color: #2563eb; color: #ffffff !important;">{{ $cat->category }}</th>
                            @endforeach
                            <th class="border-0 align-middle py-3 bg-dark text-white" style="color: #ffffff !important;">مجموع هزینه ($)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php 
                            $grandTotalArea = 0;
                            $grandTotalPrice = 0;
                            $catTotals = [];
                            foreach($batchCategories as $cat) { $catTotals[$cat->id] = 0; }
                            $rowIndex = 1;
                        @endphp
                        @forelse($groupedCarpets as $carpetId => $works)
                            @php
                                $firstWork = $works->first();
                                $carpet = $firstWork->carpet;
                                $area = $carpet->area ?? 0;
                                $grandTotalArea += $area;
                                
                                $carpetTotal = $works->sum('price');
                                $grandTotalPrice += $carpetTotal;
                            @endphp
                            <tr>
                                <td class="font-weight-bold">{{ $rowIndex++ }}</td>
                                <td class="font-weight-bold text-primary">{{ $carpet->carpet_no ?? 'N/A' }}</td>
                                <td>{{ $carpet->map_number ?? '---' }}</td>
                                <td>{{ $carpet->type->carpet_type ?? '---' }}</td>
                                <td>{{ $carpet->quality->quality ?? '---' }}</td>
                                <td style="direction: ltr;">{{ $carpet->height ?? '---' }}</td>
                                <td style="direction: ltr;">{{ $carpet->width ?? '---' }}</td>
                                <td class="font-weight-bold">{{ number_format($area, 2) }}</td>
                                <td class="font-weight-bold">
                                    <span class="badge badge-light border px-2 py-1" style="background-color: #f1f5f9; color: #1e293b;">
                                        <i class="fa fa-building-o text-info mr-1"></i> {{ $carpet->warehouse->name ?? '---' }}
                                    </span>
                                </td>
                                
                                @foreach($batchCategories as $cat)
                                    @php
                                        $workForCat = $works->firstWhere('category_id', $cat->id);
                                        $workPrice = $workForCat ? $workForCat->price : 0;
                                        $catTotals[$cat->id] += $workPrice;
                                        
                                        $unitPriceStr = '';
                                        if ($workForCat && $workPrice > 0) {
                                            $totalOrig = ($workForCat->currency_code == 'AFN') ? $workForCat->price_af : $workForCat->price;
                                            $uPrice = 0;
                                            if (in_array($cat->id, [1, 3, 5, 6, 7, 9])) {
                                                $uPrice = $area > 0 ? ($totalOrig / $area) : 0;
                                            } elseif (in_array($cat->id, [4, 8])) {
                                                $uPrice = ($carpet->height > 0) ? ($totalOrig / ($carpet->height * 2)) : 0;
                                            } elseif ($cat->id == 2) {
                                                $uPrice = $totalOrig;
                                            }
                                            $curSym = $workForCat->currency_code == 'AFN' ? 'AFN' : '$';
                                            $unitPriceStr = $curSym . round($uPrice, 2);
                                        }
                                    @endphp
                                    <td style="direction: ltr;">
                                        @if($workPrice > 0)
                                            <span class="text-dark font-weight-bold">${{ number_format($workPrice, 2) }}</span><br>
                                            <span class="text-muted" style="font-size: 0.75rem;">({{ $unitPriceStr }}{{ $cat->id != 2 ? '/m' : '' }})</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                @endforeach
                                
                                <td class="font-weight-bold text-danger" style="direction: ltr; background-color: #fdf2f2;">${{ number_format($carpetTotal, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ 9 + $batchCategories->count() }}" class="text-center text-muted py-5">
                                    <i class="fa fa-folder-open-o fa-2x mb-2 d-block"></i>
                                    هیچ قالینی در این نمبر مسلسل یافت نشد.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-light">
                        <tr>
                            <td colspan="7" class="text-left font-weight-bold align-middle">مجموع کل:</td>
                            <td class="font-weight-bold align-middle text-primary">{{ number_format($grandTotalArea, 2) }} m²</td>
                            @foreach($batchCategories as $cat)
                                <td class="font-weight-bold align-middle text-dark" style="direction: ltr;">${{ number_format($catTotals[$cat->id], 2) }}</td>
                            @endforeach
                            <td class="font-weight-bold align-middle text-danger bg-dark" style="direction: ltr;">${{ number_format($grandTotalPrice, 2) }}</td>
                        </tr>
                    </tfoot>
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
</style>
@endsection
