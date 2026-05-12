@extends('dsh.master')
@section('title', 'حساب‌های مصارف ماهانه')
@section('content')
<div class="container-fluid px-4 py-4 text-right">
    <!-- Action Header -->
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h3 class="mb-0 font-weight-bold text-dark"><i class="fa fa-folder-open text-primary mr-2"></i> مدیریت حسابات ماهانه</h3>
            <p class="text-muted small mb-0">ایجاد و سازماندهی مصارف بر اساس ماه و سال</p>
        </div>
    </div>

    <!-- Info Box (Dari) -->
    <div class="alert bg-soft-primary border-0 rounded-lg p-4 mb-4 shadow-sm">
        <div class="d-flex align-items-start">
            <div class="ml-3">
                <i class="fa fa-info-circle fa-2x text-primary"></i>
            </div>
            <div>
                <h6 class="font-weight-bold text-primary mb-1">راهنمای حسابات ماهانه:</h6>
                <p class="mb-0 text-dark small leading-relaxed">
                    در این بخش می‌توانید حساب‌های مصارف ماهانه را برای نظم بیشتر ایجاد کنید. 
                    <br>
                    • <strong>ایجاد حساب:</strong> برای هر ماه و سال یک پوشه (حساب) جداگانه ایجاد کنید تا مصارف همان دوره در آن ثبت شود.
                    <br>
                    • <strong>مشاهده و ثبت:</strong> با کلیک بر روی دکمه «نمایش مصارف»، وارد لیست پرداختی‌های آن ماه شده و می‌توانید مصارف را ثبت یا ویرایش کنید.
                </p>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- New Account Form -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-lg overflow-hidden mb-4">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="mb-0 font-weight-bold text-dark">
                        <i class="fa {{ $accountEdit ? 'fa-edit' : 'fa-plus-circle' }} mr-2"></i>
                        {{ $accountEdit ? 'ویرایش حساب' : 'ایجاد حساب جدید' }}
                    </h5>
                </div>
                <div class="card-body bg-soft-light border-top">
                    <form action="/dashboard/monthly-expense-accounts{{ $accountEdit ? '/'.$accountEdit->id : '' }}" method="post">
                        @csrf
                        @if($accountEdit) @method('PUT') @endif
                        
                        <div class="form-group">
                            <label class="small font-weight-bold">نام ماه</label>
                            <select name="month_name" class="form-control border-0 shadow-sm">
                                @php($mn = ['حمل', 'ثور', 'جوزا', 'سرطان', 'اسد', 'سنبله', 'میزان', 'عقرب', 'قوس', 'جدی', 'دلو', 'حوت'])
                                @foreach($mn as $m)
                                    <option {{ ($accountEdit && $accountEdit->month_name == $m) ? 'selected' : '' }}>{{ $m }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="small font-weight-bold">سال</label>
                            <input name="year_name" type="number" value="{{ $accountEdit->year_name ?? date('Y') }}" class="form-control border-0 shadow-sm text-right">
                        </div>
                        
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary btn-block shadow-sm font-weight-bold py-2">
                                <i class="fa fa-save mr-1"></i> ذخیره حساب
                            </button>
                            @if($accountEdit)
                                <a href="/dashboard/monthly-expense-accounts" class="btn btn-light btn-block mt-2">انصراف</a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Accounts List -->
        <div class="col-md-8">
            <div class="card border-0 shadow-sm rounded-lg overflow-hidden">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="mb-0 font-weight-bold text-dark"><i class="fa fa-list text-muted mr-2"></i> لیست حساب‌های ایجاد شده</h5>
                </div>
                <div class="card-body p-0">
                    @if(session("status") || session("error"))
                        <div class="alert {{ session('status') ? 'alert-success' : 'alert-danger' }} status mx-3 mt-3 shadow-none border-0 rounded-pill text-center py-2 small">
                            {{ session('status') ?: session('error') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-muted small text-uppercase">
                                <tr>
                                    <th class="px-4 py-3 border-0">نام ماه</th>
                                    <th class="py-3 border-0">سال</th>
                                    <th class="px-4 py-3 border-0 text-left">عملیات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($months as $m)
                                <tr class="border-bottom">
                                    <td class="px-4 py-3 font-weight-bold text-dark">{{ $m->month_name }}</td>
                                    <td class="py-3">{{ $m->year_name }}</td>
                                    <td class="px-4 py-3 text-left">
                                        <a href="/dashboard/monthly-expense-accounts/{{ $m->me_id }}" class="btn btn-soft-primary btn-sm rounded-pill px-3 shadow-none font-weight-bold">
                                            <i class="fa fa-eye mr-1"></i> نمایش مصارف
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="py-5 text-center text-muted italic">هیچ حسابی ثبت نشده است.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-soft-primary { background-color: rgba(0, 123, 255, 0.1); }
    .bg-soft-light { background-color: #f8f9fa; }
    .btn-soft-primary { background-color: rgba(0, 123, 255, 0.1); color: #007bff; border: none; }
    .leading-relaxed { line-height: 1.6; }
</style>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('.status').fadeIn().delay(3000).fadeOut();
    });
</script>
@endsection
