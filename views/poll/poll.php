<?php
require_once("{$_SERVER['DOCUMENT_ROOT']}/config/db-connection.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/helpers/auth-functions.php");

authed();

$message = null;

function esc_str ($connection, $str) {
    return mysqli_real_escape_string($connection, $str);
}

function fetch_data ($con, $id) {
    if ($id === 0) {
        header("Location: /404");
        exit();
    }
    // Fetch data from the database
    $query = "SELECT * FROM candidates AS c
            INNER JOIN polls AS p ON c.poll_id = p.id
            WHERE c.poll_id = $id;";
    $res = $con->query($query);

    // Redirect to /404 if there is no results
    if(mysqli_num_rows($res) === 0) {
        header("Location: /404");
        exit();
    }

    // Filter the options
    while ( $row = mysqli_fetch_assoc($res) ) {
        $filtered_option = array_intersect_key($row, array_flip(["id", "name"]));
        $GLOBALS["options"][] = $filtered_option;
        $GLOBALS["poll"]["title"] = $row["title"];
        $GLOBALS["poll"]["description"] = $row["description"];
    }
}

if ($_SERVER['REQUEST_METHOD'] === "GET") {
    $id = (int)esc_str($connection, $id);
    fetch_data($connection, $id);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="/dist/css/create_poll.css">
    <title><?php echo $GLOBALS["poll"]["title"] ?></title>
</head>

<body style="font-family:'Segoe UI'">
    <div class="container">
        <h3 class="mt-5">Heisenberge Polls</h3>
        <h5><?php echo $message ?></h3>
        <form action="<?php echo "/poll/$id" ?>" method="POST" class="g-3 mb-4 mt-4">
            <div class="row col-12">
                <h4 class="fw-bold text-center mt-3"></h4>
                <h5 class="mb-3"><?php echo $GLOBALS["poll"]["title"] ?></h5>
                <?php
                    foreach ($GLOBALS["options"] as $idx => $val) {
                        echo '
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="exampleForm" id="option_' . $val["id"] . '" />
                                <label class="form-check-label" for="option_' . $val["id"] . '">'
                                    . $val["name"] .
                                '</label>
                            </div>
                        ';
                    }
                ?>
                <div class="text-end">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
        </form>
    </div>
</body>

</html>