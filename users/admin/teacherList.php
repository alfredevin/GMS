<?php
include '../../config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require './../../mailer/src/Exception.php';
require './../../mailer/src/PHPMailer.php';
require './../../mailer/src/SMTP.php';

if (isset($_POST['assign_subjects'])) {
    $teacher_id = $_POST['teacher_id'];
    $assigned_data = $_POST['subject_section'] ?? [];

    $teacher = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM teacher_tbl WHERE teacher_id = '$teacher_id'"));
    $teacher_name = $teacher['teacher_name'];
    $email = $teacher['email'];

    $subject_names = [];
    $assignment_count = 0;

    // 1. I-clear muna ang mga lumang assignments ng teacher na ito para malinis ang pag-update
    // Lalo na kung may in-uncheck siyang subject
    $current_subs = mysqli_query($conn, "SELECT subject_id FROM subject_tbl WHERE teacher_assign = '$teacher_id'");
    while ($row = mysqli_fetch_assoc($current_subs)) {
        $cid = $row['subject_id'];
        if (!isset($assigned_data[$cid])) {
            // Ibig sabihin in-uncheck ni admin lahat ng section para sa subject na ito
            mysqli_query($conn, "UPDATE subject_tbl SET teacher_assign = NULL, section_id = NULL WHERE subject_id = '$cid'");
        }
    }

    // 2. I-save ang mga bagong check na sections gamit ang JSON
    foreach ($assigned_data as $sub_id => $sections) {
        if (!empty($sections)) {
            // Gawing JSON Format ang array ng section IDs (ex. ["1","2","3"])
            $json_sections = json_encode($sections);

            // I-update ang row ng subject na iyon
            mysqli_query($conn, "UPDATE subject_tbl SET teacher_assign = '$teacher_id', section_id = '$json_sections' WHERE subject_id = '$sub_id'");
            $assignment_count++;

            // Kunin ang pangalan ng subject para sa email
            $sub_info = mysqli_fetch_assoc(mysqli_query($conn, "SELECT subject_name, subject_grade FROM subject_tbl WHERE subject_id = '$sub_id'"));

            // Kunin ang pangalan ng mga sections para sa email
            $sec_names = [];
            foreach ($sections as $sid) {
                $sec_info = mysqli_fetch_assoc(mysqli_query($conn, "SELECT section_name FROM section_tbl WHERE section_id = '$sid'"));
                $sec_names[] = $sec_info['section_name'];
            }
            $joined_sections = implode(", ", $sec_names);

            $subject_names[] = [
                'subject' => $sub_info['subject_name'],
                'grade' => $sub_info['subject_grade'],
                'section' => $joined_sections
            ];
        }
    }

    if ($assignment_count > 0) {
        // EMAIL SEND LOGIC
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
            $mail->Subject = 'New Subjects Assigned To You';

            $mail->Body = "
            <div style='font-family: Arial, sans-serif; font-size: 14px; color: #333;'>
                <p>👋 Good Day <strong style='color: #2c3e50;'>$teacher_name</strong>,</p>
                <p>📚 You have been assigned to the following subjects and sections:</p>
                <table style='width: 100%; border-collapse: collapse;'>
                    <thead>
                        <tr style='background-color: #f2f2f2;'>
                            <th style='padding: 8px; border: 1px solid #ccc;'>Subject</th>
                            <th style='padding: 8px; border: 1px solid #ccc;'>Grade & Sections</th>
                        </tr>
                    </thead>
                    <tbody>";

            foreach ($subject_names as $item) {
                $mail->Body .= "
                <tr>
                    <td style='padding: 8px; border: 1px solid #ccc;'>📘 {$item['subject']}</td>
                    <td style='padding: 8px; border: 1px solid #ccc;'>Grade {$item['grade']} - {$item['section']}</td>
                </tr>";
            }

            $mail->Body .= "
                    </tbody>
                </table>
                <br>
                <p>💡 Teaching is the profession that creates all others — thank you for being the spark of knowledge!</p>
                <br>
                <p style='color: #555;'>Warm regards,<br><strong>GMS Admin</strong></p>
            </div>";

            $mail->send();

            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        title: 'Success!',
                        text: 'Subjects and Sections assigned successfully. Email sent.',
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
    } else {
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Update Saved',
                    text: 'Teacher assignments updated successfully (No active sections mapped).',
                    icon: 'info',
                    confirmButtonText: 'Okay'
                }).then(() => {
                    window.location.href = 'teacherList';
                });
            });
        </script>";
        exit;
    }
}

