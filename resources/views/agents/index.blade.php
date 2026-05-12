@extends('dsh.master')
@section('title' , 'Agents')
@section('content')
  <div class="row">
    <div class="col-sm-12">
      
      <div class="card">
        
        <div class="card-body">
          @if(!$agent)
            <form action="/dashboard/agents" method="post" enctype="multipart/form-data">
              @csrf
              <input type="hidden" value="{{ $AccountNo }}" name="account_no" id="account_no" class="form-control">
              <input type="hidden" id="role" name="role" value="AO">
              <input type="hidden" id="lang" name="lang" value="fr">
              <div class="row">
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                  <div class="form-group fill">
                    <input type="text" value="{{ $AccountNo }}" name="" id="" class="form-control" disabled>
                    <small class="text-danger">@error('account_no') {{ __('message.'.$message) }} @enderror</small>
                  </div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                  <div class="form-group fill">
                    <input type="text" placeholder="نام" class="form-control" name="name"
                           value="{{ Request::old('name') }}">
                    @error('name') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                  </div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                  <div class="form-group fill">
                    <input type="text" id="last_name" placeholder="تخلص" value="{{ Request::old('last_name') }}"
                           name="last_name"
                           class="form-control">
                    <small class="text-danger">@error('last_name') {{ __('message.'.$message) }}@enderror</small>
                  
                  </div>
                
                </div>
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                  
                  <input type="text" name="agent_father_name"
                         value="{{ Request::old('agent_father_name') }}" placeholder="نام پدر" class="form-control">
                  <small class="text-danger">@error('agent_father_name') {{ __('message.'.$message) }}
                    @enderror
                  </small>
                </div>
                
                
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                  <input type="text" id="national_id" placeholder="نمبر تذکره" name="national_id"
                         value="{{ Request::old('national_id') }}" class="form-control">
                  <small class="text-danger">{{ $errors->first('national_id') }}</small>
                  <small class="text-danger">@error('national_id') {{ __('message.'.$message) }}@enderror
                  </small>
                </div>
                
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                  <input type="text" id="phone_no" name="phone_no" placeholder="شماره تماس"
                         value="{{ Request::old('phone_no') }}"
                         class="form-control">
                  <small class="text-danger">@error('phone_no') {{ __('message.'.$message) }}@enderror
                  </small>
                </div>
                
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                  <select name="province_id" id="province_id" class="form-control">
                    <option value="">~~~</option>
                    <option value="" disabled style="background-color: lightgray">ولایت</option>
                    @foreach($province as $p)
                      <option {{ (Request::old('province_id') == $p->province_id ? 'selected' : '') }} value="{{ $p->province_id }}">{{ $p->province }}</option>
                    @endforeach
                  </select>
                  <small class="text-danger">@error('province_id') {{ __('message.'.$message) }}@enderror
                  </small>
                </div>
                
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                  <input type="text" placeholder="آدرس" value="{{ Request::old('agent_address') }}" id="agent_address"
                         name="agent_address" class="form-control">
                  <small class="text-danger">@error('agent_address') {{ __('message.'.$message) }}
                    @enderror
                  </small>
                </div>
                
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                  <select name="contract_type" id="contract_type" class="form-control">
                    <option value="">~~~</option>
                    <option value="" disabled style="background-color: lightgray">نوعیت قرارداد</option>
                    
                    <option {{ (Request::old('contract_type') == 'contractional' ? 'selected' : '') }} value="contractional">
                      قراردادی
                    </option>
                    <option {{ (Request::old('contract_type') == 'weight' ? 'selected' : '') }} value="weight">
                      وزنی
                    </option>
                    
                    
                    <option {{ (Request::old('contract_type') == 'carpet seller' ? 'selected' : '') }} value="carpet seller">
                      فروشنده قالین
                    </option>
                  
                  </select>
                  <small class="text-danger">@error('contract_type') {{ __('message.'.$message) }}
                    @enderror
                  </small>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                  <select name="account_type" id="account_type" class="form-control">
                    <option value="">~~~</option>
                    <option value="" disabled style="background-color: lightgray"> نوعیت حساب</option>
                    <option {{ (Request::old('account_type') == 'afghani' ? 'selected' : '') }} value="afghani">
                      افغانی
                    </option>
                    <option {{ (Request::old('account_type') == 'dollar' ? 'selected' : '') }} value="dollar">
                      دالری
                    </option>
                    <option {{ (Request::old('account_type') == 'rs' ? 'selected' : '') }} value="rs">
                      کلداری
                    </option>
                  </select>
                  <small class="text-danger">@error('account_type') {{ __('message.'.$message) }}@enderror
                  </small>
                </div>
                
                
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                  <input type="file" value="{{ Request::old('image') }}" id="contract_scan_file"
                         name="image" class="form-control">
                  <small class="text-danger">@error('image') {{ $errors->first('image') }} @enderror</small>
                </div>
                
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                  <input type="date" value="{{ Request::old('contract_date') }}" name="contract_date"
                         class="form-control">
                  <small class="text-danger">@error('contract_date') {{ __('message.'.$message) }}
                    @enderror
                  </small>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                  <input type="text" id="email" placeholder="ایمیل" value="{{ old('email') }}" name="email"
                         class="form-control">
                  <small class="text-danger">@error('email') {{ __('message.'.$message) }} @enderror</small>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                  <input type="password" placeholder="رمز" id="password" name="password"
                         class="form-control">
                  <small class="text-danger">@error('password') {{ __('message.'.$message) }}@enderror
                  </small>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                  <input type="password" placeholder="تایید رمز" id="confirm" name="confirm" class="form-control">
                  <small class="text-danger">@error('password') {{ __('message.'.$message) }}@enderror
                  </small>
                </div>
              
              </div>
              <br>
              <div class="row">
                <div class="form-group fill">
                  <button type="reset" class="btn btn-sm btn-default">انصراف</button>
                  <button class="btn btn-sm btn-primary submit-btn" type="submit">ثبت</button>
                </div>
              </div>
            
            </form>
          @else
            <form action="/dashboard/agents/{{ $agent->agent_id }}" method="post" enctype="multipart/form-data">
              {{method_field('patch')}}
              @csrf
              <div class="row">
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                  <div class="form-group fill">
                    <input type="text" value="{{ $agent->account_no }}" name="" id="" class="form-control" disabled>
                    <small class="text-danger">@error('account_no') {{ __('message.'.$message) }} @enderror</small>
                  </div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                  <div class="form-group fill">
                    <input type="text" value="{{ $agent->user->name }}" id="name" name="name" class="form-control">
                    <small class="text-danger">@error('name') {{ __('message.'.$message) }} @enderror</small>
                  </div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                  <div class="form-group fill">
                    <input type="text" id="last_name" value="{{ $agent->user->last_name }}" name="last_name"
                           class="form-control">
                    <small class="text-danger">@error('last_name') {{ __('message.'.$message) }} @enderror</small>
                  </div>
                
                </div>
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                  
                  <input type="text" id="agent_father_name" name="agent_father_name"
                         value="{{ $agent->agent_father_name }}" class="form-control">
                  <small class="text-danger">@error('agent_father_name') {{ __('message.'.$message) }} @enderror</small>
                </div>
                
                
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                  <input type="text" id="national_id" name="national_id" value="{{ $agent->national_id }}"
                         class="form-control">
                  <small class="text-danger">{{ $errors->first('national_id') }}</small>
                  <small class="text-danger">@error('national_id') {{ __('message.'.$message) }} @enderror</small>
                </div>
                
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                  <input type="text" id="phone_no" name="phone_no"
                         value="{{ $agent->phone->count() > 0 ? $agent->phone[0]->phone_no :  ''  }}" dir="ltr"
                         class="form-control">
                  <small class="text-danger">@error('phone_no') {{ __('message.'.$message) }} @enderror</small>
                </div>
                
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                  <select name="province_id" id="province_id" class="form-control">
                    <option value="">~~~</option>
                    @foreach($province as $p)
                      <option {{ $agent->province_id == $p->province_id ? 'selected' : '' }} value="{{ $p->province_id }}">{{ $p->province }}</option>
                    @endforeach
                  </select>
                  <small class="text-danger">@error('province_id') {{ __('message.'.$message) }} @enderror</small>
                </div>
                
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                  <input type="text" value="{{ $agent->agent_address }}" id="agent_address" name="agent_address"
                         class="form-control">
                  <small class="text-danger">@error('agent_address') {{ __('message.'.$message) }} @enderror</small>
                </div>
                
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                  <select name="contract_type" id="contract_type" class="form-control">
                    <option value="">~~~</option>
                    <option value="" disabled style="background-color: lightgray">نوعیت قرارداد</option>
                    
                    <option {{ ($agent->contract_type == 'contractional' ? 'selected' : '') }} value="contractional">
                      قراردادی
                    </option>
                    <option {{ ($agent->contract_type == 'weight' ? 'selected' : '') }} value="weight">
                      وزنی
                    </option>
                    
                    
                    <option {{ ($agent->contract_type == 'carpet seller' ? 'selected' : '') }} value="carpet seller">
                      فروشنده قالین
                    </option>
                  
                  </select>
                  <small class="text-danger">@error('contract_type') {{ __('message.'.$message) }}
                    @enderror
                  </small>
                </div>
                <div class="col-lg-1 col-md-1 col-sm-1 col-xs-6">
                  <select name="account_type" id="account_type" class="form-control">
                    <option value="">~~~</option>
                    <option value="" disabled style="background-color: lightgray"> نوعیت حساب</option>
                    <option {{ ($agent->account_type == 'afghani' ? 'selected' : '') }} value="afghani">
                      افغانی
                    </option>
                    <option {{ ($agent->account_type == 'dollar' ? 'selected' : '') }} value="dollar">
                      دالری
                    </option>
                    <option {{ ($agent->account_type == 'rs' ? 'selected' : '') }} value="rs">
                      کلداری
                    </option>
                  </select>
                  <small class="text-danger">@error('account_type') {{ __('message.'.$message) }}@enderror
                  </small>
                </div>
                
                
                <div class="col-lg-1 col-md-1 col-sm-1 col-xs-6" dir="ltr">
                  @if($agent->image)
                    <a style="margin-top: 8px;" target="_blank" href="/{{ $agent->image }}">
                      <img style="margin-bottom: 4px;" src="/{{ $agent->image }}" name="agent image" alt="agent image"
                           height="30px" width="60px;">
                    </a>
                  @else
                    <span>عکس نماینده آپلود نشده</span>
                  @endif
                
                </div>
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                  <input type="file" id="contract_scan_file" name="image" class="form-control">
                  <small class="text-danger">@error('msg') {{ $errors->first('msg') }} @enderror</small>
                </div>
                
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                  <input autocomplete="off" type="date" value="{{ $agent->contract_date }}" id="contract_date"
                         name="contract_date" class="form-control">
                  <small class="text-danger">@error('contract_date') {{ __('message.'.$message) }} @enderror</small>
                </div>
              
              </div>
              <br>
              <div class="row">
                <div class="form-group fill">
                  <button type="reset" class="btn btn-sm btn-default">انصراف</button>
                  <button class="btn btn-sm btn-primary submit-btn" type="submit">ثبت</button>
                </div>
              </div>
            </form>
          @endif
        
        </div>
      </div>
    </div>
  
  </div>
  
  <div class="row">
    <!-- Extra small table start-->
    <div class="col-sm-12">
      <div class="card" id="agents">
        <div class="card-header">
          <h5>نماینده ها</h5>
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
          <div class="row">
            <div class="col-xs-3 col-lg-3 col-md-3 col-sm-3 hideOnPrint">
              <form action="/dashboard/agents/search" method="post">
                @csrf
                <input type="text" name="search" required
                       placeholder="جستجو" class="form-control">
              </form>
            </div>
            <div class="col-xs-3 col-lg-3 col-md-3 col-sm-3"></div>
            <div class="col-xs-4 col-lg-4 col-md-4 col-sm-4 hideOnPrint">
              <a href="/dashboard/agent-deactive" style="float: left" class="btn btn-sm btn-warning hideOnPrint">نماینده
                های غیر فعال</a>
              
              <a href="/dashboard/agent-accounts" style="float: left" class="btn btn-sm btn-info hideOnPrint">نماینده
                های حساب دار</a>
            </div>
            <div class="col-xs-2 col-lg-2 col-md-2 col-sm-2  hideOnPrint">
              <div class="btn-group hideOnPrint" id="exportButton" style="float: left; ">
                <div class="btn btn-sm btn-primary" style="float: left" onclick="printPage('agents')"><i
                          class="fa fa-print"></i> چاپ
                </div>
              
              </div>
            </div>
          
          </div>
        
        </div>
        <div class="card-body">
          
          <div class="table-responsive">
            <table class="table table-xs" id="agent_list">
              <thead>
              <tr>
                <th>نمبر حساب</th>
                <th>نام</th>
                <th>نوعیت حساب</th>
                <th>شماره تماس</th>
                <th>باقیات(دالر)</th>
                <th>باقیات(افغانی)</th>
                <th class="hideOnPrint">ویرایش</th>
                <th class="hideOnPrint">جزییات</th>
                <th class="hideOnPrint">قالین ها</th>
                <th class="hideOnPrint">حساب</th>
              </tr>
              </thead>
              <tbody>
              @php($total_credit_us = 0)
              @php($total_credit_af = 0)
              @php($total_debit_us = 0)
              @php($total_debit_af = 0)
              @if(!isset($accounts))
                @foreach($data as $d)
                  <tr>
                    
                    <td>{{ $d->account_no  }}</td>
                    <td>{{ $d->user->name .' '.$d->user->last_name  }}</td>
                    @if($d->contract_type == 'contractional')
                      <td>نماینده قرار دادی</td>
                    @elseif($d->contract_type == 'carpet seller')
                      <td>فروشنده قالین</td>
                    @else
                      <td>نماینده وزنی</td>
                    @endif
                    
                    <td dir="ltr">
                      @foreach($d->phone as $ph)
                        {{ $ph->phone_no }}
                        @break
                      @endforeach
                    </td>
                    
                    
                    @php($total_af = 0)
                    @php($total_usd = 0)
                      <?php

                      $total_af = \Illuminate\Support\Facades\DB::table('agent_payments')->where('agent_id', $d->agent_id)->where('type', 'رسید')->sum('amount_af') - \Illuminate\Support\Facades\DB::table('agent_payments')->where('agent_id', $d->agent_id)->where('type', 'گرفت')->sum('amount_af');
                      $total_usd = \Illuminate\Support\Facades\DB::table('agent_payments')->where('agent_id', $d->agent_id)->where('type', 'رسید')->sum('amount') - \Illuminate\Support\Facades\DB::table('agent_payments')->where('agent_id', $d->agent_id)->where('type', 'گرفت')->sum('amount');

                      ?>
                    {{--for dollars balance--}}
                    @if($total_usd > 0)
                      <td style="direction: ltr;color: green;">{{ $total_usd}}</td>
                    @elseif($total_usd < 0)
                      <td style="direction: ltr;color: red;">{{$total_usd}}</td>
                    @else
                      <td>{{ $total_usd }}</td>
                    @endif
                    {{--end dollars balance--}}
                    
                    {{--afghani balance--}}
                    @if($total_af > 0)
                      <td style="direction: ltr;color: green;">{{$total_af }}</td>
                    @elseif($total_af < 0)
                      <td style="direction: ltr;color: red;">{{$total_af }}</td>
                    @else
                      <td>{{ $total_af }}</td>
                    @endif


                      <?php
                      $total_credit_us += \App\AgentPayment::where('type', '=', 'رسید')->where('agent_id', $d->agent_id)->sum('amount');
                      $total_credit_af += \App\AgentPayment::where('type', '=', 'رسید')->where('agent_id', $d->agent_id)->sum('amount_af');

                      $total_debit_us += \App\AgentPayment::where('type', '=', 'گرفت')->where('agent_id', $d->agent_id)->sum('amount');
                      $total_debit_af += \App\AgentPayment::where('type', '=', 'گرفت')->where('agent_id', $d->agent_id)->sum('amount_af');

                      ?>
                    
                    
                    
                    
                    {{--end afghani balance--}}
                    <td class="hideOnPrint">
                      <button onclick="window.location.href = '/dashboard/agents/{{ $d->agent_id }}/edit'"
                              class="btn btn-sm btn-info hideOnPrint">ویرایش
                      </button>
                    </td>
                    <td class="hideOnPrint">
                      <button onclick="window.location.href = '/dashboard/agents/{{ $d->agent_id }}'"
                              class="btn btn-sm btn-warning">جزییات
                      </button>
                    </td>
                    <td class="hideOnPrint">
                      <a href="/dashboard/agent-carpet/{{$d->agent_id}}" class="btn btn-sm btn-primary">قالین ها</a>
                    </td>
                    <td class="hideOnPrint">
                      
                      
                      <a href="/dashboard/agent-payments/{{$d->agent_id}}"
                         class="btn btn-sm btn-primary pull-left hideOnPrint" style="margin-left: 20px">حساب
                      </a>
                    
                    </td>
                  </tr>
                @endforeach
              @else
                @foreach($data as $d)
                  
                  
                  @php($total_af = 0)
                  @php($total_usd = 0)
                  <?php

                  $total_af = \Illuminate\Support\Facades\DB::table('agent_payments')->where('agent_id', $d->agent_id)->where('type', 'رسید')->sum('amount_af') - \Illuminate\Support\Facades\DB::table('agent_payments')->where('agent_id', $d->agent_id)->where('type', 'گرفت')->sum('amount_af');
                  $total_usd = \Illuminate\Support\Facades\DB::table('agent_payments')->where('agent_id', $d->agent_id)->where('type', 'رسید')->sum('amount') - \Illuminate\Support\Facades\DB::table('agent_payments')->where('agent_id', $d->agent_id)->where('type', 'گرفت')->sum('amount');

                  ?>
                  
                  
                  
                  @if($d->payment->count() > 0 && $total_af !=  0 || $total_usd != 0)
                    
                    
                    <tr>
                      
                      <td>{{ $d->account_no  }}</td>
                      <td>{{ $d->user->name .' '.$d->user->last_name  }}</td>
                      
                      @if($d->contract_type == 'contractional')
                        <td>نماینده قرار دادی</td>
                      @elseif($d->contract_type == 'carpet seller')
                        <td>فروشنده قالین</td>
                      @else
                        <td>نماینده وزنی</td>
                      @endif
                      
                      <td dir="ltr">
                        @foreach($d->phone as $ph)
                          {{ $ph->phone_no }}
                          @break
                        @endforeach
                      </td>
                      
                      
                      {{--for dollars balance--}}
                      @if($total_usd > 0)
                        <td style="direction: ltr;color: green;">{{ $total_usd}}</td>
                      @elseif($total_usd < 0)
                        <td style="direction: ltr;color: red;">{{$total_usd}}</td>
                      @else
                        <td>{{ $total_usd }}</td>
                      @endif
                      {{--end dollars balance--}}
                      
                      {{--afghani balance--}}
                      @if($total_af > 0)
                        <td style="direction: ltr;color: green;">{{$total_af }}</td>
                      @elseif($total_af < 0)
                        <td style="direction: ltr;color: red;">{{$total_af }}</td>
                      @else
                        <td>{{ $total_af }}</td>
                      @endif



                        <?php
                        $total_credit_us += \App\AgentPayment::where('type', '=', 'رسید')->where('agent_id', $d->agent_id)->sum('amount');
                        $total_credit_af += \App\AgentPayment::where('type', '=', 'رسید')->where('agent_id', $d->agent_id)->sum('amount_af');

                        $total_debit_us += \App\AgentPayment::where('type', '=', 'گرفت')->where('agent_id', $d->agent_id)->sum('amount');
                        $total_debit_af += \App\AgentPayment::where('type', '=', 'گرفت')->where('agent_id', $d->agent_id)->sum('amount_af');

                        ?>
                      
                      
                      <td>
                        <button onclick="window.location.href = '/dashboard/agents/{{ $d->agent_id }}/edit'"
                                class="btn btn-sm btn-info">ویرایش
                        </button>
                      </td>
                      <td class="hideOnPrint">
                        <button onclick="window.location.href = '/dashboard/agents/{{ $d->agent_id }}'"
                                class="btn btn-sm btn-warning">جزییات
                        </button>
                      </td>
                      <td class="hideOnPrint">
                        <a href="/dashboard/agent-carpet/{{$d->agent_id}}" class="btn btn-sm btn-primary">قالین ها</a>
                      </td>
                      <td class="hideOnPrint">
                        
                        
                        <a href="/dashboard/agent-payments/{{$d->agent_id}}"
                           class="btn btn-sm btn-primary pull-left hideOnPrint" style="margin-left: 20px">حساب
                        </a>
                      
                      </td>
                    </tr>
                  @endif
                @endforeach
              @endif
              
              @if(!isset($search))
                <tr style="background: gainsboro">
                  
                  <td></td>
                  <td></td>
                  <td class="hideOnPrint"></td>
                  <td></td>
                  
                  @if($total_credit_us - $total_debit_us > 0)
                    <td style="direction: ltr;color: green;">{{round($total_credit_us-  $total_debit_us  , 2)}}</td>
                  @elseif($total_credit_us - $total_debit_us < 0)
                    <td style="direction: ltr;color: red;">{{round($total_credit_us-  $total_debit_us  , 2)}}</td>
                  @else
                    <td>{{round($total_credit_us-  $total_debit_us  , 2)}}</td>
                  @endif
                  
                  @if($total_credit_af - $total_debit_af > 0)
                    <td style="direction: ltr;color: green;">{{round($total_credit_af  - $total_debit_af  ,2)}}</td>
                  @elseif($total_credit_us - $total_debit_us < 0)
                    <td style="direction: ltr;color: red;">{{round($total_credit_af  - $total_debit_af  ,2)}}</td>
                  @else
                    <td>{{round($total_credit_af  - $total_debit_af  ,2)}}</td>
                  @endif
                  <td>مجموعه</td>
                  
                  <td class="hideOnPrint"></td>
                </tr>
              @endif
              </tbody>
            </table>
            <span class="text-center">{{$data->links()}}</span>
          </div>
        </div>
      </div>
    </div>
    <!-- Extra small table start-->
  </div>

