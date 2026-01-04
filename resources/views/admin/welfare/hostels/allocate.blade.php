@extends('layouts.portal')

@section('content')
    <div class="mb-6">
        <div class="flex items-center gap-2 mb-1">
            <a href="{{ route('admin.welfare.hostels.allocations') }}" class="text-indigo-600 hover:underline">&larr; Back</a>
        </div>
        <h2 class="text-2xl font-bold text-gray-800">{{ __('New Hostel Allocation') }}</h2>
    </div>

    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6">{{ session('error') }}</div>
    @endif

    <div class="bg-white rounded-lg shadow p-6 max-w-2xl">
        <form action="{{ route('admin.welfare.hostels.store-allocation') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Academic Session -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Academic Year</label>
                    <select name="academic_year_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        @foreach($academicYears as $year)
                            <option value="{{ $year->id }}">{{ $year->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Semester</label>
                    <select name="semester_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        @foreach($semesters as $sem)
                            <option value="{{ $sem->id }}">{{ $sem->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Student</label>
                <!-- Simple Select for now, ideally Select2/Autocomplete -->
                <select name="student_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <option value="">Select Student...</option>
                    @foreach(\App\Models\Student::take(50)->get() as $student)
                        <option value="{{ $student->id }}">{{ $student->user->name }} ({{ $student->admission_number }})</option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-500 mt-1">Showing first 50 students. Use search in real implementation.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Hostel</label>
                    <select name="hostel_id" id="hostel_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" onchange="loadRooms()">
                        <option value="">Select Hostel...</option>
                        @foreach($hostels as $hostel)
                            <option value="{{ $hostel->id }}">{{ $hostel->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Room</label>
                    <select name="room_id" id="room_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" disabled onchange="loadBeds()">
                        <option value="">Select Hostel First</option>
                    </select>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Bed</label>
                <select name="bed_id" id="bed_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" disabled>
                    <option value="">Select Room First</option>
                </select>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white font-bold rounded hover:bg-indigo-700">Allocate Bed</button>
            </div>
        </form>
    </div>

    <script>
        let availableRooms = [];

        function loadRooms() {
            const hostelId = document.getElementById('hostel_id').value;
            const roomSelect = document.getElementById('room_id');
            const bedSelect = document.getElementById('bed_id');
            
            roomSelect.innerHTML = '<option value="">Loading...</option>';
            roomSelect.disabled = true;
            bedSelect.innerHTML = '<option value="">Select Room First</option>';
            bedSelect.disabled = true;

            if (!hostelId) return;

            fetch(`{{ route('admin.welfare.hostels.available-beds') }}?hostel_id=${hostelId}`)
                .then(response => response.json())
                .then(data => {
                    availableRooms = data;
                    roomSelect.innerHTML = '<option value="">Select Room</option>';
                    data.forEach(room => {
                        roomSelect.innerHTML += `<option value="${room.id}">${room.number} (${room.available_beds.length} beds free)</option>`;
                    });
                    roomSelect.disabled = false;
                });
        }

        function loadBeds() {
            const roomId = document.getElementById('room_id').value;
            const bedSelect = document.getElementById('bed_id');
            
            bedSelect.innerHTML = '<option value="">Select Bed</option>';
            
            if (!roomId) {
                bedSelect.disabled = true;
                return;
            }

            const room = availableRooms.find(r => r.id == roomId);
            if (room) {
                room.available_beds.forEach(bed => {
                    bedSelect.innerHTML += `<option value="${bed.id}">${bed.label}</option>`;
                });
                bedSelect.disabled = false;
            }
        }
    </script>

@endsection
