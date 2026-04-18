## 🏠 Home Page:
<img width="1920" height="1033" alt="homeup" src="https://github.com/user-attachments/assets/47fbf715-0801-41c1-b285-26bd151bc86a" />
<img width="1920" height="1049" alt="homedown" src="https://github.com/user-attachments/assets/75f063ad-912c-49c6-b6c6-bde295104b93" />


## 📝 Signup Page:
<img width="1920" height="1044" alt="signup" src="https://github.com/user-attachments/assets/15876b7f-398f-448d-9ddd-7e7b69bfbb01" />

## 🔐 Login Page:
<img width="1920" height="1040" alt="login" src="https://github.com/user-attachments/assets/a3a7a1ed-e4c3-4cfb-8b6a-4cbcc559b81e" />

### 🗂️ Category Page:
<img width="1920" height="1046" alt="shop" src="https://github.com/user-attachments/assets/2b154024-7194-43c1-9dd3-0bc475e28d6d" />
<img width="1920" height="1040" alt="categories down" src="https://github.com/user-attachments/assets/5aff1d8b-8a7e-4da4-b302-f7205a320090" />


## 📞 Contact Us:
<img width="1920" height="1034" alt="contact up" src="https://github.com/user-attachments/assets/6e01c353-26e6-4e77-b29f-739fde75ffc5" />
<img width="1920" height="1044" alt="contact down" src="https://github.com/user-attachments/assets/006a6f69-23ce-478f-8af1-96b32bf0f3f7" />

## 📄 About:
<img width="1920" height="1037" alt="about up" src="https://github.com/user-attachments/assets/44a8fd5e-ee37-4775-8524-5e358065d68b" />
<img width="1920" height="1033" alt="down about" src="https://github.com/user-attachments/assets/8d6535a9-515a-425e-9591-1ae5b9a399d8" />
 🛒 Cart Page:
<img width="1920" height="1036" alt="cart page" src="https://github.com/user-attachments/assets/b25fe167-9dda-44b0-b5bd-6f38c2dc6ab3" />


## 🎥 **Live Demo**
👉 **[View E-Commerce Website](https://your-demo-link-here)**

https://github.com/user-attachments/assets/ab9c82a3-1615-4feb-b0be-3dc80b82d424


https://github.com/user-attachments/assets/79c2ef82-3346-43e9-97a2-976c7132cc34

















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
