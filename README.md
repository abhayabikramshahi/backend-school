# Badimalika Secondary School Management System

**A modular, secure, and responsive web application for managing administrative tasks at Badimalika Secondary School, Nepal.**

---

## Table of Contents

1. [Overview](#overview)
2. [Features](#features)
3. [Recent Updates](#recent-updates)
4. [Folder Structure](#folder-structure)
5. [Database Setup](#database-setup)
6. [Installation & Usage](#installation--usage)
7. [Implementation Plan](#implementation-plan)
8. [Security & Maintenance](#security--maintenance)
9. [Render.com Deployment](#rendercom-deployment)

---

## Overview

This system provides multi-role access (Admin, Teacher, Student) for streamlined management of:

* Users
* Teachers
* Students
* Notices
* Vacancies
* Student Results
* Blog content

Built with PHP (PDO), MySQL, Tailwind CSS, and JavaScript.

---

## Features

* **Authentication & Authorization**: Secure login/register with role-based dashboards.
* **Teacher & Student Management**: Add, edit, delete, search, and pagination.
* **Notice & Vacancy Management**: CRUD operations with file uploads.
* **Result Management**: Class-wise result upload and display.
* **Blog Module**: Create and manage school news posts.
* **Responsive UI**: Tailwind CSS ensures mobile-friendly design.
* **Security**: Prepared statements, bcrypt hashing, CSRF protection, input validation.
* **User Suspension**: Ability to suspend and ban users with automatic expiration.

---

## Render.com Deployment

Follow these steps to deploy the application to Render.com:

### Prerequisites

1. A Render.com account
2. A MySQL database (you can use an external MySQL service like AWS RDS, DigitalOcean, or PlanetScale)

### Quick Deployment Steps

1. Fork or clone this repository to your GitHub account
2. Log in to Render.com and create a new Web Service
3. Connect your GitHub repository
4. Configure the service with the following settings:
   - **Environment**: PHP
   - **Build Command**: `composer install`
   - **Start Command**: `vendor/bin/heroku-php-apache2 .`
5. Add the required environment variables:
   ```
   DB_HOST=your_database_host
   DB_NAME=your_database_name
   DB_USERNAME=your_database_username
   DB_PASSWORD=your_database_password
   APP_ENV=production
   APP_DEBUG=false
   ```
6. Deploy the service

For more detailed deployment instructions, please refer to the [RENDER_DEPLOYMENT.md](RENDER_DEPLOYMENT.md) file.

---

## Recent Updates

### Database Consolidation
* **Consolidated SQL File**: All database structure now in a single file `database_setup.sql`
* **Setup Script**: New `setup_database.php` script to apply all database changes at once
* **Fixed Column Issues**: Resolved the missing 'created_at' column problem

### User Management Enhancements
* **Suspension System**: Administrators can now suspend users for specific periods
* **Ban Functionality**: Added ability to ban users permanently
* **Notification System**: Clear notifications for suspended users
* **Username/ID Login**: Updated login to support both username and ID

### UI Improvements
* **Modern Theme**: White background with black text for better readability
* **Consistent Styling**: Updated all pages to use the modern theme
* **Improved Forms**: Enhanced form layouts and user experience
* **Responsive Design**: Better mobile experience across all pages

### New Files
* `database_setup.sql` - Consolidated database structure
* `setup_database.php` - Script to apply all database changes
* `add_suspension_functionality.php` - Adds user suspension features

---

## Folder Structure

```
backend-school/
├── assets/               # Static assets (css, js, images)
├── config/               # Database connection (PDO)
├── includes/             # Shared components (header, footer, navbar)
├── auth/                 # Authentication (login, register, logout)
├── admin/                # Admin dashboards & CRUD interfaces
├── teacher/              # Teacher dashboard
├── student/              # Student dashboard
├── pages/                # Legacy modules (vacancies, notices, blog)
├── result-system/        # Class-specific result management
├── uploads/              # Uploaded files (profiles, notices, vacancies)
└── index.php             # Public entry point
```

---

## Database Setup

1. Create a MySQL database named `look`.
2. Import `school_management.sql`:

   ```bash
   mysql -u root -p look < school_management.sql
   ```

---

## Installation & Usage

1. Clone the repo into your XAMPP `htdocs` folder:

   ```bash
   git clone <repo-url> backend-school
   ```
2. Start Apache & MySQL in XAMPP.
3. Configure `config/database.php` with your DB credentials.
4. Access the system at `http://localhost/backend-school/`.
5. Default admin credentials:

   * Username: `admin`
   * Password: `admin`

---

## Implementation Plan

**Phase 1: Setup & Auth (1 week)**

* Scaffold folder structure
* Implement PDO connection
* Build registration & login

**Phase 2: Core Modules (2 weeks)**

* Admin CRUD for users, teachers, students
* Notices & vacancies management

**Phase 3: Results & Blog (2 weeks)**

* Class-wise result upload/display
* Blog CRUD

**Phase 4: UI & Responsiveness (1 week)**

* Tailwind theme & layouts
* Mobile-first adjustments
* Visual feedback & animations

**Phase 5: Testing & Deployment (1 week)**

* Unit/integration tests
* Security audit
* Production deployment

---

## Security & Maintenance

* Use prepared statements for all queries.
* Sanitize and validate inputs.
* Hash passwords with `password_hash` (bcrypt).
* Implement CSRF tokens on forms.
* Schedule regular DB backups.
* Monitor logs and apply updates promptly.

---

© 2024 Badimalika Secondary School. All rights reserved.
