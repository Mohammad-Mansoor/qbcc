@extends('dsh.master')

@section('content')
  <div class="row">
    <div class="col-lg-10 col-md-10 col-sm-10 col-xs-12">
      <div class="card" id="details">
        <div class="card-header">
          <h4 style="text-align:center">مشخصات عمومی قالین</h4>
          <div class="btn btn-sm btn-primary hideOnPrint" style="float: left;" onclick="printPage('details')"><i
                    class="fa fa-print"></i> Print
          </div>
        </div>
        <div class="card-body">
          <div class="static-table-list">
            <table class="table text-center" id="repairShow">
              <thead>
              <tr>
                <th>مشخصات</th>
                <th>مقادیر</th>
                <th>مشخصات</th>
                <th>مقادیر</th>
              </tr>
              </thead>
              <tbody>
              <tr>
                <td>شماره قالین</td>
                <td>{{$finish->carpet->carpet_no}}</td>
                <td>شماره فرمایش</td>
                @if($finish->carpet->carpet_order)
                  <td>{{$finish->carpet->carpet_order->order_number}}</td>
                @else
                  <td></td>
                @endif
              </tr>
              <tr>
                <td>طول قالین</td>
                <td style="direction: ltr;text-align:right">{{ $newCarpet->height }} m</td>
                <td>عرض قالین</td>
                <td style="direction: ltr;text-align:right">{{ $newCarpet->width}} m</td>
              </tr>
              <tr>
                <td>مساحت قالین</td>
                <td style="direction: ltr;text-align:right">{{ $newCarpet->area}} m <sup>2</sup></td>
                <td>شماره نقشه قالین</td>
                <td>{{ $finish->carpet->map_number }}</td>
              </tr>
              <tr>
                <td>زمینه قالین</td>
                <td>{{ $finish->carpet->field }} </td>
                <td>حاشیه قالین</td>
                <td>{{ $finish->carpet->margin }}</td>
              </tr>
              </tbody>
            </table>
          </div>
          <h4 style="text-align:center">مشخصات عمومی تیاری قالین</h4>
          <div class="static-table-list">
            <table class="table text-center" id="repairShow">
              <thead>
              <tr>
                <th>مشخصات</th>
                <th>مقادیر</th>
                <th>مشخصات</th>
                <th>مقادیر</th>
              </tr>
              </thead>
              <tbody>
              <tr>
                <td>هزینه تیاری قالین</td>
                <td style="direction: ltr;text-align:right">{{round($finish->price_af , 2) }} AF</td>
                <td>تاریخ تیاری قالین</td>
                <td>{{$finish->date }}</td>
              </tr>
              <tr>
                <td>تیم تیاری قالین</td>
                <td>{{ $finish->team->name }}</td>
                <td>نوع تیاری قالین</td>
                <td>{{ $finish->category->category }} </td>
              </tr>
              <tr>
                <td>شرح تیاری قالین</td>
                <td>{{ $finish->description }} </td>
              </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    
    
    </div>
  </div>
@endsection
