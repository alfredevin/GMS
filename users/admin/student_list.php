<?php
include '../../config.php';

// --- HANDLE TRANSFER ---
if (isset($_POST['confirm_transfer'])) {
    $student_id = $_POST['student_id'];
    $reason = mysqli_real_escape_string($conn, $_POST['reason']);
    $school_to = mysqli_real_escape_string($conn, $_POST['school_to']);
    $date = date('Y-m-d');

    // Insert to transferee table
    $insert = "INSERT INTO student_transferee_tbl (student_id, transfer_date, reason, school_to_transfer) 
               VALUES ('$student_id', '$date', '$reason', '$school_to')";

    if (mysqli_query($conn, $insert)) {
        // Set status to 0 (Transferred)
        mysqli_query($conn, "UPDATE student_tbl SET status = 0 WHERE student_id = '$student_id'");
        showAlert("Transferred Successfully", "Student has been moved to transferred list.");
    }
}

// --- HANDLE DROP OUT ---
if (isset($_POST['confirm_dropout'])) {
    $student_id = $_POST['student_id'];
    $reason = mysqli_real_escape_string($conn, $_POST['reason']);
    $date = date('Y-m-d');

    // Insert to dropout table
    $insert = "INSERT INTO student_dropout_tbl (student_id, dropout_date, reason) 
               VALUES ('$student_id', '$date', '$reason')";

    if (mysqli_query($conn, $insert)) {
        // Set status to 2 (Dropped) - Assuming 2 represents Dropped
        mysqli_query($conn, "UPDATE student_tbl SET status = 2 WHERE student_id = '$student_id'");
        showAlert("Dropped Successfully", "Student has been marked as dropped.");
    }
}

// Helper function for alerts
function showAlert($title, $text)
{
    echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
    echo '<script>
        document.addEventListener("DOMContentLoaded", function () {
            Swal.fire({
                icon: "success",
                title: "' . $title . '",
                text: "' . $text . '",
                timer: 2000,
                showConfirmButton: false
            }).then(() => { window.location.href = window.location.href; });
        });
    </script>';
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
                        <div class="card-header py-3 d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-primary">List of Active Students</h6>
                            <div>
                                <a href="transferred_list" class="btn btn-secondary btn-sm mr-1"><i class="fas fa-exchange-alt"></i> Transferred</a>
                                <a href="dropout_list" class="btn btn-danger btn-sm"><i class="fas fa-user-slash"></i> Dropped</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>ID</th>
                                            <th>Student Name</th>
                                            <th>Section</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // Show only Active Students (status = 1)
                                        $sql = "SELECT * FROM student_tbl
                                                INNER JOIN enrollment_tbl ON enrollment_tbl.enrollmentId = student_tbl.enrollment_id
                                                INNER JOIN section_tbl ON section_tbl.section_id = student_tbl.section_id 
                                                WHERE student_tbl.status = 1";
                                        $result = mysqli_query($conn, $sql);

                                        while ($res = mysqli_fetch_assoc($result)) {
                                            $student_id = $res['student_id'];
                                            $fullname = $res['lastname'] . ', ' . $res['firstname'];
                                        ?>
                                            <tr>
                                                <td><?= $res['student_id'] ?></td>
                                                <td class="font-weight-bold text-uppercase"><?= $fullname; ?></td>
                                                <td><?= $res['section_name'] ?></td>
                                                <td class="text-center">
                                                    <button class="btn btn-info btn-sm shadow-sm mr-1"
                                                        data-toggle="modal"
                                                        data-target="#transferModal<?= $student_id ?>" title="Transfer Out">
                                                        <i class="fas fa-exchange-alt"></i>
                                                    </button>

                                                    <button class="btn btn-danger btn-sm shadow-sm"
                                                        data-toggle="modal"
                                                        data-target="#dropModal<?= $student_id ?>" title="Drop Out">
                                                        <i class="fas fa-user-times"></i>
                                                    </button>

                                                    <div class="modal fade" id="transferModal<?= $student_id ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                                        <div class="modal-dialog" role="document">
                                                            <div class="modal-content text-left">
                                                                <div class="modal-header bg-info text-white">
                                                                    <h5 class="modal-title">Transfer Student Out</h5>
                                                                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                                                                </div>
                                                                <form method="POST">
                                                                    <div class="modal-body">
                                                                        <p>Mark <strong><?= $fullname ?></strong> as transferred?</p>
                                                                        <input type="hidden" name="student_id" value="<?= $student_id ?>">
                                                                        <div class="form-group">
                                                                            <label class="font-weight-bold">School Transferring To:</label>
                                                                            <input type="text" name="school_to" class="form-control" required>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label class="font-weight-bold">Reason:</label>
                                                                            <textarea name="reason" class="form-control" rows="2" required></textarea>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                                        <button type="submit" name="confirm_transfer" class="btn btn-info">Confirm Transfer</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="modal fade" id="dropModal<?= $student_id ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                                        <div class="modal-dialog" role="document">
                                                            <div class="modal-content text-left">
                                                                <div class="modal-header bg-danger text-white">
                                                                    <h5 class="modal-title">Drop Student</h5>
                                                                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                                                                </div>
                                                                <form method="POST">
                                                                    <div class="modal-body">
                                                                        <div class="alert alert-warning">
                                                                            <i class="fas fa-exclamation-triangle"></i> Warning: This action will mark the student as Dropped.
                                                                        </div>
                                                                        <p>Are you sure you want to drop <strong><?= $fullname ?></strong>?</p>
                                                                        <input type="hidden" name="student_id" value="<?= $student_id ?>">
                                                                        <div class="form-group">
                                                                            <label class="font-weight-bold">Reason for Dropping:</label>
                                                                            <textarea name="reason" class="form-control" rows="3" required placeholder="e.g. Financial problems, Family issues..."></textarea>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                                        <button type="submit" name="confirm_dropout" class="btn btn-danger">Confirm Drop</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
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
</body>

</html>