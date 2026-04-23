@extends('dsh.master')

@section('content')
  <!-- navbar -->
  <div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <div class="card">
        <div class="card-header">
          <div class="main-sparkline8-hd">
            
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
                <a class="nav-link has-ripple {{$search == null ? 'active' : ''}}" id="pills-non-repaired-tab"
                   data-toggle="pill"
                   href="#non-repaired"
                   role="tab" aria-controls="pills-non-repaired" aria-selected="true">ترمیم نشده ها<span
                          class="ripple ripple-animate"
                          style="height: 71.6719px; width: 71.6719px; animation-duration: 0.7s; animation-timing-function: linear; background: rgb(70, 128, 255); opacity: 0.4; top: -30.8359px; left: 9.16405px;"></span></a>
              </li>
              <li class="nav-item">
                <a class="nav-link has-ripple {{$search != null ? 'active' : ''}}" id="pills-repaired-tab"
                   data-toggle="pill" href="#repaired"
                   role="tab" aria-controls="pills-repaired" aria-selected="false">ترمیم شده ها<span
                          class="ripple ripple-animate"
                          style="height: 82.2188px; width: 82.2188px; animation-duration: 0.7s; animation-timing-function: linear; background: rgb(70, 128, 255); opacity: 0.4; top: -15.1094px; left: 18.9375px;"></span></a>
              </li>
            </ul>
            <div class="tab-content" id="pills-tabContent">
              
              <div class="tab-pane fade {{$search == null ? 'active show' : ''}}" id="non-repaired" role="tabpanel"
                   aria-labelledby="pills-non-repaired-tab">
                <div class="row">
                  <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                    <div class="form-group fill">
                      <form action="/dashboard/repair-search" method="POST">
                        @csrf
                        <input type="text" name="search" placeholder=" جستجو شماره قالین" class="form-control" required>
                      </form>
                    </div>
                  </div>
                  <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9">
                  
                  </div>
                
                
                </div>
                <div class="btn btn-sm btn-primary" style="float: left;margin-bottom: 20px;"
                     onclick="printPage('noneRepairPrint')"><i
                          class="fa fa-print"></i> Print
                </div>
                <div class="static-table-list" style="margin-top: 20px" id="noneRepairPrint">
                  <table class="table table-hover table-xs" id="dataTable">
                    <thead>
                    <tr>
                      
                      <th>شماره قالین</th>
                      <th>اسم کچایی گر</th>
                      <th>نوعیت</th>
                      <th>طول</th>
                      <th>عرض</th>
                      <th>حاشیه</th>
                      <th>زمینه</th>
                      <th>شماره فرمایش</th>
                      <th class="printTitle">ترمیم</th>
                      <th class="printTitle">بازگشت</th>
                    
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($nonrepaireds as $nonrepaired)
                      <tr class="ur{{ $nonrepaired->carpet_id }}">
                        
                        <td>{{$nonrepaired->carpet_no}}</td>
                        <td>{{$nonrepaired->kachaee->name}}</td>
                        <td>{{$nonrepaired->type->carpet_type}}</td>
                        <td>{{$nonrepaired->height}}</td>
                        <td>{{$nonrepaired->width}}</td>
                        <td>{{$nonrepaired->margin}}</td>
                        <td>{{$nonrepaired->field}}</td>
                        @if($nonrepaired->carpet_order)
                          <td>{{$nonrepaired->carpet_order->order_number}}</td>
                        @else
                          <td></td>
                        @endif
                        <td class="hideOnPrint"><a href="/dashboard/carpet-repair-create/{{$nonrepaired->carpet_id}}"
                               class="btn btn-sm btn-info printBTN"><i
                                    class="fa fa-pencil"></i>&nbsp; ترمیم</a></td>
                        
                        <td class="hideOnPrint"><a href="/dashboard/return-to-center-from-non-repair/{{$nonrepaired->carpet_id}}"
                               class="btn btn-sm btn-warning printBTN"><i
                                    class="fa fa-pencil"></i>&nbsp; بازگشت به مرکزی</a></td>
                      </tr>
                    @endforeach
                    <tr>
                      <td><b>مجموع</b></td>
                      <td>m <sup>2</sup> {{$nonrepaireds->sum('area')}}</td>
                    
                    </tr>
                    <tr>
                      <td><b>تعداد</b></td>
                      <td>pcs {{$nonrepaireds->count()}}</td>
                    </tr>
                    </tbody>
                  </table>
                
                </div>
              
              
              </div>
              <div class="tab-pane fade {{$search != null ? 'active show' : ''}}" id="repaired" role="tabpanel"
                   aria-labelledby="pills-repaired-tab">
                <div class="row">
                  <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                    <div class="form-group fill">
                      <form action="/dashboard/search-repaired" method="POST">
                        @csrf
                        <input type="text" name="search" placeholder=" جستجو " class="form-control" required>
                      </form>
                    </div>
                  </div>
                  <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9">
                    
                    <form action="/dashboard/repair-date-search" method="POST">
                      @csrf
                      <div class="row">
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2"></div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                          <div class="form-group fill">
                            <input type="submit" value="جستجو" style="float: left;margin-top: 30px;"
                                   class="date-submit btn btn-sm btn-primary btn-block">
                          </div>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-8">
                          <div class="form-group fill">
                            <span class="date-label">شروع</span><input type="date" name="start" class="form-control"
                                                                       required>
                          </div>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-8">
                          <div class="form-group fill">
                            <span class="date-label">ختم</span><input type="date" name="end" class="form-control"
                                                                      required>
                          </div>
                        </div>
                      </div>
                    </form>
                  
                  </div>
                
                
                </div>
                <div class="btn btn-sm btn-primary" style="float: left;margin-bottom: 20px;"
                     onclick="printPage('repairPrint')"><i
                          class="fa fa-print"></i> Print
                </div>
                <div class="static-table-list" style="margin-top: 20px" id="repairPrint">
                  <table class="table table-hover table-xs" id="secondDataTable">
                    <thead>
                    <tr>
                      
                      <th>شماره قالین</th>
                      <th>نمبر کچایی</th>
                      <th>نوعیت</th>
                      <th>حاشیه</th>
                      <th>زمینه</th>
                      <th>قیمت فی متر</th>
                      <th>قیمت مجموع</th>
                      <th>تاریخ</th>
                      <th>تیم ترمیم کننده</th>
                      <th>شرح</th>
                      <th class="printTitle">ویرایش</th>
                      <th class="printTitle">ارسال به شست</th>
                      <th class="printTitle">جزئیات کلی</th>
                      <th class="printTitle">بازگشت</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($repaireds as $repaired)
                      <tr class="ur{{ $repaired->id }}">
                        <td>{{$repaired->carpet->carpet_no}}</td>
                        <td>
                          <a href="/dashboard/carpet-repair/search-kachaee-number/{{$repaired->kachaee_number}}{{$repaired->team_id}}"
                          >&nbsp; {{$repaired->kachaee_number}}</a></td>
                        <td>{{$repaired->carpet->type->carpet_type}}</td>
                        <td>{{$repaired->carpet->margin}}</td>
                        <td>{{$repaired->carpet->field}}</td>
                        
                        <td style="direction: ltr">{{$repaired->price}} af</td>
                        <td style="direction: ltr">{{$repaired->af_total_price}} af</td>
                        <td>{{$repaired->date}}</td>
                        <td>{{$repaired->team->name}}</td>
                        <td>{{$repaired->description}}</td>
                        <td class="hideOnPrint"><a href="/dashboard/carpet-repair/{{$repaired->id}}/edit"
                               class="btn btn-sm btn-info printBTN"><i
                                    class="fa fa-pencil"></i>&nbsp; ویرایش</a></td>
                        @if ($repaired->carpet->status ==12)
                          <td class="hideOnPrint"><a href="/dashboard/washing-team/sending-to-washing/{{$repaired->carpet->carpet_id}}"
                                 class="btn btn-sm btn-info printBTN"><i
                                      class="fa fa-send"></i>&nbsp; ارسال به شست</a></td>
                        @else
                          <td class="hideOnPrint">قبلا ارسال شده</td>
                        @endif
                        <td><a href="/dashboard/carpet-repair/{{$repaired->id}}" class="btn btn-sm btn-info printBTN"><i
                                    class="fa fa-eye"></i>&nbsp; نمایش</a></td>
                        @if($repaired->carpet->status == 12)
                          <td><a href="/dashboard/return-to-center-from-repair/{{$repaired->carpetId}}"
                                 class="btn btn-sm btn-warning printBTN"><i
                                      class="fa fa-pencil"></i>&nbsp; بازگشت به مرکزی</a></td>
                        @else
                        
                          <td class="hideOnPrint">
      
                            @if($repaired->carpet->status == 3)
        
                              <label for="" class="badge badge-light-warning">در شست نشده ها</label>
                            @elseif($repaired->carpet->status == 13)
        
                              <label for="" class="badge badge-primary">در شست شده ها</label>
                            @elseif($repaired->carpet->status == 4)
                              <label for="" class="badge badge-light-primary">در بخش تیاری</label>
                            @elseif($repaired->carpet->status == 5)
                              <label for="" class="badge badge-info">در گدام فروشات</label>
                            @elseif($repaired->carpet->status == 6)
                              <label for="" class="badge badge-success">فروخته شده</label>
      
                            @endif
                          </td>
                   
                        @endif
                      </tr>
                    @endforeach
                    <tr>
                      <td><b>مجموع</b></td>
                      <td>m <sup>2</sup> {{$repaireds->sum('area')}}</td>
                    
                    </tr>
                    <tr>
                      <td><b>تعداد</b></td>
                      <td>pcs {{$repaireds->count()}}</td>
                    </tr>
                    </tbody>
                  </table>
                  @if(!isset($search))
                    <p>{{$repaireds->links()}}</p>
                  @endif
                
                </div>
              </div>
            
            </div>
          
          </div>
        </div>
      </div>
    </div>
  
  
  </div>
@endsection