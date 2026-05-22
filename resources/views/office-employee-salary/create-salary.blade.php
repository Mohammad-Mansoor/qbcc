@extends('dsh.master')
@section('title' , 'قرار داد کارمند')
@section('content')
  <!-- navbar -->
  <!-- form -->
  <div class="row">
    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
      <div class="card">
        <div class="card-header">
          @if(!$salaryEdit)
            <h5 class="text-right">قرار داد جدید</h5>
          @else
            <h5 class="text-right">ویرایش قرار داد</h5>
          @endif
        </div>
        <div class="card-body">
          <div class="all-form-element-inner">
            @if(!$salaryEdit)
              <form action="/dashboard/employee-salary" method="post">
                @csrf
                
                <input type="hidden" value="{{$employee->id}}" name="employee_id">
                
                <div class="form-group-inner">
                  <div class="row">
                    <div class="col-lg-10 col-md-10 col-sm-10 col-xs-12">
                      <label class="login2 pull-right pull-right-pro">
                        قرارداد نمبر</label>
                      <input type="text" style="direction: rtl" name="contract_number" readonly=""
                             value="{{$ContractNo}}"
                             id="salary" class="form-control">
                      @error('contract_number') <p
                              class="text-danger">{{trans('message.'.$message)}}</p>
                      @enderror
                    </div>
                    <div class="col-lg-10 col-md-10 col-sm-10 col-xs-12">
                      <label class="login2 pull-right pull-right-pro">
                        معاش</label>
                      <input type="number" step="0.01" style="direction: rtl" name="salary"
                             id="salary_create" class="form-control">
                      @error('salary') <p
                              class="text-danger">{{trans('message.'.$message)}}</p>
                      @enderror
                    </div>
                    <div class="col-lg-5 col-md-5 col-sm-10 col-xs-12 mt-1">
                      <label class="login2 pull-right pull-right-pro">ارز معاش</label>
                      <select name="currency_id" id="currency_id_salary" class="form-control">
                        @foreach($currencies ?? [] as $curr)
                          <option value="{{ $curr->id }}" data-rate="{{ $curr->exchange_rate }}"
                            {{ $curr->is_base_currency ? 'selected' : '' }}>
                            {{ $curr->code }} ({{ $curr->symbol }})
                          </option>
                        @endforeach
                      </select>
                    </div>
                    <div class="col-lg-5 col-md-5 col-sm-10 col-xs-12 mt-1">
                      <label class="login2 pull-right pull-right-pro">نرخ به دالر</label>
                      <input type="number" step="0.00000001" min="0.00000001" name="exchange_rate"
                             id="exchange_rate_salary" value="1" class="form-control">
                    </div>
                    <div class="col-lg-10 col-md-10 col-sm-10 col-xs-12 mt-1">
                      <div style="background:#f0fdf4;border:1px dashed #86efac;border-radius:8px;padding:6px 14px;font-size:0.85rem;">
                        <span style="color:#475569;">معادل USD: </span>
                        <strong id="salary_usd_preview" style="color:#16a34a;">$0.00</strong>
                      </div>
                    </div>
                    <br>

                    
                    <div class="col-lg-10 col-md-10 col-sm-12 col-xs-12">
                      <label class="login2 pull-right pull-right-pro">معاش به
                        حروف</label>
                      <input type="text" name="in_words"
                             placeholder="معاش به حروف وارد کنید"
                             required
                             class="form-control">
                      @error('in_words') <p
                              class="text-danger">{{trans('message.'.$message)}}</p>
                      @enderror
                    </div>
                    
                    <div class="col-lg-10 col-md-10 col-sm-10 col-xs-12">
                      <div class="row">
                        <div class="col-lg-6 col-md-6 col-xs-6 col-sm-6">
                          <label class="login2 pull-right pull-right-pro"> از
                            تاریخ </label>
                          <input type="date" name="from_date" class="form-control">
                          <br>
                          @error('from_date') <p
                                  class="text-danger">{{trans('message.'.$message)}}</p>
                          @enderror
                        </div>
                        <div class="col-lg-6 col-md-6 col-xs-6 col-sm-6">
                          <label class="login2 pull-right pull-right-pro">الی
                            تاریخ</label>
                          <input type="date" style="direction: rtl" name="to_date"
                                 id="date" class="form-control">
                          @error('to_date') <p
                                  class="text-danger">{{trans('message.'.$message)}}</p>
                          @enderror
                        </div>
                      </div>
                    
                    
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                      <div class="form-group fill">
                        <button class="btn btn-sm btn-default" type="reset">
                          انصراف
                        </button>
                        <button class="btn btn-sm btn-primary submit-btn"
                                type="submit">ذخیره
                        </button>
                      </div>
                    
                    </div>
                  </div>
                </div>
              </form>
            @else
              <form action="/dashboard/employee-salary/{{$salaryEdit->id}}" method="post">
                @csrf
                @method('PUT')
                
                <input type="hidden" value="{{$employee->id}}" name="employee_id">
                
                <div class="form-group-inner">
                  <div class="row">
                    <div class="col-lg-10 col-md-10 col-sm-10 col-xs-12">
                      <label class="login2 pull-right pull-right-pro">
                        قرارداد نمبر</label>
                      <input type="text" style="direction: rtl" name="contract_number" readonly=""
                             value="{{$salaryEdit->contract_number}}"
                             id="salary" class="form-control">
                      @error('contract_number') <p
                              class="text-danger">{{trans('message.'.$message)}}</p>
                      @enderror
                    </div>
                    <div class="col-lg-10 col-md-10 col-sm-10 col-xs-12">
                      <input type="number" value="{{$salaryEdit->salary}}"
                             style="direction: rtl" name="salary" id="salary"
                             class="form-control">
                      @error('salary') <p
                              class="text-danger">{{trans('message.'.$message)}}</p>
                      @enderror
                    </div>
                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                      <label class="login2 pull-right pull-right-pro">
                        معاش</label>
                    </div>
                    <div class="col-lg-10 col-md-10 col-sm-10 col-xs-12">
                      <input type="text" value="{{$salaryEdit->in_words}}"
                             style="direction: rtl" name="in_words" id="in_words"
                             class="form-control">
                      @error('in_words') <p
                              class="text-danger">{{trans('message.'.$message)}}</p>
                      @enderror
                    </div>
                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                      <label class="login2 pull-right pull-right-pro"> معاش به
                        حروف</label>
                    </div>
                    
                    <div class="col-lg-10 col-md-10 col-sm-10 col-xs-12">
                      <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                          <label class="login2 pull-right pull-right-pro"> از
                            تاریخ </label>
                          <input type="date" value="{{$salaryEdit->from_date}}"
                                 name="from_date" class="form-control">
                          @error('from_date') <p
                                  class="text-danger">{{trans('message.'.$message)}}</p>
                          @enderror
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                          <label class="login2 pull-right pull-right-pro">الی
                            تاریخ</label>
                          <input type="date" value="{{$salaryEdit->to_date}}"
                                 style="direction: rtl" name="to_date" id="date"
                                 class="form-control">
                          @error('to_date') <p
                                  class="text-danger">{{trans('message.'.$message)}}</p>
                          @enderror
                        </div>
                      </div>
                    
                    </div>
                  
                  </div>
                  <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                      <div class="form-group fill">
                        <button class="btn btn-sm btn-default" type="reset">
                          انصراف
                        </button>
                        <button class="btn btn-sm btn-primary submit-btn"
                                type="submit">ذخیره
                        </button>
                      </div>
                    
                    </div>
                  </div>
                </div>
              </form>
            @endif
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
      <div class="card">
        <div class="card-header">
          <h5>جزییات کارمند</h5>
        </div>
        <div class="card-body">
          <div class="static-table-list table-responsive">
            <table class="table table-hover table-xs">
              <tbody>
              <tr>
                <td>نام</td>
                <td>{{ $employee->name }}</td>
              </tr>
              <tr>
                <td>وظیفه</td>
                <td>{{ $employee->job_title }}</td>
              </tr>
              <tr>
                <td>شماره تماس</td>
                <td>{{ $employee->phone}}</td>
              </tr>
              <tr>
                <td>تاریخ شروع قرار داد</td>
                <td>{{ $salary->from_date}}</td>
              </tr>
              <tr>
                <td>تاریخ ختم قرار داد</td>
                <td>{{ $salary->to_date }}</td>
              </tr>
              <tr>
                <td>معاش</td>
                <td>{{ $salary->salary }}</td>
              </tr>
              
              
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <div class="card">
        <div class="card-header">
          <h5 class="text-center">قرار داد ها</h5>
          <div class="alert alert-success" style="display:none;" role="alert">
            <button type="button" class="close" data-dismiss="alert"
                    aria-label="Close"><span
                      aria-hidden="true">&times;</span></button>
            <p class="text-center"> قرار داد حذف شد</p>
          </div>
          
          @if(session("status"))
            <div class="alert alert-success status" style="display:none;"
                 role="alert">
              <button type="button" class="close" data-dismiss="alert"
                      aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
              <p class="text-center">{{session('status')}}</p>
            </div>
          
          @endif
          @if(session("error"))
            
            <div class="alert alert-danger status" style="display:none;color: white;background-color: darkred"
                 role="alert">
              <button type="button" class="close" data-dismiss="alert"
                      aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
              <p class="text-center">{{session('error')}}</p>
            </div>
          
          @endif
        </div>
        <div class="card-body">
          <div class="static-table-list table-responsive">
            <table class="table table-xs table-hover">
              <thead>
              <tr>
                
                <th>معاش</th>
                <th>معاش به حروف</th>
                <th>از تاریخ</th>
                <th>الی تاریخ</th>
                
                <th>ویرایش</th>
                <!-- <th class="text-center">حذف</th> -->
              </tr>
              </thead>
              <tbody>
              @forelse($salaries as $sl)
                <tr class="ur{{ $sl->id }}">
                  <td>
                    <span style="background:#ede9fe;color:#7c3aed;padding:2px 8px;border-radius:10px;font-size:0.8rem;font-weight:600;">
                      {{ $sl->contract_number ?? $sl->contact_number ?? '—' }}
                    </span>
                  </td>
                  <td>{{ number_format($sl->salary, 2) }}</td>
                  <td>
                    <span style="background:#dbeafe;color:#1e40af;padding:2px 8px;border-radius:10px;font-size:0.8rem;font-weight:600;">
                      {{ $sl->currency_code ?? '—' }}
                    </span>
                  </td>
                  <td dir="ltr">
                    @if($sl->salary_usd)
                      <span style="color:#059669;font-weight:700;">${{ number_format($sl->salary_usd, 2) }}</span>
                    @else
                      <span style="color:#94a3b8;">—</span>
                    @endif
                  </td>
                  <td>{{$sl->in_words}}</td>
                  <td>{{$sl->from_date}}</td>
                  <td>{{$sl->to_date}}</td>
                  <td><a href="/dashboard/employee-salary/{{$sl->id}}/edit"
                         class="btn btn-sm btn-info"><i class="fa fa-pencil"></i>&nbsp; ویرایش</a></td>
                </tr>
              @empty
                <tr><td colspan="8" class="text-center text-muted">هنوز موردی ثبت نشده است</td></tr>
              @endforelse
              
              </tbody>
            </table>
          
          </div>
        </div>
      </div>
    
    </div>
  </div>
