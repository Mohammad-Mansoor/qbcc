@extends('dsh.master')
@section('title' , 'اضافه کردن پول')
@section('content')
  
  <div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      @if(auth()->user()->role == 'SP')
        
        <div class="row">
          <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
            <div class="card">
              <div class="card-body">
                <div class="row align-items-center">
                  <div class="col-8">
                    <h4 class="text-c-yellow"><b>{{$co_cashbook}} &nbsp;$</b></h4>
                  </div>
                  <div class="col-4 text-right">
                    <i class="feather icon-bar-chart-2 f-28"></i>
                  </div>
                </div>
              </div>
              <div class="card-footer bg-c-yellow">
                <div class="row align-items-center">
                  <div class="col-9">
                    <h5 class="text-white m-b-0"> پول فعلی دخل دفتر مرکزی </h5>
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
                    <h4 class="text-c-green"> {{$so_cashbook}} &nbsp;$</h4>
                  </div>
                  <div class="col-4 text-right">
                    <i class="feather icon-file-text f-28"></i>
                  </div>
                </div>
              </div>
              <div class="card-footer bg-c-green">
                <div class="row align-items-center">
                  <div class="col-9">
                    <p class="text-white m-b-0"> پول فعلی دخل دفتر فروشات</p>
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
                    @if(auth()->user()->role == 'CO' || auth()->user()->role == 'CCO')
                      <h4 class="text-c-yellow"> {{$center_total}} &nbsp;$</h4>
                    @elseif(auth()->user()->role == 'SO' || auth()->user()->role == 'SCO')
                      <h4 class="text-c-yellow"> {{$froshat_total}} &nbsp;$</h4>
                    @elseif(auth()->user()->role == 'SP')
                      <h4 class="text-c-yellow"> {{$sp_total}} &nbsp;$</h4>
                    @endif
                  </div>
                  <div class="col-4 text-right">
                    <i class="feather icon-bar-chart-2 f-28"></i>
                  </div>
                </div>
              </div>
              <div class="card-footer bg-info">
                <div class="row align-items-center">
                  <div class="col-9">
                    <h5 class="text-white m-b-0">پول مجموع </h5>
                  </div>
                  <div class="col-3 text-right">
                    <i class="feather icon-trending-up text-white f-16"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <br>
      @endif
      <div class="row">
        @if(auth()->user()->role != 'SP')
          <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
            <div class="card">
              <div class="card-body">
                <div class="row align-items-center">
                  <div class="col-8">
                    <h4 class="text-c-yellow">    @if($cash > 0)
                        <b>{{$cash}} &nbsp;$</b>
                      @else
                        <b>0 $</b>
                      @endif</h4>
                  </div>
                  <div class="col-4 text-right">
                    <i class="feather icon-bar-chart-2 f-28"></i>
                  </div>
                </div>
              </div>
              <div class="card-footer bg-success">
                <div class="row align-items-center">
                  <div class="col-9">
                    <h5 class="text-white m-b-0"> پول فعلی دخل </h5>
                  </div>
                  <div class="col-3 text-right">
                    <i class="feather icon-trending-up text-white f-16"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
        @endif
        @if(auth()->user()->role == 'SP')
          <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
            <div class="card">
              <div class="card-body">
                <div class="row align-items-center">
                  <div class="col-8">
                    <h4 class="text-c-yellow">     {{$other_user}} &nbsp;$</h4>
                  </div>
                  <div class="col-4 text-right">
                    <i class="feather icon-bar-chart-2 f-28"></i>
                  </div>
                </div>
              </div>
              <div class="card-footer bg-c-yellow">
                <div class="row align-items-center">
                  <div class="col-9">
                    <h5 class="text-white m-b-0"> پول گرفته شده توسط دفاتر </h5>
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
                    @if(auth()->user()->role == 'CO' || auth()->user()->role == 'CCO')
                      <h4 class="text-c-yellow"> {{$center_debits}} &nbsp;$</h4>
                    @elseif(auth()->user()->role == 'SO' || auth()->user()->role == 'SCO')
                      <h4 class="text-c-yellow"> {{$froshat_debits}} &nbsp;$</h4>
                    @elseif(auth()->user()->role == 'SP')
                      <h4 class="text-c-yellow"> {{$sp_debits}} &nbsp;$</h4>
                    @endif
                  </div>
                  <div class="col-4 text-right">
                    <i class="feather icon-bar-chart-2 f-28"></i>
                  </div>
                </div>
              </div>
              <div class="card-footer bg-danger">
                <div class="row align-items-center">
                  <div class="col-9">
                    <h5 class="text-white m-b-0">مصارف</h5>
                  </div>
                  <div class="col-3 text-right">
                    <i class="feather icon-trending-up text-white f-16"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
        @endif
          <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
            <div class="card">
              <div class="card-body">
                <div class="row align-items-center">
                  <div class="col-8">
                    <h4 class="text-c-blue">
                      @if($cash > 0)
                        <b>{{$cash}} &nbsp;$</b>
                      @else
                        <b>0 $</b>
                      @endif</h4>
                  </div>
                  <div class="col-4 text-right">
                    <i class="feather icon-thumbs-down f-28"></i>
                  </div>
                </div>
              </div>
              <div class="card-footer bg-c-blue">
                <div class="row align-items-center">
                  <div class="col-9">
                    <p class="text-white m-b-0">پول فعلی دخل عمومی </p>
                  </div>
                  <div class="col-3 text-right">
                    <i class="feather icon-trending-down text-white f-16"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
      </div>
    </div>
  </div>
  
  <!-- form -->
  <div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 hideOnPrint">
      <div class="card">
        <div class="card-header">
          @if(!$creditEdit)
            <h5 class="text-right">اضافه کردن پول</h5>
          @else
            <h5 class="text-right">ویرایش پول </h5>
          @endif
        </div>
        <div class="card-body">
          @if(!$creditEdit)
            <form action="/dashboard/add-office-credit" method="post">
              @csrf
              <div class="row">
                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                  <div class="form-group fill">
                    <label> مقدار
                      پول</label>
                    <input type="number" style="direction: rtl"
                           name="amount"
                           id="creditAmount" class="form-control">
                    @error('amount') <p
                            class="text-danger">{{trans('message.'.$message)}}</p>
                    @enderror
                  </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                  <div class="form-group fill">
                    <label> توضیحات</label>
                    <textarea name="description" class="form-control" id="" cols="1"
                              rows="1"></textarea>
                    
                    @error('description') <p
                            class="text-danger">{{trans('message.'.$message)}}</p>
                    @enderror
                  </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                  <div class="form-group fill">
                    <label>تاریخ</label>
                    <input type="date" style="direction: rtl" name="date" id="date"
                           class="form-control">
                    @error('date') <p
                            class="text-danger">{{trans('message.'.$message)}}</p>
                    @enderror
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                  <a class="btn btn-sm btn-default" type="reset">انصراف</a>
                  <button class="btn btn-sm btn-primary submit-btn"
                          type="submit">ذخیره
                  </button>
                </div>
              </div>
            </form>
          @else
            <form action="/dashboard/add-office-credit/{{$creditEdit->id}}" method="post">
              @csrf
              @method('PATCH')
              <div class="row">
                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                  <div class="form-group fill">
                    <label> مقدار
                      پول</label>
                    <input type="number" style="direction: rtl"
                           name="amount" value="{{$creditEdit->amount}}"
                           id="creditAmount" class="form-control">
                    @error('amount') <p
                            class="text-danger">{{trans('message.'.$message)}}</p>
                    @enderror
                  </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                  <div class="form-group fill">
                    <label> توضیحات</label>
                    <textarea name="description" class="form-control" id="" cols="1"
                              rows="1">{{$creditEdit->description}}</textarea>
                    
                    @error('description') <p
                            class="text-danger">{{trans('message.'.$message)}}</p>
                    @enderror
                  </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                  <div class="form-group fill">
                    <label>تاریخ</label>
                    <input type="date" value="{{$creditEdit->date}}" style="direction: rtl" name="date" id="date"
                           class="form-control">
                    @error('date') <p
                            class="text-danger">{{trans('message.'.$message)}}</p>
                    @enderror
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                  <a class="btn btn-sm btn-default" type="reset">انصراف</a>
                  <button class="btn btn-sm btn-primary submit-btn"
                          type="submit">ذخیره
                  </button>
                </div>
              </div>
            </form>
          @endif
        </div>
      </div>
    </div>
  </div>
  <div class="row" id="creditPrint">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <div class="card">
        <div class="card-header">
          <h5>لیست عواید</h5>
         
        <div class="row">
          <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10"></div>
          <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 hideOnPrint">
            <div class="btn-group hideOnPrint" id="exportButton" style="float: left; ">
              <div class="btn btn-sm btn-primary" style="float: left" onclick="printPage('creditPrint')"><i
                        class="fa fa-print"></i> چاپ
              </div>
  
            </div>
          </div>
        </div>
          <div class="alert alert-success" style="display:none;" role="alert">
            <button type="button" class="close" data-dismiss="alert"
                    aria-label="Close"><span
                      aria-hidden="true">&times;</span></button>
            <p class="text-center"> حذف شد</p>
          </div>
          
          @if(session("status"))
            <div class="alert alert-primary status" style="display:none;"
                 role="alert">
              <button type="button" class="close" data-dismiss="alert"
                      aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
              <p class="text-center">{{session('status')}}</p>
            </div>
          
          @endif
          @if(session("error"))
            
            <div class="alert alert-danger status" style="display:none;"
                 role="alert">
              <button type="button" class="close" data-dismiss="alert"
                      aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
              <p class="text-center" style="color: white;">{{session('error')}}</p>
            </div>
          
          @endif
        </div>
        <div class="card-body">
          <table class="table table-hover table-xs" id="add_credit">
            <thead>
            <tr>
              <th>مقدار پول(دالر)</th>
              <th>توضیحات</th>
              <th>تاریخ</th>
              @if(auth()->user()->role != 'SP')
                <th class=" hideOnPrint">حالت</th>
              @endif
              <th class=" hideOnPrint">ویرایش</th>
              <!-- <th class="text-center">حذف</th> -->
            </tr>
            </thead>
            <tbody>
            
            @if(auth()->user()->role =='CO' || auth()->user()->role =='CCO')
              @forelse ($center_credits as $credit)
                <tr class="ur{{ $credit->id }}">
                  
                  <td>{{$credit->amount}} </td>
                  <td>{{$credit->description}}</td>
                  <td>{{$credit->date}}</td>
                  @if(auth()->user()->role != 'SP')
                    @if($credit->status == 0)
                      <td class="hideOnPrint">
                        <label class="badge badge-warning">درخواست تایید
                          نشده</label>
                      
                    @else
                      <td class="hideOnPrint"><label for="" class="badge-success">درخواست تایید
                          شد</label></td>
                    @endif
                  @endif
                  @if(!$credit->customer_id &&  $credit->status == 0)
                    <td class="hideOnPrint"><a href="/dashboard/add-office-credit/{{$credit->id}}/edit"
                                               class="btn btn-sm btn-info"><i class="fa fa-pencil"></i>&nbsp;
                        ویرایش</a></td>
                  @endif
                  @if($credit->payment_id)
                    <td class="hideOnPrint"><a href="/dashboard/add-office-credit/{{$credit->id}}/edit"
                                               class="btn btn-sm btn-info"><i class="fa fa-pencil"></i>&nbsp;
                        ویرایش</a></td>
                  @endif
                
                
                </tr>
              @empty
                
                <h4 class="text-info text-center">هنوز موردی ثبت نشده است</h4>
              @endforelse
            @elseif(auth()->user()->role =='SO' || auth()->user()->role =='SCO')
              @forelse ($froshat_credits as $credit)
                <tr class="ur{{ $credit->id }}">
                  
                  <td>{{$credit->amount}} </td>
                  <td>{{$credit->description}}</td>
                  <td>{{$credit->date}}</td>
                  @if(auth()->user()->role != 'SP')
                    @if($credit->status == 0)
                      <td class="hideOnPrint"><label for="" class="badge badge-warning">درخواست تایید
                          نشده</label></td>
                    @else
                      <td class="hideOnPrint"><label for="" class="badge badge-success">درخواست تایید
                          شد</label></td>
                    @endif
                  @endif
                  @if( $credit->status == 0)
                    <td class="hideOnPrint"><a href="/dashboard/add-office-credit/{{$credit->id}}/edit"
                                               class="btn btn-sm btn-info"><i class="fa fa-pencil"></i>&nbsp;
                        ویرایش</a></td>
                  @endif
                
                
                
                </tr>
              @empty
                
                <h4 class="text-info text-center">هنوز موردی ثبت نشده است</h4>
              @endforelse
            @elseif(auth()->user()->role =='SP')
              @forelse ($sp_credits as $credit)
                <tr class="ur{{ $credit->id }}">
                  
                  <td>{{$credit->amount}} </td>
                  <td>{{$credit->description}}</td>
                  <td>{{$credit->date}}</td>
                  @if(auth()->user()->role != 'SP')
                    @if($credit->status == 0)
                      <td class="hideOnPrint"><label for="" class="label label-warning">درخواست تایید
                          نشده</label></td>
                    @else
                      <td class="hideOnPrint"><label for="" class="label label-success">درخواست تایید
                          شد</label></td>
                    @endif
                  @endif
                  @if(!$credit->customer_id &&  $credit->status == 0)
                    <td class="hideOnPrint"><a href="/dashboard/add-office-credit/{{$credit->id}}/edit"
                                               class="btn btn-sm btn-info"><i class="fa fa-pencil"></i>&nbsp;
                        ویرایش</a></td>
                  @endif
                  @if($credit->payment_id)
                    <td class="hideOnPrint"><a href="/dashboard/add-office-credit/{{$credit->id}}/edit"
                                               class="btn btn-sm btn-info"><i class="fa fa-pencil"></i>&nbsp;
                        ویرایش</a></td>
                  @endif
                
                
                </tr>
              @empty
                
                <h4 class="text-info text-center">هنوز موردی ثبت نشده است</h4>
              @endforelse
            @endif
            
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

@endsection

@section('scripts')
  <script>

      $(document).ready(function () {
          $("#add_credit").tableExport({
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
          var $buttons = $('#add_credit').find('caption').children().detach();
          // Append the buttons to an element of your choosing
          $buttons.appendTo('#exportButton');

      });


      $('.status').show();
      window.setTimeout(function () {
          $(".status").fadeTo(500, 0).slideUp(500, function () {

              $(this).remove();
          });
      }, 2000);


      function RemoveCredit(id) {
          swal({
              style: "text-center",
              text: "  مقدار حذف شود؟",
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
                          url: '/dashboard/add-office-credit/' + id,
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
