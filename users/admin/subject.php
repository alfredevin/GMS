<?php
include '../../config.php';
$subject_grade = isset($_GET['grade']) ? $_GET['grade'] : '';
if (isset($_POST['submit'])) {
    $subject_code = $_POST['subject_code'];
    $subject_name = $_POST['subject_name'];

    $check_query = "SELECT * FROM subject_tbl WHERE subject_code = '$subject_code' OR subject_name = '$subject_name' AND subject_grade = '$subject_grade'";
    $result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($result) > 0) {
        echo '<script>
            document.addEventListener("DOMContentLoaded", function () {
                const Toast = Swal.mixin({
                    toast: true,
                    position: "top-end",
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener("mouseenter", Swal.stopTimer)
                        toast.addEventListener("mouseleave", Swal.resumeTimer)
                    }
                });

                Toast.fire({
                    icon: "error",
                    title: "Subject Code Already Exists!"
                });
            });
        </script>';
    } else {
        // Insert subject into DB
        $insert_query = "INSERT INTO subject_tbl (subject_code, subject_name,subject_grade) 
                         VALUES ('$subject_code', '$subject_name','$subject_grade')";

        if (mysqli_query($conn, $insert_query)) {
            echo '<script>
                document.addEventListener("DOMContentLoaded", function () {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: "top-end",
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.addEventListener("mouseenter", Swal.stopTimer)
                            toast.addEventListener("mouseleave", Swal.resumeTimer)
                        }
                    });

                    Toast.fire({
                        icon: "success",
                        title: "Subject Successfully Added!"
                    });
                });
            </script>';
        } else {
            echo '<script>
                document.addEventListener("DOMContentLoaded", function () {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: "top-end",
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.addEventListener("mouseenter", Swal.stopTimer)
                            toast.addEventListener("mouseleave", Swal.resumeTimer)
                        }
                    });

                    Toast.fire({
                        icon: "error",
                        title: "Error Adding Subject!"
                    });
                });
            </script>';
        }
    }
}


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
                <div class="container-fluid row">
                    <div class="card shadow mb-4  col-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Add Subject</h6>
                        </div>
                        <div class="card-body">
                            <form action="" method="POST" autocomplete="off">
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label for="subject_code">Subject Code <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id=" " name="subject_code"
                                            placeholder="Enter Subject Code" required oninput="this.value = this.value.toUpperCase();">
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label for="subject_name">Subject Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id=" " name="subject_name"
                                            placeholder="Enter Subject Name" required oninput="this.value = this.value.toUpperCase();">
                                    </div>
                                    <div class="col-12">
                                        <a href="subjectGrade" class="btn btn-primary">Back to Page</a>
                                        <button type="submit" name="submit" class="btn btn-success">Add Subject</button>
                                    </div>
                                </div>
                            </form>

                        </div>
                    </div>
                    <div class="card shadow mb-4 col-7 ml-2">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">List of Subject in Grade <?php echo $subject_grade; ?></h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Subject Code</th>
                                            <th>Subject Name </th>
                                            <th> </th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <?php
                                        $sql = "SELECT * FROM subject_tbl WHERE subject_grade = '$subject_grade'";
                                        $result = mysqli_query($conn, $sql);
                                        while ($res = mysqli_fetch_assoc($result)) {

                                        ?>
                                            <tr>
                                                <td><?php echo $res['subject_code'] ?>
                                                </td>
                                                <td><?php echo $res['subject_name'] ?>
                                                </td>
                                                <td>
                                                    <a href="#"
                                                        class="btn btn-warning btn-sm editSubjectBtn"
                                                        data-id="<?= $res['subject_id']; ?>"
                                                        data-code="<?= htmlspecialchars($res['subject_code'], ENT_QUOTES); ?>"
                                                        data-grade="<?= htmlspecialchars($res['subject_grade'], ENT_QUOTES); ?>"
                                                        data-name="<?= htmlspecialchars($res['subject_name'], ENT_QUOTES); ?>">
                                                        <i class="fas fa-pen"></i> EDIT
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.editSubjectBtn').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();

                    const subjectId = this.dataset.id;
                    const subjectCode = this.dataset.code;
                    const subjectName = this.dataset.name;
                    const subjectGrade = this.dataset.grade;

                    Swal.fire({
                        title: 'Edit Subject',
                        html: `
        <input id="subject_id" type="hidden" value="${subjectId}">
        <label>Subject Code</label>
        <input id="subject_code" class="swal2-input" value="${subjectCode}" oninput="this.value = this.value.toUpperCase();">
        <label>Subject Name</label>
        <input id="subject_name" class="swal2-input" value="${subjectName}" oninput="this.value = this.value.toUpperCase();">
    `,
                        showCancelButton: true,
                        confirmButtonText: 'Update',
                        cancelButtonText: 'Cancel',
                        preConfirm: () => {
                            return {
                                id: document.getElementById('subject_id').value,
                                code: document.getElementById('subject_code').value,
                                name: document.getElementById('subject_name').value,
                                grade: subjectGrade
                            };
                        }

                    }).then((result) => {
                        if (result.isConfirmed) {
                            const updated = result.value;

                            Swal.fire({
                                title: 'Are you sure?',
                                icon: 'question',
                                showCancelButton: true,
                                confirmButtonText: 'Yes, update it!'
                            }).then((confirmResult) => {
                                if (confirmResult.isConfirmed) {
                                    const xhr = new XMLHttpRequest();
                                    xhr.open("POST", "update_subject.php", true);
                                    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

                                    const data = `subject_id=${encodeURIComponent(updated.id)}&subject_code=${encodeURIComponent(updated.code)}&subject_grade=${encodeURIComponent(updated.grade)}&subject_name=${encodeURIComponent(updated.name)}`;

                                    console.log(data);
                                    xhr.send(data);

                                    xhr.onload = function() {
                                        if (this.status == 200 && this.responseText.trim() === "success") {
                                            Swal.fire({
                                                icon: 'success',
                                                title: 'Updated!',
                                                timer: 1500,
                                                showConfirmButton: false
                                            }).then(() => location.reload());
                                        } else if (this.responseText.trim() === "duplicate") {
                                            Swal.fire({
                                                icon: 'warning',
                                                title: 'Duplicate Subject!',
                                                text: 'Subject already exists in this grade.'
                                            });
                                            return;
                                        } else {
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Duplicate Subject Code!',
                                                text: 'Subject Code already exists in this grade.'
                                            });
                                        }
                                    };
                                }
                            });
                        }
                    });

                });
            });
        });
    </script>

</body>

</html>