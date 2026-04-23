@extends('dsh.master')
@section('title' , 'Carpets')
@section('content')
  <div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <div class="card">
        <div class="card-header">
          <h4>ویرایش ترمیم قالین</h4>
        </div>
        <div class="card-body">
          <form action="/dashboard/carpet-repair/{{$carpetRepair->id}}" method="post">
            @csrf
            @method('PUT')
            <div class="row">
              <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                <div class="form-group fill">
                  <label class="pull-right">نمبر قالین</label>
                  <input type="text" value="{{$carpetRepair->carpet->carpet_no}}" readonly class="form-control">
                  @error('area') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                </div>
              </div>
              <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                <div class="form-group fill">
                  <label class="pull-right">نمبر کچایی</label>
                  <input type="text" name="kachaee_number" value="{{$carpetRepair->kachaee_number}}" class="form-control">
                  @error('kachaee_number') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                </div>
              </div>
              <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                <div class="form-group fill">
                  <label class="pull-right">قیمت</label>
                  <input type="text" name="price" value="{{$carpetRepair->price}}"  class="form-control" id="price">
                  <input type="hidden" value="{{$currency}}" id="currency">
                  <input type="hidden" name="old_price" value="{{$carpetRepair->total_price}}">
                  <input type="hidden" name="af_old_price" value="{{$carpetRepair->af_total_price}}">
                  <input type="hidden" value="{{$carpetRepair->carpetId}}" name="carpetId">
                  <input type="hidden" value="{{$carpetRepair->team_id}}" name="team_id">
                  @error('price') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                </div>
              </div>
              <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                <div class="form-group fill">
                  <label class="pull-right">مساحت قالین</label>
                  <input type="text" id="area" name="area"  value="{{$carpetRepair->carpet->area}}" readonly class="form-control" >
                  @error('area') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                </div>
              </div>
              <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                <div class="form-group fill">
                  <label class="pull-right">تاریخ</label>
                  <input type="date" name="date" id="repair_date" value="{{$carpetRepair->date}}" class="form-control">
                  @error('date') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                </div>
              </div>
              <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                <div class="form-group fill">
                  <label class="pull-right"> قیمت مجموع به افغانی</label>
                  <input type="text" id="af_total_price" name="af_total_price"  value="{{$carpetRepair->af_total_price}}" readonly class="form-control" >
                  @error('af_total_price') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                </div>
              </div>

            </div>
            <div class="row">
              <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                <div class="form-group fill">
                  <label class="pull-right">قیمت مجموع به دالر</label>
                  <input type="text" id="total_price" name="total_price" value="{{$carpetRepair->total_price}}"   readonly class="form-control" >
                  @error('total_price') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                </div>
              </div>
              <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10">

                <div class="form-group fill">
                  <label>توضیحات</label>
                  <textarea name="description" id="description" rows="2" class="form-control">{{$carpetRepair->description}}</textarea>
                  @error('description') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                <div class="form-group fill">
                  <a href="/dashboard/carpet-repair" class="btn btn-warning btn-sm" type="reset">انصراف</a>
                  <button class="btn btn-primary marginx btn-sm" type="submit"><span class="fa fa-save"></span> ذخیره</button>
                </div>
              </div>
            </div>
          
          </form>
        </div>
      </div>
    </div>
  </div>
<!-- form -->
@endsection