{{-- ─── Payment Entry Form Partial ─────────────────────────────────────────── --}}
{{-- Included by employee-payment.blade.php for both create and edit modes --}}

@php
  $isEdit = $paymentEdit && $paymentEdit !== '';
  $formAction = $isEdit
    ? '/dashboard/employee-payments/' . $paymentEdit->id
    : '/dashboard/employee-payments';
  $editCurrCode = $isEdit ? ($paymentEdit->currency_code ?? 'AFN') : '';
  $editOrigAmt  = $isEdit ? ($paymentEdit->original_amount ?? ($paymentEdit->amount > 0 ? $paymentEdit->amount : $paymentEdit->amount_af)) : '';
@endphp

<div style="background: {{ $isEdit ? '#fffbeb' : '#f0f9ff' }}; border: 1px solid {{ $isEdit ? '#fde68a' : '#bae6fd' }}; border-radius: 10px; padding: 16px 20px; margin-bottom: 4px;">
  <h6 style="font-weight: 700; color: {{ $isEdit ? '#92400e' : '#0369a1' }}; margin-bottom: 12px;">
    <i class="fa {{ $isEdit ? 'fa-edit' : 'fa-plus-circle' }}"></i>
    {{ $isEdit ? 'ویرایش پرداخت' : 'ثبت پرداخت / رسید معاش' }}
  </h6>

  <form action="{{ $formAction }}" method="post">
    @csrf
    @if($isEdit) @method('PUT') @endif
    <input type="hidden" name="employee_id" value="{{ $employee->id }}">

    <div class="row">

      {{-- Contract Number --}}
      <div class="col-lg-2 col-md-3 col-sm-6">
        <div class="form-group">
          <label style="font-size: 0.82rem; font-weight: 600; color: #475569;">قرارداد نمبر</label>
          <input type="text" name="contract_number"
                 value="{{ $isEdit ? $paymentEdit->contract_number : ($contract_number->contract_number ?? '') }}"
                 readonly class="form-control form-control-sm"
                 style="background: #f8fafc; font-weight: 600; color: #334155;">
          @error('contract_number') <p class="text-danger" style="font-size: 0.75rem;">{{ $message }}</p> @enderror
        </div>
      </div>

      {{-- Amount --}}
      <div class="col-lg-2 col-md-3 col-sm-6">
        <div class="form-group">
          <label style="font-size: 0.82rem; font-weight: 600; color: #475569;">مقدار پول</label>
          <input type="number" step="0.01" min="0" name="amount"
                 id="{{ $isEdit ? 'amount_edit' : 'amount_in' }}"
                 value="{{ $isEdit ? $editOrigAmt : old('amount') }}"
                 placeholder="0.00" class="form-control form-control-sm" required>
          @error('amount') <p class="text-danger" style="font-size: 0.75rem;">{{ $message }}</p> @enderror
        </div>
      </div>

      {{-- Currency --}}
      <div class="col-lg-2 col-md-3 col-sm-6">
        <div class="form-group">
          <label style="font-size: 0.82rem; font-weight: 600; color: #475569;">ارز پرداخت</label>
          <select name="currency_id" id="{{ $isEdit ? 'currency_id_edit' : 'currency_id_in' }}" class="form-control form-control-sm" required>
            @foreach($currencies as $curr)
              <option value="{{ $curr->id }}"
                      data-rate="{{ $curr->exchange_rate }}"
                      {{ ($isEdit ? ($paymentEdit->currency_id == $curr->id) : $curr->is_base_currency) ? 'selected' : '' }}>
                {{ $curr->code }} ({{ $curr->symbol }})
              </option>
            @endforeach
          </select>
          @error('currency_id') <p class="text-danger" style="font-size: 0.75rem;">{{ $message }}</p> @enderror
        </div>
      </div>

      {{-- Exchange Rate --}}
      <div class="col-lg-2 col-md-3 col-sm-6">
        <div class="form-group">
          <label style="font-size: 0.82rem; font-weight: 600; color: #475569;">نرخ به دالر</label>
          <input type="number" step="0.00000001" min="0.00000001" name="exchange_rate"
                 id="{{ $isEdit ? 'exchange_rate_edit' : 'exchange_rate_in' }}"
                 value="{{ $isEdit ? ($paymentEdit->exchange_rate ?? 1) : 1 }}"
                 class="form-control form-control-sm" required>
          @error('exchange_rate') <p class="text-danger" style="font-size: 0.75rem;">{{ $message }}</p> @enderror
        </div>
      </div>

      {{-- Money Type (legacy support) --}}
      <div class="col-lg-2 col-md-3 col-sm-6">
        <div class="form-group">
          <label style="font-size: 0.82rem; font-weight: 600; color: #475569;">نوع پول</label>
          <select name="money_type" class="form-control form-control-sm" required>
            @foreach($currencies as $curr)
              <option value="{{ $curr->code }}"
                      {{ ($isEdit && $editCurrCode === $curr->code) ? 'selected' : (!$isEdit && $curr->is_base_currency ? 'selected' : '') }}>
                {{ $curr->code }}
              </option>
            @endforeach
          </select>
        </div>
      </div>

      {{-- Transaction Type --}}
      <div class="col-lg-2 col-md-3 col-sm-6">
        <div class="form-group">
          <label style="font-size: 0.82rem; font-weight: 600; color: #475569;">نوع معامله</label>
          <select name="type" class="form-control form-control-sm" required>
            <option value="" disabled {{ !$isEdit ? 'selected' : '' }}>انتخاب</option>
            <option value="رسید" {{ ($isEdit && $paymentEdit->type === 'رسید') ? 'selected' : '' }}>رسید (IN)</option>
            <option value="گرفت" {{ ($isEdit && $paymentEdit->type === 'گرفت') ? 'selected' : '' }}>گرفت (OUT)</option>
          </select>
          @error('type') <p class="text-danger" style="font-size: 0.75rem;">{{ $message }}</p> @enderror
        </div>
      </div>

      {{-- Description --}}
      <div class="col-lg-4 col-md-6 col-sm-12">
        <div class="form-group">
          <label style="font-size: 0.82rem; font-weight: 600; color: #475569;">توضیحات</label>
          <textarea name="description" rows="1" class="form-control form-control-sm"
                    placeholder="توضیحات معاش..." required>{{ $isEdit ? $paymentEdit->description : '' }}</textarea>
          @error('description') <p class="text-danger" style="font-size: 0.75rem;">{{ $message }}</p> @enderror
        </div>
      </div>

      {{-- Date --}}
      <div class="col-lg-2 col-md-3 col-sm-6">
        <div class="form-group">
          <label style="font-size: 0.82rem; font-weight: 600; color: #475569;">تاریخ</label>
          <input type="date" name="date"
                 value="{{ $isEdit ? $paymentEdit->date : date('Y-m-d') }}"
                 class="form-control form-control-sm" required>
          @error('date') <p class="text-danger" style="font-size: 0.75rem;">{{ $message }}</p> @enderror
        </div>
      </div>

    </div>

    {{-- FX Live Preview --}}
    <div class="row mt-1 mb-2">
      <div class="col-12">
        <div style="background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 8px; padding: 8px 16px; display: flex; gap: 24px; align-items: center;">
          <span style="font-size: 0.82rem; color: #64748b;">مبلغ کل (ارز اصلی):</span>
          <strong style="color: #7c3aed; font-size: 0.95rem;" id="{{ $isEdit ? 'orig_edit_preview' : 'orig_preview' }}">0.00</strong>
          <span style="font-size: 0.82rem; color: #64748b; margin-right: 12px;">معادل USD:</span>
          <strong style="color: #059669; font-size: 0.95rem;" id="{{ $isEdit ? 'base_edit_preview' : 'base_preview' }}">$0.00</strong>
        </div>
      </div>
    </div>

    {{-- Submit Buttons --}}
    <div class="row">
      <div class="col-12">
        <button type="reset" class="btn btn-sm btn-secondary" style="margin-left: 6px;">
          <i class="fa fa-undo"></i> انصراف
        </button>
        <button type="submit" class="btn btn-sm {{ $isEdit ? 'btn-warning' : 'btn-primary' }}">
          <i class="fa fa-save"></i> {{ $isEdit ? 'ویرایش و ذخیره' : 'ذخیره پرداخت' }}
        </button>
        @if($isEdit)
          <a href="/dashboard/employee-payments/{{ $employee->id }}" class="btn btn-sm btn-outline-secondary" style="margin-right: 6px;">
            <i class="fa fa-times"></i> لغو ویرایش
          </a>
        @endif
      </div>
    </div>

  </form>
</div>
