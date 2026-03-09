<?php
include '../../config.php';

$section_id = $_GET['section_id'] ?? '';

// Get Section & Teacher Info
$sec_q = mysqli_query($conn, "SELECT * FROM section_tbl 
    LEFT JOIN teacher_tbl ON section_tbl.section_id = teacher_tbl.section_id 
    WHERE teacher_tbl.section_id='$section_id'");
$sec = mysqli_fetch_assoc($sec_q);
$section_name = $sec['section_name'];
$grade_level = $sec['grade_level'] ?? '';
$adviser_name = strtoupper($sec['first_name'] . ' ' . $sec['last_name']);
$school_year = "2024-2025"; // Adjust dynamic SY if needed

// Get Students
$students_sql = "SELECT * FROM student_tbl 
                 INNER JOIN enrollment_tbl ON enrollment_tbl.enrollmentId = student_tbl.enrollment_id
                 WHERE student_tbl.section_id = '$section_id' AND student_tbl.status = 1
                 ORDER BY sex DESC, lastname ASC";
$students_res = mysqli_query($conn, $students_sql);

// Helper to get grades (Placeholder Logic - Replace with real DB fetch)
function getSubjectGrade($conn, $stud_id, $subject_name, $quarter)
{
    // Logic to fetch grade based on subject name
    return '';
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>SF9 - Report Card</title>
    <style>
        @page {
            size: 11in 8.5in;
            margin: 10mm;
        }

        /* Letter Landscape */
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10px;
            margin: 0;
            padding: 0;
        }

        /* LAYOUT */
        .sheet {
            width: 100%;
            height: 100%;
            page-break-after: always;
            display: flex;
        }

        .panel {
            width: 50%;
            padding: 15px;
            box-sizing: border-box;
            position: relative;
        }

        .panel-left {
            border-right: 1px dashed #ccc;
        }

        /* Fold line */

        /* TEXT UTILS */
        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-uppercase {
            text-transform: uppercase;
        }

        .font-bold {
            font-weight: bold;
        }

        .underline {
            border-bottom: 1px solid black;
            display: inline-block;
        }

        /* TABLES */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
        }

        th,
        td {
            border: 1px solid black;
            padding: 2px 4px;
            vertical-align: middle;
            font-size: 9px;
        }

        .no-border,
        .no-border td {
            border: none !important;
        }

        /* ATTENDANCE TABLE */
        .att-header th {
            font-size: 8px;
            height: 60px;
            vertical-align: bottom;
        }

        .vertical-text {
            writing-mode: vertical-rl;
            transform: rotate(180deg);
            margin: 0 auto;
        }

        /* GRADES TABLE */
        .grades-table th {
            background-color: #fff;
            text-align: center;
        }

        .grades-table td {
            height: 16px;
        }

        .sub-subject {
            padding-left: 15px;
        }

        .gray-bg {
            background-color: #dcdcdc;
        }

        /* HEADER LOGOS */
        .header-logo {
            width: 50px;
        }

        .deped-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .deped-header h4,
        .deped-header h5,
        .deped-header p {
            margin: 0;
        }
    </style>
</head>

