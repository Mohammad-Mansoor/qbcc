@extends('dsh.master')
@section('title' , 'Create New Province')
@section('content')
  <!-- form -->
  <div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <div class="card">
        <div class="card-header">
          @if(!$provinceEdit)
            <h5 class="text-right">ایجاد ولایت جدید</h5>
          @else
            <h5 class="text-right">ویرایش نام ولایت</h5>
          @endif
        </div>
        <div class="card-body">
          <div class="all-form-element-inner">
            @if(!$provinceEdit)
              @can('create_province')
              <form action="/dashboard/provinces" method="post">
                @csrf
                <div class="row">
                  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                    <div class="form-group fill">
                      <label class="login2 pull-right pull-right-pro">نام</label>
                      <input type="text" style="direction: rtl" name="province" id="province" class="form-control">
                      @error('province') <p class="text-danger">{{$message}}</p> @enderror
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                    <div class="form-group fill">
                      <button class="btn btn-sm btn-default" type="reset">انصراف</button>
                      <button class="btn btn-sm btn-primary submit-btn" type="submit">ذخیره</button>
                    </div>
                  </div>
                </div>
              </form>
              @endcan
            @else
              @can('edit_province')
              <form action="/dashboard/provinces/{{$provinceEdit->province_id}}" method="post">
                @csrf
                @method('PUT')
                <div class="row">
                  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                    <div class="form-group fill">
                      <label class="login2 pull-right pull-right-pro">نام</label>
                      <input type="text" style="direction: rtl" name="province" value="{{$provinceEdit->province}}"
                             id="province" class="form-control">
                      @error('province') <p class="text-danger">{{$message}}</p> @enderror
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                    <div class="form-group fill">
                      <button class="btn btn-sm btn-default" type="reset">انصراف</button>
                      <button class="btn btn-sm btn-primary submit-btn" type="submit">ذخیره</button>
                    </div>
                  </div>
                </div>
              </form>
              @endcan
            @endif
          </div>
        </div>
      </div>
      <div class="card">
        <div class="card-header">
          <h5>لیست ولایات</h5>
          <div class="alert alert-success" style="display:none;" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                      aria-hidden="true">&times;</span></button>
            <p class="text-center">ولایت حذف شد</p>
          </div>
          @if(session("status"))
            <div class="alert alert-success status" style="display:none;" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
              <p class="text-center">{{session('status')}}</p>
            </div>
          @endif
          @if(session("error"))
            <div class="alert alert-success status" style="display:none;" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
              <p class="text-center">{{session('error')}}</p>
            </div>
          @endif
        </div>
        <div class="card-body">
          <div class="static-table-list table-responsive">
            <table class="table table-hover table-xs">
              <thead>
              <tr>
                {{-- <th class="text-center">آی دی</th> --}}
                <th>ولایت</th>
                @can('edit_province')<th>ویرایش</th>@endcan
      
              </tr>
              </thead>
              <tbody>
              @forelse ($provinces as $province)
                <tr class="ur{{ $province->province_id }} ">
                  {{-- <td>{{$province->province_id}}</td> --}}
                  <td>{{$province->province}}</td>
                  @can('edit_province')
                  <td><a href="/dashboard/provinces/{{$province->province_id}}/edit"
                         class="btn btn-sm btn-info"><i class="fa fa-pencil"></i>&nbsp; ویرایش</a></td>
                  @endcan
        
                </tr>
              @empty
                <h4 class="text-info text-center">هنوز موردی ثبت نشده است</h4>
              @endforelse
              </tbody>
            </table>
            <span class="text-center">{{$provinces->onEachSide(1)->links()}}</span>
          </div>
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

      function RemoveProvince(id) {
          swal({
              style: "text-center",
              text: "ولایت حذف شود؟",
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
                          method: 'DELETE',
                          data: {'_token': '{{ csrf_token() }}'},
                          url: '/dashboard/provinces/' + id,
                          success: function (data) {
                              $('.ur' + id).hide();
                              $('.alert').show();
                              window.setTimeout(function () {
                                  $(".alert").fadeTo(500, 0).slideUp(500, function () {

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
