<?php
include '../../config.php';

$student_id = $_GET['student_id'];
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
                    <a href="class_attendance" class="btn btn-primary btn-sm mb-3"> Back to Page</a>

                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <div class="row">
                                <div class="col">
                                    <h6 class="m-0 font-weight-bold text-primary text-center text-uppercase">Attendance Record</h6>
                                    <h6 class="m-0 font-weight-bold text-primary text-center">
                                        <?php
                                        $sel_student = "SELECT * FROM student_tbl
                                        INNER JOIN enrollment_tbl ON enrollment_tbl.enrollmentId  = student_tbl.enrollment_id  WHERE   student_id = '$student_id'";
                                        $result_sel_student = mysqli_query($conn, $sel_student);
                                        $res_student = mysqli_fetch_assoc($result_sel_student);
                                        $student_name_sel = $res_student["firstname"] . ' ' . $res_student["middlename"] . ' ' . $res_student["lastname"];
                                        echo $student_name_sel;
                                        ?>
                                    </h6>
                                </div>
                            </div>
                        </div>

                        <div class="card-body border-bottom bg-light">
                            <div class="row justify-content-center">
                                <div class="col-auto d-flex align-items-center mb-2">
                                    <span class="badge badge-success mr-1" style="width: 20px; height: 20px;">&nbsp;</span> Present
                                </div>
                                <div class="col-auto d-flex align-items-center mb-2 ml-3">
                                    <span class="badge badge-danger mr-1" style="width: 20px; height: 20px;">&nbsp;</span> Absent
                                </div>
                                <div class="col-auto d-flex align-items-center mb-2 ml-3">
                                    <span class="badge badge-warning mr-1" style="width: 20px; height: 20px;">&nbsp;</span> Excused
                                </div>
                                <div class="col-auto d-flex align-items-center mb-2 ml-3">
                                    <span class="badge badge-primary mr-1" style="width: 20px; height: 20px;">&nbsp;</span> Late
                                </div>
                            </div>
                            <div class="text-center mt-1 small text-muted">
                                <i class="fas fa-caret-left"></i> Left Triangle: <strong>AM</strong> &nbsp;|&nbsp;
                                Right Triangle: <strong>PM</strong> <i class="fas fa-caret-right"></i>
                            </div>
                        </div>

                        <div class="card-body">
                            <div id='calendar'></div>
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
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');

            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'en',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: ''
                },
                events: {
                    url: 'fetch_attendance.php?student_id=<?= $student_id ?>',
                    failure: function() {
                        alert('There was an error fetching attendance data!');
                    }
                },
                // FUNCTION FOR TOOLTIP ON HOVER
                eventDidMount: function(info) {
                    // Check if extendedProps exist
                    if (info.event.extendedProps.status) {
                        var period = info.event.extendedProps.period;
                        var status = info.event.extendedProps.status;

                        // Set the tooltip text (e.g. "AM: Present")
                        var tooltipText = period + ": " + status;

                        // Add generic title attribute for hover effect
                        info.el.setAttribute('title', tooltipText);

                        // Optional: Enable cursor pointer
                        info.el.style.cursor = 'pointer';
                    }
                }
            });

            calendar.render();
        });
    </script>


    <style>
        #calendar {
            max-width: 900px;
            margin: 20px auto;
        }

        .fc-daygrid-day-frame {
            position: relative;
            overflow: hidden;
            min-height: 100px;
            /* Ensure cells are tall enough */
        }

        /* IMPORTANT: Fix opacity so colors are bright */
        .fc-bg-event {
            opacity: 1 !important;
        }

        /* --- TRIANGLE STYLES --- */
        .am-present,
        .am-absent,
        .am-excused,
        .am-late,
        .pm-present,
        .pm-absent,
        .pm-excused,
        .pm-late {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }

        /* AM (Left Triangle) Colors */
        .am-present {
            background-color: green !important;
            clip-path: polygon(0 0, 100% 0, 0 100%);
        }

        .am-absent {
            background-color: red !important;
            clip-path: polygon(0 0, 100% 0, 0 100%);
        }

        .am-excused {
            background-color: orange !important;
            clip-path: polygon(0 0, 100% 0, 0 100%);
        }

        .am-late {
            background-color: blue !important;
            clip-path: polygon(0 0, 100% 0, 0 100%);
        }

        /* PM (Right Triangle) Colors */
        .pm-present {
            background-color: green !important;
            clip-path: polygon(100% 0, 100% 100%, 0 100%);
        }

        .pm-absent {
            background-color: red !important;
            clip-path: polygon(100% 0, 100% 100%, 0 100%);
        }

        .pm-excused {
            background-color: orange !important;
            clip-path: polygon(100% 0, 100% 100%, 0 100%);
        }

        .pm-late {
            background-color: blue !important;
            clip-path: polygon(100% 0, 100% 100%, 0 100%);
        }
    </style>

</body>

</html>