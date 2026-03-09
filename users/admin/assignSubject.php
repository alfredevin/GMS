<?php
include '../../config.php';
$subject_grade = isset($_GET['grade']) ? $_GET['grade'] : '';
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
                <div class="container-fluid  ">
                    <div class="card shadow mb-4  ">
                        <div class="card-header py-3">
                            <div class="row">
                                <div class="col">
                                    <h6 class="m-0 font-weight-bold text-primary">List of Subjects </h6>
                                </div>
                                <div class="col-">
                                    <form method="GET">
                                        <select name="grade" id="gradeFilter" onchange="this.form.submit()" class="form-control mb-3" style="width: 200px;">
                                            <option value="">-- All Grades --</option>
                                            <?php
                                            for ($i = 7; $i <= 10; $i++) {
                                                $selected = (isset($_GET['grade']) && $_GET['grade'] == $i) ? 'selected' : '';
                                                echo "<option value='$i' $selected>Grade - $i</option>";
                                            }
                                            ?>
                                        </select>
                                    </form>

                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Subject Code</th>
                                            <th>Subject Name </th>
                                            <th>Grade </th>
                                            <th>Teacher Assign </th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <?php
                                        $filterGrade = isset($_GET['grade']) ? $_GET['grade'] : '';
                                        $sql = "SELECT * FROM subject_tbl
                                        LEFT JOIN teacher_tbl ON teacher_tbl.teacher_id = subject_tbl.teacher_assign";
                                        if (!empty($filterGrade)) {
                                            $sql .= " WHERE subject_tbl.subject_grade = '$filterGrade'";
                                        }
                                        $result = mysqli_query($conn, $sql);
                                        while ($res = mysqli_fetch_assoc($result)) {

                                        ?>
                                            <tr>

                                                <td><?php echo $res['subject_code'] ?>
                                                </td>

                                                <td><?php echo $res['subject_name'] ?>
                                                </td>
                                                <td><?php echo 'Grade - ' . $res['subject_grade'] ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    echo $res['teacher_name'] ? $res['teacher_name'] : '
                                                <span class="text-danger">No Teacher Assigned Yet</span>';
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php if (!empty($res['teacher_assign'])): ?>
                                                        <a href="#" class="btn btn-warning btn-sm assign-teacher-btn"
                                                            data-subject-id="<?= $res['subject_id']; ?>">
                                                            Edit Assigned Teacher
                                                        </a>
                                                    <?php else: ?>
                                                        <!-- <a href="#" class="btn btn-primary btn-sm assign-teacher-btn"
                                                            data-subject-id="<?= $res['subject_id']; ?>">
                                                            Assign Teacher
                                                        </a> -->
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
    <script>
        // Load all buttons
        document.querySelectorAll('.assign-teacher-btn').forEach(button => {
            button.addEventListener('click', function() {
                const subjectId = this.getAttribute('data-subject-id');

                // Step 1: Fetch teachers from the backend
                fetch('get_teachers.php')
                    .then(response => response.json())
                    .then(teachers => {
                        let optionsHtml = '';
                        teachers.forEach(teacher => {
                            optionsHtml += `<option value="${teacher.id}">${teacher.name}</option>`;
                        });

                        // Step 2: Show SweetAlert with select input
                        Swal.fire({
                            title: 'Assign Teacher',
                            html: '<select id="teacherSelect" class="swal2-input">' +
                                optionsHtml +
                                '</select>',
                            confirmButtonText: 'Assign',
                            showCancelButton: true,
                            preConfirm: () => {
                                const selectedTeacher = document.getElementById('teacherSelect').value;
                                if (!selectedTeacher) {
                                    Swal.showValidationMessage('Please select a teacher');
                                }
                                return selectedTeacher;
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                const selectedTeacherId = result.value;

                                // Step 3: Send the update via AJAX
                                fetch('assign_teacher.php', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/x-www-form-urlencoded'
                                        },
                                        body: 'subject_id=' + subjectId + '&teacher_id=' + selectedTeacherId
                                    })
                                    .then(response => response.text())
                                    .then(data => {
                                        if (data === 'success') {
                                            Swal.fire('Assigned!', 'Teacher has been assigned.', 'success')
                                                .then(() => location.reload());
                                        } else {
                                            Swal.fire('Error', 'Failed to assign teacher.', 'error');
                                        }
                                    });
                            }
                        });
                    });
            });
        });
    </script>

</body>

</html>