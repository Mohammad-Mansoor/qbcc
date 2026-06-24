@extends('dsh.master')
@section('title', 'درخواست‌های پرداخت فروشنده مواد')
@section('content')

<div class="row">
  <div class="col-lg-12">

    <div class="card mt-3" style="border: none; box-shadow: 0 2px 12px rgba(0,0,0,0.08);">

      {{-- Premium Header --}}
      <div class="card-header" style="background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%); border-radius: 8px 8px 0 0; padding: 16px 24px;">
        <div class="d-flex align-items-center justify-content-between">
          <div>
            <h5 style="color: #fff; margin: 0; font-weight: 600; letter-spacing: 0.5px;">
              <i class="fa fa-handshake-o" style="color: #f59e0b; margin-left: 8px;"></i>
              درخواست‌های پرداخت فروشنده مواد در انتظار تایید
            </h5>
            <small style="color: #94a3b8;">String Seller Payment Requests — Pending Approval Queue</small>
          </div>
          <div class="d-flex align-items-center" style="gap: 10px;">
            <span style="background: rgba(245,158,11,0.15); color: #f59e0b; border: 1px solid rgba(245,158,11,0.3); padding: 6px 14px; border-radius: 20px; font-size: 0.8rem;">
              {{ $requests->count() }} درخواست
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
          <i class="fa fa-check-circle"></i> پرداخت موفقانه تایید شد و در دفتر روزنامچه ثبت گردید.
        </div>
        <div class="alert alert-danger deleteAlert mt-2 mb-0" style="display:none; border-radius: 6px;" role="alert">
          <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
          <i class="fa fa-times-circle"></i> درخواست رد و حذف گردید.
        </div>
        <div class="alert alert-warning errorAlert mt-2 mb-0" style="display:none; border-radius: 6px;" role="alert">
          <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
          <i class="fa fa-exclamation-triangle"></i> عملیه ناکام شد. دوباره کوشش کنید.
        </div>
      </div>

      {{-- Table --}}
      <div class="card-body p-0">
        <div class="table-responsive" id="MRDetails">
          <table class="table mb-0" style="font-size: 0.88rem;">
            <thead style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
              <tr style="color: #475569;">
                <th style="padding: 12px 16px; font-weight: 600;">نام فروشنده</th>
                <th style="padding: 12px 16px; font-weight: 600; text-align: right;">
                  باقیات فعلی
                  <small class="d-block" style="font-weight: 400; color: #94a3b8; font-size: 0.72rem;">Current Balance (USD)</small>
                </th>
                <th style="padding: 12px 16px; font-weight: 600; text-align: right; border-left: 2px solid #e2e8f0;">
                  <span style="color: #7c3aed;">مبلغ (ارز تراکنش)</span>
                  <small class="d-block" style="font-weight: 400; color: #94a3b8; font-size: 0.72rem;">Original Currency</small>
                </th>
                <th style="padding: 12px 16px; font-weight: 600; text-align: right;">
                  <span style="color: #059669;">معادل USD</span>
                  <small class="d-block" style="font-weight: 400; color: #94a3b8; font-size: 0.72rem;">نرخ × مبلغ</small>
                </th>
                <th style="padding: 12px 16px; font-weight: 600; text-align: center;">نوعیت</th>
                <th style="padding: 12px 16px; font-weight: 600;">فاکتور #</th>
                <th style="padding: 12px 16px; font-weight: 600; white-space: nowrap;">تاریخ</th>
                <th class="hideOnPrint" style="padding: 12px 16px; font-weight: 600; text-align: center;">
                  <i class="fa fa-university"></i> حسابات
                </th>
                <th class="hideOnPrint" style="padding: 12px 16px; font-weight: 600; text-align: center;">اقدام</th>
              </tr>
            </thead>
            <tbody>
            @forelse($requests as $r)
              @php
                $currCode  = $r->currency_code ?? 'USD';
                $exchRate  = $r->exchange_rate ?? 1;
                $origAmt   = $r->original_amount ?? ($r->amount > 0 ? $r->amount : $r->amount_af);
                $baseUSD   = $r->base_amount ?? ($origAmt / ($exchRate ?: 1));
                $isUSD     = ($currCode === 'USD');
                $isCredit  = ($r->type === 'رسید');
                $balance   = $r->current_balance ?? 0;
                $rowBg     = $loop->even ? '#fafafa' : '#ffffff';
              @endphp
              <tr class="ur{{ $r->id }}"
                  style="background: {{ $rowBg }}; border-bottom: 1px solid #f1f5f9;"
                  onmouseover="this.style.background='#fffbeb'" onmouseout="this.style.background='{{ $rowBg }}'">

                {{-- Seller Name --}}
                <td style="padding: 10px 16px; color: #1e293b; font-weight: 600;">
                  <i class="fa fa-user-circle" style="color: #f59e0b; margin-left: 4px;"></i>
                  {{ $r->seller_name ?? '—' }}
                </td>

                {{-- Current Balance (USD) --}}
                <td style="padding: 10px 16px; text-align: right;" dir="ltr">
                  @if($balance > 0)
                    <span style="color: #065f46; font-weight: 700; background: #d1fae5; padding: 3px 10px; border-radius: 20px; font-size: 0.82rem; border: 1px solid #6ee7b7;">
                      $ {{ number_format($balance, 2) }}
                    </span>
                  @elseif($balance < 0)
                    <span style="color: #991b1b; font-weight: 700; background: #fee2e2; padding: 3px 10px; border-radius: 20px; font-size: 0.82rem; border: 1px solid #fca5a5;">
                      $ {{ number_format($balance, 2) }}
                    </span>
                  @else
                    <span style="color: #94a3b8; font-size: 0.82rem;">$ 0.00</span>
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

                {{-- Type Badge --}}
                <td style="padding: 10px 16px; text-align: center;">
                  @if($isCredit)
                    <span style="background: #d1fae5; color: #065f46; padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 700; border: 1px solid #6ee7b7;">
                      <i class="fa fa-arrow-down"></i> {{ $r->type }}
                    </span>
                  @else
                    <span style="background: #dbeafe; color: #1e40af; padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 700; border: 1px solid #93c5fd;">
                      <i class="fa fa-arrow-up"></i> {{ $r->type }}
                    </span>
                  @endif
                </td>

                {{-- Purchase Number --}}
                <td style="padding: 10px 16px;">
                  @if($r->purchase_number)
                    <span style="font-weight: 700; color: #0891b2; font-family: monospace; font-size: 0.85rem;">
                      {{ $r->purchase_number }}
                    </span>
                  @else
                    <span style="color: #cbd5e1;">—</span>
                  @endif
                </td>

                {{-- Date --}}
                <td style="padding: 10px 16px; white-space: nowrap; color: #64748b; font-size: 0.85rem;">
                  {{ \Carbon\Carbon::parse($r->date)->format('d M Y') }}
                </td>

                {{-- Accounting Popover --}}
                <td class="hideOnPrint" style="padding: 10px 16px; text-align: center;">
                  <button class="btn btn-xs"
                          data-toggle="popover"
                          data-trigger="hover"
                          title="پیش‌نمایش حسابداری"
                          data-html="true"
                          data-content="
                            <div class='small' style='min-width:200px'>
                              <strong style='color:#3b82f6'>Debit:</strong> {{ $r->debit_account_name ?? 'Default' }}<br>
                              <strong style='color:#ef4444'>Credit:</strong> {{ $r->credit_account_name ?? 'Default' }}<br>
                              <hr class='my-1'>
                              <strong>Base (USD):</strong> \${{ number_format($baseUSD, 2) }}<br>
                              <strong>Rate:</strong> {{ number_format($exchRate, 4) }}
                            </div>
                          "
                          style="background: #eff6ff; color: #3b82f6; border: 1px solid #bfdbfe; padding: 4px 10px; border-radius: 6px; cursor: pointer;">
                    <i class="fa fa-university"></i>
                  </button>
                </td>

                {{-- Actions --}}
                <td class="hideOnPrint" style="padding: 10px 16px; text-align: center; white-space: nowrap;">
                  <div style="display: inline-flex; gap: 4px;">
                    @can('approve_seller_money_requests')
                    <button onclick="approveRequest({{ $r->id }})"
                            title="تایید درخواست"
                            style="background: #10b981; color: #fff; padding: 5px 12px; border-radius: 6px; font-size: 0.78rem; font-weight: 600; border: none; cursor: pointer;">
                      <i class="fa fa-check"></i> تایید
                    </button>
                    @endcan
                    @can('reject_seller_money_requests')
                    <button onclick="deleteRequest({{ $r->id }})"
                            title="رد درخواست"
                            style="background: #ef4444; color: #fff; padding: 5px 10px; border-radius: 6px; font-size: 0.78rem; font-weight: 600; border: none; cursor: pointer;">
                      <i class="fa fa-times"></i> رد
                    </button>
                    @endcan
                  </div>
                </td>

              </tr>
            @empty
              <tr>
                <td colspan="9" style="text-align: center; padding: 60px 20px; color: #94a3b8;">
                  <i class="fa fa-inbox" style="font-size: 2.5rem; display: block; margin-bottom: 12px; color: #cbd5e1;"></i>
                  <strong style="font-size: 1rem; color: #64748b;">هیچ درخواست پرداختی در انتظار تایید نیست</strong>
                  <p style="margin-top: 4px; font-size: 0.85rem;">تمام پرداخت‌های فروشندگان تایید یا رد شده‌اند.</p>
                </td>
              </tr>
            @endforelse
            </tbody>

            {{-- Summary Footer --}}
            @if($requests->count() > 0)
            @php
              $totalBaseUSD   = $requests->sum('base_amount');
              $totalCreditUSD = $requests->where('type', 'رسید')->sum('base_amount');
              $totalDebitUSD  = $requests->where('type', 'گرفت')->sum('base_amount');
            @endphp
            <tfoot style="background: #f8fafc; border-top: 2px solid #e2e8f0;">
              <tr>
                <td colspan="2" style="padding: 10px 16px; font-weight: 700; color: #475569; text-align: right;">
                  مجموع صفحه جاری:
                </td>
                <td style="padding: 10px 16px; font-weight: 700; color: #475569; text-align: right; border-left: 2px solid #ede9fe;" dir="ltr">
                  — mixed currencies —
                </td>
                <td style="padding: 10px 16px;" dir="ltr">
                  <div style="display: inline-flex; align-items: center; gap: 4px; background: #ecfdf5; padding: 5px 12px; border-radius: 8px; border: 1px solid #6ee7b7;">
                    <span style="color: #059669; font-weight: 600;">$</span>
                    <span style="font-weight: 800; color: #065f46;">{{ number_format($totalBaseUSD, 2) }}</span>
                  </div>
                </td>
                <td style="padding: 10px 16px; font-size: 0.8rem; color: #64748b;" dir="ltr">
                  <div>رسید: <strong style="color: #065f46;">$ {{ number_format($totalCreditUSD, 2) }}</strong></div>
                  <div>گرفت: <strong style="color: #991b1b;">$ {{ number_format($totalDebitUSD, 2) }}</strong></div>
                </td>
                <td colspan="4"></td>
              </tr>
            </tfoot>
            @endif
          </table>
        </div>
      </div>
    </div>

  </div>
