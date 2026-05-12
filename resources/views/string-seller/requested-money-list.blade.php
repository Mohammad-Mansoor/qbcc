@extends('dsh.master')

@section('content')
  
  <div class="row">
    <div class="col-lg-12 col-md-12 col-sm-6 col-xs-12">
      <div class="card">
        <div class="card-header">
          <div class="alert alert-success approve" style="display:none;" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                      aria-hidden="true">&times;</span></button>
            پرداخت موفقانه تایید شد
          </div>
          
          <div class="alert alert-danger deleteAlert" style="display:none;" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                      aria-hidden="true">&times;</span></button>
            درخواست رد شد
          </div>
          
          <div class="alert alert-danger errorAlert" style="display:none;" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                      aria-hidden="true">&times;</span></button>
            پول در دخل کم است
          </div>
          <div class="btn btn-primary btn-sm hideOnPrint pull-right" onclick="printPage('MRDetails')"
               style="position: relative;right:20px;"><i class="fa fa-print"></i> Print
          </div>
        </div>

        <div class="card-body">
          <div class="static-table-list table-responsive" id="MRDetails">
            <table class="table table-hover table-xs" id="dataTable">
              <thead>
              <tr>
                <th>نام فروشنده</th>
                <th>باقیات فعلی</th>
                <th>مبلغ (دالر)</th>
                <th>مبلغ (افغانی)</th>
                <th>نوعیت</th>
                <th>فاکتور</th>
                <th>تاریخ</th>
                <th class="hideOnPrint">پیش‌نمایش حسابداری</th>
                <th class="hideOnPrint text-center">عملیات تایید/رد</th>
              </tr>
              </thead>
              <tbody>
              @if($requests->count() > 0)
                @foreach($requests as $r)
                  <tr class="ur{{$r->id}}">
                    <td>
                        <strong>{{$r->seller_name}}</strong>
                    </td>
                    <td dir="ltr">
                        @if(($r->current_balance ?? 0) > 0)
                            <span class="text-success">{{number_format($r->current_balance, 2)}}</span>
                        @elseif(($r->current_balance ?? 0) < 0)
                            <span class="text-danger">{{number_format($r->current_balance, 2)}}</span>
                        @else
                            0.00
                        @endif
                    </td>
                    <td>{{$r->amount > 0 ? number_format($r->amount, 2) : '0'}}</td>
                    <td>{{$r->amount_af > 0 ? number_format($r->amount_af, 2) : '0'}}</td>
                    <td>
                        <span class="badge badge-{{$r->type == 'رسید' ? 'success' : 'primary'}}">
                            {{$r->type}}
                        </span>
                    </td>
                    <td>{{$r->purchase_number}}</td>
                    <td>{{$r->date}}</td>
                    
                    {{-- Accounting Preview --}}
                    <td class="hideOnPrint">
                        <button class="btn btn-outline-secondary btn-xs" 
                                data-toggle="popover" 
                                data-trigger="hover" 
                                title="پیش‌نمایش حسابداری" 
                                data-html="true"
                                data-content="<b>Debit:</b> {{$r->debit_account_name}}<br><b>Credit:</b> {{$r->credit_account_name}}">
                            <i class="fa fa-calculator"></i> نمایش
                        </button>
                    </td>

                    <td class="hideOnPrint text-center">
                      <div class="btn-group">
                        <button onclick="approveRequest({{$r->id}})" class="btn btn-info btn-xs" title="تایید">
                            <i class="fa fa-check"></i>
                        </button>
                        <button onclick="deleteRequest({{$r->id}})" class="btn btn-danger btn-xs" title="رد">
                            <i class="fa fa-times"></i>
                        </button>
                      </div>
                    </td>
                  </tr>
                @endforeach
              @else
                <tr>
                    <td colspan="9" class="text-center py-4">
                        <h5 class="text-muted">هنوز درخواست تایید نشده‌ای وجود ندارد</h5>
                    </td>
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
      $(function () {
          $('[data-toggle="popover"]').popover();
      });

      function approveRequest(id) {
          swal({
              text: "ایا مطمعین هستید ؟",
              buttons: true,
              dangerMode: true,
              buttons: {
                  confirm: {text: 'بلی', className: 'btn-danger'},
                  cancel: 'نخیر'
              },
          })
          .then((willDelete) => {
              if (willDelete) {
                  $.ajax({
                      type: 'DELETE',
                      data: {
                          '_token': '{{csrf_token()}}',
                      },
                      url: '/dashboard/string-seller-approve-request/' + id,
                      success: function (res) {
                          if (res.status == 'success') {
                              $('.ur' + id).hide();
                              $('.approve').show();
                              setTimeout(() => location.reload(), 1000);
                          } else {
                              $('.errorAlert').show();
                          }
                      },
                  })
              }
          });
      }

      function deleteRequest(id) {
          swal({
              text: "آیا مطمعین هستید ؟",
              buttons: true,
              dangerMode: true,
              buttons: {
                  confirm: {text: 'بلی', className: 'btn-danger'},
                  cancel: 'نخیر'
              },
          })
          .then((willDelete) => {
              if (willDelete) {
                  $.ajax({
                      type: 'DELETE',
                      data: {
                          '_token': '{{csrf_token()}}',
                      },
                      url: '/dashboard/string-seller-delete-request/' + id,
                      success: function (res) {
                          $('.ur' + id).hide();
                          $('.deleteAlert').show();
                          setTimeout(() => location.reload(), 1000);
                      }
                  })
              }
          });
      }
  </script>
@endsection