<?php
require_once '../config/init.php';

if (!isLoggedIn()) {
    redirect('../login.php');
}

if (isAdmin()) {
    redirect('../admin/dashboard.php');
}

$user_id = $_SESSION['user_id'];

// Get user preferences
$db->query("SELECT * FROM user_preferences WHERE user_id = :user_id");
$db->bind(':user_id', $user_id);
$preferences = $db->single();

// Get coffee recommendations based on rule-based system
$recommended_coffees = [];
$rule_recommendation = null;

// Only show recommendations if form was submitted (POST request) or session flag is set
$show_recommendations = false;

// Check if recommendations should be shown from session
if (isset($_SESSION['show_recommendations']) && $_SESSION['show_recommendations']) {
    $show_recommendations = true;
    $rule_recommendation = $_SESSION['last_recommendation'];

    // Clear the session flag after using it
    unset($_SESSION['show_recommendations']);
    unset($_SESSION['last_recommendation']);
}

if (
    $preferences && $preferences->jenis_kopi && $preferences->metode_penyajian &&
    $preferences->profil_rasa && $preferences->proses
) {

    // Check if this is a POST request (form submission) or session flag is set
    if ($_SERVER['REQUEST_METHOD'] === 'POST' || $show_recommendations) {
        if (!$show_recommendations) {
            $show_recommendations = true;

            // Get rule-based recommendation
            $rule_recommendation = getRecommendedRoast(
                $preferences->jenis_kopi,
                $preferences->metode_penyajian,
                $preferences->proses,
                $preferences->profil_rasa
            );
        }

        // Find coffees that match the recommended roast level and user preferences
        $db->query("SELECT ct.*, 
                    CASE 
                        WHEN ct.roast_level = :recommended_roast AND ct.jenis_kopi = :jenis_kopi THEN 100
                        WHEN ct.roast_level = :recommended_roast THEN 90
                        WHEN ct.jenis_kopi = :jenis_kopi THEN 80
                        WHEN ct.profil_rasa = :profil_rasa THEN 70
                        WHEN ct.proses = :proses THEN 60
                        ELSE 50
                    END as match_score
                   FROM coffee_types ct 
                   WHERE ct.id NOT IN (
                       SELECT COALESCE(r.coffee_id, 0) 
                       FROM recommendations r 
                   WHERE r.user_id = :user_id
                   )
                   ORDER BY match_score DESC, ct.price ASC 
                   LIMIT 6");
        $db->bind(':recommended_roast', $rule_recommendation['roast']);
        $db->bind(':jenis_kopi', $preferences->jenis_kopi);
        $db->bind(':profil_rasa', $preferences->profil_rasa);
        $db->bind(':proses', $preferences->proses);
        $db->bind(':user_id', $user_id);
        $recommended_coffees = $db->resultset();
    }
}

$page_title = 'Rekomendasi Kopi';

// Tambahkan fungsi mapping roast ke roast_level database
function mapRoastLevel($roast)
{
    $roast = strtolower(trim($roast));
    if (strpos($roast, 'light') !== false && strpos($roast, 'medium') !== false) {
        return 'Light to medium';
    } elseif (strpos($roast, 'medium') !== false && strpos($roast, 'high') !== false) {
        return 'Medium-light to medium-high';
    } elseif (strpos($roast, 'dark') !== false) {
        return 'Medium-light to dark';
    } elseif (strpos($roast, 'very-light') !== false) {
        return 'Very-light to medium-light';
    } elseif ($roast === 'light') {
        return 'Light to medium';
    } elseif ($roast === 'medium') {
        return 'Medium-light to medium-high';
    } elseif ($roast === 'high') {
        return 'Medium-light to medium-high';
    } elseif ($roast === 'very high') {
        return 'Medium-light to dark';
    }
    return $roast;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" href="/project/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            margin: 0;
            font-family: 'Inter', Arial, sans-serif;
            background: #faf8f6;
            color: #23190f;
        }

        .user-layout {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 220px;
            background: #2f1b14;
            color: #fff;
            display: flex;
            flex-direction: column;
            padding: 0;
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            z-index: 100;
        }

        .sidebar .sidebar-brand {
            font-size: 1.4rem;
            font-weight: bold;
            padding: 28px 20px 20px 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            border-bottom: 1px solid #4e342e;
        }

        .sidebar .sidebar-nav {
            flex: 1;
            display: flex;
            flex-direction: column;
            padding: 20px 0;
        }

        .sidebar .sidebar-link {
            color: #fff;
            text-decoration: none;
            padding: 12px 28px;
            font-size: 1.08rem;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: background 0.2s;
        }

        .sidebar .sidebar-link.active,
        .sidebar .sidebar-link:hover {
            background: #4e342e;
        }

        .sidebar .sidebar-footer {
            padding: 14px 20px;
            font-size: 0.95rem;
            border-top: 1px solid #4e342e;
            color: #bdbdbd;
        }

        .user-content {
            margin-left: 220px;
            padding: 36px 24px 24px 24px;
            width: 100%;
            min-height: 100vh;
            box-sizing: border-box;
        }

        @media (max-width: 900px) {
            .user-layout {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                flex-direction: row;
                height: auto;
                position: static;
                border-bottom: 1px solid #4e342e;
            }

            .sidebar .sidebar-nav {
                flex-direction: row;
                padding: 0 8px;
            }

            .sidebar .sidebar-link {
                padding: 10px 8px;
                font-size: 1rem;
            }

            .user-content {
                margin-left: 0;
                padding: 18px 4px;
            }
        }
    </style>
</head>

<body>
    <div class="user-layout">
        <?php include '../includes/sidebar.php'; ?>
        <div class="user-content">
            <!-- Success Message -->
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success" style="background: #d4edda; color: #155724; padding: 12px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
                    <i class="fas fa-check-circle"></i> <?php echo $_SESSION['success_message']; ?>
                </div>
                <?php unset($_SESSION['success_message']); ?>
            <?php endif; ?>

            <!-- Preferences Section -->
            <div class="preference-form">
                <h2><i class="fas fa-cog"></i> Rekomendasi Kopi</h2>
                <p>Atur preferensi Anda untuk mendapatkan rekomendasi yang dipersonalisasi menggunakan sistem berbasis aturan ahli kami</p>

                <form id="preference-form" method="POST" action="update_preferences.php">
                    <div class="preference-grid">
                        <div class="form-group">
                            <label for="jenis_kopi">Jenis Kopi</label>
                            <select id="jenis_kopi" name="jenis_kopi" required>
                                <option value="">Pilih jenis kopi</option>
                                <option value="Arabika">Arabika</option>
                                <option value="Robusta">Robusta</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="metode_penyajian">Metode Penyajian</label>
                            <select id="metode_penyajian" name="metode_penyajian" required>
                                <option value="">Pilih metode penyajian</option>
                                <option value="V60">V60</option>
                                <option value="French Press">French Press</option>
                                <option value="Espresso">Espresso</option>
                                <option value="Tubruk">Tubruk</option>
                                <option value="Cold Brew">Cold Brew</option>
                                <option value="Manual Brew">Manual Brew</option>
                                <option value="Moka Pot">Moka Pot</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="proses">Proses</label>
                            <select id="proses" name="proses" required>
                                <option value="">Pilih proses</option>
                                <option value="Full Washed">Full Washed</option>
                                <option value="Washed">Washed</option>
                                <option value="Honey">Honey</option>
                                <option value="Natural">Natural</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="profil_rasa">Profil Rasa</label>
                            <select id="profil_rasa" name="profil_rasa" required>
                                <option value="">Pilih profil rasa</option>
                                <option value="Fruity">Fruity</option>
                                <option value="Chocolate">Chocolate</option>
                                <option value="Nutty">Nutty</option>
                                <option value="Earthy">Earthy</option>
                                <option value="Floral">Floral</option>
                                <option value="Citrus">Citrus</option>
                                <option value="Caramel">Caramel</option>
                                <option value="Bitter Chocolate">Bitter Chocolate</option>
                                <option value="Honey">Honey</option>
                                <option value="Spicy">Spicy</option>
                                <option value="Bitter">Bitter</option>
                                <option value="Sweet">Sweet</option>
                            </select>
                        </div>

                    </div>

                    <div class="form-group">
                        <label for="flavor_preference">Catatan Tambahan</label>
                        <textarea id="flavor_preference" name="flavor_preference" rows="3"
                            placeholder="Tambahkan catatan khusus tentang preferensi rasa Anda"><?php echo $preferences ? htmlspecialchars($preferences->flavor_preference) : ''; ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary btn-full">Perbarui Preferensi & Dapatkan Rekomendasi</button>
                </form>
                <div style="margin-top:10px;">
                    <?php if ($rule_recommendation && $show_recommendations): ?>
                        <div class="dashboard-card rule-recommendation">
                            <h3><i class="fas fa-brain"></i> Rekomendasi Tingkat Kematangan </h3>
                            <span class="stat-number"><?php echo ucfirst(str_replace('-', ' ', $rule_recommendation['roast'])); ?></span>
                            <span class="stat-label">Berdasarkan <?php echo $rule_recommendation['rule']; ?></span>
                        </div>
                    <?php endif; ?>
                </div>

            </div>


            <!-- Rule-based Recommendation Display -->
            <?php if ($rule_recommendation && $show_recommendations): ?>
                <div class="dashboard-card rule-explanation">
                    <h3><i class="fas fa-lightbulb"></i> Penjelasan Rekomendasi Ahli</h3>
                    <div class="rule-details">
                        <p><strong>Berdasarkan preferensi Anda:</strong></p>
                        <ul>
                            <li><strong>Jenis Kopi:</strong> <?php echo $preferences->jenis_kopi; ?></li>
                            <li><strong>Metode Penyajian:</strong> <?php echo $preferences->metode_penyajian; ?></li>
                            <li><strong>Proses:</strong> <?php echo $preferences->proses; ?></li>
                            <li><strong>Profil Rasa:</strong> <?php echo $preferences->profil_rasa; ?></li>
                        </ul>
                        <div class="recommendation-result">
                            <p><strong>Sistem ahli kami merekomendasikan:</strong></p>
                            <div class="roast-recommendation">
                                <span class="roast-level <?php echo $rule_recommendation['roast']; ?>">
                                    <?php echo ucfirst(str_replace('-', ' ', $rule_recommendation['roast'])); ?> Roast
                                </span>
                                <small>Aturan yang diterapkan: <?php echo $rule_recommendation['rule']; ?></small>
                            </div>
                            <button class="btn btn-brown btn-sm btn-full" onclick="showRoastingModal('<?= htmlspecialchars(mapRoastLevel(ucfirst(str_replace('-', ' ', $rule_recommendation['roast'])))) ?>')">
                                <i class="fas fa-fire"></i> Lihat Proses Roasting
                            </button>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <!-- Modal Proses Roasting -->
            <div id="roasting-modal" class="modal" style="display:none;">
                <div class="modal-content">
                    <span class="close" onclick="closeRoastingModal()">&times;</span>
                    <div id="roasting-modal-body"></div>
                </div>
            </div>
            <style>
                .modal {
                    position: fixed;
                    left: 0;
                    top: 0;
                    width: 100vw;
                    height: 100vh;
                    background: rgba(0, 0, 0, 0.3);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    z-index: 9999;
                }

                .modal-content {
                    background: #fff;
                    border-radius: 12px;
                    padding: 32px 36px;
                    max-width: 500px;
                    width: 90%;
                    position: relative;
                    box-shadow: 0 2px 12px rgba(47, 27, 20, 0.12);
                }

                .close {
                    position: absolute;
                    top: 12px;
                    right: 18px;
                    font-size: 1.8rem;
                    color: #a86b3c;
                    cursor: pointer;
                }
            </style>
            <script>
                function showRoastingModal(roast) {
                    var modal = document.getElementById('roasting-modal');
                    var body = document.getElementById('roasting-modal-body');
                    body.innerHTML = '<p>Memuat...</p>';
                    modal.style.display = 'flex';
                    fetch('/project/proses_roasting.php?ajax=1&roast=' + encodeURIComponent(roast))
                        .then(res => res.text())
                        .then(html => {
                            body.innerHTML = html;
                        });
                }

                function closeRoastingModal() {
                    document.getElementById('roasting-modal').style.display = 'none';
                }
            </script>

            <!-- Recommendations Section -->
            <?php if (!empty($recommended_coffees) && $show_recommendations): ?>
                <div class="dashboard-card">
                    <h3><i class="fas fa-magic"></i> Direkomendasikan Untuk Anda</h3>
                    <p>Berdasarkan sistem berbasis aturan ahli kami dan preferensi Anda</p>
                    <div class="coffee-grid">
                        <?php foreach ($recommended_coffees as $coffee): ?>
                            <div class="coffee-card recommendation-card">
                                <div class="coffee-image">
                                    <img src="<?php echo $coffee->image_url; ?>" alt="<?php echo htmlspecialchars($coffee->name); ?>">
                                    <div class="recommendation-score"><?php echo $coffee->match_score; ?>% Cocok</div>
                                    <div class="roast-badge <?php echo $coffee->roast_level; ?>">
                                        <?php echo ucfirst(str_replace('-', ' ', $coffee->roast_level)); ?>
                                    </div>
                                </div>
                                <div class="coffee-info">
                                    <h3><?php echo htmlspecialchars($coffee->name); ?></h3>
                                    <p class="coffee-origin"><?php echo htmlspecialchars($coffee->origin); ?></p>
                                    <p class="coffee-description"><?php echo htmlspecialchars($coffee->description); ?></p>
                                    <div class="coffee-details">
                                        <div class="coffee-attribute">
                                            <strong>Jenis:</strong> <?php echo $coffee->jenis_kopi; ?>
                                        </div>
                                        <div class="coffee-attribute">
                                            <strong>Profil Rasa:</strong> <?php echo $coffee->profil_rasa; ?>
                                        </div>
                                        <div class="coffee-attribute">
                                            <strong>Proses:</strong> <?php echo $coffee->proses; ?>
                                        </div>
                                        <div class="brewing-method">
                                            <strong>Terbaik untuk:</strong>
                                            <span><?php echo htmlspecialchars($coffee->brewing_method); ?></span>
                                        </div>
                                    </div>
                                    <div class="coffee-price">
                                        <span class="price">Rp <?php echo number_format($coffee->price, 0, ',', '.'); ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </div>

</body>

</html>