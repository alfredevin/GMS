<?php

function encrypt($data)
{
    $key = 'your-secret-key'; // Use a long, strong key
    $iv = substr(hash('sha256', 'your-secret-iv'), 0, 16);
    return urlencode(base64_encode(openssl_encrypt($data, 'AES-256-CBC', $key, 0, $iv)));
}

function decrypt($data)
{
    $key = 'your-secret-key';
    $iv = substr(hash('sha256', 'your-secret-iv'), 0, 16);
    return openssl_decrypt(base64_decode(urldecode($data)), 'AES-256-CBC', $key, 0, $iv);
}
$enc_announcement = encrypt($res['announcement_jd']);
$enc_name = encrypt($res['subject_name']);
$enc_grade = encrypt($res['grade']);
$enc_section = encrypt($res['section']);
$enc_teacher = encrypt($res['teacher_id']);

$announcement_jd = decrypt($_GET['data1']);
$subject_name = decrypt($_GET['data2']);
$subject_grade = decrypt($_GET['data3']);
$section = decrypt($_GET['data4']);
$teacher_id = decrypt($_GET['data5']);

// <a href="gradeStudent.php?data1=<?php echo $enc_announcement; ?>&data2=<?php echo $enc_name; ?>&data3=<?php echo $enc_grade; ?>&data4=<?php echo $enc_section; ?>&data5=<?php echo $enc_teacher; ?>" 
//    class="btn btn-danger btn-sm">Grade Task</a>
