@extends('dsh.master')
@section('title' , 'لیست قالین های قرار دادی')
@section('content')
  
  <div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <div class="card" id="sales-carpets">
        <div class="card-header">
          <h5>لیست قالین های موجود در دفتر مرکزی</h5>
          <div class="btn btn-sm btn-primary hideOnPrint" style="float: left;" onclick="printPage('sales-carpets')"><i
                    class="fa fa-print"></i> Print
          </div>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-xs table-hover">
              <thead>
              <tr>
                <th>شماره قالین</th>
                <th>اسم نماینده</th>
                <th class="hideOnPrint">اسم کاریگر</th>
                <th>شماره فرمایش</th>
                <th>نوعیت</th>
                <th>کوالتی</th>
                <th class="hideOnPrint">نمبر نقشه</th>
                <th>قیمت فی متر</th>
                <th>قیمت مجموع افغانی</th>
                <th>قیمت مجموع دالر</th>
                
                <th>حاشیه</th>
                <th>زمینه</th>
                <th>طول</th>
                <th>عرض</th>
                <th>مساحت</th>
                <th>تاریخ</th>
                <th class="hideOnPrint">حالت</th>
              
              
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
                  @if($carpet->employee_id)
                    <td class="hideOnPrint">{{$carpet->carpetEmployee->first_name}}</td>
                  @else
                    <td class="hideOnPrint"></td>
                  @endif
                  @if($carpet->carpet_order)
                    <td>{{$carpet->carpet_order->order_number}}</td>
                  @else
                    <td></td>
                  @endif
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
                  <td class="hideOnPrint">{{$carpet->map_number}}</td>
                  <td style="direction: ltr">{{$carpet->price}} $</td>
                  <td style="direction: ltr">{{$carpet->total_price_af}} AF</td>
                  <td style="direction: ltr">{{$carpet->total_price}} $us</td>
                  
                  <td>{{$carpet->margin}}</td>
                  <td>{{$carpet->field}}</td>
                  <td style="direction: ltr">{{$carpet->width}} m</td>
                  <td style="direction: ltr">{{$carpet->height}} m</td>
                  <td style="direction: ltr">{{$carpet->area}} m <sup>2</sup></td>
                  <td>{{$carpet->date}}</td>
                  <td class="hideOnPrint">
                    @if($carpet->status == 3)
                      <label for="" class="badge badge-light-warning">در شست نشده ها</label>
                    @elseif($carpet->status == 13)
                      
                      <label for="" class="badge badge-primary">در شست شده ها</label>
                    @elseif($carpet->status == 4)
                      <label for="" class="badge badge-light-primary">در بخش تیاری</label>
                    @elseif($carpet->status == 5)
                      <label for="" class="badge badge-info">در گدام فروشات</label>
                    @endif
                  </td>
                </tr>
              @endforeach
              <tr>
                <td><b>مجموع تعداد قالین</b></td>
                <td style="direction: ltr">{{$carpets->count()}} pcs</td>
              </tr>
              <tr>
                <td><b>مجموع متراژ قالین</b></td>
                <td style="direction: ltr"> {{$carpets->sum('area')}}  m <sup>2</sup></td>
              </tr>
              </tbody>
            </table>
            {{-- <p>{{$carpets->links()}}</p> --}}
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection