<img width="1920" height="1033" alt="homeup" src="https://github.com/user-attachments/assets/d36332fa-d135-4b1a-81f0-a5a4642fadee" />
<img width="1920" height="1049" alt="homedown" src="https://github.com/user-attachments/assets/b5745011-e97c-47bf-af54-73ebf393ec17" />
# ShopHub E-commerce Website

A complete, modern e-commerce website built with HTML, CSS, JavaScript, jQuery, Bootstrap 5, MDBootstrap, PHP, and MySQL.

## Features

### Frontend (User Website)
- **Home Page**: Hero section with banner slider, featured categories, and trending products grid
- **Shop Page**: Product listing with category filter, search bar, and sorting options
- **Product Detail Page**: Product images carousel, price, description, reviews section, "Add to Cart" button, and related products
- **Cart Page**: View cart items, update quantity, remove item, display subtotal and total
- **Checkout Page**: Billing/shipping form with mock payment confirmation
- **User Authentication**: Login, Signup, and Forgot Password pages connected to MySQL database
- **User Dashboard**: Profile management, order history, and logout feature

### Backend (PHP + MySQL)
- **Database**: Complete e-commerce database with tables for users, products, orders, order items, categories, reviews, and admin users
- **Session Management**: PHP sessions for cart management and user login
- **Admin Panel**: Separate admin login with role validation
- **Dashboard**: Statistics showing total users, products, orders, and sales chart using Chart.js
- **Product Management**: Add, edit, delete products with image upload
- **Order Management**: View orders, update order status (pending, shipped, delivered)
- **User Management**: View or delete users

### Technical Features
- **Responsive Design**: Fully responsive layout using Bootstrap 5
- **Modern UI**: Uses Poppins and Montserrat fonts with smooth animations and shadows
- **AJAX**: Dynamic cart updates and form submissions
- **Form Validation**: Both client-side (jQuery) and server-side (PHP) validation
- **Security**: Input sanitization, password hashing, SQL injection prevention
- **File Upload**: Product image upload with validation

## Installation

### Prerequisites
- XAMPP or similar web server with PHP and MySQL
- Web browser (Chrome, Firefox, Safari, etc.)

### Setup Instructions

1. **Clone/Download the Project**
   ```bash
   git clone <repository-url>
   cd ecommerce-App
   ```

2. **Database Setup**
   - Start XAMPP and launch phpMyAdmin
   - Create a new database named `ecommerce`
   - Import the `database.sql` file to create all necessary tables and sample data

3. **Configure Database Connection**
   - Open `includes/db_connect.php`
   - Update the database credentials if needed:
   ```php
   $host = 'localhost';
   $dbname = 'ecommerce';
   $username = 'root';
   $password = '';
   ```

4. **Start the Server**
   - Start Apache and MySQL in XAMPP
   - Navigate to `http://localhost/ecommerce-App` in your browser

## Default Admin Login
- **Username**: admin
- **Password**: admin123

## File Structure

```
ecommerce-App/
├── admin/                  # Admin panel files
│   ├── index.php          # Admin dashboard
│   ├── login.php          # Admin login
│   ├── logout.php         # Admin logout
│   ├── products.php       # Product management
│   └── ...
├── user/                   # User account files
│   ├── login.php          # User login
│   ├── signup.php         # User registration
│   ├── profile.php        # User profile
│   ├── orders.php         # Order history
│   └── ...
├── includes/              # Shared components
│   ├── header.php         # Website header
│   ├── footer.php         # Website footer
│   └── db_connect.php     # Database connection
├── assets/                # Static assets
│   ├── css/
│   │   └── style.css      # Custom styles
│   ├── js/
│   │   └── script.js      # Custom JavaScript
│   └── images/            # Product images
├── index.php              # Home page
├── shop.php               # Shop page
├── product.php            # Product details
├── cart.php               # Shopping cart
├── checkout.php           # Checkout process
├── order_success.php      # Order confirmation
└── database.sql           # Database schema and sample data
```

## Usage

### For Users
1. Browse products on the home page or shop page
2. View product details and add items to cart
3. Proceed to checkout and place orders
4. Create an account to track orders and manage profile

### For Administrators
1. Login to admin panel at `/admin/login.php`
2. View dashboard statistics and sales charts
3. Manage products (add, edit, delete)
4. Process orders and update status
5. Manage users and categories

## Technologies Used

- **Frontend**: HTML5, CSS3, JavaScript, jQuery
- **Frameworks**: Bootstrap 5, MDBootstrap
- **Backend**: PHP 8+
- **Database**: MySQL
- **Charts**: Chart.js
- **Fonts**: Google Fonts (Poppins, Montserrat)
- **Icons**: Font Awesome

## Security Features

- SQL injection prevention using prepared statements
- XSS protection with input sanitization
- Password hashing using PHP's password_hash()
- Session management for authentication
- File upload validation for product images

## Browser Support

- Chrome 60+
- Firefox 55+
- Safari 12+
- Edge 79+

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## License

This project is for educational purposes. Feel free to use and modify as needed.

## Support

For any issues or questions, please check the code comments or create an issue in the repository.

---

**Note**: This is a demonstration project. For production use, additional security measures and optimizations should be implemented.
