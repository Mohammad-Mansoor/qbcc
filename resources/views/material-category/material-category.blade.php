@extends('dsh.master')
@section('title' , 'کتگوری مواد')
@section('content')
  <!-- form -->
  <br>
  <div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <div class="card">
        <div class="card-header">
          @if(!$categoryEdit)
            <h5 class="text-right">ایجاد کتگوری جدید</h5>
          @else
            <h5 class="text-right">ویرایش کتگوری </h5>
          @endif
        </div>
        <div class="card-body">
          <div class="all-form-element-inner">
            @if(!$categoryEdit)
              <form action="/dashboard/material-category" method="post">
                @csrf
                <div class="row">
                  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                    <div class="form-group fill">
                      <label>کتگوری</label>
                      <input type="text" name="material_category" id="material_category" class="form-control">
                      @error('material_category') <p class="text-danger">{{$message}}</p> @enderror
                    </div>
                  </div>
                  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                    <div class="form-group fill">
                      <label>نوعیت کتگوری (Subtype)</label>
                      <select name="subtype" class="form-control">
                        <option value="yarn">تار / مواد خام</option>
                        <option value="dye">رنگ</option>
                      </select>
                      @error('subtype') <p class="text-danger">{{$message}}</p> @enderror
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <button class="btn btn-sm btn-default" type="reset">انصراف</button>
                      <button class="btn btn-sm btn-primary submit-btn" type="submit">ثبت</button>
                    </div>
                  </div>
                </div>
              </form>
            @else
              <form method="post" id="carpetTypeForm"
                    action="/dashboard/material-category/{{$categoryEdit->material_category_id}}">
                @csrf
                @method('PUT')
                <div class="row">
                  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                    <div class="form-group fill">
                      <label>کتگوری</label>
                      <input type="text" name="material_category" id="category" class="form-control"
                             value="{{$categoryEdit->material_category}}">
                      <small class="text-danger">@error('material_category') {{ __('message.'.$message) }}@enderror
                      </small>
                    </div>
                  </div>
                  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                    <div class="form-group fill">
                      <label>نوعیت کتگوری (Subtype)</label>
                      <select name="subtype" class="form-control">
                        <option value="yarn" {{ $categoryEdit->subtype == 'yarn' ? 'selected' : '' }}>تار / مواد خام</option>
                        <option value="dye" {{ $categoryEdit->subtype == 'dye' ? 'selected' : '' }}>رنگ</option>
                      </select>
                      <small class="text-danger">@error('subtype') {{ $message }}@enderror</small>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <button class="btn btn-sm btn-default" type="reset">انصراف</button>
                      <button class="btn btn-sm btn-primary submit-btn" type="submit">ثبت</button>
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
          <h5 class="text-center">لیست کتگوری مواد</h5>
          <div class="alert alert-success" style="display:none;" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                      aria-hidden="true">&times;</span></button>
            کتگوری حذف شد
          </div>
      
          @if(session("status"))
            <div class="alert alert-success status text-center" style="display:none;" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
              {{session('status')}}
            </div>
      
          @endif
          @if(session("error"))
        
            <div class="alert alert-success status text-center" style="display:none;" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
              {{session('error')}}
            </div>
      
          @endif
        </div>
        <div class="card-body">
          <div class="static-table-list table-responsive">
            <table class="table table-hover table-xs">
              <thead>
              <tr>
                <th>آی دی</th>
                <th>کتگوری</th>
                <th>نوعیت کتگوری</th>
                <th>ویرایش</th>
              </tr>
              </thead>
              <tbody>
              @foreach($material_category as $cat)
                <tr class="ur{{ $cat->material_category_id }}">
                  <td>{{$cat->material_category_id}}</td>
                  <td>{{$cat->material_category}}</td>
                  <td>
                    @if($cat->subtype == 'dye')
                      <span class="badge badge-warning">رنگ</span>
                    @else
                      <span class="badge badge-primary">تار / مواد خام</span>
                    @endif
                  </td>
                  <td class="hideOnPrint"><a href="/dashboard/material-category/{{$cat->material_category_id}}/edit"
                                             class="btn btn-sm btn-info"><i class="fa fa-pencil"></i>&nbsp; ویرایش</a></td>
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
      $('.status').show();
      window.setTimeout(function () {
          $(".status").fadeTo(500, 0).slideUp(500, function () {

              $(this).remove();
          });
      }, 2000);


      function RemoveCategory(id) {
          swal({
              style: "text-center",
              text: "کتگوری حذف شود؟",
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
                          url: '/dashboard/material-category/' + id,
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

      function editForm(id) {
          alert(id);
      }
  </script>
@endsection