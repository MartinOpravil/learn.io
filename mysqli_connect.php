<?php 
$conn = mysqli_connect('localhost', 'root', '', 'abrakadabra');

        if (!$conn) {
            die('Connect Error (' . mysqli_connect_errno() . ') '
                    . mysqli_connect_error());
        }

        $conn->query('SET NAMES utf8');
?>
