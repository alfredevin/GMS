<?php
include '../../config.php';

// Get Filters
$filterGrade = $_GET['grade'] ?? '';
$filterSection = $_GET['section'] ?? '';
$filterSubject = $_GET['subject'] ?? '';
$teacher_id = $_GET['teacher'] ?? '';

// 1. Fetch Teacher & Subject Info
$t_sql = mysqli_query($conn, "SELECT * FROM teacher_tbl WHERE teacher_id = '$teacher_id'");
$t_row = mysqli_fetch_assoc($t_sql);
$teacherName = strtoupper($t_row['teacher_name']);

$s_sql = mysqli_query($conn, "SELECT * FROM subject_tbl WHERE subject_id = '$filterSubject'");
$s_row = mysqli_fetch_assoc($s_sql);
$subjectName = strtoupper($s_row['subject_name']);

$sec_sql = mysqli_query($conn, "SELECT * FROM section_tbl WHERE section_id = '$filterSection'");
$sec_row = mysqli_fetch_assoc($sec_sql);
$sectionName = strtoupper($sec_row['section_name']);

// Helper Function
function computeAverage($res)
{
  $total = 0;
  $count = 0;
  while ($row = mysqli_fetch_assoc($res)) {
    if ($row['items'] > 0) {
      $total += ($row['score'] / $row['items']) * 100;
      $count++;
    }
  }
  return $count > 0 ? $total / $count : 0;
}

// 2. Fetch Students & Compute Grades
$male_students = [];
$female_students = [];

$sql = "SELECT * FROM student_tbl
        INNER JOIN enrollment_tbl ON enrollment_tbl.enrollmentId = student_tbl.enrollment_id
        WHERE student_tbl.status = 1 
        AND student_tbl.student_grade = '$filterGrade' 
        AND student_tbl.section_id = '$filterSection'
        ORDER BY lastname ASC";

$result = mysqli_query($conn, $sql);

while ($res = mysqli_fetch_assoc($result)) {
  $student_id = $res['student_id'];
  $fullname = strtoupper($res['lastname'] . ', ' . $res['firstname'] . ' ' . substr($res['middlename'], 0, 1) . '.');
  $gender = strtoupper($res['sex']);

  $quarterGrades = [];
  $finalSum = 0;
  $gradeCount = 0;

  for ($q = 1; $q <= 4; $q++) {
    // Calculation Logic
    $quiz = computeAverage(mysqli_query($conn, "SELECT * FROM announcement_tbl LEFT JOIN student_scores_tbl ON announcement_tbl.announcement_jd = student_scores_tbl.announcement_id WHERE quarterly = '$q' AND type = 'quiz' AND subject = '$filterSubject' AND student_id = '$student_id'"));
    $pt = computeAverage(mysqli_query($conn, "SELECT * FROM announcement_tbl LEFT JOIN student_scores_tbl ON announcement_tbl.announcement_jd = student_scores_tbl.announcement_id WHERE quarterly = '$q' AND type = 'pt' AND subject = '$filterSubject' AND student_id = '$student_id'"));
    $exam = computeAverage(mysqli_query($conn, "SELECT * FROM announcement_tbl LEFT JOIN student_scores_tbl ON announcement_tbl.announcement_jd = student_scores_tbl.announcement_id WHERE quarterly = '$q' AND type = 'exam' AND subject = '$filterSubject' AND student_id = '$student_id'"));

    $finalQ = ($quiz * 0.20) + ($pt * 0.60) + ($exam * 0.20);

    if ($finalQ > 0) {
      $quarterGrades[$q] = round($finalQ); // DepEd rounds per quarter
      $finalSum += $quarterGrades[$q];
      $gradeCount++;
    } else {
      $quarterGrades[$q] = '';
    }
  }

  $finalRating = ($gradeCount == 4) ? round($finalSum / 4) : '';
  $remarks = ($finalRating >= 75) ? 'PASSED' : (($finalRating != '') ? 'FAILED' : '');

  $data = [
    'name' => $fullname,
    'q1' => $quarterGrades[1],
    'q2' => $quarterGrades[2],
    'q3' => $quarterGrades[3],
    'q4' => $quarterGrades[4],
    'final' => $finalRating,
    'remarks' => $remarks
  ];

  if ($gender == 'MALE') $male_students[] = $data;
  else $female_students[] = $data;
}
?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <title>Summary of Quarterly Grades</title>
  <style>
    @page {
      size: 13in 8.5in;
      margin: 10mm;
    }

    /* Legal Landscape */
    body {
      font-family: Arial, sans-serif;
      font-size: 11px;
    }

    /* Header Layout */
    .header-table {
      width: 100%;
      border: none;
      margin-bottom: 5px;
    }

    .header-table td {
      border: none;
      vertical-align: top;
    }

    .logo {
      width: 60px;
    }

    .info-grid {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 5px;
      font-weight: bold;
      font-size: 11px;
    }

    .info-grid td {
      border: none;
      padding: 2px;
    }

    .input-box {
      border: 1px solid black;
      display: inline-block;
      width: 100%;
      height: 16px;
      padding-left: 5px;
      background: white;
    }

    /* Main Table */
    .grades-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 10px;
    }

    .grades-table th,
    .grades-table td {
      border: 1px solid black;
      padding: 3px;
      text-align: center;
    }

    .grades-table th {
      font-weight: bold;
      text-transform: uppercase;
    }

    .col-name {
      width: 30%;
      text-align: left;
      padding-left: 5px;
    }

    .bg-gray {
      background-color: #d3d3d3;
      text-align: left;
      font-weight: bold;
      padding-left: 5px;
    }
  </style>
