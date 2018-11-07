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

<form action="/admin/posts" method="get" class="search-form" pjax-container>
    <div class="input-group input-group-sm ">
        <select name="" class="form-control">
            <option value="">-- 總店切換 --</option>
            @foreach ($user_company as $company)
                <option value="{{ $company['company_id'] }}">{{ $company['title'] }}</option>
            @endforeach
        </select>
    </div>
</form>