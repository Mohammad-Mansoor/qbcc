@extends('dsh.master')
@section('content')
  <!-- navbar -->
  <div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <div class="card">
        <div class="card-header" style="background: linear-gradient(135deg, #065f46 0%, #059669 100%); color: white; border-radius: 10px 10px 0 0; padding: 1.5rem;">
          <div class="d-flex justify-content-between align-items-center">
            <h4 class="mb-0" style="color: white; font-weight: 700; letter-spacing: -0.025em;">
                <i class="fa fa-shopping-cart mr-2"></i>
                @if(!$saleEdit) فروش مواد (Raw Material Sale) @else ویرایش فروش مواد (Edit Sale) @endif
            </h4>
            <span class="badge badge-light shadow-sm" style="font-size: 0.9rem; padding: 0.5rem 1rem; border-radius: 20px;">
                <i class="fa fa-shield mr-1"></i> Forensic Hardening v2.0
            </span>
          </div>
        </div>
        <div class="card-body" style="background: #fcfdfd;">
          @if(session("status"))
            <div class="alert alert-success shadow-sm border-0" role="alert" style="border-right: 5px solid #059669 !important;">
                <i class="fa fa-check-circle mr-2"></i> {{session('status')}}
            </div>
          @endif
          @if(session("error"))
            <div class="alert alert-danger shadow-sm border-0" role="alert" style="border-right: 5px solid #dc2626 !important;">
                <i class="fa fa-exclamation-triangle mr-2"></i> {{session('error')}}
            </div>
          @endif
          <div class="all-form-element-inner">
            @if(!$saleEdit)
              <form action="/dashboard/material-sales" method="post">
                @csrf
                <div class="row align-items-end">
                  <div class="col-lg-2">
                    <div class="form-group mb-3">
                      <label class="font-weight-bold text-dark text-right d-block">فاکتور فروش</label>
                      <div class="input-group">
                        <div class="input-group-prepend"><span class="input-group-text bg-light border-left-0"><i class="fa fa-hashtag"></i></span></div>
                        <input type="text" name="sale_number" value="{{$SaleNo}}" class="form-control font-weight-bold" style="background: #f8fafc;">
                      </div>
                    </div>
                  </div>

                  <div class="col-lg-3">
                    <div class="form-group mb-3 text-right">
                      <label class="font-weight-bold text-dark">نام نماینده (Agent)</label>
                      <select name="agent_id" id="agent_id" required class="form-control select2">
                        <option value="">انتخاب نماینده</option>
                        @foreach ($agents as $ag)
                          <option value="{{$ag->agent_id}}">{{$ag->user->name}}</option>
                        @endforeach
                      </select>
                      <div id="agent-balance" class="mt-1 small font-weight-bold text-primary"></div>
                    </div>
                  </div>

                  <div class="col-lg-2">
                    <div class="form-group mb-3 text-right">
                      <label class="font-weight-bold text-dark">نوعیت مواد</label>
                      <select name="type_id" id="type_id" required class="form-control select2">
                         <option value="">انتخاب نوعیت</option>
                        @foreach ($material_types as $type)
                          <option value="{{$type->material_type_id}}">{{$type->material_type}}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>

                  <div class="col-lg-2">
                    <div class="form-group mb-3 text-right">
                      <label class="font-weight-bold text-dark">دسته بندی مواد</label>
                      <select name="category_id" id="category_id" required class="form-control select2">
                         <option value="">انتخاب دسته بندی</option>
                        @foreach ($categories as $category)
                          <option value="{{$category->material_category_id}}">{{$category->material_category}}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>

                  <div class="col-lg-3">
                    <div class="form-group mb-3 text-right">
                      <label class="font-weight-bold text-dark">گدام (Warehouse)</label>
                      <select name="warehouse_id" required class="form-control select2">
                        @foreach ($warehouses as $w)
                          <option value="{{$w->id}}">{{$w->name}}</option>
                        @endforeach
                      </select>
                      <div id="material-stock-info" class="mt-1 small font-weight-bold text-info"></div>
                    </div>
                  </div>
                </div>

                <div class="row mt-2 align-items-end">
                  <div class="col-lg-2">
                    <div class="form-group mb-3 text-right">
                      <label class="font-weight-bold text-dark">تاریخ (Date)</label>
                      <input type="date" name="date" required value="{{ date('Y-m-d') }}" class="form-control text-right">
                    </div>
                  </div>

                  <div class="col-lg-2">
                    <div class="form-group mb-3 text-right">
                      <label class="font-weight-bold text-dark text-success">مقدار مواد (Kg)</label>
                      <div class="input-group">
                        <input type="number" step="0.01" name="amount" required placeholder="0.00" class="form-control font-weight-bold border-success" id="material-amount" style="font-size: 1.1rem; text-align: center;">
                        <div class="input-group-append"><span class="input-group-text bg-success text-white">KG</span></div>
                      </div>
                    </div>
                  </div>

                  <div class="col-lg-2">
                    <div class="form-group mb-3 text-right">
                        <label class="font-weight-bold text-dark">واحد پولی (Currency)</label>
                        <select name="currency_id" id="currency_id" class="form-control font-weight-bold" style="border: 2px solid #059669;">
                            @foreach($currencies as $curr)
                                <option value="{{ $curr->id }}" 
                                        data-rate="{{ $curr->exchange_rate }}" 
                                        data-code="{{ $curr->code }}"
                                        {{ $curr->code == 'AFN' ? 'selected' : '' }}>
                                    {{ $curr->name }} ({{ $curr->code }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                  </div>

                  <div class="col-lg-2">
                    <div class="form-group mb-3 text-right">
                        <label class="font-weight-bold text-dark">نرخ تسطیح (Rate to USD)</label>
                        <input type="text" name="exchange_rate" id="exchange_rate" readonly class="form-control bg-light font-weight-bold text-center">
                    </div>
                  </div>

                  <div class="col-lg-2">
                    <div class="form-group mb-3 text-right">
                      <label class="font-weight-bold text-dark">قیمت فی کیلو (Unit Price)</label>
                      <input type="number" step="0.01" name="price" required placeholder="0.00" class="form-control font-weight-bold text-center" id="material-price">
                    </div>
                  </div>

                  <div class="col-lg-2">
                    <div class="form-group mb-3 text-right">
                      <label class="font-weight-bold text-primary">مجموع (Grand Total)</label>
                      <input type="text" name="original_amount" id="original_amount" readonly class="form-control font-weight-bold text-primary bg-light text-center" style="font-size: 1.1rem;">
                      <input type="hidden" name="total_price_af" id="material-af-total-price">
                      <input type="hidden" name="total_price" id="material-total-price">
                    </div>
                  </div>
                </div>

                <!-- LIVE TRUTH PREVIEW -->
                <div class="row mt-3 mb-4">
                    <div class="col-lg-12">
                        <div class="p-3 shadow-sm" style="background: linear-gradient(to right, #ecfdf5, #f0fdf4); border: 1px solid #10b981; border-radius: 12px;">
                            <div class="row align-items-center">
                                <div class="col-md-3 border-left text-center">
                                    <span class="text-muted small d-block uppercase font-weight-bold">Base Value (USD)</span>
                                    <h3 id="usd-truth-preview" class="mb-0 font-weight-bold text-success">$ 0.00</h3>
                                </div>
                                <div class="col-md-3 border-left text-center">
                                    <span class="text-muted small d-block uppercase font-weight-bold">Est. COGS (USD)</span>
                                    <h4 id="cogs-truth-preview" class="mb-0 font-weight-bold text-muted">$ 0.00</h4>
                                </div>
                                <div class="col-md-3 border-left text-center">
                                    <span class="text-muted small d-block uppercase font-weight-bold">Est. Profit (USD)</span>
                                    <h4 id="profit-truth-preview" class="mb-0 font-weight-bold text-primary">$ 0.00</h4>
                                </div>
                                <div class="col-md-3 text-center">
                                    <div id="margin-badge" class="badge badge-success p-2" style="font-size: 1rem; border-radius: 8px;">Margin: 0%</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <hr>
                <!-- ACCOUNT OVERRIDES -->
                <div class="row mt-3 p-3" style="background: #f0f7ff; border: 1px solid #cce5ff; border-radius: 8px;">
                    <div class="col-lg-12">
                        <h6 class="mb-3 text-primary"><i class="fa fa-university"></i> تنظیمات حسابی (Material Sale Accounting)</h6>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label class="pull-right text-muted">حساب دریافتنی (Debit) <span class="badge badge-info">{{ count($allowedDebitAccounts) }}</span></label>
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
                            <label class="pull-right text-muted">حساب فروش مواد (Credit) <span class="badge badge-info">{{ count($allowedCreditAccounts) }}</span></label>
                            <select name="override_credit_account_id" id="override_credit_account_id" class="form-control select2">
                                @foreach($allowedCreditAccounts as $acc)
                                    <option value="{{ $acc->id }}" {{ ($mapping && $mapping->credit_account_id == $acc->id) ? 'selected' : '' }}>
                                        {{ $acc->account_code }} - {{ $acc->account_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label class="pull-right text-muted">حساب مصرف (COGS Debit) <span class="badge badge-info">{{ count($allowedCogsDebit) }}</span></label>
                            <select name="override_cogs_debit_id" id="override_cogs_debit_id" class="form-control select2">
                                @foreach($allowedCogsDebit as $acc)
                                    <option value="{{ $acc->id }}">
                                        {{ $acc->account_code }} - {{ $acc->account_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label class="pull-right text-muted">حساب گدام (COGS Credit) <span class="badge badge-info">{{ count($allowedCogsCredit) }}</span></label>
                            <select name="override_cogs_credit_id" id="override_cogs_credit_id" class="form-control select2">
                                @foreach($allowedCogsCredit as $acc)
                                    <option value="{{ $acc->id }}">
                                        {{ $acc->account_code }} - {{ $acc->account_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <button class="btn btn-white" type="reset">انصراف</button>
                      <button class="btn btn-primary marginx" type="submit"><span
                                class="fa fa-save"></span> ذخیره
                      </button>
                    </div>
                  </div>
                </div>
              </form>
            @else
              <form action="/dashboard/material-sales/{{$saleEdit->id}}" method="post">
                @csrf
                @method('PUT')
                <input type="hidden" name="old_agent_id" value="{{$saleEdit->agent_id}}">
                
                <div class="row align-items-end">
                  <div class="col-lg-2">
                    <div class="form-group mb-3">
                      <label class="font-weight-bold text-dark text-right d-block">فاکتور فروش</label>
                      <input type="text" class="form-control font-weight-bold bg-light" value="{{$saleEdit->sale_number}}" name="sale_number">
                    </div>
                  </div>

                  <div class="col-lg-3">
                    <div class="form-group mb-3 text-right">
                      <label class="font-weight-bold text-dark">نام نماینده (Agent)</label>
                      <select name="agent_id" id="agent_id" required class="form-control select2">
                        @foreach ($agents as $ag)
                          <option {{($ag->agent_id == $saleEdit->agent_id ? 'selected' : '')}} value="{{$ag->agent_id}}">{{$ag->user->name}}</option>
                        @endforeach
                      </select>
                      <div id="agent-balance" class="mt-1 small font-weight-bold text-primary"></div>
                    </div>
                  </div>

                  <div class="col-lg-2">
                    <div class="form-group mb-3 text-right">
                      <label class="font-weight-bold text-dark">نوعیت مواد</label>
                      <select name="type_id" id="type_id" required class="form-control select2">
                        @foreach ($material_types as $type)
                          <option {{($saleEdit->type_id == $type->material_type_id ? 'selected' : '')}} value="{{$type->material_type_id}}">{{$type->material_type}}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>

                  <div class="col-lg-2">
                    <div class="form-group mb-3 text-right">
                      <label class="font-weight-bold text-dark">دسته بندی مواد</label>
                      <select name="category_id" id="category_id" required class="form-control select2">
                        @foreach ($categories as $category)
                          <option {{($saleEdit->category_id == $category->material_category_id ? 'selected' : '')}} value="{{$category->material_category_id}}">{{$category->material_category}}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>

                  <div class="col-lg-3">
                    <div class="form-group mb-3 text-right">
                      <label class="font-weight-bold text-dark">گدام (Warehouse)</label>
                      <select name="warehouse_id" required class="form-control select2">
                        @foreach ($warehouses as $w)
                          <option value="{{$w->id}}" {{ $saleEdit->warehouse_id == $w->id ? 'selected' : '' }}>{{$w->name}}</option>
                        @endforeach
                      </select>
                      <div id="material-stock-info" class="mt-1 small font-weight-bold text-info"></div>
                    </div>
                  </div>
                </div>

                <div class="row mt-2 align-items-end">
                  <div class="col-lg-2">
                    <div class="form-group mb-3 text-right">
                      <label class="font-weight-bold text-dark">تاریخ (Date)</label>
                      <input type="date" name="date" value="{{$saleEdit->date}}" required class="form-control text-right">
                    </div>
                  </div>

                  <div class="col-lg-2">
                    <div class="form-group mb-3 text-right">
                      <label class="font-weight-bold text-dark text-success">مقدار مواد (Kg)</label>
                      <div class="input-group">
                        <input type="number" step="0.01" name="amount" required value="{{$saleEdit->amount}}" class="form-control font-weight-bold border-success text-center" id="material-amount" style="font-size: 1.1rem;">
                        <div class="input-group-append"><span class="input-group-text bg-success text-white">KG</span></div>
                      </div>
                    </div>
                  </div>

                  <div class="col-lg-2">
                    <div class="form-group mb-3 text-right">
                        <label class="font-weight-bold text-dark">واحد پولی (Currency)</label>
                        <select name="currency_id" id="currency_id" class="form-control font-weight-bold" style="border: 2px solid #059669;">
                            @foreach($currencies as $curr)
                                <option value="{{ $curr->id }}" 
                                        data-rate="{{ $curr->exchange_rate }}" 
                                        data-code="{{ $curr->code }}"
                                        {{ $saleEdit->currency_id == $curr->id ? 'selected' : '' }}>
                                    {{ $curr->name }} ({{ $curr->code }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                  </div>

                  <div class="col-lg-2">
                    <div class="form-group mb-3 text-right">
                        <label class="font-weight-bold text-dark">نرخ تسطیح (Rate to USD)</label>
                        <input type="text" name="exchange_rate" id="exchange_rate" value="{{$saleEdit->exchange_rate}}" readonly class="form-control bg-light font-weight-bold text-center">
                    </div>
                  </div>

                  <div class="col-lg-2">
                    <div class="form-group mb-3 text-right">
                      <label class="font-weight-bold text-dark">قیمت فی کیلو (Unit Price)</label>
                      <input type="number" step="0.01" name="price" value="{{$saleEdit->price}}" required class="form-control font-weight-bold text-center" id="material-price">
                    </div>
                  </div>

                  <div class="col-lg-2">
                    <div class="form-group mb-3 text-right">
                      <label class="font-weight-bold text-primary">مجموع (Grand Total)</label>
                      <input type="text" name="original_amount" id="original_amount" value="{{$saleEdit->original_amount}}" readonly class="form-control font-weight-bold text-primary bg-light text-center" style="font-size: 1.1rem;">
                      <input type="hidden" name="total_price_af" id="material-af-total-price" value="{{$saleEdit->total_price_af}}">
                      <input type="hidden" name="total_price" id="material-total-price" value="{{$saleEdit->total_price}}">
                    </div>
                  </div>
                </div>

                <!-- LIVE TRUTH PREVIEW (EDIT) -->
                <div class="row mt-3 mb-4">
                    <div class="col-lg-12">
                        <div class="p-3 shadow-sm" style="background: linear-gradient(to right, #f8fafc, #f1f5f9); border: 1px solid #94a3b8; border-radius: 12px;">
                            <div class="row align-items-center">
                                <div class="col-md-3 border-left text-center">
                                    <span class="text-muted small d-block uppercase font-weight-bold">Base Value (USD)</span>
                                    <h3 id="usd-truth-preview" class="mb-0 font-weight-bold text-success">$ 0.00</h3>
                                </div>
                                <div class="col-md-3 border-left text-center">
                                    <span class="text-muted small d-block uppercase font-weight-bold">Est. COGS (USD)</span>
                                    <h4 id="cogs-truth-preview" class="mb-0 font-weight-bold text-muted">$ 0.00</h4>
                                </div>
                                <div class="col-md-3 border-left text-center">
                                    <span class="text-muted small d-block uppercase font-weight-bold">Est. Profit (USD)</span>
                                    <h4 id="profit-truth-preview" class="mb-0 font-weight-bold text-primary">$ 0.00</h4>
                                </div>
                                <div class="col-md-3 text-center">
                                    <div id="margin-badge" class="badge badge-secondary p-2" style="font-size: 1rem; border-radius: 8px;">Margin: 0%</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <hr>
                <!-- ACCOUNT OVERRIDES EDIT -->
                <div class="row mt-3 p-3" style="background: #f0f7ff; border: 1px solid #cce5ff; border-radius: 8px;">
                    <div class="col-lg-12">
                        <h6 class="mb-3 text-primary"><i class="fa fa-university"></i> تنظیمات حسابی (Accounting Override Edit)</h6>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label class="pull-right text-muted small">حساب دریافتنی (Debit)</label>
                            <select name="override_debit_account_id" class="form-control select2">
                                @foreach($allowedDebitAccounts as $acc)
                                    <option value="{{ $acc->id }}" {{ ($saleEdit->override_debit_account_id == $acc->id) ? 'selected' : '' }}>
                                        {{ $acc->account_code }} - {{ $acc->account_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label class="pull-right text-muted small">حساب فروش (Credit)</label>
                            <select name="override_credit_account_id" class="form-control select2">
                                @foreach($allowedCreditAccounts as $acc)
                                    <option value="{{ $acc->id }}" {{ ($saleEdit->override_credit_account_id == $acc->id) ? 'selected' : '' }}>
                                        {{ $acc->account_code }} - {{ $acc->account_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label class="pull-right text-muted small">حساب مصرف (COGS Debit)</label>
                            <select name="override_cogs_debit_id" class="form-control select2">
                                @foreach($allowedCogsDebit as $acc)
                                    <option value="{{ $acc->id }}" {{ ($saleEdit->override_cogs_debit_id == $acc->id) ? 'selected' : '' }}>
                                        {{ $acc->account_code }} - {{ $acc->account_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label class="pull-right text-muted small">حساب گدام (COGS Credit)</label>
                            <select name="override_cogs_credit_id" class="form-control select2">
                                @foreach($allowedCogsCredit as $acc)
                                    <option value="{{ $acc->id }}" {{ ($saleEdit->override_cogs_credit_id == $acc->id) ? 'selected' : '' }}>
                                        {{ $acc->account_code }} - {{ $acc->account_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                  <div class="col-lg-12 text-right">
                      <a href="/dashboard/material-sales" class="btn btn-light px-4 mr-2">انصراف (Cancel)</a>
                      <button class="btn btn-primary px-5 shadow-sm" type="submit">
                          <i class="fa fa-save mr-1"></i> بروزرسانی (Update Sale)
                      </button>
                  </div>
                </div>
              </form>
            @endif
          </div>
        </div>
      </div>

      <div class="card shadow-sm border-0 mt-4">
        <div class="card-header bg-white py-3">
          <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-dark font-weight-bold">
                <i class="fa fa-list-alt mr-2 text-success"></i> لیست فروشات مواد (Sales Ledger)
            </h5>
            <button class="btn btn-sm btn-outline-secondary hideOnPrint" onclick="window.print()">
                <i class="fa fa-print mr-1"></i> چاپ (Print)
            </button>
          </div>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive" id="sales">
            <table class="table table-hover mb-0">
              <thead class="bg-light">
                <tr>
                  <th class="border-top-0">فاکتور</th>
                  <th class="border-top-0">نماینده</th>
                  <th class="border-top-0">مقدار</th>
                  <th class="border-top-0">واحد پولی</th>
                  <th class="border-top-0 text-center">نرخ (Rate)</th>
                  <th class="border-top-0 text-center">قیمت مجموع</th>
                  <th class="border-top-0 text-center text-success">معادل (USD)</th>
                  <th class="border-top-0">کتگوری</th>
                  <th class="border-top-0">تاریخ</th>
                  <th class="border-top-0">حالت</th>
                  <th class="border-top-0 hideOnPrint">عملیات</th>
                </tr>
              </thead>
              <tbody>
                @forelse ($material_sales as $material)
                  <tr>
                    <td class="font-weight-bold text-primary">{{$material->sale_number}}</td>
                    <td>{{$material->agent->user->name}}</td>
                    <td class="font-weight-bold text-success">{{$material->amount}} kg</td>
                    <td><span class="badge badge-info">{{$material->currency_code}}</span></td>
                    <td class="small text-center">{{ number_format($material->exchange_rate, 6) }}</td>
                    <td class="font-weight-bold text-center">{{ number_format($material->original_amount, 2) }}</td>
                    <td class="font-weight-bold text-center text-success">${{ number_format($material->base_currency_amount, 2) }}</td>
                    <td>{{$material->category->material_category}}</td>
                    <td class="small">{{$material->date}}</td>
                    <td>
                        @if($material->status == 0)
                          <span class="badge badge-warning font-weight-normal"><i class="fa fa-clock-o mr-1"></i> در انتظار تایید</span>
                        @else
                          <span class="badge badge-success font-weight-normal"><i class="fa fa-check mr-1"></i> تایید شده</span>
                        @endif
                    </td>
                    <td class="hideOnPrint">
                        <a class="btn btn-sm btn-outline-emerald py-0 px-2" href="/dashboard/material-sales/{{$material->id}}/edit">
                            <i class="fa fa-edit"></i>
                        </a>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="11" class="text-center py-5">
                      <div class="text-muted"><i class="fa fa-info-circle mr-1"></i> هنوز فروش ثبت نشده است</div>
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    
    
    </div>
  </div>
@endsection


@section('scripts')
  <style>
      .btn-outline-emerald {
          color: #059669;
          border-color: #059669;
      }
      .btn-outline-emerald:hover {
          background-color: #059669;
          color: white;
      }
      .select2-container--default .select2-selection--single {
          height: 38px;
          border: 1px solid #ced4da;
          border-radius: 4px;
      }
      .select2-container--default .select2-selection--single .select2-selection__rendered {
          line-height: 36px;
          padding-right: 12px;
          text-align: right;
      }
      .select2-container--default .select2-selection--single .select2-selection__arrow {
          height: 36px;
      }
  </style>

  <script>
      $(document).ready(function() {
          var currentWAC = 0;
          var afnRate = 1; // Default fallback

          $('.select2').select2({
              width: '100%',
              dir: 'rtl'
          });

          // Currency Handling
          function updateRate() {
              var selected = $('#currency_id').find(':selected');
              var rate = selected.data('rate');
              $('#exchange_rate').val(parseFloat(rate).toFixed(8));
              calculateTruth();
          }

          $('#currency_id').on('change', updateRate);
          updateRate(); // Initial run

          // Fetch WAC and Agent Balance
          $('#agent_id, #category_id, #type_id, [name="warehouse_id"]').on('change', fetchContext);

          function fetchContext() {
              var agentId = $('#agent_id').val();
              var catId = $('#category_id').val();
              var typeId = $('#type_id').val();
              var whId = $('[name="warehouse_id"]').val();

              if (agentId || (catId && typeId)) {
                  $.ajax({
                      url: "{{ route('dashboard.material-sales-info') }}",
                      data: { agent_id: agentId, category_id: catId, type_id: typeId, warehouse_id: whId },
                      success: function(res) {
                          if (res.balance !== undefined) {
                              $('#agent-balance').html('Balance: ' + parseFloat(res.balance).toLocaleString() + ' AFN');
                          }
                          if (res.wac !== undefined) {
                              currentWAC = parseFloat(res.wac);
                              // Note: WAC is in USD base
                              calculateTruth();
                          }
                          if (res.available_stock !== undefined) {
                              $('#material-stock-info').html('Available: ' + parseFloat(res.available_stock).toLocaleString() + ' Kg');
                          }
                      }
                  });
              }
          }

          // Fetch AFN rate for legacy fields
          var afnOption = $('#currency_id option[data-code="AFN"]');
          if (afnOption.length) afnRate = parseFloat(afnOption.data('rate'));

          function calculateTruth() {
              var qty = parseFloat($('#material-amount').val()) || 0;
              var price = parseFloat($('#material-price').val()) || 0;
              var rate = parseFloat($('#exchange_rate').val()) || 0;
              
              var totalOriginal = qty * price;
              var totalUsd = totalOriginal * rate;
              
              $('#original_amount').val(totalOriginal.toLocaleString(undefined, {minimumFractionDigits: 2}));
              $('#usd-truth-preview').text('$ ' + totalUsd.toLocaleString(undefined, {minimumFractionDigits: 2}));

              // Legacy compatibility
              $('#material-af-total-price').val((totalUsd / afnRate).toFixed(2));
              $('#material-total-price').val(totalUsd.toFixed(2));

              // COGS and Profit
              var estCogsUsd = qty * currentWAC;
              var estProfitUsd = totalUsd - estCogsUsd;
              var margin = totalUsd > 0 ? (estProfitUsd / totalUsd) * 100 : 0;

              $('#cogs-truth-preview').text('$ ' + estCogsUsd.toLocaleString(undefined, {minimumFractionDigits: 2}));
              $('#profit-truth-preview').text('$ ' + estProfitUsd.toLocaleString(undefined, {minimumFractionDigits: 2}));
              
              $('#profit-truth-preview').removeClass('text-success text-danger text-primary');
              if (estProfitUsd > 0) $('#profit-truth-preview').addClass('text-success');
              else if (estProfitUsd < 0) $('#profit-truth-preview').addClass('text-danger');
              else $('#profit-truth-preview').addClass('text-primary');

              $('#margin-badge').text('Margin: ' + margin.toFixed(1) + '%');
              $('#margin-badge').removeClass('badge-success badge-warning badge-danger');
              if (margin > 15) $('#margin-badge').addClass('badge-success');
              else if (margin > 0) $('#margin-badge').addClass('badge-warning');
              else $('#margin-badge').addClass('badge-danger');
          }

          $('#material-amount, #material-price').on('input blur', calculateTruth);

          // Initial context load
          fetchContext();
      });
  </script>
@endsection