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
  
  
                <th>شماره قالین</th>
                <th>نمبر تیاری</th>
                <th> قیمت تیاری</th>
                <th>تاریخ تیاری</th>
                <th>تیم تیاری</th>
                <th>نوع تیاری</th>
                <th>شرح</th>
                <th class="hideOnPrint"><b>تایید پرداخت</b></th>
                <th class="hideOnPrint"><b>رد نمودن پرداخت</b></th>
                
                <!-- <th>حذف</th> -->
              </tr>
              </thead>
              <tbody>
              
              
              
              
              @if($requests)
                
                @foreach($requests as $r)
                  
                    
                    <tr class="ur{{$r->id}}">
                      <td>{{$r->carpet->carpet_no ?? ''}}</td>
                      <td>{{$r->finish_number}}</td>
                      <td style="direction: ltr">{{round($r->price_af,2) ?? ''}} AF</td>
                      <td>{{$r->date}}</td>
                      <td>{{$r->team->name ?? ''}}</td>
                      <td>{{$r->category->category ?? ''}}</td>
                      <td>{{$r->description}}</td>
                  
                      <td class="hideOnPrint">
                        
                        <button onclick="approveRequest({{$r->id}})" class="btn btn-info btn-sm"><i
                                  class="fa fa-tick"></i> تایید پرداخت ؟
                        </button>
                      </td>
                      
                      <td class="hideOnPrint">
                        
                        <button onclick="deleteRequest({{$r->id}})" class="btn btn-danger btn-sm"><i
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
                          url: '/dashboard/refinish-approve-request/' + id,
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
                          url: '/dashboard/refinish-delete-request/' + id,
                          success: function (res) {

                              $('.ur' + id).hide();
                              $('.deleteAlert').show();

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