@extends('dsh.master')
@section('title')
    گزارش  مصارف
@endsection
@section('content')


    <div class="row">
        <div class="col col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="card-header">
                    <h4>
                        گزارش مصارف
                    </h4>
                </div>
                <div class="card-body">
                    <div id="search_form">
                        <form action="{{url('/dashboard/get_expense_report')}}" method="POST" autocomplete="off">
                            @csrf
                            <div class="row">
                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                                    <div class="form-group fill">
                                        <label for="" class="control-label">تاریخ شروع</label>
                                        <input type="date" class="form-control" name="from_date"
                                               placeholder=" تاریخ شروع..." autocomplete="off"/>
                                    </div>

                                </div>
                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">

                                    <div class="form-group fill">
                                        <label for="" class="control-label">تاریخ ختم</label>
                                        <input type="date" class="form-control" name="to_date"
                                               placeholder=" تاریخ ختم..." autocomplete="off"/>
                                    </div>

                                </div>

                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4" style="margin-top: 10px;">
                                    <div class="form-group fill">
                                        <label for="category_search_input" class="control-label">کتگوری</label>
                                        <select class="form-control" id="category_search_input" name="category_id">
                                            <option value="">انتخاب کتگوری</option>
                                            <option value="all">همه</option>
                                            <option>خوراکه</option>
                                            <option>متفرقه دفتر</option>
                                            <option>کرایه و برق</option>
                                            <option>ترانسپورت</option>
                                            <option>برداشت</option>
                                            <option>ترمیمات و تیل</option>
                                            <option>معاشات</option>
                                            <option>اجوره</option>
                                        </select>

                                    </div>

                                </div>


                                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2" style="margin-top: 20px;">
                                    <div class="form-group fill">
                                        <label for=""></label>
                                        <button class="btn btn-primary btn-sm btn-block"
                                                id="SubmitSeacrchBtn" type="submit">جستجو
                                        </button>
                                    </div>
                                </div>
                                {{--input type  hidden --}}
                                <input type="hidden" id="token" value="{{csrf_token()}}">

                            </div>
                        </form>

                    </div>
                    @if($expenses)

                        <div class="btn-group" id="exportButton" style="float: left;">
                            <button onclick="window.print()" class="btn btn-primary btn-sm">چاپ <span
                                    class="fas fa-print"></span></button>

                        </div>
                        @if($all)
                            <h5 class="pull-right" style="padding:5px;">

                                گزارش تمام مصارف از تاریخ
                                ({{$from_date}} ) الی تاریخ
                                ( {{$to_date}} ) </h5>
                            <table class="table table-hover table-sm" id="customer_demands">
                                <thead>
                                <tr>
                                    <th style="text-align: right;" width="5%">شماره</th>
                                    <th style="text-align: right;">تاریخ</th>
                                    <th style="text-align: right;">تفصیل</th>
                                    <th style="text-align: right;">مقدارپول افغانی</th>
                                    <th style="text-align: right;">مقدارپول دالر</th>
                                </tr>

                                </thead>
                                <tbody>
                                    <?php $c = 1; $total_usd = 0; $total_afg = 0; ?>
                                @foreach($expenses as $ex)
                                    <tr>
                                        <td>{{$c}}</td>
                                        <td>{{$ex->date}}</td>
                                        <td>{{$ex->description}}</td>
                                        <td>{{$ex->amount}} {{$ex->currency == 1?'افغانی':'دالر'}}</td>
                                    </tr>
                                        <?php $c++;


                                        if ($ex->currency == 1){
                                            $total_afg += $ex->amount;
                                        }
                                        else{
                                            $total_usd += $ex->amount;
                                        }



                                        ?>
                                @endforeach
                                </tbody>
                                <thead>
                                <tr>
                                    <th colspan="3">مجموعه</th>
                                    <th>{{$total_afg}} افغانی</th>
                                    <th>{{$total_usd}} دالر</th>
                                </tr>
                                </thead>
                            </table>

                        @else
                            <h5 class="pull-right" style="padding:5px;">
                                گزارش مصرف {{$cat}} از تاریخ
                                ({{$from_date}} ) الی تاریخ
                                ( {{$to_date}} ) </h5>
                            <table class="table table-hover table-sm" id="customer_demands">
                                <thead>
                                <tr>
                                    <th style="text-align: right;" width="5%">شماره</th>
                                    <th style="text-align: right;" width="5%">کتگوری مصرف</th>
                                    <th style="text-align: right;">تاریخ</th>
                                    <th style="text-align: right;">تفصیل</th>
                                    <th style="text-align: right;">مقدارپول افغانی</th>
                                    <th style="text-align: right;">مقدارپول دالر</th>
                                </tr>

                                </thead>
                                <tbody>
                                    <?php $c = 1; $total_usd = 0; $total_afg = 0; ?>
                                @foreach($expenses as $ex)
                                    <tr>
                                        <td>{{$c}}</td>

                                        <td>{{$ex->category}}</td>
                                        <td>{{$ex->date}}</td>
                                        <td>{{$ex->description}}</td>
                                        <td>{{$ex->amount}}</td>
                                    </tr>
                                        <?php $c++;


                                        if ($ex->currency == 1){
                                            $total_afg += $ex->amount;
                                        }
                                        else{
                                            $total_usd += $ex->amount;
                                        }


                                        ?>
                                @endforeach
                                </tbody>
                                <thead>
                                <tr>
                                    <th colspan="4">مجموعه</th>
                                    <th>{{$total_afg}} افغانی</th>
                                    <th>{{$total_usd}} دالر</th>
                                </tr>
                                </thead>
                            </table>

                        @endif

                    @endif
                </div>

            </div>
        </div>
    </div>
@endsection

@section('scripts')

    <script>

        $('#category_search_input').select2();


        });


    </script>
@endsection
