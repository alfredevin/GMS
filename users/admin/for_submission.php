<?php
include '../../config.php';
?>
<!DOCTYPE html>
<html lang="en">
<?php include './../template/header.php' ?>

<style>
    /* Table Styling */
    .table-hover tbody tr:hover {
        background-color: #f8f9fc;
    }

    .avatar-initial {
        width: 35px;
        height: 35px;
        background-color: #36b9cc;
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 14px;
        margin-right: 10px;
    }

    .student-info {
        display: flex;
        align-items: center;
    }

    .progress-container {
        min-width: 150px;
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
                        <h1 class="h3 mb-0 text-gray-800">Requirements Submission</h1>
                    </div>

                    <div class="card shadow mb-4 border-bottom-info">
                        <div class="card-header py-3 bg-white">
                            <h6 class="m-0 font-weight-bold text-info">
                                <i class="fas fa-file-upload mr-1"></i> Students for Sectioning & Confirmation
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover" id="dataTable" width="100%" cellspacing="0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Learner</th>
                                            <th>Grade Level</th>
                                            <th>Requirements Status</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // Status 2 = Requirement Submission / Ready for Sectioning
                                        $select_school = mysqli_query($conn, 'SELECT * FROM enrollment_tbl WHERE enrollment_status = 2 ORDER BY enrollmentId DESC');
                                        $counter = 1;

                                        // Count total requirements needed
                                        $req_total_sql = mysqli_query($conn, "SELECT count(*) as total FROM enrollment_requirement_tbl");
                                        $req_total = mysqli_fetch_assoc($req_total_sql)['total'];

                                        while ($row = mysqli_fetch_array($select_school)) {
                                            $enrollmentId = $row['enrollmentId'];
                                            $fullname = $row['lastname'] . ', ' . $row['firstname'];
                                            $initial = substr($row['firstname'], 0, 1);

                                            // Count uploaded files for this student
                                            $uploads_sql = mysqli_query($conn, "SELECT count(*) as total FROM enrollment_uploaded_files WHERE enrollment_id = '$enrollmentId'");
                                            $uploads_count = mysqli_fetch_assoc($uploads_sql)['total'];

                                            // Calculate Progress
                                            $percentage = ($req_total > 0) ? ($uploads_count / $req_total) * 100 : 0;
                                            $color = ($percentage == 100) ? 'bg-success' : 'bg-warning';
                                            $label = ($percentage == 100) ? 'Complete' : 'Pending Files';
                                        ?>
                                            <tr>
                                                <td class="align-middle"><?php echo $counter; ?></td>
                                                <td class="align-middle">
                                                    <div class="student-info">
                                                        <div class="avatar-initial"><?php echo $initial; ?></div>
                                                        <div>
                                                            <div class="font-weight-bold text-dark text-uppercase"><?php echo $fullname; ?></div>
                                                            <small class="text-muted"><?php echo $row['sex']; ?> | <?php echo $row['age']; ?> yrs old</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="align-middle">
                                                    <span class="badge badge-info px-2 py-1">Grade <?php echo $row['grade']; ?></span>
                                                </td>

                                                <td class="align-middle">
                                                    <div class="progress-container">
                                                        <div class="d-flex justify-content-between small font-weight-bold mb-1">
                                                            <span><?php echo $label; ?></span>
                                                            <span><?php echo $uploads_count; ?>/<?php echo $req_total; ?></span>
                                                        </div>
                                                        <div class="progress" style="height: 10px;">
                                                            <div class="progress-bar <?php echo $color; ?>" role="progressbar"
                                                                style="width: <?php echo $percentage; ?>%"></div>
                                                        </div>
                                                    </div>
                                                </td>

                                                <td class="align-middle text-center">
                                                    <button class="btn btn-primary btn-sm shadow-sm view-details-btn px-3"
                                                        data-toggle="modal"
                                                        data-target="#detailsModal"
                                                        data-id="<?php echo $row['enrollmentId']; ?>"
                                                        data-grade="<?php echo $row['grade']; ?>">
                                                        <i class="fas fa-check-double mr-1"></i> Verify & Enroll
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
                <div class="modal-header bg-gradient-info text-white">
                    <h5 class="modal-title font-weight-bold">
                        <i class="fas fa-user-check mr-2"></i> Final Verification
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body" id="modal-details-body" style="background: #f8f9fc;">
                    <div class="text-center py-5">
                        <div class="spinner-border text-info" role="status"></div>
                        <p class="mt-2">Loading details...</p>
                    </div>
                </div>

                <div class="modal-footer bg-white">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>

                    <form method="POST" id="confirmForm">
                        <input type="hidden" id="confirm_id" name="confirm_id">
                        <input type="hidden" id="grade" name="grade">
                        <button type="button" id="confirmEnrollmentBtn" class="btn btn-success font-weight-bold">
                            <i class="fas fa-school mr-1"></i> Assign Section & Enroll
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Handle Modal Opening
        document.querySelectorAll('.view-details-btn').forEach(button => {
            button.addEventListener('click', function() {
                const learnerId = this.getAttribute('data-id');
                const grade = this.getAttribute('data-grade');

                document.getElementById('confirm_id').value = learnerId;
                document.getElementById('grade').value = grade;

                // Load Details via AJAX
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
            const grade = document.getElementById('grade').value;

            // 1. Fetch Sections First
            fetch('get_sections_ajax.php?grade=' + encodeURIComponent(grade))
                .then(response => response.json())
                .then(sections => {

                    if (sections.length === 0) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'No Sections Available',
                            text: 'Please create a section for Grade ' + grade + ' first.'
                        });
                        return;
                    }

                    // 2. Build Dropdown Options
                    let optionsHtml = '';
                    sections.forEach(section => {
                        optionsHtml += `<option value="${section.section_id}">${section.section_name}</option>`;
                    });

                    // 3. Show SweetAlert with Dropdown
                    Swal.fire({
                        title: 'Assign a Section',
                        text: 'Select the section for this student:',
                        html: `<select id="sectionSelect" class="swal2-input form-control">${optionsHtml}</select>`,
                        showCancelButton: true,
                        confirmButtonText: 'Confirm Enrollment',
                        confirmButtonColor: '#28a745',
                        cancelButtonColor: '#d33',
                        preConfirm: () => {
                            const selectedSection = document.getElementById('sectionSelect').value;
                            if (!selectedSection) {
                                Swal.showValidationMessage('Please select a section');
                            }
                            return selectedSection;
                        }
                    }).then(result => {
                        if (result.isConfirmed) {
                            const sectionId = result.value;

                            // 4. Final Processing
                            Swal.fire({
                                title: 'Enrolling Student...',
                                text: 'Saving data and sending email confirmation.',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });

                            // 5. Send to PHP
                            fetch('confirm_enrollment_requirements.php', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/x-www-form-urlencoded'
                                    },
                                    body: 'confirm_id=' + encodeURIComponent(learnerId) +
                                        '&grade=' + encodeURIComponent(grade) +
                                        '&section_id=' + encodeURIComponent(sectionId)
                                })
                                .then(response => response.text())
                                .then(data => {
                                    // You might want to parse 'data' if your PHP returns JSON
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Success!',
                                        text: 'Student has been officially enrolled.'
                                    }).then(() => {
                                        location.reload();
                                    });
                                })
                                .catch(() => {
                                    Swal.fire('Error', 'Failed to enroll student.', 'error');
                                });
                        }
                    });
                })
                .catch(error => {
                    console.error('Error fetching sections:', error);
                    Swal.fire('System Error', 'Could not fetch sections.', 'error');
                });
        });
    </script>

</body>

</html>