<?php
include '../../config.php';
if (isset($_POST['submit'])) {
    $enrollment_requirement_name = $_POST['enrollment_requirement_name'];
    $check_requirements = mysqli_prepare($conn, "SELECT * FROM enrollment_requirement_tbl WHERE enrollment_requirement_name = ?");
    mysqli_stmt_bind_param($check_requirements, "s", $enrollment_requirement_name);
    mysqli_execute($check_requirements);
    $result_check = mysqli_stmt_get_result($check_requirements);
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
                        title: "Enrollment Name already exists!"
                    });
                });
            </script>';
    } else {
        $insert_requirements = mysqli_prepare($conn, "INSERT INTO enrollment_requirement_tbl (enrollment_requirement_name) VALUES (?)");
        mysqli_stmt_bind_param($insert_requirements, "s", $enrollment_requirement_name);
        if (mysqli_execute($insert_requirements)) {
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
                                title: "Requirements successfully added!"
                            });
                        });
                    </script>';
        }
    }
}


if (isset($_POST['update'])) {
    $enrollment_requirement_id  = $_POST['enrollment_requirement_id'];
    $enrollment_requirement_name = $_POST['enrollment_requirement_name'];
    $check_enrollment_requirements = mysqli_prepare($conn, "SELECT * FROM enrollment_requirement_tbl WHERE enrollment_requirement_name = ? AND enrollment_requirement_id  != ?");
    mysqli_stmt_bind_param($check_enrollment_requirements, "si", $enrollment_requirement_name, $enrollment_requirement_id );
    mysqli_execute($check_enrollment_requirements);
    $result_check = mysqli_stmt_get_result($check_enrollment_requirements);
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
                        title: "Enrollment Name already exists!"
                    });
                });
            </script>';
    } else {
        $update_requirements = mysqli_prepare($conn, "UPDATE enrollment_requirement_tbl SET enrollment_requirement_name = ? WHERE enrollment_requirement_id  = ?");
        mysqli_stmt_bind_param($update_requirements, "si", $enrollment_requirement_name, $enrollment_requirement_id );
        if (mysqli_execute($update_requirements)) {
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
                                title: "Requirements successfully Update!"
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
                                    <h6 class="m-0 font-weight-bold text-primary">List of Enrollment Requirements</h6>
                                </div>
                                <div class="col-">
                                    <a href="#addRoom" data-toggle="modal" class="btn btn-success btn-success btn-sm "><i class="fas fa-plus"></i> Add Requirements</a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Requirements</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $query = "SELECT * FROM enrollment_requirement_tbl";

                                        $result = mysqli_query($conn, $query);

                                        while ($row = mysqli_fetch_assoc($result)) {
                                        ?>
                                            <tr>
                                                <td><?php echo $row['enrollment_requirement_name']; ?></td>
                                                <td>
                                                    <a href="#editRoom<?= $row['enrollment_requirement_id']; ?>" data-toggle="modal" class="btn btn-warning btn-sm">
                                                        <i class="fas fa-pen-square"></i> EDIT
                                                    </a>
                                                </td>
                                            </tr>
                                            <div class="modal fade" id="editRoom<?= $row['enrollment_requirement_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="addRoomLabel" aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="addRoomLabel">Update Requirements</h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <form method="POST">
                                                            <div class="modal-body">
                                                                <input type="hidden" name="enrollment_requirement_id" value="<?= $row['enrollment_requirement_id']; ?>">
                                                                <div class="form-group">
                                                                    <label for="roomName">Requirements Name</label>
                                                                    <input type="text" class="form-control" id="roomName" value="<?php echo $row['enrollment_requirement_name']; ?>" name="enrollment_requirement_name" oninput="this.value = this.value.toUpperCase();" required>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="submit" name="update" class="btn btn-primary">Save Requirement</button>
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
                    <h5 class="modal-title" id="addRoomLabel">Add Requirements</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="roomName">Enrollment Name</label>
                            <input type="text" class="form-control" id="roomName" oninput="this.value = this.value.toUpperCase();" name="enrollment_requirement_name" required>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" name="submit" class="btn btn-primary">Save Requirements</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                </form>

            </div>
        </div>
    </div>

</body>

</html>