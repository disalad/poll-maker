<?php
require_once("{$_SERVER['DOCUMENT_ROOT']}/config/db-connection.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/helpers/functions.php");

authed();

function delete_poll($con, $u_id, $p_id) {
    $query = "SELECT * FROM polls
            WHERE id = $p_id";
    $res = $con->query($query);
    $result = mysqli_fetch_assoc($res);

    var_dump($result);
    var_dump($u_id);
    if ($result["owner_id"] != $u_id) {
        echo "403 Forbidden";
        exit();
    }

    // Delete poll
    $query = "DELETE FROM polls
            WHERE id = $p_id";
    $res = $con->query($query);

    // Delete candidates
    $query = "DELETE FROM candidates
            WHERE poll_id = $p_id";
    $res = $con->query($query);

    // Delete votes
    $query = "DELETE FROM votes
            WHERE poll_id = $p_id";
    $res = $con->query($query);
    
    redirectTo("/poll/my");
}

if ($_SERVER['REQUEST_METHOD'] === "GET") {
    $u_id = get_username($connection);
    delete_poll($connection, $u_id, $id);
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Heisenberge Polls</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="/dist/css/nav.css">
</head>

<body>
    <?php include("{$_SERVER['DOCUMENT_ROOT']}/includes/nav.php") ?>
    <div class="container mt-5">
        
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>

</html>