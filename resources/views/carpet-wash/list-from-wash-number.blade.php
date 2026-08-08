@extends('dsh.master')

@section('content')

<style>
  /* PREMIUM GLASSMORPHISM UI */
  .glass-card {
    background: white;
    border: 1px solid var(--QBIC-border, #e2e8f0);
    border-radius: 16px;
    box-shadow: 0 4px 30px rgba(0, 0, 0, 0.03);
    margin-bottom: 30px;
    overflow: hidden;
    transition: all 0.3s ease;
  }

  .glass-header {
    background: #f8fafc;
    padding: 20px 25px;
    border-bottom: 1px solid var(--QBIC-border, #e2e8f0);
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .glass-header h4 {
    margin: 0;
    font-weight: 700;
    color: #1e293b;
    font-size: 1.2rem;
  }

  .table-modern thead th {
    background: #f8fafc;
    color: #64748b;
    font-weight: 700;
    text-transform: uppercase;
    border: none;
    letter-spacing: 0.5px;
    padding: 15px;
    font-size: 0.8rem;
  }

  .table-modern tbody td {
    padding: 15px;
    vertical-align: middle;
    border-top: 1px solid #f1f5f9;
    color: #334155;
  }

  .table-modern tbody tr:hover {
    background: #f8fafc;
  }

  /* METADATA CARDS */
  .meta-box {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 20px;
  }

  .meta-title {
    font-size: 0.95rem;
    font-weight: 700;
    color: var(--QBIC-primary, #3b82f6);
    margin-bottom: 15px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }

  .meta-item {
    display: flex;
    justify-content: space-between;
    margin-bottom: 8px;
    font-size: 0.85rem;
  }

  .meta-label {
    color: #64748b;
    font-weight: 600;
  }

  .meta-value {
    color: #1e293b;
    font-weight: 700;
  }

  /* SEARCH INPUT MODERNIZATION */
  .search-modern .form-control {
    border-radius: 8px;
    border: 2px solid #e2e8f0;
    box-shadow: none;
    padding: 10px 15px;
    transition: all 0.2s;
  }

  .search-modern .form-control:focus {
    border-color: var(--QBIC-accent, #3b82f6);
  }

  .search-modern .btn-search {
    border-radius: 8px;
    background: var(--QBIC-accent, #3b82f6);
    color: white;
    border: none;
    padding: 8px 20px;
  }

  .search-modern .btn-search:hover {
    background: #2563eb;
  }

  .search-modern .btn-clear {
    border-radius: 8px;
    background: #fef2f2;
    color: #ef4444;
    border: 1px solid #fecaca;
    margin-right: 10px;
    transition: all 0.2s;
  }

  .search-modern .btn-clear:hover {
    background: #fee2e2;
    color: #dc2626;
  }
</style>

<div id="PaidToDA">
  <div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <div class="glass-card">

        <!-- Header -->
        <div class="glass-header">
          <h4><i class="fa fa-file-text-o mr-2 text-primary"></i> جزئیات بل شستشوی قالین (Wash Bill Details)</h4>
          <div>
            @if(session("status"))
              <span class="badge badge-success px-3 py-2"><i class="fa fa-check mr-1"></i> {{session('status')}}</span>
            @endif
            @if(session("error"))
              <span class="badge badge-danger px-3 py-2"><i class="fa fa-times mr-1"></i> {{session('error')}}</span>
            @endif
          </div>
        </div>

        <div class="card-body p-4">

          <!-- Metadata Overview Grid -->
          <div class="row mb-4">
            <!-- Bill details -->
            <div class="col-md-6 mb-3 mb-md-0">
              <div class="meta-box h-100">
                <div class="meta-title"><i class="fa fa-info-circle mr-2"></i> مشخصات بل (Bill Details)</div>
                <div class="meta-item">
                  <span class="meta-label">Bill Number (نمبر بل):</span>
                  <span class="meta-value text-primary">{{$wash_number}}</span>
                </div>
                <div class="meta-item">
                  <span class="meta-label">Bill Date (تاریخ بل):</span>
                  <span class="meta-value">{{$wash_date->created_at->format('Y-m-d')}}</span>
                </div>
              </div>
            </div>

            <!-- Worker Details -->
            <div class="col-md-6">
              <div class="meta-box h-100">
                <div class="meta-title"><i class="fa fa-user mr-2"></i> مشخصات تحویل گیرنده (Worker Info)</div>
                <div class="meta-item">
                  <span class="meta-label">Washing Team (تیم شستشو):</span>
                  <span class="meta-value text-success">{{$team->name}}</span>
                </div>
                <div class="meta-item">
                  <span class="meta-label">Phone (شماره تماس):</span>
                  <span class="meta-value">{{$team->contact_no ?: '-'}}</span>
                </div>
                <div class="meta-item">
                  <span class="meta-label">Address (آدرس):</span>
                  <span class="meta-value">{{$team->address ?: '-'}}</span>
                </div>
                <?php
$none_washed_total = DB::table('carpets')
  ->join('carpet_washes', 'carpets.carpet_id', 'carpet_washes.carpetId')
  ->where('carpet_washes.team_id', $team->id)
  ->where('carpets.status', 3)
  ->get();
                ?>
                <div class="meta-item">
                  <span class="meta-label">Pending Washes (شست نشده ها):</span>
                  <span
                    class="meta-value text-danger font-weight-bold">{{$none_washed_total ? $none_washed_total->count() : 0}}
                    قالین</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Filters Row -->
          <div class="row align-items-center mb-4 search-modern">
            <!-- Dropdowns Search Form -->
            <div class="col-lg-6 mb-3 mb-lg-0 hideOnPrint">
              <form action="/dashboard/search-wash-number-for-wash" method="POST" class="w-100">
                @csrf
                <input type="hidden" name="team_id" value="{{$team->id}}">
                <div class="row">
                  <div class="col-sm-5">
                    <div class="form-group mb-0">
                      <label class="font-weight-bold mb-1">شست نمبر ها:</label>
                      <select name="wash_number" class="form-control">
                        @foreach($wash_numbers as $w)
                          <option {{($w->wash_number == $wash_number ? 'selected' : '')}} value="{{$w->wash_number}}">
                            {{$w->wash_number}}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                  <div class="col-sm-5">
                    <div class="form-group mb-0">
                      <label class="font-weight-bold mb-1">وضعیت شستشو:</label>
                      <select name="wash_nonwash" class="form-control">
                        <option value="all" {{($wash_check == 'all' ? 'selected' : '')}}>همه</option>
                        <option value="washed" {{($wash_check == 'washed' ? 'selected' : '')}}>شسته شده ها</option>
                        <option value="nonwashed" {{($wash_check == 'nonwashed' ? 'selected' : '')}}>نشسته ها</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-sm-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-search btn-block"><i class="fa fa-filter"></i></button>
                  </div>
                </div>
              </form>
            </div>

            <!-- Carpet Number Search Form -->
            <div class="col-lg-3 col-sm-6 mb-3 mb-lg-0 hideOnPrint">
              <form action="/dashboard/search-wash-number-for-wash" method="POST">
                @csrf
                <input type="hidden" name="team_id" value="{{$team->id}}">
                <input type="hidden" name="wash_number" value="{{$wash_number}}">
                <div class="form-group mb-0">
                  <label class="font-weight-bold mb-1">جستجو نمبر قالین یا نقشه:</label>
                  <div class="input-group">
                    <input type="text" value="{{ Request::old('search') }}" name="search" class="form-control"
                      placeholder="جستجو نمبر قالین یا نقشه..." required>
                    <div class="input-group-append">
                      <button type="submit" class="btn btn-search"><i class="fa fa-search"></i></button>
                    </div>
                  </div>
                </div>
              </form>
            </div>

            <!-- Carpet Type Search Form -->
            <div class="col-lg-2 col-sm-4 mb-3 mb-lg-0 hideOnPrint">
              <form action="/dashboard/search-carpet-type-from-wash-number" method="POST">
                @csrf
                <input type="hidden" name="team_id" value="{{$team->id}}">
                <input type="hidden" name="wash_number" value="{{$wash_number}}">
                <div class="form-group mb-0">
                  <label class="font-weight-bold mb-1">جستجو نوعیت قالین:</label>
                  <?php $carpet_types = \App\CarpetType::all(); ?>
                  <select name="carpet_type_id" class="form-control" onchange="this.form.submit()">
                    <option value="">همه نوعیت ها</option>
                    @foreach($carpet_types as $type)
                      <option value="{{$type->carpet_type_id}}">{{$type->carpet_type}}</option>
                    @endforeach
                  </select>
                </div>
              </form>
            </div>

            <!-- Print Button -->
            <div class="col-lg-1 col-sm-2 text-left hideOnPrint">
              <div class="form-group mb-0">
                <label class="d-none d-sm-block mb-1">&nbsp;</label>
                @php
                  $batch = \App\ProductionBatch::where('reference_number', $wash_number)->where('type', 'wash')->first();
                @endphp
                @if($batch)
                  <a href="{{ route('batches.details', $batch->id) }}?export=pdf" target="_blank" class="btn btn-light border shadow-sm btn-block py-2" title="چاپ PDF شست نمبر">
                    <i class="fa fa-print text-primary"></i>
                  </a>
                @else
                  <a href="/dashboard/batches/{{ $wash_number }}/details?export=pdf" target="_blank" class="btn btn-light border shadow-sm btn-block py-2" title="چاپ PDF شست نمبر">
                    <i class="fa fa-print text-primary"></i>
                  </a>
                @endif
              </div>
            </div>
          </div>

          <!-- Table -->
          <div class="table-responsive">
            <table class="table table-modern text-center" style="direction: ltr;">
              <thead>
                <tr>
                  <th>ITEM#</th>
                  <th>MAP NO</th>
                  <th>ITEM DESCRIPTION</th>
                  <th>HEIGHT</th>
                  <th>WIDTH</th>
                  <th>AREA</th>
                  <th>BACKGROUND</th>
                  <th class="hideOnPrint">STATUS / WASH</th>
                  <th>RETURN</th>
                </tr>
              </thead>

              <tbody>
                @php($washed = 0)
                @php($nonwashed = 0)
                @php($washed_total_area = 0)
                @php($nonwashed_total_area = 0)

                @forelse ($carpet_washes as $wash)
                                <?php
                  $showRow = false;
                  if ($wash_check == 'washed') {
                    if ($wash->total_price > 0 || $wash->carpet->status == 13 || $wash->carpet->status != 3) {
                      $showRow = true;
                    }
                  } elseif ($wash_check == 'nonwashed') {
                    if ($wash->total_price <= 0 && $wash->carpet->status == 3) {
                      $showRow = true;
                    }
                  } else {
                    $showRow = true;
                  }
                                ?>

                                @if($showRow)
                                  <tr>
                                    <td class="font-weight-bold text-dark">{{$wash->carpet->carpet_no}}</td>
                                    <td><span class="badge badge-light border">{{$wash->carpet->map_number ?? '-'}}</span></td>
                                    <td><span class="badge badge-light border">{{$wash->carpet->type->carpet_type ?? '-'}}</span></td>

                                    <td style="direction: ltr;">
                                      <div>{{$wash->height ?: ($wash->carpet->height ?? 0)}} m</div>
                                      <small class="text-muted" title="Buying Height">{{$wash->carpet->buying_height ?? $wash->carpet->height ?? 0}} m</small>
                                    </td>
                                    <td style="direction: ltr;">
                                      <div>{{$wash->width ?: ($wash->carpet->width ?? 0)}} m</div>
                                      <small class="text-muted" title="Buying Width">{{$wash->carpet->buying_width ?? $wash->carpet->width ?? 0}} m</small>
                                    </td>

                                    <td style="direction: ltr;">
                                      @if($wash->area)
                                        <span style="display: none">{{$washed_total_area += $wash->area}}</span>
                                        {{$wash->area}} m<sup>2</sup>
                                      @else
                                        {{$wash->carpet->area ?? 0}} m<sup>2</sup>
                                        <span style="display: none">{{$nonwashed_total_area += ($wash->carpet->area ?? 0)}}</span>
                                      @endif
                                    </td>

                                    @if($wash->total_price > 0 || $wash->carpet->status == 13 || $wash->carpet->status != 3)
                                      <span style="display: none;">{{$washed++}}</span>
                                    @else
                                      <span style="display: none;">{{$nonwashed++}}</span>
                                    @endif

                                    <td style="direction: ltr;">{{$wash->carpet->field ?? '-'}}</td>

                                    <!-- Wash Status / Action -->
                                    <td class="hideOnPrint">
                                      @if($wash->total_price > 0 || $wash->carpet->status == 13)
                                        <span class="badge badge-success px-2 py-1"
                                          style="background: rgba(16, 185, 129, 0.1); color: #10b981;">Washed</span>
                                      @elseif($wash->carpet->status == 3)
                                        @can('create_carpet_wash')
                                          <a href="/dashboard/carpet-wash/create/{{$wash->id}}" class="btn btn-sm btn-primary px-3"
                                            style="border-radius: 8px;">
                                            <i class="fa fa-pencil-alt mr-1"></i> Wash
                                          </a>
                                        @else
                                          <span class="badge badge-warning px-2 py-1"
                                            style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">Unwashed</span>
                                        @endcan
                                      @else
                                        <span class="badge badge-secondary px-2 py-1">Sent to Finish</span>
                                      @endif
                                    </td>

                                    <!-- Return Action -->
                                    <td>
                                      @if(($wash->total_price > 0 || $wash->carpet->status == 13) || $wash->carpet->status == 3)
                                          <?php        $kachaee = \App\CarpetRepair::where('carpetId', $wash->carpetId)->first(); ?>
                                          @if($kachaee)
                                            @can('send_carpet_to_kachaee')
                                            <button type="button" class="btn btn-sm btn-outline-warning btn-confirm-action"
                                              style="border-radius: 8px;"
                                              data-action-url="/dashboard/carpet-wash/return-to-kachaee/{{$wash->id}}"
                                              data-carpet-no="{{$wash->carpet->carpet_no}}" data-action-label="بازگشت به کچایی"
                                              data-action-sublabel="Return to Kachaee (Repair)" data-modal-icon="fa fa-reply"
                                              data-btn-class="warning" data-needs-warehouse="1">
                                              <i class="fa fa-reply mr-1" style="color:#d97706;"></i> بازگشت به کچایی
                                            </button>
                                            @else
                                              <span class="text-muted">-</span>
                                            @endcan
                                          @else
                                            @can('return_carpet_from_wash')
                                            <button type="button" class="btn btn-sm btn-outline-danger btn-confirm-action"
                                              style="border-radius: 8px;"
                                              data-action-url="/dashboard/carpet-wash/return-to-center/{{$wash->id}}"
                                              data-carpet-no="{{$wash->carpet->carpet_no}}" data-action-label="بازگشت به مرکزی"
                                              data-action-sublabel="Return to Central Warehouse" data-modal-icon="fa fa-reply-all"
                                              data-btn-class="danger" data-needs-warehouse="0">
                                              <i class="fa fa-reply-all mr-1" style="color:#dc2626;"></i> بازگشت به مرکزی
                                            </button>
                                            @else
                                              <span class="text-muted">-</span>
                                            @endcan
                                          @endif
                                      @else
                                        <span class="text-muted">-</span>
                                      @endif
                                    </td>
                                  </tr>
                                @endif
                @empty
                  <tr>
                    <td colspan="8" class="text-center text-muted py-4">هنوز موردی ثبت نشده است</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>

          <!-- Summary Block -->
          <div class="row mt-4">
            <div class="col-md-5">
              <?php
if ($wash_check == 'washed') {
  $carpets = DB::table('carpets')->join('carpet_washes', 'carpets.carpet_id', 'carpet_washes.carpetId')->where('carpet_washes.team_id', $team->id)->where('carpet_washes.wash_number', $wash_number)->where('carpets.status', '!=', 3)->get();
} elseif ($wash_check == 'nonwashed') {
  $carpets = DB::table('carpets')->join('carpet_washes', 'carpets.carpet_id', 'carpet_washes.carpetId')->where('carpet_washes.team_id', $team->id)->where('carpet_washes.wash_number', $wash_number)->where('carpets.status', 3)->get();
} else {
  $carpets = DB::table('carpets')->join('carpet_washes', 'carpets.carpet_id', 'carpet_washes.carpetId')->where('carpet_washes.team_id', $team->id)->where('carpet_washes.wash_number', $wash_number)->get();
}
              ?>
              <div class="meta-box">
                <div class="meta-title"><i class="fa fa-calculator mr-2"></i> خلاصه بل (Bill Summary)</div>
                <div class="meta-item">
                  <span class="meta-label">QUANTITY (تعداد کل):</span>
                  <span class="meta-value">{{$carpets->count()}} Pcs</span>
                </div>
                <div class="meta-item">
                  <span class="meta-label">TOTAL AREA (مساحت کل):</span>
                  <span class="meta-value">{{number_format($washed_total_area + $nonwashed_total_area, 2)}} m²</span>
                </div>
                <div class="meta-item">
                  <span class="meta-label">WASHED (شسته شده):</span>
                  <span class="meta-value text-success font-weight-bold">{{$washed}} Pcs</span>
                </div>
                <div class="meta-item">
                  <span class="meta-label">NONE WASHED (نشسته):</span>
                  <span class="meta-value text-danger font-weight-bold">{{$nonwashed}} Pcs</span>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>

{{-- ============================================================
CONFIRMATION MODAL — shared for all status-changing actions
============================================================ --}}
<div class="modal fade" id="confirmActionModal" tabindex="-1" role="dialog" aria-labelledby="confirmActionModalLabel"
  aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 500px;">
    <div class="modal-content" style="border: none; border-radius: 20px; overflow: hidden;
         box-shadow: 0 25px 60px rgba(0,0,0,0.18);">

      {{-- Header --}}
      <div class="modal-header" id="confirmModalHeader" style="padding: 24px 28px 16px; border-bottom: none;">
        <div class="d-flex align-items-center">
          <div id="confirmModalIconWrap" style="width:52px; height:52px; border-radius:14px; display:flex;
                      align-items:center; justify-content:center; font-size:1.4rem;
                      margin-left:16px; flex-shrink:0;">
            <i id="confirmModalIcon"></i>
          </div>
          <div>
            <h5 class="mb-0 font-weight-bold" id="confirmModalTitle" style="font-size:1.1rem; color:#1e293b;">تأیید
              عملیات</h5>
            <small id="confirmModalSubtitle" class="text-muted">لطفاً قبل از ادامه مطالعه کنید</small>
          </div>
        </div>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"
          style="position:absolute; top:16px; left:20px; font-size:1.4rem; color:#94a3b8;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      {{-- Body --}}
      <div class="modal-body" style="padding: 8px 28px 24px;">

        {{-- Info card --}}
        <div style="background:#f8fafc; border-radius:12px; padding:18px 20px;
                    border: 1px solid #e2e8f0; margin-bottom: 16px;">
          <div class="d-flex justify-content-between align-items-center">
            <span class="text-muted" style="font-size:.85rem;">شماره قالین</span>
            <span class="font-weight-bold" id="confirmModalCarpetNo"
              style="font-size:1rem; color:#1e293b; letter-spacing:.5px;"></span>
          </div>
          <hr style="border-color:#e2e8f0; margin: 10px 0;">
          <div class="d-flex justify-content-between align-items-center">
            <span class="text-muted" style="font-size:.85rem;">عملیات</span>
            <span id="confirmModalActionName" style="font-size:.9rem; font-weight:600;"></span>
          </div>
        </div>

        {{-- ► Warehouse selector (only shown for Return-to-Kachaee) --}}
        <div id="confirmModalWarehouseWrap" style="display:none; margin-bottom:16px;">
          <label style="font-size:.85rem; font-weight:700; color:#374151; margin-bottom:6px; display:block;">
            <i class="fa fa-building-o mr-1" style="color:#f59e0b;"></i>
            انتخاب انبار مقصد (Destination Warehouse)
            <span class="text-danger">*</span>
          </label>
          <select id="confirmModalWarehouseSelect" name="warehouse_id" style="width:100%; border-radius:10px; border:2px solid #e2e8f0;
                         padding:10px 14px; font-size:.9rem; color:#1e293b;
                         background:#fff; appearance:none; outline:none;
                         transition: border-color .2s;">
            <option value="">— انبار را انتخاب کنید —</option>
            @foreach(\App\Warehouse::where('is_active', true)->get() as $wh)
              <option value="{{ $wh->id }}">{{ $wh->name }}@if($wh->location) — {{ $wh->location }}@endif</option>
            @endforeach
          </select>
          <small class="text-muted" style="font-size:.78rem; margin-top:4px; display:block;">
            قالین پس از بازگشت به کچایی در این انبار ثبت خواهد شد.
          </small>
        </div>

        <p class="text-muted mb-0" style="font-size:.85rem; line-height:1.7;">
          آیا مطمئن هستید؟ این عملیات وضعیت قالین را تغییر می‌دهد
          و نمی‌توان به راحتی آن را بازگرداند.
          <br><em style="font-size:.8rem;">
            Are you sure? This will change the carpet status and may not be easily reversible.
          </em>
        </p>
      </div>

      {{-- Footer: hidden POST form + buttons --}}
      <div class="modal-footer" style="border-top: 1px solid #f1f5f9; padding: 16px 28px 20px;
           background:#fcfcfd; border-radius: 0 0 20px 20px;">

        {{-- Hidden form used when action needs POST (e.g. return-to-kachaee with warehouse) --}}
        <form id="confirmModalPostForm" method="POST" action="" style="display:none;">
          @csrf
          <input type="hidden" id="confirmModalPostWarehouseId" name="warehouse_id" value="">
        </form>

        <button type="button" class="btn btn-light" data-dismiss="modal" style="border-radius:10px; padding:9px 22px; font-weight:600;
                       border: 1px solid #e2e8f0; color:#64748b;">
          <i class="fa fa-times mr-1"></i> انصراف
        </button>

        {{-- GET link (default: send-to-finish, return-to-center) --}}
        <a href="#" id="confirmModalProceedBtn" class="btn btn-success" style="border-radius:10px; padding:9px 24px; font-weight:600;
                  box-shadow: 0 4px 12px rgba(0,0,0,0.15); min-width:140px;">
          <i id="confirmModalBtnIcon" class="fa fa-check mr-1"></i>
          <span id="confirmModalBtnLabel">تأیید</span>
        </a>

        {{-- POST submit (return-to-kachaee only) --}}
        <button type="button" id="confirmModalPostBtn" class="btn btn-warning" style="display:none; border-radius:10px; padding:9px 24px; font-weight:600;
                       min-width:140px;">
          <i id="confirmModalPostBtnIcon" class="fa fa-reply mr-1"></i>
          <span id="confirmModalPostBtnLabel">تأیید</span>
        </button>
      </div>

    </div>
  </div>
</div>

@endsection

@section('scripts')
  <script>
    /* ── Confirmation Modal Logic ── */
    var colorMap = {
      success: { bg: 'rgba(16,185,129,.12)', color: '#10b981', btn: 'success' },
      warning: { bg: 'rgba(245,158,11,.12)', color: '#f59e0b', btn: 'warning' },
      danger: { bg: 'rgba(239,68,68,.12)', color: '#ef4444', btn: 'danger' }
    };

    $(document).on('click', '.btn-confirm-action', function () {
      var url = $(this).data('action-url');
      var carpetNo = $(this).data('carpet-no');
      var label = $(this).data('action-label');
      var sublabel = $(this).data('action-sublabel');
      var icon = $(this).data('modal-icon');
      var btnClass = $(this).data('btn-class');
      var needsWarehouse = parseInt($(this).data('needs-warehouse') || 0);
      var palette = colorMap[btnClass] || colorMap['success'];

      /* populate modal header */
      $('#confirmModalIconWrap').css({ background: palette.bg, color: palette.color });
      $('#confirmModalIcon').attr('class', icon);
      $('#confirmModalHeader').css('border-left', '4px solid ' + palette.color);
      $('#confirmModalTitle').text(label);
      $('#confirmModalSubtitle').text(sublabel);
      $('#confirmModalCarpetNo').text(carpetNo);
      $('#confirmModalActionName').text(label).css('color', palette.color);

      if (needsWarehouse) {
        /* ── KACHAEE RETURN & TAYAARI: show warehouse selector, use POST form ── */
        $('#confirmModalWarehouseWrap').show();
        $('#confirmModalWarehouseSelect').val('');
        $('#confirmModalPostForm').attr('action', url);
        $('#confirmModalPostWarehouseId').val('');

        /* Dynamically update helper text based on action */
        if (url.indexOf('sent-to-finish') !== -1) {
          $('#confirmModalWarehouseWrap small').text('قالین پس از ارسال به بخش تیاری در این انبار ثبت خواهد شد.');
        } else {
          $('#confirmModalWarehouseWrap small').text('قالین پس از بازگشت به کچایی در این انبار ثبت خواهد شد.');
        }

        /* hide GET button, show POST button */
        $('#confirmModalProceedBtn').hide();
        $('#confirmModalPostBtn')
          .show()
          .removeClass('btn-success btn-warning btn-danger')
          .addClass('btn-' + palette.btn);
        $('#confirmModalPostBtnIcon').attr('class', icon + ' mr-1');
        $('#confirmModalPostBtnLabel').text(label);
      } else {
        /* ── OTHER ACTIONS: simple GET link ── */
        $('#confirmModalWarehouseWrap').hide();
        $('#confirmModalProceedBtn')
          .show()
          .attr('href', url)
          .removeClass('btn-success btn-warning btn-danger')
          .addClass('btn-' + palette.btn)
          .css('box-shadow', '0 4px 12px ' + palette.bg);
        $('#confirmModalBtnIcon').attr('class', icon + ' mr-1');
        $('#confirmModalBtnLabel').text(label);
        $('#confirmModalPostBtn').hide();
      }

      $('#confirmActionModal').modal('show');
    });

    /* POST form submit — validate warehouse selected first */
    $('#confirmModalPostBtn').on('click', function () {
      var warehouseId = $('#confirmModalWarehouseSelect').val();
      if (!warehouseId) {
        /* highlight the select as required */
        $('#confirmModalWarehouseSelect').css('border-color', '#ef4444');
        $('#confirmModalWarehouseSelect').focus();
        return;
      }
      $('#confirmModalWarehouseSelect').css('border-color', '#e2e8f0');
      $('#confirmModalPostWarehouseId').val(warehouseId);
      $('#confirmModalPostForm').submit();
    });

    /* Clear validation highlight when user picks a warehouse */
    $('#confirmModalWarehouseSelect').on('change', function () {
      if ($(this).val()) {
        $(this).css('border-color', '#10b981');
      }
    });
  </script>
@endsection