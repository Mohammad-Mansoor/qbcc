@extends('dsh.master')
@section('title', 'لیست درخواست‌های خرید تار')
@section('content')

<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

    {{-- Header Card --}}
    <div class="card mt-3" style="border: none; box-shadow: 0 2px 12px rgba(0,0,0,0.08);">
      <div class="card-header" style="background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%); border-radius: 8px 8px 0 0; padding: 16px 24px;">
        <div class="d-flex align-items-center justify-content-between">
          <div>
            <h5 style="color: #fff; margin: 0; font-weight: 600; letter-spacing: 0.5px;">
              <i class="fa fa-clock-o" style="color: #fbbf24; margin-left: 8px;"></i>
              درخواست‌های خرید در انتظار تایید
            </h5>
            <small style="color: #94a3b8;">Material Purchase Requests — Pending Approval Queue</small>
          </div>
          <div class="d-flex align-items-center" style="gap: 10px;">
            <span class="badge" style="background: rgba(251,191,36,0.15); color: #fbbf24; border: 1px solid rgba(251,191,36,0.3); padding: 6px 14px; border-radius: 20px; font-size: 0.8rem;">
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
          <i class="fa fa-check-circle"></i> درخواست موفقانه تایید شد و در سیستم مالی ثبت گردید.
        </div>
        <div class="alert alert-danger deleteAlert mt-2 mb-0" style="display:none; border-radius: 6px;" role="alert">
          <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
          <i class="fa fa-times-circle"></i> درخواست رد و حذف گردید.
        </div>
      </div>

      <div class="card-body p-0">
        <div class="table-responsive" id="MRDetails">
          <table class="table mb-0" style="font-size: 0.88rem;">
            <thead style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
              <tr style="color: #475569;">
                <th style="padding: 12px 16px; font-weight: 600; white-space: nowrap;">فاکتور #</th>
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
                  <small class="d-block" style="font-weight: 400; color: #94a3b8; font-size: 0.72rem;">نرخ × مبلغ</small>
                </th>
                <th style="padding: 12px 16px; font-weight: 600;">قیمت به حروف</th>
                <th class="hideOnPrint" style="padding: 12px 16px; font-weight: 600; text-align: center;">اقدام</th>
              </tr>
            </thead>
            <tbody>
            @forelse($requests as $p)
              @php
                $currCode = $p->currency_code ?? 'AFN';
                $exchRate = $p->exchange_rate ?? 1;
                $origAmt  = $p->original_amount ?? ($p->quantity * $p->price_per_kilo);
                $baseUSD  = $p->base_currency_amount ?? ($origAmt / ($exchRate ?: 1));
                $isUSD    = ($currCode === 'USD');
                $rowBg    = $loop->even ? '#fafafa' : '#ffffff';
              @endphp
              <tr style="background: {{ $rowBg }}; border-bottom: 1px solid #f1f5f9;"
                  onmouseover="this.style.background='#fff7ed'" onmouseout="this.style.background='{{ $rowBg }}'">

                {{-- PO Number --}}
                <td style="padding: 10px 16px; white-space: nowrap;">
                  <span style="font-weight: 700; color: #f59e0b; font-family: monospace; font-size: 0.85rem;">
                    {{ $p->purchase_number }}
                  </span>
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

                {{-- Unit Price --}}
                <td style="padding: 10px 16px; text-align: right;" dir="ltr">
                  <span style="font-weight: 600; color: #374151;">{{ number_format($p->price_per_kilo, 2) }}</span>
                  <span style="color: #94a3b8; font-size: 0.75rem; margin-right: 3px;">{{ $currCode }}</span>
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
                    <span style="font-weight: 700; color: #065f46; font-size: 0.95rem;">
                      {{ number_format($baseUSD, 2) }}
                    </span>
                  </div>
                </td>

                {{-- In Words --}}
                <td style="padding: 10px 16px; color: #64748b; font-size: 0.82rem; max-width: 160px;">
                  {{ $p->in_words }}
                </td>

                {{-- Actions --}}
                <td class="hideOnPrint" style="padding: 10px 16px; text-align: center; white-space: nowrap;">
                  <button onclick="approveRequest({{ $p->id }})"
                          class="btn btn-xs"
                          style="background: #10b981; color: #fff; padding: 5px 12px; border-radius: 6px; font-size: 0.78rem; font-weight: 600; border: none; cursor: pointer; margin-bottom: 4px;">
                    <i class="fa fa-check"></i> تایید
                  </button>
                  <button onclick="deleteRequest({{ $p->id }})"
                          class="btn btn-xs"
                          style="background: #ef4444; color: #fff; padding: 5px 10px; border-radius: 6px; font-size: 0.78rem; font-weight: 600; border: none; cursor: pointer;">
                    <i class="fa fa-times"></i> رد
                  </button>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="11" style="text-align: center; padding: 60px 20px; color: #94a3b8;">
                  <i class="fa fa-inbox" style="font-size: 2.5rem; display: block; margin-bottom: 12px; color: #cbd5e1;"></i>
                  <strong style="font-size: 1rem; color: #64748b;">هیچ درخواستی در انتظار تایید نیست</strong>
                  <p style="margin-top: 4px; font-size: 0.85rem;">تمام درخواست‌های خرید تایید یا رد شده‌اند.</p>
                </td>
              </tr>
            @endforelse
            </tbody>

            {{-- Summary Footer --}}
            @if($requests->count() > 0)
            <tfoot style="background: #f8fafc; border-top: 2px solid #e2e8f0;">
              <tr>
                <td colspan="4" style="padding: 10px 16px; font-weight: 700; color: #475569; text-align: right;">
                  مجموع صفحه جاری:
                </td>
                <td></td>
                <td style="padding: 10px 16px; font-weight: 700; color: #1e293b; text-align: center;" dir="ltr">
                  {{ number_format($requests->sum('quantity'), 2) }} KG
                </td>
                <td></td>
                <td style="padding: 10px 16px; font-weight: 700; color: #7c3aed; text-align: right;" dir="ltr">
                  — mixed currencies —
                </td>
                <td style="padding: 10px 16px; text-align: right;" dir="ltr">
                  <div style="display: inline-flex; align-items: center; gap: 4px; background: #ecfdf5; padding: 5px 12px; border-radius: 8px; border: 1px solid #6ee7b7;">
                    <span style="color: #059669; font-weight: 600;">$</span>
                    <span style="font-weight: 800; color: #065f46;">
                      {{ number_format($requests->sum('base_currency_amount'), 2) }}
                    </span>
                  </div>
                </td>
                <td colspan="2"></td>
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

  function approveRequest(id) {
    swal({
      text: "آیا از تایید این درخواست مطمئن هستید؟ این عملیه در سیستم مالی ثبت خواهد شد.",
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
          url: '/dashboard/purchase-material-approve-request/' + id,
          success: function(res) {
            if (res.status === 'success') {
              $('.approve').show();
              setTimeout(function() { location.reload(); }, 1200);
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
          url: '/dashboard/purchase-material-delete-request/' + id,
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