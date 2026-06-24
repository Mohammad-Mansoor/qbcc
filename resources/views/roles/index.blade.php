@extends('dsh.master')

@section('content')
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            @if(session('status'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('status') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>مدیریت نقش ها و دسترسی ها</h5>
                    <a href="{{ route('roles.create') }}" class="btn btn-sm btn-info"><i class="fa fa-plus"></i> ایجاد نقش جدید</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover text-center">
                            <thead class="bg-primary text-white">
                                <tr>
                                    <th>شماره</th>
                                    <th>نام نقش</th>
                                    <th>عملیات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($roles as $key => $role)
                                    <tr>
                                        <td>{{ ++$key }}</td>
                                        <td>{{ $role->name }}</td>
                                        <td>
                                            <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-sm btn-warning" title="ویرایش"><i class="fa fa-edit"></i></a>
                                            @if($role->name !== 'Super Admin')
                                            <button class="btn btn-sm btn-danger delete-role" data-id="{{ $role->id }}" title="حذف"><i class="fa fa-trash"></i></button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{ $roles->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('.delete-role').on('click', function() {
            var id = $(this).data('id');
            var btn = $(this);
            Swal.fire({
                title: 'آیا مطمیین هستید؟',
                text: "شما نمیتوانید این نقش را برگردانید!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'بلی، حذف شود!',
                cancelButtonText: 'نخیر'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/dashboard/roles/' + id,
                        type: 'DELETE',
                        data: {
                            "_token": "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            if(response.status == 'success') {
                                Swal.fire('حذف شد!', 'نقش موفقانه حذف گردید.', 'success');
                                btn.closest('tr').fadeOut();
                            } else {
                                Swal.fire('خطا!', response.message, 'error');
                            }
                        }
                    });
                }
            });
        });
    });
</script>
@endsection
