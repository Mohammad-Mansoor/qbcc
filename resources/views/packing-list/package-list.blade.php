@extends('dsh.master')
@section('title' , 'پکیچ لیست')
@section('content')
  <div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <div class="card">
        <div class="card-header"></div>
        <div class="card-body">
          <form action="/dashboard/package-list" method="post">
            @csrf
            <input type="hidden" name="packing_id" value="{{$packing_id->id}}">
            
            <div class="row">
              <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                <div class="form-group fill">
                  <label class="login2 pull-right pull-right-pro">نمبر
                    پکیج</label>
                  <input type="text" value="{{ $package_no }}"
                         name="package_no" id="" class="form-control"
                         readonly>
                  <small class="text-danger">
                    @error('package_no') {{ __('message.'.$message) }}
                    @enderror
                  </small>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                <div class="form-group fill">
                  <button class="btn btn-info btn-sm" type="submit"><i
                            class="fa fa-save"></i> &nbsp; ثبت
                  </button>
                  <button class="btn btn-default btn-sm" type="reset"> منصرف
                  </button>

                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <div class="card" id="package-list">
        <div class="card-header">
          <div class="row">
            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8"></div>
            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
              <div class="btn btn-sm btn-primary hideOnPrint" style="float: left" onclick="printPage('package-list')"><i
                        class="fa fa-print"></i> Print
              </div>
            </div>
          </div>
  
          <h1 class="text-center"><b style="font-weight: bolder">{{$packing_id->packing_no}}</b></h1>
  
          @if(session("status"))
            <div class="alert alert-success status" style="display:none;"
                 role="alert">
              <button type="button" class="close" data-dismiss="alert"
                      aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
              <p class="text-center">{{session('status')}}</p>
            </div>
          @endif
          @if(session("error"))
            <div class="alert alert-success status" style="display:none;"
                 role="alert">
              <button type="button" class="close" data-dismiss="alert"
                      aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
              <p class="text-center">{{session('error')}}</p>
            </div>
          @endif
        </div>
        <div class="card-body">
          <div class="static-table-list table-responsive">
            <table class="table table-bordered table-xs">
              <thead>
              <tr class="text-center">
                <th colspan="3">Size-Mtr</th>
                <th rowspan="2">Type</th>
                <th rowspan="2">Sr No</th>
                <th rowspan="2">NO</th>
              </tr>
              <tr class="text-center">
        
        
                <th>Sqr-Mtr</th>
                <th>Width</th>
                <th>Length</th>
      
              </tr>
      
              </thead>
              <tbody>
      
      
              @forelse ($package_list as $package)
                <tr class="text-left">
          
                  <th colspan="7" class="text-left"><h5><b>{{$package->package_no}}</b></h5></th>
        
        
                </tr>
          <?php   $total_carpet_area = 0; ?>
        
                @foreach($package->carpet as $carpet)

                    <?php
                        $sale = \Illuminate\Support\Facades\DB::table('sales')->where('carpet_id',$carpet->carpet_id)->first();
                        
                        ?>
                  <tr class="text-center">
                                                         <td> <span style="display: none;">@if($sale->carpet_area){{$total_carpet_area = $total_carpet_area + $sale->carpet_area}}@else{{$total_carpet_area = $total_carpet_area + $carpet->area}}@endif </span> 
                                                         <b>@if($sale->carpet_area) {{number_format((float)$sale->carpet_area, 2, '.', '')}} @else{{$carpet->area}} {{number_format((float)$carpet->area, 2, '.', '')}}@endif</b>
                                                         
                                                         </td>


                    <td><b>@if($sale->carpet_width){{number_format((float)$sale->carpet_width, 2, '.', '')}}@else {{number_format((float)$carpet->width, 2, '.', '')}}@endif</b></td>
                   <td><b>@if($sale->carpet_height){{number_format((float)$sale->carpet_height, 2, '.', '')}} @else{{number_format((float)$carpet->height, 2, '.', '')}}@endif</b></td>
                    <td><b>{{$carpet->type->carpet_type}}</b></td>
                    <td><b>{{$carpet->carpet_no}}</b></td>
                    <td><b>{{$no++}}</b></td>
                  </tr>


                @endforeach
                <tr class="text-left">
                  @if($package->carpet)
                       <th> {{number_format((float)$total_carpet_area, 2, '.', '')}} </th>
                    <th class="text-left"
                        colspan="2">
                      Total
            
                    </th>
                    <th class="text-left" colspan="3"
                        style="direction: ltr;">{{$package->carpet->count('carpet_id')}}
                      Pieces
                    </th>
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
