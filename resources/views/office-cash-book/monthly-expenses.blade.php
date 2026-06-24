@extends('dsh.master')

@section('content')
  
  <div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 hideOnPrint">
      <div class="card">
        <div class="card-header">
          <h5>فورم مصرف</h5>
        </div>
        <div class="card-body">
          @can('create_monthly_expense')
          @if(!$expenseEdit)
            <form action="/dashboard/monthly-expenses" method="post">
              @csrf
              <br>
              
              <div class="row">
                
                <div class="col-xs-2">
                  <label class="pull-right">نرخ دالر</label>
                  <input type="text" name="dollar_rate" value="{{$currency}}"
                         class="form-control">
                  @error('dollar_rate') <p class="text-danger">
                    {{trans('message.'.$message)}}</p>
                  @enderror
                </div>
                <div class="col-xs-2">
                  <label class="pull-right">مقدار پول</label>
                  <input type="text" name="amount" value="{{old('amount')}}"
                         placeholder="مبلغ پول " class="form-control">
                  @error('amount') <p class="text-danger">
                    {{trans('message.'.$message)}}</p>
                  @enderror
                </div>
                <div class="col-xs-2">
                  <label class="pull-right">نوع پول</label>
                  <select name="currency" id="" class="form-control">
                    <option disabled>انتخاب</option>
                    <option value="1">افغانی</option>
                    <option value="2">دالر</option>
                    <option value="3">کلدار</option>
                  </select>
                  
                  @error('type') <p class="text-danger">
                    {{trans('message.'.$message)}}</p>
                  @enderror
                </div>
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                  <div class="form-group fill">
                    <label class="">کتگوری مصرف</label>
                    <select name="category" id="expense_type" class="form-control">
                      
                      <option>خوراکه</option>
                      <option>متفرقه دفتر</option>
                      <option>کرایه و برق</option>
                      <option>ترانسپورت</option>
                      <option>برداشت</option>
                      <option>ترمیمات و تیل</option>
                      <option>معاشات</option>
                      <option>اجوره</option>
                    
                    </select>
                    <small class="text-danger">@error('category') {{ __('message.'.$message) }} @enderror</small>
                  </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                  <div class="form-group fill">
                    
                    <label class="">توضیحات</label>
                    <textarea id="description" name="description" rows="1"
                              class="form-control">{{ old('description') }}</textarea>
                    <small class="text-danger">@error('description') {{ __('message.'.$message) }} @enderror</small>
                  </div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                  <div class="form-group fill">
                    
                    <label class="">تاریخ</label>
                    <input id="date" name="date" type="date" class="form-control quantity">
                    <small class="text-danger">@error('date') {{ __('message.'.$message) }} @enderror</small>
                  </div>
                </div>
              
              
              </div>
              <div class="row">
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                  <div class="form-group fill">
                    
                    <button class="btn btn-warning btn-sm" type="button">انصراف</button>
                    <button class="btn btn-primary btn-sm" type="submit"><span class="fa fa-save"></span> ذخیره</button>
                  </div>
                </div>
              </div>
            </form>
          @else
            <form action="/dashboard/monthly-expenses/{{$expenseEdit->id}}" method="post">
              @csrf
              @method('PUT')
              <br>
              <div class="row">
                <div class="col-xs-2">
                  <label class="pull-right">نرخ دالر</label>
                  <input type="text" name="dollar_rate" value="{{$expenseEdit->dollar_rate}}"
                         class="form-control">
                  @error('dollar_rate') <p class="text-danger">
                    {{trans('message.'.$message)}}</p>
                  @enderror
                </div>
                <div class="col-xs-2">
                  <label class="pull-right">مقدار پول</label>
                  <input type="text" name="amount" value="{{$expenseEdit->amount}}"
                         placeholder="مبلغ پول " class="form-control">
                  @error('amount') <p class="text-danger">
                    {{trans('message.'.$message)}}</p>
                  @enderror
                </div>
                <div class="col-xs-2">
                  <label class="pull-right">نوع پول</label>
                  <select name="currency" id="" class="form-control">
                    <option disabled>انتخاب</option>
                    <option value="1" {{($expenseEdit->currency == 1 ? 'selected' : '')}}>افغانی</option>
                    <option value="2" {{($expenseEdit->currency == 2 ? 'selected' : '')}}>دالر</option>
                    <option value="3" {{($expenseEdit->currency == 3 ? 'selected' : '')}}>کلدار</option>
                  </select>
                  
                  @error('type') <p class="text-danger">
                    {{trans('message.'.$message)}}</p>
                  @enderror
                </div>
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                  <div class="form-group fill">
                    <label class="">کتگوری مصرف</label>
                    <select name="category" id="expense_type" class="form-control">
                      
                      <option {{($expenseEdit->category == 'خوراکه' ? 'selected' : '')}}>خوراکه</option>
                      <option {{($expenseEdit->category == 'متفرقه دفتر' ? 'selected' : '')}}>متفرقه دفتر</option>
                      <option {{($expenseEdit->category == 'کرایه و برق' ? 'selected' : '')}}>کرایه و برق</option>
                      <option {{($expenseEdit->category == 'ترانسپورت' ? 'selected' : '')}}>ترانسپورت</option>
                      <option {{($expenseEdit->category == 'برداشت' ? 'selected' : '')}}>برداشت</option>
                      <option {{($expenseEdit->category == 'ترمیمات و تیل' ? 'selected' : '')}}>ترمیمات و تیل</option>
                      <option {{($expenseEdit->category == 'معاشات' ? 'selected' : '')}}>معاشات</option>
                      <option {{($expenseEdit->category == 'اجوره' ? 'selected' : '')}}>اجوره</option>
                    
                    </select>
                    <small class="text-danger">@error('category') {{ __('message.'.$message) }} @enderror</small>
                  </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                  <div class="form-group fill">
                    
                    <label class="">توضیحات</label>
                    <textarea id="description" name="description" rows="1"
                              class="form-control">{{$expenseEdit->description}}</textarea>
                    <small class="text-danger">@error('description') {{ __('message.'.$message) }} @enderror</small>
                  </div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                  <div class="form-group fill">
                    
                    <label class="">تاریخ</label>
                    <input id="date" name="date" type="date" value="{{$expenseEdit->date}}"
                           class="form-control quantity">
                    <small class="text-danger">@error('date') {{ __('message.'.$message) }} @enderror</small>
                  </div>
                </div>
              
              
              </div>
              <div class="row">
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                  <div class="form-group fill">
                    
                    <button class="btn btn-warning btn-sm" type="button">انصراف</button>
                    <button class="btn btn-primary btn-sm" type="submit"><span class="fa fa-save"></span> ذخیره</button>
                  </div>
                </div>
              </div>
            </form>
          @endif
          @endcan
        </div>
      </div>
    </div>
  </div>
  <div class="row" id="expensePrint">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      
      <div class="card">
        <div class="card-header">
          <div class="row">
            <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9 pull-right hideOnPrint"></div>
            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 pull-right hideOnPrint">
              
              <div class="btn-group hideOnPrint" id="exportButton" style="float: left; ">
                <div class="btn btn-sm btn-primary" style="float: left" onclick="printPage('expensePrint')"><i
                          class="fa fa-print"></i> چاپ
                </div>
              
              </div>
            </div>
            
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 pull-right hideOnPrint">
              <form action="/dashboard/monthly-expenses-search" method="post" id="dateSearch">
                @csrf
                <div class="row">
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 hideOnPrint">
                    <input type="submit" value="جستجو" class="date-submit btn btn-primary btn-sm btn-block"
                           style="margin-top: 20px;">
                  </div>
                  
                  <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                    <span class="date-label">ماه</span>
                    <select name="month" id="month" class="form-control">
                      <option value="1">1-جنوری</option>
                      <option value="2">2-فبروری</option>
                      <option value="3">3-مارچ</option>
                      <option value="4">4-اپریل</option>
                      <option value="5">5-می</option>
                      <option value="6">6-جون</option>
                      <option value="7">7-جولای</option>
                      <option value="8">8-اگست</option>
                      <option value="9">9-سپتمبر</option>
                      <option value="10">10-اکتبر</option>
                      <option value="11">11-نومبر</option>
                      <option value="12">12-دسمبر</option>
                    </select>
                  </div>
                  <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                    <span class="date-label">سال</span>
                    <select name="year" id="year" class="form-control">
                      <option value="2022">2022</option>
                      <option value="2023">2023</option>
                      <option value="2024">2024</option>
                      <option value="2025">2025</option>
                      <option value="2026">2026</option>
                      <option value="2027">2027</option>
                      <option value="2028">2028</option>
                      <option value="2029">2029</option>
                      <option value="2030">2030</option>
                      <option value="2031">2031</option>
                      <option value="2032">2032</option>
                    </select>
                  </div>
                </div>
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
          @if(session("error"))
            
            <div class="alert alert-danger status" style="display:none;" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
              <p class="text-center">{{session('error')}}</p>
            </div>
          
          @endif
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover table-xs" id="expense_list">
              <thead>
              <tr>
                <th>مقدار</th>
                <th>کتگوری مصرف</th>
                <th>توضیحات</th>
                <th>تاریخ</th>
                @if(auth()->user()->role == 'SP' || auth()->user()->role == 'FI')
                  <th>دفتر</th>
                @endif
                <th class="printTitle">عملیات</th>
              
              </tr>
              </thead>
              <tbody>
              @if(auth()->user()->role == 'SP' || auth()->user()->role == 'FI')
                @foreach($expenses_sp as $exp)
                  
                  <tr>
                    
                    <td style="direction: ltr">{{$exp->amount}} @if ($exp->currency == 1)
                        افغانی @elseif($exp->currency == 2) دالر @else کلدار @endif</td>
                    <td>{{$exp->category}}</td>
                    <td>{{$exp->description}}</td>
                    <td>{{$exp->date}}</td>
                    <td>@if($exp->user_role == 'SO') دفتر فروشات @elseif($exp->user_role == 'CO') دفتر مرکزی @else سوپر
                      ادمین @endif</td>
                    
                    <td class="printTitle hideOnPrint">
                      @can('manage_monthly_expense_payments')
                      <a href="/dashboard/monthly-expenses/{{$exp->id}}/edit"
                         class="btn btn-info btn-sm">
                        <i class="fa fa-pencil"></i> &nbsp; ویرایش
                      </a>
                       @if(auth()->user()->role == 'SP')
                      <button onclick="deleteExpense({{$exp->id}})"
                              class="btn btn-danger btn-sm ">
                        <i
                                class="fa fa-tick"></i>حذف
                      </button>
                      @endif
                      @endcan
                    </td>
                  
                  </tr>
                @endforeach
              @else
                @foreach($expenses as $exp)
                  
                  <tr>
                    
                    <td style="direction: ltr">{{$exp->amount}} @if ($exp->currency == 1)
                        افغانی @elseif($exp->currency == 2) دالر @else کلدار @endif</td>
                    <td>{{$exp->category}}</td>
                    <td>{{$exp->description}}</td>
                    <td>{{$exp->date}}</td>
                    
                    <td class="printTitle hideOnPrint">
                      @can('manage_monthly_expense_payments')
                      <a href="/dashboard/monthly-expenses/{{$exp->id}}/edit"
                         class="btn btn-info btn-sm">
                        <i class="fa fa-pencil"></i> &nbsp; ویرایش
                      </a>
                      <button onclick="deleteExpense({{$exp->id}})"
                              class="btn btn-danger btn-sm ">
                        <i
                                class="fa fa-tick"></i>حذف
                      </button>
                      @endcan
                    </td>
                  
                  </tr>
                @endforeach
              @endif
              @if(auth()->user()->role == 'SP' || auth()->user()->role == 'FI')
                <tr>
                  <td><b>جمله خوراکه</b></td>
                  <td> {{$khoraka_sp_af}} افغانی</td>
                  <td> {{$khoraka_sp_usd}} دالر</td>
                  <td> {{$khoraka_sp_cd}} کلدار</td>
                </tr>
                <tr>
                  <td><b>جمله متفرقه دفتر</b></td>
                  <td> {{$motafrqa_sp_af}} افغانی</td>
                  <td> {{$motafrqa_sp_usd}} دالر</td>
                  <td> {{$motafrqa_sp_cd}} کلدار</td>
                </tr>
                <tr>
                  <td><b>جمله کرایه و برق</b></td>
                  <td> {{$keraia_sp_af}} افغانی</td>
                  <td> {{$keraia_sp_usd}} دالر</td>
                  <td> {{$keraia_sp_cd}} کلدار</td>
                </tr>
                <tr>
                  <td><b>جمله ترانسپورت</b></td>
                  <td> {{$transport_sp_af}} افغانی</td>
                  <td> {{$transport_sp_usd}} دالر</td>
                  <td> {{$transport_sp_cd}} کلدار</td>
                </tr>
                <tr>
                  <td><b>جمله برداشت</b></td>
                  <td> {{$bardasht_sp_af}} افغانی</td>
                  <td> {{$bardasht_sp_usd}} دالر</td>
                  <td> {{$bardasht_sp_cd}} کلدار</td>
                </tr>
                <tr>
                  <td><b>جمله ترمیمات و تیل</b></td>
                  <td> {{$tel_sp_af}} افغانی</td>
                  <td> {{$tel_sp_usd}} دالر</td>
                  <td> {{$tel_sp_cd}} کلدار</td>
                </tr>
                <tr>
                  <td><b>جمله معاشات</b></td>
                  <td> {{$mashat_sp_af}} افغانی</td>
                  <td> {{$mashat_sp_usd}} دالر</td>
                  <td> {{$mashat_sp_cd}} کلدار</td>
                </tr>
                <tr>
                  <td><b>جمله اجوره</b></td>
                  <td> {{$ajora_sp_af}} افغانی</td>
                  <td> {{$ajora_sp_usd}} دالر</td>
                  <td> {{$ajora_sp_cd}} کلدار</td>
                </tr>
              @else
                <tr>
                  <td><b>جمله خوراکه</b></td>
                  <td> {{$khoraka_af}} افغانی</td>
                  <td> {{$khoraka_usd}} دالر</td>
                  <td> {{$khoraka_cd}} کلدار</td>
                </tr>
                <tr>
                  <td><b>جمله متفرقه دفتر</b></td>
                  <td> {{$motafrqa_af}} افغانی</td>
                  <td> {{$motafrqa_usd}} دالر</td>
                  <td> {{$motafrqa_cd}} کلدار</td>
                </tr>
                <tr>
                  <td><b>جمله کرایه و برق</b></td>
                  <td> {{$keraia_af}} افغانی</td>
                  <td> {{$keraia_usd}} دالر</td>
                  <td> {{$keraia_cd}} کلدار</td>
                </tr>
                <tr>
                  <td><b>جمله ترانسپورت</b></td>
                  <td> {{$transport_af}} افغانی</td>
                  <td> {{$transport_usd}} دالر</td>
                  <td> {{$transport_cd}} کلدار</td>
                </tr>
                <tr>
                  <td><b>جمله برداشت</b></td>
                  <td> {{$bardasht_af}} افغانی</td>
                  <td> {{$bardasht_usd}} دالر</td>
                  <td> {{$bardasht_cd}} کلدار</td>
                </tr>
                <tr>
                  <td><b>جمله ترمیمات و تیل</b></td>
                  <td> {{$tel_af}} افغانی</td>
                  <td> {{$tel_usd}} دالر</td>
                  <td> {{$tel_cd}} کلدار</td>
                </tr>
                <tr>
                  <td><b>جمله معاشات</b></td>
                  <td> {{$mashat_af}} افغانی</td>
                  <td> {{$mashat_usd}} دالر</td>
                  <td> {{$mashat_cd}} کلدار</td>
                </tr>
                <tr>
                  <td><b>جمله اجوره</b></td>
                  <td> {{$ajora_af}} افغانی</td>
                  <td> {{$ajora_usd}} دالر</td>
                  <td> {{$ajora_cd}} کلدار</td>
                </tr>
              @endif
              
              </tbody>
            </table>
          </div>
          @if(!$search)
          @if(auth()->user()->role == 'SP' || auth()->user()->role == 'FI')
            <p>{{$expenses_sp->links()}}</p>
          @else
            <p>{{$expenses->links()}}</p>
          @endif
          @endif
        </div>
      </div>
    </div>
  </div>



