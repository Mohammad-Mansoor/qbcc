@extends('dsh.master')

@section('content')
<div class="container-fluid">
    <br>
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                <div class="card-body p-4">
                    <div class="row align-items-center mb-4">
                        <div class="col-md-6">
                            <h3 class="font-weight-bold mb-1">تراز آزمایشی (Trial Balance <span class="text-primary">USD</span>)</h3>
                            <p class="text-muted mb-0">گزارش مجموع بدهکار و بستانکار تمامی حساب‌ها (معادل دالر) در بازه زمانی مشخص</p>
                        </div>
                        <div class="col-md-6">
                            <form action="{{ route('accounting.reports.trial_balance') }}" method="GET">
                                <div class="row no-gutters align-items-end justify-content-end">
                                    <div class="col-md-4 px-1">
                                        <label class="small font-weight-bold text-muted mb-1">از تاریخ:</label>
                                        <input type="date" name="start_date" value="{{ $startDate }}" class="form-control bg-light border-0 rounded-pill">
                                    </div>
                                    <div class="col-md-4 px-1">
                                        <label class="small font-weight-bold text-muted mb-1">الی تاریخ:</label>
                                        <input type="date" name="end_date" value="{{ $endDate }}" class="form-control bg-light border-0 rounded-pill">
                                    </div>
                                    <div class="col-md-2 px-1">
                                        <button type="submit" class="btn btn-primary btn-block rounded-pill shadow-sm">تایید</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <hr class="opacity-10">
                    
                    <div class="table-responsive mt-4">
                        <table class="table table-hover border" id="report-table">
                            <thead class="bg-dark text-white">
                                <tr>
                                    <th class="py-3 px-4">کد حساب</th>
                                    <th class="py-3">نام حساب (Account Name)</th>
                                    <th class="py-3 text-right">دیبت (Debit USD)</th>
                                    <th class="py-3 text-right">کریدت (Credit USD)</th>
                                    <th class="py-3 text-right px-4">بیلانس (Balance USD)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $sumDebit = 0; $sumCredit = 0; @endphp
                                @foreach($report as $row)
                                <tr>
                                    <td class="py-3 px-4 font-weight-bold">{{ $row->account_code }}</td>
                                    <td class="py-3">{{ $row->account_name }}</td>
                                    <td class="py-3 text-right text-primary font-weight-bold">{{ number_format($row->total_debit, 2) }}</td>
                                    <td class="py-3 text-right text-danger font-weight-bold">{{ number_format($row->total_credit, 2) }}</td>
                                    <td class="py-3 text-right px-4 font-weight-bold {{ $row->balance >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ number_format(abs($row->balance), 2) }} {{ $row->balance >= 0 ? '(Dr)' : '(Cr)' }}
                                    </td>
                                </tr>
                                @php $sumDebit += $row->total_debit; $sumCredit += $row->total_credit; @endphp
                                @endforeach
                            </tbody>
                            <tfoot class="bg-light font-weight-bold">
                                <tr style="font-size: 1.1rem;">
                                    <td colspan="2" class="py-4 px-4 text-right">مجموع (Grand Total USD):</td>
                                    <td class="py-4 text-right text-primary border-top-double">{{ number_format($sumDebit, 2) }} $</td>
                                    <td class="py-4 text-right text-danger border-top-double">{{ number_format($sumCredit, 2) }} $</td>
                                    <td class="py-4 text-right px-4 border-top-double">
                                        {{ number_format(abs($sumDebit - $sumCredit), 2) }} $
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .border-top-double { border-top: 3px double #333 !important; }
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
        $('#report-table').DataTable({
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: '<i class="feather icon-file-text"></i> EXCEL',
                    className: 'btn btn-success rounded-pill px-4 shadow-sm',
                    title: 'QASIMI BROTHERS CARPET CO. - تراز آزمایشی',
                    messageTop: 'گزارش بیلانس آزمایشی تمامی حسابات',
                    footer: true, // Includes the total row
                    customize: function(xlsx) {
                        var sheet = xlsx.xl.worksheets['sheet1.xml'];
                        $('row c[r^="A1"]', sheet).attr('s', '51');
                    }
                },
                {
                    extend: 'pdfHtml5',
                    text: '<i class="feather icon-file"></i> PDF',
                    className: 'btn btn-danger rounded-pill px-4 shadow-sm',
                    title: 'QASIMI BROTHERS CARPET CO. - تراز آزمایشی',
                    footer: true,
                    customize: function(doc) {
                        doc.defaultStyle.font = 'Arial';
                        doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                        doc.styles.tableHeader.fillColor = '#1a237e'; // Premium Blue
                        doc.styles.tableHeader.color = 'white';
                        doc.styles.tableHeader.alignment = 'center';
                        doc.styles.tableFooter.fillColor = '#f5f5f5';
                    }
                },
                {
                    extend: 'print',
                    text: '<i class="feather icon-printer"></i> PRINT',
                    className: 'btn btn-dark rounded-pill px-4 shadow-sm',
                    title: 'QASIMI BROTHERS CARPET CO. - تراز آزمایشی',
                    footer: true
                }
            ],
            paging: false,
            searching: true,
            info: false,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.12.1/i18n/fa.json'
            }
        });
    });
</script>
@endsection
