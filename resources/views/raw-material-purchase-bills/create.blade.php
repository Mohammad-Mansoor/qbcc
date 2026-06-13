@extends('dsh.master')
@section('title', 'ثبت بل خرید مواد خام جدید')
@section('content')

<div class="row justify-content-center">
  <div class="col-lg-7 col-md-10">
    <div class="card" style="border:none; box-shadow:0 2px 12px rgba(0,0,0,0.08); border-radius:10px;">
      <div class="card-header" style="background:linear-gradient(135deg,#0f172a 0%,#1e293b 100%); border-radius:8px 8px 0 0; padding:16px 24px;">
        <h5 style="color:#fff; margin:0; font-weight:600;">
          <i class="fa fa-plus-circle" style="color:#60a5fa; margin-left:8px;"></i>
          ثبت بل خرید مواد خام جدید
        </h5>
        <small style="color:#94a3b8;">شماره بل به صورت خودکار تولید می‌شود</small>
      </div>
      <div class="card-body" style="padding:28px;">

        @if($errors->any())
          <div class="alert alert-danger" style="border-radius:8px;">
            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
          </div>
        @endif

        <form action="{{ route('raw-material-purchase-bills.store') }}" method="post">
          @csrf

          <div class="form-group">
            <label style="font-weight:600; color:#334155;">شماره بل (Bill Number)</label>
            <input type="text" class="form-control bg-light" value="{{ $nextNumber }}" readonly
                   style="font-family:monospace; font-weight:700; color:#3b82f6; font-size:1rem;">
            <small class="text-muted">شماره سریال به‌صورت خودکار تعیین می‌شود.</small>
          </div>

          <div class="form-group mt-3">
            <label style="font-weight:600; color:#334155;">فروشنده مواد خام (Seller) <span class="text-danger">*</span></label>
            <select name="seller_id" id="seller_id_create" class="form-control select2" style="width:100%;" required>
              <option value="">— انتخاب فروشنده —</option>
              @foreach($sellers as $s)
                <option value="{{ $s->id }}" {{ old('seller_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
              @endforeach
            </select>
            <small class="text-muted">فروشنده‌ای که این بل خرید به آن تعلق دارد.</small>
          </div>

          <div class="form-group mt-3">
            <label style="font-weight:600; color:#334155;">تاریخ بل (Date) <span class="text-danger">*</span></label>
            <input type="date" name="date" class="form-control" value="{{ old('date', date('Y-m-d')) }}" required>
            <small class="text-muted">تاریخ رسمی صدور این بل خرید.</small>
          </div>

          <div class="d-flex justify-content-between mt-4">
            <a href="{{ route('raw-material-purchase-bills.index') }}" class="btn btn-secondary btn-sm">
              <i class="fa fa-arrow-right"></i> بازگشت
            </a>
            <button type="submit" class="btn btn-primary btn-sm" style="font-weight:600;">
              <i class="fa fa-save"></i> ثبت بل خرید
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
  $('#seller_id_create').select2({ width: '100%' });
</script>
@endsection
