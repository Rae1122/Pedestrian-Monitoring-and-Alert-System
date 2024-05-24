<?php

$servername = "localhost";
// REPLACE with your Database name
$dbname = "esp_data";
// REPLACE with Database user
$username = "admin";
// REPLACE with Database user password
$password = "your_password";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

$sql = "SELECT speed, reading_time FROM `SensorData` WHERE speed IS NOT NULL;";
//SELECT ROUND(AVG(s.speed), 2) AS average_speed,    HOUR(s.reading_time) AS hour FROM `SensorData` s WHERE s.speed IS NOT NULL GROUP BY HOUR(s.reading_time); 
// Query to get the average speed per hour 

$result = $conn->query($sql);

while ($data = $result->fetch_assoc()){
    $sensor_data[] = $data;
}

$readings_time = array_column($sensor_data, 'reading_time');
$value1 = json_encode(array_reverse(array_column($sensor_data, 'speed')), JSON_NUMERIC_CHECK);
$reading_time = json_encode(array_reverse($readings_time), JSON_NUMERIC_CHECK);



$result->free();
$conn->close();
?>

<!DOCTYPE html>
<html>
<meta name="viewport" content="width=device-width, initial-scale=1">
  <script src="https://code.highcharts.com/highcharts.js"></script>
  <style>
    body {
      min-width: 310px;
    	max-width: 1280px;
    	height: 800px;
      margin: 0 auto;
    }
    h2 {
      font-family: Arial;
      font-size: 2.5rem;
      text-align: center;
    }
  </style>
  <body>
    <br/>
    <br/>   
    <h2>Speed Data</h2>
    <div id="chart-Speed" class="container"></div>
    <script>

var value1 = <?php echo $value1; ?>;
var reading_time = <?php echo $reading_time; ?>;

var chartT = new Highcharts.Chart({
  chart:{ renderTo : 'chart-Speed' },
  title: { text: 'Speed(km/h) vs Timestamp' },
  series: [{
    showInLegend: false,
    data: value1
  }],
  plotOptions: {
    line: { animation: false,
      dataLabels: { enabled: true }
    },
    series: { color: '#059e8a' }
  },
  xAxis: { 
    type: 'datetime',
    categories: reading_time
  },
  yAxis: {
    title: { text: 'Speed (Km/h)' }
  
  },
  credits: { enabled: false }
});

</script>
</body>
</html>