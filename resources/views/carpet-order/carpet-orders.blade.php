@extends('dsh.master')
@section('content')
  <div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <div class="card">
        <div class="card-header">
          <h5>شماره فرمایش</h5>
        </div>
        <div class="card-body">
          <div class="all-form-element-inner">
            @if(!$orderEdit)
              @can('create_order')
              <form action="/dashboard/carpet-orders" method="post" id="user-form">
                @csrf
                <div class="row">
                  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                    <div class="form-group fill">
                      <label class="pull-right">نمبر فرمایش</label>
                      <input type="text" name="order_number" placeholder="نمبر فرمایش  را وارد کنید"
                             class="form-control">
                      <small class="text-danger">@error('order_number') {{ __('message.'.$message) }} @enderror</small>
                    </div>
                  </div>
                  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                    <div class="form-group fill">
                      <label class="pull-right">دیزاین نمبر</label>
                      <input type="text" name="design_number" placeholder="دیزاین نمبر  را وارد کنید"
                             class="form-control">
                      <small class="text-danger">@error('design_number') {{ __('message.'.$message) }} @enderror</small>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                    <div class="form-group fill">
                      <button class="btn btn-white" type="reset">لغو</button>
                      <button class="btn btn-sm btn-info" type="submit"><i class="fa fa-save"></i>&nbsp; ثبت</button>
                    </div>
                  </div>
                </div>
              </form>
              @endcan
            @else
              @can('edit_order')
              <form action="/dashboard/carpet-orders/{{$orderEdit->id}}" method="post" id="user-form">
                @csrf
                @method('PATCH')
                <div class="row">
                  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                    <div class="form-group fill">
                      <label class="pull-right">نمبر فرمایش</label>
                      <input type="text" name="order_number" value="{{$orderEdit->order_number}}" placeholder="نمبر فرمایش  را وارد کنید"
                             class="form-control">
                      <small class="text-danger">@error('order_number') {{ __('message.'.$message) }} @enderror</small>
                    </div>
                  </div>
                  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                    <div class="form-group fill">
                      <label class="pull-right">دیزاین نمبر</label>
                      <input type="text" name="design_number" value="{{$orderEdit->design_number}}" placeholder="دیزاین نمبر  را وارد کنید"
                             class="form-control">
                      <small class="text-danger">@error('design_number') {{ __('message.'.$message) }} @enderror</small>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                    <div class="form-group fill">
                      <button class="btn btn-white" type="reset">لغو</button>
                      <button class="btn btn-sm btn-info" type="submit"><i class="fa fa-save"></i>&nbsp; ثبت</button>
                    </div>
                  </div>
                </div>
              </form>
              @endcan
            @endif
          </div>
        </div>
      </div>
      <div class="card">
        <div class="card-header">
          <h5>
            شماره های فرمایش</h5>
          <div class="alert alert-success" style="display:none;" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                      aria-hidden="true">&times;</span></button>
            خرید حذف شد
          </div>
          
          @if(session("status"))
            <div class="alert alert-success status text-center" style="display:none;" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
              {{session('status')}}
            </div>
          
          @endif
          @if(session("error"))
            
            <div class="alert alert-success status text-center" style="display:none;" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
              {{session('error')}}
            </div>
          
          @endif
        </div>
        <div class="card-body">
          <div class="static-table-list table-responsive" style="margin-top: 60px">
            <table class="table table-hover table-xs">
              <thead>
              <tr>
                <th>آی دی</th>
                <th>نمبر فرمایش</th>
                <th> نمبر دیزاین</th>
                @can('edit_order')<th>ویرایش</th>@endcan
              
              
              </tr>
              </thead>
              <tbody>
              @foreach($orders as $order)
                <tr>
                  <td>{{ $order->id}}</td>
                  <td>{{ $order->order_number}} </td>
                  <td>{{$order->design_number}}</td>
                  
                  @can('edit_order')
                  <td><a href="/dashboard/carpet-orders/{{$order->id}}/edit"
                         class="btn btn-sm btn-info"><i class="fa fa-pencil"></i>&nbsp; ویرایش</a></td>
                  @endcan
                
                </tr>
              
              @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>


@endsection