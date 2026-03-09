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
                        <h1 class="h3 mb-0 text-gray-800">Dropped Out Students History</h1>
                        <a href="print_dropout_report.php" target="_blank" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                            <i class="fas fa-print fa-sm text-white-50"></i> Print Official Report
                        </a>
                    </div>

                    <div class="card shadow mb-4 border-left-danger">
                        <div class="card-header py-3 d-flex justify-content-between align-items-center bg-white">
                            <h6 class="m-0 font-weight-bold text-danger">
                                <i class="fas fa-user-slash mr-2"></i> List of Dropped Out Students
                            </h6>
                            <a href="student_list " class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> Back to Active List
                            </a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Date Dropped</th>
                                            <th>Student ID</th>
                                            <th>Student Name</th>
                                            <th>Reason for Dropping</th>
                                            <th class="text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // JOIN TABLES: 
                                        // 1. student_dropout_tbl (date/reason)
                                        // 2. student_tbl (id)
                                        // 3. enrollment_tbl (name)

                                        $sql = "SELECT 
                                                    d.dropout_date, 
                                                    d.reason,
                                                    s.student_id,
                                                    e.lastname, e.firstname, e.middlename
                                                FROM student_dropout_tbl d
                                                INNER JOIN student_tbl s ON d.student_id = s.student_id
                                                INNER JOIN enrollment_tbl e ON s.enrollment_id = e.enrollmentId
                                                ORDER BY d.dropout_date DESC";

                                        $result = mysqli_query($conn, $sql);

                                        if (mysqli_num_rows($result) > 0) {
                                            while ($row = mysqli_fetch_assoc($result)) {
                                                $fullname = strtoupper($row['lastname'] . ', ' . $row['firstname'] . ' ' . $row['middlename']);
                                                $date = date('M d, Y', strtotime($row['dropout_date']));
                                        ?>
                                                <tr>
                                                    <td class="font-weight-bold"><?= $date ?></td>
                                                    <td><?= $row['student_id'] ?></td>
                                                    <td class="font-weight-bold text-uppercase"><?= $fullname ?></td>
                                                    <td><?= $row['reason'] ?></td>
                                                    <td class="text-center">
                                                        <span class="badge badge-danger px-3 py-2">DROPPED</span>
                                                    </td>
                                                </tr>
                                        <?php
                                            }
                                        } else {
                                            echo "<tr><td colspan='5' class='text-center text-muted py-4'>No dropout records found.</td></tr>";
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
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>
    <?php include './../template/script.php'; ?>

    <style>
        @media print {
            body * {
                visibility: hidden;
            }

            .card,
            .card * {
                visibility: visible;
            }

            .card {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                border: none !important;
            }

            .btn,
            .no-print {
                display: none !important;
            }
        }
    </style>
</body>

</html>