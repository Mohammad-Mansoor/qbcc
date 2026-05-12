@extends('dsh.master')
@section('content')

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4>گزارش سرمایه در حال کار (WIP Valuation)</h4>
                    <div class="btn-group" style="float: left;">
                        <button onclick="window.print()" class="btn btn-primary btn-sm">چاپ <span class="fas fa-print"></span></button>
                    </div>
                </div>
                <div class="card-body">
                    <p class="text-muted">این گزارش سرمایه ای که در حال حاضر در مراحل تولید (شست، تیاری، کچایی) قفل شده است را نشان می دهد.</p>
                    
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>نوع</th>
                                    <th>شناسه</th>
                                    <th>نام / نمبر</th>
                                    <th>سرمایه درگیر (Investment)</th>
                                    <th>عملیات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $totalWip = 0; @endphp
                                @foreach($wipItems as $i => $item)
                                    <?php 
                                        $legacy = DB::table(str_replace('App\\', '', $item->type) == 'Carpet' ? 'carpets' : 'material_stocks')
                                            ->where(str_replace('App\\', '', $item->type) == 'Carpet' ? 'carpet_id' : 'id', $item->ref_id)
                                            ->first();
                                        $totalWip += $item->total_investment;
                                    ?>
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td>{{ $item->type == 'App\Carpet' ? 'قالین' : 'مواد' }}</td>
                                        <td>{{ $item->ref_id }}</td>
                                        <td>
                                            @if($item->type == 'App\Carpet' && $legacy)
                                                {{ $legacy->carpet_no ?? 'N/A' }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>${{ number_format($item->total_investment, 2) }}</td>
                                        <td>
                                            <a href="{{ route('inventory.reports.detail', $item->id) }}" class="btn btn-info btn-sm">Audit Trail</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="font-weight-bold bg-light">
                                    <td colspan="4" class="text-right">مجموع سرمایه در جریان تولید (WIP Total):</td>
                                    <td>${{ number_format($totalWip, 2) }}</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