</head>

<body onload="window.print()">

  <table class="header-table">
    <tr>
      <td width="10%"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/20/Department_of_Education.svg/1200px-Department_of_Education.svg.png" class="logo"></td>
      <td width="70%" style="text-align: center;">
        <h2 style="margin:0; text-transform: uppercase;">Summary of Quarterly Grades</h2>
      </td>
      <td width="20%" style="text-align: right;"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/8/8c/Department_of_Education_%28DepEd%29.svg/2560px-Department_of_Education_%28DepEd%29.svg.png" class="logo"></td>
    </tr>
  </table>

  <table class="info-grid">
    <tr>
      <td width="10%" style="text-align:right;">REGION</td>
      <td width="20%">
        <div class="input-box">IV-B MIMAROPA</div>
      </td>
      <td width="10%" style="text-align:right;">DIVISION</td>
      <td width="20%">
        <div class="input-box">MARINDUQUE</div>
      </td>
      <td width="10%" style="text-align:right;">DISTRICT</td>
      <td width="30%">
        <div class="input-box">MOGPOG</div>
      </td>
    </tr>
    <tr>
      <td style="text-align:right;">SCHOOL NAME</td>
      <td colspan="3">
        <div class="input-box">BANGBANG NATIONAL HIGH SCHOOL</div>
      </td>
      <td style="text-align:right;">SCHOOL ID</td>
      <td>
        <div class="input-box">301531</div>
      </td>
    </tr>
  </table>

  <table class="grades-table" style="border-bottom: none;">
    <tr>
      <td width="30%" style="text-align:left; border:2px solid black;">
        GRADE & SECTION: <b><?php echo $filterGrade . ' - ' . $sectionName; ?></b>
      </td>
      <td width="40%" style="text-align:left; border:2px solid black;">
        TEACHER: <b><?php echo $teacherName; ?></b>
      </td>
      <td width="30%" style="text-align:left; border:2px solid black;">
        SUBJECT: <b><?php echo $subjectName; ?></b>
      </td>
    </tr>
  </table>

  <table class="grades-table" style="border-top: none;">
    <thead>
      <tr>
        <th rowspan="2" class="col-name">LEARNERS' NAMES</th>
        <th colspan="4">QUARTERLY GRADES</th>
        <th rowspan="2" width="10%">FINAL GRADE</th>
        <th rowspan="2" width="10%">REMARKS</th>
      </tr>
      <tr>
        <th>1ST QUARTER</th>
        <th>2ND QUARTER</th>
        <th>3RD QUARTER</th>
        <th>4TH QUARTER</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td colspan="7" class="bg-gray">MALE</td>
      </tr>
      <?php foreach ($male_students as $s): ?>
        <tr>
          <td class="col-name"><?php echo $s['name']; ?></td>
          <td><?php echo $s['q1']; ?></td>
          <td><?php echo $s['q2']; ?></td>
          <td><?php echo $s['q3']; ?></td>
          <td><?php echo $s['q4']; ?></td>
          <td><b><?php echo $s['final']; ?></b></td>
          <td><?php echo $s['remarks']; ?></td>
        </tr>
      <?php endforeach; ?>

      <?php if (empty($male_students)) echo "<tr><td colspan='7'>&nbsp;</td></tr>"; ?>

      <tr>
        <td colspan="7" class="bg-gray">FEMALE</td>
      </tr>
      <?php foreach ($female_students as $s): ?>
        <tr>
          <td class="col-name"><?php echo $s['name']; ?></td>
          <td><?php echo $s['q1']; ?></td>
          <td><?php echo $s['q2']; ?></td>
          <td><?php echo $s['q3']; ?></td>
          <td><?php echo $s['q4']; ?></td>
          <td><b><?php echo $s['final']; ?></b></td>
          <td><?php echo $s['remarks']; ?></td>
        </tr>
      <?php endforeach; ?>

      <?php if (empty($female_students)) echo "<tr><td colspan='7'>&nbsp;</td></tr>"; ?>
    </tbody>
  </table>

</body>

</html>