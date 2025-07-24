<?php
require_once 'config/init.php';

$is_admin = isLoggedIn() && isAdmin();

$roast = $_GET['roast'] ?? '';

if (isset($_GET['ajax']) && $_GET['ajax'] == '1') {
    $db->query("SELECT * FROM proses_roasting WHERE roast_level = :roast");
    $db->bind(':roast', $roast);
    $proses = $db->single();
    if ($proses) {
        ?>
        <h3><?= htmlspecialchars($proses->roast_level) ?> Roast</h3>
        <table style="width:100%; border-collapse:collapse; margin:18px 0; font-size:15px;">
            <tr><th style="text-align:left; padding:8px; background:#f7f7f7; color:#a86b3c;">Density Level</th><td style="padding:8px;"><?= htmlspecialchars($proses->density_level) ?></td></tr>
            <tr><th style="text-align:left; padding:8px; background:#f7f7f7; color:#a86b3c;">Apparent Density</th><td style="padding:8px;"><?= htmlspecialchars($proses->apparent_density) ?></td></tr>
            <tr><th style="text-align:left; padding:8px; background:#f7f7f7; color:#a86b3c;">Suhu Awal</th><td style="padding:8px;"><?= htmlspecialchars($proses->initial_temp) ?>°C</td></tr>
            <tr><th style="text-align:left; padding:8px; background:#f7f7f7; color:#a86b3c;">Suhu Akhir</th><td style="padding:8px;"><?= htmlspecialchars($proses->final_temp) ?>°C</td></tr>
            <tr><th style="text-align:left; padding:8px; background:#f7f7f7; color:#a86b3c;">Agtron</th><td style="padding:8px;"><?= htmlspecialchars($proses->agtron) ?></td></tr>
            <tr><th style="text-align:left; padding:8px; background:#f7f7f7; color:#a86b3c;">Rentang Kematangan</th><td style="padding:8px;"><?= htmlspecialchars($proses->roast_level) ?></td></tr>
        </table>
        <?php
    } else {
        echo '<p>Data proses roasting tidak ditemukan.</p>';
    }
    exit;
}

if (!$roast) {
    echo "<p>Roast level tidak ditemukan.</p>";
    exit;
}

$db->query("SELECT * FROM proses_roasting WHERE roast_level = :roast");
$db->bind(':roast', $roast);
$proses = $db->single();

if (!$proses) {
    echo "<p>Data proses roasting untuk level <b>" . htmlspecialchars($roast) . "</b> tidak ditemukan.</p>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Proses Roasting - <?= htmlspecialchars($proses->roast_level) ?></title>
    <link rel="stylesheet" href="/project/css/style.css">
    <style>
        .roasting-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(47,27,20,0.06);
            padding: 32px 36px;
            max-width: 600px;
            margin: 40px auto;
        }
        .roasting-title {
            font-size: 2rem;
            color: #a86b3c;
            margin-bottom: 12px;
        }
        .roasting-table {
            width: 100%;
            border-collapse: collapse;
            margin: 18px 0;
        }
        .roasting-table th, .roasting-table td {
            border: 1px solid #eee;
            padding: 10px 14px;
            text-align: left;
        }
        .roasting-table th {
            background: #f7f7f7;
            color: #a86b3c;
        }
        .btn-edit {
            display: inline-block;
            background: #f7b731;
            color: #23190f;
            padding: 10px 18px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            margin-top: 18px;
        }
        .btn-edit:hover {
            background: #e1a722;
        }
    </style>
</head>
<body>
    <div class="roasting-card">
        <div class="roasting-title"><?= htmlspecialchars($proses->roast_level) ?> Roast</div>
        <table class="roasting-table">
            <tr>
                <th>Density Level</th>
                <td><?= htmlspecialchars($proses->density_level) ?></td>
            </tr>
            <tr>
                <th>Apparent Density (g/l)</th>
                <td><?= htmlspecialchars($proses->apparent_density) ?></td>
            </tr>
            <tr>
                <th>Suhu Awal</th>
                <td><?= htmlspecialchars($proses->initial_temp) ?>°C</td>
            </tr>
            <tr>
                <th>Suhu Akhir</th>
                <td><?= htmlspecialchars($proses->final_temp) ?>°C</td>
            </tr>
            <tr>
                <th>Nilai Agtron</th>
                <td><?= htmlspecialchars($proses->agtron) ?></td>
            </tr>
            <tr>
                <th>Rentang Kematangan</th>
                <td><?= htmlspecialchars($proses->roast_level) ?></td>
            </tr>
        </table>
        <?php if ($is_admin): ?>
            <a href="/project/admin/proses_roasting_crud.php?edit=<?= urlencode($proses->roast_level) ?>" class="btn-edit">
                Edit Proses Roasting
            </a>
        <?php endif; ?>
    </div>
</body>
</html>