@endsection

@section('scripts')
  <script>
      $('.status').show();
      window.setTimeout(function () {
          $(".status").fadeTo(500, 0).slideUp(500, function () {
              $(this).remove();
          });
      }, 10000);

      // ─── Live Salary FX Preview (Create) ──────────────────────────────────
      function calcSalaryPreview() {
          var salary = parseFloat($('#salary_create').val()) || 0;
          var rate   = parseFloat($('#exchange_rate_salary').val()) || 1;
          $('#salary_usd_preview').text('$' + (salary * rate).toFixed(2));
      }
      $('#salary_create, #exchange_rate_salary').on('input', calcSalaryPreview);
      $('#currency_id_salary').change(function() {
          var rate = $(this).find(':selected').data('rate') || 1;
          $('#exchange_rate_salary').val(rate);
          calcSalaryPreview();
      });

      function RemovePay(id) {
          swal({
              style: "text-center",
              text: "  دیپارتمنت حذف شود؟",
              buttons: true,
              dangerMode: true,
              buttons: {
                  confirm: {text: 'بلی', className: 'btn-danger'},
                  cancel: 'نخیر'
              },
          })
              .then((willDelete) => {
                  if (willDelete) {
                      $.ajax({
                          method: 'DELETE',
                          data: {'_token': '{{ csrf_token() }}'},
                          url: '/dashboard/employee-salary/' + id,
                          success: function (data) {
                              $('.ur' + id).hide();
                              $('.alert').show();
                              window.setTimeout(function () {
                                  $(".alert").fadeTo(500, 0).slideUp(500, function () {

                                      $(this).remove();
                                  });
                              }, 2000);
                          }
                      })
                  }
              });
      }
  </script>
@endsection
