# Deploying to Render.com

This guide provides instructions for deploying the School Management System to Render.com.

## Prerequisites

1. A Render.com account
2. A MySQL database (you can use Render's PostgreSQL or an external MySQL service like AWS RDS, DigitalOcean, or PlanetScale)

## Setup Steps

### 1. Database Setup

1. Create a MySQL database on your preferred provider
2. Note down the following credentials:
   - Database Host
   - Database Name
   - Database Username
   - Database Password
   - Database Port (usually 3306)

### 2. Render.com Setup

1. Log in to your Render.com account
2. Click on "New" and select "Web Service"
3. Connect your GitHub repository or upload your code
4. Configure the service with the following settings:
   - **Name**: Your application name (e.g., school-management-system)
   - **Environment**: PHP
   - **Build Command**: `composer install`
   - **Start Command**: Use the command from the Procfile: `vendor/bin/heroku-php-apache2 .`

### 3. Environment Variables

Add the following environment variables in the Render dashboard:

```
DB_HOST=your_database_host
DB_NAME=your_database_name
DB_USERNAME=your_database_username
DB_PASSWORD=your_database_password
APP_ENV=production
APP_DEBUG=false
```

### 4. Database Configuration

The application is configured to use environment variables for database connection. Make sure to update the database credentials in the Render dashboard.

### 5. Deploy

Click on "Create Web Service" to deploy your application. Render will automatically build and deploy your application.

## Troubleshooting

### Database Connection Issues

If you encounter database connection issues:

1. Verify that your database credentials are correct
2. Ensure that your database allows connections from Render.com's IP addresses
3. Check if your database service is running

### Application Errors

If your application shows errors after deployment:

1. Check the Render logs for any PHP errors
2. Set `APP_DEBUG=true` temporarily to see detailed error messages
3. Ensure all required PHP extensions are available

## Important Notes

1. The application uses the `Database.env.php` file which reads environment variables for database connection
2. Make sure your database schema is properly set up before deployment
3. For production use, ensure proper security measures are in place

## Updating Your Application

To update your application:

1. Push changes to your connected repository
2. Render will automatically rebuild and deploy your application

Or manually trigger a deploy from the Render dashboard.