<?php
include '../../config.php';
?>
<!DOCTYPE html>
<html lang="en">
<?php include './../template/header.php' ?>

<style>
    /* Custom Styles for better Table UI */
    .table-responsive {
        overflow-x: auto;
    }

    .align-middle {
        vertical-align: middle !important;
    }

    .avatar-initial {
        width: 40px;
        height: 40px;
        background-color: #4e73df;
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 18px;
        margin-right: 10px;
    }

    .student-name-container {
        display: flex;
        align-items: center;
    }

    .progress {
        height: 20px;
        background-color: #e9ecef;
        border-radius: 5px;
    }
</style>

<body id="page-top">
    <div id="wrapper">
        <?php include './../template/sidebar.php' ?>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include './../template/navbar.php'; ?>
                <div class="container-fluid">

                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Enrollment Management</h1>
                    </div>

                    <div class="card shadow mb-4 border-left-primary">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-white">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-users mb-1"></i> List of New Enrollees (Pending Approval)
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover" id="dataTable" width="100%" cellspacing="0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th width="5%">#</th>
                                            <th width="25%">Learner Info</th>
                                            <th width="10%">Sex/Age</th>
                                            <th width="10%">Grade</th>
                                            <th width="30%">Requirements Status</th>
                                            <th width="10%" class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // 1 = Pending/New Enrollees
                                        $select_school = mysqli_query($conn, 'SELECT * FROM enrollment_tbl WHERE enrollment_status = 1 ORDER BY enrollmentId DESC');
                                        $counter = 1;

                                        // Get total number of requirements needed
                                        $req_query = mysqli_query($conn, "SELECT count(*) as total FROM enrollment_requirement_tbl");
                                        $req_count = mysqli_fetch_assoc($req_query)['total'];

                                        while ($row = mysqli_fetch_array($select_school)) {
                                            $enrollmentId = $row['enrollmentId'];
                                            $fullname = $row['lastname'] . ', ' . $row['firstname'] . ' ' . $row['middlename'];
                                            $initial = substr($row['firstname'], 0, 1);

                                            // Calculate Age
                                            $birthdate = new DateTime($row['birthdate']);
                                            $today = new DateTime('today');
                                            $age = $birthdate->diff($today)->y;

                                            // Count Submitted Files
                                            $files_query = mysqli_query($conn, "SELECT count(*) as submitted FROM enrollment_uploaded_files WHERE enrollment_id = '$enrollmentId'");
                                            $submitted_count = mysqli_fetch_assoc($files_query)['submitted'];

                                            // Determine Progress Color
                                            $progress_percent = ($req_count > 0) ? ($submitted_count / $req_count) * 100 : 0;
                                            $progress_class = ($progress_percent == 100) ? 'bg-success' : 'bg-warning';
                                            $status_text = ($progress_percent == 100) ? 'Complete' : 'Incomplete';
                                        ?>
                                            <tr>
                                                <td class="align-middle"><?php echo $counter; ?></td>
                                                <td class="align-middle">
                                                    <div class="student-name-container">
                                                        <div class="avatar-initial"><?php echo $initial; ?></div>
                                                        <div>
                                                            <div class="font-weight-bold text-dark" style="text-transform: uppercase;"><?php echo $fullname; ?></div>
                                                            <small class="text-muted">LRN: <?php echo $row['lrn']; ?></small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="align-middle">
                                                    <div style="line-height: 1.2;">
                                                        <span class="d-block"><?php echo $row['sex']; ?></span>
                                                        <small class="text-muted"><?php echo $age; ?> yrs old</small>
                                                    </div>
                                                </td>
                                                <td class="align-middle">
                                                    <span class="badge badge-info px-2 py-1" style="font-size: 12px;">Grade <?php echo $row['grade']; ?></span>
                                                </td>

                                                <td class="align-middle">
                                                    <div class="d-flex justify-content-between small font-weight-bold mb-1">
                                                        <span><?php echo $status_text; ?></span>
                                                        <span><?php echo $submitted_count; ?>/<?php echo $req_count; ?> Files</span>
                                                    </div>
                                                    <div class="progress">
                                                        <div class="progress-bar <?php echo $progress_class; ?>" role="progressbar"
                                                            style="width: <?php echo $progress_percent; ?>%"
                                                            aria-valuenow="<?php echo $progress_percent; ?>" aria-valuemin="0" aria-valuemax="100">
                                                        </div>
                                                    </div>
                                                </td>

                                                <td class="align-middle text-center">
                                                    <button class="btn btn-primary btn-sm shadow-sm view-details-btn"
                                                        data-toggle="modal"
                                                        data-target="#detailsModal"
                                                        data-id="<?php echo $row['enrollmentId']; ?>">
                                                        <i class="fas fa-search-plus"></i> Review
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php
                                            $counter++;
                                        } ?>
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

    <div class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-gradient-primary text-white">
                    <h5 class="modal-title font-weight-bold" id="detailsModalLabel">
                        <i class="fas fa-user-graduate mr-2"></i> Review Learner Application
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body" id="modal-details-body" style="background-color: #f8f9fc;">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">Fetching student information...</p>
                    </div>
                </div>

                <div class="modal-footer bg-white">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Close
                    </button>
                    <form method="POST" id="confirmForm">
                        <input type="hidden" name="confirm_id" id="confirm_id">
                        <button type="button" id="confirmEnrollmentBtn" class="btn btn-success font-weight-bold px-4">
                            <i class="fas fa-check-circle"></i> Approve Enrollment
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('.view-details-btn').forEach(button => {
            button.addEventListener('click', function() {
                const learnerId = this.getAttribute('data-id');
                document.getElementById('confirm_id').value = learnerId;

                // Reset modal body to loading state
                document.getElementById('modal-details-body').innerHTML = `
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">Fetching student information...</p>
                    </div>
                `;

                // AJAX Request to fetch details
                fetch('get_enrollment_details.php?id=' + learnerId)
                    .then(response => response.text())
                    .then(data => {
                        document.getElementById('modal-details-body').innerHTML = data;
                    });
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.getElementById('confirmEnrollmentBtn').addEventListener('click', function() {
            const learnerId = document.getElementById('confirm_id').value;

            Swal.fire({
                title: 'Confirm Enrollment?',
                text: "This will officially enroll the student and send them an email notification.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#1cc88a',
                cancelButtonColor: '#858796',
                confirmButtonText: 'Yes, Approve!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Processing...',
                        text: 'Please wait while we send the email.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    fetch('confirm_enrollment.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: 'confirm_id=' + learnerId
                        })
                        .then(response => response.text())
                        .then(data => {
                            Swal.fire({
                                title: 'Approved!',
                                text: 'Student has been successfully enrolled.',
                                icon: 'success'
                            }).then(() => {
                                location.reload();
                            });
                        })
                        .catch(error => {
                            Swal.fire('Error', 'Something went wrong.', 'error');
                        });
                }
            });
        });
    </script>

</body>

</html>