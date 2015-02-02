<?php echo Bundles::getValue('awesome');?>
<?php echo Bundles::getValue('payment');?>
<?php echo Bundles::getValue('highcharts');?>
<?php echo Bundles::getValue('highcharts3d');?>
<h1>GAMES Controller</h1>

<div class="row">
	<div class="col-md-6">
		<div class="btn btn-success"><i class="fa fa-child"></i></div>
		<h1 style="display: inline; position:relative; top:10px;">Name of player club</h1>
	</div>
	<div class="col-md-6">
		<div id="map" style="min-width: 350px; height: 400px; margin: 0 auto"></div>
	</div>
</div>
<script type="text/javascript">
$(function () {
    $('#map').highcharts({
        chart: {
            type: 'pie',
            options3d: {
                enabled: true,
                alpha: 45,
                beta: 0
            }
        },
        title: {
            text: 'Statistics of the club'
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                colors: ['#90ed7d', '#f7a35c', '#8085e9', '#f15c80'],
                depth: 35,
                dataLabels: {
                    enabled: true,
                    format: '{point.name}'
                }
            }
        },
        series: [{
            type: 'pie',
            name: 'Browser totals',
            data: [
                ['Win',   45.0],
                ['Draw',       26.8],
                {
                    name: 'Lose',
                    y: 28.2,
                    sliced: true,
                    selected: true
                }
            ]
        }]
    });
});
</script>