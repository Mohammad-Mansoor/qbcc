@extends('dsh.master')

@section('content')
  
  <div class="section-admin container-fluid">
    <div class="row admin text-center">
      <div class="col-md-12">
        <br>
        <div class="row">
          <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
            <a href="/dashboard/talab-mardom?type=nomainda">
              <div class="admin-content analysis-progrebar-ctn res-mg-t-15"
                   style="background-color:#fff; margin-bottom:20px; border-bottom:3px solid #0D2267; text-shadow: 0.1em 0.1em 0.15em #ccc;">
                <h5 class="text-right text-uppercase" style="color:#000; margin-bottom:20px;"><b>مجموعه طلب نماینده
                    ها</b>
                </h5>
                <hr>
                <div class="row vertical-center-box vertical-center-box-tablet">
                  <h4 class="text-left"
                      style="color:#cc7227; margin-left:15px;direction:ltr;">@if($talab_nomainda)
                      AF  {{round($talab_nomainda,2)}}
                    @else  AF 0  @endif </h4>
                </div>
              </div>
            </a>
          </div>
          <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
            <a href="/dashboard/talab-mardom?type=kachaee">
              <div class="admin-content analysis-progrebar-ctn res-mg-t-15"
                   style="background-color:#fff; margin-bottom:20px; border-bottom:3px solid #0D2267; text-shadow: 0.1em 0.1em 0.15em #ccc;">
                <h5 class="text-right text-uppercase" style="color:#000; margin-bottom:20px;"><b>مجموعه طلب کچایی</b>
                </h5>
                <hr>
                <div class="row vertical-center-box vertical-center-box-tablet">
                  <h4 class="text-left"
                      style="color:#cc7227; margin-left:15px;direction:ltr;">@if($talab_kachaee)
                      AF {{round($talab_kachaee,2)}}
                    @else  AF 0  @endif </h4>
                </div>
              </div>
            </a>
          
          </div>
          <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
            <a href="/dashboard/talab-mardom?type=shost">
              <div class="admin-content analysis-progrebar-ctn res-mg-t-15"
                   style="background-color:#fff; margin-bottom:20px; border-bottom:3px solid #0D2267; text-shadow: 0.1em 0.1em 0.15em #ccc;">
                <h5 class="text-right text-uppercase" style="color:#000; margin-bottom:20px;"><b>مجموعه طلب شست</b></h5>
                <hr>
                <div class="row vertical-center-box vertical-center-box-tablet">
                  <h4 class="text-left"
                      style="color:#cc7227; margin-left:15px;direction:ltr;">@if($talab_shost)
                      AF {{round($talab_shost,2)}}
                    @else  AF 0  @endif </h4>
                </div>
              </div>
            </a>
          
          </div>
          <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
            <a href="/dashboard/talab-mardom?type=tayari">
              <div class="admin-content analysis-progrebar-ctn res-mg-t-15"
                   style="background-color:#fff; margin-bottom:20px; border-bottom:3px solid #0D2267; text-shadow: 0.1em 0.1em 0.15em #ccc;">
                <h5 class="text-right text-uppercase" style="color:#000; margin-bottom:20px;"><b>مجموعه طلب تیاری</b>
                </h5>
                <hr>
                <div class="row vertical-center-box vertical-center-box-tablet">
                  <h4 class="text-left"
                      style="color:#cc7227; margin-left:15px;direction:ltr;">@if($talab_tayari)
                      AF {{round($talab_tayari,2)}}
                    @else  AF 0  @endif </h4>
                </div>
              </div>
            </a>
          
          </div>
        </div>
        
        <div class="row">
          
          
          <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
            <a href="/dashboard/talab-mardom?type=tar_frosh">
              <div class="admin-content analysis-progrebar-ctn res-mg-t-15"
                   style="background-color:#fff; margin-bottom:20px; border-bottom:3px solid #0D2267; text-shadow: 0.1em 0.1em 0.15em #ccc;">
                <h5 class="text-right text-uppercase" style="color:#000; margin-bottom:20px;"><b>مجموعه طلب تار فروش</b>
                </h5>
                <hr>
                <div class="row vertical-center-box vertical-center-box-tablet">
                  <h4 class="text-left"
                      style="color:#cc7227; margin-left:15px;direction:ltr;">@if($talab_tar_frosh)
                      AF {{round($talab_tar_frosh,2)}}
                    @else  AF 0  @endif </h4>
                </div>
              </div>
            </a>
          
          </div>
          <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
            <a href="/dashboard/talab-mardom?type=motafareqa">
              <div class="admin-content analysis-progrebar-ctn res-mg-t-15"
                   style="background-color:#fff; margin-bottom:20px; border-bottom:3px solid #0D2267; text-shadow: 0.1em 0.1em 0.15em #ccc;">
                <h5 class="text-right text-uppercase" style="color:#000; margin-bottom:20px;"><b>مجموعه طلب متفرقه</b>
                </h5>
                <hr>
                <div class="row vertical-center-box vertical-center-box-tablet">
                  <h4 class="text-left"
                      style="color:#cc7227; margin-left:15px;direction:ltr;">@if($talab_motafareqa)
                      $ {{round($talab_motafareqa,2)}}
                    @else  $ 0  @endif </h4>
                </div>
              </div>
            </a>
          
          </div>
          
          <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
            <a href="/dashboard/talab-mardom?type=froshenda_qalin">
              <div class="admin-content analysis-progrebar-ctn res-mg-t-15"
                   style="background-color:#fff; margin-bottom:20px; border-bottom:3px solid #0D2267; text-shadow: 0.1em 0.1em 0.15em #ccc;">
                <h5 class="text-right text-uppercase" style="color:#000; margin-bottom:20px;"><b>مجموعه طلب فروشنده
                    قالین</b></h5>
                <hr>
                <div class="row vertical-center-box vertical-center-box-tablet">
                  <h4 class="text-left"
                      style="color:#cc7227; margin-left:15px;direction:ltr;">@if($talab_froshenda_qalin)
                      $ {{round($talab_froshenda_qalin,2)}}
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
                      <th>مچموع پول</th>
                      <th>پول برداخت شده</th>
                      <th>پول باقیمانده بالای شرکت</th>
                      
                      
                      <!-- <th>حذف</th> -->
                    
                    </tr>
                    </thead>
                    <tbody>
                    
                    @if($all)
                      @foreach($all as $a)
                        <tr>
                          @if($type == 'nomainda')
                            <td>{{$a->agent->user->name}}</td>
                          @elseif($type == 'tayari')
                            <td>{{$a->team->name}}</td>
                          @elseif($type == 'shost')
                            <td>{{$a->washing_team->name}}</td>
                          @elseif($type == 'kachaee')
                            <td>{{$a->kachaee->name}}</td>
                          @elseif($type == 'froshenda_qalin')
                            <td>{{$a->agent->user->name}}</td>
                          @elseif($type == 'motafareqa')
                            <td>{{$a->account->name}}</td>
                          @elseif($type == 'tar_frosh')
                            <td>{{$a->seller->name}}</td>
                          @endif
                          <td>{{$a->total}}</td>
                          <td>{{$a->paid}}</td>
                          <td>{{$a->remaining}}</td>
                        </tr>
                      @endforeach
                      <tr>
                        <th colspan="2">مجموعه طلب مردم از شرکت</th>
                        <td style="display: none;"></td>
                        <td></td>
                        <td>{{$all->sum('remaining')}}</td>

                      </tr>
                    
                    @else
                      <h5 style="color: red;text-align: center">نوعیت طلب انتخاب نشده</h5>
                    @endif
                    
                    
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