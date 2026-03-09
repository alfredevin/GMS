<?php
include '../../config.php';
if (isset($_POST['submit'])) {
    $room_name = $_POST['room_name'];
    $check_room = mysqli_prepare($conn, "SELECT * FROM room_tbl WHERE room_name = ?");
    mysqli_stmt_bind_param($check_room, "s", $room_name);
    mysqli_execute($check_room);
    $result_check = mysqli_stmt_get_result($check_room);
    if (mysqli_num_rows($result_check) > 0) {
        echo '<script>
                document.addEventListener("DOMContentLoaded", function () {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: "top-end",
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.addEventListener("mouseenter", Swal.stopTimer)
                            toast.addEventListener("mouseleave", Swal.resumeTimer)
                        }
                    });

                    Toast.fire({
                        icon: "error",
                        title: "Room name already exists!"
                    });
                });
            </script>';
    } else {
        $insert_room = mysqli_prepare($conn, "INSERT INTO room_tbl (room_name) VALUES (?)");
        mysqli_stmt_bind_param($insert_room, "s", $room_name);
        if (mysqli_execute($insert_room)) {
            echo '<script>
                        document.addEventListener("DOMContentLoaded", function () {
                            const Toast = Swal.mixin({
                                toast: true,
                                position: "top-end",
                                showConfirmButton: false,
                                timer: 2000,
                                timerProgressBar: true,
                                didOpen: (toast) => {
                                    toast.addEventListener("mouseenter", Swal.stopTimer)
                                    toast.addEventListener("mouseleave", Swal.resumeTimer)
                                }
                            });

                            Toast.fire({
                                icon: "success",
                                title: "Room successfully added!"
                            });
                        });
                    </script>';
        }
    }
}


if (isset($_POST['update'])) {
    $room_id = $_POST['room_id'];
    $room_name = $_POST['room_name'];
    $check_room = mysqli_prepare($conn, "SELECT * FROM room_tbl WHERE room_name = ? AND room_id != ?");
    mysqli_stmt_bind_param($check_room, "si", $room_name, $room_id);
    mysqli_execute($check_room);
    $result_check = mysqli_stmt_get_result($check_room);
    if (mysqli_num_rows($result_check) > 0) {
        echo '<script>
                document.addEventListener("DOMContentLoaded", function () {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: "top-end",
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.addEventListener("mouseenter", Swal.stopTimer)
                            toast.addEventListener("mouseleave", Swal.resumeTimer)
                        }
                    });

                    Toast.fire({
                        icon: "error",
                        title: "Room name already exists!"
                    });
                });
            </script>';
    } else {
        $update_room = mysqli_prepare($conn, "UPDATE room_tbl SET room_name = ? WHERE room_id = ?");
        mysqli_stmt_bind_param($update_room, "si", $room_name, $room_id);
        if (mysqli_execute($update_room)) {
            echo '<script>
                        document.addEventListener("DOMContentLoaded", function () {
                            const Toast = Swal.mixin({
                                toast: true,
                                position: "top-end",
                                showConfirmButton: false,
                                timer: 2000,
                                timerProgressBar: true,
                                didOpen: (toast) => {
                                    toast.addEventListener("mouseenter", Swal.stopTimer)
                                    toast.addEventListener("mouseleave", Swal.resumeTimer)
                                }
                            });

                            Toast.fire({
                                icon: "success",
                                title: "Room successfully Update!"
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
                                    <h6 class="m-0 font-weight-bold text-primary">List of Room</h6>
                                </div>
                                <div class="col-">
                                    <a href="#addRoom" data-toggle="modal" class="btn btn-success btn-success btn-sm "><i class="fas fa-plus"></i> Add Room</a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Room</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $query = "SELECT * FROM room_tbl";

                                        $result = mysqli_query($conn, $query);

                                        while ($row = mysqli_fetch_assoc($result)) {
                                        ?>
                                            <tr>
                                                <td><?php echo $row['room_name']; ?></td>
                                                <td>
                                                    <a href="#editRoom<?= $row['room_id']; ?>" data-toggle="modal" class="btn btn-warning btn-sm">
                                                        <i class="fas fa-pen-square"></i> EDIT
                                                    </a>
                                                </td>
                                            </tr>
                                            <div class="modal fade" id="editRoom<?= $row['room_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="addRoomLabel" aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="addRoomLabel">Update Room</h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <form method="POST">
                                                            <div class="modal-body">
                                                                <input type="hidden" name="room_id" value="<?= $row['room_id']; ?>">
                                                                <div class="form-group">
                                                                    <label for="roomName">Room Name</label>
                                                                    <input type="text" class="form-control" id="roomName" value="<?php echo $row['room_name']; ?>" name="room_name" oninput="this.value = this.value.toUpperCase();" required>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="submit" name="update" class="btn btn-primary">Save Room</button>
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
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>
    <?php include './../template/script.php'; ?>
    <div class="modal fade" id="addRoom" tabindex="-1" role="dialog" aria-labelledby="addRoomLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addRoomLabel">Add Room</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="roomName">Room Name</label>
                            <input type="text" class="form-control" id="roomName" oninput="this.value = this.value.toUpperCase();" name="room_name" required>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" name="submit" class="btn btn-primary">Save Room</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                </form>

            </div>
        </div>
    </div>

</body>

</html>