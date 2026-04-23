@extends('dsh.master')

@section('content')
  <div class="row">
    <div class="col-lg-12 col-md-12 col-sm-6 col-xs-12">
      
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
          <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
            <li class="nav-item">
              <a class="nav-link has-ripple {{$material == null ? 'active' : ''}}" id="pills-carpet-details-tab"
                 data-toggle="pill"
                 href="#carpet-details"
                 role="tab" aria-controls="pills-carpet-details" aria-selected="true">مشخصات کلی قالین<span
                        class="ripple ripple-animate"
                        style="height: 71.6719px; width: 71.6719px; animation-duration: 0.7s; animation-timing-function: linear; background: rgb(70, 128, 255); opacity: 0.4; top: -30.8359px; left: 9.16405px;"></span></a>
            </li>
            <li class="nav-item">
              <a class="nav-link has-ripple" id="pills-carpet-checkbook-tab" data-toggle="pill" href="#carpet-checkbook"
                 role="tab" aria-controls="pills-carpet-checkcbook" aria-selected="false">چک بٌک قالین<span
                        class="ripple ripple-animate"
                        style="height: 82.2188px; width: 82.2188px; animation-duration: 0.7s; animation-timing-function: linear; background: rgb(70, 128, 255); opacity: 0.4; top: -15.1094px; left: 18.9375px;"></span></a>
            </li>
            
            <li class="nav-item">
              <a class="nav-link has-ripple {{$material != null ? 'active' : ''}}" id="pills-material-recieves-tab"
                 data-toggle="pill"
                 href="#material-recieves"
                 role="tab" aria-controls="pills-material-recieves" aria-selected="false"> دریافت های مواد<span
                        class="ripple ripple-animate"
                        style="height: 74.2812px; width: 74.2812px; animation-duration: 0.7s; animation-timing-function: linear; background: rgb(70, 128, 255); opacity: 0.4; top: -3.1328px; left: 8.19532px;"></span></a>
            </li>
          
          </ul>
          <div class="tab-content" id="pills-tabContent">
            <div class="tab-pane fade {{$material == null ? 'active show' : ''}}" id="carpet-details" role="tabpanel"
                 aria-labelledby="pills-carpet-details-tab">
              <div class="btn btn-sm btn-primary hideOnPrint pull-right"
                   style="position: relative;top:20px;right:10px;float: left"
                   onclick="printPage('carpet-details')"><i class="fa fa-print"></i> Print
              </div>
              
              <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 sol-xs-12">
                  <h4 style="margin:20px auto">مشخصات عمومی قالین</h4>
                  <div class="static-table-list">
                    <table class="table table-hover table-xs">
                      <thead>
                      <tr>
                        <th style="text-align:right !important">مشخصات</th>
                        <th style="text-align:right !important">مقادیر</th>
                      </tr>
                      </thead>
                      <tbody>
                      <tr>
                        <td>شماره قالین</td>
                        <td>{{$carpet->parcha_number}}</td>
                      </tr>
                      <tr>
                        <td>شماره فرمایش</td>
                        @if($carpet->carpet_order)
                          <td>{{$carpet->carpet_order->order_number}}</td>
                        @else
                          <td></td>
                        @endif
                      </tr>
                      <tr>
                        <td>طول قالین</td>
                        <td style="direction: ltr;text-align:right">{{ $carpet->height }} m</td>
                      </tr>
                      <tr>
                        <td>عرض قالین</td>
                        <td style="direction: ltr;text-align:right">{{ $carpet->width }} m</td>
                      </tr>
                      <tr>
                        <td>مساحت قالین</td>
                        <td style="direction: ltr;text-align:right">{{ $carpet->area }} m
                          <sup>2</sup></td>
                      </tr>
                      <tr>
                        <td>شماره نقشه قالین</td>
                        <td>{{ $carpet->map_number }}</td>
                      </tr>
                      <tr>
                        <td>زمینه قالین</td>
                        <td>{{ $carpet->field }} </td>
                      </tr>
                      <tr>
                        <td>حاشیه قالین</td>
                        <td>{{ $carpet->margin }}</td>
                      </tr>
                      <tr>
                        <td> قیمت فی متر</td>
                        <td style="direction: ltr;text-align:right">{{ $carpet->price }} $</td>
                      </tr>
                     <tr>
                        <td> قیمت مجموعی افغانی</td>
                        <td style="direction: ltr;text-align:right">{{ $carpet->price * $carpet->area * $carpet->dollar_rate}} AF
                        </td>
                      </tr>
                      <tr>
                        <td> قیمت مجموعی دالر</td>
                        <td style="direction: ltr;text-align:right">{{ $carpet->price * $carpet->area }} $
                        </td>
                      </tr>
                      <tr>
                        <td>تاریخ</td>
                        <td>{{ $carpet->date }}</td>
                      </tr>
                      <tr>
                        <td> حالت</td>
                        @if($carpet->status == 5)
                          <td>تکمیل شده</td>
                        @else
                          <td>زیر کار است</td>
                        @endif
                      </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
            <div class="tab-pane fade" id="carpet-checkbook" role="tabpanel"
                 aria-labelledby="pills-carpet-checkbook-tab">
              <div class="btn btn-sm btn-primary hideOnPrint"
                   style="position: relative;top:20px;float: left;right: 10px;"
                   onclick="printPage('checkDetails')"><i class="fa fa-print"></i> Print
              </div>
              <div class="row" id="checkDetails">
                @if (!$carpetCheckBook)
                  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <h4> فورم چک بٌک</h4>
                    <div class="all-form-element-inner">
                      <form action="/dashboard/check-book" method="post">
                        @csrf
                        <input type="hidden" name="carpet_id" value="{{$carpet->carpet_id}}">
                        <input type="hidden" name="agent_id" value="{{$carpet->agent_id}}">
                        <div class="row">
                          
                          <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                            <div class="form-group fill">
                              <label class="pull-right">چک بوک نمبر</label>
                              <input type="text" name="check_number" value="{{$CheckNo}}"
                                     placeholder=" نمبر چک بوک قالین " class="form-control"
                                     id="check_number">
                              @error('check_number') <p class="text-danger">
                                {{trans('message.'.$message)}}</p>
                              @enderror
                            </div>
                          </div>
                          <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                            <div class="form-group fill">
                              
                              <label class="pull-right">پول برای کچایی به
                                افغانی</label>
                              <input type="text" name="kachaee_amount" value="{{old('kachaee_amount')}}"
                                     placeholder=" پول برای کچایی" class="form-control"
                                     id="kachaee_amount">
                            </div>
                          </div>
                          <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                            <div class="form-group fill">
                              <label class="pull-right">پول برای کچایی به دالر</label>
                              <input type="text" name="kachaee_dollar_amount"
                                     value="{{old('kachaee_dollar_amount')}}"
                                     placeholder=" پول برای کچایی" class="form-control"
                                     id="kachaee_dollar_amount" readonly>
                              <input type="hidden" value="{{$currency}}"
                                     id="currency">
                            </div>
                          </div>
                          
                          <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                            <div class="form-group fill">
                              <label class="pull-right" for="qheight">طول</label>
                              <input type="text"
                                     class="form-control" id="qheight" name="height" value="{{$carpet->height}}"
                                     readonly>
                              @error('height') <p class="text-danger">
                                {{trans('message.'.$message)}}</p>
                              @enderror
                            </div>
                          </div>
                          <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                            <div class="form-group fill">
                              <label class="pull-right" for="qwidth">عرض</label>
                              <input type="text"
                                     class="form-control" value="{{$carpet->width}}" name="width" id="qwidth" readonly>
                              @error('width') <p class="text-danger">
                                {{trans('message.'.$message)}}</p>
                              @enderror
                            </div>
                          </div>
                          
                          <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                            <div class="form-group fill">
                              <label class="pull-right" for="area">مساحت</label>
                              <input type="text" name="area"
                                     class="form-control" value="{{$carpet->area}}" id="area">
                              @error('area') <p class="text-danger">
                                {{trans('message.'.$message)}}</p>
                              @enderror
                            </div>
                          </div>
                          <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                            <div class="form-group fill">
                              <label class="pull-right">تاریخ</label>
                              <input type="date" name="date" value="{{$carpet->date}}"
                                     placeholder="تاریخ را وارد کنید" class="form-control">
                              @error('date') <p class="text-danger">
                                {{trans('message.'.$message)}}</p>
                              @enderror
                            </div>
                          </div>
                        
                        
                        </div>
                        <div class="row">
                          <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                            <div class="form-group fill">
                              <a href="/dashboard/contract-carpet/{{$carpet->carpet_id}}"
                                 class="btn btn-warning btn-sm" type="reset">انصراف</a>
                              <button class="btn btn-primary marginx btn-sm" type="submit">
                                <span class="fa fa-save"></span> ذخیره
                              </button>
                            </div>
                          </div>
                        </div>
                      
                      
                      </form>
                    
                    </div>
                  </div>
                @else
                  <div class="col-sm-12" id="editDetails">
                    <div class="sparkline8-graph text-muted">
                      <h4 style="margin:40px auto; text-align:center">مشخصات عمومی چک بٌک</h4>
                      <div class="static-table-list">
                        <table class="table table-hover table-xs">
                          <thead>
                          <tr>
                            <th style="text-align:right !important">مشخصات</th>
                            <th style="text-align:right !important">مقادیر</th>
                          </tr>
                          </thead>
                          <tbody>
                          <tr>
                            <td>شماره قالین</td>
                            <td>{{$carpetCheckBook->carpet->carpet_no}}</td>
                          </tr>
                          <tr>
                            <td>شماره چک بوک</td>
                            <td>{{$carpetCheckBook->check_number}}</td>
                          </tr>
                          <tr>
                            <td> طول قالین</td>
                            <td style="direction: ltr;text-align:right">
                              {{$carpetCheckBook->height}}
                              m
                            </td>
                          </tr>
                          <tr>
                            <td> عرض قالین</td>
                            <td style="direction: ltr;text-align:right">
                              {{$carpetCheckBook->width}}
                              m
                            </td>
                          </tr>
                          <tr>
                            <td> مساحت قالین</td>
                            <td style="direction: ltr;text-align:right">
                              {{$carpetCheckBook->area}}
                              m <sup>2</sup></td>
                          </tr>
                          
                          @if($carpetCheckBook->kachaee_amount)
                            <tr>
                              <td> پول اخذ شده برای کجایی</td>
                              <td style="direction: ltr;text-align:right">
                                {{$carpetCheckBook->kachaee_amount}}
                                AF
                              </td>
                            </tr>
                          @endif
                          
                          <tr>
                            <td>تاریخ</td>
                            <td>{{$carpetCheckBook->date}}</td>
                          </tr>
                             @if(auth()->user()->role == 'SP')
                          <tr class="hideOnPrint">
                            <td>ویرایش</td>
                            <td>
                              <button class="btn btn-info btn-sm" id="editBtn"><i
                                        class="fa fa-pencil"></i> ویرایش
                              </button>
                            </td>
                          </tr>
                          @endif
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                  <div class="col-sm-12" id="editForm">
                    <h4 style="margin:40px 80px"> فورم ویرایش چک بٌک</h4>
                    <div class="all-form-element-inner">
                      <form action="/dashboard/check-book/{{$carpetCheckBook->id}}" method="post">
                        @csrf
                        @method("PUT")
                        <input type="hidden" name="carpet_id" value="{{$carpet->carpet_id}}">
                        <div class="row">
                          
                          <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                            <div class="form-group fill">
                              <label class="pull-right">چک بوک نمبر</label>
                              <input type="text" name="check_number"
                                     value="{{$carpetCheckBook->check_number}}"
                                     class="form-control" id="check_number">
                              @error('check_number') <p class="text-danger">
                                {{trans('message.'.$message)}}</p>
                              @enderror
                            </div>
                          </div>
                          
                          <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                            <div class="form-group fill">
                              <label class="pull-right">پول برای کچایی به
                                افغانی</label>
                              <input type="text" name="kachaee_amount"
                                     value="{{$carpetCheckBook->kachaee_amount}}"
                                     class="form-control" id="kachaee_amount">
                              <input type="hidden" name="old_kachaee_amount"
                                     value="{{$carpetCheckBook->kachaee_amount}}">
                            </div>
                          </div>
                          
                          <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                            <div class="form-group fill">
                              <label class="pull-right">پول برای کچایی به دالر</label>
                              <input type="text" name="kachaee_dollar_amount"
                                     value="{{$carpetCheckBook->kachaee_dollar_amount}}"
                                     class="form-control" id="kachaee_dollar_amount"
                                     readonly>
                              <input type="hidden" name="old_kachaee_dollar_amount"
                                     value="{{$carpetCheckBook->kachaee_dollar_amount}}">
                              <input type="hidden" value="{{$currency}}"
                                     id="currency">
                            </div>
                          </div>
                          
                          <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                            <div class="form-group fill">
                              <label class="pull-right">طول</label>
                              <input type="text" name="height"
                                     value="{{$carpetCheckBook->height}}"
                                     class="form-control" id="height" readonly>
                              @error('height') <p class="text-danger">
                                {{trans('message.'.$message)}}</p>
                              @enderror
                            </div>
                          </div>
                          
                          <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                            <div class="form-group fill">
                              <label class="pull-right">عرض</label>
                              <input type="text" name="width"
                                     value="{{$carpetCheckBook->width}}" class="form-control"
                                     id="width" readonly>
                              @error('width') <p class="text-danger">
                                {{trans('message.'.$message)}}</p>
                              @enderror
                            </div>
                          </div>
                          
                          <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                            <div class="form-group fill">
                              <label class="pull-right">مساحت</label>
                              <input type="text" name="area"
                                     value="{{$carpetCheckBook->area}}" class="form-control"
                                     id="area" readonly>
                              @error('area') <p class="text-danger">
                                {{trans('message.'.$message)}}</p>
                              @enderror
                            </div>
                          </div>
                          <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                            <div class="form-group fill">
                              <label class="pull-right">تاریخ</label>
                              <input type="date" name="date"
                                     value="{{$carpet->date}}" class="form-control">
                              @error('date') <p class="text-danger">
                                {{trans('message.'.$message)}}</p>
                              @enderror
                            </div>
                          </div>
                        
                        
                        </div>
                        <div class="row">
                          <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                            <div class="form-group fill">
                              <a href="/dashboard/contract-carpet/{{$carpet->carpet_id}}"
                                 class="btn btn-warning btn-sm" type="reset">انصراف</a>
                              <button class="btn btn-primary btn-sm marginx" type="submit">
                                <span class="fa fa-save"></span> بروز کردن
                              </button>
                            </div>
                          </div>
                        </div>
                      </form>
                    </div>
                  </div>
                @endif
              </div>
            </div>
            <div class="tab-pane fade {{$material != null ? 'active show' : ''}}" id="material-recieves" role="tabpanel"
                 aria-labelledby="pills-material-recieves-tab">
              
              <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                  
                  @if(!$material)
                    <h4 style="margin:40px 80px"> فورم دریافت های مواد</h4>
                  @else
                    <h4 style="margin:40px 80px"> فورم ویرایش دریافت های مواد</h4>
                  @endif
                  <div class="all-form-element-inner">
                    @if(!$material)
                      <form action="/dashboard/carpet-material" method="post">
                        @csrf
                        <input type="hidden" name="carpet_id" value="{{$carpet->carpet_id}}">
                        <input type="hidden" name="agent_id" value="{{$carpet->agent->agent_id}}">
                        <input type="hidden" value="{{$currency}}" id="currency">
                        <div class="row" style=" display:flex;justify-content:center">
                          
                          <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                            <div class="form-group fill">
                              <label class="pull-right">مقدار مواد</label>
                              <input type="text" name="amount" value="{{old('amount')}}"
                                     placeholder="مقدار را به کیلو گرام وارد کنید"
                                     class="form-control" id="material-amount">
                              @error('amount') <p class="text-danger">
                                {{trans('message.'.$message)}}</p>
                              @enderror
                            </div>
                          </div>
                          <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                            <div class="form-group fill">
                              <label class="pull-right"> قیمت فی کیلو</label>
                              <input type="text" name="price" value="{{old('price')}}"
                                     placeholder="قیمت مواد مذکور" class="form-control"
                                     id="material-price">
                              @error('price') <p class="text-danger">
                                {{trans('message.'.$message)}}</p>
                              @enderror
                            </div>
                          </div>
                          
                          <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                            <div class="form-group fill">
                              <label class="pull-right"> قیمت مجموع به افغانی</label>
                              <input type="text" name="total_price_af" readonly value="{{old('total_price_af')}}"
                                     placeholder="قیمت مجموع به افغانی " class="form-control"
                                     id="material-af-total-price">
                              @error('total_price') <p class="text-danger">
                                {{trans('message.'.$message)}}</p>
                              @enderror
                            </div>
                          </div>
                          <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                            <div class="form-group fill">
                              <label class="pull-right"> قیمت مجموع به دالر</label>
                              <input type="text" name="total_price" readonly value="{{old('total_price')}}"
                                     placeholder="قیمت مجموع به دالر " class="form-control"
                                     id="material-total-price">
                              @error('total_price') <p class="text-danger">
                                {{trans('message.'.$message)}}</p>
                              @enderror
                            </div>
                          </div>
                          <div class="col-lg-1 col-md-1 col-sm-1 col-xs-6">
                            <div class="form-group fill">
                              <label class="pull-right">دسته بندی مواد</label>
                              <select name="category_id" class="form-control"
                                      style="direction: rtl">
                                @foreach ($categories as $category)
                                  <option value="{{$category->material_category_id}}">
                                    {{$category->material_category}}</option>
                                @endforeach
                              </select>
                              @error('category_id') <p class="text-danger">
                                {{trans('message.'.$message)}}</p>
                              @enderror
                            </div>
                          </div>
                          <div class="col-lg-1 col-md-1 col-sm-1 col-xs-6">
                            <div class="form-group fill">
                              <label class="pull-right" for="type_id">نوعیت مواد</label>
                              <select name="type_id" id="type_id" class="form-control"
                                      style="direction: rtl">
                                @foreach ($material_types as $type)
                                  <option value="{{$type->material_type_id}}">
                                    {{$type->material_type}}</option>
                                @endforeach
                              </select>
                              @error('type_id') <p class="text-danger">
                                {{trans('message.'.$message)}}</p>
                              @enderror
                            </div>
                          </div>
                          
                          <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                            <div class="form-group fill">
                              <label class="pull-right">تاریخ</label>
                              <input type="date" name="date" value="{{old('date')}}"
                                     placeholder="تاریخ را وارد کنید" class="form-control">
                              @error('date') <p class="text-danger">
                                {{trans('message.'.$message)}}</p>
                              @enderror
                            </div>
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                            <div class="form-group fill">
                              <button class="btn btn-default">
                                <a href="/dashboard/contract-carpet/{{$carpet->carpet_id}}" type="reset"
                                   class="btn btn-warning btn-sm">انصراف</a>
                              </button>
                              <button class="btn btn-primary btn-sm marginx" type="submit"><span
                                        class="fa fa-save"></span> ذخیره
                              </button>
                            </div>
                          </div>
                        </div>
                      </form>
                    @else
                      
                      <form action="/dashboard/carpet-material/{{$material->id}}" method="post">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="carpet_id" value="{{$material->carpet_id}}">
                        <input type="hidden" name="agent_id" value="{{$material->agent_id}}">
                        <div class="row" style=" display:flex;justify-content:center">
                          <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                            <div class="form-group fill">
                              <label class="pull-right">مقدار مواد به کیلو گرام</label>
                              <input type="hidden" name="oldMawad" value="{{$material->amount}}">
                              <input type="text" name="amount" value="{{$material->amount}}" class="form-control"
                                     id="material-amount">
                              @error('amount') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                            </div>
                          </div>
                          <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                            <div class="form-group fill">
                              <div class="col-xs-6">
                                <label class="pull-right"> قیمت فی کیلو</label>
                                <input type="text" name="price" value="{{$material->price}}" class="form-control"
                                       id="material-price">
                                @error('price') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                              </div>
                            </div>
                          </div>
                          <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                            <div class="form-group fill">
                              <label class="pull-right"> قیمت مجموع به افغانی</label>
                              <input type="text" name="total_price_af" value="{{$material->total_price_af}}"
                                     readonly class="form-control" id="material-af-total-price">
                              <input type="hidden" name="old_af" value="{{$material->total_price_af}}">
                              @error('total_price_af') <p class="text-danger">{{trans('message.'.$message)}}</p>
                              @enderror
                            </div>
                          </div>
                          <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                            <div class="form-group fill">
                              <label class="pull-right"> قیمت مجموع به دالر</label>
                              <input type="text" name="total_price" value="{{$material->total_price}}" readonly
                                     class="form-control" id="material-total-price">
                              <input type="hidden" name="old_dollar" value="{{$material->total_price}}">
                              @error('total_price') <p class="text-danger">{{trans('message.'.$message)}}</p>
                              @enderror
                            </div>
                          </div>
                          
                          <input type="hidden" value="{{$currency}}" id="currency">
                          <div class="col-lg-1 col-md-1 col-sm-1 col-xs-6">
                            <div class="form-group fill">
                              <label class="pull-right">نوعیت مواد</label>
                              <select name="type_id" class="form-control" style="direction: rtl">
                                
                                @foreach ($material_types as $type)
                                  <option {{$material->type_id == $type->material_type_id ? 'selected' : ''}} value="{{$type->material_type_id}}">{{$type->material_type}}</option>
                                @endforeach
                              </select>
                              @error('type_id') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                            </div>
                          </div>
                          <div class="col-lg-1 col-md-1 col-sm-1 col-xs-6">
                            <div class="form-group fill">
                              <label class="pull-right">دسته بندی مواد</label>
                              <select name="category_id" class="form-control" style="direction: rtl">
                                
                                @foreach ($categories as $category)
                                  <option {{$material->category_id == $category->material_category_id ? 'selected' : ''}} value="{{$category->material_category_id}}">{{$category->material_category}}</option>
                                @endforeach
                              </select>
                              @error('category_id') <p class="text-danger">{{trans('message.'.$message)}}</p>
                              @enderror
                            </div>
                          </div>
                          
                          <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                            <div class="form-group fill">
                              <label class="pull-right">تاریخ</label>
                              <input type="date" name="date" value="{{$material->date}}" class="form-control">
                              @error('date') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                            </div>
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                            <div class="form-group fill">
                              <button class="btn btn-default">
                                <a href="/dashboard/contract-carpet/{{$material->carpet_id}}"
                                   type="reset" class="btn btn-warning btn-sm">انصراف</a>
                              </button>
                              <button class="btn btn-primary btn-sm marginx" type="submit"><span
                                        class="fa fa-save"></span>
                                ذخیره
                              </button>
                            </div>
                          </div>
                        </div>
                      </form>
                    @endif
                  </div>
                </div>
                
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 " id="MRDetails">
                  <div class="btn btn-primary btn-sm hideOnPrint" onclick="printPage('MRDetails')"
                       style="position: relative;float: left;"><i class="fa fa-print"></i> Print
                  </div>
                  
                  <div class="sparkline8-graph text-muted">
                    
                    <div class="row" style="margin-top: 40px">
                      <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
      
                        <div class="static-table-list">
                          <table class="table table-hover table-xs">
                            <thead>
                            <tr>
                              <th style="text-align:right !important">نماینده</th>
                              <th style="text-align:right !important">{{$carpet->agent->user->name}}</th>
                            </tr>
                            <tr>
                              <th style="text-align:right !important">محل کار</th>
                              <th style="text-align:right !important">{{$carpet->agent->agent_address}}</th>
                            </tr>
                            <tr>
                              <th style="text-align:right !important">نمبر پارچه</th>
                              <th style="text-align:right !important">{{$carpet->parcha_number}}</th>
                            </tr>
                            <tr>
                              <th style="text-align:right !important">تاریخ شروع کار</th>
                              <th style="text-align:right !important">{{$carpet->date}}</th>
                            </tr>
                            <tr>
                              <th style="text-align:right !important">زمینه</th>
                              <th style="text-align:right !important">{{$carpet->field}}</th>
                            </tr>
                            <tr>
                              <th style="text-align:right !important">حاشیه</th>
                              <th style="text-align:right !important">{{$carpet->margin}}</th>
                            </tr>
                            <tr>
                              <th style="text-align:right !important">طول</th>
                              <th style="text-align:right !important">{{$carpet->height}}</th>
                            </tr>
                            <tr>
                              <th style="text-align:right !important">عرض</th>
                              <th style="text-align:right !important">{{$carpet->width}}</th>
                            </tr>
                            <tr>
                              <th style="text-align:right !important">مساحت</th>
                              <th style="text-align:right !important">{{$carpet->area}}</th>
                            </tr>
                             <tr>
                              <th style="text-align:right !important">کوالتی</th>
                              <th style="text-align:right !important">{{$carpet->quality->quality}}</th>
                            </tr>
                            <tr>
                              <th style="text-align:right !important">نرخ فی متر</th>
                              <th style="text-align:right !important">{{$carpet->price}}</th>
                            </tr>
                            </thead>
                          </table>
                        </div>
                      </div>
                    </div>
                    <div class="static-table-list">
                      <table class="table text-center">
                        <thead>
                        <tr>
                        
                          <th> مقدار</th>
                          <th>قیمت فی کیلو</th>
                          <th>قیمت افغانی</th>
                          <th>قیمت دالر</th>
                          <th> دسته بندی مواد</th>
                          <th> نوعیت مواد</th>
                          <th> تاریخ</th>
                          <th class="hideOnPrint"> ویرایش</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($carpetMaterials as $material)
                          <tr>
                          
                          
                            <td style="direction: ltr">{{$material->amount}} kg</td>
                            <td style="direction: ltr">{{$material->price}} AF</td>
                            <td style="direction: ltr">{{$material->total_price_af}} AF</td>
                            <td style="direction: ltr">{{$material->total_price}} $</td>
                            <td>{{$material->category->material_category}}</td>
                            <td>{{$material->type->material_type}}</td>
                            <td>{{$material->date}}</td>
                            <td class="hideOnPrint"><a class="btn btn-sm btn-info"
                                                       href="/dashboard/carpet-material/{{$material->id}}/edit"><i
                                        class="fa fa-pencil">&nbsp;&nbsp;&nbsp;</i>ویرایش</a>
                            </td>
                          </tr>
                        @empty
                          <p style="text-align:center;color:red;margin:40px auto;">هنوز
                            دریافتی ثبت نشده است</p>
                        @endforelse


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
                          <td style="direction: ltr"> {{$carpetMaterials->sum('total_price_af')}} AF</td>
                        </tr>
                        <tr>
                          <th>جمله پول دالر</th>
                          <td style="direction: ltr"> {{$carpetMaterials->sum('total_price')}} $</td>
                        </tr>
                        </tbody>
                      </table>
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

          $('#editForm').hide();

          // DESPLAYING EDIT FORM
          $('#editBtn').click(function () {
              $('#editForm').show();
              $('#editDetails').hide();
          });
      });
      // remove carpet type function
      $('.status').show();
      window.setTimeout(function () {
          $(".status").fadeTo(500, 0).slideUp(500, function () {

              $(this).remove();
          });
      }, 2000);

      // chheight
      $("#CH").blur(function () {
          var height = $('#CH').val();
          var mainHeight = parseFloat(height).toFixed(2);
          if (isNaN(mainHeight)) {
              $("#CH").val();
          } else {
              $("#CH").val(mainHeight);
          }

          var qh = $('#qheight').val();
          var sub = qh - mainHeight;
          var hw = parseFloat(sub).toFixed(2);
          $('#heightWaste').val(hw);

      });
      // chwidth
      $("#CW").blur(function () {
          var width = $('#CW').val();
          var mainWidth = parseFloat(width).toFixed(2);
          if (isNaN(mainWidth)) {
              $("#CW").val();
          } else {
              $("#CW").val(mainWidth);
          }

          var qw = $('#qwidth').val();
          var sub = qw - mainWidth;
          var ww = parseFloat(sub).toFixed(2);
          $('#widthWaste').val(ww);

      });
      //KACAHEE RECEIVED IN CHECK-BOOK
      $("#kachaee_amount").blur(function () {
          var c = $('#currency').val();
          var kp = $('#kachaee_amount').val();
          var mainPrice = parseFloat(kp).toFixed(2);
          if (isNaN(mainPrice)) {
              $("#kachaee_amount").val();
          } else {
              $("#kachaee_amount").val(mainPrice);
              var mar = mainPrice / c;
              var usPrice = parseFloat(mar).toFixed(2);
              $('#kachaee_dollar_amount').val(usPrice);
          }
      });

      // Material Amount
      $("#material-amount").blur(function () {
          var ma = $('#material-amount').val();
          var mainma = parseFloat(ma).toFixed(2);
          if (isNaN(mainma)) {
              $("#material-amount").val();
          } else {
              $("#material-amount").val(mainma);
          }
      });
      // Material Price
      $("#material-price").blur(function () {
          var mp = $('#material-price').val();
          var mainmp = parseFloat(mp).toFixed(2);
          if (isNaN(mainmp)) {
              $("#material-price").val();
          } else {
              $("#material-price").val(mainmp);
          }
      });
      //total price
      $("#material-amount,#material-price").blur(function () {
          var mp = $('#material-price').val();
          var midmp = parseFloat(mp).toFixed(2);
          var c = $('#currency').val();
          var mainmp = midmp / c;
          var ma = $('#material-amount').val();
          var mainma = parseFloat(ma).toFixed(2);
          if (mainma != '' && mainmp != '') {
              var t = mainmp * mainma;
              var at = midmp * mainma;
              var total = parseFloat(t).toFixed(2);
              var atotal = parseFloat(at).toFixed(2);
              if (isNaN(total)) {
                  $("#material-total-price").val();

              } else {
                  $("#material-total-price").val(total);
              }
              if (isNaN(atotal)) {
                  $("#material-af-total-price").val();
              } else {
                  $("#material-af-total-price").val(atotal);
              }
          }
      });
  
  
  </script>
@endsection