<?php
require_once '../config/init.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

$coffee_id = (int)($_GET['id'] ?? 0);

if ($coffee_id <= 0) {
    redirect('manage_coffees.php');
}

// Get coffee data
$db->query("SELECT * FROM coffee_types WHERE id = :id");
$db->bind(':id', $coffee_id);
$coffee = $db->single();

if (!$coffee) {
    $_SESSION['error_message'] = 'Kopi tidak ditemukan';
    redirect('manage_coffees.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $roast_level = $_POST['roast_level'];
    $flavor_notes = trim($_POST['flavor_notes']);
    $brewing_method = trim($_POST['brewing_method']);
    $origin = trim($_POST['origin']);
    $price = (float)$_POST['price'];
    $image_url = trim($_POST['image_url']);

    // Hapus input image_url dari form, hanya gunakan upload file
    // Pada proses update, jika tidak upload file baru, gunakan gambar lama

    // Proses upload file gambar jika ada
    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['image_file']['tmp_name'];
        $fileName = $_FILES['image_file']['name'];
        $fileSize = $_FILES['image_file']['size'];
        $fileType = $_FILES['image_file']['type'];
        $fileNameCmps = explode('.', $fileName);
        $fileExtension = strtolower(end($fileNameCmps));
        $allowedfileExtensions = ['jpg', 'jpeg', 'png', 'webp'];
        if (in_array($fileExtension, $allowedfileExtensions)) {
            $newFileName = uniqid('kopi_', true) . '.' . $fileExtension;
            $uploadFileDir = __DIR__ . '/../uploads/';
            $dest_path = $uploadFileDir . $newFileName;
            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                $image_url = 'uploads/' . $newFileName;
            } else {
                $error = 'Gagal mengupload gambar.';
            }
        } else {
            $error = 'Tipe file gambar tidak didukung. Hanya jpg, jpeg, png, webp.';
        }
    } else {
        // Tidak upload file baru, gunakan gambar lama
        $image_url = $coffee->image_url;
    }

    // Validation
    if (empty($name) || empty($description) || empty($roast_level) || empty($flavor_notes) || 
        empty($brewing_method) || empty($origin) || $price <= 0 || empty($image_url)) {
        $error = 'Silakan isi semua field dengan data yang valid';
    } else {
        // Check if coffee name already exists (excluding current coffee)
        $db->query("SELECT id FROM coffee_types WHERE name = :name AND id != :id");
        $db->bind(':name', $name);
        $db->bind(':id', $coffee_id);
        $existing = $db->single();
        
        if ($existing) {
            $error = 'Kopi dengan nama ini sudah ada';
        } else {
            // Update coffee
            $db->query("UPDATE coffee_types 
                       SET name = :name, description = :description, roast_level = :roast_level, 
                           flavor_notes = :flavor_notes, brewing_method = :brewing_method, 
                           origin = :origin, price = :price, image_url = :image_url 
                       WHERE id = :id");
            $db->bind(':name', $name);
            $db->bind(':description', $description);
            $db->bind(':roast_level', $roast_level);
            $db->bind(':flavor_notes', $flavor_notes);
            $db->bind(':brewing_method', $brewing_method);
            $db->bind(':origin', $origin);
            $db->bind(':price', $price);
            $db->bind(':image_url', $image_url);
            $db->bind(':id', $coffee_id);
            
            if ($db->execute()) {
                $_SESSION['success_message'] = 'Kopi berhasil diperbarui!';
                redirect('manage_coffees.php');
            } else {
                $error = 'Error memperbarui kopi. Silakan coba lagi.';
            }
        }
    }
}

$page_title = 'Ubah Kopi';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Ubah Kopi</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            background: #f7f3ef;
            font-family: 'Segoe UI', Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .edit-container {
            max-width: 900px;
            margin: 0 auto;
            background: #fff;
            border-radius: 0;
            box-shadow: none;
            padding: 32px 4vw 32px 4vw;
            min-height: 100vh;
        }
        .edit-title {
            font-size: 2.2rem;
            font-weight: 700;
            color: #6b3e26;
            margin-bottom: 8px;
            letter-spacing: 0.5px;
        }
        .edit-desc {
            color: #7a5c3c;
            margin-bottom: 28px;
            font-size: 1.05rem;
        }
        .form-group {
            margin-bottom: 18px;
        }
        label {
            display: block;
            font-weight: 600;
            color: #4e342e;
            margin-bottom: 6px;
        }
        input[type="text"],
        input[type="number"],
        input[type="url"],
        select,
        textarea {
            width: 100%;
            padding: 10px 14px;
            border: 1.5px solid #e0cfc2;
            border-radius: 6px;
            font-size: 1rem;
            background: #faf8f6;
            color: #3d2b1f;
            transition: border 0.2s;
            box-sizing: border-box;
        }
        input[type="text"]:focus,
        input[type="number"]:focus,
        input[type="url"]:focus,
        select:focus,
        textarea:focus {
            border-color: #a86b3c;
            outline: none;
        }
        textarea {
            min-height: 60px;
            resize: vertical;
        }
        .form-row {
            display: flex;
            gap: 18px;
        }
        .form-row > .form-group {
            flex: 1;
        }
        .current-image {
            margin: 18px 0 10px 0;
        }
        .current-image img {
            max-width: 220px;
            height: 120px;
            object-fit: cover;
            border-radius: 8px;
            border: 2px solid #e0cfc2;
            background: #f3ede7;
        }
        .form-actions {
            display: flex;
            gap: 12px;
            margin-top: 24px;
        }
        .btn {
            padding: 10px 26px;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.2s, color 0.2s;
        }
        .btn-primary {
            background: #a86b3c;
            color: #fff;
        }
        .btn-primary:hover {
            background: #7b4a1e;
        }
        .btn-secondary {
            background: #e0cfc2;
            color: #6b3e26;
        }
        .btn-secondary:hover {
            background: #cbb49a;
        }
        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #e0cfc2;
            color: #6b3e26;
            border: none;
            border-radius: 6px;
            font-size: 1.05rem;
            font-weight: 600;
            padding: 9px 20px;
            margin-bottom: 32px;
            cursor: pointer;
            transition: background 0.2s, color 0.2s;
            text-decoration: none;
        }
        .btn-back:hover {
            background: #cbb49a;
            color: #a86b3c;
        }
        .icon-back {
            font-size: 1.2em;
            line-height: 1;
        }
        .alert-error {
            background: #ffe5e0;
            color: #b23c2e;
            border: 1px solid #ffb3a7;
            padding: 10px 16px;
            border-radius: 6px;
            margin-bottom: 18px;
            font-size: 1rem;
        }
        @media (max-width: 900px) {
            .edit-container {
                max-width: 100vw;
                padding: 18px 2vw 18px 2vw;
            }
            .form-row {
                flex-direction: column;
                gap: 0;
            }
        }
    </style>
