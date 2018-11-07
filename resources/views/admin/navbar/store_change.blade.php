<style>
    .search-form {
        width: 250px;
        margin: 10px 0 0 20px;
        border-radius: 3px;
        float: left;
    }
    .search-form select {
        color: #666;
        border: 0;
    }
</style>

<form action="/admin/system/change_config" method="get" class="search-form" pjax-container>
    <div class="input-group input-group-sm ">
        <select name="store_id" class="form-control">
            <option value="0-0">-- 店家切換 --</option>
            @foreach ($store_change as $store)
                <option value="{{ $store['id'] }}"{{ $store['selected'] }}>{{ $store['title'] }}</option>
            @endforeach
        </select>
        <span class="input-group-btn">
            <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-exchange"></i></button>
        </span>
    </div>
</form>