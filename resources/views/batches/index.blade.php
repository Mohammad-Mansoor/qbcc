@extends('dsh.master')
@section('title', 'مدیریت نمبرهای مسلسل تولید')
@section('content')

<style>
    /* Premium Glassmorphism Theme */
    .glass-card {
        background: white;
        border: 1px solid var(--qbcc-border, #e2e8f0);
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.02);
        margin-bottom: 30px;
        overflow: hidden;
        transition: all 0.3s ease;
    }
    
    .glass-header {
        background: #f8fafc;
        padding: 20px 25px;
        border-bottom: 1px solid #e2e8f0;
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

    .nav-tabs-premium {
        border-bottom: 2px solid #e2e8f0;
        margin-bottom: 25px;
        display: flex;
        gap: 10px;
    }

    .nav-tabs-premium .nav-link-premium {
        padding: 12px 24px;
        font-weight: 600;
        color: #64748b;
        border: none;
        border-bottom: 3px solid transparent;
        background: none;
        transition: all 0.2s;
        text-decoration: none;
    }

    .nav-tabs-premium .nav-link-premium:hover {
        color: #0f172a;
    }

    .nav-tabs-premium .nav-link-premium.active {
        color: #3b82f6;
        border-bottom-color: #3b82f6;
    }

    .badge-status-open {
        background-color: #dcfce7;
        color: #15803d;
        padding: 6px 12px;
        border-radius: 9999px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .badge-status-closed {
        background-color: #fee2e2;
        color: #b91c1c;
        padding: 6px 12px;
        border-radius: 9999px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .btn-premium {
        border-radius: 8px;
        font-weight: 600;
        padding: 10px 20px;
        transition: all 0.2s;
    }
</style>

<div class="row">
    <div class="col-lg-12">
        <!-- Navigation Tabs -->
        <div class="nav-tabs-premium">
            <a href="/dashboard/batches/kachaee" class="nav-link-premium {{ $type == 'kachaee' ? 'active' : '' }}">
                <i class="fa fa-wrench mr-1"></i> نمبرهای کچایی (Kachaee)
            </a>
            <a href="/dashboard/batches/wash" class="nav-link-premium {{ $type == 'wash' ? 'active' : '' }}">
                <i class="fa fa-tint mr-1"></i> نمبرهای شست (Washing)
            </a>
            <a href="/dashboard/batches/finish" class="nav-link-premium {{ $type == 'finish' ? 'active' : '' }}">
                <i class="fa fa-scissors mr-1"></i> نمبرهای تیاری (Tayaari)
            </a>
        </div>

        <div class="glass-card">
            <div class="glass-header">
                <h4>
                    <i class="fa fa-list text-primary mr-2"></i> 
                    لیست نمبرهای مسلسل برای {{ $title }}
                </h4>
                
                <form action="/dashboard/batches/{{ $type }}" method="post" class="m-0">
                    @csrf
                    <button type="submit" class="btn btn-success btn-premium shadow-sm">
                        <i class="fa fa-plus mr-1"></i> ایجاد نمبر جدید (Generate Next)
                    </button>
                </form>
            </div>
            
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle">
                        <thead>
                            <tr class="text-muted small uppercase">
                                <th class="font-weight-bold">#</th>
                                <th class="font-weight-bold">نمبر مسلسل (Batch Reference)</th>
                                <th class="font-weight-bold text-center">تعداد قالین (Carpets)</th>
                                <th class="font-weight-bold text-center">مساحت کل (Total Area)</th>
                                <th class="font-weight-bold text-center">حالت (Status)</th>
                                <th class="font-weight-bold">تاریخ ایجاد (Created At)</th>
                                <th class="font-weight-bold text-center">عملیات (Actions)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($batches as $index => $batch)
                                @php
                                    $batchStats = $stats->get($batch->reference_number);
                                    $totalCarpets = $batchStats ? $batchStats->total_carpets : 0;
                                    $totalArea = $batchStats ? $batchStats->total_area : 0.0;
                                @endphp
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td class="font-weight-bold text-primary" style="font-size: 1.1rem; letter-spacing: 0.5px;">
                                        {{ $batch->reference_number }}
                                    </td>
                                    <td class="text-center font-weight-bold text-dark">
                                        <span class="badge badge-light border px-3 py-2 rounded-pill">{{ $totalCarpets }} قالین</span>
                                    </td>
                                    <td class="text-center font-weight-bold text-success">
                                        {{ number_format($totalArea, 2) }} m²
                                    </td>
                                    <td class="text-center">
                                        @if($batch->status == 'open')
                                            <span class="badge-status-open">
                                                <i class="fa fa-unlock-alt mr-1"></i> باز (Open)
                                            </span>
                                        @else
                                            <span class="badge-status-closed">
                                                <i class="fa fa-lock mr-1"></i> بسته (Closed)
                                            </span>
                                        @endif
                                    </td>
                                    <td>{{ $batch->created_at->format('Y-m-d H:i') }}</td>
                                    <td class="text-center">
                                        <div class="d-flex align-items-center justify-content-center" style="gap: 8px; flex-wrap: nowrap;">
                                            <a href="/dashboard/batches/{{ $batch->id }}/details" class="btn btn-sm btn-outline-primary rounded-lg font-weight-bold d-inline-flex align-items-center" style="padding: 6px 12px; gap: 4px;">
                                                <i class="fa fa-info-circle"></i> جزئیات (Details)
                                            </a>
                                            <form action="/dashboard/batches/{{ $batch->id }}/toggle-status" method="post" class="m-0 d-inline-block">
                                                @csrf
                                                @if($batch->status == 'open')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-lg font-weight-bold d-inline-flex align-items-center" style="padding: 6px 12px; gap: 4px;">
                                                        <i class="fa fa-lock"></i> غیرفعال (Close)
                                                    </button>
                                                @else
                                                    <button type="submit" class="btn btn-sm btn-outline-success rounded-lg font-weight-bold d-inline-flex align-items-center" style="padding: 6px 12px; gap: 4px;">
                                                        <i class="fa fa-unlock-alt"></i> فعال (Open)
                                                    </button>
                                                @endif
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        <i class="fa fa-folder-open-o fa-2x mb-2 d-block"></i>
                                        هیچ نمبری برای این مرحله ایجاد نشده است.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
