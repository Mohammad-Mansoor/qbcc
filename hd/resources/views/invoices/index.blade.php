@extends('dsh.master')
@section('title' , 'انوایس ها')
@section('content')
  <!-- form -->
  <div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <div class="card">
        <div class="card-header">
          @if(!$invoiceEdit)
            <h5 class="text-right">ایجاد انوایس جدید</h5>
          @else
            <h5 class="text-right">ویرایش انوایس</h5>
          @endif
        </div>
        <div class="card-body">
          <div class="all-form-element-inner">
            @if(!$invoiceEdit)
              <form action="/dashboard/invoices" method="post">
                @csrf
                <div class="row">
                  <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                    <div class="form-group fill">
                      <label>نمبر انوایس</label>
                      <input type="text" value="{{ $invoice_no }}" name="invoice_no" id="" class="form-control"
                             readonly>
                      <small class="text-danger">@error('invoice_no') {{ __('message.'.$message) }} @enderror</small>
                    </div>
                  </div>
                  
                  <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                    <div class="form-group fill">
                      <label>تاریخ انوایس</label>
                      <input type="date" name="invoice_date" required id="" class="form-control">
                      <small class="text-danger">@error('invoice_date') {{ __('message.'.$message) }} @enderror</small>
                    </div>
                  </div>
                  <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                    <div class="form-group fill">
                      <label class="">مشتری را انتخاب کنید</label>
                      <select name="customer_id" required id="agent_id" class="form-control">
                        <option value="">~~~</option>
                        @foreach($customers as $cust)
                          <option {{ (Request::old('customer_id') == $cust->id ? 'selected' : '') }} value="{{$cust->id}}">{{$cust->name}}</option>
                        @endforeach
                      </select>
                      <small class="text-danger">@error('customer_id') {{ __('message.'.$message) }} @enderror</small>
                    </div>
                  </div>
                  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="form-group fill">
                      <label class="login2 pull-right pull-right-pro">توضیحات</label>
                      <textarea name="invoice_description" id=""  rows="1" class="form-control"></textarea>
      
                      <small class="text-danger">@error('invoice_description') {{ __('message.'.$message) }} @enderror</small>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                    <div class="form-group fill">
                      <button class="btn btn-info btn-sm" type="submit"><i class="fa fa-save"></i> &nbsp; ثبت</button>
                      <button class="btn btn-warning btn-sm" type="reset"> منصرف</button>
                    </div>
                  </div>
                </div>
              </form>
            @else
              <form action="/dashboard/invoices/{{$invoiceEdit->id}}" method="post">
                @csrf
                @method('PUT')
                
                <div class="row">
                  <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                    <div class="form-group fill">
                      <label class="login2 pull-right pull-right-pro">نمبر انوایس</label>
                      <input type="text" value="{{ $invoiceEdit->invoice_no }}" name="invoice_no" id=""
                             class="form-control" READONLY>
                      <small class="text-danger">@error('invoice_no') {{ __('message.'.$message) }} @enderror</small>
                    </div>
                  
                  </div>
                  
                  <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                    <div class="form-group fill">
                      <label class="login2 pull-right pull-right-pro">تاریخ انوایس</label>
                      <input type="date" name="invoice_date" value="{{$invoiceEdit->invoice_date}}" required id=""
                             class="form-control">
                      <small class="text-danger">@error('invoice_date') {{ __('message.'.$message) }} @enderror</small>
                    </div>
                  </div>
                
                  <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                    <div class="form-group fill">
                      <label class="">مشتری را انتخاب کنید</label>
                      <select name="customer_id" required id="agent_id" class="form-control">
                        <option value="">~~~</option>
                        @foreach($customers as $cust)
                          <option {{ ($invoiceEdit->customer_id == $cust->id ? 'selected' : '')  }} {{ (Request::old('customer_id') == $cust->id ? 'selected' : '') }} value="{{$cust->id}}">{{$cust->name}}</option>
                        @endforeach
                      </select>
                      <small class="text-danger">@error('customer_id') {{ __('message.'.$message) }} @enderror</small>
                    </div>
                  </div>
                  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="form-group fill">
                      <label class="login2 pull-right pull-right-pro">توضیحات</label>
                      <textarea name="invoice_description" id=""  rows="1" class="form-control">{{$invoiceEdit->invoice_description}}</textarea>
      
                      <small class="text-danger">@error('invoice_description') {{ __('message.'.$message) }} @enderror</small>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                    <div class="form-group fill">
                      <button class="btn btn-info btn-sm" type="submit"><i class="fa fa-save"></i> &nbsp; ثبت</button>
                      <button class="btn btn-default btn-sm" type="reset"> منصرف</button>
                    </div>
                  </div>
                </div>
              </form>
            @endif
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <div class="card">
        <div class="card-header">
          <h5>لیست انوایس ها</h5>
  
          <div class="alert alert-success" style="display:none;" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                      aria-hidden="true">&times;</span></button>
            <p class="text-center">انوایس حذف شد</p>
          </div>
          @if(session("status"))
            <div class="alert alert-success status" style="display:none;" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
              <p class="text-center">{{session('status')}}</p>
            </div>
          @endif
          @if(session("error"))
            <div class="alert alert-success status" style="display:none;" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
              <p class="text-center">{{session('error')}}</p>
            </div>
          @endif
         <div class="row">
           <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 hideOnPrint">
             <form action="/dashboard/search-invoice" method="POST">
               @csrf
             
               <input type="text" value="{{ Request::old('search') }}" name="search"
                      placeholder=" جستجو" class="form-control" required>
             </form>
           </div>
         </div>
        </div>
        <div class="card-body">
          <div class="static-table-list table-responsive">
            <table class="table table-hover table-xs">
              <thead>
              <tr >
                <th>انوایس نمبر</th>
                <th>تاریخ انوایس</th>
                <th>اسم مشتری</th>
                <th>توضیحات انوایس</th>
                <th>ویرایش</th>
                <th>چزییات</th>
              </tr>
              </thead>
              <tbody>
              @forelse ($invoices as $invoice)
                <tr class="ur{{ $invoice->id }}">
                  <td>{{$invoice->invoice_no}}</td>
                  <td>{{$invoice->invoice_date}}</td>
                  <td>{{$invoice->customer->name}}</td>
                  <td>{{$invoice->invoice_description}}</td>
                  <td><a href="/dashboard/invoices/{{$invoice->id}}/edit"
                         class="btn btn-sm btn-info"><i class="fa fa-pencil"></i>&nbsp; ویرایش</a>
                  </td>
                  <td><a href="/dashboard/invoices/{{$invoice->id}}"
                         class="btn btn-sm btn-info"><i class="fa fa-info"></i>&nbsp; جزییات</a>
                  </td>
                </tr>
              @empty
                <h4 class="text-info text-center">هنوز موردی ثبت نشده است</h4>
              @endforelse
              </tbody>
            </table>
            <span class="text-center">{{$invoices->onEachSide(1)->links()}}</span>
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
      }, 2000);

      function RemoveCategory(id) {
          swal({
              style: "text-center",
              text: "دسته بندی حذف شود؟",
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
                          url: '/dashboard/finish-team-categroy/' + id,
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
