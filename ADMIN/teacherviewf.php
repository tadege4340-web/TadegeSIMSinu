<!-- teacherviewf.php -->

<div class="students-page">

    <!-- HEADER -->
    <div class="students-header">
        <h2>VIEW Teachers</h2>
        
    </div>

    <!-- SUCCESS MESSAGE -->
    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'registered'): ?>
        <div class="alert alert-success">✅ Teacher registered successfully!</div>
    <?php endif; ?>

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

        <!-- POSITION -->
        <div class="filter-group">
            <label for="position"> Position</label>
            <select name="position" id="position" onchange="this.form.submit()">
                <option value="">All Positions</option>
                <?php foreach ($positions as $p): ?>
                    <option value="<?= htmlspecialchars($p) ?>"
                        <?= $f_position === $p ? 'selected' : '' ?>>
                        <?= htmlspecialchars($p) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- STATUS -->
        <div class="filter-group">
            <label for="status"> Status</label>
            <select name="status" id="status" onchange="this.form.submit()">
                <option value="">All Statuses</option>
                <?php foreach ($statuses as $s): ?>
                    <option value="<?= htmlspecialchars($s) ?>"
                        <?= $f_status === $s ? 'selected' : '' ?>>
                        <?= htmlspecialchars($s) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- SEARCH -->
        <div class="filter-group filter-search">
            <label for="search"> Search</label>
            <input type="text" name="search" id="search"
                   placeholder="ID, name, email…"
                   value="<?= htmlspecialchars($f_search) ?>">
        </div>

        <!-- ACTION BUTTONS -->
        <div class="filter-actions">
            <button type="submit" class="btn-apply">Apply</button>
            <a href="teacherview.php" class="btn-reset">Reset</a>
        </div>
    </form>

    <!-- ACTIVE FILTER SUMMARY -->
    <?php if ($f_college || $f_department || $f_position || $f_status || $f_search): ?>
        <div class="filter-summary">
            <strong>Showing:</strong>
            <?php if ($f_college): ?>
                <span class="chip"> <?= htmlspecialchars($college_names[$f_college] ?? $f_college) ?></span>
            <?php endif; ?>
            <?php if ($f_department): ?>
                <span class="chip"><?= htmlspecialchars($f_department) ?></span>
            <?php endif; ?>
            <?php if ($f_position): ?>
                <span class="chip"> <?= htmlspecialchars($f_position) ?></span>
            <?php endif; ?>
            <?php if ($f_status): ?>
                <span class="chip"> <?= htmlspecialchars($f_status) ?></span>
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
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Age</th>
                    <th>Gender</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Address</th>
                    <th>College</th>
                    <th>Department</th>
                    <th>Qualification</th>
                    <th>Position</th>
                    <th>Hire Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($teachers)): ?>
                    <?php $count = 1; ?>
                    <?php foreach ($teachers as $row): ?>
                        <tr>
                            <td class="center"><?= $count++ ?></td>
                            <td class="mono"><?= htmlspecialchars($row['id']) ?></td>
                            <td><strong><?= htmlspecialchars($row['full_name']) ?></strong></td>
                            <td class="center"><?= htmlspecialchars($row['age'] ?? '—') ?></td>
                            <td class="center"><?= htmlspecialchars($row['gender'] ?? '—') ?></td>
                            <td><?= htmlspecialchars($row['email'] ?? '—') ?></td>
                            <td><?= htmlspecialchars($row['phone'] ?? '—') ?></td>
                            <td><?= htmlspecialchars($row['address'] ?? '—') ?></td>
                            <td>
                                <?php
                                $code = $row['college'] ?? '';
                                echo htmlspecialchars($college_names[$code] ?? ($code ?: '—'));
                                ?>
                            </td>
                            <td><?= htmlspecialchars($row['department'] ?? '—') ?></td>
                            <td><?= htmlspecialchars($row['qualification'] ?? '—') ?></td>
                            <td><?= htmlspecialchars($row['position'] ?? '—') ?></td>
                            <td class="date-cell"><?= htmlspecialchars($row['hire_date'] ?? '—') ?></td>
                            <td class="center">
                                <?php
                                $st = strtolower($row['status'] ?? '');
                                $cls = $st === 'active' ? 'badge-approved'
                                     : ($st === 'inactive' ? 'badge-submitted' : 'badge-pending');
                                ?>
                                <span class="<?= $cls ?>">
                                    <?= htmlspecialchars($row['status'] ?? '—') ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="14" class="no-data">
                            <div class="empty-state">
                                <div class="es-icon"></div>
                                <h3>No teachers match your filters</h3>
                                <p>Try changing the college, department, or position.</p>
                                <a href="teacherview.php" class="btn-reset">Clear all filters</a>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- ADD TEACHER BUTTON -->
    <div class="add-teacher-bar">
        <a href="teacherform.php" class="btn-approve-all">➕ Add Teacher</a>
    </div>
</div>