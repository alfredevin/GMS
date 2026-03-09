<?php
include '../../config.php';
$section_grade = isset($_GET['grade']) ? $_GET['grade'] : '';
if (isset($_POST['submit'])) {
    $section_name = $_POST['section_name'];

    $check_query = "SELECT * FROM section_tbl WHERE section_name = '$section_name'  AND section_grade = '$section_grade'";
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
                    title: "Section   Already Exists!"
                });
            });
        </script>';
    } else {
        // Insert subject into DB
        $insert_query = "INSERT INTO section_tbl ( section_name,section_grade) 
                         VALUES ('$section_name', '$section_grade' )";

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
                        title: "Section Successfully Added!"
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
                        title: "Error Adding Section!"
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
                            <h6 class="m-0 font-weight-bold text-primary">Add Section</h6>
                        </div>
                        <div class="card-body">
                            <form action="" method="POST" autocomplete="off">
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label for="section_name">Section Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id=" " name="section_name"
                                            placeholder="Enter Section Name" required oninput="this.value = this.value.toUpperCase();">
                                    </div>
                                    <div class="col-12">
                                        <a href="subjectGrade" class="btn btn-primary">Back to Page</a>
                                        <button type="submit" name="submit" class="btn btn-success">Add Section</button>
                                    </div>
                                </div>
                            </form>

                        </div>
                    </div>
                    <div class="card shadow mb-4 col-7 ml-2">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">List of Section in Grade <?php echo $section_grade; ?></h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Section Name </th>
                                            <th> </th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <?php
                                        $sql = "SELECT * FROM section_tbl WHERE section_grade = '$section_grade'";
                                        $result = mysqli_query($conn, $sql);
                                        while ($res = mysqli_fetch_assoc($result)) {

                                        ?>
                                            <tr>
                                                <td><?php echo $res['section_name'] ?>
                                                </td>
                                                <td>
                                                    <a href="#"
                                                        class="btn btn-warning btn-sm editSubjectBtn"
                                                        data-id="<?= $res['section_id']; ?>"
                                                        data-grade="<?= htmlspecialchars($res['section_grade'], ENT_QUOTES); ?>"
                                                        data-name="<?= htmlspecialchars($res['section_name'], ENT_QUOTES); ?>">
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

                    const sectionId = this.dataset.id;
                    const sectionName = this.dataset.name;
                    const sectionGrade = this.dataset.grade;

                    Swal.fire({
                        title: 'Edit Section',
                        html: `
        <input id="section_id" type="hidden" value="${sectionId}"> 
        <input id="section_name" class="swal2-input" value="${sectionName}" oninput="this.value = this.value.toUpperCase();">
    `,
                        showCancelButton: true,
                        confirmButtonText: 'Update',
                        cancelButtonText: 'Cancel',
                        preConfirm: () => {
                            return {
                                id: document.getElementById('section_id').value,
                                name: document.getElementById('section_name').value,
                                grade: sectionGrade
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
                                    xhr.open("POST", "update_section.php", true);
                                    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

                                    const data = `section_id=${encodeURIComponent(updated.id)}&section_grade=${encodeURIComponent(updated.grade)}&section_name=${encodeURIComponent(updated.name)}`;

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
                                                title: 'Duplicate Section!',
                                                text: 'Section already exists in this grade.'
                                            });
                                            return;
                                        } else {
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Duplicate Section!',
                                                text: 'Section already exists in this grade.'
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