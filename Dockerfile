# Use the official PHP image as the base image
FROM php:7.4-apache

# Set the working directory
WORKDIR /var/www/html

# Copy the current directory contents into the container at /var/www/html
COPY . /var/www/html

# Install any needed packages specified in the requirements.txt
RUN docker-php-ext-install pdo pdo_mysql

# Expose port 80 to the outside world
EXPOSE 80

# Run the application
CMD ["apache2-foreground"]