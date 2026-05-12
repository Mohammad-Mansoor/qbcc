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
                <label for="">جستجو عمومی</label>
                <input type="text" value="{{ Request::old('search') }}" name="search"
                       placeholder=" نمبر قالین، نقشه و غیره..." class="form-control" required>
              </form>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 hideOnPrint">
              <form action="/dashboard/carpet-stock" method="GET" id="warehouseFilterForm">
                <label for="">فیلتر بر اساس گدام</label>
                <select name="warehouse_id" id="warehouse_filter" class="form-control" onchange="this.form.submit();">
                  <option value="">همه گدام ها</option>
                  @foreach($warehouses as $wh)
                    <option value="{{$wh->id}}" {{ (Request::get('warehouse_id') == $wh->id ? 'selected' : '') }}>{{$wh->name}} ({{$wh->location}})</option>
                  @endforeach
                </select>
              </form>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 hideOnPrint">
              
              <form action="/dashboard/carpet-stock/search-date-range" method="POST" id="dateSearch">
                @csrf
                <div class="row">
                  <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
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
                  <div class="col-lg-5 col-md-5 col-sm-5 col-xs-5">
                    <label>ختم</label>
                    <input type="date" name="to_date"
                           @if(isset($to_date))
                           value="{{$to_date}}"
                           @endif
                           class="form-control" required>
                  </div>
                </div>
              </form>
            </div>
            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 hideOnPrint">
              <form action="/dashboard/filter-ba-asas-type" method="POST" id="dateSearch">
                @csrf
                <label for="">جستجو نوعیت</label>
                
                <select name="carpet_type" id="type_id" class="form-control" required onchange="this.form.submit();">
                  <option value="">انتخاب نوعیت</option>
                  @foreach($carpet_types as $ag)
                    <option value="{{$ag->carpet_type_id}}">{{$ag->carpet_type}}</option>
                  @endforeach
                </select>
              </form>
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
              <div class="modal-content shadow-lg border-0">
                <div class="modal-header bg-primary text-white">
                  <h5 class="modal-title h4 text-white" id="roomEditModalLabel">ثبت فروش قالین</h5>
                  <button type="button" class="close text-white" data-dismiss="modal"
                          aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <form action="/dashboard/sales" method="post" id="sale_form">
          
                  <div class="modal-body">
                    @csrf
                    <div class="row">
                      <div class="col-lg-4 col-md-4 col-sm-4 col-12">
                        <div class="form-group fill">
    
                          <label class=""> نمبر انوایس</label>
                          <select name="invoice_id" id="invoice_id" required class="form-control select2-modal">
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
                          <select name="packing_id" id="packing_id" required class="form-control select2-modal">
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
                          <select name="package_id" id="package_id" required class="form-control select2-modal">
                            <option value=""></option>
                          </select>
                          <small class="text-danger">@error('package_id') {{ __('message.'.$message) }}@enderror
                          </small>
                        </div>
                      </div>
                      <div class="col-lg-4 col-md-4 col-sm-4 col-12">
                        <div class="form-group fill">
                          <label>نام مشتری</label>
                          <input type="text" name="customer_name" id="customer_name" class="form-control bg-light" readonly>
                        </div>
                      </div>
                      <div class="col-lg-4 col-md-4 col-sm-4 col-12"
                          >
                        <div class="form-group fill">
                          <label class="login2 pull-right pull-right-pro">نام کمپنی</label>
                          <input type="text" name="company_name" id="company_name" class="form-control bg-light" readonly>
                        </div>
                      </div>
                      <div class="col-lg-4 col-md-4 col-sm-4 col-12 location_value_div">
                        <div class="form-group fill">
                          <label>ادرس کمپنی</label>
                          <input type="text" name="company_address" id="company_address" class="form-control bg-light"
                                 readonly>
                        </div>
                      </div>
  
                      <div class="col col-lg-4 col-md-4 col-sm-4 col-12">
                        <div class="form-group fill">
                          <label>نمبر قالین</label>
                          <input type="text" name="carpet_no" id="carpet_no" class="form-control bg-light"
                                 readonly>
                          <input type="hidden" name="carpet_id" id="carpet_id">
                        </div>
                      </div>
                      <div class="col col-lg-4 col-md-4 col-sm-4 col-12">
                        <div class="form-group fill">
                          <label>نوعیت قالین</label>
                          <input type="text" name="carpet_type" id="carpet_type" class="form-control bg-light" readonly>
                        </div>
                      </div>
                      <div class="col col-lg-4 col-md-4 col-sm-4 col-12">
                        <div class="form-group fill">
                          <label>کوالتی</label>
                          <input type="text" name="carpet_quality" id="carpet_quality" class="form-control bg-light"
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
                          <input type="text" name="carpet_area" readonly id="carpet_area" class="form-control bg-light">
                        </div>
                      </div>
              
                      <div class="col col-lg-4 col-md-4 col-sm-4 col-12">
                        <div class="form-group fill">
    
                          <label>قیمت تمام شد فی متر</label>
                          <input type="text" name="price_per_meter" id="price_per_meter" class="form-control bg-light" readonly>
  
                        </div>
                      </div>
              
                      <div class="col col-lg-4 col-md-4 col-sm-4 col-12">
                        <div class="form-group fill">
    
                          <label>قیمت مجموع تمام شد</label>
                          <input type="text" name="total_price_cost" id="total_price_cost" class="form-control bg-light" readonly>
                        </div>
                      </div>
                      <div class="col col-lg-4 col-md-4 col-sm-4 col-12">
                        <div class="form-group fill">
                          <label>قیمت فروش فی متر</label>
                          <input type="text" required name="sale_cost_per_meter" id="sale_cost_per_meter"
                                 class="form-control border-primary">
                          <small class="text-danger">@error('sale_cost_per_meter') {{ __('message.'.$message) }}
                            @enderror
                          </small>
                        </div>
                      </div>
                      <div class="col col-lg-4 col-md-4 col-sm-4 col-12">
                        <div class="form-group fill">
    
                          <label>قیمت مجموع فروش</label>
                          <input type="text" name="sale_cost_total" id="sale_cost_total" class="form-control bg-light font-weight-bold text-primary" readonly>
  
                        </div>
                      </div>
                      <div class="col col-lg-4 col-md-4 col-sm-4 col-12">
                        <div class="form-group fill">
    
                          <label>کود مشتری</label>
                          <input type="text" name="customer_code" required="" id="customer_code" class="form-control border-primary">
  
                        </div>
                      </div>
            
                    </div>

                    <!-- ACCOUNT OVERRIDES -->
                    <div class="row mt-4" style="background: #f8f9fa; padding: 20px; border-radius: 12px; border: 1px solid #e9ecef;">
                        <div class="col-lg-12 mb-3">
                            <h6 class="font-weight-bold text-dark border-bottom pb-2">
                                <i class="fa fa-university text-primary mr-2"></i> تنظیمات حسابی (Accounting Overrides)
                            </h6>
                            <p class="small text-muted mb-0">در این بخش می‌توانید حساب‌های پیش‌فرض را برای این فروش تغییر دهید.</p>
                        </div>
                        
                        <!-- Revenue Mapping Overrides -->
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <div class="form-group fill">
                                <label class="text-info font-weight-bold small">حساب دریافتنی/نقد (Revenue Debit)</label>
                                <select name="override_debit_account_id" id="override_debit_account_id" class="form-control select2-modal">
                                    @foreach($allowedRevenueDebit as $acc)
                                        <option value="{{ $acc->id }}" {{ ($mappingRevenue && $mappingRevenue->debit_account_id == $acc->id) ? 'selected' : '' }}>
                                            {{ $acc->account_code }} - {{ $acc->account_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <div class="form-group fill">
                                <label class="text-info font-weight-bold small">حساب فروش/عاید (Revenue Credit)</label>
                                <select name="override_credit_account_id" id="override_credit_account_id" class="form-control select2-modal">
                                    @foreach($allowedRevenueCredit as $acc)
                                        <option value="{{ $acc->id }}" {{ ($mappingRevenue && $mappingRevenue->credit_account_id == $acc->id) ? 'selected' : '' }}>
                                            {{ $acc->account_code }} - {{ $acc->account_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- COGS Mapping Overrides (Optional/Advanced) -->
                        <div class="col-lg-6 col-md-6 col-sm-12 mt-2">
                            <div class="form-group fill">
                                <label class="text-warning font-weight-bold small">حساب هزینه تمام شد (COGS Debit)</label>
                                <select name="override_cogs_debit_id" id="override_cogs_debit_id" class="form-control select2-modal">
                                    @foreach($allowedCogsDebit as $acc)
                                        <option value="{{ $acc->id }}" {{ ($mappingCogs && $mappingCogs->debit_account_id == $acc->id) ? 'selected' : '' }}>
                                            {{ $acc->account_code }} - {{ $acc->account_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 mt-2">
                            <div class="form-group fill">
                                <label class="text-warning font-weight-bold small">حساب موجودی گدام (Inventory Credit)</label>
                                <select name="override_cogs_credit_id" id="override_cogs_credit_id" class="form-control select2-modal">
                                    @foreach($allowedCogsCredit as $acc)
                                        <option value="{{ $acc->id }}" {{ ($mappingCogs && $mappingCogs->credit_account_id == $acc->id) ? 'selected' : '' }}>
                                            {{ $acc->account_code }} - {{ $acc->account_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="alert alert-warning border-0 bg-soft-warning mt-3 py-2" style="font-size: 0.85rem;">
                                <i class="fa fa-exclamation-triangle"></i> <strong>توجه:</strong> هرگونه تغییر در این بخش مستقیماً بر بیلانس مالی و گزارشات عایدات تاثیر می‌گذارد.
                            </div>
                        </div>
                    </div>
                  </div>
                  <div class="modal-footer bg-light">
                    <div class="form-group mb-0">
                      <button type="submit" class="btn btn-primary shadow-sm">
                        <i class="fa fa-check-circle mr-1"></i> تایید و ثبت فروش
                      </button>
                      <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
                        انصراف
                      </button>
                    </div>
          
                  </div>
                </form>
      
              </div>
    
            </div>
          </div>
          
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0 text-primary"><i class="fa fa-cubes"></i> موجودی قالین در گدام</h4>
            <span class="badge badge-info py-2 px-3">تعداد کل: {{ $carpets->total() }} تخته</span>
          </div>

          <div class="static-table-list table-responsive">
            <table class="table table-hover table-striped table-bordered text-center">
              <thead class="bg-light text-dark">
              <tr>
                <th>شماره قالین</th>
                <th>نقشه / کوالتی</th>
                <th>نوعیت</th>
                <th>ابعاد (m)</th>
                <th>مساحت</th>
                <th>زمینه/حاشیه</th>
                <th>گدام</th>
                <th>مدت در گدام</th>
                <th>قیمت تمام شد</th>
                <th class="hideOnPrint ">عملیات</th>
              </tr>
              </thead>
              <tbody>
              @if($carpets->count() > 0)
                @foreach($carpets as $carpet)
                  @php
                    $daysInStock = \Carbon\Carbon::parse($carpet->date)->diffInDays(now());
                    $agingClass = $daysInStock > 180 ? 'text-danger font-weight-bold' : ($daysInStock > 90 ? 'text-warning' : 'text-success');
                  @endphp
                  <tr>
                    <td class="font-weight-bold text-dark">{{ $carpet->carpet_no }}</td>
                    <td>
                       <div class="small">{{ $carpet->map_number }}</div>
                       <span class="badge badge-light border">{{ $carpet->quality->quality ?? '---' }}</span>
                    </td>
                    <td><span class="badge badge-soft-primary">{{ $carpet->type->carpet_type ?? '---' }}</span></td>
                    <td><span class="small">{{ $carpet->width }} × {{ $carpet->height }}</span></td>
                    <td class="font-weight-bold">{{ $carpet->area }} <small>m²</small></td>
                    <td>
                       <span class="small">{{ $carpet->field }} / {{ $carpet->margin }}</span>
                    </td>
                    <td>
                       <div class="font-weight-bold text-info">{{ $carpet->warehouse->name ?? 'نامشخص' }}</div>
                       <small class="text-muted">{{ $carpet->warehouse->location ?? '' }}</small>
                    </td>
                    <td class="{{ $agingClass }}">
                       {{ $daysInStock }} روز
                       @if($daysInStock > 180) <i class="fa fa-clock-o text-danger ml-1" title="بیش از ۶ ماه"></i> @endif
                    </td>
                    <td class="text-primary font-weight-bold font-italic">{{ number_format($carpet->total_price, 2) }} $</td>
                    <td class="hideOnPrint">
                       <div class="btn-group">
                           <a class="btn btn-sm btn-outline-info" href="/dashboard/carpet-stock-details/{{ $carpet->carpet_id }}" title="مشاهده جزئیات">
                               <i class="fa fa-eye"></i>
                           </a>
                           <button class="btn btn-sm btn-success ml-1" data-toggle="modal" data-target="#sale_modal"
                              onclick="
                              $('#carpet_id').val('{{$carpet->carpet_id}}');
                              $('#carpet_no').val('{{$carpet->carpet_no}}');
                              $('#carpet_type').val('{{$carpet->type->carpet_type ?? ''}}');
                              $('#carpet_quality').val('{{$carpet->quality->quality ?? ''}}');
                              $('#carpet_area').val('{{$carpet->area}}');
                              $('#carpet_height').val('{{$carpet->height}}');
                              $('#carpet_width').val('{{$carpet->width}}');
                              $('#total_price_cost').val('{{$carpet->total_price}}');
                              $('#price_per_meter').val('{{ $carpet->area > 0 ? round($carpet->total_price / $carpet->area, 2) : 0 }}');
                              " title="ثبت فروش">
                               <i class="fa fa-shopping-cart"></i> فروش
                           </button>
                       </div>
                    </td>
                  </tr>
                @endforeach
              @else
                <tr>
                  <td colspan="10" class="text-center py-5 text-muted">
                       <i class="fa fa-search fa-3x mb-3 d-block opacity-25"></i>
                       هیچ قالینی در حال حاضر در گدام موجود نیست
                  </td>
                </tr>
              @endif
              </tbody>
            </table>
            @if(!isset($search))
               <div class="d-flex justify-content-center mt-3">
                   {{ $carpets->appends(request()->input())->links() }}
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
    // Fix Select2 in Modal
    $(document).ready(function() {
        $('#type_id').select2();
        $('#warehouse_filter').select2();
        
        // Fix for Select2 in Bootstrap Modal
        // This prevents the search box from losing focus or auto-closing
        $.fn.modal.Constructor.prototype._enforceFocus = function() {};

        $('.select2-modal').each(function() {
            var $p = $(this).closest('.modal');
            $(this).select2({
                dropdownParent: $p,
                width: '100%',
                dropdownAutoWidth: true,
                placeholder: "انتخاب کنید..."
            });
        });

        // Re-initialize Select2 when modal is shown to fix positioning
        $('#sale_modal').on('shown.bs.modal', function () {
            $('.select2-modal').select2({
                dropdownParent: $('#sale_modal'),
                width: '100%'
            });
        });

        $('.status').show();
        window.setTimeout(function () {
            $(".status").fadeTo(500, 0).slideUp(500, function () {
                $(this).remove();
            });
        }, 2000);
    });
  </script>

  <script type="text/javascript">
      $(document).ready(function () {

          $('#carpet_height, #carpet_width').keyup(function () {
              var carpet_height = parseFloat($('#carpet_height').val()) || 0;
              var carpet_width = parseFloat($('#carpet_width').val()) || 0;
              var carpet_area = (carpet_width * carpet_height).toFixed(2);
              $('#carpet_area').val(carpet_area);
              
              var total_price = parseFloat($('#total_price_cost').val()) || 0;
              if(carpet_area > 0) {
                  $('#price_per_meter').val((total_price / carpet_area).toFixed(2));
              }
              
              var sale_cost_per_meter = parseFloat($("#sale_cost_per_meter").val()) || 0;
              if (sale_cost_per_meter > 0) {
                  $('#sale_cost_total').val((sale_cost_per_meter * carpet_area).toFixed(2));
              }
          });

          $('#invoice_id').change(function () {
              var customer_name = $('#invoice_id option:selected').attr('customer_name');
              $('#customer_name').val(customer_name);

              var customer_company = $('#invoice_id option:selected').attr('customer_company');
              $('#company_name').val(customer_company);

              var company_address = $('#invoice_id option:selected').attr('customer_address');
              $('#company_address').val(company_address);
          });

          $("#sale_cost_per_meter").keyup(function () {
              var sale_cost_per_meter = parseFloat($(this).val()) || 0;
              var carpet_area = parseFloat($('#carpet_area').val()) || 0;
              $('#sale_cost_total').val((sale_cost_per_meter * carpet_area).toFixed(2));
          });
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
