# Triak Coffee & Roaster

A comprehensive coffee recommendation system built with PHP for Triak Coffee & Roaster.

## Features

### For Users:
- Browse coffee catalog with detailed information
- Set personal coffee preferences
- Get personalized coffee recommendations
- Rate and review coffees
- Manage favorite coffees
- User dashboard with statistics

### For Admins:
- Complete admin dashboard
- Manage coffee inventory (add, edit, delete)
- Manage users
- View analytics and statistics
- Monitor reviews and ratings

## Technology Stack

- **Backend**: PHP with MySQL
- **Frontend**: HTML, CSS, JavaScript
- **Database**: MySQL with PDO
- **Authentication**: Session-based with password hashing
- **Design**: Responsive design with coffee-themed styling

## Database Structure

### Tables:
1. **users** - User accounts (admin/user roles)
2. **coffee_types** - Coffee varieties and details
3. **user_preferences** - User taste preferences
4. **recommendations** - User ratings and reviews

## Installation (For PHP Environment)

1. Set up a PHP server (XAMPP, WAMP, or similar)
2. Create a MySQL database named `triak_coffee`
3. Place all files in your web server directory
4. Access `index.php` in your browser
5. The system will automatically create tables and sample data

## Default Admin Account
- Username: `admin`
- Password: `admin123`

## Current Preview

This preview shows the frontend design and layout. To run the full PHP application with database functionality, you need:

1. PHP 7.4 or higher
2. MySQL 5.7 or higher
3. Web server (Apache/Nginx)

## File Structure

```
/
├── config/
│   ├── database.php      # Database connection
│   └── init.php          # Initialization and setup
├── admin/
│   ├── dashboard.php     # Admin dashboard
│   ├── manage_coffees.php # Coffee management
│   ├── add_coffee.php    # Add new coffee
│   ├── edit_coffee.php   # Edit coffee
│   └── manage_users.php  # User management
├── user/
│   ├── dashboard.php     # User dashboard
│   ├── update_preferences.php # Update preferences
│   └── add_review.php    # Add coffee review
├── css/
│   └── style.css         # Main stylesheet
├── js/
│   └── script.js         # JavaScript functionality
├── includes/
│   ├── header.php        # Common header
│   └── footer.php        # Common footer
├── index.php             # Homepage
├── catalog.php           # Coffee catalog
├── login.php             # Login page
├── register.php          # Registration page
└── logout.php            # Logout handler
```

## Features Implemented

✅ User registration and authentication
✅ Admin and user role management
✅ Coffee catalog with filtering
✅ User preferences system
✅ Coffee recommendation algorithm
✅ Rating and review system
✅ Admin dashboard with statistics
✅ Coffee management (CRUD operations)
✅ User management
✅ Responsive design
✅ Security features (password hashing, SQL injection prevention)

## Notes

This is a complete PHP application ready for deployment in a PHP environment. The current preview shows the frontend design and demonstrates the user interface.