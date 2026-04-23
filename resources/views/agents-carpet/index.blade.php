@extends('dsh.master')
@section('title' , 'لیست قالین های قرار دادی')
@section('content')
  <div class="row" >
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
            <form action="/dashboard/contract-carpet" method="post">
              @csrf
              <input type="hidden" value="{{ $AccountNo }}" name="carpet_no" id="account_no"
                     class="form-control" value="{{old('carpet_no')}}">
              <input type="hidden" value="0" name="status" id="account_no" class="form-control"
                     value="{{old('status')}}">
              <input type="hidden" value="{{ $agent->agent_id }}" name="agent_id">
              <input type="hidden" value="agent carpet" name="agent_carpet">
              <input type="hidden" value="{{$currency}}" id="currency">
              <div class="row">
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                  <div class="form-group fill">
                    <label class="login2 pull-right pull-right-pro">نمبر قالین</label>
                    <input type="text" value="{{ $AccountNo }}" name="" id=""
                           class="form-control" disabled>
                    <small class="text-danger">@error('account_no')
                      {{ __('message.'.$message) }} @enderror
                    </small>
                  </div>
                </div>
                
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                  <div class="form-group fill">
                    <label class="">اسم نماینده</label>
                    <input type="text" value="{{ $agent->user->name }}" name="" id=""
                           class="form-control" readonly>
                    <small class="text-danger">@error('agent_id')
                      {{ __('message.'.$message) }} @enderror
                    </small>
                  </div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                  <div class="form-group fill">
                    <label class="">اسم کارگر</label>
                    <select name="employee_id" id="employee_id"
                            class="form-control">
                      <option value="">~~~</option>
                      @foreach($employees as $emp)
                        <option value="{{$emp->id}}">{{$emp->first_name}}</option>
                      @endforeach
                    </select>
                    <small class="text-danger">@error('employee_id')
                      {{ __('message.'.$message) }} @enderror
                    </small>
                  </div>
                </div>
                
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                  <div class="form-group fill">
                    <label class="">شماره فرمایش</label>
                    <select name="order_id" id="agent_id" class="form-control">
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
                
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
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
                
                
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                  <div class="form-group fill">
                    <label class=""> کوالتی</label>
                    <select name="quality_id" id="quality_id" class="form-control">
                      <option value=""></option>
                    </select>
                    <small class="text-danger">@error('quality_id') {{ __('message.'.$message) }}@enderror
                    </small>
                  </div>
                </div>
                
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                  <div class="form-group fill">
                    <label class="pull-right">نمبر نقشه</label>
                    <input type="text" name="map_number"
                           placeholder="نمبر نقشه  را وارد کنید" class="form-control"
                           value="{{old('map_number')}}">
                    @error('map_number') <p class="text-danger">
                      {{trans('message.'.$message)}}</p> @enderror
                  </div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                  <div class="form-group fill">
                    <label class="pull-right">حاشیه</label>
                    <input type="text" name="margin"
                           placeholder="حاشیه را وارد کنید" class="form-control" value="{{old('margin')}}">
                    @error('margin') <p class="text-danger">
                      {{trans('message.'.$message)}}</p> @enderror
                  </div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                  <div class="form-group fill">
                    <label class="pull-right">زمینه</label>
                    <input type="text" name="field" placeholder="زمینه را وارد کنید"
                           class="form-control" value="{{old('field')}}">
                    @error('field') <p class="text-danger">
                      {{trans('message.'.$message)}}</p> @enderror
                  </div>
                </div>
                
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                  <div class="form-group fill">
                    <label for="ppm">قیمت فی متر به افغانی</label>
                    <input type="text" name="price"
                           class="form-control" placeholder="قیمت فی متر به افغانی" value="{{old('price')}}"
                           id="ppm">
                    @error('price') <p class="text-danger">
                      {{trans('message.'.$message)}}</p> @enderror
                  </div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                  <div class="form-group fill">
                    <label class="pull-right">طول</label>
                    <input type="text" name="height" placeholder="طول را وارد کنید"
                           class="form-control" value="{{old('height')}}" id="height">
                    @error('height') <p class="text-danger">
                      {{trans('message.'.$message)}}</p> @enderror
                  </div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                  <div class="form-group fill">
                    <label class="pull-right">عرض</label>
                    <input type="text" name="width" placeholder="عرض را وارد کنید"
                           class="form-control" value="{{old('width')}}" id="width">
                    @error('width') <p class="text-danger">
                      {{trans('message.'.$message)}}</p> @enderror
                  </div>
                </div>
                
                
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                  <div class="form-group fill">
                    <label class="pull-right">مساحت</label>
                    <input type="text" name="area" placeholder="مساحت را وارد کنید"
                           class="form-control" value="{{old('area')}}" readonly id="area">
                    @error('area') <p class="text-danger">
                      {{trans('message.'.$message)}}</p> @enderror
                  </div>
                </div>
                
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
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
                
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
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
                
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                  <div class="form-group fill">
                    <label class="pull-right">تاریخ</label>
                    <input type="date" name="date" placeholder="تاریخ را وارد کنید"
                           class="form-control" value="{{old('date')}}">
                    @error('date') <p class="text-danger">
                      {{trans('message.'.$message)}}</p> @enderror
                  </div>
                </div>
              </div>
              
              <div class="row">
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                  <div class="form-group fill">
                    <button class="btn btn-info btn-sm" type="submit"><i
                              class="fa fa-save"></i> &nbsp; ثبت
                    </button>
                    <button class="btn btn-default btn-sm" type="reset">منصرف
                    </button>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                  </div>
                </div>
              </div>
            </form>
          
          @else
            <form action="/dashboard/contract-carpet/{{$editCarpet->carpet_id}}" method="post">
              @method('PATCH')
              @csrf
              <input type="hidden" value="0" name="status" id="account_no" class="form-control">
              <input type="hidden" value="{{ $agent->agent_id }}" name="agent_id">
              <input type="hidden" value="agent carpet" name="agent_carpet">
              <input type="hidden" value="{{$currency}}" id="currency">
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
                
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                  <div class="form-group fill">
                    <label class="">اسم نماینده</label>
                    <input type="text" value="{{ $agent->user->name }}" name="" id=""
                           class="form-control" readonly>
                    <small class="text-danger">@error('agent_id')
                      {{ __('message.'.$message) }} @enderror
                    </small>
                  </div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                  <div class="form-group fill">
                    <label class="">اسم کارگر</label>
                    <select name="employee_id" id="employee_id"
                            class="form-control">
                      <option value="">~~~</option>
                      @foreach($employees as $emp)
                        <option {{ ($editCarpet->employee_id == $emp->id ? 'selected' : '') }} value="{{$emp->id}}">{{$emp->first_name}}</option>
                      @endforeach
                    </select>
                    <small class="text-danger">@error('employee_id')
                      {{ __('message.'.$message) }} @enderror
                    </small>
                  </div>
                </div>
                
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                  <div class="form-group fill">
                    <label class="">شماره فرمایش</label>
                    <select name="order_id" id="agent_id" class="form-control">
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
                
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
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
                
                
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                  <div class="form-group fill">
                    <label class=""> کوالتی</label>
                    <select name="quality_id" id="quality_id" class="form-control">
                      
                      @foreach($qualities as $q)
                        <option
                                {{ ($editCarpet->quality_id == $q->id ? 'selected' : '') }}
                                value="{{$q->id}}">{{$q->quality}}
                        </option>
                      @endforeach
                      <option value="{{$editCarpet->quality->quality}}">{{$editCarpet->quality->quality}}</option>
                    </select>
                    <small class="text-danger">@error('quality_id') {{ __('message.'.$message) }}@enderror
                    </small>
                  </div>
                </div>
                
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                  <div class="form-group fill">
                    <label class="pull-right">نمبر نقشه</label>
                    <input type="text" name="map_number"
                           placeholder="نمبر نقشه  را وارد کنید" class="form-control"
                           value="{{$editCarpet->map_number}}">
                    @error('map_number') <p class="text-danger">
                      {{trans('message.'.$message)}}</p> @enderror
                  </div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                  <div class="form-group fill">
                    <label class="pull-right">حاشیه</label>
                    <input type="text" name="margin"
                           placeholder="حاشیه را وارد کنید" class="form-control" value="{{$editCarpet->margin}}">
                    @error('margin') <p class="text-danger">
                      {{trans('message.'.$message)}}</p> @enderror
                  </div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                  <div class="form-group fill">
                    <label class="pull-right">زمینه</label>
                    <input type="text" name="field" placeholder="زمینه را وارد کنید"
                           class="form-control" value="{{$editCarpet->field}}">
                    @error('field') <p class="text-danger">
                      {{trans('message.'.$message)}}</p> @enderror
                  </div>
                </div>
                
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                  <div class="form-group fill">
                    <label for="ppm">قیمت فی متر به افغانی</label>
                    <input type="text" name="price"
                           class="form-control" placeholder="قیمت فی متر به افغانی" value="{{$editCarpet->price}}"
                           id="ppm">
                    @error('price') <p class="text-danger">
                      {{trans('message.'.$message)}}</p> @enderror
                  </div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                  <div class="form-group fill">
                    <label class="pull-right">طول</label>
                    <input type="text" name="height" placeholder="طول را وارد کنید"
                           class="form-control" value="{{$editCarpet->height}}" id="height">
                    @error('height') <p class="text-danger">
                      {{trans('message.'.$message)}}</p> @enderror
                  </div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                  <div class="form-group fill">
                    <label class="pull-right">عرض</label>
                    <input type="text" name="width" placeholder="عرض را وارد کنید"
                           class="form-control" value="{{$editCarpet->width}}" id="width">
                    @error('width') <p class="text-danger">
                      {{trans('message.'.$message)}}</p> @enderror
                  </div>
                </div>
                
                
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                  <div class="form-group fill">
                    <label class="pull-right">مساحت</label>
                    <input type="text" name="area" placeholder="مساحت را وارد کنید"
                           class="form-control" value="{{$editCarpet->area}}" readonly id="area">
                    @error('area') <p class="text-danger">
                      {{trans('message.'.$message)}}</p> @enderror
                  </div>
                </div>
                
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
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
                
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
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
                
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                  <div class="form-group fill">
                    <label class="pull-right">تاریخ</label>
                    <input type="date" name="date" placeholder="تاریخ را وارد کنید"
                           class="form-control" value="{{$editCarpet->date}}">
                    @error('date') <p class="text-danger">
                      {{trans('message.'.$message)}}</p> @enderror
                  </div>
                </div>
              </div>
              
              <div class="row">
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                  <div class="form-group fill">
                    <button class="btn btn-info btn-sm" type="submit"><i
                              class="fa fa-save"></i> &nbsp; ثبت
                    </button>
                    <button class="btn btn-default btn-sm" type="reset">منصرف
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
  
  <div class="row" id="agent-carpet">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <div class="card">
        
        <div class="card-header">
          <h5>قالین های نزد {{$agent->user->name}}</h5>
          <div class="row hideOnPrint">
            <div class="col-xs-3 col-lg-3 col-md-3 col-sm-3">
              <form action="/dashboard/agent-carpet/search" method="post">
                @csrf
                <input type="text" name="search" required
                       placeholder="جستجو" class="form-control">
                <input type="hidden" name="agent_id" value="{{$agent->agent_id}}">
              </form>
            </div>
            <div class="col-xs-6 col-lg-6 col-md-6 col-sm-6"></div>
            <div class="col-xs-2 col-lg-2 col-md-2 col-sm-2">
            
            </div>
            <div class="col-xs-1 col-lg-1 col-md-1 col-sm-1">
              <div class="btn btn-sm btn-primary hideOnPrint" style="float: left" onclick="printPage('agent-carpet')"><i
                        class="fa fa-print"></i> Print
              </div>
            </div>
          
          </div>
        </div>
        <div class="card-body">
          <div class="table-responsive" style="margin-top: 60px">
            <table class="table  table-xs table-hover">
              <thead>
              <tr>
                <th>شماره قالین</th>
                <th>اسم کاریگر</th>
                <th>شماره فرمایش</th>
                <th>نوعیت</th>
                <th>کوالتی</th>
                <th>قیمت فی متر</th>
                <th>حاشیه</th>
                <th>زمینه</th>
                <th>طول</th>
                <th>عرض</th>
                <th>مساحت</th>
                <th>تاریخ</th>
                <th class="hideOnPrint">ویرایش</th>
                <th class="hideOnPrint">ارسال به کچایی</th>
                <th class="hideOnPrint">ارسال به شست</th>
                <th class="hideOnPrint">جزییات</th>
              
              </tr>
              </thead>
              <tbody>
              @foreach($carpets as $carpet)
                <tr class="ur{{ $carpet->carpet_id }}">
                  <td>{{$carpet->carpet_no}}</td>
                  @if($carpet->employee_id)
                    <td>{{$carpet->carpetEmployee->first_name}}</td>
                  @else
                    <td></td>
                  @endif
                  @if($carpet->carpet_order)
                    <td>{{$carpet->carpet_order->order_number}}</td>
                  @else
                    <td></td>
                  @endif
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
                  <td style="direction: ltr">{{$carpet->price}} AF</td>
                  <td>{{$carpet->margin}}</td>
                  <td>{{$carpet->field}}</td>
                  <td style="direction: ltr">{{$carpet->width}} m</td>
                  <td style="direction: ltr">{{$carpet->height}} m</td>
                  <td style="direction: ltr">{{$carpet->area}} m <sup>2</sup></td>
                  <td>{{$carpet->date}}</td>
                  <td class="hideOnPrint"><a href="/dashboard/agent-carpet/{{$carpet->carpet_id}}/edit"
                         class="btn btn-sm btn-info hideOnPrint"><i class="fa fa-pencil"></i>&nbsp; ویرایش</a></td>
                  
                  <td class="hideOnPrint"> <a href="/dashboard/carpet-repaire/sending-to-kachaee/{{$carpet->carpet_id}}"
                          class="btn btn-sm btn-info hideOnPrint">&nbsp; ارسال به کچایی</a></td>
                  
                  <td class="hideOnPrint">  <a href="/dashboard/washing-team/sending-to-washing/{{$carpet->carpet_id}}"
                           class="btn btn-sm btn-info hideOnPrint">&nbsp; ارسال به شست</a></td>
                  
                  <td class="hideOnPrint"><a href="/dashboard/agent-carpets/{{$carpet->carpet_id}}" class="btn btn-sm btn-info "><i
                              class="fa fa-pencil"></i>&nbsp; جزییات</a></td>
                
                
                </tr>
              @endforeach
              </tbody>
            </table>
            @if(!isset($search))
              <p>{{$carpets->links()}}</p>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('scripts')
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