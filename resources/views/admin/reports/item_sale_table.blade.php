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

<table class="table table-hover" id="item_sale_tab">
    <thead>
    <tr>
        <th>餐點類別</th>
        <th>餐點品項</th>
        <th>銷售數量</th>
        <th>銷售總額</th>
        <th>金額占比%</th>
        <th>標準成本</th>
        <th>預估毛利額</th>
    </tr>
    </thead>

    @if (!empty($rows))
        <tbody style="text-align: center">
            @foreach($rows as $val)
                <tr>
                    <td>{{ empty($val->group_name) ? '' : $val->group_name }}</td>
                    <td>{{ empty($val->item_name) ? '' : $val->item_name }}</td>
                    <td>{{ empty($val->qty) ? 0 : $val->qty }}</td>
                    <td>{{ empty($val->sub_price) ? 0 : $val->sub_price }}</td>
                    <td>{{ empty($val->percent) ? '0%' : $val->percent.'%' }}</td>
                    <td>{{ empty($val->costs) ? 0 : $val->costs }}</td>
                    <td>{{ empty($val->profits) ? 0 : $val->profits }}</td>
                </tr>
            @endforeach
        </tbody>
    @endif
    @if (!empty($sum))
        <tfoot>
        @foreach($sum as $val)
            <tr>
                <th style="text-align: center">合計：</th>
                <th style="text-align: center"></th>
                <th style="text-align: center">{{ empty($val->qty) ? 0 : $val->qty }}</th>
                <th style="text-align: center">{{ empty($val->sub_price) ? 0 : $val->sub_price }}</th>
                <th style="text-align: center">{{ empty($val->percent) ? '0%' : $val->percent.'%' }}</th>
                <th style="text-align: center">{{ empty($val->costs) ? 0 : $val->costs }}</th>
                <th style="text-align: center">{{ empty($val->profits) ? 0 : $val->profits }}</th>
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
        $('#item_sale_tab').dataTable({
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
                    title: "餐點品項銷售統計表"
                }
            ]
        });
    });
</script>
