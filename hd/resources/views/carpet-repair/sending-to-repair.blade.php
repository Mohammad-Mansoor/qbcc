@extends('dsh.master')
@section('title' , 'Repairing')
@section('content')
  <!-- form -->
  <div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <div class="sparkline12-list">
        <div class="card">
          <div class="card-header">ثبت قالین برای ترمیم</div>
          <div class="card-body">
            <form action="/dashboard/carpet-repair/sent-to-repair/{{$carpetId->carpet_id}}" method="POST">
              @csrf
              <div class="row">
                <div class="col-lg-5 col-md-5 col-sm-5 col-xs-5">
                  <div class="form-group fill">
                    <label class="pull-right">تیم تعمیر کننده</label>
                    <select name="team_id" class="form-control" style="direction: rtl">
                      @foreach ($kachaee_team as $team)
                        <option value="{{$team->id}}">{{$team->name}}</option>
                      @endforeach
                    </select>
                    @error('team_id') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                  </div>
                </div>
              </div>
              
              
              {{-- <input type="hidden" name="carpetId" value="{{$carpetId->carpet_id}}"> --}}
              <div class="row">
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                  <div class="form-group fill">
                    <button class="btn btn-warning btn-sm" type="reset"> انصراف
                    </button>
                    <button class="btn btn-primary btn-sm marginx" type="submit"><span class="fa fa-save"></span> ذخیره
                    </button>
                  </div>
                </div>
              </div>
          </form>
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
          $('#contract_date').persianDatepicker();

          // price
          $("#price").blur(function () {
              var price = $('#price').val();
              var mainPrice = parseFloat(price).toFixed(2);
              $("#price").val(mainPrice);
          });
      });
  
  </script>
@endsection