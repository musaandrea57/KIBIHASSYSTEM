@extends('layouts.portal')

@section('content')
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Create Single Invoice</h2>
    </div>

    <form action="{{ route('admin.finance.invoices.store') }}" method="POST" id="invoice-form">
        @csrf
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                    <!-- Left Column: Student & Context -->
                    <div class="lg:col-span-1 space-y-6">
                        <div class="bg-white shadow sm:rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Student Details</h3>
                            
                            <!-- Student Search -->
                            <div class="mb-4 relative">
                                <label class="block text-sm font-medium text-gray-700">Search Student</label>
                                <input type="text" id="student-search" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Name or Admission No...">
                                <input type="hidden" name="student_id" id="student_id" required>
                                <div id="student-results" class="absolute z-10 w-full bg-white shadow-lg rounded-md mt-1 hidden max-h-60 overflow-y-auto border border-gray-200"></div>
                            </div>

                            <!-- Context (Read-only / Hidden) -->
                            <div id="student-context" class="space-y-4 hidden">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Program</label>
                                    <input type="text" id="program_name" readonly class="mt-1 block w-full bg-gray-100 border-gray-300 rounded-md shadow-sm text-sm">
                                    <input type="hidden" name="program_id" id="program_id">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">NTA Level</label>
                                    <input type="text" name="nta_level" id="nta_level" readonly class="mt-1 block w-full bg-gray-100 border-gray-300 rounded-md shadow-sm text-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Academic Year</label>
                                    <input type="hidden" name="academic_year_id" id="academic_year_id">
                                    <input type="text" id="academic_year_display" readonly class="mt-1 block w-full bg-gray-100 border-gray-300 rounded-md shadow-sm text-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Semester</label>
                                    <input type="hidden" name="semester_id" id="semester_id">
                                    <input type="text" id="semester_display" readonly class="mt-1 block w-full bg-gray-100 border-gray-300 rounded-md shadow-sm text-sm">
                                </div>
                            </div>
                        </div>

                        <div class="bg-white shadow sm:rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Invoice Dates</h3>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Issue Date</label>
                                    <input type="date" name="issue_date" value="{{ date('Y-m-d') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Due Date</label>
                                    <input type="date" name="due_date" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Invoice Items -->
                    <div class="lg:col-span-2 space-y-6">
                        <div class="bg-white shadow sm:rounded-lg p-6">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-medium text-gray-900">Invoice Items</h3>
                                <button type="button" id="fetch-structure-btn" class="text-sm text-indigo-600 hover:text-indigo-900 hidden" onclick="fetchFeeStructure()">
                                    Refresh from Fee Structure
                                </button>
                            </div>
                            
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
                                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="items-container" class="bg-white divide-y divide-gray-200">
                                        <!-- Items will be populated here -->
                                        <tr id="empty-row">
                                            <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">
                                                Select a student to load fee structure items.
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot class="bg-gray-50">
                                        <tr>
                                            <td class="px-6 py-4 text-right font-bold">Total</td>
                                            <td class="px-6 py-4 text-right font-bold" id="total-display">0.00</td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            
                            <div class="mt-4">
                                <button type="button" onclick="addManualItem()" class="text-sm text-blue-600 hover:text-blue-500">
                                    + Add Manual Item
                                </button>
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded shadow hover:bg-blue-700 text-lg font-semibold">
                                Create Invoice
                            </button>
                        </div>
                    </div>
                </div>
    </form>

    <script>
        // Debounce function
        function debounce(func, wait) {
            let timeout;
            return function(...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(this, args), wait);
            };
        }

        const studentSearch = document.getElementById('student-search');
        const resultsContainer = document.getElementById('student-results');
        
        studentSearch.addEventListener('input', debounce(function(e) {
            const term = e.target.value;
            if (term.length < 2) {
                resultsContainer.classList.add('hidden');
                return;
            }

            fetch(`{{ route('admin.finance.invoices.search-students') }}?term=${term}`)
                .then(response => response.json())
                .then(data => {
                    resultsContainer.innerHTML = '';
                    if (data.length > 0) {
                        data.forEach(student => {
                            const div = document.createElement('div');
                            div.className = 'p-2 hover:bg-gray-100 cursor-pointer text-sm';
                            div.textContent = student.text;
                            div.onclick = () => selectStudent(student);
                            resultsContainer.appendChild(div);
                        });
                        resultsContainer.classList.remove('hidden');
                    } else {
                        resultsContainer.classList.add('hidden');
                    }
                });
        }, 300));

        function selectStudent(student) {
            document.getElementById('student_id').value = student.id;
            document.getElementById('student-search').value = student.text;
            resultsContainer.classList.add('hidden');

            // Fetch Context
            fetch(`{{ url('admin/finance/invoices/student-context') }}/${student.id}`)
                .then(response => response.json())
                .then(context => {
                    document.getElementById('student-context').classList.remove('hidden');
                    document.getElementById('program_id').value = context.program_id;
                    document.getElementById('program_name').value = context.program_name;
                    document.getElementById('nta_level').value = context.nta_level;
                    document.getElementById('academic_year_id').value = context.academic_year_id;
                    document.getElementById('semester_id').value = context.semester_id;
                    
                    // Display text for IDs (Assuming backend sends IDs, we might need names or just show "Active")
                    // The context response I saw in controller sends IDs. 
                    // Ideally we should have names too. 
                    // I will assume for now we just show "Current" or update controller to send names.
                    // Controller sends: program_name. 
                    // For Year/Semester it sends IDs. I'll just put "Current Active" for now or fetch names if possible.
                    document.getElementById('academic_year_display').value = "Active Year (ID: " + context.academic_year_id + ")"; 
                    document.getElementById('semester_display').value = "Active Semester (ID: " + context.semester_id + ")";

                    // Fetch Fee Structure
                    fetchFeeStructure();
                });
        }

        function fetchFeeStructure() {
            const program_id = document.getElementById('program_id').value;
            const nta_level = document.getElementById('nta_level').value;
            const academic_year_id = document.getElementById('academic_year_id').value;
            const semester_id = document.getElementById('semester_id').value;

            const params = new URLSearchParams({
                program_id, nta_level, academic_year_id, semester_id
            });

            fetch(`{{ route('admin.finance.invoices.get-fee-structure') }}?${params}`)
                .then(response => {
                    if (!response.ok) throw new Error('No fee structure');
                    return response.json();
                })
                .then(structure => {
                    const container = document.getElementById('items-container');
                    container.innerHTML = '';
                    
                    structure.items.forEach((item, index) => {
                        addItemRow(index, item.fee_item.name, item.amount, item.fee_item_id);
                    });
                    
                    calculateTotal();
                    document.getElementById('fetch-structure-btn').classList.remove('hidden');
                })
                .catch(err => {
                    console.error(err);
                    document.getElementById('items-container').innerHTML = `
                        <tr><td colspan="3" class="px-6 py-4 text-center text-red-500">No active fee structure found for this student's context. Please add items manually.</td></tr>
                    `;
                });
        }

        let itemIndex = 0;
        function addItemRow(index, description, amount, feeItemId = null) {
            itemIndex = Math.max(itemIndex, index + 1);
            const container = document.getElementById('items-container');
            
            const row = document.createElement('tr');
            row.innerHTML = `
                <td class="px-6 py-4">
                    <input type="text" name="items[${index}][description]" value="${description}" class="w-full border-gray-300 rounded-md shadow-sm text-sm" required>
                    <input type="hidden" name="items[${index}][fee_item_id]" value="${feeItemId || ''}">
                </td>
                <td class="px-6 py-4">
                    <input type="number" name="items[${index}][amount]" value="${amount}" step="0.01" class="amount-input w-full border-gray-300 rounded-md shadow-sm text-sm text-right" required onchange="calculateTotal()">
                </td>
                <td class="px-6 py-4 text-right">
                    <button type="button" onclick="this.closest('tr').remove(); calculateTotal()" class="text-red-600 hover:text-red-900">Remove</button>
                </td>
            `;
            container.appendChild(row);
        }

        function addManualItem() {
            addItemRow(itemIndex++, '', 0);
        }

        function calculateTotal() {
            let total = 0;
            document.querySelectorAll('.amount-input').forEach(input => {
                total += parseFloat(input.value) || 0;
            });
            document.getElementById('total-display').textContent = total.toLocaleString('en-US', {minimumFractionDigits: 2});
        }
    </script>

@endsection
