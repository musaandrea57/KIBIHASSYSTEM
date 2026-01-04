@extends('layouts.portal')

@section('content')
    <div class="mb-6"><h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Integration Hub Logs') }}
        </h2></div>

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <!-- Manual Verification Tools -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                            <h4 class="font-bold text-blue-800 mb-2">NECTA Simulation</h4>
                            <form id="necta-form" class="space-y-3">
                                @csrf
                                <div>
                                    <input type="text" name="index_number" placeholder="Index No (S0000/0000/0000)" class="w-full text-sm rounded border-gray-300" value="S0101/0001/2020">
                                </div>
                                <div>
                                    <input type="number" name="year" placeholder="Year" class="w-full text-sm rounded border-gray-300" value="2020">
                                </div>
                                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold py-2 rounded">
                                    Verify Candidate
                                </button>
                            </form>
                            <pre id="necta-result" class="mt-2 text-xs bg-white p-2 rounded hidden overflow-auto max-h-40"></pre>
                        </div>

                        <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                            <h4 class="font-bold text-green-800 mb-2">NACTE Simulation</h4>
                            <form id="nacte-form" class="space-y-3">
                                @csrf
                                <div>
                                    <input type="text" name="avn" placeholder="AVN Number" class="w-full text-sm rounded border-gray-300" value="AVN-123456">
                                </div>
                                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white text-sm font-bold py-2 rounded">
                                    Check Eligibility
                                </button>
                            </form>
                            <pre id="nacte-result" class="mt-2 text-xs bg-white p-2 rounded hidden overflow-auto max-h-40"></pre>

                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium text-gray-900">Verification History</h3>
                        <span class="text-sm text-gray-500">Real-time logs from integration providers</span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Timestamp</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Provider</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IP Address</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Details</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($logs as $log)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $log->created_at->format('Y-m-d H:i:s') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $log->provider == 'NECTA' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                                {{ $log->provider }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $log->action }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $log->status == 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ ucfirst($log->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $log->ip_address }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <button onclick="alert('Request: {{ json_encode($log->request_data) }}\n\nResponse: {{ json_encode($log->response_data) }}')" class="text-primary-600 hover:text-primary-900">
                                                View Payload
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            No integration logs found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $logs->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('necta-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            const form = new FormData(this);
            const resEl = document.getElementById('necta-result');
            resEl.innerText = 'Verifying...';
            resEl.classList.remove('hidden');
            
            try {
                const response = await fetch("{{ route('admin.integration.verify-necta') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(Object.fromEntries(form))
                });
                const data = await response.json();
                resEl.innerText = JSON.stringify(data, null, 2);
                if(response.ok) setTimeout(() => location.reload(), 2000); // Reload to show log
            } catch (err) {
                resEl.innerText = 'Error: ' + err.message;
            }
        });

        document.getElementById('nacte-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            const form = new FormData(this);
            const resEl = document.getElementById('nacte-result');
            resEl.innerText = 'Verifying...';
            resEl.classList.remove('hidden');
            
            try {
                const response = await fetch("{{ route('admin.integration.verify-nacte') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(Object.fromEntries(form))
                });
                const data = await response.json();
                resEl.innerText = JSON.stringify(data, null, 2);
                if(response.ok) setTimeout(() => location.reload(), 2000); // Reload to show log
            } catch (err) {
                resEl.innerText = 'Error: ' + err.message;
            }
        });
    </script>

@endsection
