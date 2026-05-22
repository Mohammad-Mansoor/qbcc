@extends('dsh.master')
@section('title', 'لیست درخواست های پول کچایی')

@section('content')
<style>
    /* PREMIUM GLASSMORPHISM UI */
    .glass-card {
        background: white;
        border: 1px solid var(--qbcc-border);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-soft);
        margin-bottom: 30px;
        overflow: hidden;
        transition: all 0.3s ease;
    }
    
    .glass-header {
        background: var(--qbcc-surface);
        padding: 20px 25px;
        border-bottom: 1px solid var(--qbcc-border);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .glass-header h4 {
        margin: 0;
        font-weight: 700;
        color: var(--qbcc-primary);
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

    .badge-premium {
        border-radius: 6px;
        padding: 6px 12px;
        font-weight: 600;
        font-size: 0.75rem;
    }
</style>

<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="glass-card">
            <div class="glass-header">
                <h4><i class="fa fa-money text-success mr-2"></i> لیست درخواست‌های پول کچایی</h4>
                <div class="d-flex align-items-center">
                    <button class="btn btn-outline-primary btn-sm hideOnPrint" onclick="printPage('MRDetails')">
                        <i class="fa fa-print mr-1"></i> چاپ (Print)
                    </button>
                </div>
            </div>

            <div class="card-body p-4">
                <div class="alert alert-success approve" style="display:none;" role="alert">
                    <i class="fa fa-check-circle mr-2"></i> پرداخت موفقانه تایید شد
                </div>
                
                <div class="alert alert-danger deleteAlert" style="display:none;" role="alert">
                    <i class="fa fa-times-circle mr-2"></i> درخواست رد شد
                </div>
                
                <div class="alert alert-danger errorAlert" style="display:none;" role="alert">
                    <i class="fa fa-exclamation-triangle mr-2"></i> پول در دخل کم است
                </div>

                <div class="static-table-list table-responsive" id="MRDetails">
                    <table class="table table-modern text-center mb-0" id="dataTable">
                        <thead>
                            <tr>
                                <th>نام کچای گر</th>
                                <th>رسید (USD $)</th>
                                <th>گرفت (USD $)</th>
                                <th>رسید (AFN ؋)</th>
                                <th>گرفت (AFN ؋)</th>
                                <th>مقدار و اسعار اصلی</th>
                                <th>کچای نمبر</th>
                                <th>تفصیلات</th>
                                <th>تاریخ</th>
                                <th class="hideOnPrint">تایید پرداخت</th>
                                <th class="hideOnPrint">رد نمودن</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($requests) > 0)
                                @foreach($requests as $r)
                                    @php
                                        $kachaee_name = DB::table('kachaees')->where('id', $r->team_id)->first();
                                    @endphp
                                    <tr class="ur{{$r->id}}">
                                        <td class="font-weight-bold text-dark">{{ $kachaee_name ? $kachaee_name->name : 'N/A' }}</td>
                                        
                                        <!-- USD Received -->
                                        <td style="direction: ltr;">
                                            {{ ($r->type == 'رسید' && $r->amount > 0) ? number_format($r->amount, 2) : '0.00' }}
                                        </td>
                                        
                                        <!-- USD Sent -->
                                        <td style="direction: ltr;">
                                            {{ ($r->type == 'گرفت' && $r->amount > 0) ? number_format($r->amount, 2) : '0.00' }}
                                        </td>
                                        
                                        <!-- AFN Received -->
                                        <td style="direction: ltr;">
                                            {{ ($r->type == 'رسید' && $r->amount_af > 0) ? number_format($r->amount_af, 2) : '0.00' }}
                                        </td>
                                        
                                        <!-- AFN Sent -->
                                        <td style="direction: ltr;">
                                            {{ ($r->type == 'گرفت' && $r->amount_af > 0) ? number_format($r->amount_af, 2) : '0.00' }}
                                        </td>

                                        <!-- NEW: Original Request Currency & Amount Column -->
                                        <td style="direction: ltr;" class="font-weight-bold text-info">
                                            {{ $r->currency_symbol ?: ($r->amount > 0 ? '$' : '؋') }} 
                                            {{ number_format($r->original_amount ?: ($r->amount > 0 ? $r->amount : $r->amount_af), 2) }}
                                            <span class="badge badge-info ml-1 badge-premium">{{ $r->currency_code ?: ($r->amount > 0 ? 'USD' : 'AFN') }}</span>
                                        </td>

                                        <td>
                                            <span class="badge badge-light-secondary badge-premium font-weight-bold">{{ $r->kachaee_number ?: 'N/A' }}</span>
                                        </td>
                                        <td>{{ $r->description }}</td>
                                        <td>{{ $r->date }}</td>
                                        
                                        <!-- Approve Button -->
                                        <td class="hideOnPrint">
                                            <button onclick="approveRequest({{$r->id}})" class="btn btn-sm btn-success shadow-sm rounded-lg font-weight-bold">
                                                <i class="fa fa-check mr-1"></i> تایید
                                            </button>
                                        </td>
                                        
                                        <!-- Reject Button -->
                                        <td class="hideOnPrint">
                                            <button onclick="deleteRequest({{$r->id}})" class="btn btn-sm btn-outline-danger rounded-lg font-weight-bold">
                                                <i class="fa fa-times mr-1"></i> رد کردن
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="11" class="text-center py-4 font-weight-bold text-danger">هنوز درخواست پول ثبت نشده است.</td>
                                </tr>
                            @endif
                        </tbody>
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
            text: "آیا مطمئن هستید که این درخواست را تایید کنید؟",
            buttons: true,
            dangerMode: false,
            buttons: {
                confirm: {text: 'بلی، تایید شود', className: 'btn-success'},
                cancel: 'انصراف'
            },
        }).then((willApprove) => {
            if (willApprove) {
                $.ajax({
                    type: 'DELETE',
                    data: {
                        '_token': '{{csrf_token()}}',
                    },
                    url: '/dashboard/kachaee-approve-request-money/' + id,
                    success: function (res) {
                        if (res.status == 'success') {
                            $('.ur' + id).hide();
                            $('.approve').show();
                            window.setTimeout(function () {
                                location.reload();
                            }, 1000);
                        } else {
                            $('.errorAlert').show();
                        }
                    }
                });
            }
        });
    }

    function deleteRequest(id) {
        swal({
            text: "آیا مطمئن هستید که این درخواست را رد کنید؟",
            buttons: true,
            dangerMode: true,
            buttons: {
                confirm: {text: 'بلی، رد شود', className: 'btn-danger'},
                cancel: 'انصراف'
            },
        }).then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    type: 'DELETE',
                    data: {
                        '_token': '{{csrf_token()}}',
                    },
                    url: '/dashboard/kachaee-delete-request-money/' + id,
                    success: function (res) {
                        $('.ur' + id).hide();
                        $('.deleteAlert').show();
                        window.setTimeout(function () {
                            location.reload();
                        }, 1000);
                    }
                });
            }
        });
    }
</script>
@endsection