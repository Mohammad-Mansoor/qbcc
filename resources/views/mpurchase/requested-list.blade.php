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
  
                <th>فاکتور خرید</th>
                <th>فروشنده</th>
                <th>تاریخ خرید</th>
                <th>کتگوری مواد</th>
                <th>نوعیت مواد</th>
                <th>مقدار</th>
                <th>قیمت فی کیلو</th>
                <th>قیمت مجموع افغانی</th>
                <th>قیمت مجموع دالر</th>
                <th>قیمت به حروف</th>
                <td class="hideOnPrint">تایید درخواست</td>
                <td class="hideOnPrint">رد نمودن درخواست</td>
                
                <!-- <th>حذف</th> -->
              </tr>
              </thead>
              <tbody>
              
              
              
              
              @if($requests)
  
                @foreach($requests as $p)
                  <tr>
                    <td>{{$p->purchase_number}}</td>
                    <td>{{ $p->seller->name }}</td>
                    <td dir="ltr"
                        style="text-align: right;">{{ \Carbon\Carbon::parse($p->purchase_date)->format('d-M-Y') }}</td>
                    <td>{{ $p->materialCategory->material_category }}</td>
                    <td>{{ $p->materialType->material_type }}</td>
      
                    <td dir="ltr">{{ $p->quantity }} KG</td>
                    <td dir="ltr">{{ $p->price_per_kilo .'AF' }}</td>
                    <td dir="ltr">AF {{ $p->total_af}}</td>
                    <td dir="ltr">$ {{ $p->total}}</td>
      
                    <td>{{ $p->in_words .' ' }}</td>
      
                   
                    <td class="hideOnPrint">
                      
                      <button onclick="approveRequest({{$p->id}})" class="btn btn-info btn-sm"><i
                                class="fa fa-tick"></i> تایید درخواست ؟
                      </button>
                    </td>
                    
                    <td class="hideOnPrint">
                      
                      <button onclick="deleteRequest({{$p->id}})" class="btn btn-danger btn-sm"><i
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
          <p class="text-center">{{$requests->links()}}</p>
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
                          url: '/dashboard/purchase-material-approve-request/' + id,
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
                          url: '/dashboard/purchase-material-delete-request/' + id,
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