@extends('dsh.master')
@section('title', 'مدیریت نمبرهای مسلسل تولید')
@section('content')

    <style>
        /* Premium Glassmorphism Theme */
        .glass-card {
            background: white;
            border: 1px solid var(--QBIC-border, #e2e8f0);
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.02);
            margin-bottom: 30px;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .glass-header {
            background: #f8fafc;
            padding: 20px 25px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .glass-header h4 {
            margin: 0;
            font-weight: 700;
            color: #1e293b;
            font-size: 1.2rem;
        }

        .nav-tabs-premium {
            border-bottom: 2px solid #e2e8f0;
            margin-bottom: 25px;
            display: flex;
            gap: 10px;
        }

        .nav-tabs-premium .nav-link-premium {
            padding: 12px 24px;
            font-weight: 600;
            color: #64748b;
            border: none;
            border-bottom: 3px solid transparent;
            background: none;
            transition: all 0.2s;
            text-decoration: none;
        }

        .nav-tabs-premium .nav-link-premium:hover {
            color: #0f172a;
        }

        .nav-tabs-premium .nav-link-premium.active {
            color: #3b82f6;
            border-bottom-color: #3b82f6;
        }

        .badge-status-open {
            background-color: #dcfce7;
            color: #15803d;
            padding: 6px 12px;
            border-radius: 9999px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .badge-status-closed {
            background-color: #fee2e2;
            color: #b91c1c;
            padding: 6px 12px;
            border-radius: 9999px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .btn-premium {
            border-radius: 8px;
            font-weight: 600;
            padding: 10px 20px;
            transition: all 0.2s;
        }
    </style>

    <div class="row">
        <div class="col-lg-12">
            <!-- Navigation Tabs -->
            <div class="nav-tabs-premium">
                <a href="/dashboard/batches/kachaee" class="nav-link-premium {{ $type == 'kachaee' ? 'active' : '' }}">
                    <i class="fa fa-wrench mr-1"></i> نمبرهای کچایی (Kachaee)
                </a>
                <a href="/dashboard/batches/wash" class="nav-link-premium {{ $type == 'wash' ? 'active' : '' }}">
                    <i class="fa fa-tint mr-1"></i> نمبرهای شست (Washing)
                </a>
                <a href="/dashboard/batches/finish" class="nav-link-premium {{ $type == 'finish' ? 'active' : '' }}">
                    <i class="fa fa-scissors mr-1"></i> نمبرهای تیاری (Tayaari)
                </a>
            </div>

            <div class="glass-card">
                <div class="glass-header flex-wrap" style="gap: 15px;">
                    <h4>
                        <i class="fa fa-list text-primary mr-2"></i>
                        لیست نمبرهای مسلسل برای {{ $title }}
                    </h4>

                    @php
                        $statsTeams = $stats->pluck('team_name')->filter();
                        $batchTeams = $batches->pluck('team_name')->filter();
                        $uniqueTeams = $statsTeams->concat($batchTeams)->unique()->values();
                    @endphp

                    <div class="d-flex align-items-center flex-wrap" style="gap: 15px;">
                        <!-- Search by ID -->
                        <div class="input-group m-0" style="width: 230px;">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-white border-right-0"
                                    style="border-radius: 8px 0 0 8px; border-color: #e2e8f0;">
                                    <i class="feather icon-search text-muted"></i>
                                </span>
                            </div>
                            <input type="text" id="batchSearchInput" class="form-control border-left-0 pl-0"
                                placeholder="جستجو نمبر مسلسل..."
                                style="border-radius: 0 8px 8px 0; border-color: #e2e8f0; box-shadow: none;">
                        </div>

                        <!-- Filter by Team -->
                        <div class="input-group m-0" style="width: 230px;">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-white border-right-0"
                                    style="border-radius: 8px 0 0 8px; border-color: #e2e8f0;">
                                    <i class="feather icon-users text-muted"></i>
                                </span>
                            </div>
                            <select id="teamFilter" class="form-control border-left-0 pl-0 custom-select"
                                style="border-radius: 0 8px 8px 0; border-color: #e2e8f0; box-shadow: none;">
                                <option value="">همه تیم‌ها (All Teams)</option>
                                @foreach($uniqueTeams as $teamName)
                                    <option value="{{ $teamName }}">{{ $teamName }}</option>
                                @endforeach
                            </select>
                        </div>

                        @php
                            $createPermission = 'create_' . ($type == 'wash' ? 'washing' : ($type == 'finish' ? 'finishing' : 'kachaee')) . '_batch';
                            $managePermission = 'manage_' . ($type == 'wash' ? 'washing' : ($type == 'finish' ? 'finishing' : 'kachaee')) . '_batch_status';
                        @endphp

                        @can($createPermission)
                            <button type="button" class="btn btn-success btn-premium shadow-sm m-0" data-toggle="modal" data-target="#createBatchModal">
                                <i class="fa fa-plus mr-1"></i> ایجاد نمبر جدید (Generate Next)
                            </button>
                        @endcan
                    </div>
                </div>

                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped align-middle">
                            <thead>
                                <tr class="text-muted small uppercase">
                                    <th class="font-weight-bold">#</th>
                                    <th class="font-weight-bold">نمبر مسلسل (Batch Reference)</th>
                                    <th class="font-weight-bold">تیم/کارمند (Team)</th>
                                    <th class="font-weight-bold text-center">تعداد قالین (Carpets)</th>
                                    <th class="font-weight-bold text-center">مساحت کل (Total Area)</th>
                                    <th class="font-weight-bold text-center">حالت (Status)</th>
                                    <th class="font-weight-bold">تاریخ ایجاد (Created At)</th>
                                    <th class="font-weight-bold text-center">عملیات (Actions)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($batches as $index => $batch)
                                    @php
                                        $batchStats = $stats->get($batch->reference_number);
                                        $totalCarpets = $batchStats ? $batchStats->total_carpets : 0;
                                        $totalArea = $batchStats ? $batchStats->total_area : 0.0;
                                        $teamName = $batch->team_name ?: ($batchStats ? $batchStats->team_name : '');
                                    @endphp
                                    <tr class="batch-row" data-ref="{{ strtolower($batch->reference_number) }}"
                                        data-team="{{ strtolower($teamName) }}">
                                        <td>{{ $index + 1 }}</td>
                                        <td class="font-weight-bold text-primary"
                                            style="font-size: 1.1rem; letter-spacing: 0.5px; direction: ltr; text-align: right;">
                                            {{ $batch->reference_number }}
                                        </td>
                                        <td class="font-weight-bold text-secondary">
                                            @if($teamName)
                                                <i class="feather icon-user mr-1"></i> {{ $teamName }}
                                            @else
                                                <span class="text-muted" style="opacity: 0.5;">اختصاص داده نشده</span>
                                            @endif
                                        </td>
                                        <td class="text-center font-weight-bold text-dark">
                                            <span class="badge badge-light border px-3 py-2 rounded-pill">{{ $totalCarpets }}
                                                قالین</span>
                                        </td>
                                        <td class="text-center font-weight-bold text-success" style="direction: ltr;">
                                            {{ number_format($totalArea, 2) }} m²
                                        </td>
                                        <td class="text-center">
                                            @if($batch->status == 'open')
                                                <span class="badge-status-open">
                                                    <i class="fa fa-unlock-alt mr-1"></i> باز (Open)
                                                </span>
                                            @else
                                                <span class="badge-status-closed">
                                                    <i class="fa fa-lock mr-1"></i> بسته (Closed)
                                                </span>
                                            @endif
                                        </td>
                                        <td>{{ $batch->created_at->format('Y-m-d H:i') }}</td>
                                        <td class="text-center">
                                            <div class="d-flex align-items-center justify-content-center"
                                                style="gap: 8px; flex-wrap: nowrap;">
                                                <a href="/dashboard/batches/{{ $batch->id }}/details"
                                                    class="btn btn-sm btn-outline-primary rounded-lg font-weight-bold d-inline-flex align-items-center"
                                                    style="padding: 6px 12px; gap: 4px;">
                                                    <i class="fa fa-info-circle"></i> جزئیات (Details)
                                                </a>
                                                @can($managePermission)
                                                    <form action="/dashboard/batches/{{ $batch->id }}/toggle-status" method="post"
                                                        class="m-0 d-inline-block">
                                                        @csrf
                                                        @if($batch->status == 'open')
                                                            <button type="submit"
                                                                class="btn btn-sm btn-outline-danger rounded-lg font-weight-bold d-inline-flex align-items-center"
                                                                style="padding: 6px 12px; gap: 4px;">
                                                                <i class="fa fa-lock"></i> غیرفعال (Close)
                                                            </button>
                                                        @else
                                                            <button type="submit"
                                                                class="btn btn-sm btn-outline-success rounded-lg font-weight-bold d-inline-flex align-items-center"
                                                                style="padding: 6px 12px; gap: 4px;">
                                                                <i class="fa fa-unlock-alt"></i> فعال (Open)
                                                            </button>
                                                        @endif
                                                    </form>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">
                                            <i class="fa fa-folder-open-o fa-2x mb-2 d-block"></i>
                                            هیچ نمبری برای این مرحله ایجاد نشده است.
                                        </td>
                                    </tr>
                                @endforelse
                                <tr id="noResultsRow" style="display: none;">
                                    <td colspan="8" class="text-center text-muted py-5">
                                        <i class="feather icon-search fa-2x mb-2 d-block" style="opacity: 0.3;"></i>
                                        موردی با این مشخصات یافت نشد!
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Batch Modal -->
    <div class="modal fade" id="createBatchModal" tabindex="-1" role="dialog" aria-labelledby="createBatchModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);">
                <div class="modal-header" style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; border-radius: 12px 12px 0 0;">
                    <h5 class="modal-title font-weight-bold text-dark" id="createBatchModalLabel">
                        <i class="fa fa-plus-circle text-success mr-2"></i>ایجاد نمبر جدید برای {{ $title }}
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="padding: 1rem;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="/dashboard/batches/{{ $type }}" method="post">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="form-group mb-4">
                            <label class="font-weight-bold text-secondary mb-2">نمبر مسلسل (Batch Reference)</label>
                            <input type="text" class="form-control" value="{{ $nextNumber }}" readonly style="background-color: #f1f5f9; font-size: 1.1rem; letter-spacing: 0.5px; direction: ltr; font-weight: bold;">
                        </div>
                        <div class="form-group mb-2">
                            <label class="font-weight-bold text-secondary mb-2">انتخاب تیم (Select Team) <span class="text-danger">*</span></label>
                            <select name="team_id" class="form-control custom-select" required>
                                <option value="" disabled selected>لطفاً یک تیم را انتخاب کنید</option>
                                @foreach($teams as $team)
                                    <option value="{{ $team->id }}">{{ $team->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer bg-light" style="border-top: 1px solid #e2e8f0; border-radius: 0 0 12px 12px;">
                        <button type="button" class="btn btn-secondary font-weight-bold" data-dismiss="modal">انصراف (Cancel)</button>
                        <button type="submit" class="btn btn-success font-weight-bold"><i class="fa fa-check mr-1"></i> ایجاد (Create)</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            function filterBatches() {
                var searchTerm = $('#batchSearchInput').val().toLowerCase().trim();
                var teamTerm = $('#teamFilter').val().toLowerCase().trim();
                var visibleCount = 0;

                $('.batch-row').each(function () {
                    var ref = $(this).data('ref') || '';
                    var team = $(this).data('team') || '';

                    var matchSearch = ref.includes(searchTerm);
                    var matchTeam = teamTerm === '' || team === teamTerm;

                    if (matchSearch && matchTeam) {
                        $(this).show();
                        visibleCount++;
                    } else {
                        $(this).hide();
                    }
                });

                if (visibleCount === 0 && $('.batch-row').length > 0) {
                    $('#noResultsRow').show();
                } else {
                    $('#noResultsRow').hide();
                }
            }

            $('#batchSearchInput').on('input', filterBatches);
            $('#teamFilter').on('change', filterBatches);
        });
    </script>
@endsection