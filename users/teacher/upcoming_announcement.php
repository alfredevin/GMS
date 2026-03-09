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
                                    <h6 class="m-0 font-weight-bold text-primary">Upcoming Task </h6>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Subject</th>
                                            <th> Section</th>
                                            <th>Type </th>
                                            <th>Quarterly </th>
                                            <th>Items</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <?php
                                        $sql = "SELECT * FROM announcement_tbl
                                        INNER JOIN subject_tbl ON subject_tbl.subject_id = announcement_tbl.subject	
                                        INNER JOIN section_tbl ON section_tbl.section_id  = announcement_tbl.section WHERE teacher_id = '$teacher_id' AND date > CURDATE()";
                                        $result = mysqli_query($conn, $sql);
                                        while ($res = mysqli_fetch_assoc($result)) {

                                        ?>
                                            <tr>
                                                <td><?php echo $res['subject_name'] ?> </td>
                                                <td><?php echo $res['section_name'] ?> </td>
                                                <td>
                                                    <?php
                                                    $type =  $res['type'];
                                                    if ($type == 'exam') {
                                                        echo 'Quarterly Examination';
                                                    } else if ($type == 'pt') {
                                                        echo 'Performance Task';
                                                    } else {
                                                        echo 'Quiz';
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    $quarterly =  $res['quarterly'];
                                                    if ($quarterly == 1) {
                                                        echo '1st quarterly';
                                                    } else if ($quarterly == 2) {
                                                        echo '2nd quarterly';
                                                    } else if ($quarterly == 3) {
                                                        echo '3rd quarterly';
                                                    } else if ($quarterly == 4) {
                                                        echo '4th quarterly';
                                                    } else {
                                                        echo '';
                                                    }
                                                    ?></td>
                                                <td><?php echo $res['items'] . ' Points' ?> </td>
                                                <td><?php echo $res['date'] ?> </td>

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