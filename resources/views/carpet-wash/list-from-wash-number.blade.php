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
                      
                      <td><b>{{$wash_number}}</b></td>
                      <td>Bill#:</td>
                    </tr>
                    <tr style="direction: ltr;">
                      
                      <td><b>{{$wash_date->created_at->format('Y-m-d')}}</b></td>
                      
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


                    <?php
                    $none_washed_total = DB::table('carpets')->join('carpet_washes', 'carpets.carpet_id', 'carpet_washes.carpetId')->where('carpet_washes.team_id', $team->id)->where('carpets.status', 3)->get();

                    ?>
                    <tr style="direction: ltr;text-align: left">
                      @if($none_washed_total)
                        <td><b>{{$none_washed_total->count()}}</b></td>
                      @else
                        <td><b>0</b></td>
                      @endif
                      
                      <td>None Washed Total:</td>
                    </tr>
                    </tbody>
                  </table>
                </div>
              
              </div>
            </div>
          </div>
          <div class="card-body">
            <div class="alert alert-success" style="display:none;" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
              جزئیات حذف شد
            </div>
            
            @if(session("status"))
              <div class="alert alert-success status text-center" style="display:none;" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                          aria-hidden="true">&times;</span></button>
                {{session('status')}}
              </div>
            
            @endif
            @if(session("error"))
              
              <div class="alert alert-danger status text-center" style="display:none;" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                          aria-hidden="true">&times;</span></button>
                {{session('error')}}
              </div>
            
            @endif
            <div class="row">
              <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 hideOnPrint">
                <form action="/dashboard/search-wash-number-for-wash" method="POST">
                  @csrf
                  
                  <div class="row">
                    <div class="col-lg-5 col-md-5 col-sm-5 col-xs-5 hideOnPrint">
                      <input type="hidden" name="team_id" value="{{$team->id}}">
                      
                      <label for="">شست نمبر ها</label>
                      <select name="wash_number" id="" class="form-control">
                        @foreach($wash_numbers as $w)
                          <option {{($w->wash_number == $wash_number ? 'selected' : '')}} value="{{$w->wash_number}}">{{$w->wash_number}}</option>
                        @endforeach
                      </select>
                    </div>
                    <div class="col-lg-5 col-md-5 col-sm-5 col-xs-5 hideOnPrint">
                      
                      
                      <label for="">شسته شده و نشسته ها</label>
                      <select name="wash_nonwash" id="" class="form-control">
                        <option value="all">همه</option>
                        <option value="washed">شسته شده ها</option>
                        <option value="nonwashed">نشسته ها</option>
                      </select>
                    </div>
                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 hideOnPrint" style="margin-top: 40px">
                      <button type="submit" class="btn btn-sm btn-primary btn-block">جستجو</button>
                    </div>
                  </div>
                
                </form>
              </div>
              <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 hideOnPrint">
                <form action="/dashboard/search-wash-number-for-wash" method="POST" id="dateSearch">
                  @csrf
                  <input type="hidden" name="team_id" value="{{$team->id}}">
                  <input type="hidden" name="wash_number" value="{{$wash_number}}">
                  <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-21">
                      <span class="date-label">جستجو نمبر قالین</span><input type="text"
                                                                             value="{{ Request::old('search') }}"
                                                                             name="search" class="form-control"
                                                                             placeholder="چستجو نمبر قالین"
                                                                             required>
                    </div>
                  </div>
                </form>
              </div>
              <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                <form action="/dashboard/search-carpet-type-from-wash-number" method="POST" id="dateSearch">
                  @csrf
                  <input type="hidden" name="team_id" value="{{$team->id}}">
                  <input type="hidden" name="wash_number" value="{{$wash_number}}">
                  <div class="row">
                      <?php
                      $carpet_types = \App\CarpetType::all();
                      ?>
                    <span class="date-label">جستجو نوعیت</span>
                    <select name="carpet_type_id" id="" class="form-control" onchange="this.form.submit()">
              <option value="">جستجو نوعیت</option>
                      @foreach($carpet_types as $type)
                        <option value="{{$type->carpet_type_id}}">{{$type->carpet_type}}</option>
                      @endforeach
                    </select>
      
                  </div>
                </form>
              </div>
              
              <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 hideOnPrint">
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
                  <td class="hideOnPrint"><b>WASH</b></td>
                  <td>RETURN</td>
                </tr>
                </thead>
                
                <tbody>
                @php($washed = 0)
                @php($nonwashed = 0)
                @php($washed_total_area = 0)
                @php($nonwashed_total_area = 0)
                @forelse ($carpet_washes as $wash)
                  @if($wash_check == 'washed')
                    @if($wash->carpet->status == 13  || $wash->carpet->status != 3)
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
                        @if($wash->carpet->status == 13  || $wash->carpet->status != 3)
                          <span style="display: none;">{{$washed ++}}</span>
                        @elseif($wash->carpet->status == 3)
                          <span style="display: none;">{{$nonwashed ++}}</span>
                        @endif
                        <td style="direction: ltr">{{$wash->carpet->field}} m</td>
                        
                        @if($wash->carpet->status == 13)
                          <td>washed</td>
                        @elseif($wash->carpet->status == 3)
                          @if(auth()->user()->role == 'SO' || auth()->user()->role == 'SCO')
                            <td class="hideOnPrint"><a href="/dashboard/carpet-wash/create/{{$wash->id}}"
                                                       class="btn btn-sm btn-info printBTN"><i
                                        class="fa fa-pencil"></i>&nbsp; wash</a></td>
                          @else
                            <td>None Washed</td>
                          @endif
                        @else
                          <td>از بخش شست رفته</td>
                        @endif
                      
                      
                      </tr>
                    @endif
                  @elseif($wash_check == 'nonwashed')
  
                    @if($wash->carpet->status == 3)
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
                        @if($wash->carpet->status == 13  || $wash->carpet->status != 3)
                          <span style="display: none;">{{$washed ++}}</span>
                        @elseif($wash->carpet->status == 3)
                          <span style="display: none;">{{$nonwashed ++}}</span>
                        @endif
                        <td style="direction: ltr">{{$wash->carpet->field}} m</td>
      
                        @if($wash->carpet->status == 13)
                          <td>washed</td>
                        @elseif($wash->carpet->status == 3)
                          @if(auth()->user()->role == 'SO' || auth()->user()->role == 'SCO')
                            <td class="hideOnPrint"><a href="/dashboard/carpet-wash/create/{{$wash->id}}"
                                                       class="btn btn-sm btn-info printBTN"><i
                                        class="fa fa-pencil"></i>&nbsp; wash</a></td>
                          @else
                            <td>None Washed</td>
                          @endif
                        @else
                          <td>از بخش شست رفته</td>
                        @endif
    
    
                      </tr>
                    @endif
                  @else
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
                      @if($wash->carpet->status == 13  || $wash->carpet->status != 3)
                        <span style="display: none;">{{$washed ++}}</span>
                      @elseif($wash->carpet->status == 3)
                        <span style="display: none;">{{$nonwashed ++}}</span>
                      @endif
                      <td style="direction: ltr">{{$wash->carpet->field}} m</td>
    
                      @if($wash->carpet->status == 13)
                        <td>washed</td>
                      @elseif($wash->carpet->status == 3)
                        @if(auth()->user()->role == 'SO' || auth()->user()->role == 'SCO')
                          <td class="hideOnPrint"><a href="/dashboard/carpet-wash/create/{{$wash->id}}"
                                                     class="btn btn-sm btn-info printBTN"><i
                                      class="fa fa-pencil"></i>&nbsp; wash</a></td>
                        @else
                          <td>None Washed</td>
                        @endif
                      @else
                        <td>از بخش شست رفته</td>
                      @endif
  
  
                    </tr>
                  @endif
                @empty
                  <h4 class="text-info text-center">هنوز موردی ثبت نشده است</h4>
                @endforelse
                
                
                </tbody>
              </table>
              <hr style="height: 3px;width: 100%;color: #0b97c4;background-color: #0b97c4">
            
            </div>
            <div class="row">
              
              <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                  <?php
                  if ($wash_check == 'washed'){
                      $carpets = DB::table('carpets')->join('carpet_washes', 'carpets.carpet_id', 'carpet_washes.carpetId')->where('carpet_washes.team_id', $team->id)->where('carpet_washes.wash_number', $wash_number)->where('carpets.status','!=', 3)->get();

                  }else if ($wash_check == 'nonwashed'){
                      $carpets = DB::table('carpets')->join('carpet_washes', 'carpets.carpet_id', 'carpet_washes.carpetId')->where('carpet_washes.team_id', $team->id)->where('carpet_washes.wash_number', $wash_number)->where('carpets.status', 3)->get();

                  }else{
                      $carpets = DB::table('carpets')->join('carpet_washes', 'carpets.carpet_id', 'carpet_washes.carpetId')->where('carpet_washes.team_id', $team->id)->where('carpet_washes.wash_number', $wash_number)->get();

                  }

                  ?>
                <table class="table table-xs table-hover">
                  <tbody>
                  <tr>
                    <td>&nbsp;{{$carpets->count()}} pcs</td>
                    <td style="font-size: 12px;color: #0b97c4">QUANTITY</td>
                  
                  </tr>
                  <tr>
                    <td>m <sup>2</sup> &nbsp;{{$washed_total_area + $nonwashed_total_area}}</td>
                    <td style="font-size: 12px;color: #0b97c4">TOTAL</td>
                  
                  </tr>
                  
                  <tr>
                    
                    <td>{{$washed}} pcs</td>
                    <td style="font-size: 12px;color: #0b97c4">WASHED</td>
                  </tr>
                  <tr>
                    
                    <td>{{$nonwashed}} pcs</td>
                    <td style="font-size: 12px;color: #0b97c4">NONE WASHED</td>
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
