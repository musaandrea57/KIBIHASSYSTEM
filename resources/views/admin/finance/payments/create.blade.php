@extends('layouts.portal')

@section('content')
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Record Payment</h2>
    </div>

    <form action="{{ route('admin.finance.payments.store') }}" method="POST">
        @csrf
        <div class="bg-white shadow sm:rounded-lg mb-6">
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        <!-- Student Search -->
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Search Student</label>
                            <div class="relative">
                                <input type="text" id="student-search" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Name or Admission No...">
                                <input type="hidden" name="student_id" id="student_id" required>
                                <div id="student-results" class="absolute z-10 w-full bg-white shadow-lg rounded-md mt-1 hidden max-h-60 overflow-y-auto border border-gray-200"></div>
                            </div>
                        </div>

                        <!-- Invoice Selection -->
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Select Invoice to Pay</label>
                            <select name="invoice_id" id="invoice_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 disabled:bg-gray-100" disabled>
                                <option value="">-- First Select a Student --</option>
                            </select>
                            <p class="mt-1 text-sm text-gray-500" id="invoice-info"></p>
                        </div>

                        <!-- Payment Details -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Payment Date</label>
                            <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Amount (TZS)</label>
                            <input type="number" name="amount" id="amount" step="0.01" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Payment Method</label>
                            <select name="method" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="bank">Bank Deposit</option>
                                <option value="mobile_money">Mobile Money</option>
                                <option value="cash">Cash</option>
                                <option value="cheque">Cheque</option>
                                <option value="card">Card</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Transaction Reference / Receipt No</label>
                            <input type="text" name="transaction_ref" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                        Submit Payment
                    </button>
                </div>
    </form>

    <script>
        function debounce(func, wait) {
            let timeout;
            return function(...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(this, args), wait);
            };
        }

        const studentSearch = document.getElementById('student-search');
        const resultsContainer = document.getElementById('student-results');
        const invoiceSelect = document.getElementById('invoice_id');
        
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

            // Fetch Invoices
            fetch(`{{ url('admin/finance/payments/student-invoices') }}/${student.id}`)
                .then(response => response.json())
                .then(invoices => {
                    invoiceSelect.innerHTML = '<option value="">-- Choose Invoice --</option>';
                    if (invoices.length > 0) {
                        invoices.forEach(inv => {
                            const option = document.createElement('option');
                            option.value = inv.id;
                            option.textContent = inv.text;
                            option.dataset.balance = inv.balance;
                            invoiceSelect.appendChild(option);
                        });
                        invoiceSelect.disabled = false;
                    } else {
                        invoiceSelect.innerHTML = '<option value="">No unpaid invoices found</option>';
                        invoiceSelect.disabled = true;
                    }
                });
        }

        invoiceSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const balance = selectedOption.dataset.balance;
            if (balance) {
                document.getElementById('amount').value = balance;
                document.getElementById('invoice-info').textContent = `Outstanding Balance: ${Number(balance).toLocaleString()} TZS`;
            } else {
                document.getElementById('invoice-info').textContent = '';
            }
        });
    </script>

@endsection
