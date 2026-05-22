@extends('dsh.master')

@section('content')
<div class="container-fluid no-print-padding">
    <br class="no-print">
    
    <div class="row mb-4 no-print">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-4">
                            <h3 class="font-weight-bold mb-1">صورت سود و ضرر (<span class="text-primary">{{ $currencyCode }}</span>)</h3>
                            <p class="text-muted mb-0">Profit & Loss Statement ({{ $currencyCode }})</p>
                        </div>
                        <div class="col-md-8">
                            <form action="{{ route('accounting.reports.profit_loss') }}" method="GET">
                                <div class="row no-gutters align-items-end justify-content-end">
                                    <div class="col-md-3 px-1">
                                        <label class="small font-weight-bold text-muted mb-1">ارز گزارش‌دهی:</label>
                                        <select name="currency" class="form-control bg-light border-0 rounded-pill px-3" style="height: 38px;">
                                            @foreach($currencies as $c)
                                                <option value="{{ $c->code }}" {{ $currencyCode == $c->code ? 'selected' : '' }}>
                                                    {{ $c->name }} ({{ $c->code }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3 px-1">
                                        <label class="small font-weight-bold text-muted mb-1">از تاریخ:</label>
                                        <input type="date" name="start_date" value="{{ $startDate }}" class="form-control bg-light border-0 rounded-pill">
                                    </div>
                                    <div class="col-md-3 px-1">
                                        <label class="small font-weight-bold text-muted mb-1">الی تاریخ:</label>
                                        <input type="date" name="end_date" value="{{ $endDate }}" class="form-control bg-light border-0 rounded-pill">
                                    </div>
                                    <div class="col-md-3 px-1">
                                        <button type="submit" class="btn btn-primary btn-block rounded-pill shadow-sm" style="height: 38px;">تایید</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- P&L Statement Card -->
    <div class="row justify-content-center">
        <div class="col-lg-9 col-md-12">
            <div class="card border-0 shadow-lg printable-document" style="border-radius: 20px; overflow: hidden;">
                
                <!-- Corporate Header (Print Only) -->
                <div class="d-none d-print-block text-center p-5 border-bottom">
                    <h1 class="font-weight-bold text-dark mb-1" style="letter-spacing: 2px;">QASIMI BROTHERS CARPET CO.</h1>
                    <h3 class="text-muted mb-2">صورت سود و ضرر (Profit & Loss - {{ $currencyCode }})</h3>
                    <p class="mb-0 font-weight-bold text-dark">دوره مالی: {{ $startDate }} الی {{ $endDate }}</p>
                </div>

                <div class="card-body p-5">
                    <!-- Web-Only Top Action Bar -->
                    <div class="d-flex justify-content-between align-items-center mb-5 no-print">
                        <div class="text-muted">
                            <i class="feather icon-calendar mr-1"></i> بازه زمانی: <strong>{{ $startDate }}</strong> الی <strong>{{ $endDate }}</strong>
                        </div>
                        <div id="export-buttons"></div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover border-bottom" id="pl-table">
                            <thead>
                                <tr class="bg-dark text-white d-none"> <!-- Hidden on web, used by DataTables -->
                                    <th>شرح (Description)</th>
                                    <th class="text-right">مبلغ (Amount {{ $currencyCode }})</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- REVENUE SECTION -->
                                <tr class="bg-light">
                                    <td colspan="2" class="py-3 px-4 font-weight-bold text-primary" style="font-size: 1.1rem;">
                                        <i class="feather icon-trending-up mr-2"></i> عواید عملیاتی (Operating Revenue)
                                    </td>
                                </tr>
                                @foreach($revenue as $row)
                                <tr>
                                    <td class="py-3 px-5 text-dark">{{ $row->account_name }}</td>
                                    <td class="py-3 text-right font-weight-bold px-4">{{ number_format($row->balance * $rate, 2) }}</td>
                                </tr>
                                @endforeach
                                <tr class="font-weight-bold" style="background: #e3f2fd;">
                                    <td class="py-3 px-4">مجموع عواید (Total Revenue)</td>
                                    <td class="py-3 text-right px-4 text-primary" style="font-size: 1.2rem;">
                                        {{ number_format($revenue->sum('balance') * $rate, 2) }} {{ $currencyCode }}
                                    </td>
                                </tr>

                                <tr><td colspan="2" class="py-4 border-0"></td></tr>

                                <!-- EXPENSES SECTION -->
                                <tr class="bg-light">
                                    <td colspan="2" class="py-3 px-4 font-weight-bold text-danger" style="font-size: 1.1rem;">
                                        <i class="feather icon-trending-down mr-2"></i> هزینه‌های عملیاتی (Operating Expenses)
                                    </td>
                                </tr>
                                @foreach($expenses as $row)
                                <tr>
                                    <td class="py-3 px-5 text-dark">{{ $row->account_name }}</td>
                                    <td class="py-3 text-right font-weight-bold px-4 text-danger">({{ number_format(abs($row->balance) * $rate, 2) }})</td>
                                </tr>
                                @endforeach
                                <tr class="font-weight-bold" style="background: #ffebee;">
                                    <td class="py-3 px-4 text-danger">مجموع هزینه‌ها (Total Expenses)</td>
                                    <td class="py-3 text-right px-4 text-danger" style="font-size: 1.2rem;">
                                        ({{ number_format(abs($expenses->sum('balance')) * $rate, 2) }}) {{ $currencyCode }}
                                    </td>
                                </tr>

                                <tr><td colspan="2" class="py-5 border-0"></td></tr>
                            </tbody>
                            <tfoot>
                                <tr class="net-profit-row" style="background: {{ $netProfit >= 0 ? '#e8f5e9' : '#fce4ec' }};">
                                    <td class="py-4 px-4">
                                        <h4 class="font-weight-bold mb-0 {{ $netProfit >= 0 ? 'text-success' : 'text-danger' }}">
                                            {{ $netProfit >= 0 ? 'سود خالص (Net Profit)' : 'ضرر خالص (Net Loss)' }}
                                        </h4>
                                    </td>
                                    <td class="py-4 text-right px-4">
                                        <h3 class="font-weight-bold mb-0 {{ $netProfit >= 0 ? 'text-success' : 'text-danger' }}">
                                            {{ number_format($netProfit * $rate, 2) }} {{ $currencyCode }}
                                        </h3>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Print Signatures -->
                    <div class="mt-5 pt-5 d-none d-print-block">
                        <div class="row text-center">
                            <div class="col-4"><div class="border-top pt-2 font-weight-bold">ترتیب کننده</div></div>
                            <div class="col-4"><div class="border-top pt-2 font-weight-bold">مدیر مالی</div></div>
                            <div class="col-4"><div class="border-top pt-2 font-weight-bold">تایید کننده</div></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        body { background: white !important; }
        .no-print, .pcoded-navbar, .pcoded-header { display: none !important; }
        .pcoded-main-container { margin-left: 0 !important; margin-top: 0 !important; }
        .printable-document { box-shadow: none !important; border: 1px solid #eee !important; width: 100% !important; }
        .no-print-padding { padding: 0 !important; }
        .net-profit-row { -webkit-print-color-adjust: exact; }
    }
    .table td { border-color: #f1f1f1; vertical-align: middle; }
    .border-top-double { border-top: 4px double #333 !important; }
</style>
@endsection

@section('footer-plugins')
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
<script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.print.min.js"></script>

<script>
    $(document).ready(function() {
        var table = $('#pl-table').DataTable({
            dom: 'B',
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: '<i class="feather icon-file-text"></i> EXCEL',
                    className: 'btn btn-success rounded-pill px-4 mr-2',
                    title: 'QASIMI BROTHERS - Profit and Loss Statement'
                },
                {
                    extend: 'pdfHtml5',
                    text: '<i class="feather icon-file"></i> PDF',
                    className: 'btn btn-danger rounded-pill px-4 mr-2',
                    title: 'QASIMI BROTHERS - Profit and Loss Statement'
                },
                {
                    text: '<i class="feather icon-printer"></i> PRINT',
                    className: 'btn btn-dark rounded-pill px-4',
                    action: function() { window.print(); }
                }
            ]
        });
        table.buttons().container().appendTo('#export-buttons');
    });
</script>
@endsection
