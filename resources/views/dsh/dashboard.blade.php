@extends('dsh.master')

@section('content')
  <div class="sparkline8-hd">
    <div class="main-sparkline8-hd">
      
      <div class="alert alert-success" style="display:none;" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                  aria-hidden="true">&times;</span></button>
        جزئیات حذف شد
      </div>
      
      @if(session("status"))
        <div class="alert alert-success status text-center" style="display:none;" role="alert">
          <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
          {{session('status')}}
        </div>
      
      @endif
      @if(session("error"))
        
        <div class="alert alert-danger status text-center" style="display:none;" role="alert">
          <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
          {{session('error')}}
        </div>
      
      @endif
    </div>
  </div>
  <div class="section-admin container-fluid">
    <div class="row admin">
      <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <br>
        
        @if(auth()->user()->role != 'PH')
          @if(auth()->user()->role == 'SP')
            <div class="row">
              <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                <div class="card">
                  <div class="card-body">
                    <div class="row align-items-center">
                      <div class="col-8">
                        
                        <h4 class="text-c-yellow">@if($all_office_cashbook)
                            $  {{round($all_office_cashbook,2)}}
                          @else  $ 0  @endif </h4>
                      
                      </div>
                      <div class="col-4 text-center">
                        <i class="feather icon-bar-chart-2 f-28"></i>
                      </div>
                    </div>
                  </div>
                  <div class="card-footer bg-success">
                    <div class="row align-items-center">
                      <div class="col-9">
                        <h5 class="text-white m-b-0">مجموعه پول تمام دفاتر</h5>
                      </div>
                      <div class="col-3 text-right">
                        <i class="feather icon-trending-up text-white f-16"></i>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              
              <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                <div class="card">
                  <div class="card-body">
                    <div class="row align-items-center">
                      <div class="col-8">
                        
                        <h4 class="text-c-yellow">@if($sales_cashbook)
                            $ {{round($sales_cashbook,2)}}
                          @else  $ 0  @endif </h4>
                      
                      </div>
                      <div class="col-4 text-center">
                        <i class="feather icon-bar-chart-2 f-28"></i>
                      </div>
                    </div>
                  </div>
                  <div class="card-footer bg-behance">
                    <div class="row align-items-center">
                      <div class="col-9">
                        <h5 class="text-white m-b-0">مجموعه پول نقد در دخل فروشات</h5>
                      </div>
                      <div class="col-3 text-right">
                        <i class="feather icon-trending-up text-white f-16"></i>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              
              <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                <div class="card">
                  <div class="card-body">
                    <div class="row align-items-center">
                      <div class="col-8">
                        
                        <h4 class="text-c-yellow">@if($central_cashbook)
                            $ {{round($central_cashbook,2)}}
                          @else  $ 0  @endif </h4>
                      
                      </div>
                      <div class="col-4 text-center">
                        <i class="feather icon-bar-chart-2 f-28"></i>
                      </div>
                    </div>
                  </div>
                  <div class="card-footer bg-info">
                    <div class="row align-items-center">
                      <div class="col-9">
                        <h5 class="text-white m-b-0">مجموعه پول نقد در دخل مرکزی</h5>
                      </div>
                      <div class="col-3 text-right">
                        <i class="feather icon-trending-up text-white f-16"></i>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              
              <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                <a href="/dashboard/add-office-credit">
                  <div class="card">
                    <div class="card-body">
                      <div class="row align-items-center">
                        <div class="col-8">
                          
                          <h4 class="text-c-yellow">@if($general_cashbook) {{round($general_cashbook,2)}}
                            $ @else  $ 0  @endif  </h4>
                        
                        </div>
                        <div class="col-4 text-center">
                          <i class="feather icon-bar-chart-2 f-28"></i>
                        </div>
                      </div>
                    </div>
                    <div class="card-footer bg-primary">
                      <div class="row align-items-center">
                        <div class="col-9">
                          <h5 class="text-white m-b-0">مجموعه پول نقد در دخل عمومی</h5>
                        </div>
                        <div class="col-3 text-right">
                          <i class="feather icon-trending-up text-white f-16"></i>
                        </div>
                      </div>
                    </div>
                  </div>
                </a>
              </div>
            
            </div>
          @endif
        @endif
        <div class="row">
          @if(auth()->user()->role != 'PH')
            @if(auth()->user()->role != 'CO')
              <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                <a href="/dashboard/carpet-stock">
                  <div class="card">
                    <div class="card-body">
                      <div class="row align-items-center">
                        <div class="col-6">
                          
                          <h4 class="text-c-yellow"> @if($ready_to_sale_carpet)
                               {{$ready_to_sale_carpet->count()}} تخته
                            @else 0 تخته @endif</h4>
  
                      
                        
                        </div>
                        <div class="col-6 text-right">
                          <h4 class="text-c-yellow" > @if($ready_to_sale_carpet)
                             {{$ready_to_sale_carpet->sum('area')}} m <sup>2</sup>
                            @else 0 m <sup>2</sup> @endif</h4>

                        </div>
                      </div>
                    </div>
                    <div class="card-footer bg-c-blue">
                      <div class="row align-items-center">
                        <div class="col-9">
                          <h5 class="text-white m-b-0"> مجموعه قالین های
                            اماده
                            به
                            فروش</h5>
                        </div>
                        <div class="col-3 text-right">
                          <i class="feather icon-trending-up text-white f-16"></i>
                        </div>
                      </div>
                    </div>
                  </div>
                </a>
              </div>
            @endif
            @if(auth()->user()->role == 'SP')
              <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                <a href="/dashboard/all-carpets">
                  <div class="card">
                    <div class="card-body">
                      <div class="row align-items-center">
                        <div class="col-6">
                          
                          <h4 class="text-c-yellow">  @if($all_carpet_count) {{$all_carpet_count->count()}} تخته
                            @else تخته 0  @endif</h4>
                        
                        </div>
                        <div class="col-6 text-center">
                          <h4 class="text-c-yellow">  @if($all_carpet_count) {{$all_carpet_count->sum('area')}}  m <sup>2</sup>
                            @else m <sup>2</sup> 0  @endif</h4>
                        </div>
                      </div>
                    </div>
                    <div class="card-footer bg-success">
                      <div class="row align-items-center">
                        <div class="col-9">
                          <h5 class="text-white m-b-0"> مجموعه عمومی تعداد قالین</h5>
                        </div>
                        <div class="col-3 text-right">
                          <i class="feather icon-trending-up text-white f-16"></i>
                        </div>
                      </div>
                    </div>
                  </div>
                </a>
              </div>
              
            @endif
            @if(auth()->user()->role != 'CO')
              <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                <a href="/dashboard/carpets-in-sales-office">
                  <div class="card">
                    <div class="card-body">
                      <div class="row align-items-center">
                        <div class="col-6">
                          
                          <h4 class="text-c-yellow">@if($sale_carpet_area) {{$sale_carpet_area->sum('area')}} m <sup>2</sup>
                            @else   0 m <sup>2</sup> @endif</h4>
                        
                        </div>
                        <div class="col-6 text-right">
                          <h4 class="text-c-yellow">@if($sale_carpet_area) {{$sale_carpet_area->count()}}  تخته
                            @else   0 تخته @endif</h4>
                        </div>
                      </div>
                    </div>
                    <div class="card-footer bg-c-yellow">
                      <div class="row align-items-center">
                        <div class="col-9">
                          <h5 class="text-white m-b-0"> مجموعه متراژ قالین در دفتر فروشات</h5>
                        </div>
                        <div class="col-3 text-right">
                          <i class="feather icon-trending-up text-white f-16"></i>
                        </div>
                      </div>
                    </div>
                  </div>
                </a>
              </div>
            @endif
            
            @if(auth()->user()->role != 'SO' && auth()->user()->role != 'OM')
              @if(auth()->user()->role == 'CO')
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                  @else
                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                      @endif
                      <div class="card">
                        <a href="/dashboard/carpets-in-center-office">
                          <div class="card-body">
                            <div class="row align-items-center">
                              <div class="col-6">
                                
                                <h4 class="text-c-yellow"> @if($central_carpet_area)  {{$central_carpet_area->sum('area')}}
                                  m <sup>2</sup>
                                  @else   0 m <sup>2</sup>  @endif </h4>
                              
                              </div>
                              <div class="col-6 text-right">
                                <h4 class="text-c-yellow"> @if($central_carpet_area)  {{$central_carpet_area->count()}}
                               تخته
                                  @else   0 تخته  @endif </h4>
                              </div>
                            </div>
                          </div>
                          <div class="card-footer bg-info">
                            <div class="row align-items-center">
                              <div class="col-9">
                                <h5 class="text-white m-b-0">مجموعه متراژ قالین در دفتر مرکزی</h5>
                              </div>
                              <div class="col-3 text-right">
                                <i class="feather icon-trending-up text-white f-16"></i>
                              </div>
                            </div>
                          </div>
                        </a>
                      </div>
                    </div>
                  @endif
                  
                  @if(auth()->user()->role != 'SP')
                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                      <a href="/dashboard/add-office-credit">
                        <div class="card">
                          <div class="card-body">
                            <div class="row align-items-center">
                              <div class="col-8">
                                
                                <h4 class="text-c-yellow">@if($loged_user_cashbook) {{round($loged_user_cashbook,2)}}
                                  $ @else  $ 0  @endif </h4>
                              
                              </div>
                              <div class="col-4 text-center">
                                <i class="feather icon-bar-chart-2 f-28"></i>
                              </div>
                            </div>
                          </div>
                          <div class="card-footer bg-c-yellow">
                            <div class="row align-items-center">
                              <div class="col-9">
                                <h5 class="text-white m-b-0">پول نقد در دخل</h5>
                              </div>
                              <div class="col-3 text-right">
                                <i class="feather icon-trending-up text-white f-16"></i>
                              </div>
                            </div>
                          </div>
                        </div>
                      </a>
                    </div>
                  @endif
                </div>
                
                <div class="row">
                  @if(auth()->user()->role != 'SO' && auth()->user()->role != 'OM')
                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                      <a href="/dashboard/material-stock">
                        <div class="card">
                          <div class="card-body">
                            <div class="row align-items-center">
                              <div class="col-8">
                                
                                <h4 class="text-c-yellow">{{$firstTotal}} kg </h4>
                              
                              </div>
                              <div class="col-4 text-center">
                                <i class="feather icon-bar-chart-2 f-28"></i>
                              </div>
                            </div>
                          </div>
                          <div class="card-footer bg-c-yellow">
                            <div class="row align-items-center">
                              <div class="col-9">
                                <h5 class="text-white m-b-0"> مجموعه {{$firstName->material_category}}</h5>
                              </div>
                              <div class="col-3 text-right">
                                <i class="feather icon-trending-up text-white f-16"></i>
                              </div>
                            </div>
                          </div>
                        </div>
                      </a>
                    </div>
                    
                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                      <a href="/dashboard/material-stock">
                        <div class="card">
                          <div class="card-body">
                            <div class="row align-items-center">
                              <div class="col-8">
                                
                                <h4 class="text-c-yellow">{{$secondTotal}} kg </h4>
                              
                              </div>
                              <div class="col-4 text-center">
                                <i class="feather icon-bar-chart-2 f-28"></i>
                              </div>
                            </div>
                          </div>
                          <div class="card-footer bg-c-yellow">
                            <div class="row align-items-center">
                              <div class="col-9">
                                <h5 class="text-white m-b-0"> مجموعه {{$secondName->material_category}}</h5>
                              </div>
                              <div class="col-3 text-right">
                                <i class="feather icon-trending-up text-white f-16"></i>
                              </div>
                            </div>
                          </div>
                        </div>
                      </a>
                    </div>
                    
                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                      <a href="/dashboard/material-stock">
                        <div class="card">
                          <div class="card-body">
                            <div class="row align-items-center">
                              <div class="col-8">
                                
                                <h4 class="text-c-yellow">{{$thirdTotal}} kg </h4>
                              
                              </div>
                              <div class="col-4 text-center">
                                <i class="feather icon-bar-chart-2 f-28"></i>
                              </div>
                            </div>
                          </div>
                          <div class="card-footer bg-c-yellow">
                            <div class="row align-items-center">
                              <div class="col-9">
                                <h5 class="text-white m-b-0"> مجموعه {{$thirdName->material_category}}</h5>
                              </div>
                              <div class="col-3 text-right">
                                <i class="feather icon-trending-up text-white f-16"></i>
                              </div>
                            </div>
                          </div>
                        </div>
                      </a>
                    </div>
                  
                  @endif
                </div>
                @if(auth()->user()->role == 'SP')
                  <div class="row">
                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                      <div class="card">
                        <div class="card-body">
                          <div class="row align-items-center">
                            <div class="col-8">
                              
                              <a href="/dashboard/edit-currency/1" class="btn btn-primary btn-sm">ویرایش</a>
                            
                            </div>
                            <div class="col-4 text-right">
                              {{$currency}} $
                            </div>
                          </div>
                        </div>
                        <div class="card-footer bg-c-yellow">
                          <div class="row align-items-center">
                            <div class="col-9">
                              <h5 class="text-white m-b-0">نرخ تبادله دالر</h5>
                            </div>
                            <div class="col-3 text-right">
                              <i class="feather icon-trending-up text-white f-16"></i>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                      
                      <div class="card">
                        <div class="card-body">
                          <div class="row align-items-center">
                            <div class="col-8">
                              
                              <h4 class="text-c-yellow">
                                
                                @if($gerft_ha - $rasidat > 0)
                                  $ {{ round($gerft_ha - $rasidat,2)}}
                                @else
                                  0
                                @endif
                              </h4>
                            
                            </div>
                            <div class="col-4 text-center">
                              <i class="feather icon-bar-chart-2 f-28"></i>
                            </div>
                          </div>
                        </div>
                        <div class="card-footer bg-c-yellow">
                          <div class="row align-items-center">
                            <div class="col-9">
                              <h5 class="text-white m-b-0">مجموع طلب شرکت بالای مردم</h5>
                            </div>
                            <div class="col-3 text-right">
                              <i class="feather icon-trending-up text-white f-16"></i>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                      <div class="card">
                        <div class="card-body">
                          <div class="row align-items-center">
                            <div class="col-8">
                              
                              <h4 class="text-c-yellow">
                                @if( $rasidat - $gerft_ha > 0)
                                  $ {{ round($rasidat - $gerft_ha,2)}}
                                @else
                                  0
                                @endif
                              
                              </h4>
                            
                            </div>
                            <div class="col-4 text-center">
                              <i class="feather icon-bar-chart-2 f-28"></i>
                            </div>
                          </div>
                        </div>
                        <div class="card-footer bg-c-yellow">
                          <div class="row align-items-center">
                            <div class="col-9">
                              <h5 class="text-white m-b-0">مجموع طلب مردم بالای شرکت</h5>
                            </div>
                            <div class="col-3 text-right">
                              <i class="feather icon-trending-up text-white f-16"></i>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                @endif
              @endif
              
             
              
        
              <div class="card">
                <div class="card-header">
                   <div class="row">
                    <div class="col-lg-2 col-md-2  col-sm-12 col-xs-12">
                      <form action="/dashboard/all-carpet-dashboard/search" method="post">
                        @csrf
                        <input type="text" name="search" required
                               placeholder="جستجو" class="form-control">
                      </form>
                    </div>
                    <div class="col-lg-7 col-md-7 col-sm-12 col-xs-12 hideOnPrint">
                      @if(auth()->user()->role == 'SP')
                        <form action="/dashboard/search-this-month-carpet" method="POST" id="dateSearch">
                          @csrf
                          <div class="row">
                            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 hideOnPrint">
                              <input type="submit" value="جستجو" class="date-submit btn btn-primary btn-sm btn-block"
                                     style="margin-top: 20px;">
                            </div>
                            
                            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                              <span class="date-label">ماه</span>
                              <select name="month" id="month" class="form-control">
                                <option value="1">1-جنوری</option>
                                <option value="2">2-فبروری</option>
                                <option value="3">3-مارچ</option>
                                <option value="4">4-اپریل</option>
                                <option value="5">5-می</option>
                                <option value="6">6-جون</option>
                                <option value="7">7-جولای</option>
                                <option value="8">8-اگست</option>
                                <option value="9">9-سپتمبر</option>
                                <option value="10">10-اکتبر</option>
                                <option value="11">11-نومبر</option>
                                <option value="12">12-دسمبر</option>
                              </select>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                              <span class="date-label">سال</span>
                              <select name="year" id="year" class="form-control">
                                <option value="2022">2022</option>
                                <option value="2023">2023</option>
                                <option value="2024">2024</option>
                                <option value="2025">2025</option>
                                <option value="2026">2026</option>
                                <option value="2027">2027</option>
                                <option value="2028">2028</option>
                                <option value="2029">2029</option>
                                <option value="2030">2030</option>
                                <option value="2031">2031</option>
                                <option value="2032">2032</option>
                              </select>
                            </div>
                          </div>
                        </form>
                      @endif
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 hideOnPrint">
                      <div class="btn-group hideOnPrint" id="exportButton" style="float: left; ">
                        <div class="btn btn-sm btn-primary" style="float: left" onclick="printPage('carpet_list')"><i
                                  class="fa fa-print"></i> چاپ
                        </div>
                      
                      </div>
                      <a href="/dashboard/all-carpet-dashboard-show-all" style="float: left;"
                         class="btn btn-sm btn-info hideOnPrint">نمایش
                        همه</a>
                    
                     <a href="/dashboard/all-exesting-carpet" style="float: left;"
                         class="btn btn-sm btn-info hideOnPrint">نمایش قالین های موجود</a>
                    
                    </div>
                  </div>
                </div>
                <div class="card-body" id="carpet_list">
                    <div class="modal fade carpet_image" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
                       aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title h4" id="myLargeModalLabel">
                            <input type="text" name="carpet_no" id="parcha_number" class="form-control" readonly>
                          </h5>
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        </div>
                        <div class="modal-body">
          
                          <img src="" id="carpet_image" height="800px" width="700px" alt="">
        
        
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn  btn-secondary" data-dismiss="modal">بستن</button>
                        </div>
                      </div>
                    </div>
                  </div>
                  
                  
                  <div class="table-responsive">
                    <table class="table table-hover table-xs" id="carpet-list">
                      <thead>
                      <tr>
                        <th>شماره قالین</th>
                        <th>اسم نماینده</th>
                        <th>شماره پارچه</th>
                        <th>شماره فرمایش</th>
                        <th>نوعیت</th>
                        <th>کوالتی</th>
                        <th>نمبر نقشه</th>
                        <th>قیمت فی متر</th>
                        <th>قیمت مجموع افغانی</th>
                        <th>قیمت مجموع دالر</th>
                        <th>حاشیه</th>
                        <th>زمینه</th>
                        <th>طول</th>
                        <th>عرض</th>
                        <th>مساحت</th>
                        <th>تاریخ</th>
                           <th>تصویر</th>
                        @if(auth()->user()->role != 'PH')
                          <th class="hideOnPrint">ویرایش</th>
                          <th class="hideOnPrint">حالت</th>
                        @endif
                      </tr>
                      </thead>
                      <tbody>
                      @foreach($all_carpets_for_centeral as $carpet)
                        <tr class="ur{{ $carpet->carpet_id }}">
                          <td>{{$carpet->carpet_no}}</td>
                          
                          <td>{{$carpet->agent->user->name}}</td>
                          <td>{{$carpet->parcha_number}}</td>
                          @if($carpet->carpet_order)
                            <td>{{$carpet->carpet_order->order_number}}</td>
                          @else
                            <td></td>
                          @endif
                          @if($carpet->type)
                            <td>{{$carpet->type->carpet_type}}</td>
                          @else
                            <td></td>
                          @endif
                          @if($carpet->quality)
                            <td>{{$carpet->quality->quality}}</td>
                          @else
                            <td></td>
                          @endif
                          <td>{{$carpet->map_number}}</td>
                          <td style="direction: ltr">{{$carpet->price}} $</td>
                          <td style="direction: ltr">{{$carpet->carpet_price}} AF</td>
                          <td style="direction: ltr">{{$carpet->carpet_price_us}} $</td>
                          
                          <td>{{$carpet->margin}}</td>
                          <td>{{$carpet->field}}</td>
                          <td style="direction: ltr">{{$carpet->width}} m</td>
                          <td style="direction: ltr">{{$carpet->height}} m</td>
                          <td style="direction: ltr">{{$carpet->area}} m <sup>2</sup></td>
                          <td>{{$carpet->date}}</td>
                           <td><a href="#"
                                 onclick="   $('#parcha_number').val('  تصویر قالین  {{$carpet->parcha_number}} ');
                                         $('#carpet_image').attr('src', '/{{str_replace('\\','/',$carpet->carpet_image)}}');
                                         "
                                 data-toggle="modal"
                                 data-target=".carpet_image"><img src="/{{$carpet->carpet_image}}" style="height: 32px;" alt=""></a></td>
                          @if(auth()->user()->role != 'PH' )
                            {{--
                                                   @if($carpet->status == 0 || $carpet->status == 1 || $carpet->status == 2 || $carpet->status == 12 || $carpet->status == 3 || $carpet->status == 13)--}}
    
                            @if(auth()->user()->role == 'SP' )
                            <td class="hideOnPrint"><a href="/dashboard/all-carpet-edit/{{$carpet->carpet_id}}"
                                                       class="btn btn-sm btn-info hideOnPrint"><i
                                        class="fa fa-pencil"></i>&nbsp; ویرایش</a></td>
                            @endif
                            {{--@else--}}
                            {{--<td></td>--}}
                            {{--@endif--}}
                            <td class="hideOnPrint">
                              @if($carpet->status == 0)
                                <label for="" class="badge badge-success">در نزد نماینده</label>
                              @elseif($carpet->status == 1)
                                <label for="" class="badge badge-warning">در گدام مرکزی</label>
                              @elseif($carpet->status == 2)
                                
                                <label for="" class="badge badge-danger">در کچایی نشده ها</label>
                              @elseif($carpet->status == 12)
                                
                                <label for="" class="badge badge-primary">در کچایی شده ها</label>
                              @elseif($carpet->status == 3)
                                
                                <label for="" class="badge badge-light-warning">در شست نشده ها</label>
                              @elseif($carpet->status == 13)
                                
                                <label for="" class="badge badge-primary">در شست شده ها</label>
                              @elseif($carpet->status == 4)
                                <label for="" class="badge badge-light-primary">در بخش تیاری</label>
                              @elseif($carpet->status == 5)
                                <label for="" class="badge badge-info">در گدام فروشات</label>
                              @elseif($carpet->status == 6)
                                <label for="" class="badge badge-success">فروخته شده</label>
  
                              @endif
                            </td>
                          @endif
                        
                        </tr>
                      @endforeach
                      
                    
                   
                      </tbody>
                    </table>
                     
                  </div>
                  <div style="float: right;">
                   @if(!isset($search))
                      <p>{{$all_carpets_for_centeral->links()}}</p>
                    

                    @endif
                  </div>
                </div>
              </div>
        
        </div>
      </div>
    </div>
  </div>



