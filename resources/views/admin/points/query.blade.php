<form id="form1" method="GET" action="/admin/store/point/account/search">
    <input type="hidden" id="csrf_token" name="csrf_token" value="' . csrf_token() . '" />
    <div class="row">
        <div class="col-md-4">
            <div class="input-group">
                <span class="input-group-addon">國碼：</span>
                <input type="text" class="form-control" placeholder="請輸入手機國碼" aria-label="country"
                       aria-describedby="basic-addon1" id="country" name="country" value="886">
            </div>
        </div>
        <div class="col-md-8">
            <div class="input-group">
                <input type="text" class="form-control" placeholder="請輸入會員手機號碼" aria-label="account"
                       aria-describedby="basic-addon2" id="account" name="account">
                <span class="input-group-btn">
                    <button class="btn btn-primary" type="submit" id="search">查詢</button>
                </span>
            </div>
        </div>
    </div>
    <div class="row">
        <br/>
        <div class="col-md-12">
            <button class="btn btn-danger points" type="button">扣點</button>
            <button class="btn btn-success pull-right points" type="button">加點</button>
        </div>
    </div>


    <div class="modal modal-danger fade" id="modal-danger">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">扣點</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="rpoint" class="col-sm-2 control-label">點數</label>

                        <div class="col-sm-10">
                            <input type="number" class="form-control" id="rpoint" placeholder="點數">
                        </div>
                    </div>
                    <p>&nbsp;</p>
                    <div class="form-group">
                        <label for="rdesc" class="col-sm-2 control-label">說明</label>

                        <div class="col-sm-10">
                            <textarea rows="3" class="form-control" id="rdesc" placeholder="說明"></textarea>
                        </div>
                    </div>
                    <p>&nbsp;</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline pull-left" data-dismiss="modal">關閉</button>
                    <button type="button" class="btn btn-outline" id="reduce">扣點</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal modal-success fade" id="modal-success">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">加點</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="apoint" class="col-sm-2 control-label">點數</label>

                        <div class="col-sm-10">
                            <input type="number" class="form-control" id="apoint" placeholder="點數">
                        </div>
                    </div>
                    <p>&nbsp;</p>
                    <div class="form-group">
                        <label for="adesc" class="col-sm-2 control-label">說明</label>

                        <div class="col-sm-10">
                            <textarea rows="3" class="form-control" id="adesc" placeholder="說明"></textarea>
                        </div>
                    </div>
                    <p>&nbsp;</p>
                    <div class="form-group">
                        <label for="valid_date" class="col-sm-2 control-label">有效期限</label>
                        <div class="col-sm-10">
                            <select class="form-control" id="valid_date">
                                <option>三個月</option>
                                <option>半年</option>
                                <option>一年</option>
                                <option>一年半</option>
                                <option>二年</option>
                                <option>二年半</option>
                                <option>三年</option>
                            </select>
                        </div>
                    </div>
                    <p>&nbsp;</p>
                </div>
                <div class="modal-footer">
                    <p></p>
                    <button type="button" class="btn btn-outline pull-left" data-dismiss="modal">關閉</button>
                    <button type="button" class="btn btn-outline" id="add">加點</button>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" name="_token" value="{{ csrf_token() }}">
</form>

<script>
    $(function()
    {
        $(document)
            .ready(function () {
                $('#country').val(<?php echo request()->query('country', '');?>);
                $('#account').val(<?php echo request()->query('account', '');?>);
            });
    });
</script>