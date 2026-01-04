# Fee Clearance Enforcement Layer - QA Checklist

## 1. Data Integrity & Setup
- [ ] **Migrations**:
    - [ ] `fee_clearance_statuses` table exists with correct schema (student_id, academic_year_id, semester_id, status, outstanding_balance, last_calculated_at).
    - [ ] `fee_clearance_overrides` table exists with correct schema (expiry_date, is_active, reason, granted_by, revoked_by).
- [ ] **Seeding**:
    - [ ] `FeeClearanceSeeder` runs successfully.
    - [ ] Creates "Student Not Cleared" with unpaid invoice.
    - [ ] Creates "Student Cleared" with fully paid invoice.
    - [ ] Creates "Accountant User".

## 2. Business Logic (FeeClearanceService)
- [ ] **Calculation**:
    - [ ] `calculateForStudent` correctly sums invoices and payments.
    - [ ] Returns `is_cleared = true` ONLY if balance <= 0 (strict check).
- [ ] **Overrides**:
    - [ ] `isCleared` returns true if valid active override exists, regardless of balance.
    - [ ] Override respects `expiry_date`.
- [ ] **Snapshot**:
    - [ ] `refreshSnapshot` updates the `fee_clearance_statuses` table.
    - [ ] Status correctly reflects 'cleared', 'not_cleared', or 'overridden'.

## 3. Automatic Updates (Observers)
- [ ] **Payment Created**:
    - [ ] Posting a full payment for "Student Not Cleared" triggers snapshot refresh.
    - [ ] Status changes to 'cleared' automatically.
- [ ] **Payment Reversed**:
    - [ ] Reversing the payment triggers snapshot refresh.
    - [ ] Status changes back to 'not_cleared'.
- [ ] **Invoice Updates**:
    - [ ] Creating/updating invoice triggers refresh (if implemented in InvoiceObserver - verify).

## 4. Access Control (Middleware)
- [ ] **Restricted Routes**:
    - [ ] `student.academic.results`
    - [ ] `student.academic.transcript`
    - [ ] `student.academic.assessments`
- [ ] **Behavior**:
    - [ ] "Student Not Cleared" accessing restricted route -> Redirects to `student.finance.clearance_required`.
    - [ ] "Student Cleared" accessing restricted route -> Access granted (200 OK).
    - [ ] "Student Not Cleared" accessing `student.finance.clearance_required` -> Access granted.
- [ ] **Security**:
    - [ ] Direct URL access is blocked.
    - [ ] Query param bypass is impossible (server-side check).

## 5. User Interface
- [ ] **Student Dashboard**:
    - [ ] "Fee Clearance Status" widget shows correct status (CLEARED/NOT CLEARED).
    - [ ] "View Details" link works.
- [ ] **Clearance Required Page**:
    - [ ] Shows outstanding balance.
    - [ ] Explains why access is blocked.
    - [ ] Shows contact info for finance.
- [ ] **Admin/Finance View**:
    - [ ] List of students with clearance status.
    - [ ] "Grant Override" button works (opens modal, saves to DB).
    - [ ] "Revoke Override" button works.
    - [ ] Status indicator shows "Overridden" when applicable.

## 6. Security & Permissions
- [ ] **Authorization**:
    - [ ] Student can only see their own clearance status.
    - [ ] Admin/Accountant can see all students.
    - [ ] Only Admin can grant overrides.
- [ ] **Audit**:
    - [ ] Overrides record `granted_by` and `created_at`.
    - [ ] Revocations record `revoked_by` and `revoked_at`.

## 7. Verification Steps (Manual)
1. Login as `student.notcleared@kibihas.ac.tz`. Try to access Results. Expect Redirect.
2. Login as `accountant@kibihas.ac.tz`. Post payment for that student.
3. Login as `student.notcleared` again. Try to access Results. Expect Success.
4. Login as `accountant`. Reverse payment.
5. Login as `student.notcleared`. Try access. Expect Redirect.
6. Login as `admin`. Grant override.
7. Login as `student.notcleared`. Try access. Expect Success.
