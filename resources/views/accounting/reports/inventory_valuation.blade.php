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
                        <div class="col-md-8">
                            <h3 class="font-weight-bold mb-1">گزارش ارزش موجودی گدام (Inventory Valuation)</h3>
                            <p class="text-muted mb-0">محاسبه ارزش مالی کالاهای موجود در انبار بر اساس روش WAC</p>
                        </div>
                        <div class="col-md-4 text-right">
                            <form action="{{ route('accounting.reports.inventory_valuation') }}" method="GET" class="form-inline justify-content-end">
                                <label class="mr-2 font-weight-bold">تا تاریخ:</label>
                                <input type="date" name="date" value="{{ $date }}" class="form-control bg-light border-0 rounded-pill mr-2">
                                <button type="submit" class="btn btn-primary rounded-pill shadow-sm px-4">نمایش</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @php $grandTotal = $report->sum('total_value'); @endphp

    <!-- Dashboard Summary -->
    <div class="row mb-4 no-print">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm p-5 text-white" style="border-radius: 20px; background: linear-gradient(45deg, #1a237e, #3f51b5);">
                <div class="d-flex align-items-center">
                    <div class="bg-white-50 p-3 rounded-circle mr-4">
                        <i class="feather icon-package f-30 text-white"></i>
                    </div>
                    <div>
                        <span class="opacity-75 d-block mb-1">ارزش مجموعی موجودی (Grand Total Value)</span>
                        <h1 class="font-weight-bold mb-0 text-white">{{ number_format($grandTotal, 2) }} <small class="f-14">USD</small></h1>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm p-5 text-dark" style="border-radius: 20px; background: #fff;">
                <div class="d-flex align-items-center">
                    <div class="bg-light p-3 rounded-circle mr-4">
                        <i class="feather icon-calendar f-30 text-primary"></i>
                    </div>
                    <div>
                        <span class="text-muted d-block mb-1">تاریخ گزارش (As of Date)</span>
                        <h2 class="font-weight-bold mb-0">{{ $date }}</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Details Table -->
    <div class="row">
        <div class="col-md-12">
            <div class="card border-0 shadow-lg printable-document" style="border-radius: 20px;">
                <div class="card-body p-5">
                    
                    <div class="text-center d-none d-print-block mb-5">
                        <h2 class="font-weight-bold">{{ config('company.name') }}</h2>
                        <h4>صورت ریز ارزش موجودی گدام</h4>
                        <p>به تاریخ: {{ $date }}</p>
                    </div>

                    <table class="table table-hover border">
                        <thead class="bg-light">
                            <tr>
                                <th class="py-3 px-4">نوعیت کالا / مواد (Item Category)</th>
                                <th class="py-3 text-center px-4">مقدار موجود (On Hand Qty)</th>
                                <th class="py-3 text-right px-4">ارزش مالی مجموعی (Total Value)</th>
                                <th class="py-3 text-right px-4">اوسط قیمت (Avg Cost)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($report as $row)
                            @php 
                                $typeName = str_replace('App\\', '', $row->item_type);
                                if($typeName == 'MaterialStock') $typeName = 'مواد خام (Materials)';
                                elseif($typeName == 'Carpet') $typeName = 'قالین‌های تکمیل شده (Carpets)';
                            @endphp
                            <tr>
                                <td class="py-3 px-4 font-weight-bold">{{ $typeName }}</td>
                                <td class="py-3 text-center px-4">
                                    <span class="badge badge-light px-3 py-2 f-14">{{ number_format($row->on_hand_qty, 2) }}</span>
                                </td>
                                <td class="py-3 text-right px-4 font-weight-bold text-primary f-16">{{ number_format($row->total_value, 2) }}</td>
                                <td class="py-3 text-right px-4 text-muted small">
                                    {{ $row->on_hand_qty > 0 ? number_format($row->total_value / $row->on_hand_qty, 2) : '0.00' }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-light font-weight-bold">
                            <tr>
                                <td colspan="2" class="py-3 px-4 f-18">مجموع کل ارزش گدام</td>
                                <td class="py-3 text-right px-4 f-20 text-dark border-top-2 border-dark">{{ number_format($grandTotal, 2) }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>

                    <div class="text-right no-print mt-5">
                        <button onclick="window.print()" class="btn btn-primary btn-lg px-5 rounded-pill shadow-lg"><i class="feather icon-printer mr-2"></i> چاپ لیست ارزش موجودی</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-white-50 { background: rgba(255,255,255,0.2); }
    .f-30 { font-size: 30px; }
    .f-14 { font-size: 14px; }
    .f-16 { font-size: 16px; }
    .f-18 { font-size: 18px; }
    .f-20 { font-size: 20px; }
    @media print {
        .no-print { display: none !important; }
        .printable-document { border: none !important; box-shadow: none !important; }
    }
</style>
@endsection
