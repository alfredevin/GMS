<?php
include '../../config.php';
// AYUSIN ANG TIMEZONE PARA HINDI ADVANCE ANG ORAS SA DISPLAY
date_default_timezone_set('Asia/Manila');
?>

<!DOCTYPE html>
<html lang="en">
<?php include './../template/header.php'; ?>

<style>
    /* Custom Styling for Logs */
    .log-avatar {
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
        box-shadow: 0 3px 10px rgba(78, 115, 223, 0.2);
    }

    .ip-badge {
        background-color: #f8f9fc;
        border: 1px solid #e3e6f0;
        color: #858796;
        padding: 5px 10px;
        border-radius: 15px;
        font-family: monospace;
        font-size: 12px;
    }

    .time-text {
        font-weight: 600;
        color: #1cc88a;
    }

    .table td {
        vertical-align: middle !important;
    }
</style>

<body id="page-top">
    <div id="wrapper">
        <?php include './../template/sidebar.php'; ?>

        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include './../template/navbar.php'; ?>

                <div class="container-fluid">

                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">User Login History</h1>
                        <div class="text-gray-500 small">Monitor system access and security</div>
                    </div>

                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-white">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-list-ul mr-2"></i> Login Records
                            </h6>

                            <div class="input-group input-group-sm" style="width: 250px;">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-transparent border-0"><i class="fas fa-search"></i></span>
                                </div>
                                <input type="text" id="logSearch" class="form-control bg-light border-0 small" placeholder="Search user..." onkeyup="filterLogs()">
                            </div>
                        </div>

                        <div class="card-body px-0">
                            <div class="table-responsive">
                                <table class="table table-hover" id="dataTable" width="100%" cellspacing="0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th class="pl-4">User Profile</th>
                                            <th>IP Address</th>
                                            <th>Login Timestamp</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // ITEM 8 FIX: ORDER BY login_time DESC (Pinakabago muna)
                                        $sql = "SELECT * FROM userlogs_tbl ORDER BY login_time DESC LIMIT 100";
                                        $result = mysqli_query($conn, $sql);

                                        if (mysqli_num_rows($result) > 0) {
                                            while ($row = mysqli_fetch_assoc($result)) {
                                                $username_id = $row['username']; // Ito ang naglalaman ng ID
                                                $ip = $row['ip_address'];
                                                $time = date('M d, Y h:i A', strtotime($row['login_time']));
                                                
                                                // ITEM 7 FIX: KUNIN ANG PANGALAN GAMIT ANG ID
                                                $display_name = $username_id; // Default kung hindi mahanap
                                                $role_label = "Admin";
                                                $avatar_bg = "#e74a3b"; // Pula para sa Admin

                                                // Check sa Teacher Table
                                                $check_teacher = mysqli_query($conn, "SELECT teacher_name FROM teacher_tbl WHERE teacher_id = '$username_id'");
                                                if(mysqli_num_rows($check_teacher) > 0) {
                                                    $t_data = mysqli_fetch_assoc($check_teacher);
                                                    $display_name = $t_data['teacher_name'];
                                                    $role_label = "Teacher";
                                                    $avatar_bg = "#4e73df"; // Asul para sa Teacher
                                                } else {
                                                    // Check sa Student Table
                                                    $check_student = mysqli_query($conn, "SELECT firstname, lastname FROM student_tbl
                                                    INNER JOIN enrollment_tbl ON enrollment_tbl.enrollmentId = student_tbl.enrollment_id
                                                   WHERE student_id = '$username_id' OR lrn = '$username_id'");
                                                    if(mysqli_num_rows($check_student) > 0) {
                                                        $s_data = mysqli_fetch_assoc($check_student);
                                                        $display_name = $s_data['firstname'] . ' ' . $s_data['lastname'];
                                                        $role_label = "Student";
                                                        $avatar_bg = "#1cc88a"; // Berde para sa Student
                                                    }
                                                }

                                                $initial = strtoupper(substr($display_name, 0, 1));
                                        ?>
                                                <tr class="log-row">
                                                    <td class="pl-4">
                                                        <div class="d-flex align-items-center">
                                                            <div class="log-avatar mr-3" style="background-color: <?= $avatar_bg ?>;"><?= $initial ?></div>
                                                            <div>
                                                                <div class="font-weight-bold text-dark text-uppercase"><?= htmlspecialchars($display_name) ?></div>
                                                                <div class="small text-muted">
                                                                    ID: <?= htmlspecialchars($username_id) ?> | <span class="text-success"><i class="fas fa-check-circle"></i> Logged In as <?= $role_label ?></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                  
                                                    <td>
                                                        <span class="ip-badge"><i class="fas fa-network-wired mr-1"></i> <?= htmlspecialchars($ip) ?></span>
                                                    </td>
                                                    <td>
                                                        <span class="time-text"><?= $time ?></span>
                                                    </td>
                                                </tr>
                                        <?php
                                            }
                                        } else {
                                            echo '<tr><td colspan="3" class="text-center py-5 text-muted">No login records found.</td></tr>';
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

    <script>
        function filterLogs() {
            var input, filter, table, tr, td, i, txtValue;
            input = document.getElementById("logSearch");
            filter = input.value.toUpperCase();
            table = document.getElementById("dataTable");
            tr = table.getElementsByClassName("log-row");

            for (i = 0; i < tr.length; i++) {
                // Search by Username/Name (Column 0)
                td = tr[i].getElementsByTagName("td")[0];
                if (td) {
                    txtValue = td.textContent || td.innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        tr[i].style.display = "";
                    } else {
                        tr[i].style.display = "none";
                    }
                }
            }
        }
    </script>

</body>

</html>