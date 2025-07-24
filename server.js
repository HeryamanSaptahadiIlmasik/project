const express = require('express');
const path = require('path');
const fs = require('fs');

const app = express();
const PORT = 3000;

// Serve static files
app.use('/css', express.static('css'));
app.use('/js', express.static('js'));

// Mock data for demonstration
const mockData = {
    coffees: [
        {
            id: 1,
            name: 'Ethiopian Yirgacheffe',
            description: 'Bright and floral with citrus notes',
            roast_level: 'light',
            flavor_notes: 'Citrus, Floral, Tea-like',
            brewing_method: 'Pour-over, Aeropress',
            origin: 'Ethiopia',
            price: 18.50,
            image_url: 'https://images.pexels.com/photos/894695/pexels-photo-894695.jpeg'
        },
        {
            id: 2,
            name: 'Colombian Supremo',
            description: 'Well-balanced with chocolate and caramel notes',
            roast_level: 'medium',
            flavor_notes: 'Chocolate, Caramel, Nuts',
            brewing_method: 'Drip, French Press',
            origin: 'Colombia',
            price: 16.00,
            image_url: 'https://images.pexels.com/photos/1695052/pexels-photo-1695052.jpeg'
        },
        {
            id: 3,
            name: 'Guatemalan Antigua',
            description: 'Full-bodied with smoky and spicy notes',
            roast_level: 'medium-dark',
            flavor_notes: 'Smoky, Spicy, Dark Chocolate',
            brewing_method: 'Espresso, French Press',
            origin: 'Guatemala',
            price: 17.25,
            image_url: 'https://images.pexels.com/photos/1695052/pexels-photo-1695052.jpeg'
        },
        {
            id: 4,
            name: 'Italian Roast',
            description: 'Bold and intense with rich, smoky flavor',
            roast_level: 'dark',
            flavor_notes: 'Smoky, Bold, Intense',
            brewing_method: 'Espresso, Moka Pot',
            origin: 'Blend',
            price: 15.75,
            image_url: 'https://images.pexels.com/photos/894695/pexels-photo-894695.jpeg'
        }
    ],
    isLoggedIn: false,
    isAdmin: false,
    username: null
};

// Helper function to render PHP-like templates
function renderTemplate(filePath, data = {}) {
    try {
        let content = fs.readFileSync(filePath, 'utf8');
        
        // Simple PHP-like template rendering
        content = content.replace(/\<\?php.*?\?\>/gs, '');
        content = content.replace(/echo\s+htmlspecialchars\(\$([^)]+)\);?/g, (match, varName) => {
            return data[varName] || '';
        });
        content = content.replace(/echo\s+\$([^;]+);?/g, (match, varName) => {
            return data[varName] || '';
        });
        
        return content;
    } catch (error) {
        return `<h1>Error loading template: ${filePath}</h1>`;
    }
}

