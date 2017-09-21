<!DOCTYPE html>
<html lang="en">

<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

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
error_reporting(E_ERROR);

$host = getenv("MYSQL_SERVICE_HOST");
$port = getenv("MYSQL_SERVICE_PORT");
$database = getenv("MYSQL_SERVICE_DATABASE");
$username = getenv("MYSQL_SERVICE_USERNAME");
$password = getenv("MYSQL_SERVICE_PASSWORD");

$conn = mysqli_connect($host, $username, $password, $database, $port);

$page_count = 'Unknown';

function write_key_value($key, $value) {
  echo '<tr><td class="key">' . $key . '</td>';
  echo '<td><p id="ip">' . $value . '</p></td></tr>';
}

?>

<?php
if ($conn) {

  // Initialize the DB if necessary
  $sql = "CREATE TABLE visitors (
          id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
          containerip VARCHAR(15) NOT NULL,
          visitstamp VARCHAR(30) NOT NULL)";
  mysqli_query($conn, $sql);

  // Add entry for this request
  $containerip = $_SERVER['SERVER_ADDR'];
  $visitstamp = date("D M j G:i:s T Y");
  $sql = "INSERT INTO visitors (containerip, visitstamp)
          VALUES ('$containerip', '$visitstamp')";
  mysqli_query($conn, $sql);

  // Update the count
  $sql = 'SELECT COUNT(*) FROM visitors';
  $page_count = mysqli_query($conn, $sql);
}

?>

  <div class="details">

    <table class="table table-bordered table-striped table-responsive table-hover">
<?php
  write_key_value('Page Hits', $page_count)
?>
    </table>

    <table class="table table-bordered table-striped table-responsive table-hover">
<?php
// Server Details
$env_text = getenv("TEXT");
$uptime = shell_exec('uptime -p');
$cpu = trim(shell_exec("cat /proc/cpuinfo | grep \"model name\" | uniq | awk -F ':' '{print $2}'"));
$cores = trim(shell_exec("cat /proc/cpuinfo | grep processor | wc -l"));

write_key_value('Server IP', $_SERVER['SERVER_ADDR']);
write_key_value('Database Name', ($database != '' ? $database : 'Unknown'));
write_key_value('Environment Variable (TEXT)', $env_text);
write_key_value('Server Uptime', $uptime);
write_key_value('CPU', $cpu);
write_key_value('Number of Cores', $cores);
?>
    </table>

<?php

if ($conn) {
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

  </div>

</body>

</html>
