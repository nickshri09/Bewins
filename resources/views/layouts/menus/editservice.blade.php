@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Edit Services</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('addservices.update', $batchId) }}" method="POST">
        @csrf
        @method('PUT')

<div class="mb-3">
    <label for="product_category">Product Category</label>
    <select name="product_category" id="product_category" class="form-control" required>
        <option value="">-- Select Category --</option>
        <option value="FSSAI AND FOSCOS" {{ $mainService->product_category == 'FSSAI AND FOSCOS' ? 'selected' : '' }}>FSSAI AND FOSCOS</option>
        <option value="GOVERNMENT FEES" {{ $mainService->product_category == 'GOVERNMENT FEES' ? 'selected' : '' }}>GOVERNMENT FEES</option>
        <option value="DSC (DIGITAL SIGNATURE CERTIFICATE)" {{ $mainService->product_category == 'DSC (DIGITAL SIGNATURE CERTIFICATE)' ? 'selected' : '' }}>DSC (DIGITAL SIGNATURE CERTIFICATE)</option>
        <option value="ISO CERTIFICATION AND SURVEILLANCE" {{ $mainService->product_category == 'ISO CERTIFICATION AND SURVEILLANCE' ? 'selected' : '' }}>ISO CERTIFICATION AND SURVEILLANCE</option>
        <option value="INCOME TAX RELATED SERVICES" {{ $mainService->product_category == 'INCOME TAX RELATED SERVICES' ? 'selected' : '' }}>INCOME TAX RELATED SERVICES</option>
        <option value="VARIABLE PRODUCT AND SERVICES" {{ $mainService->product_category == 'VARIABLE PRODUCT AND SERVICES' ? 'selected' : '' }}>VARIABLE PRODUCT AND SERVICES</option>
        <option value="TRADEMARK AND INTELLECTUAL RIGHTS" {{ $mainService->product_category == 'TRADEMARK AND INTELLECTUAL RIGHTS' ? 'selected' : '' }}>TRADEMARK AND INTELLECTUAL RIGHTS</option>
        <option value="MISCELLANEOUS SERVICES" {{ $mainService->product_category == 'MISCELLANEOUS SERVICES' ? 'selected' : '' }}>MISCELLANEOUS SERVICES</option>
        <option value="INSURANCE COMPANY AND LLP REGISTRATION" {{ $mainService->product_category == 'INSURANCE COMPANY AND LLP REGISTRATION' ? 'selected' : '' }}>INSURANCE COMPANY AND LLP REGISTRATION</option>
        <option value="ROC EVENT COMPLIANCE" {{ $mainService->product_category == 'ROC EVENT COMPLIANCE' ? 'selected' : '' }}>ROC EVENT COMPLIANCE</option>
        <option value="ROC ANNUAL COMPLIANCE" {{ $mainService->product_category == 'ROC ANNUAL COMPLIANCE' ? 'selected' : '' }}>ROC ANNUAL COMPLIANCE</option>
        <option value="PAN AND TAN CARD" {{ $mainService->product_category == 'PAN AND TAN CARD' ? 'selected' : '' }}>PAN AND TAN CARD</option>
        <option value="PROJECT REPORT AND CMA REPORT" {{ $mainService->product_category == 'PROJECT REPORT AND CMA REPORT' ? 'selected' : '' }}>PROJECT REPORT AND CMA REPORT</option>
        <option value="GST (GOODS AND SERVICES TAX)" {{ $mainService->product_category == 'GST (GOODS AND SERVICES TAX)' ? 'selected' : '' }}>GST (GOODS AND SERVICES TAX)</option>
        <option value="PARTNERSHIP SERVICES" {{ $mainService->product_category == 'PARTNERSHIP SERVICES' ? 'selected' : '' }}>PARTNERSHIP SERVICES</option>
        <option value="OTHER SERVICES" {{ $mainService->product_category == 'OTHER SERVICES' ? 'selected' : '' }}>OTHER SERVICES</option>
        <option value="PAYROLL SERVICES" {{ $mainService->product_category == 'PAYROLL SERVICES' ? 'selected' : '' }}>PF, ESI AND PAYROLL SERVICES</option>
        <option value="GEM PORTAL SERVICE" {{ $mainService->product_category == 'GEM PORTAL SERVICE' ? 'selected' : '' }}>GEM PORTAL SERVICE</option>
        <option value="NET WORTH STATEMENT" {{ $mainService->product_category == 'NET WORTH STATEMENT' ? 'selected' : '' }}>NET WORTH STATEMENT</option>
        <option value="STARTUP INDIA SERVICES" {{ $mainService->product_category == 'STARTUP INDIA SERVICES' ? 'selected' : '' }}>STARTUP INDIA SERVICES</option>
        <option value="IRCTC" {{ $mainService->product_category == 'IRCTC' ? 'selected' : '' }}>IRCTC</option>
        <option value="WEBSITE DEVELOPMENT AND HOSTING SERVICES" {{ $mainService->product_category == 'WEBSITE DEVELOPMENT AND HOSTING SERVICES' ? 'selected' : '' }}>WEBSITE DEVELOPMENT AND HOSTING SERVICES</option>
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
                {{-- Main Service --}}
                <tr>
                    <td><input name="services[0][unique_id]" class="form-control" value="{{ $mainService->unique_id }}" required></td>
                    <td><input name="services[0][service_name]" class="form-control" value="{{ $mainService->service_name }}" required></td>
                    <td><input name="services[0][fee]" class="form-control" type="number" step="1" value="{{ $mainService->fee }}" required></td>
                    <td><button type="button" class="btn btn-danger remove-row">-</button></td>
                </tr>

                {{-- Child Services --}}
                @foreach($childServices as $index => $child)
                <tr>
                    <td><input name="services[{{ $index + 1 }}][unique_id]" class="form-control" value="{{ $child->unique_id }}" required></td>
                    <td><input name="services[{{ $index + 1 }}][service_name]" class="form-control" value="{{ $child->service_name }}" required></td>
                    <td><input name="services[{{ $index + 1 }}][fee]" class="form-control" type="number" step="1" value="{{ $child->fee }}" required></td>
                    <td><button type="button" class="btn btn-danger remove-row">-</button></td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Fixed Fields Below the Dynamic Table --}}
        <div class="row mb-3">
            <div class="col-md-4">
                <label>Obligation</label>
                <input name="obligation" class="form-control" value="{{ $mainService->obligation }}" required>
            </div>
            <div class="col-md-4">
                <label>Frequency</label>
                <input name="frequency" class="form-control" value="{{ $mainService->frequency }}" required>
            </div>
            <div class="col-md-4">
                <label>Maximum TAT</label>
                <input name="maximum_tat" class="form-control" value="{{ $mainService->maximum_tat }}" required>
            </div>
        </div>

        {{-- Submit --}}
        <div class="text-end">
            <a href="{{ route('addservices.index') }}" class="btn btn-secondary mt-3 me-2">Cancel</a>
            <button type="submit" class="btn btn-primary mt-3">Update Services</button>
        </div>
    </form>
</div>

{{-- JavaScript --}}
<script>
    let rowCount = {{ count($childServices) + 1 }};

    document.getElementById('add-row').addEventListener('click', function () {
        const tableBody = document.querySelector('#services-table tbody');
        const newRow = document.createElement('tr');

        newRow.innerHTML = `
            <td><input name="services[${rowCount}][unique_id]" class="form-control" required></td>
            <td><input name="services[${rowCount}][service_name]" class="form-control" value="Government" required></td>
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
</script>
@endsection
