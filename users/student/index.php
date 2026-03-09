<?php
include '../../config.php';

// Mock ID for testing if session is not set (remove this line in production)
// $my_student_id = $_SESSION['student_id']; 
// Assuming $my_student_id comes from session or config based on your previous code context
if (!isset($my_student_id) && isset($_SESSION['student_id'])) {
    $my_student_id = $_SESSION['student_id'];
}
?>

<!DOCTYPE html>
<html lang="en">

<?php include './../template/header.php'; ?>

<style>
    /* --- 1. INTERACTIVE CARD STYLES --- */
    .hover-scale {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        cursor: pointer;
    }

    .hover-scale:hover {
        transform: translateY(-5px);
        box-shadow: 0 .5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }

    /* --- 2. DYNAMIC WELCOME CARD THEMES --- */
    .theme-morning {
        background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 99%, #fecfef 100%);
        color: #5a5a5a;
    }

    .theme-afternoon {
        background: linear-gradient(120deg, #84fab0 0%, #8fd3f4 100%);
        color: #03506f;
    }

    .theme-evening {
        background: linear-gradient(to right, #4facfe 0%, #00f2fe 100%);
        color: white;
    }

    /* --- 3. ANNOUNCEMENT LIST STYLING --- */
    .announcement-list {
        max-height: 400px;
        overflow-y: auto;
        padding-right: 5px;
    }

    .announcement-item {
        transition: background-color 0.2s;
        border-radius: 10px;
        padding: 10px;
        border-bottom: 1px solid #f0f0f0;
    }

    .announcement-item:hover {
        background-color: #f8f9fc;
    }

    /* Scrollbar prettify */
    .announcement-list::-webkit-scrollbar {
        width: 5px;
    }

    .announcement-list::-webkit-scrollbar-thumb {
        background: #ccc;
        border-radius: 5px;
    }

    /* --- 4. MINI CALENDAR --- */
    .calendar {
        font-family: sans-serif;
    }

    .calendar-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
        font-weight: bold;
        color: #4e73df;
    }

    .calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 5px;
        text-align: center;
    }

    .calendar-day-name {
        font-size: 0.8rem;
        color: #858796;
        font-weight: bold;
    }

    .calendar-day {
        padding: 5px;
        border-radius: 50%;
        font-size: 0.9rem;
        transition: 0.2s;
        cursor: default;
    }

    .calendar-day:hover {
        background-color: #eaecf4;
    }

    .calendar-day.today {
        background-color: #4e73df;
        color: white;
        font-weight: bold;
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
                        <h1 class="h3 mb-0 text-gray-800">Student Dashboard</h1>
                        <div id="datetime" class="d-none d-sm-inline-block btn btn-sm btn-light shadow-sm text-primary fw-bold">
                            <i class="fas fa-clock fa-sm text-gray-50"></i> Loading time...
                        </div>
                    </div>

                    <div class="row">

                        <div class="col-xl-12 col-md-12 mb-4">
                            <div class="card shadow h-100 py-2" id="welcomeCard">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="h4 font-weight-bold mb-1" id="greeting">Welcome back!</div>
                                            <p class="mb-0" style="opacity: 0.9;">
                                                Welcome to your student dashboard! Stay updated with your school-related tasks, track your progress, and manage your requirements with ease.
                                            </p>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-sun fa-4x" id="greetingIcon" style="opacity: 0.6;"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6 mb-4">
                            <a href="my_classmate" style="text-decoration: none;">
                                <div class="card border-left-success shadow h-100 py-2 hover-scale">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                    Classmates</div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">3</div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-users fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                        <div class="mt-2 text-xs text-success">
                                            <i class="fas fa-arrow-right"></i> View List
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <div class="col-xl-3 col-md-6 mb-4">
                            <a href="my_subject" style="text-decoration: none;">
                                <div class="card border-left-warning shadow h-100 py-2 hover-scale">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                    Enrolled Subjects</div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                    <?php
                                                    // Added error handling for $my_student_id
                                                    if (isset($my_student_id)) {
                                                        $count = mysqli_query($conn, "SELECT COUNT(*) AS totalSubject FROM student_tbl
                                                        INNER JOIN subject_tbl ON subject_tbl.subject_grade = student_tbl.student_grade WHERE student_id = '$my_student_id'");
                                                        $row = mysqli_fetch_assoc($count);
                                                        echo $row['totalSubject'];
                                                    } else {
                                                        echo "0";
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-book fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                        <div class="mt-2 text-xs text-warning">
                                            <i class="fas fa-arrow-right"></i> View Grades
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <div class="col-xl-6 col-md-12 mb-4">
                            <div class="card shadow h-100">
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary"> <i class="fas fa-calendar-alt"></i> My Calendar</h6>
                                </div>
                                <div class="card-body">
                                    <div class="calendar">
                                        <div class="calendar-header">
                                            <span id="monthYear">Month Year</span>
                                        </div>
                                        <div class="calendar-grid" id="calendarDays">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-xl-12 col-md-12 mb-4">
                            <div class="card shadow h-100">
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-white">
                                    <h6 class="m-0 font-weight-bold text-success">
                                        <i class="fas fa-bullhorn mr-2"></i> Class Announcements
                                    </h6>
                                    <div class="input-group input-group-sm" style="width: 250px;">
                                        <input type="text" id="announceSearch" class="form-control bg-light border-0 small" placeholder="Search announcements..." aria-label="Search" onkeyup="filterAnnouncements()">
                                        <div class="input-group-append">
                                            <button class="btn btn-success" type="button">
                                                <i class="fas fa-search fa-sm"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="announcement-list" id="announcementContainer">
                                        <?php
                                        $date = date('Y-m-d');
                                        // Added LIMIT to prevent overload and ORDER BY to show latest first
                                        $select_announcement = mysqli_query($conn, "SELECT * FROM announcement_tbl
                                                INNER JOIN subject_tbl ON subject_tbl.subject_id = announcement_tbl.subject 
                                                INNER JOIN teacher_tbl ON teacher_tbl.teacher_id = announcement_tbl.teacher_id    
                                                INNER JOIN room_tbl ON room_tbl.room_id = announcement_tbl.room
                                                WHERE announcement_tbl.date_inserted >= '$date'
                                                ORDER BY announcement_tbl.date ASC, announcement_tbl.time ASC");

                                        if (mysqli_num_rows($select_announcement) > 0) {
                                            while ($res_announcement = mysqli_fetch_assoc($select_announcement)) {
                                                // Check if profile image exists, else use placeholder
                                                $img_path = "./../admin/teacher_profile/" . $res_announcement['profile'];
                                                $img_src = (!empty($res_announcement['profile']) && file_exists($img_path)) ? $img_path : "https://ui-avatars.com/api/?name=" . $res_announcement['teacher_name'] . "&background=random";
                                        ?>
                                                <div class="announcement-item d-flex align-items-start mb-2">
                                                    <img src="<?= $img_src; ?>" alt="Teacher" class="rounded-circle me-3 border shadow-sm" width="50" height="50" style="object-fit: cover;">
                                                    <div class="flex-grow-1">
                                                        <div class="d-flex justify-content-between">
                                                            <h6 class="mb-1 font-weight-bold text-gray-800 search-target"><?= $res_announcement['teacher_name']; ?></h6>
                                                            <span class="badge badge-primary-soft text-primary border" style="font-size: 0.7rem;">
                                                                <i class="far fa-clock"></i> <?= date("g:i A", strtotime($res_announcement['time'])); ?>
                                                            </span>
                                                        </div>
                                                        <p class="mb-1 text-sm text-dark search-target"><strong>Subject:</strong> <?= $res_announcement['subject_name']; ?> (<?= $res_announcement['type']; ?>)</p>
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <small class="text-muted"><i class="fas fa-calendar-day"></i> <?= date("M d, Y", strtotime($res_announcement['date'])); ?></small>
                                                            <small class="text-muted"><i class="fas fa-map-marker-alt"></i> <?= $res_announcement['room_name']; ?></small>
                                                        </div>
                                                    </div>
                                                </div>
                                        <?php
                                            }
                                        } else {
                                            echo '<div class="text-center p-4 text-muted"><i class="fas fa-inbox fa-3x mb-3"></i><p>No announcements for today.</p></div>';
                                        }
                                        ?>
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

    <script>
        // --- 1. DYNAMIC TIME & WELCOME THEME ---
        function updateDashboardCard() {
            const now = new Date();
            const hour = now.getHours();

            // Time Display
            const datetimeOptions = {
                weekday: 'short',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            };
            document.getElementById('datetime').innerHTML = '<i class="fas fa-clock mr-2"></i>' + now.toLocaleDateString('en-US', datetimeOptions);

            // Greeting Logic
            let greetingText = "";
            let themeClass = "";
            let iconClass = "";

            const card = document.getElementById("welcomeCard");
            const icon = document.getElementById("greetingIcon");
            const title = document.getElementById("greeting");

            // Remove old classes
            card.classList.remove("theme-morning", "theme-afternoon", "theme-evening");

            if (hour < 12) {
                greetingText = "Good Morning, Student!";
                themeClass = "theme-morning";
                iconClass = "fa-coffee"; // Morning icon
            } else if (hour < 18) {
                greetingText = "Good Afternoon, Student!";
                themeClass = "theme-afternoon";
                iconClass = "fa-sun"; // Afternoon icon
            } else {
                greetingText = "Good Evening, Student!";
                themeClass = "theme-evening";
                iconClass = "fa-moon"; // Evening icon
            }

            title.innerText = greetingText;
            card.classList.add(themeClass);
            icon.className = "fas fa-4x " + iconClass; // Reset and add new icon
        }

        setInterval(updateDashboardCard, 60000); // Update every minute
        updateDashboardCard(); // Run immediately

        // --- 2. ANNOUNCEMENT FILTER ---
        function filterAnnouncements() {
            var input, filter, container, items, i, txtValue;
            input = document.getElementById("announceSearch");
            filter = input.value.toUpperCase();
            container = document.getElementById("announcementContainer");
            items = container.getElementsByClassName("announcement-item");

            for (i = 0; i < items.length; i++) {
                // Search in all text within the item
                txtValue = items[i].textContent || items[i].innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    items[i].style.display = "";
                    items[i].style.animation = "fadeIn 0.5s";
                } else {
                    items[i].style.display = "none";
                }
            }
        }

        // --- 3. SIMPLE MINI CALENDAR ---
        function renderCalendar() {
            const date = new Date();
            const month = date.getMonth();
            const year = date.getFullYear();
            const today = date.getDate();

            const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
            document.getElementById("monthYear").innerText = monthNames[month] + " " + year;

            const firstDay = new Date(year, month, 1).getDay(); // Day of week 1st falls on
            const daysInMonth = new Date(year, month + 1, 0).getDate(); // Total days

            const calendarGrid = document.getElementById("calendarDays");
            calendarGrid.innerHTML = "";

            // Day Headers
            const daysShort = ["S", "M", "T", "W", "T", "F", "S"];
            daysShort.forEach(d => {
                calendarGrid.innerHTML += `<div class="calendar-day-name">${d}</div>`;
            });

            // Empty slots for days before the 1st
            for (let i = 0; i < firstDay; i++) {
                calendarGrid.innerHTML += `<div></div>`;
            }

            // Days
            for (let i = 1; i <= daysInMonth; i++) {
                let className = "calendar-day";
                if (i === today) className += " today";
                calendarGrid.innerHTML += `<div class="${className}">${i}</div>`;
            }
        }
        renderCalendar();
    </script>

    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }
    </style>

</body>

</html>