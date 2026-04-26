@extends('dsh.master')

@section('content')
<div class="container-fluid no-print-padding">
    <br class="no-print">
    
    <!-- Top Filter Section -->
    <div class="row mb-4 no-print">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h3 class="font-weight-bold mb-1">گزارش ترازنامه (Balance Sheet)</h3>
                            <p class="text-muted mb-0">نمای کلی از وضعیت دارایی‌ها، بدهی‌ها و سرمایه شرکت</p>
                        </div>
                        <div class="col-md-6">
                            <form action="{{ route('accounting.reports.balance_sheet') }}" method="GET">
                                <div class="row no-gutters align-items-end justify-content-end">
                                    <div class="col-md-5 px-1">
                                        <label class="small font-weight-bold text-muted mb-1">تا تاریخ (As of Date):</label>
                                        <input type="date" name="end_date" value="{{ $endDate }}" class="form-control bg-light border-0 rounded-pill">
                                    </div>
                                    <div class="col-md-3 px-1">
                                        <button type="submit" class="btn btn-primary btn-block rounded-pill shadow-sm">مشاهده گزارش</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Equation Dashboard -->
    @php 
        $totalAssets = $assets->sum('balance');
        $totalLiabEquity = $liabilities->sum('balance') + $equity->sum('balance') + $currentNetProfit;
        $isBalanced = abs($totalAssets - $totalLiabEquity) < 0.01;
    @endphp
    <div class="row mb-4 no-print">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm text-center p-4" style="border-radius: 15px; background: linear-gradient(45deg, #1a237e, #3949ab);">
                <span class="text-white opacity-75 small font-weight-bold">مجموع دارایی‌ها (Total Assets)</span>
                <h2 class="text-white font-weight-bold mt-2 mb-0">{{ number_format($totalAssets, 2) }}</h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm text-center p-4" style="border-radius: 15px; background: linear-gradient(45deg, #4a148c, #7b1fa2);">
                <span class="text-white opacity-75 small font-weight-bold">بدهی و سرمایه (Liabilities & Equity)</span>
                <h2 class="text-white font-weight-bold mt-2 mb-0">{{ number_format($totalLiabEquity, 2) }}</h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm text-center p-4" style="border-radius: 15px; background: #fff;">
                <span class="text-muted small font-weight-bold">وضعیت توازن (Balance Status)</span>
                <div class="mt-2">
                    @if($isBalanced)
                        <span class="badge badge-success px-4 py-2 rounded-pill shadow-sm"><i class="feather icon-check-circle mr-1"></i> ترازنامه متوازن است</span>
                    @else
                        <span class="badge badge-danger px-4 py-2 rounded-pill shadow-sm"><i class="feather icon-alert-triangle mr-1"></i> ترازنامه نامتوازن! ({{ number_format($totalAssets - $totalLiabEquity, 2) }})</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Main Balance Sheet Table -->
    <div class="row">
        <div class="col-md-12">
            <div class="card border-0 shadow-lg printable-document" style="border-radius: 20px; overflow: hidden;">
                
                <!-- Print Header -->
                <div class="d-none d-print-block text-center p-5 border-bottom bg-light">
                    <h1 class="font-weight-bold text-dark mb-1" style="letter-spacing: 2px;">QASIMI BROTHERS CARPET CO.</h1>
                    <h3 class="text-muted mb-2">تـرازنامـه (Balance Sheet)</h3>
                    <p class="mb-0 font-weight-bold text-dark">به تاریخ: {{ $endDate }}</p>
                </div>

                <div class="card-body p-5">
                    <!-- Web-Only Export Bar -->
                    <div class="d-flex justify-content-end mb-4 no-print">
                        <div id="export-buttons"></div>
                    </div>

                    <div class="row">
                        <!-- ASSETS SIDE -->
                        <div class="col-md-6 col-print-12">
                            <h5 class="font-weight-bold text-primary mb-4 border-bottom pb-2">
                                <i class="feather icon-briefcase mr-2"></i> دارایی‌ها (Assets)
                            </h5>
                            <table class="table table-hover border" id="assets-table">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="py-3 px-4">شرح حساب (Account Name)</th>
                                        <th class="py-3 text-right px-4">مبلغ (Amount)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($assets as $row)
                                    <tr>
                                        <td class="py-3 px-4">{{ $row->account_name }}</td>
                                        <td class="py-3 text-right px-4 font-weight-bold">{{ number_format($row->balance, 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-primary text-white">
                                    <tr class="font-weight-bold">
                                        <td class="py-3 px-4">مجموع دارایی‌ها</td>
                                        <td class="py-3 text-right px-4">{{ number_format($totalAssets, 2) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <!-- LIABILITIES & EQUITY SIDE -->
                        <div class="col-md-6 col-print-12">
                            <h5 class="font-weight-bold text-purple mb-4 border-bottom pb-2" style="color: #4a148c;">
                                <i class="feather icon-shield mr-2"></i> بدهی و سرمایه (Liabilities & Equity)
                            </h5>
                            <table class="table table-hover border" id="liab-table">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="py-3 px-4">شرح حساب (Account Name)</th>
                                        <th class="py-3 text-right px-4">مبلغ (Amount)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Liabilities Section -->
                                    <tr class="bg-lightest"><td colspan="2" class="py-2 px-4 small font-weight-bold text-muted">بدهی‌ها (Liabilities)</td></tr>
                                    @foreach($liabilities as $row)
                                    <tr>
                                        <td class="py-3 px-4">{{ $row->account_name }}</td>
                                        <td class="py-3 text-right px-4 font-weight-bold">{{ number_format($row->balance, 2) }}</td>
                                    </tr>
                                    @endforeach

                                    <!-- Equity Section -->
                                    <tr class="bg-lightest"><td colspan="2" class="py-2 px-4 small font-weight-bold text-muted">سرمایه و سود (Equity & Profit)</td></tr>
                                    @foreach($equity as $row)
                                    <tr>
                                        <td class="py-3 px-4">{{ $row->account_name }}</td>
                                        <td class="py-3 text-right px-4 font-weight-bold">{{ number_format($row->balance, 2) }}</td>
                                    </tr>
                                    @endforeach
                                    <tr>
                                        <td class="py-3 px-4">سود/ضرر دوره جاری (Current P&L)</td>
                                        <td class="py-3 text-right px-4 font-weight-bold {{ $currentNetProfit >= 0 ? 'text-success' : 'text-danger' }}">
                                            {{ number_format($currentNetProfit, 2) }}
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot class="bg-purple text-white" style="background: #4a148c;">
                                    <tr class="font-weight-bold">
                                        <td class="py-3 px-4">مجموع بدهی و سرمایه</td>
                                        <td class="py-3 text-right px-4">{{ number_format($totalLiabEquity, 2) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <!-- Print Footer -->
                    <div class="mt-5 pt-5 d-none d-print-block">
                        <div class="row text-center mt-5">
                            <div class="col-4"><div class="border-top pt-2 font-weight-bold">ترتیب کننده</div></div>
                            <div class="col-4"><div class="border-top pt-2 font-weight-bold">مدیر مالی</div></div>
                            <div class="col-4"><div class="border-top pt-2 font-weight-bold">تایید نهایی</div></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-lightest { background: #fafafa; }
    .text-purple { color: #4a148c; }
    @media print {
        body { background: white !important; }
        .no-print, .pcoded-navbar, .pcoded-header { display: none !important; }
        .pcoded-main-container { margin-left: 0 !important; margin-top: 0 !important; }
        .printable-document { box-shadow: none !important; border: 1px solid #ddd !important; width: 100% !important; }
        .no-print-padding { padding: 0 !important; }
        .col-print-12 { flex: 0 0 100%; max-width: 100%; margin-bottom: 30px; }
        .bg-primary, .bg-purple { -webkit-print-color-adjust: exact; color: white !important; }
    }
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
        var table = $('#assets-table').DataTable({
            dom: 'B',
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: '<i class="feather icon-file-text"></i> EXCEL',
                    className: 'btn btn-success rounded-pill px-4 mr-2',
                    title: 'QASIMI BROTHERS - Balance Sheet'
                },
                {
                    extend: 'pdfHtml5',
                    text: '<i class="feather icon-file"></i> PDF',
                    className: 'btn btn-danger rounded-pill px-4 mr-2',
                    title: 'QASIMI BROTHERS - Balance Sheet',
                    orientation: 'landscape'
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
