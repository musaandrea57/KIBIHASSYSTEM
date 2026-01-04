<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Portal Sidebar Navigation
    |--------------------------------------------------------------------------
    |
    | This file defines the sidebar menu structure for the KIBIHAS portal.
    | It supports role-based visibility, permissions, and collapsible groups.
    |
    */

    'menu' => [
        'admin' => [
            [
                'heading' => 'Dashboard',
                'items' => [
                    [
                        'label' => 'Dashboard',
                        'route' => 'admin.dashboard',
                        'icon' => 'home',
                    ],
                ],
            ],
            [
                'heading' => 'ADMINISTRATION',
                'items' => [
                    [
                        'label' => 'User Management',
                        'icon' => 'users',
                        'children' => [
                            ['label' => 'Teachers', 'route' => 'admin.teachers.index'],
                            ['label' => 'Students', 'route' => 'admin.students.index'],
                            // Hidden until routes exist: Admins, Academic Staff, Accountants
                        ],
                    ],
                    [
                        'label' => 'Departments',
                        'route' => 'admin.departments.index',
                        'icon' => 'building-office',
                    ]
                ],
            ],
            [
                'heading' => 'ACADEMIC',
                'items' => [
                    [
                        'label' => 'Academic Setup',
                        'icon' => 'academic-cap',
                        'children' => [
                            ['label' => 'Programmes', 'route' => 'academic-setup.programs.index'],
                            ['label' => 'Academic Years', 'route' => 'academic-setup.academic-years.index'],
                            ['label' => 'Semesters', 'route' => 'academic-setup.semesters.index'],
                            ['label' => 'Modules', 'route' => 'academic-setup.modules.index'],
                            ['label' => 'Module Offerings', 'route' => 'academic-setup.module-offerings.index'],
                        ],
                    ],
                    [
                        'label' => 'Admissions',
                        'route' => 'admin.admissions.index',
                        'icon' => 'user-plus',
                    ],
                    [
                        'label' => 'Results Management',
                        'route' => 'admin.results.index',
                        'icon' => 'clipboard-document-check',
                    ],
                    [
                        'label' => 'Assignments',
                        'route' => 'admin.assignments.index',
                        'icon' => 'clipboard-document-list',
                    ],
                    [
                        'label' => 'Evaluations',
                        'icon' => 'star',
                        'children' => [
                            ['label' => 'Templates', 'route' => 'admin.evaluation.templates.index'],
                            ['label' => 'Periods', 'route' => 'admin.evaluation.periods.index'],
                            ['label' => 'Reports', 'route' => 'admin.evaluation.reports.index'],
                        ],
                    ],
                ],
            ],
            [
                'heading' => 'FINANCE',
                'items' => [
                    [
                        'label' => 'Fee Structures',
                        'route' => 'admin.finance.fee-structures.index',
                        'icon' => 'banknotes',
                    ],
                    [
                        'label' => 'Invoices',
                        'route' => 'admin.finance.invoices.index',
                        'icon' => 'document-text',
                    ],
                    [
                        'label' => 'Payments',
                        'route' => 'admin.finance.payments.index',
                        'icon' => 'credit-card',
                    ],
                    [
                        'label' => 'Reports',
                        'route' => 'admin.finance.reports.index',
                        'icon' => 'chart-bar',
                    ],
                ],
            ],
            [
                'heading' => 'WELFARE',
                'items' => [
                    [
                        'label' => 'NHIF Management',
                        'route' => 'admin.welfare.nhif.index',
                        'icon' => 'heart',
                    ],
                    [
                        'label' => 'Hostel Management',
                        'route' => 'admin.welfare.hostels.index',
                        'icon' => 'home-modern',
                    ],
                    [
                        'label' => 'Reports',
                        'icon' => 'chart-bar',
                        'children' => [
                            ['label' => 'NHIF Compliance', 'route' => 'admin.welfare.reports.nhif'],
                            ['label' => 'Hostel Occupancy', 'route' => 'admin.welfare.reports.hostel'],
                        ],
                    ],
                ],
            ],
            [
                'heading' => 'SYSTEM',
                'items' => [
                    [
                        'label' => 'Integration Hub',
                        'route' => 'admin.integration.logs',
                        'icon' => 'server-stack',
                    ],
                    [
                        'label' => 'Downloadables',
                        'route' => 'admin.downloads.index',
                        'icon' => 'arrow-down-tray',
                    ],
                    [
                        'label' => 'Messages',
                        'route' => 'messages.index',
                        'icon' => 'chat-bubble-left-right',
                    ],
                    [
                        'label' => 'Bulk SMS',
                        'route' => 'admin.communication.sms.index',
                        'icon' => 'device-phone-mobile',
                    ],
                    // Hidden: Audit Logs, Settings
                ],
            ],
        ],

        'principal' => [
            [
                'heading' => 'Executive',
                'items' => [
                    [
                        'label' => 'Dashboard',
                        'route' => 'principal.dashboard',
                        'icon' => 'home',
                    ],
                    [
                        'label' => 'My Profile',
                        'route' => 'principal.profile',
                        'icon' => 'user',
                    ],
                ],
            ],
            [
                'heading' => 'Governance',
                'items' => [
                    [
                        'label' => 'Teacher Performance',
                        'route' => 'principal.teachers',
                        'icon' => 'users',
                    ],
                    [
                        'label' => 'Academic Reports',
                        'route' => 'principal.reports.index',
                        'icon' => 'chart-bar',
                    ],
                    [
                        'label' => 'Student Performance',
                        'route' => 'principal.student-performance.index',
                        'icon' => 'academic-cap',
                    ],
                ],
            ],
            [
                'heading' => 'Communication',
                'items' => [
                    [
                        'label' => 'Messages',
                        'route' => 'messages.index',
                        'icon' => 'envelope',
                    ],
                    [
                        'label' => 'Announcements',
                        'route' => 'principal.announcements',
                        'icon' => 'megaphone',
                    ],
                ],
            ],
        ],

        'academic_staff' => [
            [
                'heading' => 'Dashboard',
                'items' => [
                    [
                        'label' => 'Dashboard',
                        'route' => 'dashboard', 
                        'icon' => 'home',
                    ],
                ],
            ],
            [
                'heading' => 'ACADEMIC',
                'items' => [
                    [
                        'label' => 'Academic Setup',
                        'icon' => 'academic-cap',
                        'children' => [
                            ['label' => 'Programmes', 'route' => 'academic-setup.programs.index'],
                            ['label' => 'Modules', 'route' => 'academic-setup.modules.index'],
                        ],
                    ],
                    // Hidden: Teacher Assignments, Student Registrations, Timetables, Reports
                ],
            ],
            [
                'heading' => 'COMMUNICATION',
                'items' => [
                    [
                        'label' => 'Messages',
                        'route' => 'messages.index',
                        'icon' => 'chat-bubble-left-right',
                    ],
                ]
            ]
        ],

        'teacher' => [
            [
                'heading' => 'Dashboard',
                'items' => [
                    [
                        'label' => 'Dashboard',
                        'route' => 'teacher.dashboard',
                        'icon' => 'home',
                    ],
                ],
            ],
            [
                'heading' => 'ACADEMIC',
                'items' => [
                    [
                        'label' => 'Marks Entry',
                        'route' => 'teacher.marks.index',
                        'icon' => 'pencil-square',
                    ],
                ]
            ],
            [
                'heading' => 'COMMUNICATION',
                'items' => [
                    [
                        'label' => 'Messages',
                        'route' => 'messages.index',
                        'icon' => 'chat-bubble-left-right',
                    ],
                ]
            ]
        ],

        'accountant' => [
            [
                'heading' => 'Dashboard',
                'items' => [
                    [
                        'label' => 'Dashboard',
                        'route' => 'accountant.dashboard',
                        'icon' => 'home',
                    ],
                ],
            ],
            [
                'heading' => 'FINANCE',
                'items' => [
                    [
                        'label' => 'Fee Structures',
                        'route' => 'admin.finance.fee-structures.index',
                        'icon' => 'banknotes',
                    ],
                    [
                        'label' => 'Invoices',
                        'route' => 'admin.finance.invoices.index',
                        'icon' => 'document-text',
                    ],
                    [
                        'label' => 'Payments',
                        'route' => 'admin.finance.payments.index',
                        'icon' => 'credit-card',
                    ],
                    [
                        'label' => 'Reports',
                        'route' => 'admin.finance.reports.index',
                        'icon' => 'chart-bar',
                    ],
                ],
            ],
            [
                'heading' => 'COMMUNICATION',
                'items' => [
                    [
                        'label' => 'Messages',
                        'route' => 'messages.index',
                        'icon' => 'chat-bubble-left-right',
                    ],
                ]
            ]
        ],

        'welfare' => [
            [
                'heading' => 'Dashboard',
                'items' => [
                    [
                        'label' => 'Dashboard',
                        'route' => 'dashboard',
                        'icon' => 'home',
                    ],
                ],
            ],
            [
                'heading' => 'WELFARE',
                'items' => [
                    [
                        'label' => 'NHIF Management',
                        'route' => 'admin.welfare.nhif.index',
                        'icon' => 'heart',
                    ],
                    [
                        'label' => 'Hostel Management',
                        'route' => 'admin.welfare.hostels.index',
                        'icon' => 'home-modern',
                    ],
                    [
                        'label' => 'Reports',
                        'icon' => 'chart-bar',
                        'children' => [
                            ['label' => 'NHIF Compliance', 'route' => 'admin.welfare.reports.nhif'],
                            ['label' => 'Hostel Occupancy', 'route' => 'admin.welfare.reports.hostel'],
                        ],
                    ],
                ],
            ],
            [
                'heading' => 'COMMUNICATION',
                'items' => [
                    [
                        'label' => 'Messages',
                        'route' => 'messages.index',
                        'icon' => 'chat-bubble-left-right',
                    ],
                ]
            ]
        ],

        'student' => [
            [
                'heading' => 'Dashboard',
                'items' => [
                    [
                        'label' => 'Dashboard',
                        'route' => 'student.dashboard',
                        'icon' => 'home',
                    ],
                ],
            ],
            [
                'heading' => 'ACADEMIC',
                'items' => [
                    [
                        'label' => 'Course Registration',
                        'route' => 'student.registration.index',
                        'icon' => 'book-open',
                    ],
                    [
                        'label' => 'Results Center',
                        'route' => 'student.results.index',
                        'icon' => 'academic-cap',
                    ],
                ]
            ],
            [
                'heading' => 'FINANCE',
                'items' => [
                    [
                        'label' => 'My Finances',
                        'route' => 'student.finance.index',
                        'icon' => 'currency-dollar',
                    ],
                ]
            ],
            [
                'heading' => 'WELFARE',
                'items' => [
                    [
                        'label' => 'NHIF Service',
                        'route' => 'student.nhif.index',
                        'icon' => 'heart',
                    ],
                    [
                        'label' => 'Hostel Service',
                        'route' => 'student.hostel.index',
                        'icon' => 'home-modern',
                    ],
                ]
            ],
            [
                'heading' => 'COMMUNICATION',
                'items' => [
                    [
                        'label' => 'Messages',
                        'route' => 'messages.index',
                        'icon' => 'chat-bubble-left-right',
                    ],
                ]
            ]
        ],

        'parent' => [
            [
                'heading' => 'Dashboard',
                'items' => [
                    [
                        'label' => 'Dashboard',
                        'route' => 'dashboard',
                        'icon' => 'home',
                    ],
                ],
            ],
             // Hidden: My Children, Academic Results, Payments Summary
            [
                'heading' => 'COMMUNICATION',
                'items' => [
                    [
                        'label' => 'Messages',
                        'route' => 'messages.index',
                        'icon' => 'chat-bubble-left-right',
                    ],
                ]
            ]
        ],
    ],
];
