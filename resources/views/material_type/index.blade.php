@extends('dsh.master')
@section('title' , 'نوعیت مواد')
@section('content')
  <!-- form -->
  <div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <div class="card">
        <div class="card-header">
          @if(!$mtypeEdit)
            <h5 class="text-right">ثبت مواد جدید</h5>
          @else
            <h5 class="text-right">ویرایش نام مواد</h5>
          @endif
        </div>
        <div class="card-body">
          <div class="all-form-element-inner">
            @if(!$mtypeEdit)
              <form action="/dashboard/materialtypes" method="post">
                @csrf
                <div class="row">
                  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                    <div class="form-group fill">
                      <label> نام مواد</label>
                      <input type="text" style="direction: rtl" name="material_type" id="province" class="form-control" required>
                      @error('material_type') <p class="text-danger">{{$message}}</p> @enderror
                    </div>
                  </div>
                  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                    <div class="form-group fill">
                      <label> نوعیت (Subtype)</label>
                      <select name="subtype" class="form-control" style="direction: rtl" required>
                        <option value="yarn" selected>تار (Yarn)</option>
                        <option value="dye">رنگ (Dye)</option>
                      </select>
                      @error('subtype') <p class="text-danger">{{$message}}</p> @enderror
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <button class="btn btn-sm btn-default" type="reset">انصراف</button>
                      <button class="btn btn-sm btn-primary submit-btn" type="submit">ذخیره</button>
                    </div>
                  </div>
                </div>
              
              </form>
            @else
              <form action="/dashboard/materialtypes/{{$mtypeEdit->material_type_id}}" method="post">
                @csrf
                @method('PUT')
                <div class="row">
                  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                    <div class="form-group fill">
                      <label> نام مواد</label>
                      <input type="text" style="direction:rtl" name="material_type"
                             value="{{$mtypeEdit->material_type}}" class="form-control" required>
                      @error('material_type') <p class="text-danger">{{$message}}</p> @enderror
                    </div>
                  </div>
                  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                    <div class="form-group fill">
                      <label> نوعیت (Subtype)</label>
                      <select name="subtype" class="form-control" style="direction: rtl" required>
                        <option value="yarn" {{ $mtypeEdit->subtype == 'yarn' ? 'selected' : '' }}>تار (Yarn)</option>
                        <option value="dye" {{ $mtypeEdit->subtype == 'dye' ? 'selected' : '' }}>رنگ (Dye)</option>
                      </select>
                      @error('subtype') <p class="text-danger">{{$message}}</p> @enderror
                    </div>
                  </div>
                </div>
                <div class="row">
                  
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <a href="/dashboard/materialtypes" class="btn btn-sm btn-default" type="reset">انصراف</a>
                      <button class="btn btn-sm btn-primary submit-btn" type="submit">ذخیره</button>
                    </div>
                  </div>
                </div>
              </form>
            @endif
          </div>
        </div>
      </div>
      <div class="card">
        <div class="card-header">
          <h5 class="text-center">لیست مواد</h5>
  
          <div class="alert alert-success" style="display:none;" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                      aria-hidden="true">&times;</span></button>
            <p class="text-center">نوع مواد حذف شد</p>
          </div>
  
          @if(session("status"))
            <div class="alert alert-success status text-center" style="display:none;" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
              <p class="text-center">{{session('status')}}</p>
            </div>
  
          @endif
          @if(session("error"))
    
            <div class="alert alert-danger status text-center" style="display:none;" role="alert">
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
                <th>آی دی</th>
                <th>مواد</th>
                <th>نوعیت (Subtype)</th>
                <th>ویرایش</th>
              </tr>
              </thead>
              <tbody>
              @forelse ($mtype as $type)
                <tr class="ur{{ $type->material_type_id }}">
                  <td>{{$type->material_type_id}}</td>
                  <td>{{$type->material_type}}</td>
                  <td>{{$type->subtype_fa}}</td>
                  <td><a href="/dashboard/materialtypes/{{$type->material_type_id}}/edit"
                         class="btn btn-sm btn-info"><i class="fa fa-pencil"></i>&nbsp; ویرایش</a></td>
        
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
@endsection

@section('scripts')
  <script>
      $('.status').show();
      window.setTimeout(function () {
          $(".status").fadeTo(500, 0).slideUp(500, function () {

              $(this).remove();
          });
      }, 2000);


      function RemoveMaterialType(id) {
          swal({
              style: "text-center",
              text: "آیا نوع مواد حذف شود؟",
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
                          url: '/dashboard/materialtypes/' + id,
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