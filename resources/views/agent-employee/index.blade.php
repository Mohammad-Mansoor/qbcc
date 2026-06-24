@extends('dsh.master')

@section('content')
    <!-- navbar -->
    <div class="row" id="agent-employee">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="card-header">
                    <div class="alert alert-success" style="display:none;" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <p class="text-center">کارگر موفقانه حذف شد</p>
                    </div>

                    @if(session("status"))
                        <div class="alert alert-success status"  style="display:none;" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                        aria-hidden="true">&times;</span></button>
                            <p class="text-center">{{session('status')}}</p>
                        </div>

                    @endif
                    @if(session("error"))

                        <div class="alert alert-success status" style="display:none;" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                        aria-hidden="true">&times;</span></button>
                            <p class="text-center">{{session('error')}}</p>
                        </div>

                    @endif
                    <h4 class="text-center">لیست کارگرها</h4>
                    <div class="row">
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 hideOnPrint">
                            @can('create_agent_employee')
                            <a href="/dashboard/agent-employees/create" class="btn btn-primary pull-right hideOnPrint"><i
                                        class="fa fa-plus"></i>&nbsp;ثبت نام کارگر</a>
                            @endcan
                        </div>
                        <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9"></div>
                    </div>
                   
                </div>
                <div class="card-body">
                    <div class="row">
    
                        <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9"></div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 hideOnPrint">
                            <div class="btn-group hideOnPrint" id="exportButton" style="float: left; ">
                                <div class="btn btn-sm btn-primary" style="float: left" onclick="printPage('agent-employee')"><i
                                            class="fa fa-print"></i> چاپ
                                </div>
    
                            </div>
                        </div>
                    </div>
                    
                 
                    
                    <div class="static-table-list" style="margin-top: 60px">
                        <table class="table text-center" id="agent_employee">
                            <thead>
                            <tr >
                                <th>ای دی</th>
                                <th>نام</th>
                                <th>تخلص</th>
                                <th>نام پدر</th>
                                <th>نماینده مربوطه</th>
                                @can('edit_agent_employee')<th class="hideOnPrint">ویرایش</th>@endcan


                            </tr>
                            </thead>
                            <tbody>
                            @foreach($employees as $employee)
                                <tr class="ur{{ $employee->id }}">
                                    <td>{{$employee->id}}</td>
                                    <td>{{$employee->first_name}}</td>
                                    <td>{{$employee->last_name}}</td>
                                    <td>{{$employee->father_name}}</td>
                                    <td>{{$employee->agent->user->name}}</td>
                                    @can('edit_agent_employee')
                                    <td class="hideOnPrint"><a href="/dashboard/agent-employees/{{$employee->id}}/edit" class="btn btn-sm btn-info"><i
                                                    class="fa fa-pencil"></i>&nbsp; ویرایش</a></td>
                                    @endcan

                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        {{-- <p>{{$employees->onEachSide(1)->links()}}</p> --}}
                    </div>
                </div>
            </div>

        </div>
    </div>

@endsection

@section('scripts')



    <script>
    
    
        $(document).ready(function () {
            
            $("#agent_employee").tableExport({
                headers: true,                      // (Boolean), display table headers (th or td elements) in the <thead>, (default: true)
                footers: true,                      // (Boolean), display table footers (th or td elements) in the <tfoot>, (default: false)
                formats: ["xlsx"],                  // (String[]), filetype(s) for the export, (default: ['xlsx', 'csv', 'txt'])
                filename: "id",                     // (id, String), filename for the downloaded file, (default: 'id')
                bootstrap: true,                   // (Boolean), style buttons using bootstrap, (default: true)
                exportButtons: true,                // (Boolean), automatically generate the built-in export buttons for each of the specified formats (default: true)
                position: "bottom",                 // (top, bottom), position of the caption element relative to table, (default: 'bottom')
                ignoreRows: null,                   // (Number, Number[]), row indices to exclude from the exported file(s) (default: null)
                ignoreCols: 5,                   // (Number, Number[]), column indices to exclude from the exported file(s) (default: null)
                trimWhitespace: true,               // (Boolean), remove all leading/trailing newlines, spaces, and tabs from cell text in the exported file(s) (default: false)
                RTL: true,                         // (Boolean), set direction of the worksheet to right-to-left (default: false)
                sheetname: "id",
            
            });
            var $buttons = $('#agent_employee').find('caption').children().detach();
            // Append the buttons to an element of your choosing
            $buttons.appendTo('#exportButton');
        
        });
    </script>


    <script>
        $('.status').show();
        window.setTimeout(function () {
            $(".status").fadeTo(500, 0).slideUp(500, function () {

                $(this).remove();
            });
        }, 2000);
        function RemoveEmployee(id) {
            swal({
                text: "کارگر حذف شود؟",
                buttons: true,
                dangerMode: true,
                buttons: {
                    confirm: {text: 'بلی', className: 'btn-danger'},
                    cancel: 'نخیر'
                },
            })
                .then((willDelete) => {
                    if (willDelete) {
                        $.ajax({
                            method: 'DELETE',
                            data: {'_token': '{{ csrf_token() }}'},
                            url: '/dashboard/agent-employees/' + id,
                            success: function (data) {
                                $('.ur' + id).hide();
                                $('.alert').show();
                                window.setTimeout(function () {
                                    $(".alert").fadeTo(500, 0).slideUp(500, function () {

                                        $(this).remove();
                                    });
                                }, 2000);
                            }
                        })
                    }
                });
        }
    </script>
@endsection