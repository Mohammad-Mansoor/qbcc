@extends('dsh.master')
@section('title' , 'پکینگ لیست')
@section('content')
    <!-- navbar -->
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="card-header">
                    @if(!$packingEdit)
                        <h5 class="text-right">ایجاد پکینگ جدید</h5>
                    @else
                        <h5 class="text-right">ویرایش پکینگ</h5>
                    @endif
                </div>
                <div class="card-body">
                    <div class="all-form-element-inner">
                        <form action="/dashboard/packing-list" method="post">
                            @csrf
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                    <div class="form-group fill">
                                        <label>نمبر
                                            پکینگ</label>
                                        <input type="text" value="{{ $packing_no }}"
                                               name="packing_no" id="" class="form-control"
                                               readonly>
                                        <small class="text-danger">
                                            @error('packing_no') {{ __('message.'.$message) }}
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
        </div>
    </div>
    <!-- form -->
    <div class="row" id="packing-list">
        <div class="col-lg-12 col-md-21 col-sm-21 col-xs-12">
            <div class="card">
                <div class="card-header">
                    <h5>پکینگ لیست</h5>
    
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
                    <div style="position: relative;float: left">
                        <div class="btn btn-sm btn-primary hideOnPrint" onclick="printPage('packing-list')"><i class="fa fa-print"></i> Print</div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="static-table-list table-responsive">
                        <table class="table table-hover table-xs">
                            <thead>
                            <tr>
                                <th>نمبر پکینگ</th>
                                <th>تعداد پکیچ</th>
                                <th>جزییات</th>
                            </tr>
                            </thead>
                            <tbody>
            
            
                            @forelse ($packing_list as $pack)
                                <tr>
                                    <td>{{$pack->packing_no}}</td>
                    
                                    <td>{{$pack->package->count('id')}} </td>
                                    <td><a href="/dashboard/packing-list/{{$pack->id}}"
                                           class="btn btn-sm btn-info"><i class="fa fa-info"></i>&nbsp; جزییات</a>
                                    </td>
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
