# KIBIHAS Portal (Kibosho Institute of Health and Allied Sciences)

## Overview
The **KIBIHAS Portal** is a comprehensive academic and administrative management system designed for the Kibosho Institute of Health and Allied Sciences. It serves students, academic staff, administrators, and finance officers by streamlining academic processes, financial management, and institutional communication.

## Key Features

### 🎓 Academic Management
- **Program & Course Management**: Dynamic setup of academic programs, semesters, and modules.
- **Student Registration**: Digital enrollment, course registration, and history tracking.
- **Results & Transcripts**: Secure grading system, GPA calculation, and transcript generation.
- **Assessments**: Continuous assessment tracking and final examination management.

### 💰 Finance & Payments
- **Fee Structures**: Configurable fee structures for different programs and years.
- **Invoicing**: Automated invoice generation for tuition and other fees.
- **Payments**: Integration with payment records, receipt generation, and clearance status tracking.
- **Reports**: Financial collection and outstanding balance reports.

### 👥 User Portals
- **Student Portal**: Access to results, financial status, course registration, and hostel application.
- **Staff Portal**: Course management, grading interface, and student roster views.
- **Admin Dashboard**: Centralized control over all system modules and user management.

### 🏥 Welfare & Services
- **Hostel Management**: Room allocation, bed availability tracking, and occupancy reports.
- **NHIF Integration**: Health insurance membership verification and compliance tracking.

### 📢 Communication
- **Internal Messaging**: Secure messaging between staff and students.
- **Bulk SMS**: System-wide announcements and alerts.

## Technology Stack
- **Framework**: [Laravel 10.x](https://laravel.com)
- **Frontend**: [Tailwind CSS](https://tailwindcss.com), Blade Templates, Alpine.js
- **Database**: MySQL
- **Authentication**: Laravel Breeze / Spatie Permission

## Getting Started

### Prerequisites
- PHP >= 8.1
- Composer
- Node.js & NPM
- MySQL

### Installation
1. **Clone the repository**
   ```bash
   git clone https://github.com/musaandrea57/kibihas-portal.git
   cd kibihas-portal
   ```

2. **Install Dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Environment Setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   *Configure your database credentials in the `.env` file.*

4. **Database Migration**
   ```bash
   php artisan migrate --seed
   ```

5. **Build Assets & Run**
   ```bash
   npm run build
   php artisan serve
   ```

## License
Proprietary software for the Kibosho Institute of Health and Allied Sciences.
