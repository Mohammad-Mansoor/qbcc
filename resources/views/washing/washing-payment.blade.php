@extends('dsh.master')

@section('content')
  <br>
  <div class="row" id="washing-payment">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
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
          <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
              <div class="table-responsive">
                <table class="table table-xs table-hover">
                  <tbody>
                  <tr>
                    <td><b>نام</b></td>
                    <td>{{$team->name}}</td>
                  </tr>
                  <tr>
                    <td><b>ادرس</b></td>
                    <td> {{$team->address}}</td>
                  </tr>
                  <tr>
                    <td><b>شماره تماس</b></td>
                    <td style="direction: ltr;">
                      {{$team->contact_no}}
                      <i class="fa fa-phone"></i>
                    </td>
                  </tr>
                  </tbody>
                </table>
              </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6"></div>
            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
              <h4>ACCOUNT #: {{$team->id}}</h4>
            </div>
          </div>
          <div class="rowt">
            <div class="col-sm-12 hideOnPrint">
              <div class="all-form-element-inner">
                @if(!$paymentEdit)
                  <form action="/dashboard/washing-payments" method="post">
                    @csrf
                    <input type="hidden" name="team_id" value="{{$team->id}}">
                    
                    <div class="row" style=" display:flex;justify-content:center">
                      <div class="col-sm-12">
                        <div class="form-group-inner">
                          <div class="row" style=" display:flex;justify-content:space-around">
                            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2" style="margin-top: 10px">
                              <label class="pull-right"> شست نمبر</label>
                              <select name="wash_number" id="wash_number" class="form-control">
                                <option value="نقد">نقد</option>
                                @foreach($wash_numbers as $ch)
                                  <option value="{{$ch->wash_number_sh}}">{{$ch->wash_number_sh}}</option>
                                @endforeach
                              </select>
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                              <label class="pull-right">نرخ دالر</label>
                              <input type="text" name="dollar_rate" value="{{$currency}}" class="form-control">
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                              <label class="pull-right">مقدار پول</label>
                              <input type="text" name="amount" value="{{old('amount')}}" placeholder="مبلغ پول " class="form-control">
                            </div>
                            <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
                              <label class="pull-right">نوع پول</label>
                              <select name="money_type" id="" class="form-control">
                                <option value="افغانی">افغانی</option>
                                <option value="دالر">دالر</option>
                              </select>
                            </div>
                            <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
                              <label class="pull-right">نوع معامله</label>
                              <select name="type" id="" class="form-control">
                                <option value="رسید">رسید</option>
                                <option value="گرفت">گرفت</option>
                              </select>
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                              <label class="">توضیحات</label>
                              <textarea name="description" id="description" rows="1" class="form-control" placeholder="توضیحات "></textarea>
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                              <label class="pull-right">تاریخ</label>
                              <input type="date" name="date" class="form-control">
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    
                    <div class="row" style="margin-top: 10px;">
                        <div class="col-lg-12">
                            <div class="row p-3" style="background: #f8f9fa; border: 1px solid #ddd; border-radius: 5px; margin: 0 15px;">
                                <div class="col-lg-12">
                                    <h6 class="mb-3 text-muted"><i class="fa fa-university"></i> تنظیمات حسابی (Washing Payment Accounting)</h6>
                                </div>
                                <div class="col-lg-5">
                                    <div class="form-group">
                                        <label class="text-info pull-right">حساب بدهکار (Debit)</label>
                                        <select name="override_debit_account_id" id="override_debit_account_id" class="form-control">
                                            @foreach($allowedDebitAccounts as $acc)
                                                <option value="{{ $acc->id }}" {{ ($mapping && $mapping->debit_account_id == $acc->id) ? 'selected' : '' }}>
                                                    {{ $acc->account_code }} - {{ $acc->account_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-5">
                                    <div class="form-group">
                                        <label class="text-info pull-right">حساب بستانکار (Credit)</label>
                                        <select name="override_credit_account_id" id="override_credit_account_id" class="form-control">
                                            @foreach($allowedCreditAccounts as $acc)
                                                <option value="{{ $acc->id }}" {{ ($mapping && $mapping->credit_account_id == $acc->id) ? 'selected' : '' }}>
                                                    {{ $acc->account_code }} - {{ $acc->account_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-2" style="margin-top: 25px;">
                                    <button class="btn btn-primary btn-sm btn-block marginx" type="submit"><span class="fa fa-save"></span> ذخیره و ثبت نهایی</button>
                                </div>
                            </div>
                        </div>
                    </div>
                  </form>
                @else
                  <form action="/dashboard/washing-payments/{{$paymentEdit->id}}" method="post">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="team_id" value="{{$team->id}}">
                    <div class="row" style=" display:flex;justify-content:center">
                      <div class="col-sm-12">
                        <div class="form-group-inner">
                          <div class="row" style=" display:flex;justify-content:space-around">
                            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2" style="margin-top: 10px">
                              <label class="pull-right">شست نمبر</label>
                              <select name="wash_number" id="wash_number" class="form-control">
                                <option value="نقد">نقد</option>
                                @foreach($wash_numbers as $ch)
                                  <option {{ $paymentEdit->wash_number ==  $ch->wash_number_sh  ? 'selected' : '' }}  value="{{$ch->wash_number_sh}}">{{$ch->wash_number_sh}}</option>
                                @endforeach
                              </select>
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                              <label class="pull-right">نرخ دالر</label>
                              <input type="text" name="dollar_rate" value="{{$paymentEdit->dollar_rate}}" class="form-control">
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                              <label class="pull-right">مقدار پول</label>
                              <input type="text" name="amount" value="@if($paymentEdit->amount > 0 ) {{$paymentEdit->amount}} @elseif($paymentEdit->amount_af > 0)  {{$paymentEdit->amount_af}} @endif" class="form-control">
                            </div>
                            <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
                              <label class="pull-right">نوع پول</label>
                              <select name="money_type" id="" class="form-control">
                                <option {{ $paymentEdit->amount_af > 0 ? 'selected' : '' }}  value="افغانی">افغانی</option>
                                <option {{ $paymentEdit->amount > 0  ? 'selected' : '' }}  value="دالر">دالر</option>
                              </select>
                            </div>
                            <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
                              <label class="pull-right">نوع معامله</label>
                              <select name="type" id="" class="form-control">
                                <option {{ $paymentEdit->type == 'رسید' ? 'selected' : '' }} value="رسید">رسید</option>
                                <option {{ $paymentEdit->type == 'گرفت' ? 'selected' : '' }} value="گرفت">گرفت</option>
                              </select>
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                              <label class="">توضیحات</label>
                              <textarea name="description" id="description" rows="1" class="form-control">{{$paymentEdit->description}}</textarea>
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                              <label class="pull-right">تاریخ</label>
                              <input type="date" name="date" value="{{$paymentEdit->date}}" class="form-control">
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="row" style="margin-top: 20px;">
                      <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                        <div class="form-group fill">
                          <button class="btn btn-warning btn-sm" type="reset">انصراف</button>
                          <button class="btn btn-primary btn-sm marginx" type="submit"><span class="fa fa-save"></span> ذخیره</button>
                        </div>
                      </div>
                    </div>
                  </form>
                @endif
              </div>
            </div>
            
            <div class="col-sm-12">
              <div class="sparkline8-graph text-muted">
                <div class="row">
                  <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8"></div>
                  <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 hideOnPrint">
                    <div class="btn-group hideOnPrint" id="exportButton" style="float: left; ">
                      <div class="btn btn-sm btn-primary" style="float: left" onclick="printPage('washing-payment')"><i class="fa fa-print"></i> چاپ</div>
                    </div>
                    <a href="/dashboard/washing-payments-all/{{$team->id}}" style="float: left" class="btn btn-sm btn-info hideOnPrint">نمایش همه</a>
                  </div>
                </div>
                
                <div class="table-responsive">
                  <table class="table table-xs table-hover" id="washing_payment">
                    <thead>
                    <tr>
                      <td><b>رسید(دالر)</b></td>
                      <td><b>گرفت(دالر)</b></td>
                      <td><b>رسید(افغانی)</b></td>
                      <td><b>گرفت(افغانی)</b></td>
                      <td><b>شست نمبر</b></td>
                      <td><b>تفصیلات</b></td>
                      <td><b>تاریخ</b></td>
                      <td><b>حالت</b></td>
                      <td class="hideOnPrint text-center"><b>عملیات</b></td>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($payments as $pa)
                      <tr class="ur{{$pa->id}}">
                        <td>@if($pa->type == 'رسید' && $pa->amount > 0) {{$pa->amount}} @else 0 @endif</td>
                        <td>@if($pa->type == 'گرفت' && $pa->amount > 0) {{$pa->amount}} @else 0 @endif</td>
                        <td>@if($pa->type == 'رسید' && $pa->amount_af > 0) {{$pa->amount_af}} @else 0 @endif</td>
                        <td>@if($pa->type == 'گرفت' && $pa->amount_af > 0) {{$pa->amount_af}} @else 0 @endif</td>
                        <td>@if($pa->wash_number == 'نقد') نقد @else <a href="/dashboard/search-wash-numbersh-payment/{{$pa->wash_number}},{{$pa->team_id}}">{{$pa->wash_number}}</a> @endif</td>
                        <td>{{$pa->description}}</td>
                        <td>{{$pa->date}}</td>
                        <td class="hideOnPrint">@if($pa->status == 0) <label class="badge badge-warning">درخواست تایید نشده</label> @else <label class="badge badge-success">درخواست تایید شد</label> @endif</td>
                        @if( $pa->status == 0 || auth()->user()->role == 'SP')
                          <td class="hideOnPrint text-center">
                            <a href="/dashboard/washing-payments/{{$pa->id}}/edit" class="btn btn-sm btn-info">ویرایش</a>
                            <button onclick="deletePayment({{$pa->id}}, {{$pa->team_id}})" class="btn btn-danger btn-sm ">حذف</button>
                          </td>
                        @endif
                      </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="2">مجموع دالر: {{$credit_us - $debits_us}}</th>
                            <th colspan="2">مجموع افغانی: {{$credit_af - $debits_af}}</th>
                            <th colspan="5"></th>
                        </tr>
                    </tfoot>
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
  </div>
@endsection

@section('scripts')
  <script>
      $(document).ready(function() {
          $('#wash_number').select2();
          $('#override_debit_account_id').select2();
          $('#override_credit_account_id').select2();

          $("#washing_payment").tableExport({
              formats: ["xlsx"],
              bootstrap: true,
              exportButtons: false,
              position: "bottom"
          });
      });

      function deletePayment(id, team_id) {
          if(confirm("مطمعین هستید؟")) {
              $.ajax({
                  type: 'DELETE',
                  data: { '_token': '{{csrf_token()}}' },
                  url: '/dashboard/washing-payments/' + id,
                  success: function (res) {
                      if (res.status == 'success') {
                          window.location.reload();
                      }
                  }
              });
          }
      }
  </script>
@endsection
