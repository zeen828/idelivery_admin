<meta name="csrf-token" content="{{ csrf_token() }}">

<div class='row'>
    <div class='col-md-4'>姓名： {{ empty($header["name"]) ? "" : $header["name"] }}</div>
    <div class='col-md-4'>國碼： {{ empty($header["country"]) ? "" : $header["country"] }}</div>
    <div class='col-md-4'>帳號： {{ empty($header["account"]) ? "" : $header["account"] }}</div>
    <div class='col-md-4'>總點數： {{ empty($header["total_points"]) ? "" : $header["total_points"] }}</div>
    <div class='col-md-4'>點數最近逾期日期： {{ empty($header["last_expired_at"]) ? "" : $header["last_expired_at"] }}</div>
</div>