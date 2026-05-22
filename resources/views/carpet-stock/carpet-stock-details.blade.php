@extends('dsh.master')
@section('title' , 'جزییات و سوابق قالین')
@section('content')
  
  <div class="row">
    <div class="col-lg-12">
        <div class="mb-3 hideOnPrint d-flex justify-content-between align-items-center">
            <a href="/dashboard/carpet-stock" class="btn btn-outline-secondary btn-sm">
                <i class="fa fa-arrow-right"></i> بازگشت به موجودی
            </a>
            <button class="btn btn-primary btn-sm" onclick="printPage('printCarpet')">
                <i class="fa fa-print"></i> چاپ گزارش
            </button>
        </div>
    </div>
  </div>

  <div class="row" id="printCarpet">
    <!-- LEFT COLUMN: Product Details -->
    <div class="col-lg-6 col-md-6 col-sm-12">
      <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white border-bottom-0 pt-4">
          <h4 class="font-weight-bold text-primary"><i class="fa fa-info-circle"></i> مشخصات تخنیکی قالین</h4>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-borderless table-sm">
              <tbody>
                <tr class="border-bottom">
                  <td class="py-2 text-muted">شماره قالین:</td>
                  <td class="py-2 font-weight-bold">{{ $carpet->carpet_no }}</td>
                </tr>
                <tr class="border-bottom">
                  <td class="py-2 text-muted">نوعیت قالین:</td>
                  <td class="py-2"><span class="badge badge-soft-primary">{{ $carpet->type->carpet_type ?? '---' }}</span></td>
                </tr>
                <tr class="border-bottom">
                  <td class="py-2 text-muted">ابعاد (طول × عرض):</td>
                  <td class="py-2" style="direction: ltr; text-align: right;">{{ $carpet->height }}m × {{ $carpet->width }}m</td>
                </tr>
                <tr class="border-bottom">
                  <td class="py-2 text-muted">مساحت کل:</td>
                  <td class="py-2 font-weight-bold text-dark">{{ $carpet->area }} m²</td>
                </tr>
                <tr class="border-bottom">
                  <td class="py-2 text-muted">نقشه / کوالتی:</td>
                  <td class="py-2">{{ $carpet->map_number }} / <span class="badge badge-light border">{{ $carpet->quality->quality ?? '---' }}</span></td>
                </tr>
                <tr class="border-bottom">
                  <td class="py-2 text-muted">زمینه و حاشیه:</td>
                  <td class="py-2">{{ $carpet->field }} / {{ $carpet->margin }}</td>
                </tr>
                <tr class="border-bottom">
                  <td class="py-2 text-muted">محل نگهداری (گدام):</td>
                  <td class="py-2">
                    <div class="font-weight-bold text-info">{{ $carpet->warehouse->name ?? 'نامشخص' }}</div>
                    <small class="text-muted">{{ $carpet->warehouse->location ?? '' }}</small>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <h5 class="mt-4 font-weight-bold text-dark border-bottom pb-2">تحلیل قیمت تمام شد (به دالر - USD)</h5>
          <div class="table-responsive">
            <table class="table table-sm">
              <tbody>
                <tr>
                  <td class="text-muted">قیمت ابتدائی (خرید/تولید):</td>
                  <td class="text-right font-weight-bold">{{ number_format($carpet->carpet_price_us, 2) }} $</td>
                </tr>
                @php($tamam_shod = $carpet->carpet_price_us)
                <tr>
                  <td class="text-muted">مصرف کچایی:</td>
                  <td class="text-right">
                    @if($kachaee_expense)
                        {{ number_format($kachaee_expense->total_price, 2) }} $
                        @php($tamam_shod += $kachaee_expense->total_price)
                    @else
                        0.00 $
                    @endif
                  </td>
                </tr>
                <tr>
                  <td class="text-muted">مصرف شستشو:</td>
                  <td class="text-right">{{ number_format($wash_expense, 2) }} $</td>
                  @php($tamam_shod += $wash_expense)
                </tr>
                @php($total_finishing = 0)
                @foreach ($finishing_expense as $finish)
                <tr>
                  <td class="text-muted small">مصرف {{ $finish->category->category }}:</td>
                  <td class="text-right small text-info">{{ number_format($finish->price, 2) }} $</td>
                  @php($total_finishing += $finish->price)
                </tr>
                @endforeach
                <tr class="bg-light">
                  <td class="font-weight-bold">مجموع هزینه نهایی:</td>
                  <td class="text-right font-weight-bold text-primary">{{ number_format($tamam_shod + $total_finishing, 2) }} $</td>
                </tr>
                <tr>
                  <td class="text-muted">قیمت تمام شد فی متر مربع:</td>
                  <td class="text-right font-weight-bold">{{ $carpet->area > 0 ? number_format(($tamam_shod + $total_finishing) / $carpet->area, 2) : '0.00' }} $</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- RIGHT COLUMN: Activity History -->
    <div class="col-lg-6 col-md-6 col-sm-12">
      <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white border-bottom-0 pt-4">
          <h4 class="font-weight-bold text-success"><i class="fa fa-history"></i> سوابق و جریانات (Activity Log)</h4>
        </div>
        <div class="card-body">
          <div class="activity-timeline">
            @if(count($history) > 0)
                <ul class="list-unstyled">
                    @foreach($history as $act)
                        <li class="mb-4 position-relative pl-4" style="border-right: 2px solid #e9ecef; padding-right: 20px;">
                            <div class="d-flex justify-content-between">
                                <span class="font-weight-bold text-dark">{{ $act->action }}</span>
                                <small class="text-muted">{{ \Carbon\Carbon::parse($act->created_at)->format('Y-m-d H:i') }}</small>
                            </div>
                            <p class="mb-1 text-muted small">{{ $act->description }}</p>
                            <div class="small">
                                <span class="badge badge-soft-info"><i class="fa fa-user-circle"></i> کاربر: {{ $act->user->name ?? 'سیستم' }}</span>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="text-center py-5">
                    <i class="fa fa-info-circle fa-2x text-muted opacity-25 mb-3"></i>
                    <p class="text-muted">هیچ سابقه فعالیتی برای این قالین ثبت نشده است.</p>
                </div>
            @endif
          </div>
        </div>
      </div>

      <!-- Quick Actions (Only on Details Page) -->
      <div class="card shadow-sm border-0 hideOnPrint">
          <div class="card-body bg-soft-info rounded">
              <h6 class="font-weight-bold text-info mb-3">راهنمای بخش</h6>
              <p class="small mb-0">در این بخش می‌توانید تمامی مراحل تولید، شستشو و پرداخت (Finishing) این قالین را به همراه هزینه های مرتبط مشاهده نمایید. سوابق فعالیت ها بر اساس آخرین تغییرات نمایش داده شده است.</p>
          </div>
      </div>
    </div>
  </div>

@endsection

@push('styles')
<style>
    .badge-soft-primary { background-color: #e7f1ff; color: #007bff; }
    .badge-soft-info { background-color: #e0f7fa; color: #00bcd4; }
    .bg-soft-info { background-color: #f0faff; }
    @media print {
        .hideOnPrint { display: none !important; }
        .card { border: 1px solid #ddd !important; shadow: none !important; }
    }
</style>
@endpush
