<div class="box">
    <div class="box-header">
        <h3 class="box-title">餐點品項銷售統計表</h3>
    </div>
    <div class="box-body table-responsive no-padding" style="border-top: 1px solid #f4f4f4;">
        <div id="campaign_type" style="min-width: 250px; height: 400px; margin: 0 auto; padding: 10px;"></div>
    </div>
    <div class="box-footer clearfix"></div>
</div>


<script>

    $( document ).ready(function() {
        var chartData = null;
        var chartURL = "/admin/store/reports/campaign/chart";

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
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false,
                    type: 'pie'
                },
                title: {
                    text: chartData.title
                },
//            legend: {
//                enabled: true,
//                floating: true,
//                verticalAlign: 'middle',
//                align:'right',
//                layout: 'vertical',
//                y: $(this).find('#container').height()/4, //chart.height/4
//                labelFormatter : function() {
//                    var total = 0, percentage; $.each(this.series.data, function() { total+=this.y; });
//                    percentage=((this.y/total)*100).toFixed(2);
//                    return this.name + this.y + '(<span style=\"color:'+this.color+'\">'+percentage+ '%)';
//                }
//            },
//            tooltip: {
//                useHTML: true,
//                pointFormatter: function () {
//                    var point = this,
//                        series = point.series,
//                        legendSymbol = "<svg width='15' height='15'>" + series.legendSymbol.element.outerHTML + "</svg>";
//
//                    return legendSymbol + " " + series.name + ": <b>" + point.y + "</b><br/>";
//                }
//            },
//            tooltip: {
//                pointFormat: '{series.name}: <b>{point.y:.0f}</b>'
//            },
//            tooltip: {
//                pointFormat: '{series.name}: <b>{point.data}</b>'
//            },
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
                    name: '折扣金額',
                    colorByPoint: true,
                    data: chartData.dataSeries,
                    showInLegend: true,
                }]
            };

            $('#campaign_type').html('');
            Highcharts.chart('campaign_type', option);
        }

    });
</script>