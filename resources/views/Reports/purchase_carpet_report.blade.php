@extends('dsh.master')
@section('title' , 'لیست قالین های خریده شده')
@section('content')
    <!-- navbar -->

    <div class="row" id="list-buy-carpet">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="card-header">
                    <h4> گزارش خریداری قالین</h4>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <form action="{{url('/dashboard/get_purchase_carpet_report')}}" method="POST"
                                  id="search_form">
                                <div class="row">
                                    @csrf

                                    <div class="col col-lg-4 col-md-4 col-sm-12">
                                        <input type="date" class="form-control" name="from_date" required
                                               placeholder=" تاریخ شروع..." autocomplete="off" id="datePicker_from"/>
                                    </div>
                                    <div class="col col-lg-4 col-md-4 col-sm-12">
                                        <input type="date" class="form-control" name="to_date" required
                                               placeholder=" تاریخ ختم..." autocomplete="off" id="datePicker_to"/>
                                    </div>
                                    <div class="col col-lg-4 col-md-4 col-sm-12">
                                        <button class="btn btn-primary btn-sm btn-block"
                                                type="submit" style="    position: absolute;top: 22%; left: -3%;">جستجو
                                        </button>
                                    </div>

                                </div>
                            </form>
                        </div>
                    </div>
                    @if($search)
                     <div class="row" style="margin-top: 60px">

                            <div class="col-lg-8 col-md-8 col-sm-8">
                                <h5> گزارش خریداری قالین از تاریخ {{ $from_date }} الی
                                    تاریخ {{ $to_date }}</h5>

                            </div>


                        </div>
                        <div class="table-responsive " >
                            <table class="table table-hover table-xs" id="list_buy_carpet">
                                <thead>
                                <tr>
                                    <th>شماره قالین</th>
                                    <th>اسم فروشنده</th>
                                    <th>شماره فرمایش</th>
                                    <th>شماره پارچه</th>
                                    <th>نوعیت</th>
                                    <th>کوالتی</th>
                                    <th>قیمت فی متر</th>
                                    <th>حاشیه</th>
                                    <th>زمینه</th>
                                    <th>طول</th>
                                    <th>عرض</th>
                                    <th>مساحت</th>
                                    <th>تاریخ شروع کار</th>
                                    <th>تاریخ ختم کار</th>

                                </tr>
                                </thead>
                                <tbody>
                                @foreach($carpets as $carpet)
                                    <tr class="ur{{ $carpet->carpet_id }}">
                                        <td>{{$carpet->carpet_no}}</td>
                                        @foreach($agents as $agent)
                                            @if($agent->agent_id == $carpet->agent_id)
                                                <td>{{$agent->user->name}}</td>
                                            @endif
                                        @endforeach
                                        @if($carpet->carpet_order)
                                            <td>{{$carpet->carpet_order->order_number}}</td>
                                        @else
                                            <td></td>
                                        @endif
                                        <td>{{$carpet->parcha_number}}</td>
                                        @if($carpet->type)
                                            <td>{{$carpet->type->carpet_type}}</td>
                                        @else
                                            <td></td>
                                        @endif
                                        @if($carpet->quality)
                                            <td>{{$carpet->quality->quality}}</td>
                                        @else
                                            <td></td>
                                        @endif
                                        <td style="direction: ltr">{{$carpet->price}} $</td>

                                        <td>{{$carpet->margin}}</td>
                                        <td>{{$carpet->field}}</td>

                                        <td style="direction: ltr">{{$carpet->height}} m</td>
                                        <td style="direction: ltr">{{$carpet->width}} m</td>
                                        <td style="direction: ltr">{{$carpet->area}} m <sup>2</sup></td>
                                        <td>{{$carpet->date}}</td>
                                        <td>{{$carpet->end_date}}</td>


                                    </tr>
                                @endforeach
                                <tr>
                                        <td><b>تعداد</b></td>
                                        <td colspan=>{{$carpets->count()}} pcs</td>
                                    </tr>
                                    <tr>
                                        <td><b>مساحت</b></td>
                                        <td>{{$carpets->sum('area')}} m <sup>2</sup></td>
                                    </tr>
                        
                                </tbody>
                            </table>

                        </div>
                    @endif
                </div>

            </div>
        </div>

    </div>
@endsection



