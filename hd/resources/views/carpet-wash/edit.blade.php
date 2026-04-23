@extends('dsh.master')
@section('title' , 'Carpet Wash')
@section('content')

<!-- form -->
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="card-header">
                <h5>ویرایش شست قالین</h5>
            </div>
            <div class="card-body">
                <div class="all-form-element-inner">
                    <form action="/dashboard/carpet-wash/{{$wash->id}}" method="post">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                                <div class="form-group fill">
                                    <label class="pull-right">نمبر قالین </label>
                                    <input type="text" value="{{$wash->carpet->carpet_no}}" required class="form-control" readonly>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                                <div class="form-group fill">
                                    <label class="pull-right">تیم شوینده</label>
                                    <select name="team_id" class="form-control" style="direction: rtl" required>
                                        @foreach ($washing_team as $team)
                                            <option {{($team->id == $wash->team_id ? 'selected' : '')}} value="{{$team->id}}">{{$team->name}}</option>
                                        @endforeach
                                    </select>
                                    @error('team_id') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                                <div class="form-group fill">
                                    <label class="pull-right">نمبر شست مرکزی</label>
                                    <input type="text" name="wash_number" value="{{$wash->wash_number}}" class="form-control">
                                    @error('wash_number') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                                <div class="form-group fill">
                                    <label class="pull-right">نمبر شست فروشات</label>
                                    <input type="text" name="wash_number_sh" value="{{$wash->wash_number_sh}}" class="form-control">
                                    @error('wash_number_sh') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                                <div class="form-group fill">
                                    <label class="pull-right">تاریخ شست قالین</label>
                                    <input type="date" name="date" id="repair_date" value="{{$wash->date}}" class="form-control">
                                    @error('date') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                                <div class="form-group fill">
                                    <label class="pull-right">طول بعد از شست</label>
                                    <input type="text" id="wheight" name="height" value="{{$wash->height}}" class="form-control" >
                                    @error('height') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                                <div class="form-group fill">
                                    <label class="pull-right">عرض بعد از شست</label>
                                    <input type="text" id="wwidth" name="width" value="{{$wash->width}}" class="form-control" >
                                    @error('width') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                                <div class="form-group fill">
                                    <label class="pull-right">مساحت قالین</label>
                                    <input type="text" id="warea" name="area"  value="{{$wash->area}}" readonly class="form-control" >
                                    @error('area') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                                <div class="form-group fill">
                                    <label class="pull-right">قیمت شست قالین فی متر مربع</label>
                                    <input type="text" name="price" value="{{$wash->price}}"  class="form-control" id="wprice">
                                    <input type="hidden" value="{{$currency}}" id="currency">
                                    <input type="hidden" name="old_price" value="{{$wash->total_price}}">
                                    <input type="hidden" name="af_old_price" value="{{$wash->af_total_price}}">
                                    <input type="hidden" value="{{$wash->carpetId}}" name="carpetId">
                                
                                    @error('price') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                                <div class="form-group fill">
                                    <label class="pull-right"> قیمت مجموع به افغانی</label>
                                    <input type="text" id="af_total_price" name="af_total_price"  value="{{$wash->af_total_price}}" readonly class="form-control" >
                                    @error('af_total_price') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                                <div class="form-group fill">
                                    <label class="pull-right">قیمت مجموع به دالر</label>
                                    <input type="text" id="total_price" name="total_price" value="{{$wash->total_price}}"   readonly class="form-control" >
                                    @error('total_price') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                                <div class="form-group fill">
                                    <label class="">توضیحات</label>
                                    <textarea name="description" id="description" rows="1" class="form-control">{{$wash->description}}</textarea>
                                </div>
                            </div>
                            
                        </div>
                        <div class="row">
                            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                                <a href="/dashboard/carpet-wash" class="btn btn-warning btn-sm">انصراف</a>
                                <button class="btn btn-primary btn-sm marginx" type="submit"> <span class="fa fa-save"></span> ذخیره</button>
                            </div>
                        </div>
        
                    </form>
                </div>
            </div>
        </div>
      
    </div>
</div>
@endsection
