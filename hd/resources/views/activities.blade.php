@extends('dsh.master')
@section('title')
  فعالیت ها
@endsection
@section('content')
  <div class="row">
    <div class="col col-lg-12 col-md-12 col-sm-12 col-12">
      <div class="card">
        <div class="card-header">
          <h4>فعالیت ها در سیستم</h4>
        </div>
        <div class="card-body">
          <div>
            <form action="{{url('dashboard/search_activities')}}" method='post' class="navbar-form navbar-right"
                  role="search">
              @csrf
              <div class="row">
                <div class="form-group col col-lg-4 col-md-4" style="direction: rtl !important; float: right;">
                  <input type="date" class="form-control" name="search"
                         placeholder="جستجو به اساس تاریخ...">
                
                </div>
                <div class="form-group col col-lg-4 col-md-4" style="direction: rtl !important; float: right;margin-top: 10px">
                  <select name="user_id" class="form-control" id="user_id" style="margin-top: 10px">
                    <option value="همه">همه کاربرها</option>
                    @foreach($users as $user)
                      <option value="{{$user->id}}">{{$user->name.' ' .$user->last_name}}</option>
                    @endforeach
                  </select>
                
                
                </div>
                <div class="form-group col col-lg-4 col-md-4" style="float: right; margin-top: 14px;">
                  <input type="submit" class="btn btn-primary btn-sm" value="جستجو">
                </div>
              </div>
            
            </form>
          </div>
          <div class="table-responsive">
            <table class="table-sm table-hover table-bordered" style="width: 100%;">
              <thead>
              <tr>
                <th>#</th>
                <th>تاریخ</th>
                <th>ساعت</th>
                <th>تفصیل</th>
                <th>انجام دهنده</th>
                <th>فعالیت</th>
              </tr>
              </thead>
              <tbody>
              <?php $c = 1; ?>
              @foreach($activities as $a)
                <tr>
                  <td>{{$c}}</td>
                  <td>{{$a->date}}</td>
                    <?php
                    $t = new \Carbon\Carbon($a->created_at)
                    ?>
                  <td style="direction: ltr">{{$t->format('g:i:s A')}}</td>
                  <td>{{$a->description}}</td>
                    <?php

                    $user = \Illuminate\Support\Facades\DB::table('users')->where('id', $a->user_id)->first();
                    ?>
                  <td>{{$user->name.' '.$user->last_name}}</td>
                  <td>
                    <button onclick="destroyActivity({{$a->id}})" class="btn btn-sm btn-danger">
                      <span class="fa fa-trash"></span>
                    </button>
                  </td>
                </tr>
                <?php $c++; ?>
              @endforeach
              
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
      $(document).ready(function () {
          $('#user_id').select2();
      });
      function destroyActivity(a_id) {

          swal({
              text: "مطمعین هستید ؟",
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
                          url: '/dashboard/activities/' + a_id,
                          success: function (data) {

                              if (data.smessage) {
                                  $('.ur' + id).hide();
                                  $('.alert-success').show();
                              } else {
                                  $('.alert-danger').show();
                              }
                              location.reload();

                          },

                      })
                  }
              });
      }
  
  
  </script>


@endsection