</head>
<body>
    <div class="edit-container">
        <div class="edit-title">Edit Coffee</div>
        <div class="edit-desc">Update coffee information</div>
        <button type="button" class="btn-back" onclick="window.location.href='manage_coffees.php'">
            <span class="icon-back">&#8592;</span> Back to Manage Coffees
        </button>
            <form method="POST" class="coffee-form" enctype="multipart/form-data">
                <?php if ($error): ?>
                <div class="alert-error">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

            <div class="form-row">
                    <div class="form-group">
                        <label for="name">Coffee Name *</label>
                        <input type="text" id="name" name="name" required
                               value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : htmlspecialchars($coffee->name); ?>"
                               placeholder="e.g., Ethiopian Yirgacheffe">
                    </div>
                    <div class="form-group">
                        <label for="origin">Origin *</label>
                        <input type="text" id="origin" name="origin" required
                               value="<?php echo isset($_POST['origin']) ? htmlspecialchars($_POST['origin']) : htmlspecialchars($coffee->origin); ?>"
                               placeholder="e.g., Ethiopia, Colombia, Guatemala">
                </div>
                    </div>

            <div class="form-row">
                    <div class="form-group">
                        <label for="roast_level">Roast Level *</label>
                        <select id="roast_level" name="roast_level" required>
                            <option value="">Select roast level</option>
                            <?php 
                            $roast_level = isset($_POST['roast_level']) ? $_POST['roast_level'] : $coffee->roast_level;
                        $roast_options = [
                            'light' => 'Light Roast',
                            'medium' => 'Medium Roast',
                            'medium-dark' => 'Medium-Dark Roast',
                            'dark' => 'Dark Roast'
                        ];
                            foreach ($roast_options as $value => $label): 
                            ?>
                                <option value="<?php echo $value; ?>" <?php echo ($roast_level == $value) ? 'selected' : ''; ?>>
                                    <?php echo $label; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="price">Harga (Rupiah) *</label>
                        <input type="number" id="price" name="price" step="1000" min="0" required
                               value="<?php echo isset($_POST['price']) ? $_POST['price'] : $coffee->price; ?>"
                               placeholder="0.00">
                    </div>
                </div>

                <div class="form-group">
                    <label for="description">Description *</label>
                    <textarea id="description" name="description" rows="3" required
                              placeholder="Brief description of the coffee's characteristics"><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : htmlspecialchars($coffee->description); ?></textarea>
                </div>

                <div class="form-group">
                    <label for="flavor_notes">Flavor Notes *</label>
                    <input type="text" id="flavor_notes" name="flavor_notes" required
                           value="<?php echo isset($_POST['flavor_notes']) ? htmlspecialchars($_POST['flavor_notes']) : htmlspecialchars($coffee->flavor_notes); ?>"
                           placeholder="e.g., Citrus, Chocolate, Nutty">
                </div>

                <div class="form-group">
                    <label for="brewing_method">Best Brewing Methods *</label>
                    <input type="text" id="brewing_method" name="brewing_method" required
                           value="<?php echo isset($_POST['brewing_method']) ? htmlspecialchars($_POST['brewing_method']) : htmlspecialchars($coffee->brewing_method); ?>"
                           placeholder="e.g., Pour-over, French Press, Espresso">
                </div>

                <div class="form-group">
                    <label for="image_file">Upload Gambar Kopi (opsional)</label>
                    <input type="file" id="image_file" name="image_file" accept="image/*">
                    <small>Format: jpg, jpeg, png, webp. Maksimal 2MB.</small>
                </div>
                <div class="current-image">
                    <label>Current Image:</label>
                <img src="<?php echo htmlspecialchars($coffee->image_url); ?>" alt="Current coffee image">
                </div>

                <div class="form-actions">
                <button type="submit" class="btn btn-primary">Update Coffee</button>
                    <a href="manage_coffees.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
</body>
</html>