<?php
require_once '../config/init.php';

if (!isLoggedIn() || !isAdmin()) {
    echo '<p>Unauthorized access</p>';
    exit();
}

$user_id = (int)($_GET['id'] ?? 0);

if ($user_id <= 0) {
    echo '<p>Invalid user ID</p>';
    exit();
}

// Get user details
$db->query("SELECT * FROM users WHERE id = :id AND role = 'user'");
$db->bind(':id', $user_id);
$user = $db->single();

if (!$user) {
    echo '<p>User not found</p>';
    exit();
}

// Get user preferences
$db->query("SELECT * FROM user_preferences WHERE user_id = :user_id");
$db->bind(':user_id', $user_id);
$preferences = $db->single();

// Get user reviews
$db->query("SELECT ct.name as coffee_name, r.rating, r.review, r.created_at 
           FROM recommendations r 
           JOIN coffee_types ct ON r.coffee_id = ct.id 
           WHERE r.user_id = :user_id 
           ORDER BY r.created_at DESC");
$db->bind(':user_id', $user_id);
$reviews = $db->resultset();

// Get statistics
$db->query("SELECT COUNT(*) as total_reviews FROM recommendations WHERE user_id = :user_id");
$db->bind(':user_id', $user_id);
$total_reviews = $db->single()->total_reviews;

$db->query("SELECT AVG(rating) as avg_rating FROM recommendations WHERE user_id = :user_id AND rating > 0");
$db->bind(':user_id', $user_id);
$avg_rating = $db->single()->avg_rating;

$db->query("SELECT COUNT(*) as favorites_count FROM recommendations WHERE user_id = :user_id AND rating >= 4");
$db->bind(':user_id', $user_id);
$favorites_count = $db->single()->favorites_count;
?>

<div class="user-details">
    <div class="user-summary">
        <div class="user-avatar-large">
            <i class="fas fa-user"></i>
        </div>
        <div class="user-info-large">
            <h4><?php echo htmlspecialchars($user->username); ?></h4>
            <p><?php echo htmlspecialchars($user->email); ?></p>
            <small>Member since <?php echo date('F j, Y', strtotime($user->created_at)); ?></small>
        </div>
    </div>

    <div class="user-stats">
        <div class="stat-item">
            <span class="stat-number"><?php echo $total_reviews; ?></span>
            <span class="stat-label">Reviews</span>
        </div>
        <div class="stat-item">
            <span class="stat-number"><?php echo $avg_rating ? number_format($avg_rating, 1) : 'N/A'; ?></span>
            <span class="stat-label">Avg Rating</span>
        </div>
        <div class="stat-item">
            <span class="stat-number"><?php echo $favorites_count; ?></span>
            <span class="stat-label">Favorites</span>
        </div>
    </div>

    <?php if ($preferences): ?>
    <div class="user-preferences">
        <h5>Coffee Preferences</h5>
        <div class="preference-item">
            <strong>Preferred Roast:</strong> 
            <?php echo ucfirst(str_replace('-', ' ', $preferences->preferred_roast)); ?>
        </div>
        <?php if ($preferences->preferred_brewing): ?>
        <div class="preference-item">
            <strong>Preferred Brewing:</strong> 
            <?php echo htmlspecialchars($preferences->preferred_brewing); ?>
        </div>
        <?php endif; ?>
        <?php if ($preferences->flavor_preference): ?>
        <div class="preference-item">
            <strong>Flavor Preferences:</strong> 
            <?php echo htmlspecialchars($preferences->flavor_preference); ?>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <?php if (!empty($reviews)): ?>
    <div class="user-reviews">
        <h5>Recent Reviews</h5>
        <div class="reviews-list">
            <?php foreach (array_slice($reviews, 0, 5) as $review): ?>
                <div class="review-item">
                    <div class="review-header">
                        <strong><?php echo htmlspecialchars($review->coffee_name); ?></strong>
                        <div class="review-meta">
                            <div class="rating-stars">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="fas fa-star <?php echo $i <= $review->rating ? 'active' : ''; ?>"></i>
                                <?php endfor; ?>
                            </div>
                            <small><?php echo date('M j, Y', strtotime($review->created_at)); ?></small>
                        </div>
                    </div>
                    <?php if ($review->review): ?>
                        <p class="review-text"><?php echo htmlspecialchars($review->review); ?></p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<style>
.user-summary {
    display: flex;
    align-items: center;
    gap: var(--spacing-md);
    margin-bottom: var(--spacing-lg);
    padding-bottom: var(--spacing-md);
    border-bottom: 1px solid var(--light-brown);
}

.user-avatar-large {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, var(--primary-brown), var(--accent-orange));
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--white);
    font-size: var(--font-size-2xl);
}

.user-info-large h4 {
    margin-bottom: var(--spacing-xs);
    color: var(--coffee-black);
}

.user-info-large p {
    margin-bottom: var(--spacing-xs);
    color: var(--gray);
}

.user-stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: var(--spacing-md);
    margin-bottom: var(--spacing-lg);
    text-align: center;
}

.stat-item {
    padding: var(--spacing-md);
    background-color: var(--light-gray);
    border-radius: var(--radius-md);
}

.stat-number {
    display: block;
    font-size: var(--font-size-xl);
    font-weight: 700;
    color: var(--primary-brown);
    margin-bottom: var(--spacing-xs);
}

.stat-label {
    color: var(--gray);
    font-size: var(--font-size-sm);
}

.user-preferences {
    margin-bottom: var(--spacing-lg);
}

.user-preferences h5 {
    margin-bottom: var(--spacing-md);
    color: var(--coffee-black);
}

.preference-item {
    margin-bottom: var(--spacing-sm);
    padding: var(--spacing-sm);
    background-color: var(--light-gray);
    border-radius: var(--radius-sm);
}

.user-reviews h5 {
    margin-bottom: var(--spacing-md);
    color: var(--coffee-black);
}

.reviews-list {
    max-height: 300px;
    overflow-y: auto;
}

.review-item {
    padding: var(--spacing-sm);
    background-color: var(--light-gray);
    border-radius: var(--radius-sm);
    margin-bottom: var(--spacing-sm);
    border-left: 3px solid var(--accent-orange);
}

.review-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: var(--spacing-xs);
    flex-wrap: wrap;
    gap: var(--spacing-sm);
}

.review-meta {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: var(--spacing-xs);
}

.review-text {
    font-style: italic;
    color: var(--coffee-black);
    margin: 0;
    font-size: var(--font-size-sm);
}

@media (max-width: 480px) {
    .user-summary {
        flex-direction: column;
        text-align: center;
    }
    
    .user-stats {
        grid-template-columns: 1fr;
    }
    
    .review-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .review-meta {
        align-items: flex-start;
    }
}
</style>