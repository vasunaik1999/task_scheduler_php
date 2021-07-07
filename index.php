<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Sheduler</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            padding: 30px;
        }

        .background-grey {
            background-color: #f6f6f6;
        }
    </style>
</head>

<body>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col">
                    <h1 class="mx-auto text-center">Schedule Management</h1>
                    <a href="view.php" class="btn btn-primary mt-3">View Schedule</a>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="card mt-4">
                        <div class="card-body background-grey">
                            <form action="" method="post">
                                <div class="mt-3">
                                    <label for="date" class="form-label">Date</label>
                                    <input type="date" class="form-control" name="date">
                                </div>
                                <div class="mt-3">
                                    <label for="start_time" class="form-label">From Time</label>
                                    <input type="time" class="form-control" name="start_time">
                                </div>
                                <div class="mt-3">
                                    <label for="end_time" class="form-label">To time</label>
                                    <input type="time" class="form-control" name="end_time">
                                </div>
                                <div class="mt-3">
                                    <label for="comment" class="form-label">Comment</label>
                                    <textarea name="comment" class="form-control" id="comment" rows="3" placeholder="Enter comment..."></textarea>
                                </div>
                                <button type="submit" name="submit" class="btn btn-primary mt-3">Submit</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card mt-4" style="height:100%;">
                        <div class="card-body background-grey">
                            <!-- PHP SCRIPT FOR INSERTING -->
                            <?php
                            $server = "localhost";
                            $username = "root";
                            $password = "root";
                            $dbname = "task_sheduler";

                            $conn = mysqli_connect($server, $username, $password, $dbname, 3307);

                            if ($conn->connect_error) {
                                die("Connection to database failed due to " . $conn->connect_error);
                            }

                            if (isset($_POST['submit'])) {
                                $date = $_POST['date'];
                                $start_time = $_POST['start_time'];
                                $end_time = $_POST['end_time'];
                                $comment = $_POST['comment'];
                                $start_time2 = strtotime($start_time);
                                $end_time2 = strtotime($end_time);


                                if ($start_time2 >= $end_time2) {
                            ?>
                                    <div class="alert alert-danger" role="alert">
                                        Start Time is less than End Time
                                    </div>
                                    <?php
                                    die();
                                }

                                // Retrive Code
                                $flag = 0;
                                $sql1 = "select * from task_sheduler where date = '$date'";
                                if ($result = $conn->query($sql1)) {
                                    while ($row = $result->fetch_assoc()) {
                                        // echo $row['date'] . "<br>";
                                        // echo $row['start_time'] . "<br>";
                                        // echo $row['end_time'] . "<br>";
                                        // echo $row['comment'] . "<br>";


                                        $start_time1 = strtotime($row['start_time']);
                                        $end_time1 = strtotime($row['end_time']);

                                        if (($start_time2 > $start_time1) && ($end_time2 < $end_time1)) {
                                            // Check time is in between start and end time
                                            // echo "1 Time is in between start and end time <br><br>";
                                            $flag = 1;
                                            break;
                                        } elseif (($start_time2 > $start_time1 && $start_time2 < $end_time1) || ($end_time2 > $start_time1 && $end_time2 < $end_time1)) {
                                            // Check start or end time is in between start and end time
                                            //echo "2 ChK start or end Time is in between start and end time <br><br>";
                                            $flag = 1;
                                            break;
                                        } elseif ($start_time2 == $start_time1 || $end_time2 == $end_time1) {
                                            // Check start or end time is at the border of start and end time
                                            //echo "3 ChK start or end Time is at the border of start and end time <br><br>";
                                            $flag = 1;
                                            break;
                                        } elseif ($start_time1 > $start_time2 && $end_time1 < $end_time2) {
                                            // start and end time is in between  the check start and end time.
                                            //echo "4 start and end Time is overlapping  chk start and end time <br><br>";
                                            $flag = 1;
                                            break;
                                        } else {
                                            $flag = 0;
                                        }
                                    }
                                }

                                if ($flag == 0) {
                                    // Insertion Code
                                    $sql = "INSERT INTO `task_sheduler` (`date`,`start_time`, `end_time`, `comment`) VALUES ('$date','$start_time', '$end_time', '$comment')";
                                    if ($conn->query($sql) === TRUE) {
                                    ?>
                                        <div class="alert alert-success" role="alert">
                                            Schedule Booked Successfully
                                        </div>
                                    <?php
                                    } else {
                                        echo "Error: " . $sql . "<br>" . $conn->error;
                                    }
                                } elseif ($flag == 1) {
                                    ?>
                                    <div class="alert alert-warning" role="alert">
                                        This time slot is already booked, Enter some other schedule
                                    </div>
                                    <?php
                                    // echo "This time slot is already booked, Enter some other schedule";
                                    echo "<br><br>Your time slots on {$date} are as follows";

                                    ?>
                                    <div class="row">
                                        <?php
                                        if ($result = $conn->query($sql1)) {
                                            while ($row = $result->fetch_assoc()) {
                                        ?>
                                                <div class="col-md-4">
                                                    <div class="card mt-3">
                                                        <div class="card-body">
                                                            <?php
                                                            // echo $row['date'] . "<br>";
                                                            echo "<strong>{$row['start_time']} " . " to ";
                                                            echo $row['end_time'] . "</strong><br>";
                                                            echo $row['comment'] . "<br>";
                                                            ?>
                                                        </div>
                                                    </div>
                                                </div>
                                        <?php
                                            }
                                        }
                                        ?>
                                    </div>
                            <?php
                                }
                            }
                            ?>


                            <?php
                            $conn->close();

                            ?>


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

<!-- Insert Query -->
<!-- INSERT INTO `task_sheduler` (`id`, `date`, `start_time`, `end_time`, `comment`) VALUES ('1', '2021-07-05', '06:00:00', '06:30:00', 'Gauti meeting'); -->

<!-- if ($result = $conn->query($sql)) {
while ($row = $result->fetch_assoc()) {
echo $row['date'] . "<br>";
echo $row['start_time'] . "<br>";
echo $row['end_time'] . "<br>";
echo $row['comment'] . "<br>";
}
} -->