// Routes
app.get('/', (req, res) => {
    const indexContent = `
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Triak Coffee & Roaster</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-brand">
                <a href="/">
                    <i class="fas fa-coffee"></i>
                    <span>Triak Coffee</span>
                </a>
            </div>
            <div class="nav-menu">
                <a href="/" class="nav-link">Home</a>
                <a href="/catalog" class="nav-link">Coffee Catalog</a>
                <a href="/login" class="nav-link">Login</a>
                <a href="/register" class="nav-link register">Register</a>
            </div>
            <div class="nav-toggle">
                <i class="fas fa-bars"></i>
            </div>
        </div>
    </nav>

    <main>
        <!-- Hero Section -->
        <section class="hero">
            <div class="hero-content">
                <h1>Discover Your Perfect Coffee Roast</h1>
                <p>Expert recommendations tailored to your taste preferences at Triak Coffee & Roaster</p>
                <div class="hero-buttons">
                    <a href="/register" class="btn btn-primary">Get Started</a>
                    <a href="/catalog" class="btn btn-secondary">Browse Coffee</a>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="features">
            <div class="container">
                <h2>Why Choose Triak Coffee?</h2>
                <div class="features-grid">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-seedling"></i>
                        </div>
                        <h3>Premium Quality</h3>
                        <p>Hand-selected beans from the finest coffee regions around the world</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-fire"></i>
                        </div>
                        <h3>Expert Roasting</h3>
                        <p>Carefully crafted roast profiles to bring out the best in every bean</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h3>Personalized Recommendations</h3>
                        <p>AI-powered suggestions based on your taste preferences and brewing style</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-shipping-fast"></i>
                        </div>
                        <h3>Fresh Delivery</h3>
                        <p>Roasted to order and delivered fresh to your doorstep</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Roast Levels Section -->
        <section class="roast-levels">
            <div class="container">
                <h2>Understanding Roast Levels</h2>
                <div class="roast-grid">
                    <div class="roast-card">
                        <div class="roast-color light"></div>
                        <h3>Light Roast</h3>
                        <p>Bright, acidic, and floral. Perfect for pour-over and cold brew.</p>
                    </div>
                    <div class="roast-card">
                        <div class="roast-color medium"></div>
                        <h3>Medium Roast</h3>
                        <p>Balanced flavor with moderate acidity. Great for drip coffee.</p>
                    </div>
                    <div class="roast-card">
                        <div class="roast-color medium-dark"></div>
                        <h3>Medium-Dark Roast</h3>
                        <p>Rich and full-bodied with subtle oil sheen. Ideal for espresso.</p>
                    </div>
                    <div class="roast-card">
                        <div class="roast-color dark"></div>
                        <h3>Dark Roast</h3>
                        <p>Bold and smoky with prominent roast flavors. Perfect for French press.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="cta">
            <div class="container">
                <div class="cta-content">
                    <h2>Ready to Find Your Perfect Cup?</h2>
                    <p>Join thousands of coffee lovers who trust Triak Coffee for their daily brew</p>
                    <a href="/register" class="btn btn-primary">Start Your Coffee Journey</a>
                </div>
            </div>
        </section>
    </main>

    <footer class="footer">
        <div class="footer-container">
            <div class="footer-content">
                <div class="footer-brand">
                    <h3><i class="fas fa-coffee"></i> Triak Coffee & Roaster</h3>
                    <p>Crafting the perfect cup, one roast at a time.</p>
                </div>
                <div class="footer-links">
                    <div class="footer-section">
                        <h4>Coffee</h4>
                        <a href="/catalog">Coffee Catalog</a>
                        <a href="#">Roast Levels</a>
                        <a href="#">Brewing Guide</a>
                    </div>
                    <div class="footer-section">
                        <h4>Account</h4>
                        <a href="/login">Login</a>
                        <a href="/register">Register</a>
                    </div>
                    <div class="footer-section">
                        <h4>Contact</h4>
                        <p><i class="fas fa-envelope"></i> info@triakcoffee.com</p>
                        <p><i class="fas fa-phone"></i> (555) 123-4567</p>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 Triak Coffee & Roaster. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="js/script.js"></script>
</body>
</html>`;
    
    res.send(indexContent);
});

