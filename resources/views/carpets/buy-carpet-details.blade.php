@extends('dsh.master')

@section('content')
  <!-- navbar -->
  {{-- <div class="row">
      <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
          <div class="breadcome-list single-page-breadcome">
              <div class="row">
                  <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                      <div class="breadcome-heading">
                          <form  action="/dashboard/repair-search" method="POST" >
                              @csrf
                              <input type="text" name="search" placeholder=" جستجو شماره پارچه" class="form-control" required>
                              <a href=""><i class="fa fa-search"></i></a>
                          </form>
                      </div>
                  </div>
                  <div class="col-lg-5 col-md-5 col-sm-5 col-xs-5">
                      <div class="breadcome-heading">
                          <form  action="/dashboard/repair-date-search" method="POST" id="dateSearch">
                              @csrf
                              <input type="submit" value="جستجو" class="date-submit">
                              <span class="date-label">شروع</span><input type="date" name="start"  class="form-control" required>
                              <span class="date-label">ختم</span><input type="date" name="end"  class="form-control" required>
                              <a href=""><i class="fa fa-search"></i></a>
                          </form>
                      </div>
                  </div>
                  <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 pull-right">
                      <ul class="breadcome-menu">
                          <li><a href="/dashboard">داشبورد</a> <span class="bread-slash">/</span>
                          </li>
                          <li><span class="bread-blod">لیست پارچه ها</span>
                          </li>
                      </ul>
                  </div>
              </div>
          </div>
      </div>
  </div> --}}
  <div class="row">
    <div class="col-lg-12 col-md-12 col-sm-6 col-xs-12">
      <div class="sparkline8-list">
        <div class="sparkline8-hd">
          <div class="main-sparkline8-hd">
            
            <div class="alert alert-success" style="display:none;" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
              جزئیات حذف شد
            </div>
            
            @if(session("status"))
              <div class="alert alert-success status" style="display:none;" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                          aria-hidden="true">&times;</span></button>
                {{session('status')}}
              </div>
            
            @endif
            @if(session("error"))
              
              <div class="alert alert-danger status" style="display:none;" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                          aria-hidden="true">&times;</span></button>
                {{session('error')}}
              </div>
            
            @endif
          </div>
        </div>
        <div class="sparkline8-graph">
          <div class="container">
            <ul class="nav nav-tabs pull-right" style="margin-bottom:30px;">
              <li><a data-toggle="tab" href="#all-in-here"> همه معلومات قالین مذکور </a></li>
              <li><a data-toggle="tab" href="#material-recieves"> دریافت های مواد </a></li>
              <li><a data-toggle="tab" href="#agent-recieves"> دریافت های نماینده </a></li>
              <li><a data-toggle="tab" href="#carpet-checkbook">چک بٌک قالین</a></li>
              <li class="active"><a data-toggle="tab" href="#carpet-detials">مشخصات کلی قالین</a></li>
            </ul>
          </div>
          
          <div class="tab-content">
            <div id="all-in-here" class="tab-pane fade in">
              <div class="row">
                <div class="col-sm-12">
                  <div class="btn btn-sm btn-primary pull-left" onclick="printPage('printAll')"><i
                            class="fa fa-print"></i> Print
                  </div>
                </div>
              </div>
              <div class="row" id="printAll">
                <div class="col-sm-12">
                  <h4 style="margin:20px auto; text-align:center">مشخصات عمومی قالین</h4>
                  <div class="static-table-list">
                    <table class="table">
                      <thead>
                      <tr>
                        <th style="text-align:right !important">مشخصات</th>
                        <th style="text-align:right !important">مقادیر</th>
                        <th style="text-align:right !important">مشخصات</th>
                        <th style="text-align:right !important">مقادیر</th>
                        <th style="text-align:right !important">مشخصات</th>
                        <th style="text-align:right !important">مقادیر</th>
                      </tr>
                      </thead>
                      <tbody>
                      <tr>
                        <td>شماره قالین</td>
                        <td>{{$carpet->carpet_no}}</td>
                        <td>طول قالین</td>
                        <td>{{ $carpet->height }}</td>
                        <td>زمینه قالین</td>
                        <td>{{ $carpet->field }} </td>
                      </tr>
                      <tr>
                        <td>شماره فرمایش</td>
                        @if($carpet->order_number)
                          <td>{{$carpet->carpet_order->order_number}}</td>
                        @else
                          <td></td>
                        @endif
                        <td>عرض قالین</td>
                        <td>{{ $carpet->width }} </td>
                        <td>حاشیه قالین</td>
                        <td>{{ $carpet->margin }}</td>
                      </tr>
                      <tr>
                        <td>شماره نقشه قالین</td>
                        <td>{{ $carpet->map_number }}</td>
                        <td>مساحت قالین</td>
                        <td>{{ $carpet->area }} </td>
                        <td>قیمت</td>
                        <td>{{ $carpet->price }}</td>
                      </tr>
                      <tr>
                        <td>تاریخ</td>
                        <td>{{ $carpet->date }}</td>
                        <td> حالت</td>
                        @switch($carpet->status)
                          @case(0)
                          <td style="color:orange">زیر کار</td>
                          @break
                          @case(1)
                          <td style="color: green">تکمیل</td>
                          @break
                          @default
                          <td>نامشخص</td>
                        @endswitch
                      </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
                <div class="col-sm-12">
                  <div class="sparkline8-graph text-muted">
                    <h4 style="margin:20px auto; text-align:center">مشخصات عمومی چک بٌک</h4>
                    @if ($carpetCheckBook)
                      <div class="static-table-list">
                        <table class="table text-cente">
                          <thead>
                          <tr>
                            <th style="text-align:right !important">مشخصات</th>
                            <th style="text-align:right !important">مقادیر</th>
                            <th style="text-align:right !important">مشخصات</th>
                            <th style="text-align:right !important">مقادیر</th>
                            <th style="text-align:right !important">مشخصات</th>
                            <th style="text-align:right !important">مقادیر</th>
                          </tr>
                          </thead>
                          <tbody>
                          <tr>
                            <td>شماره قالین</td>
                            <td>{{$carpetCheckBook->carpet->carpet_no}}</td>
                            <td> طول قالین</td>
                            <td>{{$carpetCheckBook->height .'m'}}</td>
                            <td> طول ضایع شده قالین</td>
                            <td>{{$carpetCheckBook->heightwaste .'m'}}</td>
                          </tr>
                          <tr>
                            <td>تاریخ</td>
                            <td>{{$carpetCheckBook->date}}</td>
                            <td> عرض قالین</td>
                            <td>{{$carpetCheckBook->width .'m'}}</td>
                            <td> عرض ضایع شده قالین</td>
                            <td>{{$carpetCheckBook->widthwaste .'m'}}</td>
                          </tr>
                          <tr>
                            <td> مساحت قالین</td>
                            <td>{{$carpetCheckBook->area .'m'}}</td>
                          </tr>
                          </tbody>
                        </table>
                      </div>
                    @else
                      <p style="text-align:center;color:red"> این قالین چک بٌک نشده است</p>
                    @endif
                  </div>
                </div>
                <div class="col-sm-12 text-muted">
                  <h4 style="margin:20px auto; text-align:center;">دریافت های نماینده</h4>
                  <div class="static-table-list">
                    <table class="table text-center">
                      <thead>
                      <tr>
                        <th>شماره قالین</th>
                        <th> دریافت کننده پول</th>
                        <th>مبلغ پول</th>
                        <th> تاریخ</th>
                        <th>شرح کلی</th>
                      </tr>
                      </thead>
                      <tbody>
                      @forelse ($agnetRecieveds as $received)
                        <tr>
                          <td>{{$received->carpet->carpet_no}}</td>
                          <td>{{$carpet->agent->user->name}}</td>
                          <td>{{$received->amount.'AFG'}}</td>
                          <td>{{$received->date}}</td>
                          <td>{{$received->description}}</td>
                        </tr>
                      @empty
                        <p style="text-align:center;color:red;margin:40px auto;">هنوز دریافتی ثبت نشده است</p>
                      @endforelse
                      </tbody>
                    </table>
                  </div>
                </div>
                <div class="col-sm-12">
                  <div class="sparkline8-graph text-muted">
                    <h4 style="margin:20px auto; text-align:center"> دریافت های مواد برای قالین </h4>
                    <div class="static-table-list">
                      <table class="table text-center">
                        <thead>
                        <tr>
                          <th>شماره قالین</th>
                          <th> دریافت کننده مواد</th>
                          <th> مقدار</th>
                          <th>قیمت</th>
                          <th> دسته بندی مواد</th>
                          <th> تاریخ</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($carpetMaterials as $material)
                          <tr>
                            <td>{{$material->carpet->carpet_no}}</td>
                            <td>{{$material->carpet->agent->user->name}}</td>
                            <td>{{$material->amount . "kg"}}</td>
                            <td>{{$material->price . "AFG"}}</td>
                            <td>{{$material->category->material_category}}</td>
                            <td>{{$material->date}}</td>
                          </tr>
                        @empty
                          <p style="text-align:center;color:red;margin:40px auto;">هنوز دریافتی ثبت نشده است</p>
                        @endforelse
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
                <div class="col-sm-12 pull-right">
                  <div class="sparkline8-graph text-muted">
                    <h4 style="margin:10px auto; text-align:center"> مصرف کلی قالین</h4>
                    <div class="static-table-list">
                      <table class="table ">
                        <thead>
                        <tr>
                          <th style="text-align:right !important">مشخصات</th>
                          <th style="text-align:right !important">مقادیر</th>
                          <th style="text-align:right !important">مشخصات</th>
                          <th style="text-align:right !important">مقادیر</th>
                          <th style="text-align:right !important">مشخصات</th>
                          <th style="text-align:right !important">مقادیر</th>
                          <th style="text-align:right !important">مشخصات</th>
                          <th style="text-align:right !important">مقادیر</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                          <td>برداشت های نماینده</td>
                          <td>{{$agentMoney . 'AFG'}}</td>
                          <td>مصارف مواد</td>
                          <td>{{$materialMoney . 'AFG'}}</td>
                          <td> مصرف کلی</td>
                          <td>{{$materialMoney + $agentMoney .'AFG'}}</td>
                          <td> قیمت قالین</td>
                          <td>{{$carpet->price . 'AFG'}}</td>
                        </tr>
                        <tr>
                          <td> پول باقی مانده</td>
                          <td style="direction:ltr !important;font-weight:bold;color:
                                                            black;"
                              class="pull-right">{{$carpet->price - ($materialMoney + $agentMoney)  . 'AFG'}}</td>
                        </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div id="material-recieves" class="tab-pane fade in">
              <div class="row">
                <div class="col-sm-12">
                  <h4 style="margin:40px 80px"> فورم دریافت های مواد</h4>
                  <div class="all-form-element-inner">
                    <form action="/dashboard/carpet-material" method="post">
                      @csrf
                      <input type="hidden" name="carpet_id" value="{{$carpet->carpet_id}}">
                      <input type="hidden" name="agent_id" value="{{$carpet->agent->agent_id}}">
                      <div class="row" style=" display:flex;justify-content:center">
                        <div class="col-sm-10">
                          <div class="form-group-inner">
                            <div class="row" style=" display:flex;justify-content:space-around">
                              <div class="col-xs-6">
                                <label class="pull-right">مقدار مواد</label>
                                <input type="text" name="amount" placeholder="مقدار را به کیلو گرام وارد کنید"
                                       class="form-control" id="material-amount">
                                @error('amount') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                              </div>
                              <div class="col-xs-6">
                                <label class="pull-right"> قیمت مواد</label>
                                <input type="text" name="price" placeholder="قیمت مواد مذکور" class="form-control"
                                       id="material-price">
                                @error('price') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                              </div>
                            </div>
                          </div>
                          <div class="form-group-inner">
                            <div class="row" style=" display:flex;justify-content:space-around">
                              <div class="col-xs-6">
                                <label class="pull-right">دسته بندی مواد</label>
                                <select name="category_id" class="form-control" style="direction: rtl">
                                  @foreach ($categories as $category)
                                    <option value="{{$category->material_category_id}}">{{$category->material_category}}</option>
                                  @endforeach
                                </select>
                                @error('category_id') <p class="text-danger">{{trans('message.'.$message)}}</p>
                                @enderror
                              </div>
                              <div class="col-xs-6">
                                <label class="pull-right">تاریخ</label>
                                <input type="date" name="date" placeholder="تاریخ را وارد کنید" class="form-control">
                                @error('date') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                              </div>
                            </div>
                          </div>
                          <div class="form-group-inner">
                            <div class="row" style=" display:flex;justify-content:flex-start">
                              <div class="col-xs-6">
                                <label class="pull-right">نوعیت مواد</label>
                                <select name="type_id" class="form-control" style="direction: rtl">
                                  @foreach ($material_types as $type)
                                    <option value="{{$type->material_type_id}}">{{$type->material_type}}</option>
                                  @endforeach
                                </select>
                                @error('type_id') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                              </div>
                            
                            </div>
                          </div>
                          <div class="form-group-inner">
                            <div class="row" style="display:flex;justify-content:flex-start;padding:0 0px">
                              <a href="/dashboard/contract-carpet/{{$carpet->carpet_id}}" class="btn btn-white"
                                 type="reset">انصراف</a>
                              <button class="btn btn-primary marginx" type="submit"><span class="fa fa-save"></span>
                                ذخیره
                              </button>
                            </div>
                          </div>
                        </div>
                      </div>
                    </form>
                  </div>
                </div>
                <div class="col-sm-12">
                  <div class="sparkline8-graph text-muted">
                    <h4 style="margin:40px auto; text-align:center"> دریافت های مواد برای قالین
                      شماره&nbsp;{{$carpet->carpet_no}}</h4>
                    <div class="static-table-list">
                      <table class="table text-center">
                        <thead>
                        <tr>
                          <th>شماره قالین</th>
                          <th> دریافت کننده مواد</th>
                          <th> مقدار</th>
                          <th>قیمت</th>
                          <th> دسته بندی مواد</th>
                          <th> نوعیت مواد</th>
                          <th> تاریخ</th>
                          <th> ویرایش</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($carpetMaterials as $material)
                          <tr>
                            <td>{{$material->carpet->carpet_no}}</td>
                            <td>{{$material->carpet->agent->user->name}}</td>
                            <td>{{$material->amount . "kg"}}</td>
                            <td>{{$material->price . "AFG"}}</td>
                            <td>{{$material->category->material_category}}</td>
                            <td>{{$material->type->material_type}}</td>
                            <td>{{$material->date}}</td>
                            <td><a class="btn btn-sm btn-info" href="/dashboard/carpet-material/{{$material->id}}/edit"><i
                                        class="fa fa-pencil">&nbsp;&nbsp;&nbsp;</i>ویرایش</a></td>
                          </tr>
                        @empty
                          <p style="text-align:center;color:red;margin:40px auto;">هنوز دریافتی ثبت نشده است</p>
                        @endforelse
                        </tbody>
                      </table>
                    </div>
                    {{$carpetMaterials->links()}}
                  </div>
                </div>
              </div>
            </div>
            <div id="agent-recieves" class="tab-pane fade in">
              <div class="row">
                <div class="col-sm-12">
                  <h4 style="margin:40px 80px"> فورم دریافت های نماینده</h4>
                  <div class="all-form-element-inner">
                    <form action="/dashboard/agent-received" method="post">
                      @csrf
                      <input type="hidden" name="carpet_id" value="{{$carpet->carpet_id}}">
                      <input type="hidden" name="agent_id" value="{{$carpet->agent_id}}">
                      <div class="row" style=" display:flex;justify-content:center">
                        <div class="col-sm-10">
                          <div class="form-group-inner">
                            <div class="row" style=" display:flex;justify-content:space-around">
                              <div class="col-xs-6">
                                <label class="pull-right">مبلغ پول</label>
                                <input type="text" name="amount" placeholder="مبلغ پول" class="form-control" id="ar">
                                @error('amount') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                              </div>
                              <div class="col-xs-6">
                                <label class="pull-right">تاریخ</label>
                                <input type="date" name="date" placeholder="تاریخ را وارد کنید" class="form-control">
                                @error('date') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                              </div>
                            </div>
                          </div>
                          <div class="form-group-inner">
                            <div class="row" style=" display:flex;justify-content:space-around">
                              <div class="col-xs-12 center marginy">
                                <label class="">توضیحات</label>
                                <textarea name="description" id="description" rows="5" class="form-control"
                                          placeholder="توضیحات پرداخت "></textarea>
                              </div>
                            </div>
                          </div>
                          <div class="form-group-inner">
                            <div class="row" style="display:flex;justify-content:flex-start;padding:0 0px">
                              <a href="/dashboard/contract-carpet/{{$carpet->carpet_id}}" class="btn btn-white"
                                 type="reset">انصراف</a>
                              <button class="btn btn-primary marginx" type="submit"><span class="fa fa-save"></span>
                                ذخیره
                              </button>
                            </div>
                          </div>
                        </div>
                      </div>
                    </form>
                  </div>
                </div>
                <div class="col-sm-12">
                  <div class="sparkline8-graph text-muted">
                    <h4 style="margin:40px auto; text-align:center">دریافت های نماینده</h4>
                    <div class="static-table-list">
                      <table class="table text-center">
                        <thead>
                        <tr>
                          <th>شماره قالین</th>
                          <th> دریافت کننده پول</th>
                          <th>مبلغ پول</th>
                          <th> تاریخ</th>
                          <th>شرح کلی</th>
                          <th> ویرایش</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($agnetRecieveds as $received)
                          <tr>
                            <td>{{$received->carpet->carpet_no}}</td>
                            <td>{{$carpet->agent->user->name}}</td>
                            <td>{{$received->amount.'AFG'}}</td>
                            <td>{{$received->date}}</td>
                            <td>{{$received->description}}</td>
                            <td>
                              <a href="/dashboard/agent-received/{{$received->id}}/edit" class="btn btn-info btn-sm">
                                <i class="fa fa-pencil"></i> &nbsp; ویرایش
                              </a>
                            </td>
                          </tr>
                        @empty
                          <p style="text-align:center;color:red;margin:40px auto;">هنوز دریافتی ثبت نشده است</p>
                        @endforelse
                        </tbody>
                      </table>
                    </div>
                    {{$agnetRecieveds->links()}}
                  </div>
                </div>
              </div>
            </div>
            <div id="carpet-checkbook" class="tab-pane fade in">
              <div class="row">
                @if (!$carpetCheckBook)
                  <div class="col-sm-12">
                    <h4 style="margin:40px 80px"> فورم چک بٌک</h4>
                    <div class="all-form-element-inner">
                      <form action="/dashboard/check-book" method="post">
                        @csrf
                     
                        <input type="hidden" name="carpet_id" value="{{$carpet->carpet_id}}">
                        <input type="hidden" name="agent_id" value="{{$carpet->agent_id}}">
                        <div class="row" style=" display:flex;justify-content:center">
                          <div class="col-sm-12">
                            <div class="form-group-inner">
                              <div class="row" style=" display:flex;justify-content:space-around">
                                <div class="col-xs-5">
                                  <label class="pull-right">طول</label>
                                  <input type="text" name="height" placeholder=" طول قالین" class="form-control"
                                         id="height">
                                  @error('height') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                                </div>
                                <div class="col-xs-5">
                                  <label class="pull-right">عرض</label>
                                  <input type="text" name="width" placeholder=" عرض قالین" class="form-control"
                                         id="width">
                                  @error('width') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                                </div>
                              </div>
                            </div>
                            <div class="form-group-inner">
                              <div class="row" style=" display:flex;justify-content:space-around">
                                <div class="col-xs-5">
                                  <label class="pull-right">مساحت</label>
                                  <input type="text" name="area" placeholder="   مساحت قالین" class="form-control"
                                         id="area" readonly>
                                  @error('area') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                                </div>
                                <div class="col-xs-5">
                                  <label class="pull-right">تاریخ</label>
                                  <input type="date" name="date" placeholder="تاریخ را وارد کنید" class="form-control">
                                  @error('date') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                                </div>
                              </div>
                            </div>
                            <div class="form-group-inner">
                              <div class="row" style=" display:flex;justify-content:space-around">
                                <div class="col-xs-5">
                                  <label class="pull-right">ضایعات طول</label>
                                  <input type="text" name="heightwaste" placeholder="  ضایعات طول قالین "
                                         class="form-control" id="heightWaste">
                                  {{-- <input type="hidden" value="{{$carpet->id}}" name="carpetId"> --}}
                                  @error('heightwaste') <p class="text-danger">{{trans('message.'.$message)}}</p>
                                  @enderror
                                </div>
                                <div class="col-xs-5">
                                  <label class="pull-right">ضایعات عرض</label>
                                  <input type="text" name="widthwaste" placeholder="  ضایعات عرض قالین"
                                         class="form-control" id="widthWaste">
                                  @error('widthwaste') <p class="text-danger">{{trans('message.'.$message)}}</p>
                                  @enderror
                                </div>
                              </div>
                            </div>
                            <div class="form-group-inner">
                              <div class="row" style="display:flex;justify-content:flex-start;padding:0 70px">
                                <a href="/dashboard/contract-carpet/{{$carpet->carpet_id}}" class="btn btn-white"
                                   type="reset">انصراف</a>
                                <button class="btn btn-primary marginx" type="submit"><span class="fa fa-save"></span>
                                  ذخیره
                                </button>
                              </div>
                            </div>
                          </div>
                        </div>
                      </form>
                    </div>
                  </div>
                @else
                  <div class="col-sm-12" id="editDetails">
                    <div class="sparkline8-graph text-muted">
                      <h4 style="margin:40px auto; text-align:center">مشخصات عمومی چک بٌک</h4>
                      <div class="static-table-list">
                        <table class="table text-cente">
                          <thead>
                          <tr>
                            <th style="text-align:right !important">مشخصات</th>
                            <th style="text-align:right !important">مقادیر</th>
                          </tr>
                          </thead>
                          <tbody>
                          <tr>
                            <td>شماره قالین</td>
                            <td>{{$carpetCheckBook->carpet->carpet_no}}</td>
                          </tr>
                          <tr>
                            <td> طول قالین</td>
                            <td>{{$carpetCheckBook->height .'m'}}</td>
                          </tr>
                          <tr>
                            <td> عرض قالین</td>
                            <td>{{$carpetCheckBook->width .'m'}}</td>
                          </tr>
                          <tr>
                            <td> مساحت قالین</td>
                            <td>{{$carpetCheckBook->area .'m'}}</td>
                          </tr>
                          <tr>
                            <td> طول ضایع شده قالین</td>
                            <td>{{$carpetCheckBook->heightwaste .'m'}}</td>
                          </tr>
                          <tr>
                            <td> عرض ضایع شده قالین</td>
                            <td>{{$carpetCheckBook->widthwaste .'m'}}</td>
                          </tr>
                          <tr>
                            <td>تاریخ</td>
                            <td>{{$carpetCheckBook->date}}</td>
                          </tr>
                            @if(auth()->user()->role == 'SP')
                          <tr>
                            <td>ویرایش</td>
                            <td>
                              <button class="btn btn-info" id="editBtn"><i class="fa fa-pencil"></i> ویرایش</button>
                            </td>
                          </tr>
                          @endif
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                  <div class="col-sm-12" id="editForm">
                    <h4 style="margin:40px 80px"> فورم ویرایش چک بٌک</h4>
                    <div class="all-form-element-inner">
                      <form action="/dashboard/check-book/{{$carpetCheckBook->id}}" method="post">
                        @csrf
                        @method("PUT")
                        <input type="hidden" name="carpet_id" value="{{$carpet->carpet_id}}">
                        <div class="row" style=" display:flex;justify-content:center">
                          <div class="col-sm-12">
                            <div class="form-group-inner">
                              <div class="row" style=" display:flex;justify-content:space-around">
                                <div class="col-xs-5">
                                  <label class="pull-right">طول</label>
                                  <input type="text" name="height" value="{{$carpetCheckBook->height}}"
                                         class="form-control" id="editheight">
                                  @error('height') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                                </div>
                                <div class="col-xs-5">
                                  <label class="pull-right">عرض</label>
                                  <input type="text" name="width" value="{{$carpetCheckBook->width}}"
                                         class="form-control" id="editwidth">
                                  @error('width') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                                </div>
                              </div>
                            </div>
                            <div class="form-group-inner">
                              <div class="row" style=" display:flex;justify-content:space-around">
                                <div class="col-xs-5">
                                  <label class="pull-right">مساحت</label>
                                  <input type="text" name="area" value="{{$carpetCheckBook->area}}" class="form-control"
                                         id="editarea" readonly>
                                  @error('area') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                                </div>
                                <div class="col-xs-5">
                                  <label class="pull-right">تاریخ</label>
                                  <input type="date" name="date" value="{{$carpetCheckBook->date}}"
                                         class="form-control">
                                  @error('date') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                                </div>
                              </div>
                            </div>
                            <div class="form-group-inner">
                              <div class="row" style=" display:flex;justify-content:space-around">
                                <div class="col-xs-5">
                                  <label class="pull-right">ضایعات طول</label>
                                  <input type="text" name="heightwaste" value="{{$carpetCheckBook->heightwaste}}"
                                         class="form-control" id="editheightWaste">
                                  <input type="hidden" value="{{$carpet->id}}" name="carpetId">
                                  @error('heightwaste') <p class="text-danger">{{trans('message.'.$message)}}</p>
                                  @enderror
                                </div>
                                <div class="col-xs-5">
                                  <label class="pull-right">ضایعات عرض</label>
                                  <input type="text" name="widthwaste" value="{{$carpetCheckBook->widthwaste}}"
                                         class="form-control" id="editwidthWaste">
                                  @error('widthwaste') <p class="text-danger">{{trans('message.'.$message)}}</p>
                                  @enderror
                                </div>
                              </div>
                            </div>
                            <div class="form-group-inner">
                              <div class="row" style="display:flex;justify-content:flex-start;padding:0 70px">
                                <a href="/dashboard/contract-carpet/{{$carpet->carpet_id}}" class="btn btn-white"
                                   type="reset">انصراف</a>
                                <button class="btn btn-primary marginx" type="submit"><span class="fa fa-save"></span>
                                  بروز کردن
                                </button>
                              </div>
                            </div>
                          </div>
                        </div>
                      </form>
                    </div>
                  </div>
                @endif
              </div>
            </div>
            <div id="carpet-detials" class="tab-pane fade in active">
              <div class="sparkline8-graph">
                <h4 style="margin:20px auto">مشخصات عمومی قالین</h4>
                <div class="static-table-list">
                  <table class="table text-cente">
                    <thead>
                    <tr>
                      <th style="text-align:right !important">مشخصات</th>
                      <th style="text-align:right !important">مقادیر</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                      <td>شماره قالین</td>
                      <td>{{$carpet->carpet_no}}</td>
                    </tr>
                    <tr>
                      <td>شماره فرمایش</td>
                      <td>{{$carpet->carpet_order->order_number}}</td>
                    </tr>
                    <tr>
                      <td>طول قالین</td>
                      <td>{{ $carpet->height }}</td>
                    </tr>
                    <tr>
                      <td>عرض قالین</td>
                      <td>{{ $carpet->width }} </td>
                    </tr>
                    <tr>
                      <td>مساحت قالین</td>
                      <td>{{ $carpet->area }} </td>
                    </tr>
                    <tr>
                      <td>شماره نقشه قالین</td>
                      <td>{{ $carpet->map_number }}</td>
                    </tr>
                    <tr>
                      <td>زمینه قالین</td>
                      <td>{{ $carpet->field }} </td>
                    </tr>
                    <tr>
                      <td>حاشیه قالین</td>
                      <td>{{ $carpet->margin }}</td>
                    </tr>
                    <tr>
                      <td>قیمت</td>
                      <td>{{ $carpet->price }}</td>
                    </tr>
                    <tr>
                      <td>تاریخ</td>
                      <td>{{ $carpet->date }}</td>
                    </tr>
                    <tr>
                      <td> حالت</td>
                      @if($carpet->status == 5)
                        <td>تکمیل شده</td>
                      @else
                        <td>زیر کار است</td>
                      @endif
                    </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
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
          $(".status").fadeTo(200, 0).slideUp(200, function () {

              $(this).remove();
          });
      }, 5000);

      // FORM SCRIPTS
      $(function () {
          $(window).load(function () {
              $('#editForm').hide();
          });
          // DESPLAYING EDIT FORM
          $('#editBtn').click(function () {
              $('#editForm').show();
              $('#editDetails').hide();
          });
          // CONVERTING FORM DATA TO DECIMAL
          // height
          $("#height").blur(function () {
              var height = $('#height').val();
              var mainHeight = parseFloat(height).toFixed(2);
              $("#height").val(mainHeight);
          });
          //width
          $("#width").blur(function () {
              var width = $('#width').val();
              var mainWidth = parseFloat(width).toFixed(2);
              $("#width").val(mainWidth);
              // Area
              var mainHeight = $('#height').val();
              var area = mainHeight * mainWidth;
              var mainArea = parseFloat(area).toFixed(2);
              $('#area').val(mainArea);
          });
          // heightWaste
          $("#heightWaste").blur(function () {
              var heightWaste = $('#heightWaste').val();
              var mainHeightWaste = parseFloat(heightWaste).toFixed(2);
              $("#heightWaste").val(mainHeightWaste);
          });
          // widthWaste
          $("#widthWaste").blur(function () {
              var widthWaste = $('#widthWaste').val();
              var mainWidthWaste = parseFloat(widthWaste).toFixed(2);
              $("#widthWaste").val(mainWidthWaste);
          });

          // EDIT SECTION
          // CONVERTING FORM DATA TO DECIMAL
          // height
          $("#editheight").blur(function () {
              var height = $('#editheight').val();
              var mainHeight = parseFloat(height).toFixed(2);
              $("#editheight").val(mainHeight);
              // Area
              var mainWidth = $('#editwidth').val();
              var area = mainHeight * mainWidth;
              var mainArea = parseFloat(area).toFixed(2);
              $('#editarea').val(mainArea);
          });
          //width
          $("#editwidth").blur(function () {
              var width = $('#editwidth').val();
              var mainWidth = parseFloat(width).toFixed(2);
              $("#editwidth").val(mainWidth);
              // Area
              var mainHeight = $('#editheight').val();
              var area = mainHeight * mainWidth;
              var mainArea = parseFloat(area).toFixed(2);
              $('#editarea').val(mainArea);
          });
          // heightWaste
          $("#editheightWaste").blur(function () {
              var heightWaste = $('#editheightWaste').val();
              var mainHeightWaste = parseFloat(heightWaste).toFixed(2);
              $("#editheightWaste").val(mainHeightWaste);
          });
          // widthWaste
          $("#editwidthWaste").blur(function () {
              var widthWaste = $('#editwidthWaste').val();
              var mainWidthWaste = parseFloat(widthWaste).toFixed(2);
              $("#editwidthWaste").val(mainWidthWaste);
          });

          // AgentRecievd Money
          $("#ar").blur(function () {
              var widthWaste = $('#ar').val();
              var mainWidthWaste = parseFloat(widthWaste).toFixed(2);
              $("#ar").val(mainWidthWaste);
          });
          // Material Amount
          $("#material-amount").blur(function () {
              var widthWaste = $('#material-amount').val();
              var mainWidthWaste = parseFloat(widthWaste).toFixed(2);
              $("#material-amount").val(mainWidthWaste);
          });
          // Material Price
          $("#material-price").blur(function () {
              var widthWaste = $('#material-price').val();
              var mainWidthWaste = parseFloat(widthWaste).toFixed(2);
              $("#material-price").val(mainWidthWaste);
          });
      });
  </script>
@endsection