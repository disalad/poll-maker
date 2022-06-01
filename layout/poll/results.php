<?php
require_once("{$_SERVER['DOCUMENT_ROOT']}/config/db-connection.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/helpers/functions.php");

authed();

$GLOBALS["candidates"] = [];

function fetch_data($con, $p_id) {
    // Check whether the user have access to the results
    $query = "SELECT * FROM polls
            WHERE id = $p_id";
    $res = $con->query($query);
    $result = mysqli_fetch_assoc($res);
    if ($result["results_visibility"] === "private" && get_username($con) != $result["owner_id"]) {
        echo "403 Forbidden";
        exit();
    }

    $query = "SELECT * FROM candidates
            WHERE poll_id = $p_id";
    $res = $con->query($query);

    while ($row = mysqli_fetch_assoc($res)) {
        $r = $row["name"];
        $GLOBALS["candidates"][$r] = [];
        $GLOBALS["candidates"][$r]["male"] = [];
        $GLOBALS["candidates"][$r]["female"] = [];
    }

    // Get vote count for each candidate
    $query = "SELECT * FROM votes AS v
        INNER JOIN candidates AS c ON v.candidate_id = c.id AND v.poll_id = $p_id
        INNER JOIN users AS u ON v.user_id = u.id;";
    $res = $con->query($query);

    while ($row = mysqli_fetch_assoc($res)) {
        $g = $row["gender"];
        array_push($GLOBALS["candidates"][$row["name"]][$g], $row["gender"]);
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
    <title>Heisenberge Polls | Results</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="/dist/css/nav.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.8.0/chart.min.js" integrity="sha512-sW/w8s4RWTdFFSduOTGtk4isV1+190E/GghVffMA9XczdJ2MDzSzLEubKAs5h0wzgSJOQTRYyaz73L3d6RtJSg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
</head>

<body>
    <?php include("{$_SERVER['DOCUMENT_ROOT']}/includes/nav.php") ?>
    <canvas id="chart" class="mt-5" width="800" height="400"></canvas>
    <script>
        const data = <?php echo json_encode($GLOBALS["candidates"]) ?>;
    </script>
    <script src="/dist/js/showResults.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>

</html>