@endsection



@section('footer-plugins')
  
  
  
  <script>
      $(document).ready(function () {
          $("#agent_list").tableExport({
              headers: true,                      // (Boolean), display table headers (th or td elements) in the <thead>, (default: true)
              footers: true,                      // (Boolean), display table footers (th or td elements) in the <tfoot>, (default: false)
              formats: ["xlsx"],                  // (String[]), filetype(s) for the export, (default: ['xlsx', 'csv', 'txt'])
              filename: "id",                     // (id, String), filename for the downloaded file, (default: 'id')
              bootstrap: true,                   // (Boolean), style buttons using bootstrap, (default: true)
              exportButtons: true,                // (Boolean), automatically generate the built-in export buttons for each of the specified formats (default: true)
              position: "bottom",                 // (top, bottom), position of the caption element relative to table, (default: 'bottom')
              ignoreRows: null,                   // (Number, Number[]), row indices to exclude from the exported file(s) (default: null)
              ignoreCols: 7,                   // (Number, Number[]), column indices to exclude from the exported file(s) (default: null)
              trimWhitespace: true,               // (Boolean), remove all leading/trailing newlines, spaces, and tabs from cell text in the exported file(s) (default: false)
              RTL: true,                         // (Boolean), set direction of the worksheet to right-to-left (default: false)
              sheetname: "id",

          });
          var $buttons = $('#agent_list').find('caption').children().detach();
          // Append the buttons to an element of your choosing
          $buttons.appendTo('#exportButton');

      });

      $('.status').show();
      window.setTimeout(function () {
          $(".status").fadeTo(500, 0).slideUp(500, function () {
              $(this).remove();
          });
      }, 2000);
  
  </script>
@endsection