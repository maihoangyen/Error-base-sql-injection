<!DOCTYPE html>

<html lang="en-GB">

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    </head>
    <body>
    <div >
    <h1>Welcom to myweb</h1>

    <div >
        <form action="#" method="POST">
            <p>
                Mời nhập ID:
                <input type="text" size="15" name="id">
                <input type="submit" name="Submit" value="Submit">
            </p>

        </form>
        
    </div>
    </body>

</html>
<?php

if( isset( $_POST[ 'Submit' ] ) ) {

    $conn = new mysqli('localhost','root', '','dvwa');
   
    $id = $_POST[ 'id' ];
    $id = mysqli_real_escape_string($conn, $id);

    $query  = "SELECT first_name, last_name FROM users WHERE user_id = $id;";
    $result = mysqli_query($conn, $query) or die(mysqli_error($conn));


    while( $row = mysqli_fetch_assoc( $result ) ) {
  
        $first = $row["first_name"];
        $last  = $row["last_name"];

  
        echo "ID là:{$id}||Tên: {$first}||Họ: {$last}";
    }

}

mysqli_close($conn);

?> 