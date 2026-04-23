@extends('dsh.master')
@section('title' , 'چک قالین')
@section('content')
  <!-- navbar -->
  <div class="row" id="wash-numbers">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <div class="card">
        <div class="card-header">
          <div class="row">
            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
              <h5>لیست شست نمبر</h5>
            </div>
            
            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8 hideOnPrint">
              <form action="/dashboard/carpet-wash/search" method="POST" id="dateSearch">
                @csrf
                <div class="row">
                  <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                    <input type="submit" value="جستجو" class="date-submit btn btn-sm btn-primary btn-block"
                           style="float: left;margin-top: 35px;">
                  </div>
                  <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                    <span class="date-label">جستجو شست نمبر</span><input type="text"
                                                                         value="{{ Request::old('wash_number') }}"
                                                                         name="wash_number" class="form-control"
                                                                         placeholder="چستجو شست نمبر"
                                                                         required>
                    <input type="hidden" value="{{$team->id}}" name="team_id">
                    <input type="hidden" value="wash-check" name="wash_check">
                  </div>
                </div>
              </form>
            </div>
            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
              
              <div class="btn btn-sm btn-primary hideOnPrint" style="float: left;" onclick="printPage('wash-numbers')"><i
                        class="fa fa-print"></i> Print
              </div>
            </div>
          </div>
        </div>
        <div class="card-body">
          <h5 style="margin-right: 35px;border-bottom:1px solid grey;padding-bottom:5px"> نام شست گر {{$team->name}}
            <div class="static-table-list">
              <table class="table table-hover table-xs">
                <thead>
                <tr>
                  <th>نمبر شست</th>
                  <th>نمبر قالین</th>
                  <th>طول</th>
                  <th> عرض</th>
                  <th>مساحت</th>
                    <th>شست</th>
                
                </tr>
                </thead>
                <tbody>
                @forelse ($wash_numbers as $wash)
                  <tr>
                    
                    <td>
                      <a href="/dashboard/carpet-wash/search-wash-number-for-wash/{{$wash->wash_number}}{{$wash->team_id}}"
                      >&nbsp; {{$wash->wash_number}}</a></td>
                    <td>{{$wash->carpet->carpet_no}}</td>
                    <td>{{$wash->carpet->height}} m</td>
                    <td>{{$wash->carpet->width}} m</td>
                    <td>{{$wash->carpet->area}} m <sup>2</sup></td>
                    
                      @if($wash->carpet->status == 13)
                        <td>شسته شده است</td>
                    @else
                      @if(auth()->user()->role == 'SO' || auth()->user()->role == 'SCO')
                        <td><a href="/dashboard/carpet-wash/create/{{$wash->id}}"
                               class="btn btn-sm btn-info printBTN"><i
                                    class="fa fa-pencil"></i>&nbsp; شست </a></td>
                      @else
                        <td>شسته نشده</td>
                      @endif
                    @endif
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