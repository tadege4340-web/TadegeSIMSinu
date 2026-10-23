<?php
/* ============================================================
   MY COURSES — VIEW FILE (mycoursef.php)
   ============================================================ */

if (!isset($result)) {
    header("Location: mycourse.php");
    exit();
}
?>

<div class="course-container">
    <h1>📚 My Courses</h1>

    <p class="info-note">
        <strong>ℹ️ Note:</strong> Grades are shown 
        <b>only after the registrar approves them</b>. 
        Until then, courses appear without a grade.
    </p>

    <!-- THIS WRAPPER IS THE SECRET TO MOBILE SCROLLING -->
    <div class="table-responsive">
        <table class="course-table">
            <thead>
                <tr>
                    <th>Ac Year</th>
                    <th>Sem</th>
                    <th>Course ID</th>
                    <th>Course Code</th>
                    <th>Course Name</th>
                    <th>Credit</th>
                    <th>Category</th>
                    <th>Reg. Type</th>
                    <th>Grade</th>
                </tr>
            </thead>
            <tbody>
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <?php
                    $grade_status = $row['grade_status'] ?? 'Pending';
                    $is_approved  = ($grade_status === 'Approved');
                    $has_grade    = !empty($row['grade']);
                    ?>
                    <tr>
                        <td data-label="Ac Year"><?= htmlspecialchars($row['ac_year'] ?? '—') ?></td>
                        <td data-label="Semester"><?= htmlspecialchars($row['semester'] ?? '—') ?></td>
                        <td data-label="Course ID"><?= htmlspecialchars($row['course_id']) ?></td>
                        <td data-label="Course Code"><strong><?= htmlspecialchars($row['course_code']) ?></strong></td>
                        <td data-label="Course Name"><?= htmlspecialchars($row['course_name']) ?></td>
                        <td data-label="Credit"><?= htmlspecialchars($row['credit_hour']) ?></td>
                        <td data-label="Category"><?= htmlspecialchars($row['category']) ?></td>
                        <td data-label="Reg. Type"><?= htmlspecialchars($row['reg_type'] ?? 'N') ?></td>
                        <td data-label="Grade">
                            <?php if ($is_approved && $has_grade): ?>
                                <strong style="color:#065f46;font-size:14px;">
                                    <?= htmlspecialchars($row['grade']) ?>
                                </strong>
                            <?php elseif ($has_grade): ?>
                                <span class="grade-pending" title="Awaiting registrar approval">
                                    ⏳ Awaiting Approval
                                </span>
                            <?php else: ?>
                                <span class="grade-empty">⏳ Pending</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="9" class="no-data">No courses assigned to you.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>