app.get('/catalog', (req, res) => {
    const catalogContent = `
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coffee Catalog - Triak Coffee & Roaster</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-brand">
                <a href="/">
                    <i class="fas fa-coffee"></i>
                    <span>Triak Coffee</span>
                </a>
            </div>
            <div class="nav-menu">
                <a href="/" class="nav-link">Home</a>
                <a href="/catalog" class="nav-link">Coffee Catalog</a>
                <a href="/login" class="nav-link">Login</a>
                <a href="/register" class="nav-link register">Register</a>
            </div>
            <div class="nav-toggle">
                <i class="fas fa-bars"></i>
            </div>
        </div>
    </nav>

    <main>
        <section class="catalog-hero">
            <div class="container">
                <h1>Our Coffee Collection</h1>
                <p>Discover premium coffee beans from around the world</p>
            </div>
        </section>

        <section class="catalog">
            <div class="container">
                <div class="catalog-filters">
                    <div class="filter-group">
                        <label for="roast-filter">Filter by Roast Level:</label>
                        <select id="roast-filter">
                            <option value="">All Roasts</option>
                            <option value="light">Light Roast</option>
                            <option value="medium">Medium Roast</option>
                            <option value="medium-dark">Medium-Dark Roast</option>
                            <option value="dark">Dark Roast</option>
                        </select>
                    </div>
                </div>

                <div class="coffee-grid">
                    ${mockData.coffees.map(coffee => `
                        <div class="coffee-card" data-roast="${coffee.roast_level}">
                            <div class="coffee-image">
                                <img src="${coffee.image_url}" alt="${coffee.name}">
                                <div class="roast-badge ${coffee.roast_level}">
                                    ${coffee.roast_level.charAt(0).toUpperCase() + coffee.roast_level.slice(1).replace('-', ' ')}
                                </div>
                            </div>
                            <div class="coffee-info">
                                <h3>${coffee.name}</h3>
                                <p class="coffee-origin">${coffee.origin}</p>
                                <p class="coffee-description">${coffee.description}</p>
                                <div class="coffee-details">
                                    <div class="flavor-notes">
                                        <strong>Flavor Notes:</strong>
                                        <span>${coffee.flavor_notes}</span>
                                    </div>
                                    <div class="brewing-method">
                                        <strong>Best for:</strong>
                                        <span>${coffee.brewing_method}</span>
                                    </div>
                                </div>
                                <div class="coffee-price">
                                    <span class="price">$${coffee.price.toFixed(2)}</span>
                                </div>
                            </div>
                        </div>
                    `).join('')}
                </div>
            </div>
        </section>
    </main>

    <footer class="footer">
        <div class="footer-container">
            <div class="footer-content">
                <div class="footer-brand">
                    <h3><i class="fas fa-coffee"></i> Triak Coffee & Roaster</h3>
                    <p>Crafting the perfect cup, one roast at a time.</p>
                </div>
                <div class="footer-links">
                    <div class="footer-section">
                        <h4>Coffee</h4>
                        <a href="/catalog">Coffee Catalog</a>
                        <a href="#">Roast Levels</a>
                        <a href="#">Brewing Guide</a>
                    </div>
                    <div class="footer-section">
                        <h4>Account</h4>
                        <a href="/login">Login</a>
                        <a href="/register">Register</a>
                    </div>
                    <div class="footer-section">
                        <h4>Contact</h4>
                        <p><i class="fas fa-envelope"></i> info@triakcoffee.com</p>
                        <p><i class="fas fa-phone"></i> (555) 123-4567</p>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 Triak Coffee & Roaster. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="js/script.js"></script>
</body>
</html>`;
    
    res.send(catalogContent);
});

