@extends('dsh.master')

@section('content')
<div class="container-fluid">
    <br>
    
    <!-- Premium Header & Search Section -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                <div class="card-body p-4">
                    <div class="row align-items-center mb-4">
                        <div class="col-md-6">
                            <h3 class="font-weight-bold mb-1">روزنامچه کل (General Ledger)</h3>
                            <p class="text-muted mb-0">مشاهده و مدیریت تمامی تراکنش‌های مالی ثبت شده در سیستم</p>
                        </div>
                        <div class="col-md-6 text-right">
                            <a href="{{ route('accounting.journals.create') }}" class="btn btn-primary shadow-sm px-4" style="border-radius: 10px;">
                                <i class="feather icon-plus mr-2"></i>ثبت سند جدید (Journal Entry)
                            </a>
                        </div>
                    </div>
                    
                    <hr class="opacity-10 mb-4">

                    <!-- Advanced Filter Bar -->
                    <form action="{{ route('accounting.journals.index') }}" method="GET">
                        <div class="row">
                            <div class="col-md-3 mb-2">
                                <label class="small font-weight-bold text-muted">جستجو (سند/تفصیلات):</label>
                                <input type="text" name="search" value="{{ request('search') }}" class="form-control bg-light border-0 rounded-pill" placeholder="نمبر سند...">
                            </div>
                            <div class="col-md-2 mb-2">
                                <label class="small font-weight-bold text-muted">از تاریخ:</label>
                                <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control bg-light border-0 rounded-pill">
                            </div>
                            <div class="col-md-2 mb-2">
                                <label class="small font-weight-bold text-muted">الی تاریخ:</label>
                                <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control bg-light border-0 rounded-pill">
                            </div>
                            <div class="col-md-2 mb-2">
                                <label class="small font-weight-bold text-muted">نوعیت:</label>
                                <select name="journal_type" class="form-control bg-light border-0 rounded-pill">
                                    <option value="">همه</option>
                                    <option value="sales" {{ request('journal_type') == 'sales' ? 'selected' : '' }}>فروشات</option>
                                    <option value="purchase" {{ request('journal_type') == 'purchase' ? 'selected' : '' }}>خریداری</option>
                                    <option value="journal" {{ request('journal_type') == 'journal' ? 'selected' : '' }}>روزنامچه</option>
                                </select>
                            </div>
                            <div class="col-md-2 mb-2">
                                <label class="small font-weight-bold text-muted">حالت (Status):</label>
                                <select name="status" class="form-control bg-light border-0 rounded-pill">
                                    <option value="">همه</option>
                                    <option value="posted" {{ request('status') == 'posted' ? 'selected' : '' }}>تایید شده</option>
                                    <option value="reversed" {{ request('status') == 'reversed' ? 'selected' : '' }}>ابطال شده</option>
                                </select>
                            </div>
                            <div class="col-md-1 mb-2 text-right">
                                <label class="small d-block">&nbsp;</label>
                                <button type="submit" class="btn btn-dark btn-block rounded-pill shadow-sm">
                                    <i class="feather icon-filter"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Transactions Table -->
    <div class="card border-0 shadow-sm" style="border-radius: 15px;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="journal-table-main">
                    <thead class="bg-light text-muted small uppercase">
                        <tr>
                            <th class="border-0 py-3 px-4">تاریخ</th>
                            <th class="border-0 py-3">نمبر سند (Ref)</th>
                            <th class="border-0 py-3">تفصیلات (Description)</th>
                            <th class="border-0 py-3">نوعیت</th>
                            <th class="border-0 py-3 text-center">حالت</th>
                            <th class="border-0 py-3 text-right">مجموع دیبت</th>
                            <th class="border-0 py-3 text-right px-4">عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transactions as $tx)
                        <tr>
                            <td class="py-3 px-4">{{ $tx->date }}</td>
                            <td class="py-3 font-weight-bold text-primary">{{ $tx->reference }}</td>
                            <td class="py-3">
                                <span class="text-dark d-block" style="max-width: 300px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    {{ $tx->description }}
                                </span>
                            </td>
                            <td class="py-3">
                                <span class="badge badge-light-info text-capitalize">{{ $tx->journal_type }}</span>
                            </td>
                            <td class="py-3 text-center">
                                @if($tx->status == 'posted')
                                    <span class="badge badge-success rounded-pill px-3">تایید شده</span>
                                @else
                                    <span class="badge badge-danger rounded-pill px-3">ابطال شده</span>
                                @endif
                            </td>
                            <td class="py-3 text-right font-weight-bold">
                                ${{ number_format($tx->entries->sum('debit'), 2) }}
                            </td>
                            <td class="py-3 text-right px-4">
                                <a href="{{ route('accounting.journals.show', $tx->id) }}" class="btn btn-sm btn-icon btn-outline-primary rounded-circle mr-1" title="مشاهده">
                                    <i class="feather icon-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-3">
                {{ $transactions->appends(request()->input())->links() }}
            </div>
        </div>
    </div>
</div>

<style>
    .badge-light-info { background: #e0f7fa; color: #00838f; border-radius: 5px; padding: 5px 10px; }
    .btn-icon { width: 35px; height: 35px; padding: 0; display: inline-flex; align-items: center; justify-content: center; }
    tr:hover { background-color: #f8f9fa; }
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
        $('#journal-table-main').DataTable({
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: '<i class="feather icon-file-text"></i> EXCEL',
                    className: 'btn btn-success rounded-pill px-4 shadow-sm',
                    title: 'QASIMI BROTHERS CARPET CO. - گزارش روزنامچه',
                    messageTop: 'گزارش تمامی تراکنش‌های مالی ثبت شده',
                    exportOptions: { columns: ':not(:last-child)' },
                    customize: function(xlsx) {
                        var sheet = xlsx.xl.worksheets['sheet1.xml'];
                        $('row c[r^="A1"]', sheet).attr('s', '51'); // Bold header
                    }
                },
                {
                    extend: 'pdfHtml5',
                    text: '<i class="feather icon-file"></i> PDF',
                    className: 'btn btn-danger rounded-pill px-4 shadow-sm',
                    title: 'QASIMI BROTHERS CARPET CO. - گزارش روزنامچه',
                    exportOptions: { columns: ':not(:last-child)' },
                    customize: function(doc) {
                        doc.defaultStyle.font = 'Arial';
                        doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                        doc.styles.tableHeader.fillColor = '#333';
                        doc.styles.tableHeader.color = 'white';
                        doc.styles.tableHeader.alignment = 'center';
                    }
                },
                {
                    extend: 'print',
                    text: '<i class="feather icon-printer"></i> PRINT',
                    className: 'btn btn-dark rounded-pill px-4 shadow-sm',
                    title: 'QASIMI BROTHERS CARPET CO. - گزارش روزنامچه',
                    exportOptions: { columns: ':not(:last-child)' }
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
<style>
    .dt-buttons { margin-bottom: 15px; margin-left: 20px; }
    .dataTables_filter { margin-right: 20px; }
</style>
@endsection
