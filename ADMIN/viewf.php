<!-- viewf.php -->

<div class="students-page">

    <!-- HEADER -->
    <div class="students-header">
        <h2> VIEW Students</h2>
    </div>

    <!-- FILTER FORM -->
    <form method="get" class="students-filter-bar">

        <!-- COLLEGE -->
        <div class="filter-group">
            <label for="college"> College</label>
            <select name="college" id="college" onchange="this.form.submit()">
                <option value="">All Colleges</option>
                <?php foreach ($colleges as $c): ?>
                    <option value="<?= htmlspecialchars($c['code']) ?>"
                        <?= $f_college === $c['code'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($c['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- DEPARTMENT -->
        <div class="filter-group">
            <label for="department"> Department</label>
            <select name="department" id="department"
                    onchange="this.form.submit()"
                    <?= $f_college === '' ? 'disabled' : '' ?>>
                <option value="">
                    <?= $f_college === '' ? '— Select College First —' : 'All Departments' ?>
                </option>
                <?php foreach ($departments as $d): ?>
                    <option value="<?= htmlspecialchars($d) ?>"
                        <?= $f_department === $d ? 'selected' : '' ?>>
                        <?= htmlspecialchars($d) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- YEAR -->
        <div class="filter-group">
            <label for="year">Year</label>
            <select name="year" id="year" onchange="this.form.submit()">
                <option value="">All Years</option>
                <?php for ($y = 1; $y <= 7; $y++): ?>
                    <option value="<?= $y ?>" <?= $f_year === $y ? 'selected' : '' ?>>
                        Year <?= $y ?>
                    </option>
                <?php endfor; ?>
            </select>
        </div>

        <!-- SEMESTER -->
        <div class="filter-group">
            <label for="semester"> Semester</label>
            <select name="semester" id="semester" onchange="this.form.submit()">
                <option value="">All Semesters</option>
                <option value="1" <?= $f_semester === 1 ? 'selected' : '' ?>>Semester 1</option>
                <option value="2" <?= $f_semester === 2 ? 'selected' : '' ?>>Semester 2</option>
            </select>
        </div>

        <!-- SEARCH -->
        <div class="filter-group filter-search">
            <label for="search"> Search</label>
            <input type="text" name="search" id="search"
                   placeholder="ID or name…"
                   value="<?= htmlspecialchars($f_search) ?>">
        </div>

        <!-- ACTION BUTTONS -->
        <div class="filter-actions">
            <button type="submit" class="btn-apply">Apply</button>
            <a href="view.php" class="btn-reset">Reset</a>
        </div>
    </form>

    <!-- ACTIVE FILTER SUMMARY -->
    <?php if ($f_college || $f_department || $f_year || $f_semester || $f_search): ?>
        <div class="filter-summary">
            <strong>Showing:</strong>
            <?php if ($f_college): ?>
                <span class="chip">
                     <?= htmlspecialchars($college_names[$f_college] ?? $f_college) ?>
                </span>
            <?php endif; ?>
            <?php if ($f_department): ?>
                <span class="chip"> <?= htmlspecialchars($f_department) ?></span>
            <?php endif; ?>
            <?php if ($f_year): ?>
                <span class="chip"> Year <?= (int)$f_year ?></span>
            <?php endif; ?>
            <?php if ($f_semester): ?>
                <span class="chip"> Semester <?= (int)$f_semester ?></span>
            <?php endif; ?>
            <?php if ($f_search): ?>
                <span class="chip"> "<?= htmlspecialchars($f_search) ?>"</span>
            <?php endif; ?>
            <span class="count-badge"><?= $totalFiltered ?> of <?= $totalAll ?></span>
        </div>
    <?php endif; ?>

    <!-- RESULTS TABLE -->
    <div class="students-table-wrap">
        <table class="students-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Student ID</th>
                    <th>First Name</th>
                    <th>Father Name</th>
                    <th>Grandfather Name</th>
                    <th>Age</th>
                    <th>Gender</th>
                    <th>Phone</th>
                    <th>Region</th>
                    <th>Secondary School</th>
                    <th>College</th>
                    <th>Department</th>
                    <th>Year</th>
                    <th>Semester</th>
                    <th>Registered On</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($students)): ?>
                    <?php $count = 1; ?>
                    <?php foreach ($students as $row): ?>
                        <tr>
                            <td class="center"><?= $count++ ?></td>
                            <td class="mono"><?= htmlspecialchars($row['student_id']) ?></td>
                            <td><?= htmlspecialchars($row['firstname']) ?></td>
                            <td><?= htmlspecialchars($row['father_name']) ?></td>
                            <td><?= htmlspecialchars($row['grandfather_name']) ?></td>
                            <td class="center"><?= htmlspecialchars($row['age'] ?? '—') ?></td>
                            <td class="center"><?= htmlspecialchars($row['gender'] ?? '—') ?></td>
                            <td><?= htmlspecialchars($row['phone'] ?? '—') ?></td>
                            <td><?= htmlspecialchars($row['region'] ?? '—') ?></td>
                            <td><?= htmlspecialchars($row['secondary_school'] ?? '—') ?></td>
                            <td>
                                <?php
                                $code = $row['college'] ?? '';
                                echo htmlspecialchars($college_names[$code] ?? ($code ?: '—'));
                                ?>
                            </td>
                            <td><?= htmlspecialchars($row['department'] ?? '—') ?></td>
                            <td class="center"><?= htmlspecialchars($row['year'] ?? '—') ?></td>
                            <td class="center">Sem <?= htmlspecialchars($row['current_semester'] ?? '—') ?></td>
                            <td class="date-cell"><?= htmlspecialchars($row['created_at'] ?? '—') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="15" class="no-data">
                            <div class="empty-state">
                                <div class="es-icon">🔍</div>
                                <h3>No students match your filters</h3>
                                <p>Try changing the college, department, year, or semester.</p>
                                <a href="view.php" class="btn-reset">Clear all filters</a>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>