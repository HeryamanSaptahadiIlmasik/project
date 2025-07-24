// Triak Coffee & Roaster JavaScript Functions

document.addEventListener('DOMContentLoaded', function() {
    initializeNavigation();
    initializeCoffeeFilters();
    initializeRatingStars();
    initializePreferenceForms();
    initializeFavorites();
});

// Navigation functionality
function initializeNavigation() {
    const navToggle = document.querySelector('.nav-toggle');
    const navMenu = document.querySelector('.nav-menu');
    
    if (navToggle && navMenu) {
        navToggle.addEventListener('click', function() {
            navMenu.style.display = navMenu.style.display === 'flex' ? 'none' : 'flex';
        });
    }
}

// Coffee catalog filtering
function initializeCoffeeFilters() {
    const roastFilter = document.getElementById('roast-filter');
    
    if (roastFilter) {
        roastFilter.addEventListener('change', function() {
            filterCoffeeCards(this.value);
        });
    }
}

function filterCoffeeCards(roastLevel) {
    const coffeeCards = document.querySelectorAll('.coffee-card');
    
    coffeeCards.forEach(card => {
        const cardRoast = card.getAttribute('data-roast');
        
        if (roastLevel === '' || cardRoast === roastLevel) {
            card.style.display = 'block';
            card.classList.add('fade-in-up');
        } else {
            card.style.display = 'none';
        }
    });
}

// Rating stars functionality
function initializeRatingStars() {
    const ratingContainers = document.querySelectorAll('.rating-stars');
    
    ratingContainers.forEach(container => {
        const stars = container.querySelectorAll('.star');
        const input = container.nextElementSibling;
        
        stars.forEach((star, index) => {
            star.addEventListener('click', function() {
                const rating = index + 1;
                updateStarRating(stars, rating);
                if (input && input.type === 'hidden') {
                    input.value = rating;
                }
            });
            
            star.addEventListener('mouseover', function() {
                highlightStars(stars, index + 1);
            });
        });
        
        container.addEventListener('mouseleave', function() {
            const currentRating = input ? parseInt(input.value) || 0 : 0;
            updateStarRating(stars, currentRating);
        });
    });
}

function updateStarRating(stars, rating) {
    stars.forEach((star, index) => {
        if (index < rating) {
            star.classList.add('active');
        } else {
            star.classList.remove('active');
        }
    });
}

function highlightStars(stars, rating) {
    stars.forEach((star, index) => {
        if (index < rating) {
            star.style.color = '#DAA520';
        } else {
            star.style.color = '#D2B48C';
        }
    });
}

// Preference form handling
function initializePreferenceForms() {
    const preferenceForm = document.getElementById('preference-form');
    
    if (preferenceForm) {
        preferenceForm.addEventListener('submit', function(e) {
            e.preventDefault();
            submitPreferences();
        });
    }
}

function submitPreferences() {
    const formData = new FormData(document.getElementById('preference-form'));
    
    fetch('user/update_preferences.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Preferences updated successfully!', 'success');
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            showAlert('Error updating preferences. Please try again.', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Network error. Please try again.', 'error');
    });
}

// Favorites functionality
function initializeFavorites() {
    // This will be expanded when favorites functionality is implemented
}

function addToFavorites(coffeeId) {
    fetch('user/add_favorite.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ coffee_id: coffeeId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Added to favorites!', 'success');
        } else {
            showAlert(data.message || 'Error adding to favorites', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Network error. Please try again.', 'error');
    });
}

function removeFromFavorites(coffeeId) {
    fetch('user/remove_favorite.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ coffee_id: coffeeId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Removed from favorites!', 'success');
            // Remove the card from the favorites display
            const favoriteCard = document.querySelector(`[data-coffee-id="${coffeeId}"]`);
            if (favoriteCard) {
                favoriteCard.remove();
            }
        } else {
            showAlert(data.message || 'Error removing from favorites', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Network error. Please try again.', 'error');
    });
}

// Admin functions
function deleteCoffee(coffeeId) {
    if (confirm('Are you sure you want to delete this coffee?')) {
        fetch('admin/delete_coffee.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ coffee_id: coffeeId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('Coffee deleted successfully!', 'success');
                location.reload();
            } else {
                showAlert(data.message || 'Error deleting coffee', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Network error. Please try again.', 'error');
        });
    }
}

function deleteUser(userId) {
    if (confirm('Are you sure you want to delete this user?')) {
        fetch('admin/delete_user.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ user_id: userId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('User deleted successfully!', 'success');
                location.reload();
            } else {
                showAlert(data.message || 'Error deleting user', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Network error. Please try again.', 'error');
        });
    }
}

// Utility functions
function showAlert(message, type = 'info') {
    // Remove existing alerts
    const existingAlerts = document.querySelectorAll('.alert');
    existingAlerts.forEach(alert => alert.remove());
    
    const alert = document.createElement('div');
    alert.className = `alert alert-${type}`;
    alert.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
        ${message}
    `;
    
    // Insert at the top of the main content
    const main = document.querySelector('main');
    if (main) {
        main.insertBefore(alert, main.firstChild);
    } else {
        document.body.insertBefore(alert, document.body.firstChild);
    }
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        alert.remove();
    }, 5000);
}

function formatPrice(price) {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(price);
}

// Form validation helpers
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return false;
    
    const requiredFields = form.querySelectorAll('[required]');
    let isValid = true;
    
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            field.style.borderColor = '#DC3545';
            isValid = false;
        } else {
            field.style.borderColor = '#D2B48C';
        }
        
        if (field.type === 'email' && field.value && !validateEmail(field.value)) {
            field.style.borderColor = '#DC3545';
            isValid = false;
        }
    });
    
    return isValid;
}

// Initialize tooltips and other UI enhancements
function initializeTooltips() {
    const tooltipElements = document.querySelectorAll('[data-tooltip]');
    
    tooltipElements.forEach(element => {
        element.addEventListener('mouseenter', function() {
            const tooltip = document.createElement('div');
            tooltip.className = 'tooltip';
            tooltip.textContent = this.getAttribute('data-tooltip');
            tooltip.style.position = 'absolute';
            tooltip.style.background = 'rgba(0, 0, 0, 0.8)';
            tooltip.style.color = 'white';
            tooltip.style.padding = '8px 12px';
            tooltip.style.borderRadius = '4px';
            tooltip.style.fontSize = '14px';
            tooltip.style.zIndex = '1000';
            tooltip.style.whiteSpace = 'nowrap';
            
            document.body.appendChild(tooltip);
            
            const rect = this.getBoundingClientRect();
            tooltip.style.left = rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2) + 'px';
            tooltip.style.top = rect.top - tooltip.offsetHeight - 8 + 'px';
            
            this.tooltipElement = tooltip;
        });
        
        element.addEventListener('mouseleave', function() {
            if (this.tooltipElement) {
                this.tooltipElement.remove();
                this.tooltipElement = null;
            }
        });
    });
}

// Call tooltip initialization
document.addEventListener('DOMContentLoaded', initializeTooltips);

// Smooth scrolling for anchor links
document.addEventListener('DOMContentLoaded', function() {
    const links = document.querySelectorAll('a[href^="#"]');
    
    links.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            const targetId = this.getAttribute('href').substring(1);
            const targetElement = document.getElementById(targetId);
            
            if (targetElement) {
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
});