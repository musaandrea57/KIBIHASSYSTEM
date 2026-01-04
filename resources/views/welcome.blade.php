<x-public-layout>
    <!-- Hero Slider -->
    <div class="relative bg-primary-800 h-[500px]" x-data="{ activeSlide: 1, slides: [1, 2, 3], timer: null }" x-init="timer = setInterval(() => { activeSlide = activeSlide === 3 ? 1 : activeSlide + 1 }, 5000)">
        <!-- Slide 1 -->
        <div class="absolute inset-0 bg-cover bg-center transition-opacity duration-1000"
             x-show="activeSlide === 1"
             x-transition:enter="transition ease-out duration-1000"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-1000"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             style="background-image: url('https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80');">
        </div>
        
        <!-- Slide 2 -->
        <div class="absolute inset-0 bg-cover bg-center transition-opacity duration-1000"
             x-show="activeSlide === 2"
             x-transition:enter="transition ease-out duration-1000"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-1000"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             style="background-image: url('https://images.unsplash.com/photo-1505751172876-fa1923c5c528?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80');">
        </div>

        <!-- Slide 3 -->
        <div class="absolute inset-0 bg-cover bg-center transition-opacity duration-1000"
             x-show="activeSlide === 3"
             x-transition:enter="transition ease-out duration-1000"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-1000"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             style="background-image: url('https://images.unsplash.com/photo-1551076805-e1869033e561?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80');">
        </div>

        <div class="absolute inset-0 bg-primary-900/60"></div>
        
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full flex items-center">
            <div class="text-white max-w-3xl">
                <h2 class="text-secondary font-bold text-xl mb-2 tracking-wide uppercase">Welcome to KIBIHAS</h2>
                <h1 class="text-4xl md:text-6xl font-serif font-bold leading-tight mb-6">Excellence in Health Science Education</h1>
                <p class="text-lg md:text-xl text-gray-200 mb-8">Empowering the next generation of healthcare professionals with knowledge, skills, and integrity.</p>
                <div class="flex space-x-4">
                    <a href="{{ route('application.register') }}" class="bg-secondary text-primary-900 font-bold py-3 px-8 rounded hover:bg-white transition duration-300">Apply Now</a>
                    <a href="{{ route('about') }}" class="border-2 border-white text-white font-bold py-3 px-8 rounded hover:bg-white hover:text-primary-900 transition duration-300">Learn More</a>
                </div>
            </div>
        </div>

        <!-- Indicators -->
        <div class="absolute bottom-5 left-0 right-0 flex justify-center space-x-2">
            <template x-for="slide in slides" :key="slide">
                <button @click="activeSlide = slide; clearInterval(timer); timer = setInterval(() => { activeSlide = activeSlide === 3 ? 1 : activeSlide + 1 }, 5000)" 
                    class="w-3 h-3 rounded-full transition-colors duration-300"
                    :class="activeSlide === slide ? 'bg-secondary' : 'bg-white/50 hover:bg-white'">
                </button>
            </template>
        </div>
    </div>

    <!-- Announcements -->
    <div class="bg-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-end mb-8">
                <div>
                    <h2 class="text-3xl font-bold text-primary-900 font-serif">Latest News & Announcements</h2>
                    <div class="h-1 w-20 bg-secondary mt-2"></div>
                </div>
                <a href="#" class="text-primary-600 font-semibold hover:text-primary-800">View All News &rarr;</a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @forelse($announcements as $announcement)
                    <div class="bg-gray-50 border border-gray-100 rounded-lg overflow-hidden shadow-sm hover:shadow-md transition">
                        <div class="p-6">
                            <span class="text-xs font-semibold text-secondary-600 uppercase tracking-wider">{{ $announcement->published_at->format('M d, Y') }}</span>
                            <h3 class="text-xl font-bold text-gray-900 mt-2 mb-3"><a href="#" class="hover:text-primary-600">{{ $announcement->title }}</a></h3>
                            <p class="text-gray-600 mb-4 line-clamp-3">{{ $announcement->summary ?? Str::limit($announcement->content, 100) }}</p>
                            <a href="#" class="text-primary-600 font-semibold text-sm hover:underline">Read More</a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 text-center text-gray-500 py-8">No recent announcements.</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Programs -->
    <div class="bg-gray-50 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-primary-900 font-serif">Academic Programs</h2>
                <div class="h-1 w-20 bg-secondary mx-auto mt-2"></div>
                <p class="mt-4 text-gray-600 max-w-2xl mx-auto">We offer NACTVET accredited diploma programs designed to equip students with practical skills and theoretical knowledge.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach($programs as $program)
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:-translate-y-1 transition duration-300">
                    <div class="h-48 bg-primary-100 flex items-center justify-center">
                        <svg class="h-16 w-16 text-primary-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-primary-900 mb-2">{{ $program->name }}</h3>
                        <p class="text-sm text-gray-500 mb-4">Duration: {{ $program->duration_years }} Years | Level: Diploma (NTA 4-6)</p>
                        <a href="{{ route('application.register') }}?program={{ $program->id }}" class="block w-full text-center bg-primary-600 text-white py-2 rounded hover:bg-primary-700 transition">Apply Now</a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- CTA Strip -->
    <div class="bg-secondary py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between">
            <div class="mb-6 md:mb-0">
                <h2 class="text-2xl font-bold text-primary-900 mb-2">Ready to start your journey?</h2>
                <p class="text-primary-800">Applications for the {{ date('Y') }}/{{ date('Y')+1 }} academic year are now open.</p>
            </div>
            <a href="{{ route('application.register') }}" class="bg-primary-900 text-white font-bold py-3 px-8 rounded shadow-lg hover:bg-primary-800 transition">Apply Online</a>
        </div>
    </div>
</x-public-layout>
