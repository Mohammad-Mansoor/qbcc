@extends('dsh.master')
@section('title', 'گزارش بدهی مشتریان - AR Aging')
@section('content')
<div class="card mt-3">
    <div class="card-header bg-white">
        <div class="row">
            <div class="col-md-6">
                <h4><i class="fa fa-hourglass-half"></i> گزارش تحلیل بدهی مشتریان (AR Aging)</h4>
                <small class="text-muted">تاریخ گزارش: {{ $asOfDate }}</small>
            </div>
            <div class="col-md-6 text-right hideOnPrint">
                <form action="{{ route('accounting.reports.ar-aging') }}" method="GET" class="form-inline justify-content-end">
                    <input type="date" name="as_of" value="{{ $asOfDate }}" class="form-control form-control-sm mr-2">
                    <button type="submit" class="btn btn-sm btn-primary">بروزرسانی</button>
                    <button type="button" onclick="window.print()" class="btn btn-sm btn-info ml-2"><i class="fa fa-print"></i></button>
                </form>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover table-sm">
                <thead class="thead-dark text-center">
                    <tr>
                        <th rowspan="2" class="align-middle">نام مشتری</th>
                        <th rowspan="2" class="align-middle">مجموع بدهی</th>
                        <th colspan="4" class="bg-secondary">زمان سپری شده از سررسید (Days Past Due)</th>
                    </tr>
                    <tr class="bg-light text-dark">
                        <th>0 - 30 روز</th>
                        <th>31 - 60 روز</th>
                        <th>61 - 90 روز</th>
                        <th>91+ روز</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($customers as $customer)
                    <tr class="text-right">
                        <td class="text-left font-weight-bold">{{ $customer->name }}</td>
                        <td class="font-weight-bold">{{ number_format($customer->total_outstanding, 2) }} $</td>
                        <td>{{ number_format($customer->aging['current'], 2) }} $</td>
                        <td>{{ number_format($customer->aging['31_60'], 2) }} $</td>
                        <td>{{ number_format($customer->aging['61_90'], 2) }} $</td>
                        <td class="text-danger">{{ number_format($customer->aging['90_plus'], 2) }} $</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-light font-weight-bold text-right">
                    <tr>
                        <td class="text-center">مجموع کل (TOTAL)</td>
                        <td>{{ number_format($customers->sum('total_outstanding'), 2) }} $</td>
                        <td>{{ number_format($customers->sum(fn($c) => $c->aging['current']), 2) }} $</td>
                        <td>{{ number_format($customers->sum(fn($c) => $c->aging['31_60']), 2) }} $</td>
                        <td>{{ number_format($customers->sum(fn($c) => $c->aging['61_90']), 2) }} $</td>
                        <td class="text-danger">{{ number_format($customers->sum(fn($c) => $c->aging['90_plus']), 2) }} $</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
