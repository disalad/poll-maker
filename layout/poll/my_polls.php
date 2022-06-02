<?php
require_once("{$_SERVER['DOCUMENT_ROOT']}/config/db-connection.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/helpers/functions.php");

authed();

function fetch_data($con, $u_id)
{
    $query = "SELECT *, DATEDIFF(end_date, CURDATE()) AS DaysLeft FROM polls
            WHERE owner_id = $u_id";
    $res = $con->query($query);

    // Set the global variable if there is any polls
    if (mysqli_num_rows($res) >= 1) {
        $GLOBALS["polls"] = [];
    }

    while ($row = mysqli_fetch_assoc($res)) {
        $p_id = $row["id"];

        // Get total number of votes for the poll
        $query = "SELECT COUNT(*) AS vote_count FROM votes
                WHERE poll_id = $p_id";
        $res = $con->query($query);
        $vote_count = mysqli_fetch_assoc($res)["vote_count"];

        // Set the voting count in the poll array
        $row["vote_count"] = $vote_count;

        // Push the poll data to the global variable
        array_push($GLOBALS["polls"], $row);
    }
    // echo '<pre>', print_r($GLOBALS["polls"], 1), '</pre>';
}

if ($_SERVER['REQUEST_METHOD'] === "GET") {
    $u_id = get_username($connection);
    fetch_data($connection, $u_id);
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
    <link rel="stylesheet" href="/dist/css/my_polls.css">
    <script src="https://kit.fontawesome.com/268afe50de.js" crossorigin="anonymous"></script>
</head>

<body>
    <?php include("{$_SERVER['DOCUMENT_ROOT']}/includes/nav.php") ?>
    <div class="container mt-5">
        <?php if (!isset($GLOBALS["polls"])) : ?>
            <h4>You haven't created any polls yet! <a href="/poll/create">Start Creating</a></h4>
        <?php else : ?>
            <?php foreach ($GLOBALS["polls"] as $idx => $val) : ?>
                <?php if ($idx > 0): ?>
                    <hr/>
                <?php endif; ?>
                <article class="poll">
                    <div class="poll-info">
                        <a class="poll-title h3" href="/poll/<?php echo $val["id"] ?>/"><?php echo $val["title"] ?></a>
                    </div>
                    <div class="timer mt-3 mb-3">
                        <i class="fa-solid fa-clock"></i>
                        <?php if ($val["DaysLeft"] >= 0) : ?>
                            Voting ends in <?php echo $val["DaysLeft"] + 1 ?> days
                        <?php else: ?>
                            Voting has ended
                        <?php endif; ?>
                    </div>
                    <div class="poll-data">
                        <p class="votes-count">
                            <i class="fa-solid fa-circle-check"></i>
                            <?php echo $val["vote_count"] ?>
                        </p>
                        <i class="fa-solid fa-trash-can" data-poll-id="<?php echo $val["id"] ?>"></i>
                        <div class="share-btn">
                            <i class="fa-solid fa-share-nodes" data-poll-id="<?php echo $val["id"] ?>"></i>
                            <div class="tooltiptext">
                                <span>Copied to Clipboard</span>
                            </div>
                        </div>
                    </div>
                </article>
            <?php endforeach ?>
        <?php endif; ?>
    </div>
    <script src="/dist/js/myPolls.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>

</html>