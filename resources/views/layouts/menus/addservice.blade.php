@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Add Services</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('addservices.store') }}" method="POST">
        @csrf


<div class="mb-3">
    <label for="main_service_id">Main Service</label>
    <select name="main_service_id" id="main_service_id" class="form-control select2" required>
        <option value="">Select One</option>
        @isset($mainServices)
            @foreach($mainServices as $ms)
                <option value="{{ $ms->id }}">{{ $ms->service_code ? $ms->service_code.' - ' : '' }}{{ $ms->name }} (₹ {{ number_format($ms->amount,2) }})</option>
            @endforeach
        @endisset
    </select>
</div>



        {{-- Dynamic Inputs Table --}}
        <table class="table" id="services-table">
            <thead>
                <tr>
                    <th>Service Code</th>
                    <th>Service Name</th>
                    <th>Fee</th>
                    <th>
                        <button type="button" class="btn btn-success" id="add-row">+</button>
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><input name="services[0][unique_id]" class="form-control" required></td>
                    <td><input name="services[0][service_name]" class="form-control" required></td> <!-- No default -->
                    <td><input name="services[0][fee]" class="form-control" type="number" step="1" required></td>
                    <td><button type="button" class="btn btn-danger remove-row">-</button></td>
                </tr>
            </tbody>
        </table>

        {{-- Fixed Fields Below the Dynamic Table --}}
        <div class="row mb-3">
            <div class="col-md-4">
                <label>Obligation</label>
                <input name="obligation" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label>Frequency</label>
                <input name="frequency" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label>Maximum TAT</label>
                <input name="maximum_tat" class="form-control" required>
            </div>
        </div>

        {{-- Submit --}}
        <div class="text-end">
            <button type="submit" class="btn btn-primary mt-3">Submit</button>
        </div>
    </form>
</div>

{{-- JavaScript --}}
<script>
    let rowCount = 1;

    document.getElementById('add-row').addEventListener('click', function () {
        const tableBody = document.querySelector('#services-table tbody');
        const newRow = document.createElement('tr');

        newRow.innerHTML = `
            <td><input name="services[${rowCount}][unique_id]" class="form-control" required></td>
            <td><input name="services[${rowCount}][service_name]" class="form-control"  required></td>
            <td><input name="services[${rowCount}][fee]" class="form-control" type="number" step="1" required></td>
            <td><button type="button" class="btn btn-danger remove-row">-</button></td>
        `;

        tableBody.appendChild(newRow);
        rowCount++;
    });

    document.addEventListener('click', function (e) {
        if (e.target && e.target.classList.contains('remove-row')) {
            e.target.closest('tr').remove();
        }
    });

    // Initialize select2 for Main Service dropdown
    (function(){
        if (window.jQuery && jQuery.fn && typeof jQuery.fn.select2 === 'function') {
            jQuery('#main_service_id').select2({
                placeholder: 'Select One',
                allowClear: true,
                width: '100%'
            });
        }
    })();
</script>
@endsection
