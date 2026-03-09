<?php
include '../../config.php';

$enrollmentId = $_GET['id'];

// Get Student Data
$query = "SELECT * FROM enrollment_tbl 
          INNER JOIN student_tbl ON student_tbl.enrollment_id = enrollment_tbl.enrollmentId
          WHERE enrollment_tbl.enrollmentId = '$enrollmentId'";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);

if (!$row) {
    die("Student record not found.");
}

// --- DATA MAPPING ---
$lrn = str_split($row['lrn']); // Split LRN into array for boxes
$lname = strtoupper($row['lastname']);
$fname = strtoupper($row['firstname']);
$mname = strtoupper($row['middlename']);
$extname = strtoupper($row['extname']);
$dob = explode('-', $row['birthdate']); // YYYY-MM-DD
$sex = strtoupper($row['sex']);
$age = $row['age'];
$grade = $row['grade'];

// Helper for Checkboxes
function isChecked($val, $target)
{
    return (strtoupper($val) == strtoupper($target)) ? '&#10003;' : ''; // Checkmark symbol
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>DepEd Enrollment Form</title>
    <style>
        @page {
            size: 8.5in 13in;
            margin: 0.5in;
        }

        /* Long Bond Paper */
        body {
            font-family: Arial, sans-serif;
            font-size: 13px;
            line-height: 1.2;
        }

        /* LAYOUT UTILS */
        .w-100 {
            width: 100%;
        }

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

        .no-border {
            border: none !important;
        }

        .page-break {
            page-break-before: always;
        }

        /* TABLE STYLES */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2px;
        }

        td,
        th {
            border: 1px solid black;
            padding: 2px 4px;
            vertical-align: middle;
        }

        /* SPECIAL FIELDS */
        .box-container {
            display: flex;
            justify-content: center;
        }

        .char-box {
            width: 14px;
            height: 14px;
            border: 1px solid black;
            display: inline-block;
            text-align: center;
            margin-right: 1px;
            font-size: 10px;
            line-height: 14px;
        }

        .check-box {
            width: 12px;
            height: 12px;
            border: 1px solid black;
            display: inline-block;
            text-align: center;
            line-height: 10px;
            margin-right: 3px;
            vertical-align: middle;
        }

        .section-header {
            background-color: #dcdcdc;
            text-align: center;
            font-weight: bold;
            font-size: 11px;
            padding: 3px;
        }

        .input-line {
            border-bottom: 1px solid black;
            display: inline-block;
            width: 100%;
            text-align: center;
        }

        /* HEADER LOGOS */
        .header-table td {
            border: none;
        }
    </style>
</head>

