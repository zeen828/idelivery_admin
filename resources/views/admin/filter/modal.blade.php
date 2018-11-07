    <style>
        .custom-filter {
            box-shadow: none;
            margin-bottom: 10px;
            border: 1px solid #eee;
        }

        .custom-filter.collapsed-box {
            border: 0;
        }

        .custom-filter .pull-right {
            position: absolute;
            right: 0;
            z-index: 99;
        }

        .custom-filter.collapsed-box .pull-right {
            position: relative;
        }

        .custom-filter .form-group:last-child {
            margin-bottom: 0;
        }
    </style>
    <div class="box-body custom-filter" style="display:none;">
        {{--
        <div class="box-tools pull-right">
            <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        </div>
        --}}
        <form action="{!! $action !!}" method="get" pjax-container>
            <div class="box-body">
                <div class="form">
                    @foreach($filters as $filter)
                        <div class="form-group col-sm-4">
                            {!! $filter->render() !!}
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="box-footer clearfix">
                <button type="submit" class="btn btn-primary submit"
                        style="float: right;">{{ trans('admin.filter') }}</button>
                <a href="{!! $action !!}" class="btn btn-primary btn-facebook"
                   style="float: right; margin-right: 10px;">{{ trans('admin.reset') }}</a>
            </div>
        </form>
    </div>
    <script>
    $(function(){
        $('.box-body.custom-filter')
            .detach()
            .replaceAll('section.content .row:first .box-body')
            .end()
            .show();
    });
    </script>