@extends('dsh.master')
@section('title' , 'چک قالین')
@section('content')
  <!-- navbar -->
  
  <!-- form -->
  <div class="row" id="check-book">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <div class="card">
        <div class="card-header">
          <h5>چیک بک قالین</h5>
          <div class="btn btn-sm btn-primary hideOnPrint" style="float: left" onclick="printPage('check-book')"><i
                    class="fa fa-print"></i> Print
          </div>
          <div class="col-xs-3 col-lg-3 col-md-3 col-sm-3 hideOnPrint">
            <form action="/dashboard/check-book/search-agent" method="post">
              @csrf
              <input type="text" name="search" required
                     placeholder="جستجو" class="form-control">
            </form>
          </div>
        </div>
        <div class="card-body">
          
          
          <table class="table table-hover table-xs">
            <thead>
            <tr>
              <th>آی دی</th>
              <th>نام نماینده</th>
              <th>شماره تماس</th>
              <th>ادرس</th>
              <th class="hideOnPrint">لیست چک بٌک</th>
            </tr>
            </thead>
            <tbody>
            @forelse($agents as $agent)
              @if($agent->check_book->count() > 0)
                
                <tr class="ur{{ $agent->agent_id }}">
                  <td>{{$agent->agent_id}}</td>
                  <td>{{$agent->user->name}}</td>
                  <td style="direction: ltr">
                    @foreach($agent->phone as $p)
                      {{$p->phone_no}} ,
                    @endforeach
                  </td>
                  <td>{{$agent->agent_address}}</td>
                  <td class="hideOnPrint"><a class="btn btn-warning btn-sm"
                                             href="/dashboard/check-book/{{$agent->agent_id}}">لیست چک بٌک</a></td>
                </tr>
              @endif
            @empty
              <h4 class="text-info text-center">هنوز موردی ثبت نشده است</h4>
            @endforelse
            </tbody>
          </table>
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
  </script>
@endsection