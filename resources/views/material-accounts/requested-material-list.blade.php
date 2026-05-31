@extends('dsh.master')
@section('title', 'درخواست‌های حساب مواد خام')
@section('content')

<div class="row">
  <div class="col-lg-12">

    <div class="card mt-3" style="border: none; box-shadow: 0 2px 12px rgba(0,0,0,0.08);">

      {{-- Premium Header --}}
      <div class="card-header" style="background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%); border-radius: 8px 8px 0 0; padding: 16px 24px;">
        <div class="d-flex align-items-center justify-content-between">
          <div>
            <h5 style="color: #fff; margin: 0; font-weight: 600; letter-spacing: 0.5px;">
              <i class="fa fa-cubes" style="color: #f59e0b; margin-left: 8px;"></i>
              درخواست‌های حساب مواد خام — در انتظار تایید
            </h5>
            <small style="color: #94a3b8;">Material Account Requests — Pending Approval Queue</small>
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
          <i class="fa fa-check-circle"></i> درخواست موفقانه تایید و در سیستم مالی ثبت گردید.
        </div>
        <div class="alert alert-danger deleteAlert mt-2 mb-0" style="display:none; border-radius: 6px;" role="alert">
          <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
          <i class="fa fa-times-circle"></i> درخواست رد و حذف گردید.
        </div>
      </div>

      {{-- Table --}}
      <div class="card-body p-0">
        <div class="table-responsive" id="MRDetails">
          <table class="table mb-0" style="font-size: 0.88rem;">
            <thead style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
              <tr style="color: #475569;">
                <th style="padding: 12px 16px; font-weight: 600;">نام حساب</th>
                <th style="padding: 12px 16px; font-weight: 600; text-align: center;">نوع معامله</th>
                <th style="padding: 12px 16px; font-weight: 600; text-align: center;">مقدار (KG)</th>
                <th style="padding: 12px 16px; font-weight: 600; text-align: right; border-left: 2px solid #e2e8f0;">
                  <span style="color: #7c3aed;">مبلغ (ارز تراکنش)</span>
                  <small class="d-block" style="font-weight: 400; color: #94a3b8; font-size: 0.72rem;">Original Currency</small>
                </th>
                <th style="padding: 12px 16px; font-weight: 600; text-align: right;">
                  <span style="color: #059669;">معادل USD</span>
                  <small class="d-block" style="font-weight: 400; color: #94a3b8; font-size: 0.72rem;">نرخ × مبلغ</small>
                </th>
                <th style="padding: 12px 16px; font-weight: 600;">گدام</th>
                <th style="padding: 12px 16px; font-weight: 600;">نوعیت مواد خام</th>
                <th style="padding: 12px 16px; font-weight: 600;">تفصیلات</th>
                <th style="padding: 12px 16px; font-weight: 600; white-space: nowrap;">تاریخ</th>
                <th class="hideOnPrint" style="padding: 12px 16px; font-weight: 600; text-align: center;">اقدام</th>
              </tr>
            </thead>
            <tbody>
            @forelse($requests as $r)
              @php
                $currCode  = $r->currency_code ?? 'AFN';
                $exchRate  = $r->exchange_rate ?? 1;
                $origAmt   = $r->original_amount ?? 0;
                $baseUSD   = $r->base_currency_amount ?? ($origAmt / ($exchRate ?: 1));
                $isUSD     = ($currCode === 'USD');
                $isCredit  = ($r->type === 'رسید');
                $rowBg     = $loop->even ? '#fafafa' : '#ffffff';
                $accountName = \App\MaterialAccount::find($r->account_id);
              @endphp
              <tr class="ur{{ $r->id }}"
                  style="background: {{ $rowBg }}; border-bottom: 1px solid #f1f5f9;"
                  onmouseover="this.style.background='#fffbeb'" onmouseout="this.style.background='{{ $rowBg }}'">

                {{-- Account Name --}}
                <td style="padding: 10px 16px; color: #1e293b; font-weight: 600;">
                  <i class="fa fa-folder-open" style="color: #f59e0b; margin-left: 4px;"></i>
                  {{ $accountName->name ?? '—' }}
                  <small class="d-block" style="color: #94a3b8; font-size: 0.75rem;">حساب #{{ $r->account_id }}</small>
                </td>

                {{-- Transaction Type --}}
                <td style="padding: 10px 16px; text-align: center;">
                  @if($isCredit)
                    <span style="background: #d1fae5; color: #065f46; padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 700; border: 1px solid #6ee7b7;">
                      <i class="fa fa-arrow-down"></i> رسید
                    </span>
                  @else
                    <span style="background: #fee2e2; color: #991b1b; padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 700; border: 1px solid #fca5a5;">
                      <i class="fa fa-arrow-up"></i> گرفت
                    </span>
                  @endif
                </td>

                {{-- Quantity KG --}}
                <td style="padding: 10px 16px; text-align: center;" dir="ltr">
                  <span style="font-weight: 700; color: #1e293b;">{{ number_format($r->amount, 2) }}</span>
                  <span style="color: #94a3b8; font-size: 0.78rem;"> KG</span>
                  @if($r->price > 0)
                    <small class="d-block" style="color: #94a3b8; font-size: 0.72rem;">@ {{ number_format($r->price, 2) }}/kg</small>
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

                {{-- Warehouse --}}
                <td style="padding: 10px 16px;">
                  <span style="background: #f0fdf4; color: #16a34a; padding: 2px 8px; border-radius: 10px; font-size: 0.78rem; border: 1px solid #bbf7d0;">
                    {{ $r->warehouse->name ?? 'گدام مرکزی' }}
                  </span>
                </td>

                {{-- Material Type --}}
                <td style="padding: 10px 16px;">
                  @if($r->materialtype)
                    <span style="background: #ede9fe; color: #7c3aed; padding: 2px 8px; border-radius: 10px; font-size: 0.78rem; font-weight: 600;">
                      {{ $r->materialtype->material_type }}
                    </span>
                  @else
                    <span style="color: #cbd5e1;">—</span>
                  @endif
                </td>

                {{-- Description --}}
                <td style="padding: 10px 16px; color: #475569; max-width: 200px;">
                  <span style="display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 180px;"
                        title="{{ $r->description }}">
                    {{ $r->description }}
                  </span>
                </td>

                {{-- Date --}}
                <td style="padding: 10px 16px; white-space: nowrap; color: #64748b; font-size: 0.85rem;">
                  {{ \Carbon\Carbon::parse($r->date)->format('d M Y') }}
                </td>

                {{-- Actions --}}
                <td class="hideOnPrint" style="padding: 10px 16px; text-align: center; white-space: nowrap;">
                  <div style="display: inline-flex; gap: 4px;">
                    <button onclick="approveRequest({{ $r->id }})"
                            title="تایید — ثبت در GL + انبار"
                            style="background: #10b981; color: #fff; padding: 5px 12px; border-radius: 6px; font-size: 0.78rem; font-weight: 600; border: none; cursor: pointer;">
                      <i class="fa fa-check"></i> تایید
                    </button>
                    <button onclick="deleteRequest({{ $r->id }})"
                            title="رد درخواست"
                            style="background: #ef4444; color: #fff; padding: 5px 10px; border-radius: 6px; font-size: 0.78rem; font-weight: 600; border: none; cursor: pointer;">
                      <i class="fa fa-times"></i> رد
                    </button>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="10" style="text-align: center; padding: 60px 20px; color: #94a3b8;">
                  <i class="fa fa-inbox" style="font-size: 2.5rem; display: block; margin-bottom: 12px; color: #cbd5e1;"></i>
                  <strong style="font-size: 1rem; color: #64748b;">هیچ درخواستی در انتظار تایید نیست</strong>
                  <p style="margin-top: 4px; font-size: 0.85rem;">تمام درخواست‌های مواد تایید یا رد شده‌اند.</p>
                </td>
              </tr>
            @endforelse
            </tbody>

            {{-- Summary Footer --}}
            @if($requests->count() > 0)
            @php
              $totalKgIn   = $requests->where('type', 'رسید')->sum('amount');
              $totalKgOut  = $requests->where('type', 'گرفت')->sum('amount');
              $totalBaseUSD = $requests->sum('base_currency_amount');
            @endphp
            <tfoot style="background: #f8fafc; border-top: 2px solid #e2e8f0;">
              <tr>
                <td colspan="2" style="padding: 10px 16px; font-weight: 700; color: #475569; text-align: right;">
                  مجموع:
                </td>
                <td style="padding: 10px 16px; text-align: center;" dir="ltr">
                  <div style="font-size: 0.8rem;">
                    <span style="color: #065f46; font-weight: 700;">رسید: {{ number_format($totalKgIn, 2) }} KG</span><br>
                    <span style="color: #991b1b; font-weight: 700;">گرفت: {{ number_format($totalKgOut, 2) }} KG</span>
                  </div>
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
                <td colspan="5"></td>
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
  function approveRequest(id) {
    swal({
      text: "آیا از تایید این درخواست مطمئن هستید؟ این عملیه در دفتر حساب و انبار ثبت خواهد شد.",
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
          url: '/dashboard/material-account-approve-request/' + id,
          success: function(res) {
            if (res.status === 'success') {
              $('.ur' + id).fadeOut(300);
              $('.approve').show();
              setTimeout(function() { location.reload(); }, 1200);
            }
          },
          error: function() {
            swal("خطا در انجام عملیات", { icon: "error" });
          }
        });
      }
    });
  }

  function deleteRequest(id) {
    swal({
      text: "آیا از رد نمودن این درخواست مطمئن هستید؟ این رکورد حذف خواهد شد.",
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
          url: '/dashboard/material-account-delete-request/' + id,
          success: function(res) {
            $('.ur' + id).fadeOut(300);
            $('.deleteAlert').show();
            setTimeout(function() { location.reload(); }, 1200);
          },
          error: function() {
            swal("خطا در انجام عملیات", { icon: "error" });
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