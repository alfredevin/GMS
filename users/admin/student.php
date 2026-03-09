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
                    <a href="studentGrade" class="btn btn-primary btn-sm">Back to page</a>
                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Distinctio, molestias neque suscipit adipisci qui perferendis quod optio iure recusandae iste sed eum deserunt laudantium quidem vero in dolores? Modi, necessitatibus.</p>
                    <div class="card shadow mb-4 col-12  ">
                        <div class="card-header py-3">
                            <div class="row">
                                <div class="col">
                                    <h6 class="m-0 font-weight-bold text-primary">List of Students in Grade <?php echo $subject_grade; ?></h6>
                                </div>
                                <div class="col-">
                                    <form method="GET">
                                        <!-- Retain subject_grade -->
                                        <input type="hidden" name="grade" value="<?php echo htmlspecialchars($_GET['grade'] ?? $subject_grade); ?>">

                                        <select name="section" id="sectionFilter" onchange="this.form.submit()" class="form-control">
                                            <option value="">Select Section</option>
                                            <?php
                                            $select_section = "SELECT * FROM section_tbl WHERE section_grade = '$subject_grade'";
                                            $result_select_section = mysqli_query($conn, $select_section);
                                            while ($row_sec = mysqli_fetch_array($result_select_section)) {
                                                $selected = (isset($_GET['section']) && $_GET['section'] == $row_sec['section_id']) ? 'selected' : '';
                                            ?>
                                                <option value="<?php echo $row_sec['section_id'] ?>" <?php echo $selected; ?>>
                                                    <?php echo $row_sec['section_name'] ?>
                                                </option>
                                            <?php } ?>
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
                                            <th>Student ID</th>
                                            <th>Student Name </th>
                                            <th>Section </th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <?php

                                        $filterSection = isset($_GET['section']) ? $_GET['section'] : '';
                                        $sql = "SELECT * FROM student_tbl
                                        INNER JOIN enrollment_tbl on enrollment_tbl.enrollmentId  = student_tbl.enrollment_id
                                        INNER JOIN section_tbl ON section_tbl.section_id = student_tbl.section_id WHERE student_grade = '$subject_grade'";
                                        if (!empty($filterSection)) {
                                            $sql .= " AND student_tbl.section_id = '$filterSection'";
                                        }
                                        $result = mysqli_query($conn, $sql);
                                        while ($res = mysqli_fetch_assoc($result)) {

                                        ?>
                                            <tr>

                                                <td><?php echo $res['student_id'] ?>
                                                </td>
                                                <td><?php echo $res['firstname'] . ' ' . $res['middlename'] . ' ' . $res['lastname']; ?></td>
                                                <td><?php echo $res['section_name'] ?>
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