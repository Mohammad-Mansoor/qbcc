@extends('dsh.master')

@section('content')
  <div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <div class="card">
        <div class="card-header">
          <h5>فورم ایجاد کارمند</h5>
        </div>
        <div class="card-body">
          <div class="all-form-element-inner">
            @if(!$employeeEdit)
              <form action="/dashboard/office-employee" method="post" id="user-form" enctype="multipart/form-data">
                @csrf
                <div class="row">
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <label class="login2 pull-right pull-right-pro">دیپارتمنت</label>
                      <select name="department_id" id="" required class="form-control">
                        @foreach($department as $dep)
                          <option value="{{$dep->id}}">{{$dep->department}}</option>
                        @endforeach
                      </select>
                      @error('department_id') <p class="text-danger">{{trans('$message')}}</p> @enderror
                    </div>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <label class="pull-right">نام</label>
                      <input type="text" name="name" required placeholder="نام  را وارد کنید" class="form-control">
                      @error('name') <p class="text-danger">{{trans('message.' . $message)}}</p> @enderror
                    </div>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <label class="pull-right">وظیفه</label>
                      <input type="text" required name="job_title" placeholder="وظیفه  را وارد کنید" class="form-control">
                      @error('job_title') <p class="text-danger">{{trans('message.' . $message)}}</p> @enderror
                    </div>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <label class="pull-right">نمبر مبایل</label>
                      <input type="number" required name="phone" placeholder="نمبر مبایل  را وارد کنید"
                        class="form-control">
                      @error('phone') <p class="text-danger">{{trans('message.' . $message)}}</p> @enderror
                    </div>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <label class="login2 pull-right pull-right-pro">ایمیل</label>
                      <input type="email" name="email" placeholder="ایمیل ئ را وارد کنید" class="form-control">
                      @error('email') <p class="text-danger">{{trans('message.' . $message)}}</p> @enderror
                    </div>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <label class="login2 pull-right pull-right-pro">معاش</label>
                      <input type="number" step="0.01" required name="salary" id="emp_salary_in"
                             placeholder="مقدار معاش را وارد کنید" class="form-control">
                      @error('salary') <p class="text-danger">{{trans('message.' . $message)}}</p> @enderror
                    </div>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <label class="login2 pull-right pull-right-pro">ارز معاش</label>
                      <select name="currency_id" id="emp_currency_in" class="form-control">
                        @foreach($currencies ?? [] as $curr)
                          <option value="{{ $curr->id }}" data-rate="{{ $curr->exchange_rate }}"
                            {{ $curr->is_base_currency ? 'selected' : '' }}>
                            {{ $curr->code }} ({{ $curr->symbol }})
                          </option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <label class="login2 pull-right pull-right-pro">نرخ دالر</label>
                      <input type="number" step="0.00000001" min="0.00000001" name="exchange_rate"
                             id="emp_rate_in" value="1" class="form-control">
                    </div>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <label class="login2 pull-right pull-right-pro">معاش به حروف</label>
                      <input type="text" required name="in_words" placeholder="معاش به حروف وارد کنید" class="form-control">
                      @error('in_words') <p class="text-danger">{{trans('message.' . $message)}}</p> @enderror
                    </div>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <label class="login2 pull-right pull-right-pro">تصویر کارمند</label>
                      <input type="file" name="image" class="form-control">
                      @error('image') <p class="text-danger">{{trans('message.' . $message)}}</p> @enderror
                    </div>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <label class="pull-right">تاریخ شروع کار</label>
                      <input type="date" required name="from_date" class="form-control">
                      @error('start_date') <p class="text-danger">{{trans('message.' . $message)}}</p> @enderror
                    </div>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <label class="pull-right">تاریخ ختم کار</label>
                      <input type="date" required name="to_date" class="form-control">
                      @error('end_date') <p class="text-danger">{{trans('message.' . $message)}}</p> @enderror
                    </div>
                  </div>
                </div>
                {{-- FX Live preview row --}}
                <div class="row">
                  <div class="col-lg-4 col-md-6">
                    <div style="background:#f0fdf4;border:1px dashed #86efac;border-radius:8px;padding:6px 14px;font-size:0.85rem;margin-bottom:8px;">
                      <span style="color:#475569;">معاش معادل USD: </span>
                      <strong id="emp_salary_usd_preview" style="color:#16a34a;">$0.00</strong>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <button class="btn btn-white" type="reset">لغو</button>
                      <button class="btn btn-sm btn-info" type="submit"><i class="fa fa-save"></i>&nbsp; ثبت</button>
                    </div>
                  </div>
                </div>

              </form>
            @else
              <form action="/dashboard/office-employee/{{$employeeEdit->id}}" enctype="multipart/form-data" method="post"
                id="user-form">
                @method('PATCH')
                @csrf
                <div class="row">
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <label class="login2 pull-right pull-right-pro">دیپارتمنت</label>
                      <select name="department_id" id="" class="form-control" required>
                        @foreach($department as $dep)
                          <option {{($dep->id == $employeeEdit->department_id ? 'selected' : '')}} value="{{$dep->id}}">
                            {{$dep->department}}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <label class="pull-right">نام</label>
                      <input type="text" name="name" value="{{$employeeEdit->name}}" placeholder="نام  را وارد کنید"
                        required class="form-control">
                    </div>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <label class="pull-right">وظیفه</label>
                      <input type="text" name="job_title" value="{{$employeeEdit->job_title}}"
                        placeholder="وظیفه  را وارد کنید" required class="form-control">
                    </div>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <label class="pull-right">نمبر مبایل</label>
                      <input type="number" name="phone" value="{{$employeeEdit->phone}}"
                        placeholder="نمبر مبایل  را وارد کنید" required class="form-control">
                    </div>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <label class="login2 pull-right pull-right-pro">ایمیل</label>
                      <input type="email" name="email" value="{{$employeeEdit->email}}" placeholder="ایمیل تان را وارد کنید"
                        class="form-control">
                    </div>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <label class="login2 pull-right pull-right-pro">تصویر کارمند</label>
                      <input type="file" name="image" class="form-control">
                      <p>{{$employeeEdit->image}}</p>
                      @error('image') <p class="text-danger">{{trans('message.' . $message)}}</p> @enderror
                    </div>
                  </div>

                </div>
                <div class="row">
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <button class="btn btn-white" type="reset">لغو</button>
                      <button class="btn btn-sm btn-info" type="submit"><i class="fa fa-save"></i>&nbsp; ثبت
                      </button>
                    </div>
                  </div>
                </div>
              </form>
            @endif
          </div>
        </div>
      </div>

      <div class="card" id="employees">
        <div class="card-header">
          <h5>لیست کارمندان </h5>
          <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 hideOnPrint">
              <form action="/dashboard/office-employee/search" method="POST" id="dateSearch">
                @csrf

                <input type="text" placeholder="جستجو . . ." value="{{ Request::old('search') }}" name="search"
                  class="form-control" required>
                {{-- <a href=""><i class="fa fa-search"></i></a> --}}
              </form>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 hideOnPrint">
              <div class="btn-group hideOnPrint" id="exportButton" style="float: left; ">
                <a href="{{ route('payroll.index') }}" class="btn btn-sm btn-success" style="float: left; margin-left: 8px;">
                  <i class="fa fa-money"></i> لیست و اجرای معاشات
                </a>
                <div class="btn btn-sm btn-primary" style="float: left" onclick="printPage('employees')"><i
                    class="fa fa-print"></i> چاپ
                </div>

              </div>
            </div>
          </div>
          <div class="alert alert-success" style="display:none;" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                aria-hidden="true">&times;</span></button>
            کارمند موفقانه حذف شد
          </div>

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


        </div>
        <div class="card-body">
          <div class="static-table-list table-responsive" style="margin-top: 60px">
            <table class="table table-hover table-xs" id="employee_list">
              <thead>
                <tr>
                  <th>ای دی</th>
                  <th>نام</th>

                  <th>وظیفه</th>
                  <th>نمبر مبایل</th>
                  <th>ایمیل</th>

                  <th>دیپارتمنت</th>
                  <th>عکس</th>
                  @if(auth()->user()->role == 'SP' || auth()->user()->role == 'FI')
                    <th>کارمند بخش</th>
                  @endif
                  <th>ویرایش</th>
                  <th>قرار داد</th>

                  <th>حساب</th>


                  <!-- <th>حذف</th> -->

                </tr>
              </thead>
              <tbody>
              @if(method_exists($employees, 'total') ? $employees->total() > 0 : $employees->count() > 0)
                  @foreach($employees as $employee)
                    @if(auth()->user()->role == $employee->user_role || auth()->user()->role == 'SP' || auth()->user()->role == 'FI')
                      <tr class="ur{{ $employee->id }}">
                        <td>{{$employee->id}}</td>
                        <td>{{$employee->name}}</td>
                        <td>{{$employee->job_title}}</td>
                        <td>{{$employee->phone}}</td>
                        <td>{{$employee->email}}</td>
                        <td>{{$employee->department->department}}</td>
                        <td><img src="/{{$employee->image}}" style="height: 32px;" alt=""></td>
                        @if(auth()->user()->role == 'SP' || auth()->user()->role == 'FI')
                          @if($employee->user_role == 'SO')
                            <td> کارمند دفتر فروشات</td>
                          @elseif($employee->user_role == 'FI')
                            <td> کارمند مالی</td>
                          @else
                            <td> کارمند دفتر مرکزی</td>
                          @endif
                        @endif


                        <td><a href="/dashboard/office-employee/{{$employee->id}}/edit" class="btn btn-sm btn-info"><i
                              class="fa fa-pencil"></i>&nbsp; ویرایش</a></td>

                        <td><a href="/dashboard/employee-salary/{{$employee->id}}" class="btn btn-sm btn-primary"><i
                              class="fa fa-info-circle"></i>&nbsp;قرار داد </a></td>


                        <td><a href="/dashboard/employee-payments/{{$employee->id}}" class="btn btn-sm btn-warning"><i
                              class="fa fa-money"></i>&nbsp;حساب </a></td>

                      </tr>
                    @endif
                  @endforeach
              @else
                  <tr>
                    <td colspan="9" class="info">هیچ موردی دریافت نشد</td>
                  </tr>
              @endif
              </tbody>
            </table>
            <div class="mt-2 hideOnPrint">{{ $employees->links() }}</div>
          </div>
        </div>
      </div>

    </div>
  </div>

