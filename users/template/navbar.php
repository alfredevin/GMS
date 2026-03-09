<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
        <i class="fa fa-bars"></i>
    </button>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;800&display=swap" rel="stylesheet">

    <style>
        /* Container Styling */
        .brand-wrapper {
            text-decoration: none !important;
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
            padding: 5px 10px;
            border-radius: 10px;
        }

        .brand-wrapper:hover {
            background-color: rgba(255, 165, 0, 0.05);
            /* Subtle orange tint on hover */
            transform: translateY(-2px);
            /* Slight lift effect */
        }

        /* Logo Styling */
        .brand-logo {
            height: 45px;
            width: 45px;
            object-fit: cover;
            border-radius: 50%;
            margin-right: 12px;
            border: 2px solid #f6c23e;
            /* Gold border */
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .brand-wrapper:hover .brand-logo {
            transform: rotate(15deg) scale(1.1);
            /* Logo pops on hover */
        }

        /* Main Title Styling */
        .brand-title {
            font-family: 'Poppins', sans-serif;
            font-weight: 800;
            font-size: 1.3rem;
            line-height: 1;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 1px;
            /* Gradient Text Effect */
            background: linear-gradient(to right, #ff6b6b, #f6c23e);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.05);
        }

        /* Subtitle Styling */
        .brand-subtitle {
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            font-size: 0.75rem;
            color: #858796;
            letter-spacing: 0.5px;
            display: block;
            margin-top: 2px;
            transition: color 0.3s;
        }

        .brand-wrapper:hover .brand-subtitle {
            color: #4e73df;
            /* Changes color on hover */
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .brand-title {
                font-size: 1rem;
            }

            .brand-logo {
                height: 35px;
                width: 35px;
            }
        }
    </style>

    <div class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100">
        <a href="index" class="brand-wrapper" title="Go to Dashboard">

            <div class="d-flex flex-column">
                <h5 class="brand-title">
                    Grade Management System
                </h5>
                <span class="brand-subtitle">
                    <i class="fas fa-school mr-1"></i> Bangbang National High School
                </span>
            </div>
        </a>
    </div>

    <ul class="navbar-nav ml-auto">

        <li class="nav-item dropdown no-arrow d-sm-none">
            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button"
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-search fa-fw"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                aria-labelledby="searchDropdown">
                <form class="form-inline mr-auto w-100 navbar-search">
                    <div class="input-group">
                        <input type="text" class="form-control bg-light border-0 small"
                            placeholder="Search for..." aria-label="Search"
                            aria-describedby="basic-addon2">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="button">
                                <i class="fas fa-search fa-sm"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </li>

        <li class="nav-item dropdown no-arrow mx-1">
            <a class="nav-link dropdown-toggle hover-effect" href="#" id="alertsDropdown" role="button"
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-bell fa-fw text-gray-600"></i>
                <?php
                // Check if connection exists before querying
                if (isset($conn)) {
                    $results = $conn->query("SELECT COUNT(*) AS count FROM enrollment_tbl WHERE enrollment_status = 1");
                    $count = 0;
                    if ($results) {
                        $rows = $results->fetch_assoc();
                        $count = $rows['count'];
                    }
                } else {
                    $count = 0;
                }
                ?>
                <span class="badge badge-danger badge-counter" style="<?php echo ($count > 0) ? '' : 'display: none;'; ?>">
                    <?php echo ($count > 0) ? $count : ''; ?>
                </span>
            </a>
            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                aria-labelledby="alertsDropdown">
                <h6 class="dropdown-header bg-primary border-0">
                    New Enrollment Requests
                </h6>
                <div style="max-height: 300px; overflow-y: auto;">
                    <?php
                    if (isset($conn)) {
                        $new_enroll = $conn->query('SELECT * FROM enrollment_tbl WHERE enrollment_status = 1 ORDER BY date_created DESC LIMIT 5');
                        if (mysqli_num_rows($new_enroll) > 0) {
                            while ($row_enroll = $new_enroll->fetch_assoc()) {
                                $name = $row_enroll['firstname'] . ' ' . $row_enroll['lastname'];
                    ?>
                                <a class="dropdown-item d-flex align-items-center notification-item" href="new_enrolles">
                                    <div class="mr-3">
                                        <div class="icon-circle bg-primary">
                                            <i class="fas fa-user-plus text-white"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="small text-gray-500"><?php echo date("F j, Y", strtotime($row_enroll['date_created'])); ?></div>
                                        <span class="font-weight-bold text-gray-800"><?php echo $name; ?></span>
                                        <div class="small text-gray-600">Requesting for enrollment</div>
                                    </div>
                                </a>
                    <?php
                            }
                        } else {
                            echo '<a class="dropdown-item text-center small text-gray-500" href="#">No new notifications</a>';
                        }
                    }
                    ?>
                </div>
                <a class="dropdown-item text-center small text-gray-500 bg-light" href="new_enrolles">Show All Requests</a>
            </div>
        </li>

        <div class="topbar-divider d-none d-sm-block"></div>

        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle hover-effect" href="#" id="userDropdown" role="button" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
                <span class="mr-2 d-none d-lg-inline text-gray-600 small font-weight-bold">
                    <?php
                    if (isset($_SESSION['userid'])) {
                        $userid = $_SESSION['userid'];
                        $user_query = "SELECT fullname FROM user_tbl WHERE userid = '$userid'";
                        $result_user = mysqli_query($conn, $user_query);
                        $row_user_session = mysqli_fetch_assoc($result_user);
                        echo $row_user_session['fullname'];
                    } else if (isset($_SESSION['teacher_id'])) {
                        $teacher_id = $_SESSION['teacher_id'];
                        $teacher_query = "SELECT teacher_name FROM teacher_tbl WHERE teacher_id = '$teacher_id'";
                        $result_teacher = mysqli_query($conn, $teacher_query);
                        $row_teacher_session = mysqli_fetch_assoc($result_teacher);
                        echo $row_teacher_session['teacher_name'];
                    } else if (isset($_SESSION['student_id'])) {
                        $my_student_id = $_SESSION['student_id'];
                        $student_query = "SELECT firstname, lastname FROM student_tbl
                        INNER JOIN enrollment_tbl ON enrollment_tbl.enrollmentId = student_tbl.enrollment_id WHERE student_id = '$my_student_id'";
                        $result_student = mysqli_query($conn, $student_query);
                        $row_student_session = mysqli_fetch_assoc($result_student);
                        echo $row_student_session['firstname'] . ' ' . $row_student_session['lastname'];
                    } else {
                        // If no session, redirect logic should be handled at page top, but safely display Guest here
                        echo "Guest";
                    }
                    ?>
                </span>
                <img class="img-profile rounded-circle shadow-sm" src="../img/undraw_profile.svg">
            </a>

            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                <a class="dropdown-item" href="profile.php">
                    <i class="fas fa-user-circle fa-sm fa-fw mr-2 text-gray-400"></i>
                    My Profile & Settings
                </a>
                <?php
                if (isset($_SESSION['userid'])) {
                    echo ' <a class="dropdown-item" href="logs">
                    <i class="fas fa-file fa-sm fa-fw mr-2 text-gray-400"></i>
                    User Logs
                </a>';
                }
                ?>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="#" id="signOutLinkNav">
                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-danger"></i>
                    Logout
                </a>
            </div>
        </li>

    </ul>

</nav>

<style>
    /* Hover Effects */
    .hover-effect {
        transition: background-color 0.2s ease;
        border-radius: 5px;
    }

    .hover-effect:hover {
        background-color: #f8f9fc;
    }

    /* Notification Item Hover */
    .notification-item {
        transition: background-color 0.2s;
        padding: 10px 20px;
        border-bottom: 1px solid #eaecf4;
    }

    .notification-item:hover {
        background-color: #f1f3f9;
        text-decoration: none;
    }

    .notification-item:last-child {
        border-bottom: none;
    }

    /* Dropdown Animation Override */
    .animated--grow-in {
        animation-duration: 0.2s;
    }

    /* Profile Image Hover */
    .img-profile {
        transition: transform 0.2s;
    }

    .nav-link:hover .img-profile {
        transform: scale(1.1);
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.getElementById('signOutLinkNav').addEventListener('click', function(event) {
        event.preventDefault();

        Swal.fire({
            title: 'Ready to Leave?',
            text: "Select 'Logout' below if you are ready to end your current session.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Logout',
            cancelButtonText: 'Cancel',
            reverseButtons: true // Swaps button positions for better UX
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading state before redirect
                Swal.fire({
                    title: 'Logging out...',
                    timer: 1000,
                    timerProgressBar: true,
                    didOpen: () => {
                        Swal.showLoading()
                    }
                }).then(() => {
                    window.location.href = '../../logout.php'; // Ensure path is correct relative to file location
                });
            }
        });
    });
</script>