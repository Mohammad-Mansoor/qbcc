@extends('dsh.master')
@section('title' , 'جزییات معاشات')
@section('content')
<div class="sparkline12-list">
    <div class="sparkline12-hd">
        <div class="main-sparkline12-hd">
            <h1>جزییات معاشات: {{ $run->month_year }}</h1>
        </div>
    </div>
    <div class="sparkline12-graph">
        <div class="row mb-4">
            <div class="col-lg-3">
                <div class="card bg-light">
                    <div class="card-body">
                        <small class="text-muted">مجموع مبلغ</small>
                        <h3>${{ number_format($run->total_amount, 2) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="card bg-light">
                    <div class="card-body">
                        <small class="text-muted">تاریخ اجرا</small>
                        <h3>{{ $run->run_date }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>نام کارمند</th>
                        <th>معاش اصلی</th>
                        <th>بونس</th>
                        <th>تخفیفات</th>
                        <th>خالص پرداختی</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $item)
                    <tr>
                        <td>{{ $item->name }}</td>
                        <td>{{ number_format($item->base_salary) }}</td>
                        <td class="text-success">+{{ number_format($item->bonus) }}</td>
                        <td class="text-danger">-{{ number_format($item->deductions) }}</td>
                        <td class="font-weight-bold">{{ number_format($item->net_salary) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-2">
            {{ $items->links() }}
        </div>
        <div class="mt-4 text-right">
            <button onclick="window.print()" class="btn btn-default"><i class="fa fa-print"></i> چاپ راپور</button>
        </div>
    </div>
</div>
@endsection
