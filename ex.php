<!DOCTYPE html>
<html>
<head>
    <title>View Students</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css.css">
</head>

<body>

<?php
include 'db.php';

$foo = 25;              // Numerical variable
$bar = "Hello";         // String variable

echo $bar;              // Outputs: Hello
echo $foo, $bar;        // Outputs: 25Hello
echo "5x5=", $foo;      // Outputs: 5x5=25
echo "5x5=$foo";        // Outputs: 5x5=25
echo '5x5=$foo';        // Outputs: 5x5=$foo
?>

</body>
</html>

