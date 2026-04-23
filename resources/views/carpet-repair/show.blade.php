@extends('dsh.master')

@section('content')
  <div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <div class="card">
        <div class="card-body">
          <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
              <h4 style="text-align:center">مشخصات عمومی قالین</h4>
              <div class="static-table-list">
                <table class="table text-center" id="repairShow">
                  <thead>
                  <tr>
                    <th>مشخصات</th>
                    <th>مقادیر</th>
                    <th>مشخصات</th>
                    <th>مقادیر</th>
                  </tr>
                  </thead>
                  <tbody>
                  <tr>
                    <td>شماره قالین</td>
                    <td>{{$carpet->carpet->carpet_no}}</td>
                    <td>شماره فرمایش</td>
                    <td>{{$carpet->carpet->carpet_order->order_number}}</td>
                  </tr>
                  <tr>
                    <td>طول قالین</td>
                    <td style="direction: ltr;text-align:right">{{ $carpet->carpet->height }} m</td>
                    <td>عرض قالین</td>
                    <td style="direction: ltr;text-align:right">{{ $carpet->carpet->width }} m</td>
                  </tr>
                  <tr>
                    <td>مساحت قالین</td>
                    <td style="direction: ltr;text-align:right">{{ $carpet->carpet->area }} m <sup>2</sup></td>
                    <td>شماره نقشه قالین</td>
                    <td>{{ $carpet->carpet->map_number }}</td>
                  </tr>
                  <tr>
                    <td>زمینه قالین</td>
                    <td>{{ $carpet->carpet->field }} </td>
                    <td>حاشیه قالین</td>
                    <td>{{ $carpet->carpet->margin }}</td>
                  </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
              <h4 style="text-align:center">مشخصات عمومی ترمیم قالین</h4>
              <div class="static-table-list">
                <table class="table text-center" id="repairShow">
                  <thead>
                  <tr>
                    <th>مشخصات</th>
                    <th>مقادیر</th>
                    <th>مشخصات</th>
                    <th>مقادیر</th>
                  </tr>
                  </thead>
                  <tbody>
                  <tr>
                    <td>هزینه ترمیم قالین</td>
                    <td style="direction: ltr;text-align:right">{{$carpet->af_total_price}} af</td>
                    <td>تاریخ ترمیم قالین</td>
                    <td>{{$carpet->date}}</td>
                  </tr>
                  <tr>
                    <td>تیم ترمیم کننده قالین</td>
                    <td>{{ $carpet->team->name }}</td>
                    <td>شرح ترمیم قالین</td>
                    <td>{{ $carpet->description }} </td>
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

@section('scripts')
  
  <script>

      $('.status').show();
      window.setTimeout(function () {
          $(".status").fadeTo(500, 0).slideUp(500, function () {

              $(this).remove();
          });
      }, 2000);

      function RemoveEmployee(id) {
          swal({
              text: "آیا واقعا میخواهی که همی کارمند را حذف کنی؟",
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
                          method: 'DELETE',
                          data: {'_token': '{{ csrf_token() }}'},
                          url: '/dashboard/carpet-repair/' + id,
                          success: function (data) {
                              $('.ur' + id).hide();
                              $('.alert').show();
                              window.setTimeout(function () {
                                  $(".alert").fadeTo(500, 0).slideUp(500, function () {

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