@extends('dsh.master')
@section('title' , 'جزئیات انتقال گدام')
@section('content')

<style>
    .glass-card {
        background: white;
        border: 1px solid rgba(0,0,0,0.08);
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        margin-bottom: 30px;
    }
    
    .glass-header {
        background: #f8fafc;
        padding: 20px 25px;
        border-bottom: 1px solid rgba(0,0,0,0.08);
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

    .detail-label {
        font-weight: 700;
        color: #64748b;
        font-size: 0.85rem;
        display: block;
        margin-bottom: 4px;
    }

    .detail-val {
        color: #1e293b;
        font-size: 1rem;
        font-weight: 600;
    }
    
    .reversal-box {
        background-color: rgba(239, 68, 68, 0.04);
        border: 1px dashed rgba(239, 68, 68, 0.3);
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 25px;
    }
    .custom-input {
        border-radius: 8px;
        border: 2px solid #ffdde1;
        padding: 10px 15px;
    }
</style>

<div class="row">
    <div class="col-lg-12">
        <div class="glass-card">
            <div class="glass-header">
                <h4><i class="fa fa-info-circle text-primary mr-2"></i> جزئیات مکتوب انتقال {{ $transfer->transfer_number }}</h4>
                <div>
                    @if($transfer->status === 'posted')
                        <span class="badge badge-success p-2"><i class="fa fa-check-circle mr-1"></i> ثبت شده و نهایی</span>
                    @else
                        <span class="badge badge-danger p-2"><i class="fa fa-times-circle mr-1"></i> باطل شده</span>
                    @endif
                    <a href="{{ route('accounting.transfers.index') }}" class="btn btn-secondary btn-sm ml-2"><i class="fa fa-arrow-left"></i> بازگشت</a>
                </div>
            </div>
            
            <div class="card-body p-4">
                <div class="row mb-4">
                    <div class="col-md-3">
                        <span class="detail-label">نمبر مکتوب</span>
                        <span class="detail-val text-primary font-weight-bold">{{ $transfer->transfer_number }}</span>
                    </div>
                    <div class="col-md-3">
                        <span class="detail-label">تاریخ انتقال</span>
                        <span class="detail-val">{{ $transfer->transfer_date }}</span>
                    </div>
                    <div class="col-md-3">
                        <span class="detail-label">نوعیت جنس</span>
                        <span class="detail-val">
                            @if($transfer->item_type == 'carpet')
                                قالین
                            @elseif($transfer->item_type == 'yarn')
                                تار
                            @else
                                رنگ
                            @endif
                        </span>
                    </div>
                    <div class="col-md-3">
                        <span class="detail-label">ثبت کننده</span>
                        <span class="detail-val">{{ $transfer->creator->name ?? 'سیستم' }}</span>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-3">
                        <span class="detail-label">گدام مبدا</span>
                        <span class="detail-val text-danger"><i class="fa fa-sign-out mr-1"></i> {{ $transfer->sourceWarehouse->name }}</span>
                    </div>
                    <div class="col-md-3">
                        <span class="detail-label">گدام مقصد</span>
                        <span class="detail-val text-success"><i class="fa fa-sign-in mr-1"></i> {{ $transfer->destinationWarehouse->name }}</span>
                    </div>
                    <div class="col-md-3">
                        <span class="detail-label">تعداد مجموعی</span>
                        <span class="detail-val">{{ (float)$transfer->quantity }} {{ $transfer->item_type == 'carpet' ? 'تخته' : 'کیلوگرم' }}</span>
                    </div>
                </div>

                @if($transfer->description)
                <div class="row mb-4">
                    <div class="col-md-12">
                        <span class="detail-label">توضیحات</span>
                        <div class="alert alert-light border p-2 mt-1 text-muted">{{ $transfer->description }}</div>
                    </div>
                </div>
                @endif

                <!-- Items Details Table -->
                <h5 class="font-weight-bold text-dark mt-5 mb-3"><i class="fa fa-list mr-2"></i> اقلام انتقال یافته</h5>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-striped text-center">
                        <thead class="bg-light">
                            <tr>
                                <th>#</th>
                                <th>مشخصه جنس / نمبر قالین</th>
                                <th>مقدار انتقال شده</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transfer->items as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td class="font-weight-bold">
                                    @if($item->item_type === 'App\Carpet')
                                        قالین نمبر: {{ $item->itemModel->carpet_no ?? $item->ref_id }}
                                    @else
                                        {{ $item->itemModel->material_type ?? 'مواد خام' }}
                                    @endif
                                </td>
                                <td>
                                    {{ (float)$item->quantity }}
                                    {{ $transfer->item_type == 'carpet' ? 'تخته' : 'کیلوگرم' }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Reversal Action Section -->
                @if($transfer->status === 'posted')
                    @can('reverse_inventory_transfer')
                    <div class="reversal-box mt-5 shadow-sm">
                        <h6 class="text-danger font-weight-bold mb-3"><i class="fa fa-exclamation-triangle mr-2"></i> ابطال مکتوب انتقال (Reversal Flow)</h6>
                        <p class="small text-muted mb-4">
                            توجه: با ابطال این سند، تمامی اقلام انتقال یافته به صورت اتوماتیک به گدام مبدا برگشت داده شده و تمام اسناد حسابداری ثبت شده در دفتر روزنامه باطل خواهند شد.
                        </p>
                        
                        <form action="{{ route('accounting.transfers.reverse', $transfer->id) }}" method="POST">
                            @csrf
                            <div class="row align-items-end">
                                <div class="col-md-9 mb-3">
                                    <div class="form-group mb-0">
                                        <label class="font-weight-bold text-muted small">دلیل ابطال سند</label>
                                        <input type="text" name="reversal_reason" class="form-control custom-input" placeholder="لطفا دلیل ابطال را بنویسید..." required>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <button class="btn btn-danger btn-block font-weight-bold py-2" type="submit" onclick="return confirm('آیا واقعا میخواهید این سند انتقال را باطل کنید؟')">
                                        <i class="fa fa-undo mr-1"></i> ابطال و برگشت کامل
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    @endcan
                @endif
            </div>
        </div>
    </div>
</div>

@endsection
