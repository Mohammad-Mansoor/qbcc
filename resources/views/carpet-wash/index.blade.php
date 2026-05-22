@extends('dsh.master')

@section('content')
  <div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <div class="card">
        <div class="card-header">
          <div class="alert alert-success" style="display:none;" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                      aria-hidden="true">&times;</span></button>
            مرحله ترمیم حذف شد
          </div>
          
          @if(session("status"))
            <div class="alert alert-success status text-center" style="display:none;" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
              {{session('status')}}
            </div>
          
          @endif
          @if(session("error"))
            
            <div class="alert alert-success status text-center" style="display:none;" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
              {{session('error')}}
            </div>
          
          @endif
        
        
        </div>
        <div class="card-body">
          <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
            <li class="nav-item">
              <a class="nav-link has-ripple {{$wash_check == 1 ? 'active' : ''}} " id="pills-non-washed-tab"
                 data-toggle="pill"
                 href="#non-washed"
                 role="tab" aria-controls="pills-non-washed" aria-selected="true">لیست شست نمبر ها<span
                        class="ripple ripple-animate"
                        style="height: 71.6719px; width: 71.6719px; animation-duration: 0.7s; animation-timing-function: linear; background: rgb(70, 128, 255); opacity: 0.4; top: -30.8359px; left: 9.16405px;"></span></a>
            </li>
            <li class="nav-item">
              <a class="nav-link has-ripple {{$wash_check == 2 ? 'active' : ''}}" id="pills-washed-tab"
                 data-toggle="pill" href="#washed"
                 role="tab" aria-controls="pills-washed" aria-selected="false">لیست شسته شده و نشسته<span
                        class="ripple ripple-animate"
                        style="height: 82.2188px; width: 82.2188px; animation-duration: 0.7s; animation-timing-function: linear; background: rgb(70, 128, 255); opacity: 0.4; top: -15.1094px; left: 18.9375px;"></span></a>
            </li>
          </ul>
          
          <div class="tab-content" id="pills-tabContent">
            
            <div class="tab-pane fade {{$wash_check == 1 ? 'active show' : ''}}" id="non-washed" role="tabpanel"
                 aria-labelledby="pills-non-washed-tab">
              <div class="row">
                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                  <form action="/dashboard/carpet-wash/search" method="POST" id="dateSearch">
                    @csrf
                    <input type="hidden" name="from_non_washed" value="from non washed">
                    <div class="row">
                      
                      <div class="col-lg-2 col-md-2 col-sm-2 col-xs-4">
                        <input type="submit" style="margin-top: 30px;" value="جستجو"
                               class="date-submit btn btn-sm btn-primary btn-block">
                      </div>
                      
                      
                      <div class="col-lg-4 col-md-4 col-sm-4 col-xs-8" style="margin-top: 10px;">
                        <span class="date-label">نام کاریگر</span>
                        <select name="team_id" id="team_id" class="form-control" required>
                          
                          @foreach($team as $t)
                            <option value="{{$t->id}}">{{$t->name}}</option>
                          @endforeach
                        </select>
                      </div>
                      <div class="col-lg-4 col-md-4 col-sm-4 col-xs-8">
                      
                      </div>
                    </div>
                  </form>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                  <div class="btn btn-sm btn-primary" style="float: left;margin-bottom: 20px;"
                       onclick="printPage('noneRepairPrint')"><i
                            class="fa fa-print"></i> Print
                  </div>
                </div>
              </div>
              <div class="static-table-list" style="margin-top: 20px" id="noneRepairPrint">
                
                <table class="table table-hover table-xs">
                  <thead>
                  <tr>
                    <th>شست گر</th>
                    <th>شماره تماس</th>
                    <th>ادرس</th>
                    <th class="hideOnPrint">لیست شست نمبر</th>
                  </tr>
                  </thead>
                  <tbody>
                  @forelse ($team as $t)
                    @if($t-> carpet_wash->count() > 0)
                      <tr class="ur{{ $t->id }}">
                        <td>{{$t->name}}</td>
                        <td>{{$t->contact_no}}</td>
                        <td>{{$t->address}}</td>
                        <td class="hideOnPrint"><a class="btn btn-warning btn-sm"
                                                   href="/dashboard/carpet-wash/wash-numbers/{{$t->id}}">لیست شست
                            نمبر</a></td>
                      </tr>
                    @endif
                  @empty
                    <h4 class="text-info text-center">هنوز موردی ثبت نشده است</h4>
                  @endforelse
                  </tbody>
                </table>
                
                {{--<table class="table table-hover table-xs">--}}
                {{--<thead>--}}
                {{--<tr>--}}
                {{----}}
                {{--<th>شماره پارچه</th>--}}
                {{--<th>اسم نماینده</th>--}}
                {{--<th>شماره فرمایش</th>--}}
                {{--<th>نوعیت قالین</th>--}}
                {{--<th>نمبر نقشه</th>--}}
                {{--<th class="printTitle">شست</th>--}}
                {{--</tr>--}}
                {{--</thead>--}}
                {{--<tbody>--}}
                {{--@foreach($nonwashed as $nwashed)--}}
                {{--<tr class="ur{{ $nwashed->carpet_id }}">--}}
                {{----}}
                {{--<td>{{$nwashed->carpet_no}}</td>--}}
                {{--@foreach($agents as $agent)--}}
                {{--@if($agent->agent_id == $nwashed->agent_id)--}}
                {{--<td>{{$agent->user->name}}</td>--}}
                {{--@endif--}}
                {{--@endforeach--}}
                {{----}}
                {{--<td>{{$nwashed->carpet_order->order_number ?? ''}}</td>--}}
                {{----}}
                {{--<td>{{$nwashed->type->carpet_type ?? ''}}</td>--}}
                {{--<td>{{$nwashed->date}}</td>--}}
                {{--<td><a href="/dashboard/carpet-wash/create/{{$nwashed->carpet_id}}"--}}
                {{--class="btn btn-sm btn-info printBTN"><i--}}
                {{--class="fa fa-pencil"></i>&nbsp; شست</a></td>--}}
                {{--</tr>--}}
                {{--@endforeach--}}
                {{--</tbody>--}}
                {{--</table>--}}
              
              </div>
            
            </div>
            <div class="tab-pane fade {{$wash_check == 2 ? 'active show' : ''}}" id="washed" role="tabpanel"
                 aria-labelledby="pills-washed-tab">
              
              <div class="row">
                
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                  <form action="/dashboard/carpet-wash/search" method="POST" id="dateSearch">
                    @csrf
                    <input type="hidden" name="from_washed" value="from washed">
                    <div class="row">
                      
                      
                      <span class="date-label">جستجو</span>
                      <input type="text" name="search" class="form-control">
                    
                    </div>
                  </form>
                </div>
                <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1"></div>
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3" style="margin-top: 10px;">
                  <form action="/dashboard/carpet-wash/search-carpet-type" method="POST" id="dateSearch">
                    @csrf
                    <div class="row">
                        <?php
                        $carpet_types = \App\CarpetType::all();
                        ?>
                      <span class="date-label">جستجو نوعیت</span>
                      <select name="carpet_type_id" id="carpet_type_id" class="form-control" onchange="this.form.submit()">
                        
                        @foreach($carpet_types as $type)
                          <option value="{{$type->carpet_type_id}}">{{$type->carpet_type}}</option>
                        @endforeach
                      </select>
                    
                    </div>
                  </form>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                  <div class="btn btn-sm btn-primary" style="float: left;margin-bottom: 20px;"
                       onclick="printPage('repairPrint')"><i
                            class="fa fa-print"></i> Print
                  </div>
                </div>
              </div>
              <div class="static-table-list" style="margin-top: 20px" id="repairPrint">
                <table class="table table-hover table-xs">
                  <thead>
                  <tr>
                    
                    <th>شماره قالین</th>
                    <th>نوعیت قالین</th>
                    <th> شست نمبر از طرف مرکزی</th>
                    <th> شست نمبر از طرف فروشات</th>
                    <th> قیمت فی متر</th>
                    <th> قیمت مجموع</th>
                    <th>تاریخ شست</th>
                    <th>تیم شوینده</th>
                    <th>شرح</th>
                    <th class="printTitle">ویرایش</th>
                    <th class="printTitle">ارسال به بخش تیاری</th>
                    <th class="printTitle">بازگشت</th>
                  </tr>
                  </thead>
                  <tbody>
                  @foreach($washeds as $washed)
                    <tr class="ur{{ $washed->id }}">
                      <td>{{$washed->carpet->carpet_no}}</td>
                      <td>{{$washed->carpet->type->carpet_type}}</td>
                      <td>{{$washed->wash_number}}</td>
                      <td>
                        <a href="/dashboard/search-wash-numbersh-payment/{{$washed->wash_number_sh}},{{$washed->team_id}}"
                        >&nbsp; {{$washed->wash_number_sh}}</a></td>
                       <td style="direction: ltr">{{ number_format($washed->price, 2) }} $</td>
                       <td style="direction: ltr">
                         <span class="font-weight-bold text-success">{{ number_format($washed->af_total_price, 2) }} {{ $washed->currency_code ?: 'USD' }}</span>
                         @if($washed->currency_code && $washed->currency_code != 'USD')
                           <br><small class="text-muted">({{ number_format($washed->total_price, 2) }} USD)</small>
                         @endif
                       </td>
                       <td>{{$washed->date}}</td>
                      <td>{{$washed->washing_team->name}}</td>
                      <td>{{$washed->description}}</td>
                      <td><a href="/dashboard/carpet-wash/{{$washed->id}}/edit"
                             class="btn btn-sm btn-info printBTN"><i
                                  class="fa fa-pencil"></i>&nbsp; ویرایش</a></td>
                      @if ($washed->carpet->status == 13)
                        <td class="hideOnPrint"><a
                                  href="/dashboard/carpet-wash/sent-to-finish/{{$washed->carpet->carpet_id}}"
                                  class="btn btn-sm btn-info printBTN"><i
                                    class="fa fa-send"></i>&nbsp; ارسال به بخش تیاری</a></td>
                      @elseif($washed->carpet->status == 0)
                        <td class="hideOnPrint">
                          <label for="" class="badge badge-success">در نزد نماینده</label>
                        </td>
                      @elseif($washed->carpet->status == 1)
                        <td class="hideOnPrint">
                          <label for="" class="badge badge-warning">در گدام مرکزی</label>
                        </td>
                      @elseif($washed->carpet->status == 2)
                        <td class="hideOnPrint">
                          <label for="" class="badge badge-danger">در کچایی نشده ها</label>
                        </td>
                      @elseif($washed->carpet->status == 12)
                        <td class="hideOnPrint">
                          <label for="" class="badge badge-primary">در کچایی شده ها</label>
                        </td>
                      @elseif($washed->carpet->status == 3)
                        <td class="hideOnPrint">
                          <label for="" class="badge badge-light-warning">در شست نشده ها</label>
                        </td>
                      @elseif($washed->carpet->status == 4)
                        <td class="hideOnPrint">
                          <label for="" class="badge badge-light-primary">در بخش تیاری</label>
                        </td>
                      @elseif($washed->carpet->status == 5)
                        <td class="hideOnPrint">
                          <label for="" class="badge badge-info">در گدام فروشات</label>
                        </td>
                      @elseif($washed->carpet->status == 6)
                        <td class="hideOnPrint">
                          <label for="" class="badge badge-success">فروخته شده</label>
                        </td>
                      
                      @endif
                      
                      @if($washed->carpet->status == 13 || $washed->carpet->status == 3)
                        @if(auth()->user()->role == 'CO' || auth()->user()->role == 'CCO')
                                <?php $kachaee = \App\CarpetRepair::where('carpetId', $washed->carpetId)->first(); ?>
                          @if($kachaee)
                            <td><a href="/dashboard/carpet-wash/return-to-kachaee/{{$washed->id}}"
                                   class="btn btn-sm btn-warning printBTN"><i
                                        class="fa fa-pencil"></i>&nbsp; بازگشت به کچایی</a></td>
                          @else
                            <td><a href="/dashboard/carpet-wash/return-to-center/{{$washed->id}}"
                                   class="btn btn-sm btn-info printBTN"><i
                                        class="fa fa-pencil"></i>&nbsp; بازگشت به مرکزی</a></td>
                          @endif
                        @else
                          <td></td>
                        @endif
                      @else
                        <td class="hideOnPrint">
                          
                          @if($washed->carpet->status == 3)
                            
                            <label for="" class="badge badge-light-warning">در شست نشده ها</label>
                          @elseif($washed->carpet->status == 13)
                            
                            <label for="" class="badge badge-primary">در شست شده ها</label>
                          @elseif($washed->carpet->status == 4)
                            <label for="" class="badge badge-light-primary">در بخش تیاری</label>
                          @elseif($washed->carpet->status == 5)
                            <label for="" class="badge badge-info">در گدام فروشات</label>
                          @elseif($washed->carpet->status == 6)
                            <label for="" class="badge badge-success">فروخته شده</label>
                          
                          @endif
                        </td>
                      @endif
                    
                    </tr>
                  @endforeach
                  
                  <tr>
                    
                    
                    <th><b>مجموع پول کاریگر</b></th>
                    
                     <th><b>$ {{ number_format($washeds->sum('total_price'), 2) }} </b></th>
                    <th>مجموع متراژ قالین</th>
                    <th style="direction: ltr">{{$washeds->sum('area')}} m<sup>2</sup></th>
                    <th>تعداد قالین</th>
                    <th>{{$washeds->count()}}</th>
                    <td class="hideOnPrint"></td>
                    <td class="hideOnPrint"></td>
                    <td></td>
                    <td></td>
                  
                  </tr>
                  </tbody>
                </table>
                @if($wash_check != 2)
                  <p>{{$washeds->links()}}</p>
                @endif
              </div>
            
            </div>
          
          </div>
        
        </div>
      </div>
    </div>
  </div>

@endsection
@section('scripts')
  
  <script>
    $('#carpet_type_id').select2();
    $('#team_id').select2();
  </script>
@endsection
