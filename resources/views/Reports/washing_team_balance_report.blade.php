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
                    <h4> صورت حساب شست گر ها

                    </h4>
                    <div class="btn-group" id="exportButton" style="float: left; margin-bottom: 4px;">
                        <button onclick="window.print()" class="btn btn-primary btn-sm">چاپ <span
                                class="fas fa-print"></span></button>

                    </div>
                </div>
                <div class="card-body">

                    <form action="/dashboard/get_washing_team_balance_report" method="POST" id="search_form">
                        <div class="row">
                            @csrf
                            <div class="form-group col-lg-3 col-md-3 col-sm-12 col-xs-12" style="margin-top: 34px;">
                                <?php $washing_team_accounts = \Illuminate\Support\Facades\DB::table('washing_teams')->get(); ?>
                                <select name="team_id" id="customer_select_id" class="form-control">
                                    <option value="" selected>انتخاب حساب شست گر</option>
                                    <option value="all"> همه</option>
                                    @foreach($washing_team_accounts as $t)

                                        <option value="{{$t->id}}">{{$t->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                                <label class="">تاریخ شروع</label>
                                <input type="date" class="form-control" name="from_date"
                                       placeholder=" تاریخ شروع..." />
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                                <label class="">تاریخ ختم</label>
                                <input type="date" class="form-control" name="to_date"
                                       placeholder=" تاریخ ختم..." />
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12" style="margin-top: 30px">
                                <button class="btn btn-primary btn-sm btn-block"
                                        type="submit" style="    position: absolute;top: 22%; left: -3%;">جستجو
                                </button>
                            </div>

                        </div>
                    </form>

                    @if($balances)

                        <div class="row">

                            <div class="col-lg-8 col-md-8 col-sm-8">
                                <h5>بیلانس محترم {{ $account->name }} از تاریخ {{ $from_date }} الی
                                    تاریخ {{ $to_date }}</h5>

                            </div>


                        </div>
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <div class="table-responsive">
                                    <table class="table table-xs table-hover">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>تاریخ</th>
                                            <th>تفصیلات</th>
                                            <th>نرخ دالر</th>
                                            <th>مبلغ</th>
                                            <th>رسید</th>
                                            <th>گرفت</th>
                                            <th>بیلانس (دالر)</th>
                                            <th>بیلانس (افغانی)</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            <?php $e = 1; $total_balance_usd = 0; $total_balance_afg=0; ?>
                                        @foreach($balances as $balance)

                                            <tr>
                                                <td>{{$e}}</td>
                                                <td>{{$balance->date}}</td>
                                                <td>{{$balance->description}}</td>
                                                <td>{{$balance->dollar_rate}}</td>
                                                <td>@if($balance->amount > 0) {{$balance->amount . ' دالر '}} @else {{$balance->amount_af . ' افغانی '}} @endif </td>
                                                @if($balance->type == 'رسید')
                                                    @if($balance->amount > 0)
                                                        <td> {{round($balance->amount,2)}}</td>
                                                            <?php
                                                            $total_balance_usd += $balance->amount;
                                                            ?>
                                                    @else
                                                        <td>{{$balance->amount_af}}</td>
                                                            <?php
                                                            $total_balance_afg += $balance->amount_af;
                                                            ?>
                                                    @endif
                                                @else
                                                    <td>0</td>
                                                @endif
                                                @if($balance->type == 'گرفت')
                                                    @if($balance->amount_af > 0)
                                                        <td>  {{round($balance->amount_af,2)}} </td>
                                                            <?php
                                                            $total_balance_afg -= $balance->amount_af;
                                                            ?>
                                                    @else
                                                        <td> {{$balance->amount}}</td>
                                                            <?php
                                                            $total_balance_usd -= $balance->amount;
                                                            ?>
                                                    @endif
                                                @else
                                                    <td>0</td>
                                                @endif
                                                <td>{{round($total_balance_usd, 2)}}</td>
                                                <td>{{round($total_balance_afg,2)}}</td>

                                            </tr>
                                                <?php $e++; ?>


                                        @endforeach
                                        </tbody>
                                    </table>

                                </div>
                            </div>
                        </div>

                    @endif
                    @if($all_accounts)

                        <h4>
                            گزارش بیلانس حسابات شست گر ها ازتاریخ {{ $from_date  }} الی {{ $to_date }}
                        </h4>
                        <div class="row">
                            <div class="table-responsive container">

                                <table class="table table-xs table-hover customer_demands">
                                    <thead>
                                    <tr>
                                        <th rowspan="2" style="vertical-align: inherit;">#</th>
                                        <th rowspan="2" style="vertical-align: inherit;">نام کچایی گر</th>
                                        <th colspan="2" style="vertical-align: inherit;"> بیلانس</th>
                                    </tr>
                                    <tr>
                                        <th>افغانی</th>
                                        <th>دالر</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        <?php $c = 1; $total_afg = 0; $total_usd = 0; ?>
                                    @foreach($all_accounts as $cr)
                                            <?php
                                            $afg_temp = \Illuminate\Support\Facades\DB::table('washing_payments')->where('team_id', $cr->id)->where('type', 'رسید')->where('amount_af','>', 0)->whereBetween('date', [$from_date, $to_date])->sum('amount_af')
                                                - \Illuminate\Support\Facades\DB::table('washing_payments')->where('team_id', $cr->id)->where('type', 'گرفت')->where('amount_af','>', 0)->whereBetween('date', [$from_date, $to_date])->sum('amount_af');

                                            $usd_temp = \Illuminate\Support\Facades\DB::table('washing_payments')->where('team_id', $cr->id)->where('type', 'رسید')->where('amount','>', 0)->whereBetween('date', [$from_date, $to_date])->sum('amount')
                                                - \Illuminate\Support\Facades\DB::table('washing_payments')->where('team_id', $cr->id)->where('type', 'گرفت')->where('amount','>', 0)->whereBetween('date', [$from_date, $to_date])->sum('amount');

                                            ?>
                                        <tr>
                                            <td>{{$c}}</td>
                                            <td>{{$cr->name}}</td>
                                            <td>{{round($afg_temp , 2)}}</td>
                                                <?php $total_afg += $afg_temp; ?>

                                            <td>{{round($usd_temp , 2)}}</td>
                                                <?php $total_usd += $usd_temp; ?>
                                        </tr>
                                            <?php $c++; ?>
                                    @endforeach
                                    </tbody>
                                    <tfoot>
                                    <th colspan="2">مجموعه</th>
                                    <th style="text-align: right; direction: ltr;">{{round($total_afg ,2)}}</th>
                                    <th style="text-align: right; direction: ltr;">{{round($total_usd ,2)}}</th>
                                    </tfoot>
                                </table>
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
