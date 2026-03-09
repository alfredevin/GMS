<?php
include '../../config.php';

if (isset($_POST['submit'])) {
    $fullname = strtoupper(trim($_POST['fullname']));
    $email = $_POST['email'];
    $username = $_POST['username'];
    $contact_number = $_POST['contact_number'];
    $position = $_POST['position'];
    $password = 'Password123';
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $check_user = mysqli_prepare($conn, "SELECT * FROM user_tbl WHERE username = ?");
    mysqli_stmt_bind_param($check_user, "s", $username);
    mysqli_execute($check_user);
    $result_check = mysqli_stmt_get_result($check_user);

    if (mysqli_num_rows($result_check) > 0) {
        echo '<script>
                document.addEventListener("DOMContentLoaded", function () {
                    Swal.fire({
                        icon: "error",
                        title: "UserId already exists!",
                        toast: true,
                        position: "top-end",
                        showConfirmButton: false,
                        timer: 2000
                    });
                });
            </script>';
    } else {
        $insert_event = mysqli_prepare($conn, "INSERT INTO user_tbl (fullname, email, username, contact_number, position,password) VALUES (?, ?, ?, ?, ?,?)");
        mysqli_stmt_bind_param($insert_event, "ssssss", $fullname, $email, $username, $contact_number, $position, $hashed_password);
        if (mysqli_execute($insert_event)) {
            echo '<script>
                document.addEventListener("DOMContentLoaded", function () {
                    Swal.fire({
                        icon: "success",
                        title: "User successfully added!",
                        toast: true,
                        position: "top-end",
                        showConfirmButton: false,
                        timer: 2000
                    });
                });
            </script>';
        }
    }
}

if (isset($_POST['update'])) {
    $userid  = $_POST['userid'];
    $fullname = strtoupper(trim($_POST['fullname']));
    $email = $_POST['email'];
    $username = $_POST['username'];
    $contact_number = $_POST['contact_number'];
    $position = $_POST['position'];

    $check_user = mysqli_prepare($conn, "SELECT * FROM user_tbl WHERE username = ? AND userid  != ?");
    mysqli_stmt_bind_param($check_user, "si", $username, $userid);
    mysqli_execute($check_user);
    $result_check = mysqli_stmt_get_result($check_user);

    if (mysqli_num_rows($result_check) > 0) {
        echo '<script>s
                document.addEventListener("DOMContentLoaded", function () {
                    Swal.fire({
                        icon: "error",
                        title: "UserID title already exists!",
                        toast: true,
                        position: "top-end",
                        showConfirmButton: false,
                        timer: 2000
                    });
                });
            </script>';
    } else {
        $update_event = mysqli_prepare($conn, "UPDATE user_tbl SET fullname = ?, email = ?, username = ?, contact_number = ?, position = ? WHERE userid  = ?");
        mysqli_stmt_bind_param($update_event, "sssssi", $fullname, $email, $username, $contact_number, $position, $userid);
        if (mysqli_execute($update_event)) {
            echo '<script>
                document.addEventListener("DOMContentLoaded", function () {
                    Swal.fire({
                        icon: "success",
                        title: "User successfully updated!",
                        toast: true,
                        position: "top-end",
                        showConfirmButton: false,
                        timer: 2000
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
                <div class="container-fluid">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <div class="row">
                                <div class="col">
                                    <h6 class="m-0 font-weight-bold text-primary">List of Users</h6>
                                </div>
                                <div class="col text-right">
                                    <a href="#addEvent" data-toggle="modal" class="btn btn-success btn-sm"><i class="fas fa-plus"></i> Add User</a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>UserID</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Contact Number</th>
                                            <th>Position</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $query = "SELECT * FROM user_tbl  ";
                                        $result = mysqli_query($conn, $query);

                                        while ($row = mysqli_fetch_assoc($result)) {
                                        ?>
                                            <tr>
                                                <td><?= $row['username']; ?></td>
                                                <td><?= $row['fullname']; ?></td>
                                                <td><?= $row['email']; ?></td>
                                                <td><?= $row['contact_number']; ?></td>
                                                <td><?= $row['position']; ?></td>
                                                <td>
                                                    <a href="#editUser<?= $row['userid']; ?>" data-toggle="modal" class="btn btn-warning btn-sm">
                                                        <i class="fas fa-pen-square"></i> Edit
                                                    </a>
                                                </td>
                                            </tr>

                                            <!-- Edit Modal -->
                                            <div class="modal fade" id="editUser<?= $row['userid']; ?>" tabindex="-1" role="dialog" aria-labelledby="editEventLabel" aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <form method="POST">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Update User</h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <input type="hidden" name="userid" value="<?= $row['userid']; ?>">
                                                                <div class="form-group">
                                                                    <label>User ID</label>
                                                                    <input type="text" name="username" class="form-control" value="<?= $row['username']; ?>" required>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label> Name</label>
                                                                    <input type="text" name="fullname" class="form-control" value="<?= $row['fullname']; ?>" oninput="this.value = this.value.toUpperCase();" required>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label>email</label>
                                                                    <input type="email" name="email" class="form-control" value="<?= $row['email']; ?>" required>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label>Contact Number</label>
                                                                    <input type="number"
                                                                        name="contact_number"
                                                                        class="form-control"
                                                                        value="<?= $row['contact_number']; ?>"
                                                                        maxlength="11"
                                                                        oninput="this.value = this.value.slice(0, 11);"
                                                                        required>
                                                                </div>

                                                                <div class="form-group">
                                                                    <label>Usertype</label>
                                                                    <select name="position" class="form-control">
                                                                        <option value="" disabled <?= empty($row['position']) ? 'selected' : '' ?>>SELECT USER</option>
                                                                        <option value="ADMIN" <?= ($row['position'] == 'ADMIN') ? 'selected' : '' ?>>ADMIN</option>
                                                                        <option value="PRINCIPAL" <?= ($row['position'] == 'PRINCIPAL') ? 'selected' : '' ?>>PRINCIPAL</option>
                                                                        <option value="ENROLLMENT ADVISER" <?= ($row['position'] == 'ENROLLMENT ADVISER') ? 'selected' : '' ?>>ENROLLMENT ADVISER</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="submit" name="update" class="btn btn-primary">Save Changes</button>
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
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

    <!-- Add Event Modal -->
    <div class="modal fade" id="addEvent" tabindex="-1" role="dialog" aria-labelledby="addEventLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Add User Account</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>User Id</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" name="fullname" class="form-control" oninput="this.value = this.value.toUpperCase();" required>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Contact Number</label>
                            <input type="number" name="contact_number" class="form-control"
                                maxlength="11"
                                oninput="this.value = this.value.slice(0, 11);" required>
                        </div>
                        <div class="form-group">
                            <label>Usertype</label>
                            <select name="position" id="" class="form-control">
                                <option value="" selected disabled>SELECT USER</option>
                                <option value="ADMIN">ADMIN</option>
                                <option value="PRINCIPAL">PRINCIPAL</option>
                                <option value="ENROLLMENT ADVISER">ENROLLMENT ADVISER</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="submit" class="btn btn-primary">Save User</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include './../template/script.php'; ?>
</body>

</html>