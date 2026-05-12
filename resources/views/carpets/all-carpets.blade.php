@extends('dsh.master')
@section('title' , 'لیست قالین ها')
@section('content')
  <!-- navbar -->
  <div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <div class="card">
        <div class="card-header">
          <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-8 col-xs-8">
                <form action="/dashboard/filter-based-carpet-type" method="POST" id="dateSearch">
                  @csrf
                  <label for="">جستجو به اساس نوعیت</label>
                  <select name="carpet_type" id="agent_id" class="form-control" onchange="this.form.submit();" required>
                    <option value="">~~~</option>
                    @foreach($carpet_types as $ag)
                      <option value="{{$ag->carpet_type_id}}">{{$ag->carpet_type}}</option>
                    @endforeach
                  </select>
                </form>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                <form action="/dashboard/view-carpets/search" method="POST">
                  @csrf
                  <label for="">جستجو شماره قالین</label>
                  <input type="text" value="{{ Request::old('search') }}" name="search"
                         placeholder=" جستجو شماره قالین" class="form-control" required>
                </form>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
              <div class="btn btn-primary btn-sm hideOnPrint" onclick="printPage('MRDetails')"
                   style="float: left;"><i class="fa fa-print"></i> Print
              </div>
              &nbsp;&nbsp;
  
              <a href="/dashboard/view-carpets" class="btn btn-primary btn-sm" style="float: left"><i
                        class="fa fa-eye"></i> نمایش تمام قالین ها</a>
            </div>
          </div>
        </div>
        <div class="card-body">
          <div class="static-table-list table-responsive" id="MRDetails">
            <table class="table table-hover table-xs">
              <thead>
              <tr>
        
                <th>شماره قالین</th>
                <th>اسم نماینده</th>
                <th class="hideOnPrint">اسم کاریگر</th>
                <th>شماره فرمایش</th>
                <th>نوعیت</th>
                <th class="hideOnPrint">نمبر نقشه</th>
                <th>قیمت فی متر</th>
                <th>قیمت مجموع</th>
                <th>حاشیه</th>
                <th>زمینه</th>
                <th>طول</th>
                <th>عرض</th>
                <th>مساحت</th>
                <th>تاریخ</th>
                <th class="hideOnPrint">حالت</th>
        
                <!-- <th>حذف</th> -->
      
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
                  <td class="hideOnPrint">{{$carpet->map_number}}</td>
                  <td style="direction: ltr"> {{$carpet->price}} $
          
                  </td>
                  <td style="direction: ltr">{{$carpet->total_price}}  $
                  
                  </td>
                  <td>{{$carpet->margin}} </td>
                  <td>{{$carpet->field}}</td>
                  <td style="direction: ltr">{{$carpet->width}} m</td>
                  <td style="direction: ltr">{{$carpet->height}} </td>
                  <td style="direction: ltr">{{$carpet->area}} m <sup>2</sup></td>
                  <td>{{$carpet->date}}</td>
                  <td class="hideOnPrint">
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
            <p>{{$carpets->links()}}</p>
          </div>
        </div>
      </div>
    </div>
  
  </div>
@endsection

