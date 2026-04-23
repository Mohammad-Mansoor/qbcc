@extends('dsh.master')

@section('content')
  
  <br>
  <!-- navbar -->
  
  <div id="PaidToDA">
    
    <div class="row">
      <div class="col-lg-12 col-md-12 col-sm-6 col-xs-12">
        <div class="card">
          <div class="card-header">
            <div class="row">
              <div class="col-sm-4">
                <div class="sparkline8-graph text-muted">
        
                  <div class="table-responsive">
                    <table class="table table-sm table-hover text-right">
                      <thead>
            
                      </thead>
                      <tbody>
            
                      <tr>
                        <td style="color: dodgerblue;"><b>INVOICE DETAILS</b></td>
                        <td style="color: dodgerblue;"><b></b></td>
            
                      </tr>
                      <tr>
                        <td style="direction: ltr;"><b>{{$invoice_number}} {{$customer->name}}</b></td>
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
                    <td style="color: dodgerblue;"><b>{{$customer->name}}</b></td>
                    <td style="color: dodgerblue;"><b>TO</b></td>
        
        
                  </tr>
                  <tr>
                    <td><b>{{$customer->name}}</b></td>
                    <td style="direction: ltr">Name</td>
                  </tr>
                  <tr>
                    <td><b>{{$customer->phone}}</b></td>
                    <td style="direction: ltr">Phone</td>
                  </tr>
                  <tr>
                    <td><b>{{$customer->email}}</b></td>
                    <td style="direction: ltr">Email:</td>
                  </tr>
                  <tr>
                    <td><b>{{$customer->company_address}}</b></td>
                    <td style="direction: ltr">Address</td>
                  </tr>
        
        
                  </tbody>
                </table>
              </div>
  
            </div>
            <div class="btn btn-primary btn-sm hideOnPrint" onclick="printPage('PaidToDA')"
                 style="position: relative;float: left"><i class="fa fa-print"></i> Print
            </div>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <hr style="height: 3px;width: 100%;color: #0b97c4;background-color: #0b97c4">
              <table class="table table-sm table-hover " style="direction: ltr;text-align: left">
                <thead>
      
                <tr>
        
                  <td><b>ITEM#</b></td>
                  <td><b>ITEM DESCRIPTION</b></td>
                  <td><b>HEIGHT</b></td>
                  <td><b>WIDTH</b></td>
                  <td><b>AREA</b></td>
                  <td><b>UNIT PRICE </b></td>
                  <td><b>TOTAL PRICE</b></td>
      
      
                </tr>
                </thead>
      
                <tbody>
                @php($grand_total = 0)
                @php($area = 0)
                @php($quantity = 0)
                @forelse ($sales as $sale)
              
                    <tr style="direction:ltr;">
                      <td>{{$sale->carpet->carpet_no}}</td>
                      <td>{{$sale->type}}</td>
                      @if($sale->carpet->carpet_wash)
                      <td style="direction: ltr">{{$sale->carpet->carpet_wash->height}} m</td>
                      <td style="direction: ltr">{{$sale->carpet->carpet_wash->width}} m</td>
                      @else
                        <td style="direction: ltr">{{$sale->carpet->height}} m</td>
                        <td style="direction: ltr">{{$sale->carpet->width}} m</td>
                      @endif
                      @if($sale->carpet->carpet_wash)
                      <td style="direction: ltr"><p
                                style="display: none;">{{$quantity ++}} {{$area += $sale->carpet->carpet_wash->area}}</p>{{$sale->carpet->carpet_wash->area}}
                        m <sup>2</sup></td>
                      @else
                      <td style="direction: ltr"><p
                                style="display: none;">{{$quantity ++}} {{$area += $sale->carpet->area}}</p>{{$sale->carpet->area}}
                        m <sup>2</sup></td>
                      @endif
                      <td style="direction: ltr">{{$sale->sale_cost_per_meter}} $</td>
                      <td style="direction: ltr"><p
                                style="display: none;">{{$grand_total += $sale->sale_cost_total}}</p>{{$sale->sale_cost_total}}
                        $
                      </td>
          
                    
                    </tr>
        
                @empty
                  <h4 class="text-info text-center">هنوز موردی ثبت نشده است</h4>
                @endforelse
      
      
                </tbody>
              </table>
              <hr style="height: 3px;width: 100%;color: #0b97c4;background-color: #0b97c4">
  
            </div>
            <div class="row">
              <div class="col-lg-3 col-md-3 col-sm-3 col-xs-4">
                <table style="direction: ltr" class="table table-sm">
                  <tbody>
                  <tr style="direction: ltr">
                    <td style="font-size: 12px;color: #0b97c4">QUANTITY</td>
                
                    <td>Pcs &nbsp;{{$quantity}}</td>
                  </tr>
                  <tr style="direction: ltr">
                    <td style="font-size: 12px;color: #0b97c4">TOTAL</td>
                    <td>m <sup>2</sup> &nbsp;{{$area}}</td>
                  </tr>
      
                  <tr style="direction: ltr">
                    <td style="font-size: 12px;color: #0b97c4">GRAND TOTAL</td>
                    <td>$ &nbsp;{{$grand_total}}</td>
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
    </div>
  
  </div>

@endsection
