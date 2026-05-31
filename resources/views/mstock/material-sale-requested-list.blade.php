@extends('dsh.master')
@section('title', 'درخواست‌های فروش مواد')
@section('content')

<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

    <div class="card mt-3" style="border: none; box-shadow: 0 2px 12px rgba(0,0,0,0.08);">

      {{-- Premium Header --}}
      <div class="card-header" style="background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%); border-radius: 8px 8px 0 0; padding: 16px 24px;">
        <div class="d-flex align-items-center justify-content-between">
          <div>
            <h5 style="color: #fff; margin: 0; font-weight: 600; letter-spacing: 0.5px;">
              <i class="fa fa-shopping-cart" style="color: #34d399; margin-left: 8px;"></i>
              درخواست‌های فروش مواد در انتظار تایید
            </h5>
            <small style="color: #94a3b8;">Material Sale Requests — Pending Approval Queue</small>
          </div>
          <div class="d-flex align-items-center" style="gap: 10px;">
            <span class="badge" style="background: rgba(52,211,153,0.15); color: #34d399; border: 1px solid rgba(52,211,153,0.3); padding: 6px 14px; border-radius: 20px; font-size: 0.8rem;">
              {{ $requests->total() }} درخواست
            </span>
            <div onclick="printPage('MRDetails')"
                 style="cursor: pointer; background: rgba(255,255,255,0.1); color: #e2e8f0; padding: 6px 14px; border-radius: 8px; font-size: 0.82rem; border: 1px solid rgba(255,255,255,0.15);">
              <i class="fa fa-print"></i> پرنت
            </div>
          </div>
        </div>

        {{-- Flash Alerts --}}
        <div class="alert alert-success approve mt-2 mb-0" style="display:none; border-radius: 6px;" role="alert">
          <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
          <i class="fa fa-check-circle"></i> درخواست فروش موفقانه تایید شد و در سیستم مالی ثبت گردید.
        </div>
        <div class="alert alert-danger deleteAlert mt-2 mb-0" style="display:none; border-radius: 6px;" role="alert">
          <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
          <i class="fa fa-times-circle"></i> درخواست فروش رد و حذف گردید.
        </div>
      </div>

      {{-- Table --}}
      <div class="card-body p-0">
        <div class="table-responsive" id="MRDetails">
          <table class="table mb-0" style="font-size: 0.88rem;">
            <thead style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
              <tr style="color: #475569;">
                <th style="padding: 12px 16px; font-weight: 600; white-space: nowrap;">فاکتور #</th>
                <th style="padding: 12px 16px; font-weight: 600; white-space: nowrap;">نوعیت فروش</th>
                <th style="padding: 12px 16px; font-weight: 600;">نماینده</th>
                <th style="padding: 12px 16px; font-weight: 600;">گدام</th>
                <th style="padding: 12px 16px; font-weight: 600; text-align: center;">مقدار (KG)</th>
                <th style="padding: 12px 16px; font-weight: 600; text-align: center;">موجودی</th>
                <th style="padding: 12px 16px; font-weight: 600; text-align: right; border-left: 2px solid #e2e8f0;">
                  <span style="color: #7c3aed;">مبلغ (ارز تراکنش)</span>
                  <small class="d-block" style="font-weight: 400; color: #94a3b8; font-size: 0.72rem;">Original Currency</small>
                </th>
                <th style="padding: 12px 16px; font-weight: 600; text-align: right;">
                  <span style="color: #059669;">معادل USD</span>
                  <small class="d-block" style="font-weight: 400; color: #94a3b8; font-size: 0.72rem;">نرخ × مبلغ</small>
                </th>
                <th style="padding: 12px 16px; font-weight: 600; text-align: right;">
                  <span style="color: #059669;">مفاد تخمینی</span>
                  <small class="d-block" style="font-weight: 400; color: #94a3b8; font-size: 0.72rem;">قیمت − WAC</small>
                </th>
                <th style="padding: 12px 16px; font-weight: 600;">نوعیت مواد</th>
                <th style="padding: 12px 16px; font-weight: 600; white-space: nowrap;">تاریخ</th>
                <th style="padding: 12px 16px; font-weight: 600; text-align: center;">
                  <i class="fa fa-university"></i> حسابات
                </th>
                <th class="hideOnPrint" style="padding: 12px 16px; font-weight: 600; text-align: center;">اقدام</th>
              </tr>
            </thead>
            <tbody>
            @forelse($requests as $material)
              @php
                $currCode   = $material->currency_code ?? 'AFN';
                $exchRate   = $material->exchange_rate ?? 1;
                $origAmt    = $material->original_amount ?? $material->total_price_af;
                $baseUSD    = $material->base_currency_amount ?? ($origAmt / ($exchRate ?: 1));
                $isUSD      = ($currCode === 'USD');
                $profit     = $material->total_price_af - ($material->amount * $material->estimated_wac);
                $stockOk    = ($material->available_stock >= $material->amount);
                $rowBg      = $loop->even ? '#fafafa' : '#ffffff';
                $hoverBg    = $stockOk ? '#f0fdf4' : '#fff5f5';
                
                // Account mappings resolution
                $revDr = $material->debitAccount->account_name ?? ($revenueMapping->debitAccount->account_name ?? 'Default Debit');
                $revCr = $material->creditAccount->account_name ?? ($revenueMapping->creditAccount->account_name ?? 'Default Credit');
                $cogsDr = $material->cogsDebitAccount->account_name ?? ($cogsMapping->debitAccount->account_name ?? 'Default COGS Debit');
                $cogsCr = $material->cogsCreditAccount->account_name ?? ($cogsMapping->creditAccount->account_name ?? 'Default COGS Credit');
              @endphp
              <tr style="background: {{ $rowBg }}; border-bottom: 1px solid #f1f5f9;"
                  onmouseover="this.style.background='{{ $hoverBg }}'" onmouseout="this.style.background='{{ $rowBg }}'">
 
                {{-- Sale Number --}}
                <td style="padding: 10px 16px; white-space: nowrap;">
                  <span style="font-weight: 700; color: #0891b2; font-family: monospace; font-size: 0.85rem; display: block; margin-bottom: 2px;">
                    {{ $material->sale_number }}
                  </span>
                  <button class="btn btn-link p-0 text-info"
                          data-toggle="popover"
                          data-trigger="hover"
                          title="جزئیات فاکتور / Invoice Details"
                          data-html="true"
                          data-content="
                            <div class='small' style='min-width:200px; direction:rtl; text-align:right;'>
                              <strong>قیمت فی کیلو:</strong> {{ number_format($material->price, 2) }} {{ $currCode }}<br>
                              <strong>مبلغ کل:</strong> {{ number_format($origAmt, 2) }} {{ $currCode }}<br>
                              <strong>نرخ اسعار:</strong> {{ number_format($exchRate, 4) }}<br>
                              <strong>معادل دالر:</strong> ${{ number_format($baseUSD, 2) }}<br>
                              <hr class='my-1'>
                              <strong>گدام:</strong> {{ $material->warehouse->name ?? 'Default' }}
                            </div>
                          "
                          style="font-size: 0.75rem; text-decoration: none; display: inline-flex; align-items: center; gap: 3px;">
                    <i class="fa fa-info-circle"></i> جزئیات (Details)
                  </button>
                </td>

                {{-- Sale Subtype (Yarn/Dye) --}}
                <td style="padding: 10px 16px; white-space: nowrap;">
                  @if(($material->category->subtype ?? $material->type->subtype) === 'dye')
                    <span class="badge badge-warning text-white" style="font-weight: 600; padding: 4px 8px; background-color: #f59e0b;">رنگ (Dye)</span>
                  @else
                    <span class="badge badge-primary" style="font-weight: 600; padding: 4px 8px;">نخ (Yarn)</span>
                  @endif
                </td>
 
                {{-- Agent --}}
                <td style="padding: 10px 16px; color: #1e293b; font-weight: 500;">
                  {{ $material->agent->user->name ?? '—' }}
                </td>
 
                {{-- Warehouse --}}
                <td style="padding: 10px 16px;">
                  <span style="background: #f0fdf4; color: #16a34a; padding: 2px 8px; border-radius: 10px; font-size: 0.78rem; border: 1px solid #bbf7d0; display: block; margin-bottom: 2px; text-align: center;">
                    {{ $material->warehouse->name ?? 'گدام مرکزی' }}
                  </span>
                  @if($material->warehouse && $material->warehouse->subtype)
                    <span class="badge text-white" style="font-size: 0.7rem; padding: 2px 6px; background-color: {{ $material->warehouse->subtype === 'yarn' ? '#3b82f6' : '#f59e0b' }}; display: block; margin: 0 auto; width: fit-content;">
                      {{ ucfirst($material->warehouse->subtype) }}
                    </span>
                  @endif
                </td>
 
                {{-- Amount --}}
                <td style="padding: 10px 16px; text-align: center;" dir="ltr">
                  <span style="font-weight: 700; color: #1e293b;">{{ number_format($material->amount, 2) }}</span>
                  <span style="color: #94a3b8; font-size: 0.78rem;"> KG</span>
                </td>
 
                {{-- Available Stock --}}
                <td style="padding: 10px 16px; text-align: center;" dir="ltr">
                  @if($stockOk)
                    <span style="background: #d1fae5; color: #065f46; padding: 3px 10px; border-radius: 20px; font-size: 0.78rem; font-weight: 600; border: 1px solid #6ee7b7;">
                      <i class="fa fa-check"></i> {{ number_format($material->available_stock, 1) }} KG
                    </span>
                  @else
                    <span style="background: #fee2e2; color: #991b1b; padding: 3px 10px; border-radius: 20px; font-size: 0.78rem; font-weight: 600; border: 1px solid #fca5a5;">
                      <i class="fa fa-warning"></i> {{ number_format($material->available_stock, 1) }} KG
                    </span>
                  @endif
                </td>
 
                {{-- Original Amount in transaction currency --}}
                <td style="padding: 10px 16px; text-align: right; border-left: 2px solid #ede9fe;" dir="ltr">
                  <div style="display: flex; flex-direction: column; align-items: flex-end;">
                    <span style="font-weight: 700; color: #7c3aed; font-size: 1rem;">
                      {{ number_format($origAmt, 2) }}
                      <span style="font-size: 0.78rem; font-weight: 600; opacity: 0.75;">{{ $currCode }}</span>
                    </span>
                    @if(!$isUSD)
                      <small style="color: #94a3b8; font-size: 0.72rem;">
                        نرخ: <span style="color: #7c3aed; font-weight: 600;">{{ number_format($exchRate, 4) }}</span>
                      </small>
                    @endif
                  </div>
                </td>
 
                {{-- USD Equivalent --}}
                <td style="padding: 10px 16px; text-align: right;" dir="ltr">
                  <div style="display: inline-flex; align-items: center; gap: 4px; background: #ecfdf5; padding: 4px 10px; border-radius: 8px; border: 1px solid #a7f3d0;">
                    <span style="color: #059669; font-size: 0.8rem; font-weight: 600;">$</span>
                    <span style="font-weight: 700; color: #065f46; font-size: 0.95rem;">{{ number_format($baseUSD, 2) }}</span>
                  </div>
                </td>
 
                {{-- Estimated Profit --}}
                <td style="padding: 10px 16px; text-align: right;" dir="ltr">
                  <div style="display: inline-flex; flex-direction: column; align-items: flex-end;">
                    <span style="font-weight: 700; color: {{ $profit >= 0 ? '#065f46' : '#991b1b' }}; font-size: 0.95rem;
                                 background: {{ $profit >= 0 ? '#ecfdf5' : '#fee2e2' }}; padding: 3px 10px;
                                 border-radius: 8px; border: 1px solid {{ $profit >= 0 ? '#a7f3d0' : '#fca5a5' }};">
                      {{ $profit >= 0 ? '+' : '' }}{{ number_format($profit, 2) }} AF
                    </span>
                    <small style="color: #94a3b8; font-size: 0.72rem; margin-top: 2px;">
                      WAC: {{ number_format($material->estimated_wac, 2) }}
                    </small>
                  </div>
                </td>
 
                {{-- Category & Type --}}
                <td style="padding: 10px 16px;">
                  <span style="background: #ede9fe; color: #7c3aed; padding: 2px 8px; border-radius: 10px; font-size: 0.78rem; font-weight: 600; display: block; margin-bottom: 2px;">
                    {{ $material->type->material_type ?? '—' }}
                  </span>
                  <small style="color: #94a3b8;">{{ $material->category->material_category ?? '—' }}</small>
                </td>
 
                {{-- Date --}}
                <td style="padding: 10px 16px; white-space: nowrap; color: #64748b; font-size: 0.85rem;">
                  {{ \Carbon\Carbon::parse($material->date)->format('d M Y') }}
                </td>
 
                {{-- Accounting Popover --}}
                <td style="padding: 10px 16px; text-align: center;">
                  <button class="btn btn-xs"
                          data-toggle="popover"
                          data-trigger="hover"
                          title="Accounting Mappings"
                          data-html="true"
                          data-content="
                            <div class='small' style='min-width:250px; direction:ltr; text-align:left;'>
                              <strong style='color:#3b82f6'>Revenue Dr (Receivable):</strong><br> {{$revDr}}<br>
                              <strong style='color:#3b82f6'>Revenue Cr (Revenue):</strong><br> {{$revCr}}<br>
                              <hr class='my-1'>
                              <strong style='color:#ef4444'>COGS Dr (Expense):</strong><br> {{$cogsDr}}<br>
                              <strong style='color:#ef4444'>COGS Cr (Inventory):</strong><br> {{$cogsCr}}
                            </div>
                          "
                          style="background: #eff6ff; color: #3b82f6; border: 1px solid #bfdbfe; padding: 4px 10px; border-radius: 6px; cursor: pointer;">
                    <i class="fa fa-university"></i>
                  </button>
                </td>
 
                {{-- Actions --}}
                <td class="hideOnPrint" style="padding: 10px 16px; text-align: center; white-space: nowrap;">
                  <div class="btn-group" style="gap: 4px; display: inline-flex;">
                    <button onclick="approveRequest({{ $material->id }})"
                            title="تایید درخواست"
                            style="background: #10b981; color: #fff; padding: 5px 12px; border-radius: 6px; font-size: 0.78rem; font-weight: 600; border: none; cursor: pointer;">
                      <i class="fa fa-check"></i> تایید
                    </button>
                    <button onclick="deleteRequest({{ $material->id }})"
                            title="رد درخواست"
                            style="background: #ef4444; color: #fff; padding: 5px 10px; border-radius: 6px; font-size: 0.78rem; font-weight: 600; border: none; cursor: pointer;">
                      <i class="fa fa-times"></i> رد
                    </button>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="13" style="text-align: center; padding: 60px 20px; color: #94a3b8;">
                  <i class="fa fa-inbox" style="font-size: 2.5rem; display: block; margin-bottom: 12px; color: #cbd5e1;"></i>
                  <strong style="font-size: 1rem; color: #64748b;">هیچ درخواست فروشی در انتظار تایید نیست</strong>
                  <p style="margin-top: 4px; font-size: 0.85rem;">تمام درخواست‌های فروش مواد تایید یا رد شده‌اند.</p>
                </td>
              </tr>
            @endforelse
            </tbody>
 
            {{-- Summary Footer --}}
            @if($requests->count() > 0)
            @php
              $pageItems    = $requests->getCollection();
              $totalQty     = $pageItems->sum('amount');
              $totalBaseUSD = $pageItems->sum('base_currency_amount');
              $totalProfit  = $pageItems->sum(fn($r) => $r->total_price_af - ($r->amount * $r->estimated_wac));
            @endphp
            <tfoot style="background: #f8fafc; border-top: 2px solid #e2e8f0;">
              <tr>
                <td colspan="3" style="padding: 10px 16px; font-weight: 700; color: #475569; text-align: right;">
                  مجموع صفحه جاری:
                </td>
                <td></td>
                <td style="padding: 10px 16px; font-weight: 700; color: #1e293b; text-align: center;" dir="ltr">
                  {{ number_format($totalQty, 2) }} KG
                </td>
                <td></td>
                <td style="padding: 10px 16px; font-weight: 700; color: #7c3aed; text-align: right; border-left: 2px solid #ede9fe;" dir="ltr">
                  — mixed currencies —
                </td>
                <td style="padding: 10px 16px; text-align: right;" dir="ltr">
                  <div style="display: inline-flex; align-items: center; gap: 4px; background: #ecfdf5; padding: 5px 12px; border-radius: 8px; border: 1px solid #6ee7b7;">
                    <span style="color: #059669; font-weight: 600;">$</span>
                    <span style="font-weight: 800; color: #065f46;">{{ number_format($totalBaseUSD, 2) }}</span>
                  </div>
                </td>
                <td style="padding: 10px 16px; text-align: right;" dir="ltr">
                  <span style="font-weight: 800; color: {{ $totalProfit >= 0 ? '#065f46' : '#991b1b' }};">
                    {{ $totalProfit >= 0 ? '+' : '' }}{{ number_format($totalProfit, 2) }} AF
                  </span>
                </td>
                <td colspan="4"></td>
              </tr>
            </tfoot>
            @endif
          </table>
        </div>

        {{-- Pagination --}}
        <div style="padding: 16px 20px; border-top: 1px solid #f1f5f9; background: #fafafa; border-radius: 0 0 8px 8px;">
          {{ $requests->links() }}
        </div>
      </div>
    </div>

  </div>
