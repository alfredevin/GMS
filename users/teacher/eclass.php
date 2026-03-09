<?php
include '../../config.php';

// --- 1. EXPORT TO EXCEL LOGIC (Must be at the very top) ---
if (isset($_POST['export_excel'])) {
    $teacher_id_export = $_POST['teacher_id_export'];
    $filename = "My_Files_Report_" . date('Ymd') . ".xls";

    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=\"$filename\"");

    // Simple Excel Table Output
    echo "File Name\tDate Uploaded\tFile Type\tFile Path\n"; // Headers

    $sql = "SELECT * FROM eclass_files WHERE teacher_id = '$teacher_id_export' ORDER BY date_upload DESC";
    $result = mysqli_query($conn, $sql);

    while ($row = mysqli_fetch_assoc($result)) {
        // Clean data for Excel
        $name = $row['file_display_name'];
        $date = date('M d, Y h:i A', strtotime($row['date_upload']));
        $ext = pathinfo($row['file_path'], PATHINFO_EXTENSION);
        $path = $row['file_path'];

        echo "$name\t$date\t$ext\t$path\n";
    }
    exit; // Stop the script here so it only downloads the file
}

// --- 2. REGULAR PAGE LOGIC ---
$swal_icon = "";
$swal_title = "";
$show_swal = false;

if (isset($_POST['upload_file'])) {
    $teacher_id = $_POST['teacher_id'];
    $file_display_name = $_POST['file_name'];
    $date_upload = date('Y-m-d H:i:s');

    $target_dir = "./uploads/";

    $original_filename = basename($_FILES["file_content"]["name"]);
    $file_extension = strtolower(pathinfo($original_filename, PATHINFO_EXTENSION));

    // Add time to filename to avoid duplicates
    $new_filename = time() . "_" . preg_replace("/[^a-zA-Z0-9.]/", "", $original_filename);
    $target_file = $target_dir . $new_filename;

    $uploadOk = 1;

    // Check size (10MB)
    if ($_FILES["file_content"]["size"] > 10000000) {
        $show_swal = true;
        $swal_icon = "error";
        $swal_title = "File is too large! Max 10MB.";
        $uploadOk = 0;
    }

    // Allowed types (Added xls, xlsx, csv for Excel support)
    $allowed_types = array("jpg", "png", "jpeg", "pdf", "docx", "pptx", "xlsx", "xls", "csv", "txt", "zip");
    if (!in_array($file_extension, $allowed_types)) {
        $show_swal = true;
        $swal_icon = "error";
        $swal_title = "Invalid file type.";
        $uploadOk = 0;
    }

    if ($uploadOk == 1) {
        if (move_uploaded_file($_FILES["file_content"]["tmp_name"], $target_file)) {
            $stmt = $conn->prepare("INSERT INTO eclass_files (teacher_id, file_display_name, file_path, date_upload) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isss", $teacher_id, $file_display_name, $new_filename, $date_upload);

            if ($stmt->execute()) {
                $show_swal = true;
                $swal_icon = "success";
                $swal_title = "File Uploaded Successfully!";
            } else {
                $show_swal = true;
                $swal_icon = "error";
                $swal_title = "Database Error.";
            }
            $stmt->close();
        } else {
            $show_swal = true;
            $swal_icon = "error";
            $swal_title = "Error moving file.";
        }
    }
}

