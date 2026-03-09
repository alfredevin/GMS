<?php
include '../../config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Adjust paths based on your folder structure
require './../../mailer/src/Exception.php';
require './../../mailer/src/PHPMailer.php';
require './../../mailer/src/SMTP.php';

if (isset($_POST['qualify_student'])) {
    $student_id = $_POST['student_id'];
    $grade_average = floatval($_POST['grade_average']);

    // 1. Fetch Student Email and Details
    $sql_fetch = "SELECT student_tbl.*, new_users.email 
                  FROM student_tbl
                  INNER JOIN enrollment_tbl ON student_tbl.enrollment_id = enrollment_tbl.enrollmentId
                  LEFT JOIN new_users ON enrollment_tbl.user_ids = new_users.new_users_id
                  WHERE student_tbl.student_id = '$student_id'";

    $query_fetch = mysqli_query($conn, $sql_fetch);

    if (mysqli_num_rows($query_fetch) > 0) {
        $student_data = mysqli_fetch_assoc($query_fetch);
        $student_email = $student_data['email'];
        $student_name = $student_data['firstname'] . ' ' . $student_data['lastname'];

        // 2. Update Database (Set is_ssc to 1)
        $update_sql = "UPDATE student_tbl SET is_ssc = 1 WHERE student_id = '$student_id'";

        if (mysqli_query($conn, $update_sql)) {

            // 3. Send Email Notification
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'highschoolbangbangnational@gmail.com';
                $mail->Password = 'njdvqtbzbgtppobe';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                $mail->Port = 465;

                $mail->setFrom('highschoolbangbangnational@gmail.com', 'BNHS Registrar');
                $mail->addAddress($student_email);

                $mail->isHTML(true);
                $mail->Subject = 'Congratulations! Special Science Class Qualification';
                $mail->Body = "
                    <div style='font-family: Arial, sans-serif; padding: 20px; border: 1px solid #ddd; border-radius: 10px; background-color: #f9f9f9;'>
                        <div style='text-align: center; margin-bottom: 20px;'>
                            <h2 style='color: #1cc88a;'>Congratulations!</h2>
                            <h3 style='color: #5a5c69;'>$student_name</h3>
                        </div>
                        <p>We are pleased to inform you that based on your outstanding academic performance, you have qualified for the <strong>Special Science Class (SSC)</strong> program.</p>
                        
                        <div style='background-color: #fff; padding: 15px; border-left: 5px solid #1cc88a; margin: 20px 0;'>
                            <p style='margin: 0;'><strong>Evaluated Grade Average:</strong> $grade_average%</p>
                            <p style='margin: 5px 0 0;'><strong>Status:</strong> QUALIFIED</p>
                        </div>

                        <p>Please visit the registrar's office or reply to this email for the next steps regarding your section assignment.</p>
                        <br>
                        <p style='font-size: 12px; color: #888;'>Best regards,<br>Bangbang National High School Administration</p>
                    </div>
                ";

                $mail->send();

                echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
                echo '<script>
                    document.addEventListener("DOMContentLoaded", function () {
                        Swal.fire({
                            icon: "success",
                            title: "Student Added to SSC!",
                            text: "Database updated and email notification sent successfully.",
                            confirmButtonColor: "#1cc88a"
                        }).then(() => { window.location.href = window.location.href; });
                    });
                </script>';
            } catch (Exception $e) {
                echo '<script>alert("Database updated but Email failed. Error: ' . $mail->ErrorInfo . '");</script>';
            }
        } else {
            echo '<script>alert("Database Error: Failed to update student status.");</script>';
        }
    } else {
        echo '<script>alert("Student record not found.");</script>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<?php include './../template/header.php' ?>

<style>
    /* Custom Styles for User Friendliness */
    .avatar-circle {
        width: 45px;
        height: 45px;
        background-color: #4e73df;
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 18px;
        margin-right: 15px;
    }

    .student-row {
        display: flex;
        align-items: center;
    }

    .table td {
        vertical-align: middle !important;
    }

    .bg-ssc {
        background-color: #e8f5e9;
    }

    /* Light green row for SSC students */
</style>

<body id="page-top">
    <div id="wrapper">
        <?php include './../template/sidebar.php' ?>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include './../template/navbar.php'; ?>
                <div class="container-fluid">

                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Special Science Class (SSC) Selection</h1>
                    </div>

                    <div class="card shadow mb-4">
                        <div class="card-header py-3 bg-gradient-primary text-white d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold"><i class="fas fa-flask mr-2"></i> List of Eligible Students</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Student Name</th>
                                            <th>Current Section</th>
                                            <th>Gender</th>
                                            <th class="text-center" width="20%">Action / Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // Fetch active students
                                        // Ensure to select 'is_ssc' column
                                        $sql = "SELECT student_tbl.*, section_tbl.section_name ,enrollment_tbl.lastname,enrollment_tbl.firstname,enrollment_tbl.sex
                                                FROM student_tbl
                                                INNER JOIN enrollment_tbl ON enrollment_tbl.enrollmentId = student_tbl.enrollment_id
                                                INNER JOIN section_tbl ON section_tbl.section_id = student_tbl.section_id 
                                                WHERE student_tbl.status = 1
                                                ORDER BY is_ssc DESC, lastname ASC"; // Show SSC students first or last

                                        $result = mysqli_query($conn, $sql);

                                        while ($res = mysqli_fetch_assoc($result)) {
                                            $student_id = $res['student_id'];
                                            $fullname = strtoupper($res['lastname'] . ', ' . $res['firstname']);
                                            $initial = substr($res['firstname'], 0, 1);

                                            // Check if already SSC (Default to 0 if column is null)
                                            $is_ssc = $res['is_ssc'] ?? 0;
                                            $row_class = ($is_ssc == 1) ? 'bg-ssc' : '';
                                        ?>
                                            <tr class="<?= $row_class ?>">
                                                <td>
                                                    <div class="student-row">
                                                        <div class="avatar-circle" style="background-color: <?= ($is_ssc == 1) ? '#1cc88a' : '#4e73df' ?>;">
                                                            <?= $initial ?>
                                                        </div>
                                                        <div>
                                                            <div class="font-weight-bold text-dark"><?= $fullname ?></div>
                                                            <small class="text-muted">ID: <?= $student_id ?></small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td><?= $res['section_name'] ?></td>
                                                <td><?= $res['sex'] ?></td>

                                                <td class="text-center">
                                                    <?php if ($is_ssc == 1): ?>
                                                        <span class="badge badge-success px-3 py-2 shadow-sm" style="font-size: 12px;">
                                                            <i class="fas fa-check-circle mr-1"></i> Already SSC
                                                        </span>
                                                    <?php else: ?>
                                                        <button class="btn btn-primary btn-sm shadow-sm px-3"
                                                            data-toggle="modal"
                                                            data-target="#qualifyModal<?= $student_id ?>">
                                                            <i class="fas fa-plus mr-1"></i> Add to SSC
                                                        </button>

                                                        <div class="modal fade" id="qualifyModal<?= $student_id ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header bg-primary text-white">
                                                                        <h5 class="modal-title"><i class="fas fa-star mr-2"></i> Qualify for Special Science</h5>
                                                                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <form method="POST">
                                                                        <div class="modal-body text-left p-4">
                                                                            <div class="text-center mb-4">
                                                                                <h5 class="font-weight-bold text-gray-800"><?= $fullname ?></h5>
                                                                                <p class="text-muted">Assigning to Special Science Class</p>
                                                                            </div>

                                                                            <input type="hidden" name="student_id" value="<?= $student_id ?>">

                                                                            <div class="form-group">
                                                                                <label class="font-weight-bold text-gray-700">Science / General Average:</label>
                                                                                <div class="input-group">
                                                                                    <input type="number" name="grade_average" class="form-control form-control-lg" step="0.01" min="85" max="100" required placeholder="85.00">
                                                                                    <div class="input-group-append">
                                                                                        <span class="input-group-text font-weight-bold">%</span>
                                                                                    </div>
                                                                                </div>
                                                                                <small class="form-text text-muted">Student must have a minimum grade of 85%.</small>
                                                                            </div>

                                                                            <div class="alert alert-warning border-0 shadow-sm" role="alert" style="font-size: 12px;">
                                                                                <i class="fas fa-envelope mr-1"></i>
                                                                                An email notification will be sent to the student immediately.
                                                                            </div>
                                                                        </div>
                                                                        <div class="modal-footer bg-light">
                                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                                            <button type="submit" name="qualify_student" class="btn btn-success font-weight-bold">
                                                                                Confirm & Add <i class="fas fa-arrow-right ml-1"></i>
                                                                            </button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>
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