function company_item_remove(id) {

    swal({
        title: '確認刪除?',
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#DD6B55',
        confirmButtonText: '確認',
        closeOnConfirm: false,
        cancelButtonText: '取消'
    },
        function () {
            $.ajax({
                method: 'delete',
                url: '/admin/company/set/menu_item/' + id,
                data: {
                    _method: 'delete',
                    _token: LA.token,
                },
                success: function (data) {
                    $.pjax.reload('#pjax-container');

                    if (typeof data === 'object') {
                        if (data.status) {
                            swal(data.message, '', 'success');
                        } else {
                            swal(data.message, '', 'error');
                        }
                    }
                }
            });
        });
}


function store_item_remove(id) {

    swal({
        title: '確認刪除?',
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#DD6B55',
        confirmButtonText: '確認',
        closeOnConfirm: false,
        cancelButtonText: '取消'
    },
        function () {
            $.ajax({
                method: 'delete',
                url: '/admin/store/set/menu_item/' + id,
                data: {
                    _method: 'delete',
                    _token: LA.token,
                },
                success: function (data) {
                    $.pjax.reload('#pjax-container');

                    if (typeof data === 'object') {
                        if (data.status) {
                            swal(data.message, '', 'success');
                        } else {
                            swal(data.message, '', 'error');
                        }
                    }
                }
            });
        });
}