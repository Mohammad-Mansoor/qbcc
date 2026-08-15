@extends('dsh.master')
@section('title' , 'مدیریت انوایس‌ها')
@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Header Section -->
    <div class="row align-items-center mb-4">
        <div class="col-md-6 text-right">
            <h3 class="mb-0 font-weight-bold text-dark"><i class="fa fa-file-text text-primary mr-2"></i> مدیریت انوایس‌ها</h3>
            <p class="text-muted small mb-0">ایجاد و پیگیری فاکتورهای فروش قالین و مواد خام</p>
        </div>
    </div>

    <div class="row">
        <!-- Create/Edit Section -->
        @if((!$invoiceEdit && auth()->user()->can('create_invoice')) || ($invoiceEdit && auth()->user()->can('edit_invoice')))
        <div class="col-md-12 mb-4">
            <div class="card border-0 shadow-sm rounded-lg">
                <div class="card-header bg-white py-3 text-right">
                    <h5 class="mb-0 font-weight-bold">
                        <i class="fa {{ !$invoiceEdit ? 'fa-plus-circle text-success' : 'fa-edit text-primary' }} mr-2"></i>
                        {{ !$invoiceEdit ? 'ایجاد انوایس جدید' : 'ویرایش انوایس' }}
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ !$invoiceEdit ? '/dashboard/invoices' : '/dashboard/invoices/'.$invoiceEdit->id }}" method="post">
                        @csrf
                        @if($invoiceEdit) @method('PUT') @endif
                        
                        <div class="row text-right">
                            <div class="col-md-3 form-group">
                                <label class="small font-weight-bold text-muted">نمبر انوایس</label>
                                <input type="text" value="{{ $invoiceEdit ? $invoiceEdit->invoice_no : $invoice_no }}" name="invoice_no" 
                                       class="form-control border-0 bg-light font-weight-bold text-center" readonly>
                            </div>
                            <div class="col-md-3 form-group">
                                 <label class="small font-weight-bold text-muted">
                                     نوعیت فروش
                                     @if($invoiceEdit && $invoiceEdit->paid_amount > 0)
                                         <span class="badge badge-warning font-weight-bold ml-1" style="font-size: 10px;"><i class="fa fa-lock"></i> قفل شده</span>
                                     @endif
                                 </label>
                                 @if($invoiceEdit && $invoiceEdit->paid_amount > 0)
                                     @php
                                         $typeLabel = $invoiceEdit->type === 'carpet' ? 'قالین (Carpet)' : ($invoiceEdit->type === 'dye' ? 'رنگ (Dye)' : 'نخ (Yarn)');
                                     @endphp
                                     <input type="text" class="form-control shadow-sm border-0 bg-light text-muted font-weight-bold" 
                                            value="{{ $typeLabel }}" disabled style="border-radius: 8px;">
                                     <input type="hidden" name="type" value="{{ $invoiceEdit->type }}">
                                     <small class="text-danger font-weight-bold d-block mt-1" style="font-size: 11px;">
                                         <i class="fa fa-exclamation-triangle mr-1"></i> امکان تغییر نوعیت فروش وجود ندارد زیرا برای این انوایس تادیات ثبت شده است.
                                     </small>
                                 @else
                                     <select name="type" id="type-select" required class="form-control shadow-sm border-0 select2">
                                         <option {{ (($invoiceEdit && $invoiceEdit->type == 'carpet') || Request::old('type') == 'carpet') ? 'selected' : '' }} value="carpet">قالین (Carpet)</option>
                                         <option {{ (($invoiceEdit && $invoiceEdit->type == 'dye') || Request::old('type') == 'dye') ? 'selected' : '' }} value="dye">رنگ (Dye)</option>
                                         <option {{ (($invoiceEdit && $invoiceEdit->type == 'yarn') || Request::old('type') == 'yarn') ? 'selected' : '' }} value="yarn">نخ (Yarn)</option>
                                     </select>
                                 @endif
                             </div>
                            <div class="col-md-3 form-group">
                                <label class="small font-weight-bold text-muted">تاریخ انوایس</label>
                                <input type="date" name="invoice_date" value="{{ $invoiceEdit ? $invoiceEdit->invoice_date : date('Y-m-d') }}" 
                                       required class="form-control shadow-sm border-0">
                            </div>

                            <!-- Customer Selection (For Carpets) -->
                            <div class="col-md-3 form-group" id="customer-group">
                                <label class="small font-weight-bold text-muted">
                                    مشتری قالین
                                    @if($invoiceEdit && $invoiceEdit->paid_amount > 0)
                                        <span class="badge badge-warning font-weight-bold ml-1" style="font-size: 10px;"><i class="fa fa-lock"></i> قفل شده</span>
                                    @endif
                                </label>
                                @if($invoiceEdit && $invoiceEdit->paid_amount > 0)
                                    <input type="text" class="form-control shadow-sm border-0 bg-light text-muted font-weight-bold" 
                                           value="{{ $invoiceEdit->customer->name ?? '---' }}" disabled style="border-radius: 8px;">
                                    <input type="hidden" name="customer_id" value="{{ $invoiceEdit->customer_id }}">
                                    <small class="text-danger font-weight-bold d-block mt-1" style="font-size: 11px;">
                                        <i class="fa fa-exclamation-triangle mr-1"></i> امکان تغییر مشتری وجود ندارد زیرا برای این انوایس تادیات ثبت شده است.
                                    </small>
                                @else
                                    <select name="customer_id" id="customer-select" class="form-control shadow-sm border-0 select2">
                                        <option value="">انتخاب مشتری...</option>
                                        @foreach($customers as $cust)
                                            <option {{ (($invoiceEdit && $invoiceEdit->customer_id == $cust->id) || Request::old('customer_id') == $cust->id) ? 'selected' : '' }} 
                                                    value="{{$cust->id}}">{{$cust->name}}</option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>

                            <!-- Agent Selection (For Materials: Dye, Yarn) -->
                            <div class="col-md-3 form-group" id="agent-group" style="display: none;">
                                <label class="small font-weight-bold text-muted">
                                    نماینده / عامل
                                    @if($invoiceEdit && $invoiceEdit->paid_amount > 0)
                                        <span class="badge badge-warning font-weight-bold ml-1" style="font-size: 10px;"><i class="fa fa-lock"></i> قفل شده</span>
                                    @endif
                                </label>
                                @if($invoiceEdit && $invoiceEdit->paid_amount > 0)
                                    <input type="text" class="form-control shadow-sm border-0 bg-light text-muted font-weight-bold" 
                                           value="{{ $invoiceEdit->agent->user->name ?? ($invoiceEdit->agent->name ?? '---') }}" disabled style="border-radius: 8px;">
                                    <input type="hidden" name="agent_id" value="{{ $invoiceEdit->agent_id }}">
                                    <small class="text-danger font-weight-bold d-block mt-1" style="font-size: 11px;">
                                        <i class="fa fa-exclamation-triangle mr-1"></i> امکان تغییر نماینده / عامل وجود ندارد زیرا برای این انوایس تادیات ثبت شده است.
                                    </small>
                                @else
                                    <select name="agent_id" id="agent-select" class="form-control shadow-sm border-0 select2">
                                        <option value="">انتخاب نماینده...</option>
                                        @foreach($agents as $agent)
                                            <option {{ (($invoiceEdit && $invoiceEdit->agent_id == $agent->agent_id) || Request::old('agent_id') == $agent->agent_id) ? 'selected' : '' }} 
                                                    value="{{$agent->agent_id}}">{{$agent->user->name ?? $agent->name}}</option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>

                            <div class="col-md-12 form-group mt-2">
                                <label class="small font-weight-bold text-muted">توضیحات انوایس</label>
                                <textarea name="invoice_description" rows="2" class="form-control shadow-sm border-0" 
                                          placeholder="توضیحات اضافی را اینجا بنویسید...">{{ $invoiceEdit ? $invoiceEdit->invoice_description : '' }}</textarea>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-3 mr-auto">
                                <button class="btn {{ !$invoiceEdit ? 'btn-success' : 'btn-primary' }} btn-block shadow-sm font-weight-bold py-2" type="submit">
                                    <i class="fa fa-save mr-2"></i> {{ !$invoiceEdit ? 'ثبت انوایس' : 'بروزرسانی انوایس' }}
                                </button>
                            </div>
                            @if($invoiceEdit)
                            <div class="col-md-3">
                                <a href="/dashboard/invoices" class="btn btn-outline-secondary btn-block py-2 font-weight-bold">انصراف</a>
                            </div>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif

        <!-- List Section -->
        @can('view_invoices')
        <div class="col-md-12">
            <div class="card border-0 shadow-sm rounded-lg overflow-hidden" id="invoiceListCard">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <form action="/dashboard/search-invoice" method="POST" class="d-none d-md-block">
                        @csrf
                        <div class="input-group">
                            <input type="text" name="search" class="form-control form-control-sm border-0 bg-light px-3" 
                                   placeholder="جستجو..." style="border-radius: 20px; width: 220px;">
                            <div class="input-group-append">
                                <button class="btn btn-light btn-sm px-3" type="submit" style="border-radius: 0 20px 20px 0;"><i class="fa fa-search"></i></button>
                            </div>
                        </div>
                    </form>
                    <h5 class="mb-0 font-weight-bold text-dark text-right">لیست تمامی انوایس‌ها</h5>
                </div>
                <div class="card-body p-0">
                    @if(session("status"))
                        <div class="alert alert-success border-0 rounded-0 mb-0 py-2 text-center status">
                            <i class="fa fa-check-circle mr-1"></i> {{session('status')}}
                        </div>
                    @endif
                    @if(session("error"))
                        <div class="alert alert-danger border-0 rounded-0 mb-0 py-2 text-center status">
                            <i class="fa fa-exclamation-triangle mr-1"></i> {{session('error')}}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 text-right">
                            <thead class="bg-primary text-white">
                                <tr class="small text-uppercase">
                                    <th class="border-0 px-4 py-3">نمبر انوایس</th>
                                    <th class="border-0 py-3 text-center">نوعیت</th>
                                    <th class="border-0 py-3 text-center">حالت انوایس</th>
                                    <th class="border-0 py-3 text-center">وضعیت پرداخت</th>
                                    <th class="border-0 py-3 text-center">تاریخ</th>
                                    <th class="border-0 py-3">خریدار (مشتری/نماینده)</th>
                                    <th class="border-0 py-3">توضیحات</th>
                                    <th class="border-0 px-4 py-3 text-left">عملیات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($invoices as $invoice)
                                <tr class="border-bottom">
                                    <td class="px-4 py-3">
                                        <span class="badge badge-soft-primary px-3 py-2 rounded-pill font-weight-bold">
                                            <i class="fa fa-file-text-o mr-1"></i> {{ $invoice->invoice_no }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @if($invoice->type === 'carpet')
                                            <span class="badge badge-soft-success px-3 py-2 rounded-pill">فروش قالین</span>
                                        @elseif($invoice->type === 'dye')
                                            <span class="badge badge-soft-warning px-3 py-2 rounded-pill">فروش رنگ</span>
                                        @elseif($invoice->type === 'yarn')
                                            <span class="badge badge-soft-info px-3 py-2 rounded-pill">فروش نخ</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($invoice->status === 'closed')
                                            <span class="badge badge-danger px-3 py-2 rounded-pill"><i class="fa fa-lock mr-1"></i> بسته شده</span>
                                        @else
                                            <span class="badge badge-success px-3 py-2 rounded-pill"><i class="fa fa-unlock-alt mr-1"></i> باز / در جریان</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($invoice->payment_status === 'paid')
                                            <span class="badge badge-soft-success px-3 py-2 rounded-pill">تصفیه شده (Paid)</span>
                                        @elseif($invoice->payment_status === 'partially_paid')
                                            <span class="badge badge-soft-orange px-3 py-2 rounded-pill">تادیه قسمتی (Partially)</span>
                                        @else
                                            <span class="badge badge-soft-danger px-3 py-2 rounded-pill">پرداخت نشده (Unpaid)</span>
                                        @endif
                                    </td>
                                    <td class="text-center small text-muted">
                                        {{ $invoice->invoice_date }}
                                    </td>
                                    <td>
                                        @if($invoice->type === 'carpet')
                                            <h6 class="mb-0 font-weight-bold small text-dark"><i class="fa fa-user text-muted mr-1"></i> {{ $invoice->customer->name ?? '---' }}</h6>
                                        @else
                                            <h6 class="mb-0 font-weight-bold small text-primary"><i class="fa fa-user-circle text-muted mr-1"></i> {{ $invoice->agent->user->name ?? $invoice->agent->name ?? '---' }}</h6>
                                        @endif
                                    </td>
                                    <td class="small text-muted text-truncate" style="max-width: 200px;">
                                        {{ $invoice->invoice_description }}
                                    </td>
                                    <td class="px-4 py-3 text-left">
                                        @if($invoice->status !== 'closed')
                                            @can('edit_invoice')
                                            <a href="/dashboard/invoices/{{$invoice->id}}/edit" 
                                               class="btn btn-outline-info btn-sm rounded-pill px-3 mr-1">
                                                <i class="fa fa-pencil mr-1"></i> ویرایش
                                            </a>
                                            @endcan
                                        @else
                                            <span class="text-muted mr-2 small"><i class="fa fa-lock"></i> قفل شده</span>
                                        @endif
                                        <a href="/dashboard/invoices/{{$invoice->id}}" 
                                           class="btn btn-soft-primary btn-sm rounded-pill px-3">
                                            <i class="fa fa-info-circle mr-1"></i> جزییات / فروشات
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="py-5 text-center">
                                        <i class="fa fa-inbox fa-3x text-muted opacity-3"></i>
                                        <p class="mt-3 text-muted font-weight-bold">هیچ انوایسی یافت نشد.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white border-0 py-3 d-flex justify-content-center">
                    {{ $invoices->links() }}
                </div>
            </div>
        </div>
        @endcan
    </div>
</div>

<style>
    .badge-soft-primary { background-color: rgba(0, 123, 255, 0.1); color: #007bff; }
    .badge-soft-success { background-color: rgba(40, 167, 69, 0.1); color: #28a745; }
    .badge-soft-warning { background-color: rgba(255, 193, 7, 0.1); color: #ffc107; }
    .badge-soft-info { background-color: rgba(23, 162, 184, 0.1); color: #17a2b8; }
    .badge-soft-danger { background-color: rgba(220, 53, 69, 0.1); color: #dc3545; }
    .badge-soft-orange { background-color: rgba(253, 126, 20, 0.1); color: #fd7e14; }
    .btn-soft-primary { background-color: rgba(0, 123, 255, 0.1); color: #007bff; border: none; }
    .btn-soft-primary:hover { background-color: #007bff; color: white; }
    .rounded-lg { border-radius: 0.75rem !important; }
    .shadow-sm { box-shadow: 0 .125rem .25rem rgba(0,0,0,.075)!important; }
    .opacity-3 { opacity: 0.3; }
    .select2-container--default .select2-selection--single { border: none !important; box-shadow: 0 .125rem .25rem rgba(0,0,0,.075) !important; height: 38px !important; display: flex; align-items: center; }
</style>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('.select2').select2({ width: '100%' });
        
        function toggleBuyerFields() {
            var type = $('#type-select').val();
            if (type === 'carpet') {
                $('#customer-group').show();
                $('#customer-select').prop('required', true);
                $('#agent-group').hide();
                $('#agent-select').prop('required', false).val('').trigger('change');
            } else {
                $('#customer-group').hide();
                $('#customer-select').prop('required', false).val('').trigger('change');
                $('#agent-group').show();
                $('#agent-select').prop('required', true);
            }
        }
        
        $('#type-select').change(function() {
            toggleBuyerFields();
        });
        
        // Initial setup
        toggleBuyerFields();
        
        window.setTimeout(function () {
            $(".status").fadeTo(500, 0).slideUp(500, function () { $(this).remove(); });
        }, 3000);
    });
</script>
@endsection
