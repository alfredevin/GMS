<?php
include '../../config.php';
$teacher_assign = isset($_GET['teacher_id']) ? $_GET['teacher_id'] : '';
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
                    <a href="teacherList" class="btn btn-primary btn-sm">Back to page</a>
                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Distinctio, molestias neque suscipit adipisci qui perferendis quod optio iure recusandae iste sed eum deserunt laudantium quidem vero in dolores? Modi, necessitatibus.</p>
                    <div class="card shadow mb-4 col-12  ">
                        <div class="card-header py-3">
                            <div class="row">
                                <div class="col">
                                    <h6 class="m-0 font-weight-bold text-primary">List Subject of
                                        <?php
                                        $sql = "SELECT * FROM teacher_tbl WHERE teacher_id = '$teacher_assign'";
                                        $result = mysqli_query($conn, $sql);
                                        $row = mysqli_fetch_assoc($result);
                                        $teacher_name = $row["teacher_name"];
                                        echo $teacher_name;
                                        ?>
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
                                            <th>Subject Code</th>
                                            <th>Subject Name </th>
                                            <th>Grade </th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <?php
                                        $sql = "SELECT * FROM subject_tbl WHERE teacher_assign = '$teacher_assign'";
                                        $result = mysqli_query($conn, $sql);
                                        while ($res = mysqli_fetch_assoc($result)) {

                                        ?>
                                            <tr>

                                                <td><?php echo $res['subject_code'] ?> </td>
                                                <td><?php echo $res['subject_name'] ?> </td>
                                                <td><?php echo $res['subject_grade'] ?> </td>
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