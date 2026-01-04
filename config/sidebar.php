<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Sidebar Navigation Structure
    |--------------------------------------------------------------------------
    |
    | This configuration defines the sidebar menu items for each role.
    | Icons should be Heroicons (outline) names or Lucide icons.
    |
    */

    'menu' => [
        'admin' => [
            [
                'label' => 'Dashboard',
                'route' => 'admin.dashboard',
                'icon' => 'home',
            ],
            
            // ADMINISTRATION
            ['header' => 'ADMINISTRATION'],
            [
                'label' => 'User Management',
                'icon' => 'users',
                'children' => [
                    ['label' => 'Admins', 'route' => '#', 'icon' => 'shield-check'], // Placeholder
                    ['label' => 'Academic Staff', 'route' => '#', 'icon' => 'academic-cap'], // Placeholder
                    ['label' => 'Teachers', 'route' => 'admin.teachers.index', 'icon' => 'user-group'],
                    ['label' => 'Accountants', 'route' => '#', 'icon' => 'calculator'], // Placeholder
                    ['label' => 'Students', 'route' => 'admin.students.index', 'icon' => 'user'],
                ],
            ],

            // ACADEMIC
            ['header' => 'ACADEMIC'],
            [
                'label' => 'Academic Setup',
                'icon' => 'book-open',
                'children' => [
                    ['label' => 'Programmes', 'route' => 'academic-setup.programs.index', 'icon' => 'bookmark'],
                    ['label' => 'Academic Years', 'route' => 'academic-setup.academic-years.index', 'icon' => 'calendar'],
                    ['label' => 'Semesters', 'route' => 'academic-setup.semesters.index', 'icon' => 'clock'],
                    ['label' => 'Modules', 'route' => 'academic-setup.modules.index', 'icon' => 'collection'],
                    ['label' => 'Module Offerings', 'route' => 'academic-setup.module-offerings.index', 'icon' => 'clipboard-list'],
                ],
            ],
            [
                'label' => 'Registrations',
                'icon' => 'clipboard-check',
                'children' => [
                    ['label' => 'Overview', 'route' => 'admin.registrations.index', 'icon' => 'view-list'],
                    ['label' => 'Rules', 'route' => 'admin.registrations.rules', 'icon' => 'cog'],
                    ['label' => 'Deadlines', 'route' => 'admin.registrations.deadlines', 'icon' => 'calendar'],
                ],
            ],
            [
                'label' => 'Teacher Assignments',
                'route' => 'admin.assignments.index',
                'icon' => 'clipboard-check',
            ],
            [
                'label' => 'Admissions',
                'route' => 'admin.admissions.index',
                'icon' => 'document-add',
            ],
            [
                'label' => 'Results Management',
                'icon' => 'chart-bar',
                'children' => [
                    ['label' => 'View Results', 'route' => 'admin.results.index', 'icon' => 'eye'],
                    ['label' => 'Upload Results', 'route' => 'admin.results.create', 'icon' => 'upload'],
                ],
            ],

            // FINANCE
            ['header' => 'FINANCE'],
            [
                'label' => 'Finance',
                'icon' => 'currency-dollar',
                'children' => [
                    ['label' => 'Fee Structures', 'route' => 'admin.finance.fee-structures.index', 'icon' => 'document-text'],
                    ['label' => 'Invoices', 'route' => 'admin.finance.invoices.index', 'icon' => 'document-duplicate'],
                    ['label' => 'Payments', 'route' => 'admin.finance.payments.index', 'icon' => 'cash'],
                    ['label' => 'Reports', 'route' => 'admin.finance.reports.index', 'icon' => 'chart-pie'],
                    ['label' => 'Fee Items', 'route' => 'admin.finance.fee-items.index', 'icon' => 'tag'],
                ],
            ],

            // SYSTEM
            ['header' => 'SYSTEM'],
            [
                'label' => 'Integration Hub',
                'route' => 'admin.integration.logs',
                'icon' => 'server',
            ],
             [
                'label' => 'Downloadables',
                'route' => 'admin.downloads.index',
                'icon' => 'download',
            ],
            [
                'label' => 'Messages',
                'route' => 'messages.index',
                'icon' => 'mail',
            ],
        ],
        'academic_staff' => [
            [
                'label' => 'Dashboard',
                'route' => 'academic_staff.dashboard',
                'icon' => 'home',
            ],
            [
                'label' => 'Academic Setup',
                'icon' => 'book-open',
                'children' => [
                    ['label' => 'Programmes', 'route' => 'academic-setup.programs.index', 'icon' => 'bookmark'],
                    ['label' => 'Academic Years', 'route' => 'academic-setup.academic-years.index', 'icon' => 'calendar'],
                    ['label' => 'Semesters', 'route' => 'academic-setup.semesters.index', 'icon' => 'clock'],
                    ['label' => 'Modules', 'route' => 'academic-setup.modules.index', 'icon' => 'collection'],
                     ['label' => 'Module Offerings', 'route' => 'academic-setup.module-offerings.index', 'icon' => 'clipboard-list'],
                ],
            ],
            [
                'label' => 'Messages',
                'route' => 'messages.index',
                'icon' => 'mail',
            ],
        ],
        'teacher' => [
            [
                'label' => 'Dashboard',
                'route' => 'teacher.dashboard',
                'icon' => 'home',
            ],
             [
                'label' => 'Messages',
                'route' => 'messages.index',
                'icon' => 'mail',
            ],
        ],
        'student' => [
            [
                'label' => 'Dashboard',
                'route' => 'student.dashboard',
                'icon' => 'home',
            ],
            [
                'label' => 'Course Registration',
                'route' => 'student.registration.index',
                'icon' => 'academic-cap',
            ],
            ['header' => 'FINANCE'],
            [
                'label' => 'Finance Overview',
                'route' => 'student.finance.index',
                'icon' => 'chart-pie',
            ],
            [
                'label' => 'My Invoices',
                'route' => 'student.finance.invoices',
                'icon' => 'document-text',
            ],
            [
                'label' => 'My Payments',
                'route' => 'student.finance.payments',
                'icon' => 'cash',
            ],
            ['header' => 'SETTINGS'],
            [
                'label' => 'My Profile',
                'route' => 'profile.edit',
                'icon' => 'user-circle',
            ],
            [
                'label' => 'Messages',
                'route' => 'messages.index',
                'icon' => 'mail',
            ],
        ],
        'applicant' => [
             [
                'label' => 'Application Status',
                'route' => 'application.status',
                'icon' => 'clipboard-check',
            ],
             [
                'label' => 'Messages',
                'route' => 'messages.index',
                'icon' => 'mail',
            ],
        ],
        'parent' => [
            [
                'label' => 'Dashboard',
                'route' => 'parent.dashboard',
                'icon' => 'home',
            ],
             [
                'label' => 'Messages',
                'route' => 'messages.index',
                'icon' => 'mail',
            ],
        ],
        'principal' => [
            [
                'label' => 'Dashboard',
                'route' => 'principal.dashboard',
                'icon' => 'home',
            ],
            ['header' => 'FINANCE OVERVIEW'],
            [
                'label' => 'Finance Reports',
                'route' => 'admin.finance.reports.index',
                'icon' => 'chart-pie',
            ],
            [
                'label' => 'Messages',
                'route' => 'messages.index',
                'icon' => 'mail',
            ],
        ],
         'accountant' => [
            [
                'label' => 'Dashboard',
                'route' => 'accountant.dashboard',
                'icon' => 'home',
            ],
            ['header' => 'FINANCE OPERATIONS'],
            [
                'label' => 'Record Payment',
                'route' => 'admin.finance.payments.create',
                'icon' => 'plus-circle',
            ],
            [
                'label' => 'Payments',
                'route' => 'admin.finance.payments.index',
                'icon' => 'cash',
            ],
            [
                'label' => 'Invoices',
                'route' => 'admin.finance.invoices.index',
                'icon' => 'document-duplicate',
            ],
            [
                'label' => 'Reports',
                'route' => 'admin.finance.reports.index',
                'icon' => 'chart-pie',
            ],
            [
                'label' => 'Messages',
                'route' => 'messages.index',
                'icon' => 'mail',
            ],
        ],
    ],
];