@endsection



@section('footer-plugins')
  
  
  
  <script>
      $(document).ready(function () {
           $('#month').select2();
          $('#year').select2();
          $("#carpet-list").tableExport({
              headers: true,                      // (Boolean), display table headers (th or td elements) in the <thead>, (default: true)
              footers: true,                      // (Boolean), display table footers (th or td elements) in the <tfoot>, (default: false)
              formats: ["xlsx","txt"],              // (String[]), filetype(s) for the export, (default: ['xlsx', 'csv', 'txt'])
              filename: "id",                     // (id, String), filename for the downloaded file, (default: 'id')
              bootstrap: true,                   // (Boolean), style buttons using bootstrap, (default: true)
              exportButtons: true,                // (Boolean), automatically generate the built-in export buttons for each of the specified formats (default: true)
              position: "bottom",                 // (top, bottom), position of the caption element relative to table, (default: 'bottom')
              ignoreRows: null,                   // (Number, Number[]), row indices to exclude from the exported file(s) (default: null)
              ignoreCols: null,                   // (Number, Number[]), column indices to exclude from the exported file(s) (default: null)
              trimWhitespace: true,               // (Boolean), remove all leading/trailing newlines, spaces, and tabs from cell text in the exported file(s) (default: false)
              RTL: true,                         // (Boolean), set direction of the worksheet to right-to-left (default: false)
              sheetname: "id",

          });
          var $buttons = $('#carpet-list').find('caption').children().detach();
          // Append the buttons to an element of your choosing
          $buttons.appendTo('#exportButton');

      });
  
  
  
  </script>
@endsection