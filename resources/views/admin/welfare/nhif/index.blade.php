@extends('layouts.portal')

@section('content')
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">{{ __('NHIF Management') }}</h2>
            <p class="text-gray-600">Manage student health insurance memberships.</p>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500">
            <div class="text-sm font-bold text-gray-500 uppercase">Active</div>
            <div class="text-2xl font-bold text-green-700">{{ $stats['active'] }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-red-500">
            <div class="text-sm font-bold text-gray-500 uppercase">Expired</div>
            <div class="text-2xl font-bold text-red-700">{{ $stats['expired'] }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-yellow-500">
            <div class="text-sm font-bold text-gray-500 uppercase">Pending Verification</div>
            <div class="text-2xl font-bold text-yellow-700">{{ $stats['pending'] }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-orange-500">
            <div class="text-sm font-bold text-gray-500 uppercase">Expiring Soon (30 Days)</div>
            <div class="text-2xl font-bold text-orange-700">{{ $stats['expiring_soon'] }}</div>
        </div>
    </div>

    <!-- Search & Filters -->
    <div class="bg-white rounded-lg shadow mb-6 p-4">
        <form method="GET" action="{{ route('admin.welfare.nhif.index') }}" class="flex gap-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, reg no, or NHIF no..." class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
            <select name="status" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                <option value="">All Statuses</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                <option value="pending_verification" {{ request('status') == 'pending_verification' ? 'selected' : '' }}>Pending</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded hover:bg-gray-700">Filter</button>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NHIF Number</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expiry</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($memberships as $m)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $m->student->user->name }}</div>
                            <div class="text-xs text-gray-500">{{ $m->student->admission_number }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-mono">{{ $m->nhif_number }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-{{ $m->status_badge }}-100 text-{{ $m->status_badge }}-800">
                                {{ ucfirst(str_replace('_', ' ', $m->status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $m->expiry_date ? $m->expiry_date->format('d M Y') : 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium flex gap-2">
                            @if(Auth::user()->can('manage_nhif'))
                                <form action="{{ route('admin.welfare.nhif.verify', $m->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-indigo-600 hover:text-indigo-900">Verify</button>
                                </form>
                                <button onclick="openEditModal({{ $m->id }}, '{{ $m->status }}', '{{ $m->expiry_date ? $m->expiry_date->format('Y-m-d') : '' }}', '{{ $m->notes }}')" class="text-gray-600 hover:text-gray-900">Edit</button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">No records found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4">
            {{ $memberships->links() }}
        </div>
    </div>

    <!-- Edit Modal (Simple JS implementation) -->
    <div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full" style="display: none;">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Edit Membership</h3>
                <form id="editForm" method="POST" class="mt-2 text-left">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Status</label>
                        <select name="status" id="editStatus" class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="expired">Expired</option>
                            <option value="pending_verification">Pending Verification</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Expiry Date</label>
                        <input type="date" name="expiry_date" id="editExpiry" class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Notes</label>
                        <textarea name="notes" id="editNotes" class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
                    </div>

                    <div class="flex items-center justify-between mt-4">
                        <button type="button" onclick="document.getElementById('editModal').style.display='none'" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Cancel</button>
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openEditModal(id, status, expiry, notes) {
            document.getElementById('editModal').style.display = 'block';
            document.getElementById('editForm').action = "/admin/welfare/nhif/" + id;
            document.getElementById('editStatus').value = status;
            document.getElementById('editExpiry').value = expiry;
            document.getElementById('editNotes').value = notes;
        }
    </script>

@endsection
