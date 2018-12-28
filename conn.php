<?php
include __DIR__ . '/../src/config.php';
?>

<!DOCTYPE html>
<html>
<head>
<title>Test</title>
</head>
<body>

<h1>This is test for db connections</h1>

<?php
// test PDO connection
try {
    $conn = new PDO("mysql:host=db;dbname=imse_db", DB_USER, DB_PASS);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
catch(PDOException $e)
    {
    echo "PDO Connection failed: " . $e->getMessage();
    }
echo "PDO Connected successfully";
$conn=null;
?>

<br>

<?php
// Create connection mysqli
try {
    $conn = mysqli_connect(HOST_NAME, DB_USER, DB_PASS, DB_NAME);
}
catch(Exception $e){
    echo "failed creating mysqli connection".$e->getMessage();
}
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
echo "mysqli Connected successfully";
mysqli_close($conn);
?>

</body>
</html>