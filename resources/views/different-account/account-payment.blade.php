@extends('dsh.master')
@section('title' , 'حسابات متفرقه')
@section('content')
  
  
  <!-- navbar -->
  
  <div id="PaidToDA">
    
    <div class="row">
      <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
          <div class="card-header">
            
            @if(session("status"))
              <div class="alert alert-success status text-center" style="display:none;" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                          aria-hidden="true">&times;</span></button>
                {{session('status')}}
              </div>
            
            @endif
            @if(session("error"))
              
              <div class="alert alert-danger status text-center" style="display:none;" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                          aria-hidden="true">&times;</span></button>
                {{session('error')}}
              </div>
            
            @endif
          </div>
          
          <div class="card-body">
            <div class="row">
              <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                <div class="table-responsive">
                  <table class="table table-xs table-hover">
                    <thead>
                    
                    </thead>
                    <tbody>
                    
                    <tr>
                      <td><b>نام</b></td>
                      <td>{{$account->name}}</td>
                    </tr>
                    <tr>
                      <td><b>ادرس</b></td>
                      <td> {{$account->address}}</td>
                    </tr>
                    <tr>
                      <td><b>شماره تماس</b></td>
                      <td><i class="fa fa-phone"></i> {{$account->phone}}</td>
                    </tr>
                    
                    </tbody>
                  </table>
                </div>
              </div>
              
              <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6"></div>
              
              <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                <h4>ACCOUNT #: {{$account->id}}</h4>
              </div>
            </div>
            <hr>
            <div class="row">
              
              <div class="col-sm-12 hideOnPrint">
                <div class="all-form-element-inner">
                  @if(!$paymentEdit)
                    <form action="/dashboard/different-account-payments" method="post">
                      @csrf
                      <input type="hidden" name="account_id" value="{{$account->id}}">
                      
                      <div class="row" style=" display:flex;justify-content:center">
                        <div class="col-sm-12">
                          <div class="form-group-inner">
                            <div class="row"
                                 style=" display:flex;justify-content:space-around">
                              <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                                <label class="pull-left">مقدار پول به دالر</label>
                                
                                <input type="text" name="amount"
                                       placeholder="مبلغ پول به دالر" class="form-control">
                                @error('amount') <p class="text-danger">
                                  {{trans('message.'.$message)}}</p>
                                @enderror
                              </div>
                              <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                                <label class="pull-left">نوع معامله</label>
                                <select name="type" id="" class="form-control">
                                  <option disabled>انتخاب</option>
                                  <option value="رسید">رسید</option>
                                  <option value="گرفت">گرفت</option>
                                </select>
                                
                                @error('type') <p class="text-danger">
                                  {{trans('message.'.$message)}}</p>
                                @enderror
                              </div>
                              
                              <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 center marginy">
                                <label class="">توضیحات</label>
                                <textarea name="description" id="description" rows="1"
                                          class="form-control"
                                          placeholder="توضیحات "></textarea>
                                @error('description') <p class="text-danger">
                                  {{trans('message.'.$message)}}</p>
                                @enderror
                              </div>
                              <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                                <label class="pull-right">تاریخ</label>
                                <input type="date" name="date"
                                       placeholder="تاریخ را وارد کنید"
                                       class="form-control">
                                @error('date') <p class="text-danger">
                                  {{trans('message.'.$message)}}</p>
                                @enderror
                              </div>
                            </div>
                          </div>
                          <div class="form-group-inner">
                            <div class="row"
                                 style="display:flex;justify-content:flex-start;margin-top: 20px;">
                              <button class="btn btn-warning btn-sm" type="reset">انصراف
                              </button>
                              <button class="btn btn-primary marginx btn-sm" type="submit"><span
                                        class="fa fa-save"></span> ذخیره
                              </button>
                            </div>
                          </div>
                        </div>
                      </div>
                    </form>
                  @else
                    <form action="/dashboard/different-account-payments/{{$paymentEdit->id}}" method="post">
                      @csrf
                      @method('PUT')
                      <input type="hidden" name="account_id" value="{{$account->id}}">
                      <input type="hidden" name="old_amount" value="{{$paymentEdit->amount}}">
                      <input type="hidden" name="old_type" value="{{$paymentEdit->type}}">
                      
                      
                      <div class="row" style=" display:flex;justify-content:center">
                        <div class="col-sm-12">
                          <div class="form-group-inner">
                            <div class="row"
                                 style=" display:flex;justify-content:space-around">
                              <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                                <label class="pull-right">مقدار پول به دالر</label>
                                
                                <input type="text" name="amount" value="{{$paymentEdit->amount}}"
                                       class="form-control">
                                @error('amount') <p class="text-danger">
                                  {{trans('message.'.$message)}}</p>
                                @enderror
                              </div>
                              <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                                <label class="pull-right">نوع معامله</label>
                                <select name="type" id="" class="form-control">
                                  <option disabled>انتخاب</option>
                                  <option {{ $paymentEdit->type == 'رسید' ? 'selected' : '' }} value="رسید">رسید
                                  </option>
                                  <option {{ $paymentEdit->type == 'گرفت' ? 'selected' : '' }} value="گرفت">گرفت
                                  </option>
                                </select>
                                
                                @error('type') <p class="text-danger">
                                  {{trans('message.'.$message)}}</p>
                                @enderror
                              </div>
                              
                              <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 center marginy">
                                <label class="">توضیحات</label>
                                <textarea name="description" id="description" rows="1"
                                          class="form-control"
                                          placeholder="توضیحات ">{{$paymentEdit->description}}</textarea>
                                @error('description') <p class="text-danger">
                                  {{trans('message.'.$message)}}</p>
                                @enderror
                              </div>
                              <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                                <label class="pull-right">تاریخ</label>
                                <input type="date" name="date" value="{{$paymentEdit->date}}"
                                       placeholder="تاریخ را وارد کنید"
                                       class="form-control">
                                @error('date') <p class="text-danger">
                                  {{trans('message.'.$message)}}</p>
                                @enderror
                              </div>
                            </div>
                          </div>
                          <div class="form-group-inner">
                            <div class="row"
                                 style="display:flex;justify-content:flex-start;margin-top: 20px;">
                              <button class="btn btn-warning btn-sm" type="reset">انصراف
                              </button>
                              <button class="btn btn-primary marginx btn-sm" type="submit"><span
                                        class="fa fa-save"></span> ذخیره
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
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
          <div class="card-header">
            <div class="row">
              
              <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10"></div>
              <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 hideOnPrint">
                
                
                <div class="btn-group hideOnPrint" id="exportButton" style="float: left; ">
                  <div class="btn btn-sm btn-primary" style="float: left" onclick="printPage('PaidToDA')"><i
                            class="fa fa-print"></i> چاپ
                  </div>
                
                </div>
                <a href="/dashboard/different-account-payments-all/{{$account->id}}" style="float: left"
                   class="btn btn-sm btn-info hideOnPrint">نمایش همه</a>
              </div>
            </div>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-sm-12">
                <div class="sparkline8-graph text-muted">
                  
                  <div class="table-responsive">
                    <table class="table table-xs table-hover " id="account_payment">
                      <thead>
                      <tr>
                        
                        <td><b>رسید(دالر)</b></td>
                        <td><b>گرفت(دالر)</b></td>
                        
                        <td><b>تفصیلات</b></td>
                        <td><b>تاریخ</b></td>
                        <td><b>حالت</b></td>
                        
                        
                        <td class="hideOnPrint"><b> ویرایش</b></td>
                      
                      </tr>
                      </thead>
                      <tbody>
                      @foreach($payments as $pa)
                        <tr>
                          
                          @if($pa->type == 'رسید')
                            <td>{{$pa->amount}}</td>
                          @else
                            <td>0</td>
                          @endif
                          @if($pa->type == 'گرفت')
                            <td>{{$pa->amount}}</td>
                          @else
                            <td>0</td>
                          @endif
                          
                          
                          <td>{{$pa->description}}</td>
                          <td>{{$pa->date}}</td>
                          @if($pa->status == 0)
                            
                            <td class="hideOnPrint">
                              <label class="badge badge-warning">درخواست تایید
                                نشده</label></td>
                          @else
                            
                            <td class="hideOnPrint"><label for="" class="badge-success">درخواست تایید
                                شد</label></td>
                          
                          @endif
                          
                          @if( $pa->status == 0 || auth()->user()->role == 'SP')
                            <td class="hideOnPrint">
                              <a href="/dashboard/different-account-payments/{{$pa->id}}/edit"
                                 class="btn btn-sm btn-info">ویرایش</a>
                                 
                              @php
                                  $transaction = \App\LedgerTransaction::where('source_type', 'different_account')->where('source_id', $pa->id)->first();
                              @endphp
                              @if($transaction)
                                  <a href="{{ route('accounting.journals.show', $transaction->id) }}" target="_blank" class="btn btn-sm btn-success"><i class="fa fa-book"></i>&nbsp; روزنامچه مالی</a>
                              @endif
                            </td>
                          @endif
                        </tr>
                      @endforeach
                      <tr>
                        <td><b>{{$debits}} </b></td>
                        <td><b>گرفت ها(دالر)</b></td>
                      </tr>
                      <tr>
                        <td><b>{{$credits}} </b></td>
                        <td><b>رسیدات(دالر)</b></td>
                      </tr>
                      <tr>
                        <td style="direction: ltr"><b> {{$credits - $debits}} </b></td>
                        <td><b>صرف بیلانس(دالر)</b></td>
                      </tr>
                      
                      </tbody>
                    </table>
                  </div>
                  <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 hideOnPrint">
                      @if(!isset($all))
                        <p>{{$payments->links()}}</p>
                      @endif
                    </div>
                  </div>
                
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

