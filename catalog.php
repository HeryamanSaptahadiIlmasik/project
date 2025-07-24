<?php
require_once 'config/init.php';

// Fungsi format rupiah hanya dideklarasikan satu kali di sini
if (!function_exists('formatRupiah')) {
    function formatRupiah($angka) {
        return 'Rp' . number_format($angka, 2, ',', '.');
    }
}

// Get all coffee types with new attributes
$db->query("SELECT * FROM coffee_types ORDER BY name");
$coffees = $db->resultset();

$page_title = 'Katalog Kopi';
include 'includes/header.php';
?>

<main>
    <section class="catalog-hero">
        <div class="container">
            <h1>Koleksi Kopi Kami</h1>
            <p>Temukan biji kopi premium dengan rekomendasi berbasis aturan yang ahli</p>
        </div>
    </section>

    <section class="catalog">
        <div class="container">
            <div class="catalog-filters">
                <div class="filter-group">
                    <label for="jenis-filter">Jenis Kopi</label>
                    <select id="jenis-filter">
                        <option value="">Semua Jenis</option>
                        <option value="Arabika">Arabika</option>
                        <option value="Robusta">Robusta</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="roast-filter">Tingkat Roasting</label>
                    <select id="roast-filter">
                        <option value="">Semua Roasting</option>
                        <option value="light">Light Roast</option>
                        <option value="medium">Medium Roast</option>
                        <option value="medium-dark">Medium-Dark Roast</option>
                        <option value="dark">Dark Roast</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="profil-filter">Profil Rasa</label>
                    <select id="profil-filter">
                        <option value="">Semua Rasa</option>
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
                <div class="filter-group">
                    <label for="proses-filter">Proses</label>
                    <select id="proses-filter">
                        <option value="">Semua Proses</option>
                        <option value="Washed">Washed</option>
                        <option value="Honey">Honey</option>
                        <option value="Natural">Natural</option>
                    </select>
                </div>
            </div>

            <div class="coffee-grid">
                <?php foreach ($coffees as $coffee): ?>
                    <div class="coffee-card" 
                         data-jenis="<?php echo $coffee->jenis_kopi; ?>"
                         data-roast="<?php echo $coffee->roast_level; ?>"
                         data-profil="<?php echo $coffee->profil_rasa; ?>"
                         data-proses="<?php echo $coffee->proses; ?>">
                        <div class="coffee-image">
                            <img src="<?php echo $coffee->image_url; ?>" alt="<?php echo htmlspecialchars($coffee->name); ?>">
                            <div class="roast-badge <?php echo $coffee->roast_level; ?>">
                                <?php echo ucfirst(str_replace('-', ' ', $coffee->roast_level)); ?>
                            </div>
                            <div class="jenis-badge">
                                <?php echo $coffee->jenis_kopi; ?>
                            </div>
                        </div>
                        <div class="coffee-info">
                            <h3><?php echo htmlspecialchars($coffee->name); ?></h3>
                            <p class="coffee-origin"><?php echo htmlspecialchars($coffee->origin); ?></p>
                            <p class="coffee-description"><?php echo htmlspecialchars($coffee->description); ?></p>
                            
                            <div class="coffee-attributes">
                                <div class="attribute-item">
                                    <strong>Jenis:</strong> <?php echo $coffee->jenis_kopi; ?>
                                </div>
                                <div class="attribute-item">
                                    <strong>Profil Rasa:</strong> <?php echo $coffee->profil_rasa; ?>
                                </div>
                                <div class="attribute-item">
                                    <strong>Proses:</strong> <?php echo $coffee->proses; ?>
                                </div>
                            </div>
                            
                            <div class="coffee-details">
                                <div class="flavor-notes">
                                    <strong>Catatan Rasa:</strong>
                                    <span><?php echo htmlspecialchars($coffee->flavor_notes); ?></span>
                                </div>
                                <div class="brewing-method">
                                    <strong>Terbaik untuk:</strong>
                                    <span><?php echo htmlspecialchars($coffee->brewing_method); ?></span>
                                </div>
                            </div>
                            
                            <div class="coffee-price">
                                <span class="price">
                                    <?php
                                    echo formatRupiah($coffee->price);
                                    ?>
                                </span>
                                <?php if (isLoggedIn() && !isAdmin()): ?>
                                    <button class="btn btn-small btn-primary" onclick="addToFavorites(<?php echo $coffee->id; ?>)">
                                        <i class="fas fa-heart"></i> Tambah ke Favorit
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
</main>

<style>
.catalog-filters {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: var(--spacing-md);
    margin-bottom: var(--spacing-xl);
    padding: var(--spacing-lg);
    background-color: var(--white);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-sm);
}

.jenis-badge {
    position: absolute;
    top: var(--spacing-sm);
    left: var(--spacing-sm);
    padding: var(--spacing-xs) var(--spacing-sm);
    background-color: rgba(139, 69, 19, 0.9);
    color: var(--white);
    border-radius: var(--radius-sm);
    font-size: var(--font-size-sm);
    font-weight: 600;
}

.coffee-attributes {
    margin: var(--spacing-md) 0;
    padding: var(--spacing-sm);
    background-color: var(--light-gray);
    border-radius: var(--radius-sm);
}

.attribute-item {
    margin-bottom: var(--spacing-xs);
    font-size: var(--font-size-sm);
}

.attribute-item:last-child {
    margin-bottom: 0;
}

@media (max-width: 768px) {
    .catalog-filters {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
// Enhanced filtering functionality
document.addEventListener('DOMContentLoaded', function() {
    const filters = {
        jenis: document.getElementById('jenis-filter'),
        roast: document.getElementById('roast-filter'),
        profil: document.getElementById('profil-filter'),
        proses: document.getElementById('proses-filter')
    };
    
    Object.values(filters).forEach(filter => {
        if (filter) {
            filter.addEventListener('change', applyFilters);
        }
    });
    
    function applyFilters() {
        const filterValues = {
            jenis: filters.jenis.value,
            roast: filters.roast.value,
            profil: filters.profil.value,
            proses: filters.proses.value
        };
        
        const coffeeCards = document.querySelectorAll('.coffee-card');
        
        coffeeCards.forEach(card => {
            const cardData = {
                jenis: card.getAttribute('data-jenis'),
                roast: card.getAttribute('data-roast'),
                profil: card.getAttribute('data-profil'),
                proses: card.getAttribute('data-proses')
            };
            
            let shouldShow = true;
            
            Object.keys(filterValues).forEach(key => {
                if (filterValues[key] && cardData[key] !== filterValues[key]) {
                    shouldShow = false;
                }
            });
            
            if (shouldShow) {
                card.style.display = 'block';
                card.classList.add('fade-in-up');
            } else {
                card.style.display = 'none';
            }
        });
    }
});
</script>

<?php include 'includes/footer.php'; ?>