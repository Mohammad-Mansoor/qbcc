@extends('dsh.master')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">پروفایل کاربری (User Profile)</h1>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <!-- Profile Image -->
                    <div class="card card-primary card-outline">
                        <div class="card-body box-profile text-center">
                            <div class="text-center mb-4">
                                <img class="profile-user-img img-fluid img-circle"
                                     src="{{ asset('images/avatar5.png') }}"
                                     alt="User profile picture" style="width: 100px; height: 100px;">
                            </div>

                            <h3 class="profile-username text-center">{{ $user->name }}</h3>

                            <p class="text-muted text-center">{{ $user->email }}</p>

                            <ul class="list-group list-group-unbordered mb-3 text-right" dir="rtl">
                                <li class="list-group-item">
                                    <b>نقش کاربری (Role):</b> <a class="float-left text-primary">{{ $user->roles->pluck('name')->implode(', ') ?: 'بدون نقش' }}</a>
                                </li>
                                <li class="list-group-item">
                                    <b>تاریخ ثبت نام (Joined):</b> <a class="float-left text-primary">{{ \Carbon\Carbon::parse($user->created_at)->format('Y-m-d') }}</a>
                                </li>
                            </ul>

                            <p class="text-muted mt-4">
                                شما با موفقیت وارد سیستم شده‌اید. برای دسترسی به بخش‌های مختلف از منوی سمت راست استفاده کنید.
                                <br>
                                (You have successfully logged in. Please use the sidebar menu to access your authorized modules.)
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
