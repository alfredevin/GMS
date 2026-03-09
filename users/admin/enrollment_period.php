<?php
include '../../config.php';

if (isset($_POST['submit'])) {
    $description = $_POST['description'];
    // Ito lang ang kukunin ng PHP (yung first box), yung automatic 2026 ay display lang sa HTML
    $sy = $_POST['sy'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    $check_query = "SELECT * FROM enrollment_period_tbl WHERE start_date = '$start_date' AND sy = '$sy'";
    $result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($result) > 0) {
        // ERROR: Already exists
        echo '<script>
            document.addEventListener("DOMContentLoaded", function () {
                Swal.fire({
                    toast: true,
                    position: "top-end",
                    icon: "error",
                    title: "Enrollment already exists on this date!",
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true
                });
            });
        </script>';
    } else {
        // SUCCESS: Insert Main Period
        $insert_query = "INSERT INTO enrollment_period_tbl (description, start_date, end_date, sy) 
                         VALUES ('$description', '$start_date', '$end_date', '$sy')";

        if (mysqli_query($conn, $insert_query)) {
            $period_id = mysqli_insert_id($conn); // Get the new ID

            // Insert 4 quarters
            $quarters = ['1st', '2nd', '3rd', '4th'];
            foreach ($quarters as $i => $quarter) {
                $q_start = $_POST['quarterly_s_date_' . ($i + 1)];
                $q_end = $_POST['quarterly_e_date_' . ($i + 1)];

                $insert_quarter = "INSERT INTO enrollment_quarters_tbl (period_id, quarter_name, quarter_start, quarter_end)
                                   VALUES ('$period_id', '$quarter', '$q_start', '$q_end')";
                mysqli_query($conn, $insert_quarter);
            }

            echo '<script>
                document.addEventListener("DOMContentLoaded", function () {
                    Swal.fire({
                        toast: true,
                        position: "top-end",
                        icon: "success",
                        title: "Enrollment Period & Quarters Added!",
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true
                    });
                });
            </script>';
        } else {
            // ERROR: Database error
            echo '<script>
                document.addEventListener("DOMContentLoaded", function () {
                    Swal.fire({
                        toast: true,
                        position: "top-end",
                        icon: "error",
                        title: "Error Adding Enrollment Period!",
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true
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
                    <div class="row">

                        <div class="col-lg-4 col-md-12 mb-4">
                            <div class="card shadow">
                                <div class="card-header py-3 bg-primary text-white">
                                    <h6 class="m-0 font-weight-bold">Create Enrollment Period</h6>
                                </div>
                                <div class="card-body">
                                    <form action="" method="POST" autocomplete="off">

                                        <div class="form-group">
                                            <label class="font-weight-bold">School Year <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="number" name="sy" class="form-control"
                                                    placeholder="Start (e.g. 2025)"
                                                    min="1900" max="2099"
                                                    oninput="calculateEndYear(this)"
                                                    required>

                                                <div class="input-group-append">
                                                    <span class="input-group-text bg-white font-weight-bold">-</span>
                                                </div>

                                                <input type="text" id="sy_end" class="form-control bg-light"
                                                    placeholder="End" readonly>
                                            </div>
                                            <small class="text-muted">Type the start year, end year auto-fills.</small>
                                        </div>

                                        <div class="form-group">
                                            <label class="font-weight-bold">Description <span class="text-danger">*</span></label>
                                            <textarea class="form-control" name="description" rows="2"
                                                placeholder="e.g. 2025-2026 ENROLLMENT"
                                                oninput="this.value = this.value.toUpperCase();" required></textarea>
                                        </div>

                                        <div class="form-row">
                                            <div class="col-md-6 mb-3">
                                                <label class="font-weight-bold">Start Date <span class="text-danger">*</span></label>
                                                <input type="date" name="start_date" class="form-control" min="<?php echo date('Y-m-d') ?>" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="font-weight-bold">End Date <span class="text-danger">*</span></label>
                                                <input type="date" name="end_date" class="form-control" min="<?php echo date('Y-m-d') ?>" required>
                                            </div>
                                        </div>

                                        <hr>

                                        <div class="p-3 bg-light rounded border mb-3">
                                            <h6 class="font-weight-bold text-primary mb-3">Quarterly Schedule</h6>
                                            <?php
                                            $quarters = ['1st', '2nd', '3rd', '4th'];
                                            foreach ($quarters as $index => $label) {
                                            ?>
                                                <div class="form-group mb-2">
                                                    <label class="small font-weight-bold text-uppercase"><?php echo $label; ?> Quarter:</label>
                                                    <div class="input-group input-group-sm">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">Start</span>
                                                        </div>
                                                        <input type="date" name="quarterly_s_date_<?php echo $index + 1; ?>" class="form-control" required>

                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">End</span>
                                                        </div>
                                                        <input type="date" name="quarterly_e_date_<?php echo $index + 1; ?>" class="form-control" required>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </div>

                                        <button type="submit" name="submit" class="btn btn-success btn-block font-weight-bold">
                                            <i class="fas fa-plus-circle"></i> Add Period
                                        </button>

                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-8 col-md-12">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">List of Enrollment Periods</h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>SY</th>
                                                    <th>Description</th>
                                                    <th>Period</th>
                                                    <th class="text-center">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $sql = "SELECT * FROM enrollment_period_tbl ORDER BY enrollment_period_id DESC";
                                                $result = mysqli_query($conn, $sql);
                                                while ($res = mysqli_fetch_assoc($result)) {
                                                    $period_id = $res['enrollment_period_id'];
                                                    // Display SY as "2025-2026"
                                                    $display_sy = $res['sy'] . "-" . ($res['sy'] + 1);
                                                    $date_range = date('M d, Y', strtotime($res['start_date'])) . " - " . date('M d, Y', strtotime($res['end_date']));
                                                ?>
                                                    <tr>
                                                        <td class="font-weight-bold"><?php echo $display_sy ?></td>
                                                        <td><?php echo $res['description'] ?></td>
                                                        <td class="small"><?php echo $date_range ?></td>
                                                        <td class="text-center">
                                                            <button class="btn btn-info btn-sm shadow-sm" data-toggle="modal" data-target="#quarterModal<?php echo $period_id; ?>">
                                                                <i class="fas fa-eye"></i> View
                                                            </button>

                                                            <div class="modal fade" id="quarterModal<?php echo $period_id; ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                                                <div class="modal-dialog" role="document">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header bg-info text-white">
                                                                            <h5 class="modal-title">SY <?php echo $display_sy; ?> - Quarters</h5>
                                                                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                                                <span aria-hidden="true">&times;</span>
                                                                            </button>
                                                                        </div>
                                                                        <div class="modal-body text-left">
                                                                            <table class="table table-sm table-striped">
                                                                                <thead>
                                                                                    <tr>
                                                                                        <th>Quarter</th>
                                                                                        <th>Start</th>
                                                                                        <th>End</th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                    <?php
                                                                                    $q_sql = mysqli_query($conn, "SELECT * FROM enrollment_quarters_tbl WHERE period_id = '$period_id'");
                                                                                    while ($q = mysqli_fetch_assoc($q_sql)) {
                                                                                        echo "<tr>
                                                                                            <td>{$q['quarter_name']}</td>
                                                                                            <td>" . date('M d', strtotime($q['quarter_start'])) . "</td>
                                                                                            <td>" . date('M d', strtotime($q['quarter_end'])) . "</td>
                                                                                        </tr>";
                                                                                    }
                                                                                    ?>
                                                                                </tbody>
                                                                            </table>
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
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
        function calculateEndYear(input) {
            // Limit to 4 characters
            if (input.value.length > 4) {
                input.value = input.value.slice(0, 4);
            }

            const startYear = parseInt(input.value);
            const endYearInput = document.getElementById('sy_end');

            // If valid 4-digit year, auto-fill the next box
            if (input.value.length === 4 && !isNaN(startYear)) {
                endYearInput.value = startYear + 1;
            } else {
                endYearInput.value = "";
            }
        }
    </script>
</body>

</html>