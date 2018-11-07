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

<table class="table">
    <thead>
    <tr>
        <th>付款方式</th>
        <th>銷售金額</th>
        <th>付款方式占比%</th>
        <th>付款金額</th>
        <th>付款方式占比%</th>
    </tr>
    </thead>
    <tbody>
    @if (!empty($rows))
        @foreach($rows as $val)
            <tr>
                <td>{{ empty($val->payment) ? '合計' : $val->payment }}</td>
                <td>{{ empty($val->src_amount) ? 0 : $val->src_amount }}</td>
                <td>{{ empty($val->src_percent) ? '0%' : $val->src_percent.'%' }}</td>
                <td>{{ empty($val->amount) ? 0 : $val->amount }}</td>
                <td>{{ empty($val->percent) ? '0%' : $val->percent.'%' }}</td>
            </tr>
        @endforeach
    @endif
    </tbody>
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
        $('#order_type_tab').dataTable({
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
                    title: "付款方式統計表"
                }
            ]
        });
    });
</script>