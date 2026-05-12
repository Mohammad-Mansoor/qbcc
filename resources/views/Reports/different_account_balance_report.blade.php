@extends('dsh.master')
@section('content')

    <style>

        @media print {

            html, body {
                margin-top: -30px !important;
            }


            .pcoded-navbar menu-light, .b-brand, .footer, #styleSelector, .page-block, .pcoded-navbar, .btn-primary, .card-header, #exportButton, #search_form {
                display: none;
            }


            tr, th, td, thead, tbody, table, tfoot {
                border: 1px solid black !important;
                color: black;
                width: 75% !important;
                font-size: 12px !important;
            }

            .table thead tr th, td {
                font-weight: 400 !important;
                font-size: 16px !important;
                color: black !important;
            }

            html, body {
                width: 21cm !important;
                height: 29.7cm !important;
                background: white !important;
            }

            .card-body, .pcoded-main-container {
                background: white !important;
                margin: 0px !important;
                padding: 0px !important;

                margin-top: -20px !important;
            }



            #print_system_name {
                margin-right: -17px !important;
            }



            #print_system_logo {
                margin-left: 100px !important;
                float: left; !important;
                margin-top: -60px !important;

            }


            #print_header_card_header {
                margin-bottom: 20px !important;
            }

        }

    </style>


    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card" id="print_header_card">
                <div class="card-header" id="print_header_card_header">
                    <h4> صورت حساب متفرقه جدید

                    </h4>
                    <div class="btn-group" id="exportButton" style="float: left; margin-bottom: 4px;">
                        <button onclick="window.print()" class="btn btn-primary btn-sm">چاپ <span
                                class="fas fa-print"></span></button>

                    </div>
                </div>
                <div class="card-body">

                    <form action="/dashboard/get_different_account_balance_report" method="POST" id="search_form">
                        <div class="row">
                            @csrf
                            <div class="form-group col-lg-3 col-md-3 col-sm-12 col-xs-12" style="margin-top: 34px;">
                                <?php $accounts = \Illuminate\Support\Facades\DB::table('new_different_accounts')->get(); ?>
                                <select name="account_id" id="customer_select_id" class="form-control">
                                    <option value="all" {{ ($account_id ?? 'all') == 'all' ? 'selected' : '' }}> همه</option>
                                    @foreach($accounts as $ac)
                                        <option value="{{$ac->id}}" {{ (isset($account) && $account->id == $ac->id) ? 'selected' : '' }}>{{$ac->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                                <label class="">تاریخ شروع</label>
                                <input type="date" class="form-control" name="from_date" value="{{ $from_date ?? '2000-01-01' }}"
                                       placeholder=" تاریخ شروع..." />
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                                <label class="">تاریخ ختم</label>
                                <input type="date" class="form-control" name="to_date" value="{{ $to_date ?? date('Y-m-d') }}"
                                       placeholder=" تاریخ ختم..." />
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12" style="margin-top: 30px">
                                <button class="btn btn-primary btn-sm btn-block"
                                        type="submit" style="    position: absolute;top: 22%; left: -3%;">جستجو
                                </button>
                            </div>

                        </div>
                    </form>

                    @if(isset($ledgerTransactions))
                        <!-- Account Snapshot -->
                        <div class="row mb-4 no-print">
                            <div class="col-md-4">
                                <div class="card border-0 shadow-sm" style="border-radius: 15px; background: #f8f9fa; border-right: 5px solid #4caf50;">
                                    <div class="card-body p-3">
                                        <span class="text-muted small d-block mb-1">بیلانس انتقالی (Opening Balance)</span>
                                        <h4 class="font-weight-bold mb-0 {{ $openingBalance >= 0 ? 'text-success' : 'text-danger' }}">
                                            ${{ number_format(abs($openingBalance), 2) }}
                                            <small>{{ $openingBalance >= 0 ? '(طلبکار)' : '(بدهکار)' }}</small>
                                        </h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border-0 shadow-sm" style="border-radius: 15px; background: #f8f9fa; border-right: 5px solid #2196f3;">
                                    <div class="card-body p-3">
                                        <span class="text-muted small d-block mb-1">وضعیت حساب</span>
                                        <h4 class="font-weight-bold mb-0 text-primary">فعال</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border-0 shadow-sm" style="border-radius: 15px; background: #4caf50; color: white;">
                                    <div class="card-body p-3 text-center">
                                        <span class="opacity-80 small d-block mb-1 text-white">نام حساب</span>
                                        <h5 class="font-weight-bold mb-0 text-white">{{ $account->name }}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <h5 class="mb-3 font-weight-bold">
                                    <i class="feather icon-list mr-2"></i> صورت حساب مالی تفصیلی (General Ledger)
                                    <span class="text-muted small font-weight-normal">| {{ $from_date }} الی {{ $to_date }}</span>
                                </h5>
                                
                                <div class="table-responsive shadow-sm" style="border-radius: 10px;">
                                    <table class="table table-xs table-hover bg-white mb-0">
                                        <thead class="bg-light">
                                            <tr>
                                                <th class="py-3">تاریخ</th>
                                                <th class="py-3">تفصیلات</th>
                                                <th class="py-3 text-center">بدهکار (Debit)</th>
                                                <th class="py-3 text-center">طلبکار (Credit)</th>
                                                <th class="py-3 text-right">بیلانس (Balance)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="bg-lightest">
                                                <td colspan="2" class="font-weight-bold">بیلانس قبلی</td>
                                                <td class="text-center">-</td>
                                                <td class="text-center">-</td>
                                                <td class="text-right font-weight-bold {{ $openingBalance >= 0 ? 'text-success' : 'text-danger' }}">
                                                    ${{ number_format(abs($openingBalance), 2) }}
                                                </td>
                                            </tr>

                                            @php 
                                                $runningBalance = $openingBalance;
                                                $totalDebit = 0;
                                                $totalCredit = 0;
                                            @endphp

                                            @foreach($ledgerTransactions as $tx)
                                                @php 
                                                    $runningBalance += ($tx->credit - $tx->debit);
                                                    $totalDebit += $tx->debit;
                                                    $totalCredit += $tx->credit;
                                                @endphp
                                                <tr>
                                                    <td>{{ $tx->date }}</td>
                                                    <td>
                                                        <span class="font-weight-bold d-block text-dark">{{ $tx->description }}</span>
                                                        <small class="text-muted">ID: #{{ $tx->id }} | Ref: {{ $tx->reference }}</small>
                                                    </td>
                                                    <td class="text-center text-danger font-weight-bold">
                                                        {{ $tx->debit > 0 ? '$' . number_format($tx->debit, 2) : '-' }}
                                                    </td>
                                                    <td class="text-center text-success font-weight-bold">
                                                        {{ $tx->credit > 0 ? '$' . number_format($tx->credit, 2) : '-' }}
                                                    </td>
                                                    <td class="text-right font-weight-bold {{ $runningBalance >= 0 ? 'text-success' : 'text-danger' }}">
                                                        ${{ number_format(abs($runningBalance), 2) }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot class="bg-light">
                                            <tr>
                                                <th colspan="2" class="text-right py-3">مجموعه دوره:</th>
                                                <th class="text-center text-danger py-3">${{ number_format($totalDebit, 2) }}</th>
                                                <th class="text-center text-success py-3">${{ number_format($totalCredit, 2) }}</th>
                                                <th class="text-right py-3 font-weight-bold" style="font-size: 1.1em;">
                                                    ${{ number_format(abs($runningBalance), 2) }}
                                                    <span class="small font-weight-normal text-muted">({{ $runningBalance >= 0 ? 'طلبکار' : 'بدهکار' }})</span>
                                                </th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <hr class="my-5">

                        <!-- Legacy Records -->
                        <div class="row no-print">
                            <div class="col-lg-12">
                                <h6 class="text-muted mb-3 cursor-pointer" data-toggle="collapse" data-target="#legacyMiscTable">
                                    <i class="feather icon-clock mr-1"></i> مشاهده جزئیات پرداخت‌های قدیمی (Legacy Logs)
                                </h6>
                                <div class="collapse" id="legacyMiscTable">
                                    <div class="table-responsive">
                                        <table class="table table-xs table-hover border">
                                            <thead class="thead-dark">
                                                <tr>
                                                    <th>#</th>
                                                    <th>تاریخ</th>
                                                    <th>تفصیلات</th>
                                                    <th>مبلغ</th>
                                                    <th>رسید (CR)</th>
                                                    <th>گرفت (DR)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($balances as $index => $b)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $b->date }}</td>
                                                    <td>{{ $b->description }}</td>
                                                    <td>{{ $b->amount }} {{ $b->currency == 1 ? 'AFN' : 'USD' }}</td>
                                                    <td>{{ $b->type == 'رسید' ? $b->amount : '-' }}</td>
                                                    <td>{{ $b->type == 'گرفت' ? $b->amount : '-' }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if(isset($all_accounts))
                        <div class="row mt-4">
                            <div class="col-lg-12">
                                <h4 class="mb-3 font-weight-bold text-center">خلاصه وضعیت تمامی حسابات متفرقه</h4>
                                <div class="table-responsive shadow-sm" style="border-radius: 10px;">
                                    <table class="table table-xs table-hover bg-white customer_demands">
                                        <thead class="bg-dark text-white">
                                            <tr>
                                                <th class="py-3">#</th>
                                                <th class="py-3">نام حساب</th>
                                                <th class="py-3 text-right">بیلانس نهایی (USD)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $totalOverallMisc = 0; @endphp
                                            @foreach($all_accounts as $index => $act)
                                                @php
                                                    $balance = DB::table('ledger_entries')
                                                        ->where('party_type', 'App\NewDifferentAccount')
                                                        ->where('party_id', $act->id)
                                                        ->join('ledger_transactions', 'ledger_entries.transaction_id', '=', 'ledger_transactions.id')
                                                        ->where('ledger_transactions.status', 'posted')
                                                        ->sum(DB::raw('credit - debit'));
                                                    $totalOverallMisc += $balance;
                                                @endphp
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td class="font-weight-bold text-dark">{{ $act->name }}</td>
                                                    <td class="text-right font-weight-bold {{ $balance >= 0 ? 'text-success' : 'text-danger' }}">
                                                        ${{ number_format(abs($balance), 2) }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot class="bg-light">
                                            <tr>
                                                <th colspan="2" class="text-right py-3 font-weight-bold">مجموع کل بدهی/طلبات متفرقه:</th>
                                                <th class="text-right py-3 font-weight-bold" style="font-size: 1.2em;">
                                                    ${{ number_format(abs($totalOverallMisc), 2) }}
                                                    <small class="text-muted">({{ $totalOverallMisc >= 0 ? 'طلب کل' : 'بدهی کل' }})</small>
                                                </th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection
@section('scripts')
    <script src="{{\Illuminate\Support\Facades\URL::asset('dsh/assets/xlsx/dist/xlsx.core.min.js')}}"></script>
    <script src="{{\Illuminate\Support\Facades\URL::asset('dsh/assets/file-saverjs/FileSaver.min.js')}}"></script>
    <script src="{{\Illuminate\Support\Facades\URL::asset('dsh/assets/blobjs/Blob.min.js')}}"></script>
    {{--    <script--}}
    {{--        src="{{\Illuminate\Support\Facades\URL::asset('dsh/assets/tableexport/dist/js/tableexport.min.js')}}"></script>--}}
    <script>
        $(document).ready(function () {
            $('#customer_select_id').select2();
            $(".customer_demands").tableExport({
                headers: true,                      // (Boolean), display table headers (th or td elements) in the <thead>, (default: true)
                footers: true,                      // (Boolean), display table footers (th or td elements) in the <tfoot>, (default: false)
                formats: ["xlsx"],                  // (String[]), filetype(s) for the export, (default: ['xlsx', 'csv', 'txt'])
                filename: "id",                     // (id, String), filename for the downloaded file, (default: 'id')
                bootstrap: true,                   // (Boolean), style buttons using bootstrap, (default: true)
                exportButtons: true,                // (Boolean), automatically generate the built-in export buttons for each of the specified formats (default: true)
                position: "bottom",                 // (top, bottom), position of the caption element relative to table, (default: 'bottom')
                ignoreRows: null,                   // (Number, Number[]), row indices to exclude from the exported file(s) (default: null)
                ignoreCols: 9,                   // (Number, Number[]), column indices to exclude from the exported file(s) (default: null)
                trimWhitespace: true,               // (Boolean), remove all leading/trailing newlines, spaces, and tabs from cell text in the exported file(s) (default: false)
                RTL: true,                         // (Boolean), set direction of the worksheet to right-to-left (default: false)
                sheetname: "id",

            });
            var $buttons = $('.customer_demands').find('caption').children().detach();
            // Append the buttons to an element of your choosing
            $buttons.appendTo('#exportButton');

        });
        $(document).ready(function () {
            $("#datePicker_from").persianDatepicker({
                months: ["حمل", "ثور", "جوزا", "سرطان", "اسد", "سنبله", "میزان", "عقرب", "قوس", "جدی", "دلو", "حوت"],
                formatDate: 'YYYY-0M-0D'
            });
            $("#datePicker_to").persianDatepicker({
                months: ["حمل", "ثور", "جوزا", "سرطان", "اسد", "سنبله", "میزان", "عقرب", "قوس", "جدی", "دلو", "حوت"],
                formatDate: 'YYYY-0M-0D'
            });
        });
    </script>
@endsection
