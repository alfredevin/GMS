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
                    <div class="card shadow mb-4">

                        <div class="card-header py-3">
                            <div class="row">
                                <div class="col">
                                    <h6 class="m-0 font-weight-bold text-primary">List of Teacher</h6>
                                </div>
                                <div class="col-">
                                    <a href="addTeacher" class="btn btn-success btn-success btn-sm "><i class="fas fa-plus"></i> Add Teacher</a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Status</th>
                                            <th>Teacher ID</th>
                                            <th>Profile</th>
                                            <th> Type</th>
                                            <th> Email</th>
                                            <th> Specialization</th>
                                            <th>Grade/Section</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $query = "SELECT *,
            t.profile, 
            t.teacher_id, 
            t.teacher_name, 
            t.teacher_type, 
            s.section_name, 
            s.section_grade 
          FROM teacher_tbl t 
          LEFT JOIN section_tbl s ON t.section_id = s.section_id";

                                        $result = mysqli_query($conn, $query);

                                        while ($row = mysqli_fetch_assoc($result)) {
                                            $grade_section = ($row['teacher_type'] == 'Class Adviser')
                                                ? 'Grade ' . $row['section_grade'] . ' - ' . $row['section_name']
                                                : 'N/A';

                                            echo "<tr>";
                                            echo "<td>" . ($row['teacher_status'] == '1' ? 'Active' : 'Inactive') . "</td>";
                                            echo "<td>" . htmlspecialchars($row['teacher_id']) . "</td>";
                                            echo "<td>
                                                    <img src='./teacher_profile/" . htmlspecialchars($row['profile']) . "' 
                                                        alt='Profile' 
                                                        style='width: 50px; height: 50px; object-fit: cover; border-radius: 50%; margin-right: 10px;'>
                                                    " . htmlspecialchars($row['teacher_name']) . "
                                                </td>";
                                            echo "<td>" . htmlspecialchars($row['teacher_type']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['specialization']) . "</td>";
                                            echo "<td>" . $grade_section . "</td>";

                                            echo '<td>
    <a href="#" 
       class="btn btn-sm btn-primary editTeacherBtn"
       data-id="' . $row['teacher_id'] . '"
       data-name="' . htmlspecialchars($row['teacher_name']) . '"
       data-type="' . $row['teacher_type'] . '"
       data-grade="' . $row['grade_level'] . '"
       data-section="' . $row['section_id'] . '"
       data-email="' . $row['email'] . '"
       data-status="' . $row['teacher_status'] . '"

    >
       Edit
    </a>
</td>';

                                            echo "</tr>";
                                        }
                                        ?>

                                    </tbody>
                                </table>
                            </div>
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
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll('.editTeacherBtn').forEach(button => {
                button.addEventListener('click', function() {
                    const teacherId = this.dataset.id;
                    const teacherName = this.dataset.name;
                    const teacherType = this.dataset.type;
                    const gradeLevel = this.dataset.grade;
                    const sectionId = this.dataset.section;
                    const email = this.dataset.email;
                    const status = this.dataset.status;


                    Swal.fire({
                        title: 'Edit Teacher',
                        html: `
                    <label>Teacher Id:</label>
                    <input id="edit_teacher_id" class="swal2-input" value="${teacherId}"  >
                    <label>Name:</label>
                    <input id="edit_teacher_name" class="swal2-input" value="${teacherName}" required oninput="this.value = this.value.toUpperCase();">
                    
                    <label>Email:</label>
                    <input id="edit_email" class="swal2-input" value="${email}" required >
                    <label>Type:</label>
                    <select id="edit_teacher_type" class="swal2-input">
                        <option value="Subject Teacher" ${teacherType === 'Subject Teacher' ? 'selected' : ''}>Subject Teacher</option>
                        <option value="Class Adviser" ${teacherType === 'Class Adviser' ? 'selected' : ''}>Class Adviser</option>
                    </select>
                    <div id="grade_section_fields" style="display: ${teacherType === 'Class Adviser' ? 'block' : 'none'}">
                        <label>Grade:</label>
                        <select id="edit_grade_level" class="swal2-input">
                            <option value="7" ${gradeLevel == 7 ? 'selected' : ''}>Grade 7</option>
                            <option value="8" ${gradeLevel == 8 ? 'selected' : ''}>Grade 8</option>
                            <option value="9" ${gradeLevel == 9 ? 'selected' : ''}>Grade 9</option>
                            <option value="10" ${gradeLevel == 10 ? 'selected' : ''}>Grade 10</option>
                            <!-- Add more grades if needed -->
                        </select>
                        <label>Section:</label>
                        <select id="edit_section_id" class="swal2-input">
                            <!-- Sections will be dynamically filled -->
                        </select>
                       

                    </div>
                    <label>Status:</label>
<select id="edit_teacher_status" class="swal2-input">
    <option value="1" ${status == '1' ? 'selected' : ''}>Active</option>
    <option value="2" ${status == '2' ? 'selected' : ''}>Inactive</option>
</select>
                `,
                        didOpen: () => {
                            const teacherTypeSelect = document.getElementById('edit_teacher_type');
                            const gradeLevelSelect = document.getElementById('edit_grade_level');
                            const sectionSelect = document.getElementById('edit_section_id');
                            const status = document.getElementById('edit_teacher_status').value;


                            function loadSections(grade) {
                                fetch(`get_section.php?grade=${grade}`)
                                    .then(res => res.json())
                                    .then(data => {
                                        sectionSelect.innerHTML = '';
                                        data.forEach(sec => {
                                            const option = document.createElement('option');
                                            option.value = sec.section_id;
                                            option.textContent = sec.section_name;
                                            if (sec.section_id === sectionId) {
                                                option.selected = true;
                                            }
                                            sectionSelect.appendChild(option);
                                        });
                                    });
                            }

                            if (gradeLevelSelect) {
                                loadSections(gradeLevelSelect.value);
                                gradeLevelSelect.addEventListener('change', () => loadSections(gradeLevelSelect.value));
                            }

                            teacherTypeSelect.addEventListener('change', () => {
                                const gradeSection = document.getElementById('grade_section_fields');
                                if (teacherTypeSelect.value === 'Class Adviser') {
                                    gradeSection.style.display = 'block';
                                    loadSections(gradeLevelSelect.value);
                                } else {
                                    gradeSection.style.display = 'none';
                                }
                            });
                        },
                        showCancelButton: true,
                        confirmButtonText: 'Update'
                    }).then(result => {
                        if (result.isConfirmed) {
                            const id = document.getElementById('edit_teacher_id').value;
                            const name = document.getElementById('edit_teacher_name').value;
                            const type = document.getElementById('edit_teacher_type').value;
                            const emails = document.getElementById('edit_email').value;
                            const grade = type === 'Class Adviser' ? document.getElementById('edit_grade_level').value : '';
                            const section = type === 'Class Adviser' ? document.getElementById('edit_section_id').value : '';
                            const status = document.getElementById('edit_teacher_status').value;

                            const xhr = new XMLHttpRequest();
                            xhr.open("POST", "update_teacher.php", true);
                            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

                            xhr.onload = function() {
                                if (this.responseText.trim() === "success") {
                                    Swal.fire('Updated!', 'Teacher updated successfully.', 'success').then(() => location.reload());
                                } else if (this.responseText.trim() === "duplicate") {
                                    Swal.fire('Duplicate Adviser!', 'This grade and section already has a Class Adviser.', 'warning');
                                } else if (this.responseText.trim() === "invalid_section") {
                                    Swal.fire('Invalid Section!', 'Selected section does not exist.', 'error');
                                } else {
                                    Swal.fire('Error!', this.responseText, 'error');
                                }


                            };

                            const data = `teacher_id=${encodeURIComponent(id)}&teacher_name=${encodeURIComponent(name)}&teacher_type=${encodeURIComponent(type)}&teacher_email=${encodeURIComponent(emails)}&grade_level=${encodeURIComponent(grade)}&section_id=${encodeURIComponent(section)}&status=${encodeURIComponent(status)}`;
                            xhr.send(data);
                        }
                    });
                });
            });
        });
    </script>

</body>

</html>