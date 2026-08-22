@extends('dsh.master')
@section('content')
  <!-- navbar -->
  <div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <div class="card">
        <div class="card-header"
          style="background: linear-gradient(135deg, #065f46 0%, #059669 100%); color: white; border-radius: 10px 10px 0 0; padding: 1.5rem;">
          <div class="d-flex justify-content-between align-items-center">
            <h4 class="mb-0" style="color: white; font-weight: 700; letter-spacing: -0.025em;">
              <i class="fa fa-shopping-cart mr-2"></i>
              @if(!$saleEdit) فروش مواد (Raw Material Sale) @else ویرایش فروش مواد (Edit Sale) @endif
            </h4>
            <span class="badge badge-light shadow-sm"
              style="font-size: 0.9rem; padding: 0.5rem 1rem; border-radius: 20px;">
              <i class="fa fa-shield mr-1"></i> Forensic Hardening v2.0
            </span>
          </div>
        </div>
        <div class="card-body" style="background: #fcfdfd;">
          @if(session("status"))
            <div class="alert alert-success shadow-sm border-0" role="alert"
              style="border-right: 5px solid #059669 !important;">
              <i class="fa fa-check-circle mr-2"></i> {{session('status')}}
            </div>
          @endif
          @if(session("error"))
            <div class="alert alert-danger shadow-sm border-0" role="alert"
              style="border-right: 5px solid #dc2626 !important;">
              <i class="fa fa-exclamation-triangle mr-2"></i> {{session('error')}}
            </div>
          @endif
          <div class="all-form-element-inner">
            <!-- Modal Trigger / Action Panel -->
            <div class="d-flex justify-content-between align-items-center p-3 mb-3 hideOnPrint"
              style="background: rgba(255,255,255,0.8); backdrop-filter: blur(10px); border-radius: 12px; border: 1px solid rgba(0,0,0,0.05); box-shadow: 0 4px 6px rgba(0,0,0,0.02); direction: rtl;">
              <div>
                <h5 class="mb-0 text-dark font-weight-bold">ثبت معامله فروش جدید</h5>
                <small class="text-muted">جهت ثبت فاکتور فروش مواد خام (تار یا رنگ) کلیک کنید</small>
              </div>
              @can('create_material_sale')
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#saleModal"
                  style="background: linear-gradient(135deg, #059669 0%, #047857 100%); border: none; box-shadow: 0 4px 12px rgba(5,150,105,0.3); border-radius: 8px; padding: 10px 20px; font-weight: 600;">
                  <i class="fa fa-plus-circle mr-2"></i> ثبت فروش جدید
                </button>
              @endcan
            </div>

            <!-- Modal -->
            <div class="modal fade" id="saleModal" tabindex="-1" role="dialog" aria-labelledby="saleModalLabel"
              aria-hidden="true" style="direction: rtl;">
              <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content"
                  style="background: rgba(255, 255, 255, 0.98); backdrop-filter: blur(12px); border-radius: 16px; border: 1px solid rgba(255, 255, 255, 0.25); box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);">
                  <div class="modal-header"
                    style="background: linear-gradient(135deg, #065f46 0%, #059669 100%); border-radius: 16px 16px 0 0; padding: 16px 24px;">
                    <h5 class="modal-title text-white font-weight-bold" id="saleModalLabel">
                      <i class="fa fa-shopping-cart mr-2"></i>
                      @if(!$saleEdit) ثبت فروش مواد خام جدید @else ویرایش فروش مواد خام @endif
                    </h5>
                    <button type="button" class="close text-white ml-0 mr-auto" data-dismiss="modal" aria-label="Close"
                      style="opacity: 0.8; margin-left: 0 !important; margin-right: auto !important;">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <form
                    action="{{ !$saleEdit ? '/dashboard/material-sales' : '/dashboard/material-sales/' . $saleEdit->id }}"
                    method="post">
                    @csrf
                    @if($saleEdit)
                      @method('PUT')
                      <input type="hidden" name="old_agent_id" value="{{$saleEdit->agent_id}}">
                    @endif

                    <div class="modal-body p-4 text-right">

                      {{-- Subtype selection --}}
                      <div class="row mb-3">
                        <div class="col-lg-4">
                          <div class="form-group mb-3">
                            <label class="font-weight-bold text-dark">نوعیت مواد جهت فروش (Dye or Yarn)</label>
                            <select id="sale_subtype" name="subtype" class="form-control font-weight-bold"
                              style="border: 2px solid #059669; height: 38px;">
                              <option value="yarn" {{ ($saleEdit && optional($saleEdit->type)->subtype != 'dye') ? 'selected' : '' }}>تار / مواد خام (Yarn)</option>
                              <option value="dye" {{ ($saleEdit && optional($saleEdit->type)->subtype == 'dye') ? 'selected' : '' }}>رنگ (Dye)</option>
                            </select>
                            <small class="text-muted d-block mt-1">با تغییر این گزینه، کتگوری‌ها، انواع مواد و گدام‌ها
                              فیلتر خواهند شد.</small>
                          </div>
                        </div>
                        <div class="col-lg-4">
                          <div class="form-group mb-3">
                            <label class="font-weight-bold text-dark">انتخاب انوایس فروش (Sale Invoice)</label>
                            <select name="invoice_id" id="invoice_id_select" required
                              class="form-control font-weight-bold select2" style="height: 38px;">
                              <option value="">انتخاب انوایس...</option>
                              @foreach($invoices as $inv)
                                <option value="{{ $inv->id }}" data-type="{{ $inv->type }}"
                                  data-agent="{{ $inv->agent_id }}" {{ ($saleEdit && $saleEdit->invoice_id == $inv->id) ? 'selected' : '' }}>
                                  {{ $inv->invoice_no }} ({{ $inv->type === 'dye' ? 'رنگ' : 'نخ' }} -
                                  {{ $inv->agent->user->name ?? $inv->agent->name ?? '---' }})
                                </option>
                              @endforeach
                            </select>
                            <small class="text-muted d-block mt-1">با انتخاب انوایس، نوعیت فروش و نماینده به صورت خودکار
                              تنظیم خواهند شد.</small>
                          </div>
                        </div>
                        <div class="col-lg-4">
                          <div class="form-group mb-3">
                            <label class="font-weight-bold text-dark">تاریخ (Date)</label>
                            <input type="date" name="date" required
                              value="{{ $saleEdit ? $saleEdit->date : date('Y-m-d') }}" class="form-control text-right"
                              style="height: 38px;">
                            <small class="text-muted d-block mt-1">تاریخ انجام معامله فروش.</small>
                          </div>
                        </div>
                      </div>

                      <div class="row align-items-end">
                        <div class="col-lg-3">
                          <div class="form-group mb-3">
                            <label class="font-weight-bold text-dark">نام نماینده (Agent)</label>
                            <select name="agent_id" id="agent_id" required class="form-control select2">
                              <option value="">انتخاب نماینده</option>
                              @foreach ($agents as $ag)
                                <option value="{{$ag->agent_id}}" {{ ($saleEdit && $saleEdit->agent_id == $ag->agent_id) ? 'selected' : '' }}>{{$ag->user->name}}</option>
                              @endforeach
                            </select>
                            <div id="agent-balance" class="mt-1 small font-weight-bold text-primary"></div>
                          </div>
                        </div>

                        <div class="col-lg-3">
                          <div class="form-group mb-3">
                            <label class="font-weight-bold text-dark">دسته بندی مواد (Category)</label>
                            <select name="category_id" id="category_id" required class="form-control select2">
                              <option value="">انتخاب دسته بندی</option>
                              @foreach ($categories as $category)
                                <option value="{{$category->material_category_id}}" data-subtype="{{$category->subtype}}" {{ ($saleEdit && $saleEdit->category_id == $category->material_category_id) ? 'selected' : '' }}>
                                  {{$category->material_category}}
                                </option>
                              @endforeach
                            </select>
                          </div>
                        </div>

                        <div class="col-lg-3">
                          <div class="form-group mb-3">
                            <label class="font-weight-bold text-dark">نوعیت مواد (Type)</label>
                            <select name="type_id" id="type_id" required class="form-control select2">
                              <option value="">انتخاب نوعیت</option>
                              @foreach ($material_types as $type)
                                <option value="{{$type->material_type_id}}" data-subtype="{{$type->subtype}}" {{ ($saleEdit && $saleEdit->type_id == $type->material_type_id) ? 'selected' : '' }}>
                                  {{$type->material_type}}
                                </option>
                              @endforeach
                            </select>
                          </div>
                        </div>

                        <div class="col-lg-3">
                          <div class="form-group mb-3">
                            <label class="font-weight-bold text-dark">گدام (Warehouse)</label>
                            <select name="warehouse_id" id="warehouse_id" required class="form-control select2">
                              <option value="">انتخاب گدام</option>
                              @foreach ($warehouses as $w)
                                <option value="{{$w->id}}" data-subtype="{{$w->subtype}}" {{ ($saleEdit && $saleEdit->warehouse_id == $w->id) ? 'selected' : '' }}>
                                  {{$w->name}}
                                </option>
                              @endforeach
                            </select>
                            <div id="material-stock-info" class="mt-1 small font-weight-bold text-info"></div>
                          </div>
                        </div>
                      </div>

                      <div class="row mt-2 align-items-end">
                        <div class="col-lg-3">
                          <div class="form-group mb-3">
                            <label class="font-weight-bold text-dark text-success">مقدار فروش (Quantity - Kg)</label>
                            <div class="input-group">
                              <input type="number" step="0.01" name="amount" required placeholder="0.00"
                                class="form-control font-weight-bold border-success text-center" id="material-amount"
                                style="font-size: 1.1rem; height: 38px;" value="{{ $saleEdit ? $saleEdit->amount : '' }}">
                              <div class="input-group-append"><span
                                  class="input-group-text bg-success text-white">KG</span></div>
                            </div>
                            <small id="stock-error-text" class="text-danger d-block mt-1 font-weight-bold"></small>
                          </div>
                        </div>

                        <div class="col-lg-3">
                          <div class="form-group mb-3">
                            <label class="font-weight-bold text-dark">واحد پولی (Currency)</label>
                            <select name="currency_id" id="currency_id" class="form-control font-weight-bold"
                              style="border: 2px solid #059669; height: 38px;">
                              @foreach($currencies as $curr)
                                <option value="{{ $curr->id }}" data-rate="{{ $curr->exchange_rate }}"
                                  data-code="{{ $curr->code }}" {{ (($saleEdit && $saleEdit->currency_id == $curr->id) || (!$saleEdit && $curr->code == 'AFN')) ? 'selected' : '' }}>
                                  {{ $curr->name }} ({{ $curr->code }})
                                </option>
                              @endforeach
                            </select>
                          </div>
                        </div>

                        <div class="col-lg-2">
                          <div class="form-group mb-3">
                            <label class="font-weight-bold text-dark">نرخ تسطیح (Rate to USD)</label>
                            <input type="text" name="exchange_rate" id="exchange_rate" readonly
                              class="form-control bg-light font-weight-bold text-center" style="height: 38px;"
                              value="{{ $saleEdit ? $saleEdit->exchange_rate : '' }}">
                          </div>
                        </div>

                        <div class="col-lg-2">
                          <div class="form-group mb-3">
                            <label class="font-weight-bold text-dark">قیمت فی کیلو (Unit Price)</label>
                            <input type="number" step="0.01" name="price" required placeholder="0.00"
                              class="form-control font-weight-bold text-center" id="material-price" style="height: 38px;"
                              value="{{ $saleEdit ? $saleEdit->price : '' }}">
                            <span id="purchase-price-hint"
                              class="d-block text-warning small font-weight-bold text-center mt-1">قیمت خرید (WAC):
                              $0.00</span>
                          </div>
                        </div>

                        <div class="col-lg-2">
                          <div class="form-group mb-3">
                            <label class="font-weight-bold text-primary">مجموع (Grand Total)</label>
                            <input type="text" name="original_amount" id="original_amount" readonly
                              class="form-control font-weight-bold text-primary bg-light text-center"
                              style="font-size: 1.1rem; height: 38px;"
                              value="{{ $saleEdit ? $saleEdit->original_amount : '' }}">
                            <input type="hidden" name="total_price_af" id="material-af-total-price"
                              value="{{ $saleEdit ? $saleEdit->total_price_af : '' }}">
                            <input type="hidden" name="total_price" id="material-total-price"
                              value="{{ $saleEdit ? $saleEdit->total_price : '' }}">
                          </div>
                        </div>
                      </div>

                      <!-- LIVE TRUTH PREVIEW -->
                      <div class="row mt-3 mb-4">
                        <div class="col-lg-12">
                          <div class="p-3 shadow-sm"
                            style="background: linear-gradient(to right, #ecfdf5, #f0fdf4); border: 1px solid #10b981; border-radius: 12px;">
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
                                <div id="margin-badge" class="badge badge-success p-2"
                                  style="font-size: 1rem; border-radius: 8px;">Margin: 0%</div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>

                      <hr>
                      <!-- ACCOUNT OVERRIDES -->
                      <div class="row mt-3 p-3"
                        style="background: #f0f7ff; border: 1px solid #cce5ff; border-radius: 8px;">
                        <div class="col-lg-12">
                          <h6 class="mb-3 text-primary"><i class="fa fa-university"></i> تنظیمات حسابی (Material Sale
                            Accounting)</h6>
                        </div>
                        @php
                          $selectedDebitId = ($saleEdit && !is_null($saleEdit->override_debit_account_id))
                            ? $saleEdit->override_debit_account_id
                            : ($mapping->debit_account_id ?? null);

                          $selectedCreditId = ($saleEdit && !is_null($saleEdit->override_credit_account_id))
                            ? $saleEdit->override_credit_account_id
                            : ($mapping->credit_account_id ?? null);

                          $selectedCogsDebitId = ($saleEdit && !is_null($saleEdit->override_cogs_debit_id))
                            ? $saleEdit->override_cogs_debit_id
                            : (isset($cogsMapping) ? ($cogsMapping->debit_account_id ?? null) : null);

                          $selectedCogsCreditId = ($saleEdit && !is_null($saleEdit->override_cogs_credit_id))
                            ? $saleEdit->override_cogs_credit_id
                            : (isset($cogsMapping) ? ($cogsMapping->credit_account_id ?? null) : null);
                        @endphp
                        <div class="col-lg-3">
                          <div class="form-group text-right">
                            <label class="text-muted small">حساب دریافتنی (Debit) <span
                                class="badge badge-info">{{ count($allowedDebitAccounts) }}</span></label>
                            <select name="override_debit_account_id" id="override_debit_account_id"
                              class="form-control select2">
                              @foreach($allowedDebitAccounts as $acc)
                                <option value="{{ $acc->id }}" {{ $selectedDebitId == $acc->id ? 'selected' : '' }}>
                                  {{ $acc->account_code }} - {{ $acc->account_name }}
                                </option>
                              @endforeach
                            </select>
                          </div>
                        </div>
                        <div class="col-lg-3">
                          <div class="form-group text-right">
                            <label class="text-muted small">حساب فروش مواد (Credit) <span
                                class="badge badge-info">{{ count($allowedCreditAccounts) }}</span></label>
                            <select name="override_credit_account_id" id="override_credit_account_id"
                              class="form-control select2">
                              @foreach($allowedCreditAccounts as $acc)
                                <option value="{{ $acc->id }}" {{ $selectedCreditId == $acc->id ? 'selected' : '' }}>
                                  {{ $acc->account_code }} - {{ $acc->account_name }}
                                </option>
                              @endforeach
                            </select>
                          </div>
                        </div>
                        <div class="col-lg-3">
                          <div class="form-group text-right">
                            <label class="text-muted small">حساب مصرف (COGS Debit) <span
                                class="badge badge-info">{{ count($allowedCogsDebit) }}</span></label>
                            <select name="override_cogs_debit_id" id="override_cogs_debit_id"
                              class="form-control select2">
                              @foreach($allowedCogsDebit as $acc)
                                <option value="{{ $acc->id }}" {{ $selectedCogsDebitId == $acc->id ? 'selected' : '' }}>
                                  {{ $acc->account_code }} - {{ $acc->account_name }}
                                </option>
                              @endforeach
                            </select>
                          </div>
                        </div>
                        <div class="col-lg-3">
                          <div class="form-group text-right">
                            <label class="text-muted small">حساب گدام (COGS Credit) <span
                                class="badge badge-info">{{ count($allowedCogsCredit) }}</span></label>
                            <select name="override_cogs_credit_id" id="override_cogs_credit_id"
                              class="form-control select2">
                              @foreach($allowedCogsCredit as $acc)
                                <option value="{{ $acc->id }}" {{ $selectedCogsCreditId == $acc->id ? 'selected' : '' }}>
                                  {{ $acc->account_code }} - {{ $acc->account_name }}
                                </option>
                              @endforeach
                            </select>
                          </div>
                        </div>
                      </div>

                    </div>
                    <div class="modal-footer"
                      style="background: #f8fafc; border-top: 1px solid #e2e8f0; padding: 16px 24px; border-radius: 0 0 16px 16px;">
                      <button type="button" class="btn btn-secondary" data-dismiss="modal"
                        style="border-radius: 8px;">بستن (Close)</button>
                      <button type="submit" class="btn btn-primary" id="btn-submit-sale"
                        style="background: linear-gradient(135deg, #059669 0%, #047857 100%); border: none; border-radius: 8px; font-weight: 600; padding: 8px 24px;">
                        <i class="fa fa-save mr-1"></i> @if(!$saleEdit) ذخیره معامله (Save) @else بروزرسانی معامله
                        (Update) @endif
                      </button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
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
                <th class="border-top-0">نوعیت مواد</th>
                <th class="border-top-0">گدام</th>
                <th class="border-top-0">حسابات (Accounts)</th>
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
                  <td class="font-weight-bold text-center text-success">
                    ${{ number_format($material->base_currency_amount, 2) }}</td>
                  <td>{{$material->category->material_category}}</td>
                  <td>
                    @if(optional($material->type)->subtype == 'dye')
                      <span class="badge badge-danger">رنگ (Dye)</span>
                    @else
                      <span class="badge badge-success">تار (Yarn)</span>
                    @endif
                    <small
                      class="d-block text-muted font-weight-bold">{{ optional($material->type)->material_type }}</small>
                  </td>
                  <td><span
                      class="text-secondary font-weight-bold">{{ optional($material->warehouse)->name ?? '---' }}</span>
                  </td>
                  <td>
                    @php
                      $deb = $material->debitAccount ?? ($mapping->debitAccount ?? null);
                      $cred = $material->creditAccount ?? ($mapping->creditAccount ?? null);
                      $cogsDeb = $material->cogsDebitAccount ?? ($cogsMapping->debitAccount ?? null);
                      $cogsCred = $material->cogsCreditAccount ?? ($cogsMapping->creditAccount ?? null);

                      $debName = $deb ? ($deb->account_code . ' - ' . $deb->account_name) : '12000 - Accounts Receivable';
                      $credName = $cred ? ($cred->account_code . ' - ' . $cred->account_name) : '19000 - Revenue From Sales';
                      $cogsDebName = $cogsDeb ? ($cogsDeb->account_code . ' - ' . $cogsDeb->account_name) : '20000 - Cost of Goods Sold';
                      $cogsCredName = $cogsCred ? ($cogsCred->account_code . ' - ' . $cogsCred->account_name) : '13000 - Inventory';

                      $debCode = $deb ? $deb->account_code : '12000';
                      $credCode = $cred ? $cred->account_code : '19000';
                      $cogsDebCode = $cogsDeb ? $cogsDeb->account_code : '20000';
                      $cogsCredCode = $cogsCred ? $cogsCred->account_code : '13000';
                    @endphp
                    <div style="font-size: 0.76rem; line-height: 1.3;">
                      <div class="text-nowrap mb-1">
                        <span class="badge badge-light border text-primary font-weight-bold" style="padding: 3px 6px;" title="دریافتنی (Debit): {{ $debName }}" data-toggle="tooltip">
                          Dr: {{ $debCode }}
                        </span>
                        <span class="badge badge-light border text-success font-weight-bold" style="padding: 3px 6px;" title="فروش مواد (Credit): {{ $credName }}" data-toggle="tooltip">
                          Cr: {{ $credCode }}
                        </span>
                      </div>
                      <div class="text-nowrap">
                        <span class="badge badge-light border text-muted font-weight-bold" style="padding: 2px 5px;" title="مصرف (COGS Dr): {{ $cogsDebName }} | گدام (COGS Cr): {{ $cogsCredName }}" data-toggle="tooltip">
                          COGS: {{ $cogsDebCode }} / {{ $cogsCredCode }}
                        </span>
                      </div>
                    </div>
                  </td>
                  <td class="small">{{$material->date}}</td>
                  <td>
                    @if($material->status == 0)
                      <span class="badge badge-warning font-weight-normal"><i class="fa fa-clock-o mr-1"></i> در انتظار
                        تایید</span>
                    @else
                      <span class="badge badge-success font-weight-normal"><i class="fa fa-check mr-1"></i> تایید شده</span>
                    @endif
                  </td>
                  <td class="hideOnPrint">
                    @if($material->invoice && $material->invoice->status === 'closed')
                      <span class="text-muted small"><i class="fa fa-lock"></i> انوایس بسته شده</span>
                    @else
                      @can('edit_material_sale')
                        <a class="btn btn-sm btn-outline-emerald py-0 px-2"
                          href="/dashboard/material-sales/{{$material->id}}/edit">
                          <i class="fa fa-edit"></i>
                        </a>
                      @endcan
                    @endif
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="14" class="text-center py-5">
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
    $(document).ready(function () {
      var currentWAC = 0;
      var afnRate = 1; // Default fallback

      // Keep original options in memory for filtering
      const originalCategories = $('#category_id option').map(function () {
        return { value: this.value, text: this.text, subtype: $(this).data('subtype') };
      }).get();

      const originalTypes = $('#type_id option').map(function () {
        return { value: this.value, text: this.text, subtype: $(this).data('subtype') };
      }).get();

      const originalWarehouses = $('#warehouse_id option').map(function () {
        return { value: this.value, text: this.text, subtype: $(this).data('subtype') };
      }).get();

      const originalInvoices = $('#invoice_id_select option').map(function () {
        return { value: this.value, text: this.text, type: $(this).data('type'), agent: $(this).data('agent') };
      }).get();

      // Initialize Select2 with modal dropdown parent to prevent z-index issues
      $('#invoice_id_select, #agent_id, #category_id, #type_id, #warehouse_id, #override_debit_account_id, #override_credit_account_id, #override_cogs_debit_id, #override_cogs_credit_id').select2({
        dropdownParent: $('#saleModal'),
        width: '100%',
        dir: 'rtl'
      });

      $('#invoice_id_select').on('change', function () {
        var selected = $(this).find(':selected');
        var type = selected.data('type');
        var agentId = selected.data('agent');
        if (type && $('#sale_subtype').val() !== type) {
          $('#sale_subtype').val(type).trigger('change');
        }
        if (agentId && $('#agent_id').val() != agentId) {
          $('#agent_id').val(agentId).trigger('change');
        }
      });

      // Show modal immediately if edit mode is active
      @if($saleEdit)
        $('#saleModal').modal('show');
      @endif

      // Subtype dynamic filtering logic using memory cache (prevents select2 from breaking)
      function filterOptions(subtype) {
        // Category
        const catSelect = $('#category_id');
        const oldCatVal = catSelect.val();
        catSelect.empty().append('<option value="">انتخاب دسته بندی</option>');
        originalCategories.forEach(function (opt) {
          if (opt.value && (!opt.subtype || opt.subtype === subtype)) {
            catSelect.append($('<option>', { value: opt.value, text: opt.text, 'data-subtype': opt.subtype }));
          }
        });
        if (catSelect.find('option[value="' + oldCatVal + '"]').length > 0) {
          catSelect.val(oldCatVal);
        }
        catSelect.trigger('change.select2');

        // Type
        const typeSelect = $('#type_id');
        const oldTypeVal = typeSelect.val();
        typeSelect.empty().append('<option value="">انتخاب نوعیت</option>');
        originalTypes.forEach(function (opt) {
          if (opt.value && (!opt.subtype || opt.subtype === subtype)) {
            typeSelect.append($('<option>', { value: opt.value, text: opt.text, 'data-subtype': opt.subtype }));
          }
        });
        if (typeSelect.find('option[value="' + oldTypeVal + '"]').length > 0) {
          typeSelect.val(oldTypeVal);
        }
        typeSelect.trigger('change.select2');

        // Warehouse
        const whSelect = $('#warehouse_id');
        const oldWhVal = whSelect.val();
        whSelect.empty().append('<option value="">انتخاب گدام</option>');
        originalWarehouses.forEach(function (opt) {
          if (opt.value && (!opt.subtype || opt.subtype === subtype)) {
            whSelect.append($('<option>', { value: opt.value, text: opt.text, 'data-subtype': opt.subtype }));
          }
        });
        if (whSelect.find('option[value="' + oldWhVal + '"]').length > 0) {
          whSelect.val(oldWhVal);
        }
        whSelect.trigger('change.select2');

        // Invoice
        const invSelect = $('#invoice_id_select');
        const oldInvVal = invSelect.val();
        invSelect.empty().append('<option value="">انتخاب انوایس...</option>');
        originalInvoices.forEach(function (opt) {
          if (opt.value && (!opt.type || opt.type === subtype)) {
            invSelect.append($('<option>', {
              value: opt.value,
              text: opt.text,
              'data-type': opt.type,
              'data-agent': opt.agent
            }));
          }
        });
        if (invSelect.find('option[value="' + oldInvVal + '"]').length > 0) {
          invSelect.val(oldInvVal);
        }
        invSelect.trigger('change.select2');
      }

      $('#sale_subtype').on('change', function () {
        filterOptions($(this).val());
      });

      // Run initial filter on page load based on current selection
      filterOptions($('#sale_subtype').val());

      // Currency Handling
      function updateRate(isUserAction = false) {
        var selected = $('#currency_id').find(':selected');
        var rate = selected.data('rate');

        // FORENSIC SAFEGUARD: Only apply live rate if user changed currency OR if field is empty (New Sale)
        if (isUserAction || !$('#exchange_rate').val()) {
          $('#exchange_rate').val(parseFloat(rate).toFixed(8));
        }

        updateWacHint();
        calculateTruth();
      }

      $('#currency_id').on('change', function () {
        updateRate(true);
      });

      updateRate(false); // Initial run

      // Fetch WAC, Available Stock and Agent Balance
      $('#agent_id, #category_id, #type_id, #warehouse_id').on('change', fetchContext);

      function fetchContext() {
        var agentId = $('#agent_id').val();
        var catId = $('#category_id').val();
        var typeId = $('#type_id').val();
        var whId = $('#warehouse_id').val();

        if (agentId || (catId && typeId)) {
          $.ajax({
            url: "{{ route('dashboard.material-sales-info') }}",
            data: { agent_id: agentId, category_id: catId, type_id: typeId, warehouse_id: whId, sale_id: "{{ $saleEdit ? $saleEdit->id : '' }}" },
            success: function (res) {
              if (res.balance !== undefined) {
                $('#agent-balance').html('Balance: ' + parseFloat(res.balance).toLocaleString() + ' AFN');
              }
              if (res.wac !== undefined) {
                currentWAC = parseFloat(res.wac);
                updateWacHint();
              }
              if (res.available_stock !== undefined) {
                var stock = parseFloat(res.available_stock);
                $('#material-stock-info').html('Available: ' + stock.toLocaleString() + ' Kg');
                $('#material-amount').data('available-stock', stock);
                validateStock();
              }
              calculateTruth();
            }
          });
        }
      }

      function updateWacHint() {
        var rate = parseFloat($('#exchange_rate').val()) || 1;
        var currCode = $('#currency_id').find(':selected').data('code') || 'USD';
        var wacInTxCurrency = currentWAC;
        if (currCode !== 'USD') {
          wacInTxCurrency = currentWAC / (rate || 1);
        }
        $('#purchase-price-hint').text('قیمت خرید (WAC): ' + wacInTxCurrency.toFixed(2) + ' ' + currCode + ' ($' + currentWAC.toFixed(2) + ')');
      }

      function validateStock() {
        var qty = parseFloat($('#material-amount').val()) || 0;
        var availableStock = parseFloat($('#material-amount').data('available-stock')) || 0;

        if ($('#category_id').val() && $('#type_id').val() && $('#warehouse_id').val()) {
          if (availableStock <= 0) {
            $('#stock-error-text').text('توجه: این مواد در گدام انتخاب شده موجود نمی‌باشد!').show();
            $('#btn-submit-sale').prop('disabled', true);
          } else if (qty > availableStock) {
            $('#stock-error-text').text('توجه: مقدار وارد شده بیشتر از موجودی گدام است (' + availableStock.toFixed(2) + ' Kg)').show();
            $('#btn-submit-sale').prop('disabled', true);
          } else {
            $('#stock-error-text').hide().text('');
            $('#btn-submit-sale').prop('disabled', false);
          }
        } else {
          $('#stock-error-text').hide().text('');
          $('#btn-submit-sale').prop('disabled', false);
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

        $('#original_amount').val(totalOriginal.toLocaleString(undefined, { minimumFractionDigits: 2 }));
        $('#usd-truth-preview').text('$ ' + totalUsd.toLocaleString(undefined, { minimumFractionDigits: 2 }));

        // Legacy compatibility
        $('#material-af-total-price').val((totalUsd / afnRate).toFixed(2));
        $('#material-total-price').val(totalUsd.toFixed(2));

        // COGS and Profit
        var estCogsUsd = qty * currentWAC;
        var estProfitUsd = totalUsd - estCogsUsd;
        var margin = totalUsd > 0 ? (estProfitUsd / totalUsd) * 100 : 0;

        $('#cogs-truth-preview').text('$ ' + estCogsUsd.toLocaleString(undefined, { minimumFractionDigits: 2 }));
        $('#profit-truth-preview').text('$ ' + estProfitUsd.toLocaleString(undefined, { minimumFractionDigits: 2 }));

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
      $('#material-amount, #type_id, #warehouse_id').on('input change blur', validateStock);

      // Initial context load
      fetchContext();
    });
  </script>
@endsection