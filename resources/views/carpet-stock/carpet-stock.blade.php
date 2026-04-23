@extends('dsh.master')
@section('title' , ' گدام قالین')
@section('content')
  <!-- navbar -->
  
  <div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <div class="card">
        <div class="card-header">
          <div class="row">
            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 hideOnPrint">
              <form action="/dashboard/carpet-stock/search" method="POST">
                @csrf
                <label for="">جستجو</label>
                <input type="text" value="{{ Request::old('search') }}" name="search"
                       placeholder=" جستجو" class="form-control" required>
              </form>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 hideOnPrint">
              
              <form action="/dashboard/carpet-stock/search-date-range" method="POST" id="dateSearch">
                @csrf
                <div class="row">
                  <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                    <input type="submit" value="جستجو" class="date-submit btn btn-sm btn-primary btn-block"
                           style="margin-top: 35px;">
                  </div>
                  <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                    <label>شروع</label>
                    <input type="date"
                           @if(isset($from_date))
                           value="{{$from_date }}"
                           @endif
                           name="from_date" class="form-control"
                           required>
                  </div>
                  <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                    <label>ختم</label>
                    <input type="date" name="to_date"
                           @if(isset($to_date))
                           value="{{$to_date}}"
                           @endif
                           class="form-control" required>
                  </div>
                </div>
                
                
                {{-- <a href=""><i class="fa fa-search"></i></a> --}}
              </form>
            </div>
            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 hideOnPrint">
              <form action="/dashboard/filter-ba-asas-type" method="POST" id="dateSearch">
                @csrf
                <label for="">جستجو نوعیت</label>
                
                <select name="carpet_type" id="type_id" class="form-control" required onchange="this.form.submit();">
                  @foreach($carpet_types as $ag)
                    <option value="{{$ag->carpet_type_id}}">{{$ag->carpet_type}}</option>
                  @endforeach
                </select>
              </form>
            </div>
            <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 hideOnPrint">
            <div class="btn btn-sm btn-primary adgustbtn hideOnPrint" style="float: left"
                 onclick="printPage('carpet_stock_print')"><i class="fa fa-print"></i> Print
            </div>
            </div>
          </div>
         
          @if(session("status"))
            <div class="alert alert-success status" style="display:none;" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
              <p class="text-center">{{session('status')}}</p>
            </div>
          
          @endif
        </div>
        <div class="card-body" id="carpet_stock_print">
  
          <div class="modal fade" id="sale_modal" role="dialog"
               aria-labelledby="myLargeModalLabel" aria-modal="true">
            <div class="modal-dialog modal-lg">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title h4" id="roomEditModalLabel"></h5>
                  <button type="button" class="close" data-dismiss="modal"
                          aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <form action="/dashboard/sales" method="post" id="sale_form">
          
                  <div class="modal-body">
                    @csrf
                    <div class="row">
                      <div class="col-lg-4 col-md-4 col-sm-4 col-12">
                        <div class="form-group fill">
    
                          <label class=""> نمبر انوایس</label>
                          <select name="invoice_id" id="invoice_id" required class="form-control">
                            <option value="">~~~</option>
                            @foreach($invoices as $invoice)
                              <option {{ (Request::old('invoice_id') == $invoice->id ? 'selected' : '') }} value="{{$invoice->id}}"
                                      customer_name="{{$invoice->customer->customer_code}}"
                                      customer_company="{{$invoice->customer->company_name}}"
                                      customer_address="{{$invoice->customer->company_address}}"
        
                              >{{$invoice->invoice_no}}</option>
                            @endforeach
                          </select>
                          <small class="text-danger">@error('invoice_id') {{ __('message.'.$message) }}@enderror
                          </small>
                        </div>
                      </div>
                      <div class="col-lg-4 col-md-4 col-sm-4 col-12">
                        <div class="form-group fill">
                          <label class=""> پکینگ نمبر</label>
                          <select name="packing_id" id="packing_id" required class="form-control">
                            <option value="">~~~</option>
                            @foreach($packing_list as $packing)
                              <option {{ (Request::old('packing_id') == $packing->id ? 'selected' : '') }} value="{{$packing->id}}">{{$packing->packing_no}}</option>
                            @endforeach
                          </select>
                          <small class="text-danger">@error('packing_id') {{ __('message.'.$message) }}@enderror
                          </small>
                        </div>
                      </div>
                      
                      <div class="col-lg-4 col-md-4 col-sm-4 col-12">
                        <div class="form-group fill">
                          <label class=""> پکیج نمبر</label>
                          <select name="package_id" id="package_id" required class="form-control">
                            <option value=""></option>
                          </select>
                          <small class="text-danger">@error('package_id') {{ __('message.'.$message) }}@enderror
                          </small>
                        </div>
                      </div>
                      <div class="col-lg-4 col-md-4 col-sm-4 col-12">
                        <div class="form-group fill">
                          <label>نام مشتری</label>
                          <input type="text" name="customer_name" id="customer_name" class="form-control" readonly>
                        </div>
                      </div>
                      <div class="col-lg-4 col-md-4 col-sm-4 col-12"
                          >
                        <div class="form-group fill">
                          <label class="login2 pull-right pull-right-pro">نام کمپنی</label>
                          <input type="text" name="company_name" id="company_name" class="form-control" readonly>
                        </div>
                      </div>
                      <div class="col-lg-4 col-md-4 col-sm-4 col-12 location_value_div">
                        <div class="form-group fill">
                          <label>ادرس کمپنی</label>
                          <input type="text" name="company_address" id="company_address" class="form-control"
                                 readonly>
                        </div>
                      </div>
  
                      <div class="col col-lg-4 col-md-4 col-sm-4 col-12">
                        <div class="form-group fill">
                          <label>نمبر قالین</label>
                          <input type="text" name="carpet_no" id="carpet_no" class="form-control"
                                 readonly>
                          <input type="hidden" name="carpet_id" id="carpet_id">
                        </div>
                      </div>
                      <div class="col col-lg-4 col-md-4 col-sm-4 col-12">
                        <div class="form-group fill">
                          <label>نوعیت قالین</label>
                          <input type="text" name="carpet_type" id="carpet_type" class="form-control" readonly>
                        </div>
                      </div>
                      <div class="col col-lg-4 col-md-4 col-sm-4 col-12">
                        <div class="form-group fill">
                          <label>کوالتی</label>
                          <input type="text" name="carpet_quality" id="carpet_quality" class="form-control"
                                 readonly>
                        </div>
                      </div>
                      <div class="col col-lg-4 col-md-4 col-sm-4 col-6">
                        <div class="form-group fill">
                          <label>طول قالین</label>
                          <input type="text" name="carpet_height" id="carpet_height" class="form-control">
                        </div>
                      </div>
              
                      <div class="col col-lg-4 col-md-4 col-sm-4 col-12 ">
                        <div class="form-group fill">
                          <label>عرض قالین</label>
                          <input type="text" name="carpet_width" id="carpet_width" class="form-control">
                        </div>
                      </div>
                      <div class="col col-lg-4 col-md-4 col-sm-4 col-12">
                        <div class="form-group fill">
                          <label>سایز قالین</label>
                          <input type="text" name="carpet_area" readonly id="carpet_area" class="form-control">
                        </div>
                      </div>
              
                      <div class="col col-lg-4 col-md-4 col-sm-4 col-12">
                        <div class="form-group fill">
    
                          <label>قیمت تمام شد فی متر</label>
                          <input type="text" name="price_per_meter" id="price_per_meter" class="form-control" readonly>
  
                        </div>
                      </div>
              
                      <div class="col col-lg-4 col-md-4 col-sm-4 col-12">
                        <div class="form-group fill">
    
                          <label>قیمت مجموع تمام شد</label>
                          <input type="text" name="total_price_cost" id="total_price_cost" class="form-control" readonly>
                        </div>
                      </div>
                      <div class="col col-lg-4 col-md-4 col-sm-4 col-12">
                        <div class="form-group fill">
                          <label>قیمت فروش فی متر</label>
                          <input type="text" required name="sale_cost_per_meter" id="sale_cost_per_meter"
                                 class="form-control">
                          <small class="text-danger">@error('sale_cost_per_meter') {{ __('message.'.$message) }}
                            @enderror
                          </small>
                        </div>
                      </div>
                      <div class="col col-lg-4 col-md-4 col-sm-4 col-12">
                        <div class="form-group fill">
    
                          <label>قیمت مجموع </label>
                          <input type="text" name="sale_cost_total" id="sale_cost_total" class="form-control" readonly>
  
                        </div>
                      </div>
                      <div class="col col-lg-4 col-md-4 col-sm-4 col-12">
                        <div class="form-group fill">
    
                          <label>کود مشتری</label>
                          <input type="text" name="customer_code" required="" id="customer_code" class="form-control">
  
                        </div>
                      </div>
            
                    </div>
                  </div>
                  <div class="modal-footer">
                    <div class="form-group">
                      <input type="submit" class="btn btn-primary btn-sm"
                             value="ذخیره">
                      <button type="button" class="btn btn-sm btn-info"
                              data-dismiss="modal">
                        بسته نمودن
                      </button>
                    </div>
          
                  </div>
                </form>
      
              </div>
    
            </div>
          </div>
          
          <h5>موجودی قالین در گدام</h5>
          <div class="static-table-list table-responsive">
            <table class="table table-hover table-xs">
              <thead>
              <tr>
                <th>شماره قالین</th>
                <th>شماره فرمایش</th>
                <th>نوعیت</th>
                <th>طول</th>
                <th>عرض</th>
                <th>مساحت</th>
                <th>زمینه</th>
                <th>حاشیه</th>
                <th>نمبر نقشه</th>
                <th>قیمت</th>
                <th>تاریخ</th>
                <th class="hideOnPrint ">جزییات</th>
                <th class="hideOnPrint">فروش</th>
              
              </tr>
              </thead>
              <tbody>
              @if($carpets->count() > 0)
                @foreach($carpets as $carpet)
                  <tr>
                    <td>{{ $carpet->carpet_no }}</td>
                    @if($carpet->carpet_order)
                      <td>{{ $carpet->carpet_order->order_number }}</td>
                    @else 
                      <td></td>
                    @endif
                    @if($carpet->type)
                      <td>{{ $carpet->type->carpet_type }}</td>
                    @else
                      <td></td>
                    @endif
                    <td>{{ $carpet->width }} </td>
                    <td>{{ $carpet->height }}</td>
                    <td>{{ $carpet->area }}</td>
                    <td>{{ $carpet->field }}</td>
                    <td>{{ $carpet->margin }}</td>
                    <td>{{ $carpet->map_number }}</td>
                    <td>{{ $carpet->total_price }} $</td>
                    <td>{{ $carpet->date }}</td>
                    <td class="hideOnPrint "><a class="btn btn-sm btn-info"
                                                href="/dashboard/carpet-stock-details/{{ $carpet->carpet_id }}">جزییات</a>
                    </td>
                  <td>
                    <a href="" class="btn btn-sm btn-warning" data-toggle="modal"
                       data-target="#sale_modal"
                    onclick="
                    $('#carpet_id').val('{{$carpet->carpet_id}}');
                    $('#carpet_no').val('{{$carpet->carpet_no}}');
                    $('#carpet_type').val('@if($carpet->type){{$carpet->type->carpet_type}} @endif');
                    $('#carpet_quality').val('@if($carpet->quality){{$carpet->quality->quality}} @endif');
                    $('#carpet_area').val('@if($carpet->carpet_wash) @if($carpet->carpet_wash->area > 0){{$carpet->carpet_wash->area}}@else {{$carpet->area}} @endif @else {{$carpet->area}} @endif');
                    $('#carpet_height').val('@if($carpet->carpet_wash) @if($carpet->carpet_wash->height > 0){{$carpet->carpet_wash->height}}@else {{$carpet->height}} @endif @else {{$carpet->height}} @endif');
                    $('#carpet_width').val('@if($carpet->carpet_wash) @if($carpet->carpet_wash->width > 0){{$carpet->carpet_wash->width}}@else {{$carpet->width}} @endif @else {{$carpet->width}} @endif');
                    $('#total_price_cost').val('{{$carpet->total_price}}');
                     $('#price_per_meter').val('@if($carpet->carpet_wash) @if($carpet->carpet_wash->area > 0) {{$carpet->total_price / $carpet->carpet_wash->area}}  @else {{$carpet->total_price / $carpet->area}} @endif @else {{$carpet->total_price / $carpet->area}} @endif');
                    
                    "
                   
                    >
                 فروش
                    </a>
                  </td>
                  
                  </tr>
                @endforeach
              @else
                <tr>
                  <td colspan="13" class="text-center text-info">هیچ موردی دریافت نشد</td>
                </tr>
              @endif
              </tbody>
            </table>
            @if(!isset($search))
       <span class="text-center">{{$carpets->links()}}</span>
       @endif
          </div>
        </div>
      </div>
    
    </div>
  </div>
@endsection

@section('scripts')
  <script>
    $('#type_id').select2();
      $('.status').show();
      window.setTimeout(function () {
          $(".status").fadeTo(500, 0).slideUp(500, function () {

              $(this).remove();
          });
      }, 2000);
  
  </script>

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
          // var customer_name = $('#invoice_id option:selected').attr('customer_name');
          // $('#customer_name').val(customer_name);
          //
          // var customer_company = $('#invoice_id option:selected').attr('customer_company');
          // $('#company_name').val(customer_company);
          //
          // var company_address = $('#invoice_id option:selected').attr('customer_address');
          // $('#company_address').val(company_address);

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
          //
          //
          // var total_price = $('#carpet_id option:selected').attr('total_price');
          // $('#total_price_cost').val(total_price);
          //
          // if (total_price != null && carpet_area != null) {
          //     $('#price_per_meter').val(total_price / carpet_area);
          // }


      });
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
