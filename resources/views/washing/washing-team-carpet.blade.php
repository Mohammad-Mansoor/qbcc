@extends('dsh.master')
@section('title' , 'لیست قالین ها')
@section('content')
  <!-- navbar -->
  <div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <div class="breadcome-list single-page-breadcome">
        <div class="row">
          <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
            <div class="breadcome-heading">
            
            </div>
          </div>
          <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
            <ul class="breadcome-menu">
              <li><a href="#">داشبورد</a> <span class="bread-slash">/</span>
              </li>
              <li><span class="bread-blod"> لیست قالین های کار شده توسط {{$team->name}} </span>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  
  </div>
  <div class="row">
    <div class="col-lg-12 col-md-12 col-sm-6 col-xs-12">
      <div class="sparkline8-list">
        <div class="sparkline8-hd">
          <div class="main-sparkline8-hd">
          
          
          </div>
        </div>
        <div class="sparkline8-graph">
          
          <div class="btn btn-primary btn-sm hideOnPrint pull-right" onclick="printPage('MRDetails')"
               style="position: relative;right:20px;"><i class="fa fa-print"></i> Print
          </div>
          <br>
          <br>
          <div class="static-table-list" id="MRDetails">
            <div>
              <h5>لیست قالین های شسته شده توسط {{$team->name}}</h5>
            </div>
            <br>
            <table class="table text-center" id="dataTable">
              <thead>
              <tr>
                
                <th>شماره پارچه</th>
                <th>نوعیت قالین</th>
                <th>شست نمبر</th>
                <th>عرض بعد از شست</th>
                <th>طول بعد از شتس</th>
                <th>مساحت بعد از شست</th>
                <th>قیمت فی متر</th>
                <th>مجموع پول</th>
                <th>تاریخ</th>
                
                
                <!-- <th>حذف</th> -->
              
              </tr>
              </thead>
              <tbody>
              @foreach($carpets as $ca)
                <tr class="ur{{ $ca->carpet->carpet_id }}">
                  
                  <td>{{$ca->carpet->carpet_no}}</td>
                  
                 
                  @if($ca->carpet->type)
                    <td>{{$ca->carpet->type->carpet_type}}</td>
                  @else
                    <td></td>
                  @endif
                  <td>{{$ca->wash_number}}</td>
                  <td>{{$ca->height}} m</td>
                  <td>{{$ca->width}} m</td>
                  <td>{{$ca->area}} m</td>
                  <td>{{$ca->price}}</td>
                  <td>{{$ca->af_total_price}}</td>
                  <td>{{$ca->date}}</td>
                
                
                </tr>
              @endforeach
              
              <tr>
                
                
                
                
                <th colspan="2">مجموع تعداد قالین</th>
                <td></td>
                <td>{{$carpets->count()}}</td>
                <th colspan="2">مجموع پول مصرف شست</th>
                <td></td>
                <td>{{$carpets->sum('af_total_price')}}</td>
                
                <td></td>
                <td></td>
              
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

