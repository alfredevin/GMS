<?php
include '../../config.php';

// Handle Upload Logic
if (isset($_POST['upload_missing'])) {
    $enrollment_id = $_POST['enrollment_id'];
    foreach ($_FILES['requirements']['name'] as $requirement => $fileName) {
        $tmpName = $_FILES['requirements']['tmp_name'][$requirement];
        $uniqueName = uniqid() . '_' . $fileName; // Add unique ID to filename
        $destination = '../../website/uploads/' . $uniqueName;

        if (move_uploaded_file($tmpName, $destination)) {
            mysqli_query($conn, "INSERT INTO enrollment_uploaded_files (enrollment_id, requirement_name, file_name)
                VALUES ('$enrollment_id', '$requirement', '$uniqueName')");
        }
    }
    echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
    echo '<script>
        document.addEventListener("DOMContentLoaded", function () {
            Swal.fire({ icon: "success", title: "Uploaded!", text: "Missing requirements submitted.", timer: 2000, showConfirmButton: false })
            .then(() => { window.location.href = window.location.href; });
        });
    </script>';
}
?>
<!DOCTYPE html>
<html lang="en">
<?php include './../template/header.php' ?>

<style>
    /* User Friendly Styles */
    .table td {
        vertical-align: middle !important;
    }

    .avatar-circle {
        width: 40px;
        height: 40px;
        background-color: #4e73df;
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 16px;
        margin-right: 10px;
    }

    .student-flex {
        display: flex;
        align-items: center;
    }
</style>

<body id="page-top">
    <div id="wrapper">
        <?php include './../template/sidebar.php' ?>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include './../template/navbar.php'; ?>
                <div class="container-fluid">

                    <div class="card shadow mb-4">
                        <div class="card-header py-3 bg-white border-bottom-primary">
                            <h6 class="m-0 font-weight-bold text-primary">Masterlist of Enrolled Students</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover" id="dataTable" width="100%" cellspacing="0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Student Name</th>
                                            <th>Sex / Age</th>
                                            <th>Grade Level</th>
                                            <th>Requirements</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // Fetch Enrolled Students (Status 2 usually means enrolled/processed)
                                        // Adjust status ID if necessary based on your flow
                                        $query = "SELECT *, enrollment_tbl.enrollmentId, enrollment_tbl.grade, enrollment_tbl.birthdate, enrollment_tbl.sex 
                                                  FROM student_tbl
                                                  INNER JOIN enrollment_tbl ON enrollment_tbl.enrollmentId = student_tbl.enrollment_id";

                                        $result = mysqli_query($conn, $query);

                                        // Count total requirements needed
                                        $req_total_sql = mysqli_query($conn, "SELECT count(*) as total FROM enrollment_requirement_tbl");
                                        $req_total = mysqli_fetch_assoc($req_total_sql)['total'];

                                        while ($row = mysqli_fetch_assoc($result)) {
                                            $enrollmentId = $row['enrollmentId'];
                                            $fullname = strtoupper($row['lastname'] . ', ' . $row['firstname']);
                                            $initial = substr($row['firstname'], 0, 1);

                                            // Calculate Age
                                            $age = (new DateTime($row['birthdate']))->diff(new DateTime('today'))->y;

                                            // Count uploaded files
                                            $uploads_sql = mysqli_query($conn, "SELECT count(*) as total FROM enrollment_uploaded_files WHERE enrollment_id = '$enrollmentId'");
                                            $uploads_count = mysqli_fetch_assoc($uploads_sql)['total'];

                                            $is_complete = ($uploads_count >= $req_total);
                                            $badge_class = $is_complete ? 'badge-success' : 'badge-warning';
                                            $status_text = $is_complete ? 'Complete' : 'Incomplete';
                                        ?>
                                            <tr>
                                                <td>
                                                    <div class="student-flex">
                                                        <div class="avatar-circle"><?php echo $initial; ?></div>
                                                        <div>
                                                            <div class="font-weight-bold text-dark"><?php echo $fullname; ?></div>
                                                            <small class="text-muted">ID: <?php echo $row['student_id']; ?></small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div><?php echo $row['sex']; ?></div>
                                                    <small class="text-muted"><?php echo $age; ?> yrs old</small>
                                                </td>
                                                <td><span class="badge badge-info px-2">Grade <?php echo $row['grade']; ?></span></td>

                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <span class="badge <?php echo $badge_class; ?> mr-2"><?php echo $status_text; ?></span>
                                                        <small class="text-muted"><?php echo $uploads_count . '/' . $req_total; ?></small>
                                                    </div>
                                                </td>

                                                <td class="text-center">
                                                    <button class="btn btn-primary btn-sm view-details-btn shadow-sm"
                                                        data-toggle="modal"
                                                        data-target="#detailsModal"
                                                        data-id="<?php echo $enrollmentId; ?>">
                                                        <i class="fas fa-eye"></i> View Profile
                                                    </button>
                                                    <a href="print_enrollment_form.php?id=<?php echo $enrollmentId; ?>" target="_blank" class="btn btn-danger btn-sm shadow-sm">
                                                        <i class="fas fa-print"></i> Print Form
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

    <div class="modal fade" id="detailsModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header bg-gradient-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-user-graduate"></i> Student Information</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body bg-light" id="modal-details-body">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="mt-2 text-muted">Loading details...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <?php include './../template/script.php'; ?>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const buttons = document.querySelectorAll('.view-details-btn');

            buttons.forEach(button => {
                button.addEventListener('click', function() {
                    const learnerId = this.getAttribute('data-id');

                    // Show Loading Spinner
                    document.getElementById('modal-details-body').innerHTML = `
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary" role="status"></div>
                            <p class="mt-2 text-muted">Loading details...</p>
                        </div>
                    `;

                    // Fetch Data
                    fetch('get_enrollment_details.php?id=' + learnerId)
                        .then(response => response.text())
                        .then(data => {
                            document.getElementById('modal-details-body').innerHTML = data;
                        })
                        .catch(error => {
                            document.getElementById('modal-details-body').innerHTML = '<p class="text-danger text-center">Error loading details.</p>';
                        });
                });
            });
        });
    </script>

</body>

</html>