@endsection

@section('scripts')
  <script>


      function deleteExpense(id) {

          swal({
              text: "مطمعین هستید ؟",
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
                          type: 'DELETE',
                          data: {
                              '_token': '{{csrf_token()}}',
                          },
                          url: '/dashboard/monthly-expenses/' + id,
                          success: function (res) {

                              if (res.status == 'success') {
                                  $('.ur' + id).hide();
                                  $('.alert-success').show();
                                  window.location = '/dashboard/monthly-expenses'
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
@section('footer-plugins')
  
  
  
  <script>
      $(document).ready(function () {
          $('#month').select2();
          $('#year').select2();


          $("#expense_list").tableExport({
              headers: true,                      // (Boolean), display table headers (th or td elements) in the <thead>, (default: true)
              footers: true,                      // (Boolean), display table footers (th or td elements) in the <tfoot>, (default: false)
              formats: ["xlsx"],                  // (String[]), filetype(s) for the export, (default: ['xlsx', 'csv', 'txt'])
              filename: "id",                     // (id, String), filename for the downloaded file, (default: 'id')
              bootstrap: true,                   // (Boolean), style buttons using bootstrap, (default: true)
              exportButtons: true,                // (Boolean), automatically generate the built-in export buttons for each of the specified formats (default: true)
              position: "bottom",                 // (top, bottom), position of the caption element relative to table, (default: 'bottom')
              ignoreRows: null,                   // (Number, Number[]), row indices to exclude from the exported file(s) (default: null)
              ignoreCols: null,                   // (Number, Number[]), column indices to exclude from the exported file(s) (default: null)
              trimWhitespace: true,               // (Boolean), remove all leading/trailing newlines, spaces, and tabs from cell text in the exported file(s) (default: false)
              RTL: true,                         // (Boolean), set direction of the worksheet to right-to-left (default: false)
              sheetname: "id",

          });
          var $buttons = $('#expense_list').find('caption').children().detach();
          // Append the buttons to an element of your choosing
          $buttons.appendTo('#exportButton');

      });
  
  
  </script>


@endsection