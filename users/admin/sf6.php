<?php
include '../../config.php';
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
                    <div class="card shadow mb-4 border-bottom-success">
                        <div class="card-header py-3 bg-white">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h6 class="m-0 font-weight-bold text-success text-uppercase">
                                        <i class="fas fa-chart-bar mr-2"></i> School Form 6 (SF6) - Summarized Register
                                        of Learner Status
                                    </h6>
                                </div>
                                <div class="col text-right">
                                    <a href="print_sf6.php" target="_blank" class="btn btn-success shadow-sm">
                                        <i class="fas fa-print"></i> Generate & Print SF6
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-success bg-gradient-success text-white border-0 shadow-sm">
                                <h5 class="font-weight-bold"><i class="fas fa-info-circle mr-2"></i> End of School Year
                                    (EoSY) Report</h5>
                                <p class="mb-0">School Form 6 contains the overall summary of the students' academic
                                    status. The system automatically computes the following based on the teachers'
                                    e-Class Records:</p>
                                <ul class="mt-2 mb-0">
                                    <li><strong>Promoted:</strong> No failed subjects.</li>
                                    <li><strong>Conditionally Promoted:</strong> 1 to 2 failed subjects.</li>
                                    <li><strong>Retained:</strong> 3 or more failed subjects.</li>
                                    <li><strong>Achievement Levels:</strong> Outstanding (90-100), Very Satisfactory
                                        (85-89), Satisfactory (80-84), Fairly Satisfactory (75-79), Did Not Meet
                                        Expectations (Below 75).</li>
                                </ul>
                            </div>

                            <div class="text-center mt-5 mb-4">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/8/8c/Department_of_Education_%28DepEd%29.svg/512px-Department_of_Education_%28DepEd%29.svg.png"
                                    width="100" class="mb-3" style="opacity: 0.5;">
                                <h5 class="text-gray-500 font-weight-bold">Ready to generate the official DepEd SF6
                                    Report?</h5>
                                <p class="text-muted">Click the print button on the top right to open the printable
                                    document.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php include './../template/footer.php'; ?>
        </div>
    </div>

    <a class="scroll-to-top rounded" href="#page-top"><i class="fas fa-angle-up"></i></a>
    <?php include './../template/script.php'; ?>
</body>

</html>