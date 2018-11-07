<meta name="csrf-token" content="{{ csrf_token() }}">
<table class="table table-striped">
    <thead>
        <tr>
            <th>兌換類型</th>
            <th>說明</th>
            <th>操作</th>
        </tr>
    </thead>
    <tbody>
        @foreach($rows as $row)
        <tr>
            <td>{{ $row['condition'] }}</td>
            <td>{{ $row['example'] }}</td>
            <td><a href="{{ $row['url'] }}">前往設定</a></td>
        </tr>
        @endforeach
    </tbody>
</table>
