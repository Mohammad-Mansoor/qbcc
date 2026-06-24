@extends('dsh.master')
@section('content')
  <!-- navbar -->
  
  <!-- form -->
  <br>
  <div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <div class="card">
        <div class="card-header">
          @if(!$typeEdit)
            <h5>ایجاد نوعیت جدید</h5>
          @else
            <h5>ویرایش نوعیت</h5>
          @endif
        </div>
        <div class="card-body">
          <div class="all-form-element-inner">
            @if(!$typeEdit)
              @can('create_carpet_type')
              <form method="post" id="" action="/dashboard/carpet-types">
                @csrf
                <div class="row">
                  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                    <div class="form-group fill">
                      <label>نوعیت</label>
                      <input type="text" class="form-control" name="carpet_type" idate="type">
                      @error('carpet_type') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                    <div class="form-group fill">
                      <button class="btn btn-sm btn-default" type="reset">انصراف</button>
                      <button class="btn btn-sm btn-primary submit-btn" type="submit">ثبت</button>
                    </div>
                  </div>
                </div>
              </form>
              @endcan
            @else
              @can('edit_carpet_type')
              <form method="post" id="carpetTypeForm" action="/dashboard/carpet-types/{{$typeEdit->carpet_type_id}}">
                @csrf
                @method('PUT')
                <div class="row">
                  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                    <div class="form-group fill">
                      <label>نوعیت</label>
                      <input type="text" class="form-control" name="carpet_type" value="{{$typeEdit->carpet_type}}">
                      @error('carpet_type') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                    <div class="form-group fill">
                      <button class="btn btn-sm btn-default" type="reset">انصراف</button>
                      <button class="btn btn-sm btn-primary submit-btn" type="submit">ثبت</button>
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
          <h5>نوعیت قالین</h5>
          <div class="alert alert-success" style="display:none;" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                      aria-hidden="true">&times;</span></button>
            مخاطب حذف شد
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
              <tr >
                <th>آی دی</th>
                <th>نوعیت قالین</th>
                @can('edit_carpet_type')<th>ویرایش</th>@endcan
              </tr>
              </thead>
              <tbody>
              @foreach($carpet_type as $type)
                <tr class="ur{{ $type->carpet_type_id }}">
                  <td>{{$type->carpet_type_id}}</td>
                  <td>{{$type->carpet_type}}</td>
                  @can('edit_carpet_type')<td><a href="/dashboard/carpet-types/{{$type->carpet_type_id}}/edit"
                         class="btn btn-sm btn-info"><i class="fa fa-pencil"></i>&nbsp; ویرایش</a></td>@endcan
        
                </tr>
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
      $('#form2').hide();

      function editType(id) {
          $('#form1').hide();
          $('#form2').show();
          $('#typeId').val(typeId);
          var data = new FormData($('#type-form')[0]);
          $.ajax({
              url: '/dashboard/carpet-types',
              method: 'POST',
              dataType: 'JSON',
              data: data,
              cache: false,
              contentType: false,
              processData: false,

              success: function (category) {
                  if (data.result == 'success') {

                  }

              },
              error: function (err) {

              },

          })

      }


      // remove carpet type function
      $('.status').show();
      window.setTimeout(function () {
          $(".status").fadeTo(500, 0).slideUp(500, function () {

              $(this).remove();
          });
      }, 2000);

      function RemoveType(id) {
          swal({
              text: "نوعیت حذف شود؟",
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
                          url: '/dashboard/carpet-types/' + id,
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

