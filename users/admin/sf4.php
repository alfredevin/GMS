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
                <div class="container-fluid">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h6 class="m-0 font-weight-bold text-primary">
                                        <i class="fas fa-file-alt mr-2"></i> School Form 4 (SF4) - Monthly Learner's Movement
                                    </h6>
                                </div>
                                <div class="col text-right">
                                    <button class="btn btn-danger no-print" id="printSF4Btn">
                                        <i class="fas fa-print"></i> Generate & Print SF4
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle mr-1"></i> Ang School Form 4 (SF4) ay ang kabuuang summary ng attendance at movement ng <strong>BUONG SCHOOL</strong>. I-click ang "Generate & Print SF4" button para pumili ng buwan.
                            </div>
                            <div class="table-responsive mt-3">
                                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Grade Level</th>
                                            <th>Section Name</th>
                                            <th>Class Adviser</th>
                                            <th class="text-center">Total Enrolled</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // I-display lahat ng sections bilang preview
                                        $sql = "SELECT sec.section_grade, sec.section_name, t.teacher_name,
                                                (SELECT COUNT(*) FROM student_tbl WHERE section_id = sec.section_id AND status = 1) as total_students
                                                FROM section_tbl sec
                                                LEFT JOIN teacher_tbl t ON sec.section_id = t.section_id AND t.teacher_type = 'Class Adviser'
                                                ORDER BY sec.section_grade ASC, sec.section_name ASC";
                                        
                                        $result = mysqli_query($conn, $sql);

                                        while ($res = mysqli_fetch_assoc($result)) {
                                            $adviser = !empty($res['teacher_name']) ? $res['teacher_name'] : '<span class="text-danger">No Adviser</span>';
                                        ?>
                                            <tr>
                                                <td>Grade <?= htmlspecialchars($res['section_grade']) ?></td>
                                                <td class="font-weight-bold"><?= htmlspecialchars($res['section_name']) ?></td>
                                                <td><?= $adviser ?></td>
                                                <td class="text-center font-weight-bold"><?= $res['total_students'] ?></td>
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
    
    <a class="scroll-to-top rounded" href="#page-top"><i class="fas fa-angle-up"></i></a>
    <?php include './../template/script.php'; ?>

    <script>
        // SWEETALERT LOGIC PARA SA SF4
        document.getElementById('printSF4Btn').addEventListener('click', function() {
            
            // Generate month options (Jan to Dec) - Papasa natin yung pangalan ng buwan
            const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
            const currentMonth = new Date().getMonth(); // 0-11
            
            const monthOptions = months.map((monthName, index) => {
                const selected = (index === currentMonth) ? 'selected' : '';
                return `<option value="${monthName}" ${selected}>${monthName}</option>`;
            }).join('');

            // SweetAlert Modal
            Swal.fire({
                title: 'Generate SF4 Report',
                html: `
                    <div class="form-group text-left mt-3">
                        <label for="swal-month" class="font-weight-bold">Select Month</label>
                        <select id="swal-month" class="form-control mb-3">${monthOptions}</select>
                        
                        <label for="swal-year" class="font-weight-bold">Select Year</label>
                        <input type="number" id="swal-year" class="form-control" value="${new Date().getFullYear()}" min="2020" max="2100">
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: '<i class="fas fa-print"></i> Proceed to Print',
                confirmButtonColor: '#e74a3b',
                focusConfirm: false,
                preConfirm: () => {
                    const month = document.getElementById('swal-month').value;
                    const year = document.getElementById('swal-year').value;

                    if (!month || !year) {
                        Swal.showValidationMessage('Please select both month and year');
                        return false;
                    }
                    
                    // Bubuksan ang print_sf4.php sa bagong tab
                    const url = `print_sf4.php?month=${month}&year=${year}`;
                    window.open(url, '_blank');
                }
            });
        });
    </script>
</body>
</html>