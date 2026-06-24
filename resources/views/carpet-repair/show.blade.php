@extends('dsh.master')
@section('title', 'مشخصات ترمیم قالین')
@section('content')

  <style>
    /* PREMIUM GLASSMORPHISM UI */
    .glass-card {
      background: white;
      border: 1px solid var(--QBIC-border);
      border-radius: var(--radius-lg);
      box-shadow: var(--shadow-soft);
      margin-bottom: 30px;
      overflow: hidden;
      transition: all 0.3s ease;
    }

    .glass-header {
      background: var(--QBIC-surface);
      padding: 20px 25px;
      border-bottom: 1px solid var(--QBIC-border);
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .glass-header h4 {
      margin: 0;
      font-weight: 700;
      color: var(--QBIC-primary);
      font-size: 1.2rem;
    }

    .form-section-title {
      color: #2e7d32;
      font-weight: 700;
      border-bottom: 2px solid #e8f5e9;
      padding-bottom: 10px;
      margin-bottom: 20px;
    }

    .detail-table {
      width: 100%;
      margin-bottom: 1rem;
      color: #212529;
      border-collapse: collapse;
    }

    .detail-table th {
      background-color: #f8fafc;
      color: #475569;
      font-weight: 700;
      padding: 12px 15px;
      border-bottom: 2px solid #e2e8f0;
      text-align: right;
    }

    .detail-table td {
      padding: 12px 15px;
      border-bottom: 1px solid #f1f5f9;
      text-align: right;
      font-size: 0.95rem;
    }

    .detail-table tr:hover {
      background-color: #f8fafc;
    }

    .badge-premium {
      background-color: #e8f5e9;
      color: #2e7d32;
      padding: 5px 12px;
      border-radius: 6px;
      font-weight: 600;
      font-size: 0.85rem;
    }

    .btn-premium {
      border-radius: 8px;
      font-weight: 600;
      padding: 10px 25px;
      transition: all 0.2s;
    }
  </style>

  <div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <div class="glass-card">
        <div class="glass-header">
          <h4><i class="fa fa-info-circle text-primary mr-2"></i> مشخصات عمومی ترمیم قالین (Carpet Repair Specifications)
          </h4>
          <a href="/dashboard/carpet-repair" class="btn btn-light btn-premium shadow-sm"><i
              class="fa fa-arrow-left mr-2"></i> بازگشت</a>
        </div>
        <div class="card-body p-4">

          <div class="row mb-5">
            <div class="col-lg-6 col-md-6 col-sm-12 mb-4">
              <h6 class="form-section-title"><i class="fa fa-diamond mr-2"></i> مشخصات عمومی قالین (Carpet General
                Specifications)</h6>
              <div class="table-responsive">
                <table class="detail-table">
                  <thead>
                    <tr>
                      <th>مشخصات (Spec)</th>
                      <th>مقادیر (Value)</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td class="text-muted">شماره قالین (Carpet Number)</td>
                      <td class="font-weight-bold">{{$carpet->carpet->carpet_no}}</td>
                    </tr>
                    <tr>
                      <td class="text-muted">شماره فرمایش (Order Number)</td>
                      <td class="font-weight-bold text-primary">
                        {{ optional($carpet->carpet->carpet_order)->order_number ?? 'بدون نمبر فرمایش (No Order)' }}
                      </td>
                    </tr>
                    <tr>
                      <td class="text-muted">طول قالین (Length)</td>
                      <td style="direction: ltr; font-weight: 600;">{{ $carpet->carpet->height }} m</td>
                    </tr>
                    <tr>
                      <td class="text-muted">عرض قالین (Width)</td>
                      <td style="direction: ltr; font-weight: 600;">{{ $carpet->carpet->width }} m</td>
                    </tr>
                    <tr>
                      <td class="text-muted">مساحت قالین (Area)</td>
                      <td style="direction: ltr; font-weight: 600;" class="text-success">{{ $carpet->carpet->area }} m²
                      </td>
                    </tr>
                    <tr>
                      <td class="text-muted">شماره نقشه (Map Number)</td>
                      <td class="font-weight-bold">{{ $carpet->carpet->map_number ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                      <td class="text-muted">زمینه قالین (Field)</td>
                      <td>{{ $carpet->carpet->field ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                      <td class="text-muted">حاشیه قالین (Margin)</td>
                      <td>{{ $carpet->carpet->margin ?? 'N/A' }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <div class="col-lg-6 col-md-6 col-sm-12 mb-4">
              <h6 class="form-section-title"><i class="fa fa-wrench mr-2"></i> مشخصات عمومی ترمیم قالین (Repair General
                Specifications)</h6>
              <div class="table-responsive">
                <table class="detail-table">
                  <thead>
                    <tr>
                      <th>مشخصات (Spec)</th>
                      <th>مقادیر (Value)</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td class="text-muted">کچایی نمبر (Kachaee Number)</td>
                      <td class="font-weight-bold">{{ $carpet->kachaee_number ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                      <td class="text-muted">تیم ترمیم کننده (Repair Team)</td>
                      <td><span class="badge-premium">{{ optional($carpet->team)->name ?? 'N/A' }}</span></td>
                    </tr>
                    <tr>
                      <td class="text-muted">واحد پولی (Transaction Currency)</td>
                      <td class="font-weight-bold">{{ $carpet->currency_code ?? 'AFN' }}</td>
                    </tr>
                    <tr>
                      <td class="text-muted">نرخ تبادله (Exchange Rate to USD)</td>
                      <td style="direction: ltr; font-weight: 600;">{{ number_format($carpet->exchange_rate ?? 1.0, 4) }}
                      </td>
                    </tr>
                    <tr>
                      <td class="text-muted">هزینه ترمیم به ارز محلی (Local Total Cost)</td>
                      <td style="direction: ltr; font-weight: 700;" class="text-success">
                        {{ number_format($carpet->af_total_price, 2) }} {{ $carpet->currency_code ?? 'AFN' }}
                      </td>
                    </tr>
                    <tr>
                      <td class="text-muted">هزینه ترمیم به دالر (USD Equivalent)</td>
                      <td style="direction: ltr; font-weight: 700;" class="text-primary">
                        $ {{ number_format($carpet->total_price, 2) }}
                      </td>
                    </tr>
                    <tr>
                      <td class="text-muted">تاریخ ترمیم (Repair Date)</td>
                      <td>{{ $carpet->date }}</td>
                    </tr>
                    <tr>
                      <td class="text-muted">شرح ترمیم (Description / Audit Notes)</td>
                      <td class="text-muted small" style="max-width: 250px; word-wrap: break-word;">
                        {{ $carpet->description ?? 'بدون شرح' }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>

@endsection