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
                
                
                <td><b>نام شست گر</b></td>
                <td><b>رسید(دالر)</b></td>
                <td><b>گرفت(دالر)</b></td>
                <td><b>رسید(افغانی)</b></td>
                <td><b>گرفت(افغانی)</b></td>
                <td><b>شست نمبر</b></td>
                <td><b>تفصیلات</b></td>
                <td><b>تاریخ</b></td>
                <td class="hideOnPrint"><b>تایید پرداخت</b></td>
                <td class="hideOnPrint"><b>رد نمودن پرداخت</b></td>
                
                <!-- <th>حذف</th> -->
              </tr>
              </thead>
              <tbody>
              
              
              
              
              @if($requests)
               
                @foreach($requests as $r)
                  
                  
                    <?php

                   
                    
                    $washing_name = DB::table('washing_teams')
                        ->where('id', $r->team_id)->first();
                    ?>
                  
                  
                  <tr class="ur{{$r->id}}">
                    <td>{{$washing_name->name}}</td>
                    
                    
                    @if($r->type == 'رسید')
                      @if($r->amount > 0)
                        <td>{{$r->amount}} </td>
                      @else
                        <td>0</td>
                      @endif
                    @else
                      <td>0</td>
                    @endif
                    @if($r->type == 'گرفت')
                      @if($r->amount > 0)
                        <td>{{$r->amount}}</td>
                      @else
                        <td>0</td>
                      @endif
                    @else
                      <td>0</td>
                    @endif
                    
                    @if($r->type == 'رسید')
                      @if($r->amount_af > 0)
                        <td>{{$r->amount_af}} </td>
                      @else
                        <td>0</td>
                      @endif
                    @else
                      <td>0</td>
                    @endif
                    @if($r->type == 'گرفت')
                      @if($r->amount_af > 0)
                        <td>{{$r->amount_af}}</td>
                      @else
                        <td>0</td>
                      @endif
                    @else
                      <td>0</td>
                    @endif
                      <td>{{$r->wash_number}}</td>
                  
                    <td>{{$r->description}}</td>
                    <td>{{$r->date}}</td>
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
                          url: '/dashboard/washing-approve-request-money/' + id,
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
                          url: '/dashboard/washing-delete-request-money/' + id,
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