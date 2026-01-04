<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Roles
        $roles = [
            'admin',
            'principal',
            'academic_staff',
            'accountant',
            'teacher',
            'student',
            'parent',
            'applicant'
        ];

        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }
        
        // Define Permissions (Basic set, can be expanded)
        $permissions = [
            'view_dashboard',
            'manage_users',
            'manage_finance',
            'view_finance',
            'manage_academics',
            'view_academics',
            'manage_results',
            'view_results',
            'publish_results',
            'send_messages',
            'view_reports',
            'upload_documents',
            // Module 2 Permissions
            'manage_departments',
            'manage_teachers',
            'assign_teachers',
            'view_own_assignments',
            // Module 3 Permissions
            'view_integration_logs',
            // Module 4 Permissions (Finance)
            'manage_fee_structures',
            'view_fee_structures',
            'create_invoice',
            'view_invoices',
            'record_payments',
            'reverse_payments',
            'view_finance_reports',
            'download_receipts',
            'view_own_finance',
            // Fee Clearance
            'manage_fee_clearance_overrides',
            'view_fee_clearance_status',
        ];

        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName]);
        }

        // Assign Permissions to Roles
        $adminRole = Role::findByName('admin');
        $adminRole->givePermissionTo(Permission::all());

        $principalRole = Role::findByName('principal');
        $principalRole->givePermissionTo([
            'view_dashboard', 'view_finance', 'view_academics', 'view_results', 'view_reports', 'send_messages',
            'view_fee_structures', 'view_invoices', 'view_finance_reports', 'view_fee_clearance_status'
        ]);

        $academicStaffRole = Role::findByName('academic_staff');
        $academicStaffRole->givePermissionTo([
            'view_dashboard', 'manage_academics', 'view_results', 'send_messages', 'upload_documents',
            'assign_teachers', 'view_academics', 'view_integration_logs'
        ]);

        $accountantRole = Role::findByName('accountant');
        $accountantRole->givePermissionTo([
            'view_dashboard', 'manage_finance', 'send_messages', 'view_reports',
            'manage_fee_structures', 'view_fee_structures', 'create_invoice', 'view_invoices', 
            'record_payments', 'reverse_payments', 'view_finance_reports', 'download_receipts',
            'view_fee_clearance_status'
        ]);

        $teacherRole = Role::findByName('teacher');
        $teacherRole->givePermissionTo([
            'view_dashboard', 'view_academics', 'manage_results', 'send_messages',
            'view_own_assignments'
        ]);

        $studentRole = Role::findByName('student');
        $studentRole->givePermissionTo([
            'view_dashboard', 'view_academics', 'view_results', // Restricted by middleware
            'view_own_finance', 'download_receipts'
        ]);

        $parentRole = Role::findByName('parent');
        $parentRole->givePermissionTo([
            'view_dashboard', 'view_results' // Restricted by middleware
        ]);
    }
}
