@extends('dsh.master')

@section('content')
<style>
    /* Premium Dashboard Profile CSS */
    .content-wrapper {
        background: #f4f7f6;
        min-height: 100vh;
        padding-bottom: 50px;
    }
    .profile-banner {
        position: relative;
        width: 100%;
        height: 280px;
        background: linear-gradient(135deg, #0f2027 0%, #203a43 50%, #2c5364 100%);
        overflow: hidden;
    }
    /* Abstract decorative shapes for the banner */
    .profile-banner::before {
        content: '';
        position: absolute;
        top: -50%; left: -10%;
        width: 600px; height: 600px;
        background: radial-gradient(circle, rgba(255,255,255,0.08) 0%, rgba(255,255,255,0) 70%);
        border-radius: 50%;
    }
    .profile-banner::after {
        content: '';
        position: absolute;
        bottom: -30%; right: -5%;
        width: 400px; height: 400px;
        background: radial-gradient(circle, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0) 70%);
        border-radius: 50%;
    }
    
    .profile-main-card {
        background: #ffffff;
        border: none;
        border-radius: 20px;
        box-shadow: 0 15px 35px rgba(0,0,0,0.06);
        margin-top: -100px; /* Pull into the banner */
        position: relative;
        z-index: 10;
        padding-bottom: 25px;
    }
    .avatar-wrapper-overlap {
        width: 130px; height: 130px;
        border-radius: 50%;
        border: 5px solid #ffffff;
        box-shadow: 0 8px 20px rgba(0,0,0,0.12);
        margin: -65px auto 20px; /* Overlap effect */
        background: #fff;
        position: relative;
        z-index: 20;
    }
    .user-initials-avatar {
        width: 100%; height: 100%;
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        border-radius: 50%;
        color: white; font-size: 2.8rem; font-weight: 800;
        display: flex; align-items: center; justify-content: center;
        text-shadow: 2px 2px 5px rgba(0,0,0,0.15);
    }
    .user-name-title {
        font-size: 1.8rem; font-weight: 800; color: #1e293b; margin-bottom: 5px;
    }
    .user-email-subtitle {
        font-size: 1rem; color: #64748b; font-weight: 500; margin-bottom: 20px;
    }
    .role-badge {
        background: rgba(79, 172, 254, 0.1);
        color: #0284c7;
        padding: 8px 24px;
        border-radius: 30px;
        font-size: 0.95rem;
        font-weight: 700;
        border: 1px solid rgba(79, 172, 254, 0.25);
        display: inline-block;
    }

    /* Cards */
    .premium-card {
        border-radius: 16px;
        border: none;
        box-shadow: 0 5px 20px rgba(0,0,0,0.04);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        background: #ffffff;
    }
    .premium-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 30px rgba(0,0,0,0.08);
    }
    .card-title-custom {
        font-size: 1.15rem; font-weight: 800; color: #334155;
        border-bottom: 2px solid #f1f5f9; padding-bottom: 12px; margin-bottom: 20px;
    }
    .icon-box {
        width: 40px; height: 40px;
        border-radius: 12px; display: flex; align-items: center; justify-content: center;
        font-size: 1.2rem; margin-left: 15px;
    }
    .bg-gradient-success { background: linear-gradient(135deg, #10b981, #047857); color: white; }
    .bg-gradient-warning { background: linear-gradient(135deg, #f59e0b, #b45309); color: white; }
    
    /* Timeline */
    .activity-timeline {
        position: relative; padding-left: 30px; list-style: none;
    }
    .activity-timeline::before {
        content: ''; position: absolute; top: 0; left: 10px; height: 100%; width: 2px; background: #e2e8f0;
    }
    .timeline-item {
        position: relative; margin-bottom: 25px;
    }
    .timeline-item::before {
        content: ''; position: absolute; left: -26px; top: 5px; width: 14px; height: 14px;
        border-radius: 50%; background: #4facfe; border: 3px solid #fff; box-shadow: 0 0 0 2px #e2e8f0;
    }
    .timeline-date {
        font-size: 0.85rem; color: #94a3b8; font-weight: 600; margin-bottom: 4px;
    }
    .timeline-content {
        background: #f8fafc; padding: 12px 18px; border-radius: 10px; font-size: 0.95rem; color: #475569; border: 1px solid #f1f5f9;
    }
    
    /* Permissions */
    .permission-badge {
        background: #f8fafc; color: #475569; border: 1px solid #e2e8f0;
        padding: 8px 14px; border-radius: 10px; font-size: 0.85rem; font-weight: 600;
        margin: 4px; display: inline-block; transition: all 0.2s;
    }
    .permission-badge:hover {
        background: #4facfe; color: white; border-color: #4facfe;
        transform: translateY(-2px); box-shadow: 0 4px 10px rgba(79, 172, 254, 0.3);
    }
</style>

<div class="content-wrapper" id="main-content-wrapper">
    <!-- Premium Banner -->
    <div class="profile-banner"></div>
    
    <div class="container-fluid" id="main-content-fluid">
        <!-- Main Overlapping Card -->
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card premium-card profile-main-card">
                    <div class="card-body text-center">
                        <div class="avatar-wrapper-overlap">
                            @php
                                $initials = mb_substr(trim($user->name), 0, 2);
                            @endphp
                            <div class="user-initials-avatar">
                                {{ mb_strtoupper($initials) }}
                            </div>
                        </div>
                        <h2 class="user-name-title">{{ $user->name }}</h2>
                        <p class="user-email-subtitle"><i class="fas fa-envelope mr-1"></i> {{ $user->email }}</p>
                        <div>
                            <span class="role-badge">
                                <i class="fas fa-user-shield mr-1"></i> 
                                {{ $user->roles->pluck('name')->implode(', ') ?: 'کاربر عادی' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Info & Activities Section -->
        <div class="row justify-content-center mt-4">
            
            <!-- Left Column: User Info & Permissions -->
            <div class="col-lg-4 col-md-12 mb-4">
                
                <!-- Account Info -->
                <div class="card premium-card mb-4">
                    <div class="card-body" dir="rtl">
                        <h4 class="card-title-custom text-right"><i class="fas fa-info-circle text-primary ml-2"></i> اطلاعات حساب</h4>
                        
                        <div class="d-flex align-items-center mb-3 text-right" style="justify-content: flex-start;">
                            <div class="icon-box bg-gradient-success text-white ml-3">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 text-muted" style="font-size: 0.85rem;">تاریخ ثبت نام</h6>
                                <strong class="text-dark">{{ \Carbon\Carbon::parse($user->created_at)->format('Y-m-d') }}</strong>
                            </div>
                        </div>

                        <div class="d-flex align-items-center mb-3 text-right" style="justify-content: flex-start;">
                            <div class="icon-box bg-gradient-warning text-white ml-3">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 text-muted" style="font-size: 0.85rem;">آخرین بروزرسانی</h6>
                                <strong class="text-dark">{{ \Carbon\Carbon::parse($user->updated_at)->locale('fa')->diffForHumans() }}</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Permissions -->
                <div class="card premium-card">
                    <div class="card-body" dir="rtl">
                        <h4 class="card-title-custom text-right"><i class="fas fa-key text-warning ml-2"></i> دسترسی‌ها (Permissions)</h4>
                        <div class="text-right">
                            @if(count($permissions) > 0)
                                @php
                                    $visiblePermissions = array_slice($permissions, 0, 20);
                                    $hiddenPermissions = array_slice($permissions, 20);
                                @endphp
                                
                                @foreach($visiblePermissions as $perm)
                                    <span class="permission-badge">{{ $perm }}</span>
                                @endforeach
                                
                                @if(count($hiddenPermissions) > 0)
                                    <div id="more-permissions" style="display: none;">
                                        @foreach($hiddenPermissions as $perm)
                                            <span class="permission-badge">{{ $perm }}</span>
                                        @endforeach
                                    </div>
                                    <div class="text-center mt-3">
                                        <button type="button" class="btn btn-sm btn-outline-primary" id="toggle-permissions-btn" onclick="togglePermissions()" style="border-radius: 20px; padding: 6px 20px; font-weight: 600;">
                                            مشاهده بیشتر (Show More) <i class="fas fa-chevron-down"></i>
                                        </button>
                                    </div>
                                @endif
                            @else
                                <div class="alert alert-light border text-center" style="border-radius: 12px; font-weight: 600; color: #64748b;">
                                    شما هیچ دسترسی خاصی ندارید.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

            </div>

            <!-- Right Column: Recent Activity -->
            <div class="col-lg-6 col-md-12">
                <div class="card premium-card h-100">
                    <div class="card-body" dir="rtl">
                        <h4 class="card-title-custom text-right"><i class="fas fa-history text-info ml-2"></i> فعالیت‌های اخیر (Recent Activities)</h4>
                        
                        @if(count($activities) > 0)
                            <ul class="activity-timeline mt-4 text-right" style="direction: ltr;">
                                @foreach($activities as $activity)
                                    <li class="timeline-item" style="direction: rtl; text-align: right;">
                                        <div class="timeline-date">{{ \Carbon\Carbon::parse($activity->created_at ?? $activity->date)->format('Y-m-d H:i') }}</div>
                                        <div class="timeline-content">
                                            {{ $activity->description }}
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="text-center mt-5">
                                <i class="fas fa-inbox fa-3x mb-3" style="color: #cbd5e1;"></i>
                                <p style="color: #64748b; font-weight: 500; font-size: 1.1rem;">هیچ فعالیتی یافت نشد.</p>
                            </div>
                        @endif

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function togglePermissions() {
    var moreDiv = document.getElementById('more-permissions');
    var btn = document.getElementById('toggle-permissions-btn');
    if(moreDiv.style.display === 'none') {
        moreDiv.style.display = 'inline';
        btn.innerHTML = 'مشاهده کمتر (Show Less) <i class="fas fa-chevron-up"></i>';
    } else {
        moreDiv.style.display = 'none';
        btn.innerHTML = 'مشاهده بیشتر (Show More) <i class="fas fa-chevron-down"></i>';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    var wrapper = document.getElementById('main-content-wrapper');
    
    // Smoothly stretch the background to the edges of AdminLTE padding
    if(wrapper && wrapper.parentElement) {
        var parentStyle = window.getComputedStyle(wrapper.parentElement);
        var paddingLeft = parentStyle.paddingLeft || '0px';
        var paddingRight = parentStyle.paddingRight || '0px';
        var paddingTop = parentStyle.paddingTop || '0px';
        
        wrapper.style.marginLeft = '-' + paddingLeft;
        wrapper.style.marginRight = '-' + paddingRight;
        wrapper.style.marginTop = '-' + paddingTop;
        
        // Add padding back to the inner container fluid so cards stay aligned safely
        var fluid = document.getElementById('main-content-fluid');
        if(fluid) {
            fluid.style.paddingLeft = paddingLeft;
            fluid.style.paddingRight = paddingRight;
        }
    }
});
</script>
@endsection
