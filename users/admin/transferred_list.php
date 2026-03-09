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

                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Transferred Out History</h1>
                    </div>

                    <div class="card shadow mb-4 border-left-danger">
                        <div class="card-header py-3 d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-danger">List of Transferred Students</h6>
                            <a href="student_list" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> Back to Active List
                            </a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Date Transferred</th>
                                            <th>Student ID</th>
                                            <th>Student Name</th>
                                            <th>Transferred To (School)</th>
                                            <th>Reason</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // JOIN 3 TABLES: 
                                        // 1. transferee_tbl (para sa reason/date)
                                        // 2. student_tbl (para sa student_id)
                                        // 3. enrollment_tbl (para sa pangalan)

                                        $sql = "SELECT 
                                                    t.transfer_date, 
                                                    t.school_to_transfer, 
                                                    t.reason,
                                                    s.student_id,
                                                    e.lastname, e.firstname, e.middlename
                                                FROM student_transferee_tbl t
                                                INNER JOIN student_tbl s ON t.student_id = s.student_id
                                                INNER JOIN enrollment_tbl e ON s.enrollment_id = e.enrollmentId
                                                ORDER BY t.transfer_date DESC";

                                        $result = mysqli_query($conn, $sql);

                                        while ($row = mysqli_fetch_assoc($result)) {
                                            $fullname = strtoupper($row['lastname'] . ', ' . $row['firstname'] . ' ' . $row['middlename']);
                                            // Format Date (e.g., Nov 29, 2025)
                                            $date = date('M d, Y', strtotime($row['transfer_date']));
                                        ?>
                                            <tr>
                                                <td class="font-weight-bold text-danger"><?= $date ?></td>
                                                <td><?= $row['student_id'] ?></td>
                                                <td class="font-weight-bold"><?= $fullname ?></td>
                                                <td><?= $row['school_to_transfer'] ?></td>
                                                <td><?= $row['reason'] ?></td>
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