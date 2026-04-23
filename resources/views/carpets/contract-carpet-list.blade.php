@extends('dsh.master')
@section('title' , 'لیست قالین های قرار دادی')
@section('content')
  <div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      
      <div class="card">
        <div class="card-header">
          <div class="alert alert-success" style="display:none;" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                      aria-hidden="true">&times;</span></button>
            جزئیات حذف شد
          </div>
          
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
          @if(!$editCarpet)
            <h4>ایجاد پارچه جدید</h4>
          @else
            <h4>ویرایش پارچه</h4>
          @endif
        </div>
        <div class="card-body">
          @if(!$editCarpet)
            <form action="/dashboard/contract-carpet" method="post" enctype="multipart/form-data" id='contractCarpetForm'>
              @csrf
              <input type="hidden" value="{{ $AccountNo }}" name="parcha_number" id="account_no"
                     class="form-control" value="{{old('carpet_no')}}">
              <input type="hidden" value="0" name="status" id="account_no" class="form-control"
                     value="{{old('status')}}">
              
              <input type="hidden" value="{{$currency}}" name="dollar_rate" id="currency_fo_af_convert">
              <div class="row">
                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                  <div class="form-group fill">
                    <label class="login2 pull-right pull-right-pro">نمبر
                      پارچه</label>
                    <input type="text" value="{{ $AccountNo }}" name="" id=""
                           class="form-control" disabled>
                    <small class="text-danger">@error('account_no')
                      {{ __('message.'.$message) }} @enderror
                    </small>
                  </div>
                </div>
                
                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12" style="margin-top: 10px">
                  <div class="form-group fill">
                    <label class="">اسم نماینده</label>
                    <select name="agent_id" id="agent_id" class="form-control">
                      <option value="">~~~</option>
                      @foreach($agents as $ag)
                        <option
                                {{ (Request::old('agent_id') == $ag->agent_id ? 'selected' : '') }}
                                value="{{$ag->agent_id}}">{{$ag->user->name}}
                          &nbsp; {{$ag->account_no}}</option>
                      @endforeach
                    </select>
                    <small class="text-danger">@error('agent_id')
                      {{ __('message.'.$message) }} @enderror
                    </small>
                  </div>
                </div>
                 <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                  <div class="form-group fill">
                    <label class="pull-right">اسم کاریگر</label>
                    <input type="text" name="employee_name"
                           placeholder="اسم کاریگر را وارد کنید" class="form-control" value="{{old('employee_name')}}">
                    @error('employee_name') <p class="text-danger">
                      {{trans('message.'.$message)}}</p> @enderror
                  </div>
                </div>
                
                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12" style="margin-top: 10px">
                  <div class="form-group fill">
                    <label class="">شماره فرمایش</label>
                    <select name="order_id" id="order_id" class="form-control">
                      <option value="">~~~</option>
                      @foreach($orders as $ord)
                        <option
                                {{ (Request::old('order_id') == $ord->id ? 'selected' : '') }}
                                value="{{$ord->id}}">{{$ord->order_number}}</option>
                      @endforeach
                    </select>
                    <small class="text-danger">@error('order_id')
                      {{ __('message.'.$message) }} @enderror
                    </small>
                  </div>
                </div>
                
                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12" style="margin-top: 10px">
                  <div class="form-group fill">
                    <label class="">نوعیت</label>
                    <select name="type_id" id="type_id" class="form-control">
                      <option value="">~~~</option>
                      @foreach($types as $type)
                        <option
                                {{ (Request::old('type_id') == $type->carpet_type_id ? 'selected' : '') }}
                                value="{{$type->carpet_type_id}}">{{$type->carpet_type}}
                        </option>
                      @endforeach
                    </select>
                    <small class="text-danger">@error('type_id')
                      {{ __('message.'.$message) }} @enderror
                    </small>
                  </div>
                </div>
                
                
                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                  <div class="form-group fill">
                    <label class=""> کوالتی</label>
                    <select name="quality_id" id="quality_id" class="form-control">
                      <option value=""></option>
                    </select>
                    <small class="text-danger">@error('quality_id') {{ __('message.'.$message) }}@enderror
                    </small>
                  </div>
                </div>
                
                <div class="col-lg-2 col-md- col-sm-12 col-xs-12">
                  <div class="form-group fill">
                    <label class="pull-right">نمبر نقشه</label>
                    <input type="text" name="map_number"
                           placeholder="نمبر نقشه  را وارد کنید" class="form-control"
                           value="{{old('map_number')}}">
                    @error('map_number') <p class="text-danger">
                      {{trans('message.'.$message)}}</p> @enderror
                  </div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                  <div class="form-group fill">
                    <label class="pull-right">حاشیه</label>
                    <input type="text" name="margin"
                           placeholder="حاشیه را وارد کنید" class="form-control" value="{{old('margin')}}">
                    @error('margin') <p class="text-danger">
                      {{trans('message.'.$message)}}</p> @enderror
                  </div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                  <div class="form-group fill">
                    <label class="pull-right">زمینه</label>
                    <input type="text" name="field" placeholder="زمینه را وارد کنید"
                           class="form-control" value="{{old('field')}}">
                    @error('field') <p class="text-danger">
                      {{trans('message.'.$message)}}</p> @enderror
                  </div>
                </div>
                
                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                  <div class="form-group fill">
                    <label for="ppm">قیمت فی متر به دالر</label>
                    <input type="text" name="price"
                           class="form-control" placeholder="قیمت فی متر به دالر" value="{{old('price')}}"
                           id="ppm">
                    @error('price') <p class="text-danger">
                      {{trans('message.'.$message)}}</p> @enderror
                  </div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                  <div class="form-group fill">
                    <label class="pull-right">طول</label>
                    <input type="text" name="height" placeholder="طول را وارد کنید"
                           class="form-control" value="{{old('height')}}" id="height">
                    @error('height') <p class="text-danger">
                      {{trans('message.'.$message)}}</p> @enderror
                  </div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                  <div class="form-group fill">
                    <label class="pull-right">عرض</label>
                    <input type="text" name="width" placeholder="عرض را وارد کنید"
                           class="form-control" value="{{old('width')}}" id="width">
                    @error('width') <p class="text-danger">
                      {{trans('message.'.$message)}}</p> @enderror
                  </div>
                </div>
                
                
                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                  <div class="form-group fill">
                    <label class="pull-right">مساحت</label>
                    <input type="text" name="area" placeholder="مساحت را وارد کنید"
                           class="form-control" value="{{old('area')}}" readonly id="area">
                    @error('area') <p class="text-danger">
                      {{trans('message.'.$message)}}</p> @enderror
                  </div>
                </div>
                
                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                  <div class="form-group fill">
                    <label class="pull-right">قیمت به افغانی</label>
                    <input type="text" name="total_price_af"
                           placeholder="قیمت مجموع به افغانی  " readonly
                           class="form-control" value="{{old('total_price_af')}}" id="total_price_af">
                    <input type="hidden" name="carpet_price" id="carpet_price">
                    @error('total_price_af') <p class="text-danger">
                      {{trans('message.'.$message)}}</p> @enderror
                  
                  </div>
                </div>
                
                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                  <div class="form-group fill">
                    <label class="pull-right">قیمت به دالر</label>
                    <input type="text" name="total_price"
                           placeholder="قیمت مجموع به دالر  " readonly
                           class="form-control" value="{{old('total_price')}}" id="total_price">
                    <input type="hidden" name="carpet_price_us" id="carpet_price_us">
                    @error('total_price') <p class="text-danger">
                      {{trans('message.'.$message)}}</p> @enderror
                  </div>
                </div>
                
                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                  <div class="form-group fill">
                    <label class="pull-right"> تاریخ شروع کار</label>
                    <input type="date" name="date" placeholder="تاریخ شروع کار را وارد کنید"
                           class="form-control" value="{{old('date')}}">
                    @error('date') <p class="text-danger">
                      {{trans('message.'.$message)}}</p> @enderror
                  </div>
                </div>
                
                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                  <div class="form-group fill">
                    <label class="pull-right">تاریخ ختم کار</label>
                    <input type="date" name="end_date" placeholder="تاریخ ختم کار را وارد کنید"
                           class="form-control" value="{{old('end_date')}}">
                    @error('end_date') <p class="text-danger">
                      {{trans('message.'.$message)}}</p> @enderror
                  </div>
                </div>
  
                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                  <div class="form-group fill">
                    <label class="login2 pull-right pull-right-pro">تصویر قالین</label>
                    <input type="file" name="carpet_image"
      
                           class="form-control">
                    @error('carpet_image') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                  </div>
                </div>
                
              </div>
              
              <div class="row">
                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                  <div class="form-group fill">
                    <button class="btn btn-info btn-sm" type="submit"><i
                              class="fa fa-save"></i> &nbsp; ثبت
                    </button>
                    <button class="btn btn-default btn-sm" type="reset"><a href="/dashboard/contract-carpet">منصرف</a>
                    </button>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                  </div>
                </div>
              </div>
            </form>
          
          @else
            <form action="/dashboard/contract-carpet/{{$editCarpet->carpet_id}}" method="post" enctype="multipart/form-data">
              @method('PATCH')
              @csrf
              <input type="hidden" value="0" name="status" id="account_no" class="form-control">
              
              <input type="hidden" value="{{$currency}}" name="dollar_rate" id="currency_fo_af_convert">
              <div class="row">
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                  <div class="form-group fill">
                    <label class="login2 pull-right pull-right-pro">نمبر
                      پارچه</label>
                    <input type="text" value="{{ $editCarpet->parcha_number }}" name="parcha_number" id=""
                           class="form-control" disabled>
                    <small class="text-danger">@error('account_no')
                      {{ __('message.'.$message) }} @enderror
                    </small>
                  </div>
                </div>
                
                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                  <div class="form-group fill">
                    <label class="">اسم نماینده</label>
                    <select name="agent_id" id="agent_id" class="form-control">
                      <option value="">~~~</option>
                      @foreach($agents as $ag)
                        <option
                                {{ ($editCarpet->agent_id == $ag->agent_id ? 'selected' : '') }}
                                value="{{$ag->agent_id}}">{{$ag->user->name}}
                          &nbsp; {{$ag->account_no}}</option>
                      @endforeach
                    </select>
                    <small class="text-danger">@error('agent_id')
                      {{ __('message.'.$message) }} @enderror
                    </small>
                  </div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                  <div class="form-group fill">
                    <label class="pull-right">اسم کاریگر</label>
                    <input type="text" name="employee_name"
                           placeholder="اسم کاریگر را وارد کنید" class="form-control" value="{{$editCarpet->employee_name}}">
                    @error('employee_name') <p class="text-danger">
                      {{trans('message.'.$message)}}</p> @enderror
                  </div>
                </div>
                
                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                  <div class="form-group fill">
                    <label class="">شماره فرمایش</label>
                    <select name="order_id" id="order_id" class="form-control">
                      <option value="">~~~</option>
                      @foreach($orders as $ord)
                        <option
                                {{ ($editCarpet->order_id == $ord->id ? 'selected' : '') }}
                                value="{{$ord->id}}">{{$ord->order_number}}</option>
                      @endforeach
                    </select>
                    <small class="text-danger">@error('order_id')
                      {{ __('message.'.$message) }} @enderror
                    </small>
                  </div>
                </div>
                
                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                  <div class="form-group fill">
                    <label class="">نوعیت</label>
                    <select name="type_id" id="type_id" class="form-control">
                      <option value="">~~~</option>
                      @foreach($types as $type)
                        <option
                                {{ ($editCarpet->type_id == $type->carpet_type_id ? 'selected' : '') }}
                                value="{{$type->carpet_type_id}}">{{$type->carpet_type}}
                        </option>
                      @endforeach
                    </select>
                    <small class="text-danger">@error('type_id')
                      {{ __('message.'.$message) }} @enderror
                    </small>
                  </div>
                </div>
                
                
                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                  <div class="form-group fill">
                    <label class=""> کوالتی</label>
                    <select name="quality_id" id="quality_id" class="form-control">
                      
                      @foreach($qualities as $q)
                        <option
                                {{ ($editCarpet->quality_id == $q->id ? 'selected' : '') }}
                                value="{{$q->id}}">{{$q->quality}}
                        </option>
                      @endforeach
                    
                    </select>
                    <small class="text-danger">@error('quality_id') {{ __('message.'.$message) }}@enderror
                    </small>
                  </div>
                </div>
                
                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                  <div class="form-group fill">
                    <label class="pull-right">نمبر نقشه</label>
                    <input type="text" name="map_number"
                           placeholder="نمبر نقشه  را وارد کنید" class="form-control"
                           value="{{$editCarpet->map_number}}">
                    @error('map_number') <p class="text-danger">
                      {{trans('message.'.$message)}}</p> @enderror
                  </div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                  <div class="form-group fill">
                    <label class="pull-right">حاشیه</label>
                    <input type="text" name="margin"
                           placeholder="حاشیه را وارد کنید" class="form-control" value="{{$editCarpet->margin}}">
                    @error('margin') <p class="text-danger">
                      {{trans('message.'.$message)}}</p> @enderror
                  </div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                  <div class="form-group fill">
                    <label class="pull-right">زمینه</label>
                    <input type="text" name="field" placeholder="زمینه را وارد کنید"
                           class="form-control" value="{{$editCarpet->field}}">
                    @error('field') <p class="text-danger">
                      {{trans('message.'.$message)}}</p> @enderror
                  </div>
                </div>
                
                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                  <div class="form-group fill">
                    <label for="ppm">قیمت فی متر به دالر</label>
                    <input type="text" name="price"
                           class="form-control" placeholder="قیمت فی متر به دالر" value="{{$editCarpet->price}}"
                           id="ppm">
                    @error('price') <p class="text-danger">
                      {{trans('message.'.$message)}}</p> @enderror
                  </div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                  <div class="form-group fill">
                    <label class="pull-right">طول</label>
                    <input type="text" name="height" placeholder="طول را وارد کنید"
                           class="form-control" value="{{$editCarpet->height}}" id="height">
                    @error('height') <p class="text-danger">
                      {{trans('message.'.$message)}}</p> @enderror
                  </div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                  <div class="form-group fill">
                    <label class="pull-right">عرض</label>
                    <input type="text" name="width" placeholder="عرض را وارد کنید"
                           class="form-control" value="{{$editCarpet->width}}" id="width">
                    @error('width') <p class="text-danger">
                      {{trans('message.'.$message)}}</p> @enderror
                  </div>
                </div>
                
                
                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                  <div class="form-group fill">
                    <label class="pull-right">مساحت</label>
                    <input type="text" name="area" placeholder="مساحت را وارد کنید"
                           class="form-control" value="{{$editCarpet->area}}" readonly id="area">
                    @error('area') <p class="text-danger">
                      {{trans('message.'.$message)}}</p> @enderror
                  </div>
                </div>
                
                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                  <div class="form-group fill">
                    <label class="pull-right">قیمت به افغانی</label>
                    <input type="text" name="total_price_af"
                           placeholder="قیمت مجموع به افغانی  " readonly
                           class="form-control" value="{{$editCarpet->total_price_af}}" id="total_price_af">
                    <input type="hidden" name="carpet_price" id="carpet_price">
                    @error('total_price_af') <p class="text-danger">
                      {{trans('message.'.$message)}}</p> @enderror
                  
                  </div>
                </div>
                
                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                  <div class="form-group fill">
                    <label class="pull-right">قیمت به دالر</label>
                    <input type="text" name="total_price"
                           placeholder="قیمت مجموع به دالر  " readonly
                           class="form-control" value="{{$editCarpet->total_price}}" id="total_price">
                    <input type="hidden" name="carpet_price_us" id="carpet_price_us">
                    @error('total_price') <p class="text-danger">
                      {{trans('message.'.$message)}}</p> @enderror
                  </div>
                </div>
                
                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                  <div class="form-group fill">
                    <label class="pull-right">تاریخ شروع کار</label>
                    <input type="date" name="date" placeholder="تاریخ شروع کار را وارد کنید"
                           class="form-control" value="{{$editCarpet->date}}">
                    @error('date') <p class="text-danger">
                      {{trans('message.'.$message)}}</p> @enderror
                  </div>
                </div>
                
                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                  <div class="form-group fill">
                    <label class="pull-right">تاریخ ختم کار</label>
                    <input type="date" name="end_date" placeholder="تاریخ ختم کار را وارد کنید"
                           class="form-control" value="{{$editCarpet->end_date}}">
                    @error('end_date') <p class="text-danger">
                      {{trans('message.'.$message)}}</p> @enderror
                  </div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                  <div class="form-group fill">
                    <label class="login2 pull-right pull-right-pro">تصویر قالین</label>
                    <input type="file" name="carpet_image"
      
                           class="form-control">
                    @error('carpet_image') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                  </div>
                </div>
              </div>
             
              <div class="row">
                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                  <div class="form-group fill">
                    <button class="btn btn-info btn-sm" type="submit"><i
                              class="fa fa-save"></i> &nbsp; ثبت
                    </button>
                    <button class="btn btn-default btn-sm" type="reset"><a href="/dashboard/contract-carpet">منصرف</a>
                    </button>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                  </div>
                </div>
              </div>
            </form>
          
          @endif
        </div>
      </div>
    </div>
  </div>
  
  <div class="row" id="contract-carpet">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <div class="card">
        
        <div class="card-header">
          <h5>پارچه های قراردادی</h5>
          <div class="row hideOnPrint">
            <div class="col-xs-3 col-lg-3 col-md-3 col-sm-3 hideOnPrint">
              <form action="/dashboard/contract-carpet/search" method="post">
                @csrf
                <input type="text" name="search" required
                       placeholder="جستجو" class="form-control">
              </form>
            </div>
            <div class="col-xs-4 col-lg-4 col-md-4 col-sm-4">
              
              
              <form action="/dashboard/search-contract-carpet-by-agent" method="POST" id="dateSearch">
                @csrf
                <div class="row">
                  
                  
                  <select name="agent_id" id="agent_id" class="form-control" onchange="this.form.submit()">
                    <option value="">جستجو به اساس نماینده</option>
                    @foreach($agents as $ag)
                      <option
                              {{ (Request::old('agent_id') == $ag->agent_id ? 'selected' : '') }}
                              value="{{$ag->agent_id}}">{{$ag->user->name}}
                        &nbsp; {{$ag->account_no}}</option>
                    @endforeach
                  </select>
                
                </div>
              </form>
            </div>
            
            <div class="col-xs-2 col-lg-2 col-md-2 col-sm-2 hideOnPrint">
              <a href="/dashboard/contract-carpet-show-all" style="float: left" class="btn btn-sm btn-info hideOnPrint">نمایش
                همه</a>
            </div>
            <div class="col-xs-3 col-lg-3 col-md-3 col-sm-3 hideOnPrint">
              <div class="btn-group hideOnPrint" id="exportButton" style="float: left; ">
                <div class="btn btn-sm btn-primary" style="float: left" onclick="printPage('contract-carpet')"><i
                          class="fa fa-print"></i> چاپ
                </div>
              
              </div>
            </div>
          
          </div>
        </div>
        <div class="card-body">
          
          <div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
               aria-hidden="true">
            <div class="modal-dialog modal-lg">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title h4" id="myLargeModalLabel">پاس کردن پارچه</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                  
                  <input type="hidden" name="carpet_id" id="carpet_id" class="form-control">
                  <div class="row">
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">
                      <div class="form-group fill">
                        <label class="login2 pull-right pull-right-pro">نمبر
                          قالین</label>
                        <input type="text" name="carpet_no" id="carpet_no"
                               class="form-control" readonly>
                      </div>
                    </div>
                    
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">
                      <div class="form-group fill">
                        <label class="">اسم نماینده</label>
                        <input type="text" name="agent_name" id="agent_name"
                               class="form-control" readonly>
                        <small class="text-danger">@error('agent_id')
                          {{ __('message.'.$message) }} @enderror
                        </small>
                      </div>
                    </div>
                    
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">
                      <div class="form-group fill">
                        <label class="">شماره فرمایش</label>
                        <input type="text" name="order_id" id="order_id"
                               class="form-control" readonly>
                        <small class="text-danger">@error('order_number')
                          {{ __('message.'.$message) }} @enderror
                        </small>
                      </div>
                    </div>
                    
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">
                      <div class="form-group fill">
                        <label class="pull-right">نمبر نقشه</label>
                        <input type="text" name="map_number" id="map_number"
                               placeholder="نمبر نقشه  را وارد کنید" class="form-control"
                        >
                        @error('map_number') <p class="text-danger">
                          {{trans('message.'.$message)}}</p> @enderror
                      </div>
                    </div>
                    
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">
                      <div class="form-group fill">
                        <label class="pull-right">طول</label>
                        <input type="text" name="height" placeholder="طول را وارد کنید"
                               class="form-control" id="carpet_height">
                        @error('height') <p class="text-danger">
                          {{trans('message.'.$message)}}</p> @enderror
                      </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">
                      <div class="form-group fill">
                        <label class="pull-right">عرض</label>
                        <input type="text" name="width" placeholder="عرض را وارد کنید"
                               class="form-control" id="carpet_width">
                        @error('width') <p class="text-danger">
                          {{trans('message.'.$message)}}</p> @enderror
                      </div>
                    </div>
                    
                    
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">
                      <div class="form-group fill">
                        <label class="pull-right">مساحت</label>
                        <input type="text" name="area" placeholder="مساحت را وارد کنید"
                               class="form-control" readonly id="carpet_area">
                        @error('area') <p class="text-danger">
                          {{trans('message.'.$message)}}</p> @enderror
                      </div>
                    </div>
                    
                    
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">
                      <div class="form-group fill">
                        <label class="pull-right"> تاریخ شروع کار</label>
                        <input type="date" name="date" placeholder="تاریخ شروع کار را وارد کنید" id="date"
                               class="form-control">
                        @error('date') <p class="text-danger">
                          {{trans('message.'.$message)}}</p> @enderror
                      </div>
                    </div>
                    
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">
                      <div class="form-group fill">
                        <label class="pull-right">تاریخ ختم کار</label>
                        <input type="date" name="end_date" placeholder="تاریخ ختم کار را وارد کنید" id="end_date"
                               class="form-control">
                        @error('end_date') <p class="text-danger">
                          {{trans('message.'.$message)}}</p> @enderror
                      </div>
                    </div>
                  </div>
                
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn  btn-secondary" data-dismiss="modal">بستن</button>
                  <button type="button" class="btn  btn-primary accept_or_cancel">پاس کردن</button>
                </div>
              </div>
            </div>
          </div>
          <div class="modal fade carpet_image" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
               aria-hidden="true">
            <div class="modal-dialog modal-lg">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title h4" id="myLargeModalLabel">
                    <input type="text" name="carpet_no" id="parcha_number" class="form-control" readonly>
                  </h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
  
                  <img src="" id="carpet_image" height="800px" width="700px" alt="">
                
        
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn  btn-secondary" data-dismiss="modal">بستن</button>
                </div>
              </div>
            </div>
          </div>
          
          
          <div class="table-responsive" style="margin-top: 60px">
            <table class="table  table-xs table-hover" id="contract_carpet">
              <thead>
              <tr>
                <th>شماره قالین</th>
                <th>اسم نماینده</th>
                <th>شماره فرمایش</th>
                <th>نوعیت</th>
                <th>کوالتی</th>
                <th>نمبر نقشه</th>
                <th>قیمت فی متر</th>
                <th>حاشیه</th>
                <th>زمینه</th>
                <th>طول</th>
                <th>عرض</th>
                <th>مساحت</th>
                <th>تاریخ شروع کار</th>
                <th>تاریخ ختم کار</th>
                <th>تصویر</th>
                <th class="hideOnPrint">ویرایش</th>
                <th class="hideOnPrint">پاس کردن</th>
                {{--<th class="hideOnPrint">ارسال به کچایی</th>--}}
                {{--<th class="hideOnPrint">ارسال به شست</th>--}}
                <th class="hideOnPrint">جزییات</th>
              
              </tr>
              </thead>
              <tbody>
              @foreach($carpets as $carpet)
                @if(isset($search))
                  @if($carpet->contract_type == 'contractional' && $carpet->status == 0)
                    <tr class="ur{{ $carpet->carpet_id }}">   <td>{{$carpet->margin}}</td>
                      <td>{{$carpet->field}}</td>
                      <td style="direction: ltr">{{$carpet->height}} m</td>
                      <td style="direction: ltr">{{$carpet->width}} m</td>
  
                      <td style="direction: ltr">{{$carpet->area}} m <sup>2</sup></td>
                      <td>{{$carpet->date}}</td>
                      <td>{{$carpet->parcha_number}}</td>
                      <td>{{$carpet->name}}</td>
                      <td>{{$carpet->order_number}}</td>
                      <td>{{$carpet->carpet_type}}</td>
                      <td>{{$carpet->quality}}</td>
                      <td>{{$carpet->map_number}}</td>
                      <td style="direction: ltr">{{$carpet->price}} $</td>
                      
                      
                      <td>{{$carpet->margin}}</td>
                      <td>{{$carpet->field}}</td>
                      <td style="direction: ltr">{{$carpet->height}} m</td>
                      <td style="direction: ltr">{{$carpet->width}} m</td>
                      
                      <td style="direction: ltr">{{$carpet->area}} m <sup>2</sup></td>
                      <td>{{$carpet->date}}</td>
                      <td>{{$carpet->end_date }}</td>
                      <td><a href="#"
                             onclick="   $('#parcha_number').val('  تصویر قالین  {{$carpet->parcha_number}} ');
                                     $('#carpet_image').attr('src', '/{{str_replace('\\','/',$carpet->carpet_image)}}');
                                     "
                             data-toggle="modal"
                             data-target=".carpet_image"><img src="/{{$carpet->carpet_image}}" style="height: 32px;" alt=""></a></td>
                      
                      <td class="hideOnPrint"><a href="/dashboard/contract-carpet/{{$carpet->carpet_id}}/edit"
                                                 class="btn btn-sm btn-info"><i class="fa fa-pencil"></i>&nbsp;
                          ویرایش</a>
                      </td>
                      
                      <td class="hideOnPrint">

                          <?php
                          $lastId = \Illuminate\Support\Facades\DB::table('carpets')->max('carpet_no');

                          $AccountNo = '';
                          if ($lastId) {
                              $lastId = substr($lastId, -5);
                              $lastId++;
                              $AccountNo = 'QB' . sprintf('%05d', $lastId);
                          } else {
                              $AccountNo = 'QB' . sprintf('%05d', '10101');
                          }


                          ?>
                        
                        <button type="button" class="btn btn-sm  btn-info" data-toggle="modal"
                                data-target=".bd-example-modal-lg"
                        
                                onclick="
                                        $('#carpet_no').val('{{$AccountNo}}');
                                        $('#agent_name').val('{{$carpet->name}}');
                                        $('#order_id').val('{{$carpet->order_number}}');
                                        $('#map_number').val('{{$carpet->map_number}}');
                                        $('#carpet_height').val('{{$carpet->height}}');
                                        $('#carpet_width').val('{{$carpet->width}}');
                                        $('#carpet_area').val('{{$carpet->area}}');
                                        $('#date').val('{{$carpet->date}}');
                                        $('#end_date').val('{{$carpet->end_date}}');
                                        $('#carpet_id').val('{{$carpet->carpet_id}}');
                                
                                
                                        "
                        >پاس کردن
                        </button>
                      
                      </td>
                      
                      
                      <td class="hideOnPrint"><a href="/dashboard/contract-carpet/{{$carpet->carpet_id}}"
                                                 class="btn btn-sm btn-info"><i
                                  class="fa fa-pencil"></i>&nbsp; جزییات</a></td>
                    
                    
                    </tr>
                  @endif
                @else
                  <tr class="ur{{ $carpet->carpet_id }}">
                    <td>{{$carpet->parcha_number}}</td>
                    <td>{{$carpet->name}}</td>
                    <td>{{$carpet->order_number}}</td>
                    <td>{{$carpet->carpet_type}}</td>
                    <td>{{$carpet->quality}}</td>
                    <td>{{$carpet->map_number}}</td>
                    <td style="direction: ltr">{{$carpet->price}} $</td>
                    
                    
                    <td>{{$carpet->margin}}</td>
                    <td>{{$carpet->field}}</td>
                    <td style="direction: ltr">{{$carpet->height}} m</td>
                    <td style="direction: ltr">{{$carpet->width}} m</td>
                    
                    <td style="direction: ltr">{{$carpet->area}} m <sup>2</sup></td>
                    <td>{{$carpet->date}}</td>
                    <td>{{$carpet->end_date}}</td>
                    <td><a href="#"
                           onclick="   $('#parcha_number').val('  تصویر قالین  {{$carpet->parcha_number}} ');
                                   $('#carpet_image').attr('src', '/{{str_replace('\\','/',$carpet->carpet_image)}}');
                                   "
                           data-toggle="modal"
                           data-target=".carpet_image"><img src="/{{$carpet->carpet_image}}" style="height: 32px;" alt=""></a></td>
                    
                    
                    <td class="hideOnPrint"><a href="/dashboard/contract-carpet/{{$carpet->carpet_id}}/edit"
                                               class="btn btn-sm btn-info"><i class="fa fa-pencil"></i>&nbsp; ویرایش</a>
                    </td>
                    
                    <td class="hideOnPrint">

                        <?php
                        $lastId = \Illuminate\Support\Facades\DB::table('carpets')->max('carpet_no');

                        $AccountNo = '';
                        if ($lastId) {
                            $lastId = substr($lastId, -5);
                            $lastId++;
                            $AccountNo = 'QB' . sprintf('%05d', $lastId);
                        } else {
                            $AccountNo = 'QB' . sprintf('%05d', '10101');
                        }


                        ?>
                      
                      <button type="button" class="btn btn-sm  btn-info" data-toggle="modal"
                              data-target=".bd-example-modal-lg"
                      
                              onclick="
                                      $('#carpet_no').val('{{$AccountNo}}');
                                      $('#agent_name').val('{{$carpet->name}}');
                                      $('#order_id').val('{{$carpet->order_number}}');
                                      $('#map_number').val('{{$carpet->map_number}}');
                                      $('#carpet_height').val('{{$carpet->height}}');
                                      $('#carpet_width').val('{{$carpet->width}}');
                                      $('#carpet_area').val('{{$carpet->area}}');
                                      $('#date').val('{{$carpet->date}}');
                                      $('#end_date').val('{{$carpet->end_date}}');
                                      $('#carpet_id').val('{{$carpet->carpet_id}}');
                              
                              
                                      "
                      >پاس کردن
                      </button>
                    
                    </td>
                    
                    
                    <td class="hideOnPrint"><a href="/dashboard/contract-carpet/{{$carpet->carpet_id}}"
                                               class="btn btn-sm btn-info"><i
                                class="fa fa-pencil"></i>&nbsp; جزییات</a></td>
                  
                  
                  </tr>
                @endif
              
              
              @endforeach
                <?php

              $total_carpet = DB::table('carpets')
                  ->join('agents', 'carpets.agent_id', 'agents.agent_id')
                  ->join('users', 'agents.user_id', 'users.id')
                  ->join('carpet_orders', 'carpets.order_id', 'carpet_orders.id')
                  ->join('carpet_types', 'carpets.type_id', 'carpet_types.carpet_type_id')
                  ->join('qualities', 'carpets.quality_id', 'qualities.id')
                  ->where('status', 0)
                  ->where('contract_type', 'contractional')
                  ->get();
              ?>

            
              @if(!isset($agent_id))
              <tr>
                <th>جمله تعداد قالین</th>
                <td style="direction: ltr"> {{$total_carpet->count()}} Pcs</td>
              </tr>
              <tr>
                <th>جمله متراژ قالین</th>
                <td style="direction: ltr"> {{$total_carpet->sum('area')}} m <sup>2</sup></td>
              </tr>
              @endif
              
              @if(isset($agent_id))
               <tr>
                  <th>جمله تعداد قالین</th>
                  <td style="direction: ltr"> {{$carpets->count()}} Pcs</td>
                </tr>
                <tr>
                  <th>جمله متراژ قالین</th>
                  <td style="direction: ltr"> {{$carpets->sum('area')}} m <sup>2</sup></td>
                </tr>
                
                <tr>
                  <th>جمله تار پخته</th>
                  <td style="direction: ltr"> {{$tar_pakhta}} kg</td>
                </tr>
                
                <tr>
                  <th>جمله تار پشم</th>
                  <td style="direction: ltr"> {{$tar_pashm}} kg</td>
                </tr>
                
                <tr>
                  <th>جمله تار ابریشم</th>
                  <td style="direction: ltr"> {{$tar_abrishm}} kg</td>
                </tr>
                
                <tr>
                  <th>جمله پول افغانی</th>
                  <td style="direction: ltr"> {{$afg_money}} AF</td>
                </tr>
                <tr>
                  <th>جمله پول دالر</th>
                  <td style="direction: ltr"> {{$usd_money}} $</td>
                </tr>
              @endif
              
              </tbody>
            </table>
            @if(!isset($all))
              <p class="hideOnPrint">{{$carpets->links()}}</p>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('scripts')

