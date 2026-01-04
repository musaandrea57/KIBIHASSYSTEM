<x-public-layout>
    <div class="bg-primary-900 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-3xl md:text-4xl font-serif font-bold text-white">Admissions</h1>
            <p class="text-gray-300 mt-2">Join our academic community</p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
            <div class="lg:col-span-2">
                <h2 class="text-2xl font-bold text-primary-900 mb-6">Entry Requirements</h2>
                <div class="space-y-6">
                    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                        <h3 class="text-lg font-bold text-primary-700 mb-2">Diploma in Nursing and Midwifery</h3>
                        <p class="text-gray-600 mb-2">Holders of Certificate of Secondary Education Examination (CSEE) with four (4) Passes in non-religious subjects including "D" Passes in Chemistry, Biology and Physics/Engineering Sciences.</p>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                        <h3 class="text-lg font-bold text-primary-700 mb-2">Diploma in Clinical Dentistry</h3>
                        <p class="text-gray-600 mb-2">Holders of Certificate of Secondary Education Examination (CSEE) with four (4) Passes in non-religious subjects including "D" Passes in Chemistry, Biology and Physics/Engineering Sciences.</p>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                        <h3 class="text-lg font-bold text-primary-700 mb-2">Diploma in Diagnostic Radiography</h3>
                        <p class="text-gray-600 mb-2">Holders of Certificate of Secondary Education Examination (CSEE) with four (4) Passes in non-religious subjects including "D" Passes in Chemistry, Biology and Physics/Engineering Sciences.</p>
                    </div>
                </div>

                <h2 class="text-2xl font-bold text-primary-900 mt-12 mb-6">Application Procedure</h2>
                <ol class="list-decimal list-inside space-y-4 text-gray-700">
                    <li class="pl-2">Click the "Apply Now" button below or on the home page.</li>
                    <li class="pl-2">Fill in your personal details and education background.</li>
                    <li class="pl-2">Upload scanned copies of your academic certificates and passport photo.</li>
                    <li class="pl-2">Submit your application. You will receive an Application Number.</li>
                    <li class="pl-2">Use the "Check Status" feature to track your application.</li>
                </ol>

                <div class="mt-8">
                    <a href="{{ route('application.create') }}" class="inline-block bg-secondary text-primary-900 font-bold py-3 px-8 rounded shadow hover:bg-secondary-600 transition">Start Online Application</a>
                </div>
            </div>

            <div class="lg:col-span-1">
                <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
                    <h3 class="text-lg font-bold text-primary-900 mb-4">Downloads</h3>
                    @php
                        $documents = \App\Models\Downloadable::where('category', 'admission')->orWhere('category', 'general')->latest()->take(5)->get();
                    @endphp
                    <ul class="space-y-3">
                        @forelse($documents as $doc)
                            <li>
                                <a href="{{ Storage::url($doc->file_path) }}" target="_blank" class="flex items-center text-primary-600 hover:text-primary-800">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    {{ $doc->title }}
                                </a>
                            </li>
                        @empty
                             <li class="text-sm text-gray-500">No documents available.</li>
                        @endforelse
                    </ul>
                </div>

                <div class="bg-primary-50 p-6 rounded-lg border border-primary-100 mt-8">
                    <h3 class="text-lg font-bold text-primary-900 mb-4">Need Help?</h3>
                    <p class="text-sm text-gray-600 mb-4">Contact our admissions office for assistance.</p>
                    <p class="font-bold text-primary-800">+255 123 456 789</p>
                    <p class="text-primary-800">admission@kibihas.ac.tz</p>
                </div>
            </div>
        </div>
    </div>
</x-public-layout>
