<?php
session_start();
include '../config.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Bangbang National Highschool</title>
  <meta name="description" content="">
  <meta name="keywords" content="">

  <!-- Favicons -->
  <link href="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS9BRQM_uqdXGt-qLZgiHczlYTKTnEcxifgsQ&s" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
    rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

  <!-- Main CSS File -->
  <link href="assets/css/main.css" rel="stylesheet">


  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/sweetalert2@10">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10">
  </script>
  <script src="sweetalert2.min.js"></script>
  <link rel="stylesheet" href="sweetalert2.min.css">
  <script src="sweetalert2.all.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>

<body class="index-page">

  <header id="header" class="header d-flex align-items-center fixed-top">
    <div class="container-fluid container-xl position-relative d-flex align-items-center justify-content-between">

      <a href="index" class="logo d-flex align-items-center">
        <!-- Uncomment the line below if you also wish to use an image logo -->
        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS9BRQM_uqdXGt-qLZgiHczlYTKTnEcxifgsQ&s"
          style="border-radius: 50%;" alt="">
        <h1 class="sitename">BNHS</h1>
      </a>

      <nav id="navmenu" class="navmenu">
        <ul>
          <li><a href="#hero" class="active">Home</a></li>
          <li><a href="#about">About</a></li>

          <?php if (!isset($_SESSION['new_users_id'])): ?>
            <li><a href="#" data-toggle="modal" data-target="#authModal">Login</a></li>
            <!-- <li><a href="#" data-toggle="modal" data-target="#recover_account">Recover Account</a></li> -->
          <?php else: ?>
            <li class="  submenu dropdown ">
              <a href="#" class=" dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
                aria-expanded="false">
                My Account
              </a>
              <ul class="dropdown-menu">

                <li class=""><a class="" href="profile">My Account</a></li>
                <li class=""><a class="" href="#" id="signOutLink">Logout</a></li>
              </ul>
            </li>
          <?php endif; ?>
        </ul>
        <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
      </nav>

    </div>
  </header>

  <main class="main">

    <!-- Hero Section -->
    <section id="hero" class="hero section dark-background">
      <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRoRsljm7GdU0EL_EtQ6lO2af-nWJD9EqlE8A&s" alt=""
        class="hero-bg">

      <div class="container">
        <div class="row gy-4 justify-content-between">
          <div class="col-lg-4 order-lg-last hero-img" data-aos="zoom-out" data-aos-delay="100">
            <img
              src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT9WH7YFFvNqfIsvrGmSbUcbWcmTg8EgpNbSn_8lek594-dlrSQZXqr1TsXmzTSR42kFUo&usqp=CAU"
              class="img-fluid  " width="100%" alt="">
          </div>

          <div class="col-lg-6  d-flex flex-column justify-content-center" data-aos="fade-in">
            <h1>Welcome to <span>Bangbang National High School</span></h1>
            <p>Empowering students through quality education, technology, and excellence.</p>

            <div class="d-flex">
              <a href="#about" class="btn-get-started">About Us</a>
            </div>
          </div>

        </div>
      </div>



    </section><!-- /Hero Section -->

    <!-- About Section -->


    <section id="details" class="details section">

      <!-- Section Title -->
      <?php

      $today = date('Y-m-d');
      $sql = "SELECT * FROM enrollment_period_tbl ORDER BY start_date ASC LIMIT 1";
      $result = mysqli_query($conn, $sql);

      if (mysqli_num_rows($result) > 0):
        $res = mysqli_fetch_assoc($result);
        $start_date = $res['start_date'];
        $end_date = $res['end_date'];
        $school_year = $res['sy'];

        $seven_days_before = date('Y-m-d', strtotime($start_date . ' -7 days'));

        if ($today >= $seven_days_before && $today <= $end_date):
          // Check if enrollment has started or not yet
          $enrollment_started = ($today >= $start_date);

          $formatted_start = date('F d, Y', strtotime($start_date)); // e.g. July 23, 2025
          $formatted_end = date('F d, Y', strtotime($end_date));     // e.g. August 23, 2025


          $year = date('Y', strtotime($start_date));     // e.g. August 23, 2025

      ?>

          <div class="container section-title" data-aos="fade-up">
            <h2>Enrollment</h2>
            <div><span>Now Open For</span> <span class="description-title">School Year <?= $school_year . ' - ' . $school_year + 1 ?></span></div>
          </div><!-- End Section Title -->
          <div class="container">



            <div class="col-md-7" data-aos="fade-up" data-aos-delay="100">
              <h3>Enrollment Period <?= $enrollment_started ? 'Has Officially Started!' : 'is Almost Here!' ?></h3>
              <p><i class="bi bi-calendar"></i> Enrollment Period: <strong><?= $formatted_start ?> – <?= $formatted_end ?></strong></p>

              <p class="fst-italic">
                <?= $enrollment_started
                  ? 'We are now accepting enrollees for Grades 7 to 10. Be part of our growing community and secure your slot for the upcoming school year.'
                  : 'Enrollment will begin soon. Be ready to join our growing community!' ?>
              </p>
              <ul>
                <li><i class="bi bi-check"></i> <span>Open to incoming Grade 7 to Grade 10 students.</span></li>
                <li><i class="bi bi-check"></i> <span>Submit your requirements online or at the school campus.</span></li>
                <li><i class="bi bi-check"></i> <span>First-come, first-served basis.</span></li>
              </ul>

              <?php if ($enrollment_started): ?>
                <?php if (isset($_SESSION['new_users_id'])): ?>
                  <h3><a href="enroll?sy=<?= $school_year; ?>" class="btn btn-primary mt-3">Enroll Now</a></h3>
                <?php else: ?>
                  <h3><a href="#" class="btn btn-primary mt-3" data-toggle="modal" data-target="#authModal">Login to Enroll Now</a></h3>
                <?php endif; ?>
              <?php else: ?>
                <?php
                $days_remaining = (strtotime($start_date) - strtotime($today)) / 86400;
                ?>
                <div class="alert alert-warning mt-2">
                  <strong><?= $days_remaining ?> day<?= $days_remaining > 1 ? 's' : '' ?> to go until enrollment starts!</strong>
                </div>
              <?php endif; ?>
            </div>
        <?php
        endif;
      endif;
        ?>

          </div>

    </section><!-- /Details Section -->


    <section id="events" class="details section light-background">
      <div class="container section-title" data-aos="fade-up">
        <h2>School Events</h2>
        <div><span>Check out our upcoming school activities and gatherings</span> </div>
      </div><!-- End Section Title -->
      <div class="container">
        <?php
        $today = date('Y-m-d');
        $sql = "SELECT * FROM school_events_tbl WHERE event_date >= ? ORDER BY event_date ASC";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $today);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0):
          while ($event = mysqli_fetch_assoc($result)):
            $formatted_date = date('F d, Y', strtotime($event['event_date']));
            $formatted_time = date('h:i A', strtotime($event['event_time']));
        ?>

            <div class="col-md-7" data-aos="fade-up" data-aos-delay="100">
              <h3>Event Title : <?= strtoupper($event['event_title']); ?> </h3>
              <p><i class="bi bi-calendar"></i> Description : <strong> <?= strtoupper($event['event_description']); ?> </strong></p>


              <ul style="list-style:none;">
                <li><i class="bi bi-check"></i> <span>Event Date : <?= $formatted_date ?></span></li>
                <li><i class="bi bi-check"></i> <span>Event Time : <?= $formatted_time ?></span></li>
                <li><i class="bi bi-check"></i> <span>Place : <?= $event['event_place']; ?></span></li>
              </ul>
            </div>
          <?php
          endwhile;
        else:
          ?>
          <div class="col-12 text-center">
            <div class="alert alert-info">
              <i class="bi bi-info-circle"></i> No upcoming events at the moment. Please check back later.
            </div>
          </div>
        <?php endif; ?>
      </div>
    </section>


    <section id="about" class="about section">

      <div class="container" data-aos="fade-up" data-aos-delay="100">
        <div class="row align-items-xl-center gy-5">

          <div class="col-xl-5 content">
            <h2>About Us</h2>
            <p>"Bangbang National High School is committed to nurturing students through quality education, strong
              values, and a supportive learning environment that prepares them to become responsible and future-ready
              citizens.".</p>
          </div>

          <div class="col-xl-7">
            <div class="row gy-4 icon-boxes">

              <div class="col-md-6" data-aos="fade-up" data-aos-delay="200">
                <div class="icon-box">
                  <i class="bi bi-bullseye"></i>
                  <h3>Mission</h3>
                  <p>To protect and promote the right of every Filipino to quality, equitable, culture-based, and
                    complete basic education where:

                    Students learn in a child-friendly, gender-sensitive, safe, and motivating environment.

                    Teachers facilitate learning and constantly nurture every learner.

                    Administrators and staff, as stewards of the institution, ensure an enabling and supportive
                    environment for effective learning to happen.

                    Family, community, and other stakeholders are actively engaged and share responsibility for
                    developing life-long learners.</p>
                </div>
              </div> <!-- End Icon Box -->

              <div class="col-md-6" data-aos="fade-up" data-aos-delay="300">
                <div class="icon-box">
                  <i class="bi bi-eye"></i>
                  <h3>Vision</h3>
                  <p>We dream of Filipinos
                    who passionately love their country and whose values and competencies
                    enable them to realize their full potential and contribute meaningfully to building the nation
                    As a learner-centered public institution,
                    the Department of Education continuously improves itself to better serve its stakeholders.</p>
                </div>
              </div> <!-- End Icon Box -->



            </div>
          </div>

        </div>
      </div>

    </section><!-- /About Section -->

    <!-- Features Section -->


    <!-- Stats Section -->
    <section id="stats" class="stats section light-background">

      <div class="container" data-aos="fade-up" data-aos-delay="100">

        <div class="row gy-4">

          <div class="col-lg-3 col-md-6 d-flex flex-column align-items-center">
            <i class="bi bi-emoji-smile"></i>
            <div class="stats-item">
              <span data-purecounter-start="0" data-purecounter-end="
              <?php
              $count = mysqli_query($conn, 'SELECT COUNT(*) AS total_student FROM student_tbl WHERE status = 1');
              $count_student = mysqli_fetch_assoc($count);
              echo $count_student['total_student'];
              ?>" data-purecounter-duration="1"
                class="purecounter"></span>
              <p>Active Students</p>
            </div>
          </div><!-- End Stats Item -->

          <div class="col-lg-3 col-md-6 d-flex flex-column align-items-center">
            <i class="bi bi-journal-richtext"></i>
            <div class="stats-item">
              <span data-purecounter-start="0" data-purecounter-end="
              <?php
              $count = mysqli_query($conn, 'SELECT COUNT(*) AS total_teacher FROM teacher_tbl ');
              $count_teacher = mysqli_fetch_assoc($count);
              echo $count_teacher['total_teacher'];
              ?>" data-purecounter-duration="1"
                class="purecounter"></span>
              <p>Teacher</p>
            </div>
          </div><!-- End Stats Item -->

          <div class="col-lg-3 col-md-6 d-flex flex-column align-items-center">
            <i class="bi bi-headset"></i>
            <div class="stats-item">
              <span data-purecounter-start="0" data-purecounter-end="
               <?php
                $count = mysqli_query($conn, 'SELECT COUNT(*) AS total_student FROM student_tbl WHERE status = 1');
                $count_student = mysqli_fetch_assoc($count);
                echo $count_student['total_student'];
                ?>" data-purecounter-duration="1"
                class="purecounter"></span>
              <p>Alumni</p>
            </div>
          </div><!-- End Stats Item -->

          <div class="col-lg-3 col-md-6 d-flex flex-column align-items-center">
            <i class="bi bi-people"></i>
            <div class="stats-item">
              <span data-purecounter-start="0" data-purecounter-end="
               <?php
                $count = mysqli_query($conn, 'SELECT COUNT(*) AS total_student FROM student_tbl');
                $count_student = mysqli_fetch_assoc($count);
                echo $count_student['total_student'];
                ?>" data-purecounter-duration="1"
                class="purecounter"></span>
              <p>Total Students</p>
            </div>
          </div><!-- End Stats Item -->

        </div>

      </div>

    </section><!-- /Stats Section -->

    <!-- Details Section -->
    <!-- Gallery Section -->


    <!-- Team Section -->
    <section id="team" class="team section">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <h2>Employee</h2>
        <div><span>Check Our</span> <span class="description-title">Teachers</span></div>
      </div><!-- End Section Title -->

      <div class="container">

        <div class="row gy-5">

          <?php
          $select_teacher = mysqli_query($conn, 'SELECT * FROM teacher_tbl
          LEFT JOIN section_tbl ON section_tbl.section_id = teacher_tbl.section_id');
          while ($res_teacher = mysqli_fetch_assoc($select_teacher)) {

          ?>
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
              <div class="member">
                <div class="pic">
                  <img src="./../users/admin/teacher_profile/<?php echo $res_teacher['profile'] ?>"
                    alt="Profile"
                    style="width: 500px; height: 300px; object-fit: cover;   border: 3px solid #eee;">
                </div>
                <div class="member-info">
                  <h4><?= $res_teacher['teacher_name']; ?></h4>
                  <span>
                    <?php
                    if ($res_teacher['teacher_type'] == 'Class Adviser') {
                      echo $res_teacher['teacher_type'] . ' | Grade ' . $res_teacher['grade_level'] . ' -  ' . $res_teacher['section_name'];
                    } else {
                      echo $res_teacher['teacher_type'];
                    }
                    ?>
                  </span>

                </div>
              </div>
            </div>
          <?php } ?>
        </div>

      </div>

    </section><!-- /Team Section -->

    <!-- Pricing Section -->


    <!-- Faq Section -->
    <section id="faq" class="faq section light-background">
      <div class="container-fluid">
        <div class="row gy-4">
          <div class="col-lg-7 d-flex flex-column justify-content-center order-2 order-lg-1">
            <div class="content px-xl-5" data-aos="fade-up" data-aos-delay="100">
              <h3><span>Frequently Asked </span><strong>Questions</strong></h3>
              <p>
                Here are the most commonly asked questions by Grade 7 to 10 students regarding school policies,
                subjects, and daily concerns.
              </p>
            </div>

            <div class="faq-container px-xl-5" data-aos="fade-up" data-aos-delay="200">

              <div class="faq-item faq-active">
                <i class="faq-icon bi bi-question-circle"></i>
                <h3>What time does the class start and end?</h3>
                <div class="faq-content">
                  <p>Classes usually start at 7:30 AM and end at 4:00 PM, depending on your grade level and schedule.
                    Please refer to your class adviser for your official schedule.</p>
                </div>
                <i class="faq-toggle bi bi-chevron-right"></i>
              </div>

              <div class="faq-item">
                <i class="faq-icon bi bi-question-circle"></i>
                <h3>Are students allowed to use mobile phones during class?</h3>
                <div class="faq-content">
                  <p>No, students are not allowed to use mobile phones during class hours unless permitted by the
                    teacher for educational purposes.</p>
                </div>
                <i class="faq-toggle bi bi-chevron-right"></i>
              </div>

              <div class="faq-item">
                <i class="faq-icon bi bi-question-circle"></i>
                <h3>What should I do if I’m late or absent?</h3>
                <div class="faq-content">
                  <p>If you're late or absent, make sure to provide a valid excuse letter signed by your parent or
                    guardian and submit it to your class adviser upon return.</p>
                </div>
                <i class="faq-toggle bi bi-chevron-right"></i>
              </div>

              <div class="faq-item">
                <i class="faq-icon bi bi-question-circle"></i>
                <h3>How do I join school clubs or organizations?</h3>
                <div class="faq-content">
                  <p>Club recruitment usually happens at the start of the school year. Watch out for announcements from
                    your class adviser or the student government.</p>
                </div>
                <i class="faq-toggle bi bi-chevron-right"></i>
              </div>

              <div class="faq-item">
                <i class="faq-icon bi bi-question-circle"></i>
                <h3>Where can I get help if I’m struggling with my studies?</h3>
                <div class="faq-content">
                  <p>You can talk to your subject teacher, class adviser, or visit the guidance office for academic and
                    emotional support.</p>
                </div>
                <i class="faq-toggle bi bi-chevron-right"></i>
              </div>

            </div>
          </div>

          <div class="col-lg-5 order-1 order-lg-2">
            <img src="assets/img/faq.jpg" class="img-fluid" alt="FAQ" data-aos="zoom-in" data-aos-delay="100">
          </div>
        </div>
      </div>
    </section>




  </main>

  <footer id="footer" class="footer dark-background">

    <div class="container footer-top">
      <div class="row gy-4">

        <div class="col-lg-4 col-md-6 footer-about">
          <a href="index" class="logo d-flex align-items-center">
            <span class="sitename">Bangbang NHS</span>
          </a>
          <div class="footer-contact pt-3">
            <p>Brgy. Bangbang, Gasan</p>
            <p>Marinduque, Philippines 4905</p>
            <p class="mt-3"><strong>Phone:</strong> <span>+63 912 345 6789</span></p>
            <p><strong>Email:</strong> <span>bangbangnhs@gmail.com</span></p>
          </div>
          <div class="social-links d-flex mt-4">
            <a href="https://www.facebook.com/bangbangnhs/" target="_blank">
              <i class="bi bi-facebook"></i>
            </a>

            <a href="mailto:bangbangnhs@gmail.com">
              <i class="bi bi-envelope-fill"></i>
            </a>

            <a href="https://www.google.com/maps?q=Bangbang+National+High+School,+Gasan,+Marinduque" target="_blank">
              <i class="bi bi-geo-alt-fill"></i>
            </a>

          </div>
        </div>

        <div class="col-lg-2 col-md-3 footer-links">
          <h4>Quick Links</h4>
          <ul>
            <li><a href="#">Home</a></li>
            <li><a href="#about">About Us</a></li>
            <li><a href="#faq">FAQs</a></li>
          </ul>
        </div>

        <div class="col-lg-2 col-md-3 footer-links">
          <h4>Student Services</h4>
          <ul>
            <li><a href="#">Enrollment</a></li>
            <li><a href="#">Announcements</a></li>
            <li><a href="#">School Forms</a></li>
          </ul>
        </div>

        <div class="col-lg-4 col-md-12 footer-newsletter">
          <h4>Office Hours</h4>
          <p>Visit us during office hours for inquiries and assistance:</p>
          <ul style="padding-left: 1rem;">
            <li>Monday - Friday: 8:00 AM - 5:00 PM</li>
            <li>Lunch Break: 12:00 PM - 1:00 PM</li>
            <li>Saturday & Sunday: Closed</li>
          </ul>
        </div>
      </div>
    </div>

    <div class="container copyright text-center mt-4">
      <p>© <span>Copyright</span> <strong class="px-1 sitename">Bangbang National High School</strong> <span>All Rights
          Reserved</span></p>
      <div class="credits">
        Designed and Developed by the MarSU Capstone Developer
      </div>
    </div>

  </footer>

  <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i
      class="bi bi-arrow-up-short"></i></a>

  <!-- Preloader -->
  <div id="preloader"></div>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets/vendor/purecounter/purecounter_vanilla.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>

  <!-- Main JS File -->
  <script src="assets/js/main.js"></script>
  <?php
  include 'login.php';
  ?>
</body>


<div class="modal fade" id="authModal" tabindex="-1" role="dialog" aria-labelledby="authModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="authModalLabel">Login / Register</h5>
      </div>
      <div class="modal-body">
        <div id="loginForm">
          <h6>Login</h6>
          <form method="POST">
            <div class="form-group">
              <label for="loginEmail">Username<span class="text-danger">*</span></label>
              <input type="text" name="username" class="form-control" id="loginEmail"
                placeholder="Enter username" required>
            </div>
            <div class="form-group">
              <label for="loginPassword">Password<span class="text-danger">*</span></label>
              <input type="password" name="password" class="form-control" id="loginPassword"
                placeholder="Password" required>
            </div>
            <button type="submit" name="submit" class="btn btn-primary">Login</button>
          </form>
          <p class="mt-2">Don't have an account? <a href="#" id="showRegister">Register here</a></p>
        </div>
        <div id="registerForm" style="display: none;">
          <h6>Register</h6>
          <form method="POST" enctype="multipart/form-data">
            <div class="row">
              <div class="form-group col-12">
                <label for="fullName">Full Name<span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="full_name" placeholder="Enter full name"
                  required>
              </div>
              <div class="form-group col-12">
                <label for="email">Email<span class="text-danger">*</span></label>
                <input type="email" class="form-control" name="email" id="email" placeholder="Enter email"
                  required oninput="validateEmail()">

                <span id="email-status" style="color: red; font-size: 14px;"></span>
                <script>
                  function validateEmail() {
                    const email = document.getElementById('email').value;
                    const statusMessage = document.getElementById('email-status');
                    const submitBtn = document.getElementById('submitBtn');

                    // Reset the message every time the user types
                    statusMessage.textContent = "";

                    // Check if the email is valid (basic format check)
                    if (!validateEmailFormat(email)) {
                      statusMessage.textContent = "Please enter a valid email address.";
                      statusMessage.style.color = "red";
                      submitBtn.disabled = true;
                      return;
                    }

                    // Extract domain from email (after '@')
                    const domain = email.split('@')[1];

                    // If there's no domain, exit early
                    if (!domain) {
                      statusMessage.textContent = "Please enter a valid email address.";
                      statusMessage.style.color = "red";
                      submitBtn.disabled = true;
                      return;
                    }

                    // Check if the domain exists (has MX records)
                    checkDomainExists(domain, function(isValid) {
                      if (isValid) {
                        statusMessage.textContent = "This email domain exists.";
                        statusMessage.style.color = "green";
                        submitBtn.disabled = false;
                      } else {
                        statusMessage.textContent = "This email domain does not exist.";
                        statusMessage.style.color = "red";
                        submitBtn.disabled = true;
                      }
                    });
                  }

                  // Basic email format check (using regular expression)
                  function validateEmailFormat(email) {
                    const regex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
                    return regex.test(email);
                  }

                  // Function to check if the email domain has valid MX records (uses AJAX)
                  function checkDomainExists(domain, callback) {
                    const xhr = new XMLHttpRequest();
                    xhr.open('GET', 'validate_domain.php?domain=' + domain, true);
                    xhr.onload = function() {
                      if (xhr.status === 200) {
                        const response = xhr.responseText.trim();
                        if (response === 'valid') {
                          callback(true);
                        } else {
                          callback(false);
                        }
                      }
                    };
                    xhr.send();
                  }
                </script>
              </div>
              <div class="form-group col-12">
                <label for="username">Username<span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="username" placeholder="Enter username"
                  required>
              </div>

              <div class="form-group col-12">
                <label for="password">Password<span class="text-danger">*</span></label>
                <input type="password" class="form-control" name="password" id="password" placeholder="Password"
                  required id="password">
                <small id="password-strength" class="form-text"></small>
              </div>
              <div class="form-group col-12">
                <label for="confirmPassword">Confirm Password<span class="text-danger">*</span></label>
                <input type="password" class="form-control" name="confirm_password"
                  placeholder="Confirm password" required id="confirm_password">
                <small id="password-match" class="form-text"></small>
              </div>
              <script>
                const passwordInput = document.getElementById('password');
                const confirmPasswordInput = document.getElementById('confirm_password');
                const passwordStrengthLabel = document.getElementById('password-strength');
                const passwordMatchLabel = document.getElementById('password-match');

                passwordInput.addEventListener('input', () => {
                  const password = passwordInput.value;
                  const strength = checkPasswordStrength(password);
                  passwordStrengthLabel.textContent = `Password strength: ${strength}`;
                  passwordStrengthLabel.style.color = strength === 'Strong' ? 'green' : (strength === 'Medium' ? 'orange' : 'red');
                });

                confirmPasswordInput.addEventListener('input', () => {
                  const password = passwordInput.value;
                  const confirmPassword = confirmPasswordInput.value;
                  if (confirmPassword === password) {
                    passwordMatchLabel.textContent = 'Passwords match';
                    passwordMatchLabel.style.color = 'green';
                  } else {
                    passwordMatchLabel.textContent = 'Passwords do not match';
                    passwordMatchLabel.style.color = 'red';
                  }
                });

                function checkPasswordStrength(password) {
                  const hasLower = /[a-z]/.test(password);
                  const hasUpper = /[A-Z]/.test(password);
                  const hasNumber = /[0-9]/.test(password);
                  const hasSpecial = /[!@#$%^&*(),.?":{}|<>]/.test(password);
                  const isLongEnough = password.length >= 8;

                  if (hasLower && hasUpper && hasNumber && hasSpecial && isLongEnough) {
                    return 'Strong';
                  } else if ((hasLower + hasUpper + hasNumber + hasSpecial) >= 3 && isLongEnough) {
                    return 'Medium';
                  } else {
                    return 'Weak';
                  }
                }
              </script>
            </div>
            <button type="submit" name="register" class="btn btn-primary">Register</button>
          </form>

          <p class="mt-2">Already have an account? <a href="#" id="showLogin">Login here</a></p>
        </div>
      </div>
    </div>
  </div>
</div>
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
        window.location.href = 'logout.php';
      }
    });
  });
</script>

</html>