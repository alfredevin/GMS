<?php
session_start();

if (isset($_SESSION['userid'])) {
    $userid = $_SESSION['userid'] ?? null;
    $user_query = "SELECT * FROM user_tbl  WHERE userid = '$userid'";
    $result_user = mysqli_query($conn, $user_query);
    $row_user_session = mysqli_fetch_assoc($result_user);
    $user_position = $row_user_session["position"];
} else if (isset($_SESSION['teacher_id'])) {
    $teacher_id = $_SESSION['teacher_id'] ?? null;
} else if (isset($_SESSION['student_id'])) {
    $my_student_id = $_SESSION['student_id'] ?? null;
} else {
    header("Location: ../");
    exit();
}
$current_page = basename($_SERVER['PHP_SELF'], '.php');



?>
<ul class="navbar-nav  sidebar sidebar-dark accordion" id="accordionSidebar" style="background:#2C2C2C ;">
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index">
        <div class="sidebar-brand-icon ">
            <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS9BRQM_uqdXGt-qLZgiHczlYTKTnEcxifgsQ&s" width="50"
                style="border-radius: 50%;" alt="">
        </div>
    </a>
    <hr class="sidebar-divider my-0">
    <li class="nav-item <?= $current_page == 'index' ? 'active' : '' ?>">
        <a class="nav-link" href="index">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li>

    <?php if (isset($_SESSION['teacher_id'])): ?>
        <?php
        $select_tec = "SELECT * FROM teacher_tbl WHERE teacher_type = 'Class Adviser' AND teacher_id = '$teacher_id'";
        $result_sec_tec = mysqli_query($conn, $select_tec);
        while ($result_sec_tec_row = mysqli_fetch_assoc($result_sec_tec)) {
            echo '  <hr class="sidebar-divider my-0">
   <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#class_students"
                aria-expanded="true" aria-controls="class_students">
                <i class="fas fa-fw fa-folder"></i>
                <span>Class Students</span>
            </a>
            <div id="class_students" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Manage Class Students:</h6>
                    <a class="collapse-item" href="class_attendance">Class Attendance</a>
                    <a class="collapse-item" href="class_subject">Class Subject</a>
                </div>
            </div>
        </li>';
        }
        ?>
    <?php endif; ?>
    <hr class="sidebar-divider">
    <?php if (isset($_SESSION['userid'])): ?>
        <?php
        $enrollment_pages = ['new_enrolles', 'for_submission', 'enrolled_students'];
        $is_enrollment_active = in_array($current_page, $enrollment_pages);
        ?>

        <?php if ($user_position == 'ENROLLMENT ADVISER' || $user_position == 'ADMIN'): ?>
            <li class="nav-item <?= $is_enrollment_active ? 'active' : '' ?>">
                <a class="nav-link <?= $is_enrollment_active ? '' : 'collapsed' ?>" href="#" data-toggle="collapse" data-target="#collapseUtilities">
                    <i class="fas fa-user"></i>
                    <span>Enrollment</span>
                </a>
                <div id="collapseUtilities" class="collapse <?= $is_enrollment_active ? 'show' : '' ?>">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Enrollment:</h6>
                        <?php if ($user_position == 'ENROLLMENT ADVISER'): ?>
                            <a class="collapse-item <?= $current_page == 'new_enrolles' ? 'active' : '' ?>" href="new_enrolles">New Enrollees</a>
                            <a class="collapse-item <?= $current_page == 'for_submission' ? 'active' : '' ?>" href="for_submission">For Submission</a>
                        <?php endif; ?>

                        <?php if ($user_position == 'ENROLLMENT ADVISER' || $user_position == 'ADMIN'): ?>
                            <a class="collapse-item <?= $current_page == 'enrolled_students' ? 'active' : '' ?>" href="enrolled_students">Enrolled Students</a>
                        <?php endif; ?>

                    </div>
                </div>
            </li>
        <?php endif; ?>
        <?php
        $student_pages = ['student_list', 'student_clearance', 'graduate_students'];
        $is_student_active = in_array($current_page, $student_pages);
        ?>

        <?php if ($user_position == 'ADMIN'): ?>
            <li class="nav-item <?= $is_student_active ? 'active' : '' ?>">
                <a class="nav-link <?= $is_student_active ? '' : 'collapsed' ?>" href="#" data-toggle="collapse" data-target="#student_manage"
                    aria-expanded="<?= $is_student_active ? 'true' : 'false' ?>" aria-controls="student_manage">
                    <i class="fas fa-users"></i>
                    <span>Student</span>
                </a>
                <div id="student_manage" class="collapse <?= $is_student_active ? 'show' : '' ?>" aria-labelledby="headingUtilities"
                    data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Enrollment:</h6>
                        <a class="collapse-item <?= $current_page == 'student_list' ? 'active' : '' ?>" href="student_list">Student List</a>
                        <a class="collapse-item <?= $current_page == 'sscList' ? 'active' : '' ?>" href="sscList">Special Science Class <br> Student List</a>
                        <a class="collapse-item <?= $current_page == 'student_clearance' ? 'active' : '' ?>" href="student_clearance">Promote Student</a>
                        <a class="collapse-item <?= $current_page == 'graduate_students' ? 'active' : '' ?>" href="graduate_students">Graduate Students</a>
                    </div>
                </div>
            </li>
            <?php
            $manage_pages = ['assignSubject', 'teacherList'];
            $is_manage_active = in_array($current_page, $manage_pages);
            ?>

        <?php endif; ?>
        <?php if ($user_position == 'PRINCIPAL' || $user_position == 'ADMIN'): ?>
            <li class="nav-item <?= $is_manage_active ? 'active' : '' ?>">
                <a class="nav-link <?= $is_manage_active ? '' : 'collapsed' ?>" href="#" data-toggle="collapse" data-target="#collapseTwo"
                    aria-expanded="<?= $is_manage_active ? 'true' : 'false' ?>" aria-controls="collapseTwo">
                    <i class="fas fa-fw fa-folder"></i>
                    <span>Manage</span>
                </a>
                <div id="collapseTwo" class="collapse <?= $is_manage_active ? 'show' : '' ?>" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Manage:</h6>
                        <a class="collapse-item <?= $current_page == 'assignSubject' ? 'active' : '' ?>" href="assignSubject">Subject List</a>
                        <a class="collapse-item <?= $current_page == 'teacherList' ? 'active' : '' ?>" href="teacherList">Assign Teacher Subject</a>
                    </div>
                </div>
            </li>
        <?php endif; ?>

        <?php if ($user_position == 'ADMIN'): ?>
            <hr class="sidebar-divider">
            <div class="sidebar-heading">
                Reports
            </div>
            <?php
            $reports_pages = ['sf1', 'sf5', 'grade_report_card'];
            $is_reports_active = in_array($current_page, $reports_pages);
            ?>

            <li class="nav-item <?= $is_reports_active ? 'active' : '' ?>">
                <a class="nav-link <?= $is_reports_active ? '' : 'collapsed' ?>" href="#" data-toggle="collapse" data-target="#collapsePages"
                    aria-expanded="<?= $is_reports_active ? 'true' : 'false' ?>" aria-controls="collapsePages">
                    <i class="fas fa-fw fa-folder"></i>
                    <span>Reports</span>
                </a>
                <div id="collapsePages" class="collapse <?= $is_reports_active ? 'show' : '' ?>" aria-labelledby="headingPages" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">List of Reports:</h6>
                        <a class="collapse-item <?= $current_page == 'sf1' ? 'active' : '' ?>" href="sf1">School Form 1</a>
                        <a class="collapse-item <?= $current_page == 'sf5' ? 'active' : '' ?>" href="sf5">School Form 5</a>
                        <a class="collapse-item <?= $current_page == 'sf10' ? 'active' : '' ?>" href="sf10">School Form 10 </a>
                        <a class="collapse-item <?= $current_page == 'grade_report_card' ? 'active' : '' ?>" href="grade_report_card">Grade Report Cards</a>

                    </div>
                </div>
            </li>

            <div class="sidebar-heading">
                Maintenance
            </div>
            <?php
            $maintenance_pages = ['enrollment_period', 'sectionGrade', 'subjectGrade', 'room', 'teacher', 'requirements', 'users', 'school_events'];
            $is_maintenance_active = in_array($current_page, $maintenance_pages);
            ?>

            <li class="nav-item <?= $is_maintenance_active ? 'active' : '' ?>">
                <a class="nav-link <?= $is_maintenance_active ? '' : 'collapsed' ?>" href="#" data-toggle="collapse" data-target="#maintenance"
                    aria-expanded="<?= $is_maintenance_active ? 'true' : 'false' ?>" aria-controls="maintenance">
                    <i class="fas fa-fw fa-cog"></i>
                    <span>Maintenance</span>
                </a>
                <div id="maintenance" class="collapse <?= $is_maintenance_active ? 'show' : '' ?>" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Maintenance:</h6>
                        <a class="collapse-item" href="users">User Account</a>
                        <a class="collapse-item <?= $current_page == 'enrollment_period' ? 'active' : '' ?>" href="enrollment_period">Enrollment Period-(SY)</a>
                        <a class="collapse-item <?= $current_page == 'sectionGrade' ? 'active' : '' ?>" href="sectionGrade">Section</a>
                        <a class="collapse-item <?= $current_page == 'subjectGrade' ? 'active' : '' ?>" href="subjectGrade">Subject</a>
                        <a class="collapse-item <?= $current_page == 'room' ? 'active' : '' ?>" href="room">Classroom</a>
                        <a class="collapse-item <?= $current_page == 'teacher' ? 'active' : '' ?>" href="teacher">Teacher</a>
                        <a class="collapse-item <?= $current_page == 'requirements' ? 'active' : '' ?>" href="requirements">Requirements</a>
                        <a class="collapse-item <?= $current_page == 'school_events' ? 'active' : '' ?>" href="school_events">Announcement <br>(School Events)</a>
                        <a class="collapse-item <?= $current_page == 'ssc' ? 'active' : '' ?>" href="ssc">Special Science Class</a>
                    </div>
                </div>
            </li>
        <?php endif; ?>

        <hr class="sidebar-divider d-none d-md-block">
    <?php elseif (isset($_SESSION['student_id'])): ?>
        <div class="sidebar-heading">
            Manage
        </div>
        <li class="nav-item <?= $current_page == 'my_subject' ? 'active' : '' ?>">
            <a class="nav-link" href="my_subject">
                <i class="fas fa-fw fa-folder"></i>
                <span>Enrolled Subject</span>
            </a>
        </li>

        <li class="nav-item <?= $current_page == 'my_classmate' ? 'active' : '' ?>">
            <a class="nav-link" href="my_classmate">
                <i class="fas fa-fw fa-users"></i>
                <span>My Classmates</span>
            </a>
        </li>

        <li class="nav-item <?= $current_page == 'academic_record' ? 'active' : '' ?>">
            <a class="nav-link" href="academic_record">
                <i class="fas fa-fw fa-file"></i>
                <span>Academic Record This Year</span>
            </a>
        </li>

        <hr class="sidebar-divider d-none d-md-block">
    <?php else: ?>

        <div class="sidebar-heading">
            Manage
        </div>
        <!-- Teacher Subject -->
        <li class="nav-item <?= $current_page == 'teacher_subject' ? 'active' : '' ?>">
            <a class="nav-link" href="teacher_subject">
                <i class="fas fa-fw fa-folder"></i>
                <span>Subject</span>
            </a>
        </li>

        <!-- Task Dropdown -->
        <?php
        $task_pages = ['upcoming_announcement', 'today_task', 'recent_task'];
        $is_task_active = in_array($current_page, $task_pages);
        ?>

        <li class="nav-item <?= $is_task_active ? 'active' : '' ?>">
            <a class="nav-link <?= $is_task_active ? '' : 'collapsed' ?>" href="#" data-toggle="collapse" data-target="#task_manage"
                aria-expanded="<?= $is_task_active ? 'true' : 'false' ?>" aria-controls="task_manage">
                <i class="fas fa-fw fa-file-alt"></i>
                <span>Task</span>
            </a>
            <div id="task_manage" class="collapse <?= $is_task_active ? 'show' : '' ?>" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">List of Task:</h6>
                    <a class="collapse-item <?= $current_page == 'upcoming_announcement' ? 'active' : '' ?>" href="upcoming_announcement">Upcoming Task</a>
                    <a class="collapse-item <?= $current_page == 'today_task' ? 'active' : '' ?>" href="today_task">Today Task</a>
                    <a class="collapse-item <?= $current_page == 'recent_task' ? 'active' : '' ?>" href="recent_task">Recent Task</a>
                </div>
            </div>
        </li>

        <!-- Student Grades -->
        <li class="nav-item <?= $current_page == 'student_grade' ? 'active' : '' ?>">
            <a class="nav-link" href="student_grade">
                <i class="fas fa-fw fa-users"></i>
                <span>Student Subject Grades</span>
            </a>
        </li>



        <hr class="sidebar-divider d-none d-md-block">
        <div class="sidebar-heading">
            Report
        </div>
        <?php
        $report_pages = ['sf2'];
        $is_report_active = in_array($current_page, $report_pages);
        ?>

        <!-- Report Dropdown -->
        <li class="nav-item <?= $is_report_active ? 'active' : '' ?>">
            <a class="nav-link <?= $is_report_active ? '' : 'collapsed' ?>" href="#" data-toggle="collapse" data-target="#teacher_manage"
                aria-expanded="<?= $is_report_active ? 'true' : 'false' ?>" aria-controls="teacher_manage">
                <i class="fas fa-fw fa-file-alt"></i>
                <span>Report</span>
            </a>
            <div id="teacher_manage" class="collapse <?= $is_report_active ? 'show' : '' ?>" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Report:</h6>
                    <a class="collapse-item <?= $current_page == 'sf2' ? 'active' : '' ?>" href="sf2">School Form 2 Report</a>
                    <a class="collapse-item <?= $current_page == 'sf9' ? 'active' : '' ?>" href="sf9">School Form 9 Report</a>
                </div>
            </div>
        </li>

        <!-- Settings Heading -->
        <div class="sidebar-heading">Settings</div>
        <hr class="sidebar-divider d-none d-md-block">

        <!-- Create Announcement -->
        <li class="nav-item <?= $current_page == 'class_announcement' ? 'active' : '' ?>">
            <a class="nav-link" href="class_announcement">
                <i class="fas fa-fw fa-file"></i>
                <span>Create Announcement</span>
            </a>
        </li>
        <li class="nav-item <?= $current_page == 'eClass' ? 'active' : '' ?>">
            <a class="nav-link" href="eClass">
                <i class="fas fa-fw fa-file"></i>
                <span>E-Class</span>
            </a>
        </li>

        <hr class="sidebar-divider d-none d-md-block">
        <div class="text-center d-none d-md-inline">
            <button class="rounded-circle border-0" id="sidebarToggle"></button>
        </div>
    <?php endif; ?>


</ul>
<!-- End of Sidebar -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.getElementById('signOutLink').addEventListener('click', function(event) {
        event.preventDefault();

        Swal.fire({
            title: 'Are you sure?',
            text: "Do you want to sign out?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, sign out',
            cancelButtonText: 'No, stay here'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '../../logout';
            }
        });
    });
</script>