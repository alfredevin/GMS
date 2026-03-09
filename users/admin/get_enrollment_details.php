<?php
include '../../config.php'; // Siguraduhin tama ang path ng config mo

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // 1. Get Student Details
    $query = mysqli_query($conn, "SELECT * FROM enrollment_tbl WHERE enrollmentId = '$id'");
    $row = mysqli_fetch_assoc($query);

    if (!$row) {
        echo '<div class="alert alert-danger">Student record not found.</div>';
        exit;
    }
?>

    <div class="container-fluid">
        <div class="row">

            <div class="col-md-7 border-right">
                <h6 class="text-primary font-weight-bold mb-3"><i class="fas fa-user-circle"></i> Learner Information</h6>

                <table class="table table-bordered table-sm text-dark" style="font-size: 14px;">
                    <tr>
                        <th class="bg-light" width="30%">Full Name</th>
                        <td class="font-weight-bold text-uppercase">
                            <?php echo $row['lastname'] . ', ' . $row['firstname'] . ' ' . $row['middlename']; ?>
                        </td>
                    </tr>
                    <tr>
                        <th class="bg-light">LRN</th>
                        <td><?php echo $row['lrn']; ?></td>
                    </tr>
                    <tr>
                        <th class="bg-light">Grade Level</th>
                        <td><span class="badge badge-info" style="font-size: 12px;">Grade <?php echo $row['grade']; ?></span></td>
                    </tr>
                    <tr>
                        <th class="bg-light">Sex / Age</th>
                        <td><?php echo $row['sex']; ?> / <?php echo $row['age']; ?> yrs old</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Birthdate</th>
                        <td><?php echo date('F d, Y', strtotime($row['birthdate'])); ?></td>
                    </tr>
                    <tr>
                        <th class="bg-light">Address</th>
                        <td><?php echo $row['current_address']; ?></td>
                    </tr>
                    <tr>
                        <th class="bg-light">IP / 4Ps / PWD</th>
                        <td>
                            IP: <b><?php echo $row['ip']; ?></b> |
                            4Ps: <b><?php echo $row['is_4ps']; ?></b> |
                            PWD: <b><?php echo $row['has_disability']; ?></b>
                        </td>
                    </tr>
                </table>

                <h6 class="text-primary font-weight-bold mb-3 mt-4"><i class="fas fa-users"></i> Parent / Guardian</h6>
                <table class="table table-sm table-borderless" style="font-size: 13px;">
                    <tr>
                        <td width="30%" class="font-weight-bold">Mother:</td>
                        <td><?php echo $row['mother_firstname'] . ' ' . $row['mother_lastname']; ?> <br> <small class="text-muted"><?php echo $row['mother_contact']; ?></small></td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold">Father:</td>
                        <td><?php echo $row['father_firstname'] . ' ' . $row['father_lastname']; ?> <br> <small class="text-muted"><?php echo $row['father_contact']; ?></small></td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold">Guardian:</td>
                        <td><?php echo $row['guardian_firstname'] . ' ' . $row['guardian_lastname']; ?> <br> <small class="text-muted"><?php echo $row['guardian_contact']; ?></small></td>
                    </tr>
                </table>
            </div>

            <div class="col-md-5">
                <h6 class="text-primary font-weight-bold mb-3"><i class="fas fa-folder-open"></i> Submitted Requirements</h6>

                <div class="list-group">
                    <?php
                    // Get all required documents from database
                    $req_query = mysqli_query($conn, "SELECT * FROM enrollment_requirement_tbl");

                    while ($req = mysqli_fetch_assoc($req_query)) {
                        $req_name = $req['enrollment_requirement_name'];

                        // Check if the student uploaded this specific file
                        $file_check = mysqli_query($conn, "SELECT * FROM enrollment_uploaded_files WHERE enrollment_id = '$id' AND requirement_name = '$req_name'");
                        $file = mysqli_fetch_assoc($file_check);
                    ?>

                        <div class="list-group-item list-group-item-action flex-column align-items-start p-3 mb-2 shadow-sm border-left-<?php echo $file ? 'success' : 'danger'; ?>" style="border-left-width: 5px;">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1 font-weight-bold text-dark"><?php echo $req_name; ?></h6>

                                <?php if ($file): ?>
                                    <small class="text-success"><i class="fas fa-check-circle"></i> Submitted</small>
                                <?php else: ?>
                                    <small class="text-danger"><i class="fas fa-times-circle"></i> Missing</small>
                                <?php endif; ?>
                            </div>

                            <?php if ($file): ?>
                                <div class="mt-2">
                                    <a href="../../website/uploads/<?php echo $file['file_name']; ?>" target="_blank" class="btn btn-sm btn-info btn-block">
                                        <i class="fas fa-eye"></i> View / Download File
                                    </a>
                                </div>
                            <?php else: ?>
                                <p class="mb-1 text-muted small">Student has not uploaded this file yet.</p>
                            <?php endif; ?>
                        </div>

                    <?php } ?>
                </div>
            </div>

        </div>
    </div>

<?php
}
?>