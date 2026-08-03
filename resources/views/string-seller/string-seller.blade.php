@extends('dsh.master')
@section('content')
  <!-- navbar -->
  
  <!-- form -->
  <br>
  <div class="row" id="sellers">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <style>
        .modern-modal-content {
          background: rgba(255, 255, 255, 0.98) !important;
          backdrop-filter: blur(15px);
          border-radius: 20px !important;
          border: 1px solid rgba(255, 255, 255, 0.25) !important;
          box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.08) !important;
          overflow: hidden;
        }
        .modern-modal-header {
          background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
          border-bottom: 1px solid #e2e8f0 !important;
          padding: 20px 24px !important;
        }
        .modern-modal-title {
          font-weight: 800;
          color: #0f172a;
          font-size: 1.15rem;
          display: flex;
          align-items: center;
          gap: 10px;
          flex-direction: row-reverse;
          margin: 0;
        }
        .modern-modal-title i {
          color: #3b82f6;
          background: rgba(59, 130, 246, 0.1);
          padding: 8px;
          border-radius: 10px;
          font-size: 1.1rem;
        }
        .modern-close-btn {
          opacity: 0.6;
          transition: opacity 0.2s ease, transform 0.2s ease;
          outline: none !important;
          font-size: 1.5rem;
          color: #64748b;
          background: transparent;
          border: none;
          cursor: pointer;
        }
        .modern-close-btn:hover {
          opacity: 1;
          transform: rotate(90deg);
          color: #ef4444;
        }
        .modern-form-group {
          margin-bottom: 20px;
        }
        .modern-form-group label {
          font-size: 0.82rem;
          font-weight: 700;
          color: #475569;
          margin-bottom: 6px;
          display: block;
        }
        .modern-input-wrapper {
          position: relative;
          display: flex;
          align-items: center;
        }
        .modern-input-wrapper i {
          position: absolute;
          right: 14px;
          color: #94a3b8;
          font-size: 1rem;
          pointer-events: none;
          transition: color 0.2s ease;
        }
        .modern-input {
          width: 100%;
          padding: 12px 42px 12px 16px !important;
          border-radius: 12px !important;
          border: 1px solid #cbd5e1 !important;
          background-color: #f8fafc !important;
          color: #0f172a !important;
          font-size: 0.9rem !important;
          transition: border-color 0.25s ease, background-color 0.25s ease, box-shadow 0.25s ease !important;
          text-align: right;
        }
        .modern-input:focus {
          border-color: #3b82f6 !important;
          background-color: #ffffff !important;
          box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1) !important;
          outline: none;
        }
        .modern-input:focus + i {
          color: #3b82f6;
        }
        .modern-modal-footer {
          border-top: 1px solid #e2e8f0 !important;
          background-color: #f8fafc;
          padding: 16px 24px !important;
        }
        .btn-modern-primary {
          background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%) !important;
          border: none !important;
          color: #ffffff !important;
          padding: 10px 24px !important;
          border-radius: 10px !important;
          font-weight: 700 !important;
          box-shadow: 0 4px 10px rgba(59, 130, 246, 0.2) !important;
          transition: transform 0.2s ease, box-shadow 0.2s ease !important;
          cursor: pointer;
        }
        .btn-modern-primary:hover {
          transform: translateY(-1px);
          box-shadow: 0 6px 15px rgba(59, 130, 246, 0.3) !important;
        }
        .btn-modern-secondary {
          background-color: #e2e8f0 !important;
          border: none !important;
          color: #475569 !important;
          padding: 10px 20px !important;
          border-radius: 10px !important;
          font-weight: 700 !important;
          transition: background-color 0.2s ease !important;
          cursor: pointer;
        }
        .btn-modern-secondary:hover {
          background-color: #cbd5e1 !important;
        }
        .btn-icon-only {
          width: 32px;
          height: 32px;
          display: inline-flex;
          align-items: center;
          justify-content: center;
          border-radius: 50%;
          transition: background-color 0.2s ease, transform 0.2s ease;
          background: transparent;
          border: none;
          padding: 0;
          cursor: pointer;
        }
        .btn-icon-only:hover {
          background-color: rgba(0, 0, 0, 0.05);
          transform: scale(1.05);
        }
        .btn-icon-only.text-primary:hover {
          background-color: rgba(59, 130, 246, 0.1);
        }
        .btn-icon-only.text-info:hover {
          background-color: rgba(6, 182, 212, 0.1);
        }
        .btn-icon-only.text-success:hover {
          background-color: rgba(34, 197, 94, 0.1);
        }
      </style>

      <!-- Modal for Creating/Editing String Seller -->
      <div class="modal fade" id="sellerModal" tabindex="-1" role="dialog" aria-labelledby="sellerModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content modern-modal-content">
            <form id="sellerForm" method="post" action="/dashboard/string-seller">
              @csrf
              <div id="method_override"></div>
              <div class="modal-header modern-modal-header d-flex justify-content-between align-items-center">
                <h5 class="modal-title modern-modal-title" id="sellerModalLabel">
                  <i class="fa fa-user-plus"></i>
                  <span>ایجاد فروشنده جدید</span>
                </h5>
                <button type="button" class="modern-close-btn m-0 p-0" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body text-right p-4">
                <div class="form-group modern-form-group">
                  <label class="font-weight-bold">نام فروشنده</label>
                  <div class="modern-input-wrapper">
                    <input type="text" id="seller_name" class="modern-input" required name="name" placeholder="نام کامل فروشنده را وارد کنید">
                    <i class="fa fa-user"></i>
                  </div>
                  @error('name') <p class="text-danger small mt-1">{{trans('message.'.$message)}}</p> @enderror
                </div>
                <div class="form-group modern-form-group">
                  <label class="font-weight-bold">شماره تماس</label>
                  <div class="modern-input-wrapper">
                    <input type="text" id="seller_phone" class="modern-input" required name="phone" placeholder="شماره تماس را وارد کنید">
                    <i class="fa fa-phone"></i>
                  </div>
                  @error('phone') <p class="text-danger small mt-1">{{trans('message.'.$message)}}</p> @enderror
                </div>
                <div class="form-group modern-form-group">
                  <label class="font-weight-bold">آدرس</label>
                  <div class="modern-input-wrapper">
                    <textarea id="seller_address" class="modern-input" required rows="3" name="address" placeholder="آدرس دقیق محل فعالیت را بنویسید" style="padding-top: 12px !important; resize: none;"></textarea>
                    <i class="fa fa-map-marker" style="top: 15px;"></i>
                  </div>
                  @error('address') <p class="text-danger small mt-1">{{trans('message.'.$message)}}</p> @enderror
                </div>
                <div class="form-group modern-form-group">
                  <label class="font-weight-bold">یادداشت (Note)</label>
                  <div class="modern-input-wrapper">
                    <textarea id="seller_note" class="modern-input" rows="3" name="note" placeholder="یادداشت یا توضیحات اضافی را بنویسید" style="padding-top: 12px !important; resize: vertical; min-height: 80px;"></textarea>
                    <i class="fa fa-file-text-o" style="top: 15px;"></i>
                  </div>
                </div>
              </div>
              <div class="modal-footer modern-modal-footer justify-content-start">
                <button type="button" class="btn btn-modern-secondary" data-dismiss="modal">انصراف</button>
                <button type="submit" class="btn btn-modern-primary submit-btn">ثبت اطلاعات</button>
              </div>
            </form>
          </div>
        </div>
      </div>
      
      <div class="card">
        <div class="card-header">
          <h5>فروشنده ها</h5>
          <div class="alert alert-success" style="display:none;" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                      aria-hidden="true">&times;</span></button>
            مشتری حذف شد
          </div>
          
          @if(session("status"))
            <div class="alert alert-success status" style="display:none;" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
              {{session('status')}}
            </div>
          
          @endif
          @if(session("error"))
            
            <div class="alert alert-success status" style="display:none;" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
              {{session('error')}}
            </div>
          
          @endif
            <div class="row">
              <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 hideOnPrint">
                <form action="/dashboard/string-seller/search" method="post">
                  @csrf
                  <input type="text" name="search" required
                         placeholder="جستجو" class="form-control">
                </form>
              </div>
              <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8 hideOnPrint">
                <div class="btn btn-primary btn-sm hideOnPrint" onclick="printPage('sellers')"
                     style="position: relative;float: left"><i class="fa fa-print"></i> Print
  
                </div>
                @can('view_seller_statement')
                <a href="/dashboard/sttring-seller-accounts" style="float: left; margin-left: 10px;" class="btn btn-sm btn-info hideOnPrint">فروشنده های حسابدار</a>
                @endcan
                @can('create_string_seller')
                <button type="button" class="btn btn-sm btn-success hideOnPrint" onclick="openCreateModal()" style="float: left; margin-left: 10px;">
                  <i class="fa fa-plus"></i> ایجاد فروشنده جدید
                </button>
                @endcan
              </div>
            </div>
        </div>
        <div class="card-body">
          <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap hideOnPrint">
            <h5 class="m-0 font-weight-bold" style="font-size: 1.1rem; color: #1e293b;">لیست فروشندگان خام</h5>
            <div class="d-flex align-items-center gap-2">
              <span class="text-muted small font-weight-bold" style="margin-left: 8px;">نمایش بر اساس اسعار:</span>
              <select id="currencySelector" class="form-control form-control-sm" style="width: 140px; border-radius: 8px; font-weight: bold; background-color: #f8fafc; border: 1px solid #cbd5e1;" onchange="convertTableCurrencies()">
                @foreach($currencies as $curr)
                  <option value="{{ $curr->code }}" {{ $curr->is_base_currency ? 'selected' : '' }}>
                    {{ $curr->name }} ({{ $curr->symbol }})
                  </option>
                @endforeach
              </select>
            </div>
          </div>

          <div class="table-responsive">
            <table class="table table-xs table-hover align-middle" style="font-size: 11px;">
              <thead class="thead-light">
                <tr style="background-color: #f1f5f9;">
                  <th class="py-3 px-4 text-right">نام فروشنده / آدرس</th>
                  <th class="py-3 px-3 text-right">شماره تماس</th>
                  <th class="py-3 px-3 text-right">مجموع خرید (kg)</th>
                  <th class="py-3 px-3 text-right">باقیات حسابداری</th>
                  <th class="py-3 px-3 text-right">آخرین فعالیت</th>
                  <th class="py-3 px-4 text-center hideOnPrint" style="width: 120px;">عملیات</th>
                </tr>
              </thead>
              <tbody>
              @if(!isset($accounts))
                @foreach($sellers as $seller)
                  <tr class="seller-row" data-id="{{ $seller->id }}">
                    <td class="py-3 px-4">
                      <strong class="text-dark">{{$seller->name}}</strong>
                      @if(!empty($seller->note))
                        <i class="fa fa-sticky-note text-warning mr-1" onclick="openNoteModal('{{ $seller->id }}', '{{ e($seller->name) }}', '{{ preg_replace('/\r|\n/', ' ', e($seller->note)) }}')" style="cursor:pointer;" data-toggle="tooltip" title="دارای یادداشت: {{ e($seller->note) }}"></i>
                      @endif
                      <div class="text-muted small mt-1"><i class="fa fa-map-marker text-muted" style="margin-left: 4px;"></i>{{$seller->address}}</div>
                    </td>
                    <td class="py-3 px-3 text-muted">{{$seller->phone}}</td>
                    <td class="py-3 px-3">
                      <span class="badge badge-light border" style="font-size: 10px; padding: 5px 8px; border-radius: 6px;">
                        {{number_format($seller->total_supplied, 2)}} kg
                      </span>
                    </td>
                    
                    {{-- Accounting Balance --}}
                    <td class="py-3 px-3 font-weight-bold ltr text-right accounting-bal-cell" data-usd-val="{{ $seller->accounting_balance }}">
                      <span></span>
                    </td>

                    <td class="py-3 px-3 text-muted">
                      <small><i class="fa fa-calendar-o" style="margin-left: 4px;"></i>{{ $seller->last_activity ? \Carbon\Carbon::parse($seller->last_activity)->format('Y-m-d') : 'بدون فعالیت' }}</small>
                    </td>
                    
                    <td class="py-3 px-4 text-center hideOnPrint">
                      <div class="btn-group">
                        @can('edit_string_seller')
                        <button type="button" onclick="openEditModal('{{ $seller->id }}', '{{ e($seller->name) }}', '{{ e($seller->phone) }}', '{{ preg_replace('/\r|\n/', ' ', e($seller->address)) }}', '{{ preg_replace('/\r|\n/', ' ', e($seller->note)) }}')" 
                           class="btn btn-icon-only text-primary" data-toggle="tooltip" data-placement="top" title="ویرایش اطلاعات">
                           <i class="fa fa-edit"></i>
                        </button>
                        @endcan
                        <button type="button" onclick="openNoteModal('{{ $seller->id }}', '{{ e($seller->name) }}', '{{ preg_replace('/\r|\n/', ' ', e($seller->note)) }}')" 
                           class="btn btn-icon-only text-warning" data-toggle="tooltip" data-placement="top" title="یادداشت (Note)">
                           <i class="fa fa-sticky-note"></i>
                        </button>
                        @can('manage_seller_payments')
                        <a href="/dashboard/string-seller-payments/{{$seller->id}}"
                           class="btn btn-icon-only text-info" data-toggle="tooltip" data-placement="top" title="حساب میراثی">
                           <i class="fa fa-history"></i>
                        </a>
                        @endcan
                        @can('view_account_ledger')
                        <a href="{{ route('accounting.reports.account_ledger', ['account_id' => 1]) }}?party_type=App\StringSeller&party_id={{$seller->id}}" 
                           class="btn btn-icon-only text-success" data-toggle="tooltip" data-placement="top" title="دفتر کل مالی">
                           <i class="fa fa-calculator"></i>
                        </a>
                        @endcan
                        @can('view_seller_statement')
                        <a href="{{ route('accounting.statements.show', ['entity' => 'string-seller', 'id' => $seller->id]) }}" 
                           class="btn btn-icon-only text-warning" data-toggle="tooltip" data-placement="top" title="صورت حساب مالی">
                           <i class="fa fa-file-text"></i>
                        </a>
                        @endcan
                      </div>
                    </td>
                  </tr>
                @endforeach
              @else
                @foreach($sellers as $seller)
                  @php($total_af = \Illuminate\Support\Facades\DB::table('seller_payments')->where('seller_id', $seller->id)->where('type', 'رسید')->sum('amount_af') - \Illuminate\Support\Facades\DB::table('seller_payments')->where('seller_id', $seller->id)->where('type', 'گرفت')->sum('amount_af'))
                  @php($total_usd = \Illuminate\Support\Facades\DB::table('seller_payments')->where('seller_id', $seller->id)->where('type', 'رسید')->sum('amount') - \Illuminate\Support\Facades\DB::table('seller_payments')->where('seller_id', $seller->id)->where('type', 'گرفت')->sum('amount'))

                  @if($seller->payment->count() > 0 && ($total_af != 0 || $total_usd != 0))
                    <tr class="seller-row" data-id="{{ $seller->id }}">
                      <td class="py-3 px-4">
                        <strong class="text-dark">{{$seller->name}}</strong>
                        @if(!empty($seller->note))
                          <i class="fa fa-sticky-note text-warning mr-1" onclick="openNoteModal('{{ $seller->id }}', '{{ e($seller->name) }}', '{{ preg_replace('/\r|\n/', ' ', e($seller->note)) }}')" style="cursor:pointer;" data-toggle="tooltip" title="دارای یادداشت: {{ e($seller->note) }}"></i>
                        @endif
                        <div class="text-muted small mt-1"><i class="fa fa-map-marker text-muted" style="margin-left: 4px;"></i>{{$seller->address}}</div>
                      </td>
                      <td class="py-3 px-3 text-muted">{{$seller->phone}}</td>
                      <td class="py-3 px-3">
                        <span class="badge badge-light border" style="font-size: 10px; padding: 5px 8px; border-radius: 6px;">
                          {{number_format($seller->total_supplied, 2)}} kg
                        </span>
                      </td>

                      {{-- Accounting Balance --}}
                      <td class="py-3 px-3 font-weight-bold ltr text-right accounting-bal-cell" data-usd-val="{{ $seller->accounting_balance }}">
                        <span></span>
                      </td>

                      <td class="py-3 px-3 text-muted">
                        <small><i class="fa fa-calendar-o" style="margin-left: 4px;"></i>{{ $seller->last_activity ? \Carbon\Carbon::parse($seller->last_activity)->format('Y-m-d') : 'بدون فعالیت' }}</small>
                      </td>

                      <td class="py-3 px-4 text-center hideOnPrint">
                        <div class="btn-group">
                          @can('edit_string_seller')
                          <button type="button" onclick="openEditModal('{{ $seller->id }}', '{{ e($seller->name) }}', '{{ e($seller->phone) }}', '{{ preg_replace('/\r|\n/', ' ', e($seller->address)) }}', '{{ preg_replace('/\r|\n/', ' ', e($seller->note)) }}')" 
                             class="btn btn-icon-only text-primary" data-toggle="tooltip" data-placement="top" title="ویرایش اطلاعات">
                             <i class="fa fa-edit"></i>
                          </button>
                          @endcan
                          <button type="button" onclick="openNoteModal('{{ $seller->id }}', '{{ e($seller->name) }}', '{{ preg_replace('/\r|\n/', ' ', e($seller->note)) }}')" 
                             class="btn btn-icon-only text-warning" data-toggle="tooltip" data-placement="top" title="یادداشت (Note)">
                             <i class="fa fa-sticky-note"></i>
                          </button>
                          @can('manage_seller_payments')
                          <a href="/dashboard/string-seller-payments/{{$seller->id}}"
                             class="btn btn-icon-only text-info" data-toggle="tooltip" data-placement="top" title="حساب میراثی">
                             <i class="fa fa-history"></i>
                          </a>
                          @endcan
                          @can('view_account_ledger')
                          <a href="{{ route('accounting.reports.account_ledger', ['account_id' => 1]) }}?party_type=App\StringSeller&party_id={{$seller->id}}" 
                             class="btn btn-icon-only text-success" data-toggle="tooltip" data-placement="top" title="دفتر کل مالی">
                             <i class="fa fa-calculator"></i>
                          </a>
                          @endcan
                          @can('view_seller_statement')
                          <a href="{{ route('accounting.statements.show', ['entity' => 'string-seller', 'id' => $seller->id]) }}" 
                             class="btn btn-icon-only text-warning" data-toggle="tooltip" data-placement="top" title="صورت حساب مالی">
                             <i class="fa fa-file-text"></i>
                          </a>
                          @endcan
                        </div>
                      </td>
                    </tr>
                  @endif
                @endforeach
              @endif
              
              @if(!isset($search))
                <tr class="footer-totals-row" style="background-color: #f8fafc; font-weight: bold; border-top: 2px solid #e2e8f0;">
                  <td class="py-3 px-4 text-right" colspan="3">مجموعه کل باقیات:</td>
                  <td class="py-3 px-3 ltr text-right font-weight-bold" id="total-accounting-bal"></td>
                  <td></td>
                  <td class="hideOnPrint"></td>
                </tr>
              @endif
              </tbody>
            </table>
            {{-- <span class="text-center">{{$customers->links()}}</span> --}}
          </div>
        </div>
      </div>
    
    
    </div>
  </div>

  <!-- Modal for Viewing & Editing String Seller Note -->
  <div class="modal fade" id="sellerNoteModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
      <div class="modal-content modern-modal-content">
        <form id="sellerNoteForm" method="post" action="">
          @csrf
          <div class="modal-header modern-modal-header d-flex justify-content-between align-items-center">
            <h5 class="modal-title modern-modal-title" id="sellerNoteModalTitle">
              <i class="fa fa-file-text-o"></i>
              <span id="sellerNoteModalName">یادداشت فروشنده</span>
            </h5>
            <button type="button" class="modern-close-btn m-0 p-0" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body text-right p-4">
            <input type="hidden" id="note_seller_id" name="seller_id">
            <div class="form-group mb-3">
              <label class="font-weight-bold text-muted small mb-2">متن یادداشت (Note Text):</label>
              <textarea id="sellerNoteTextarea" name="note" class="form-control" rows="12" style="border-radius: 12px; border: 1px solid #cbd5e1; font-size: 14px; line-height: 1.6; min-height: 280px; max-height: 500px; resize: vertical;" placeholder="یادداشت را اینجا وارد کنید..." @cannot('edit_string_seller') readonly @endcannot></textarea>
            </div>
            <div class="text-right mt-4">
              @can('edit_string_seller')
              <button type="submit" class="btn btn-primary rounded-pill px-4 font-weight-bold shadow-sm">
                <i class="fa fa-save mr-1"></i> ذخیره یادداشت
              </button>
              <button type="button" class="btn btn-light rounded-pill px-4 text-muted mr-2" data-dismiss="modal">انصراف</button>
              @else
              <button type="button" class="btn btn-secondary rounded-pill px-4 font-weight-bold shadow-sm" data-dismiss="modal">بستن</button>
              @endcan
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
@endsection

