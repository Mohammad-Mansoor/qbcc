@extends('dsh.master')

@section('content')
<div class="container-fluid no-print-padding">
    <br class="no-print">
    
    <!-- Header -->
    <div class="row mb-4 no-print">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-7">
                            <h3 class="font-weight-bold mb-1">تفتیش اصلاحات و ریورس (Audit Corrections)</h3>
                            <p class="text-muted mb-0">نظارت بر تمامی معاملات اصلاحی و ریورس شده در سیستم</p>
                        </div>
                        <div class="col-md-5">
                            <form action="{{ route('accounting.reports.audit_corrections') }}" method="GET">
                                <div class="row no-gutters align-items-end justify-content-end">
                                    <div class="col-md-5 px-1">
                                        <label class="small font-weight-bold text-muted mb-1">از تاریخ:</label>
                                        <input type="date" name="start_date" value="{{ $startDate }}" class="form-control bg-light border-0 rounded-pill">
                                    </div>
                                    <div class="col-md-5 px-1">
                                        <label class="small font-weight-bold text-muted mb-1">تا تاریخ:</label>
                                        <input type="date" name="end_date" value="{{ $endDate }}" class="form-control bg-light border-0 rounded-pill">
                                    </div>
                                    <div class="col-md-2 px-1">
                                        <button type="submit" class="btn btn-danger btn-block rounded-pill shadow-sm"><i class="feather icon-filter"></i></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Audit List -->
    <div class="row">
        <div class="col-md-12">
            <div class="card border-0 shadow-lg printable-document" style="border-radius: 20px;">
                <div class="card-body p-5">
                    
                    <div class="text-center mb-5 d-none d-print-block">
                        <h2 class="font-weight-bold text-danger">گزارش تفتیش معاملات اصلاحی</h2>
                        <p>دوره: {{ $startDate }} الی {{ $endDate }}</p>
                    </div>

                    <table class="table table-hover border">
                        <thead class="bg-light">
                            <tr>
                                <th class="py-3 px-4">نمبر معامله (Trans ID)</th>
                                <th class="py-3 px-4">تاریخ</th>
                                <th class="py-3 px-4">مرجع (Reference)</th>
                                <th class="py-3 px-4">دلیل و توضیحات (Reason)</th>
                                <th class="py-3 text-center px-4">حالت</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($report as $row)
                            <tr>
                                <td class="py-3 px-4 font-weight-bold text-danger">#{{ $row->id }}</td>
                                <td class="py-3 px-4">{{ $row->date }}</td>
                                <td class="py-3 px-4"><span class="badge badge-light-danger">{{ $row->reference }}</span></td>
                                <td class="py-3 px-4">{{ $row->description }}</td>
                                <td class="py-3 text-center px-4">
                                    <span class="badge badge-danger px-3 py-2 rounded-pill shadow-sm">ریورس شده</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="py-5 text-center text-muted">هیچ معامله اصلاحی در این دوره یافت نشد.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-5 no-print text-right">
                        <button onclick="window.print()" class="btn btn-danger btn-lg px-5 rounded-pill shadow-lg"><i class="feather icon-alert-triangle mr-2"></i> چاپ لیست تفتیش</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .badge-light-danger { background: #fbe9e7; color: #d50000; font-weight: bold; }
    @media print {
        .no-print { display: none !important; }
        .printable-document { border: none !important; box-shadow: none !important; }
    }
</style>
@endsection
