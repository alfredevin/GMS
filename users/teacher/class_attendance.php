<?php
include '../../config.php';

// 1. DYNAMIC DATE LOGIC
// Kapag may piniling date sa form, yun ang gagamitin. Kung wala, 'Today' ang default.
$filter_date = isset($_GET['date']) ? $_GET['date'] : date("Y-m-d");
?>

<!DOCTYPE html>
<html lang="en">
<?php include './../template/header.php' ?>

<style>
    /* Kulay para sa Attendance Status */
    .status-present {
        border-left: 5px solid #1cc88a !important;
        color: #1cc88a;
        font-weight: bold;
        background-color: #f0fdf4;
    }

    .status-absent {
        border-left: 5px solid #e74a3b !important;
        color: #e74a3b;
        font-weight: bold;
        background-color: #fef2f2;
    }

    .status-late {
        border-left: 5px solid #f6c23e !important;
        color: #f6c23e;
        font-weight: bold;
        background-color: #fffbeb;
    }

    .status-excused {
        border-left: 5px solid #36b9cc !important;
        color: #36b9cc;
        font-weight: bold;
        background-color: #f0f9ff;
    }

    /* Round corners for inputs */
    .custom-select-status {
        border-radius: 20px;
        text-align-last: center;
        font-weight: 600;
        cursor: pointer;
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
                        <h1 class="h3 mb-0 text-gray-800">Class Attendance</h1>

                        <form class="form-inline bg-white p-2 rounded shadow-sm" method="GET">
                            <label class="mr-2 font-weight-bold text-gray-700"><i class="fas fa-calendar-alt mr-1"></i> Date:</label>
                            <input type="date" name="date" class="form-control border-0 bg-light font-weight-bold"
                                value="<?= $filter_date ?>" onchange="this.form.submit()">
                        </form>
                    </div>

                    <div class="card shadow mb-4 border-left-primary">
                        <div class="card-body py-2 d-flex align-items-center justify-content-between flex-wrap">
                            <span class="font-weight-bold text-primary m-1">Quick Actions:</span>
                            <div>
                                <button onclick="markAll('am', 'Present')" class="btn btn-sm btn-success shadow-sm m-1">
                                    <i class="fas fa-check-double"></i> Mark All AM Present
                                </button>
                                <button onclick="markAll('pm', 'Present')" class="btn btn-sm btn-success shadow-sm m-1">
                                    <i class="fas fa-check-double"></i> Mark All PM Present
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-primary">Student List - <span class="text-dark"><?= date("F d, Y", strtotime($filter_date)) ?></span></h6>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover" id="dataTable">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Student Name</th>
                                            <th class="text-center" width="20%">AM Attendance</th>
                                            <th class="text-center" width="20%">PM Attendance</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <?php
                                        // YOUR ORIGINAL QUERY LOGIC (Updated to use $filter_date instead of $date_today)

                                        $select_tec_class = "SELECT * FROM teacher_tbl WHERE teacher_type='Class Adviser' AND teacher_id='$teacher_id'";
                                        $res_tclass = mysqli_query($conn, $select_tec_class);
                                        $tc = mysqli_fetch_assoc($res_tclass);

                                        if ($tc) {
                                            $grade_class = $tc["grade_level"];
                                            $section_class = $tc["section_id"];

                                            // Get Students
                                            $sql = "SELECT *, section_tbl.section_name FROM student_tbl
                                                INNER JOIN enrollment_tbl ON enrollment_tbl.enrollmentId = student_tbl.enrollment_id
                                                INNER JOIN section_tbl ON section_tbl.section_id = student_tbl.section_id
                                                WHERE student_grade='$grade_class' AND student_tbl.section_id='$section_class'
                                                ORDER BY enrollment_tbl.lastname ASC"; // Added Sort A-Z

                                            $result = mysqli_query($conn, $sql);

                                            while ($row = mysqli_fetch_assoc($result)):
                                                $student_id = $row['student_id'];

                                                // Get Attendance based on FILTER DATE
                                                $att = mysqli_query($conn, "SELECT * FROM attendance_tbl WHERE student_id='$student_id' AND attendance_date='$filter_date'");
                                                $attData = mysqli_fetch_assoc($att);

                                                $am_status = $attData['am_status'] ?? '';
                                                $pm_status = $attData['pm_status'] ?? '';
                                        ?>
                                                <tr>
                                                    <td class="align-middle">
                                                        <div class="d-flex align-items-center">
                                                            <div class="btn-circle btn-sm btn-primary mr-3 font-weight-bold d-flex justify-content-center align-items-center" style="width: 35px; height: 35px;">
                                                                <?= strtoupper(substr($row['firstname'], 0, 1) . substr($row['lastname'], 0, 1)) ?>
                                                            </div>
                                                            <div>
                                                                <div class="font-weight-bold text-gray-800"><?= $row['lastname'] . ", " . $row['firstname'] ?></div>
                                                                <div class="small text-gray-500"><?= $row['student_id'] ?></div>
                                                            </div>
                                                        </div>
                                                    </td>

                                                    <td class="text-center align-middle">
                                                        <select class="form-control custom-select-status attendance-select am-select"
                                                            data-id="<?= $student_id ?>"
                                                            data-type="am"
                                                            onchange="updateColor(this)">
                                                            <option value="" <?= $am_status == "" ? "selected" : "" ?>>--</option>
                                                            <option value="Present" <?= $am_status == "Present" ? "selected" : "" ?>>Present</option>
                                                            <option value="Absent" <?= $am_status == "Absent" ? "selected" : "" ?>>Absent</option>
                                                            <option value="Late" <?= $am_status == "Late" ? "selected" : "" ?>>Late</option>
                                                            <option value="Excused" <?= $am_status == "Excused" ? "selected" : "" ?>>Excused</option>
                                                        </select>
                                                    </td>

                                                    <td class="text-center align-middle">
                                                        <select class="form-control custom-select-status attendance-select pm-select"
                                                            data-id="<?= $student_id ?>"
                                                            data-type="pm"
                                                            onchange="updateColor(this)">
                                                            <option value="" <?= $pm_status == "" ? "selected" : "" ?>>--</option>
                                                            <option value="Present" <?= $pm_status == "Present" ? "selected" : "" ?>>Present</option>
                                                            <option value="Absent" <?= $pm_status == "Absent" ? "selected" : "" ?>>Absent</option>
                                                            <option value="Late" <?= $pm_status == "Late" ? "selected" : "" ?>>Late</option>
                                                            <option value="Excused" <?= $pm_status == "Excused" ? "selected" : "" ?>>Excused</option>
                                                        </select>
                                                    </td>

                                                    <td class="text-center align-middle">
                                                        <a href="attendance_record_student.php?student_id=<?= $student_id ?>" class="btn btn-info btn-sm btn-circle shadow-sm" title="View History">
                                                            <i class="fas fa-history"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                        <?php
                                            endwhile;
                                        } else {
                                            echo "<tr><td colspan='4' class='text-center text-danger p-5'><h5>You are not assigned as a Class Adviser.</h5></td></tr>";
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
</body>

<?php include './../template/script.php'; ?>

<script>
    // 1. Color Coding Logic (Runs on Load & Change)
    $(document).ready(function() {
        $(".attendance-select").each(function() {
            updateColor(this); // Set initial colors based on DB values
        });
    });

    function updateColor(selectElement) {
        var value = $(selectElement).val();
        // Reset classes
        $(selectElement).removeClass("status-present status-absent status-late status-excused");

        // Add specific class
        if (value === "Present") $(selectElement).addClass("status-present");
        else if (value === "Absent") $(selectElement).addClass("status-absent");
        else if (value === "Late") $(selectElement).addClass("status-late");
        else if (value === "Excused") $(selectElement).addClass("status-excused");
    }

    // 2. Mark All Function (Para sa Buttons)
    function markAll(type, status) {
        // Confirm muna baka mapindot lang
        Swal.fire({
            title: 'Mark all as ' + status + '?',
            text: "This will update all " + type.toUpperCase() + " records for this date.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, Proceed'
        }).then((result) => {
            if (result.isConfirmed) {
                // Loop lahat ng select box na may class na .am-select or .pm-select
                $("." + type + "-select").each(function() {
                    var currentVal = $(this).val();
                    // Update lang kung iba ang value para bawas load sa server
                    if (currentVal !== status) {
                        $(this).val(status).trigger('change'); // Trigger change para mag-save
                        updateColor(this);
                    }
                });
            }
        });
    }

    // 3. Auto-Save AJAX (Toast Style)
    $(document).on("change", ".attendance-select", function() {
        let studentId = $(this).data("id");
        let type = $(this).data("type"); // am or pm
        let status = $(this).val();
        let date = "<?= $filter_date ?>"; // Gamitin ang selected date sa PHP

        $.post("save_attendance.php", {
            student_id: studentId,
            type: type,
            status: status,
            date: date
        }, function(response) {
            // Gumamit ng TOAST (Maliit sa gilid) instead na Modal
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 1500,
                timerProgressBar: false,
            });

            Toast.fire({
                icon: 'success',
                title: 'Saved'
            });
        });
    });
</script>

</html>