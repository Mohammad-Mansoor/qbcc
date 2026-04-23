@extends('dsh.master')

@section('content')
  <!-- navbar -->
  <div class="row">
    @if(!isset($search))
      <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
        <div class="card">
          <div class="card-body">
            <div class="row align-items-center">
              <div class="col-8">
                
                <h4 class="text-c-yellow"> {{$credit}} &nbsp;$</h4>
              
              </div>
              <div class="col-4 text-right">
                <i class="feather icon-bar-chart-2 f-28"></i>
              </div>
            </div>
          </div>
          <div class="card-footer bg-c-yellow">
            <div class="row align-items-center">
              <div class="col-9">
                <h5 class="text-white m-b-0"> درامد پول</h5>
              </div>
              <div class="col-3 text-right">
                <i class="feather icon-trending-up text-white f-16"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
        <div class="card">
          <div class="card-body">
            <div class="row align-items-center">
              <div class="col-8">
                <h4 class="text-c-yellow"> {{round($debit_sum,2)}} &nbsp;$</h4>
              </div>
              <div class="col-4 text-right">
                <i class="feather icon-bar-chart-2 f-28"></i>
              </div>
            </div>
          </div>
          <div class="card-footer bg-c-yellow">
            <div class="row align-items-center">
              <div class="col-9">
                <h5 class="text-white m-b-0"> مصارف</h5>
              </div>
              <div class="col-3 text-right">
                <i class="feather icon-trending-up text-white f-16"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
        <div class="card">
          <div class="card-body">
            <div class="row align-items-center">
              <div class="col-8">
                
                <h4 class="text-c-yellow">    @if($cashbook)
                    @if($cashbook->count() > 0)
                      <b>{{round($cashbook->balance,2)}} &nbsp;$</b>
                    
                    @endif
                  @else
                    <b>0 &nbsp;$</b>
                  @endif</h4>
              
              </div>
              <div class="col-4 text-right">
                <i class="feather icon-bar-chart-2 f-28"></i>
              </div>
            </div>
          </div>
          <div class="card-footer bg-c-yellow">
            <div class="row align-items-center">
              <div class="col-9">
                <h5 class="text-white m-b-0">پول فعلی دخل</h5>
              </div>
              <div class="col-3 text-right">
                <i class="feather icon-trending-up text-white f-16"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    @else
      <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
        <div class="card">
          <div class="card-body">
            <div class="row align-items-center">
              <div class="col-8">
                <h4 class="text-c-yellow">{{$debits->sum('amount')}} &nbsp;$</h4>
              </div>
              <div class="col-4 text-right">
                <i class="feather icon-bar-chart-2 f-28"></i>
              </div>
            </div>
          </div>
          <div class="card-footer bg-c-yellow">
            <div class="row align-items-center">
              <div class="col-9">
                <h5 class="text-white m-b-0"> مچموع مصرف به دالر </h5>
              </div>
              <div class="col-3 text-right">
                <i class="feather icon-trending-up text-white f-16"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
        <div class="card">
          <div class="card-body">
            <div class="row align-items-center">
              <div class="col-8">
                <h4 class="text-c-yellow"> {{round($debits->sum('amount_af'),2)}} &nbsp;AF</h4>
              </div>
              <div class="col-4 text-right">
                <i class="feather icon-bar-chart-2 f-28"></i>
              </div>
            </div>
          </div>
          <div class="card-footer bg-c-yellow">
            <div class="row align-items-center">
              <div class="col-9">
                <h5 class="text-white m-b-0"> مجموع مصرف به افغانی </h5>
              </div>
              <div class="col-3 text-right">
                <i class="feather icon-trending-up text-white f-16"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
        <div class="card">
          <div class="card-body">
            <div class="row align-items-center">
              <div class="col-8">
              
              </div>
              <div class="col-4 text-right">
                <i class="feather icon-bar-chart-2 f-28"></i>
              </div>
            </div>
          </div>
          <div class="card-footer bg-c-yellow">
            <div class="row align-items-center">
              <div class="col-9">
                <h5 class="text-white m-b-0">{{$search}} </h5>
              </div>
              <div class="col-3 text-right">
                <i class="feather icon-trending-up text-white f-16"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    @endif
  </div>
  <div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 hideOnPrint">
      <div class="card">
        <div class="card-header">
          <h5>فورم مصرف</h5>
        </div>
        <div class="card-body">
          @if(!$expenseEdit)
            <form action="/dashboard/expenses" method="post">
              @csrf
              <br>
              <input type="hidden" value="{{$currency}}" id="currency">
              <div class="row">
                
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                  <div class="form-group fill">
                    <label class="">نام دریافت کننده</label>
                    <input id="name" name="name" type="text" class="form-control quantity">
                    <small class="text-danger">@error('name') {{ __('message.'.$message) }} @enderror</small>
                  </div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                  <div class="form-group fill">
                    <label class="">مصرف برای کجا</label>
                    <select name="expense_for_where" id="expense_for_where" class="form-control">
                      <option>دفتر</option>
                      <option>خانه</option>
                      <option>ساختمان</option>
                    </select>
                    <small class="text-danger">@error('expense_for_where') {{ __('message.'.$message) }}@enderror
                    </small>
                  </div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                  <div class="form-group fill">
                    <label class="">نوع مصرف</label>
                    <select name="expense_type" id="expense_type" class="form-control">
                      
                      <option>خوراکه</option>
                      <option>داکتری</option>
                      <option>معاشات</option>
                      <option>شست</option>
                      <option>تیاری</option>
                      <option>دیزاین قالین</option>
                      <option>صادرات</option>
                      <option>متفرقه</option>
                      <option>لباس و لوازم</option>
                      <option>انترنت و کریدیت</option>
                      <option>کرایه</option>
                      <option>تجهیزات</option>
                      <option>خرید قالین</option>
                      <option>خرید مواد خام قالین</option>
                      <option>اداری</option>
                      <option>درس و ورزش</option>
                    
                    </select>
                    <small class="text-danger">@error('expense_type') {{ __('message.'.$message) }} @enderror</small>
                  </div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                  <div class="form-group fill">
                    
                    <label class="">تاریخ</label>
                    <input id="date" name="date" type="date" class="form-control quantity">
                    <small class="text-danger">@error('date') {{ __('message.'.$message) }} @enderror</small>
                  </div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                  <div class="form-group fill">
                    
                    <label class="">مقدار پول به افغانی</label>
                    <input type="text" dir="ltr" name="amount_af" value="{{ old('amount') }}" id="fp"
                           class="form-control">
                    <input type="hidden" name="amount" dir="ltr" id="mainP" value="{{ old('amount') }}"
                           class="form-control">
                    <small class="text-danger">@error('amount') {{ __('message.'.$message) }} @enderror</small>
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
            <form action="/dashboard/expenses/{{$expenseEdit->id}}" method="post">
              @csrf
              @method('PUT')
              <input type="hidden" value="{{$currency}}" id="currency">
              <br>
              <div class="row">
                
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                  <div class="form-group fill">
                    <label class="">نام دریافت کننده</label>
                    <input id="name" name="name" type="text" value="{{$expenseEdit->name}}"
                           class="form-control quantity">
                    <small class="text-danger">@error('name') {{ __('message.'.$message) }} @enderror</small>
                  </div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                  <div class="form-group fill">
                    <label class="">مصرف برای کجا</label>
                    <select name="expense_for_where" id="expense_for_where" class="form-control">
                      <option {{($expenseEdit->expense_for_where == 'دفتر' ? 'selected' : '')}}>دفتر</option>
                      <option {{($expenseEdit->expense_for_where == 'خانه' ? 'selected' : '')}}>خانه</option>
                      <option {{($expenseEdit->expense_for_where == 'ساختمان' ? 'selected' : '')}}>ساختمان</option>
                    </select>
                    <small class="text-danger">@error('expense_for_where') {{ __('message.'.$message) }}@enderror
                    </small>
                  </div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                  <div class="form-group fill">
                    <label class="">نوع مصرف</label>
                    <select name="expense_type" id="expense_type" class="form-control">
                      
                      <option {{($expenseEdit->expense_type == 'خوراکه' ? 'selected' : '')}}>خوراکه</option>
                      <option {{($expenseEdit->expense_type == 'داکتری' ? 'selected' : '')}}>داکتری</option>
                      <option {{($expenseEdit->expense_type == 'معاشات' ? 'selected' : '')}}>معاشات</option>
                      <option {{($expenseEdit->expense_type == 'شست' ? 'selected' : '')}}>شست</option>
                      <option {{($expenseEdit->expense_type == 'تیاری' ? 'selected' : '')}}>تیاری</option>
                      <option {{($expenseEdit->expense_type == 'دیزاین قالین' ? 'selected' : '')}}>دیزاین قالین</option>
                      <option {{($expenseEdit->expense_type == 'صادرات' ? 'selected' : '')}}>صادرات</option>
                      <option {{($expenseEdit->expense_type == 'متفرقه' ? 'selected' : '')}}>متفرقه</option>
                      <option {{($expenseEdit->expense_type == 'لباس و لوازم' ? 'selected' : '')}}>لباس و لوازم</option>
                      <option {{($expenseEdit->expense_type == 'انترنت و کریدیت' ? 'selected' : '')}}>انترنت و کریدیت
                      </option>
                      <option {{($expenseEdit->expense_type == 'کرایه' ? 'selected' : '')}}>کرایه</option>
                      <option {{($expenseEdit->expense_type == 'تجهیزات' ? 'selected' : '')}}>تجهیزات</option>
                      <option {{($expenseEdit->expense_type == 'خرید قالین' ? 'selected' : '')}}>خرید قالین</option>
                      <option {{($expenseEdit->expense_type == 'خرید مواد خام قالین' ? 'selected' : '')}}>خرید مواد خام
                        قالین
                      </option>
                      <option {{($expenseEdit->expense_type == 'اداری' ? 'selected' : '')}}>اداری</option>
                      <option {{($expenseEdit->expense_type == 'درس و ورزش' ? 'selected' : '')}}>درس و ورزش</option>
                    </select>
                    <small class="text-danger">@error('expense_type') {{ __('message.'.$message) }} @enderror</small>
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
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                  <div class="form-group fill">
                    
                    <label class="">مقدار</label>
                    <input type="text" dir="ltr" name="amount_af" value="{{ $expenseEdit->amount_af }}" id="fp"
                           class="form-control">
                    <input type="hidden" name="amount" dir="ltr" id="mainP" value="{{ $expenseEdit->amount }}"
                           class="form-control">
                    <small class="text-danger">@error('amount') {{ __('message.'.$message) }} @enderror</small>
                  </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                  <div class="form-group fill">
                    
                    <label class="">توضیحات</label>
                    <textarea id="description" name="description" rows="1"
                              class="form-control">{{ $expenseEdit->description }}</textarea>
                    <small class="text-danger">@error('description') {{ __('message.'.$message) }} @enderror</small>
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
              <div style="float: left" class="hideOnPrint">
                <a class="btn btn-sm btn-info" href="/dashboard/all-expenses"><i class="fa fa-eye"></i>
                  &nbsp;تمام مصارف </a>
              </div>
            </div>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 hideOnPrint">
              
              <form action="/dashboard/office-cash-book/search-date-range" method="POST" id="dateSearch">
                @csrf
                <div class="row">
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 hideOnPrint">
                    <input type="submit" value="جستجو" class="date-submit btn btn-block btn-sm btn-primary"
                           style="margin-top: 30px;">
                  </div>
                  <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                    <span class="date-label">شروع</span><input type="date" @isset($start) value="{{$start}}"
                                                               @endisset name="from_date" class="form-control" required>
                  </div>
                  <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                    <span class="date-label">ختم</span><input type="date" name="to_date" @isset($end)
                    value="{{$end}}" @endisset class="form-control" required>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 hideOnPrint">
                    <label>مصرف برای کجای</label>
                    <select name="search_for_where" required class="form-control" id="">
                      <option>دفتر</option>
                      <option>خانه</option>
                      <option>ساختمان</option>
                      <option>همه مصارف</option>
                    </select>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 hideOnPrint">
                    <label>نوعیت مصرف</label>
                    <select name="expense_type" required class="form-control" id="expense_type2">
                      <option>همه مصارف</option>
                      <option>خوراکه</option>
                      <option>داکتری</option>
                      <option>معاشات</option>
                      <option>شست</option>
                      <option>تیاری</option>
                      <option>دیزاین قالین</option>
                      <option>صادرات</option>
                      <option>متفرقه</option>
                      <option>لباس و لوازم</option>
                      <option>انترنت و کریدیت</option>
                      <option>کرایه</option>
                      <option>تجهیزات</option>
                      <option>خرید قالین</option>
                      <option>خرید مواد خام قالین</option>
                      <option>اداری</option>
                      <option>درس و ورزش</option>
    
                    </select>
                  </div>
                  
                
                </div>
              </form>
            </div>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 pull-right">
              <form action="/dashboard/office-cash-book/search" method="POST" id="dateSearch">
                @csrf
                <div class="row">
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 hideOnPrint">
                    <input type="submit" value="جستجو" class="date-submit btn btn-primary btn-sm btn-block"
                           style="margin-top: 35px;">
                  </div>
                  <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                    <span class="date-label">نام</span><input type="text" required name="search" placeholder="نام"
                                                              @isset($search)
                                                              value="{{$search}}" @endisset class="form-control">
                  </div>
                  <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                    <span class="date-label">شروع</span><input type="date" @isset($start) value="{{$start}}"
                                                               @endisset name="from_date" class="form-control" required>
                  </div>
                  <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                    <span class="date-label">ختم</span><input type="date" name="to_date" @isset($end)
                    value="{{$end}}" @endisset class="form-control" required>
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
              <th>نام</th>
              
              <th>مقدار پول به افغانی</th>
              <th>مقدار پول به دالر</th>
              <th>نوع مصرف</th>
              <th>مصرف برای کجا</th>
              <th>توضیحات</th>
              <th>تاریخ</th>
              <th class="printTitle">ویرایش</th>
              <th class="printTitle">حساب</th>
            </tr>
            </thead>
            <tbody>
            @foreach($debits as $debit)
              
              <tr>
                
                <td style="direction: ltr">{{$debit->name}}</td>
                <td> @if($debit->amount_af){{$debit->amount_af}} @else 0 @endif </td>
                <td> {{$debit->amount}} </td>
                <td>{{$debit->expense_type}}</td>
                <td>{{$debit->expense_for_where}}</td>
                <td>{{$debit->description}}</td>
                <td>{{$debit->date}}</td>
                
                <td class="printTitle hideOnPrint">
                  <a href="/dashboard/office-cash-book/{{$debit->id}}/edit"
                     class="btn btn-info btn-sm">
                    <i class="fa fa-pencil"></i> &nbsp; ویرایش
                  </a>
                </td>
                <td class="printTitle hideOnPrint">
                  <a href="/dashboard/office-cash-book/{{$debit->id}}"
                     class="btn btn-info btn-sm">
                    <i class="fa fa-pencil"></i> &nbsp; حساب
                  </a>
                </td>
              
              </tr>
            @endforeach
            
            <tr>
              <td><b>مجموع مصارف به افغانی</b></td>
              <td> {{$debits->sum('amount_af')}}</td>
            </tr>
            <tr>
              <td><b>مجموع مصارف به دالر</b></td>
              <td>{{$debits->sum('amount')}} </td>
            </tr>
            
            
            </tbody>
          </table>
          </div>
          @if(isset($pagination))
            <p class="hideOnPrint">{{$debits->links()}}</p>
          @endif
        </div>
      </div>
    </div>
  </div>



@endsection


@section('footer-plugins')
  
  
  
  <script>
    $('#expense_type').select2();
     $('#expense_type2').select2();
      $(document).ready(function () {
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