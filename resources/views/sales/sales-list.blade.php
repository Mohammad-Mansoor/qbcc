@extends('dsh.master')
@section('title' , 'لیست قالین های فروخته شده')
@section('content')
  
  <div class="row">
    <div class="col-lg-12 col-md-12 col-sm-6 col-xs-12">
      @if($sale)
      <div class="card">
        <div class="card-header">
          <h5>ویرایش فروش</h5>
        </div>
        <div class="card-body">
          <div class="all-form-element-inner">
           
              <form action="/dashboard/sales/{{$sale->id}}" method="post">
  
                @method('PATCH')
                @csrf
                <input type="hidden" name="old_invoice" value="{{$sale->invoice_id}}">
                
                <div class="row">
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      
                      <label class=""> نمبر انوایس</label>
                      
                      <select name="invoice_id" id="invoice_id" required class="form-control">
                        @foreach($invoices as $invoice)
                          <option {{ ($sale->invoice_id == $invoice->id ? 'selected' : '') }} value="{{$invoice->id}}"
                                  customer_name="{{$invoice->customer->name}}"
                                  customer_company="{{$invoice->customer->company_name}}"
                                  customer_address="{{$invoice->customer->company_address}}"
                          
                          >{{$invoice->invoice_no}}</option>
                        @endforeach
                      </select>
                      <small class="text-danger">@error('invoice_id') {{ __('message.'.$message) }} @enderror</small>
                    </div>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <label class=""> پکینگ نمبر</label>
                      <select name="package_id" id="packing_id" required class="form-control">
                        <option value="">~~~</option>
                        @foreach($packing_list as $pack)
                          <option {{ ($package->packing_id == $pack->id ? 'selected' : '') }} value="{{$pack->id}}"
                          
                          >{{$pack->packing_no}}</option>
                        @endforeach
                      </select>
                      <small class="text-danger">@error('packing_id') {{ __('message.'.$message) }} @enderror</small>
                    </div>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <label class=""> پکیج نمبر</label>
                      <select name="package_id" id="package_id" required class="form-control">
                        <option value="{{$package->id}}">{{$package->package_no}}</option>
                      </select>
                      <small class="text-danger">@error('package_id') {{ __('message.'.$message) }}@enderror
                      </small>
                    </div>
                  </div>
                  
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <label>نام مشتری</label>
                      <input type="text" name="customer_name" id="customer_name" class="form-control" readonly>
                    </div>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <label class="login2 pull-right pull-right-pro">نام کمپنی</label>
                      <input type="text" name="company_name" id="company_name" class="form-control" readonly>
                    </div>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <label>ادرس کمپنی</label>
                      <input type="text" name="company_address" id="company_address" class="form-control" readonly>
                    </div>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <label class="">نمبر قالین</label>
                      <input type="text" name="carpet_no" id="carpet_no" class="form-control" value="{{$sale->carpet->carpet_no}}" readonly>
                      <input type="hidden" name="carpet_id"  class="form-control" value="{{$sale->carpet_id}}">
                      
                    </div>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <label>نوعیت قالین</label>
                      <input type="text" name="carpet_type" value="{{$sale->type}}" class="form-control" readonly>
                    </div>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <label>کوالتی</label>
                      <input type="text" name="carpet_quality"  value="{{$sale->quality}}" class="form-control" readonly>
                    </div>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <label>طول قالین</label>
                      <input type="text" name="carpet_height" id="carpet_height"  value="{{$sale->carpet_height}}"
                             class="form-control">
                    </div>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <label>عرض قالین</label>
                      <input type="text" name="carpet_width" id="carpet_width" value="{{$sale->carpet_width}}"
                             class="form-control">
                    </div>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <label>سایز قالین</label>
                      <input type="text" name="carpet_area" readonly id="carpet_area" value="{{$sale->carpet_area}}"
                             class="form-control">
                    </div>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      
                      <label>قیمت تمام شد فی متر</label>
                      <input type="text" name="price_per_meter" id="price_per_meter"  class="form-control" readonly>
                    
                    </div>
                  </div>
                  
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      
                      <label>قیمت مجموع تمام شد</label>
                      <input type="text" name="total_price_cost" id="total_price_cost" value="{{$sale->carpet->total_price}}" class="form-control" readonly>
                    </div>
                  </div>
                  
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <label>قیمت فروش فی متر</label>
                      <input type="text" required name="sale_cost_per_meter" value="{{$sale->sale_cost_per_meter}}"
                             id="sale_cost_per_meter" class="form-control">
                      <input type="hidden" value="{{$sale->sale_cost_per_meter}}" name="old_cost_per_meter">
                      <small class="text-danger">@error('sale_cost_per_meter') {{ __('message.'.$message) }}
                        @enderror
                      </small>
                    </div>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      
                      <label>قیمت مجموع </label>
                      <input type="text" name="sale_cost_total" value="{{$sale->sale_cost_total}}" id="sale_cost_total"
                             class="form-control" readonly>
                      <input type="hidden" value="{{$sale->sale_cost_total}}" name="old_cost_total">
                    
                    </div>
                  </div>
                  
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      
                      <label>کود مشتری</label>
                      <input type="text" name="customer_code" value="{{$sale->customer_code}}" required
                             id="customer_code"
                             class="form-control">
                    
                    </div>
                  </div>
                
                </div>
                
                
                <div class="row">
                  <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                    <div class="form-group fill">
                      <button class="btn btn-info btn-sm" type="submit"><i class="fa fa-save"></i> &nbsp; ثبت
                      </button>
                      <button class="btn btn-warning btn-sm" type="reset"> منصرف</button>
                    </div>
                  </div>
                </div>
              
              </form>
         
          </div>
        
        </div>
      </div>
      @endif
      <div class="card" id="salePrint">
        <div class="card-header">
          <h5>لیست فروشات قالین</h5>
          @if(session("status"))
            <div class="alert alert-success status" style="display:none;" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
              {{session('status')}}
            </div>
          
          @endif
          @if(session("error"))
            
            <div class="alert alert-danger status" style="display:none;" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
              {{session('error')}}
            </div>
          
          @endif
          
          <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 hideOnPrint">
              <form action="/dashboard/search-carpet-from-sales" method="post">
                @csrf
                <input type="text" name="search" required
                       placeholder="جستجو" class="form-control">
                {{--<input type="hidden" name="invoice_id" value="">--}}
              </form>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4"></div>
            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 hideOnPrint">
              <div class="btn btn-sm btn-primary adgustbtn hideOnPrint" style="float: left"
                   onclick="printPage('salePrint')"><i
                        class="fa fa-print"></i> Print
              </div>
              <a href="/dashboard/sales-all" style="float: left"
                 class="btn btn-sm btn-info hideOnPrint">نمایش همه</a>
            </div>
          </div>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover table-xs">
              <thead>
              <tr>
                <th>نمبر انوایس</th>
                
                <th>نام مشتری</th>
                <th>نمبر قالین</th>
                <th>نوعیت</th>
                <th>کوالتی</th>
                <th>طول</th>
                <th>عرض</th>
                <th>سایز</th>
               <th>قیمت خرید</th>
               <th>فی متر مصرف</th>
                <th>فی متر فروش</th>
                <th>مجموع فروش</th>
                @if(auth()->user()->role == 'SP')
                  <th>قیمت مجموع تمام شد</th>
                  <th>مفاد</th>
                @endif
                <th class="printTitle">ویرایش</th>
              
              
              </tr>
              </thead>
              <tbody>
                  
                   @php($majmo_tamam_shod = 0)
              @php($total_sale_price = 0)
              @php($sizes = 0)
              @php($profits = 0)
              
              
              
              @if(isset($search))
                @foreach($sales as $sale)
                   <?php $invoice = DB::table('invoices')->where('id',$sale->invoice_id)->first(); ?>
                    <?php $customer = DB::table('customers')->where('id',$sale->customer_id)->first(); ?>
                     <?php  $kachaee_expense = DB::table('carpet_repairs')->where('carpetId',$sale->carpet_id)->sum('total_price'); ?>
               <?php  $wash_expense = DB::table('carpet_washes')->where('carpetId',$sale->carpet_id)->sum('total_price'); ?>
              <?php  $finishing_expense = DB::table('finishing_works')->where('carpetId',$sale->carpet_id)->sum('price'); ?>
     
                  <tr>

                    <td>{{$invoice->invoice_no}}</td>
                
                    <td>{{$customer->name}}</td>
                  
                    <td>{{$sale->carpet_no}}</td>
                    <td>{{$sale->type}}</td>
                    <td>{{$sale->quality}}</td>

                    <td>{{$sale->carpet_height}} m</td>
                    <td>{{$sale->carpet_width}} m</td>
                    <td>{{$sale->carpet_area}} m <sup>2</sup></td>

                   <td>{{$sale->price}} $</td>
                       @if($sale->carpet_area > 0)
                     <td>{{round(($kachaee_expense + $wash_expense +  $finishing_expense) / $sale->carpet_area , 2)}} $</td>
                     @else 
                       <td>{{round(($kachaee_expense + $wash_expense +  $finishing_expense) / $sale->carpet->area , 2)}} $</td>
                       @endif
                      
                    <td>{{$sale->sale_cost_per_meter}} $</td>
                       <td>{{$sale->carpet_area * $sale->sale_cost_per_meter}} $</td>
                  
                        @if(auth()->user()->role == 'SP')
                       <td>{{round((($sale->carpet_area * $sale->price) + $kachaee_expense + $wash_expense +  $finishing_expense) , 2)}} $</td>
                      <td>{{round(($sale->carpet_area * $sale->sale_cost_per_meter) - ($sale->carpet_area * $sale->price) + $kachaee_expense + $wash_expense +  $finishing_expense,2) }} $</td>

                      @endif
                      <td class="hideOnPrint"><a href="/dashboard/sales/{{$sale->id}}/edit"
                                                 class="btn btn-sm btn-info printBTN"><i
                                  class="fa fa-pencil"></i>&nbsp; ویرایش</a></td>



                  </tr>
                  
                   <span style="display: none">{{$majmo_tamam_shod += ($sale->carpet_price_us + $kachaee_expense + $wash_expense +  $finishing_expense) }}</span>
                  <span style="display: none">{{$total_sale_price += $sale->sale_cost_total}}</span>
                  <span style="display: none">{{$profits += $sale->profit}}</span>
                  <span style="display: none">{{$sizes += $sale->carpet_area}}</span>
                @endforeach
              @else
                @foreach($sales as $sale)
                
                 <?php  $kachaee_expense = DB::table('carpet_repairs')->where('carpetId',$sale->carpet_id)->sum('total_price'); ?>
               <?php  $wash_expense = DB::table('carpet_washes')->where('carpetId',$sale->carpet_id)->sum('total_price'); ?>
              <?php  $finishing_expense = DB::table('finishing_works')->where('carpetId',$sale->carpet_id)->sum('price'); ?>
                  <tr>
                    
                    <td>{{$sale->invoice->invoice_no}}</td>
                    
                    <td class="hideOnPrint">{{$sale->customer->name}}</td>
            
                    <td>{{$sale->carpet->carpet_no}}</td>
                    <td>{{$sale->type}}</td>
                    <td>{{$sale->quality}}</td>
                    <td>{{$sale->carpet_height}} m</td>
                    <td>{{$sale->carpet_width}} m</td>
                    <td>{{$sale->carpet_area}} m <sup>2</sup></td>
                  <td>{{$sale->carpet->price}} $</td>
                    
                     
                        @if($sale->carpet_area > 0)
                     <td>{{round(($kachaee_expense + $wash_expense +  $finishing_expense) / $sale->carpet_area , 2)}} $</td>
                     @else 
                       <td>{{round(($kachaee_expense + $wash_expense +  $finishing_expense) / $sale->carpet->area , 2)}} $</td>
                       @endif
                      
                    <td>{{$sale->sale_cost_per_meter}} $</td>
                    <td>{{round($sale->carpet_area * $sale->sale_cost_per_meter,2)}} $</td>
                  
                @if(auth()->user()->role == 'SP')
                      <td>{{round((($sale->carpet_area * $sale->carpet->price) + $kachaee_expense + $wash_expense +  $finishing_expense) , 2)}} $</td>
                      <td>{{round(($sale->carpet_area * $sale->sale_cost_per_meter) - (($sale->carpet_area * $sale->carpet->price) + $kachaee_expense + $wash_expense +  $finishing_expense),2)}} $</td>
                    
                    @endif
                      <td class="hideOnPrint"><a href="/dashboard/sales/{{$sale->id}}/edit"
                                                 class="btn btn-sm btn-info printBTN"><i
                                  class="fa fa-pencil"></i>&nbsp; ویرایش</a></td>
               
                  
                  
                  </tr>
                  
                <span style="display: none">{{$majmo_tamam_shod += ($sale->carpet->carpet_price_us + $kachaee_expense + $wash_expense +  $finishing_expense) }}</span>
                  <span style="display: none">{{$total_sale_price += $sale->sale_cost_total}}</span>
                  <span style="display: none">{{$profits += $sale->profit}}</span>
                  <span style="display: none">{{$sizes += $sale->carpet_area}}</span>
                @endforeach
              @endif
              
              </tbody>
             <tr>
             
         
                <th></th>
                <th></th>
                <th>Pcs {{$sales->count()}}</th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th>{{round($sizes,2)}} m <sup>2</sup></th>
                <th></th>
                <th></th>
                <th></th>
                <th>{{round($total_sale_price,2)}} $</th>
                <th>{{round($majmo_tamam_shod,2)}} $</th>
                <th>{{round($profits,2)}} $</th>
              </tr>
            </table>
            @if(!isset($all))
              <p class="hideOnPrint">{{$sales->links()}}</p>
            @endif
          </div>
        </div>
      </div>
    </div>
  
  </div>

