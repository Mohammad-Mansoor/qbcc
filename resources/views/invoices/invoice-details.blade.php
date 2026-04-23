@extends('dsh.master')

@section('content')
  <div class="row" id="invoicePrint">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 center">
      <div class="card">
        <div class="card-header">
          <div class="row">
            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8"></div>
            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 hideOnPrint">
              <div class="btn btn-sm btn-primary hideOnPrint" style="float: left;"
                   onclick="printPage('invoicePrint')"><i class="fa fa-print"></i> Print
              </div>
            </div>
          
          </div>
        </div>
        <div class="card-body">
          <div class="row">
            
            <div class="col-lg-4 col-md-4 col-sm-4">
              <table class="table table-hover table-xs right" style="direction: ltr;text-align: left">
                <thead>
                
                </thead>
                <tbody>
                
                <tr>
                  <td style="color: dodgerblue;"><b>INVOICE DETAILS</b></td>
                  <td style="color: dodgerblue;"><b></b></td>
                
                </tr>
                <tr>
                  <td style="direction: ltr;">Invoice#:</td>
                  <td style="direction: ltr;text-align: left;"><b>{{$invoice->invoice_no}}</b></td>
                
                </tr>
                <tr>
                  <td style="direction: ltr">Invoice Date:</td>
                  <td><b>{{$invoice->invoice_date}}</b></td>
                
                </tr>
                
                </tbody>
              </table>
            </div>
            <div class="col-md-8 col-sm-8 col-xs-8">
              <table class="table table-sm table-hover text-left">
                <thead>
                
                </thead>
                <tbody>
                <tr style="direction: ltr;text-align: left;font-size: 15px;">
                  
                  <td style="color: dodgerblue;text-align: left;"><b>{{$invoice->customer->name}}</b></td>
                  <td style="color: dodgerblue;"><b>To</b></td>
                </tr>
                <tr style="direction: ltr;text-align: left">
                  <td><b>{{$invoice->customer->company_name}}</b></td>
                  <td>Company Name :</td>
                </tr>
                <tr style="direction: ltr;text-align: left">
                  <td><b>{{$invoice->customer->company_address}}</b></td>
                  <td>Address :</td>
                </tr>
                <tr style="direction: ltr;text-align: left">
                  <td><b>{{$invoice->customer->phone}}</b></td>
                  <td>Phone :</td>
                </tr>
                
                <tr style="direction: ltr;text-align: left">
                  <td><b>{{$invoice->customer->email}}</b></td>
                  <td>Email :</td>
                </tr>
                <tr style="direction: ltr;text-align: left">
                  <td><b>{{$invoice->customer->website}}</b></td>
                  <td>Website :</td>
                </tr>
                </tbody>
              </table>
            
            </div>
          
          
          </div>
          
          <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <h5>{{$invoice->invoice_description}}</h5>
          </div>
          <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 hideOnPrint">
              <form action="/dashboard/search-carpet-from-invoice" method="post">
                @csrf
                <input type="text" name="search" required
                       placeholder="جستجو" class="form-control">
                <input type="hidden" name="invoice_id" value="{{$invoice->id}}">
              </form>
            </div>
          </div>
          <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="margin:20px auto">
            <div class="alert alert-success" style="display:none;" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
              موفقانه ارسال شد
            </div>
            
            <div class="static-table-list table-responsive">
              <hr style="height: 3px;width: 100%;color: #0b97c4;background-color: #0b97c4">
              <table class="table table-hover table-xs right" style="direction: ltr;text-align: left;color: #000;"
                     id="repairShow">
                <thead>
                <tr style="color: dodgerblue;">
                  <th>Item Number</th>
                  <th>Item Description</th>
                  <th>Height</th>
                  <th>Width</th>
                  
                  <th>Area</th>
                  
                  <th>Unit Price</th>
                  <th>Total Price</th>
                  <th class="hideOnPrint">Return</th>
                
                
                </tr>
                </thead>
                <tbody>
                @php($total_area = 0)
                
                @if(isset($search))
                  
                  @foreach($sales as $sale)
                    <tr>
                      <td>{{$sale->carpet_no}}</td>
                      <td>{{$sale->type}}</td>
                      
                      <td>{{$sale->carpet_height}} m</td>
                      <td>{{$sale->carpet_width}} m</td>
                      <td>{{$sale->carpet_area}} m <sup>2</sup></td>
                      @if($sale->carpet_area != null)
                      <span style="display: none">{{$total_area += $sale->carpet_area}}</span>
                      @endif
                      
                      
                      <td> ${{$sale->sale_cost_per_meter}}</td>
                      <td> ${{$sale->sale_cost_total}}</td>
                      <td class="hideOnPrint">
                        <button onclick="sendToStock({{$sale->carpet_id}})"
                                class="btn btn-sm btn-info printBTN"><i class="fa fa-send"></i>&nbsp;بازگشت به گدام
                        </button>
                      </td>
                    
                    </tr>
                  @endforeach
                @else
                  
                  @foreach($sales as $sale)
                    <tr>
                      <td>{{$sale->carpet->carpet_no}}</td>
                      <td>{{$sale->type}}</td>
                      
                      <td>{{$sale->carpet_height}} m</td>
                      
                      <td>{{$sale->carpet_width}} m</td>
                      
                      
                      <td>{{$sale->carpet_area}} m <sup>2</sup></td>
                      @if($sale->carpet_area != null)
                      <span style="display: none">{{$total_area += $sale->carpet_area}}</span>
                      @endif
                      
                      <td> ${{$sale->sale_cost_per_meter}}</td>
                      <td> ${{$sale->sale_cost_total}}</td>
                      <td class="hideOnPrint">
                        <button onclick="sendToStock({{$sale->carpet_id}})"
                                class="btn btn-sm btn-info printBTN"><i class="fa fa-send"></i>&nbsp;بازگشت به گدام
                        </button>
                      </td>
                    
                    </tr>
                  @endforeach
                @endif
                </tbody>
              </table>
              <hr style="height: 3px;width: 100%;color: #0b97c4;background-color: #0b97c4">
            </div>
            @if($sales)
              <div class="row">
                
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                  
                  <table class="table table-xs table-hover" style="direction: ltr;text-align: left;color: black">
                    <tbody>
                    <tr>
                      <td style="color: #0b97c4"><b>QUANTITY</b></td>
                      <td>{{$sales->count('carpet_id')}} pcs</td>
                    </tr>
                    <tr>
                      <td style="color: #0b97c4"><b>TOTAL</b></td>
                      <td>{{$total_area}} m<sup>2</sup></td>
                    </tr>
                    
                    <tr>
                      <td style="color: #0b97c4"><b>GRAND TOTAL</b></td>
                      <td>{{$sales->sum('sale_cost_total')}} $</td>
                    </tr>
                    
                    </tbody>
                  </table>
                </div>
                <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9">
                
                </div>
              
              </div>
            @endif
          </div>
        
        </div>
      </div>
    </div>
  </div>

@endsection


@section('scripts')
  
  <script>


      function sendToStock(carpet_id) {

          swal({
              text: "مطمعین هستید ؟",
              buttons: true,
              dangerMode: true,
              buttons: {
                  confirm: {text: 'بلی', className: 'btn-success'},
                  cancel: 'نخیر'
              },
          })
              .then((willDelete) => {
                  if (willDelete) {
                      $.ajax({
                          type: 'GET',
                          data: {
                            {{--'_token': '{{csrf_token()}}',--}}
                          },
                          url: '/dashboard/invoices/sent-to-stock/' + carpet_id,
                          success: function (res) {

                              if (res.status == 'success') {
                                  $('.alert-success').show();
                                  location.reload();
                              } else {
                                  $('.alert-danger').show();
                              }


                              window.setTimeout(function () {
                                  $(".alert-success").fadeTo(500, 0).slideUp(500, function () {

                                      $(this).remove();
                                  });
                              }, 2000);
                          },

                      })
                  }
              });
      }
  
  
  </script>

@endsection

