@extends('dsh.master')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card shadow-sm border-0">
            <div class="card-header d-flex justify-content-between align-items-center bg-white py-3">
                <h5 class="mb-0 text-primary font-weight-bold">
                    <i class="feather icon-globe mr-2"></i>مدیریت اسعار (Currencies)
                </h5>
                @can('create_currency')
                <a href="{{ route('accounting.currencies.create') }}" class="btn btn-primary btn-sm shadow-sm">
                    <i class="feather icon-plus mr-1"></i>افزودن اسعار جدید
                </a>
                @endcan
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-top-0">کد</th>
                                <th class="border-top-0">نام اسعار</th>
                                <th class="border-top-0 text-center">سمبول</th>
                                <th class="border-top-0">نرخ تبادله (1 unit = X {{ \App\Currency::getBase()->code }})</th>
                                <th class="border-top-0 text-center">اسعار پایه</th>
                                <th class="border-top-0 text-center">وضعیت</th>
                                <th class="border-top-0 text-center">عملیات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($currencies as $currency)
                            <tr class="{{ $currency->is_base_currency ? 'table-primary-light' : '' }}">
                                <td class="font-weight-bold">{{ $currency->code }}</td>
                                <td>{{ $currency->name }}</td>
                                <td class="text-center"><span class="badge badge-light px-3 py-2">{{ $currency->symbol }}</span></td>
                                <td>
                                    <span class="text-muted small">1 {{ $currency->code }} =</span>
                                    <span class="font-weight-bold text-dark">{{ number_format($currency->exchange_rate, 8) }}</span>
                                    <span class="text-muted small">{{ \App\Currency::getBase()->code }}</span>
                                </td>
                                <td class="text-center">
                                    @if($currency->is_base_currency)
                                        <span class="badge badge-pill badge-success shadow-sm">
                                            <i class="feather icon-check-circle mr-1"></i>بله
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($currency->is_active)
                                        <span class="badge badge-pill badge-info">فعال</span>
                                    @else
                                        <span class="badge badge-pill badge-secondary">غیرفعال</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm shadow-sm">
                                        @can('edit_currency')
                                        <a href="{{ route('accounting.currencies.edit', $currency->id) }}" class="btn btn-outline-primary" title="ویرایش">
                                            <i class="feather icon-edit-2"></i>
                                        </a>
                                        @endcan
                                        
                                        @if(!$currency->is_base_currency)
                                            @can('delete_currency')
                                            <button type="button" class="btn btn-outline-danger" onclick="confirmDelete('{{ $currency->id }}')" title="حذف">
                                                <i class="feather icon-trash-2"></i>
                                            </button>
                                            <form id="delete-form-{{ $currency->id }}" action="{{ route('accounting.currencies.destroy', $currency->id) }}" method="POST" style="display: none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                            @endcan
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .table-primary-light {
        background-color: rgba(63, 103, 247, 0.05);
    }
    .badge-pill {
        padding-left: 0.8em;
        padding-right: 0.8em;
    }
    .card-header h5 {
        font-size: 1.1rem;
    }
</style>

<script>
    function confirmDelete(id) {
        swal({
            title: "آیا مطمئن هستید؟",
            text: "این اسعار حذف خواهد شد و قابل بازیابی نیست!",
            icon: "warning",
            buttons: ["انصراف", "بله، حذف شود"],
            dangerMode: true,
        })
        .then((willDelete) => {
            if (willDelete) {
                document.getElementById('delete-form-' + id).submit();
            }
        });
    }
</script>
@endsection
