<?php
require_once("{$_SERVER['DOCUMENT_ROOT']}/config/db-connection.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/helpers/functions.php");

authed();

$message = null;
$user_inputs = array(
    "title" => null,
    "description" => null,
    "result_visibility" => null,
    "end_date" => null,
    "options" => null
);

function validate_inputs($inputs)
{
    if (!$inputs["title"] || !$inputs["end_date"] || count($inputs["options"]) < 2) {
        return "You must fill all the required fields";
    }

    if ($inputs["result_visibility"] != "public" && $inputs["result_visibility"] != "private") {
        return "Something's wrong I can feel it";
    }
}

function create_poll($connection, $inputs, $owner_id)
{
    $con = $connection;
    $title = $inputs["title"];
    $description = $inputs["description"];
    $result_visibility = $inputs["result_visibility"];
    $end_date = $inputs["end_date"];
    $poll_id = null;

    // Insert data to tables
    try {
        // Start the transaction
        $query = "START TRANSACTION;";
        $connection->query($query);

        // Create the poll
        $query = "INSERT INTO polls(title, `description`, owner_id, end_date, results_visibility)
                    VALUES ('$title', '$description', '$owner_id', '$end_date', '$result_visibility');";
        $connection->query($query);
        
        // Get poll id
        $query = "SELECT LAST_INSERT_ID() AS id";
        $poll_id = mysqli_fetch_assoc($connection->query($query))["id"];

        foreach($inputs["options"] as $idx => $opt) {
            $opt = esc_str($con, $opt);
            $query = "INSERT INTO `candidates` (`name`, poll_id) VALUES ('$opt', $poll_id);";
            $connection->query($query);
        }

        // Commit the changes
        $query = "COMMIT;";
        $connection->query($query);
    } catch (Exception $e) {
        // Try to rollback the changes if there is an error
        $query = "ROLLBACK;";
        $connection->query($query);
        die("Internal Server Error" . $e);
    }

    // Redirect to the newly created poll
    redirectTo("/poll/$poll_id");
}

if (isset($_POST['submit'])) {
    try {
        $con = $connection;
        // Escape user inputs to prevent sqli
        $user_inputs["title"] = esc_str($con, $_POST['title']);
        $user_inputs["description"] = esc_str($con, $_POST['description']);
        $user_inputs["result_visibility"] = esc_str($con, $_POST['result_visibility']);
        $user_inputs["end_date"] = esc_str($con, $_POST['end_date']);
        $user_inputs["options"] = $_POST['options'];

        $message = validate_inputs($user_inputs);
        $owner_id = get_username($con);

        // Create the poll if there is no error message
        if (!$message) {
            create_poll($connection, $user_inputs, $owner_id);
        }
    } catch (Exception $e) {
        die("Internal Server Error" . $e);
    }
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
    <link rel="stylesheet" href="/dist/css/nav.css">
    <title>Heisenberge Polls | Create</title>
</head>

<body style="font-family:'Segoe UI'">
    <?php include("{$_SERVER['DOCUMENT_ROOT']}/includes/nav.php") ?>
    <div class="container">
        <!-- <h3 class="mt-5">Heisenberge Polls</h3> -->
        <h5><?php echo $message ?></h3>
        <form action="/poll/create" method="POST" class="row g-3 mb-4 mt-4">
            <!-- Title -->
            <div class="col-md-12">
                <label for="inputTitle" class="form-label h6">Title</label>
                <input type="text" class="form-control" id="inputTitle" name="title" placeholder="Type your question here" value="<?php echo $user_inputs["title"] ?>">
            </div>
            <!-- Description -->
            <div class="col-md-12">
                <label for="description" class="form-label h6">Description (Optional)</label>
                <textarea id="description" class="form-control rounded-0" name="description" rows="3"><?php echo $user_inputs["description"] ?></textarea>
            </div>
            <!-- Options -->
            <div class="col-12">
                <div class="options">
                    <label for="inputOption1" class="form-label h6">Options</label>
                    <div class="poll-option mb-3">
                        <input type="text" class="form-control" id="inputOption1" name="options[]" placeholder="Option 1">
                        <span class="option-remove-icon">&#10006</span>
                    </div>
                    <div class="poll-option mb-3">
                        <input type="text" class="form-control" id="inputOption2" name="options[]" placeholder="Option 2">
                        <span class="option-remove-icon">&#10006</span>
                    </div>
                </div>
                <button type="button" class="btn btn-outline-primary mb-2" id="add-option-btn">
                    <svg class="-ml-2 mr-1 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" id="plus-symbol">
						<path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"></path>
                    </svg>
                    Add Option
                </button>
            </div>
            <!-- Results Publicity -->
            <div class="col-md-12">
                <div class="form-group">
                    <label for="result-visibility" class="form-label h6">Results Visibility</label>
                    <select class="form-control" id="result-visibility" name="result_visibility">
                        <option value="public">Public</option>
                        <option value="private">Private</option>
                    </select>
                </div>
            </div>
            <!-- End Date -->
            <div class="col-md-12">
                <label for="end-date" class="form-label h6">End date</label>
                <input type="date" id="end-date" name="end_date" class="form-control">
            </div>
            <!-- Create Poll -->
            <div class="col-12 mt-4">
                <button type="submit" class="btn btn-primary" name="submit">Create</button>
            </div>
        </form>
        <script src="/dist/js/create_poll.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    </div>
</body>

</html>