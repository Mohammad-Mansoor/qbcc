@extends('dsh.master')

@section('content')
  
  <div class="row">
    <div class="col-lg-12 col-md-12 col-sm-6 col-xs-12">
      <div class="card" id="sale-number-list">
        <div class="card-body">
          
          <div class="row">
            <div class="col-sm-4">
              <div class="sparkline8-graph text-muted">
                
                <div class="table-responsive">
                  <table class="table table-sm table-hover text-right">
                    <thead>
                    
                    </thead>
                    <tbody>
                    
                    <tr>
                      <td style="color: dodgerblue;"><b>SALE DETAILS</b></td>
                      <td style="color: dodgerblue;"><b></b></td>
                    
                    </tr>
                    <tr>
                      <td style="direction: ltr;"><b>{{$sale_number}} {{$agent->user->name}}</b></td>
                      <td style="direction: ltr">Bill #:</td>
                    </tr>
                    <tr>
                      <td><b>{{Carbon\Carbon::today()->format('Y-m-d')}}</b></td>
                      <td style="direction: ltr">Bill DATE:</td>
                    </tr>
                    
                    </tbody>
                  </table>
                </div>
              
              </div>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4"></div>
            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
              <table class="table table-sm table-hover text-right">
                <thead>
                
                </thead>
                <tbody>
                
                <tr>
                  <td style="color: dodgerblue;"><b>{{$agent->user->name}}</b></td>
                  <td style="color: dodgerblue;"><b>TO</b></td>
                
                
                </tr>
                <tr>
                  <td><b>{{$agent->user->name}}</b></td>
                  <td style="direction: ltr">Name</td>
                </tr>
                <tr>
                  <td><b>
                      @foreach($agent->phone as $p)
                        {{$p->phone_no}}
                      @endforeach
                    </b>
                  
                  </td>
                  <td style="direction: ltr">Phone</td>
                </tr>
                <tr>
                  <td><b>{{$agent->address}}</b></td>
                  <td style="direction: ltr">Address</td>
                </tr>
                
                
                </tbody>
              </table>
            </div>
          
          </div>
        </div>
        <div class="row">
          
          
          <div class="col-sm-12">
            <div class="sparkline8-graph text-muted">
              <div class="btn btn-primary btn-sm hideOnPrint " onclick="printPage('sale-number-list')"
                   style="position: relative;float: left;"><i class="fa fa-print"></i> Print
              </div>
              <div class="table-responsive">
                <hr style="height: 3px;width: 100%;color: #0b97c4;background-color: #0b97c4">
                <table class="table table-sm table-hover text-right" style="direction: ltr;font-size: 10px;">
                  <thead>
                  
                  <tr style="direction:rtl">
                    
                    <td><b>ITEM#</b></td>
                    <td><b>ITEM TYPE</b></td>
                    <td><b>QUANTITY</b></td>
                    <td><b>UNIT PRICE</b></td>
                    <td><b>TOTAL AF</b></td>
                    <td><b>TOTAL DOLLAR</b></td>
                  </tr>
                  </thead>
                  
                  <tbody>
                  @php($grand_total_af = 0)
                  @php($grand_total_dollar = 0)
                  @forelse ($sales as $p)
                    <tr style="direction:ltr;">
                      <td>{{$p->category->material_category}}</td>
                      <td>{{$p->type->material_type}}</td>
                      <td style="direction: ltr">{{$p->amount}} KG</td>
                      <td style="direction: ltr">{{$p->price}} AF</td>
                      <td style="direction: ltr">{{$p->total_price_af}} AF</td>
                      <td style="direction: ltr">{{$p->total_price}} $</td>
                      
                      <td style="direction: ltr;display: none;"><span
                                style="display: none">{{$grand_total_af += $p->total_price_af}}</span>{{$p->total_price_af}}
                      </td>
                      <td style="direction: ltr;display: none;"><span
                                style="display: none">{{$grand_total_dollar += $p->total_price}}</span>{{$p->total_price}}
                      </td>
                    
                    </tr>
                  @empty
                    <h4 class="text-info text-center">هنوز موردی ثبت نشده است</h4>
                  @endforelse
                  
                  
                  </tbody>
                </table>
                <hr style="height: 3px;width: 100%;color: #0b97c4;background-color: #0b97c4">
              
              </div>
            
            </div>
          </div>
        </div>
        <div class="row">
          
          <div class="col-lg-3 col-md-3 col-sm-3 col-xs-4">
            <table style="direction: ltr" class="table table-sm">
              <tbody>
              <tr style="direction: ltr">
                <td style="font-size: 12px;color: #0b97c4">QUANTITY</td>
                <td> &nbsp;{{$sales->sum('amount')}} kg</td>
              </tr>
              <tr style="direction: ltr">
                <td style="font-size: 12px;color: #0b97c4">GRAND TOTAL AF</td>
                <td>AF &nbsp;{{$grand_total_af}}</td>
              </tr>
              <tr style="direction: ltr">
                <td style="font-size: 12px;color: #0b97c4">GRAND TOTAL DOLLAR</td>
                <td>$ &nbsp;{{$grand_total_dollar}}</td>
              </tr>
              </tbody>
            </table>
          </div>
          <div class="col-lg-9 col-md-9 col-sm-9 col-xs-8">
          
          </div>
        </div>
      </div>
    </div>
  </div>

@endsection
