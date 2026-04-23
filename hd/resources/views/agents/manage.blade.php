@extends('dsh.master')
@section('title' , 'مدیریت نماینده ها')
@section('content')
  <!-- navbar -->
  
  <!-- notifications -->
  @if(session("status"))
    <div class="alert alert-success status" style="display:none;" role="alert">
      <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                aria-hidden="true">&times;</span></button>
      {{session('status')}}
    </div>
  @endif
  <!-- tables -->
  <div class="row">
    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-12">
      <div class="card">
        <div class="card-header">
          جزییات قرار داد
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-xs">
              <tbody>
              <tr>
                <td>نوعیت قرارداد</td>
                <td>{{ ($agent->contract_type == 'contractional' ?  'قراردادی' : 'وزنی') }}</td>
              </tr>
              <tr>
                <td>نوعیت حساب</td>
                <td>
                  @if($agent->account_type == 'afghani') AFN
                  @elseif($agent->account_type == 'dollar') DOLLAR
                  @elseif($agent->account_type == 'rs') Rs
                  @endif
                </td>
              </tr>
              <tr>
                <td>تاریخ قرار داد</td>
                <td>{{ $agent->contract_date }}</td>
              </tr>
              <tr>
                <td>اسکن قرارداد</td>
                <td>
                  @if($agent->contract_scan_file)
                    <a class="btn btn-warning" target="_blank" href="/{{ $agent->contract_scan_file }}">اسکن قرار
                      داد</a>
                    <a class="btn btn-primary" href="/">آپدیت اسکن قرار داد</a>
                  @else
                    <span>اسکن قرار داد آپلود نشده </span>
                  @endif
                </td>
              </tr>
              <tr>
                <td>نوت و تشریحات</td>
                <td>{{ $agent->description }}</td>
              </tr>
              </tbody>
            </table>
          </div>
        
        
        </div>
      </div>
    </div>
    <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
      <div class="card">
        <div class="card-header">
          جزییات نماینده
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-xs">
              <tbody>
              <tr>
                <td>نمبر حساب</td>
                <td>{{ $agent->account_no }}</td>
              </tr>
              <tr>
                <td>نام</td>
                <td>{{ $agent->user->name }}</td>
              </tr>
              <tr>
                <td>تخلص</td>
                <td>{{ $agent->user->last_name }}</td>
              </tr>
              <tr>
                <td>نام پدر</td>
                <td>{{ $agent->agent_father_name }}</td>
              </tr>
              <tr>
                <td>نمبر تذکره</td>
                <td>{{ $agent->national_id }}</td>
              </tr>
              <tr>
                <td>ایمیل آدرس</td>
                <td>{{ $agent->user->email }}</td>
              </tr>
              <!-- <tr>
                                <td>شماره تماس</td>
                                <td>
                                    <ul dir="ltr">
                                        @foreach($agent->phone as $ph)
                <li>{{ $ph->phone_no }}</li>
                                        @endforeach
                      </ul>
                  </td>
              </tr> -->
              <tr>
                <td>آدرس</td>
                <td>{{ $agent->province->province . ' - ' . $agent->agent_address }}</td>
              </tr>
              <tr>
                <td>حالت حساب</td>
                <td>@if($agent->account_status == 0) غیر فعال @else فعال @endif</td>
              </tr>
              <tr>
                <td>تغیر حالت حساب</td>
                @if($agent->account_status == 0)
                  <td><a href="/dashboard/agent-status-change/{{$agent->agent_id}}" class="btn btn-sm btn-primary">فعال
                      کردن حساب</a></td>
                @else
                  <td><a href="/dashboard/agent-status-change/{{$agent->agent_id}}" class="btn btn-sm btn-danger">غیر فعال کردن حساب</a></td>
                @endif
              
              </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <br/>
      <div class="card">
        <div class="card-header">
          شماره تماس
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-xs">
              <tbody>
              <?php $c = 0 ?>
              @foreach($agent->phone as $ph)
                  <?php $c++ ?>
                  <tr>
                    <td>
                      @if($c > 1)
                        <button class="btn btn-sm btn-danger"
                                onclick="window.location.href = '/dashboard/agent/phone/{{ $ph->phone_id }}'"><span
                                  class="fa fa-remove"></span></button>
                      @endif
                    </td>
                    <td dir="ltr">{{ $ph->phone_no }} @if($c == 1)
                        <small class="text-primary">Primary</small> @endif</td>
                  </tr>
              @endforeach
              </tbody>
            </table>
            {{--<div class="phone-form" style="display: none;">--}}
            {{--<form action="/dashboard/agent/phone/{{ $agent->agent_id }}" method="post">--}}
            {{--@csrf--}}
            {{--<div class="form-group-inner" >--}}
            {{--<div class="row">--}}
            {{--<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">--}}
            {{--<input type="text" value="" dir="ltr" id="phone_no" name="phone_no" class="form-control">--}}
            {{--<br/>--}}
            {{--<button type="button" class="btn btn-sm " onclick="CloseFormBox()"> انصراف </button>--}}
            {{--<button type="submit" class="btn btn-sm btn-info"><span class="fa fa-save"></span> ذخیره </button>--}}
            {{--</div>--}}
            {{--</div>--}}
            {{--</div>--}}
            {{--</form>--}}
            {{--</div>--}}
            {{--<button onclick="OpenFormBox()" class="btn btn-sm btn-primary add-new-phone"><span class="fa fa-plus"></span> جدید</button>--}}
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('footer-plugins')
  <script type="text/javascript" src="/dsh/inmask/dist/jquery.inputmask.js"></script>
  <script>
      $(document).ready(function () {
          $('#phone_no').inputmask({mask: ['(99) 99-999-999']});  //static mask
      });

      function OpenFormBox() {
          $('.phone-form').show();
          $('.add-new-phone').hide();
      }

      function CloseFormBox() {
          $('.phone-form').hide();
          $('.add-new-phone').show();
      }

      $('.status').show();
      window.setTimeout(function () {
          $(".status").fadeTo(500, 0).slideUp(500, function () {
              $(this).remove();
          });
      }, 2000);

      function ChangePasswordBox() {
          $('.password-box').show();
      }
  </script>
@endsection