</div>

@endsection

@section('scripts')
<script>
  // Init popovers
  $(document).ready(function() {
    $('[data-toggle="popover"]').popover();
  });

  function approveRequest(id) {
    swal({
      text: "آیا از تایید این درخواست فروش مطمئن هستید؟ این عملیه در سیستم مالی ثبت خواهد شد.",
      icon: "warning",
      buttons: {
        confirm: { text: 'بلی، تایید کن', className: 'btn-success' },
        cancel: 'انصراف'
      },
      dangerMode: false
    }).then(function(willApprove) {
      if (willApprove) {
        $.ajax({
          type: 'DELETE',
          data: { '_token': '{{ csrf_token() }}' },
          url: '/dashboard/material-sale-approve-request/' + id,
          success: function(res) {
            if (res.status === 'success') {
              $('.approve').show();
              setTimeout(function() { location.reload(); }, 1200);
            } else {
              swal('خطا', 'عملیه ناکام شد.', 'error');
            }
          },
          error: function() {
            swal('خطا', 'عملیه ناکام شد. دوباره کوشش کنید.', 'error');
          }
        });
      }
    });
  }

  function deleteRequest(id) {
    swal({
      text: "آیا از رد نمودن این درخواست فروش مطمئن هستید؟ این رکورد حذف خواهد شد.",
      icon: "warning",
      buttons: {
        confirm: { text: 'بلی، رد کن', className: 'btn-danger' },
        cancel: 'انصراف'
      },
      dangerMode: true
    }).then(function(willDelete) {
      if (willDelete) {
        $.ajax({
          type: 'DELETE',
          data: { '_token': '{{ csrf_token() }}' },
          url: '/dashboard/material-sale-delete-request/' + id,
          success: function(res) {
            $('.deleteAlert').show();
            setTimeout(function() { location.reload(); }, 1200);
          },
          error: function() {
            swal('خطا', 'عملیه ناکام شد. دوباره کوشش کنید.', 'error');
          }
        });
      }
    });
  }

  // Auto-hide alerts
  window.setTimeout(function() {
    $(".approve, .deleteAlert").fadeTo(500, 0).slideUp(500, function() { $(this).remove(); });
  }, 4000);
</script>
@endsection