// Delete Logic
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $query = "SELECT file_path FROM eclass_files WHERE file_id = '$id'";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);

    if ($row) {
        $file_to_delete = "./uploads/" . $row['file_path'];
        if (file_exists($file_to_delete)) {
            unlink($file_to_delete);
        }
        mysqli_query($conn, "DELETE FROM eclass_files WHERE file_id='$id'");
        echo "<script>window.location.href='eclass_upload.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<?php include './../template/header.php' ?>

<style>
    .upload-area {
        border: 2px dashed #4e73df;
        background-color: #f8f9fc;
        padding: 30px;
        text-align: center;
        border-radius: 10px;
        cursor: pointer;
        transition: 0.3s;
    }

    .upload-area:hover {
        background-color: #eaecf4;
        border-color: #224abe;
    }

    .file-icon-lg {
        font-size: 40px;
        color: #4e73df;
        margin-bottom: 10px;
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
                        <h1 class="h3 mb-0 text-gray-800">eClass File Manager</h1>

                        <form method="POST" action="">
                            <input type="hidden" name="teacher_id_export" value="<?php echo $teacher_id; ?>">
                            <button type="submit" name="export_excel" class="d-none d-sm-inline-block btn btn-sm btn-success shadow-sm">
                                <i class="fas fa-file-excel fa-sm text-white-50"></i> Export List to Excel
                            </button>
                        </form>
                    </div>

                    <div class="row">
                        <div class="col-lg-4">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3 bg-primary text-white">
                                    <h6 class="m-0 font-weight-bold"><i class="fas fa-cloud-upload-alt"></i> Upload Files</h6>
                                </div>
                                <div class="card-body">
                                    <form action="" method="POST" enctype="multipart/form-data" autocomplete="off">
                                        <input type="hidden" name="teacher_id" value="<?php echo $teacher_id; ?>">

                                        <div class="form-group">
                                            <label class="font-weight-bold text-gray-700">File Name / Title</label>
                                            <input type="text" name="file_name" class="form-control" placeholder="e.g. Science Quiz 1" required>
                                        </div>

                                        <div class="form-group">
                                            <label class="font-weight-bold text-gray-700">Attach File</label>
                                            <div class="upload-area" onclick="document.getElementById('customFile').click()">
                                                <i class="fas fa-file-upload file-icon-lg"></i>
                                                <p class="mb-0 text-gray-600" id="fileNameDisplay">Click to Browse or Drag File Here</p>
                                                <small class="text-muted d-block mt-2">Supports: Excel, PDF, Word, Images</small>
                                            </div>
                                            <input type="file" name="file_content" id="customFile" style="display:none;" required>
                                        </div>

                                        <button type="submit" name="upload_file" class="btn btn-primary btn-block btn-lg">
                                            Upload Now
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-8">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">Uploaded Documents</h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>Document</th>
                                                    <th>Date</th>
                                                    <th class="text-center" width="15%">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $sql = "SELECT * FROM eclass_files WHERE teacher_id = '$teacher_id' ORDER BY date_upload DESC";
                                                $result = mysqli_query($conn, $sql);
                                                while ($row = mysqli_fetch_assoc($result)) {
                                                    $file_url = "./uploads/" . $row['file_path'];
                                                    $ext = strtolower(pathinfo($row['file_path'], PATHINFO_EXTENSION));

                                                    // Dynamic Icon Logic
                                                    $icon = "fa-file text-secondary"; // Default
                                                    if (in_array($ext, ['xls', 'xlsx', 'csv'])) $icon = "fa-file-excel text-success";
                                                    elseif (in_array($ext, ['pdf'])) $icon = "fa-file-pdf text-danger";
                                                    elseif (in_array($ext, ['doc', 'docx'])) $icon = "fa-file-word text-primary";
                                                    elseif (in_array($ext, ['jpg', 'png', 'jpeg'])) $icon = "fa-file-image text-info";
                                                ?>
                                                    <tr>
                                                        <td class="align-middle">
                                                            <div class="d-flex align-items-center">
                                                                <div class="mr-3">
                                                                    <i class="fas <?php echo $icon; ?> fa-2x"></i>
                                                                </div>
                                                                <div>
                                                                    <div class="font-weight-bold text-gray-800"><?php echo $row['file_display_name']; ?></div>
                                                                    <div class="small text-gray-500"><?php echo $row['file_path']; ?></div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td class="align-middle">
                                                            <?php echo date('M d, Y', strtotime($row['date_upload'])); ?><br>
                                                            <small class="text-muted"><?php echo date('h:i A', strtotime($row['date_upload'])); ?></small>
                                                        </td>
                                                        <td class="text-center align-middle">
                                                            <div class="btn-group">
                                                                <a href="<?php echo $file_url; ?>" download class="btn btn-sm btn-light border" title="Download">
                                                                    <i class="fas fa-download text-primary"></i>
                                                                </a>
                                                                <a href="?delete=<?php echo $row['file_id']; ?>" class="btn btn-sm btn-light border delete-btn" title="Delete">
                                                                    <i class="fas fa-trash text-danger"></i>
                                                                </a>
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

    <a class="scroll-to-top rounded" href="#page-top"><i class="fas fa-angle-up"></i></a>
    <?php include './../template/script.php'; ?>

    <?php if ($show_swal): ?>
        <script>
            Swal.fire({
                icon: '<?php echo $swal_icon; ?>',
                title: '<?php echo $swal_title; ?>',
                timer: 2000,
                showConfirmButton: false
            });
        </script>
    <?php endif; ?>

    <script>
        // Enhanced File Input Display
        document.getElementById('customFile').addEventListener('change', function() {
            var fileName = this.files[0].name;
            var fileSize = (this.files[0].size / 1024 / 1024).toFixed(2); // Convert to MB

            document.getElementById('fileNameDisplay').innerHTML =
                `<strong>Selected:</strong> ${fileName} <br> <span class="badge badge-info">${fileSize} MB</span>`;

            // Change color to show success
            document.querySelector('.upload-area').style.borderColor = "#1cc88a";
            document.querySelector('.upload-area').style.backgroundColor = "#f0fdf4";
        });

        // Delete Confirmation
        $('.delete-btn').on('click', function(e) {
            e.preventDefault();
            const href = $(this).attr('href');
            Swal.fire({
                title: 'Delete this file?',
                text: "This action cannot be undone.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e74a3b',
                confirmButtonText: 'Yes, Delete it'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.location.href = href;
                }
            })
        });
    </script>
</body>

</html>