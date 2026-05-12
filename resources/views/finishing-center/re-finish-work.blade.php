@extends('dsh.master')
@section('title' , 'Finishing Center Works')
@section('content')
  <!-- form -->
  <div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <div class="card">
        @if(session("error"))

          <div class="alert alert-danger status text-center" style="display:none;" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                      aria-hidden="true">&times;</span></button>
            {{session('error')}}
          </div>

        @endif
        <div class="card-header">
          <h5>ثبت تیاری قالین</h5>
        </div>
        <div class="card-body">
          <div class="all-form-element-inner">
            <form action="/dashboard/finishing-center/refinish" method="post">
              @csrf
              <div class="row mb-4 p-3" style="background: #fdfdfe; border: 1px solid #e0e0e0; border-radius: 8px; margin-bottom: 25px;">
                  <div class="col-lg-12">
                      <h6 class="text-primary mb-3"><i class="fa fa-money"></i> تنظیمات عمومی مالی (Global Financial Settings for Refinish)</h6>
                  </div>
                  <div class="col-lg-3">
                      <div class="form-group">
                          <label class="pull-right">واحد پولی (Currency)</label>
                          <select name="currency_code" id="currency_code" class="form-control" required>
                              <option value="USD">USD ($)</option>
                              <option value="AFN">AFN (؋)</option>
                          </select>
                      </div>
                  </div>
                  <div class="col-lg-3">
                      <div class="form-group">
                          <label class="pull-right">نرخ تبادله (به دالر)</label>
                          <input type="text" name="exchange_rate" id="exchange_rate" value="{{ $currency }}" class="form-control" required>
                      </div>
                  </div>
                  <div class="col-lg-3">
                      <div class="form-group">
                          <label class="text-info pull-right">حساب بدهکار (Debit Account)</label>
                          <select name="override_debit_account_id" id="override_debit_account_id" class="form-control select2">
                              @foreach($allowedDebitAccounts as $acc)
                                  <option value="{{ $acc->id }}" {{ ($mapping && $mapping->debit_account_id == $acc->id) ? 'selected' : '' }}>
                                      {{ $acc->account_code }} - {{ $acc->account_name }}
                                  </option>
                              @endforeach
                          </select>
                      </div>
                  </div>
                  <div class="col-lg-3">
                      <div class="form-group">
                          <label class="text-info pull-right">حساب بستانکار (Credit Account)</label>
                          <select name="override_credit_account_id" id="override_credit_account_id" class="form-control select2">
                              @foreach($allowedCreditAccounts as $acc)
                                  <option value="{{ $acc->id }}" {{ ($mapping && $mapping->credit_account_id == $acc->id) ? 'selected' : '' }}>
                                      {{ $acc->account_code }} - {{ $acc->account_name }}
                                  </option>
                              @endforeach
                          </select>
                      </div>
                  </div>
              </div>

                <div class="row">
                  <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
                    <div class="form-group fill">
                      <label class="pull-right">نمبر قالین</label>
                      <input type="text" value="{{$carpet->carpet_no}}" readonly
                             class="form-control">

                    </div>
                  </div>
                  <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
                    <div class="form-group fill">
                      <label class="pull-right">نوعیت</label>
                      <input type="text" value="{{$carpet->type->carpet_type}}" readonly
                             class="form-control">

                    </div>
                  </div>
                  <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
                    <div class="form-group fill">
                      <label class="pull-right">تیاری نمبر</label>
                      <input type="text" name="finish_number_qaitan" id="finish_number_qaitan" value="{{$FinishNo}}"
                             class="form-control">
                      @if(session("finish_number_qaitan"))
                        <small class="text-danger">{{session("finish_number_qaitan")}}
                        </small>
                      @endif
                    </div>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <label class="pull-right">تیم تیاری</label>
                      <select name="team_id_qaitan" id="team_id" class="form-control">
                        @foreach($teams as $team)
                          <option {{ (Request::old('team_id'))}} value="{{$team->id}}">{{$team->name}}</option>
                        @endforeach
                      </select>
                      <small class="text-danger">@error('team_id_qaitan') {{ __('message.'.$message) }}@enderror
                      </small>
                    </div>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <label class="pull-right">نوع تیاری</label>
                      <select name="category_id_qaitan" id="category_id" class="form-control">
                        {{--@foreach($team_categories as $category)--}}
                        <option {{ (Request::old('category_id'))}} value="1">قیتان</option>
                        {{--@endforeach--}}
                      </select>

                      <small class="text-danger">@error('category_id_qaitan') {{ __('message.'.$message) }}@enderror
                      </small>
                    </div>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <label class="pull-right">مصرف فی متر یا متر مربع</label>
                      <input type="text" placeholder="مصرف تیاری به دالر"
                             class="form-control price_af_qaitan" name="price_af_qaitan" id="fpq"
                             value="{{\Illuminate\Support\Facades\Request::old('price_af_qaitan')}}">
                      <input type="hidden" id="mainPq" value="{{old('price')}}" name="price_qaitan">
                      <input type="hidden" id="currencyq" value="{{$currency}}">
                      <input type="hidden" value="{{$carpet->carpet_id}}"
                             name="carpetId">
                      @if(session("price_af_qaitan"))
                        <small class="text-danger">{{session("price_af_qaitan")}}
                        </small>
                      @endif
                    </div>
                  </div>

                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <label class="pull-right">تاریخ تیاری قالین</label>
                      <input type="date" id="date_qaitan" name="date_qaitan"
                             placeholder="تاریخ را وارد کنید" class="form-control"
                             value="{{old('date_qaitan')}}">
                      @if(session("date_qaitan"))
                        <small class="text-danger">{{session("date_qaitan")}}
                        </small>
                      @endif
                    </div>
                  </div>
                  <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
                    <div class="form-group fill" style="margin-top: 35px;">

                      <div class="switch switch-primary d-inline m-r-10">
                        <input type="checkbox" id="switch-p-1" class="qaitan_checkbox" name="qaitan_checkbox">
                        <label for="switch-p-1" class="cr"></label>
                      </div>

                    </div>
                  </div>
                </div>


                <div class="row">
                  <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
                    <div class="form-group fill">
                      <label class="pull-right">نمبر قالین</label>
                      <input type="text" value="{{$carpet->carpet_no}}" readonly
                             class="form-control">

                    </div>
                  </div>
                  <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
                    <div class="form-group fill">
                      <label class="pull-right">نوعیت</label>
                      <input type="text" value="{{$carpet->type->carpet_type}}" readonly
                             class="form-control">

                    </div>
                  </div>
                  <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
                    <div class="form-group fill">
                      <label class="pull-right">تیاری نمبر</label>
                      <input type="text" name="finish_number_rofo" id="finish_number_rofo" value="{{$FinishNo}}"
                             class="form-control">
                      @if(session("finish_number_rofo"))
                        <small class="text-danger">{{session("finish_number_rofo")}}
                        </small>
                      @endif
                    </div>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <label class="pull-right">تیم تیاری</label>
                      <select name="team_id_rofo" id="team_id" class="form-control">
                        @foreach($teams as $team)
                          <option {{ (Request::old('team_id'))}} value="{{$team->id}}">{{$team->name}}</option>
                        @endforeach
                      </select>
                      <small class="text-danger">@error('team_id') {{ __('message.'.$message) }} @enderror</small>
                    </div>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <label class="pull-right">نوع تیاری</label>
                      <select name="category_id_rofo" id="category_id" class="form-control">
                        {{--@foreach($team_categories as $category)--}}
                        <option {{ (Request::old('category_id'))}} value="2">رفو</option>
                        {{--@endforeach--}}
                      </select>

                      <small class="text-danger">@error('category_id') {{ __('message.'.$message) }} @enderror</small>
                    </div>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <label class="pull-right">مصرف فی متر یا متر مربع</label>
                      <input type="text" placeholder="مصرف تیاری به دالر"
                             class="form-control price_af_rofo" name="price_af_rofo" id="fpr"
                             value="{{old('price_af')?old('price_af'): ''}}">
                      <input type="hidden" id="mainPr" value="{{old('price')}}" name="price_rofo">
                      <input type="hidden" id="currencyr" value="{{$currency}}">
                      <input type="hidden" value="{{$carpet->carpet_id}}"
                             name="carpetId">
                      @if(session("price_af_rofo"))
                        <small class="text-danger">{{session("price_af_rofo")}}
                        </small>
                      @endif
                    </div>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <label class="pull-right">تاریخ تیاری قالین</label>
                      <input type="date" id="date_rofo" name="date_rofo"
                             placeholder="تاریخ را وارد کنید" class="form-control"
                             value="{{old('date')}}">
                      @if(session("date_rofo"))
                        <small class="text-danger">{{session("date_rofo")}}
                        </small>
                      @endif
                    </div>
                  </div>


                  <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
                    <div class="form-group fill" style="margin-top: 35px;">

                      <div class="switch switch-primary d-inline m-r-10">
                        <input type="checkbox" class="rofo_checkbox" id="switch-p-2" name="rofo_checkbox">
                        <label for="switch-p-2" class="cr"></label>
                      </div>

                    </div>
                  </div>
                </div>


                <div class="row">
                  <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
                    <div class="form-group fill">
                      <label class="pull-right">نمبر قالین</label>
                      <input type="text" value="{{$carpet->carpet_no}}" readonly
                             class="form-control">

                    </div>
                  </div>
                  <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
                    <div class="form-group fill">
                      <label class="pull-right">نوعیت</label>
                      <input type="text" value="{{$carpet->type->carpet_type}}" readonly
                             class="form-control">

                    </div>
                  </div>
                  <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
                    <div class="form-group fill">
                      <label class="pull-right">تیاری نمبر</label>
                      <input type="text" name="finish_number_cheet" id="finish_number_cheet" value="{{$FinishNo}}"
                             class="form-control">

                      @if(session("finish_number_cheet"))
                        <small class="text-danger">{{session("finish_number_cheet")}}
                        </small>
                      @endif
                    </div>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <label class="pull-right">تیم تیاری</label>
                      <select name="team_id_cheet" id="team_id" class="form-control">
                        @foreach($teams as $team)
                          <option {{ (Request::old('team_id'))}} value="{{$team->id}}">{{$team->name}}</option>
                        @endforeach
                      </select>
                      <small class="text-danger">@error('team_id') {{ __('message.'.$message) }} @enderror</small>
                    </div>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <label class="pull-right">نوع تیاری</label>
                      <select name="category_id_cheet" id="category_id" class="form-control">
                        {{--@foreach($team_categories as $category)--}}
                        <option {{ (Request::old('category_id'))}} value="3">چیت</option>
                        {{--@endforeach--}}
                      </select>

                      <small class="text-danger">@error('category_id') {{ __('message.'.$message) }} @enderror</small>
                    </div>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <label class="pull-right">مصرف فی متر یا متر مربع</label>
                      <input type="text" placeholder="مصرف تیاری به دالر"
                             class="form-control price_af_cheet" name="price_af_cheet"  id="fpc"
                             value="{{old('price_af')?old('price_af'): ''}}">
                      <input type="hidden" id="mainPc" value="{{old('price')}}" name="price_cheet">
                      <input type="hidden" id="currencyc" value="{{$currency}}">
                      <input type="hidden" value="{{$carpet->carpet_id}}"
                             name="carpetId">
                      @if(session("price_af_cheet"))
                        <small class="text-danger">{{session("price_af_cheet")}}
                        </small>
                      @endif
                    </div>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <label class="pull-right">تاریخ تیاری قالین</label>
                      <input type="date" id="date_cheet" name="date_cheet"
                             placeholder="تاریخ را وارد کنید" class="form-control"
                             value="{{old('date')}}">
                      @if(session("date_cheet"))
                        <small class="text-danger">{{session("date_cheet")}}
                        </small>
                      @endif
                    </div>
                  </div>

                  <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
                    <div class="form-group fill" style="margin-top: 35px;">

                      <div class="switch switch-primary d-inline m-r-10">
                        <input type="checkbox" class="cheet_checkbox" id="switch-p-3" name="cheet_checkbox">
                        <label for="switch-p-3" class="cr"></label>
                      </div>

                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
                    <div class="form-group fill">
                      <label class="pull-right">نمبر قالین</label>
                      <input type="text" value="{{$carpet->carpet_no}}" readonly
                             class="form-control">

                    </div>
                  </div>
                  <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
                    <div class="form-group fill">
                      <label class="pull-right">نوعیت</label>
                      <input type="text" value="{{$carpet->type->carpet_type}}" readonly
                             class="form-control">

                    </div>
                  </div>
                  <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
                    <div class="form-group fill">
                      <label class="pull-right">تیاری نمبر</label>
                      <input type="text" name="finish_number_labaki" id="finish_number_labaki" value="{{$FinishNo}}"
                             class="form-control">

                      @if(session("finish_number_labaki"))
                        <small class="text-danger">{{session("finish_number_labaki")}}
                        </small>
                      @endif
                    </div>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <label class="pull-right">تیم تیاری</label>
                      <select name="team_id_labaki" id="team_id" class="form-control">
                        @foreach($teams as $team)
                          <option {{ (Request::old('team_id'))}} value="{{$team->id}}">{{$team->name}}</option>
                        @endforeach
                      </select>
                      <small class="text-danger">@error('team_id') {{ __('message.'.$message) }} @enderror</small>
                    </div>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <label class="pull-right">نوع تیاری</label>
                      <select name="category_id_labaki" id="category_id" class="form-control">
                        {{--@foreach($team_categories as $category)--}}
                        <option {{ (Request::old('category_id'))}} value="4">لبکی</option>
                        {{--@endforeach--}}
                      </select>

                      <small class="text-danger">@error('category_id') {{ __('message.'.$message) }} @enderror</small>
                    </div>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <label class="pull-right">مصرف فی متر یا متر مربع</label>
                      <input type="text" placeholder="مصرف تیاری به دالر"
                             class="form-control price_af_labaki" name="price_af_labaki" id="fpl"
                             value="{{old('price_af')?old('price_af'): ''}}">
                      <input type="hidden" id="mainPl" value="{{old('price')}}" name="price_labaki">
                      <input type="hidden" id="currencyl" value="{{$currency}}">
                      <input type="hidden" value="{{$carpet->carpet_id}}"
                             name="carpetId">
                      @if(session("price_af_labaki"))
                        <small class="text-danger">{{session("price_af_labaki")}}
                        </small>
                      @endif
                    </div>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <label class="pull-right">تاریخ تیاری قالین</label>
                      <input type="date" id="date_labaki" name="date_labaki"
                             placeholder="تاریخ را وارد کنید" class="form-control"
                             value="{{old('date')}}">
                      @if(session("date_labaki"))
                        <small class="text-danger">{{session("date_labaki")}}
                        </small>
                      @endif
                    </div>
                  </div>

                  <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
                    <div class="form-group fill" style="margin-top: 35px;">

                      <div class="switch switch-primary d-inline m-r-10">
                        <input type="checkbox" class="labaki_checkbox" id="switch-p-4" name="labaki_checkbox">
                        <label for="switch-p-4" class="cr"></label>
                      </div>

                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
                    <div class="form-group fill">
                      <label class="pull-right">نمبر قالین</label>
                      <input type="text" value="{{$carpet->carpet_no}}" readonly
                             class="form-control">

                    </div>
                  </div>
                  <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
                    <div class="form-group fill">
                      <label class="pull-right">نوعیت</label>
                      <input type="text" value="{{$carpet->type->carpet_type}}" readonly
                             class="form-control">

                    </div>
                  </div>
                  <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
                    <div class="form-group fill">
                      <label class="pull-right">تیاری نمبر</label>
                      <input type="text" name="finish_number_popak" id="finish_number_popak" value="{{$FinishNo}}"
                             class="form-control">

                      @if(session("finish_number_popak"))
                        <small class="text-danger">{{session("finish_number_popak")}}
                        </small>
                      @endif
                    </div>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <label class="pull-right">تیم تیاری</label>
                      <select name="team_id_popak" id="team_id" class="form-control">
                        @foreach($teams as $team)
                          <option {{ (Request::old('team_id'))}} value="{{$team->id}}">{{$team->name}}</option>
                        @endforeach
                      </select>
                      <small class="text-danger">@error('team_id') {{ __('message.'.$message) }} @enderror</small>
                    </div>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <label class="pull-right">نوع تیاری</label>
                      <select name="category_id_popak" id="category_id" class="form-control">
                        {{--@foreach($team_categories as $category)--}}
                        <option {{ (Request::old('category_id'))}} value="5">پوپک</option>
                        {{--@endforeach--}}
                      </select>

                    </div>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <label class="pull-right">مصرف فی متر یا متر مربع</label>
                      <input type="text" placeholder="مصرف تیاری به دالر"
                             class="form-control price_af_popak" name="price_af_popak" id="fppo"
                             value="{{old('price_af')?old('price_af'): ''}}">
                      <input type="hidden" id="mainPpo" value="{{old('price')}}" name="price_popak">
                      <input type="hidden" id="currencypo" value="{{$currency}}">
                      <input type="hidden" value="{{$carpet->carpet_id}}"
                             name="carpetId">
                      @if(session("price_af_popak"))
                        <small class="text-danger">{{session("price_af_popak")}}
                        </small>
                      @endif
                    </div>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <label class="pull-right">تاریخ تیاری قالین</label>
                      <input type="date" id="date_popak" name="date_popak"
                             placeholder="تاریخ را وارد کنید" class="form-control"
                             value="{{old('date')}}">
                      @if(session("date_popak"))
                        <small class="text-danger">{{session("date_popak")}}
                        </small>
                      @endif
                    </div>
                  </div>

                  <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
                    <div class="form-group fill" style="margin-top: 35px;">

                      <div class="switch switch-primary d-inline m-r-10">
                        <input type="checkbox" class="popak_checkbox" id="switch-p-5" name="popak_checkbox">
                        <label for="switch-p-5" class="cr"></label>
                      </div>

                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
                    <div class="form-group fill">
                      <label class="pull-right">نمبر قالین</label>
                      <input type="text" value="{{$carpet->carpet_no}}" readonly
                             class="form-control">

                    </div>
                  </div>
                  <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
                    <div class="form-group fill">
                      <label class="pull-right">نوعیت</label>
                      <input type="text" value="{{$carpet->type->carpet_type}}" readonly
                             class="form-control">

                    </div>
                  </div>
                  <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
                    <div class="form-group fill">
                      <label class="pull-right">تیاری نمبر</label>
                      <input type="text" name="finish_number_kash" id="finish_number_kash" value="{{$FinishNo}}"
                             class="form-control">

                      <small class="text-danger">@error('finish_number') {{ __('message.'.$message) }} @enderror</small>
                    </div>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <label class="pull-right">تیم تیاری</label>
                      <select name="team_id_kash" id="team_id" class="form-control">
                        @foreach($teams as $team)
                          <option {{ (Request::old('team_id'))}} value="{{$team->id}}">{{$team->name}}</option>
                        @endforeach
                      </select>
                      <small class="text-danger">@error('team_id') {{ __('message.'.$message) }} @enderror</small>
                    </div>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <label class="pull-right">نوع تیاری</label>
                      <select name="category_id_kash" id="category_id" class="form-control">
                        {{--@foreach($team_categories as $category)--}}
                        <option {{ (Request::old('category_id'))}} value="6">کش</option>
                        {{--@endforeach--}}
                      </select>

                      <small class="text-danger">@error('category_id') {{ __('message.'.$message) }} @enderror</small>
                    </div>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <label class="pull-right">مصرف فی متر یا متر مربع</label>
                      <input type="text" placeholder="مصرف تیاری به دالر"
                             class="form-control price_af_kash" name="price_af_kash" id="fpk"
                             value="{{old('price_af')?old('price_af'): ''}}">
                      <input type="hidden" id="mainPk" value="{{old('price')}}" name="price_kash">
                      <input type="hidden" id="currencyk" value="{{$currency}}">
                      <input type="hidden" value="{{$carpet->carpet_id}}"
                             name="carpetId">
                      @error('price') <p class="text-danger">
                        {{trans('message.'.$message)}}</p> @enderror
                    </div>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <label class="pull-right">تاریخ تیاری قالین</label>
                      <input type="date" id="date_kash" name="date_kash"
                             placeholder="تاریخ را وارد کنید" class="form-control"
                             value="{{old('date')}}">
                      @error('date') <p class="text-danger">
                        {{trans('message.'.$message)}}</p> @enderror
                    </div>
                  </div>

                  <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
                    <div class="form-group fill" style="margin-top: 35px;">

                      <div class="switch switch-primary d-inline m-r-10">
                        <input type="checkbox" id="switch-p-6" class="kash_checkbox" name="kash_checkbox">
                        <label for="switch-p-6" class="cr"></label>
                      </div>

                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
                    <div class="form-group fill">
                      <label class="pull-right">نمبر قالین</label>
                      <input type="text" value="{{$carpet->carpet_no}}" readonly
                             class="form-control">

                    </div>
                  </div>
                  <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
                    <div class="form-group fill">
                      <label class="pull-right">نوعیت</label>
                      <input type="text" value="{{$carpet->type->carpet_type}}" readonly
                             class="form-control">

                    </div>
                  </div>
                  <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
                    <div class="form-group fill">
                      <label class="pull-right">تیاری نمبر</label>
                      <input type="text" name="finish_number_rang" id="finish_number_rang" value="{{$FinishNo}}"
                             class="form-control">

                      @if(session("finish_number_rang"))
                        <small class="text-danger">{{session("finish_number_rang")}}
                        </small>
                      @endif
                    </div>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <label class="pull-right">تیم تیاری</label>
                      <select name="team_id_rang" id="team_id" class="form-control">
                        @foreach($teams as $team)
                          <option {{ (Request::old('team_id'))}} value="{{$team->id}}">{{$team->name}}</option>
                        @endforeach
                      </select>
                      <small class="text-danger">@error('team_id') {{ __('message.'.$message) }} @enderror</small>
                    </div>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <label class="pull-right">نوع تیاری</label>
                      <select name="category_id_rang" id="category_id" class="form-control">
                        {{--@foreach($team_categories as $category)--}}
                        <option {{ (Request::old('category_id'))}} value="7">رنگ</option>
                        {{--@endforeach--}}
                      </select>

                      <small class="text-danger">@error('category_id') {{ __('message.'.$message) }} @enderror</small>
                    </div>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <label class="pull-right">مصرف فی متر یا متر مربع</label>
                      <input type="text" placeholder="مصرف تیاری به دالر"
                             class="form-control price_af_rang" name="price_af_rang" id="fpra"
                             value="{{old('price_af')?old('price_af'): ''}}">
                      <input type="hidden" id="mainPra" value="{{old('price')}}" name="price_rang">
                      <input type="hidden" id="currencyra" value="{{$currency}}">
                      <input type="hidden" value="{{$carpet->carpet_id}}"
                             name="carpetId">
                      @if(session("price_af_rang"))
                        <small class="text-danger">{{session("price_af_rang")}}
                        </small>
                      @endif
                    </div>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <label class="pull-right">تاریخ تیاری قالین</label>
                      <input type="date" id="date_rang"  name="date_rang"
                             placeholder="تاریخ را وارد کنید" class="form-control"
                             value="{{old('date')}}">
                      @if(session("date_rang"))
                        <small class="text-danger">{{session("date_rang")}}
                        </small>
                      @endif
                    </div>
                  </div>

                  <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
                    <div class="form-group fill" style="margin-top: 35px;">

                      <div class="switch switch-primary d-inline m-r-10">
                        <input type="checkbox" id="switch-p-7" class="rang_checkbox" name="rang_checkbox">
                        <label for="switch-p-7" class="cr"></label>
                      </div>

                    </div>
                  </div>
                </div>

              <div class="row">
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                  <div class="form-group fill">
                    <button class="btn btn-warning btn-sm"><a href="/dashboard/finishing-center">
                        انصراف </a></button>
                    <button class="btn btn-primary btn-sm" onclick="valid()" type="submit"> <span
                              class="fa fa-save"></span> ذخیره و ثبت نهایی
                    </button>
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection


