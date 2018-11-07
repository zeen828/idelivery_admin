<div class="box">
    <div class="box-header">
        <h3 class="box-title">訂單銷售統計報表</h3>
        <div class="pull-right">
            <div class="btn-group pull-right" style="margin-right: 10px">
                <a class="btn btn-sm btn-twitter"><i class="fa fa-calendar"></i> 月份</a>
                <button type="button" class="btn btn-sm btn-twitter dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                    <span class="caret"></span>
                    <span class="sr-only">Toggle Dropdown</span>
                </button>
                <ul class="dropdown-menu" role="menu">
                    <li><a onclick="loadStatiData('order', idxYear['order'], null, idxDay['order']);">所有月份</a></li>
                    @for ($idxMonth=1; $idxMonth<=12; $idxMonth++)
                        <li><a onclick="loadStatiData('order', idxYear['order'], {{ $idxMonth }}, idxDay['order']);">{{ $idxMonth }}月</a></li>
                    @endfor
                </ul>
            </div>
            <div class="btn-group pull-right" style="margin-right: 10px">
                <a class="btn btn-sm btn-twitter"><i class="fa fa-calendar"></i> 年份</a>
                <button type="button" class="btn btn-sm btn-twitter dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                    <span class="caret"></span>
                    <span class="sr-only">Toggle Dropdown</span>
                </button>
                <ul class="dropdown-menu" role="menu">
                    @for ($idxYear=2017; $idxYear<=date('Y'); $idxYear++)
                        <li><a onclick="loadStatiData('order', {{ $idxYear }}, idxMonth['order']);">{{ $idxYear }}年</a></li>
                    @endfor
                </ul>
            </div>
        </div>
    </div>
    <div class="box-body table-responsive no-padding" style="border-top: 1px solid #f4f4f4;">
        <div id="order" style="min-width: 250px; height: 400px; margin: 0 auto; padding: 10px;"></div>
    </div>
    <div class="box-footer clearfix"></div>
</div>

<div class="box">
    <div class="box-header">
        <h3 class="box-title">品項銷售統計報表</h3>
        <div class="pull-right">
            <div class="btn-group pull-right" style="margin-right: 10px">
                <a class="btn btn-sm btn-twitter"><i class="fa fa-calendar"></i> 月份</a>
                <button type="button" class="btn btn-sm btn-twitter dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                    <span class="caret"></span>
                    <span class="sr-only">Toggle Dropdown</span>
                </button>
                <ul class="dropdown-menu" role="menu">
                    <li><a onclick="loadStatiData('item', idxYear['item'], null, idxDay['item']);">所有月份</a></li>
                    @for ($idxMonth=1; $idxMonth<=12; $idxMonth++)
                        <li><a onclick="loadStatiData('item', idxYear['item'], {{ $idxMonth }}, idxDay['item']);">{{ $idxMonth }}月</a></li>
                    @endfor
                </ul>
            </div>
            <div class="btn-group pull-right" style="margin-right: 10px">
                <a class="btn btn-sm btn-twitter"><i class="fa fa-calendar"></i> 年份</a>
                <button type="button" class="btn btn-sm btn-twitter dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                    <span class="caret"></span>
                    <span class="sr-only">Toggle Dropdown</span>
                </button>
                <ul class="dropdown-menu" role="menu">
                    @for ($idxYear=2017; $idxYear<=date('Y'); $idxYear++)
                        <li><a onclick="loadStatiData('item', {{ $idxYear }}, idxMonth['item'], idxDay['item']);">{{ $idxYear }}年</a></li>
                    @endfor
                </ul>
            </div>
        </div>
    </div>
    <div class="box-body table-responsive no-padding" style="border-top: 1px solid #f4f4f4;">
        <div id="item" style="min-width: 250px; height: 400px; margin: 0 auto; padding: 10px;"></div>
    </div>
    <div class="box-footer clearfix"></div>
</div>

<script>
    var idxYear = {order: {{ date('Y') }}, item: {{ date('Y') }}};
    var idxMonth = {order: null, item: null};
    var idxDay = {order: null, item: null};
    var chartData = {order: {}, item: {}};
    var chartURL = {
        order: "/admin/store/reports/statistics/load_year_orders",
        item: "/admin//store/reports/statistics/load_order_items",
    };

    function loadStatiData(chart, year, month, day)
    {
        idxYear[chart] = year ? year : idxYear[chart];
        idxMonth[chart] = month;
        idxDay[chart] = day;

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            url: chartURL[chart],
            type: 'POST',
            data: {year: idxYear[chart], month: idxMonth[chart]},
            success: function(res) {
                chartData[chart] = res;

                if (chartData[chart].dataSeries.length === 0) {
                    $('#'+ chart).html('');
                    $('#'+ chart).html('<center style="line-height: 370px; font-size: 2em;">沒有 '+ chartData[chart].title +'報表資訊</center>');
                } else {
                    renderDiagram(chart);
                }
            }
        });
    }

    function renderDiagram(chart)
    {
        var option = {
            chart: {
                type: 'line'
            },
            title: {
                text: chartData[chart].title
            },
            yAxis: {
                title: {
                    text: chartData[chart].sidetitle
                }
            },
            plotOptions: {
                line: {
                    dataLabels: {
                        enabled: true
                    },
                    enableMouseTracking: true
                }
            },
            series: chartData[chart].dataSeries,
            xAxis: {
                categories: chartData[chart].dataColumn
            }
        };
        
        $('#'+ chart).html('');
        Highcharts.chart(chart, option);
    }

    (function() {
        loadStatiData('order', {{ date('Y') }}, null,  null);
        loadStatiData('item', {{ date('Y') }}, null,  null);
    })();
</script>