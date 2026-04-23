@extends('dsh.master')
@section('title' , 'Carpets Wash')
@section('content')
  <!-- form -->
  <div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <div class="card">
        <div class="alert alert-success" style="display:none;" role="alert">
          <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
          مرحله ترمیم حذف شد
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
        <div class="card-header">
          <h4>ثبت شست قالین</h4>
          <div class="row">
            <div class="col-lg-11 col-md-11 col-sm-11 col-xs-11"></div>
          <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1" id="system_back_button">
          
            
          </div>
          
          </div>
        </div>
        <div class="card-body">
          <form action="/dashboard/carpet-wash" method="post">
            @csrf
            <input type="hidden" name="wash_id" value="{{$carpet_wash->id}}">
            <input type="hidden" name="carpetId" value="{{$carpet_wash->carpet->carpet_id}}">
            <div class="row">
              <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                <div class="form-group fill">
                  <label class="pull-right">نمبر قالین </label>
                  <input type="text" value="{{$carpet_wash->carpet->carpet_no}}" required class="form-control" readonly>
                </div>
              </div>
              <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                <div class="form-group fill">
                  <label class="pull-right">نام تیم</label>
                  <input type="text" name="team_id" required readonly="" placeholder="نمبر پارچه شست را وارد کنید"
                         class="form-control" value="{{$carpet_wash->washing_team->name}}">
                  @error('team_id') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                </div>
              </div>
              <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                <div class="form-group fill">
                  <label class="pull-right"> نمبر پارچه شست</label>
                  <input type="text" name="wash_number" required readonly="" placeholder="نمبر پارچه شست را وارد کنید"
                         class="form-control" value="{{$carpet_wash->wash_number}}">
                  @error('wash_number') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                </div>
              </div>
              
              
              <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                <div class="form-group fill">
                  <label class="pull-right">طول قبلی </label>
                  <input type="text" value="{{$carpet_wash->carpet->height}}" required class="form-control" readonly>
                </div>
              </div>
              <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                <div class="form-group fill">
                  <label class="pull-right">عرض قبلی </label>
                  <input type="text" value="{{$carpet_wash->carpet->width}}" required class="form-control" readonly>
                </div>
              </div>
              <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                <div class="form-group fill">
                  <label class="pull-right"> شست نمبر جدید</label>
                  <input type="text" name="wash_number_sh" required  placeholder="نمبر پارچه شست را وارد کنید"
                         class="form-control" value="{{$WashNo}}">
                  @error('wash_number_sh') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                </div>
              </div>
              <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                <div class="form-group fill">
                  <label class="pull-right">طول بعد از شست</label>
                  <input type="text" id="wheight" name="height" required class="form-control">
                  @error('height') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                </div>
              </div>
              <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                <div class="form-group fill">
                  <label class="pull-right">عرض بعد از شست</label>
                  <input type="text" id="wwidth" name="width" required class="form-control">
                  @error('width') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                </div>
              </div>
              
              <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                <div class="form-group fill">
                  <label class="pull-right">مساحت قالین</label>
                  <input type="text" id="warea" name="area" required readonly class="form-control">
                  @error('area') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                </div>
              </div>
              <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                <div class="form-group fill">
                  <label class="pull-right">مصرف شست فی متر مربع</label>
                  <input type="text" name="price" placeholder="مصرف شست فی متر" required class="form-control"
                         id="wprice" value="{{old('price')}}">
                  <input type="hidden" value="{{$currency}}" id="currency">
                  @error('price') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                </div>
              </div>
              
              <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                <div class="form-group fill">
                  <label class="pull-right">قیمت مجموعی به افغانی</label>
                  <input type="text" id="af_total_price" name="af_total_price" required readonly
                         class="form-control">
                  @error('af_total_price') <p class="text-danger">{{trans('message.'.$message)}}</p>
                  @enderror
                </div>
              </div>
              <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                <div class="form-group fill">
                  <label class="pull-right">قیمت مجموعی به دالر</label>
                  <input type="text" id="total_price" name="total_price" required readonly class="form-control">
                  @error('total_price') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                </div>
              </div>
              <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                <div class="form-group fill">
                  <label class="pull-right">تاریخ شست قالین</label>
                  <input type="date" name="date" placeholder="تاریخ را وارد کنید" required class="form-control"
                         value="{{old('date')}}">
                  @error('date') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                </div>
              </div>
              <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                <div class="form-group fill">
                  <label class="">توضیحات</label>
                  <textarea name="description" id="description" rows="1" required class="form-control"
                            placeholder="توضیحات شست قالین"></textarea>
                </div>
              </div>
            
            
            </div>
            <div class="row">
              <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                <div class="form-group fill">
                  <button class="btn btn-warning btn-sm"> انصراف </button>
                  <button class="btn btn-primary btn-sm marginx" type="submit"><span class="fa fa-save"></span> ذخیره
                  </button>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection
