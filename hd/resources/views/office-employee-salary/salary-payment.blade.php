@extends('dsh.master')
@section('title' , 'تعین کردن معاش کارمند')
@section('content')
  <!-- navbar -->
  <div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <div class="breadcome-list single-page-breadcome">
        <div class="row">
          
          <div class="col-lg-5 col-md-5 col-sm-5 col-xs-5">
            <div class="breadcome-heading">
              <form action="/dashboard/expenses/search" method="POST" id="dateSearch">
                @csrf
                
                <input type="hidden" value="{{$employee->id}}" name="employee_id">
                <input type="submit" value="جستجو" class="date-submit">
                <span class="date-label">شروع</span><input type="date" @if($from_date)
                                                           value="{{ ($from_date ? $from_date : Request::old('from_date')) }}"
                                                           @endif
                                                           name="from_date" class="form-control" required>
                <span class="date-label">ختم</span><input type="date" name="to_date"
                                                          value="{{ ($to_date ? $to_date : Request::old('to_date')) }}"
                                                          class="form-control" required>
                {{-- <a href=""><i class="fa fa-search"></i></a> --}}
              </form>
            </div>
          </div>
          <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 pull-right">
            <ul class="breadcome-menu">
              <li><a href="/dashboard">داشبورد</a> <span class="bread-slash">/</span>
              </li>
              <li><span class="bread-blod">لیست کارمندان</span>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- form -->
  <div class="row" style="margin-left : 1px;">
    <div class="col-lg-7 col-md-7 col-sm-4 col-xs-12">
      <div class="row">
        <div class="sparkline12-list">
          <div class="sparkline12-hd">
            <div class="main-sparkline12-hd">
              @if(!$paymentEdit)
                <h1 class="text-right">پرداخت </h1>
              @else
                <h1 class="text-right">ویرایش پرداخت</h1>
              @endif
            </div>
          </div>
          <div class="sparkline12-graph">
            <div class="basic-login-form-ad">
              <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                  <div class="all-form-element-inner">
                    @if(!$paymentEdit)
                      <form action="/dashboard/expenses" method="post">
                        @csrf
                        
                        <input type="hidden" value="مصرف معاش" name="expense_type">
                        <input type="hidden" value="{{$employee->id}}" name="employee_id">
                        
                        <div class="form-group-inner">
                          <div class="row">
                            <div class="col-lg-2 col-md-2 col-sm-4 col-xs-12">
                              <input type="text" value="AF" disabled class="form-control">
                            </div>
                            <div class="col-lg-8 col-md-8 col-sm-10 col-xs-12">
                              <input type="number" name="amount_af" style="direction: rtl" value="{{ old('amount') }}"
                                     id="fp" class="form-control">
                              
                              <input type="hidden" name="amount" dir="ltr" id="mainP" value="{{ old('amount') }}"
                                     class="form-control">
                              <input type="hidden" value="{{$currency}}" id="currency">
                              
                              @error('amount') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                              <label class="login2 pull-right pull-right-pro"> مقدار پرداخت افغانی</label>
                            </div>
                            
                            <div class="col-lg-10 col-md-10 col-sm-10 col-xs-12">
                              <br>
                              <textarea name="description" id="" cols="5" rows="2" class="form-control"></textarea>
                              <br>
                              @error('description') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                              <label class="login2 pull-right pull-right-pro"> توضیحات </label>
                            </div>
                            
                            <div class="col-lg-10 col-md-10 col-sm-10 col-xs-12">
                              <input type="date" style="direction: rtl" name="date" id="date" class="form-control">
                              @error('date') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                              <label class="login2 pull-right pull-right-pro"> تاریخ</label>
                            </div>
                            
                            <div class="col-md-10">
                              <div class="login-horizental cancel-wp pull-right">
                                <button class="btn btn-sm btn-default" type="reset">انصراف</button>
                                <button class="btn btn-sm btn-primary submit-btn" type="submit">ذخیره</button>
                              </div>
                            </div>
                          </div>
                        </div>
                      </form>
                    @else
                      <form action="/dashboard/expenses/{{$paymentEdit->id}}" method="post">
                        @csrf
                        @method('PUT')
                        
                        <input type="hidden" value="{{$employee->id}}" name="employee_id">
                        <input type="hidden" value="مصرف معاش" name="expense_type">
                        
                        <div class="form-group-inner">
                          <div class="row">
                            <div class="col-lg-2 col-md-2 col-sm-10 col-xs-12">
                              <input type="text" value="Af" class="form-control" disabled>
                            </div>
                            <div class="col-lg-8 col-md-8 col-sm-10 col-xs-12">
                              <input type="number" name="amount_af" value="{{$paymentEdit->amount_af}}"
                                     style="direction: rtl" id="fp" class="form-control">
                              
                              <input type="hidden" name="amount" dir="ltr" id="mainP" value="{{ $paymentEdit->amount }}"
                                     class="form-control">
                              <input type="hidden" value="{{$currency}}" id="currency">
                              @error('amount') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                            </div>
                            
                            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                              <label class="login2 pull-right pull-right-pro"> مقدار پرداخت</label>
                            </div>
                            
                            <div class="col-lg-10 col-md-10 col-sm-10 col-xs-12">
                              <br>
                              <textarea name="description" style="direction: rtl" id="" cols="5" rows="2"
                                        class="form-control">{{$paymentEdit->description}}</textarea>
                              <br>
                              @error('description') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                              <label class="login2 pull-right pull-right-pro"> توضیحات </label>
                            </div>
                            
                            <div class="col-lg-10 col-md-10 col-sm-10 col-xs-12">
                              <input type="date" style="direction: rtl" value="{{$paymentEdit->date}}" name="date"
                                     id="date" class="form-control">
                              @error('date') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                              <label class="login2 pull-right pull-right-pro"> تاریخ</label>
                            </div>
                            
                            <div class="col-md-10">
                              <div class="login-horizental cancel-wp pull-right">
                                <button class="btn btn-sm btn-default" type="reset">انصراف</button>
                                <button class="btn btn-sm btn-primary submit-btn" type="submit">ذخیره</button>
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
      <br>
      
      <div class="row">
        <div class="sparkline12-list">
          <div class="sparkline12-graph">
            <div class="basic-login-form-ad">
              <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                  <div class="sparkline8-list">
                    <div class="sparkline8-hd">
                      <div class="main-sparkline8-hd">
                        <h1 class="text-center">پرداخت ها</h1>
                        <div class="alert alert-success" style="display:none;" role="alert">
                          <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                          <p class="text-center"> پرداخت حذف شد</p>
                        </div>
                        
                        @if(session("status"))
                          <div class="alert alert-success status" style="display:none;" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                      aria-hidden="true">&times;</span></button>
                            <p class="text-center">{{session('status')}}</p>
                          </div>
                        
                        @endif
                        @if(session("error"))
                          
                          <div class="alert alert-danger status" style="display:none;" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                      aria-hidden="true">&times;</span></button>
                            <p class="text-center">{{session('error')}}</p>
                          </div>
                        
                        @endif
                      </div>
                    </div>
                    <div class="sparkline8-graph">
                      <div class="static-table-list" id="employee-payment">
                        <div style="position: relative;">
                          <div class="btn btn-sm btn-primary hideOnPrint" onclick="printPage('employee-payment')"><i
                                    class="fa fa-print"></i> Print
                          </div>
                        </div>
                        <br>
                        <table class="table" id="dataTable">
                          <thead>
                          <tr class="text-center">
                            
                            <th class="text-center">نام کارمند</th>
                            <th class="text-center">توضیحات</th>
                            <th class="text-center">مقدار دالر</th>
                            <th class="text-center">مقدار به افغانی</th>
                            <th class="text-center"> تاریخ</th>
                            
                            <th class="text-center printTitle">ویرایش</th>
                            <!-- <th class="text-center">حذف</th> -->
                          </tr>
                          </thead>
                          <tbody>
                          @forelse($debits as $dib)
                            <tr class="ur{{ $dib->id }} text-center">
                              
                              <td>{{$dib->employee->name}}</td>
                              <td>{{$dib->description}}</td>
                              
                              <td>${{$dib->amount}}</td>
                              <td>AF {{$dib->amount_af}}</td>
                              <td>{{$dib->date}}</td>
                              
                              <td class="printBTN"><a href="/dashboard/expenses/{{$dib->id}}/edit"
                                                      class="btn btn-sm btn-info"><i class="fa fa-pencil"></i>&nbsp;
                                  ویرایش</a></td>
                            
                            </tr>
                          @empty
                            <h4 class="text-info text-center">هنوز موردی ثبت نشده است</h4>
                          @endforelse
                          
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
    
    <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
      <div class="sparkline8-list">
        <div class="sparkline8-hd">
          <div class="main-sparkline8-hd">
            <h1>جزئیات کارمند</h1>
          </div>
        </div>
        <div class="sparkline8-graph">
          <div class="static-table-list">
            <table class="table">
              <tbody>
              <tr>
                <td>نام</td>
                <td>{{ $employee->name }}</td>
              </tr>
              <tr>
                <td>وظیفه</td>
                <td>{{ $employee->job_title }}</td>
              </tr>
              <tr>
                <td>شماره تماس</td>
                <td>{{ $employee->phone}}</td>
              </tr>
              <tr>
                <td>تاریخ شروع قرار داد</td>
                <td>{{ $salary->from_date}}</td>
              </tr>
              <tr>
                <td>تاریخ ختم قرار داد</td>
                <td>{{ $salary->to_date }}</td>
              </tr>
              <tr>
                <td>معاش</td>
                <td>AF {{ $salary->salary }}</td>
              </tr>
              <tr>
                <td>معاش به دالر</td>
                <td>${{ $salary->salary / $currency }}</td>
              </tr>
              <tr>
                <td>جمله پرداخت در این ماه</td>
                <td>{{$debits_this_month->sum('amount_af')}}</td>
              </tr>
              
              <tr>
                <td>معاش باقیمانده در این ماه</td>
                <td>AF {{$salary->salary - $debits_this_month->sum('amount_af')}}</td>
              </tr>
              
              </tbody>
            </table>
          </div>
        </div>
      </div>
    
    </div>
  </div>
@endsection
