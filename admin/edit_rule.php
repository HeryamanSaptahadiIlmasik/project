<?php
require_once '../config/init.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

$id = $_GET['id'] ?? 0;
$db->query("SELECT * FROM roasting_rules WHERE id = :id");
$db->bind(':id', $id);
$rule = $db->single();

if (!$rule) {
    redirect('manage_rules.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rule_name = $_POST['rule_name'];
    $jenis_kopi = $_POST['jenis_kopi'];
    $metode_penyajian = $_POST['metode_penyajian'];
    $profil_rasa = $_POST['profil_rasa'];
    $proses = $_POST['proses'];
    $recommended_roast = $_POST['recommended_roast'];
    $roasting_notes = $_POST['roasting_notes'];

    $db->query("UPDATE roasting_rules SET rule_name=:rule_name, jenis_kopi=:jenis_kopi, metode_penyajian=:metode_penyajian, profil_rasa=:profil_rasa, proses=:proses, recommended_roast=:recommended_roast, roasting_notes=:roasting_notes WHERE id=:id");
    $db->bind(':rule_name', $rule_name);
    $db->bind(':jenis_kopi', $jenis_kopi);
    $db->bind(':metode_penyajian', $metode_penyajian);
    $db->bind(':profil_rasa', $profil_rasa);
    $db->bind(':proses', $proses);
    $db->bind(':recommended_roast', $recommended_roast);
    $db->bind(':roasting_notes', $roasting_notes);
    $db->bind(':id', $id);
    $db->execute();

    redirect('manage_rules.php');
}

// Ambil value unik untuk datalist
$tmp = $db->query("SELECT DISTINCT metode_penyajian FROM roasting_rules");
$metodeList = $tmp ? $tmp->fetchAll(PDO::FETCH_COLUMN) : [];
$tmp = $db->query("SELECT DISTINCT profil_rasa FROM roasting_rules");
$profilList = $tmp ? $tmp->fetchAll(PDO::FETCH_COLUMN) : [];
$tmp = $db->query("SELECT DISTINCT proses FROM roasting_rules");
$prosesList = $tmp ? $tmp->fetchAll(PDO::FETCH_COLUMN) : [];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Rule</title>
    <style>
    body {
        margin: 0;
        font-family: 'Inter', Arial, sans-serif;
        background: #faf8f6;
        color: #23190f;
    }
    .form-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(47,27,20,0.06);
        padding: 32px 28px;
        max-width: 520px;
        margin: 60px auto 0 auto;
        position: relative;
    }
    .form-card h2 {
        margin-top: 0;
        color: #2f1b14;
        margin-bottom: 24px;
    }
    .form-group {
        margin-bottom: 18px;
    }
    .form-group label {
        display: block;
        font-weight: 500;
        margin-bottom: 6px;
    }
    .form-group input,
    .form-group select {
        width: 100%;
        padding: 8px 10px;
        border: 1px solid #ccc;
        border-radius: 6px;
        font-size: 1rem;
    }
    .form-actions {
        margin-top: 24px;
        display: flex;
        gap: 12px;
        justify-content: flex-end;
    }
    .btn {
        padding: 8px 20px;
        border: none;
        border-radius: 6px;
        font-size: 1rem;
        cursor: pointer;
        background: #28a745;
        color: #fff;
        transition: background 0.2s;
        text-decoration: none;
    }
    .btn:hover {
        background: #218838;
    }
    .btn-cancel {
        background: #dc3545;
        position: absolute;
        top: 18px;
        right: 24px;
        color: #fff;
        padding: 7px 16px;
        border-radius: 6px;
        font-size: 0.98rem;
        text-decoration: none;
        border: none;
        transition: background 0.2s;
        z-index: 2;
    }
    .btn-cancel:hover {
        background: #b52a37;
        color: #fff;
    }
    @media (max-width: 900px) {
        .form-card {
            padding: 18px 6px;
        }
    }
    </style>
</head>
<body>
    <div class="form-card">
        <a href="manage_rules.php" class="btn btn-cancel back-btn">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
        <h2>Edit Rule Roasting</h2>
        <form method="post">
            <div class="form-group">
                <label>Rule Name:</label>
                <input type="text" name="rule_name" value="<?= htmlspecialchars($rule->rule_name) ?>" required>
            </div>
            <div class="form-group">
                <label>Jenis Kopi:</label>
                <input type="text" name="jenis_kopi" value="<?= htmlspecialchars($rule->jenis_kopi) ?>" required>
            </div>
            <div class="form-group">
                <label>Metode Penyajian:</label>
                <input type="text" name="metode_penyajian" list="metodeList" value="<?= htmlspecialchars($rule->metode_penyajian) ?>" required>
                <datalist id="metodeList">
                    <?php foreach($metodeList as $m): ?>
                        <option value="<?= htmlspecialchars($m) ?>">
                    <?php endforeach; ?>
                </datalist>
            </div>
            <div class="form-group">
                <label>Profil Rasa:</label>
                <input type="text" name="profil_rasa" list="profilList" value="<?= htmlspecialchars($rule->profil_rasa) ?>" required>
                <datalist id="profilList">
                    <?php foreach($profilList as $p): ?>
                        <option value="<?= htmlspecialchars($p) ?>">
                    <?php endforeach; ?>
                </datalist>
            </div>
            <div class="form-group">
                <label>Proses:</label>
                <input type="text" name="proses" list="prosesList" value="<?= htmlspecialchars($rule->proses) ?>" required>
                <datalist id="prosesList">
                    <?php foreach($prosesList as $pr): ?>
                        <option value="<?= htmlspecialchars($pr) ?>">
                    <?php endforeach; ?>
                </datalist>
            </div>
            <div class="form-group">
                <label>Rekomendasi Roast:</label>
                <select name="recommended_roast" required>
                    <option value="light" <?= $rule->recommended_roast == 'light' ? 'selected' : '' ?>>Light</option>
                    <option value="medium" <?= $rule->recommended_roast == 'medium' ? 'selected' : '' ?>>Medium</option>
                    <option value="medium-dark" <?= $rule->recommended_roast == 'medium-dark' ? 'selected' : '' ?>>Medium-Dark</option>
                    <option value="dark" <?= $rule->recommended_roast == 'dark' ? 'selected' : '' ?>>Dark</option>
                </select>
            </div>

            <div class="form-group">
                <label>Roasting Notes:</label>
                <input type="text" name="roasting_notes" value="<?= htmlspecialchars($rule->roasting_notes) ?>">
            </div>
            <div class="form-actions">
                <button type="submit" class="btn">Update</button>
            </div>
        </form>
    </div>
</body>
</html>
