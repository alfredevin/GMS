<?php
include '../../config.php';
$filterGrade = isset($_GET['subject_grade']) ? $_GET['subject_grade'] : '';
$filterSubject = isset($_GET['subject_name']) ? $_GET['subject_name'] : '';
$name = isset($_GET['name']) ? $_GET['name'] : '';
$filterSection = isset($_GET['section']) ? $_GET['section'] : '';
$announcement_jd = $_GET['announcement_jd'];
$max_items = $_GET['items'];

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
                    <a href="today_task" class="btn btn-primary btn-sm">Back to Page</a>
                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Repudiandae perferendis id similique, fuga rem velit? Dolorem molestiae dolorum amet nisi cumque porro in dolores, dignissimos nulla sit quasi, culpa accusamus!</p>

                    <div class="card shadow mb-4  ">

                        <div class="card-header py-3">
                            <div class="row">
                                <div class="col">
                                    <h6 class="m-0 font-weight-bold text-primary">List of Students </h6>
                                    <h6 class="m-0 font-weight-bold text-primary">Subject Name : <?php echo $name; ?> </h6>
                                    <h6 class="m-0 font-weight-bold text-primary">Grade :
                                        <?php echo $filterGrade;
                                        if (!empty($filterSection)) {
                                            $select_section = "SELECT * FROM section_tbl WHERE section_id  = '$filterSection'";
                                            $result_select_section = mysqli_query($conn, $select_section);
                                            $sec_row = $result_select_section->fetch_array(MYSQLI_ASSOC);
                                            $select_section = $sec_row["section_name"];
                                            echo ' |  ' . $select_section . '';
                                        } ?>
                                         </h6>
                                </div>
                                <div class="col-">


                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Student ID</th>
                                            <th>Student Name </th>
                                            <th>Score </th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $sql = "SELECT * FROM student_tbl
        INNER JOIN enrollment_tbl ON enrollment_tbl.enrollmentId = student_tbl.enrollment_id
        INNER JOIN section_tbl ON section_tbl.section_id = student_tbl.section_id 
        WHERE student_grade = '$filterGrade' AND student_tbl.section_id = '$filterSection'";
                                        $result = mysqli_query($conn, $sql);

                                        while ($res = mysqli_fetch_assoc($result)) {
                                            $student_id = $res['student_id'];

                                            // Get existing score if any
                                            $scoreQuery = "SELECT * FROM student_scores_tbl 
                                            INNER JOIN announcement_tbl ON announcement_tbl.announcement_jd = student_scores_tbl.announcement_id 
                   WHERE student_id = '$student_id' 
                   AND announcement_id = '$announcement_jd' LIMIT 1";
                                            $scoreResult = mysqli_query($conn, $scoreQuery);
                                            $scoreRow = mysqli_fetch_assoc($scoreResult);
                                            $items = $scoreRow ? $scoreRow['items'] : null;
                                            $score = $scoreRow ? $scoreRow['score'] : null;
                                        ?>
                                            <tr>
                                                <td><?php echo $student_id; ?></td>
                                                <td><?php echo $res['firstname'] . ' ' . $res['middlename'] . ' ' . $res['lastname']; ?></td>
                                                <td>
                                                    <?php echo $score !== null ? $score . '/' . $items : '<span class="text-muted">No score yet</span>'; ?>
                                                </td>
                                                <td>
                                                    <button class="btn btn-<?php echo $score !== null ? 'warning' : 'danger'; ?> btn-sm record-score-btn"
                                                        data-student="<?php echo $student_id; ?>"
                                                        data-name="<?php echo $res['firstname'] . ' ' . $res['lastname']; ?>"
                                                        data-score="<?php echo $score !== null ? $score : ''; ?>">
                                                        <?php echo $score !== null ? 'Update Score' : 'Record Score'; ?>
                                                    </button>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const maxItems = <?php echo $max_items; ?>;
        const announcementId = <?php echo $announcement_jd; ?>;

        document.querySelectorAll('.record-score-btn').forEach(button => {
            button.addEventListener('click', function() {
                const studentId = this.getAttribute('data-student');
                const studentName = this.getAttribute('data-name');
                const currentScore = this.getAttribute('data-score');

                Swal.fire({
                    title: `Enter score for ${studentName}`,
                    input: 'number',
                    inputAttributes: {
                        min: 0,
                        max: maxItems,
                        step: 1
                    },
                    inputLabel: `Score (Max: ${maxItems})`,
                    inputValue: currentScore || '',
                    showCancelButton: true,
                    confirmButtonText: currentScore ? 'Update' : 'Save',
                    preConfirm: (value) => {
                        if (value === '') {
                            Swal.showValidationMessage('Score is required');
                        } else if (value < 0 || value > maxItems) {
                            Swal.showValidationMessage(`Score must be between 0 and ${maxItems}`);
                        }
                        return value;
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        const score = result.value;

                        // AJAX to save/update score
                        fetch('save_score.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded',
                                },
                                body: `student_id=${studentId}&score=${score}&announcement_id=${announcementId}`
                            })
                            .then(res => res.text())
                            .then(response => {
                                Swal.fire('Success', 'Score has been saved', 'success')
                                    .then(() => {
                                        location.reload(); // Reload page to show updated score and button
                                    });
                            });
                    }
                });
            });
        });
    </script>


</body>

</html>