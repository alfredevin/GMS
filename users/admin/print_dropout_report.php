<?php
include '../../config.php';

// Count Variables
$total_male = 0;
$total_female = 0;

// Fetch Data
$sql = "SELECT 
            d.dropout_date, 
            d.reason,
            e.lrn, e.lastname, e.firstname, e.middlename, e.sex,
            sec.section_name, sec.section_grade
        FROM student_dropout_tbl d
        INNER JOIN student_tbl s ON d.student_id = s.student_id
        INNER JOIN enrollment_tbl e ON s.enrollment_id = e.enrollmentId
        LEFT JOIN section_tbl sec ON s.section_id = sec.section_id
        ORDER BY d.dropout_date DESC";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>

<head>
    <title>Report on Dropped Out Learners</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 10mm;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
        }

        /* HEADER */
        .header-table {
            width: 100%;
            text-align: center;
            margin-bottom: 20px;
            border: none;
        }

        .header-text h3,
        .header-text h4 {
            margin: 2px;
            text-transform: uppercase;
        }

        /* INFO GRID */
        .info-table {
            width: 100%;
            margin-bottom: 15px;
            font-weight: bold;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 5px;
        }

        .border-bottom {
            border-bottom: 1px solid black;
        }

        /* MAIN DATA TABLE */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table.data-table th,
        table.data-table td {
            border: 1px solid black;
            padding: 6px;
            vertical-align: middle;
        }

        table.data-table th {
            background-color: #f0f0f0;
            text-transform: uppercase;
            font-size: 10px;
            text-align: center;
        }

        .text-center {
            text-align: center;
        }

        .text-uppercase {
            text-transform: uppercase;
        }

        .text-danger {
            color: red;
            font-weight: bold;
        }

        /* SUMMARY BOX */
        .summary-box {
            border: 1px solid black;
            width: 30%;
            padding: 10px;
            float: left;
        }

        /* SIGNATORIES */
        .signatures {
            float: right;
            width: 60%;
            margin-top: 20px;
            text-align: center;
        }

        .sig-line {
            border-bottom: 1px solid black;
            width: 200px;
            display: inline-block;
            margin-top: 30px;
            font-weight: bold;
        }
    </style>
</head>

<body onload="window.print()">

    <table class="header-table">
        <tr>
            <td width="15%"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/f/fa/Seal_of_the_Department_of_Education_of_the_Philippines.png/1200px-Seal_of_the_Department_of_Education_of_the_Philippines.png" width="70"></td>
            <td width="70%">
                <div class="header-text">
                    <h4>Republic of the Philippines</h4>
                    <h3>Department of Education</h3>
                    <h4>Region IV-B MIMAROPA</h4>
                    <h3>BANGBANG NATIONAL HIGH SCHOOL</h3>
                    <p style="margin:0; font-size: 10px;">Bangbang, Gasan, Marinduque</p>
                    <br>
                    <h2 style="text-decoration: underline;">LIST OF LEARNERS WITH DROPPED OUT STATUS</h2>
                </div>
            </td>
            <td width="15%"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/8/84/Coat_of_arms_of_the_Philippines.svg/1200px-Coat_of_arms_of_the_Philippines.svg.png" width="70"></td>
        </tr>
    </table>

    <table class="info-table">
        <tr>
            <td width="10%">School ID:</td>
            <td width="15%" class="border-bottom">301531</td>
            <td width="10%" style="text-align: right;">School Year:</td>
            <td width="15%" class="border-bottom">2024-2025</td>
            <td width="10%" style="text-align: right;">Date Printed:</td>
            <td width="15%" class="border-bottom"><?= date('F d, Y') ?></td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">No.</th>
                <th width="15%">Date Dropped</th>
                <th width="15%">LRN</th>
                <th width="25%">Learner's Name<br>(Last Name, First Name, M.I.)</th>
                <th width="5%">Sex</th>
                <th width="10%">Grade/Section</th>
                <th width="25%">Reason / Remarks</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $count = 1;
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $fullname = strtoupper($row['lastname'] . ', ' . $row['firstname'] . ' ' . substr($row['middlename'], 0, 1) . '.');
                    $sex = strtoupper($row['sex']);
                    $section = "G" . $row['section_grade'] . " - " . $row['section_name'];
                    $date = date('M d, Y', strtotime($row['dropout_date']));

                    // Counters
                    if ($sex == 'MALE') $total_male++;
                    else $total_female++;
            ?>
                    <tr>
                        <td class="text-center"><?= $count++ ?></td>
                        <td class="text-center"><?= $date ?></td>
                        <td class="text-center"><?= $row['lrn'] ?></td>
                        <td class="text-uppercase font-bold"><?= $fullname ?></td>
                        <td class="text-center"><?= $sex ?></td>
                        <td class="text-center"><?= $section ?></td>
                        <td><?= $row['reason'] ?></td>
                    </tr>
            <?php
                }
            } else {
                echo "<tr><td colspan='7' class='text-center' style='padding: 20px;'>No dropout records found.</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <div style="width: 100%; margin-top: 20px;">

        <div class="summary-box">
            <strong>SUMMARY OF DROPOUTS:</strong>
            <table style="width: 100%; margin-top: 10px; font-size: 12px;">
                <tr>
                    <td>Male:</td>
                    <td style="text-align: right; font-weight: bold;"><?= $total_male ?></td>
                </tr>
                <tr>
                    <td>Female:</td>
                    <td style="text-align: right; font-weight: bold;"><?= $total_female ?></td>
                </tr>
                <tr>
                    <td colspan="2" style="border-bottom: 1px solid black;"></td>
                </tr>
                <tr>
                    <td><strong>TOTAL:</strong></td>
                    <td style="text-align: right; font-weight: bold; font-size: 14px;"><?= $total_male + $total_female ?></td>
                </tr>
            </table>
        </div>

        <div class="signatures">
            <table width="100%">
                <tr>
                    <td>Prepared by:</td>
                    <td>Noted by:</td>
                </tr>
                <tr>
                    <td>
                        <span class="sig-line"></span><br>
                        Guidance Counselor / Designated
                    </td>
                    <td>
                        <span class="sig-line">NORMINDA SOL MABAO</span><br>
                        School Principal I
                    </td>
                </tr>
            </table>
        </div>

    </div>

</body>

</html>