<meta name="csrf-token" content="{{ csrf_token() }}">
<table class="table table-striped">
    <thead>
        <tr>
            <th>條件</th>
            <th>獎勵</th>
            <th>說明</th>
            <th>操作</th>
        </tr>
    </thead>
    <tbody>
        @foreach($rows as $row)
        <tr>
            <td>{{ $row['condition'] }}</td>
            <td>{{ $row['offer'] }}</td>
            <td>{{ $row['example'] }}</td>
            <td><a href="/admin/{{ $row['url'] }}">前往設定</a></td>
        </tr>
        @endforeach
    </tbody>
</table>

<!-- <div class="modal" id="edit_store_status_modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">請款狀態</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label style="margin-left: 20px">
                        <input type="radio" name="store_status" class="flat-red" value="0">
                        未請款
                    </label>
                    <label style="margin-left: 20px">
                        <input type="radio" name="store_status" class="flat-red" value="1">
                        已請款
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary pull-right" id="store_status_save">儲存</button>
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">取消</button>
            </div>
        </div>
    </div>
</div>-->

<script>
    // $(function() {
    //     $(document)
    //         .on('click', '.store_status_edit', function()
    //         {
    //             $('#edit_store_status_modal').modal('show');
    //             var rps_id = $(this).attr('data-value');
    //             var radio_val = $(this).attr('data-status');
    //             var $radios = $('input:radio[name=store_status]');
    //             $radios.filter('[value=' + radio_val + ']').iCheck('check');

    //             $(document).data('rps_id', rps_id);
    //         })

    //         .on('click', '#store_status_save', function () {
    //             var status = $('input[name=store_status]:checked').val();
    //             var rps_id = $(document).data('rps_id');

    //             $.ajaxSetup({
    //                 headers: {
    //                     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //                 }
    //             });

    //             $.ajax({
    //                 method : "PUT",
    //                 url: "/admin/bill/stores/set/"+rps_id+"/status",
    //                 data: { val : status }
    //             })
    //             .done(function(obj)
    //             {
    //                 toastr.success('設定成功');
    //                 $('#edit_store_status_modal').modal('hide');
    //                 location.reload();
    //             })
    //             .fail(function(obj){
    //                 toastr.warning('設定失敗');
    //                 $('#edit_store_status_modal').modal('hide');
    //                 location.reload();
    //             });
    //         });

    //     //Flat red color scheme for iCheck
    //     $('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
    //         checkboxClass: 'icheckbox_flat-green',
    //         radioClass   : 'iradio_flat-green'
    //     });

    // });
</script>