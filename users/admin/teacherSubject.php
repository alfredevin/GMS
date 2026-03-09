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
                <div class="container-fluid">
                    <a href="teacherList" class="btn btn-primary btn-sm mb-3">
                        <i class="fas fa-arrow-left"></i> Back to Teacher List
                    </a>

                    <div class="card shadow mb-4">
                        <div
                            class="card-header py-3 bg-gradient-primary text-white d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold">
                                <i class="fas fa-book-reader mr-2"></i> Assigned Subjects of
                                <?php
                                $sql = "SELECT * FROM teacher_tbl WHERE teacher_id = '$teacher_assign'";
                                $result = mysqli_query($conn, $sql);
                                $teacher_name = "Unknown Teacher";
                                if (mysqli_num_rows($result) > 0) {
                                    $row = mysqli_fetch_assoc($result);
                                    $teacher_name = strtoupper($row["teacher_name"]);
                                }
                                echo "<span style='text-decoration: underline;'>$teacher_name</span>";
                                ?>
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" id="dataTable" width="100%"
                                    cellspacing="0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Subject Code</th>
                                            <th>Subject Name </th>
                                            <th>Grade Level</th>
                                            <th>Assigned Sections</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <?php
                                        // Kunin muna lahat ng sections para madaling hanapin ang pangalan
                                        $all_sections = [];
                                        $sec_res = mysqli_query($conn, "SELECT section_id, section_name FROM section_tbl");
                                        while ($s = mysqli_fetch_assoc($sec_res)) {
                                            $all_sections[$s['section_id']] = $s['section_name'];
                                        }

                                        // Kunin ang mga subjects na naka-assign kay teacher
                                        $sql = "SELECT * FROM subject_tbl WHERE teacher_assign = '$teacher_assign'";
                                        $result = mysqli_query($conn, $sql);

                                        if (mysqli_num_rows($result) > 0) {
                                            while ($res = mysqli_fetch_assoc($result)) {
                                                // ITEM 12 FIX: Basahin ang JSON array ng sections
                                                $section_names_arr = [];
                                                if (!empty($res['section_id'])) {
                                                    $sec_ids = json_decode($res['section_id'], true);
                                                    if (is_array($sec_ids)) {
                                                        foreach ($sec_ids as $sid) {
                                                            if (isset($all_sections[$sid])) {
                                                                $section_names_arr[] = "<span class='badge badge-success mr-1' style='font-size: 13px;'>" . $all_sections[$sid] . "</span>";
                                                            }
                                                        }
                                                    }
                                                }

                                                // Kung walang section na naka-assign
                                                $display_sections = !empty($section_names_arr) ? implode(" ", $section_names_arr) : "<span class='text-muted font-italic'>No sections assigned</span>";
                                                ?>
                                                <tr>
                                                    <td class="align-middle font-weight-bold text-dark">
                                                        <?php echo htmlspecialchars($res['subject_code']); ?></td>
                                                    <td class="align-middle">
                                                        <?php echo htmlspecialchars($res['subject_name']); ?></td>
                                                    <td class="align-middle text-center">Grade
                                                        <?php echo htmlspecialchars($res['subject_grade']); ?></td>
                                                    <td class="align-middle"><?php echo $display_sections; ?></td>
                                                </tr>
                                            <?php
                                            }
                                        } else {
                                            echo "<tr><td colspan='4' class='text-center py-4 text-muted'>No subjects currently assigned to this teacher.</td></tr>";
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
</body>

</html>