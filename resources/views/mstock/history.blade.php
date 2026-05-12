@extends('dsh.master')
@section('title' , 'تاریخچه حرکات گدام')
@section('content')
    <div class="card">
        <div class="card-header">
            <h5>تاریخچه حرکات: {{ $category->material_category }} - {{ $typeModel->material_type }}</h5>
            <a href="{{ route('material-stock.index') }}" class="btn btn-secondary btn-sm float-left">بازگشت</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th>تاریخ</th>
                            <th>گدام</th>
                            <th>نوعیت حرکت</th>
                            <th>ورودی (IN)</th>
                            <th>خروجی (OUT)</th>
                            <th>قیمت فی کیلو</th>
                            <th>مجموع ارزش</th>
                            <th>توضیحات / نمبر فاکتور</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $balance = 0; @endphp
                        @foreach($movements as $m)
                            @php 
                                if($m->direction == 'IN') $balance += $m->quantity;
                                else $balance -= $m->quantity;
                            @endphp
                            <tr>
                                <td>{{ $m->created_at }}</td>
                                <td>{{ $m->warehouse_name ?? 'گدام مرکزی' }}</td>
                                <td>
                                    @if($m->type == 'PURCHASE') <span class="badge badge-success">خریداری</span>
                                    @elseif($m->type == 'SALE') <span class="badge badge-info">فروش</span>
                                    @elseif($m->type == 'TRANSFER') <span class="badge badge-warning">انتقال</span>
                                    @else <span class="badge badge-secondary">{{ $m->type }}</span>
                                    @endif
                                </td>
                                <td class="text-success font-weight-bold">
                                    {{ $m->direction == 'IN' ? $m->quantity . ' kg' : '-' }}
                                </td>
                                <td class="text-danger font-weight-bold">
                                    {{ $m->direction == 'OUT' ? $m->quantity . ' kg' : '-' }}
                                </td>
                                <td>{{ number_format($m->unit_cost, 2) }} AFN</td>
                                <td>{{ number_format($m->total_cost, 2) }} AFN</td>
                                <td>
                                    <small class="text-muted">
                                        {{ $m->reference_type }} #{{ $m->reference_id }}
                                    </small>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-light">
                            <th colspan="3">موجودی فعلی:</th>
                            <th colspan="2" class="text-primary h5">{{ number_format($balance, 2) }} kg</th>
                            <th colspan="3"></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@endsection
