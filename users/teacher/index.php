<?php
include '../../config.php';
?>


<!DOCTYPE html>
<html lang="en">

<?php include './../template/header.php'; ?>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <?php include './../template/sidebar.php'; ?>

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">
            <!-- Main Content -->
            <div id="content">
                <!-- Topbar -->
                <?php include './../template/navbar.php'; ?>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">
                    <!-- Content Row -->
                    <div class="row">
                        <div id="adminCard" class="col-xl-12 col-md-6 mb-4">
                            <div class="card shadow h-100 py-4 px-4 text-white" id="adminCardBody">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <div>
                                            <h5 class="font-weight-bold text-primary mb-1" id="greeting"> </h5>
                                        </div>
                                        <div id="datetime" class="text-end fw-bold" style="font-size: 1.1rem; color: #6c757d;"></div>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1">
                                            <p class="mb-0 text-gray-800" style="font-size:1rem;">
                                                This dashboard provides teachers with an intuitive and centralized platform to manage student-related activities efficiently.
                                                Instructors can input and monitor grades, create and publish announcements for quizzes, activities, and exams, and track student attendance in real time.
                                                Designed to streamline academic tasks, this tool empowers teachers to deliver organized and effective classroom management.
                                            </p>

                                        </div>
                                        <div class="ms-3">
                                            <img src="https://cliparts.co/cliparts/riL/nxz/riLnxz6rT.gif" alt="Admin Image" width="150">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                Students</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                <?php
                                                $count = mysqli_query($conn, "SELECT COUNT(*) AS totalStudent FROM student_tbl WHERE status = 1");
                                                $row = mysqli_fetch_assoc($count);
                                                echo $row['totalStudent'];
                                                ?>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-users fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Earnings (Monthly) Card Example -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-info shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Teacher
                                            </div>
                                            <div class="row no-gutters align-items-center">
                                                <div class="col-auto">
                                                    <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">
                                                        <?php
                                                        $count = mysqli_query($conn, "SELECT COUNT(*) AS totalTeacher FROM teacher_tbl");
                                                        $row = mysqli_fetch_assoc($count);
                                                        echo $row['totalTeacher'];
                                                        ?>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-chalkboard-teacher fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pending Requests Card Example -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-warning shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                Subject</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">

                                                <?php
                                                $count = mysqli_query($conn, "SELECT COUNT(*) AS totalSubject FROM subject_tbl WHERE teacher_assign = '$teacher_id'");
                                                $row = mysqli_fetch_assoc($count);
                                                echo $row['totalSubject'];
                                                ?>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-book fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>



                    </div>
                    <!-- /.container-fluid -->

                </div>
                <!-- End of Main Content -->

                <!-- Footer -->
                <?php include './../template/footer.php'; ?>

                <!-- End of Footer -->

            </div>
            <!-- End of Content Wrapper -->

        </div>
        <!-- End of Page Wrapper -->

        <!-- Scroll to Top Button-->
        <a class="scroll-to-top rounded" href="#page-top">
            <i class="fas fa-angle-up"></i>
        </a>

        <?php include './../template/script.php'; ?>
        <script>
            function updateDashboardCard() {
                const now = new Date();
                const hour = now.getHours();

                const datetimeOptions = {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit'
                };
                document.getElementById('datetime').innerText = now.toLocaleDateString('en-US', datetimeOptions);

                let greeting = "";
                let bgColor = "";
                let textColor = "text-white";
                const cardBody = document.getElementById("adminCardBody");

                if (hour < 12) {
                    greeting = "Good Morning ";
                    cardBody.style.background = "white"; // light orange
                } else if (hour < 18) {
                    greeting = "Good Afternoon ";
                    cardBody.style.background = "white"; // yellow/orange
                } else {
                    greeting = "Good Evening ";
                    cardBody.style.background = "white"; // dark blue/black
                }

                document.getElementById("greeting").innerText = greeting;
            }

            setInterval(updateDashboardCard, 1000);
            updateDashboardCard();
        </script>
</body>

</html>