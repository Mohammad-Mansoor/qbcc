@extends('dsh.master')
@section('title', 'درخواست‌های معاش کارمندان')
@section('content')

<div class="row">
  <div class="col-lg-12">

    <div class="card mt-3" style="border: none; box-shadow: 0 2px 14px rgba(0,0,0,0.1);">

      {{-- Premium Header --}}
      <div class="card-header" style="background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 100%); border-radius: 8px 8px 0 0; padding: 16px 24px;">
        <div class="d-flex align-items-center justify-content-between">
          <div>
            <h5 style="color: #fff; margin: 0; font-weight: 700; letter-spacing: 0.5px;">
              <i class="fa fa-users" style="color: #f59e0b; margin-left: 8px;"></i>
              درخواست‌های معاش کارمندان — در انتظار تایید
            </h5>
            <small style="color: #94a3b8;">Employee Salary Payment Requests — Pending Approval Queue</small>
          </div>
          <div class="d-flex align-items-center" style="gap: 10px;">
            <span style="background: rgba(245,158,11,0.2); color: #f59e0b; border: 1px solid rgba(245,158,11,0.4); padding: 6px 14px; border-radius: 20px; font-size: 0.8rem; font-weight: 700;">
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
          <i class="fa fa-check-circle"></i> درخواست موفقانه تایید و در دفتر حساب ثبت گردید.
        </div>
        <div class="alert alert-danger deleteAlert mt-2 mb-0" style="display:none; border-radius: 6px;" role="alert">
          <i class="fa fa-times-circle"></i> درخواست رد و حذف گردید.
        </div>
        <div class="alert alert-warning errorAlert mt-2 mb-0" style="display:none; border-radius: 6px;" role="alert">
          <i class="fa fa-exclamation-triangle"></i> خطا در پردازش درخواست.
        </div>
      </div>

      {{-- Table --}}
      <div class="card-body p-0">
        <div class="table-responsive" id="MRDetails">
          <table class="table mb-0" style="font-size: 0.87rem;">
            <thead style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
              <tr style="color: #475569;">
                <th style="padding: 12px 16px; font-weight: 600;">کارمند</th>
                <th style="padding: 12px 16px; font-weight: 600; text-align: center;">نوع</th>
                <th style="padding: 12px 16px; font-weight: 600; text-align: right; border-left: 2px solid #ede9fe;">
                  <span style="color: #7c3aed;">مبلغ (ارز تراکنش)</span>
                  <small class="d-block" style="font-weight: 400; color: #94a3b8; font-size: 0.71rem;">Original Currency</small>
                </th>
                <th style="padding: 12px 16px; font-weight: 600; text-align: right;">
                  <span style="color: #059669;">معادل USD</span>
                  <small class="d-block" style="font-weight: 400; color: #94a3b8; font-size: 0.71rem;">نرخ FX × مبلغ</small>
                </th>
                <th style="padding: 12px 16px; font-weight: 600;">نرخ دالر</th>
                <th style="padding: 12px 16px; font-weight: 600;">قرارداد</th>
                <th style="padding: 12px 16px; font-weight: 600;">توضیحات</th>
                <th style="padding: 12px 16px; font-weight: 600; white-space: nowrap;">تاریخ</th>
                <th class="hideOnPrint" style="padding: 12px 16px; font-weight: 600; text-align: center;">اقدام</th>
              </tr>
            </thead>
            <tbody>
            @forelse($requests as $r)
              @php
                $currCode = $r->currency_code ?? ($r->amount > 0 ? 'USD' : 'AFN');
                $exchRate = $r->exchange_rate ?? $r->dollar_rate ?? 1;
                $origAmt  = $r->original_amount ?? ($r->amount > 0 ? $r->amount : $r->amount_af);
                $baseUSD  = $r->base_currency_amount ?? ($r->amount > 0 ? $r->amount : 0);
                $isCredit = ($r->type === 'رسید');
                $rowBg    = $loop->even ? '#fafafa' : '#ffffff';
              @endphp
              <tr class="ur{{ $r->id }}"
                  style="background: {{ $rowBg }}; border-bottom: 1px solid #f1f5f9;"
                  onmouseover="this.style.background='#fffbeb'" onmouseout="this.style.background='{{ $rowBg }}'">

                {{-- Employee Name --}}
                <td style="padding: 10px 16px; color: #1e293b; font-weight: 600;">
                  <i class="fa fa-user-circle" style="color: #7c3aed; margin-left: 4px;"></i>
                  {{ $r->employee->name ?? '—' }}
                  <small class="d-block" style="color: #94a3b8; font-size: 0.74rem;">کارمند #{{ $r->employee_id }}</small>
                </td>

                {{-- Type --}}
                <td style="padding: 10px 16px; text-align: center;">
                  @if($isCredit)
                    <span style="background: #d1fae5; color: #065f46; padding: 4px 12px; border-radius: 20px; font-size: 0.78rem; font-weight: 700; border: 1px solid #6ee7b7; white-space: nowrap;">
                      <i class="fa fa-arrow-down"></i> رسید
                    </span>
                  @else
                    <span style="background: #fee2e2; color: #991b1b; padding: 4px 12px; border-radius: 20px; font-size: 0.78rem; font-weight: 700; border: 1px solid #fca5a5; white-space: nowrap;">
                      <i class="fa fa-arrow-up"></i> گرفت
                    </span>
                  @endif
                </td>

                {{-- Original Amount --}}
                <td style="padding: 10px 16px; text-align: right; border-left: 2px solid #ede9fe;" dir="ltr">
                  <div style="display: flex; flex-direction: column; align-items: flex-end;">
                    <span style="font-weight: 700; color: #7c3aed; font-size: 1rem;">
                      {{ number_format($origAmt, 2) }}
                      <span style="font-size: 0.76rem; font-weight: 600; opacity: 0.75;">{{ $currCode }}</span>
                    </span>
                  </div>
                </td>

                {{-- USD Equivalent --}}
                <td style="padding: 10px 16px; text-align: right;" dir="ltr">
                  <div style="display: inline-flex; align-items: center; gap: 4px; background: #ecfdf5; padding: 4px 10px; border-radius: 8px; border: 1px solid #a7f3d0;">
                    <span style="color: #059669; font-size: 0.78rem; font-weight: 600;">$</span>
                    <span style="font-weight: 700; color: #065f46; font-size: 0.95rem;">{{ number_format($baseUSD, 2) }}</span>
                  </div>
                </td>

                {{-- Exchange Rate --}}
                <td style="padding: 10px 16px; color: #64748b; font-size: 0.82rem;" dir="ltr">
                  {{ is_numeric($exchRate) ? number_format((float)$exchRate, 4) : $exchRate }}
                </td>

                {{-- Contract --}}
                <td style="padding: 10px 16px;">
                  <span style="background: #ede9fe; color: #7c3aed; padding: 2px 8px; border-radius: 10px; font-size: 0.78rem; font-weight: 600;">
                    {{ $r->contract_number ?? '—' }}
                  </span>
                </td>

                {{-- Description --}}
                <td style="padding: 10px 16px; color: #475569; max-width: 180px;">
                  <span style="display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 160px;" title="{{ $r->description }}">
                    {{ $r->description }}
                  </span>
                </td>

                {{-- Date --}}
                <td style="padding: 10px 16px; white-space: nowrap; color: #64748b; font-size: 0.84rem;">
                  {{ \Carbon\Carbon::parse($r->date)->format('d M Y') }}
                </td>

                {{-- Actions --}}
                <td class="hideOnPrint" style="padding: 10px 16px; text-align: center; white-space: nowrap;">
                  <div style="display: inline-flex; gap: 4px;">
                    @can('approve_employee_money_requests')
                    <button onclick="approveRequest({{ $r->id }})"
                            title="تایید — ثبت در GL"
                            style="background: #10b981; color: #fff; padding: 5px 12px; border-radius: 6px; font-size: 0.78rem; font-weight: 600; border: none; cursor: pointer; white-space: nowrap;">
                      <i class="fa fa-check"></i> تایید
                    </button>
                    @endcan
                    @can('reject_employee_money_requests')
                    <button onclick="deleteRequest({{ $r->id }})"
                            title="رد درخواست"
                            style="background: #ef4444; color: #fff; padding: 5px 10px; border-radius: 6px; font-size: 0.78rem; font-weight: 600; border: none; cursor: pointer;">
                      <i class="fa fa-times"></i> رد
                    </button>
                    @endcan
                    @if(!auth()->user()->can('approve_employee_money_requests') && !auth()->user()->can('reject_employee_money_requests'))
                      <span style="color: #94a3b8;">—</span>
                    @endif
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="9" style="text-align: center; padding: 60px 20px; color: #94a3b8;">
                  <i class="fa fa-check-circle" style="font-size: 2.5rem; display: block; margin-bottom: 12px; color: #10b981;"></i>
                  <strong style="font-size: 1rem; color: #64748b;">هیچ درخواستی در انتظار تایید نیست</strong>
                  <p style="margin-top: 4px; font-size: 0.85rem;">تمام درخواست‌های معاش تایید یا رد شده‌اند.</p>
                </td>
              </tr>
            @endforelse
            </tbody>

            {{-- Summary Footer --}}
            @if($requests->count() > 0)
            @php
              $totalBaseUSD = $requests->sum('base_currency_amount');
              $totalOrigAFN = $requests->where('currency_code', 'AFN')->sum('original_amount') + $requests->whereNull('currency_code')->where('amount_af', '>', 0)->sum('amount_af');
              $totalOrigUSD = $requests->where('currency_code', 'USD')->sum('original_amount') + $requests->whereNull('currency_code')->where('amount', '>', 0)->sum('amount');
            @endphp
            <tfoot style="background: #f8fafc; border-top: 2px solid #e2e8f0;">
              <tr>
                <td colspan="2" style="padding: 10px 16px; font-weight: 700; color: #475569; text-align: right;">مجموع:</td>
                <td style="padding: 10px 16px; text-align: right; border-left: 2px solid #ede9fe;" dir="ltr">
                  <span style="color: #7c3aed; font-weight: 700; font-size: 0.85rem;">Mixed Currencies</span>
                </td>
                <td style="padding: 10px 16px; text-align: right;" dir="ltr">
                  <div style="display: inline-flex; align-items: center; gap: 4px; background: #ecfdf5; padding: 5px 12px; border-radius: 8px; border: 1px solid #6ee7b7;">
                    <span style="color: #059669; font-weight: 600;">$</span>
                    <span style="font-weight: 800; color: #065f46; font-size: 1rem;">{{ number_format($totalBaseUSD, 2) }}</span>
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
      text: 'تایید این درخواست معاش؟ مبلغ در دفتر کل ثبت خواهد شد.',
      icon: 'warning',
      buttons: {
        confirm: { text: 'بلی، تایید', className: 'btn-success' },
        cancel: 'انصراف'
      },
      dangerMode: false
    }).then(function(willApprove) {
      if (willApprove) {
        $.ajax({
          type: 'DELETE',
          data: { '_token': '{{ csrf_token() }}' },
          url: '/dashboard/employee-approve-request/' + id,
          success: function(res) {
            if (res.status === 'success') {
              $('.ur' + id).fadeOut(300);
              $('.approve').show();
              setTimeout(function() { location.reload(); }, 1200);
            } else {
              $('.errorAlert').show();
            }
          },
          error: function() { $('.errorAlert').show(); }
        });
      }
    });
  }

  function deleteRequest(id) {
    swal({
      text: 'این درخواست رد شود و حذف گردد؟',
      icon: 'warning',
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
          url: '/dashboard/employee-delete-request/' + id,
          success: function(res) {
            $('.ur' + id).fadeOut(300);
            $('.deleteAlert').show();
            setTimeout(function() { location.reload(); }, 1200);
          },
          error: function() { $('.errorAlert').show(); }
        });
      }
    });
  }

  window.setTimeout(function() {
    $('.approve, .deleteAlert, .errorAlert').fadeTo(500, 0).slideUp(500, function() { $(this).remove(); });
  }, 5000);
</script>
@endsection