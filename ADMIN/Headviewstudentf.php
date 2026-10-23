<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- This links the CSS so the table looks good inside the iframe -->
    <link rel="stylesheet" href="Head.css">
    <style>
        /* Small tweak just for the iframe background */
        body { background-color: #f4f6f9; padding: 20px; }
    </style>
</head>
<body>

    <div class="page-container">

        <!-- Page Header -->
        <div class="page-header">
            <div class="header-text">
                <h2> Department Students</h2>
                <p>
                    Showing students for: <strong><?= htmlspecialchars($hod_department ?: 'All Departments (Admin)') ?></strong> 
                    <?= !empty($hod_college) ? '(' . htmlspecialchars($hod_college) . ')' : '' ?>
                </p>
            </div>
            <div class="total-badge">
                Total Students: <?= count($students) ?>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="filter-bar">
            <!-- Make sure the action points to your PHP controller -->
            <form method="GET" action="Headviewstudent.php" class="filter-form">
                
                <div class="filter-group search-group">
                    <label> Search</label>
                    <input type="text" name="search" value="<?= htmlspecialchars($search_query) ?>" placeholder="Search by ID or Name...">
                </div>

                <div class="filter-group year-group">
                    <label>Year</label>
                    <select name="year">
                        <option value="">All Years</option>
                        <?php for ($i = 1; $i <= 7; $i++): ?>
                            <option value="<?= $i ?>" <?= $filter_year == $i ? 'selected' : '' ?>>Year <?= $i ?></option>
                        <?php endfor; ?>
                    </select>
                </div>

                <!-- 🔥 NEW SEMESTER FILTER -->
                <div class="filter-group semester-group">
                    <label> Semester</label>
                    <select name="semester">
                        <option value="">All Semesters</option>
                        <option value="1" <?= $filter_semester == 1 ? 'selected' : '' ?>>Semester 1</option>
                        <option value="2" <?= $filter_semester == 2 ? 'selected' : '' ?>>Semester 2</option>
                    </select>
                </div>

                <div class="filter-actions">
                    <button type="submit" class="btn-apply">Apply Filters</button>
                    <a href="Headviewstudent.php" class="btn-reset">Reset</a>
                </div>
            </form>
        </div>

        <!-- Students Table -->
        <?php if (empty($students)): ?>
            <div class="empty-state">
                <h3>No students found</h3>
                <p>There are no students matching your criteria in this department.</p>
            </div>
        <?php else: ?>
            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Full Name</th>
                            <th>Gender</th>
                            <th>Year</th>
                            <th>Semester</th> <!-- 🔥 ADDED -->
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $s): ?>
                            <tr>
                                <td class="student-id"><?= htmlspecialchars($s['student_id']) ?></td>
                                <td class="student-name"><?= htmlspecialchars($s['firstname'] . ' ' . $s['father_name']) ?></td>
                                <td><?= htmlspecialchars($s['gender'] ?? 'N/A') ?></td>
                                <td>Year <?= htmlspecialchars($s['year'] ?? 'N/A') ?></td>
                                <!-- 🔥 ADDED DATA -->
                                <td>Sem <?= htmlspecialchars($s['current_semester'] ?? 'N/A') ?></td> 
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

    </div>

</body>
</html>