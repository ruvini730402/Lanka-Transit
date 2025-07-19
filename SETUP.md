# Lanka Transit Development Setup

## Prerequisites

- PHP 8.0 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx)
- Composer (optional, for dependency management)

## Installation Steps

### 1. Clone the Repository
```bash
git clone <repository-url>
cd Lanka-Transit
```

### 2. Database Setup
1. Create a MySQL database named `lanka_transit`
2. Import the database schema:
   ```bash
   mysql -u root -p lanka_transit < database/schema.sql
   ```

### 3. Configuration
1. Update database credentials in `config/database.php`:
   ```php
   private const DB_HOST = 'localhost';
   private const DB_NAME = 'lanka_transit';
   private const DB_USER = 'your_username';
   private const DB_PASS = 'your_password';
   ```

### 4. Web Server Setup
Configure your web server to point to the project root directory.

#### Apache Configuration
Add to your `.htaccess` file in the project root:
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]
```

#### Nginx Configuration
```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

### 5. File Permissions
Ensure proper file permissions:
```bash
chmod 755 -R .
chmod 644 -R *.php
```

### 6. Testing
1. Access the application in your browser: `http://localhost/lanka-transit`
2. Test the search functionality
3. Check database connectivity

## Security Considerations

- Change default admin credentials
- Update database passwords
- Enable HTTPS in production
- Configure proper file permissions
- Set up regular database backups

## Development Environment

For development, ensure:
- Error reporting is enabled
- Display errors is turned on
- Debug mode is active

## Production Deployment

For production:
- Disable error display
- Enable error logging
- Use HTTPS
- Configure proper caching
- Set up monitoring

## API Endpoints

- `GET /api/search.php?action=origins` - Get all origins
- `GET /api/search.php?action=destinations&origin=<origin>` - Get destinations for origin
- `GET /api/search.php?action=search&origin=<origin>&destination=<destination>&date=<date>&max_fare=<fare>` - Search buses

## File Structure

```
Lanka-Transit/
├── api/                    # API endpoints
│   └── search.php         # Search API
├── config/                # Configuration files
│   ├── app.php           # Application config
│   └── database.php      # Database config
├── database/             # Database files
│   └── schema.sql       # Database schema
├── public/              # Public assets
│   ├── css/            # Stylesheets
│   └── js/             # JavaScript files
├── src/                # Source code
│   ├── controllers/    # Controllers
│   ├── models/        # Data models
│   ├── utils/         # Utility classes
│   └── views/         # View templates
├── index.php           # Main entry point
├── bootstrap.php       # Application bootstrap
└── README.md          # This file
```

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## License

This project is licensed under the MIT License.
