@extends('dsh.master')
@section('content')
    <!-- navbar -->

    <!-- form -->

    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    @if(!$accountEdit)
                        <h4>ایجاد حساب مصارف ماهانه</h4>
                    @else
                        <h4>ویرایش حساب مصارف ماهانه</h4>
                    @endif
                </div>
                <div class="card-body">
                    @if(!$accountEdit)
                        <form method="post" id="" action="/dashboard/monthly-expense-accounts">
                            @csrf
                            <div class="row">
                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                    <span class="date-label">ماه</span>
                                    <select name="month_name" id="month" class="form-control">
                                        <option value="جنوری">1-جنوری</option>
                                        <option value="فبروری">2-فبروری</option>
                                        <option value="مارچ">3-مارچ</option>
                                        <option value="اپریل">4-اپریل</option>
                                        <option value="می">5-می</option>
                                        <option value="جون">6-جون</option>
                                        <option value="جولای">7-جولای</option>
                                        <option value="اگست">8-اگست</option>
                                        <option value="سپتمبر">9-سپتمبر</option>
                                        <option value="اکتبر">10-اکتبر</option>
                                        <option value="نومبر">11-نومبر</option>
                                        <option value="دسمبر">12-دسمبر</option>
                                    </select>
                                </div>

                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                    <span class="date-label">سال</span>
                                    <select name="year_name" id="year" class="form-control">
                                        <option value="2025">2025</option>
                                        <option value="2026">2026</option>
                                        <option value="2027">2027</option>
                                        <option value="2028">2028</option>
                                        <option value="2029">2029</option>
                                        <option value="2030">2030</option>

                                    </select>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12" style="margin-top: 20px">
                                    <button class="btn btn-block btn-primary submit-btn" type="submit">ثبت</button>
                                </div>
                            </div>


                        </form>
                    @else
                        <form method="post" id="" action="/dashboard/monthly-expense-accounts/{{$accountEdit->id}}">
                            {{method_field('patch')}}
                            @csrf
                            <div class="row">
                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                    <span class="date-label">ماه</span>
                                    <select name="month" id="month" class="form-control">
                                        <option value="جنوری" {{($accountEdit->month_name == 'جنوری' ? 'selected' : '')}}>
                                            1-جنوری
                                        </option>

                                        <option value="فبروری" {{($accountEdit->month_name == 'فبروری' ? 'selected' : '')}}>
                                            2-فبروری
                                        </option>
                                        <option value="مارچ" {{($accountEdit->month_name == 'مارچ' ? 'selected' : '')}}>
                                            3-مارچ
                                        </option>
                                        <option value="اپریل" {{($accountEdit->month_name == 'اپریل' ? 'selected' : '')}}>
                                            4-اپریل
                                        </option>
                                        <option value="می" {{($accountEdit->month_name == 'می' ? 'selected' : '')}}>5-می
                                        </option>
                                        <option value="جون" {{($accountEdit->month_name == 'جون' ? 'selected' : '')}}>
                                            6-جون
                                        </option>
                                        <option value="جولای" {{($accountEdit->month_name == 'جولای' ? 'selected' : '')}}>
                                            7-جولای
                                        </option>
                                        <option value="اگست" {{($accountEdit->month_name == 'اگست' ? 'selected' : '')}}>
                                            8-اگست
                                        </option>
                                        <option value="سپتمبر" {{($accountEdit->month_name == 'سپتمبر' ? 'selected' : '')}}>
                                            9-سپتمبر
                                        </option>
                                        <option value="اکتبر" {{($accountEdit->month_name == 'اکتبر' ? 'selected' : '')}}>
                                            10-اکتبر
                                        </option>
                                        <option value="نومبر" {{($accountEdit->month_name == 'نومبر' ? 'selected' : '')}}>
                                            11-نومبر
                                        </option>
                                        <option value="دسمبر" {{($accountEdit->month_name == 'دسمبر' ? 'selected' : '')}}>
                                            12-دسمبر
                                        </option>
                                    </select>
                                </div>

                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                    <span class="date-label">سال</span>
                                    <select name="year" id="year" class="form-control">

                                        <option value="2025" {{($accountEdit->year_name == '2025' ? 'selected' : '')}}>
                                            2025
                                        </option>
                                        <option value="2026" {{($accountEdit->year_name == '2026' ? 'selected' : '')}}>
                                            2026
                                        </option>
                                        <option value="2027" {{($accountEdit->year_name == '2027' ? 'selected' : '')}}>
                                            2027
                                        </option>
                                        <option value="2028" {{($accountEdit->year_name == '2028' ? 'selected' : '')}}>
                                            2028
                                        </option>
                                        <option value="2029" {{($accountEdit->year_name == '2029' ? 'selected' : '')}}>
                                            2029
                                        </option>
                                        <option value="2030" {{($accountEdit->year_name == '2030' ? 'selected' : '')}}>
                                            2030
                                        </option>

                                    </select>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12" style="margin-top: 20px">
                                    <button class="btn btn-block btn-primary submit-btn" type="submit">ثبت</button>
                                </div>


                            </div>

                        </form>
                    @endif

                </div>
            </div>
        </div>

    </div>

    <div class="row" id="accounts">
        <!-- Extra small table start-->
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>حسابات مصارف ماهانه</h5>
                    @if(session("status"))
                        <div class="alert alert-success status" style="display:none;" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                            {{session('status')}}
                        </div>

                    @endif
                    @if(session("error"))

                        <div class="alert alert-danger status" style="display:none;" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                            {{session('error')}}
                        </div>

                    @endif
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-xs table-hover" id="monthly_expense_account_list">
                            <thead>
                            <tr>
                                <th>شماره</th>
                                <th>نام ماه</th>
                                <th>سال</th>
                                <th class="hideOnPrint">حسابات</th>

                            </tr>
                            </thead>
                            <tbody>
                            @foreach($months  as $m)
                                <tr>
                                    <td>{{$m->me_id}}</td>
                                    <td>{{$m->month_name}}</td>
                                    <td>{{$m->year_name}}</td>
                                    <td class="hideOnPrint"><a href="/dashboard/monthly-expense-accounts/{{$m->me_id}}"
                                                               class="btn btn-sm btn-warning">&nbsp;
                                            حسابات</a></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- Extra small table start-->
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            $("#monthly_expense_account_list").tableExport({
                headers: true,                      // (Boolean), display table headers (th or td elements) in the <thead>, (default: true)
                footers: true,                      // (Boolean), display table footers (th or td elements) in the <tfoot>, (default: false)
                formats: ["xlsx"],                  // (String[]), filetype(s) for the export, (default: ['xlsx', 'csv', 'txt'])
                filename: "id",                     // (id, String), filename for the downloaded file, (default: 'id')
                bootstrap: true,                   // (Boolean), style buttons using bootstrap, (default: true)
                exportButtons: true,                // (Boolean), automatically generate the built-in export buttons for each of the specified formats (default: true)
                position: "bottom",                 // (top, bottom), position of the caption element relative to table, (default: 'bottom')
                ignoreRows: null,                   // (Number, Number[]), row indices to exclude from the exported file(s) (default: null)
                ignoreCols: 7,                   // (Number, Number[]), column indices to exclude from the exported file(s) (default: null)
                trimWhitespace: true,               // (Boolean), remove all leading/trailing newlines, spaces, and tabs from cell text in the exported file(s) (default: false)
                RTL: true,                         // (Boolean), set direction of the worksheet to right-to-left (default: false)
                sheetname: "id",

            });
            var $buttons = $('#monthly_expense_account_list').find('caption').children().detach();
            // Append the buttons to an element of your choosing
            $buttons.appendTo('#exportButton');

        });
        $('#form2').hide();





    </script>
@endsection

