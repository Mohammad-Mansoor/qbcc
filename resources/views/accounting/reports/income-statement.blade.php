@extends('dsh.master')
@section('title', 'صورت سود و زیان - Income Statement')
@section('content')
<div class="card mt-3">
    <div class="card-header bg-white">
        <div class="row">
            <div class="col-md-6">
                <h4><i class="fa fa-line-chart"></i> صورت سود و زیان (P&L Statement)</h4>
                <small class="text-muted">دوره: {{ $startDate }} الی {{ $endDate }}</small>
            </div>
            <div class="col-md-6 text-right hideOnPrint">
                <form action="{{ route('accounting.reports.income-statement') }}" method="GET" class="form-inline justify-content-end">
                    <input type="date" name="start_date" value="{{ $startDate }}" class="form-control form-control-sm mr-2">
                    <input type="date" name="end_date" value="{{ $endDate }}" class="form-control form-control-sm mr-2">
                    <button type="submit" class="btn btn-sm btn-primary">نمایش</button>
                    <button type="button" onclick="window.print()" class="btn btn-sm btn-info ml-2"><i class="fa fa-print"></i></button>
                </form>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="row justify-content-center">
            <div class="col-md-10">
                
                <!-- Revenue Section -->
                <h5 class="text-success border-bottom pb-2">درآمدها (REVENUE)</h5>
                <table class="table table-sm table-borderless">
                    @foreach($revenue as $acc)
                    <tr>
                        <td>{{ $acc->account_name }}</td>
                        <td class="text-right">{{ number_format($acc->balance, 2) }} $</td>
                    </tr>
                    @endforeach
                    <tr class="font-weight-bold border-top">
                        <td>مجموع درآمد (Total Revenue)</td>
                        <td class="text-right">{{ number_format($totalRevenue, 2) }} $</td>
                    </tr>
                </table>

                <br>

                <!-- Expenses Section -->
                <h5 class="text-danger border-bottom pb-2">هزینه‌ها (EXPENSES)</h5>
                <table class="table table-sm table-borderless">
                    @foreach($expenses as $acc)
                    <tr>
                        <td>{{ $acc->account_name }}</td>
                        <td class="text-right">({{ number_format($acc->balance, 2) }}) $</td>
                    </tr>
                    @endforeach
                    <tr class="font-weight-bold border-top">
                        <td>مجموع هزینه‌ها (Total Expenses)</td>
                        <td class="text-right">({{ number_format($totalExpenses, 2) }}) $</td>
                    </tr>
                </table>

                <br>

                <!-- Net Income Section -->
                <div class="card bg-light">
                    <div class="card-body py-3">
                        <div class="row font-weight-bold">
                            <div class="col-md-6 h4 mb-0">سود/زیان خالص (NET INCOME)</div>
                            <div class="col-md-6 h4 mb-0 text-right {{ $netIncome >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ number_format($netIncome, 2) }} $
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
