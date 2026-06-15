@extends('dsh.master')

@section('title', 'گزارش موجودی گدام')

@section('content')

<style>
    :root {
        --primary-blue: #0A192F;
        --accent-green: #10B981;
        --accent-blue: #00acc1;
        --glass-bg: rgba(255, 255, 255, 0.95);
        --glass-border: rgba(255, 255, 255, 0.4);
        --glass-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.07);
    }

    body { background-color: #f4f7fa; font-family: 'Inter', sans-serif; direction: rtl; }

    /* Premium Header */
    .dashboard-header {
        background: linear-gradient(135deg, var(--primary-blue), #1e3c72);
        color: white;
        padding: 25px;
        border-radius: 16px;
        margin-bottom: 25px;
        box-shadow: 0 10px 20px rgba(10, 25, 47, 0.15);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    /* Table */
    .table-container {
        background: var(--glass-bg);
        border-radius: 16px;
        padding: 20px;
        box-shadow: var(--glass-shadow);
    }
    .premium-table { width: 100%; border-collapse: separate; border-spacing: 0 8px; }
    .premium-table th { color: #64748b; font-weight: 700; padding: 12px 15px; border: none; background: transparent; }
    .premium-table tbody tr { background: white; box-shadow: 0 2px 8px rgba(0,0,0,0.02); transition: transform 0.2s; }
    .premium-table tbody tr:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.06); }
    .premium-table td { padding: 15px; border: none; vertical-align: middle; }
    .premium-table td:first-child { border-top-right-radius: 10px; border-bottom-right-radius: 10px; }
    .premium-table td:last-child { border-top-left-radius: 10px; border-bottom-left-radius: 10px; }

    /* Badges */
    .status-badge { padding: 6px 12px; border-radius: 8px; font-weight: 700; font-size: 0.85rem; }
    .btn-export { border-radius: 10px; padding: 10px 20px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; transition: 0.3s; }
</style>

<div class="container-fluid">
    <!-- Header -->
    <div class="dashboard-header">
        <div>
            <h3 class="mb-1 text-white" style="font-weight: 800;"><i class="fa fa-box mr-2"></i> گزارش موجودی ({{ $warehouse->name }})</h3>
            <p class="mb-0 text-white-50">رهگیری و تحلیل تمام موجودی داخل این گدام</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('accounting.warehouses.stock_report_excel', $warehouse->id) }}" class="btn btn-success btn-export"><i class="fa fa-file-excel-o"></i> خروجی اکسل (Excel)</a>
            <a href="{{ route('accounting.warehouses.stock_report_pdf', $warehouse->id) }}" target="_blank" class="btn btn-danger btn-export"><i class="fa fa-file-pdf-o"></i> خروجی پی‌دی‌اف (PDF)</a>
        </div>
    </div>

    <!-- Table Container -->
    <div class="table-container">
        <div class="table-responsive">
            <table class="premium-table text-right">
                @if($warehouse->subtype === 'carpet')
                <thead>
                    <tr>
                        <th>ردیف</th>
                        <th>شماره پارچه</th>
                        <th>نماینده فروشنده</th>
                        <th>نوعیت</th>
                        <th>کوالیتی</th>
                        <th>طول</th>
                        <th>عرض</th>
                        <th>مساحت (M2)</th>
                        <th>قیمت فی متر / کل</th>
                        <th>حالت فعلی</th>
                    </tr>
                </thead>
                <tbody>
                    @php $sum_area = 0; $sum_total = 0; @endphp
                    @forelse($items as $index => $item)
                    @php $sum_area += $item->area; $sum_total += $item->total_price; @endphp
                    <tr>
                        <td class="font-weight-bold">{{ $index + 1 }}</td>
                        <td class="font-weight-bold text-dark">{{ $item->carpet_no }}</td>
                        <td>{{ $item->agent->user->name ?? 'نامشخص' }} {{ $item->agent->user->last_name ?? '' }}</td>
                        <td><strong style="color: var(--accent-blue);">{{ $item->type->carpet_type ?? '-' }}</strong></td>
                        <td>{{ $item->quality->quality ?? '-' }}</td>
                        <td dir="ltr" class="text-right">{{ $item->height }}</td>
                        <td dir="ltr" class="text-right">{{ $item->width }}</td>
                        <td class="font-weight-bold text-success" dir="ltr">{{ number_format($item->area, 2) }}</td>
                        <td>
                            <div>{{ number_format($item->price) }} $</div>
                            <div class="text-danger" style="font-weight: 700;">{{ number_format($item->total_price) }} $</div>
                        </td>
                        <td>
                            <span class="badge badge-light border shadow-sm">{{ $statuses[$item->status] ?? 'نامشخص' }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center text-muted py-4">هیچ قالینی در این گدام یافت نشد.</td>
                    </tr>
                    @endforelse
                    @if($items->count() > 0)
                    <tr>
                        <td colspan="7" class="font-weight-bold text-center">مجموع:</td>
                        <td class="font-weight-bold text-success" dir="ltr">{{ number_format($sum_area, 2) }}</td>
                        <td class="font-weight-bold text-danger">{{ number_format($sum_total, 2) }} $</td>
                        <td></td>
                    </tr>
                    @endif
                </tbody>
                @else
                <thead>
                    <tr>
                        <th>ردیف</th>
                        <th>کتگوری</th>
                        <th>نوعیت</th>
                        <th>موجودی در دسترس (KG)</th>
                        <th>آخرین فیت قیمت ($)</th>
                        <th>ارزش تخمینی ($)</th>
                    </tr>
                </thead>
                <tbody>
                    @php $sum_qty = 0; $sum_val = 0; @endphp
                    @forelse($items as $index => $item)
                    @php 
                        $val = $item->available_qty * $item->current_cost;
                        $sum_qty += $item->available_qty;
                        $sum_val += $val;
                    @endphp
                    <tr>
                        <td class="font-weight-bold">{{ $index + 1 }}</td>
                        <td class="font-weight-bold" style="color: var(--accent-blue);">{{ $item->material_category ?? '-' }}</td>
                        <td class="font-weight-bold text-dark">{{ $item->material_type ?? '-' }}</td>
                        <td class="font-weight-bold text-success" dir="ltr">{{ number_format($item->available_qty, 2) }}</td>
                        <td class="text-danger" dir="ltr">{{ number_format($item->current_cost, 2) }}</td>
                        <td class="font-weight-bold" dir="ltr">{{ number_format($val, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">هیچ موادی در این گدام یافت نشد.</td>
                    </tr>
                    @endforelse
                    @if(count($items) > 0)
                    <tr>
                        <td colspan="3" class="font-weight-bold text-center">مجموع:</td>
                        <td class="font-weight-bold text-success" dir="ltr">{{ number_format($sum_qty, 2) }}</td>
                        <td></td>
                        <td class="font-weight-bold text-danger" dir="ltr">{{ number_format($sum_val, 2) }} $</td>
                    </tr>
                    @endif
                </tbody>
                @endif
            </table>
        </div>
    </div>
</div>
@endsection
