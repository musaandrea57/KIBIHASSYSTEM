<x-public-layout>
<!-- Hero Section -->
<div class="relative bg-primary-800 py-20">
    <div class="absolute inset-0 overflow-hidden">
        <img src="https://images.unsplash.com/photo-1550751827-4bd374c3f58b?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80" class="w-full h-full object-cover opacity-20" alt="ICT Services">
    </div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl font-serif font-bold text-white mb-4">ICT Services</h1>
        <p class="text-xl text-gray-300 max-w-2xl mx-auto">Empowering education through technology. Access digital resources, support, and institutional systems.</p>
    </div>
</div>

<!-- Quick Links Section -->
<div class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Student Portal -->
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-8 text-center hover:shadow-lg transition duration-300">
                <div class="bg-primary-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-primary-800" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                </div>
                <h3 class="text-2xl font-bold text-primary-900 mb-2">Student Portal</h3>
                <p class="text-gray-600 mb-6">Access course results, registration, payments, and academic records.</p>
                <a href="{{ route('login') }}" class="inline-block bg-primary-700 text-white font-bold py-2 px-6 rounded hover:bg-primary-800 transition">Login</a>
            </div>

            <!-- Staff Portal -->
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-8 text-center hover:shadow-lg transition duration-300">
                <div class="bg-primary-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-primary-800" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <h3 class="text-2xl font-bold text-primary-900 mb-2">Staff Portal</h3>
                <p class="text-gray-600 mb-6">Manage student data, upload results, and access administrative tools.</p>
                <a href="{{ route('login') }}" class="inline-block bg-primary-700 text-white font-bold py-2 px-6 rounded hover:bg-primary-800 transition">Login</a>
            </div>

            <!-- E-Learning -->
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-8 text-center hover:shadow-lg transition duration-300">
                <div class="bg-primary-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-primary-800" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                </div>
                <h3 class="text-2xl font-bold text-primary-900 mb-2">E-Learning</h3>
                <p class="text-gray-600 mb-6">Access lecture notes, assignments, and online learning materials.</p>
                <a href="#" class="inline-block bg-gray-400 text-white font-bold py-2 px-6 rounded cursor-not-allowed" title="Coming Soon">Access LMS</a>
            </div>
        </div>
    </div>
</div>

<!-- Email & Support Section -->
<div class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            <!-- Institutional Email -->
            <div>
                <h2 class="text-2xl font-bold text-primary-900 mb-6">Institutional Email</h2>
                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                    <p class="text-gray-600 mb-4">
                        All students and staff are provided with an official KIBIHAS email address (e.g., name@kibihas.ac.tz). This email is required for all official communication.
                    </p>
                    <ul class="space-y-3 mb-6">
                        <li class="flex items-start">
                            <svg class="h-6 w-6 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span class="text-gray-700">Official communication channel</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="h-6 w-6 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span class="text-gray-700">Access to academic resources</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="h-6 w-6 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span class="text-gray-700">Password reset for portals</span>
                        </li>
                    </ul>
                    <a href="https://mail.google.com" target="_blank" class="text-secondary font-bold hover:underline">Go to Webmail &rarr;</a>
                </div>
            </div>

            <!-- Support Form -->
            <div>
                <h2 class="text-2xl font-bold text-primary-900 mb-6">IT Support Request</h2>
                <form action="#" method="POST" class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                    @csrf
                    <div class="mb-4">
                        <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Full Name</label>
                        <input type="text" id="name" name="name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent" placeholder="Your Name">
                    </div>
                    <div class="mb-4">
                        <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email Address</label>
                        <input type="email" id="email" name="email" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent" placeholder="your@email.com">
                    </div>
                    <div class="mb-4">
                        <label for="category" class="block text-gray-700 text-sm font-bold mb-2">Issue Category</label>
                        <select id="category" name="category" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                            <option>Login Issue</option>
                            <option>Email Access</option>
                            <option>Network/Wi-Fi</option>
                            <option>Software/Hardware</option>
                            <option>Other</option>
                        </select>
                    </div>
                    <div class="mb-6">
                        <label for="message" class="block text-gray-700 text-sm font-bold mb-2">Message</label>
                        <textarea id="message" name="message" rows="4" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent" placeholder="Describe your issue..."></textarea>
                    </div>
                    <div class="flex items-center justify-end">
                        <button type="button" class="bg-primary-700 hover:bg-primary-800 text-white font-bold py-2 px-6 rounded focus:outline-none focus:shadow-outline transition" onclick="alert('Support request simulation: Your ticket has been logged.')">
                            Submit Ticket
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</x-public-layout>