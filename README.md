# Lanka Transit - Bus Seat Booking Application

A comprehensive bus seat booking system built with modern web technologies.

## Technology Stack

- **Frontend**: HTML5, Vanilla JavaScript, Bootstrap 5
- **Backend**: PHP (OOP, No Frameworks)
- **Database**: MySQL
- **Security**: SQL Injection Prevention, XSS Protection

## Features

- Search buses by origin, destination, date, and fare
- Real-time seat availability
- Secure booking system
- Responsive design for all devices
- User authentication and management
- Admin panel for bus and route management

## Installation

1. Clone the repository
2. Set up a MySQL database
3. Import the database schema from `database/schema.sql`
4. Configure database connection in `config/database.php`
5. Set up a web server (Apache/Nginx) pointing to the project root

## Project Structure

```
Lanka-Transit/
├── config/             # Configuration files
├── src/               # Source code
│   ├── controllers/   # Controllers
│   ├── models/        # Data models
│   ├── views/         # View templates
│   └── utils/         # Utility classes
├── public/            # Public assets
│   ├── css/           # Stylesheets
│   ├── js/            # JavaScript files
│   └── images/        # Images
├── database/          # Database files
└── api/              # API endpoints
```

## License

This project is licensed under the MIT License.
