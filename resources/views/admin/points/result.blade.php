<meta name="csrf-token" content="{{ csrf_token() }}">

<table class="table">
    <thead>
    <tr>
        <th>日期</th>
        <th>店家</th>
        <th>點數類型</th>
        <th>點數</th>
        <th>說明</th>
        <th>狀態</th>
    </tr>
    </thead>
    <tbody>
    @if( ! empty($lists))
        @foreach($lists as $val)
            <tr>
                <td>{{ empty($val->created_at) ? "" : $val->created_at }}</td>
                <td>{{ $val->store_id }}</td>
                <td>{{ empty($val->point_type) ? "" : $val->point_type }}</td>
                <td>{{ $val->point }}</td>
                <td>{{ $val->description }}</td>
                <td>{{ ['1'=>'加點', '2'=>'扣點', '3'=>'逾期'][$val->status] }}</td>
                <td><span class="label label-{{ ['1'=>'success', '2'=>'danger', '3'=>'warning'][$val->status->status] }}">{{ ['1'=>'加點', '2'=>'扣點', '3'=>'逾期'][$val->status] }}</span></td>

            </tr>
        @endforeach
    @endif
    </tbody>
</table>

<div class="col-sm-5">
    <div style="margin-top: 20px;">
        @if( ! empty($lists))
            從 {{ $lists->firstItem() }} 到 {{ $lists->lastItem() }} ，總共 {{ $lists->total() }} 筆
        @endif
    </div>
</div>
<div class="col-sm-7">
    <div style="float: right;">
        @if (!empty($lists))
            {{ $lists->links() }}
        @endif
    </div>
</div>