// Fetch all teachers for displaying "Taken By" info
$teacher_map = [];
$t_res = mysqli_query($conn, "SELECT teacher_id, teacher_name FROM teacher_tbl");
while ($t = mysqli_fetch_assoc($t_res)) {
    $teacher_map[$t['teacher_id']] = $t['teacher_name'];
}
?>

<!DOCTYPE html>
<html lang="en">
<?php include './../template/header.php' ?>

<style>
    .subject-card {
        border: 1px solid #e3e6f0;
        border-radius: 0.5rem;
        padding: 15px;
        margin-bottom: 15px;
        background-color: #fff;
        height: 100%;
    }
    .section-list {
        max-height: 140px;
        overflow-y: auto;
        border: 1px solid #eaecf4;
        padding: 10px;
        border-radius: 5px;
        background-color: #f8f9fc;
    }
    .badge-assigned {
        font-size: 0.65rem;
        padding: 3px 6px;
        border-radius: 3px;
        margin-left: 5px;
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
                        <div class="card-header py-3 bg-gradient-primary text-white">
                            <h6 class="m-0 font-weight-bold"><i class="fas fa-chalkboard-teacher mr-2"></i> List of Teachers</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" id="dataTable" width="100%">
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
                                            $count_query = "SELECT COUNT(*) AS total FROM subject_tbl WHERE teacher_assign = '$tId'";
                                            $count_row = mysqli_fetch_assoc(mysqli_query($conn, $count_query));
                                            $subject_count = $count_row['total'];
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
                                                            <i class="fas fa-plus-circle"></i> Assign Subjects
                                                        </button>
                                                        <a href="teacherSubject?teacher_id=<?= $res['teacher_id'] ?>" class="btn btn-primary btn-sm shadow-sm">
                                                            <i class="fas fa-eye"></i> View Load
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
    
    <?php include './../template/script.php'; ?>

    <div class="modal fade" id="subjectModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <form method="POST" id="assignSubjectsForm">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title font-weight-bold" id="modalTitleDisplay">
                            <i class="fas fa-book-reader mr-2"></i> Assign Subjects
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                    </div>

                    <div class="modal-body bg-light">
                        <input type="hidden" name="teacher_id" id="modalTeacherId">

                        <?php for ($grade = 7; $grade <= 10; $grade++): ?>
                                <?php
                                $sections = [];
                                $sec_res = mysqli_query($conn, "SELECT section_id, section_name FROM section_tbl WHERE section_grade = '$grade'");
                                while ($sec = mysqli_fetch_assoc($sec_res)) {
                                    $sections[] = $sec;
                                }
                                ?>

                                <div class="card mb-3 border-left-primary shadow-sm teacher-grade-card">
                                    <div class="card-header py-2 bg-white">
                                        <h6 class="m-0 font-weight-bold text-primary">Grade <?= $grade ?> Subjects</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <?php
                                            $subjects = mysqli_query($conn, "SELECT * FROM subject_tbl WHERE subject_grade = $grade");
                                            while ($subj = mysqli_fetch_assoc($subjects)):
                                                $sub_id = $subj['subject_id'];

                                                // ITEM 12 FIX: Decode ang JSON section_id ng subject table
                                                $current_sections = [];
                                                if (!empty($subj['section_id'])) {
                                                    $decoded = json_decode($subj['section_id'], true);
                                                    if (is_array($decoded)) {
                                                        $current_sections = $decoded;
                                                    }
                                                }
                                                $current_teacher = $subj['teacher_assign'];
                                                ?>
                                                    <div class="col-md-4 mb-3">
                                                        <div class="subject-card w-100">
                                                    
                                                            <div class="font-weight-bold text-dark mb-2 border-bottom pb-2 d-flex justify-content-between align-items-center">
                                                                <span><i class="fas fa-book text-primary mr-1"></i> <?= htmlspecialchars($subj['subject_name']) ?></span>
                                                                <div class="custom-control custom-checkbox small">
                                                                    <input type="checkbox" class="custom-control-input select-all-btn" id="selectAll_<?= $sub_id ?>" data-target=".chk_<?= $sub_id ?>">
                                                                    <label class="custom-control-label text-primary font-weight-bold" style="cursor:pointer;" for="selectAll_<?= $sub_id ?>">All</label>
                                                                </div>
                                                            </div>
                                                    
                                                            <div class="section-list">
                                                                <?php foreach ($sections as $sec): ?>
                                                                        <?php
                                                                        $sec_id = $sec['section_id'];
                                                                        $is_checked = "";
                                                                        $is_disabled = "";
                                                                        $label_add = "";

                                                                        // Logic kung naka-check na o kinuha na ng iba
                                                                        if (in_array($sec_id, $current_sections)) {
                                                                            // Kapag ang teacher ID (na idadagdag via JS) ay mag-match, iche-check natin ito mamaya sa Frontend.
                                                                            // Pero para i-lock ang kinuhang section ng IBANG teacher:
                                                                            $owner_name = $teacher_map[$current_teacher] ?? 'Unknown';
                                                                            // We set a data attribute to handle checking via JavaScript
                                                                            $label_add = "<span class='badge badge-danger text-white ml-2' style='font-size:10px;' title='Taken by $owner_name'>Taken by $owner_name</span>";
                                                                        }
                                                                        ?>
                                                                        <div class="custom-control custom-checkbox mb-1">
                                                                            <input type="checkbox" class="custom-control-input chk_<?= $sub_id ?> section-checkbox" 
                                                                                   name="subject_section[<?= $sub_id ?>][]" 
                                                                                   value="<?= $sec_id ?>" 
                                                                                   id="sec_<?= $sub_id ?>_<?= $sec_id ?>"
                                                                                   data-owner="<?= $current_teacher ?>"
                                                                                   data-checked-array='<?= json_encode($current_sections) ?>'>
                                                                            <label class="custom-control-label small" style="cursor:pointer;" for="sec_<?= $sub_id ?>_<?= $sec_id ?>">
                                                                                <?= $sec['section_name'] ?> <span class="owner-label"></span>
                                                                            </label>
                                                                        </div>
                                                                <?php endforeach; ?>
                                                            </div>
                                                        </div>
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
        const teacherMap = <?php echo json_encode($teacher_map); ?>;

        document.querySelectorAll('.btn-assign-subject').forEach(btn => {
            btn.addEventListener('click', function() {
                const teacherId = this.getAttribute('data-teacher-id');
                const teacherName = this.getAttribute('data-teacher-name');

                document.getElementById('modalTeacherId').value = teacherId;
                document.getElementById('modalTitleDisplay').innerHTML = `<i class="fas fa-book-reader mr-2"></i> Assign Subjects to <span style="text-decoration: underline;">${teacherName}</span>`;

                // I-reset muna lahat ng checkboxes bago buksan
                $('.section-checkbox').prop('checked', false).prop('disabled', false);
                $('.owner-label').html('');
                $('.select-all-btn').prop('checked', false);

                // Setup ang Checkboxes base sa JSON data na naka-save
                $('.section-checkbox').each(function() {
                    let sec_id = $(this).val();
                    let owner_id = $(this).attr('data-owner');
                    let checked_array = JSON.parse($(this).attr('data-checked-array'));

                    if(checked_array.includes(sec_id)) {
                        if(owner_id === teacherId) {
                            // Pagmamay-ari ng kasalukuyang tinitingnang teacher, i-check automatically
                            $(this).prop('checked', true);
                            $(this).siblings('label').find('.owner-label').html("<span class='badge badge-success text-white ml-2' style='font-size:10px;'>Your Load</span>");
                        } else if (owner_id !== "") {
                            // Pagmamay-ari ng iba, i-disable para di manakaw
                            $(this).prop('disabled', true);
                            let tname = teacherMap[owner_id] || "Other Teacher";
                            $(this).siblings('label').find('.owner-label').html("<span class='badge badge-danger text-white ml-2' style='font-size:10px;'>Taken by " + tname + "</span>");
                        }
                    }
                });

                $('#subjectModal').modal('show');
            });
        });

        // "Select All" Logic
        $('.select-all-btn').on('change', function() {
            var targetClass = $(this).data('target');
            $(targetClass).not(':disabled').prop('checked', $(this).prop('checked'));
        });

        // Auto-uncheck Select All kung may isang section na hindi naka-check
        $('.section-checkbox').on('change', function() {
            var classes = $(this).attr('class').split(' ');
            var targetClass = '.' + classes.find(c => c.startsWith('chk_'));
            var parentSelectAll = $('input[data-target="' + targetClass + '"]');
            
            var allChecked = $(targetClass).not(':disabled').length === $(targetClass + ':checked').length;
            parentSelectAll.prop('checked', allChecked);
        });
    </script>
</body>
</html>