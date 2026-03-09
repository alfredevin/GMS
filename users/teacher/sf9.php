<?php
include '../../config.php';

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
                        <?php
                        $select_tec_class = "SELECT * FROM teacher_tbl WHERE teacher_type = 'Class Adviser' AND teacher_id = '$teacher_id'";
                        $result_sec_tec_class = mysqli_query($conn, $select_tec_class);
                        $tec_class = mysqli_fetch_assoc($result_sec_tec_class);
                        $grade_class = $tec_class["grade_level"];
                        $section_class = $tec_class["section_id"];
                        $date_today = date('Y-m-d');
                        ?>
                        <div class="card-header py-3">
                            <div class="row">
                                <div class="col">
                                    <h6 class="m-0 font-weight-bold text-primary">School Form 9 (SF9) </h6>
                                </div>
                                <div class="col-">
                                    <a href="printsf9.php?section_id=<?php echo $section_class; ?>" target="_blank" class="btn btn-danger">
                                        <i class="fas fa-print"></i> Print SF9
                                    </a>
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
                                            <th>Section </th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <?php


                                        $sql = "SELECT * FROM student_tbl
                                                INNER JOIN enrollment_tbl ON enrollment_tbl.enrollmentId = student_tbl.enrollment_id
                                                INNER JOIN section_tbl ON section_tbl.section_id = student_tbl.section_id 
                                                WHERE student_grade = '$grade_class' 
                                                AND student_tbl.section_id = '$section_class' 
                                                ORDER BY lastname ASC";


                                        $result = mysqli_query($conn, $sql);

                                        while ($res = mysqli_fetch_assoc($result)) {
                                            $student_id = $res['student_id'];

                                        ?>
                                            <tr>
                                                <td><?= $res['student_id'] ?></td>
                                                <td><?= $res['lastname'] . ' ' . $res['firstname'] . ' ' . $res['middlename']; ?></td>
                                                <td><?= $res['section_name'] ?></td>
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
        document.getElementById('printReportBtn').addEventListener('click', function() {
            const sectionId = this.getAttribute('data-section');

            // Generate month options (Jan to Dec)
            const monthOptions = [...Array(12).keys()].map(i => {
                const month = i + 1;
                const label = new Date(0, month - 1).toLocaleString('default', {
                    month: 'long'
                });
                return `<option value="${month}">${label}</option>`;
            }).join('');

            // SweetAlert Modal
            Swal.fire({
                title: 'Select Month and Year',
                html: `
            <label for="swal-month">Month</label>
            <select id="swal-month" class="swal2-input">${monthOptions}</select>
            <label for="swal-year">Year</label>
            <input type="number" id="swal-year" class="swal2-input" value="${new Date().getFullYear()}" min="2020" max="2100">
        `,
                showCancelButton: true,
                confirmButtonText: 'Print',
                focusConfirm: false,
                preConfirm: () => {
                    const month = document.getElementById('swal-month').value;
                    const year = document.getElementById('swal-year').value;

                    if (!month || !year) {
                        Swal.showValidationMessage('Please select both month and year');
                        return false;
                    }

                    const url = `attendance_report.php?section_id=${sectionId}&month=${month}&year=${year}`;
                    window.open(url, '_blank');
                }
            });
        });
    </script>


</body>

</html>