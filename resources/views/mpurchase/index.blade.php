@extends('dsh.master')
@section('title' , 'لیست خرید مواد خام')
@section('content')

  <style>
  .glass-modal {
      background: rgba(255, 255, 255, 0.95) !important;
      backdrop-filter: blur(12px) !important;
      -webkit-backdrop-filter: blur(12px) !important;
      border: 1px solid rgba(255, 255, 255, 0.3) !important;
      border-radius: 16px !important;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15) !important;
  }
  .glass-modal .modal-header {
      background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%) !important;
      color: #fff !important;
      border-top-left-radius: 15px !important;
      border-top-right-radius: 15px !important;
  }
  .glass-modal .modal-footer {
      border-top: 1px solid rgba(0,0,0,0.05) !important;
  }
  .form-helper {
      font-size: 0.72rem;
      color: #64748b;
      margin-top: 3px;
      display: block;
  }
  .glass-modal label {
      font-weight: 600;
      color: #334155;
      font-size: 0.85rem;
  }
  </style>

  <!-- Container -->
  <div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <!-- Main Action Header Card -->
      <div class="card" style="border: none; box-shadow: 0 2px 12px rgba(0,0,0,0.08); margin-bottom: 20px;">
        <div class="card-header" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); border-radius: 8px; padding: 16px 24px;">
          <div class="d-flex align-items-center justify-content-between">
            <div>
              <h5 style="color: #fff; margin: 0; font-weight: 600; letter-spacing: 0.5px;">
                <i class="fa fa-shopping-cart" style="color: #60a5fa; margin-left: 8px;"></i>
                مدیریت خریدهای مواد خام
              </h5>
              <small style="color: #94a3b8;">ثبت فاکتورهای خرید مواد خام (تار و رنگ) همراه با پیگیری حسابات و گدام‌ها</small>
            </div>
            <div>
              <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#purchaseModal" style="border-radius: 6px; font-weight: 600;">
                <i class="fa fa-plus-circle"></i> ثبت خرید جدید
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Purchase Table Ledger Card -->
      <div class="card" style="border: none; box-shadow: 0 2px 12px rgba(0,0,0,0.08);">
        <div class="card-header" style="background: #f8fafc; border-radius: 8px 8px 0 0; padding: 16px 24px; border-bottom: 1px solid #e2e8f0;">
          <div class="d-flex align-items-center justify-content-between">
            <div>
              <h5 style="color: #334155; margin: 0; font-weight: 600;">
                <i class="fa fa-list-alt" style="color: #475569; margin-left: 8px;"></i>
                لیست خریدهای ثبت شده
              </h5>
              <small style="color: #64748b;">Material Purchase Ledger — Forensic View</small>
            </div>
            <div>
              <span class="badge" style="background: rgba(59,130,246,0.1); color: #3b82f6; border: 1px solid rgba(59,130,246,0.2); padding: 6px 14px; border-radius: 20px; font-size: 0.8rem;">
                {{ $purchase->total() }} رکورد
              </span>
            </div>
          </div>
          @if(session("status"))
            <div class="alert alert-success status mt-2 mb-0" role="alert" style="border-radius: 6px;">
              <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
              {{ session('status') }}
            </div>
          @endif
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table mb-0" id="purchase_table" style="font-size: 0.88rem;">
              <thead style="background: #f1f5f9; border-bottom: 2px solid #cbd5e1;">
                <tr style="color: #475569;">
                  <th style="padding: 12px 16px; font-weight: 600; white-space: nowrap;">فاکتور #</th>
                  <th style="padding: 12px 16px; font-weight: 600; text-align: center; white-space: nowrap;">بل خرید</th>
                  <th style="padding: 12px 16px; font-weight: 600; white-space: nowrap;">نوعیت خرید</th>
                  <th style="padding: 12px 16px; font-weight: 600;">فروشنده</th>
                  <th style="padding: 12px 16px; font-weight: 600; white-space: nowrap;">تاریخ</th>
                  <th style="padding: 12px 16px; font-weight: 600;">کتگوری</th>
                  <th style="padding: 12px 16px; font-weight: 600;">نوعیت</th>
                  <th style="padding: 12px 16px; font-weight: 600; text-align: center;">مقدار</th>
                  <th style="padding: 12px 16px; font-weight: 600; text-align: right;">قیمت/KG</th>
                  <th style="padding: 12px 16px; font-weight: 600; text-align: right; border-left: 2px solid #e2e8f0;">
                    <span style="color: #7c3aed;">مبلغ (ارز تراکنش)</span>
                  </th>
                  <th style="padding: 12px 16px; font-weight: 600; text-align: right;">
                    <span style="color: #059669;">معادل USD</span>
                    <small class="d-block" style="font-weight:400; color:#94a3b8; font-size:0.72rem;">نرخ × مبلغ</small>
                  </th>
                  <th style="padding: 12px 16px; font-weight: 600; text-align: center;">گدام</th>
                  <th style="padding: 12px 16px; font-weight: 600; text-align: center;">حالت</th>
                  <th style="padding: 12px 16px; font-weight: 600; text-align: center;">اقدام</th>
                </tr>
              </thead>
              <tbody>
              @forelse($purchase as $p)
                @php
                  $currCode    = $p->currency_code ?? 'AFN';
                  $exchRate    = $p->exchange_rate ?? 1;
                  $origAmt     = $p->original_amount ?? ($p->quantity * $p->price_per_kilo);
                  $baseUSD     = $p->base_currency_amount ?? ($origAmt * $exchRate);
                  $isUSD       = ($currCode === 'USD');
                  $rowBg       = $loop->even ? '#fafafa' : '#ffffff';
                @endphp
                <tr style="background: {{ $rowBg }}; border-bottom: 1px solid #f1f5f9; transition: background 0.15s;"
                    onmouseover="this.style.background='#eff6ff'" onmouseout="this.style.background='{{ $rowBg }}'">

                  {{-- PO Number --}}
                  <td style="padding: 10px 16px; white-space: nowrap;">
                    <a href="/dashboard/material-purchase/search-purchase-number/{{ $p->purchase_number }},{{ $p->seller_id }}"
                       style="font-weight: 700; color: #3b82f6; font-family: monospace; font-size: 0.85rem;">
                      {{ $p->purchase_number }}
                    </a>
                  </td>

                  {{-- Purchase Bill Image or Link --}}
                  <td style="padding: 10px 16px; text-align: center; vertical-align: middle;">
                    @if($p->purchaseBill)
                      <a href="{{ route('raw-material-purchase-bills.show', $p->raw_material_purchase_bill_id) }}" target="_blank" style="font-weight: bold; color: #1e3a8a; font-size: 0.85rem; background: #eff6ff; padding: 4px 8px; border-radius: 6px; border: 1px solid #bfdbfe; text-decoration: none;">
                        {{ $p->purchaseBill->bill_number }}
                      </a>
                    @elseif($p->purchase_bill)
                      <a href="{{ asset($p->purchase_bill) }}" target="_blank">
                        <img src="{{ asset($p->purchase_bill) }}" alt="بل خرید" style="width: 45px; height: 45px; object-fit: cover; border-radius: 8px; border: 1px solid #cbd5e1; box-shadow: 0 2px 6px rgba(0,0,0,0.05); transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.15)'" onmouseout="this.style.transform='scale(1)'">
                      </a>
                    @else
                      <span style="color: #cbd5e1;">—</span>
                    @endif
                  </td>

                  {{-- Parent Type --}}
                  <td style="padding: 10px 16px; white-space: nowrap;">
                    @if(optional($p->materialType)->subtype == 'dye')
                      <span class="badge badge-warning" style="font-weight: 600; padding: 4px 8px;">رنگ (Dye)</span>
                    @else
                      <span class="badge badge-primary" style="font-weight: 600; padding: 4px 8px;">تار (Yarn)</span>
                    @endif
                  </td>

                  {{-- Seller --}}
                  <td style="padding: 10px 16px; color: #1e293b; font-weight: 500;">
                    {{ $p->seller->name ?? '—' }}
                  </td>

                  {{-- Date --}}
                  <td style="padding: 10px 16px; white-space: nowrap; color: #64748b;" dir="ltr">
                    {{ \Carbon\Carbon::parse($p->purchase_date)->format('d M Y') }}
                  </td>

                  {{-- Category --}}
                  <td style="padding: 10px 16px; color: #475569;">
                    {{ $p->materialCategory->material_category ?? '—' }}
                  </td>

                  {{-- Type --}}
                  <td style="padding: 10px 16px;">
                    <span style="background: #ede9fe; color: #7c3aed; padding: 2px 10px; border-radius: 12px; font-size: 0.8rem; font-weight: 600;">
                      {{ $p->materialType->material_type ?? '—' }}
                    </span>
                  </td>

                  {{-- Quantity --}}
                  <td style="padding: 10px 16px; text-align: center;" dir="ltr">
                    <span style="font-weight: 700; color: #1e293b;">{{ number_format($p->quantity, 2) }}</span>
                    <span style="color: #94a3b8; font-size: 0.78rem;"> KG</span>
                  </td>

                  {{-- Unit Price with currency --}}
                  <td style="padding: 10px 16px; text-align: right;" dir="ltr">
                    <span style="font-weight: 600; color: #374151;">{{ number_format($p->price_per_kilo, 2) }}</span>
                    <span style="color: #94a3b8; font-size: 0.75rem; margin-right: 3px;">{{ $currCode }}</span>
                  </td>

                  {{-- Original Amount in transaction currency --}}
                  <td style="padding: 10px 16px; text-align: right; border-left: 2px solid #ede9fe;" dir="ltr">
                    <div style="display:flex; flex-direction:column; align-items:flex-end;">
                      <span style="font-weight: 700; color: #7c3aed; font-size: 1rem;">
                        {{ number_format($origAmt, 2) }}
                        <span style="font-size:0.78rem; font-weight:600; opacity:0.75;">{{ $currCode }}</span>
                      </span>
                      @if(!$isUSD)
                        <small style="color: #94a3b8; font-size: 0.72rem;">
                          نرخ: <span style="color:#7c3aed; font-weight:600;">{{ number_format($exchRate, 4) }}</span>
                        </small>
                      @endif
                    </div>
                  </td>

                  {{-- USD Equivalent (normalized base) --}}
                  <td style="padding: 10px 16px; text-align: right;" dir="ltr">
                    <div style="display: inline-flex; align-items: center; gap: 4px; background: #ecfdf5; padding: 4px 10px; border-radius: 8px; border: 1px solid #a7f3d0;">
                      <span style="color: #059669; font-size: 0.8rem; font-weight: 600;">$</span>
                      <span style="font-weight: 700; color: #065f46; font-size: 0.95rem;">
                        {{ number_format($baseUSD, 2) }}
                      </span>
                    </div>
                  </td>

                  {{-- Warehouse --}}
                  <td style="padding: 10px 16px; text-align: center;">
                    @if($p->warehouse_id)
                      <span style="background: #f0fdf4; color: #16a34a; padding: 2px 8px; border-radius: 10px; font-size: 0.78rem; border: 1px solid #bbf7d0;">
                        {{ optional($p->warehouse)->name ?? 'گدام #'.$p->warehouse_id }}
                      </span>
                    @else
                      <span style="color: #cbd5e1;">—</span>
                    @endif
                  </td>

                  {{-- Status --}}
                  <td style="padding: 10px 16px; text-align: center;">
                    @if($p->status == 0)
                      <span style="background: #fef3c7; color: #92400e; padding: 3px 10px; border-radius: 20px; font-size: 0.78rem; font-weight: 600; border: 1px solid #fcd34d;">
                        <i class="fa fa-clock-o"></i> در انتظار
                      </span>
                    @else
                      <span style="background: #d1fae5; color: #065f46; padding: 3px 10px; border-radius: 20px; font-size: 0.78rem; font-weight: 600; border: 1px solid #6ee7b7;">
                        <i class="fa fa-check"></i> تایید شده
                      </span>
                    @endif
                  </td>

                  {{-- Actions --}}
                  <td style="padding: 10px 16px; text-align: center; white-space: nowrap;">
                    @if($p->status == 0 || auth()->user()->role == 'SP')
                      <a href="/dashboard/material-purchase/{{ $p->id }}/edit"
                         class="btn btn-xs"
                         style="background: #3b82f6; color: #fff; padding: 4px 12px; border-radius: 6px; font-size: 0.78rem; font-weight: 600; text-decoration: none;">
                        <i class="fa fa-edit"></i> ویرایش
                      </a>
                      <button onclick="deletePurchase({{ $p->id }})"
                              class="btn btn-xs"
                              style="background: #ef4444; color: #fff; padding: 4px 10px; border-radius: 6px; font-size: 0.78rem; margin-right: 4px; border: none; cursor: pointer;">
                        <i class="fa fa-trash"></i>
                      </button>
                    @else
                      <span style="color:#cbd5e1; font-size:0.78rem;">—</span>
                    @endif
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="14" style="text-align:center; padding: 40px; color: #94a3b8;">
                    <i class="fa fa-inbox" style="font-size: 2rem; display:block; margin-bottom: 8px;"></i>
                    هیچ رکوردی یافت نشد
                  </td>
                </tr>
              @endforelse
              </tbody>

              {{-- Summary Footer Row --}}
              @if($purchase->count() > 0)
              <tfoot style="background: #f8fafc; border-top: 2px solid #e2e8f0;">
                <tr>
                  <td colspan="7" style="padding: 10px 16px; font-weight: 700; color: #475569; text-align: right;">
                    مجموع صفحه جاری:
                  </td>
                  <td style="padding: 10px 16px; font-weight: 700; color: #1e293b; text-align: center;" dir="ltr">
                    {{ number_format($purchase->sum('quantity'), 2) }} KG
                  </td>
                  <td></td>
                  <td style="padding: 10px 16px; font-weight: 700; color: #7c3aed; text-align: right;" dir="ltr">
                    — mixed currencies —
                  </td>
                  <td style="padding: 10px 16px; text-align: right;" dir="ltr">
                    <div style="display: inline-flex; align-items: center; gap: 4px; background: #ecfdf5; padding: 5px 12px; border-radius: 8px; border: 1px solid #6ee7b7;">
                      <span style="color:#059669; font-weight:600;">$</span>
                      <span style="font-weight: 800; color: #065f46;">
                        {{ number_format($purchase->sum('base_currency_amount'), 2) }}
                      </span>
                    </div>
                  </td>
                  <td colspan="3"></td>
                </tr>
              </tfoot>
              @endif
            </table>
          </div>

          {{-- Pagination --}}
          <div style="padding: 16px 20px; border-top: 1px solid #f1f5f9; background: #fafafa; border-radius: 0 0 8px 8px;">
            {{ $purchase->links() }}
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Modern Glassmorphism Modal Form -->
  <div class="modal fade" id="purchaseModal" tabindex="-1" role="dialog" aria-labelledby="purchaseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content glass-modal">
        <div class="modal-header">
          <h5 class="modal-title" id="purchaseModalLabel">
            @if($purchaseMaterial)
              <i class="fa fa-edit" style="color: #60a5fa; margin-left: 8px;"></i> ویرایش خرید مواد خام
            @else
              <i class="fa fa-plus-circle" style="color: #60a5fa; margin-left: 8px;"></i> ثبت خرید جدید مواد خام
            @endif
          </h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        
        @if(!$purchaseMaterial)
          <!-- Create Form -->
          <form action="/dashboard/material-purchase" method="post" enctype="multipart/form-data">
            @csrf
            <div class="modal-body">
              <div class="row">
                <!-- Subtype selector -->
                <div class="col-lg-4 col-md-6 col-sm-12">
                  <div class="form-group fill">
                    <label class="text-primary" style="font-weight: 700;">نوعیت خرید (Purchase Subtype)</label>
                    <select name="purchase_subtype" id="purchase_subtype" class="form-control" style="border: 2px solid #3b82f6;">
                      <option value="yarn" selected>تار (Yarn)</option>
                      <option value="dye">رنگ (Dye)</option>
                    </select>
                    <span class="form-helper">انتخاب کنید که مایل به خرید تار هستید یا رنگ. این فیلتر به صورت خودکار کتگوری، نوعیت مواد و گودام‌ها را فیلتر می‌کند.</span>
                  </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-12">
                  <div class="form-group fill">
                    <label>شماره فاکتور</label>
                    <input type="text" class="form-control" value="{{$PurchaseNo}}" name="purchase_number" required>
                    <span class="form-helper">شماره مرجع فاکتور خرید مواد خام (تولید خودکار سیستم).</span>
                  </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-12">
                  <div class="form-group fill">
                    <label>فروشنده (Seller)</label>
                    <select name="seller_id" id="seller_id" class="form-control select2" style="width: 100%;" required>
                      <option value="">~~~</option>
                      @foreach($sellers as $s)
                        <option value="{{ $s->id }}" {{ old('seller_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                      @endforeach
                    </select>
                    <span class="form-helper">فروشنده یا تامین‌کننده طرف معامله این فاکتور خرید.</span>
                  </div>
                </div>
              </div>

              {{-- Purchase Bill row (create) --}}
              <div class="row mt-3">
                <div class="col-lg-12">
                  <div class="form-group fill">
                    <label><i class="fa fa-file-text-o" style="color:#3b82f6;"></i> بل خرید مواد خام (Purchase Bill)
                      <a href="{{ route('raw-material-purchase-bills.create') }}" target="_blank"
                         style="font-size:.78rem; font-weight:600; margin-right:8px; color:#059669;">
                        <i class="fa fa-plus-circle"></i> ثبت بل جدید
                      </a>
                    </label>
                    <select name="raw_material_purchase_bill_id" id="purchase_bill_id_create" class="form-control select2" style="width: 100%;">
                      <option value="">— بدون بل / انتخاب بعد —</option>
                      @foreach($purchaseBills as $pb)
                        <option value="{{ $pb->id }}"
                                data-seller="{{ $pb->seller_id }}"
                                {{ old('raw_material_purchase_bill_id') == $pb->id ? 'selected' : '' }}>
                          {{ $pb->bill_number }} — {{ optional($pb->seller)->name }} ({{ $pb->date }})
                        </option>
                      @endforeach
                    </select>
                    <span class="form-helper">پس از انتخاب فروشنده، فقط بل‌های مرتبط با آن فروشنده نمایش می‌یابند.</span>
                  </div>
                </div>
              </div>

              <div class="row mt-3">
                <div class="col-lg-4 col-md-6 col-sm-12">
                  <div class="form-group fill">
                    <label>ارز پرداخت (Currency)</label>
                    <select name="currency_id" id="currency_id" class="form-control select2" style="width: 100%;" required>
                      @foreach($currencies as $curr)
                        <option value="{{ $curr->id }}" data-rate="{{ $curr->exchange_rate }}" {{ old('currency_id') == $curr->id ? 'selected' : ($curr->code == 'AFN' ? 'selected' : '') }}>
                          {{ $curr->code }} ({{ $curr->symbol }})
                        </option>
                      @endforeach
                    </select>
                    <span class="form-helper">ارز اصلی معامله (به عنوان مثال افغانی یا دالر).</span>
                  </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-12">
                  <div class="form-group fill">
                    <label>نرخ تبادله به دالر (Exchange Rate)</label>
                    <input type="number" step="0.00000001" name="exchange_rate" id="exchange_rate" class="form-control bg-light" 
                           value="{{ old('exchange_rate', $currencies->where('code', 'AFN')->first()->exchange_rate ?? 0) }}" readonly>
                    <span class="form-helper">نرخ تبدیل ارز تراکنش به دالر جهت محاسبات نارمل‌سازی سیستم.</span>
                  </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-12">
                  <div class="form-group fill">
                    <label>تاریخ معامله (Date)</label>
                    <input id="purchase_date" name="purchase_date" type="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    <span class="form-helper">تاریخ رسمی ثبت فاکتور در سیستم مالی.</span>
                  </div>
                </div>
              </div>

              <div class="row mt-3">
                <div class="col-lg-4 col-md-6 col-sm-12">
                  <div class="form-group fill">
                    <label>کتگوری مواد (Category)</label>
                    <select name="material_category" id="material_category" class="form-control select2" style="width: 100%;" required>
                      <option value="">~~~</option>
                      @foreach($material_category as $mc)
                        <option {{ (Request::old('material_category') == $mc->material_category_id ? 'selected' : '') }} value="{{ $mc->material_category_id }}" data-subtype="{{ $mc->subtype }}">{{ $mc->material_category }}</option>
                      @endforeach
                    </select>
                    <span class="form-helper">دسته‌بندی کلی مواد (مثلاً تار پشم یا برندهای رنگ).</span>
                  </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-12">
                  <div class="form-group fill">
                    <label>نوعیت مواد (Type)</label>
                    <select name="material_type" id="material_type" class="form-control select2" style="width: 100%;" required>
                      <option value="">~~~</option>
                      @foreach($material_type as $mt)
                        <option {{ (Request::old('material_type') == $mt->material_type_id ? 'selected' : '') }} value="{{ $mt->material_type_id }}" data-subtype="{{ $mt->subtype }}">{{ $mt->material_type }}</option>
                      @endforeach
                    </select>
                    <span class="form-helper">نوع دقیق کالا (به طور فرض پشم طبیعی یا رنگ آبی).</span>
                  </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-12">
                  <div class="form-group fill">
                    <label>گودام ذخیره (Warehouse)</label>
                    <select name="warehouse_id" id="warehouse_id" class="form-control select2" style="width: 100%;" required>
                      @foreach($warehouses as $w)
                        <option value="{{ $w->id }}" data-subtype="{{ $w->subtype }}" {{ old('warehouse_id') == $w->id ? 'selected' : '' }}>{{ $w->name }}</option>
                      @endforeach
                    </select>
                    <span class="form-helper">گودامی که کالا فیزیکاً به آن وارد می‌شود.</span>
                  </div>
                </div>
              </div>

              <div class="row mt-3">
                <div class="col-lg-4 col-md-6 col-sm-12">
                  <div class="form-group fill">
                    <label>مقدار خریداری شده (Weight KG)</label>
                    <input name="quantity" id="material-amount" type="number" step="0.01" value="{{ old('quantity') }}" class="form-control" required>
                    <span class="form-helper">وزن خالص خریداری شده به مقیاس کیلوگرام.</span>
                  </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-12">
                  <div class="form-group fill">
                    <label>قیمت فی کیلوگرام (Price/KG)</label>
                    <input name="price_per_kilo" id="material-price" value="{{ old('price_per_kilo') }}" type="number" step="0.0001" class="form-control" required>
                    <span class="form-helper">قیمت خرید به ازای هر کیلوگرم بر اساس ارز فاکتور.</span>
                  </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-12">
                  <div class="form-group fill">
                    <label>قیمت کل به حروف (In Words)</label>
                    <input type="text" class="form-control" name="in_words" placeholder="قیمت به حروف" required>
                    <span class="form-helper">بیان مبلغ نهایی فاکتور به حروف فارسی جهت کنترل دستی.</span>
                  </div>
                </div>
              </div>

              <div class="row mt-3">
                <div class="col-lg-12">
                  <div class="form-group fill">
                    <label style="font-weight: 700; color: #334155;">تصویر بل خرید (Purchase Bill Image)</label>
                    <input type="file" name="purchase_bill" class="form-control" accept="image/*" style="border: 2px dashed #cbd5e1; padding: 12px; height: auto; border-radius: 8px;">
                    <span class="form-helper">تصویر فاکتور خرید فیزیکی (فرمت‌های مجاز: jpeg, png, jpg, gif, webp).</span>
                  </div>
                </div>
              </div>

              <div class="row mt-3">
                <div class="col-lg-12">
                  <div class="truth-preview-box" style="background: #f8fafc; padding: 16px; border-radius: 12px; border: 1px solid #e2e8f0; border-right: 5px solid #2563eb;">
                    <div class="row align-items-center">
                      <div class="col-md-4">
                        <span class="text-muted small"><i class="fa fa-calculator"></i> معادل دالر (USD Normalize):</span><br>
                        <span class="font-weight-bold text-primary" id="usd_truth_preview" style="font-size: 1.4rem; font-family: monospace;">$ 0.0000</span>
                      </div>
                      <div class="col-md-4 border-right" style="border-color: #cbd5e1 !important; padding-right: 20px;">
                        <span class="text-muted small">مجموع ارز اصلی (Total Original):</span><br>
                        <span class="text-dark font-weight-bold" id="original_total_preview" style="font-size: 1.2rem; font-family: monospace;">0.00</span>
                      </div>
                      <div class="col-md-4 text-left">
                        <span class="badge badge-success p-2" style="font-size: 0.78rem;"><i class="fa fa-shield"></i> محاسبه دقیق WAC</span>
                      </div>
                    </div>
                  </div>
                  <!-- Hidden Fields -->
                  <input type="hidden" id="material-af-total-price" name="total_af">
                  <input type="hidden" id="material-total-price" name="total">
                </div>
              </div>

              <!-- ACCOUNT OVERRIDES -->
              <div class="row mt-4" style="background: rgba(248, 250, 252, 0.75); padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0;">
                <div class="col-lg-12">
                  <h6 class="text-dark mb-3" style="font-weight: 700;"><i class="fa fa-university" style="color: #475569; margin-left: 8px;"></i>تنظیمات حسابی پیشرفته (GL Accounts override)</h6>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12">
                  <div class="form-group fill">
                    <label>حساب بدهکار (Debit Account) <span class="badge badge-info">{{ count($allowedDebitAccounts) }}</span></label>
                    <select name="override_debit_account_id" id="override_debit_account_id" class="form-control select2" style="width: 100%;" data-placeholder="انتخاب حساب گدام">
                      <option value=""></option>
                      @foreach($allowedDebitAccounts as $acc)
                        <option value="{{ $acc->id }}" {{ ($mapping && $mapping->debit_account_id == $acc->id) ? 'selected' : '' }}>
                          {{ $acc->account_code }} - {{ $acc->account_name }}
                        </option>
                      @endforeach
                    </select>
                    <span class="form-helper">تغییر حساب معین بدهکار (پیش‌فرض: دارایی گدام مواد خام).</span>
                  </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12">
                  <div class="form-group fill">
                    <label>حساب بستانکار (Credit Account) <span class="badge badge-info">{{ count($allowedCreditAccounts) }}</span></label>
                    <select name="override_credit_account_id" id="override_credit_account_id" class="form-control select2" style="width: 100%;" data-placeholder="انتخاب حساب تادیه">
                      <option value=""></option>
                      @foreach($allowedCreditAccounts as $acc)
                        <option value="{{ $acc->id }}" {{ ($mapping && $mapping->credit_account_id == $acc->id) ? 'selected' : '' }}>
                          {{ $acc->account_code }} - {{ $acc->account_name }}
                        </option>
                      @endforeach
                    </select>
                    <span class="form-helper">تغییر حساب معین بستانکار (پیش‌فرض: پرداختنی به فروشندگان مواد).</span>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">بستن انصراف</button>
              <button type="submit" class="btn btn-primary btn-sm"><span class="fa fa-save"></span> ثبت فاکتور</button>
            </div>
          </form>
        @else
          <!-- Edit Form -->
          <form action="/dashboard/material-purchase/{{$purchaseMaterial->id}}" method="post" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <input type="hidden" name="old_material_category" value="{{$purchaseMaterial->material_category}}">
            <input type="hidden" name="old_material_type" value="{{$purchaseMaterial->material_type}}">
            <input type="hidden" name="old_quantity" value="{{$purchaseMaterial->quantity}}">
            <input type="hidden" name="old_price_per_kilo" value="{{$purchaseMaterial->price_per_kilo}}">

            <div class="modal-body">
              <div class="row">
                <!-- Subtype selector -->
                <div class="col-lg-4 col-md-6 col-sm-12">
                  <div class="form-group fill">
                    <label class="text-primary" style="font-weight: 700;">نوعیت خرید (Purchase Subtype)</label>
                    <select name="purchase_subtype" id="purchase_subtype" class="form-control" style="border: 2px solid #3b82f6;">
                      <option value="yarn" {{ optional($purchaseMaterial->materialType)->subtype == 'yarn' ? 'selected' : '' }}>تار (Yarn)</option>
                      <option value="dye" {{ optional($purchaseMaterial->materialType)->subtype == 'dye' ? 'selected' : '' }}>رنگ (Dye)</option>
                    </select>
                    <span class="form-helper">نوعیت اصلی فاکتور خرید مواد خام.</span>
                  </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-12">
                  <div class="form-group fill">
                    <label>شماره فاکتور</label>
                    <input type="text" class="form-control" value="{{$purchaseMaterial->purchase_number}}" name="purchase_number" required>
                    <span class="form-helper">شماره مرجع فاکتور خرید مواد خام.</span>
                  </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-12">
                  <div class="form-group fill">
                    <label>فروشنده (Seller)</label>
                    <select name="seller_id" id="seller_id" class="form-control select2" style="width: 100%;" required>
                      <option value="">~~~</option>
                      @foreach($sellers as $s)
                        <option value="{{ $s->id }}" {{ $purchaseMaterial->seller_id == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                      @endforeach
                    </select>
                    <span class="form-helper">فروشنده فاکتور ویرایش شونده.</span>
                  </div>
                </div>
              </div>

              {{-- Purchase Bill row (edit) --}}
              <div class="row mt-3">
                <div class="col-lg-12">
                  <div class="form-group fill">
                    <label><i class="fa fa-file-text-o" style="color:#3b82f6;"></i> بل خرید مواد خام (Purchase Bill)
                      <a href="{{ route('raw-material-purchase-bills.create') }}" target="_blank"
                         style="font-size:.78rem; font-weight:600; margin-right:8px; color:#059669;">
                        <i class="fa fa-plus-circle"></i> ثبت بل جدید
                      </a>
                    </label>
                    <select name="raw_material_purchase_bill_id" id="purchase_bill_id_edit" class="form-control select2" style="width: 100%;">
                      <option value="">— بدون بل —</option>
                      @foreach($purchaseBills as $pb)
                        <option value="{{ $pb->id }}"
                                data-seller="{{ $pb->seller_id }}"
                                {{ $purchaseMaterial->raw_material_purchase_bill_id == $pb->id ? 'selected' : '' }}>
                          {{ $pb->bill_number }} — {{ optional($pb->seller)->name }} ({{ $pb->date }})
                        </option>
                      @endforeach
                    </select>
                    <span class="form-helper">بل خرید مرتبط با این فاکتور (اختیاری).</span>
                  </div>
                </div>
              </div>

              <div class="row mt-3">
                <div class="col-lg-4 col-md-6 col-sm-12">
                  <div class="form-group fill">
                    <label>ارز پرداخت (Currency)</label>
                    <select name="currency_id" id="currency_id" class="form-control select2" style="width: 100%;" required>
                      @foreach($currencies as $curr)
                        <option value="{{ $curr->id }}" data-rate="{{ $curr->exchange_rate }}" 
                                {{ $purchaseMaterial->currency_id == $curr->id ? 'selected' : ($curr->code == 'AFN' ? 'selected' : '') }}>
                          {{ $curr->code }} ({{ $curr->symbol }})
                        </option>
                      @endforeach
                    </select>
                    <span class="form-helper">ارز اصلی معامله خرید.</span>
                  </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-12">
                  <div class="form-group fill">
                    <label>نرخ تبادله به دالر (Exchange Rate)</label>
                    <input type="number" step="0.00000001" name="exchange_rate" id="exchange_rate" class="form-control bg-light" 
                           value="{{ $purchaseMaterial->exchange_rate ?? 0 }}" readonly>
                    <span class="form-helper">نرخ تبدیل ارز سند به ارز مرجع سیستم.</span>
                  </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-12">
                  <div class="form-group fill">
                    <label>تاریخ معامله (Date)</label>
                    <input name="purchase_date" type="date" class="form-control" value="{{ $purchaseMaterial->purchase_date }}" required>
                    <span class="form-helper">تاریخ رسمی ثبت فاکتور.</span>
                  </div>
                </div>
              </div>

              <div class="row mt-3">
                <div class="col-lg-4 col-md-6 col-sm-12">
                  <div class="form-group fill">
                    <label>کتگوری مواد (Category)</label>
                    <select name="material_category" id="material_category" class="form-control select2" style="width: 100%;" required>
                      <option value="">~~~</option>
                      @foreach($material_category as $mc)
                        <option {{ ($purchaseMaterial->material_category == $mc->material_category_id ? 'selected' : '') }} value="{{ $mc->material_category_id }}" data-subtype="{{ $mc->subtype }}">{{ $mc->material_category }}</option>
                      @endforeach
                    </select>
                    <span class="form-helper">دسته‌بندی کلی مواد.</span>
                  </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-12">
                  <div class="form-group fill">
                    <label>نوعیت مواد (Type)</label>
                    <select name="material_type" id="material_type" class="form-control select2" style="width: 100%;" required>
                      <option value="">~~~</option>
                      @foreach($material_type as $mt)
                        <option {{ ($purchaseMaterial->material_type == $mt->material_type_id ? 'selected' : '') }} value="{{ $mt->material_type_id }}" data-subtype="{{ $mt->subtype }}">{{ $mt->material_type }}</option>
                      @endforeach
                    </select>
                    <span class="form-helper">نوع دقیق کالا.</span>
                  </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-12">
                  <div class="form-group fill">
                    <label>گودام ذخیره (Warehouse)</label>
                    <select name="warehouse_id" id="warehouse_id" class="form-control select2" style="width: 100%;" required>
                      @foreach($warehouses as $w)
                        <option value="{{ $w->id }}" data-subtype="{{ $w->subtype }}" {{ $purchaseMaterial->warehouse_id == $w->id ? 'selected' : '' }}>{{ $w->name }}</option>
                      @endforeach
                    </select>
                    <span class="form-helper">گودامی مقصد جهت ذخیره‌سازی.</span>
                  </div>
                </div>
              </div>

              <div class="row mt-3">
                <div class="col-lg-4 col-md-6 col-sm-12">
                  <div class="form-group fill">
                    <label>مقدار خریداری شده (Weight KG)</label>
                    <input name="quantity" id="material-amount" type="number" step="0.01" value="{{ $purchaseMaterial->quantity }}" class="form-control" required>
                    <span class="form-helper">وزن وارده خالص به کیلوگرام.</span>
                  </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-12">
                  <div class="form-group fill">
                    <label>قیمت فی کیلوگرام (Price/KG)</label>
                    <input name="price_per_kilo" id="material-price" value="{{ $purchaseMaterial->price_per_kilo }}" type="number" step="0.0001" class="form-control" required>
                    <span class="form-helper">قیمت فی کیلو کالا.</span>
                  </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-12">
                  <div class="form-group fill">
                    <label>قیمت کل به حروف (In Words)</label>
                    <input type="text" class="form-control" name="in_words" value="{{ $purchaseMaterial->in_words }}" placeholder="قیمت به حروف" required>
                    <span class="form-helper">قیمت نهایی فاکتور به حروف فارسی.</span>
                  </div>
                </div>
              </div>

              <div class="row mt-3">
                <div class="col-lg-12">
                  <div class="form-group fill">
                    <label style="font-weight: 700; color: #334155;">تصویر بل خرید (Purchase Bill Image)</label>
                    @if($purchaseMaterial->purchase_bill)
                      <div class="mb-2">
                        <img src="{{ asset($purchaseMaterial->purchase_bill) }}" alt="بل فعلی" style="max-height: 120px; border-radius: 8px; border: 1px solid #cbd5e1; display: block;">
                        <span class="form-helper text-muted">تصویر بل فعلی در بالا نمایش داده شده است. برای تغییر، تصویر جدیدی انتخاب کنید.</span>
                      </div>
                    @endif
                    <input type="file" name="purchase_bill" class="form-control" accept="image/*" style="border: 2px dashed #cbd5e1; padding: 12px; height: auto; border-radius: 8px;">
                    <span class="form-helper">تصویر فاکتور خرید فیزیکی جدید (فرمت‌های مجاز: jpeg, png, jpg, gif, webp).</span>
                  </div>
                </div>
              </div>

              <div class="row mt-3">
                <div class="col-lg-12">
                  <div class="truth-preview-box" style="background: #f8fafc; padding: 16px; border-radius: 12px; border: 1px solid #e2e8f0; border-right: 5px solid #2563eb;">
                    <div class="row align-items-center">
                      <div class="col-md-4">
                        <span class="text-muted small"><i class="fa fa-calculator"></i> معادل دالر (USD Normalize):</span><br>
                        <span class="font-weight-bold text-primary" id="usd_truth_preview" style="font-size: 1.4rem; font-family: monospace;">$ {{ number_format($purchaseMaterial->total, 4) }}</span>
                      </div>
                      <div class="col-md-4 border-right" style="border-color: #cbd5e1 !important; padding-right: 20px;">
                        <span class="text-muted small">مجموع ارز اصلی (Total Original):</span><br>
                        <span class="text-dark font-weight-bold" id="original_total_preview" style="font-size: 1.2rem; font-family: monospace;">{{ number_format($purchaseMaterial->original_amount ?? ($purchaseMaterial->quantity * $purchaseMaterial->price_per_kilo), 2) }}</span>
                      </div>
                      <div class="col-md-4 text-left">
                        <span class="badge badge-success p-2" style="font-size: 0.78rem;"><i class="fa fa-shield"></i> محاسبه دقیق WAC</span>
                      </div>
                    </div>
                  </div>
                  <!-- Hidden Fields -->
                  <input type="hidden" id="material-af-total-price" name="total_af" value="{{ $purchaseMaterial->total_af }}">
                  <input type="hidden" id="material-total-price" name="total" value="{{ $purchaseMaterial->total }}">
                </div>
              </div>

              <!-- ACCOUNT OVERRIDES EDIT -->
              <div class="row mt-4" style="background: rgba(248, 250, 252, 0.75); padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0;">
                <div class="col-lg-12">
                  <h6 class="text-dark mb-3" style="font-weight: 700;"><i class="fa fa-university" style="color: #475569; margin-left: 8px;"></i>تنظیمات حسابی پیشرفته ویرایشی (GL Accounts override Edit)</h6>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12">
                  <div class="form-group fill">
                    <label>حساب بدهکار (Debit Account) <span class="badge badge-info">{{ count($allowedDebitAccounts) }}</span></label>
                    <select name="override_debit_account_id" class="form-control select2" style="width: 100%;" data-placeholder="انتخاب حساب گدام">
                      <option value=""></option>
                      <option value="">Standard Default</option>
                      @foreach($allowedDebitAccounts as $acc)
                        <option value="{{ $acc->id }}" {{ $purchaseMaterial->override_debit_account_id == $acc->id ? 'selected' : '' }}>
                          {{ $acc->account_code }} - {{ $acc->account_name }}
                        </option>
                      @endforeach
                    </select>
                    <span class="form-helper">تغییر حساب معین بدهکار.</span>
                  </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12">
                  <div class="form-group fill">
                    <label>حساب بستانکار (Credit Account) <span class="badge badge-info">{{ count($allowedCreditAccounts) }}</span></label>
                    <select name="override_credit_account_id" class="form-control select2" style="width: 100%;" data-placeholder="انتخاب حساب تادیه">
                      <option value=""></option>
                      <option value="">Standard Default</option>
                      @foreach($allowedCreditAccounts as $acc)
                        <option value="{{ $acc->id }}" {{ $purchaseMaterial->override_credit_account_id == $acc->id ? 'selected' : '' }}>
                          {{ $acc->account_code }} - {{ $acc->account_name }}
                        </option>
                      @endforeach
                    </select>
                    <span class="form-helper">تغییر حساب معین بستانکار.</span>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <a href="/dashboard/material-purchase" class="btn btn-secondary btn-sm">انصراف</a>
              <button type="submit" class="btn btn-primary btn-sm"><span class="fa fa-save"></span> ذخیره تغییرات</button>
            </div>
          </form>
        @endif
      </div>
    </div>
  </div>

@endsection

@section('scripts')
  <script>
    // Keep original options in memory for filtering
    const originalCategories = $('#material_category option').map(function() {
      return { value: this.value, text: this.text, subtype: $(this).data('subtype') };
    }).get();

    const originalTypes = $('#material_type option').map(function() {
      return { value: this.value, text: this.text, subtype: $(this).data('subtype') };
    }).get();

    const originalWarehouses = $('#warehouse_id option').map(function() {
      return { value: this.value, text: this.text, subtype: $(this).data('subtype') };
    }).get();

    function filterOptions(subtype) {
      // Category
      const catSelect = $('#material_category');
      const oldCatVal = catSelect.val();
      catSelect.empty().append('<option value="">~~~</option>');
      originalCategories.forEach(function(opt) {
        if (opt.value && (!opt.subtype || opt.subtype === subtype)) {
          catSelect.append($('<option>', { value: opt.value, text: opt.text, 'data-subtype': opt.subtype }));
        }
      });
      if (catSelect.find('option[value="' + oldCatVal + '"]').length > 0) {
        catSelect.val(oldCatVal);
      }
      catSelect.trigger('change.select2');

      // Type
      const typeSelect = $('#material_type');
      const oldTypeVal = typeSelect.val();
      typeSelect.empty().append('<option value="">~~~</option>');
      originalTypes.forEach(function(opt) {
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
      whSelect.empty();
      originalWarehouses.forEach(function(opt) {
        if (opt.value && (!opt.subtype || opt.subtype === subtype)) {
          whSelect.append($('<option>', { value: opt.value, text: opt.text, 'data-subtype': opt.subtype }));
        }
      });
      if (whSelect.find('option[value="' + oldWhVal + '"]').length > 0) {
        whSelect.val(oldWhVal);
      }
      whSelect.trigger('change.select2');
    }

    // Modal show triggers auto open for edit mode
    @if($purchaseMaterial)
      $('#purchaseModal').modal('show');
      // Trigger filtering based on edit model subtype on load
      filterOptions($('#purchase_subtype').val());
    @else
      // Trigger filtering based on default (yarn) on load
      filterOptions('yarn');
    @endif

    $('#purchase_subtype').on('change', function() {
      filterOptions($(this).val());
    });

    // Select2 init with modal compatibility
    $('#seller_id, #material_category, #material_type, #override_debit_account_id, #override_credit_account_id, #warehouse_id').select2({
      dropdownParent: $('#purchaseModal')
    });
    $('#purchase_bill_id_create, #purchase_bill_id_edit').select2({
      dropdownParent: $('#purchaseModal')
    });

    // Filter purchase bills by selected seller (CREATE form)
    function filterBillsBySeller(sellerId, selectId) {
      const $sel = $(selectId);
      const currentVal = $sel.val();
      $sel.find('option[value!=""]').each(function() {
        const optSeller = $(this).data('seller');
        if (!sellerId || String(optSeller) === String(sellerId)) {
          $(this).show();
        } else {
          $(this).hide();
          if ($(this).val() === currentVal) { $sel.val(''); }
        }
      });
      $sel.trigger('change.select2');
    }

    $('#seller_id').on('change', function() {
      const sellerId = $(this).val();
      filterBillsBySeller(sellerId, '#purchase_bill_id_create');
      filterBillsBySeller(sellerId, '#purchase_bill_id_edit');
    });

    // Run once on load to set initial filter state
    filterBillsBySeller($('#seller_id').val(), '#purchase_bill_id_create');
    filterBillsBySeller($('#seller_id').val(), '#purchase_bill_id_edit');

    // Flash status message
    $('.status').show();
    window.setTimeout(function() {
      $(".status").fadeTo(500, 0).slideUp(500, function() { $(this).remove(); });
    }, 2500);

    // LIVE FORENSIC CALCULATION
    $('#currency_id').on('change', function() {
      const rate = $(this).find(':selected').data('rate');
      const code = $(this).find(':selected').text().split('(')[0].trim();
      $('#exchange_rate').val(rate).removeAttr('readonly');
      Calculate();
    });

    function Calculate() {
      const qty       = parseFloat($('#material-amount').val()) || 0;
      const unitPrice = parseFloat($('#material-price').val()) || 0;
      const rate      = parseFloat($('#exchange_rate').val()) || 1;
      const code      = $('#currency_id option:selected').text().split('(')[0].trim();

      const originalTotal = qty * unitPrice;
      const baseUSD       = originalTotal * rate;

      // Update live preview
      $('#original_total_preview').html(
        '<strong>' + originalTotal.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) +
        '</strong> <span style="color:#7c3aed; font-weight:700;">' + code + '</span>'
      );
      $('#usd_truth_preview').text('$ ' + baseUSD.toLocaleString('en-US', {minimumFractionDigits: 4, maximumFractionDigits: 4}));

      // Update legacy hidden fields
      $('#material-total-price').val(baseUSD.toFixed(4));
      $('#material-af-total-price').val(originalTotal.toFixed(4));
    }

    $('#material-amount, #material-price, #exchange_rate').on('input', Calculate);
    Calculate();

    // Delete purchase confirmation
    function deletePurchase(id) {
      if (typeof swal === 'function') {
        swal({
          text: "آیا از حذف این رکورد مطمئن هستید؟",
          icon: "warning",
          buttons: { confirm: { text: 'بلی، حذف کن', className: 'btn-danger' }, cancel: 'انصراف' },
          dangerMode: true
        }).then(function(willDelete) {
          if (willDelete) {
            $.ajax({
              type: 'DELETE',
              url: '/dashboard/material-purchase/' + id,
              data: { '_token': '{{ csrf_token() }}' },
              success: function(res) {
                if (res.status === 'success') location.reload();
              }
            });
          }
        });
      }
    }
  </script>
@endsection
