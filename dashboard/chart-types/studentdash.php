<?php include '../header.php'; ?>
<?php include '../sidebar.php'; ?>
<?php include '../content.php'; ?>

<br /><br />
<div id="chartContainer1" style="width: 45%; height: 300px;display: inline-block;"></div>
<div id="chartContainer2" style="width: 45%; height: 300px;display: inline-block;"></div>
<br /><br />
<div id="chartContainer3" style="width: 45%; height: 300px;display: inline-block;"></div>
<div id="chartContainer4" style="width: 45%; height: 300px;display: inline-block;"></div>

<?php
    $dataPoints1 = array(
	array("x" => 0, "y" => 101.3),
	array("x" => 152, "y" => 99.49),
	array("x" => 457, "y" => 95.91),
	array("x" => 762, "y" => 92.46),
	array("x" => 914, "y" => 90.81),
	array("x" => 1067, "y" => 89.15),
	array("x" => 2743, "y" => 72.4),
	array("x" => 3048, "y" => 69.64),
	array("x" => 4572, "y" => 57.16),
	array("x" => 6096, "y" => 46.61),
	array("x" => 7620, "y" => 37.65),
	array("x" => 9144, "y" => 30.13),
	array("x" => 10668, "y" => 23.93),
	array("x" => 12192, "y" => 18.82),
	array("x" => 18288, "y" => 7.24),
	array("x" => 21336, "y" => 4.49),
	array("x" => 24384, "y" => 2.8),
	array("x" => 27432, "y" => 1.76)
    );
    $dataPoints2 = array(
	array("y" => 72.48, "legendText" => "Remembering", "label" => "Remembering"),
	array("y" => 10.39, "legendText" => "Understanding", "label" => "Understanding"),
	array("y" => 7.78, "legendText" => "Applying", "label" => "Applying"),
	array("y" => 7.14, "legendText" => "Analyzing", "label" => "Analyzing"),
	array("y" => 0.22, "legendText" => "Evaluating", "label" => "Evaluating"),
	array("y" => 0.15, "legendText" => "Creating", "label" => "Creating")
    );
    $dataPoints3 = array(
	array("y" => 297571, "label" => "week1"),
	array("y" => 267017, "label" => "week2"),
	array("y" => 175200, "label" => "week3"),
	array("y" => 154580, "label" => "week4"),
	array("y" => 116000, "label" => "week5"),
	array("y" => 97800, "label" => "week6"),
	array("y" => 20682, "label" => "week7"),
	array("y" => 20350, "label" => "week8")
    );
    $dataPoints4 = array(
	array("x" => 883593000000, "y" => 1872000),
	array("x" => 915129000000, "y" => 2140000),
	array("x" => 946665000000, "y" => 7289000),
	array("x" => 978287400000, "y" => 4830000),
	array("x" => 1009823400000, "y" => 2009000),
	array("x" => 1041359400000, "y" => 2840000),
	array("x" => 1072895400000, "y" => 2396000),
	array("x" => 1104517800000, "y" => 1613000),
	array("x" => 1136053800000, "y" => 2821000),
	array("x" => 1167589800000, "y" => 2000000),
	array("x" => 1199125800000, "y" => 1397000)
    );
    
?>

<script type="text/javascript">

    $(function () {
        var chart1 = new CanvasJS.Chart("chartContainer1",{
            title:{
                text: "Access to the site"
            },
            axisY :{
                title: "Times",
                titleFontSize: 18,
                suffix: " kPa"
            },
            animationEnabled: true,
            axisX :{
                title: "Weeks",
                suffix: " m"
            },
            data: [
            {
                markerSize: 0,
                toolTipContent: "<span style='\"color: {color};'\"'><strong>Elevation</strong></span> {x} m<br/><span style='\"'color: {color};'\"'><strong>Pressure</strong></span> {y} kPa",
                type: "spline",
                showInLegend: true,
                legendText: "Computed for 15 deg. C at Sea Level",
                dataPoints: <?php echo json_encode($dataPoints1, JSON_NUMERIC_CHECK); ?>
            }
            ]
        });

    chart1.render();

    var chart2 = new CanvasJS.Chart("chartContainer2", {
        title: {
            text: "Bloom's Taxonomy"
        },
        animationEnabled: true,
        legend: {
            verticalAlign: "center",
            horizontalAlign: "left",
            fontSize: 18,
            fontFamily: "Helvetica"
        },
        theme: "light2",
        data: [
        {
            type: "pie",
            indexLabelFontFamily: "Garamond",
            indexLabelFontSize: 20,
            indexLabel: "{label} {y}%",
            startAngle: -20,
            showInLegend: true,
            toolTipContent: "{legendText} {y}%",
            dataPoints: <?php echo json_encode($dataPoints2, JSON_NUMERIC_CHECK); ?>
        }
        ]
    });
    chart2.render();

    var chart3 = new CanvasJS.Chart("chartContainer3",
    {
        theme: "light2",
        animationEnabled: true,
        title: {
            text: "Access to the site"
        },
        axisY: {
            title: "Times"
        },
        data: [{
            type: "column",
            showInLegend: true,
            legendMarkerColor: "grey",
            legendText: "Weeks/Semeter",
            dataPoints: <?php echo json_encode($dataPoints3, JSON_NUMERIC_CHECK); ?>
        }]
    });
    chart3.render();

    var chart4 = new CanvasJS.Chart("chartContainer4",
	{
	    title:{
	        text: "How well are you learning"
	    },
	    theme: "light2",
	    animationEnabled: true,
	    axisY: {
	        title: "Performance",
	        valueFormatString: "#0,,.",
	        suffix: " %"
	    },
	    data: [
            {
                toolTipContent: "{y} units",
                type: "splineArea",
                markerSize: 5,
                dataPoints: <?php echo json_encode($dataPoints4, JSON_NUMERIC_CHECK); ?>
            }
	    ]
	});
    chart4.render();

    });
</script>

<?php include '../footer.php'; ?>