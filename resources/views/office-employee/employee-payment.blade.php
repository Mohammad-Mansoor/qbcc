@extends('dsh.master')

@section('content')
  <div class="row" id="employee-payment">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <div class="card">
        <div class="card-header"></div>
        <div class="card-body">
          <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
              <div class="sparkline8-graph text-muted">
                
                <div class="table-responsive">
                  <table class="table table-sm table-hover">
                    <thead>
                    
                    </thead>
                    <tbody>
                    
                    <tr>
                      <td><b>نام</b></td>
                      <td>{{$employee->name}}</td>
                    </tr>
                    <tr>
                      <td><b>وظیفه</b></td>
                      <td> {{$employee->job_title}}</td>
                    </tr>
                    <tr>
                      <td><b>دیپارتمنت</b></td>
                      <td> {{$employee->department->department}}</td>
                    </tr>
                    <tr>
                      <td><b>شماره تماس</b></td>
                      
                      <td>
                        {{$employee->phone}}
                        <i class="fa fa-phone"></i>
                      </td>
                    </tr>
                    
                    </tbody>
                  </table>
                </div>
              
              </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6"></div>
            
            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
              <h4>ACCOUNT #: {{$employee->id}}</h4>
            </div>
          </div>
          <hr>
          <div class="row">
            <div class="col-sm-12 hideOnPrint">
              @if(!isset($check_contract))
                @if($contract_number->to_date <= \Carbon\Carbon::today())
                  <h4>قرارداد شخص مذکور تکمیل است در اول قرارداد را تمدید نماید بعدا متیوانید پرداخت کنید</h4>
                @else
                  <div class="all-form-element-inner">
                    @if(!$paymentEdit)
                      <form action="/dashboard/employee-payments" method="post">
                        @csrf
                        <input type="hidden" name="employee_id" value="{{$employee->id}}">
                        
                        <div class="row" style=" display:flex;justify-content:center">
                          <div class="col-sm-12">
                            <div class="form-group-inner">
                              <div class="row"
                                   style=" display:flex;justify-content:space-around">
                                <div class="col-xs-2">
                                  <label class="pull-right">قرار داد نمبر</label>
                                  <input type="text" name="contract_number"
                                         value="{{$contract_number->contract_number}}"
                                         readonly=""
                                         class="form-control">
                                  @error('contract_number') <p class="text-danger">
                                    {{trans('message.'.$message)}}</p>
                                  @enderror
                                </div>
                                <div class="col-xs-2">
                                  <label class="pull-right">نرخ دالر</label>
                                  <input type="text" name="dollar_rate" value="{{$currency}}"
                                         class="form-control">
                                  @error('dollar_rate') <p class="text-danger">
                                    {{trans('message.'.$message)}}</p>
                                  @enderror
                                </div>
                                <div class="col-xs-2">
                                  <label class="pull-right">مقدار پول</label>
                                  <input type="text" name="amount" value="{{old('amount')}}"
                                         placeholder="مبلغ پول " class="form-control">
                                  @error('amount') <p class="text-danger">
                                    {{trans('message.'.$message)}}</p>
                                  @enderror
                                </div>
                                <div class="col-xs-2">
                                  <label class="pull-right">نوع پول</label>
                                  <select name="money_type" id="" class="form-control">
                                    <option disabled>انتخاب</option>
                                    <option value="افغانی">افغانی</option>
                                    <option value="دالر">دالر</option>
                                  </select>
                                  
                                  @error('type') <p class="text-danger">
                                    {{trans('message.'.$message)}}</p>
                                  @enderror
                                </div>
                                <div class="col-xs-2">
                                  <label class="pull-right">نوع معامله</label>
                                  <select name="type" id="" class="form-control">
                                    <option disabled>انتخاب</option>
                                    <option value="رسید">رسید</option>
                                    <option value="گرفت">گرفت</option>
                                  </select>
                                  
                                  @error('type') <p class="text-danger">
                                    {{trans('message.'.$message)}}</p>
                                  @enderror
                                </div>
                                
                                <div class="col-xs-2 center marginy">
                                  <label class="">توضیحات</label>
                                  <textarea name="description" id="description" rows="1"
                                            class="form-control"
                                            placeholder="توضیحات "></textarea>
                                  @error('description') <p class="text-danger">
                                    {{trans('message.'.$message)}}</p>
                                  @enderror
                                </div>
                                <div class="col-xs-2">
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
                          
                          </div>
                        </div>
                        <div class="row" style="margin-top: 20px;">
                          <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                            <div class="form-group-inner">
                              <div class="row"
                                   style="display:flex;justify-content:flex-start;">
                                <button class="btn btn-warning btn-sm" type="reset">انصراف
                                </button>
                                <button class="btn btn-primary btn-sm marginx" type="submit"><span
                                          class="fa fa-save"></span> ذخیره
                                </button>
                              </div>
                            </div>
                          </div>
                        </div>
                      </form>
                    @else
                      <form action="/dashboard/employee-payments/{{$paymentEdit->id}}" method="post">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="employee_id" value="{{$employee->id}}">
                        
                        
                        <div class="row" style=" display:flex;justify-content:center">
                          <div class="col-sm-12">
                            <div class="form-group-inner">
                              <div class="row"
                                   style=" display:flex;justify-content:space-around">
                                <div class="col-xs-2">
                                  <label class="pull-right">قرارداد نمبر</label>
                                  <input type="text" name="contract_number" value="{{$paymentEdit->contract_number}}"
                                         readonly
                                         class="form-control">
                                  @error('dollar_rate') <p class="text-danger">
                                    {{trans('message.'.$message)}}</p>
                                  @enderror
                                </div>
                                
                                <div class="col-xs-2">
                                  <label class="pull-right">نرخ دالر</label>
                                  <input type="text" name="dollar_rate" value="{{$paymentEdit->dollar_rate}}"
                                         class="form-control">
                                  @error('dollar_rate') <p class="text-danger">
                                    {{trans('message.'.$message)}}</p>
                                  @enderror
                                </div>
                                
                                <div class="col-xs-2">
                                  <label class="pull-right">مقدار پول</label>
                                  <input type="text" name="amount"
                                         value="@if($paymentEdit->amount > 0 ) {{$paymentEdit->amount}} @elseif($paymentEdit->amount_af > 0)  {{$paymentEdit->amount_af}} @endif"
                                         class="form-control">
                                  @error('amount') <p class="text-danger">
                                    {{trans('message.'.$message)}}</p>
                                  @enderror
                                </div>
                                <div class="col-xs-2">
                                  <label class="pull-right">نوع پول</label>
                                  <select name="money_type" id="" class="form-control">
                                    <option disabled>انتخاب</option>
                                    <option {{ $paymentEdit->amount_af > 0 ? 'selected' : '' }}  value="افغانی">افغانی
                                    </option>
                                    <option {{ $paymentEdit->amount > 0  ? 'selected' : '' }}  value="دالر">دالر
                                    </option>
                                  </select>
                                  
                                  @error('type') <p class="text-danger">
                                    {{trans('message.'.$message)}}</p>
                                  @enderror
                                </div>
                                <div class="col-xs-2">
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
                                
                                <div class="col-xs-2 center marginy">
                                  <label class="">توضیحات</label>
                                  <textarea name="description" id="description" rows="1"
                                            class="form-control"
                                            placeholder="توضیحات ">{{$paymentEdit->description}}</textarea>
                                  @error('description') <p class="text-danger">
                                    {{trans('message.'.$message)}}</p>
                                  @enderror
                                </div>
                                <div class="col-xs-2">
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
                          </div>
                        </div>
                        <div class="row" style="margin-top: 20px;">
                          <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                            <div class="form-group-inner">
                              <div class="row"
                                   style="display:flex;justify-content:flex-start;">
                                <button class="btn btn-warning btn-sm" type="reset">انصراف
                                </button>
                                <button class="btn btn-primary btn-sm marginx" type="submit"><span
                                          class="fa fa-save"></span> ذخیره
                                </button>
                              </div>
                            </div>
                          </div>
                        </div>
                      
                      </form>
                    @endif
                  </div>
                @endif
              @else
                <div class="all-form-element-inner">
                  @if(!$paymentEdit)
                    <form action="/dashboard/employee-payments" method="post">
                      @csrf
                      <input type="hidden" name="employee_id" value="{{$employee->id}}">
                      
                      <div class="row" style=" display:flex;justify-content:center">
                        <div class="col-sm-12">
                          <div class="form-group-inner">
                            <div class="row"
                                 style=" display:flex;justify-content:space-around">
                              <div class="col-xs-2">
                                <label class="pull-right">قرار داد نمبر</label>
                                <input type="text" name="contract_number"
                                       value="{{$contract_number->contract_number}}"
                                       readonly=""
                                       class="form-control">
                                @error('contract_number') <p class="text-danger">
                                  {{trans('message.'.$message)}}</p>
                                @enderror
                              </div>
                              <div class="col-xs-2">
                                <label class="pull-right">نرخ دالر</label>
                                <input type="text" name="dollar_rate" value="{{$currency}}"
                                       class="form-control">
                                @error('dollar_rate') <p class="text-danger">
                                  {{trans('message.'.$message)}}</p>
                                @enderror
                              </div>
                              <div class="col-xs-2">
                                <label class="pull-right">مقدار پول</label>
                                <input type="text" name="amount" value="{{old('amount')}}"
                                       placeholder="مبلغ پول " class="form-control">
                                @error('amount') <p class="text-danger">
                                  {{trans('message.'.$message)}}</p>
                                @enderror
                              </div>
                              <div class="col-xs-2">
                                <label class="pull-right">نوع پول</label>
                                <select name="money_type" id="" class="form-control">
                                  <option disabled>انتخاب</option>
                                  <option value="افغانی">افغانی</option>
                                  <option value="دالر">دالر</option>
                                </select>
                                
                                @error('type') <p class="text-danger">
                                  {{trans('message.'.$message)}}</p>
                                @enderror
                              </div>
                              <div class="col-xs-2">
                                <label class="pull-right">نوع معامله</label>
                                <select name="type" id="" class="form-control">
                                  <option disabled>انتخاب</option>
                                  <option value="رسید">رسید</option>
                                  <option value="گرفت">گرفت</option>
                                </select>
                                
                                @error('type') <p class="text-danger">
                                  {{trans('message.'.$message)}}</p>
                                @enderror
                              </div>
                              
                              <div class="col-xs-2 center marginy">
                                <label class="">توضیحات</label>
                                <textarea name="description" id="description" rows="1"
                                          class="form-control"
                                          placeholder="توضیحات "></textarea>
                                @error('description') <p class="text-danger">
                                  {{trans('message.'.$message)}}</p>
                                @enderror
                              </div>
                              <div class="col-xs-2">
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
                        
                        </div>
                      </div>
                      <div class="row" style="margin-top: 20px;">
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                          <div class="form-group-inner">
                            <div class="row"
                                 style="display:flex;justify-content:flex-start;">
                              <button class="btn btn-warning btn-sm" type="reset">انصراف
                              </button>
                              <button class="btn btn-primary btn-sm marginx" type="submit"><span
                                        class="fa fa-save"></span> ذخیره
                              </button>
                            </div>
                          </div>
                        </div>
                      </div>
                    </form>
                  @else
                    <form action="/dashboard/employee-payments/{{$paymentEdit->id}}" method="post">
                      @csrf
                      @method('PUT')
                      <input type="hidden" name="employee_id" value="{{$employee->id}}">
                      
                      
                      <div class="row" style=" display:flex;justify-content:center">
                        <div class="col-sm-12">
                          <div class="form-group-inner">
                            <div class="row"
                                 style=" display:flex;justify-content:space-around">
                              <div class="col-xs-2">
                                <label class="pull-right">قرارداد نمبر</label>
                                <input type="text" name="contract_number" value="{{$paymentEdit->contract_number}}"
                                       readonly
                                       class="form-control">
                                @error('dollar_rate') <p class="text-danger">
                                  {{trans('message.'.$message)}}</p>
                                @enderror
                              </div>
                              
                              <div class="col-xs-2">
                                <label class="pull-right">نرخ دالر</label>
                                <input type="text" name="dollar_rate" value="{{$paymentEdit->dollar_rate}}"
                                       class="form-control">
                                @error('dollar_rate') <p class="text-danger">
                                  {{trans('message.'.$message)}}</p>
                                @enderror
                              </div>
                              
                              <div class="col-xs-2">
                                <label class="pull-right">مقدار پول</label>
                                <input type="text" name="amount"
                                       value="@if($paymentEdit->amount > 0 ) {{$paymentEdit->amount}} @elseif($paymentEdit->amount_af > 0)  {{$paymentEdit->amount_af}} @endif"
                                       class="form-control">
                                @error('amount') <p class="text-danger">
                                  {{trans('message.'.$message)}}</p>
                                @enderror
                              </div>
                              <div class="col-xs-2">
                                <label class="pull-right">نوع پول</label>
                                <select name="money_type" id="" class="form-control">
                                  <option disabled>انتخاب</option>
                                  <option {{ $paymentEdit->amount_af > 0 ? 'selected' : '' }}  value="افغانی">افغانی
                                  </option>
                                  <option {{ $paymentEdit->amount > 0  ? 'selected' : '' }}  value="دالر">دالر
                                  </option>
                                </select>
                                
                                @error('type') <p class="text-danger">
                                  {{trans('message.'.$message)}}</p>
                                @enderror
                              </div>
                              <div class="col-xs-2">
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
                              
                              <div class="col-xs-2 center marginy">
                                <label class="">توضیحات</label>
                                <textarea name="description" id="description" rows="1"
                                          class="form-control"
                                          placeholder="توضیحات ">{{$paymentEdit->description}}</textarea>
                                @error('description') <p class="text-danger">
                                  {{trans('message.'.$message)}}</p>
                                @enderror
                              </div>
                              <div class="col-xs-2">
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
                        </div>
                      </div>
                      <div class="row" style="margin-top: 20px;">
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                          <div class="form-group-inner">
                            <div class="row"
                                 style="display:flex;justify-content:flex-start;">
                              <button class="btn btn-warning btn-sm" type="reset">انصراف
                              </button>
                              <button class="btn btn-primary btn-sm marginx" type="submit"><span
                                        class="fa fa-save"></span> ذخیره
                              </button>
                            </div>
                          </div>
                        </div>
                      </div>
                    
                    </form>
                  @endif
                </div>
              @endif
            </div>
          
          
          </div>
        </div>
      </div>
      
      <div class="card">
        
        <div class="card-header">
          <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6"></div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 hideOnPrint">
              <div class="btn-group hideOnPrint" id="exportButton" style="float: left; ">
                <div class="btn btn-sm btn-primary" style="float: left" onclick="printPage('employee-payment')"><i
                          class="fa fa-print"></i> چاپ
                </div>
              
              </div>
              <a href="/dashboard/employee-payments-all/{{$employee->id}}" style="float: left"
                 class="btn btn-sm btn-info hideOnPrint">نمایش همه</a>
            </div>
          </div>
          
          
          <form action="/dashboard/employee-payments-list-contract" method="post">
            @csrf
            <input type="hidden" name="employee_id" value="{{$employee->id}}">
            <div class="row">
              <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 hideOnPrint">
                <div class="form-group fill">
                  <label for="">جستجو بر اساس قرارداد</label>
                  <select name="contract_number" id="" class="form-control" onchange="this.form.submit()">
                      <option value="">~~~</option>
                    @foreach($contract_number_list as $contract)
                      
                      <option value="{{$contract->contract_number}}">
                        {{$contract->contract_number}}
                      </option>
                    @endforeach
                  </select>
                </div>
              </div>
            </div>
          </form>
          
          
          <div class="alert alert-success" style="display:none;" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                      aria-hidden="true">&times;</span></button>
            کارمند موفقانه حذف شد
          </div>
          
          @if(session("status"))
            <div class="alert alert-success status" style="display:none;" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
              {{session('status')}}
            </div>
          
          @endif
          @if(session("error"))
            
            <div class="alert alert-success status" style="display:none;" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
              {{session('error')}}
            </div>
          
          @endif
        
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
              
              
              <div class="table-responsive">
                
                <table class="table table-xs table-hover" id="employee_payment">
                  <thead>
                  <tr>
                    <td><b>رسید(دالر)</b></td>
                    <td><b>گرفت(دالر)</b></td>
                    <td><b>رسید(افغانی)</b></td>
                    <td><b>گرفت(افغانی)</b></td>
                    <td><b>قرارداد نمبر</b></td>
                    <td><b>تفصیلات</b></td>
                    <td><b>تاریخ</b></td>
                    <td><b>حالت</b></td>
                    <td class="hideOnPrint text-center"><b>عملیات</b></td>
                  </tr>
                  </thead>
                  <tbody>
                  @foreach($payments as $pa)
                    <tr class="ur{{$pa->id}}">
                      
                      @if($pa->type == 'رسید')
                        @if($pa->amount > 0)
                          <td>{{$pa->amount}} </td>
                        @else
                          <td>0</td>
                        @endif
                      @else
                        <td>0</td>
                      @endif
                      @if($pa->type == 'گرفت')
                        @if($pa->amount > 0)
                          <td>{{$pa->amount}}</td>
                        @else
                          <td>0</td>
                        @endif
                      @else
                        <td>0</td>
                      @endif
                      
                      @if($pa->type == 'رسید')
                        @if($pa->amount_af > 0)
                          <td>{{$pa->amount_af}} </td>
                        @else
                          <td>0</td>
                        @endif
                      @else
                        <td>0</td>
                      @endif
                      @if($pa->type == 'گرفت')
                        @if($pa->amount_af > 0)
                          <td>{{$pa->amount_af}}</td>
                        @else
                          <td>0</td>
                        @endif
                      @else
                        <td>0</td>
                      @endif
                      <td>{{$pa->contract_number}}</td>
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
                        <td class="hideOnPrint text-center">
                          <a href="/dashboard/employee-payments/{{$pa->id}}/edit"
                             class="btn btn-sm btn-info">ویرایش</a>
                          
                          <button onclick="deletePayment({{$pa->id}}, {{$pa->employee_id}})"
                                  class="btn btn-danger btn-sm "><i
                                    class="fa fa-tick"></i>حذف
                          </button>
                        </td>
                      @endif
                    
                    </tr>
                  @endforeach
                  <tr>
  
                    <th><b>گرفت ها(افغانی)</b></th>
                    <th><b>گرفت ها(دالر)</b></th>
                  </tr>
                  <tr>
                    <td><b>{{$debits_af}} </b></td>
                    <td><b>{{$debits_us}} </b></td>
                  </tr>

                  <tr>
                    <th><b>رسیدات(افغانی)</b></th>
                    <th><b>رسیدات(دالر)</b></th>
                  </tr>
                  <tr>
                    <td><b>{{$credit_af}} </b></td>
                    <td><b>{{$credit_us}} </b></td>
                  </tr>
                  <tr>
  
                    <th><b>صرف بیلانس(افغانی)</b></th>
                    <th><b>صرف بیلانس(دالر)</b></th>
                  </tr>
                  <tr>
                    @if($credit_af - $debits_af > 0)
                      <td style="direction: ltr;color: green;"><b> {{$credit_af - $debits_af}} </b></td>
                    @elseif($credit_af - $debits_af < 0)
                      <td style="direction: ltr;color: red;"><b> {{round($credit_af - $debits_af , 2)}} </b>
                      </td>
                    @else
                      <td style="direction: ltr">0</td>
                    @endif
                    @if($credit_us - $debits_us > 0)
                      <td style="direction: ltr;color: green;"><b> {{$credit_us - $debits_us}} </b></td>
                    @elseif($credit_us - $debits_us < 0)
                      <td style="direction: ltr;color: red;"><b> {{$credit_us - $debits_us}} </b></td>
                    @else
                      <td style="direction: ltr">0</td>
                    @endif
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
@endsection
@section('scripts')
  
  <script>

      $(document).ready(function () {
          $("#employee_payment").tableExport({
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
          var $buttons = $('#employee_payment').find('caption').children().detach();
          // Append the buttons to an element of your choosing
          $buttons.appendTo('#exportButton');

      });

      function deletePayment(id, employee_id) {

          swal({
              text: "مطمعین هستید ؟",
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
                          type: 'DELETE',
                          data: {
                              '_token': '{{csrf_token()}}',
                          },
                          url: '/dashboard/employee-payments/' + id,
                          success: function (res) {

                              if (res.status == 'success') {
                                  $('.ur' + id).hide();
                                  $('.alert-success').show();
                                  window.location = '/dashboard/employee-payments/' + employee_id
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