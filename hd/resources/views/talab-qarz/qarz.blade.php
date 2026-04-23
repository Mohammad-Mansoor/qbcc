@extends('dsh.master')

@section('content')
  
  <div class="section-admin container-fluid">
    <div class="row admin text-center">
      <div class="col-md-12">
        <br>
        
        
        <div class="row">
          
          
          <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
            <a href="/dashboard/qarz-mardom?type=kharidar_tar">
              <div class="admin-content analysis-progrebar-ctn res-mg-t-15"
                   style="background-color:#fff; margin-bottom:20px; border-bottom:3px solid #0D2267; text-shadow: 0.1em 0.1em 0.15em #ccc;">
                <h5 class="text-right text-uppercase" style="color:#000; margin-bottom:20px;"><b>مجموعه قرض مشتری
                    تار</b>
                </h5>
                <hr>
                <div class="row vertical-center-box vertical-center-box-tablet">
                  <h4 class="text-left"
                      style="color:#cc7227; margin-left:15px;direction:ltr;">@if($qarz_kharidar_tar_af)
                      AF {{round($qarz_kharidar_tar_af,2)}}
                    @else  AF 0  @endif </h4>
                </div>
              </div>
            </a>
          
          </div>
          <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
            <a href="/dashboard/qarz-mardom?type=motafareqa">
              <div class="admin-content analysis-progrebar-ctn res-mg-t-15"
                   style="background-color:#fff; margin-bottom:20px; border-bottom:3px solid #0D2267; text-shadow: 0.1em 0.1em 0.15em #ccc;">
                <h5 class="text-right text-uppercase" style="color:#000; margin-bottom:20px;"><b>مجموعه قرض متفرقه</b>
                </h5>
                <hr>
                <div class="row vertical-center-box vertical-center-box-tablet">
                  <h4 class="text-left"
                      style="color:#cc7227; margin-left:15px;direction:ltr;">@if($qarz_motafareqa)
                      $ {{round($qarz_motafareqa,2)}}
                    @else  $ 0  @endif </h4>
                </div>
              </div>
            </a>
          
          </div>
          
          <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
            <a href="/dashboard/qarz-mardom?type=kharidar_qalin">
              <div class="admin-content analysis-progrebar-ctn res-mg-t-15"
                   style="background-color:#fff; margin-bottom:20px; border-bottom:3px solid #0D2267; text-shadow: 0.1em 0.1em 0.15em #ccc;">
                <h5 class="text-right text-uppercase" style="color:#000; margin-bottom:20px;"><b>مجموعه قرض مشتری
                    قالین</b></h5>
                <hr>
                <div class="row vertical-center-box vertical-center-box-tablet">
                  <h4 class="text-left"
                      style="color:#cc7227; margin-left:15px;direction:ltr;">@if($qarz_kharidar_qalin)
                      $ {{round($qarz_kharidar_qalin,2)}}
                    @else  $ 0  @endif </h4>
                </div>
              </div>
            </a>
          
          </div>
        
        </div>
        <br>
        
        <div class="row">
          <div class="col-lg-12 col-md-12 col-sm-6 col-xs-12">
            <div class="sparkline8-list">
              <div class="sparkline8-hd">
                <div class="main-sparkline8-hd">
                
                
                </div>
              </div>
              <div class="sparkline8-graph">
                <div class="btn btn-primary btn-sm hideOnPrint pull-right" onclick="printPage('MRDetails')"
                     style="position: relative;right:20px;"><i class="fa fa-print"></i> Print
                </div>
                <br>
                <br>
                <div class="static-table-list" id="MRDetails">
                  <table class="table text-center" id="dataTable">
                    <thead>
                    <tr>
                      
                      <th>نام</th>
                      <th> مچموع طلب شرکت از مشتری</th>
                      <th>پول برداخت شده توسط مشتری</th>
                      <th>پول باقیمانده بالای مشتری</th>
                      
                      
                      <!-- <th>حذف</th> -->
                    
                    </tr>
                    </thead>
                    <tbody>
                    
                    @if($all)
                      @foreach($all as $a)
                        <tr>
                          
                          @if($type == 'motafareqa')
                            <td>{{$a->account->name}}</td>
                          @elseif($type == 'kharidar_tar')
                            <td>{{$a->customer->name}}</td>
                          @elseif($type == 'kharidar_qalin')
                            <td>{{$a->customer->name}}</td>
                          @endif
                          <td>{{$a->total}}</td>
                          <td>{{$a->paid}}</td>
                          @if($type == 'motafareqa')
                            <td>{{$a->remaining / -1}}</td>
                          @else
                            <td>{{$a->remaining}}</td>
                          @endif
                        
                        </tr>
                      @endforeach
                    
                        <tr>
                          <th colspan="2">مجموعه طلب شرکت از مشتری</th>
                          <td style="display: none;"></td>
                          <td></td>
                          <td>{{$all->sum('remaining')}}</td>
                        
                        </tr>
                    @else
                      <h5 style="color: red;text-align: center">نوعیت طلب انتخاب نشده</h5>
                    @endif
                    
                    
                    </tbody>
                  </table>
                  {{-- <p>{{$carpets->links()}}</p> --}}
                </div>
              </div>
            </div>
          </div>
        
        </div>
      
      </div>
    </div>
  </div>



@endsection