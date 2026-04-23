@extends('dsh.master')
@section('title' , 'لیست قالین های موجود')
@section('content')

  <div class="row" id="existing-carpet">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <div class="card">
        
        <div class="card-header">
          <h5>لیست قالین های موجود</h5>
         
        </div>
        <div class="card-body">
          
       <div class="row hideOnPrint">
            <div class="col-xs-12 col-lg-9 col-md-9 col-sm-12 hideOnPrint"></div>
            <div class="col-xs-12 col-lg-3 col-md-3 col-sm-12 hideOnPrint">
             
                <a class="btn btn-sm btn-primary btn-block" style="float: right;margin-bottom:10px;color:white;" onclick="printPage('existing-carpet')"><i
                          class="fa fa-print"></i> چاپ
                </a>
             
            </div>
          
          </div>
          <div class="table-responsive">
            <table class="table  table-xs table-hover" id="contract_carpet">
              <thead>
              <tr>
                <th>شماره قالین</th>
            
                <th>شماره فرمایش</th>
                <th>نوعیت</th>
                <th>کوالتی</th>
                <th>نمبر نقشه</th>
                <th>قیمت فی متر</th>
                <th>حاشیه</th>
                <th>زمینه</th>
                <th>طول</th>
                <th>عرض</th>
                <th>مساحت</th>
                <th>تاریخ شروع کار</th>
                <th>تاریخ ختم کار</th>
                <th>حالت</th>
            
              
              
              </tr>
              </thead>
              <tbody>
              @foreach($existing_carpets as $carpet)
               
                    <tr class="ur{{ $carpet->carpet_id }}"> 
                      <td>@if($carpet->carpet_no) {{$carpet->carpet_no}} @else {{$carpet->parcha_number}} @endif</td>
                           <td>{{$carpet->order_number}}</td>
                            <td>{{$carpet->carpet_type}}</td>
                           <td>{{$carpet->quality}}</td>
                           <td>{{$carpet->map_number}}</td>
                             <td style="direction: ltr">{{$carpet->price}} $</td>
                           
                    <td>{{$carpet->margin}}</td>
                      <td>{{$carpet->field}}</td>
                      <td style="direction: ltr">{{$carpet->height}} m</td>
                      <td style="direction: ltr">{{$carpet->width}} m</td>
  
                      <td style="direction: ltr">{{$carpet->area}} m <sup>2</sup></td>
    
                      <td>{{$carpet->date}}</td>
                      <td>{{$carpet->end_date }}</td>
                       <td>
                              @if($carpet->status == 0)
                                <label for="" class="badge badge-success">در نزد نماینده</label>
                              @elseif($carpet->status == 1)
                                <label for="" class="badge badge-warning">در گدام مرکزی</label>
                              @elseif($carpet->status == 2)
                                
                                <label for="" class="badge badge-danger">در کچایی نشده ها</label>
                              @elseif($carpet->status == 12)
                                
                                <label for="" class="badge badge-primary">در کچایی شده ها</label>
                              @elseif($carpet->status == 3)
                                
                                <label for="" class="badge badge-light-warning">در شست نشده ها</label>
                              @elseif($carpet->status == 13)
                                
                                <label for="" class="badge badge-primary">در شست شده ها</label>
                              @elseif($carpet->status == 4)
                                <label for="" class="badge badge-light-primary">در بخش تیاری</label>
                              @elseif($carpet->status == 5)
                                <label for="" class="badge badge-info">در گدام فروشات</label>
                              @elseif($carpet->status == 6)
                                <label for="" class="badge badge-success">فروخته شده</label>
  
                              @endif
                            </td>
                    
                    
                    </tr>
               
              
              @endforeach
              
              <?php
              
                 $total_carpet  = DB::table('carpets')
            ->join('agents','carpets.agent_id','agents.agent_id')
            ->join('users','agents.user_id','users.id')
            ->join('carpet_orders','carpets.order_id','carpet_orders.id')
            ->join('carpet_types','carpets.type_id','carpet_types.carpet_type_id')
            ->join('qualities','carpets.quality_id','qualities.id')

            ->where('status','!=', 6)
            
            ->where('status','!=', 0)
            ->get();
              ?>
            
              @if(!isset($agent_id))
              <tr>
                <th colspan="2">تعداد قالین در این صفحه</th>
                <td style="direction: ltr"> {{$existing_carpets->count()}} Pcs</td>
              </tr>
              <tr>
                <th colspan="2">متراژ قالین در این صفحه</th>
                <td style="direction: ltr"> {{$existing_carpets->sum('area')}} m <sup>2</sup></td>
              </tr>
              <tr>
                <th colspan="2">جمله تعداد قالین موجود</th>
                <td style="direction: ltr"> {{$total_carpet->count()}} Pcs</td>
              </tr>
              <tr>
                <th colspan="2">جمله متراژ قالین موجود</th>
                <td style="direction: ltr"> {{$total_carpet->sum('area')}} m <sup>2</sup></td>
              </tr>
              @endif
              
            
              
              </tbody>
            </table>
        
        
                <div class="hideOnPrint">
              <p class="hideOnPrint">{{$existing_carpets->links()}}</p>
              </div>
         
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

