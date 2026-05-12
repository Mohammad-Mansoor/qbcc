@extends('dsh.master')
@section('title', 'بیلان آزمایشی - Trial Balance')
@section('content')
<div class="card mt-3">
    <div class="card-header bg-white">
        <div class="row">
            <div class="col-md-6">
                <h4><i class="fa fa-balance-scale"></i> بیلان آزمایشی (Trial Balance)</h4>
            </div>
            <div class="col-md-6 text-right hideOnPrint">
                <form action="{{ route('accounting.reports.trial-balance') }}" method="GET" class="form-inline justify-content-end">
                    <label class="mr-2">As Of:</label>
                    <input type="date" name="as_of" value="{{ $asOfDate }}" class="form-control form-control-sm mr-2">
                    <button type="submit" class="btn btn-sm btn-primary">بروزرسانی</button>
                    <button type="button" onclick="window.print()" class="btn btn-sm btn-info ml-2"><i class="fa fa-print"></i></button>
                </form>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th>کد حساب</th>
                        <th>نام حساب</th>
                        <th class="text-right">مجموع بدهکار (Debit)</th>
                        <th class="text-right">مجموع بستانکار (Credit)</th>
                        <th class="text-right">مانده (Balance)</th>
                    </tr>
                </thead>
                <tbody>
                    @php($totalDebit = 0)
                    @php($totalCredit = 0)
                    @foreach($accounts as $account)
                        <tr>
                            <td>{{ $account->account_code }}</td>
                            <td>{{ $account->account_name }}</td>
                            <td class="text-right">{{ number_format($account->total_debit, 2) }} $</td>
                            <td class="text-right">{{ number_format($account->total_credit, 2) }} $</td>
                            <td class="text-right font-weight-bold {{ $account->display_balance < 0 ? 'text-danger' : 'text-success' }}">
                                {{ number_format(abs($account->display_balance), 2) }} $
                                <small>{{ $account->display_balance >= 0 ? ($account->account_type == 'asset' || $account->account_type == 'expense' ? 'DR' : 'CR') : ($account->account_type == 'asset' || $account->account_type == 'expense' ? 'CR' : 'DR') }}</small>
                            </td>
                        </tr>
                        @php($totalDebit += $account->total_debit)
                        @php($totalCredit += $account->total_credit)
                    @endforeach
                </tbody>
                <tfoot class="bg-light font-weight-bold">
                    <tr>
                        <td colspan="2" class="text-center">مجموع کل (TOTAL)</td>
                        <td class="text-right">{{ number_format($totalDebit, 2) }} $</td>
                        <td class="text-right">{{ number_format($totalCredit, 2) }} $</td>
                        <td class="text-right">
                            @if(abs($totalDebit - $totalCredit) < 0.01)
                                <span class="badge badge-success"><i class="fa fa-check-circle"></i> تراز است</span>
                            @else
                                <span class="badge badge-danger"><i class="fa fa-times-circle"></i> تراز نیست! ({{ number_format(abs($totalDebit - $totalCredit), 2) }})</span>
                            @endif
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
