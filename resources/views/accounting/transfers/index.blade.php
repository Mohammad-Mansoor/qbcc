@extends('dsh.master')
@section('title' , 'مکتوب های انتقال گدام')
@section('content')

<style>
    .glass-card {
        background: white;
        border: 1px solid rgba(0,0,0,0.08);
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        margin-bottom: 30px;
        overflow: hidden;
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
    .custom-input {
        border-radius: 8px;
        border: 2px solid #e8f5e9;
        padding: 10px 15px;
        transition: all 0.2s;
    }
    .btn-premium {
        border-radius: 8px;
        font-weight: 600;
        padding: 10px 25px;
    }
    .badge-success-light {
        background-color: rgba(16, 185, 129, 0.1);
        color: #10b981;
    }
    .badge-danger-light {
        background-color: rgba(239, 68, 68, 0.1);
        color: #ef4444;
    }
</style>

<div class="row">
    <div class="col-md-12">
        <div class="glass-card">
            <div class="glass-header">
                <h4><i class="fa fa-exchange text-primary mr-2"></i> انتقال جنس بین گدام‌ها (Inter-Warehouse Transfers)</h4>
                <a href="{{ route('accounting.transfers.create') }}" class="btn btn-success btn-premium shadow-sm">
                    <i class="fa fa-plus mr-2"></i> ثبت مکتوب انتقال جدید
                </a>
            </div>
            
            <div class="card-body p-4">
                <form action="{{ route('accounting.transfers.index') }}" method="GET" class="mb-4">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="input-group">
                                <input type="text" name="search" value="{{ $search }}" class="form-control custom-input" placeholder="نمبر مکتوب یا توضیحات...">
                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="submit"><i class="fa fa-search"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover table-striped text-center">
                        <thead class="bg-light">
                            <tr>
                                <th>نمبر مکتوب</th>
                                <th>تاریخ انتقال</th>
                                <th>نوعیت جنس</th>
                                <th>گدام مبدا</th>
                                <th>گدام مقصد</th>
                                <th>تعداد/مقدار</th>
                                <th>حالت</th>
                                <th>اقدامات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transfers as $t)
                            <tr>
                                <td class="font-weight-bold text-dark">{{ $t->transfer_number }}</td>
                                <td>{{ $t->transfer_date }}</td>
                                <td>
                                    @if($t->item_type == 'carpet')
                                        <span class="badge badge-info">قالین</span>
                                    @elseif($t->item_type == 'yarn')
                                        <span class="badge badge-warning">تار</span>
                                    @else
                                        <span class="badge badge-danger">رنگ</span>
                                    @endif
                                </td>
                                <td>{{ $t->sourceWarehouse->name }}</td>
                                <td>{{ $t->destinationWarehouse->name }}</td>
                                <td>
                                    {{ (float)$t->quantity }} 
                                    {{ $t->item_type == 'carpet' ? 'تخته' : 'کیلوگرم' }}
                                </td>
                                <td>
                                    @if($t->status === 'posted')
                                        <span class="badge badge-success-light p-2 rounded"><i class="fa fa-check-circle mr-1"></i> تایید شده</span>
                                    @else
                                        <span class="badge badge-danger-light p-2 rounded"><i class="fa fa-times-circle mr-1"></i> باطل شده</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('accounting.transfers.show', $t->id) }}" class="btn btn-sm btn-info text-white" title="مشاهده">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-muted p-4">هیچ مکتوب انتقالی یافت نشد.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $transfers->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