<script type="text/javascript">

   $(document).ready(function () {
      $("#contractCarpetForm").submit(function() {
   $(":submit", this).attr("disabled", "disabled");
});
    });



<!--</script>-->
  
  <script>

      $('#agent_id').select2();
      $('#employee_id').select2();
      $('#order_id').select2();
      $('#type_id').select2();
      $(document).ready(function () {


          // height
          $("#carpet_height").blur(function () {
              var height = $('#carpet_height').val();
              var mainHeight = parseFloat(height).toFixed(2);
              if (isNaN(mainHeight)) {
                  $("#carpet_height").val();
              } else {
                  $("#carpet_height").val(mainHeight);
              }


              // Area
              var mainWidth = $('#carpet_width').val();
              if (mainHeight != '' && mainWidth != '') {
                  var area = mainHeight * mainWidth;
                  var mainArea = parseFloat(area).toFixed(2);
                  $('#carpet_area').val(mainArea);
              }

          });
          //width
          $("#carpet_width").blur(function () {
              var width = $('#carpet_width').val();
              var mainWidth = parseFloat(width).toFixed(2);
              if (isNaN(mainWidth)) {
                  $("#carpet_width").val();
              } else {
                  $("#carpet_width").val(mainWidth);
              }

              // Area
              var mainHeight = $('#carpet_height').val();
              if (mainHeight != '' && mainWidth != '') {
                  var area = mainHeight * mainWidth;
                  var mainArea = parseFloat(area).toFixed(2);
                  $('#carpet_area').val(mainArea);
              }


          });


          // [ sweet-multiple ]
          $('.accept_or_cancel').on('click', function () {


              var carpet_no = $('#carpet_no').val();
              var map_number = $('#map_number').val();
              var carpet_height = $('#carpet_height').val();
              var carpet_width = $('#carpet_width').val();
              var carpet_area = $('#carpet_area').val();
              var start_date = $('#date').val();
              var end_date = $('#end_date').val();
              var carpet_id = $('#carpet_id').val();


              swal({
                  title: "آیا مطمعین هستید ؟",
                  text: "شما قادر به برگرداندن این پارچه نیستید!",
                  icon: "warning",
                  buttons: true,
                  dangerMode: true,
              })
                  .then((willDelete) => {
                      if (willDelete) {
                          swal("پارچه موفقانه پاس شد!", {
                              icon: "success",
                          });
                          $('.modal').modal('toggle');


                          $.ajax({
                              type: 'post',
                              data: {
                                  '_token': '{{csrf_token()}}',
                                  'carpet_id': carpet_id,
                                  'carpet_no': carpet_no,
                                  'map_number': map_number,
                                  'height': carpet_height,
                                  'width': carpet_width,
                                  'area': carpet_area,
                                  'date': start_date,
                                  'end_date': end_date,

                              },
                              url: '/dashboard/pass-parcha',
                              success: function (res) {

                                  if (res.status == 'success') {

                                      $('.alert-success').show();
                                      window.location = '/dashboard/contract-carpet'
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


                      } else {
                          swal("پارچه پاس نشد!", {
                              icon: "error",
                          });
                      }
                  });
          });


          $("#contract_carpet").tableExport({
              headers: true,                      // (Boolean), display table headers (th or td elements) in the <thead>, (default: true)
              footers: true,                      // (Boolean), display table footers (th or td elements) in the <tfoot>, (default: false)
              formats: ["xlsx", "txt"],              // (String[]), filetype(s) for the export, (default: ['xlsx', 'csv', 'txt'])
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
          var $buttons = $('#contract_carpet').find('caption').children().detach();
          // Append the buttons to an element of your choosing
          $buttons.appendTo('#exportButton');

      });
  
  
  </script>
  
  
  <script type="text/javascript">
      $("#type_id").change(function () {
          $.ajax({
              url: "{{ route('dashboard.qualities.get_by_type') }}?type_id=" + $(this).val(),
              method: 'GET',
              success: function (data) {
                  $('#quality_id').html(data.html);
              }
          });
      });
  </script>
  
  <script>
      $('#form2').hide();


      // remove carpet type function
      $('.status').show();
      window.setTimeout(function () {
          $(".status").fadeTo(500, 0).slideUp(500, function () {

              $(this).remove();
          });
      }, 2000);
  
  </script>
@endsection