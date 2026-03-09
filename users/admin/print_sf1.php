<?php
include '../../config.php';

$filter_school_year = $_GET['school_year'] ?? '';
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>School Form 1 (SF1)</title>
    <style>
        @page {
            size: 13in 8.5in;
            /* Legal Landscape */
            margin: 10mm;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10px;
            margin: 0;
            padding: 0;
        }

        /* HEADER STYLES */
        .header-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .header-center {
            text-align: center;
            flex-grow: 1;
        }

        .header-center h1 {
            font-size: 18px;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
        }

        .header-center p {
            font-size: 10px;
            margin: 0;
            font-style: italic;
        }

        .logo {
            height: 60px;
            width: auto;
        }

        /* SCHOOL INFO GRID */
        .school-info {
            width: 100%;
            margin-bottom: 5px;
            font-size: 11px;
        }

        .school-info td {
            padding: 2px 5px;
        }

        .info-data {
            border-bottom: 1px solid black;
            font-weight: bold;
            display: inline-block;
            width: 95%;
            text-align: center;
        }

        /* MAIN TABLE */
        .report-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
        }

        .report-table th {
            background-color: #d1d1d1;
            text-align: center;
            vertical-align: middle;
            border: 1px solid black;
            padding: 2px;
            font-weight: bold;
        }

        .report-table td {
            border: 1px solid black;
            padding: 2px 4px;
            vertical-align: middle;
        }

        .text-center {
            text-align: center;
        }

        .text-uppercase {
            text-transform: uppercase;
        }

        /* SUMMARY FOOTER */
        .footer-container {
            margin-top: 10px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .summary-table {
            width: 300px;
            border-collapse: collapse;
            font-size: 10px;
        }

        .summary-table th,
        .summary-table td {
            border: 1px solid black;
            padding: 3px;
            text-align: center;
        }

        .signature-section {
            text-align: center;
            width: 300px;
            margin-top: 20px;
        }

        .signature-line {
            border-top: 1px solid black;
            margin-top: 30px;
            font-weight: bold;
        }
    </style>
</head>

<body onload="window.print()">

    <div class="header-container">

        <div class="header-center">
            <h1>School Form 1 (SF 1) School Register</h1>
            <p>(This replaces Form 1, Master List & STS Form 2-Family Background)</p>
        </div>
    </div>

    <table class="school-info">
        <tr>
            <td width="10%">School ID</td>
            <td width="15%"><span class="info-data">301531</span></td>
            <td width="10%">Region</td>
            <td width="15%"><span class="info-data">IV-B MIMAROPA</span></td>
            <td width="10%">Division</td>
            <td width="20%"><span class="info-data">Marinduque</span></td>
            <td width="10%">District</td>
            <td width="10%"><span class="info-data">Mogpog</span></td>
        </tr>
        <tr>
            <td>School Name</td>
            <td colspan="3"><span class="info-data" style="width: 98%;">Bangbang National High School</span></td>
            <td>School Year</td>
            <td><span class="info-data"><?= htmlspecialchars($filter_school_year) ?></span></td>
            <td>Grade Level</td>
            <td><span class="info-data"> </span></td>
        </tr>
    </table>

    <table class="report-table">
        <thead>
            <tr>
                <th rowspan="2" width="3%">LRN</th>
                <th rowspan="2" width="15%">NAME<br>(Last Name, First Name, Middle Name)</th>
                <th rowspan="2" width="3%">Sex<br>(M/F)</th>
                <th rowspan="2" width="6%">Birth Date<br>(mm/dd/yyyy)</th>
                <th rowspan="2" width="3%">Age</th>
                <th rowspan="2" width="5%">Mother<br>Tongue</th>
                <th rowspan="2" width="3%">IP<br>(Y/N)</th>
                <th rowspan="2" width="5%">Religion</th>
                <th colspan="4">ADDRESS</th>
                <th colspan="2">PARENTS</th>
                <th colspan="2">GUARDIAN</th>
                <th rowspan="2" width="6%">Contact Number</th>
                <th rowspan="2">Remarks</th>
            </tr>
            <tr>
                <th>House # / Street</th>
                <th>Barangay</th>
                <th>Municipality</th>
                <th>Province</th>
                <th>Father's Name</th>
                <th>Mother's Maiden Name</th>
                <th>Name</th>
                <th>Relationship</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (!empty($filter_school_year)) {
                $sql = "SELECT * FROM student_tbl
                        INNER JOIN enrollment_tbl ON enrollment_tbl.enrollmentId = student_tbl.enrollment_id
                        WHERE student_tbl.status = 1
                        AND enrollment_tbl.stud_sy = '$filter_school_year'
                        ORDER BY sex DESC, lastname ASC";

                $result = mysqli_query($conn, $sql);

                $maleCount = 0;
                $femaleCount = 0;
                $rowsMale = "";
                $rowsFemale = "";

                while ($res = mysqli_fetch_assoc($result)) {
                    // Format Name: DELA CRUZ, JUAN A.
                    $mi = !empty($res['middlename']) ? substr($res['middlename'], 0, 1) . '.' : '';
                    $fullname = strtoupper($res['lastname'] . ', ' . $res['firstname'] . ' ' . $mi);

                    $sex = strtoupper($res['sex']);
                    $genderLabel = ($sex == 'MALE') ? 'M' : (($sex == 'FEMALE') ? 'F' : '-');

                    $birthdate = date('m/d/Y', strtotime($res['birthdate']));
                    $age = (new DateTime($res['birthdate']))->diff(new DateTime('today'))->y;

                    // Address Parsing (Assuming 'current_address' is one string, putting it in Barangay column for now)
                    // If you have separate columns, change these variables.
                    $address_full = $res['current_address'];
                    $street = '';
                    $barangay = $address_full;
                    $muni = 'Boac'; // Default or from DB
                    $prov = 'Marinduque'; // Default or from DB

                    // Parents
                    $father = strtoupper($res['father_lastname'] . ', ' . $res['father_firstname']);
                    $mother = strtoupper($res['mother_lastname'] . ', ' . $res['mother_firstname']);

                    // Placeholders for fields that might not be in your query yet
                    $religion = $res['religion'] ?? '';
                    $guardian = $res['guardian_name'] ?? '';
                    $relation = $res['guardian_relation'] ?? '';
                    $contact = $res['contact_number'] ?? '';
                    $remarks = '';

                    $row = "<tr>
                        <td class='text-center'>{$res['lrn']}</td>
                        <td>{$fullname}</td>
                        <td class='text-center'>{$genderLabel}</td>
                        <td class='text-center'>{$birthdate}</td>
                        <td class='text-center'>{$age}</td>
                        <td class='text-center'>{$res['mothertongue']}</td>
                        <td class='text-center'>{$res['ip']}</td>
                        <td class='text-center'>{$religion}</td>
                        <td>{$street}</td>
                        <td>{$barangay}</td>
                        <td>{$muni}</td>
                        <td>{$prov}</td>
                        <td>{$father}</td>
                        <td>{$mother}</td>
                        <td>{$guardian}</td>
                        <td>{$relation}</td>
                        <td>{$contact}</td>
                        <td>{$remarks}</td>
                    </tr>";

                    if ($sex == 'MALE') {
                        $rowsMale .= $row;
                        $maleCount++;
                    } elseif ($sex == 'FEMALE') {
                        $rowsFemale .= $row;
                        $femaleCount++;
                    }
                }

                // OUTPUT ROWS
                if ($maleCount > 0) {
                    echo $rowsMale;
                    echo "<tr class='bg-light'><td colspan='18' style='font-weight:bold; background:#eee;'>TOTAL MALE: {$maleCount}</td></tr>";
                }

                if ($femaleCount > 0) {
                    echo $rowsFemale;
                    echo "<tr class='bg-light'><td colspan='18' style='font-weight:bold; background:#eee;'>TOTAL FEMALE: {$femaleCount}</td></tr>";
                }

                echo "<tr><td colspan='18' style='background:black; color:white; font-weight:bold;'>COMBINED TOTAL: " . ($maleCount + $femaleCount) . "</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <div class="footer-container">
        <table class="summary-table">
            <thead>
                <tr>
                    <th rowspan="2" width="40%">REGISTERED<br>LEARNERS<br>(BoSY)</th>
                    <th rowspan="2">MALE</th>
                    <th rowspan="2">FEMALE</th>
                    <th rowspan="2">TOTAL</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="text-align: left;">Learners</td>
                    <td><?= $maleCount ?></td>
                    <td><?= $femaleCount ?></td>
                    <td><?= $maleCount + $femaleCount ?></td>
                </tr>
            </tbody>
        </table>

        <div class="signature-section">
            <p>Prepared by:</p>
            <br>
            <div class="signature-line">
                Signature of Adviser over Printed Name
            </div>
            <p style="font-size: 10px; margin-top: 5px;">
                BoSY Date: ______________ &nbsp;&nbsp; EoSY Date: ______________
            </p>
        </div>
    </div>

</body>

</html>