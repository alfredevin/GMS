<?php
include '../../config.php';

if (isset($_POST['submit'])) {
    $event_title = strtoupper(trim($_POST['event_title']));
    $event_description = $_POST['event_description'];
    $event_date = $_POST['event_date'];
    $event_time = $_POST['event_time'];
    $event_place = strtoupper(trim($_POST['event_place']));

    $check_event = mysqli_prepare($conn, "SELECT * FROM school_events_tbl WHERE event_title = ?");
    mysqli_stmt_bind_param($check_event, "s", $event_title);
    mysqli_execute($check_event);
    $result_check = mysqli_stmt_get_result($check_event);

    if (mysqli_num_rows($result_check) > 0) {
        echo '<script>
                document.addEventListener("DOMContentLoaded", function () {
                    Swal.fire({
                        icon: "error",
                        title: "Event title already exists!",
                        toast: true,
                        position: "top-end",
                        showConfirmButton: false,
                        timer: 2000
                    });
                });
            </script>';
    } else {
        $insert_event = mysqli_prepare($conn, "INSERT INTO school_events_tbl (event_title, event_description, event_date, event_time, event_place) VALUES (?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($insert_event, "sssss", $event_title, $event_description, $event_date, $event_time, $event_place);
        if (mysqli_execute($insert_event)) {
            echo '<script>
                document.addEventListener("DOMContentLoaded", function () {
                    Swal.fire({
                        icon: "success",
                        title: "Event successfully added!",
                        toast: true,
                        position: "top-end",
                        showConfirmButton: false,
                        timer: 2000
                    });
                });
            </script>';
        }
    }
}

if (isset($_POST['update'])) {
    $event_id = $_POST['event_id'];
    $event_title = strtoupper(trim($_POST['event_title']));
    $event_description = $_POST['event_description'];
    $event_date = $_POST['event_date'];
    $event_time = $_POST['event_time'];
    $event_place = strtoupper(trim($_POST['event_place']));

    $check_event = mysqli_prepare($conn, "SELECT * FROM school_events_tbl WHERE event_title = ? AND event_id != ?");
    mysqli_stmt_bind_param($check_event, "si", $event_title, $event_id);
    mysqli_execute($check_event);
    $result_check = mysqli_stmt_get_result($check_event);

    if (mysqli_num_rows($result_check) > 0) {
        echo '<script>
                document.addEventListener("DOMContentLoaded", function () {
                    Swal.fire({
                        icon: "error",
                        title: "Event title already exists!",
                        toast: true,
                        position: "top-end",
                        showConfirmButton: false,
                        timer: 2000
                    });
                });
            </script>';
    } else {
        $update_event = mysqli_prepare($conn, "UPDATE school_events_tbl SET event_title = ?, event_description = ?, event_date = ?, event_time = ?, event_place = ? WHERE event_id = ?");
        mysqli_stmt_bind_param($update_event, "sssssi", $event_title, $event_description, $event_date, $event_time, $event_place, $event_id);
        if (mysqli_execute($update_event)) {
            echo '<script>
                document.addEventListener("DOMContentLoaded", function () {
                    Swal.fire({
                        icon: "success",
                        title: "Event successfully updated!",
                        toast: true,
                        position: "top-end",
                        showConfirmButton: false,
                        timer: 2000
                    });
                });
            </script>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<?php include './../template/header.php' ?>

<body id="page-top">
    <div id="wrapper">
        <?php include './../template/sidebar.php' ?>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include './../template/navbar.php'; ?>
                <div class="container-fluid">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <div class="row">
                                <div class="col">
                                    <h6 class="m-0 font-weight-bold text-primary">List of School Events</h6>
                                </div>
                                <div class="col text-right">
                                    <a href="#addEvent" data-toggle="modal" class="btn btn-success btn-sm"><i class="fas fa-plus"></i> Add Event</a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Title</th>
                                            <th>Description</th>
                                            <th>Date</th>
                                            <th>Time</th>
                                            <th>Place</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $query = "SELECT * FROM school_events_tbl ORDER BY event_date ASC";
                                        $result = mysqli_query($conn, $query);

                                        while ($row = mysqli_fetch_assoc($result)) {
                                        ?>
                                            <tr>
                                                <td><?= $row['event_title']; ?></td>
                                                <td><?= $row['event_description']; ?></td>
                                                <td><?= $row['event_date']; ?></td>
                                                <td><?= date('h:i A', strtotime($row['event_time'])); ?></td>
                                                <td><?= $row['event_place']; ?></td>
                                                <td>
                                                    <a href="#editEvent<?= $row['event_id']; ?>" data-toggle="modal" class="btn btn-warning btn-sm">
                                                        <i class="fas fa-pen-square"></i> Edit
                                                    </a>
                                                </td>
                                            </tr>

                                            <!-- Edit Modal -->
                                            <div class="modal fade" id="editEvent<?= $row['event_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="editEventLabel" aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <form method="POST">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Update Event</h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <input type="hidden" name="event_id" value="<?= $row['event_id']; ?>">
                                                                <div class="form-group">
                                                                    <label>Event Title</label>
                                                                    <input type="text" name="event_title" class="form-control" value="<?= $row['event_title']; ?>" oninput="this.value = this.value.toUpperCase();" required>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label>Description</label>
                                                                    <textarea name="event_description" class="form-control" required><?= $row['event_description']; ?></textarea>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label>Date</label>
                                                                    <input type="date" name="event_date" class="form-control" value="<?= $row['event_date']; ?>" required>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label>Time</label>
                                                                    <input type="time" name="event_time" class="form-control" value="<?= $row['event_time']; ?>" required>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label>Place</label>
                                                                    <input type="text" name="event_place" class="form-control" value="<?= $row['event_place']; ?>" oninput="this.value = this.value.toUpperCase();" required>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="submit" name="update" class="btn btn-primary">Save Changes</button>
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php include './../template/footer.php'; ?>
        </div>
    </div>

    <!-- Add Event Modal -->
    <div class="modal fade" id="addEvent" tabindex="-1" role="dialog" aria-labelledby="addEventLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Add School Event</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Event Title</label>
                            <input type="text" name="event_title" class="form-control" oninput="this.value = this.value.toUpperCase();" required>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="event_description" class="form-control" required></textarea>
                        </div>
                        <div class="form-group">
                            <label>Date</label>
                            <input type="date" name="event_date" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Time</label>
                            <input type="time" name="event_time" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Place</label>
                            <input type="text" name="event_place" class="form-control" oninput="this.value = this.value.toUpperCase();" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="submit" class="btn btn-primary">Save Event</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include './../template/script.php'; ?>
</body>
</html>
 