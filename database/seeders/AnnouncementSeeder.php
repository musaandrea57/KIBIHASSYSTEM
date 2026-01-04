<?php

namespace Database\Seeders;

use App\Models\Announcement;
use Illuminate\Database\Seeder;

class AnnouncementSeeder extends Seeder
{
    public function run(): void
    {
        Announcement::create([
            'title' => 'Applications for Academic Year 2025/2026 Now Open',
            'summary' => 'KIBIHAS invites applications from qualified candidates for admission into various Diploma programs.',
            'content' => 'Kibosho Institute of Health and Allied Sciences (KIBIHAS) is pleased to announce that the admission window for the academic year 2025/2026 is now open. We offer Diploma programs in Nursing and Midwifery, Clinical Dentistry, and Diagnostic Radiography. Interested applicants can apply online through our admission portal.',
            'published_at' => now(),
            'is_published' => true,
        ]);

        Announcement::create([
            'title' => 'Orientation Week Schedule',
            'summary' => 'Orientation for new students will begin on October 1st, 2025.',
            'content' => 'All new students are required to report to the campus on October 1st for the orientation week. Activities will include campus tours, registration, and introduction to academic life at KIBIHAS.',
            'published_at' => now()->subDays(5),
            'is_published' => true,
        ]);

        Announcement::create([
            'title' => 'Examination Results Released',
            'summary' => 'End of Semester 1 examination results have been released.',
            'content' => 'The examination results for Semester 1 of the 2024/2025 academic year have been officially released. Students can view their results by logging into the Student Information Management System (SIMS).',
            'published_at' => now()->subDays(10),
            'is_published' => true,
        ]);
    }
}
