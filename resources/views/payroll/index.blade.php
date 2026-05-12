@extends('dsh.master')
@section('title' , 'لیست معاشات')
@section('content')
<div class="sparkline12-list">
    <div class="sparkline12-hd">
        <div class="main-sparkline12-hd d-flex justify-content-between align-items-center">
            <h1>لیست پرداخت‌های معاشات</h1>
            <a href="{{ route('payroll.create') }}" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> اجرای معاش جدید</a>
        </div>
    </div>
    <div class="sparkline12-graph">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ماه و سال</th>
                        <th>تاریخ اجرا</th>
                        <th>مجموع مبلغ ($)</th>
                        <th>حالت</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($runs as $run)
                    <tr>
                        <td>{{ $run->month_year }}</td>
                        <td>{{ $run->run_date }}</td>
                        <td>{{ number_format($run->total_amount, 2) }}</td>
                        <td><span class="badge badge-success">ثبت شده</span></td>
                        <td>
                            <a href="{{ route('payroll.show', $run->id) }}" class="btn btn-info btn-xs"><i class="fa fa-eye"></i> جزییات</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $runs->links() }}
    </div>
</div>
@endsection
