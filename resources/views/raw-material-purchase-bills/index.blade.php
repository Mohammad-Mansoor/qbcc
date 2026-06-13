@extends('dsh.master')
@section('title', 'بل‌های خرید مواد خام')
@section('content')

<style>
.rmb-card {
    border: none;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    border-radius: 10px;
}
.rmb-header {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
    border-radius: 8px;
    padding: 16px 24px;
}
.badge-open   { background:#d1fae5; color:#065f46; border:1px solid #6ee7b7; padding:3px 10px; border-radius:20px; font-size:.78rem; font-weight:600; }
.badge-closed { background:#fee2e2; color:#991b1b; border:1px solid #fca5a5; padding:3px 10px; border-radius:20px; font-size:.78rem; font-weight:600; }
</style>

<div class="row">
  <div class="col-lg-12">

    {{-- Header --}}
    <div class="card rmb-card mb-3">
      <div class="rmb-header d-flex align-items-center justify-content-between">
        <div>
          <h5 style="color:#fff; margin:0; font-weight:600;">
            <i class="fa fa-file-text-o" style="color:#60a5fa; margin-left:8px;"></i>
            بل‌های خرید مواد خام
          </h5>
          <small style="color:#94a3b8;">مدیریت بل‌های خرید مواد خام — شناسه سریال: RM-PB-YYYY-NNNN</small>
        </div>
        <a href="{{ route('raw-material-purchase-bills.create') }}" class="btn btn-primary btn-sm" style="border-radius:6px; font-weight:600;">
          <i class="fa fa-plus-circle"></i> ثبت بل جدید
        </a>
      </div>
    </div>

    {{-- Alert --}}
    @if(session('status'))
      <div class="alert alert-success alert-dismissible" role="alert" style="border-radius:8px;">
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        {{ session('status') }}
      </div>
    @endif

    {{-- Table --}}
    <div class="card rmb-card">
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table mb-0" style="font-size:.88rem;">
            <thead style="background:#f1f5f9; border-bottom:2px solid #cbd5e1;">
              <tr style="color:#475569;">
                <th style="padding:12px 16px; font-weight:600;">#</th>
                <th style="padding:12px 16px; font-weight:600;">شماره بل</th>
                <th style="padding:12px 16px; font-weight:600;">فروشنده</th>
                <th style="padding:12px 16px; font-weight:600;">تاریخ</th>
                <th style="padding:12px 16px; font-weight:600; text-align:center;">تعداد خریدها</th>
                <th style="padding:12px 16px; font-weight:600; text-align:center;">وضعیت</th>
                <th style="padding:12px 16px; font-weight:600; text-align:center;">اقدام</th>
              </tr>
            </thead>
            <tbody>
            @forelse($bills as $bill)
              @php $rowBg = $loop->even ? '#fafafa' : '#ffffff'; @endphp
              <tr style="background:{{ $rowBg }}; border-bottom:1px solid #f1f5f9;"
                  onmouseover="this.style.background='#eff6ff'" onmouseout="this.style.background='{{ $rowBg }}'">
                <td style="padding:10px 16px; color:#94a3b8; font-size:.8rem;">{{ $loop->iteration }}</td>
                <td style="padding:10px 16px;">
                  <span style="font-family:monospace; font-weight:700; color:#3b82f6; font-size:.9rem;">
                    {{ $bill->bill_number }}
                  </span>
                </td>
                <td style="padding:10px 16px; font-weight:500; color:#1e293b;">
                  {{ optional($bill->seller)->name ?? '—' }}
                </td>
                <td style="padding:10px 16px; color:#64748b;" dir="ltr">
                  {{ \Carbon\Carbon::parse($bill->date)->format('d M Y') }}
                </td>
                <td style="padding:10px 16px; text-align:center;">
                  <span style="background:#ede9fe; color:#7c3aed; padding:2px 10px; border-radius:12px; font-size:.8rem; font-weight:600;">
                    {{ $bill->purchases()->count() }}
                  </span>
                </td>
                <td style="padding:10px 16px; text-align:center;">
                  @if($bill->status == 'open')
                    <span class="badge-open"><i class="fa fa-unlock"></i> باز</span>
                  @else
                    <span class="badge-closed"><i class="fa fa-lock"></i> بسته</span>
                  @endif
                </td>
                <td style="padding:10px 16px; text-align:center; white-space:nowrap;">
                  <a href="{{ route('raw-material-purchase-bills.show', $bill->id) }}"
                     class="btn btn-xs"
                     style="background:#10b981; color:#fff; padding:4px 12px; border-radius:6px; font-size:.78rem; font-weight:600; text-decoration:none; margin-left:4px;">
                    <i class="fa fa-eye"></i> مشاهده
                  </a>
                  <a href="{{ route('raw-material-purchase-bills.edit', $bill->id) }}"
                     class="btn btn-xs"
                     style="background:#3b82f6; color:#fff; padding:4px 12px; border-radius:6px; font-size:.78rem; font-weight:600; text-decoration:none;">
                    <i class="fa fa-edit"></i> ویرایش
                  </a>
                  <button onclick="deleteBill({{ $bill->id }})"
                          class="btn btn-xs"
                          style="background:#ef4444; color:#fff; padding:4px 10px; border-radius:6px; font-size:.78rem; margin-right:4px; border:none; cursor:pointer;">
                    <i class="fa fa-trash"></i>
                  </button>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" style="text-align:center; padding:40px; color:#94a3b8;">
                  <i class="fa fa-inbox" style="font-size:2rem; display:block; margin-bottom:8px;"></i>
                  هیچ بلی ثبت نشده است
                </td>
              </tr>
            @endforelse
            </tbody>
          </table>
        </div>
        <div style="padding:16px 20px; border-top:1px solid #f1f5f9; background:#fafafa; border-radius:0 0 8px 8px;">
          {{ $bills->links() }}
        </div>
      </div>
    </div>

  </div>
</div>

@endsection

@section('scripts')
<script>
function deleteBill(id) {
  if (typeof swal === 'function') {
    swal({
      text: 'آیا از حذف این بل مطمئن هستید؟ خریدهای مرتبط از این بل جدا می‌شوند.',
      icon: 'warning',
      buttons: { confirm: { text: 'بلی، حذف کن', className: 'btn-danger' }, cancel: 'انصراف' },
      dangerMode: true
    }).then(function(willDelete) {
      if (willDelete) {
        $.ajax({
          type: 'DELETE',
          url: '/dashboard/raw-material-purchase-bills/' + id,
          data: { '_token': '{{ csrf_token() }}' },
          success: function(res) { if (res.status === 'success') location.reload(); }
        });
      }
    });
  }
}
</script>
@endsection
