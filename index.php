<html>

<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Latest compiled and minified CSS -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

  <style>
        .table-striped>tbody>tr:nth-child(odd)>td,
        .table-striped>tbody>tr:nth-child(odd)>th {
            background-color: #e6e6ff;
        }

        .details {
            margin-right: 100px;
            margin-left: 100px;
        }

        .key {
            width: 250px;
        }
  </style>

</head>

<body>

  <h1 id="heading" style="text-align: center">Red Hat OpenShift - PHP & Database Demo</h1>

<?php

$host = getenv("MYSQL_SERVICE_HOST");
$port = getenv("MYSQL_SERVICE_PORT");
$database = getenv("MYSQL_SERVICE_DATABASE");
$username = getenv("MYSQL_SERVICE_USERNAME");
$password = getenv("MYSQL_SERVICE_PASSWORD");

?>

  <div class="details">
    <table class="table table-bordered table-striped table-responsive table-hover">

<?php
error_reporting(E_ERROR);

function write_key_value($key, $value) {
  echo '<tr><td class="key">' . $key . '</td>';
  echo '<td><p id="ip">' . $value . '</p></td></tr>';
}

// Server Details
$env_text = getenv("TEXT");
$uptime = shell_exec('uptime -p');
$cpu = trim(shell_exec("cat /proc/cpuinfo | grep \"model name\" | uniq | awk -F ':' '{print $2}'"));
$cores = trim(shell_exec("cat /proc/cpuinfo | grep processor | wc -l"));

write_key_value('Server IP', $_SERVER['SERVER_ADDR']);
write_key_value('Database Name', ($database != '' ? $database : 'Unknown'));
write_key_value('Environment Variable (TEXT)', ($env_text != '' ? $env_text : 'Unknown'));
write_key_value('Server Uptime', $uptime);
write_key_value('CPU', $cpu);
write_key_value('Number of Cores', $cores);
?>
    </table>
  </div>

<?php
$conn = mysqli_connect($host, $username, $password, $database, $port);

if ($conn) {

  echo "Database is available <br/><br/>";
  $sql = "CREATE TABLE visitors (
          id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
          containerip VARCHAR(15) NOT NULL,
          visitstamp VARCHAR(30) NOT NULL)";
  if (mysqli_query($conn, $sql)) {
    echo "Database initialized <br/>";
  } else {
    echo "Database initialization skipped: ";
    echo mysqli_error($conn) . "<br/>";
  }

  echo "<br/>";

  $containerip = $_SERVER['SERVER_ADDR'];
  $visitstamp = date("D M j G:i:s T Y");
  $sql = "INSERT INTO visitors (containerip, visitstamp)
          VALUES ('$containerip', '$visitstamp')";

  if (mysqli_query($conn, $sql)) {
    echo "New visitor logged <br/>";
  } else {
    echo "Error: " . $sql . " - " . mysqli_error($conn) . "<br/>";
  }

  echo "<br/>";

  echo "<h3> Visitor Log </h3>";
  $sql = "SELECT id, containerip, visitstamp FROM visitors ORDER BY id DESC LIMIT 20";
  $result = mysqli_query($conn, $sql);
  if (mysqli_num_rows($result) > 0) {
    echo "<table><tr><th>Id</th><th>Container IP</th><th>Timestamp</th></tr>";
    while($row = mysqli_fetch_assoc($result)) {
        echo "<tr><td>" . $row["id"] . "</td><td>" . $row["containerip"] . "</td><td>" . $row["visitstamp"] . "</td></tr>";
    }
    echo "</table>";
  } else {
    echo "0 results";
  }

  mysqli_close($conn);
}
?>

  <br/><br/>
  <img src="powered-transparent-black.png" alt="Powered by OpenShift"/>

</body>

</html>