app.get('/login', (req, res) => {
    const loginContent = `
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Triak Coffee & Roaster</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-brand">
                <a href="/">
                    <i class="fas fa-coffee"></i>
                    <span>Triak Coffee</span>
                </a>
            </div>
            <div class="nav-menu">
                <a href="/" class="nav-link">Home</a>
                <a href="/catalog" class="nav-link">Coffee Catalog</a>
                <a href="/login" class="nav-link">Login</a>
                <a href="/register" class="nav-link register">Register</a>
            </div>
            <div class="nav-toggle">
                <i class="fas fa-bars"></i>
            </div>
        </div>
    </nav>

    <main class="auth-page">
        <div class="auth-container">
            <div class="auth-card">
                <div class="auth-header">
                    <h2><i class="fas fa-sign-in-alt"></i> Login</h2>
                    <p>Welcome back to Triak Coffee</p>
                </div>
                
                <form method="POST" class="auth-form">
                    <div class="form-group">
                        <label for="username">Username or Email</label>
                        <input type="text" id="username" name="username" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-full">Login</button>
                </form>

                <div class="auth-footer">
                    <p>Don't have an account? <a href="/register">Register here</a></p>
                    <div class="demo-accounts">
                        <small>Demo accounts:</small><br>
                        <small><strong>Admin:</strong> admin / admin123</small><br>
                        <small><strong>User:</strong> Create a new account</small>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="footer">
        <div class="footer-container">
            <div class="footer-content">
                <div class="footer-brand">
                    <h3><i class="fas fa-coffee"></i> Triak Coffee & Roaster</h3>
                    <p>Crafting the perfect cup, one roast at a time.</p>
                </div>
                <div class="footer-links">
                    <div class="footer-section">
                        <h4>Coffee</h4>
                        <a href="/catalog">Coffee Catalog</a>
                        <a href="#">Roast Levels</a>
                        <a href="#">Brewing Guide</a>
                    </div>
                    <div class="footer-section">
                        <h4>Account</h4>
                        <a href="/login">Login</a>
                        <a href="/register">Register</a>
                    </div>
                    <div class="footer-section">
                        <h4>Contact</h4>
                        <p><i class="fas fa-envelope"></i> info@triakcoffee.com</p>
                        <p><i class="fas fa-phone"></i> (555) 123-4567</p>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 Triak Coffee & Roaster. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="js/script.js"></script>
</body>
</html>`;
    
    res.send(loginContent);
});

app.get('/register', (req, res) => {
    const registerContent = `
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Triak Coffee & Roaster</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-brand">
                <a href="/">
                    <i class="fas fa-coffee"></i>
                    <span>Triak Coffee</span>
                </a>
            </div>
            <div class="nav-menu">
                <a href="/" class="nav-link">Home</a>
                <a href="/catalog" class="nav-link">Coffee Catalog</a>
                <a href="/login" class="nav-link">Login</a>
                <a href="/register" class="nav-link register">Register</a>
            </div>
            <div class="nav-toggle">
                <i class="fas fa-bars"></i>
            </div>
        </div>
    </nav>

    <main class="auth-page">
        <div class="auth-container">
            <div class="auth-card">
                <div class="auth-header">
                    <h2><i class="fas fa-user-plus"></i> Register</h2>
                    <p>Join the Triak Coffee community</p>
                </div>
                
                <form method="POST" class="auth-form">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required>
                        <small>Minimum 6 characters</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">Confirm Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-full">Register</button>
                </form>

                <div class="auth-footer">
                    <p>Already have an account? <a href="/login">Login here</a></p>
                </div>
            </div>
        </div>
    </main>

    <footer class="footer">
        <div class="footer-container">
            <div class="footer-content">
                <div class="footer-brand">
                    <h3><i class="fas fa-coffee"></i> Triak Coffee & Roaster</h3>
                    <p>Crafting the perfect cup, one roast at a time.</p>
                </div>
                <div class="footer-links">
                    <div class="footer-section">
                        <h4>Coffee</h4>
                        <a href="/catalog">Coffee Catalog</a>
                        <a href="#">Roast Levels</a>
                        <a href="#">Brewing Guide</a>
                    </div>
                    <div class="footer-section">
                        <h4>Account</h4>
                        <a href="/login">Login</a>
                        <a href="/register">Register</a>
                    </div>
                    <div class="footer-section">
                        <h4>Contact</h4>
                        <p><i class="fas fa-envelope"></i> info@triakcoffee.com</p>
                        <p><i class="fas fa-phone"></i> (555) 123-4567</p>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 Triak Coffee & Roaster. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="js/script.js"></script>
</body>
</html>`;
    
    res.send(registerContent);
});

// Start server
app.listen(PORT, () => {
    console.log(`Triak Coffee & Roaster preview server running on http://localhost:${PORT}`);
    console.log('Note: This is a static preview of your PHP application.');
    console.log('To run the full PHP application, you need a PHP server environment.');
});