@extends('dsh.master')

@section('content')

  <div class="row" id="finish-number-list">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <div class="card">
        <div class="card-header">
          <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-4 ">
              <table class="table table-sm table-hover text-right">
                <thead>

                </thead>
                <tbody>

                <tr>
                  <td style="color: dodgerblue;"><b></b></td>
                  <td style="color: dodgerblue;"><b>FINISHING WORK DETAILS</b></td>


                </tr>
                <tr>
                  <td style="direction: ltr"><b>{{$finish_number}}</b></td>
                  <td style="direction: ltr">Bill#:</td>
                </tr>
                <tr>
                    <?php
                    $today_date = \App\FinishingWork::where('team_id', $team->id)->where('finish_number', $finish_number)->first();
                    ?>
                  <td>
                    <b>@if($today_date) {{$today_date->date}} @else {{Carbon\Carbon::today()->format('Y-m-d')}} @endif</b>
                  </td>
                  <td style="direction: ltr">Bill Date:</td>
                </tr>

                </tbody>
              </table>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-4 ">
            </div>
            <div class="col-lg-4 col-md-4 col-sm-4 ">
              <table class="table table-sm table-hover text-left">
                <thead>

                </thead>
                <tbody>
                <tr style="direction: ltr;text-align: left;font-size: 15px;">

                  <td style="color: dodgerblue;"><b>{{$team->name}}</b></td>
                  <td style="color: dodgerblue;"><b>To</b></td>
                </tr>

                </tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="card-body">

          <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 hideOnPrint">
              <form action="/dashboard/search-from-finish-number" method="POST">
                @csrf
                <input type="hidden" name="team_id" value="{{$team->id}}">

                <label for="">تیاری نمبر ها</label>
                <select name="finish_number" id="finish_number" class="form-control" onchange="this.form.submit()">
                  @foreach($finish_numbers as $w)
                    <option {{($w->finish_number == $finish_number ? 'selected' : '')}} value="{{$w->finish_number}}">{{$w->finish_number}}</option>
                  @endforeach
                </select>

              </form>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 hideOnPrint"></div>
            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 hideOnPrint">
              <div class="btn btn-primary btn-sm hideOnPrint pull-left" onclick="printPage('finish-number-list')"
                   style="float: left;"><i class="fa fa-print"></i> Print
              </div>
            </div>
          </div>

          <div class="table-responsive">
            <hr style="height: 3px;width: 100%;color: #0b97c4;background-color: #0b97c4">
            <table class="table table-xs table-hover table-bordered">
              <thead>

              <tr>

                <td><b>SN</b></td>
                <td><b>شماره قالین</b></td>
                <td><b>طول</b></td>
                <td><b>عرض</b></td>
                <td><b>مساحت</b></td>

                <td><b>نوعیت</b></td>
                <td><b>نرخ</b></td>
                <td><b>قیتان</b></td>
                <td><b>نرخ</b></td>
                <td><b>لبکی</b></td>
                <td><b>نرخ</b></td>
                <td><b>پوپک</b></td>
                <td><b>نرخ</b></td>
                <td><b>روفو</b></td>
                <td><b>نرخ</b></td>
                <td><b>چیت</b></td>
                <td><b>نرخ</b></td>
                <td><b>رنگ</b></td>
                <td><b>نرخ</b></td>
                <td><b>کش</b></td>
                  <td><b>نرخ</b></td>
                                <td><b>شیرازه</b></td>
                                <td><b>نرخ</b></td>
                                <td><b>کنترول کیفیت</b></td>
                                <td><b>نرخ</b></td>


              </tr>
              </thead>

              <tbody>
              @php($total_amount = 0)

              <?php $n = 1 ?>
              @php($total_area = 0)
              @php($total_qaitan = 0)
              @php($total_rofo = 0)
              @php($total_cheet = 0)
              @php($total_labaki = 0)
              @php($total_popak = 0)
              @php($total_kash = 0)
              @php($total_rang = 0)
                @php($total_shiraza = 0)
                @php($total_quality = 0)

              @forelse ($finishing_works as $finish)

                  <?php

                  $qaitan = \App\FinishingWork::where('carpetId', $finish->carpetId)->where('team_id', $team->id)->where('finish_number', $finish_number)->where('category_id', 1)->get();
                  $rofo = \App\FinishingWork::where('carpetId', $finish->carpetId)->where('team_id', $team->id)->where('finish_number', $finish_number)->where('category_id', 2)->get();
                  $cheet = \App\FinishingWork::where('carpetId', $finish->carpetId)->where('team_id', $team->id)->where('finish_number', $finish_number)->where('category_id', 3)->get();
                  $labaki = \App\FinishingWork::where('carpetId', $finish->carpetId)->where('team_id', $team->id)->where('finish_number', $finish_number)->where('category_id', 4)->get();
                  $popak = \App\FinishingWork::where('carpetId', $finish->carpetId)->where('team_id', $team->id)->where('finish_number', $finish_number)->where('category_id', 5)->get();
                  $kash = \App\FinishingWork::where('carpetId', $finish->carpetId)->where('team_id', $team->id)->where('finish_number', $finish_number)->where('category_id', 6)->get();
                  $rang = \App\FinishingWork::where('carpetId', $finish->carpetId)->where('team_id', $team->id)->where('finish_number', $finish_number)->where('category_id', 7)->get();
                  $shiraza = \App\FinishingWork::where('carpetId', $finish->carpetId)->where('team_id', $team->id)->where('finish_number', $finish_number)->where('category_id', 8)->get();
                  $quality = \App\FinishingWork::where('carpetId', $finish->carpetId)->where('team_id', $team->id)->where('finish_number', $finish_number)->where('category_id', 9)->get();

                  ?>
                  <span style="display: none">
                  {{--{{    $total = \App\CarpetWash::where('carpetId',$finish->carpetId)->sum('area')}}--}}
                    {{--@if($total)--}}
                    {{--{{$total_area = $total_area + $total}}--}}
                    {{--@else--}}
                    {{--{{$total_area = $total_area +  \App\Carpet::where('carpet_id',$finish->carpetId)->sum('area')}}--}}
                    {{--@endif--}}
                    @if($finish->carpet->carpet_wash)
                      {{$total_area += $finish->carpet->carpet_wash->area}}
                     @else
                          {{$total_area += $finish->carpet->area}}
                        @endif
                </span>
                  <tr style="direction:ltr;">
                    <td>{{$n}}</td>
                    <td>{{$finish->carpet->carpet_no}}</td>
                    @if($finish->carpet->carpet_wash)
                      <td>{{$finish->carpet->carpet_wash->height}} m<sup>2</sup></td>
                    @else
                      <td>{{$finish->carpet->height}} m<sup>2</sup></td>
                    @endif
                    @if($finish->carpet->carpet_wash)
                      <td>{{$finish->carpet->carpet_wash->width}} m<sup>2</sup></td>
                    @else
                      <td>{{$finish->carpet->width}} m<sup>2</sup></td>
                    @endif
                    @if($finish->carpet->carpet_wash)
                      <td>{{$finish->carpet->carpet_wash->area}} m<sup>2</sup></td>
                    @else
                      <td>{{$finish->carpet->area}} m<sup>2</sup></td>
                    @endif

                    <td>{{$finish->carpet->type->carpet_type}}</td>
                    @if($qaitan)
                      <td>
                        @foreach($qaitan as $qai)
                          @if($qai->carpet->carpet_wash)
                                  <span style="display: none">{{$total_qaitan += $qai->carpet->carpet_wash->area}}</span>
                          {{round($qai->price_af / $qai->carpet->carpet_wash->area , 2)}} |
                          @else
                                  <span style="display: none">  {{$total_qaitan += $qai->carpet->area}}</span>
                            {{round($qai->price_af / $qai->carpet->area,2)}} |
                          @endif

                        @endforeach
                      </td>
                      <td>
                        @foreach($qaitan as $qai)
                          <span style="display: none">
                          {{$total_amount += $qai->price_af}}


                           </span>
                          {{$qai->price_af}} |
                        @endforeach
                      </td>
                    @else
                      <td></td>
                    @endif
                    @if($labaki)
                      <td>
                        @foreach($labaki as $la)
                          @if($la->carpet->carpet_wash)
                                  <span style="display: none">{{$total_labaki += $la->carpet->carpet_wash->height * 2}}</span>
                                  {{round($la->price_af / $la->carpet->carpet_wash->height / 2 , 2)}} |
                          @else
                                  <span style="display: none">{{$total_labaki += $la->carpet->height * 2}}</span>
                                  {{round($la->price_af / $la->carpet->height / 2 , 2)}} |
                          @endif
                        @endforeach
                      </td>
                      <td>
                        @foreach($labaki as $la)
                          <span style="display: none">
                          {{$total_amount += $la->price_af}}

                           </span>
                          {{$la->price_af}} |
                        @endforeach
                      </td>
                    @else
                      <td></td>
                    @endif
                    @if($popak)
                      <td>
                        @foreach($popak as $po)
                          @if($po->carpet->carpet_wash)
                                  <span style="display: none">{{$total_popak += $po->carpet->carpet_wash->area }}</span>
                            {{round($po->price_af / $po->carpet->carpet_wash->area , 2)}} |
                          @else
                                  <span style="display: none">{{$total_popak += $po->carpet->area }}</span>
                            {{round($po->price_af / $po->carpet->area , 2)}} |
                          @endif
                        @endforeach
                      </td>
                      <td>
                        @foreach($popak as $po)
                          <span style="display: none">

                          {{$total_amount += $po->price_af}}
                           </span>
                          {{$po->price_af}} |
                        @endforeach
                      </td>
                    @else
                      <td></td>
                    @endif
                    @if($rofo)
                      <td>
                        @foreach($rofo as $ro)
                          @if($ro->carpet->carpet_wash)
                                  <span style="display: none">{{$total_rofo += $ro->carpet->carpet_wash->area }}</span>
                            {{round($ro->price_af , 2 )}} |
                          @else
                                  <span style="display: none">{{$total_rofo += $ro->carpet->area }}</span>
                            {{round($ro->price_af , 2)}} |
                          @endif
                        @endforeach
                      </td>
                      <td>
                        @foreach($rofo as $ro)
                          <span style="display: none">
                          {{$total_amount += $ro->price_af}}
                           </span>
                          {{$ro->price_af}} |
                        @endforeach
                      </td>
                    @else
                      <td></td>
                    @endif
                    @if($cheet)
                      <td>
                        @foreach($cheet as $ch)
                          @if($ch->carpet->carpet_wash)
                                  <span style="display: none">{{$total_cheet += $ch->carpet->carpet_wash->area }}</span>
                            {{round($ch->price_af / $ch->carpet->carpet_wash->area , 2)}} |
                          @else
                                  <span style="display: none">{{$total_cheet += $ch->carpet->area }}</span>
                            {{round($ch->price_af / $ch->carpet->area , 2)}} |
                          @endif
                        @endforeach
                      </td>
                      <td>
                        @foreach($cheet as $ch)
                          <span style="display: none">

                          {{$total_amount += $ch->price_af}}
                           </span>
                          {{$ch->price_af}} |
                        @endforeach
                      </td>
                    @else
                      <td></td>
                    @endif
                    @if($rang)
                      <td>
                        @foreach($rang as $ra)
                          @if($ra->carpet->carpet_wash)
                                  <span style="display: none">{{$total_rang += $ra->carpet->carpet_wash->area }}</span>
                                  {{round($ra->price_af / $ra->carpet->carpet_wash->area , 2)}} |
                          @else
                                  <span style="display: none">{{$total_rang += $ra->carpet->area }}</span>
                            {{round($ra->price_af / $ra->carpet->area , 2)}} |
                          @endif
                        @endforeach
                      </td>
                      <td>
                        @foreach($rang as $ra)
                          <span style="display: none">

                          {{$total_amount += $ra->price_af}}

                           </span>
                          {{$ra->price_af}} |
                        @endforeach
                      </td>
                    @else
                      <td></td>
                    @endif
                    @if($kash)
                      <td>
                        @foreach($kash as $ka)
                          @if($ka->carpet->carpet_wash)
                                  <span style="display: none">{{$total_kash += $ka->carpet->carpet_wash->area }}</span>
                            {{round($ka->price_af / $ka->carpet->carpet_wash->area , 2)}} |
                          @else
                                  <span style="display: none">{{$total_kash += $ka->carpet->area }}</span>
                            {{round($ka->price_af / $ka->carpet->area , 2)}} |
                          @endif
                        @endforeach
                      </td>
                      <td>
                        @foreach($kash as $ka)
                          <span style="display: none">

                          {{$total_amount += $ka->price_af}}

                           </span>
                          {{$ka->price_af}} |
                        @endforeach
                      </td>
                    @else
                      <td></td>
                    @endif
                    
                    
                     @if($shiraza)
                                        <td>
                                            @foreach($shiraza as $sh)
                                                @if($sh->carpet->carpet_wash)
                                                    <span
                                                        style="display: none">{{$total_shiraza += $sh->carpet->carpet_wash->height * 2 }}</span>
                                                    {{round($sh->price_af / $sh->carpet->carpet_wash->height / 2 , 2)}} |
                                                @else
                                                    <span
                                                        style="display: none">{{$total_shiraza += $sh->carpet->height * 2 }}</span>
                                                    {{round($sh->price_af / $sh->carpet->height / 2 , 2)}} |
                                                @endif
                                            @endforeach
                                        </td>
                                        <td>
                                            @foreach($shiraza as $sh)
                                                <span style="display: none">

                          {{$total_amount += $sh->price_af}}

                           </span>
                                                {{$sh->price_af}} |
                                            @endforeach
                                        </td>
                                    @else
                                        <td></td>
                                        <td></td>
                                    @endif

                                     @if($quality)
                                        <td>
                                            @foreach($quality as $qa)
                                                @if($qa->carpet->carpet_wash)
                                                    <span style="display: none">{{$total_quality += $qa->carpet->carpet_wash->area }}</span>
                                                    {{round($qa->price_af / $qa->carpet->carpet_wash->area , 2)}} |
                                                @else
                                                    <span style="display: none">{{$total_quality += $qa->carpet->area }}</span>
                                                    {{round($qa->price_af / $qa->carpet->area , 2)}} |
                                                @endif
                                            @endforeach
                                        </td>
                                        <td>
                                            @foreach($quality as $qa)
                                                <span style="display: none">
                                                    {{$total_amount += $qa->price_af}}
                                                </span>
                                                {{$qa->price_af}} |
                                            @endforeach
                                        </td>
                                    @else
                                        <td></td>
                                        <td></td>
                                    @endif

                  </tr>
                  <?php $n++ ?>
              @empty
                <h4 class="text-info text-center">هنوز موردی ثبت نشده است</h4>
              @endforelse
              <tr>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td>جمله متراژ قیتان</td>
                  <td>{{$total_qaitan}}</td>
                  <td>جمله متراژ لبکی</td>
                  <td>{{$total_labaki}}</td>
                  <td>جمله متراژ پوپک </td>
                  <td>{{$total_popak}}</td>
                  <td>جمله متراژ روفو</td>
                  <td>{{$total_rofo}}</td>
                  <td>جمله متراژ چیت</td>
                  <td>{{$total_cheet}}</td>
                  <td>جمله متراژ رنگ</td>
                  <td>{{$total_rang}}</td>
                  <td>جمله متراژ کش</td>
                  <td>{{$total_kash}}</td>
                  
                   <td>جمله متراژ شیرازه</td>
                                <td>{{$total_shiraza}}</td>
                   <td>جمله متراژ کنترول کیفیت</td>
                                <td>{{$total_quality}}</td>


              </tr>


              </tbody>
            </table>
            <hr style="height: 3px;width: 100%;color: #0b97c4;background-color: #0b97c4">

          </div>

          <div class="row">
            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-4">
              <table style="direction: ltr" class="table table-sm">
                <tbody>
                <tr style="direction: ltr">
                  <td style="font-size: 12px;color: #0b97c4">QUANTITY</td>
                  <td style="direction: ltr">&nbsp;{{$finishing_works->count()}} Pcs</td>
                </tr>


                <tr style="direction: ltr">
                  <td style="font-size: 12px;color: #0b97c4">TOTAL AMOUNT</td>
                  <td style="direction: ltr">&nbsp;{{$total_amount}} $</td>
                </tr>

                <tr style="direction: ltr">
                  <td style="font-size: 12px;color: #0b97c4">TOTAL AREA</td>
                  <td style="direction: ltr">&nbsp;{{$total_area}} m <sup>2</sup></td>
                </tr>
                </tbody>
              </table>
            </div>
            <div class="col-lg-9 col-md-9 col-sm-9 col-xs-8">

            </div>
          </div>
          <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
              <h5>به تعداد &nbsp; {{$finishing_works->count()}} &nbsp; تخته قالین را اینجانب که شهرتم در فوق ذکر است
                تسلیم شرکت نمودم</h5>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>


@endsection
@section('scripts')

  <script>
      $('#finish_number').select2();
  </script>
@endsection
