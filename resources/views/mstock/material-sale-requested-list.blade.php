@extends('dsh.master')

@section('content')
  
  
  <div class="row">
    <div class="col-lg-12 col-md-12 col-sm-6 col-xs-12">
      <div class="card">
        <div class="card-header">
          <div class="alert alert-success approve" style="display:none;" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                      aria-hidden="true">&times;</span></button>
            درخواست موفقانه تایید شد
          </div>
          
          <div class="alert alert-danger deleteAlert" style="display:none;" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                      aria-hidden="true">&times;</span></button>
            درخواست رد شد
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
                <th>فاکتور</th>
                <th>نماینده</th>
                <th>گدام</th>
                <th>مقدار (kg)</th>
                <th>موجودی</th>
                <th>قیمت مجموع (AFN)</th>
                <th>مفاد تخمینی</th>
                <th>نوعیت</th>
                <th>تاریخ</th>
                <th>حسابات</th>
                <th class="hideOnPrint text-center">عملیات</th>
              </tr>
              </thead>
              <tbody>
              @if($requests->count() > 0)
                @foreach($requests as $material)
                  @php
                    $profit = $material->total_price_af - ($material->amount * $material->estimated_wac);
                    $stockStatus = ($material->available_stock >= $material->amount) ? 'success' : 'danger';
                  @endphp
                  <tr>
                    <td><span class="badge badge-light">{{$material->sale_number}}</span></td>
                    <td><strong>{{$material->agent->user->name}}</strong></td>
                    <td>{{$material->warehouse->name ?? 'گدام مرکزی'}}</td>
                    <td>{{$material->amount}} kg</td>
                    <td>
                        <span class="badge badge-{{$stockStatus}}">
                            {{ number_format($material->available_stock, 1) }} kg
                        </span>
                    </td>
                    <td>AF{{ number_format($material->total_price_af, 2) }}</td>
                    <td>
                        <span class="text-{{ $profit >= 0 ? 'success' : 'danger' }} font-weight-bold">
                            AF{{ number_format($profit, 2) }}
                        </span>
                        <br><small class="text-muted">WAC: {{ number_format($material->estimated_wac, 2) }}</small>
                    </td>
                    <td>{{$material->category->material_category}} - {{$material->type->material_type}}</td>
                    <td><small>{{$material->date}}</small></td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary p-1" 
                                data-toggle="popover" 
                                data-trigger="hover"
                                title="Accounting Mappings"
                                data-html="true"
                                data-content="
                                    <div class='small'>
                                        <strong>Revenue Dr:</strong> {{ $material->debitAccount->account_name ?? 'Default' }}<br>
                                        <strong>Revenue Cr:</strong> {{ $material->creditAccount->account_name ?? 'Default' }}<br>
                                        <hr class='my-1'>
                                        <strong>COGS Dr:</strong> {{ $material->cogsDebitAccount->account_name ?? 'Default' }}<br>
                                        <strong>COGS Cr:</strong> {{ $material->cogsCreditAccount->account_name ?? 'Default' }}
                                    </div>
                                ">
                            <i class="fa fa-university"></i>
                        </button>
                    </td>
                    <td class="hideOnPrint text-center">
                      <div class="btn-group">
                          <button onclick="approveRequest({{$material->id}})" class="btn btn-success btn-xs" title="تایید">
                            <i class="fa fa-check"></i>
                          </button>
                          <button onclick="deleteRequest({{$material->id}})" class="btn btn-danger btn-xs" title="رد">
                            <i class="fa fa-times"></i>
                          </button>
                      </div>
                    </td>
                  </tr>
                @endforeach
              @else
                <tr>
                    <td colspan="11" class="text-center py-4">
                        <h5 class="text-muted">هنوز درخواست فروش ثبت نشده است</h5>
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
      $(document).ready(function() {
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
                          url: '/dashboard/material-sale-approve-request/' + id,
                          success: function (res) {

                              if (res.status == 'success') {
                                  $('.ur' + id).hide();
                                  $('.approve').show();

                              } else {
                                  $('.errorAlert').show();
                              }
                              location.reload();


                              window.setTimeout(function () {
                                  $(".approve").fadeTo(500, 0).slideUp(500, function () {

                                      $(this).remove();
                                  });
                              }, 2000);
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
                          url: '/dashboard/material-sale-delete-request/' + id,
                          success: function (res) {

                              $('.ur' + id).hide();
                              $('.deleteAlert').show();

                              location.reload();

                              window.setTimeout(function () {
                                  $(".deleteAlert").fadeTo(500, 0).slideUp(500, function () {

                                      $(this).remove();
                                  });
                              }, 2000);
                          }

                      })
                  }
              });
      }
  </script>
@endsection