<body onload="window.print()">

    <?php while ($stud = mysqli_fetch_assoc($students_res)):
        $stud_id = $stud['student_id'];
        $fullname = strtoupper($stud['lastname'] . ', ' . $stud['firstname'] . ' ' . substr($stud['middlename'], 0, 1) . '.');
        $lrn = $stud['lrn'];
        $age = (new DateTime($stud['birthdate']))->diff(new DateTime('today'))->y;
        $sex = strtoupper($stud['sex']);
    ?>

        <div class="sheet">

            <div class="panel panel-left">

                <div class="text-center font-bold" style="margin-bottom: 5px;">ATTENDANCE RECORD</div>
                <table>
                    <tr class="att-header">
                        <th width="20%"></th>
                        <th>
                            <div class="vertical-text">JUNE</div>
                        </th>
                        <th>
                            <div class="vertical-text">JULY</div>
                        </th>
                        <th>
                            <div class="vertical-text">AUGUST</div>
                        </th>
                        <th>
                            <div class="vertical-text">SEPTEMBER</div>
                        </th>
                        <th>
                            <div class="vertical-text">OCTOBER</div>
                        </th>
                        <th>
                            <div class="vertical-text">NOVEMBER</div>
                        </th>
                        <th>
                            <div class="vertical-text">DECEMBER</div>
                        </th>
                        <th>
                            <div class="vertical-text">JANUARY</div>
                        </th>
                        <th>
                            <div class="vertical-text">FEBRUARY</div>
                        </th>
                        <th>
                            <div class="vertical-text">MARCH</div>
                        </th>
                        <th>
                            <div class="vertical-text">APRIL</div>
                        </th>
                        <th>
                            <div class="vertical-text">TOTAL</div>
                        </th>
                    </tr>
                    <tr>
                        <td>School Days</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Days Present</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Days Absent</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                </table>

                <br>
                <div class="text-center font-bold" style="margin-bottom: 10px;">PARENT / GUARDIAN'S SIGNATURE</div>
                <table class="no-border" style="width: 80%; margin: 0 auto;">
                    <tr>
                        <td width="30%">1st Quarter</td>
                        <td class="underline" style="width:70%"></td>
                    </tr>
                    <tr>
                        <td>2nd Quarter</td>
                        <td class="underline" style="width:70%"></td>
                    </tr>
                    <tr>
                        <td>3rd Quarter</td>
                        <td class="underline" style="width:70%"></td>
                    </tr>
                    <tr>
                        <td>4th Quarter</td>
                        <td class="underline" style="width:70%"></td>
                    </tr>
                </table>

                <br>
                <div class="text-center font-bold" style="margin-bottom: 5px;">Certificate of Transfer</div>

                <div style="font-size: 10px; line-height: 1.6;">
                    Admitted to Grade: <span class="underline" style="width: 50px;"></span> &nbsp; Section: <span class="underline" style="width: 100px;"></span> <br>
                    Eligibility for Admission to Grade: <span class="underline" style="width: 150px;"></span> <br>
                    Approved:
                    <br><br>
                    <div style="display: flex; justify-content: space-between; padding: 0 20px;">
                        <div class="text-center">
                            <span class="underline" style="width: 130px;">&nbsp;</span><br>
                            Principal
                        </div>
                        <div class="text-center">
                            <span class="underline" style="width: 130px;"><?php echo $adviser_name; ?></span><br>
                            Teacher
                        </div>
                    </div>

                    <br>
                    <div class="text-center font-bold">Cancellation of Eligibility to Transfer</div>
                    <br>
                    Admitted in: <span class="underline" style="width: 150px;"></span> <br>
                    Date: <span class="underline" style="width: 100px;"></span>
                    <br><br>
                    <div class="text-right" style="padding-right: 30px;">
                        <span class="underline" style="width: 130px;">&nbsp;</span><br>
                        Principal
                    </div>
                </div>
            </div>

            <div class="panel">
                <div style="position: absolute; top: 10px; left: 10px; font-size: 8px;">SF 9 - JHS</div>

                <div class="deped-header">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/20/Department_of_Education.svg/1200px-Department_of_Education.svg.png" class="header-logo" style="float: left; margin-left: 20px;">
                    <div style="display: inline-block;">
                        <h5>Republic of the Philippines</h5>
                        <h4>Department of Education</h4>
                        <h5>Region IV-B MIMAROPA</h5>
                        <h5>Division of Marinduque</h5>
                        <br>
                        <div class="underline" style="width: 150px;">Gasan</div><br>
                        <span style="font-size: 9px;">District</span><br>

                        <div class="underline" style="width: 200px; font-weight: bold; margin-top: 5px;">BANGBANG NATIONAL HIGH SCHOOL</div><br>
                        <span style="font-size: 9px;">School</span>
                    </div>
                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/8/84/Coat_of_arms_of_the_Philippines.svg/1200px-Coat_of_arms_of_the_Philippines.svg.png" class="header-logo" style="float: right; margin-right: 20px;">
                </div>

                <br><br>
                <div class="text-center font-bold" style="font-size: 14px; margin-bottom: 20px;">LEARNER'S PROGRESS REPORT CARD</div>

                <div style="font-size: 11px; line-height: 1.8;">
                    Name: <span class="underline" style="width: 280px; font-weight: bold; text-transform: uppercase;"><?php echo $fullname; ?></span> <br>
                    Learner's Reference Number: <span class="underline" style="width: 180px; font-weight: bold;"><?php echo $lrn; ?></span> <br>
                    Age: <span class="underline" style="width: 50px; text-align: center;"><?php echo $age; ?></span> &nbsp;&nbsp;&nbsp;
                    Sex: <span class="underline" style="width: 100px; text-align: center;"><?php echo $sex; ?></span> <br>
                    Grade: <span class="underline" style="width: 80px; text-align: center;"><?php echo $grade_level; ?></span> &nbsp;&nbsp;&nbsp;
                    Section: <span class="underline" style="width: 120px; text-align: center;"><?php echo $section_name; ?></span> <br>
                    School Year: <span class="underline" style="width: 100px; text-align: center;"><?php echo $school_year; ?></span>
                </div>

                <br>
                <div style="font-size: 10px; text-align: justify;">
                    <strong>Dear Parent,</strong>
                    <p style="text-indent: 30px; margin-top: 5px;">
                        This report card shows the ability and progress your child has made in different learning areas as well as his/her core values.
                    </p>
                    <p style="text-indent: 30px; margin-top: 0;">
                        The school welcomes you should you desire to know more about your child's progress.
                    </p>
                </div>

                <br><br><br>
                <div style="display: flex; justify-content: space-between; padding: 0 20px;">
                    <div class="text-center">
                        <span class="underline" style="width: 150px; font-weight: bold;">NORMINDA SOL MABAO</span><br>
                        Principal
                    </div>
                    <div class="text-center">
                        <span class="underline" style="width: 150px; font-weight: bold;"><?php echo $adviser_name; ?></span><br>
                        Teacher
                    </div>
                </div>
            </div>
        </div>

        <div class="sheet">

            <div class="panel panel-left">
                <div class="text-center font-bold" style="font-size: 11px; margin-bottom: 5px;">REPORT ON LEARNING PROGRESS AND ACHIEVEMENT</div>

                <table class="grades-table">
                    <thead>
                        <tr>
                            <th rowspan="2" width="40%">Learning Areas</th>
                            <th colspan="4">Quarter</th>
                            <th rowspan="2" width="10%">Final<br>Rating</th>
                            <th rowspan="2" width="15%">Remarks</th>
                        </tr>
                        <tr>
                            <th>1</th>
                            <th>2</th>
                            <th>3</th>
                            <th>4</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="text-align: left;">Filipino</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td style="text-align: left;">English</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td style="text-align: left;">Mathematics</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td style="text-align: left;">Science</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td style="text-align: left;">Araling Panlipunan (AP)</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td style="text-align: left;">Edukasyon sa Pagpapakatao (EsP)</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td style="text-align: left;">Technology and Livelihood Education (TLE)</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>

                        <tr>
                            <td style="text-align: left;">MAPEH</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="sub-subject" style="text-align: left;">Music</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="sub-subject" style="text-align: left;">Arts</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="sub-subject" style="text-align: left;">Physical Education</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="sub-subject" style="text-align: left;">Health</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>

                        <tr>
                            <td class="text-right font-bold">General Average</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="font-bold"></td>
                            <td class="font-bold"></td>
                        </tr>
                    </tbody>
                </table>

                <div style="display: flex; justify-content: space-between; font-size: 9px; margin-top: 20px;">
                    <div style="width: 45%;">
                        <strong>Descriptors</strong><br>
                        Outstanding<br>
                        Very Satisfactory<br>
                        Satisfactory<br>
                        Fairly Satisfactory<br>
                        Did Not Meet Expectations
                    </div>
                    <div style="width: 25%; text-align: center;">
                        <strong>Grading Scale</strong><br>
                        90-100<br>
                        85-89<br>
                        80-84<br>
                        75-79<br>
                        Below 75
                    </div>
                    <div style="width: 25%; text-align: center;">
                        <strong>Remarks</strong><br>
                        Passed<br>
                        Passed<br>
                        Passed<br>
                        Passed<br>
                        Failed
                    </div>
                </div>
            </div>

            <div class="panel">
                <div class="text-center font-bold" style="font-size: 11px; margin-bottom: 5px;">REPORT ON LEARNER'S OBSERVED VALUES</div>

                <table>
                    <thead>
                        <tr>
                            <th rowspan="2" width="15%">Core Values</th>
                            <th rowspan="2" width="45%">Behavior Statements</th>
                            <th colspan="4">Quarter</th>
                        </tr>
                        <tr>
                            <th>1</th>
                            <th>2</th>
                            <th>3</th>
                            <th>4</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td rowspan="2" class="text-center font-bold">1. Maka-Diyos</td>
                            <td>Expresses one's spiritual beliefs while respecting the spiritual beliefs of others.</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Shows adherence to ethical principles by upholding truth in all undertakings.</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>

                        <tr>
                            <td rowspan="2" class="text-center font-bold">2. Makatao</td>
                            <td>Is sensitive to individual, social, and cultural differences.</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Demonstrates contributions towards solidarity.</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>

                        <tr>
                            <td class="text-center font-bold">3. Maka-Kalikasan</td>
                            <td>Cares for environment and utilizes resources wisely, judiciously and economically.</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>

                        <tr>
                            <td rowspan="2" class="text-center font-bold">4. Maka-Bansa</td>
                            <td>Demonstrates pride in being a Filipino; exercises the rights and responsibilities of a Filipino citizen.</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Demonstrates appropriate behavior in carrying out activities in school, community and country.</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>

                <div style="display: flex; justify-content: space-between; font-size: 9px; margin-top: 10px;">
                    <div style="width: 20%; text-align: center;"><strong>Marking</strong></div>
                    <div style="width: 40%;"><strong>Non-Numerical Rating</strong></div>
                    <div style="width: 40%;"></div>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 9px;">
                    <div style="width: 20%; text-align: center;">AO</div>
                    <div style="width: 40%;">Always Observed</div>
                    <div style="width: 40%;"></div>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 9px;">
                    <div style="width: 20%; text-align: center;">SO</div>
                    <div style="width: 40%;">Sometimes Observed</div>
                    <div style="width: 40%;"></div>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 9px;">
                    <div style="width: 20%; text-align: center;">RO</div>
                    <div style="width: 40%;">Rarely Observed</div>
                    <div style="width: 40%;"></div>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 9px;">
                    <div style="width: 20%; text-align: center;">NO</div>
                    <div style="width: 40%;">Not Observed</div>
                    <div style="width: 40%;"></div>
                </div>
            </div>
        </div>

    <?php endwhile; ?>

</body>

</html>