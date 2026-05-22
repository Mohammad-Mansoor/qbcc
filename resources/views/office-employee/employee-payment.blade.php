@extends('dsh.master')
@section('title', 'حساب معاش کارمند')
@section('content')

@php
  $hasSalary   = $contract_number && isset($contract_number->contract_number);
  $baseCurrCode = $baseCurrency->code ?? 'USD';
  $totalDebitUSD  = $total_debit_usd ?? 0;
  $totalCreditUSD = $total_credit_usd ?? 0;
  $netBalanceUSD  = $totalCreditUSD - $totalDebitUSD;
@endphp

<div class="row" id="employee-payment">
  <div class="col-lg-12">

    {{-- ═══════════════════════════ HEADER CARD ═══════════════════════════ --}}
    <div class="card" style="border: none; box-shadow: 0 2px 16px rgba(0,0,0,0.1); margin-bottom: 16px;">
      <div class="card-header" style="background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 100%); border-radius: 8px 8px 0 0; padding: 18px 24px;">
        <div class="row align-items-center">
          <div class="col-lg-4">
            <table class="table table-sm mb-0" style="color: #e2e8f0; font-size: 0.88rem;">
              <tr><td style="color: #94a3b8; width: 100px;">نام</td><td style="font-weight: 700; color: #fff;">{{ $employee->name }}</td></tr>
              <tr><td style="color: #94a3b8;">وظیفه</td><td style="color: #e2e8f0;">{{ $employee->job_title }}</td></tr>
              <tr><td style="color: #94a3b8;">دیپارتمنت</td><td style="color: #e2e8f0;">{{ $employee->department->department ?? '—' }}</td></tr>
            </table>
          </div>
          <div class="col-lg-4 text-center">
            {{-- Balance Cards --}}
            <div style="display: flex; gap: 10px; justify-content: center; flex-wrap: wrap;">
              <div style="background: rgba(16,185,129,0.15); border: 1px solid rgba(16,185,129,0.4); border-radius: 10px; padding: 10px 16px; text-align: center;">
                <div style="color: #6ee7b7; font-size: 0.72rem; font-weight: 600; letter-spacing: 0.5px;">رسیدات (USD)</div>
                <div style="color: #fff; font-size: 1.1rem; font-weight: 800;" dir="ltr">${{ number_format($totalCreditUSD, 2) }}</div>
              </div>
              <div style="background: rgba(239,68,68,0.15); border: 1px solid rgba(239,68,68,0.4); border-radius: 10px; padding: 10px 16px; text-align: center;">
                <div style="color: #fca5a5; font-size: 0.72rem; font-weight: 600; letter-spacing: 0.5px;">پرداخت‌ها (USD)</div>
                <div style="color: #fff; font-size: 1.1rem; font-weight: 800;" dir="ltr">${{ number_format($totalDebitUSD, 2) }}</div>
              </div>
              <div style="background: {{ $netBalanceUSD >= 0 ? 'rgba(16,185,129,0.2)' : 'rgba(239,68,68,0.2)' }}; border: 1px solid {{ $netBalanceUSD >= 0 ? 'rgba(16,185,129,0.5)' : 'rgba(239,68,68,0.5)' }}; border-radius: 10px; padding: 10px 16px; text-align: center;">
                <div style="color: #c7d2fe; font-size: 0.72rem; font-weight: 600; letter-spacing: 0.5px;">بیلانس خالص</div>
                <div style="color: {{ $netBalanceUSD >= 0 ? '#6ee7b7' : '#fca5a5' }}; font-size: 1.1rem; font-weight: 800;" dir="ltr">${{ number_format($netBalanceUSD, 2) }}</div>
              </div>
            </div>
          </div>
          <div class="col-lg-4 text-left">
            <h5 style="color: #f59e0b; margin: 0;">ACCOUNT #: {{ $employee->id }}</h5>
            @if($hasSalary)
              <small style="color: #94a3b8;">قرارداد: {{ $contract_number->contract_number }}</small><br>
              <small style="color: #94a3b8;">معاش: {{ number_format($contract_number->salary, 2) }} ({{ $contract_number->in_words }})</small>
            @else
              <span style="color: #fbbf24; background: rgba(245,158,11,0.2); padding: 4px 10px; border-radius: 20px; font-size: 0.78rem;">بدون قرارداد</span>
            @endif
          </div>
        </div>

        {{-- Flash Alerts --}}
        @if(session('status'))
          <div class="alert alert-success mt-2 mb-0" style="border-radius: 6px;" role="alert">
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            <i class="fa fa-check-circle"></i> {{ session('status') }}
          </div>
        @endif
        @if(session('error'))
          <div class="alert alert-danger mt-2 mb-0" style="border-radius: 6px;" role="alert">
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            <i class="fa fa-times-circle"></i> {{ session('error') }}
          </div>
        @endif
      </div>

      {{-- ─── PAYMENT FORM ─────────────────────────────────────────────────── --}}
      <div class="card-body hideOnPrint">
        @if(!isset($check_contract))
          @if(!$hasSalary)
            <div class="alert alert-warning text-center" style="border-radius: 8px;">
              <i class="fa fa-exclamation-triangle" style="font-size: 1.5rem; display: block; margin-bottom: 8px;"></i>
              <strong>این کارمند هنوز قرارداد ندارد.</strong><br>
              لطفاً ابتدا قرارداد ثبت نمایید.
              <a href="/dashboard/employee-salary/{{ $employee->id }}" class="btn btn-sm btn-warning mt-2">
                <i class="fa fa-file-text-o"></i> ثبت قرارداد
              </a>
            </div>
          @elseif(\Carbon\Carbon::parse($contract_number->to_date)->lt(\Carbon\Carbon::today()))
            <div class="alert alert-warning text-center" style="border-radius: 8px;">
              <i class="fa fa-clock-o" style="font-size: 1.5rem; display: block; margin-bottom: 8px;"></i>
              <strong>قرارداد شخص مذکور تکمیل است.</strong> لطفاً اول قرارداد را تمدید نمایید.
              <a href="/dashboard/employee-salary/{{ $employee->id }}" class="btn btn-sm btn-warning mt-2">تمدید قرارداد</a>
            </div>
          @else
            @include('office-employee._payment-form')
          @endif
        @else
          @include('office-employee._payment-form')
        @endif
      </div>
    </div>

    {{-- ═══════════════════════════ PAYMENTS TABLE ═══════════════════════════ --}}
    <div class="card" style="border: none; box-shadow: 0 2px 12px rgba(0,0,0,0.08);">
      <div class="card-header" style="background: #f8fafc; padding: 12px 20px; border-bottom: 2px solid #e2e8f0;">
        <div class="row align-items-center">
          <div class="col-lg-6">
            <h5 style="margin: 0; color: #1e293b; font-weight: 700;">
              <i class="fa fa-history" style="color: #7c3aed; margin-left: 6px;"></i>
              لیست پرداخت‌ها و رسیدات معاش
            </h5>
          </div>
          <div class="col-lg-6 text-left hideOnPrint">
            <a href="/dashboard/employee-payments-all/{{ $employee->id }}"
               class="btn btn-sm btn-info" style="margin-left: 8px;">
              <i class="fa fa-list"></i> نمایش همه
            </a>
            <div onclick="printPage('employee-payment')"
                 style="display: inline-block; cursor: pointer; background: #1e293b; color: #e2e8f0; padding: 6px 14px; border-radius: 8px; font-size: 0.82rem; border: none;">
              <i class="fa fa-print"></i> چاپ
            </div>
            <div class="btn-group" id="exportButton" style="margin-left: 4px;"></div>
          </div>
        </div>

        {{-- Contract Filter --}}
        <div class="row mt-2 hideOnPrint">
          <div class="col-lg-4">
            <form action="/dashboard/employee-payments-list-contract" method="post">
              @csrf
              <input type="hidden" name="employee_id" value="{{ $employee->id }}">
              <select name="contract_number" class="form-control form-control-sm" onchange="this.form.submit()">
                <option value="">— همه قراردادها —</option>
                @foreach($contract_number_list as $contract)
                  <option value="{{ $contract->contract_number }}"
                    {{ isset($check_contract) && request('contract_number') == $contract->contract_number ? 'selected' : '' }}>
                    {{ $contract->contract_number }} ({{ $contract->from_date }} تا {{ $contract->to_date }})
                  </option>
                @endforeach
              </select>
            </form>
          </div>
        </div>
      </div>

      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table mb-0" style="font-size: 0.85rem;" id="employee_payment">
            <thead style="background: #1e293b; color: #e2e8f0;">
              <tr>
                <th style="padding: 10px 12px; white-space: nowrap;">نوع</th>
                <th style="padding: 10px 12px; white-space: nowrap; text-align: right; border-left: 2px solid #7c3aed;">
                  مبلغ (ارز تراکنش)
                  <small class="d-block" style="font-weight: 400; color: #94a3b8; font-size: 0.7rem;">Original Currency</small>
                </th>
                <th style="padding: 10px 12px; white-space: nowrap; text-align: right;">
                  معادل USD
                  <small class="d-block" style="font-weight: 400; color: #94a3b8; font-size: 0.7rem;">نرخ × مبلغ</small>
                </th>
                <th style="padding: 10px 12px; white-space: nowrap;">نرخ دالر</th>
                <th style="padding: 10px 12px; white-space: nowrap;">قرارداد</th>
                <th style="padding: 10px 12px;">تفصیلات</th>
                <th style="padding: 10px 12px; white-space: nowrap;">تاریخ</th>
                <th style="padding: 10px 12px; text-align: center;">حالت</th>
                <th class="hideOnPrint" style="padding: 10px 12px; text-align: center;">اقدامات</th>
              </tr>
            </thead>
            <tbody>
            @forelse($payments as $pa)
              @php
                $isCredit  = ($pa->type === 'رسید');
                $currCode  = $pa->currency_code ?? ($pa->amount > 0 ? 'USD' : 'AFN');
                $exchRate  = $pa->exchange_rate ?? $pa->dollar_rate ?? 1;
                $origAmt   = $pa->original_amount ?? ($pa->amount > 0 ? $pa->amount : $pa->amount_af);
                $baseUSD   = $pa->base_currency_amount ?? ($pa->amount > 0 ? $pa->amount : 0);
                $rowBg     = $loop->even ? '#fafafa' : '#fff';
              @endphp
              <tr class="ur{{ $pa->id }}"
                  style="background: {{ $rowBg }}; border-bottom: 1px solid #f1f5f9;"
                  onmouseover="this.style.background='#fffbeb'" onmouseout="this.style.background='{{ $rowBg }}'">

                {{-- Type Badge --}}
                <td style="padding: 9px 12px;">
                  @if($isCredit)
                    <span style="background: #d1fae5; color: #065f46; padding: 3px 10px; border-radius: 20px; font-size: 0.78rem; font-weight: 700; border: 1px solid #6ee7b7; white-space: nowrap;">
                      <i class="fa fa-arrow-down"></i> رسید
                    </span>
                  @else
                    <span style="background: #fee2e2; color: #991b1b; padding: 3px 10px; border-radius: 20px; font-size: 0.78rem; font-weight: 700; border: 1px solid #fca5a5; white-space: nowrap;">
                      <i class="fa fa-arrow-up"></i> گرفت
                    </span>
                  @endif
                </td>

                {{-- Original Amount --}}
                <td style="padding: 9px 12px; text-align: right; border-left: 2px solid #ede9fe;" dir="ltr">
                  <span style="font-weight: 700; color: #7c3aed; font-size: 0.95rem;">
                    {{ number_format($origAmt, 2) }}
                    <span style="font-size: 0.75rem; font-weight: 600; opacity: 0.8;">{{ $currCode }}</span>
                  </span>
                  @if($exchRate != 1 && $exchRate > 0)
                    <small class="d-block" style="color: #94a3b8; font-size: 0.7rem;">نرخ: {{ number_format($exchRate, 4) }}</small>
                  @endif
                </td>

                {{-- USD Equivalent --}}
                <td style="padding: 9px 12px; text-align: right;" dir="ltr">
                  <div style="display: inline-flex; align-items: center; gap: 3px; background: #ecfdf5; padding: 3px 10px; border-radius: 8px; border: 1px solid #a7f3d0;">
                    <span style="color: #059669; font-size: 0.78rem; font-weight: 600;">$</span>
                    <span style="font-weight: 700; color: #065f46;">{{ number_format($baseUSD, 2) }}</span>
                  </div>
                </td>

                {{-- Exchange Rate --}}
                <td style="padding: 9px 12px; color: #64748b; font-size: 0.82rem;" dir="ltr">
                  {{ number_format($exchRate, 4) }}
                </td>

                {{-- Contract --}}
                <td style="padding: 9px 12px;">
                  <span style="background: #ede9fe; color: #7c3aed; padding: 2px 8px; border-radius: 10px; font-size: 0.78rem;">
                    {{ $pa->contract_number }}
                  </span>
                </td>

                {{-- Description --}}
                <td style="padding: 9px 12px; color: #475569; max-width: 180px;">
                  <span style="display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 160px;" title="{{ $pa->description }}">
                    {{ $pa->description }}
                  </span>
                </td>

                {{-- Date --}}
                <td style="padding: 9px 12px; white-space: nowrap; color: #64748b; font-size: 0.82rem;">
                  {{ \Carbon\Carbon::parse($pa->date)->format('d M Y') }}
                </td>

                {{-- Status --}}
                <td style="padding: 9px 12px; text-align: center;">
                  @if($pa->status == 0)
                    <span class="badge badge-warning" style="font-size: 0.72rem;">انتظار تایید</span>
                  @else
                    <span class="badge badge-success" style="font-size: 0.72rem;">تایید شده</span>
                  @endif
                </td>

                {{-- Actions --}}
                <td class="hideOnPrint" style="padding: 9px 12px; text-align: center; white-space: nowrap;">
                  @if($pa->status == 0 || auth()->user()->role == 'SP')
                    <a href="/dashboard/employee-payments/{{ $pa->id }}/edit" class="btn btn-xs btn-info">
                      <i class="fa fa-pencil"></i> ویرایش
                    </a>
                    <button onclick="deletePayment({{ $pa->id }}, {{ $pa->employee_id }})"
                            class="btn btn-danger btn-xs">
                      <i class="fa fa-trash"></i> حذف
                    </button>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="9" style="text-align: center; padding: 50px 20px; color: #94a3b8;">
                  <i class="fa fa-inbox" style="font-size: 2rem; display: block; margin-bottom: 10px; color: #cbd5e1;"></i>
                  هیچ پرداختی ثبت نشده است.
                </td>
              </tr>
            @endforelse
            </tbody>

            {{-- Summary Footer --}}
            @if(method_exists($payments, 'count') && $payments->count() > 0)
            <tfoot style="background: #f8fafc; border-top: 2px solid #e2e8f0; font-weight: 700;">
              <tr>
                <td style="padding: 10px 12px; color: #475569;">مجموع:</td>
                <td style="padding: 10px 12px; text-align: right; border-left: 2px solid #ede9fe;">— mixed currencies —</td>
                <td style="padding: 10px 12px; text-align: right;" dir="ltr">
                  <div style="display: inline-flex; gap: 3px; align-items: center; background: #ecfdf5; padding: 4px 12px; border-radius: 8px; border: 1px solid #6ee7b7;">
                    <span style="color: #059669; font-weight: 600;">$</span>
                    <span style="color: #065f46; font-weight: 800;">{{ number_format($totalCreditUSD - $totalDebitUSD, 2) }}</span>
                  </div>
                </td>
                <td colspan="6"></td>
              </tr>
              <tr style="background: #f1f5f9;">
                <td colspan="2" style="padding: 8px 12px; color: #475569;">پرداخت (گرفت) USD:</td>
                <td style="padding: 8px 12px; color: #dc2626; font-weight: 700;" dir="ltr">${{ number_format($totalDebitUSD, 2) }}</td>
                <td colspan="6"></td>
              </tr>
              <tr style="background: #f0fdf4;">
                <td colspan="2" style="padding: 8px 12px; color: #475569;">رسیدات (رسید) USD:</td>
                <td style="padding: 8px 12px; color: #16a34a; font-weight: 700;" dir="ltr">${{ number_format($totalCreditUSD, 2) }}</td>
                <td colspan="6"></td>
              </tr>
            </tfoot>
            @endif
          </table>
        </div>

        @if(!isset($all))
          <div class="p-3 hideOnPrint">
            @if(method_exists($payments, 'links'))
              {{ $payments->links() }}
            @endif
          </div>
        @endif
      </div>
    </div>

  </div>
