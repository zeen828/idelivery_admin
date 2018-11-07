<meta name="csrf-token" content="{{ csrf_token() }}">

<style>
    .dataTables_wrapper .dt-buttons {
        float:right;
    }
    .buttons-excel {
        background-color: #55acee;
        color: white;
        border-color: rgba(0,0,0,0.2);
        border-radius: 3px;
        box-shadow: none;
        border: 1px solid transparent;
        display: inline-block;
        padding: 5px 10px;
        font-size: 12px;
        line-height: 1.5;
    }
    .buttons-excel:hover {
        background-color: #2795e9;
        border-color: rgba(0,0,0,0.2);
    }
</style>

<table class="table table-hover" id="campaign_type_tab">
    <thead>
    <tr>
        <th>折扣類型</th>
        <th>折扣金額</th>
        <th>折扣類型占比%</th>
    </tr>
    </thead>
    @if (!empty($rows))
        <tbody style="text-align: center">
            @foreach($rows as $val)
                <tr>
                    <td>{{ empty($val->setting_title) ? '合計' : $val->setting_title }}</td>
                    <td>{{ empty($val->deduct_price) ? 0 : $val->deduct_price }}</td>
                    <td>{{ empty($val->percent) ? '0%' : $val->percent.'%' }}</td>
                </tr>
            @endforeach
        </tbody>
    @endif
    @if (!empty($sum))
        <tfoot>
        @foreach($sum as $val)
            <tr>
                <th style="text-align: center">合計：</th>
                <th style="text-align: center">{{ empty($val->deduct_price) ? 0 : $val->deduct_price }}</th>
                <th style="text-align: center">{{ empty($val->percent) ? '0%' : $val->percent.'%' }}</th>
            </tr>
        @endforeach
        </tfoot>
    @endif

</table>

<script src='https://cdn.datatables.net/1.10.10/js/jquery.dataTables.min.js'></script>
<script src='https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js'></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js'></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js'></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js'></script>
<script src='https://cdn.datatables.net/buttons/1.5.2/js/buttons.html5.min.js'></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">

<script>
    $( document ).ready(function() {
        $('#campaign_type_tab').dataTable({
            "lengthChange": false,
            "searching": false,
            "ordering": true,
            "autoWidth": true,
            "info": false,
            "filter": false,
            "paging": false,
            "language": {
                "decimal":        "",
                "emptyTable":     "查無資料顯示",
                "info":           "顯示 _START_ 到 _END_ 筆 / 共 _TOTAL_ 筆",
                "infoEmpty":      "顯示 0 到 0 筆 / 共 0 筆",
                "infoFiltered":   "(從 _MAX_ 總筆數中過濾)",
                "infoPostFix":    "",
                "thousands":      ",",
                "lengthMenu":     "顯示 _MENU_ 筆",
                "loadingRecords": "載入中...",
                "processing":     "處理中...",
                "search":         "搜尋:",
                "zeroRecords":    "查無符合資料",
                "paginate": {
                    "first":      "第一頁",
                    "last":       "最末頁",
                    "next":       "下一頁",
                    "previous":   "前一頁"
                },
                "aria": {
                    "sortAscending":  ": 遞增排序",
                    "sortDescending": ": 遞減排序"
                }
            },
            "dom": 'Bfrtip',
            "buttons": [
                {
                    extend: 'excelHtml5',
                    footer: true,
                    text: "<i class='fa fa-download'></i> 匯出",
                    title: "折扣類型統計表"
                }
            ]
        });
    });
</script>