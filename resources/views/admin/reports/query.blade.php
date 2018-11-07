@if (!empty($route))
<meta name="csrf-token" content="{{ csrf_token() }}">
<form id="form1" method="GET" action={{ $route }}>
<div class='row'>
    <div class="col-sm-4">
        <button type="button" class="btn btn-default" id="today">今天</button>
        <button type="button" class="btn btn-default" id="yesterday">昨天</button>
        <button type="button" class="btn btn-default" id="last_7">最近 7天</button>
        <button type="button" class="btn btn-default" id="last_30">最近30天</button>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <label class="col-sm-2" style="margin-top: 8px;">日期</label>
            <div class="col-sm-5">
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                    <input type="text" name="start_date" value="" class="form-control start_date" />
                </div>
            </div>

            <div class="col-sm-5">
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                    <input type="text" name="end_date" value="" class="form-control end_date" />
                </div>
            </div>
        </div>
    </div>
    {{--<div class="col-sm-4">--}}
        {{--<div class="form-group">--}}
            {{--<label class="col-sm-2" style="margin-top: 8px;">時間</label>--}}
            {{--<div class="col-sm-5">--}}
                {{--<div class="input-group">--}}
                    {{--<span class="input-group-addon"><i class="fa fa-clock-o"></i></span>--}}
                    {{--<input type="text" name="start_time" value="" class="form-control start_time" style="width: 100px"  />--}}
                {{--</div>--}}
            {{--</div>--}}

            {{--<div class="col-sm-5">--}}
                {{--<div class="input-group">--}}
                    {{--<span class="input-group-addon"><i class="fa fa-clock-o"></i></span>--}}
                    {{--<input type="text" name="end_time" value="" class="form-control end_time" style="width: 100px"  />--}}
                {{--</div>--}}
            {{--</div>--}}
        {{--</div>--}}
    {{--</div>--}}
    <div class="col-sm-2">
        <button class="btn btn-info">查詢</button>
    </div>
</div>
<input type="hidden" name="_token" value="rEjj40PcYPgr46WEiCklmSvMUlcbRwbizIUZziHz">
</form>

<script src="/vendor/laravel-admin/moment/min/moment-with-locales.min.js"></script>
<script src="/vendor/laravel-admin/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>
<script>
    $(function()
    {
        function get_date(date_input)
        {
            var dd = date_input.getDate();
            var mm = date_input.getMonth()+1; //January is 0!
            var yyyy = date_input.getFullYear();

            if(dd<10) {
                dd = '0'+dd
            }

            if(mm<10) {
                mm = '0'+mm
            }

            date_output = yyyy + '-' + mm + '-' + dd;

            return date_output;

        }

        $(document)
            .ready(function () {
                $('.start_date').datetimepicker({"format":"YYYY-MM-DD 00:00:00","locale":"zh-TW","useCurrent":false});
                $('.end_date').datetimepicker({"format":"YYYY-MM-DD 23:59:59","locale":"zh-TW","useCurrent":false});

                $('.start_date').val('<?php echo request()->query('start_date', date("Y-m-d 00:00:00"));?>');
                $('.end_date').val('<?php echo request()->query('end_date', date("Y-m-d 23:59:59"));?>');

            });


        $("#today").click( function () {
            $('.start_date').val(get_date(new Date()) + " 00:00:00");
            $('.end_date').val(get_date(new Date()) + " 23:59:59");
        });

        $("#yesterday").on('click', function () {
            var date = new Date();
            date.setDate(date.getDate() - 1);
            $('.start_date').val(get_date(date) + " 00:00:00");
            $('.end_date').val(get_date(date) + " 23:59:59");
        });

        $("#last_7").on('click', function () {
            var date = new Date();
            date.setDate(date.getDate() - 6);
            $('.start_date').val(get_date(date) + " 00:00:00");
            $('.end_date').val(get_date(new Date()) + " 23:59:59");
        });

        $("#last_30").on('click', function () {
            var date = new Date();
            date.setDate(date.getDate() - 29);
            $('.start_date').val(get_date(date) + " 00:00:00");
            $('.end_date').val(get_date(new Date()) + " 23:59:59");
        });

        $("#today").click().focus();


    });



</script>
@endif