</div>

@endsection

@section('scripts')
<script>
  // ─── Table Export ─────────────────────────────────────────────
  $(document).ready(function() {
    $('#employee_payment').tableExport({
      headers: true, footers: true, formats: ['xlsx'],
      filename: 'employee-payments', bootstrap: true,
      exportButtons: true, position: 'bottom',
      ignoreCols: 8, trimWhitespace: true, RTL: true, sheetname: 'معاش'
    });
    var $buttons = $('#employee_payment').find('caption').children().detach();
    $buttons.appendTo('#exportButton');

    // Initialize Select2
    $('.select2').select2();

    // Live calculations on load
    calculateLiveAmounts();
    if ($('#amount_edit').length) { calculateLiveEditAmounts(); }
  });

  // ─── Live FX preview (Create form) ────────────────────────────
  function calculateLiveAmounts() {
    var amount = parseFloat($('#amount_in').val()) || 0;
    var rate   = parseFloat($('#exchange_rate_in').val()) || 1;
    var baseUSD = amount * rate;
    $('#orig_preview').text(amount.toFixed(2) + ' ' + ($('#currency_id_in option:selected').text().split(' ')[0] || ''));
    $('#base_preview').text('$' + baseUSD.toFixed(2));
  }
  $('#amount_in, #exchange_rate_in').on('input', calculateLiveAmounts);
  $('#currency_id_in').change(function() {
    var rate = $(this).find(':selected').data('rate') || 1;
    $('#exchange_rate_in').val(rate);
    calculateLiveAmounts();
  });

  // ─── Live FX preview (Edit form) ──────────────────────────────
  function calculateLiveEditAmounts() {
    var amount = parseFloat($('#amount_edit').val()) || 0;
    var rate   = parseFloat($('#exchange_rate_edit').val()) || 1;
    var baseUSD = amount * rate;
    $('#orig_edit_preview').text(amount.toFixed(2) + ' ' + ($('#currency_id_edit option:selected').text().split(' ')[0] || ''));
    $('#base_edit_preview').text('$' + baseUSD.toFixed(2));
  }
  $('#amount_edit, #exchange_rate_edit').on('input', calculateLiveEditAmounts);
  $('#currency_id_edit').change(function() {
    var rate = $(this).find(':selected').data('rate') || 1;
    $('#exchange_rate_edit').val(rate);
    calculateLiveEditAmounts();
  });

  // ─── Delete Confirmation ────────────────────────────────────────
  function deletePayment(id, employee_id) {
    swal({
      text: 'مطمئن هستید که این پرداخت را حذف کنید؟ ورودی حسابداری نیز معکوس خواهد شد.',
      icon: 'warning',
      buttons: {
        confirm: { text: 'بلی، حذف کن', className: 'btn-danger' },
        cancel: 'انصراف'
      },
      dangerMode: true
    }).then(function(willDelete) {
      if (willDelete) {
        $.ajax({
          type: 'DELETE',
          data: { '_token': '{{ csrf_token() }}' },
          url: '/dashboard/employee-payments/' + id,
          success: function(res) {
            if (res.status === 'success') {
              $('.ur' + id).fadeOut(300);
              setTimeout(function() {
                window.location = '/dashboard/employee-payments/' + employee_id;
              }, 400);
            } else {
              swal('خطا در انجام عملیات', { icon: 'error' });
            }
          },
          error: function() {
            swal('خطا در ارتباط با سرور', { icon: 'error' });
          }
        });
      }
    });
  }

  // Auto-hide flash alerts
  window.setTimeout(function() {
    $('.alert.alert-success, .alert.alert-danger').not('[data-permanent]').fadeTo(500, 0).slideUp(500);
  }, 4000);
</script>
@endsection