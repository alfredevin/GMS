<?php
include '../../config.php';

// Initialize feedback variables
$swal_icon = "";
$swal_title = "";
$show_swal = false;

if (isset($_POST['submit'])) {
    // Collect Data
    $subject = $_POST['subject'];
    $section = $_POST['section'];
    $type = $_POST['type'];
    $date = $_POST['date'];
    $teacher_id = $_POST['teacher_id'];
    $quarterly = $_POST['quarterly'];
    $items = $_POST['items'];
    $time = $_POST['time'];
    $grade = $_POST['grade'];
    $room = $_POST['room'];

    // 1. PREVENT DUPLICATES (Using Prepared Statement)
    $check_stmt = $conn->prepare("SELECT * FROM announcement_tbl WHERE date = ? AND subject = ? AND section = ? AND type = ? AND teacher_id = ? AND quarterly = ?");
    $check_stmt->bind_param("ssssss", $date, $subject, $section, $type, $teacher_id, $quarterly);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        $show_swal = true;
        $swal_icon = "warning";
        $swal_title = "Announcement already exists for this date/class!";
    } else {
        // 2. INSERT DATA (Using Prepared Statement)
        $insert_stmt = $conn->prepare("INSERT INTO announcement_tbl (subject, section, type, date, teacher_id, quarterly, items, grade, time, room) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $insert_stmt->bind_param("ssssssssss", $subject, $section, $type, $date, $teacher_id, $quarterly, $items, $grade, $time, $room);

        if ($insert_stmt->execute()) {
            $show_swal = true;
            $swal_icon = "success";
            $swal_title = "Announcement Successfully Added!";
        } else {
            $show_swal = true;
            $swal_icon = "error";
            $swal_title = "Error Adding Announcement: " . $conn->error;
        }
        $insert_stmt->close();
    }
    $check_stmt->close();
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

                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Class Announcements</h1>
                    </div>

                    <div class="row">
                        <div class="col-lg-4 mb-4">
                            <div class="card shadow">
                                <div class="card-header py-3 bg-primary text-white">
                                    <h6 class="m-0 font-weight-bold">Create New Announcement</h6>
                                </div>
                                <div class="card-body">
                                    <form action="" method="POST" autocomplete="off">
                                        <input type="hidden" value="<?php echo $teacher_id; ?>" name="teacher_id">

                                        <div class="form-group">
                                            <label class="font-weight-bold">Subject & Grade Level <span class="text-danger">*</span></label>
                                            <select name="subject" class="form-control" id="subjectDropdown" required>
                                                <option value="" selected disabled>Select Subject</option>
                                                <?php
                                                $sql = "SELECT * FROM subject_tbl INNER JOIN teacher_tbl ON teacher_tbl.teacher_id = subject_tbl.teacher_assign WHERE teacher_id = '$teacher_id'";
                                                $result = mysqli_query($conn, $sql);
                                                while ($res = mysqli_fetch_assoc($result)) {
                                                ?>
                                                    <option value="<?php echo $res['subject_id'] ?>" data-grade="<?php echo $res['subject_grade']; ?>">
                                                        <?php echo $res['subject_name'] ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <small class="text-muted">Grade Level</small>
                                                <input type="text" id="gradeDisplay" class="form-control bg-light" readonly placeholder="Auto-filled">
                                                <input type="hidden" name="grade" id="gradeInput">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <small class="text-muted">Target Section <span class="text-danger">*</span></small>
                                                <select name="section" class="form-control" id="sectionDropdown" required>
                                                    <option value="" selected disabled>Select Section</option>
                                                </select>
                                            </div>
                                        </div>

                                        <hr>

                                        <div class="form-group">
                                            <label class="font-weight-bold">Details</label>
                                            <div class="row">
                                                <div class="col-6 mb-2">
                                                    <small>Type</small>
                                                    <select name="type" class="form-control" required>
                                                        <option value="quiz">Quiz</option>
                                                        <option value="pt">Performance Task</option>
                                                        <option value="exam">Quarterly Exam</option>
                                                        <option value="assignment">Assignment</option>
                                                    </select>
                                                </div>
                                                <div class="col-6 mb-2">
                                                    <small>Quarter</small>
                                                    <select name="quarterly" class="form-control" required>
                                                        <option value="1">1st Quarter</option>
                                                        <option value="2">2nd Quarter</option>
                                                        <option value="3">3rd Quarter</option>
                                                        <option value="4">4th Quarter</option>
                                                    </select>
                                                </div>
                                                <div class="col-12 mb-2">
                                                    <small>Total Items/Points</small>
                                                    <input type="number" name="items" class="form-control" placeholder="e.g. 50" required>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="font-weight-bold">Schedule & Location</label>
                                            <div class="input-group mb-2">
                                                <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-calendar"></i></span></div>
                                                <input type="date" name="date" class="form-control" min="<?php echo date('Y-m-d') ?>" required>
                                            </div>
                                            <div class="input-group mb-2">
                                                <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-clock"></i></span></div>
                                                <input type="time" name="time" class="form-control" required>
                                            </div>
                                            <div class="input-group mb-2">
                                                <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-door-open"></i></span></div>
                                                <select name="room" class="form-control" required>
                                                    <option value="" selected disabled>Select Room</option>
                                                    <?php
                                                    $room = mysqli_query($conn, 'SELECT * FROM room_tbl');
                                                    while ($room_row = mysqli_fetch_assoc($room)) {
                                                        echo "<option value='{$room_row['room_id']}'>{$room_row['room_name']}</option>";
                                                    } ?>
                                                </select>
                                            </div>
                                        </div>

                                        <button type="submit" name="submit" class="btn btn-primary btn-block">
                                            <i class="fas fa-plus-circle"></i> Add Announcement
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-8">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Posted Announcements</h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>Date/Time</th>
                                                    <th>Subject</th>
                                                    <th>Type</th>
                                                    <th>Section</th>
                                                    <th>Room</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $sql = "SELECT * FROM announcement_tbl
                                                INNER JOIN subject_tbl ON subject_tbl.subject_id = announcement_tbl.subject      
                                                INNER JOIN room_tbl ON room_tbl.room_id = announcement_tbl.room
                                                INNER JOIN section_tbl ON section_tbl.section_id  = announcement_tbl.section 
                                                WHERE teacher_id = '$teacher_id' ORDER BY date DESC";

                                                $result = mysqli_query($conn, $sql);
                                                while ($res = mysqli_fetch_assoc($result)) {
                                                    // Badge Color Logic
                                                    $badgeClass = 'badge-secondary';
                                                    if ($res['type'] == 'quiz') $badgeClass = 'badge-info';
                                                    if ($res['type'] == 'exam') $badgeClass = 'badge-danger';
                                                    if ($res['type'] == 'pt') $badgeClass = 'badge-warning';
                                                ?>
                                                    <tr>
                                                        <td>
                                                            <div class="font-weight-bold"><?php echo date('M d, Y', strtotime($res['date'])); ?></div>
                                                            <small class="text-muted"><?php echo date('h:i A', strtotime($res['time'])); ?></small>
                                                        </td>
                                                        <td><?php echo $res['subject_name'] ?></td>
                                                        <td>
                                                            <span class="badge <?php echo $badgeClass; ?> p-2">
                                                                <?php echo strtoupper($res['type']) ?>
                                                            </span>
                                                            <div class="small mt-1 text-gray-500">Q<?php echo $res['quarterly'] ?></div>
                                                        </td>
                                                        <td><?php echo $res['section_name'] ?></td>
                                                        <td><?php echo $res['room_name'] ?></td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php include './../template/footer.php'; ?>
        </div>
    </div>

    <a class="scroll-to-top rounded" href="#page-top"><i class="fas fa-angle-up"></i></a>
    <?php include './../template/script.php'; ?>

    <?php if ($show_swal): ?>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const Toast = Swal.mixin({
                    toast: true,
                    position: "top-end",
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener("mouseenter", Swal.stopTimer)
                        toast.addEventListener("mouseleave", Swal.resumeTimer)
                    }
                });
                Toast.fire({
                    icon: "<?php echo $swal_icon; ?>",
                    title: "<?php echo $swal_title; ?>"
                });
            });
        </script>
    <?php endif; ?>

    <script>
        document.getElementById('subjectDropdown').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const grade = selectedOption.getAttribute('data-grade');

            // Visual feedback
            const display = document.getElementById('gradeDisplay');
            display.value = 'Grade ' + grade;

            document.getElementById('gradeInput').value = grade;

            // Simple loading text while fetching
            const secDropdown = document.getElementById('sectionDropdown');
            secDropdown.innerHTML = '<option>Loading...</option>';

            fetch('get_sections_by_grade.php?grade=' + grade)
                .then(response => response.text())
                .then(data => {
                    secDropdown.innerHTML = data;
                })
                .catch(error => {
                    secDropdown.innerHTML = '<option>Error loading sections</option>';
                });
        });
    </script>
</body>

</html>