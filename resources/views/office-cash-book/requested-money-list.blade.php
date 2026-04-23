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
                <th>درخواست کننده</th>
                <th>مقدار</th>
                <th>توضیحات</th>
                <th>تاریخ</th>
                <th>تایید پرداخت</th>
                <th>رد نمودن پرداخت</th>
                <!-- <th>حذف</th> -->
              </tr>
              </thead>
              <tbody>
      
              @if($credits)
                @foreach($credits as $c)
                  <tr class="ur{{$c->id}}">
            
                    @if($c->user_role == 'CO')
                      <td>دفتر مرکزی</td>
                    @else
                      <td>دفتر فروشات</td>
                    @endif
                    <td>{{$c->amount}}</td>
                    <td>{{$c->description}}</td>
                    <td>{{$c->date}}</td>
                    <td>
              
                      <button onclick="approveCredit({{$c->id}})" class="btn btn-info btn-sm"><i
                                class="fa fa-tick"></i> تایید پرداخت ؟
                      </button>
                    </td>
            
                    <td>
              
                      <button onclick="deleteCredit({{$c->id}})" class="btn btn-danger btn-sm"><i
                                class="fa fa-tick"></i> رد نمودن پرداخت ؟
                      </button>
                    </td>
          
          
                  </tr>
                @endforeach
      
              @else
                <h5 style="color: red;text-align: center">هنوز درخواست صورت نگرفته</h5>
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


      function approveCredit(id) {

          swal({
              text: "آیا واقعا میخواهی که همی کاربر را حذف کنی؟",
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
                          url: '/dashboard/approve-request-money/' + id,
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

      function deleteCredit(id) {

          swal({
              text: "آیا واقعا میخواهی که همی کاربر را حذف کنی؟",
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
                          url: '/dashboard/delete-request-money/' + id,
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