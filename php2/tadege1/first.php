<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php
    echo "hallo tadege  this is myfirt phpcode";
    $host = "localhost";
    $user = "tadege";
    $password="tadege";
    $database="lab";
    $connect =mysqli_connect( $host, $user,  $password,  $database);
    if(!$connect){
        die('connection failed');
    }
    echo 'connected successfully';
    $table1 ='create table table1'
    
    ?>
</body>
</html>