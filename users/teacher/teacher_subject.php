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
                        <div class="card-header py-3">
                            <div class="row">
                                <div class="col">
                                    <h6 class="m-0 font-weight-bold text-primary">List Subjects </h6>
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
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <?php
                                        $filterGrade = isset($_GET['grade']) ? $_GET['grade'] : '';
                                        $sql = "SELECT * FROM subject_tbl
                                        LEFT JOIN teacher_tbl ON teacher_tbl.teacher_id = subject_tbl.teacher_assign WHERE teacher_assign = '$teacher_id'";
                                        if (!empty($filterGrade)) {
                                            $sql .= " AND subject_tbl.subject_grade = '$filterGrade'";
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
                                                    <a href="viewStudents?subject_grade=<?php echo $res['subject_grade'] ?>&subject_name=<?php echo $res['subject_id'] ?>&name=<?php echo $res['subject_name'] ?>" class="btn btn-primary btn-sm  ">
                                                        View Students
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
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>
    <?php include './../template/script.php'; ?>
</body>

</html>