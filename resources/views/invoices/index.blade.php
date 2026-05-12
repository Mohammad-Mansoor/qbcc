@extends('dsh.master')
@section('title' , 'مدیریت انوایس‌ها')
@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Header Section -->
    <div class="row align-items-center mb-4">
        <div class="col-md-6 text-right">
            <h3 class="mb-0 font-weight-bold text-dark"><i class="fa fa-file-text text-primary mr-2"></i> مدیریت انوایس‌ها</h3>
            <p class="text-muted small mb-0">ایجاد و پیگیری فاکتورهای فروش مشتریان</p>
        </div>
    </div>

    <div class="row">
        <!-- Create/Edit Section -->
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
                                <label class="small font-weight-bold text-muted">تاریخ انوایس</label>
                                <input type="date" name="invoice_date" value="{{ $invoiceEdit ? $invoiceEdit->invoice_date : date('Y-m-d') }}" 
                                       required class="form-control shadow-sm border-0">
                            </div>
                            <div class="col-md-3 form-group">
                                <label class="small font-weight-bold text-muted">مشتری</label>
                                <select name="customer_id" required class="form-control shadow-sm border-0 select2">
                                    <option value="">انتخاب مشتری...</option>
                                    @foreach($customers as $cust)
                                        <option {{ (($invoiceEdit && $invoiceEdit->customer_id == $cust->id) || Request::old('customer_id') == $cust->id) ? 'selected' : '' }} 
                                                value="{{$cust->id}}">{{$cust->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 d-flex align-items-end form-group">
                                <button class="btn {{ !$invoiceEdit ? 'btn-success' : 'btn-primary' }} btn-block shadow-sm font-weight-bold py-2" type="submit">
                                    <i class="fa fa-save mr-2"></i> {{ !$invoiceEdit ? 'ثبت انوایس' : 'بروزرسانی' }}
                                </button>
                            </div>
                            <div class="col-md-12 form-group mt-2">
                                <label class="small font-weight-bold text-muted">توضیحات انوایس</label>
                                <textarea name="invoice_description" rows="1" class="form-control shadow-sm border-0" 
                                          placeholder="توضیحات اضافی را اینجا بنویسید...">{{ $invoiceEdit ? $invoiceEdit->invoice_description : '' }}</textarea>
                            </div>
                        </div>
                        @if($invoiceEdit)
                        <div class="text-center mt-2">
                            <a href="/dashboard/invoices" class="btn btn-link btn-sm text-muted">انصراف از ویرایش</a>
                        </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>

        <!-- List Section -->
        <div class="col-md-12">
            <div class="card border-0 shadow-sm rounded-lg overflow-hidden" id="invoiceListCard">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <form action="/dashboard/search-invoice" method="POST" class="d-none d-md-block">
                        @csrf
                        <div class="input-group">
                            <input type="text" name="search" class="form-control form-control-sm border-0 bg-light px-3" 
                                   placeholder="جستجوی انوایس یا مشتری..." style="border-radius: 20px; width: 200px;">
                            <div class="input-group-append">
                                <button class="btn btn-light btn-sm px-3" type="submit" style="border-radius: 0 20px 20px 0;"><i class="fa fa-search"></i></button>
                            </div>
                        </div>
                    </form>
                    <h5 class="mb-0 font-weight-bold text-dark text-right">لیست تمامی انوایس‌ها</h5>
                </div>
                <div class="card-body p-0">
                    @if(session("status"))
                        <div class="alert alert-success border-0 rounded-0 mb-0 py-2 text-center small status">
                            <i class="fa fa-check-circle mr-1"></i> {{session('status')}}
                        </div>
                    @endif
                    @if(session("error"))
                        <div class="alert alert-danger border-0 rounded-0 mb-0 py-2 text-center small status">
                            <i class="fa fa-exclamation-triangle mr-1"></i> {{session('error')}}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 text-right">
                            <thead class="bg-light">
                                <tr class="text-muted small text-uppercase">
                                    <th class="border-0 px-4 py-3">نمبر انوایس</th>
                                    <th class="border-0 py-3 text-center">تاریخ</th>
                                    <th class="border-0 py-3">مشتری</th>
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
                                    <td class="text-center small text-muted">
                                        {{ $invoice->invoice_date }}
                                    </td>
                                    <td>
                                        <h6 class="mb-0 font-weight-bold small text-dark">{{ $invoice->customer->name ?? '---' }}</h6>
                                    </td>
                                    <td class="small text-muted text-truncate" style="max-width: 200px;">
                                        {{ $invoice->invoice_description }}
                                    </td>
                                    <td class="px-4 py-3 text-left">
                                        <a href="/dashboard/invoices/{{$invoice->id}}/edit" 
                                           class="btn btn-outline-info btn-sm rounded-pill px-3 mr-1">
                                            <i class="fa fa-pencil mr-1"></i> ویرایش
                                        </a>
                                        <a href="/dashboard/invoices/{{$invoice->id}}" 
                                           class="btn btn-soft-primary btn-sm rounded-pill px-3">
                                            <i class="fa fa-info-circle mr-1"></i> جزییات
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="py-5 text-center">
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
    </div>
</div>

<style>
    .badge-soft-primary { background-color: rgba(0, 123, 255, 0.1); color: #007bff; }
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
        window.setTimeout(function () {
            $(".status").fadeTo(500, 0).slideUp(500, function () { $(this).remove(); });
        }, 3000);
    });
</script>
@endsection
