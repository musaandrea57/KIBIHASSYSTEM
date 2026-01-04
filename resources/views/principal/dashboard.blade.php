@extends('layouts.portal')

@section('content')
<div class="space-y-6">
    <!-- A) Executive Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Principal Dashboard</h1>
            <p class="text-sm text-slate-500 mt-1">Executive Overview & Governance</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            @if($active_year && $active_semester)
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">
                <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                {{ $active_year->year }} - {{ $active_semester->name }}
            </span>
            @endif
            
            <div class="flex items-center bg-gray-50 rounded-lg p-1 border border-gray-200">
                <button class="px-3 py-1 text-xs font-medium text-slate-700 bg-white rounded shadow-sm">This Week</button>
                <button class="px-3 py-1 text-xs font-medium text-slate-500 hover:text-slate-700">Month</button>
                <button class="px-3 py-1 text-xs font-medium text-slate-500 hover:text-slate-700">Semester</button>
            </div>
            
            <div class="text-xs text-slate-400 font-mono">
                Updated: {{ $last_updated->format('H:i') }}
            </div>
        </div>
    </div>

    <!-- B) Key KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
        <!-- Total Students -->
        <x-kpi-card 
            label="Total Students" 
            value="{{ number_format($kpis['total_students']) }}" 
            icon="users" 
            color="blue"
            trend="active"
        />

        <!-- Total Teachers -->
        <x-kpi-card 
            label="Total Teachers" 
            value="{{ number_format($kpis['total_teachers']) }}" 
            icon="academic-cap" 
            color="indigo"
        />

        <!-- Attendance Rate -->
        <x-kpi-card 
            label="Attendance Rate" 
            value="{{ $kpis['attendance_rate'] }}%" 
            icon="clock" 
            color="emerald"
            trend="neutral"
        />

        <!-- Results Status -->
        <x-kpi-card 
            label="Published Results" 
            value="{{ $kpis['results_published'] }}/{{ $kpis['total_offerings'] }}" 
            icon="clipboard-check" 
            color="violet"
        />

        <!-- Fee Clearance -->
        <x-kpi-card 
            label="Fee Cleared" 
            value="{{ $kpis['fee_clearance']['percentage'] }}%" 
            subtext="{{ $kpis['fee_clearance']['cleared'] }} / {{ $kpis['fee_clearance']['total'] }}"
            icon="currency-dollar" 
            color="{{ $kpis['fee_clearance']['percentage'] < 70 ? 'amber' : 'green' }}"
        />

        <!-- Admissions -->
        <x-kpi-card 
            label="Admissions" 
            value="{{ $kpis['admissions']['approved'] }}" 
            subtext="{{ $kpis['admissions']['applications'] }} Applied"
            icon="user-plus" 
            color="cyan"
        />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- C) Governance Alerts -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden flex flex-col h-full">
            <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-red-50/50">
                <h3 class="font-bold text-slate-800 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    Governance Alerts
                </h3>
                <span class="bg-red-100 text-red-700 text-xs font-bold px-2 py-0.5 rounded-full">{{ count($alerts) }}</span>
            </div>
            <div class="p-0 overflow-y-auto max-h-[400px]">
                @if(count($alerts) > 0)
                    <div class="divide-y divide-gray-100">
                        @foreach($alerts as $alert)
                        <div class="p-4 hover:bg-gray-50 transition-colors border-l-4 {{ $alert['severity'] === 'critical' ? 'border-red-500' : ($alert['severity'] === 'warning' ? 'border-amber-500' : 'border-blue-500') }}">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h4 class="text-sm font-semibold text-slate-800">{{ $alert['title'] ?? 'Alert' }}</h4>
                                    <p class="text-xs text-slate-600 mt-1">{{ $alert['message'] }}</p>
                                </div>
                                <a href="{{ $alert['action_url'] }}" class="text-xs font-medium text-blue-600 hover:text-blue-800">Review &rarr;</a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-8 text-center text-slate-500">
                        <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <p>No critical governance alerts.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- D) Performance Overview (Placeholder Charts) -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h3 class="font-bold text-slate-800 mb-4">Student Performance Trend</h3>
                <!-- Simple CSS Bar Chart Placeholder -->
                <div class="h-64 flex items-end justify-between gap-2 border-b border-gray-200 pb-2">
                    <!-- Fake data bars -->
                    @foreach(['NTA 4', 'NTA 5', 'NTA 6', 'Diploma', 'Cert'] as $level)
                    <div class="flex flex-col items-center flex-1 group">
                        <div class="w-full max-w-[40px] bg-blue-100 rounded-t-md relative group-hover:bg-blue-200 transition-all overflow-hidden">
                            @php $height = rand(40, 95); @endphp
                            <div class="absolute bottom-0 left-0 right-0 bg-blue-600 transition-all duration-1000" style="height: {{ $height }}%"></div>
                            <div class="h-full w-full opacity-0">.</div>
                        </div>
                        <span class="text-xs text-slate-500 mt-2 font-medium">{{ $level }}</span>
                        <span class="text-xs font-bold text-slate-800">{{ $height }}%</span>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                 <!-- Teacher Workload -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <h3 class="font-bold text-slate-800 mb-2">Teacher Workload</h3>
                    <p class="text-xs text-slate-500 mb-4">Sessions Delivered vs Planned</p>
                    <div class="space-y-3">
                         <div class="flex items-center justify-between text-sm">
                            <span class="text-slate-600">Department A</span>
                            <span class="font-semibold text-slate-900">92%</span>
                         </div>
                         <div class="w-full bg-gray-100 rounded-full h-2">
                            <div class="bg-indigo-500 h-2 rounded-full" style="width: 92%"></div>
                         </div>
                         
                         <div class="flex items-center justify-between text-sm">
                            <span class="text-slate-600">Department B</span>
                            <span class="font-semibold text-slate-900">78%</span>
                         </div>
                         <div class="w-full bg-gray-100 rounded-full h-2">
                            <div class="bg-amber-500 h-2 rounded-full" style="width: 78%"></div>
                         </div>

                         <div class="flex items-center justify-between text-sm">
                            <span class="text-slate-600">Department C</span>
                            <span class="font-semibold text-slate-900">85%</span>
                         </div>
                         <div class="w-full bg-gray-100 rounded-full h-2">
                            <div class="bg-blue-500 h-2 rounded-full" style="width: 85%"></div>
                         </div>
                    </div>
                </div>

                <!-- Finance Overview -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <h3 class="font-bold text-slate-800 mb-2">Finance Overview</h3>
                    <p class="text-xs text-slate-500 mb-4">Clearance by Programme</p>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center p-3 bg-green-50 rounded-lg">
                            <span class="text-sm font-medium text-green-900">Fully Cleared</span>
                            <span class="text-lg font-bold text-green-700">{{ $kpis['fee_clearance']['percentage'] }}%</span>
                        </div>
                         <div class="flex justify-between items-center p-3 bg-red-50 rounded-lg">
                            <span class="text-sm font-medium text-red-900">Outstanding</span>
                            <span class="text-lg font-bold text-red-700">{{ 100 - $kpis['fee_clearance']['percentage'] }}%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- E) Recent Activity & Audit Snapshot -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h3 class="font-bold text-slate-800">Recent Audit Activity</h3>
            <button class="text-sm text-blue-600 hover:text-blue-800 font-medium">View Full Log</button>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Details</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($activity as $log)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">{{ $log['time'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $log['user'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                {{ $log['event'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 truncate max-w-xs">{{ Str::limit($log['details'], 50) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-gray-500 text-sm">No recent activity logged.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- F) Quick Reports & G) Shortcuts -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Quick Report Tiles -->
        <x-report-tile title="Academic Performance" icon="chart-bar" color="blue" />
        <x-report-tile title="Teacher Attendance" icon="user-group" color="indigo" />
        <x-report-tile title="Fee Clearance" icon="banknotes" color="emerald" />
        
        <!-- Messaging Shortcut -->
        <div class="bg-gradient-to-br from-slate-800 to-slate-900 rounded-xl p-4 text-white shadow-lg flex flex-col justify-between">
            <div>
                <h4 class="font-bold text-lg mb-1">Communications</h4>
                <p class="text-slate-300 text-xs">0 Unread Messages</p>
            </div>
            <div class="mt-4 flex gap-2">
                <a href="{{ route('messages.create') }}" class="flex-1 bg-white/10 hover:bg-white/20 text-center py-2 rounded text-sm font-medium transition-colors">Compose</a>
                <a href="{{ route('principal.announcements') }}" class="flex-1 bg-white/10 hover:bg-white/20 text-center py-2 rounded text-sm font-medium transition-colors">Announce</a>
            </div>
        </div>
    </div>
</div>
@endsection
