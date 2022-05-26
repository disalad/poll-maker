<?php
try {
    $connection = mysqli_connect('localhost', 'root', '', 'voting_system');
} catch (Exception $e) {
    if (str_contains($e->getMessage(), 'Access denied')) { 
        die('Database connection failed');
    } else {
        die('Internal server error : ' . $e->getMessage()); //TODO: 
    }
}
?>