@extends('dsh.master')
@section('title' , 'لیست قالین های خریده شده')
@section('content')
  <!-- navbar -->
  
  <div class="row" id="list-buy-carpet">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 hideOnPrint">
      <div class="card hideOnPrint">
        <div class="card-header">
          <h5>فورم پارچه خرید شده</h5>
        </div>
        <div class="card-body">
          @if(!$editCarpet)
            <form action="/dashboard/post-buy-carpet" method="post" enctype="multipart/form-data">
              @csrf
              <input type="hidden" value="{{ $AccountNo }}" name="carpet_no" id="account_no" class="form-control">
              <input type="hidden" value="1" name="status" class="form-control">
              <input type="hidden" value="{{$currency}}" name="dollar_rate" id="currency_fo_af_convert">
              <div class="row">
                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                  <div class="form-group fill">
                    <label class="login2 pull-right pull-right-pro">نمبر پارچه</label>
                    <input type="text" value="{{ $AccountNo }}" name="" id="" class="form-control" disabled>
                    <small class="text-danger">@error('account_no') {{ __('message.'.$message) }} @enderror</small>
                  </div>
                </div>
  
                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12" style="margin-top: 10px">
                  <div class="form-group fill">
                    <label class="">اسم فروشنده</label>
                    <select name="agent_id" id="agent_id" class="form-control">
                      <option value="">~~~</option>
                      @foreach($agents as $ag)
                        <option {{ (Request::old('agent_id') == $ag->agent_id ? 'selected' : '') }} value="{{$ag->agent_id}}">{{$ag->user->name}}
                          &nbsp; {{$ag->account_no}}</option>
                      @endforeach
                    </select>
                    <small class="text-danger">@error('agent_id') {{ __('message.'.$message) }} @enderror</small>
                  </div>
                </div>
  
                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12" style="margin-top: 10px">
                  <div class="form-group fill">
                    <label class="">شماره فرمایش</label>
                    <select name="order_id" id="order_id" class="form-control">
                      <option value="">~~~</option>
                      @foreach($orders as $ord)
                        <option {{ (Request::old('order_id') == $ord->id ? 'selected' : '') }} value="{{$ord->id}}">{{$ord->order_number}}</option>
                      @endforeach
                    </select>
                    <small class="text-danger">@error('order_id') {{ __('message.'.$message) }} @enderror</small>
                  </div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12" style="margin-top: 10px">
                  <div class="form-group fill">
                    <label class="">نوعیت</label>
                    <select name="type_id" id="type_id" class="form-control">
                      <option value="">~~~</option>
                      @foreach($types as $type)
                        <option {{ (Request::old('type_id') == $type->carpet_type_id ? 'selected' : '') }} value="{{$type->carpet_type_id}}">{{$type->carpet_type}}</option>
                      @endforeach
                    </select>
                    <small class="text-danger">@error('type_id') {{ __('message.'.$message) }} @enderror</small>
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
                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                  <div class="form-group fill">
                    <label class="pull-right">نمبر نقشه</label>
                    <input type="text" name="map_number" placeholder="نمبر نقشه  را وارد کنید"
                    
                           class="form-control">
                    @error('map_number') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                  </div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                  <div class="form-group fill">
                    <label class="pull-right">حاشیه</label>
                    <input type="text" name="margin" placeholder="حاشیه را وارد کنید"
                           class="form-control" id="margin">
                    @error('margin') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                  </div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                  <div class="form-group fill">
                    <label class="pull-right">زمینه</label>
                    <input type="text" name="field" placeholder="زمینه را وارد کنید"
                    
                           class="form-control">
                    @error('field') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                  </div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                  <div class="form-group fill">
                    <label class="pull-right">قیمت فی متر</label>
                    <input type="text" name="price" placeholder="قیمت فی متر به دالر  "
                    
                           class="form-control" id="ppm">
                    @error('price') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                  </div>
                </div>
                
                {{-- <input type="hidden" value="{{$currency}}" id="currency"> --}}
                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                  <div class="form-group fill">
                    <label class="pull-right">طول</label>
                    <input type="text" name="height" placeholder="طول را وارد کنید"
                    
                           class="form-control" id="height">
                    @error('height') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                  </div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                  <div class="form-group fill">
                    <label class="pull-right">عرض</label>
                    <input type="text" name="width" placeholder="عرض را وارد کنید"
                    
                           class="form-control" id="width">
                    @error('width') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                  </div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                  <div class="form-group fill">
                    <label class="pull-right">مساحت</label>
                    <input type="text" name="area" placeholder="مساحت را وارد کنید"
                    
                           class="form-control" readonly id="area">
                    @error('area') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                  </div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                  <div class="form-group fill">
                    <label class="pull-right">قیمت به افغانی</label>
                    <input type="text" name="total_price_af" placeholder="قیمت مجموع به افغانی  " readonly
                           class="form-control" id="total_price_af">
                    <input type="hidden" name="carpet_price" id="carpet_price">
                    @error('total_price_af') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                  </div>
                </div>
  
                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                  <div class="form-group fill">
                    <label class="pull-right">قیمت به دالر</label>
                    <input type="text" name="total_price" placeholder="قیمت مجموع به دالر  " readonly
                           class="form-control"
                           id="total_price">
                    <input type="hidden" name="carpet_price_us" id="carpet_price_us">
                    @error('total_price') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
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
  
              <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                <div class="form-group fill">
                  <button class="btn btn-info btn-sm" type="submit"><i class="fa fa-save"></i> &nbsp; ثبت</button>
                  <button class="btn btn-default btn-sm" type="reset"><a href="/dashboard/list-buy-carpet">منصرف</a>
                  </button>
                
                </div>
              </div>
            
            </form>
          @else
            <form action="/dashboard/upd-buy-carpet/{{$editCarpet->carpet_id}}" method="post" enctype="multipart/form-data">
              @csrf
              <input type="hidden" value="1" name="status" id="status" class="form-control">
              <input type="hidden" value="{{$currency}}" name="dollar_rate" id="currency_fo_af_convert">
              <div class="row">
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                  <div class="form-group fill">
                    <label class="login2 pull-right pull-right-pro">نمبر
                      پارچه</label>
                    <input type="text" value="{{ $editCarpet->carpet_no }}" name="carpet_no" id=""
                           class="form-control" disabled>
                    <small class="text-danger">@error('account_no')
                      {{ __('message.'.$message) }} @enderror
                    </small>
                  </div>
                </div>
  
                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                  <div class="form-group fill">
                    <label class="">اسم فروشنده</label>
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
                    <label class="pull-right">قیمت فی متر</label>
                    <input type="text" name="price" placeholder="قیمت فی متر به دالر  " value="{{$editCarpet->price}}"
                    
                           class="form-control" id="ppm">
                    @error('price') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                  </div>
                </div>
                
                {{-- <input type="hidden" value="{{$currency}}" id="currency"> --}}
                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                  <div class="form-group fill">
                    <label class="pull-right">طول</label>
                    <input type="text" name="height" placeholder="طول را وارد کنید"
                    
                           class="form-control" id="height" value="{{$editCarpet->height}}">
                    @error('height') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                  </div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                  <div class="form-group fill">
                    <label class="pull-right">عرض</label>
                    <input type="text" name="width" placeholder="عرض را وارد کنید"
                    
                           class="form-control" id="width" value="{{$editCarpet->width}}">
                    @error('width') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                  </div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                  <div class="form-group fill">
                    <label class="pull-right">مساحت</label>
                    <input type="text" name="area" placeholder="مساحت را وارد کنید"
                    
                           class="form-control" readonly id="area" value="{{$editCarpet->area}}">
                    @error('area') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                  </div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                  <div class="form-group fill">
                    <label class="pull-right">قیمت به افغانی</label>
                    <input type="text" name="total_price_af" placeholder="قیمت مجموع به افغانی  " readonly
                           class="form-control" id="total_price_af" value="{{$editCarpet->total_price_af}}">
                    <input type="hidden" name="carpet_price" id="carpet_price">
                    @error('total_price_af') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                  </div>
                </div>
  
                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                  <div class="form-group fill">
                    <label class="pull-right">قیمت به دالر</label>
                    <input type="text" name="total_price" placeholder="قیمت مجموع به دالر  " readonly
                           value="{{$editCarpet->total_price}}"
                           class="form-control"
                           id="total_price">
                    <input type="hidden" name="carpet_price_us" id="carpet_price_us">
                    @error('total_price') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
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
                    <button class="btn btn-default btn-sm" type="reset"><a href="/dashboard/list-buy-carpe">منصرف</a>
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
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <div class="card">
        <div class="card-header">
          <h4> قالین های خرید شده</h4>
          
          <div class="alert alert-success" style="display:none;" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                      aria-hidden="true">&times;</span></button>
            پارچه موفقانه ارسال شد
          </div>
          
          @if(session("error"))
            
            <div class="alert alert-danger status text-center" style="display:none;" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
              {{session('error')}}
            </div>
          
          @endif
          
          <div class="row ">
            <div class="col-xs-3 col-lg-3 col-md-3 col-sm-3 hideOnPrint">
              <form action="/dashboard/list-buy-carpet/search" method="post">
                @csrf
                <input type="text" name="search" required
                       placeholder="جستجو" class="form-control">
              </form>
            </div>
            <div class="col-xs-5 col-lg-5 col-md-5 col-sm-5"></div>
            <div class="col-xs-2 col-lg-2 col-md-2 col-sm-2 hideOnPrint">
              <a href="/dashboard/list-buy-carpet/show-all" style="float: left" class="btn btn-sm btn-info hideOnPrint">نمایش
                همه</a>
            </div>
            <div class="col-xs-2 col-lg-2 col-md-2 col-sm-2 hideOnPrint">
          
  
              <div class="btn-group hideOnPrint" id="exportButton" style="float: left; ">
                <div class="btn btn-sm btn-primary" style="float: left" onclick="printPage('list-buy-carpet')"><i
                          class="fa fa-print"></i> چاپ
                </div>
  
              </div>
            </div>
          
          </div>
        </div>
        <div class="card-body">
  
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
          
          <div class="table-responsive " style="margin-top: 60px">
            <table class="table table-hover table-xs" id="list_buy_carpet">
              <thead>
              <tr>
                <th>شماره قالین</th>
                <th>اسم فروشنده</th>
                <th>شماره فرمایش</th>
                <th>شماره پارچه</th>
                <th>نوعیت</th>
                <th>کوالتی</th>
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
                <th class="text-center hideOnPrint">عملیات</th>
                <th class="hideOnPrint">نمایش</th>
              </tr>
              </thead>
              <tbody>
              @foreach($carpets as $carpet)
                <tr class="ur{{ $carpet->carpet_id }}">
                  <td>{{$carpet->carpet_no}}</td>
                  @foreach($agents as $agent)
                    @if($agent->agent_id == $carpet->agent_id)
                      <td>{{$agent->user->name}}</td>
                    @endif
                  @endforeach
                  @if($carpet->carpet_order)
                    <td>{{$carpet->carpet_order->order_number}}</td>
                  @else
                    <td></td>
                  @endif
                  <td>{{$carpet->parcha_number}}</td>
                  @if($carpet->type)
                    <td>{{$carpet->type->carpet_type}}</td>
                  @else
                    <td></td>
                  @endif
                  @if($carpet->quality)
                    <td>{{$carpet->quality->quality}}</td>
                  @else
                    <td></td>
                  @endif
                  <td style="direction: ltr">{{$carpet->price}} $</td>
                  
                  <td>{{$carpet->margin}}</td>
                  <td>{{$carpet->field}}</td>
                  
                  <td style="direction: ltr">{{$carpet->height}} m</td>
                  <td style="direction: ltr">{{$carpet->width}} m</td>
                  <td style="direction: ltr">{{$carpet->area}} m <sup>2</sup></td>
                  <td>{{$carpet->date}}</td>
                  <td>{{$carpet->end_date}}</td>
                  <td><a href="#"
                         onclick="   $('#parcha_number').val('  تصویر قالین  {{$carpet->carpet_no}} ');
                                 $('#carpet_image').attr('src', '/{{str_replace('\\','/',$carpet->carpet_image)}}');
                                 "
                         data-toggle="modal"
                         data-target=".carpet_image"><img src="/{{$carpet->carpet_image}}" style="height: 32px;" alt=""></a></td>
                  @if(auth()->user()->role == 'SP' )
                  <td class="hideOnPrint"><a href="/dashboard/edit-buy-carpet/{{$carpet->carpet_id}}"
                                             class="btn btn-sm btn-info"><i class="fa fa-pencil"></i>&nbsp; ویرایش</a>
                  </td>
                  @endif
                  <td class="hideOnPrint">
                    
                    @if($carpet->status == 12)
                      کچایی شده
                    @else
                      <a href="/dashboard/carpet-repaire/sending-to-kachaee/{{$carpet->carpet_id}}"
                         class="btn btn-sm btn-info hideOnPrint">&nbsp;ارسال کچایی</a>
                    @endif
                    &nbsp;&nbsp;
                    <a href="/dashboard/washing-team/sending-to-washing/{{$carpet->carpet_id}}"
                       class="btn btn-sm btn-info hideOnPrint">&nbsp;ارسال شست</a>
                      &nbsp;&nbsp;
                    <a href="/dashboard/carpet-wash/sent-to-finish/{{$carpet->carpet_id}}"
                       class="btn btn-sm btn-info hideOnPrint">&nbsp;ارسال به تیاری</a>
                      &nbsp;&nbsp;
                    
                    <button onclick="sendToStock({{$carpet->carpet_id}})"
                            class="btn btn-sm btn-info printBTN"><i class="fa fa-send"></i>&nbsp; ارسال به گدام
                    </button>
                  
                  
                  </td>
                  <td class="hideOnPrint"><a href="/dashboard/print-buy-carpet/{{$carpet->carpet_id}}"
                                             class="btn btn-sm btn-info">&nbsp; نمایش</a></td>
                </tr>
              @endforeach
              @if(isset($all))
                <tr>
                  <td>تعداد</td>
                  <td>{{$carpets->count()}} pcs</td>
                </tr>
                <tr>
                  <td>مساحت</td>
                  <td>{{$carpets->sum('area')}} m <sup>2</sup></td>
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
  
  <script>

      $('#agent_id').select2();
   
      $('#order_id').select2();
      $('#type_id').select2();
    
      $(document).ready(function () {
          $("#list_buy_carpet").tableExport({
              headers: true,                      // (Boolean), display table headers (th or td elements) in the <thead>, (default: true)
              footers: true,                      // (Boolean), display table footers (th or td elements) in the <tfoot>, (default: false)
              formats: ["xlsx","txt"],              // (String[]), filetype(s) for the export, (default: ['xlsx', 'csv', 'txt'])
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
          var $buttons = $('#list_buy_carpet').find('caption').children().detach();
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
   

      function sendToStock(carpet_id) {

          swal({
              text: "مطمعین هستید ؟",
              buttons: true,
              dangerMode: true,
              buttons: {
                  confirm: {text: 'بلی', className: 'btn-success'},
                  cancel: 'نخیر'
              },
          })
              .then((willDelete) => {
                  if (willDelete) {
                      $.ajax({
                          type: 'GET',
                          data: {
                            {{--'_token': '{{csrf_token()}}',--}}
                          },
                          url: '/dashboard/carpet-stock/sent-to-stock/' + carpet_id,
                          success: function (res) {

                              if (res.status == 'success') {
                                  $('.alert-success').show();
                                  location.reload();
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

