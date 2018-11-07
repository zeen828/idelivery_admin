<script>
    $('.form-history-back').on('click', function (event) {
        event.preventDefault();
        history.back(1);
    });

    $('.start_date').datetimepicker({"format":"YYYY-MM-DD 00:00:00","locale":"zh-TW"});
    $('.end_date').datetimepicker({"format":"YYYY-MM-DD 23:59:59","locale":"zh-TW"});

    $('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
      checkboxClass: 'icheckbox_flat-green',
      radioClass   : 'iradio_flat-green'
    });

    $('.point:not(.initialized)')
        .addClass('initialized')
        .bootstrapNumber({
            upClass: 'success',
            downClass: 'primary',
            center: true
    });

    $('.amount:not(.initialized)')
        .addClass('initialized')
        .bootstrapNumber({
            upClass: 'success',
            downClass: 'primary',
            center: true
    });

    $('.multiple1:not(.initialized)')
        .addClass('initialized')
        .bootstrapNumber({
            upClass: 'success',
            downClass: 'primary',
            center: true
    });

    $(".weekly").select2({
        allowClear: true,
        placeholder: "週優惠日"
    });

    // $(".daily").select2({
    //     allowClear: true,
    //     placeholder: "月優惠日"
    // });

    // $('.first.la_checkbox').bootstrapSwitch({
    //     size:'small',
    //     onText: '啟用',
    //     offText: '關閉',
    //     onColor: 'success',
    //     offColor: 'danger',
    //     onSwitchChange: function(event, state) {
    //         $(event.target).closest('.bootstrap-switch').next().val(state ? '1' : '0').change();
    //     }
    // });

    $('.status.la_checkbox').bootstrapSwitch({
        size:'small',
        onText: '啟用',
        offText: '關閉',
        onColor: 'success',
        offColor: 'danger',
        onSwitchChange: function(event, state) {
            $(event.target).closest('.bootstrap-switch').next().val(state ? '1' : '2').change();
        }
    });
</script>
<section class="content-header">
    <h1>
        促銷活動設定
        <small>Promotions Setting</small>
    </h1>
