@extends('dsh.master')
@section('title' , 'لیست فروشات')
@section('content')
<div class="container-fluid px-4 py-4">
    @php($lockDate = \DB::table('financial_settings')->where('key', 'financial_lock_date')->value('value'))

    <!-- Header & Search Section -->
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h3 class="mb-0 font-weight-bold text-dark"><i class="fa fa-shopping-cart text-primary mr-2"></i> مدیریت فروشات</h3>
            <p class="text-muted small mb-0">لیست تمامی قالین‌های فروخته شده و تحلیل مفاد</p>
        </div>
        <div class="col-md-6 text-right">
            <div class="d-flex justify-content-end align-items-center">
                <form action="/dashboard/search-carpet-from-sales" method="post" class="mr-2">
                    @csrf
                    <div class="input-group">
                        <input type="text" name="search" class="form-control form-control-sm border-0 shadow-sm px-3" 
                               placeholder="جستجوی نمبر قالین، انوایس یا مشتری..." style="border-radius: 20px; width: 250px;">
                        <div class="input-group-append">
                            <button class="btn btn-primary btn-sm px-3 shadow-sm" type="submit" style="border-radius: 0 20px 20px 0;">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </div>
                </form>
                <button class="btn btn-outline-primary btn-sm px-3 shadow-sm" onclick="printPage('salesTableCard')">
                    <i class="fa fa-print mr-1"></i> چاپ لیست
                </button>
            </div>
        </div>
    </div>

    <!-- Stats Summary Section -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-lg p-3 bg-gradient-primary text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="mb-0 opacity-7">تعداد فروشات</p>
                        <h4 class="mb-0 font-weight-bold">{{ \App\Sale::where('is_returned', 0)->count() }}</h4>
                    </div>
                    <i class="fa fa-shopping-bag fa-2x opacity-5"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-lg p-3 bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="mb-0 text-muted small text-uppercase font-weight-bold">مجموع سایز (متر مربع)</p>
                        <h4 class="mb-0 font-weight-bold text-dark">{{ round(\App\Sale::where('is_returned', 0)->join('carpets', 'sales.carpet_id', '=', 'carpets.carpet_id')->sum('carpets.area'), 2) }}</h4>
                    </div>
                    <i class="fa fa-expand fa-2x text-info opacity-2"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-lg p-3 bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="mb-0 text-muted small text-uppercase font-weight-bold">مجموع فروشات ($)</p>
                        <h4 class="mb-0 font-weight-bold text-success">{{ number_format(\App\Sale::where('is_returned', 0)->sum('sale_cost_total'), 2) }}</h4>
                    </div>
                    <i class="fa fa-money fa-2x text-success opacity-2"></i>
                </div>
            </div>
        </div>
        @if(auth()->user()->role == 'SP')
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-lg p-3 bg-white border-left-success" style="border-left: 4px solid #28a745 !important;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="mb-0 text-muted small text-uppercase font-weight-bold">مجموع مفاد خالص ($)</p>
                        <h4 class="mb-0 font-weight-bold text-dark">{{ number_format(\App\Sale::where('is_returned', 0)->sum('profit'), 2) }}</h4>
                    </div>
                    <i class="fa fa-line-chart fa-2x text-success opacity-2"></i>
                </div>
            </div>
        </div>
        @endif
    </div>

    @if(isset($sale) && $sale)
    @can('edit_sale')
    <!-- Edit Sale Modal-like Section -->
    <div class="card border-0 shadow-sm rounded-lg mb-4 border-top-primary" style="border-top: 4px solid #007bff !important;">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 font-weight-bold"><i class="fa fa-edit text-primary mr-2"></i> ویرایش اطلاعات فروش</h5>
        </div>
        <div class="card-body">
            <form action="/dashboard/sales/{{$sale->id}}" method="post">
                @method('PATCH')
                @csrf
                <input type="hidden" name="old_invoice" value="{{$sale->invoice_id}}">
                
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-12">
                      <div class="form-group fill">
                        <label class=""> نمبر انوایس</label>
                        <select name="invoice_id" id="invoice_id" required class="form-control select2">
                            @foreach($invoices as $invoice)
                                <option {{ ($sale->invoice_id == $invoice->id ? 'selected' : '') }} value="{{$invoice->id}}"
                                        customer_name="{{$invoice->customer->name}}"
                                        customer_code="{{$invoice->customer->customer_code}}"
                                        customer_company="{{$invoice->customer->company_name}}"
                                        customer_address="{{$invoice->customer->company_address}}">{{$invoice->invoice_no}}</option>
                            @endforeach
                        </select>
                      </div>
                    </div>

                    <div class="col-lg-4 col-md-4 col-sm-4 col-12">
                      <div class="form-group fill">
                        <label>نام مشتری</label>
                        <input type="text" name="customer_name" id="customer_name" value="{{$sale->customer->name ?? ''}}" class="form-control bg-light" readonly>
                      </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-4 col-12">
                      <div class="form-group fill">
                        <label>کود مشتری</label>
                        <input type="text" name="customer_code" id="customer_code" value="{{$sale->customer->customer_code ?? ''}}" class="form-control bg-light" readonly>
                      </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-4 col-12">
                      <div class="form-group fill">
                        <label class="login2 pull-right pull-right-pro">نام کمپنی</label>
                        <input type="text" name="company_name" id="company_name" value="{{$sale->customer->company_name ?? ''}}" class="form-control bg-light" readonly>
                      </div>
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-12">
                      <div class="form-group fill">
                        <label>ادرس کمپنی</label>
                        <input type="text" name="company_address" id="company_address" value="{{$sale->customer->company_address ?? ''}}" class="form-control bg-light" readonly>
                      </div>
                    </div>

                    <div class="col col-lg-4 col-md-4 col-sm-4 col-12">
                      <div class="form-group fill">
                        <label>نمبر قالین</label>
                        <input type="text" name="carpet_no" id="carpet_no" value="{{$sale->carpet->carpet_no}}" class="form-control bg-light" readonly>
                        <input type="hidden" name="carpet_id" id="carpet_id" value="{{$sale->carpet_id}}">
                      </div>
                    </div>
                    <div class="col col-lg-4 col-md-4 col-sm-4 col-12">
                      <div class="form-group fill">
                        <label>نوعیت قالین</label>
                        <input type="text" name="carpet_type" id="carpet_type" value="{{$sale->carpet->type->carpet_type ?? ''}}" class="form-control bg-light" readonly>
                      </div>
                    </div>
                    <div class="col col-lg-4 col-md-4 col-sm-4 col-12">
                      <div class="form-group fill">
                        <label>کوالتی</label>
                        <input type="text" name="carpet_quality" id="carpet_quality" value="{{$sale->carpet->quality->quality ?? ''}}" class="form-control bg-light" readonly>
                      </div>
                    </div>
                    <div class="col col-lg-4 col-md-4 col-sm-4 col-6">
                      <div class="form-group fill">
                        <label>طول قالین</label>
                        <input type="text" name="carpet_height" id="carpet_height" value="{{$sale->carpet->height}}" class="form-control bg-light" readonly>
                      </div>
                    </div>
            
                    <div class="col col-lg-4 col-md-4 col-sm-4 col-12">
                      <div class="form-group fill">
                        <label>عرض قالین</label>
                        <input type="text" name="carpet_width" id="carpet_width" value="{{$sale->carpet->width}}" class="form-control bg-light" readonly>
                      </div>
                    </div>
                    <div class="col col-lg-4 col-md-4 col-sm-4 col-12">
                      <div class="form-group fill">
                        <label>سایز قالین</label>
                        <input type="text" name="carpet_area" readonly id="carpet_area" value="{{$sale->carpet->area}}" class="form-control bg-light">
                      </div>
                    </div>
            
                    <div class="col col-lg-4 col-md-4 col-sm-4 col-12">
                      <div class="form-group fill">
                        <label class="font-weight-bold text-muted">قیمت تمام شد فی متر (COGS / m²)</label>
                        <input type="text" name="price_per_meter" id="price_per_meter" value="{{ $sale->carpet->area > 0 ? number_format($sale->carpet->total_price / $sale->carpet->area, 2) : 0 }}" class="form-control bg-light" readonly>
                      </div>
                    </div>
            
                    <div class="col col-lg-4 col-md-4 col-sm-4 col-12">
                      <div class="form-group fill">
                        <label class="font-weight-bold text-muted">مجموع قیمت تمام شد (COGS Total)</label>
                        <input type="hidden" name="total_price_cost" value="{{$sale->carpet->total_price}}">
                        <input type="text" id="total_price_cost_display" value="{{$sale->carpet->total_price}}" class="form-control bg-light font-weight-bold text-danger" readonly>
                      </div>
                    </div>
                    
                    <div class="col col-lg-4 col-md-4 col-sm-4 col-12"></div> <!-- Spacer -->

                    <div class="col col-lg-4 col-md-4 col-sm-4 col-12">
                      <div class="form-group fill">
                        <label class="font-weight-bold text-success">اسعار فروش (Currency)</label>
                        <select name="currency_id" id="sale_currency_id" class="form-control font-weight-bold border-success select2" style="border: 2px solid #28a745;" required>
                          @foreach($currencies as $curr)
                            <option value="{{ $curr->id }}" data-rate="{{ $curr->exchange_rate }}" data-code="{{ $curr->code }}" {{ $sale->currency_id == $curr->id ? 'selected' : ($curr->code == 'USD' && !$sale->currency_id ? 'selected' : '') }}>
                              {{ $curr->code }} ({{ $curr->symbol }}) - Rate: {{ $curr->exchange_rate }}
                            </option>
                          @endforeach
                        </select>
                      </div>
                    </div>
        
                    <div class="col col-lg-4 col-md-4 col-sm-4 col-12">
                      <div class="form-group fill">
                        <label class="font-weight-bold text-success">نرخ تبادله (Exchange Rate)</label>
                        <input type="number" step="any" name="exchange_rate" id="sale_exchange_rate" class="form-control border-success font-weight-bold" style="border: 2px solid #28a745;" value="{{$sale->exchange_rate ?? 1.0}}" required>
                      </div>
                    </div>
        
                    <div class="col col-lg-4 col-md-4 col-sm-4 col-12">
                      <div class="form-group fill">
                        <label class="font-weight-bold text-success">قیمت فروش فی متر (به اسعار انتخابی)</label>
                        <input type="number" step="any" required name="sale_cost_per_meter" id="sale_cost_per_meter" value="{{$sale->sale_cost_per_meter}}" class="form-control border-primary font-weight-bold">
                      </div>
                    </div>
        
                    <div class="col col-lg-4 col-md-4 col-sm-4 col-12">
                      <div class="form-group fill">
                        <label class="font-weight-bold text-primary">قیمت مجموع فروش (به اسعار انتخابی)</label>
                        <input type="text" name="sale_cost_total" id="sale_cost_total" value="{{$sale->sale_cost_total}}" class="form-control bg-light font-weight-bold text-primary" readonly>
                      </div>
                    </div>
        
                    <div class="col col-lg-4 col-md-4 col-sm-4 col-12">
                      <div class="form-group fill">
                        <label class="font-weight-bold text-danger">مجموع فروش به دالر (Total Sale USD)</label>
                        <input type="text" name="sale_cost_total_usd" id="sale_cost_total_usd" class="form-control bg-light font-weight-bold text-danger" readonly>
                        <div id="margin-warning" class="text-danger font-weight-bold mt-1" style="display:none; font-size: 0.82rem;">
                          <i class="fa fa-exclamation-triangle"></i> هشدار: قیمت فروش کمتر از قیمت تمام‌شد است! (Negative Margin)
                        </div>
                      </div>
                    </div>
                </div>

                <!-- ACCOUNT OVERRIDES (Edit) -->
                <div class="row mt-4 mb-3 border p-3 rounded-lg mx-0" style="background-color: #fdfdfe;">
                    <div class="col-12 mb-2">
                        <h6 class="small font-weight-bold text-dark border-bottom pb-1">
                            <i class="fa fa-university text-primary mr-1"></i> تنظیمات حسابی (Accounting Overrides)
                        </h6>
                    </div>
                    
                    <div class="col-md-3 form-group mb-2">
                        <label class="tiny font-weight-bold text-muted">حساب دریافتنی (Revenue Debit)</label>
                        <select name="override_debit_account_id" class="form-control form-control-sm select2">
                            @foreach($allowedRevenueDebit as $acc)
                                <option value="{{ $acc->id }}" {{ ($sale->override_debit_account_id == $acc->id || (!$sale->override_debit_account_id && $mappingRevenue && $mappingRevenue->debit_account_id == $acc->id)) ? 'selected' : '' }}>
                                    {{ $acc->account_code }} - {{ $acc->account_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 form-group mb-2">
                        <label class="tiny font-weight-bold text-muted">حساب عاید (Revenue Credit)</label>
                        <select name="override_credit_account_id" class="form-control form-control-sm select2">
                            @foreach($allowedRevenueCredit as $acc)
                                <option value="{{ $acc->id }}" {{ ($sale->override_credit_account_id == $acc->id || (!$sale->override_credit_account_id && $mappingRevenue && $mappingRevenue->credit_account_id == $acc->id)) ? 'selected' : '' }}>
                                    {{ $acc->account_code }} - {{ $acc->account_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 form-group mb-2">
                        <label class="tiny font-weight-bold text-muted">هزینه تمام شد (COGS Debit)</label>
                        <select name="override_cogs_debit_id" class="form-control form-control-sm select2">
                            @foreach($allowedCogsDebit as $acc)
                                <option value="{{ $acc->id }}" {{ ($sale->override_cogs_debit_id == $acc->id || (!$sale->override_cogs_debit_id && $mappingCogs && $mappingCogs->debit_account_id == $acc->id)) ? 'selected' : '' }}>
                                    {{ $acc->account_code }} - {{ $acc->account_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 form-group mb-2">
                        <label class="tiny font-weight-bold text-muted">حساب گدام (Inventory Credit)</label>
                        <select name="override_cogs_credit_id" class="form-control form-control-sm select2">
                            @foreach($allowedCogsCredit as $acc)
                                <option value="{{ $acc->id }}" {{ ($sale->override_cogs_credit_id == $acc->id || (!$sale->override_cogs_credit_id && $mappingCogs && $mappingCogs->credit_account_id == $acc->id)) ? 'selected' : '' }}>
                                    {{ $acc->account_code }} - {{ $acc->account_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="text-right">
                    <a href="/dashboard/sales" class="btn btn-light btn-sm px-4 mr-2">منصرف</a>
                    <button type="submit" class="btn btn-primary btn-sm px-4 shadow-sm font-weight-bold">بروزرسانی فروش</button>
                </div>
            </form>
        </div>
    </div>
    @endcan
    @endif

    <!-- Main Table Card -->
    <div class="card border-0 shadow-sm rounded-lg overflow-hidden" id="salesTableCard">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="min-width: 1200px;">
                    <thead class="bg-light">
                        <tr class="text-muted small text-uppercase">
                            <th class="border-0 px-4 py-3">نمبر انوایس</th>
                            <th class="border-0 py-3">مشتری</th>
                            <th class="border-0 py-3">مشخصات قالین</th>
                            <th class="border-0 py-3 text-center">ابعاد (m)</th>
                            <th class="border-0 py-3 text-center">مساحت (m²)</th>
                            <th class="border-0 py-3 text-center">قیمت فی متر</th>
                            <th class="border-0 py-3 text-center">مجموع فروش</th>
                            @if(auth()->user()->role == 'SP')
                            <th class="border-0 py-3 text-center">مفاد خالص</th>
                            @endif
                            <th class="border-0 px-4 py-3 text-right">عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sales as $sale)
                        <tr class="border-bottom {{ $sale->is_returned ? 'text-muted bg-light' : '' }}">
                            <td class="px-4 py-3">
                                @if($sale->is_returned)
                                    <del class="badge badge-soft-primary px-3 py-2 rounded-pill font-weight-bold">
                                        <i class="fa fa-file-text-o mr-1"></i> {{ $sale->invoice->invoice_no ?? '---' }}
                                    </del>
                                @else
                                    <span class="badge badge-soft-primary px-3 py-2 rounded-pill font-weight-bold">
                                        <i class="fa fa-file-text-o mr-1"></i> {{ $sale->invoice->invoice_no ?? '---' }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-xs bg-soft-info text-info rounded-circle mr-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                        {{ mb_substr($sale->customer->name ?? '?', 0, 1) }}
                                    </div>
                                    <div>
                                        <h6 class="mb-0 font-weight-bold small">
                                            @if($sale->is_returned)
                                                <del>{{ $sale->customer->name ?? '---' }}</del>
                                            @else
                                                {{ $sale->customer->name ?? '---' }}
                                            @endif
                                        </h6>
                                        <span class="text-muted tiny">{{ $sale->customer->customer_code ?? '---' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <h6 class="mb-0 font-weight-bold text-dark small">
                                    @if($sale->is_returned)
                                        <del>{{ $sale->carpet->carpet_no ?? '---' }}</del>
                                        <span class="badge badge-danger ml-1 font-weight-bold tiny px-2 py-1" style="display: inline-block;">
                                            <i class="fa fa-reply mr-1"></i> مرجوع شده
                                        </span>
                                    @else
                                        {{ $sale->carpet->carpet_no ?? '---' }}
                                    @endif
                                </h6>
                                <span class="badge badge-light tiny px-2 py-1">{{ $sale->type }} | {{ $sale->quality }}</span>
                            </td>
                            <td class="text-center small font-weight-bold text-muted">
                                @if($sale->is_returned)
                                    <del>{{ $sale->carpet_height ?? ($sale->carpet->height ?? '---') }} × {{ $sale->carpet_width ?? ($sale->carpet->width ?? '---') }}</del>
                                @else
                                    {{ $sale->carpet_height ?? ($sale->carpet->height ?? '---') }} × {{ $sale->carpet_width ?? ($sale->carpet->width ?? '---') }}
                                @endif
                            </td>
                            <td class="text-center font-weight-bold text-dark">
                                @if($sale->is_returned)
                                    <del>{{ round($sale->carpet_area ?? ($sale->carpet->area ?? 0), 2) }}</del>
                                @else
                                    {{ round($sale->carpet_area ?? ($sale->carpet->area ?? 0), 2) }}
                                @endif
                            </td>
                            <td class="text-center font-weight-bold text-info">
                                @if($sale->is_returned)
                                    <del>${{ number_format($sale->sale_cost_per_meter, 2) }}</del>
                                @else
                                    ${{ number_format($sale->sale_cost_per_meter, 2) }}
                                @endif
                            </td>
                            <td class="text-center">
                                @if($sale->is_returned)
                                    <del class="font-weight-bold text-success">${{ number_format($sale->sale_cost_total, 2) }}</del>
                                @else
                                    <span class="font-weight-bold text-success">${{ number_format($sale->sale_cost_total, 2) }}</span>
                                @endif
                            </td>
                            @if(auth()->user()->role == 'SP')
                            <td class="text-center">
                                @if($sale->is_returned)
                                    <del class="badge badge-soft-success px-3 py-1 font-weight-bold">${{ number_format($sale->profit, 2) }}</del>
                                @else
                                    <span class="badge {{ $sale->profit >= 0 ? 'badge-soft-success' : 'badge-soft-danger' }} px-3 py-1 font-weight-bold">
                                        ${{ number_format($sale->profit, 2) }}
                                    </span>
                                @endif
                            </td>
                            @endif
                            <td class="px-4 py-3 text-right">
                                <div class="dropdown">
                                    <button class="btn btn-light btn-xs shadow-none border-0 bg-transparent p-0" type="button" data-toggle="dropdown">
                                        <i class="fa fa-ellipsis-v text-muted"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right shadow-sm border-0 py-2" style="border-radius: 10px;">
                                        @if(!($sale->invoice && $sale->invoice->status === 'closed'))
                                        @can('edit_sale')
                                        <a class="dropdown-item py-2 px-3 small text-primary" href="/dashboard/sales/{{$sale->id}}/edit">
                                            <i class="fa fa-edit mr-2"></i> ویرایش فروش
                                        </a>
                                        @endcan
                                        @endif

                                        @if($sale->ledger_transaction_id)
                                        <a class="dropdown-item py-2 px-3 small" href="{{ route('accounting.journals.show', $sale->ledger_transaction_id) }}" target="_blank">
                                            <i class="fa fa-book text-success mr-2"></i> مشاهده در روزنامچه
                                        </a>
                                        @endif

                                        @if($sale->is_returned && $sale->return_ledger_transaction_id)
                                        <a class="dropdown-item py-2 px-3 small text-danger" href="{{ route('accounting.journals.show', $sale->return_ledger_transaction_id) }}" target="_blank">
                                            <i class="fa fa-reply text-danger mr-2"></i> سند برگشتی روزنامچه
                                        </a>
                                        @endif
                                        
                                        <div class="dropdown-divider border-light"></div>
                                        <a class="dropdown-item py-2 px-3 small" href="/dashboard/invoices/{{$sale->invoice_id}}">
                                            <i class="fa fa-file-pdf-o text-danger mr-2"></i> مشاهده انوایس
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="py-5 text-center">
                                <img src="/assets/img/empty-cart.png" alt="Empty" style="width: 80px; opacity: 0.5;">
                                <p class="mt-3 text-muted">هیچ فروشاتی یافت نشد.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white border-0 py-3">
            <div class="d-flex justify-content-between align-items-center">
                <span class="text-muted small">نمایش {{ $sales->firstItem() ?? 0 }} تا {{ $sales->lastItem() ?? 0 }} از {{ $sales->total() }} مورد</span>
                <div>
                    {{ $sales->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-gradient-primary { background: linear-gradient(135deg, #007bff 0%, #0056b3 100%); }
    .badge-soft-primary { background-color: rgba(0, 123, 255, 0.1); color: #007bff; }
    .badge-soft-success { background-color: rgba(40, 167, 69, 0.1); color: #28a745; }
    .badge-soft-danger { background-color: rgba(220, 53, 69, 0.1); color: #dc3545; }
    .badge-soft-info { background-color: rgba(23, 162, 184, 0.1); color: #17a2b8; }
    .avatar-xs { font-size: 14px; font-weight: bold; }
    .tiny { font-size: 10px; }
    .table td, .table th { vertical-align: middle; }
    .shadow-sm { box-shadow: 0 .125rem .25rem rgba(0,0,0,.075)!important; }
    .rounded-lg { border-radius: 0.75rem !important; }
    .dropdown-item:hover { background-color: #f8f9fa; }
    @media print {
        .hideOnPrint { display: none !important; }
        .card { border: none !important; shadow: none !important; }
        .container-fluid { padding: 0 !important; }
    }
</style>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('.select2').select2({
            width: '100%'
        });

        function updateEditFormLabels() {
            let customerName = $('#invoice_id option:selected').attr('customer_name');
            $('#display_customer_name').text(customerName || '---');
        }

        $('#invoice_id').on('change', updateEditFormLabels);
        updateEditFormLabels();

        function calculateSalePrices() {
            var carpet_area = parseFloat($('#carpet_area').val()) || 0;
            var sale_cost_per_meter = parseFloat($('#sale_cost_per_meter').val()) || 0;
            var exchange_rate = parseFloat($('#sale_exchange_rate').val()) || 1.0;
            var cost_total = parseFloat($('#total_price_cost').val()) || 0;
            
            if (carpet_area > 0 && sale_cost_per_meter > 0) {
                var total_cost = (sale_cost_per_meter * carpet_area).toFixed(2);
                $('#sale_cost_total').val(total_cost);
                
                // Total in USD
                var total_usd = (total_cost * (exchange_rate > 0 ? 1/exchange_rate : 1)).toFixed(2);
                if ($('#sale_currency_id').find(':selected').data('rate') == 1 || $('#sale_currency_id').find(':selected').text().includes('USD')) {
                    total_usd = (total_cost * exchange_rate).toFixed(2); // If they input differently, but typically rate is 1 for USD. Let's stick to exchange logic
                    // Wait, standard logic in Create form was: total_usd = (total_cost * exchange_rate) if exchange_rate was direct. 
                    // Let's use the exact create form logic:
                    total_usd = (total_cost * exchange_rate).toFixed(2);
                } else {
                    total_usd = (total_cost / exchange_rate).toFixed(2);
                }
                
                // Wait, standard Create form logic:
                // var total_usd = (total_cost * exchange_rate).toFixed(2);
                // Actually if AFN is 70, they should divide. The user's original logic in create form was: `(total_cost * exchange_rate).toFixed(2)`. Let's stick to the original if they had it that way, OR I can just use the controller's logic:
                // saleCostUsd = (currency == 'USD') ? total_cost : (total_cost / exchange_rate)
                var currencyCode = $('#sale_currency_id').find(':selected').text();
                if (currencyCode.includes('USD')) {
                    total_usd = total_cost;
                } else {
                    total_usd = (exchange_rate > 0 ? total_cost / exchange_rate : 0).toFixed(2);
                }
                
                $('#sale_cost_total_usd').val(total_usd);
                
                if (parseFloat(total_usd) < cost_total) {
                    $('#margin-warning').show();
                    $('#sale_cost_total_usd').addClass('is-invalid border-danger text-danger');
                } else {
                    $('#margin-warning').hide();
                    $('#sale_cost_total_usd').removeClass('is-invalid border-danger text-danger');
                }
            } else {
                $('#sale_cost_total').val('0.00');
                $('#sale_cost_total_usd').val('0.00');
                $('#margin-warning').hide();
                $('#sale_cost_total_usd').removeClass('is-invalid border-danger text-danger');
            }
        }

        $('#sale_currency_id').change(function () {
            var selected = $(this).find(':selected');
            var rate = parseFloat(selected.data('rate')) || 1.0;
            $('#sale_exchange_rate').val(rate);
            calculateSalePrices();
        });

        $('#sale_exchange_rate, #sale_cost_per_meter').on('input change keyup', function() {
            calculateSalePrices();
        });
        
        // Initial Calculation on load
        calculateSalePrices();

        $("#packing_id").change(function () {
            $.ajax({
                url: "{{ route('dashboard.package_list.get_by_packing') }}?packing_id=" + $(this).val(),
                method: 'GET',
                success: function (data) {
                    $('#package_id').html(data.html).trigger('change');
                }
            });
        });
    });
</script>
@endsection