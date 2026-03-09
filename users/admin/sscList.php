<?php
include '../../config.php';

// --- REMOVE FROM SSC LOGIC ---
if (isset($_POST['remove_ssc'])) {
    $student_id = $_POST['student_id'];

    // Set is_ssc back to 0
    $update_sql = "UPDATE student_tbl SET is_ssc = 0 WHERE student_id = '$student_id'";

    if (mysqli_query($conn, $update_sql)) {
        echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
        echo '<script>
            document.addEventListener("DOMContentLoaded", function () {
                Swal.fire({
                    icon: "success",
                    title: "Removed",
                    text: "Student removed from Special Science Class list.",
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => { window.location.href = window.location.href; });
            });
        </script>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<?php include './../template/header.php' ?>

<style>
    .avatar-initials {
        width: 40px;
        height: 40px;
        background: linear-gradient(to right, #1cc88a, #13855c);
        /* Green Gradient for SSC */
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 16px;
        margin-right: 12px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .student-info {
        display: flex;
        align-items: center;
    }

    @media print {
        .no-print {
            display: none !important;
        }

        .card {
            border: none !important;
            box-shadow: none !important;
        }

        .card-header {
            display: none !important;
        }

        table {
            width: 100%;
            border: 1px solid black;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid black;
            padding: 5px;
        }
    }
</style>

<body id="page-top">
    <div id="wrapper">
        <?php include './../template/sidebar.php' ?>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include './../template/navbar.php'; ?>
                <div class="container-fluid">

                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Special Science Class (SSC) Masterlist</h1>
                        <a href="print_ssc_list.php" target="_blank" class="d-none d-sm-inline-block btn btn-sm btn-success shadow-sm">
                            <i class="fas fa-print fa-sm text-white-50"></i> Print Official List
                        </a>
                    </div>

                    <div class="card shadow mb-4 border-left-success">
                        <div class="card-header py-3 d-flex justify-content-between align-items-center bg-white">
                            <h6 class="m-0 font-weight-bold text-success">
                                <i class="fas fa-atom mr-2"></i> Qualified Students
                            </h6>
                            <?php
                            // Count Total SSC Students
                            $count_sql = mysqli_query($conn, "SELECT count(*) as total FROM student_tbl WHERE status = 1 AND is_ssc = 1");
                            $count = mysqli_fetch_assoc($count_sql)['total'];
                            ?>
                            <span class="badge badge-success px-3 py-2">Total: <?= $count ?></span>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover" id="dataTable" width="100%" cellspacing="0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Student Name</th>
                                            <th>Original Section</th>
                                            <th>Gender</th>
                                            <th class="text-center no-print">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // Query only students with is_ssc = 1
                                        $sql = "SELECT *, section_tbl.section_name 
                                                FROM student_tbl
                                                INNER JOIN enrollment_tbl ON enrollment_tbl.enrollmentId = student_tbl.enrollment_id
                                                INNER JOIN section_tbl ON section_tbl.section_id = student_tbl.section_id 
                                                WHERE student_tbl.status = 1 AND student_tbl.is_ssc = 1
                                                ORDER BY lastname ASC";

                                        $result = mysqli_query($conn, $sql);

                                        if (mysqli_num_rows($result) > 0) {
                                            while ($row = mysqli_fetch_assoc($result)) {
                                                $stud_id = $row['student_id'];
                                                $fullname = strtoupper($row['lastname'] . ', ' . $row['firstname']);
                                                $initial = substr($row['firstname'], 0, 1);
                                        ?>
                                                <tr>
                                                    <td>
                                                        <div class="student-info">
                                                            <div class="avatar-initials"><?= $initial ?></div>
                                                            <div>
                                                                <div class="font-weight-bold text-dark"><?= $fullname ?></div>
                                                                <small class="text-muted">LRN: <?= $row['lrn'] ?></small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-light border text-dark"><?= $row['section_name'] ?></span>
                                                    </td>
                                                    <td><?= $row['sex'] ?></td>

                                                    <td class="text-center no-print">
                                                        <button class="btn btn-outline-danger btn-sm"
                                                            data-toggle="modal"
                                                            data-target="#removeModal<?= $stud_id ?>"
                                                            title="Remove from SSC">
                                                            <i class="fas fa-times"></i> Remove
                                                        </button>

                                                        <div class="modal fade" id="removeModal<?= $stud_id ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header bg-danger text-white">
                                                                        <h5 class="modal-title"><i class="fas fa-exclamation-triangle"></i> Confirm Removal</h5>
                                                                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <form method="POST">
                                                                        <div class="modal-body text-left">
                                                                            <p>Are you sure you want to remove <strong><?= $fullname ?></strong> from the Special Science Class list?</p>
                                                                            <input type="hidden" name="student_id" value="<?= $stud_id ?>">
                                                                            <small class="text-muted">This will allow the student to be added again if needed.</small>
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                                            <button type="submit" name="remove_ssc" class="btn btn-danger">Yes, Remove</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                        <?php
                                            }
                                        } else {
                                            echo "<tr><td colspan='4' class='text-center py-4 text-muted'>No students found in Special Science Class.</td></tr>";
                                        }
                                        ?>
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