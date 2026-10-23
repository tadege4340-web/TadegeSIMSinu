<!-- LINK CSS -->
<link rel="stylesheet" href="mystatus.css">

<div class="mystatus-container">

    <!-- PAGE HEADER -->
    <div class="mystatus-header">
        <h2>📊 My Academic Status</h2>
    </div>

    <!-- SEARCH BOX -->
    <form method="GET" class="mystatus-search">
        <input
            type="text"
            name="q"
            placeholder="Search your academic records..."
            value="<?= htmlspecialchars($_GET['q'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
        >
        <button type="submit">Search</button>
    </form>

    <!-- TABLE -->
    <div class="mystatus-table-wrap">
        <table class="mystatus-table">
            <thead>
                <tr>
                    <th class="left">AcYear</th>
                    <th class="center">Year</th>
                    <th class="center">Semester</th>
                    <th class="left">Registration Date</th>
                    <th class="left">Remark</th>
                    <th class="center">Reg.Type</th>
                    <th class="center">SGPA</th>
                    <th class="center">CGPA</th>
                    <th class="center">Prev. Status</th>
                </tr>
            </thead>

            <tbody>
                <?php if (empty($rows)): ?>
                    <tr>
                        <td colspan="9" class="no-data">
                            No academic records found.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($rows as $r): ?>
                        <tr>
                            <td data-label="AcYear">
                                <?= htmlspecialchars((string)$r['ac_year']) ?>
                            </td>
                            <td data-label="Year" class="center">
                                <?= htmlspecialchars((string)$r['year']) ?>
                            </td>
                            <td data-label="Semester" class="center">
                                <?= htmlspecialchars((string)$r['semester']) ?>
                            </td>
                            <td data-label="Reg. Date">
                                <?= htmlspecialchars((string)$r['reg_date']) ?>
                            </td>
                            <td data-label="Remark">
                                <?= htmlspecialchars((string)$r['remark']) ?>
                            </td>
                            <td data-label="Reg. Type" class="center">
                                <?= htmlspecialchars((string)$r['reg_type']) ?>
                            </td>

                            <td data-label="SGPA" class="center sgpa-cell">
                                <?= (float)$r['sgpa'] > 0
                                    ? number_format((float)$r['sgpa'], 2)
                                    : '' ?>
                            </td>

                            <td data-label="CGPA" class="center cgpa-cell">
                                <?= (float)$r['cgpa'] > 0
                                    ? number_format((float)$r['cgpa'], 2)
                                    : '' ?>
                            </td>

                            <td data-label="Status" class="center">
                                <?php if ($r['status'] === 'Pass'): ?>
                                    <span class="status-pass">Pass</span>
                                <?php elseif ($r['status'] === 'Fail'): ?>
                                    <span class="status-fail">Fail</span>
                                <?php else: ?>
                                    <span class="status-none">---</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- FOOTER -->
    <div class="mystatus-footer">
        <div>
            Records per page:
            <select aria-label="Records per page">
                <option value="16">16</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select>
        </div>

        <div>
            Records:
            <?= count($rows) > 0 ? '1' : '0' ?>
            – <?= count($rows) ?>
            of <?= count($rows) ?>
        </div>
    </div>

</div>