</div>

@endsection

@section('scripts')
<script>
  $(function() {
    $('[data-toggle="popover"]').popover();
  });

  function approveRequest(id) {
    swal({
      text: "آیا از تایید این پرداخت مطمئن هستید؟ این عملیه در دفتر روزنامچه ثبت خواهد شد.",
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
          url: '/dashboard/string-seller-approve-request/' + id,
          success: function(res) {
            if (res.status === 'success') {
              $('.ur' + id).hide();
              $('.approve').show();
              setTimeout(function() { location.reload(); }, 1200);
            } else {
              $('.errorAlert').show();
            }
          },
          error: function() {
            $('.errorAlert').show();
          }
        });
      }
    });
  }

  function deleteRequest(id) {
    swal({
      text: "آیا از رد نمودن این درخواست پرداخت مطمئن هستید؟ این رکورد حذف خواهد شد.",
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
          url: '/dashboard/string-seller-delete-request/' + id,
          success: function(res) {
            $('.ur' + id).hide();
            $('.deleteAlert').show();
            setTimeout(function() { location.reload(); }, 1200);
          },
          error: function() {
            $('.errorAlert').show();
          }
        });
      }
    });
  }

  // Auto-hide alerts
  window.setTimeout(function() {
    $(".approve, .deleteAlert, .errorAlert").fadeTo(500, 0).slideUp(500, function() { $(this).remove(); });
  }, 4000);
</script>
@endsection