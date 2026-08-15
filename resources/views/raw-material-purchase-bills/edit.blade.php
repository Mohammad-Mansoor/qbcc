@extends('dsh.master')
@section('title', 'ویرایش بل خرید مواد خام')
@section('content')

<div class="row justify-content-center">
  <div class="col-lg-7 col-md-10">
    <div class="card" style="border:none; box-shadow:0 2px 12px rgba(0,0,0,0.08); border-radius:10px;">
      <div class="card-header" style="background:linear-gradient(135deg,#0f172a 0%,#1e293b 100%); border-radius:8px 8px 0 0; padding:16px 24px;">
        <h5 style="color:#fff; margin:0; font-weight:600;">
          <i class="fa fa-edit" style="color:#60a5fa; margin-left:8px;"></i>
          ویرایش بل خرید — {{ $bill->bill_number }}
        </h5>
      </div>
      <div class="card-body" style="padding:28px;">

        @if($errors->any())
          <div class="alert alert-danger" style="border-radius:8px;">
            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
          </div>
        @endif

        <form action="{{ route('raw-material-purchase-bills.update', $bill->id) }}" method="post">
          @csrf
          @method('PUT')

          <div class="form-group">
            <label style="font-weight:600; color:#334155;">شماره بل (Bill Number)</label>
            <input type="text" class="form-control bg-light" value="{{ $bill->bill_number }}" readonly
                   style="font-family:monospace; font-weight:700; color:#3b82f6; font-size:1rem;">
          </div>

          @if($bill->paid_amount > 0)
            <div class="form-group mt-3">
              <label style="font-weight:600; color:#334155;">
                فروشنده مواد خام (Seller)
                <span class="badge badge-warning mr-2" style="font-size:11px;"><i class="fa fa-lock"></i> قفل شده</span>
              </label>
              <input type="text" class="form-control bg-light text-muted font-weight-bold" value="{{ $bill->seller->name ?? '---' }}" disabled style="border-radius:8px;">
              <input type="hidden" name="seller_id" value="{{ $bill->seller_id }}">
              <small class="text-danger font-weight-bold d-block mt-2" style="font-size:12px;">
                <i class="fa fa-exclamation-triangle mr-1"></i> امکان تغییر فروشنده وجود ندارد زیرا برای این بل خرید تادیات ثبت شده است.
              </small>
            </div>
          @else
            <div class="form-group mt-3">
              <label style="font-weight:600; color:#334155;">فروشنده مواد خام (Seller) <span class="text-danger">*</span></label>
              <select name="seller_id" id="seller_id_edit" class="form-control select2" style="width:100%;" required>
                <option value="">— انتخاب فروشنده —</option>
                @foreach($sellers as $s)
                  <option value="{{ $s->id }}" {{ $bill->seller_id == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                @endforeach
              </select>
            </div>
          @endif

          <div class="form-group mt-3">
            <label style="font-weight:600; color:#334155;">تاریخ بل (Date) <span class="text-danger">*</span></label>
            <input type="date" name="date" class="form-control" value="{{ $bill->date }}" required>
          </div>

          <div class="form-group mt-3">
            <label style="font-weight:600; color:#334155;">وضعیت (Status)</label>
            <select name="status" class="form-control" required>
              <option value="open"   {{ $bill->status == 'open'   ? 'selected' : '' }}>باز (Open)</option>
              <option value="closed" {{ $bill->status == 'closed' ? 'selected' : '' }}>بسته (Closed)</option>
            </select>
          </div>

          {{-- Linked purchases summary --}}
          @if($bill->purchases()->count() > 0)
            <div class="mt-4 p-3" style="background:#f0fdf4; border-radius:8px; border:1px solid #bbf7d0;">
              <strong style="color:#065f46;">
                <i class="fa fa-link"></i>
                {{ $bill->purchases()->count() }} خرید به این بل مرتبط است
              </strong>
            </div>
          @endif

          <div class="d-flex justify-content-between mt-4">
            <a href="{{ route('raw-material-purchase-bills.index') }}" class="btn btn-secondary btn-sm">
              <i class="fa fa-arrow-right"></i> بازگشت
            </a>
            <button type="submit" class="btn btn-primary btn-sm" style="font-weight:600;">
              <i class="fa fa-save"></i> ذخیره تغییرات
            </button>
          </div>
        </form>

      </div>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<script>
  $('#seller_id_edit').select2({ width: '100%' });
</script>
@endsection
