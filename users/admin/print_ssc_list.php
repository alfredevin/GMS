<?php
include '../../config.php';

// Fetch SSC Students
// Sorted by Sex (Male first) then Lastname for cleaner list
$sql = "SELECT *, section_tbl.section_name 
        FROM student_tbl
        INNER JOIN enrollment_tbl ON enrollment_tbl.enrollmentId = student_tbl.enrollment_id
        INNER JOIN section_tbl ON section_tbl.section_id = student_tbl.section_id 
        WHERE student_tbl.status = 1 AND student_tbl.is_ssc = 1
        ORDER BY sex DESC, lastname ASC";

$result = mysqli_query($conn, $sql);
$male_count = 0;
$female_count = 0;
?>

<!DOCTYPE html>
<html>

<head>
    <title>SSC Masterlist</title>
    <style>
        @page {
            size: A4;
            margin: 10mm;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: black;
        }

        /* HEADER */
        .header-table {
            width: 100%;
            text-align: center;
            margin-bottom: 20px;
        }

        .header-text h3,
        .header-text h4 {
            margin: 2px;
            text-transform: uppercase;
        }

        /* MAIN TABLE */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        table.data-table th,
        table.data-table td {
            border: 1px solid black;
            padding: 5px;
        }

        table.data-table th {
            background-color: #f0f0f0;
            text-transform: uppercase;
        }

        .text-center {
            text-align: center;
        }

        .text-uppercase {
            text-transform: uppercase;
        }

        .font-bold {
            font-weight: bold;
        }

        /* SIGNATORIES */
        .signatures {
            margin-top: 40px;
            width: 100%;
        }

        .sig-box {
            width: 40%;
            display: inline-block;
            text-align: center;
            margin-top: 20px;
        }

        .sig-line {
            border-bottom: 1px solid black;
            width: 80%;
            margin: 0 auto;
            font-weight: bold;
            text-transform: uppercase;
        }
    </style>
</head>

<body onload="window.print()">

    <table class="header-table">
        <tr>
            <td width="15%"><img src="https://upload.wikimedia.org/wikipedia/commons/2/20/Seal_of_the_Department_of_Education_of_the_Philippines.png" width="70"></td>
            <td width="70%">
                <div class="header-text">
                    <h4>Republic of the Philippines</h4>
                    <h3>Department of Education</h3>
                    <h4>Region IV-B MIMAROPA</h4>
                    <h3>BANGBANG NATIONAL HIGH SCHOOL</h3>
                    <p style="margin:0; font-size: 10px;">Bangbang, Gasan, Marinduque</p>
                    <br>
                    <h3 style="text-decoration: underline;">MASTERLIST OF SPECIAL SCIENCE CLASS (SSC)</h3>
                    <p>School Year: 2024-2025</p>
                </div>
            </td>
            <td width="15%"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/8/84/Coat_of_arms_of_the_Philippines.svg/1200px-Coat_of_arms_of_the_Philippines.svg.png" width="70"></td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">No.</th>
                <th width="20%">LRN</th>
                <th width="40%">Learner's Name</th>
                <th width="10%">Sex</th>
                <th width="25%">Original Section</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $count = 1;
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $fullname = strtoupper($row['lastname'] . ', ' . $row['firstname'] . ' ' . substr($row['middlename'], 0, 1) . '.');
                    $sex = strtoupper($row['sex']);

                    // Count Genders
                    if ($sex == 'MALE') $male_count++;
                    else $female_count++;
            ?>
                    <tr>
                        <td class="text-center"><?= $count++ ?></td>
                        <td class="text-center"><?= $row['lrn'] ?></td>
                        <td class="font-bold"><?= $fullname ?></td>
                        <td class="text-center"><?= $sex ?></td>
                        <td class="text-center"><?= $row['section_name'] ?></td>
                    </tr>
            <?php
                }
            } else {
                echo "<tr><td colspan='5' class='text-center'>No students found in SSC list.</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <div style="margin-top: 10px; font-weight: bold;">
        <p>SUMMARY:</p>
        <p style="margin-left: 20px;">
            MALE: <?= $male_count ?> <br>
            FEMALE: <?= $female_count ?> <br>
            <strong>TOTAL: <?= $male_count + $female_count ?></strong>
        </p>
    </div>

    <div class="signatures">
        <div class="sig-box" style="float: left;">
            <p style="font-size: 10px; margin-bottom: 30px; text-align: left; padding-left: 40px;">Prepared by:</p>
            <div class="sig-line">SSC COORDINATOR NAME</div>
            <p style="font-size: 10px;">SSC Coordinator</p>
        </div>

        <div class="sig-box" style="float: right;">
            <p style="font-size: 10px; margin-bottom: 30px; text-align: left; padding-left: 40px;">Noted by:</p>
            <div class="sig-line">NORMINDA SOL MABAO</div>
            <p style="font-size: 10px;">School Principal</p>
        </div>
    </div>

</body>

</html>