</section>
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title"><?php if ($action == 'POST') echo '創建'; else echo '編輯'; ?></h3>

                    <div class="box-tools">
                        <div class="btn-group pull-right" style="margin-right: 10px">
                            <a href="/admin/company/set/promotion" class="btn btn-sm btn-default"><i class="fa fa-list"></i>&nbsp;列表</a>
                        </div> 
                        <div class="btn-group pull-right" style="margin-right: 10px">
                            <a class="btn btn-sm btn-default form-history-back"><i class="fa fa-arrow-left"></i>&nbsp;返回</a>
                        </div>
                    </div>
                </div>
                
                <form action="<?php if ($action == 'POST') echo '/admin/company/set/promotion'; else echo '/admin/company/set/promotion/'.strval($promotion->id).'/edit'; ?>" method="POST" class="form-horizontal">
                        <div class="row">
                            <div class="col-md-2 control-label">
                                <label style='font-size: 16px;'><u>基本設定</u></label>
                            </div>
                        </div>
                        <br/>
                        <div class="row">
                            <div class="col-md-2 control-label">
                                <label>滿足條件</label>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group" style="margin-left:0px;margin-top:6px">
                                    <label>
                                        <input type="radio" name="cond" class="flat-red" value=1 <?php if (!empty($promotion) && !empty($promotion->amount)) echo 'checked'; else echo ''; ?>>
                                        滿金額&nbsp;&nbsp;&nbsp;&nbsp;
                                    </label>
                                    <label>
                                        <input type="radio" name="cond" class="flat-red" value=2 <?php if (!empty($promotion) && !empty($promotion->qty)) echo 'checked'; else echo ''; ?>>
                                        達數量
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-2 control-label">
                                <label>金額/數量</label>
                            </div>
                            <div class="col-md-2">
                                <div class="input-group">
                                    <input type="number" class="form-control amount" name="amount"  value=<?php if (!empty($promotion) && !empty($promotion->qty)) echo $promotion->qty; else if (!empty($promotion) && !empty($promotion->amount)) echo $promotion->amount; else echo 0; ?>></input>
                                </div>
                            </div>
                        </div>
                        <br/>
                        <div class="row">
                            <div class="col-md-2 control-label">
                                <label>獲得點數</label>
                            </div>
                            <div class="col-md-2">
                                <div class="input-group">
                                    <input type="number" class="form-control point" name="point" value=<?php if (!empty($promotion->point)) echo $promotion->point; else echo 0;?>></input>
                                </div>
                            </div>
                        </div>
                        <br/>
                        <div class="row">
                            <div class="col-md-2 control-label">
                                <label>有效期限</label>
                            </div>
                            <div class="col-md-3">
                                <select class="form-control expired" style="width: 100%;" name="expired" data-placeholder="選擇 有效期限" value="<?php  if (!empty($promotion) && !empty($promotion->expired)) echo $promotion->expired; else echo ''; ?>" >
                                    <option value="+3 months" >三個月</option>
                                    <option value="+6 months" >半年</option>
                                    <option value="+1 year" selected>一年</option>
                                    <option value="+18 months" >一年半</option>
                                    <option value="+2 years" >二年</option>
                                    <option value="+30 monthes" >二年半</option>
                                    <option value="+3 years" >三年</option>
                                </select>
                            </div>
                        </div>
                        <br/>
                        <div class="row">
                            <div class="col-md-2 control-label">
                                <label>狀態</label>
                            </div>
                            <div class="col-md-5">
                                <input type="checkbox" class="status la_checkbox" <?php if (!empty($promotion) && !empty($promotion->status) && $promotion->status == '1') echo 'checked'; else '';?> />
                                <input type="hidden" class="status" name="status" value="<?php if (!empty($promotion->status)) echo $promotion->status; else echo '2';?>"/>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-2 control-label">
                                <label style='font-size: 16px;'><u>活動優惠設定</u></label>
                            </div>
                        </div>
                        <br/>
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-2 control-label">
                                    <label>起始日期</label>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <div class='input-group date datetimepicker' style='margin-left:15px;'>
                                            <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                            <input type='text' class="form-control start_date" name="start_date" value="<?php if (!empty($promotion) && !empty($promotion->start_date)) echo $promotion->start_date; else echo ''; ?>"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2 control-label">
                                    <label>結束日期</label>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <div class='input-group date datetimepicker' style='margin-left:15px;'>
                                            <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                            <input type='text' class="form-control end_date" name="end_date" value="<?php if (!empty($promotion) && !empty($promotion->end_date)) echo $promotion->end_date; else echo ''; ?>"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                            <div class="col-md-2 control-label">
                                <label>週優惠日</label>
                            </div>
                            <div class="col-md-8">
                                <?php 
                                    $week = array();
                                    $multiple = 2;
                                    if (!empty($promotion->weekly))
                                    {
                                        $week_decode = json_decode($promotion->weekly, true);
                                        foreach ($week_decode as $val)
                                        {
                                            $week[] = $val['week'];
                                            $multiple = $val['multiple'];
                                        }
                                    }
                                ?>
                                <select class="form-control weekly" style="width: 100%;" name="weekly[]" multiple="multiple" data-placeholder="選擇 週優惠日"  >
                                    <option value="0" <?php if (!empty($week) && in_array("0", $week)) echo 'selected'; ?>>星期日</option>
                                    <option value="1" <?php if (!empty($week) && in_array("1", $week)) echo 'selected'; ?>>星期一</option>
                                    <option value="2" <?php if (!empty($week) && in_array("2", $week)) echo 'selected'; ?>>星期二</option>
                                    <option value="3" <?php if (!empty($week) && in_array("3", $week)) echo 'selected'; ?>>星期三</option>
                                    <option value="4" <?php if (!empty($week) && in_array("4", $week)) echo 'selected'; ?>>星期四</option>
                                    <option value="5" <?php if (!empty($week) && in_array("5", $week)) echo 'selected'; ?>>星期五</option>
                                    <option value="6" <?php if (!empty($week) && in_array("6", $week)) echo 'selected'; ?>>星期六</option>
                                </select>
                                <input type="hidden" name="weekly[]" />
                            </div>
                        </div>
                        <br/>
                        <div class="row">
                            <div class="col-md-2 control-label">
                                <label>倍數</label>
                            </div>
                            <div class="col-md-2">
                                <div class="input-group">
                                    <input type="number" class="form-control multiple1" name="multiple1"  value=<?php if (!empty($multiple)) echo $multiple; else echo 0; ?>></input>
                                </div>
                            </div>
                        </div>
                        <br/>
                        <!-- <div class="row">
                            <div class="col-md-2 control-label">
                                <label>月優惠日</label>
                            </div>
                            <div class="col-md-5">
                                <select class="form-control daily" style="width: 100%;" name="daily[]" multiple="multiple" data-placeholder="選擇 月優惠日"  >
                                    <?php 
                                        // for ($i = 1; $i <= 31; $i++)
                                        // {
                                        //     echo '<option value='.$i.'>'.strval($i).'</option>';
                                        // }
                                    ?>
                                </select>
                                <input type="hidden" name="daily[]" />
                            </div>
                        </div>
                        <br/>
                        <div class="row">
                            <div class="col-md-2 control-label">
                                <label>首購</label>
                            </div>
                            <div class="col-md-5">
                                <input type="checkbox" class="first la_checkbox"   />
                                <input type="hidden" class="first" name="first" class="" value="0" />
                            </div>
                        </div> -->
                    </div>
                    <div class="box-footer">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <div class="col-md-2">
                        </div>
                        <div class="col-md-8">
                            <div class="btn-group pull-right">
                                <button type="submit" class="btn btn-info pull-right" data-loading-text="<i class='fa fa-spinner fa-spin '></i> 提交">提交</button>
                            </div>

                            <div class="btn-group pull-left">
                                <button type="reset" class="btn btn-warning">復原</button>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="_method" value="{{$action}}" class="_method"  />
                    
                </form>
            </div>
        </div>
    </div>

</section>
