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
                            <h6 class="m-0 font-weight-bold text-primary">List of Students</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id=" " width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Grade List</th>
                                            <th>Count of Students</th>
                                            <th> </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Grade 7</td>
                                            <td>
                                                <?php
                                                $count = "SELECT COUNT(*) AS total FROM student_tbl WHERE student_grade = 7";
                                                $result_count = mysqli_query($conn, $count);

                                                if ($row = mysqli_fetch_assoc($result_count)) {
                                                    echo $row['total'];
                                                } else {
                                                    echo "0";
                                                }
                                                ?>

                                            </td>
                                            <td>
                                                <a class="btn btn-primary btn-sm" href="student?grade=7">View Students</a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Grade 8</td>
                                            <td>
                                                <?php
                                                $count = "SELECT COUNT(*) AS total FROM student_tbl WHERE student_grade = 8";
                                                $result_count = mysqli_query($conn, $count);

                                                if ($row = mysqli_fetch_assoc($result_count)) {
                                                    echo $row['total'];
                                                } else {
                                                    echo "0";
                                                }
                                                ?>

                                            </td>
                                            <td>
                                                <a class="btn btn-primary btn-sm" href="student?grade=8">View Students</a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Grade 9</td>
                                            <td>
                                                <?php
                                                $count = "SELECT COUNT(*) AS total FROM student_tbl WHERE student_grade = 9";
                                                $result_count = mysqli_query($conn, $count);

                                                if ($row = mysqli_fetch_assoc($result_count)) {
                                                    echo $row['total'];
                                                } else {
                                                    echo "0";
                                                }
                                                ?>

                                            </td>
                                            <td>
                                                <a class="btn btn-primary btn-sm" href="student?grade=8">View Students</a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Grade 10</td>
                                              <td>
                                                <?php
                                                $count = "SELECT COUNT(*) AS total FROM student_tbl WHERE student_grade = 10";
                                                $result_count = mysqli_query($conn, $count);

                                                if ($row = mysqli_fetch_assoc($result_count)) {
                                                    echo $row['total'];
                                                } else {
                                                    echo "0";  
                                                }
                                                ?>

                                            </td>
                                            <td>
                                                <a class="btn btn-primary btn-sm" href="student?grade=10">View Students</a>
                                            </td>
                                        </tr>

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