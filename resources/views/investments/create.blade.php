@extends('layouts.app')

@section('content')
    <script src="https://cdn.tailwindcss.com"></script>
    <div class="min-h-screen bg-gray-50 py-2">
        <div class="max-w-8xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow-xl rounded-lg overflow-hidden">
                <!-- Header -->
                <div class="bg-gradient-to-r from-blue-900 to-blue-800 px-6 py-4 border-b border-blue-700">
                    <h2 class="text-2xl font-bold text-white text-center">{{ _lang('Add New Bewin Collateral Security') }}</h2>
                </div>

                <!-- Form Body -->
                <div class="p-6">
                    <form method="post" autocomplete="off" action="{{ route('investments.store') }}" class="space-y-6">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Customer -->
                           <select id="customer_id" name="customer_id" required
    class="customer-select">
    <option value="">{{ _lang('Select One') }}</option>

    @foreach ($customers as $customer)
        <option value="{{ $customer->id }}">
            {{ $customer->name }}
        </option>
    @endforeach
</select>

                            <!-- Currency -->
                            <div>
                                <label for="currency_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ _lang('Currency') }} <span class="text-red-500">*</span>
                                </label>
                                <select id="currency_id" name="currency_id" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                    <option value="">{{ _lang('Select One') }}</option>
                                    {{ create_option('currency', 'id', 'name', '', ['status=' => 1]) }}
                                </select>
                            </div>

                            <!-- Investment Amount -->
                            <div>
                                <label for="investment_amount" class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ _lang('Bewin Collateral Security Amount') }} <span class="text-red-500">*</span>
                                </label>
                                <input type="number" id="investment_amount" name="investment_amount"
                                    value="{{ old('investment_amount') }}" required
                                    placeholder="Enter Amount : "
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            </div>

                            <!-- Investment Type -->
                            <div>
                                <label for="investment_type" class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ _lang('Bewin Collateral Security Type') }} <span class="text-red-500">*</span>
                                </label>
                                <select id="investment_type" name="investment_type" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                    <option value="">{{ _lang('Select One') }}</option>
                                    <option value="mutual_fund">{{ _lang('Mutual Fund') }}</option>
                                </select>
                            </div>

                            <!-- Contribution Type -->
                            <div>
                                <label for="contribution_type" class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ _lang('Contribution Type') }} <span class="text-red-500">*</span>
                                </label>
                                <select id="contribution_type" name="contribution_type" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                    <option value="">{{ _lang('Select One') }}</option>
                                    <option value="onetime">{{ _lang('One Time') }}</option>
                                    {{-- <option value="monthly">{{ _lang('Monthly (SIP)') }}</option> --}}
                                </select>
                            </div>

                            <!-- Risk Level -->
                            <div>
                                <label for="risk_level" class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ _lang('Risk Level') }} <span class="text-red-500">*</span>
                                </label>
                                <select id="risk_level" name="risk_level" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                    <option value="">{{ _lang('Select One') }}</option>
                                    <option value="low">{{ _lang('Low') }}</option>
                                    <option value="medium">{{ _lang('Medium') }}</option>
                                    <option value="high">{{ _lang('High') }}</option>
                                </select>
                            </div>

                            <!-- Expected Return -->
                            <div>
                                <label for="expected_return" class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ _lang('Expected Return (%)') }} <span class="text-red-500">*</span>
                                </label>
                                <select id="expected_return" name="expected_return" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                    <option value="">{{ _lang('Select One') }}</option>
                                    @foreach ($expectedReturns as $expectedReturn)
                                        <option value="{{ $expectedReturn->expected_return }}">
                                            {{ $expectedReturn->expected_return }}%
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Start Date -->
                            <div>
                                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ _lang('Start Date') }} <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="start_date" name="start_date" value="{{ old('start_date') }}"
                                    required
                                    class="form-control datepicker">
                            </div>

                            <!-- Maturity Date -->
                            <div>
                                <label for="maturity_date" class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ _lang('Maturity Date / Target Date') }}
                                </label>
                                <input type="text" id="maturity_date" name="maturity_date"
                                    value="{{ old('maturity_date') }}"
                                    class="form-control datepicker">
                            </div>
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ _lang('Description') }}
                            </label>
                            <textarea id="description" name="description" rows="3"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 transition-colors">{{ old('description') }}</textarea>
                        </div>

                        <!-- Remarks -->
                        <div>
                            <label for="remarks" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ _lang('Remarks') }}
                            </label>
                            <textarea id="remarks" name="remarks" rows="3"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 transition-colors">{{ old('remarks') }}</textarea>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-center pt-4">
                            <button type="submit"
                                class="px-6 py-3 bg-blue-900 hover:bg-blue-800 text-white font-medium rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all transform hover:scale-105 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ _lang('Submit') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'blue-900': '#273C75',
                        'blue-800': '#1a2751',
                        'blue-700': '#132244',
                    }
                }
            }
        }
        
        
    .select2-search--dropdown {
        padding: 8px;
    }

    .select2-search--dropdown .select2-search__field {
        padding: 8px 10px;
        border: 1px solid #d1d5db;
        border-radius: 5px;
        outline: none;
    }

    .select2-results__option {
        padding: 9px 12px;
    }

    .select2-results__option--highlighted {
        background-color: #273C75 !important;
    }
    </script>
@endpush

@section('js-script')
    <script>
         <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        (function($) {
            "use strict";

            $(document).ready(function() {

                $('#customer_id').select2({
                    placeholder: "{{ _lang('Select Customer') }}",
                    allowClear: true,
                    width: '100%'
                });

            });

        })(jQuery);
        
        (function($) {
            "use strict";
            // No custom logic needed as global scripts handle datepicker and validation
        })(jQuery);
    </script>
@endsection
