@extends('dsh.master')

@section('content')
  <!-- navbar -->
  <div class="row" style="display: flex; justify-content: center;">
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
              <a class="nav-link has-ripple  {{$check == null ? 'active' : ''}}"
                 id="pills-non-finished-tab"
                 data-toggle="pill"
                 href="#non-finished"
                 role="tab" aria-controls="pills-non-finished" aria-selected="true">تیاری نشده ها<span
                        class="ripple ripple-animate"
                        style="height: 71.6719px; width: 71.6719px; animation-duration: 0.7s; animation-timing-function: linear; background: rgb(70, 128, 255); opacity: 0.4; top: -30.8359px; left: 9.16405px;"></span></a>
            </li>
            <li class="nav-item">
              <a class="nav-link has-ripple {{$check != null ? 'active' : ''}}"
                 id="pills-finished-tab"
                 data-toggle="pill" href="#finished"
                 role="tab" aria-controls="pills-finished" aria-selected="false">تیاری شده ها<span
                        class="ripple ripple-animate"
                        style="height: 82.2188px; width: 82.2188px; animation-duration: 0.7s; animation-timing-function: linear; background: rgb(70, 128, 255); opacity: 0.4; top: -15.1094px; left: 18.9375px;"></span></a>
            </li>
          </ul>
          
          <div class="tab-content" id="pills-tabContent">
            
            <div class="tab-pane fade {{$check == null ? 'active show' : ''}}" id="non-finished"
                 role="tabpanel"
                 aria-labelledby="pills-non-finished-tab">
              <div class="row">
                <div class="col-xs-3 col-lg-3 col-md-3 col-sm-3 hideOnPrint">
                  <form action="/dashboard/finishing-center/search-non" method="post">
                    @csrf
                    <input type="text" name="search_non" required
                           placeholder="جستجو" class="form-control">
                  </form>
                </div>
                <div class="col-xs-9 col-lg-9 col-md-9 col-sm-9"></div>
              
              </div>
              <div class="btn btn-sm btn-primary" style="float: left;margin-bottom: 30px;"
                   onclick="printPage('noneRepairPrint')"><i
                        class="fa fa-print"></i> Print
              </div>
              <div class="static-table-list table-responsive" id="noneRepairPrint">
                <table class="table table-hover table-xs" id="secondDataTable">
                  <thead>
                  <tr>
                    
                    <th>شماره قالین</th>
                    <th>اسم نماینده</th>
                    <th>شماره فرمایش</th>
                    <th>نوعیت قالین</th>
                    <th>نام شست گر</th>
                    <th>شست نمبر</th>
                    <th>طول</th>
                    <th>عرض</th>
                    <th>مصاحت</th>
                    <th>تاریخ شست</th>
                    <th class="printTitle">تیاری</th>
           
                    
                    <th class="printTitle">بازگشت</th>
                  </tr>
                  </thead>
                  <tbody>
                  @foreach($nonfinished as $nonfinish)
                    <tr class="ur{{ $nonfinish->carpet_id  ?? ''}}">
                      
                      <td>{{$nonfinish->carpet_no ?? ''}}</td>
                      @foreach($agents as $agent)
                        @if($agent->agent_id == $nonfinish->agent_id)
                          <td>{{$agent->user->name ?? ''}}</td>
                        @endif
                      @endforeach
                      <td>{{$nonfinish->carpet_order->order_number ?? ''}}</td>
                      <td>{{$nonfinish->type->carpet_type ?? ''}}</td>
                      @if($nonfinish->washing)
                        <td>{{$nonfinish->washing->name ?? ''}}</td>
                      @else
                        <td></td>
                      @endif
                      @if($nonfinish->carpet_wash)
                        <td>{{$nonfinish->carpet_wash->wash_number ?? ''}}</td>
                      @else
                        <td></td>
                      @endif
                      @if($nonfinish->carpet_wash)
                        <td>{{$nonfinish->carpet_wash->height ?? ''}}</td>
                      @else
                        <td></td>
                      @endif
                      @if($nonfinish->carpet_wash)
                        <td>{{$nonfinish->carpet_wash->width ?? ''}}</td>
                      @else
                        <td></td>
                      @endif
                      @if($nonfinish->carpet_wash)
                        <td>{{$nonfinish->carpet_wash->area ?? ''}}</td>
                      @else
                        <td></td>
                      @endif
                      @if($nonfinish->carpet_wash)
                        <td>{{$nonfinish->carpet_wash->date ?? ''}}</td>
                      @else
                        <td></td>
                      @endif
                      
                      
                      <td class="hideOnPrint"><a
                                href="/dashboard/finishing-center/finish-work/{{$nonfinish->carpet_id ?? ''}}"
                                class="btn btn-sm btn-info printBTN"><i
                                  
                                  class="fa fa-pencil"></i>&nbsp; تیاری</a></td>
  
                     
                        <?php $wash = \App\CarpetWash::where('carpetId', $nonfinish->carpet_id)->first(); ?>
                   
                      
                      @if($wash)
                        <td><a href="/dashboard/return-to-wash/{{$nonfinish->carpet_id}}"
                               class="btn btn-sm btn-warning printBTN"><i
                                    class="fa fa-pencil"></i>&nbsp; بازگشت به شست</a></td>
                      @else
                        
                        <td><a href="/dashboard/return-to-center-from-finish/{{$nonfinish->carpet_id}}"
                               class="btn btn-sm btn-info printBTN"><i
                                    class="fa fa-pencil"></i>&nbsp; بازگشت به مرکزی</a></td>
                      @endif
                    
                    
                    </tr>
                  @endforeach
                  
                  </tbody>
                </table>
                <p>{{$nonfinished->links() ?? ''}}</p>
              </div>
              
              <div class="row">
                
                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                  
                  <table class="table table-xs table-hover">
                    <tbody>
                    <tr>
                      <td>&nbsp;{{$nonfinished->count()}} pcs</td>
                      <td style="font-size: 12px;color: #0b97c4">QUANTITY</td>
                    
                    </tr>
                    <tr>
                      <td>m <sup>2</sup> &nbsp;{{$nonfinished->sum('area')}}</td>
                      <td style="font-size: 12px;color: #0b97c4">TOTAL</td>
                    
                    </tr>
                    </tbody>
                  </table>
                </div>
                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                
                </div>
              </div>
            
            </div>
            
            
            <div class="tab-pane fade {{$check != null ? 'active show' : ''}}" id="finished"
                 role="tabpanel"
                 aria-labelledby="pills-finished-tab">
              
              
              <div class="row">
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                  <form action="/dashboard/finishing-center/search" method="POST" id="dateSearch">
                    
                    @csrf
                    <input type="text" name="search_finish" required
                           placeholder="جستجو" class="form-control">
                  
                  </form>
                </div>
              </div>
              
              
              <div class="btn btn-sm btn-primary" style="float: left;margin-bottom: 30px;"
                   onclick="printPage('repairPrint')"><i
                        class="fa fa-print"></i> Print
              </div>
              <div class="static-table-list table-responsive" style="margin-top: 20px" id="repairPrint">
                <table class="table table-hover table-xs">
                  <thead>
                  <tr>
                    
                    <th>شماره قالین</th>
                    <th>نمبر تیاری</th>
                    <th> قیمت تیاری</th>
                    <th>تاریخ تیاری</th>
                    <th>تیم تیاری</th>
                    <th>نوع تیاری</th>
                    <th>شرح</th>
                    <th class="printTitle">حالت</th>
                    <th class="printTitle">دوباره تیاری</th>
                    <th class="printTitle">ویرایش</th>
                    {{-- <th class="printTitle">ارسال به گدام</th> --}}
                    <th class="printTitle">جزئیات کلی</th>
                  </tr>
                  </thead>
                  <tbody>
                  @php($total_area = 0)
                  @foreach($finisheds as $finish)
                    <tr class="ur{{ $finish->id  ?? ''}}">
                      <td>{{$finish->carpet->carpet_no ?? ''}}</td>
                      <td>
                        <a href="/dashboard/finishing-center/search-finish-number/{{$finish->finish_number}},{{$finish->team_id}}"
                        >&nbsp; {{$finish->finish_number}}</a></td>
                      <td style="direction: ltr">{{round($finish->price_af,2) ?? ''}} $</td>
                      <td>{{$finish->date ?? ''}}</td>
                      <td>{{$finish->team->name ?? ''}}</td>
                      <td>{{$finish->category->category ?? ''}}</td>
                      <td>{{$finish->description ?? ''}}</td>
                      @if($finish->status == 0)
    
                        <td class="hideOnPrint">
                          <label class="badge badge-warning">درخواست تایید
                            نشده</label></td>
                      @else
    
                        <td class="hideOnPrint"><label for="" class="badge-success">درخواست تایید
                            شد</label></td>
  
                      @endif
  
                      <td class="hideOnPrint"><a
                                href="/dashboard/finishing-center/re-finish-work/{{$finish->carpet->carpet_id ?? ''}}"
                                class="btn btn-sm btn-info printBTN"><i
              
                                  class="fa fa-pencil"></i>&nbsp; دوباره تیاری</a></td>
                      <td class="hideOnPrint"><a href="/dashboard/finishing-center/{{$finish->id ?? ''}}/edit" class="btn btn-sm btn-info printBTN"><i class="fa fa-pencil"></i>&nbsp; ویرایش</a></td>
                      
                      <td class="hideOnPrint"><a href="/dashboard/finishing-center/{{$finish->id ?? ''}}"
                                                 class="btn btn-sm btn-info printBTN"><i
                                  class="fa fa-eye"></i>&nbsp; نمایش</a></td>
                    </tr>
                    
                    <span style="display: none">
                      @if($finish->carpet->carpet_wash)
                        {{$total_area += $finish->carpet->carpet_wash->area}}
                      @endif
                      
                    
                    </span>
                  
                  @endforeach
                  <tr>
                    
                    
                    <th colspan="2"><b>تعداد</b></th>
                    
                    <td style="direction: ltr"><b>{{$finisheds->count()}} pcs </b></td>
                  
                  
                  </tr>
                  <tr>
                    
                    
                    <th colspan="2"><b>متراژ</b></th>
                    
                    <td style="direction: ltr"><b>{{$total_area}} m <sup>2</sup> </b></td>
                  
                  
                  </tr>
                  </tbody>
                </table>
                
                <p>{{$finisheds->links() ?? ''}}</p>
              
              </div>
            
            </div>
          
          </div>
        
        </div>
      </div>
    </div>
  </div>

@endsection