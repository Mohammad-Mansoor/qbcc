@extends('dsh.master')
@section('content')
  <br>
  <div class="row" id="customers">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="form1">
      <div class="card hideOnPrint mb-4">
        <div class="card-header">
          <h5>{{ !$customerEdit ? 'ایجاد مشتری جدید' : 'ویرایش مشتری' }}</h5>
        </div>
        <div class="card-body">
          <div class="all-form-element-inner">
            <form method="post" action="{{ !$customerEdit ? '/dashboard/customers' : '/dashboard/customers/'.$customerEdit->id }}">
              @if($customerEdit) @method('PATCH') @endif
              @csrf
              <div class="row">
                <div class="col-md-2">
                  <div class="form-group">
                    <label>کود مشتری</label>
                    <input type="text" value="{{ $customerEdit ? $customerEdit->customer_code : old('customer_code') }}" class="form-control form-control-sm" required name="customer_code">
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <label>نام مشتری</label>
                    <input type="text" class="form-control form-control-sm" value="{{ $customerEdit ? $customerEdit->name : old('name') }}" required name="name">
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <label>کمپنی</label>
                    <input type="text" value="{{ $customerEdit ? $customerEdit->company_name : old('company_name') }}" class="form-control form-control-sm" required name="company_name">
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <label>نمبر مبایل</label>
                    <input type="text" value="{{ $customerEdit ? $customerEdit->phone : old('phone') }}" class="form-control form-control-sm" required name="phone">
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <label>نوعیت</label>
                    <select name="type" class="form-control form-control-sm">
                      <option value="مشتری قالین" {{ ($customerEdit && $customerEdit->type == 'مشتری قالین') ? 'selected' : '' }}>مشتری قالین</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-2">
                    <label>&nbsp;</label>
                    <button class="btn btn-sm btn-primary btn-block shadow-sm" type="submit"><i class="fa fa-save"></i> {{ !$customerEdit ? 'ثبت مشتری' : 'بروزرسانی' }}</button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>

      <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-info text-white shadow-sm border-0">
                <div class="card-body p-3">
                    <h6 class="text-white-50 mb-1">کل طلبات از مشتریان (General Ledger)</h6>
                    <h2 class="mb-0 font-weight-bold">{{ number_format($total_receivable ?? 0, 0) }} AFN</h2>
                </div>
            </div>
        </div>
      </div>

      <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-bottom py-3">
          <div class="row align-items-center">
            <div class="col-md-6">
                <h5 class="mb-0">لیست و گزارش مالی مشتریان</h5>
            </div>
            <div class="col-md-6 text-left">
                <form action="/dashboard/customers/search" method="post" class="d-inline-block w-50">
                    @csrf
                    <div class="input-group">
                        <input type="text" name="search" placeholder="جستجو..." class="form-control form-control-sm">
                        <div class="input-group-append">
                            <button class="btn btn-sm btn-secondary" type="submit"><i class="fa fa-search"></i></button>
                        </div>
                    </div>
                </form>
                <button class="btn btn-sm btn-outline-primary ml-2" onclick="printPage('customers')"><i class="fa fa-print"></i> چاپ</button>
            </div>
          </div>
        </div>

        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover table-xs" id="customer_list">
              <thead class="bg-light">
              <tr>
                <th>کود</th>
                <th>نام و کمپنی</th>
                <th>ارزش کل تجارت (USD)</th>
                <th>بیلانس حسابی (AFN)</th>
                <th>باقیات نقدی (USD)</th>
                <th>باقیات نقدی (AFN)</th>
                <th>آخرین فعالیت</th>
                <th class="hideOnPrint text-center">عملیات</th>
              </tr>
              </thead>
              <tbody>
              @foreach($customers as $cust)
                <tr>
                  <td><span class="badge badge-light border font-weight-normal">{{ $cust->customer_code }}</span></td>
                  <td>
                    <strong>{{ $cust->name }}</strong><br>
                    <small class="text-muted">{{ $cust->company_name }}</small>
                  </td>
                  
                  <td dir="ltr" class="text-primary font-weight-bold">
                    ${{ number_format($cust->lifetime_sales ?? 0, 0) }}
                  </td>

                  <td dir="ltr" class="{{ ($cust->ledger_balance ?? 0) > 0 ? 'text-success font-weight-bold' : (($cust->ledger_balance ?? 0) < 0 ? 'text-danger font-weight-bold' : '') }}">
                    {{ number_format($cust->ledger_balance ?? 0, 0) }}
                  </td>

                  @php($total_af = 0)
                  @php($total_usd = 0)
                  <?php
                    $total_af = \Illuminate\Support\Facades\DB::table('customer_payments')->where('customer_id', $cust->id)->where('type', 'رسید')->sum('amount_af') - \Illuminate\Support\Facades\DB::table('customer_payments')->where('customer_id', $cust->id)->where('type', 'گرفت')->sum('amount_af');
                    $total_usd = \Illuminate\Support\Facades\DB::table('customer_payments')->where('customer_id', $cust->id)->where('type', 'رسید')->sum('amount') - \Illuminate\Support\Facades\DB::table('customer_payments')->where('customer_id', $cust->id)->where('type', 'گرفت')->sum('amount');
                  ?>

                  <td dir="ltr" class="{{ $total_usd > 0 ? 'text-success' : ($total_usd < 0 ? 'text-danger' : '') }}">
                    {{ number_format($total_usd, 0) }}
                  </td>

                  <td dir="ltr" class="{{ $total_af > 0 ? 'text-success' : ($total_af < 0 ? 'text-danger' : '') }}">
                    {{ number_format($total_af, 0) }}
                  </td>

                  <td>
                    @if($cust->last_activity)
                      <small class="{{ ($cust->days_since_active ?? 0) > 60 ? 'text-danger' : 'text-muted' }}">
                        {{ $cust->last_activity }}<br>
                        ({{ $cust->days_since_active }} روز قبل)
                      </small>
                    @else
                      <small class="text-muted">فعالیتی ثبت نشده</small>
                    @endif
                  </td>

                  <td class="hideOnPrint text-center">
                    <div class="btn-group">
                        <a href="/dashboard/customers/{{$cust->id}}/edit" class="btn btn-xs btn-primary shadow-sm" title="ویرایش">
                            <i class="fa fa-pencil"></i>
                        </a>
                        <a href="/dashboard/customer-payments/{{$cust->id}}" class="btn btn-xs btn-info shadow-sm" title="حساب و پرداخت">
                            <i class="fa fa-money"></i>
                        </a>
                    </div>
                  </td>
                </tr>
              @endforeach
              </tbody>
            </table>
            <div class="mt-3">
                {{ $customers->links() }}
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
          // Additional scripts if needed
      });
  </script>
@endsection
