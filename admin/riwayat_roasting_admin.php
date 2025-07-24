<?php
require_once '../config/init.php';
if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

// Handle CSV export
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="riwayat_rekomendasi_admin.csv"');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['ID', 'User ID', 'Jenis Kopi', 'Metode Penyajian', 'Proses', 'Profil Rasa', 'Rekomendasi', 'Aturan', 'Waktu']);

    // Fetch all data for export
    $db->query("SELECT * FROM recommendation_history ORDER BY created_at DESC");
    $export_data = $db->resultset();

    foreach ($export_data as $item) {
        fputcsv($output, [
            $item->id,
            $item->user_id,
            $item->jenis_kopi,
            $item->metode_penyajian,
            $item->proses,
            $item->profil_rasa,
            $item->recommended_roast,
            $item->rule_applied,
            $item->created_at
        ]);
    }
    fclose($output);
    exit;
}

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'])) {
    $db->query("UPDATE recommendation_history SET 
        jenis_kopi=:jenis_kopi, 
        metode_penyajian=:metode_penyajian, 
        profil_rasa=:profil_rasa, 
        proses=:proses, 
        recommended_roast=:recommended_roast, 
        rule_applied=:rule_applied 
        WHERE id=:id");
    $db->bind(':jenis_kopi', $_POST['jenis_kopi']);
    $db->bind(':metode_penyajian', $_POST['metode_penyajian']);
    $db->bind(':profil_rasa', $_POST['profil_rasa']);
    $db->bind(':proses', $_POST['proses']);
    $db->bind(':recommended_roast', $_POST['recommended_roast']);
    $db->bind(':rule_applied', $_POST['rule_applied']);
    $db->bind(':id', $_POST['edit_id']);
    $db->execute();
    redirect('riwayat_roasting_admin.php');
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $db->query("DELETE FROM recommendation_history WHERE id = :id");
    $db->bind(':id', $id);
    $db->execute();
    redirect('riwayat_roasting_admin.php');
}

// Fetch data
$db->query("SELECT * FROM recommendation_history ORDER BY created_at DESC");
$history = $db->resultset();
$page_title = 'Kelola Riwayat Rekomendasi';
$edit_id = $_GET['edit'] ?? null;
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title><?= $page_title ?></title>
    <link rel="stylesheet" href="/project/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: #faf8f6;
            font-family: 'Inter', Arial, sans-serif;
            color: #23190f;
        }

        .user-layout {
            display: flex;
            min-height: 100vh;
        }

        .user-content {
            flex: 1;
            padding: 48px 0 0 0;
            margin-left: 240px;
        }

        .riwayat-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(47, 27, 20, 0.08);
            padding: 32px 28px 28px 28px;
            width: 96%;
            margin: 36px auto 0 auto;
            max-width: none;
        }

        h3 {
            margin-top: 0;
            color: #a86b3c;
            font-size: 1.4rem;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .history-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 15px;
            margin-top: 0;
        }

        .history-table th,
        .history-table td {
            border: 1px solid #eee;
            padding: 10px 14px;
            text-align: left;
        }

        .history-table th {
            background: #f7f7f7;
            font-weight: bold;
            color: #a86b3c;
        }

        .history-table tr:nth-child(even) {
            background: #fcf8f4;
        }

        .history-table tr:hover {
            background: #f3e7de;
        }

        .btn-brown {
            background: #a86b3c;
            color: #fff;
            border: none;
            border-radius: 6px;
            padding: 7px 16px;
            font-weight: 600;
            cursor: pointer;
            font-size: 0.98em;
            transition: background 0.2s;
            margin-bottom: 2px;
        }

        .btn-brown:hover {
            background: #7b4a1e;
        }

        .btn-sm {
            font-size: 0.95em;
            padding: 5px 12px;
        }

        input[type="text"] {
            width: 120px;
            padding: 6px 8px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1em;
        }

        @media (max-width: 1100px) {
            .riwayat-card {
                max-width: 100%;
                padding: 18px 6px;
            }

            .user-content {
                margin-left: 0;
                padding: 24px 0 0 0;
            }
        }

        @media (max-width: 700px) {

            .history-table th,
            .history-table td {
                font-size: 13px;
                padding: 7px 6px;
            }

            .riwayat-card {
                padding: 8px 2px;
            }
        }
    </style>
</head>

<body>
    <div class="user-layout">
        <?php include '../includes/admin_sidebar.php'; ?>
        <div class="user-content">
            <div class="riwayat-card">
                <h3><i class="fas fa-history"></i> Riwayat Rekomendasi</h3>
                <a href="?export=csv" class="btn-brown" style="margin-bottom:16px; display:inline-block; text-decoration:none;"><i class="fas fa-file-csv"></i> Export CSV</a>
                <table class="history-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User ID</th>
                            <th>Jenis Kopi</th>
                            <th>Metode Penyajian</th>
                            <th>Proses</th>
                            <th>Profil Rasa</th>
                            <th>Rekomendasi</th>
                            <th>Aturan</th>
                            <th>Waktu</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($history as $item): ?>
                            <?php if ($edit_id == $item->id): ?>
                                <!-- Edit Form Row -->
                                <tr>
                                    <form method="post">
                                        <td><?= $item->id ?><input type="hidden" name="edit_id" value="<?= $item->id ?>"></td>
                                        <td><?= $item->user_id ?></td>
                                        <td><input type="text" name="jenis_kopi" value="<?= htmlspecialchars($item->jenis_kopi) ?>"></td>
                                        <td><input type="text" name="metode_penyajian" value="<?= htmlspecialchars($item->metode_penyajian) ?>"></td>
                                        <td><input type="text" name="proses" value="<?= htmlspecialchars($item->proses) ?>"></td>
                                        <td><input type="text" name="profil_rasa" value="<?= htmlspecialchars($item->profil_rasa) ?>"></td>
                                        <td><input type="text" name="recommended_roast" value="<?= htmlspecialchars($item->recommended_roast) ?>"></td>
                                        <td><input type="text" name="rule_applied" value="<?= htmlspecialchars($item->rule_applied) ?>"></td>
                                        <td><?= htmlspecialchars($item->created_at) ?></td>
                                        <td>
                                            <button type="submit" class="btn-brown btn-sm">Simpan</button>
                                            <a href="riwayat_roasting_admin.php" class="btn-brown btn-sm">Batal</a>
                                        </td>
                                    </form>
                                </tr>
                            <?php else: ?>
                                <!-- Normal Row -->
                                <tr>
                                    <td><?= $item->id ?></td>
                                    <td><?= $item->user_id ?></td>
                                    <td><?= htmlspecialchars($item->jenis_kopi) ?></td>
                                    <td><?= htmlspecialchars($item->metode_penyajian) ?></td>
                                    <td><?= htmlspecialchars($item->proses) ?></td>
                                    <td><?= htmlspecialchars($item->profil_rasa) ?></td>
                                    <td><?= htmlspecialchars($item->recommended_roast) ?></td>
                                    <td><?= htmlspecialchars($item->rule_applied) ?></td>
                                    <td><?= htmlspecialchars($item->created_at) ?></td>
                                    <td>
                                        <a href="riwayat_roasting_admin.php?edit=<?= $item->id ?>" class="btn-brown btn-sm"><i class="fas fa-edit"></i> Edit</a>
                                        <a href="riwayat_roasting_admin.php?delete=<?= $item->id ?>" class="btn-danger btn-sm" onclick="return confirm('Yakin hapus data ini?')"><i class="fas fa-trash"></i> Hapus</a>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>