@extends('dsh.master')
@section('title' , 'چک قالین')
@section('content')
  <!-- navbar -->
  <div class="row" id="check-book-list">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <div class="card">
        <div class="card-header">
          <div class="row">
            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
              <h5>لیست چک بک</h5>
            </div>
        
            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8 hideOnPrint">
              <form action="/dashboard/check-book/search" method="POST" id="dateSearch">
                @csrf
                <div class="row">
                  <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                    <input type="submit" value="جستجو" class="date-submit btn btn-sm btn-primary btn-block" style="float: left;margin-top: 35px;">
                  </div>
                  <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                    <span class="date-label">جستجو چک نمبر</span><input type="text"
                                                                             value="{{ Request::old('search') }}"
                                                                             name="search" class="form-control" placeholder="چستجو چک نمبر"
                                                                             required>
                    <input type="hidden" value="{{$agent->agent_id}}" name="agentId">
                  </div>
                </div>
              </form>
            </div>
            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 hideOnPrint">
    
              <div class="btn btn-sm btn-primary" style="float: left;" onclick="printPage('check-book-list')"><i
                        class="fa fa-print"></i> Print
              </div>
            </div>
          </div>
        </div>
        <div class="card-body">
          <h5 style="margin-right: 35px;border-bottom:1px solid grey;padding-bottom:5px">نام نماینده
            : {{$agent->user->name}}</h5>
          <div class="static-table-list">
            <table class="table table-hover table-xs">
              <thead>
              <tr class="text-center">
                <th class="text-center">نمبر چک</th>
                <th class="text-center">طول</th>
                <th class="text-center"> عرض</th>
                <th class="text-center">مساحت</th>
                <th class="text-center"> ضایعات طول</th>
                <th class="text-center">ضایعات عرض</th>
                <th class="text-center">تاریخ</th>
              </tr>
              </thead>
              <tbody>
              @forelse ($checkbooks as $checkbook)
                <tr class="ur{{ $checkbook->id }} text-center">
           
                  {{--<td>--}}
                  <td>
                    <a href="/dashboard/check-book/search-check-number/{{$checkbook->check_number}},{{$checkbook->agent_id}}"
                    >&nbsp; {{$checkbook->check_number}}</a></td>
                 
            
                  <td style="direction: ltr">@if($checkbook->heightwaste){{$checkbook->heightwaste}} m @else 0.00
                    m @endif</td>
                  <td style="direction: ltr">@if($checkbook->widthwaste){{$checkbook->widthwaste}} m @else 0.00
                    m @endif</td>
                  <td>{{$checkbook->date}}</td>
                </tr>
              @empty
                <h4 class="text-info text-center">هنوز موردی ثبت نشده است</h4>
              @endforelse
              </tbody>
            </table>
          
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- form -->

@endsection

@section('scripts')
  <script>
      $('.status').show();
      window.setTimeout(function () {
          $(".status").fadeTo(500, 0).slideUp(500, function () {

              $(this).remove();
          });
      }, 2000);
  
  </script>
@endsection