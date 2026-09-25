<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>
# 🏥 Tacunan Health Information Management System (Tacunan HIMS)

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>
[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.3%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-3.x-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)](https://tailwindcss.com)
[![MySQL](https://img.shields.io/badge/MySQL-8.0%2B-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://www.mysql.com/)

## About Laravel
A comprehensive Barangay Health Center (BHC) management and clinical records system built for **Barangay Tacunan Health Center**. It features Role-Based Access Control (RBAC), community patient profiling, clinical vital signs intake, 8 public health program modules, appointment scheduling, FIFO inventory tracking for medicines and vaccines, and automated audit logging.

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:
---

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).
## 📋 Table of Contents
- [System Requirements](#-system-requirements)
- [Step-by-Step Installation Guide](#-step-by-step-installation-guide)
  - [1. Clone the Repository](#1-clone-the-repository)
  - [2. Install PHP Dependencies](#2-install-php-dependencies)
  - [3. Install Node.js Dependencies](#3-install-nodejs-dependencies)
  - [4. Environment Configuration](#4-environment-configuration)
  - [5. Generate Application Key](#5-generate-application-key)
  - [6. Setup Database](#6-setup-database)
  - [7. Compile Frontend Assets](#7-compile-frontend-assets)
  - [8. Start the Local Server](#8-start-the-local-server)
- [Default Demo Accounts](#-default-demo-accounts)
- [System Modules & Features](#-system-modules--features)
- [Troubleshooting & FAQ](#-troubleshooting--faq)

Laravel is accessible, powerful, and provides tools required for large, robust applications.
---

## Learning Laravel
## 💻 System Requirements

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.
Make sure your machine has the following installed before proceeding:

In addition, [Laracasts](https://laracasts.com) contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.
* **PHP:** `^8.3` or `^8.5` with extensions: `pdo_mysql`, `mbstring`, `openssl`, `bcmath`, `curl`, `xml`, `zip`
* **Composer:** `v2.x` ([Download Composer](https://getcomposer.org/))
* **Node.js:** `v18.x` or `v20.x` & **NPM** ([Download Node.js](https://nodejs.org/))
* **Database Server:** **MySQL** or **MariaDB** (via [XAMPP](https://www.apachefriends.org/), [Laragon](https://laragon.org/), or standalone MySQL)
* **Git:** ([Download Git](https://git-scm.com/))

You can also watch bite-sized lessons with real-world projects on [Laravel Learn](https://laravel.com/learn), where you will be guided through building a Laravel application from scratch while learning PHP fundamentals.
---

## Agentic Development
## 🚀 Step-by-Step Installation Guide

Laravel's predictable structure and conventions make it ideal for AI coding agents like Claude Code, Cursor, and GitHub Copilot. Install [Laravel Boost](https://laravel.com/docs/ai) to supercharge your AI workflow:
Follow these steps in order to set up and run the system locally from GitHub:

### 1. Clone the Repository
Open your terminal (PowerShell, Command Prompt, or Git Bash) and run:
```bash
composer require laravel/boost --dev
git clone https://github.com/YOUR_USERNAME/BHCProject.git
cd BHCProject
```
*(Replace `YOUR_USERNAME/BHCProject.git` with your actual repository URL).*

php artisan boost:install
---

### 2. Install PHP Dependencies
Download and install all backend PHP packages:
```bash
composer install
```

Boost provides your agent 15+ tools and skills that help agents build Laravel applications while following best practices.
---

## Contributing
### 3. Install Node.js Dependencies
Download and install all JavaScript and CSS libraries:
```bash
npm install
```

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).
---

## Code of Conduct
### 4. Environment Configuration
Create your local `.env` configuration file by duplicating `.env.example`:

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).
**Windows PowerShell:**
```powershell
Copy-Item .env.example .env
```
**Linux / macOS / Git Bash:**
```bash
cp .env.example .env
```

## Security Vulnerabilities
Open `.env` in your text editor (VS Code, Notepad, etc.) and configure the database connection settings:
```ini
APP_NAME="Tacunan Integrated Health System"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tacunan_hims_db
DB_USERNAME=root
DB_PASSWORD=
```
> **Note for XAMPP users:** The default `DB_USERNAME` is `root` and `DB_PASSWORD` is left empty `""`.

## License
---

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
### 5. Generate Application Key
Generate the Laravel encryption security key:
```bash
php artisan key:generate
```

---

### 6. Setup Database

Start your MySQL server (e.g. click **Start** for MySQL in XAMPP or Laragon).

Create the database named `tacunan_hims_db`:
* **Via phpMyAdmin:** Go to `http://localhost/phpmyadmin`, click **New**, enter `tacunan_hims_db` as database name, and click **Create**.
* **Via MySQL CLI:**
  ```sql
  CREATE DATABASE tacunan_hims_db;
  ```

Choose **ONE** of the two database population methods below:

#### Option A: Run Laravel Migrations & Seeders (Recommended)
This creates all tables, seeds standard barangay puroks, the 6 user role accounts, initial inventory items, batches, and demo patients:
```bash
php artisan migrate --seed
```

#### Option B: Import the Pre-packaged SQL Dump
If you prefer importing the full database snapshot provided in this repository:
1. Open **phpMyAdmin** (`http://localhost/phpmyadmin`).
2. Select the `tacunan_hims_db` database on the left menu.
3. Click the **Import** tab at the top.
4. Click **Choose File** and select `tacunan_hims_db.sql` from the project root.
5. Scroll down and click **Import**.

---

### 7. Compile Frontend Assets
Build Tailwind CSS and Alpine.js assets:
```bash
npm run build
```
*(For active development with hot-reloading, run `npm run dev` in a separate terminal).*

---

### 8. Start the Local Server
Launch the local Laravel development web server:
```bash
php artisan serve
```

Once started, open your web browser and navigate to:
```
http://localhost:8000
```
or
```
http://127.0.0.1:8000
```

---

## 👥 Default Demo Accounts

The system provides 6 pre-configured role accounts. All accounts share the same default password:

> **Default Password:** `password123`

| Role Display Name | Role Key | Email Address | Access Level |
|---|---|---|---|
| **Administrator** | `admin` | `admin@tacunan.gov.ph` | Full Access, User Management, System Audit Logs |
| **Barangay Health Nurse** | `nurse` | `nurse@tacunan.gov.ph` | Clinical Care, NTP, CVD, PhilPEN, Vaccines, Inventory |
| **Barangay Midwife** | `midwife` | `midwife@tacunan.gov.ph` | Maternal/Prenatal, Family Planning, Appointments |
| **Barangay Nutrition Scholar** | `bns` | `bns@tacunan.gov.ph` | Operation Timbang, Anthropometrics, Feeding Programs |
| **Barangay Health Worker** | `bhw` | `bhw@tacunan.gov.ph` | Patient Intake, Baseline Vitals, Purok Kalusugan |
| **Barangay Health Volunteer** | `bhv` | `bhv@tacunan.gov.ph` | Community Intake Assistance, Field Outreach |

💡 **Quick Login Tip:** On the login page (`/login`), click any of the 1-click **Demo Account Quick Fill** buttons at the bottom of the form to automatically fill in the credentials.

---

## 🩺 System Modules & Features

1. **Patient Registry & Demographics:**
   * Automated Patient Control Numbers (`TAC-YYYY-XXXX`).
   * Barangay Purok assignment, PhilHealth numbers, and emergency contact details.
2. **Clinical Health Assessments & Vitals:**
   * Anthropometrics: Height, Weight, and automated Body Mass Index (BMI) calculation with nutritional status categorization (Normal, Underweight, Overweight, Obese, Wasted, Stunted).
   * Vital Signs: Blood Pressure (Systolic/Diastolic), Pulse Rate, Respiration Rate, and Body Temperature.
3. **The 8 Barangay Health Center Programs:**
   * **Family Planning:** Methods (Pills, Injectables/DMPA, Condoms, IUD, Implants, NFP-BOM) and client classification.
   * **Prenatal Care:** LMP, EDC, and trimester milestones (Visit 1 to 4+).
   * **Immunization:** Pediatric & adult routine vaccines (BCG, HepB, Pentavalent, OPV, IPV, PCV, MMR, Td) with dosage sequence and injection site.
   * **CVD Screening:** Cardiovascular risk stratification and target organ damage screen.
   * **PhilPEN:** Package of Essential NCD Interventions (Tobacco, Alcohol, and Fasting Blood Sugar).
   * **NTP (Tuberculosis):** Chronic cough screening, presumptive TB flags, and GeneXpert referrals.
   * **Purok Kalusugan:** Community and household outreach visit notes.
   * **BNS Program:** 120-day feeding intake, Vitamin A mega-dose, iron, and deworming.
4. **Appointments & Follow-up Scheduling:**
   * Automated follow-up booking during consultations.
   * Status tracking: `scheduled`, `attended`, `cancelled`, and `missed`.
5. **Medicine & Vaccine Inventory with FIFO:**
   * Catalog categorization: Medicines vs. Vaccines with Cold-Chain storage indicators.
   * Batch tracking with receipt dates and expiration dates.
   * **Automated FIFO (First-In, First-Out) Stock Dispensing** ensuring nearest-expiring batches are depleted first.
   * Low-stock and near-expiry warning indicators.
6. **Reports & Analytics:**
   * Demographic distribution by purok and gender.
   * Consultation volume across the 8 health programs with date-range filters.
   * Appointment attendance and inventory usage summaries.
7. **Security & Audit Logs (Admin Only):**
   * Role-based access control protecting administrative routes.
   * Audit trail logging all actions with user stamps, module tags, action types, and IP addresses.
   * Account deactivation middleware preventing unauthorized access.

---

## 🛠️ Troubleshooting & FAQ

### 1. `ViteException: Unable to locate file in Vite manifest`
* **Cause:** The frontend assets haven't been compiled yet.
* **Fix:** Run `npm run build` in your terminal.

### 2. `SQLSTATE[HY000] [1049] Unknown database 'tacunan_hims_db'`
* **Cause:** The MySQL database has not been created yet.
* **Fix:** Open phpMyAdmin (`http://localhost/phpmyadmin`) or MySQL CLI and run:
  ```sql
  CREATE DATABASE tacunan_hims_db;
  ```
  Then run `php artisan migrate --seed`.

### 3. `SQLSTATE[HY000] [2002] Connection refused`
* **Cause:** MySQL server is stopped.
* **Fix:** Open XAMPP or Laragon and start the **MySQL** service. Verify port `3306` is open.

### 4. `No application encryption key has been specified`
* **Cause:** The `APP_KEY` in `.env` is empty.
* **Fix:** Run `php artisan key:generate`.

### 5. Resetting the Database to Fresh Demo State
If you want to wipe the database and re-seed clean sample data at any point, run:
```bash
php artisan migrate:fresh --seed
```

---

## 📄 License
This project is developed for educational and barangay health center management purposes.

