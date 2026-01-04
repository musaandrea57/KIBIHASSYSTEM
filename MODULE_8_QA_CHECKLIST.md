# Module 8 QA Checklist & Verification Guide

## 1. Lecturer Evaluation System

### 1.1 Core Functionality
- [x] **Database Schema**: Tables `evaluation_templates`, `evaluation_questions`, `evaluation_periods`, `evaluations`, `evaluation_answers` created and linked.
- [x] **Seed Data**: Standard template with Likert questions seeded. Current semester evaluation period seeded.
- [x] **Anonymity**: `Evaluation` links student to teacher for permission checks, but reports aggregate data. Teachers have no view of individual student names.
- [x] **Duplicate Prevention**: `Evaluation` table has unique constraint (or logic) per student/offering/period.

### 1.2 Student UI & Submission
- [x] **Route**: `/student/evaluations` lists pending evaluations.
- [x] **Submission**: Form allows rating and comments.
- [x] **Logic**: Can only submit if `status` is `pending`. Once submitted, status becomes `submitted` and cannot be edited.
- [x] **Validation**: Required questions must be answered.

### 1.3 Admin Management
- [x] **Templates**: CRUD operations at `/admin/evaluation/templates`.
- [x] **Periods**: CRUD operations at `/admin/evaluation/periods`.
- [x] **Generation**: "Generate Evaluations" button creates pending records for all eligible students.

## 2. Bulk SMS Module

### 2.1 SMS Provider Adapter
- [x] **Interface**: `SmsProviderInterface` defined.
- [x] **Simulated Provider**: `SimulatedSmsProvider` implemented. Logs to `sms_messages` table and `laravel.log`.
- [x] **Configuration**: `config/sms.php` set to use `simulated` driver.

### 2.2 Console Commands (Scheduled Alerts)
- [x] **Fee Reminders**: `php artisan sms:fee-reminders` (Sends if balance > 0).
- [x] **Registration Reminders**: `php artisan sms:registration-reminders` (Sends if active but not registered).
- [x] **NHIF Expiry**: `php artisan sms:nhif-reminders` (Sends if expiring in 30 days).
- [x] **Results Alert**: `php artisan sms:send-results-alerts` (Sends when results published & fee cleared).
- [x] **Idempotency**: All commands check `sms_messages` history to prevent duplicate sends within a time window.

### 2.3 UI & Logs
- [x] **SMS Logs**: Viewable at `/admin/communication/sms/logs`. Shows status (Sent/Failed/Queued).
- [x] **Send Bulk**: UI at `/admin/communication/sms` to select target groups (Admissions, Finance, etc.).

## 3. Phone Number Normalization
- [x] **Service**: `PhoneNumberService` implemented.
- [x] **Standard**: Normalizes to E.164 (e.g., `+2557XXXXXXXX`).
- [x] **Integration**: Applied in `SmsService` before sending.

## 4. Verification Steps
1. **Run Migrations & Seed**:
   ```bash
   php artisan migrate:fresh --seed
   ```
2. **Test Fee Reminder**:
   ```bash
   php artisan sms:fee-reminders
   ```
   *Expected Output*: "Processed X reminders." (Check `sms_messages` table).
3. **Check Logs**:
   Visit `/admin/communication/sms/logs` to see the generated messages.
4. **Student Evaluation**:
   Login as student, go to Evaluations, submit a pending evaluation.

## 5. Security & Roles
- [x] **Permissions**: Routes protected by `manage_evaluation_templates`, `manage_sms_settings`, etc.
- [x] **Middleware**: `auth`, `verified`, and role/permission checks applied.
