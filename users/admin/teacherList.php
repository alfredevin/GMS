<?php
include '../../config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require './../../mailer/src/Exception.php';
require './../../mailer/src/PHPMailer.php';
require './../../mailer/src/SMTP.php';

$subject_grade = isset($_GET['grade']) ? $_GET['grade'] : '';

if (isset($_POST['assign_subjects'])) {
    $teacher_id = $_POST['teacher_id'];
    $subject_ids = $_POST['subject_ids'] ?? [];

    $teacher = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM teacher_tbl WHERE teacher_id = '$teacher_id'"));
    $teacher_name = $teacher['teacher_name'];
    $email = $teacher['email'];

    $subject_names = [];
    foreach ($subject_ids as $subject_id) {
        $update = mysqli_query($conn, "UPDATE subject_tbl SET teacher_assign = '$teacher_id' WHERE subject_id = '$subject_id'");

        $subj = mysqli_fetch_assoc(mysqli_query($conn, "SELECT subject_name FROM subject_tbl WHERE subject_id = '$subject_id'"));
        if ($subj) {
            $subject_names[] = $subj['subject_name'];
        }
    }

    $subject_list_html = '<ul>';
    foreach ($subject_names as $subject) {
        $subject_list_html .= "<li>$subject</li>";
    }
    $subject_list_html .= '</ul>';

    // EMAIL SEND
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'highschoolbangbangnational@gmail.com';
        $mail->Password = 'njdvqtbzbgtppobe';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;

        $mail->setFrom('highschoolbangbangnational@gmail.com');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = ' Subjects Assigned To You';

        $mail->Body = "
    <div style='font-family: Arial, sans-serif; font-size: 14px; color: #333;'>
        <p>👋 Good Day <strong style='color: #2c3e50;'>$teacher_name</strong>,</p>

        <p>📚 You have been assigned to the following subjects:</p>

        <table style='width: 100%; border-collapse: collapse;'>
            <thead>
                <tr style='background-color: #f2f2f2;'>
                    <th style='padding: 8px; border: 1px solid #ccc;'>Subject</th>
                    <th style='padding: 8px; border: 1px solid #ccc;'>Grade Level</th>
                </tr>
            </thead>
            <tbody>";

        foreach ($subject_ids as $subject_id) {
            $subject = mysqli_fetch_assoc(mysqli_query($conn, "SELECT subject_name, subject_grade FROM subject_tbl WHERE subject_id = '$subject_id'"));
            $subject_name = $subject['subject_name'];
            $subject_grade = $subject['subject_grade'];

            $mail->Body .= "
        <tr>
            <td style='padding: 8px; border: 1px solid #ccc;'>📘 $subject_name</td>
            <td style='padding: 8px; border: 1px solid #ccc;'>Grade $subject_grade</td>
        </tr>";
        }

        $mail->Body .= "
            </tbody>
        </table>

        <br>
     <p>💡 Teaching is the profession that creates all others — thank you for being the spark of knowledge!</p>
        <br>
        <p style='color: #555;'>Warm regards,<br><strong>GMS Admin</strong></p>
    </div>
";

        $mail->send();

        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'Success!',
                text: 'Subjects assigned successfully and email has been sent.',
                icon: 'success',
                confirmButtonText: 'Okay'
            }).then(() => {
                window.location.href = 'teacherList';
            });
        });
    </script>";
        exit;
    } catch (Exception $e) {
        echo "<script>alert('Email failed to send: " . $mail->ErrorInfo . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<?php include './../template/header.php' ?>

<style>
    /* Custom Card Styling for Subject Selection */
    .subject-card-label {
        display: block;
        cursor: pointer;
        border: 1px solid #e3e6f0;
        border-radius: 0.5rem;
        padding: 10px;
        transition: all 0.2s ease-in-out;
        background-color: #fff;
    }

    .subject-card-label:hover {
        border-color: #4e73df;
        background-color: #f8f9fc;
        transform: translateY(-2px);
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    }

    /* Hide default checkbox but keep functionality */
    .subject-check-input {
        display: none;
    }

    /* Style when checked */
    .subject-check-input:checked+.subject-card-label {
        border-color: #1cc88a;
        background-color: #e8f5e9;
        position: relative;
    }

    .subject-check-input:checked+.subject-card-label::after {
        content: '\f00c';
        /* Check icon */
        font-family: "Font Awesome 5 Free";
        font-weight: 900;
        position: absolute;
        top: 5px;
        right: 10px;
        color: #1cc88a;
    }

    /* Disabled Style */
    .subject-check-input:disabled+.subject-card-label {
        background-color: #eaecf4;
        border-color: #d1d3e2;
        cursor: not-allowed;
        opacity: 0.7;
    }

    .badge-assigned {
        font-size: 0.7rem;
        background-color: #e74a3b;
        color: white;
        padding: 2px 5px;
        border-radius: 3px;
        float: right;
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
                        <h1 class="h3 mb-0 text-gray-800">Teacher Management</h1>
                    </div>

                    <div class="card shadow mb-4">
                        <div class="card-header py-3 bg-gradient-primary text-white d-flex align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold"><i class="fas fa-chalkboard-teacher mr-2"></i> List of Teachers</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>ID</th>
                                            <th>Teacher Name</th>
                                            <th>Type</th>
                                            <th class="text-center">Subjects Assigned</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $sql = "SELECT * FROM teacher_tbl";
                                        $result = mysqli_query($conn, $sql);

                                        while ($res = mysqli_fetch_assoc($result)) {
                                            $tId = $res['teacher_id'];
                                            // Count subjects
                                            $count_query = "SELECT COUNT(*) AS total FROM subject_tbl WHERE teacher_assign = '$tId'";
                                            $count_result = mysqli_query($conn, $count_query);
                                            $count_row = mysqli_fetch_assoc($count_result);
                                            $subject_count = $count_row['total'];

                                            // Badge Color based on count
                                            $badge_color = ($subject_count > 0) ? 'badge-primary' : 'badge-secondary';
                                        ?>
                                            <tr>
                                                <td class="align-middle"><?= $res['teacher_id'] ?></td>
                                                <td class="align-middle font-weight-bold text-dark"><?= $res['teacher_name'] ?></td>
                                                <td class="align-middle">
                                                    <span class="badge badge-info px-2 py-1"><?= $res['teacher_type'] ?></span>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <span class="badge <?= $badge_color ?> px-3 py-2" style="font-size: 1rem;"><?= $subject_count ?></span>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <button class="btn btn-success btn-sm shadow-sm btn-assign-subject mr-1"
                                                        data-toggle="modal"
                                                        data-target="#subjectModal"
                                                        data-teacher-id="<?= $res['teacher_id'] ?>"
                                                        data-teacher-name="<?= htmlspecialchars($res['teacher_name']) ?>">
                                                        <i class="fas fa-plus-circle"></i> Assign
                                                    </button>

                                                    <a href="teacherSubject?teacher_id=<?= $res['teacher_id'] ?>" class="btn btn-primary btn-sm shadow-sm">
                                                        <i class="fas fa-eye"></i> View
                                                    </a>
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

    <div class="modal fade" id="subjectModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">

                <form method="POST" id="assignSubjectsForm">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title font-weight-bold" id="modalTitleDisplay">
                            <i class="fas fa-book-reader mr-2"></i> Assign Subjects
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body bg-light">
                        <div class="alert alert-info border-left-info shadow-sm" role="alert">
                            <i class="fas fa-info-circle mr-1"></i> Select the subjects below to assign. Subjects marked in <strong>grey</strong> are already assigned to other teachers.
                        </div>

                        <input type="hidden" name="teacher_id" id="modalTeacherId">

                        <?php for ($grade = 7; $grade <= 10; $grade++): ?>
                            <div class="card mb-3 border-left-primary shadow-sm">
                                <div class="card-header py-2">
                                    <h6 class="m-0 font-weight-bold text-primary">Grade <?= $grade ?> Subjects</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <?php
                                        $subjects = mysqli_query($conn, "SELECT * FROM subject_tbl WHERE subject_grade = $grade");
                                        while ($subj = mysqli_fetch_assoc($subjects)):
                                            $isAssigned = !empty($subj['teacher_assign']);
                                            $disabled = $isAssigned ? 'disabled' : '';
                                        ?>
                                            <div class="col-md-3 mb-2">
                                                <input class="subject-check-input" type="checkbox" name="subject_ids[]"
                                                    value="<?= $subj['subject_id'] ?>"
                                                    id="subj_<?= $subj['subject_id'] ?>" <?= $disabled ?>>

                                                <label class="subject-card-label w-100" for="subj_<?= $subj['subject_id'] ?>">
                                                    <div class="font-weight-bold text-dark">
                                                        <?= $subj['subject_name'] ?>
                                                    </div>
                                                    <?php if ($isAssigned): ?>
                                                        <div class="mt-2">
                                                            <span class="badge badge-assigned">Taken</span>
                                                        </div>
                                                    <?php endif; ?>
                                                </label>
                                            </div>
                                        <?php endwhile; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endfor; ?>
                    </div>

                    <div class="modal-footer bg-white">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" name="assign_subjects" class="btn btn-success font-weight-bold shadow-sm">
                            <i class="fas fa-save mr-1"></i> Save Assignments
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('.btn-assign-subject').forEach(btn => {
            btn.addEventListener('click', function() {
                const teacherId = this.getAttribute('data-teacher-id');
                const teacherName = this.getAttribute('data-teacher-name');

                // Set hidden input value
                document.getElementById('modalTeacherId').value = teacherId;

                // Update Modal Title
                document.getElementById('modalTitleDisplay').innerHTML = `<i class="fas fa-book-reader mr-2"></i> Assign Subjects to <span style="text-decoration: underline;">${teacherName}</span>`;

                // Show the modal
                $('#subjectModal').modal('show');
            });
        });
    </script>

</body>

</html>