@endsection

@section('scripts')

<script type="text/javascript">
      $(document).ready(function () {


         $('#carpet_height').keyup(function () {
              var carpet_height = $('#carpet_height').val();
              var carpet_width = $('#carpet_width').val();
              var carpet_area = $('#carpet_area').val(carpet_width * carpet_height);
              var total_price = $('#total_price_cost').val();

              $('#price_per_meter').val(total_price / $('#carpet_area').val());


              var sale_cost_per_meter = $("#sale_cost_per_meter").val();
              if (sale_cost_per_meter != null) {
                  $('#sale_cost_total').val(sale_cost_per_meter * $('#carpet_area').val());
              }


          });

          $('#carpet_width').keyup(function () {
              var carpet_height = $('#carpet_height').val();
              var carpet_width = $('#carpet_width').val();
              var carpet_area = $('#carpet_area').val(carpet_width * carpet_height);


              var total_price = $('#total_price_cost').val();

              $('#price_per_meter').val(total_price / $('#carpet_area').val());

              var sale_cost_per_meter = $("#sale_cost_per_meter").val();
              if (sale_cost_per_meter != null) {
                  $('#sale_cost_total').val(sale_cost_per_meter * $('#carpet_area').val());
              }

          });


          // $('#carpet_id').select2();

          $('#invoice_id').change(function () {
              var customer_name = $('#invoice_id option:selected').attr('customer_name');
              $('#customer_name').val(customer_name);

              var customer_company = $('#invoice_id option:selected').attr('customer_company');
              $('#company_name').val(customer_company);

              var company_address = $('#invoice_id option:selected').attr('customer_address');
              $('#company_address').val(company_address);

          });

          // $('#carpet_id').change(function () {
          //     var carpet_type = $('#carpet_id option:selected').attr('carpet_type');
          //     $('#carpet_type').val(carpet_type);
          //
          //     var carpet_quality = $('#carpet_id option:selected').attr('carpet_quality');
          //     $('#carpet_quality').val(carpet_quality);
          //
          //     var carpet_height = $('#carpet_id option:selected').attr('carpet_height');
          //     $('#carpet_height').val(carpet_height);
          //
          //     var carpet_width = $('#carpet_id option:selected').attr('carpet_width');
          //     $('#carpet_width').val(carpet_width);
          //
          //     var carpet_area = $('#carpet_id option:selected').attr('carpet_area');
          //     $('#carpet_area').val(carpet_area);
          //
          //
          //     var total_price = $('#carpet_id option:selected').attr('total_price');
          //     $('#total_price_cost').val(total_price);
          //
          //     // var price_per_meter = $('#carpet_id option:selected').attr('price_per_meter');
          //
          //     $('#price_per_meter').val(total_price / carpet_area);
          //
          //
          // })

          
          

          $("#sale_cost_per_meter").keyup(function () {
              var sale_cost_per_meter = $('#sale_cost_per_meter').val();
              var mainCostPM = parseFloat(sale_cost_per_meter).toFixed(2);

              var carpet_area = $('#carpet_area').val();
              var mainCarpetArea = parseFloat(carpet_area).toFixed(2);


              $("#sale_cost_per_meter").val();

              $('#sale_cost_total').val(mainCostPM * mainCarpetArea);


          });

          // $('#invoice_id').onload(function () {
          var customer_name = $('#invoice_id option:selected').attr('customer_name');
          $('#customer_name').val(customer_name);

          var customer_company = $('#invoice_id option:selected').attr('customer_company');
          $('#company_name').val(customer_company);

          var company_address = $('#invoice_id option:selected').attr('customer_address');
          $('#company_address').val(company_address);

          // })
          // $('#carpet_id').onload(function () {
          // var carpet_type = $('#carpet_id option:selected').attr('carpet_type');
          // $('#carpet_type').val(carpet_type);
          //
          // var carpet_quality = $('#carpet_id option:selected').attr('carpet_quality');
          // $('#carpet_quality').val(carpet_quality);
          //
          // var carpet_height = $('#carpet_id option:selected').attr('carpet_height');
          // $('#carpet_height').val(carpet_height);
          //
          // var carpet_width = $('#carpet_id option:selected').attr('carpet_width');
          // $('#carpet_width').val(carpet_width);
          //
          // var carpet_area = $('#carpet_id option:selected').attr('carpet_area');
          // $('#carpet_area').val(carpet_area);


          // var total_price = $('#carpet_id option:selected').attr('total_price');
          // $('#total_price_cost').val(total_price);
          //
          // if (total_price != null && carpet_area != null) {
          //     $('#price_per_meter').val(total_price / carpet_area);
          // }


      });
  </script>
  <script>

      $('.status').show();
      window.setTimeout(function () {
          $(".status").fadeTo(500, 0).slideUp(500, function () {

              $(this).remove();
          });
      }, 2000);
  
  </script>
  <script type="text/javascript">
      $("#packing_id").change(function () {
          $.ajax({
              url: "{{ route('dashboard.package_list.get_by_packing') }}?packing_id=" + $(this).val(),
              method: 'GET',
              success: function (data) {
                  $('#package_id').html(data.html);
              }
          });
      });
  </script>


@endsection