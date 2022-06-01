<?php
require_once("{$_SERVER['DOCUMENT_ROOT']}/config/db-connection.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/helpers/functions.php");

authed();

$message = null;

function fetch_data($con, $id)
{
    // Fetch data from the database
    $query = "SELECT * FROM polls
            WHERE id = $id;";
    $p_res = $con->query($query);

    // Redirect to /404 if there is no results
    if (mysqli_num_rows($p_res) !== 1) {
        redirectTo("/404");
    }

    // If the user hasn't voted, redirect to 404
    $u_id = get_username($con);
    $query = "SELECT * FROM users AS u
            INNER JOIN polls AS p ON p.id = $id
            INNER JOIN votes AS v ON v.user_id = $u_id AND u.id = $u_id AND v.poll_id = $id;";
    $res = $con->query($query);
    if (mysqli_num_rows($res) !== 1) {
        redirectTo("/404");
    }

    // Check if the user is the owner
    $query = "SELECT * FROM polls AS p
            INNER JOIN users AS u ON p.owner_id = $u_id AND p.id = $id";
    $u_res = $con->query($query);
    $user = mysqli_fetch_assoc($u_res);

    // Set the global variable
    while ($row = mysqli_fetch_assoc($p_res)) {
        if (isset($user)) {
            $GLOBALS["private"] = "NO";
            break;
        }
        if ($row["results_visibility"] == "public") {
            $GLOBALS["private"] = "NO";
        } else {
            $GLOBALS["private"] = "YES";
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === "GET") {
    $id = (int)esc_str($connection, $id);
    fetch_data($connection, $id);
} else {
    redirectTo("/404");
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
    <style>
        .h-100 {
            height: calc(100vh - 5rem) !important;
            max-height: 924px;
        }

        a.view-results {
            margin-left: 1rem;
        }
    </style>
</head>

<body>
    <?php include("{$_SERVER['DOCUMENT_ROOT']}/includes/nav.php") ?>
    <header class="masthead">
        <div class="container px-4 px-lg-5 h-100">
            <div class="row gx-4 gx-lg-5 h-100 align-items-center justify-content-center text-center">
                <div class="col-lg-8 align-self-end">
                    <h1 class="font-weight-bold mb-4">Success!</h1>
                </div>
                <div class="col-lg-8 align-self-baseline">
                    <?php if ($GLOBALS["private"] != "NO") : ?>
                        <a class="btn btn-primary btn-xl" href="/poll/<?php echo $id ?>">Back to the Poll</a>
                    <?php else : ?>
                        <a class="btn btn-primary btn-xl" href="/poll/<?php echo $id ?>">Back to the Poll</a>
                        <a class="btn btn-primary btn-xl view-results" href="/poll/<?php echo $id ?>/results">View Results</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>

</html>