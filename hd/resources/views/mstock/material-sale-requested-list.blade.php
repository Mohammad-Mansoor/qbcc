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
  
                <th>فاکتور فروش</th>
                <th>نام نماینده</th>
                <th> مقدار مواد</th>
                <th>قیمت فی کیلو</th>
                <th>قیمت مجموع به افغانی</th>
                <th>قیمت مجموع به دالر</th>
                <th>کتگوری</th>
                <th> نوعیت مواد</th>
                <th> تاریخ</th>
                <th class="hideOnPrint">تایید درخواست</th>
                <th class="hideOnPrint">رد نمودن درخواست</th>
                
                <!-- <th>حذف</th> -->
              </tr>
              </thead>
              <tbody>
              
              
              
              
              @if($requests)
  
                @foreach($requests as $material)
                  <tr>
                    <td>{{$material->sale_number}}</td>
                    <td>{{$material->agent->user->name}}</td>
                    <td>{{$material->amount . "kg"}}</td>
                    <td>{{$material->price . "AFG"}}</td>
                    <td>AF{{$material->total_price_af }}</td>
                    <td>${{$material->total_price }}</td>
                    <td>{{$material->category->material_category}}</td>
                    <td>{{$material->type->material_type}}</td>
                    <td>{{$material->date}}</td>
      
                   
                    <td class="hideOnPrint">
                      
                      <button onclick="approveRequest({{$material->id}})" class="btn btn-info btn-sm"><i
                                class="fa fa-tick"></i> تایید درخواست ؟
                      </button>
                    </td>
                    
                    <td class="hideOnPrint">
                      
                      <button onclick="deleteRequest({{$material->id}})" class="btn btn-danger btn-sm"><i
                                class="fa fa-tick"></i> رد نمودن درخواست ؟
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