<body onload="window.print()">

    <div style="text-align: right; font-size: 9px;">Revised as of 03/27/2023</div>
    <div style="text-align: right; font-size: 10px; border: 1px solid black; display: inline-block; padding: 2px; float: right; margin-bottom: 10px;">ANNEX 1</div>

    <table class="header-table">
        <tr>
            <td width="15%" class="text-center"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/20/Department_of_Education.svg/1200px-Department_of_Education.svg.png" width="60"></td>
            <td width="70%" class="text-center">
                <h2 style="margin: 0; font-size: 16px;">BASIC EDUCATION ENROLLMENT FORM</h2>
                <div style="font-size: 9px;">THIS FORM IS NOT FOR SALE.</div>
            </td>
            <td width="15%"></td>
        </tr>
    </table>

    <table class="no-border">
        <tr>
            <td width="15%" class="no-border">School Year:</td>
            <td width="15%" class="no-border">
                <div style="border: 1px solid black; height: 18px; width: 100px;"> 2024-2025</div>
            </td>
            <td width="20%" class="no-border text-right">Check the appropriate box only:</td>
            <td class="no-border">
                <span class="check-box"></span> 1. With LRN? &nbsp;
                <span class="check-box"></span> Yes &nbsp;
                <span class="check-box"></span> No
            </td>
            <td class="no-border">
                <span class="check-box"></span> 2. Returning (Balik-Aral) &nbsp;
                <span class="check-box"></span> Yes &nbsp;
                <span class="check-box"></span> No
            </td>
        </tr>
        <tr>
            <td colspan="5" class="no-border" style="font-size: 9px; font-style: italic;">
                INSTRUCTIONS: Print legibly all information required in CAPITAL letters. Submit accomplished form to the Person-in-Charge/Registrar/Class Adviser. Use black or blue pen only.
            </td>
        </tr>
    </table>

    <div class="section-header">LEARNER INFORMATION</div>

    <table>
        <tr>
            <td width="20%" style="border-right: none;">PSA Birth Certificate No. <br><small>(if available upon registration)</small></td>
            <td width="30%" style="border-left: none;"><input type="text" style="width: 100%; border:none; border-bottom: 1px solid black;"></td>
            <td width="20%" style="text-align: right; border-right: none;">Learner Reference No. (LRN)</td>
            <td width="30%" style="border-left: none;">
                <div class="box-container" style="justify-content: flex-end;">
                    <?php
                    // Display LRN in boxes
                    for ($i = 0; $i < 12; $i++) {
                        $char = isset($lrn[$i]) ? $lrn[$i] : '';
                        echo "<span class='char-box'>$char</span>";
                    }
                    ?>
                </div>
            </td>
        </tr>
    </table>

    <table>
        <tr>
            <td width="20%">Last Name</td>
            <td colspan="3" class="text-uppercase font-bold"><?= $lname ?></td>
            <td width="20%">Birthdate (mm/dd/yyyy)</td>
            <td class="text-center font-bold"><?= $row['birthdate'] ?></td>
        </tr>
        <tr>
            <td>First Name</td>
            <td colspan="3" class="text-uppercase font-bold"><?= $fname ?></td>
            <td colspan="2" rowspan="2">
                <table class="no-border" style="margin:0;">
                    <tr>
                        <td class="no-border">Sex:</td>
                        <td class="no-border"><span class="check-box"><?= isChecked($sex, 'Male') ?></span> Male</td>
                        <td class="no-border"><span class="check-box"><?= isChecked($sex, 'Female') ?></span> Female</td>
                    </tr>
                    <tr>
                        <td class="no-border">Age:</td>
                        <td class="no-border font-bold"><?= $age ?></td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td>Middle Name</td>
            <td colspan="3" class="text-uppercase font-bold"><?= $mname ?></td>
        </tr>
        <tr>
            <td>Extension Name e.g. Jr., III</td>
            <td colspan="3" class="text-uppercase font-bold"><?= $extname ?></td>
            <td>Mother Tongue</td>
            <td class="text-uppercase font-bold"><?= $row['mothertongue'] ?></td>
        </tr>
    </table>

    <table>
        <tr>
            <td width="50%">Belonging to any Indigenous Peoples (IP) Community/Indigenous Cultural Community?</td>
            <td>
                <span class="check-box"><?= isChecked($row['ip'], 'Yes') ?></span> Yes &nbsp;&nbsp;
                <span class="check-box"><?= isChecked($row['ip'], 'No') ?></span> No
            </td>
            <td>If Yes, please specify: <span class="input-line" style="width: 100px;"></span></td>
        </tr>
        <tr>
            <td>Is your family a beneficiary of 4Ps?</td>
            <td>
                <span class="check-box"><?= isChecked($row['is_4ps'], 'Yes') ?></span> Yes &nbsp;&nbsp;
                <span class="check-box"><?= isChecked($row['is_4ps'], 'No') ?></span> No
            </td>
            <td>If Yes, write the 4Ps Household ID Number below:<br>
                <div style="margin-top: 2px;">
                    <?php for ($k = 0; $k < 18; $k++) echo "<span class='char-box'></span>"; ?>
                </div>
            </td>
        </tr>
    </table>

    <table style="margin-top: 5px;">
        <tr>
            <td colspan="4" style="background-color: #f0f0f0;">
                <strong>Is the child a Learner with Disability?</strong> &nbsp;&nbsp;
                <span class="check-box"><?= isChecked($row['has_disability'], 'Yes') ?></span> Yes &nbsp;&nbsp;
                <span class="check-box"><?= isChecked($row['has_disability'], 'No') ?></span> No
            </td>
        </tr>
        <tr>
            <td colspan="4" style="font-size: 9px;">If Yes, specify the type of disability:</td>
        </tr>
        <tr style="font-size: 9px;">
            <td width="25%" class="no-border" style="vertical-align: top;">
                <div><span class="check-box"></span> Visual Impairment</div>
                <div style="padding-left: 15px;"><span class="check-box"></span> Low Vision</div>
                <div style="padding-left: 15px;"><span class="check-box"></span> Blind</div>
            </td>
            <td width="25%" class="no-border" style="vertical-align: top;">
                <div><span class="check-box"></span> Hearing Impairment</div>
                <div><span class="check-box"></span> Autism Spectrum Disorder</div>
                <div><span class="check-box"></span> Speech/Language Disorder</div>
            </td>
            <td width="25%" class="no-border" style="vertical-align: top;">
                <div><span class="check-box"></span> Learning Disability</div>
                <div><span class="check-box"></span> Intellectual Disability</div>
                <div><span class="check-box"></span> Cerebral Palsy</div>
            </td>
            <td width="25%" class="no-border" style="vertical-align: top;">
                <div><span class="check-box"></span> Emotional-Behavioral Disorder</div>
                <div><span class="check-box"></span> Orthopedic/Physical Handicap</div>
                <div><span class="check-box"></span> Special Health Problem/Chronic Disease</div>
                <div><span class="check-box"></span> Cancer</div>
            </td>
        </tr>
    </table>

    <div class="section-header" style="text-align: left; margin-top: 5px;">Current Address</div>
    <table>
        <tr>
            <td width="15%">House No. / Street</td>
            <td width="25%"><?= $row['current_address'] ?></td>
            <td width="15%">Street Name</td>
            <td width="15%"></td>
            <td width="15%">Barangay</td>
            <td width="15%"></td>
        </tr>
        <tr>
            <td>Municipality/City</td>
            <td>Gasan</td>
            <td>Province</td>
            <td>Marinduque</td>
            <td>Country</td>
            <td>Philippines</td>
        </tr>
        <tr>
            <td>Zip Code</td>
            <td colspan="5">4905</td>
        </tr>
    </table>

    <div class="section-header" style="text-align: left; background: none; border: 1px solid black; border-bottom: none; margin-bottom: 0;">
        Permanent Address &nbsp;&nbsp;
        <span style="font-weight: normal; font-size: 9px;">Same with your Current Address? <span class="check-box"></span> Yes <span class="check-box"></span> No</span>
    </div>
    <table>
        <tr>
            <td width="15%">House No. / Street</td>
            <td width="25%"><?= $row['permanent_address'] ?></td>
            <td width="15%">Street Name</td>
            <td width="15%"></td>
            <td width="15%">Barangay</td>
            <td width="15%"></td>
        </tr>
        <tr>
            <td>Municipality/City</td>
            <td></td>
            <td>Province</td>
            <td></td>
            <td>Country</td>
            <td></td>
        </tr>
        <tr>
            <td>Zip Code</td>
            <td colspan="5"></td>
        </tr>
    </table>

    <div class="section-header">PARENT'S / GUARDIAN'S INFORMATION</div>
    <table>
        <tr>
            <td width="20%"><strong>Father's Name</strong><br><small>Last Name, First Name, Middle Name</small></td>
            <td width="50%" class="text-uppercase"><?= $row['father_lastname'] . ', ' . $row['father_firstname'] ?></td>
            <td width="30%">Contact Number: <br> <?= $row['father_contact'] ?></td>
        </tr>
        <tr>
            <td><strong>Mother's Maiden Name</strong><br><small>Last Name, First Name, Middle Name</small></td>
            <td class="text-uppercase"><?= $row['mother_lastname'] . ', ' . $row['mother_firstname'] ?></td>
            <td>Contact Number: <br> <?= $row['mother_contact'] ?></td>
        </tr>
        <tr>
            <td><strong>Legal Guardian's Name</strong><br><small>Last Name, First Name, Middle Name</small></td>
            <td class="text-uppercase"><?= $row['guardian_lastname'] . ', ' . $row['guardian_firstname'] ?></td>
            <td>Contact Number: <br> <?= $row['guardian_contact'] ?></td>
        </tr>
    </table>

    <div class="page-break"></div>

    <div class="section-header">For Returning Learner (Balik-Aral) and Those Who will Transfer/Move In</div>

    <table style="margin-top: 15px; margin-bottom: 20px;">
        <tr>
            <td width="20%" class="no-border">Last Grade Level Completed</td>
            <td width="30%" class="no-border"><span class="input-line"></span></td>
            <td width="20%" class="no-border text-right">Last School Year Completed</td>
            <td width="30%" class="no-border"><span class="input-line"></span></td>
        </tr>
        <tr>
            <td class="no-border">Last School Attended</td>
            <td class="no-border"><span class="input-line"></span></td>
            <td class="no-border text-right">School ID</td>
            <td class="no-border">
                <?php for ($k = 0; $k < 6; $k++) echo "<span class='char-box'></span>"; ?>
            </td>
        </tr>
    </table>

    <div class="section-header">For Learners in Senior High School</div>
    <table style="margin-top: 15px; margin-bottom: 20px;">
        <tr>
            <td width="15%" class="no-border">Semester</td>
            <td width="20%" class="no-border">
                <span class="check-box"></span> 1st &nbsp; <span class="check-box"></span> 2nd
            </td>
            <td width="10%" class="no-border text-right">Track</td>
            <td width="55%" class="no-border"><span class="input-line"></span></td>
        </tr>
        <tr>
            <td class="no-border"></td>
            <td class="no-border"></td>
            <td class="no-border text-right">Strand</td>
            <td class="no-border"><span class="input-line"></span></td>
        </tr>
    </table>

    <div style="border: 1px solid black; padding: 10px; margin-bottom: 20px;">
        <p style="font-weight: bold; margin-top: 0;">If school will implement other distance learning modalities aside from face-to-face instruction, what would you prefer for your child?</p>
        <p style="font-size: 9px; margin-top: -5px;">Choose all that apply:</p>

        <table class="no-border">
            <tr>
                <td class="no-border"><span class="check-box"></span> Modular (Print)</td>
                <td class="no-border"><span class="check-box"></span> Online</td>
                <td class="no-border"><span class="check-box"></span> Radio-Based Instruction</td>
                <td class="no-border"><span class="check-box"></span> Blended</td>
            </tr>
            <tr>
                <td class="no-border"><span class="check-box"></span> Modular (Digital)</td>
                <td class="no-border"><span class="check-box"></span> Educational Television</td>
                <td class="no-border"><span class="check-box"></span> Homeschooling</td>
                <td class="no-border"></td>
            </tr>
        </table>
    </div>

    <div style="margin-top: 50px; text-align: justify; padding: 0 20px;">
        <p>
            I hereby certify that the above information given are true and correct to the best of my knowledge and I allow the Department of Education to use my child's details to create and/or update his/her learner profile in the Learner Information System. The information herein shall be treated as confidential in compliance with the Data Privacy Act of 2012.
        </p>
    </div>

    <table class="no-border" style="margin-top: 50px;">
        <tr>
            <td width="40%" class="no-border text-center">
                <div class="input-line"></div>
                Signature Over Printed Name of Parent/Guardian
            </td>
            <td width="20%" class="no-border"></td>
            <td width="40%" class="no-border text-center">
                <div class="input-line"></div>
                Date
            </td>
        </tr>
    </table>

</body>

</html>