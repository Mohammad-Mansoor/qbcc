@extends('dsh.master')
@section('content')
  
  <div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <div class="card">
        <div class="card-header">
          <h5>ویرایش دفترچه تلفون</h5>
        </div>
        <div class="card-body">
          <div class="all-form-element-inner">
            <form action="/dashboard/phone-book/{{$phone->phone_book_id}}" method="post">
              @method('PATCH')
              @csrf
              <div class="row">
                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                  <div class="form-group fill">
                    <label class="login2">نام</label>
                    <input type="text" name="name" value="{{$phone->name}}" placeholder="نام تان را وارد کنید"
                           class="form-control" required>
                  </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                  <div class="form-group fill">
                    <label class="login2">وظیفه</label>
                    <input type="text" name="job_title" value="{{$phone->job_title}}"
                           placeholder="وظیفه تان را وارد کنید" class="form-control" required>
                  </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                  <div class="form-group fill">
                    <label class="login2">تلفن</label>
                    <input type="text" name="phone" value="{{$phone->phone}}" placeholder="شماره تلفن تان را وارد کنید"
                           class="form-control" required>
                  </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                  <div class="form-group fill">
                    <label class="login2">ایمیل</label>
                    <input type="text" name="email" value="{{$phone->email}}" placeholder="ایمیل تان را وارد کنید"
                           class="form-control" required>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                  <div class="form-group fill">
                    <button class="btn btn-sm btn-info" type="submit"><i class="fa fa-save"></i> بروز</button>
                    <a class="btn btn-default btn-sm" href="/dashboard/phone-book">انصراف</a>
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
