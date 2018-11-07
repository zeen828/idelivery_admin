<div class="box">
    <div class="box-header">
        <h3 class="box-title">餐點分類銷售統計表</h3>
    </div>
    <div class="box-body table-responsive no-padding" style="border-top: 1px solid #f4f4f4;">
        <div id="group_sale" style="min-width: 250px; height: 400px; margin: 0 auto; padding: 10px;"></div>
    </div>
    <div class="box-footer clearfix"></div>
</div>


<script>
    $( document ).ready(function() {
        var chartData = null;
        var chartURL = "/admin/store/reports/group_sale/chart";

        loadStatiData();

        function loadStatiData()
        {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                method : "GET",
                url: chartURL,
                data: { start_date : '<?php echo request()->query('start_date', date("Y-m-d"));?>',
                    end_date: '<?php echo request()->query('end_date', date("Y-m-d"));?>',
                    start_time: '<?php echo request()->query('start_time', '00:00:00');?>',
                    end_time: '<?php echo request()->query('end_time', '23:59:59');?>'}
            })
                .done(function(obj)
                {
                    //toastr.success('查詢成功');
                    chartData = obj;
                    renderDiagram();
                })
                .fail(function(obj){
                    //toastr.warning('查詢失敗');
                });
        }

        function renderDiagram()
        {
            var option = {
                chart: {
                    type: 'column'
                },
                title: {
                    text: chartData.title
                },
                yAxis: {
                    title: {
                        text: chartData.sidetitle
                    }
                },
                tooltip: {
                    useHTML: true,
                    pointFormatter: function () {
                        var point = this,
                            series = point.series,
                            legendSymbol = "<svg width='15' height='15'>" + series.legendSymbol.element.outerHTML + "</svg>";

                        return legendSymbol + " " + series.name + ": <b>" + point.y + "</b><br/>";
                    }
                },
//            tooltip: {
//                pointFormat: '{series.name}: <b>{point.y:.0f}</b>'
//            },
                legend: {
                    layout: 'vertical',
                    align: 'right',
                    verticalAlign: 'middle'
                },
                xAxis: {
                    categories: chartData.dataColumn,
                    title: {
                        text: chartData.bottomtitle
                    }
                },
                series: chartData.dataSeries,

            };

            $('#group_sale').html('');
            Highcharts.chart('group_sale', option);
        }
    });
</script>