@endsection

@section('footer-plugins')
  <script>
    $(document).ready(function () {
      $("#employee_list").tableExport({
        headers: true, footers: true, formats: ["xlsx"],
        filename: "id", bootstrap: true, exportButtons: true,
        position: "bottom", ignoreRows: null, ignoreCols: null,
        trimWhitespace: true, RTL: true, sheetname: "id",
      });
      var $buttons = $('#employee_list').find('caption').children().detach();
      $buttons.appendTo('#exportButton');
    });

    // ─── Live FX Preview: Employee Create Form ──────────────────────────────
    function calcEmpSalaryUSD() {
      var sal  = parseFloat($('#emp_salary_in').val()) || 0;
      var rate = parseFloat($('#emp_rate_in').val()) || 1;
      $('#emp_salary_usd_preview').text('$' + (sal * rate).toFixed(2));
    }
    $('#emp_salary_in, #emp_rate_in').on('input', calcEmpSalaryUSD);
    $('#emp_currency_in').change(function() {
      var rate = $(this).find(':selected').data('rate') || 1;
      $('#emp_rate_in').val(rate);
      calcEmpSalaryUSD();
    });

    // Auto-show flash alerts
    $('.status').show();
    window.setTimeout(function() {
      $('.status').fadeTo(500, 0).slideUp(500, function() { $(this).remove(); });
    }, 6000);
  </script>
@endsection