@section('scripts')
  <script>
      $('#form2').hide();

      // remove carpet type function
      $('.status').show();
      window.setTimeout(function () {
          $(".status").fadeTo(500, 0).slideUp(500, function () {

              $(this).remove();
          });
      }, 2000);

      function openCreateModal() {
          $('#sellerModalLabel').html('<i class="fa fa-user-plus"></i> <span>ایجاد فروشنده مواد خام</span>');
          $('#sellerForm').attr('action', '/dashboard/string-seller');
          $('#method_override').html('');
          $('#seller_name').val('');
          $('#seller_phone').val('');
          $('#seller_address').val('');
          $('#seller_note').val('');
          $('#sellerModal').modal('show');
      }

      function openEditModal(id, name, phone, address, note) {
          $('#sellerModalLabel').html('<i class="fa fa-edit"></i> <span>ویرایش فروشنده مواد خام</span>');
          $('#sellerForm').attr('action', '/dashboard/string-seller/' + id);
          $('#method_override').html('<input type="hidden" name="_method" value="PATCH">');
          $('#seller_name').val(name);
          $('#seller_phone').val(phone);
          $('#seller_address').val(address);
          $('#seller_note').val(note || '');
          $('#sellerModal').modal('show');
      }

      function openNoteModal(id, name, note) {
          $('#sellerNoteModalName').text('یادداشت: ' + name);
          $('#note_seller_id').val(id);
          $('#sellerNoteTextarea').val(note || '');
          $('#sellerNoteForm').attr('action', '/dashboard/string-seller/note/' + id);
          $('#sellerNoteModal').modal('show');
      }

      @if($sellerEdit)
        $(document).ready(function() {
          openEditModal(
            '{{ $sellerEdit->id }}',
            '{{ e($sellerEdit->name) }}',
            '{{ e($sellerEdit->phone) }}',
            '{{ preg_replace("/\r|\n/", " ", e($sellerEdit->address)) }}',
            '{{ preg_replace("/\r|\n/", " ", e($sellerEdit->note)) }}'
          );
        });
      @endif

      const exchangeRates = {
          @foreach($currencies as $curr)
              '{{ $curr->code }}': {
                  rate: {{ $curr->exchange_rate }},
                  symbol: '{{ $curr->symbol }}',
                  is_base: {{ $curr->is_base_currency ? 1 : 0 }},
                  precision: {{ $curr->decimal_precision }}
              },
          @endforeach
      };

      function formatValue(value, currencyCode) {
          const curr = exchangeRates[currencyCode];
          if (!curr) return value.toFixed(2);
          return value.toLocaleString('en-US', {
              minimumFractionDigits: curr.precision,
              maximumFractionDigits: curr.precision
          }) + ' ' + curr.symbol;
      }

      function convertTableCurrencies() {
          const selectedCode = $('#currencySelector').val();
          const targetCurrency = exchangeRates[selectedCode];
          if (!targetCurrency) return;

          let sumAccounting = 0;

          // 1. Accounting Balance (Stored in USD)
          $('.accounting-bal-cell').each(function() {
              const usdVal = parseFloat($(this).attr('data-usd-val')) || 0;
              const convertedVal = usdVal / targetCurrency.rate;
              sumAccounting += convertedVal;
              
              $(this).find('span').text(formatValue(convertedVal, selectedCode));
              if (convertedVal > 0) {
                  $(this).css('color', 'green');
              } else if (convertedVal < 0) {
                  $(this).css('color', 'red');
              } else {
                  $(this).css('color', '#475569');
              }
          });

          // 2. Update Footer Totals
          $('#total-accounting-bal').text(formatValue(sumAccounting, selectedCode));
          if (sumAccounting > 0) {
              $('#total-accounting-bal').css('color', 'green');
          } else if (sumAccounting < 0) {
              $('#total-accounting-bal').css('color', 'red');
          } else {
              $('#total-accounting-bal').css('color', '#0f172a');
          }
      }

      $(document).ready(function() {
          // Initialize currency view conversion on load
          convertTableCurrencies();
          
          // Enable tooltips
          if (typeof $ !== 'undefined' && $.fn.tooltip) {
              $('[data-toggle="tooltip"]').tooltip();
          }
      });
  </script>
@endsection
