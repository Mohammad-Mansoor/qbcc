@extends('dsh.master')
@section('title' , 'Washing')
@section('content')
  <!-- form -->
  <div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <div class="card">
        <div class="card-header"><h5>ثبت قالین برای شست</h5></div>
        <div class="card-body">
          <form action="/dashboard/washing-team/sent-to-washing/{{$carpetId->carpet_id}}" method="POST">
            @csrf
            <div class="row">
              <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                <div class="form-group fill">
                  <label class="pull-right">تیم شوینده</label>
                  <select name="team_id" class="form-control" style="direction: rtl" required>
                    @foreach ($washing_team as $team)
                      <option value="{{$team->id}}">{{$team->name}}</option>
                    @endforeach
                  </select>
                  @error('team_id') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                </div>
              </div>
  
              <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                <div class="form-group fill">
                  <label class="pull-right">نمبر شست</label>
                  <input type="text" name="wash_number" value="{{$WashNo}}" class="form-control" required>
                </div>
              </div>
              <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                <div class="form-group fill">
                  <label class="pull-right">طول</label>
                  <input type="text" name="" value="{{$carpetId->height}}" class="form-control" readonly>
                </div>
              </div>
              <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                <div class="form-group fill">
                  <label class="pull-right">عرض</label>
                  <input type="text" name="" value="{{$carpetId->width}}" class="form-control" readonly="">
                </div>
              </div>
            
            </div>
            <div class="row">
              <div class="col-lg-5 col-md-5 col-sm-5 col-xs-5">
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
@endsection