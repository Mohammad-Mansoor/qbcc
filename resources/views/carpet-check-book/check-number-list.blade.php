@extends('dsh.master')

@section('content')
  
  <div class="row" id="check-number-list">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <div class="card">
        <div class="card-body">
          
          <div class="row">
            <div class="col-sm-4">
              <div class="sparkline8-graph text-muted">
                
                <div class="table-responsive">
                  <table class="table table-sm table-hover text-right">
                    <thead>
                    
                    </thead>
                    <tbody>
                    
                    <tr>
                      <td style="color: dodgerblue;"><b>PURCHASE DETAILS</b></td>
                      <td style="color: dodgerblue;"><b></b></td>
                    
                    </tr>
                    <tr>
                      <td><b>{{$check_number}}</b></td>
                      <td style="direction: ltr">Bill #:</td>
                    </tr>
                    <tr>
                      <td><b>{{$check_date->date}}</b></td>
                      <td style="direction: ltr">Bill DATE:</td>
                    </tr>
                    
                    </tbody>
                  </table>
                </div>
              
              </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6"></div>
            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
              <h4 style="direction: ltr">TO : {{$agent->user->name}}</h4>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 hideOnPrint">
              <form action="/dashboard/check-book/search-check-number" method="post">
                @csrf
                <input type="hidden" name="agent_id" value="{{$agent->agent_id}}">
                
                <label for="">چیک نمبر ها</label>
                <select name="check_number" id="" class="form-control" onchange="this.form.submit()">
                  @foreach($check_numbers as $check)
                    <option {{($check->check_number == $check_number ? 'selected' : '')}} value="{{$check->check_number}}">{{$check->check_number}}</option>
                  @endforeach
                </select>
              
              </form>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 hideOnPrint">
              <form action="/dashboard/check-book/search-check-number" method="POST" id="dateSearch">
                @csrf
                <input type="hidden" name="agent_id" value="{{$agent->agent_id}}">
                <input type="hidden" name="check_number" value="{{$check_number}}">
                <div class="row">
                  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-21">
                    <span class="date-label">جستجو نمبر قالین</span><input type="text"
                                                                           value="{{ Request::old('search') }}"
                                                                           name="search" class="form-control"
                                                                           placeholder="چستجو نمبر قالین"
                                                                           required>
                  </div>
                </div>
              </form>
            
            </div>
            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 hideOnPrint">
              
              <div class="btn-group hideOnPrint" id="exportButton" style="float: left; ">
                <div class="btn btn-sm btn-primary" style="float: left" onclick="printPage('check-number-list')"><i
                          class="fa fa-print"></i> چاپ
                </div>
              
              </div>
            
            </div>
          </div>
          <div class="row">
            <div class="col-sm-12">
              
              <div class="sparkline8-graph text-muted">
                
                <div class="table-responsive">
                  <hr style="height: 3px;width: 100%;color: #0b97c4;background-color: #0b97c4">
                  <table class="table table-sm table-hover text-right" style="direction: ltr;" id="check_number_list">
                    <thead>
                    
                    <tr style="direction:rtl">
                      
                      <td><b>ITEM#</b></td>
                      <td><b>ITEM DESCRIPTION</b></td>
                      <td><b>HEIGHT</b></td>
                      <td><b>WIDTH</b></td>
                      <td><b>AREA</b></td>
                      <td><b>UNIT PRICE</b></td>
                      <td><b>TOTAL</b></td>
                      <td><b>EXPEN</b></td>
                      <td><b>TOTAL AMOUNT</b></td>
                    
                    </tr>
                    </thead>
                    
                    <tbody>
                    @php($grand_total = 0)
                    @forelse ($checkbooks as $checkbook)
                      <tr style="direction:ltr;">
                        <td>{{$checkbook->carpet->carpet_no}}</td>
                        <td>{{$checkbook->carpet->type->carpet_type}}</td>
                        <td style="direction: ltr">{{$checkbook->height}} m</td>
                        <td style="direction: ltr">{{$checkbook->width}} m</td>
                        <td style="direction: ltr">{{$checkbook->area}} m <sup>2</sup></td>
                        <td style="direction: ltr">{{$checkbook->carpet->price}} $</td>
                        <td style="direction: ltr">{{$checkbook->carpet->price * $checkbook->carpet->area}}
                          $
                        </td>
                        <td>{{$checkbook->kachaee_dollar_amount }}</td>
                        <td><span
                                  style="display: none">{{$grand_total += ($checkbook->carpet->price * $checkbook->carpet->area) - $checkbook->kachaee_dollar_amount }}}</span> {{($checkbook->carpet->price * $checkbook->carpet->area) - $checkbook->kachaee_dollar_amount }}
                          $
                        </td>
                      </tr>
                    @empty
                      <h4 class="text-info text-center">هنوز موردی ثبت نشده است</h4>
                    @endforelse
                    
                    
                    </tbody>
                  </table>
                  <hr style="height: 3px;width: 100%;color: #0b97c4;background-color: #0b97c4">
                
                </div>
              
              </div>
            </div>
          </div>
          <div class="row">
            
            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-4">
              <table style="direction: ltr" class="table table-sm">
                <tbody>
                <tr style="direction: ltr">
                  <td style="font-size: 12px;color: #0b97c4">QUANTITY</td>
                  <td>Pcs &nbsp;{{$checkbooks->count()}}</td>
                </tr>
                <tr style="direction: ltr">
                  <td style="font-size: 12px;color: #0b97c4">TOTAL</td>
                  <td> &nbsp;{{$checkbooks->sum('area')}} m <sup>2</sup></td>
                </tr>
                
                <tr style="direction: ltr">
                  <td style="font-size: 12px;color: #0b97c4">GRAND TOTAL</td>
                  <td>$ &nbsp;{{$grand_total}}</td>
                </tr>
                
                </tbody>
              </table>
            </div>
            <div class="col-lg-9 col-md-9 col-sm-9 col-xs-8">
            
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>


@endsection

@section('scripts')
  
  <script>
      $(document).ready(function () {
          $("#check_number_list").tableExport({
              headers: true,                      // (Boolean), display table headers (th or td elements) in the <thead>, (default: true)
              footers: true,                      // (Boolean), display table footers (th or td elements) in the <tfoot>, (default: false)
              formats: ["xlsx", "txt"],              // (String[]), filetype(s) for the export, (default: ['xlsx', 'csv', 'txt'])
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
          var $buttons = $('#check_number_list').find('caption').children().detach();
          // Append the buttons to an element of your choosing
          $buttons.appendTo('#exportButton');

      });
  
  
  </script>
@endsection