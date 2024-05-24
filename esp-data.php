<!DOCTYPE html>

<html><body>
<link rel="stylesheet" href="style.css" /> 
<?php include('header.php'); ?>
<?php

$servername = "localhost";

//  Database name
$dbname = "esp_data";
//  Database user
$username = "admin";
//  Database user password
$password = "your_password";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

$sql = "SELECT id, speed, license, reading_time FROM SensorData ORDER BY id DESC";
// SELECT avg(s.speed)average_speed,hour(s.reading_time) as hour FROM `SensorData` s group by hour(s.reading_time);
echo '<table cellspacing="5" cellpadding="5">
      <thead>
      <td>ID</td> 
      <td>Speed (km/h)</td> 
      <td>License</td>  
      <td>Timestamp</td> 
      </thead>
      ';
 
if ($result = $conn->query($sql)) {
    while ($row = $result->fetch_assoc()) {
        $row_id = $row["id"];
        $row_speed = $row["speed"];
        $row_license = $row["license"]; 
        $row_reading_time = $row["reading_time"];
        // Uncomment to set timezone to - 1 hour (you can change 1 to any number)
        //$row_reading_time = date("Y-m-d H:i:s", strtotime("$row_reading_time - 1 hours"));
      
        // Uncomment to set timezone to + 4 hours (you can change 4 to any number)
        //$row_reading_time = date("Y-m-d H:i:s", strtotime("$row_reading_time + 4 hours"));
      
        echo '<tr> 
                <td>' . $row_id . '</td> 
                <td>' . $row_speed . '</td> 
                <td>' . $row_license . '</td> 
                <td>' . $row_reading_time . '</td> 
              </tr>';
    }
    $result->free();
}

$conn->close();
?> 
</table>
</body>
</html>
