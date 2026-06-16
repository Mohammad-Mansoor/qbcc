@extends('dsh.master')

@section('content')
<div class="container-fluid no-print-padding">
    <br>
    
    <!-- Header Section -->
    <div class="row mb-4 no-print">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm" style="border-radius: 15px; background: linear-gradient(45deg, #1e3a8a, #3b82f6);">
                <div class="card-body p-4 text-white">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h3 class="text-white font-weight-bold mb-1"><i class="feather icon-package mr-2"></i>گزارش ورودی و خروجی گدام‌ها (Warehouse IN/OUT)</h3>
                            <p class="mb-0 opacity-80">گزارش جامع و مانیتورینگ کلیه تراکنش‌ها، انتقالات، خریدها و خروجی‌های گدام‌های شرکت.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="row mb-4 no-print">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm" style="border-radius: 15px; border-right: 5px solid #3b82f6;">
                <div class="card-body p-4">
                    <form action="{{ route('accounting.warehouses.movements') }}" method="GET" id="filterForm">
                        <div class="row">
                            <!-- Warehouse Filter -->
                            <div class="col-md-3 mb-3">
                                <label class="font-weight-bold small text-muted mb-1">انتخاب گدام:</label>
                                <select name="warehouse_id" class="form-control rounded-pill bg-light border-0">
                                    <option value="">همه گدام‌ها</option>
                                    @foreach($warehouses as $wh)
                                        <option value="{{ $wh->id }}" {{ request('warehouse_id') == $wh->id ? 'selected' : '' }}>
                                            {{ $wh->name }} ({{ $wh->subtype_fa }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Direction Filter -->
                            <div class="col-md-2 mb-3">
                                <label class="font-weight-bold small text-muted mb-1">جهت تراکنش:</label>
                                <select name="direction" class="form-control rounded-pill bg-light border-0">
                                    <option value="">همه جهت‌ها</option>
                                    <option value="IN" {{ request('direction') == 'IN' ? 'selected' : '' }}>ورودی (IN)</option>
                                    <option value="OUT" {{ request('direction') == 'OUT' ? 'selected' : '' }}>خروجی (OUT)</option>
                                </select>
                            </div>

                            <!-- Item Type Filter -->
                            <div class="col-md-2 mb-3">
                                <label class="font-weight-bold small text-muted mb-1">نوعیت آیتم:</label>
                                <select name="item_type" class="form-control rounded-pill bg-light border-0">
                                    <option value="">همه نوعیت‌ها</option>
                                    <option value="carpet" {{ request('item_type') == 'carpet' ? 'selected' : '' }}>قالین (Carpet)</option>
                                    <option value="material" {{ request('item_type') == 'material' ? 'selected' : '' }}>مواد خام (Material)</option>
                                </select>
                            </div>

                            <!-- Transaction Type Filter -->
                            <div class="col-md-3 mb-3">
                                <label class="font-weight-bold small text-muted mb-1">نوع تراکنش:</label>
                                <select name="type" class="form-control rounded-pill bg-light border-0">
                                    <option value="">همه نوع تراکنش‌ها</option>
                                    <option value="PURCHASE" {{ request('type') == 'PURCHASE' ? 'selected' : '' }}>خرید مواد خام</option>
                                    <option value="PROD_SERVICE" {{ request('type') == 'PROD_SERVICE' ? 'selected' : '' }}>خدمات تولیدی</option>
                                    <option value="PROD_ISSUE" {{ request('type') == 'PROD_ISSUE' ? 'selected' : '' }}>مصرف مواد (تولید)</option>
                                    <option value="PROD_FINISH" {{ request('type') == 'PROD_FINISH' ? 'selected' : '' }}>ورود قالین از تولید</option>
                                    <option value="SALE" {{ request('type') == 'SALE' ? 'selected' : '' }}>فروش</option>
                                    <option value="TRANSFER_OUT" {{ request('type') == 'TRANSFER_OUT' ? 'selected' : '' }}>انتقال (خروج)</option>
                                    <option value="TRANSFER_IN" {{ request('type') == 'TRANSFER_IN' ? 'selected' : '' }}>انتقال (ورود)</option>
                                    <option value="REVERSAL" {{ request('type') == 'REVERSAL' ? 'selected' : '' }}>برگشتی / باطل شده</option>
                                    <option value="WASH" {{ request('type') == 'WASH' ? 'selected' : '' }}>شستشو</option>
                                    <option value="REPAIR" {{ request('type') == 'REPAIR' ? 'selected' : '' }}>ترمیم</option>
                                </select>
                            </div>

                            <!-- Search Filter -->
                            <div class="col-md-2 mb-3">
                                <label class="font-weight-bold small text-muted mb-1">جستجو:</label>
                                <input type="text" name="search" value="{{ request('search') }}" class="form-control rounded-pill bg-light border-0" placeholder="شماره سند ...">
                            </div>

                            <!-- Date Range Filters -->
                            <div class="col-md-3 mb-3">
                                <label class="font-weight-bold small text-muted mb-1">از تاریخ:</label>
                                <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control rounded-pill bg-light border-0">
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="font-weight-bold small text-muted mb-1">الی تاریخ:</label>
                                <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control rounded-pill bg-light border-0">
                            </div>

                            <div class="col-md-6 mb-3 d-flex align-items-end justify-content-end">
                                <button type="submit" class="btn btn-primary rounded-pill px-4 mr-2 shadow-sm" style="height: 38px;">
                                    <i class="feather icon-filter mr-1"></i> اعمال فیلتر
                                </button>
                                <a href="{{ route('accounting.warehouses.movements') }}" class="btn btn-light rounded-pill px-4 mr-2" style="height: 38px; display: inline-flex; align-items: center; justify-content: center;">
                                    پاک کردن فیلتر
                                </a>
                                <button type="submit" name="export" value="pdf" formtarget="_blank" class="btn btn-danger rounded-pill px-4 mr-2 shadow-sm" style="height: 38px;">
                                    <i class="fa fa-file-pdf-o mr-1"></i> چاپ / PDF
                                </button>
                                <button type="submit" name="export" value="excel" class="btn btn-success rounded-pill px-4 shadow-sm" style="height: 38px;">
                                    <i class="fa fa-file-excel-o mr-1"></i> خروجی اکسل
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Movements Table Card -->
    <div class="row">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm" style="border-radius: 15px; overflow: hidden;">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" style="font-size: 0.9rem;">
                            <thead class="bg-dark text-white">
                                <tr class="text-right">
                                    <th class="py-3 px-4">تاریخ ثبت</th>
                                    <th>شماره سند مرجع</th>
                                    <th>گدام</th>
                                    <th>نوعیت آیتم</th>
                                    <th>شرح آیتم</th>
                                    <th>نوع تراکنش</th>
                                    <th class="text-center">جهت حرکت</th>
                                    <th class="text-left">مقدار/تعداد</th>
                                    <th class="text-left">ابعاد (m)</th>
                                    <th class="text-left">مساحت (م²)</th>
                                    <th class="text-left">قیمت واحد (USD)</th>
                                    <th class="text-left">مجموع (USD)</th>
                                    <th>کاربر ثبت کننده</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactions as $tx)
                                <tr class="text-right">
                                    <td class="py-3 px-4 text-muted small" style="direction: ltr; font-family: monospace;">
                                        {{ \Carbon\Carbon::parse($tx->created_at)->format('Y-m-d H:i') }}
                                    </td>
                                    <td class="font-weight-bold">
                                        @if($tx->reference_type === 'App\PurchaseMaterial')
                                            @php
                                                $pm = \App\PurchaseMaterial::find($tx->reference_id);
                                            @endphp
                                            @if($pm && $pm->raw_material_purchase_bill_id)
                                                <a href="/dashboard/raw-material-purchase-bills/{{ $pm->raw_material_purchase_bill_id }}" target="_blank" class="text-primary font-weight-bold" style="text-decoration: underline;">
                                                    {{ $tx->reference_code }}
                                                </a>
                                            @else
                                                <span class="text-dark">{{ $tx->reference_code }}</span>
                                            @endif
                                        @elseif($tx->reference_type === 'App\MaterialSale')
                                            @php
                                                $ms = \App\MaterialSale::find($tx->reference_id);
                                            @endphp
                                            @if($ms && $ms->invoice_id)
                                                <a href="/dashboard/invoices/{{ $ms->invoice_id }}" target="_blank" class="text-info font-weight-bold" style="text-decoration: underline;">
                                                    {{ $tx->reference_code }}
                                                </a>
                                            @else
                                                <span class="text-dark">{{ $tx->reference_code }}</span>
                                            @endif
                                        @elseif($tx->reference_type === 'App\WarehouseTransfer')
                                            <a href="{{ route('accounting.transfers.show', $tx->reference_id) }}" target="_blank" class="text-warning font-weight-bold" style="text-decoration: underline;">
                                                {{ $tx->reference_code }}
                                            </a>
                                        @else
                                            <span class="text-dark">{{ $tx->reference_code }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $tx->warehouse ? $tx->warehouse->name : 'N/A' }}</td>
                                    <td>
                                        @if($tx->item && $tx->item->type === 'App\Carpet')
                                            <span class="badge badge-primary-light text-primary font-weight-bold rounded-pill px-3 py-1">قالین</span>
                                        @else
                                            <span class="badge badge-info-light text-info font-weight-bold rounded-pill px-3 py-1">مواد خام</span>
                                        @endif
                                    </td>
                                    <td class="font-weight-bold text-dark">{{ $tx->item_name }}</td>
                                    <td>{{ $tx->type_fa }}</td>
                                    <td class="text-center">
                                        @if($tx->direction === 'IN')
                                            <span class="badge badge-success rounded-pill px-3 py-1 font-weight-bold shadow-sm" style="font-size: 0.8rem;">
                                                <i class="feather icon-arrow-down-left mr-1"></i> ورود (IN)
                                            </span>
                                        @else
                                            <span class="badge badge-danger rounded-pill px-3 py-1 font-weight-bold shadow-sm" style="font-size: 0.8rem;">
                                                <i class="feather icon-arrow-up-right mr-1"></i> خروج (OUT)
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-left font-weight-bold font-mono">
                                        @if($tx->item && $tx->item->type === 'App\Carpet')
                                            {{ number_format($tx->quantity) }} تخته
                                        @else
                                            {{ number_format($tx->quantity, 2) }} KG
                                        @endif
                                    </td>
                                    <td class="text-left text-muted font-mono" style="direction: ltr;">
                                        @if($tx->item && $tx->item->type === 'App\Carpet')
                                            @php
                                                $actualCarpet = \App\Carpet::find($tx->item->ref_id);
                                            @endphp
                                            @if($actualCarpet && ($actualCarpet->height || $actualCarpet->width))
                                                {{ $actualCarpet->height ?? '-' }} x {{ $actualCarpet->width ?? '-' }}
                                            @else
                                                -
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-left font-weight-bold text-muted font-mono">
                                        {{ $tx->area > 0 ? number_format($tx->area, 2) . ' م²' : '-' }}
                                    </td>
                                    <td class="text-left text-muted font-mono">
                                        ${{ number_format($tx->unit_cost, 2) }}
                                    </td>
                                    <td class="text-left font-weight-bold text-dark font-mono">
                                        ${{ number_format($tx->total_cost, 2) }}
                                    </td>
                                    <td class="text-muted small">
                                        {{ $tx->creator ? $tx->creator->name : 'سیستم' }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="12" class="text-center py-5 text-muted">
                                        <i class="feather icon-info f-30 d-block mb-3"></i>
                                        هیچ تراکنشی یافت نشد.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Web Pagination -->
            @if($transactions->hasPages())
            <div class="d-flex justify-content-center mt-4 no-print">
                {{ $transactions->appends(request()->except('page'))->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<style>
    .badge-primary-light {
        background-color: rgba(64, 153, 255, 0.1);
    }
    .badge-info-light {
        background-color: rgba(0, 172, 193, 0.1);
    }
    .font-mono {
        font-family: monospace;
    }
    @media print {
        .no-print {
            display: none !important;
        }
        body {
            background-color: white !important;
        }
        .printable-table th {
            background-color: #343a40 !important;
            color: white !important;
        }
    }
</style>
@endsection