@section('scripts')

  <script>
      $(document).ready(function () {
          $('.select2').select2();
          $('#override_debit_account_id').select2();
          $('#override_credit_account_id').select2();
          $('#currency_code').select2();
          $('select').select2();
      });
      function valid() {
          var qaitan_checkbox = $('.qaitan_checkbox').prop('checked');
          if (qaitan_checkbox){

              $('.price_af_qaitan').attr('required','required');
              $('#date_qaitan').attr('required','required');
              $('#finish_number_qaitan').attr('required','required');

          }else{

              $('.price_af_qaitan').removeAttr('required');
              $('#date_qaitan').removeAttr('required');
              $('#finish_number_qaitan').removeAttr('required');
          }

          var rofo_checkbox = $('.rofo_checkbox').prop('checked');
          if (rofo_checkbox){

              $('.price_af_rofo').attr('required','required');
              $('#date_rofo').attr('required','required');
              $('#finish_number_rofo').attr('required','required');

          }else{

              $('.price_af_rofo').removeAttr('required');
              $('#date_rofo').removeAttr('required');
              $('#finish_number_rofo').removeAttr('required');
          }

          var cheet_checkbox = $('.cheet_checkbox').prop('checked');
          if (cheet_checkbox){

              $('.price_af_cheet').attr('required','required');
              $('#date_cheet').attr('required','required');
              $('#finish_number_cheet').attr('required','required');

          }else{

              $('.price_af_cheet').removeAttr('required');
              $('#date_cheet').removeAttr('required');
              $('#finish_number_cheet').removeAttr('required');
          }

          var labaki_checkbox = $('.labaki_checkbox').prop('checked');
          if (labaki_checkbox){

              $('.price_af_labaki').attr('required','required');
              $('#date_labaki').attr('required','required');
              $('#finish_number_labaki').attr('required','required');

          }else{

              $('.price_af_labaki').removeAttr('required');
              $('#date_labaki').removeAttr('required');
              $('#finish_number_labaki').removeAttr('required');
          }

          var popak_checkbox = $('.popak_checkbox').prop('checked');
          if (popak_checkbox){

              $('.price_af_popak').attr('required','required');
              $('#date_popak').attr('required','required');
              $('#finish_number_popak').attr('required','required');

          }else{

              $('.price_af_popak').removeAttr('required');
              $('#date_popak').removeAttr('required');
              $('#finish_number_popak').removeAttr('required');
          }

          var kash_checkbox = $('.kash_checkbox').prop('checked');
          if (kash_checkbox){

              $('.price_af_kash').attr('required','required');
              $('#date_kash').attr('required','required');
              $('#finish_number_kash').attr('required','required');

          }else{

              $('.price_af_kash').removeAttr('required');
              $('#date_kash').removeAttr('required');
              $('#finish_number_kash').removeAttr('required');
          }

          var rang_checkbox = $('.rang_checkbox').prop('checked');
          if (rang_checkbox){

              $('.price_af_rang').attr('required','required');
              $('#date_rang').attr('required','required');
              $('#finish_number_rang').attr('required','required');

          }else{

              $('.price_af_rang').removeAttr('required');
              $('#date_rang').removeAttr('required');
              $('#finish_number_rang').removeAttr('required');
          }

          if(qaitan_checkbox == true && rofo_checkbox == true && cheet_checkbox == true && labaki_checkbox == true && popak_checkbox == true && popak_checkbox == true && kash_checkbox == true && rang_checkbox == true){
              $('.finished').val(1)
          }
      }
  </script>


@endsection
