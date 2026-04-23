@extends('dsh.master')

@section('content')
  
  <br>
  <!-- navbar -->
  
  <div id="PaidToDA">
    
    <div class="row">
      <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
          <div class="card-header">
            <div class="row">
              <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                
                
                <div class="table-responsive">
                  <table class="table table-xs table-hover">
                    <thead>
                    
                    </thead>
                    <tbody>
                    
                    <tr>
                      <td style="color: dodgerblue;font-size: 15px;"><b>BILL DETAILS</b></td>
                      <td style="color: dodgerblue;"><b></b></td>
                    </tr>
                    
                    <tr style="direction: ltr;">
                      
                      <td><b>{{$wash_number_sh}}</b></td>
                      <td>Bill#:</td>
                    </tr>
                    <tr style="direction: ltr;">
                      
                      <td><b>{{$wash_date->date}}</b></td>
                      <td>Bill Date:</td>
                    </tr>
                    
                    </tbody>
                  </table>
                </div>
              </div>
                  <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3"></div>
              
             <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                <div class="table-responsive">
                  <table class="table table-xs table-hover">
                    <thead>
                    
                    </thead>
                    <tbody>
                    <tr style="direction: ltr;text-align: left;font-size: 15px;">
                      
                      <td style="color: dodgerblue;"><b>{{$team->name}}</b></td>
                      <td style="color: dodgerblue;"><b>To</b></td>
                    </tr>
                   
                    <tr style="direction: ltr;text-align: left">
                      <td><b>{{$team->address}}</b></td>
                      <td>Address :</td>
                    </tr>
                    <tr style="direction: ltr;text-align: left">
                      <td><b>{{$team->contact_no}}</b></td>
                      <td>Phone :</td>
                    </tr>
                    </tbody>
                  </table>
                </div>
              
              </div>
            </div>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 hideOnPrint">
                <form action="/dashboard/search-wash-number-sh-for-wash" method="POST">
                  @csrf
                  <input type="hidden" name="team_id" value="{{$team->id}}">
        
                  <label for="">شست نمبر ها</label>
                  <select name="wash_number_sh" id="" class="form-control" onchange="this.form.submit()">
                    @foreach($wash_numbers as $w)
                      <option {{($w->wash_number_sh == $wash_number_sh ? 'selected' : '')}} value="{{$w->wash_number_sh}}">{{$w->wash_number_sh}}</option>
                    @endforeach
                  </select>
      
                </form>
              </div>
              <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 hideOnPrint">
                <form action="/dashboard/search-wash-number-sh-for-wash" method="POST" id="dateSearch">
                  @csrf
                  <input type="hidden" name="team_id" value="{{$team->id}}">
                  <input type="hidden" name="wash_number_sh" value="{{$wash_number_sh}}">
                  <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-21">
                      <span class="date-label">جستجو نمبر قالین</span><input type="text"
                                                                             value="{{ Request::old('search') }}"
                                                                             name="search" class="form-control" placeholder="چستجو نمبر قالین"
                                                                             required>
                    </div>
                  </div>
                </form>
              </div>
              <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 hideOnPrint">
              <div class="btn btn-primary btn-sm hideOnPrint" onclick="printPage('PaidToDA')"
                   style="float: left"><i class="fa fa-print"></i> Print
              </div>
              </div>
            </div>
           
            <div class="table-responsive">
              <hr style="height: 3px;width: 100%;color: #0b97c4;background-color: #0b97c4">
              <table class="table table-xs table-hover" style="direction: ltr;text-align: left">
                <thead>
                
                <tr style="color: dodgerblue;">
                  
                  <td><b>ITEM#</b></td>
                  <td><b>ITEM DESCRIPTION</b></td>
                  <td><b>HEIGHT</b></td>
                  <td><b>WIDTH</b></td>
                  <td><b>AREA</b></td>
                  <td><b>BACKGROUND</b></td>
                  <td><b>UNIT PRICE</b></td>
                  <td><b>TOTAL</b></td>
                    <td class="hideOnPrint"><b>WASH</b></td>
                </tr>
                </thead>
                
                <tbody>
                @php($washed = 0)
                @php($nonwashed = 0)
                @php($washed_total_area = 0)
                @php($nonwashed_total_area = 0)
                @php($total_price = 0)
                @forelse ($carpet_washes as $wash)
                  <tr>
                    <td>{{$wash->carpet->carpet_no}}</td>
                    @if($wash->carpet->type)
                    <td>{{$wash->carpet->type->carpet_type}}</td>
                    @else
                    <td></td>
                    @endif
                    @if($wash->height)
                      <td style="direction: ltr">{{$wash->height}} m</td>
                    @else
                      <td style="direction: ltr">{{$wash->carpet->height}} m</td>
                    @endif
                    @if($wash->width)
                      <td style="direction: ltr">{{$wash->width}} m</td>
                    @else
                      <td style="direction: ltr">{{$wash->carpet->width}} m</td>
                    @endif
                    @if($wash->area)
                      <span style="display: none">{{$washed_total_area += $wash->area}}</span>
                      <td style="direction: ltr">{{$wash->area}} m <sup>2</sup></td>
                    @else

                      <td style="direction: ltr">{{$wash->carpet->area}} m <sup>2</sup></td>
                      <span style="display: none">{{$nonwashed_total_area += $wash->carpet->area}}</span>
                    @endif
                    @if($wash->carpet->status == 13)
                      <span style="display: none;">{{$washed ++}}</span>
                    @elseif($wash->carpet->status == 3)
                      <span style="display: none;">{{$nonwashed ++}}</span>
                    @endif
                    <td style="direction: ltr">{{$wash->carpet->field}} </td>
                    <td style="direction: ltr">{{$wash->price}} AF </td>
                    <td style="direction: ltr">{{round($wash->af_total_price , 2)}} AF </td>
                      <span style="display: none">{{$total_price += round($wash->af_total_price , 2)}}</span>

          
                    
                    @if($wash->carpet->status == 13)
                      <td>washed</td>
                    @elseif($wash->carpet->status == 3)
                      @if(auth()->user()->role == 'SO' || auth()->user()->role == 'SCO')
                        <td><a href="/dashboard/carpet-wash/create/{{$wash->id}}"
                               class="btn btn-sm btn-info printBTN"><i
                                    class="fa fa-pencil"></i>&nbsp; wash</a></td>
                      @else
                        <td>None Washed</td>
                      @endif
                    @else
                      <td>از بخش شست رفته</td>
                    @endif



                  </tr>
                @empty
                  <h4 class="text-info text-center">هنوز موردی ثبت نشده است</h4>
                @endforelse
                
                
                </tbody>
              </table>
              <hr style="height: 3px;width: 100%;color: #0b97c4;background-color: #0b97c4">
            
            </div>
            <div class="row">
              
              <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                
                <table class="table table-xs table-hover">
                  <tbody>
                  <tr>
                    <td style="direction: ltr">&nbsp;{{$carpet_washes->count()}} pcs</td>
                    <td style="font-size: 12px;color: #0b97c4">QUANTITY</td>
                  
                  </tr>
                  <tr>
                    <td style="direction: ltr"> {{$washed_total_area + $nonwashed_total_area}}&nbsp; m <sup>2</sup></td>
                    <td style="font-size: 12px;color: #0b97c4">TOTAL</td>
                  
                  </tr>

                  <tr>
                    <td style="direction: ltr">{{$total_price}} AF</td>
                    <td style="font-size: 12px;color: #0b97c4">TOTAL AMOUNT</td>

                  </tr>



                  </tbody>
                </table>
              </div>
              <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
              
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  
  </div>

@endsection
