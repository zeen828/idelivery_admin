<div class="box">
    <div class="box-header">
        <h3 class="box-title">訂單類型統計表</h3>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <div class="box-body table-responsive no-padding" style="border-top: 1px solid #f4f4f4;">
                <div id="order_count" style="min-width: 250px; height: 400px; margin: 0 auto; padding: 10px;"></div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="box-body table-responsive no-padding" style="border-top: 1px solid #f4f4f4;">
                <div id="src_amount" style="min-width: 250px; height: 400px; margin: 0 auto; padding: 10px;"></div>
            </div>
        </div>
    </div>
    <div class="box-footer clearfix"></div>
</div>


<script>


    $( document ).ready(function() {
        var chartData = {order_count: {}, src_amount: {}};
        //var chartURL = "/admin/store/reports/order_type/chart";
        var chartURL = {
            order_count: "/admin/store/reports/order_type/count_chart",
            src_amount: "/admin/store/reports/order_type/amount_chart",
        };

        loadStatiData('order_count');
        loadStatiData('src_amount');

        function loadStatiData(types)
        {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                method : "GET",
                url: chartURL[types],
                data: { start_date : '<?php echo request()->query('start_date', date("Y-m-d"));?>',
                    end_date: '<?php echo request()->query('end_date', date("Y-m-d"));?>',
                    start_time: '<?php echo request()->query('start_time', '00:00:00');?>',
                    end_time: '<?php echo request()->query('end_time', '23:59:59');?>'}
            })
                .done(function(obj)
                {
                    //toastr.success('查詢成功');
                    chartData[types] = obj;
                    renderDiagram(types);
                })
                .fail(function(obj){
                    //toastr.warning('查詢失敗');
                });
        }

        function renderDiagram(types)
        {
            var option = {
                chart: {
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false,
                    type: 'pie'
                },
                title: {
                    text: chartData[types].title
                },
                tooltip: {
                    pointFormat: '{series.name}: <b>{point.data}</b><br/>' +
                    '{point.avg_name}: <b>{point.avg:.0f}</b>'
//                useHTML: true,
//                pointFormatter: function () {
//                    var point = this,
//                        series = point.series;
//                        //console.log(series.chart.legend.box);
//                        //legendSymbol = series.chart.legend.box.element.ownerSVGElement.outerHTML;
//
//                    return series.name + ": <b>" + point.data + "</b><br/>";
//                    //return legendSymbol + " " + series.name + ": <b>" + point.data + "</b><br/>";
//                }
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: true,
                            format: '<b>{point.name}</b>: {point.percentage:.2f} %',
                            style: {
                                color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                            }
                        }
                    }
                },
                series: [{
                    name: chartData[types].sub_title,
                    colorByPoint: true,
                    data: chartData[types].dataSeries,
                    showInLegend: true,
                }]
            };

            $('#'+ types).html('');
            Highcharts.chart(types, option);

        }

    });
</script>