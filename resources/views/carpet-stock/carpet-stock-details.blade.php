@extends('dsh.master')
@section('title' , 'جزییات قالین')
@section('content')
  
  
  <!-- form -->
  <div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <div class="card">
        <div class="card-header">
          <div class="btn btn-sm btn-primary hideOnPrint" style="float: left"
               onclick="printPage('printCarpet')"><i class="fa fa-print"></i> Print
          </div>
        </div>
        <div class="card-body" id="printCarpet">
          <h4>جزئیات قالین</h4>
          
          <div class="static-table-list table-responsive">
            <table class="table table-xs table-hover">
              <tbody>
              <tr>
                <td>شماره قالین</td>
                <td>{{ $carpet->carpet_no }}</td>
              </tr>
              
              <tr>
                <td>طول</td>
                <td style="direction: ltr;text-align:right">{{ $carpet->height}} m</td>
              </tr>
              <tr>
                <td>عرض</td>
                <td style="direction: ltr;text-align:right">{{ $carpet->width }} m</td>
              </tr>
              <tr>
                <td>مساحت</td>
                <td style="direction: ltr;text-align:right">{{ $carpet->area}} m <sup>2</sup></td>
              </tr>
              <tr>
                <td>زمینه</td>
                <td>{{ $carpet->field }}</td>
              </tr>
              <tr>
                <td>حاشیه</td>
                <td>{{ $carpet->margin }}</td>
              </tr>
              <tr>
                <td>نمبر نقشه</td>
                <td>{{ $carpet->map_number }}</td>
              </tr>
              <tr>
                <td>نوعیت قالین</td>
                @if($carpet->type)
                <td>{{ $carpet->type->carpet_type }}</td>
                  @else
                <td></td>
                  @endif
              </tr>
              <tr>
                <td> قیمت ابتدائی قالین</td>
                <td>{{ $carpet->carpet_price_us }} $</td>
              </tr>
                       @php($tamam_shod = 0)
              <tr>
                <td> پول مصرف شده برای کچایی</td>
                @if($kachaee_expense)
    
                  <td>{{ $kachaee_expense->total_price }} $</td>
                  <span style="display: none">{{$tamam_shod = $carpet->carpet_price_us + $kachaee_expense->total_price + $wash_expense}}</span>
                @else
                  <td>0 $</td>
                  <span style="display: none">{{$tamam_shod = $carpet->carpet_price_us  + $wash_expense}}</span>
                @endif
              </tr>

              <tr>
                <td><h4>جزئیات مصرف</h4></td>
              </tr>
              <tr>
                <td> مصرف شست</td>
                <td>{{$wash_expense}} $</td>
              </tr>
              @php($total_finishing = 0)
              @forelse ($finishing_expense as $finish)
                <tr>
                  <td>مصرف {{$finish->category->category}}</td>
                  <td>{{$finish->price}} $</td>
  
                </tr>
                <span style="display: none">{{$total_finishing =+ $finish->price}}</span>
              @empty
                <tr>
                  <td><p>مصرف تیاری</p></td>
                  <td><p>بخش تیاری کار نشده است</p></td>
                </tr>
              @endforelse
              <tr>
                <td>قیمت تمام شد فی متر</td>
               
                  <td>{{round(($tamam_shod + $total_finishing ) / $carpet->area  , 3)}} $</td>
              </tr>
              <tr>
  
                <td>قیمت تمام شد</td>
                <td>{{$tamam_shod + $total_finishing}} $</td>
              </tr>
              
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
@endsection