@endsection
@section('scripts')
  
  <script>
      $(document).ready(function () {
          $("#account_payment").tableExport({
              headers: true,                      // (Boolean), display table headers (th or td elements) in the <thead>, (default: true)
              footers: true,                      // (Boolean), display table footers (th or td elements) in the <tfoot>, (default: false)
              formats: ["xlsx"],                  // (String[]), filetype(s) for the export, (default: ['xlsx', 'csv', 'txt'])
              filename: "id",                     // (id, String), filename for the downloaded file, (default: 'id')
              bootstrap: true,                   // (Boolean), style buttons using bootstrap, (default: true)
              exportButtons: true,                // (Boolean), automatically generate the built-in export buttons for each of the specified formats (default: true)
              position: "bottom",                 // (top, bottom), position of the caption element relative to table, (default: 'bottom')
              ignoreRows: null,                   // (Number, Number[]), row indices to exclude from the exported file(s) (default: null)
              ignoreCols: 5,                   // (Number, Number[]), column indices to exclude from the exported file(s) (default: null)
              trimWhitespace: true,               // (Boolean), remove all leading/trailing newlines, spaces, and tabs from cell text in the exported file(s) (default: false)
              RTL: true,                         // (Boolean), set direction of the worksheet to right-to-left (default: false)
              sheetname: "id",

          });
          var $buttons = $('#account_payment').find('caption').children().detach();
          // Append the buttons to an element of your choosing
          $buttons.appendTo('#exportButton');

      });
  </script>
@endsection