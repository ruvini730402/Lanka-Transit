# 🚌 Lanka Transit - Bus Booking System

[![PHP](https://img.shields.io/badge/PHP-8.0+-777BB4?style=flat&logo=php&logoColor=white)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.0+-4479A1?style=flat&logo=mysql&logoColor=white)](https://mysql.com)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-7952B3?style=flat&logo=bootstrap&logoColor=white)](https://getbootstrap.com)
[![PayHere](https://img.shields.io/badge/PayHere-Integration-00B4D8?style=flat)](https://payhere.lk)

A comprehensive bus booking and management system for Sri Lankan public transport, built with PHP and MySQL. Features real-time seat availability, secure payment processing via PayHere, and comprehensive admin management tools.

![Lanka Transit Logo](info/logo.jpeg)

## 🌟 Features

### 🎯 **Core Functionality**
- **Smart Bus Search** - Advanced search with intermediate stops support
- **Real-time Seat Management** - Live seat availability and booking
- **Secure Payment Gateway** - PayHere integration with sandbox/live modes
- **User Authentication** - Registration, login, and session management
- **Booking Management** - Create, view, and cancel bookings
- **Announcement System** - Priority-based announcements with date filtering

### 👥 **User Features**
- **Quick Registration** - Email-based account creation
- **Advanced Search** - Search by origin, destination, date, and time
- **Seat Selection** - Visual seat map with gender-based allocation
- **Booking History** - Track all past and upcoming journeys
- **Payment Tracking** - Real-time payment status updates
- **Cancellation Requests** - Submit and track booking cancellations

### 🔧 **Admin Panel**
- **Bus Fleet Management** - Add, edit, and manage bus information
- **Booking Oversight** - View and manage all system bookings
- **Payment Monitoring** - Track payments and financial reports
- **User Management** - Manage user accounts and permissions
- **Announcement Control** - Create and manage system announcements
- **Cancellation Processing** - Review and process cancellation requests

## 🚀 Quick Start

### Prerequisites
- PHP 8.0 or higher
- MySQL 8.0 or higher
- Web server (Apache/Nginx)
- Composer (optional, for dependencies)

### Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/ruvini730402/Lanka-Transit.git
   cd Lanka-Transit
   ```

2. **Set up the database**
   ```bash
   # Import the database schema
   mysql -u your_username -p your_database < config/schema.sql
   
   # Import sample data (optional)
   mysql -u your_username -p your_database < config/sample_data.sql
   ```

3. **Configure environment**
   ```bash
   # Copy environment template
   cp .env.example .env
   
   # Edit .env with your database and PayHere credentials
   nano .env
   ```

4. **Environment Variables**
   ```env
   # Database Configuration
   DB_HOST=localhost
   DB_NAME=lanka_transit
   DB_USER=your_username
   DB_PASS=your_password
   
   # PayHere Configuration
   PAYHERE_MERCHANT_ID=your_merchant_id
   PAYHERE_MERCHANT_SECRET=your_merchant_secret
   PAYHERE_SANDBOX=true  # Set to false for production
   
   # Application Settings
   APP_ENV=development
   APP_DEBUG=true
   ```

5. **Set up web server**
   ```bash
   # For Apache (ensure mod_rewrite is enabled)
   # Point document root to the project directory
   
   # For development, you can use PHP built-in server
   php -S localhost:8000
   ```

6. **Run tests** (optional)
   ```bash
   # Run the complete test suite
   php tests/run_all_tests.php
   
   # Or access via browser
   http://localhost:8000/tests/run_all_tests.php
   ```

## 📁 Project Structure

```
Lanka-Transit/
├── 📄 index.php              # Homepage and main entry point
├── 🔐 auth/                  # Authentication system
│   ├── login.php            # User/Admin login
│   ├── register.php         # User registration
│   └── logout.php           # Session termination
├── 📱 pages/                 # User-facing pages
│   ├── search.php           # Bus search and results
│   ├── booking.php          # Booking creation
│   ├── payment.php          # Payment processing
│   ├── dashboard.php        # User dashboard
│   └── announcements.php    # System announcements
├── 🛠️ Admin/                 # Administrative panel
│   ├── dashboard.php        # Admin overview
│   ├── buses.php            # Fleet management
│   ├── bookings.php         # Booking management
│   └── payments.php         # Payment monitoring
├── 🏗️ classes/               # Core business logic
│   ├── Database.php         # Database abstraction
│   ├── User.php             # User management
│   ├── Auth.php             # Authentication logic
│   ├── Bus.php              # Bus operations
│   ├── Booking.php          # Booking management
│   ├── Payment.php          # Payment processing
│   ├── Announcement.php     # Announcement system
│   └── BookingCancellation.php # Cancellation handling
├── ⚙️ config/                # Configuration files
│   ├── database.php         # Database configuration
│   ├── schema.sql           # Database schema
│   └── sample_data.sql      # Sample data for testing
├── 🧪 tests/                 # Comprehensive test suite
│   ├── run_all_tests.php    # Test runner
│   ├── test_core_*.php      # Core functionality tests
│   └── README.md            # Testing documentation
├── 🎨 assets/                # Static assets
├── 📧 PHPMailer/             # Email functionality
├── 🔧 includes/              # Shared components
└── 📋 info/                  # Documentation and specs
    ├── ER.txt              # Database schema documentation
    ├── classes.txt         # Class specifications
    └── structure.txt       # Project structure guide
```

## 🔧 Configuration

### Database Setup
The system uses MySQL with a comprehensive schema supporting:
- User management with role-based access
- Bus fleet and route management
- Booking and payment tracking
- Announcement and feedback systems
- Incident reporting and resolution

### PayHere Integration
Secure payment processing with support for:
- Sandbox and production environments
- Multiple payment methods
- Real-time payment verification
- Automated status updates

### Environment Configuration
All sensitive configuration is managed through environment variables:
- Database credentials
- PayHere API keys
- Application settings
- Debug flags

## 🧪 Testing

The project includes a comprehensive test suite covering all core functionality:

```bash
# Run all tests
php tests/run_all_tests.php

# Run individual test modules
php tests/test_core_database.php     # Database & environment
php tests/test_core_auth.php         # Authentication
php tests/test_core_search.php       # Search functionality
php tests/test_core_booking.php      # Booking management
php tests/test_core_payment.php      # Payment processing
php tests/test_core_announcement.php # Announcements
php tests/test_core_booking_cancellation.php # Cancellations
```

### Test Coverage
- ✅ Database connectivity and security
- ✅ User authentication and session management
- ✅ Bus search algorithm with intermediate stops
- ✅ Booking creation and validation
- ✅ PayHere payment integration
- ✅ Announcement system with priority management
- ✅ Booking cancellation workflow
- ✅ Input validation and sanitization
- ✅ Error handling and edge cases

## 🔒 Security Features

- **SQL Injection Prevention** - Prepared statements throughout
- **XSS Protection** - Input sanitization and output encoding
- **CSRF Protection** - Token-based form validation
- **Password Security** - Bcrypt hashing with salt
- **Session Management** - Secure session handling
- **Input Validation** - Comprehensive server-side validation
- **Environment Isolation** - Sensitive data in environment variables

## 📱 User Interface

- **Responsive Design** - Bootstrap 5 for mobile-first approach
- **Intuitive Navigation** - Clean, user-friendly interface
- **Real-time Updates** - AJAX for seamless user experience
- **Accessibility** - WCAG 2.1 compliant design
- **Multi-language Ready** - Structured for internationalization

## 🛠️ API Integration

### PayHere Payment Gateway
- Sandbox and production mode support
- Secure signature verification
- Real-time payment status updates
- Automated booking confirmation

### Email Services
- PHPMailer integration for notifications
- Booking confirmations
- Password reset functionality
- Administrative alerts

## 📊 Business Logic

### Search Algorithm
Advanced bus search supporting:
- Direct route matching
- Intermediate stop calculations
- Seat availability checks
- Price and time filtering
- Multi-criteria sorting

### Booking Management
Comprehensive booking system with:
- Real-time seat allocation
- Gender-based seating rules
- Overbooking prevention
- Automated status updates
- Cancellation workflow

## 🔄 Development Workflow

1. **Environment Setup** - Configure `.env` file
2. **Database Migration** - Run schema and sample data scripts
3. **Feature Development** - Follow PSR coding standards
4. **Testing** - Run comprehensive test suite
5. **Code Review** - Ensure security and performance standards
6. **Deployment** - Use provided deployment configurations

## 🚀 Deployment

### Production Checklist
- [ ] Set `APP_ENV=production` in `.env`
- [ ] Set `PAYHERE_SANDBOX=false` for live payments
- [ ] Configure SSL certificates
- [ ] Set up database backups
- [ ] Configure error logging
- [ ] Enable production optimizations

### Server Requirements
- PHP 8.0+ with required extensions
- MySQL 8.0+ or MariaDB 10.4+
- Apache/Nginx with mod_rewrite
- SSL certificate for HTTPS
- Minimum 512MB RAM
- 1GB disk space

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

### Development Guidelines
- Follow PSR-12 coding standards
- Write comprehensive tests for new features
- Update documentation for significant changes
- Ensure security best practices
- Test across different environments

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🆘 Support

- **Documentation**: Check the `/info/` folder for detailed specifications
- **Issues**: Report bugs via GitHub Issues
- **Testing**: Use the comprehensive test suite in `/tests/`
- **Email**: Contact the development team for support

## 🏆 Acknowledgments

- **PayHere** for payment gateway integration
- **Bootstrap** for responsive UI framework
- **PHPMailer** for email functionality
- **Contributors** who helped build and test the system

---

**Lanka Transit** - Connecting Sri Lanka, one journey at a time. 🇱🇰

Built with ❤️ for Sri Lankan public transport by [Ruvini730402](https://github.com/ruvini730402)