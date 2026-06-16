@extends('dsh.master')
@section('title' , 'Agents Management - QBCC Forensic ERP')

@section('content')
<style>
    /* QBCC PREMIUM DESIGN SYSTEM */
    :root {
        --qbcc-primary: #1e3a8a;
        --qbcc-secondary: #3b82f6;
        --qbcc-header-bg: #ffffff;
        --qbcc-border: #e2e8f0;
        --qbcc-gradient: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
    }

    .page-header-modern {
        background: var(--qbcc-header-bg);
        border-bottom: 1px solid var(--qbcc-border);
        margin-bottom: 25px;
        padding: 20px 30px;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }

    .glass-card {
        background: white;
        border: 1px solid var(--qbcc-border);
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    /* COMPACT PREMIUM MODAL DESIGN */
    .qbcc-modal-content {
        background: white !important;
        border-radius: 20px !important;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1) !important;
        overflow: hidden;
    }

    .modal-header {
        background: var(--qbcc-gradient);
        padding: 18px 30px;
        border: none;
    }

    .modal-title { color: #ffffff; font-weight: 800; font-size: 18px; }
    .modal-header .close { color: #ffffff; opacity: 0.9; font-size: 24px; }

    .form-section {
        background: #f8fafc;
        padding: 8px 15px;
        border-radius: 8px;
        font-weight: 800;
        color: var(--qbcc-primary);
        font-size: 12px;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 8px;
        border-right: 4px solid var(--qbcc-secondary);
    }

    .input-group-modern {
        margin-bottom: 12px;
    }

    .input-group-modern label {
        display: block;
        font-weight: 600;
        color: #475569;
        font-size: 12px;
        margin-bottom: 5px;
    }

    .form-control-modern {
        width: 100%;
        height: 40px;
        padding: 8px 14px;
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        font-size: 13px;
        transition: all 0.2s ease;
        color: #1e293b;
    }

    .form-control-modern:focus {
        border-color: var(--qbcc-secondary);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        outline: none;
    }

    /* COMPACT AVATAR SYSTEM */
    .avatar-upload {
        position: relative;
        max-width: 90px;
        margin: 0 auto 20px auto;
    }

    .avatar-preview {
        width: 90px; height: 90px;
        position: relative;
        border-radius: 100%;
        border: 3px solid #f1f5f9;
        overflow: hidden;
    }

    .avatar-preview > img { width: 100%; height: 100%; object-fit: cover; }

    .avatar-edit { position: absolute; right: 2px; bottom: 2px; z-index: 1; }
    .avatar-edit label {
        width: 28px; height: 28px;
        border-radius: 100%;
        background: var(--qbcc-secondary);
        border: 2px solid #ffffff;
        cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        color: white; font-size: 11px;
    }

    .btn-qbcc-save {
        background: var(--qbcc-primary);
        color: white;
        border-radius: 8px;
        padding: 12px 40px;
        font-weight: 700;
        border: none;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        transition: all 0.2s;
    }

    /* TABLE CONTROLS - REFINED ALIGNMENT */
    .table-controls { 
        display: flex; 
        justify-content: space-between; 
        align-items: center; 
        margin-bottom: 25px; 
        padding: 0 5px; 
    }
    
    .controls-right { 
        display: flex; 
        align-items: center; 
        gap: 15px; 
    }
    
    .controls-left { 
        display: flex; 
        align-items: center; 
        gap: 10px; 
    }

    .btn-filter { font-weight: 700; font-size: 12px; padding: 8px 16px; border-radius: 8px; }

    /* TABLE STYLING - PRESERVED */
    .table-modern { width: 100%; border-collapse: separate; border-spacing: 0 10px; }
    .table-modern thead th { background: #f8fafc; padding: 15px; font-weight: 700; color: #64748b; font-size: 12px; border: none; }
    .table-modern tbody tr { background: white; box-shadow: 0 2px 4px rgba(0,0,0,0.02); }
    .table-modern td { padding: 15px; vertical-align: middle; border: none; }
    .table-modern td:first-child { border-radius: 12px 0 0 12px; }
    .table-modern td:last-child { border-radius: 0 12px 12px 0; }

    .agent-identity { display: flex; align-items: center; gap: 15px; }
    .agent-avatar { width: 40px; height: 40px; border-radius: 10px; object-fit: cover; border: 2px solid #f1f5f9; }
    .agent-info .name { font-weight: 800; color: #1e293b; font-size: 14px; }
    .agent-info .email { color: #64748b; font-size: 11px; display: block; margin-top: 1px; }

    .btn-action-round { width: 32px; height: 32px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; margin: 0 2px; border: none; cursor: pointer; }
    .btn-action-round:hover { transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
</style>

<div class="row">
    <div class="col-sm-12">
        <div class="page-header-modern d-flex justify-content-between align-items-center">
            <div>
                <h3 class="mb-1 text-dark font-weight-bold">مدیریت حسابات نمایندگان</h3>
                <p class="text-muted mb-0 small"><i class="feather icon-shield text-success mr-1"></i> تفتیش مالی QBCC</p>
            </div>
            <button class="btn btn-primary rounded-lg px-4 font-weight-bold shadow-sm" data-toggle="modal" data-target="#agentModal" style="height: 45px;">
                <i class="feather icon-plus mr-1"></i> ثبت نماینده جدید
            </button>
        </div>

        <div class="card glass-card">
            <div class="card-body">
                @if(session("status"))
                    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                        {{session('status')}}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                @endif

                <div class="table-controls hideOnPrint">
                    <!-- RIGHT SIDE -->
                    <div class="controls-right">
                        <form action="/dashboard/agents/search" method="GET" style="width: 320px; margin-left: 15px;">
                            <div class="input-group shadow-sm" style="border-radius: 10px; overflow: hidden; border: 1px solid #cbd5e1; background: white; transition: all 0.3s ease;" onmouseover="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 3px rgba(59, 130, 246, 0.1)';" onmouseout="this.style.borderColor='#cbd5e1'; this.style.boxShadow='0 .125rem .25rem rgba(0,0,0,.075)';">
                                <input type="text" name="search" required value="{{ $search ?? '' }}" placeholder="جستجو (نام، تذکره، شماره)..." class="form-control border-0 shadow-none px-3" style="height: 42px; font-size: 13px; font-weight: 600; background: transparent;">
                                <div class="input-group-append">
                                    <button class="btn btn-primary m-0 px-4 d-flex align-items-center justify-content-center" type="submit" style="height: 42px; border-radius: 10px 0 0 10px; font-weight: 700; gap: 8px;">
                                        <i class="feather icon-search"></i> جستجو
                                    </button>
                                </div>
                            </div>
                        </form>
                        <div class="d-flex gap-2">
                            <a href="/dashboard/agents" class="btn btn-light-primary btn-filter">فعال</a>
                            <a href="/dashboard/agent-deactive" class="btn btn-light-warning btn-filter">غیر فعال</a>
                            <a href="/dashboard/agent-accounts" class="btn btn-light-info btn-filter">همه نمایندگان</a>
                        </div>
                    </div>

                    <!-- LEFT SIDE -->
                    <div class="controls-left">
                        <button class="btn btn-white btn-sm border-light shadow-sm px-3" onclick="printStandardized('agents')">
                            <i class="fa fa-print text-primary mr-1"></i> چاپ
                        </button>
                        <div id="exportButton" style="display: inline-block;"></div>
                    </div>
                </div>

                <div class="table-responsive" id="agents">
                    <table class="table-modern" id="agent_list">
                        <thead>
                            <tr>
                                <th>کد</th>
                                <th>مشخصات نماینده</th>
                                <th>نوع قرارداد</th>
                                <th>تماس</th>
                                <th class="text-center">باقیات (USD)</th>
                                <th class="text-center">باقیات (AFN)</th>
                                <th class="hideOnPrint text-center">مدیریت</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php($total_usd = 0)
                            @php($total_afn = 0)
                            @foreach($data as $d)
                                @php($bal_usd = $d->netBalanceUsd())
                                @php($bal_af = $d->netBalanceAfn())
                                @php($total_usd += $bal_usd)
                                @php($total_afn += $bal_af)
                                <tr>
                                    <td class="font-weight-bold text-primary">{{ $d->account_no }}</td>
                                    <td>
                                        <div class="agent-identity">
                                            <img src="{{ $d->image ? '/'.$d->image : 'https://ui-avatars.com/api/?name='.$d->user->name.'&background=1e3a8a&color=fff' }}" class="agent-avatar">
                                            <div class="agent-info">
                                                <span class="name">{{ $d->user->name .' '.$d->user->last_name }}</span>
                                                <span class="email">{{ $d->user->email }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($d->contract_type == 'contractional') <span class="badge badge-light-success">قراردادی</span>
                                        @elseif($d->contract_type == 'carpet seller') <span class="badge badge-light-primary">فروشنده قالین</span>
                                        @else <span class="badge badge-light-warning">وزنی</span> @endif
                                    </td>
                                    <td dir="ltr" class="small text-muted">{{ $d->phone->count() > 0 ? $d->phone[0]->phone_no : '---' }}</td>
                                    <td dir="ltr" class="text-center font-weight-bold {{ $bal_usd >= 0 ? 'text-success' : 'text-danger' }}">{{ number_format($bal_usd, 2) }}</td>
                                    <td dir="ltr" class="text-center font-weight-bold {{ $bal_af >= 0 ? 'text-success' : 'text-danger' }}">{{ number_format($bal_af, 0) }}</td>
                                    <td class="hideOnPrint text-center">
                                        <div class="d-flex justify-content-center">
                                            <a href="/dashboard/agents/{{ $d->agent_id }}/edit" class="btn-action-round bg-light-info text-info" title="ویرایش"><i class="feather icon-edit-2"></i></a>
                                            <a href="/dashboard/agents/{{ $d->agent_id }}" class="btn-action-round bg-light-warning text-warning" title="جزییات"><i class="feather icon-user"></i></a>
                                            <a href="/dashboard/agent-carpet/{{$d->agent_id}}" class="btn-action-round bg-light-primary text-primary" title="قالین ها"><i class="feather icon-package"></i></a>
                                            <a href="/dashboard/agent-payments/{{$d->agent_id}}" class="btn-action-round bg-light-success text-success" data-toggle="tooltip" title="ثبت رسید و پرداخت (Account)"><i class="feather icon-credit-card"></i></a>
                                            <a href="{{ route('accounting.reports.agent_statement', ['agent_id' => $d->agent_id]) }}" class="btn-action-round bg-light-info text-info" data-toggle="tooltip" title="صورت حساب مالی (Statement)"><i class="feather icon-file-text"></i></a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL -->
<div class="modal fade" id="agentModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content qbcc-modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ $agent ? 'ویرایش پروفایل' : 'ثبت نماینده جدید' }}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body p-4">
                <form action="{{ $agent ? '/dashboard/agents/'.$agent->agent_id : '/dashboard/agents' }}" method="post" enctype="multipart/form-data">
                    @csrf
                    @if($agent) {{ method_field('patch') }} @endif
                    
                    <input type="hidden" value="{{ $AccountNo }}" name="account_no">
                    <input type="hidden" name="role" value="AO">

                    <div class="avatar-upload">
                        <div class="avatar-edit">
                            <input type='file' name="image" id="imageUpload" accept=".png, .jpg, .jpeg" style="display: none;" />
                            <label for="imageUpload"><i class="feather icon-camera"></i></label>
                        </div>
                        <div class="avatar-preview">
                            <img id="imagePreview" src="{{ ($agent && $agent->image) ? '/'.$agent->image : 'https://ui-avatars.com/api/?name=QBCC&background=cbd5e1&color=fff' }}">
                        </div>
                    </div>

                    <div class="form-section"><i class="feather icon-user"></i> ۱. هویت و شناسایی</div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="input-group-modern">
                                <label>نام</label>
                                <input type="text" name="name" value="{{ $agent ? $agent->user->name : old('name') }}" class="form-control-modern" required>
                                @error('name') <small class="text-danger">{{ __('message.'.$message) }}</small> @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group-modern">
                                <label>تخلص</label>
                                <input type="text" name="last_name" value="{{ $agent ? $agent->user->last_name : old('last_name') }}" class="form-control-modern" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group-modern">
                                <label>نام پدر</label>
                                <input type="text" name="agent_father_name" value="{{ $agent ? $agent->agent_father_name : old('agent_father_name') }}" class="form-control-modern">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="input-group-modern">
                                <label>نمبر تذکره</label>
                                <input type="text" name="national_id" value="{{ $agent ? $agent->national_id : old('national_id') }}" class="form-control-modern">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group-modern">
                                <label>شماره تماس</label>
                                <input type="text" name="phone_no" value="{{ $agent && $agent->phone->count() > 0 ? $agent->phone[0]->phone_no : old('phone_no') }}" class="form-control-modern" dir="ltr">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group-modern">
                                <label>کد حساب</label>
                                <input type="text" value="{{ $agent ? $agent->account_no : $AccountNo }}" class="form-control-modern bg-light font-weight-bold text-primary" disabled>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="input-group-modern">
                                <label>آدرس دقیق</label>
                                <input type="text" name="agent_address" value="{{ $agent ? $agent->agent_address : old('agent_address') }}" class="form-control-modern">
                            </div>
                        </div>
                    </div>

                    <div class="form-section mt-3"><i class="feather icon-file-text"></i> ۲. قرارداد و مالی</div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="input-group-modern">
                                <label>ولایت</label>
                                <select name="province_id" class="form-control-modern">
                                    @foreach($province as $p)
                                        <option value="{{ $p->province_id }}" {{ ($agent && $agent->province_id == $p->province_id) ? 'selected' : '' }}>{{ $p->province }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group-modern">
                                <label>نوع قرارداد</label>
                                <select name="contract_type" class="form-control-modern">
                                    <option value="contractional">قراردادی</option>
                                    <option value="weight">وزنی</option>
                                    <option value="carpet seller">فروشنده</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group-modern">
                                <label>ارز پایه</label>
                                <select name="account_type" class="form-control-modern">
                                    @foreach($currencies as $curr)
                                        <option value="{{ strtolower($curr->name) }}" {{ ($agent && strtolower($agent->account_type) == strtolower($curr->name)) ? 'selected' : '' }}>{{ $curr->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group-modern">
                                <label>تاریخ قرارداد</label>
                                <input type="date" name="contract_date" value="{{ date('Y-m-d') }}" class="form-control-modern">
                            </div>
                        </div>
                    </div>

                    @if(!$agent)
                    <div class="form-section mt-3"><i class="feather icon-lock"></i> ۳. امنیت</div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="input-group-modern">
                                <label>ایمیل</label>
                                <input type="email" name="email" class="form-control-modern" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group-modern">
                                <label>رمز عبور</label>
                                <input type="password" name="password" class="form-control-modern" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group-modern">
                                <label>تایید رمز</label>
                                <input type="password" name="confirm" class="form-control-modern" required>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary rounded-pill px-5 py-2 font-weight-bold shadow-sm">تایید و ثبت نهایی</button>
                        <button type="button" class="btn btn-link text-muted ml-3" data-dismiss="modal">انصراف</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('footer-plugins')
<script>
    function printStandardized(sectionID) {
        var page = document.body.innerHTML;
        var sectionContent = document.getElementById(sectionID).cloneNode(true);
        $(sectionContent).find('.print-header').removeClass('d-none');
        document.getElementById("SC").innerHTML = sectionContent.innerHTML;
        var contain = document.getElementById("PC").innerHTML;
        document.body.innerHTML = contain;
        window.print();
        document.body.innerHTML = page;
        window.location.reload();
    }

    $(document).ready(function () {
        @if($errors->any() || $agent) $('#agentModal').modal('show'); @endif

        $("#imageUpload").change(function() {
            if (this.files && this.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) { $('#imagePreview').attr('src', e.target.result); }
                reader.readAsDataURL(this.files[0]);
            }
        });

        $("#agent_list").tableExport({
            formats: ["xlsx"],
            filename: "QBCC-Agents",
            bootstrap: true,
            exportButtons: true,
            RTL: true
        });
        
        var $excelBtn = $('#agent_list').find('caption').children().detach();
        // CHANGED TO btn-success FOR WHITE TEXT AND ICON
        $excelBtn.addClass('btn btn-success btn-sm rounded-lg shadow-sm px-3').html('<i class="fa fa-file-excel-o mr-1"></i> Excel');
        $excelBtn.appendTo('#exportButton');

        window.setTimeout(function () { $(".alert").fadeTo(500, 0).slideUp(500); }, 4000);
    });
</script>
@endsection