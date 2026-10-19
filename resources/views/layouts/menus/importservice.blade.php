@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Import Services</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <form action="{{ route('addservices.import.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label for="file" class="form-label">Choose File</label>
                    <input type="file" class="form-control" id="file" name="file" accept=".csv,.xlsx,.xls" required>
                    <div class="form-text">Supported formats: CSV, XLSX, XLS (Max size: 2MB)</div>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary">Import Services</button>
                </div>
            </form>
        </div>

        <div class="col-md-12 py-4 bg-[#FFF5F2]">
            <div class="card ">
                <div class="card-header">
                    <h5>Required Fields Format</h5>
                </div>
                <div class="card-body">
                    <p><strong>CSV/Excel file should contain these columns:</strong></p>
                    <ul class="list-unstyled">
                        <li><strong>unique_id</strong> - Service Code (Required)</li>
                        <li><strong>service_name</strong> - Service Name (Required)</li>
                        <li><strong>fee</strong> - Service Fee (Required)</li>
                        <li><strong>product_category</strong> - Product Category (Required)</li>
                        <li><strong>obligation</strong> - Obligation (Required)</li>
                        <li><strong>frequency</strong> - Frequency (Required)</li>
                        <li><strong>maximum_tat</strong> - Maximum TAT (Required)</li>
                    </ul>
                    
                    <hr>
                    
                    <h6>Sample CSV Format:</h6>
                    <div class="bg-light p-2 rounded">
                        <small>
                            unique_id,service_name,fee,product_category,obligation,frequency,maximum_tat<br>
                            SRV001,Service Name 1,1000,FSSAI AND FOSCOS,Monthly,1 year,30 days<br>
                            SRV002,Service Name 2,2000,GST,Quarterly,1 year,15 days
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>Product Categories Available</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <ul>
                                <li>FSSAI AND FOSCOS</li>
                                <li>GOVERNMENT FEES</li>
                                <li>DSC (DIGITAL SIGNATURE CERTIFICATE)</li>
                                <li>ISO CERTIFICATION AND SURVEILLANCE</li>
                                <li>INCOME TAX RELATED SERVICES</li>
                                <li>VARIABLE PRODUCT AND SERVICES</li>
                                <li>TRADEMARK AND INTELLECTUAL RIGHTS</li>
                                <li>MISCELLANEOUS SERVICES</li>
                                <li>INSURANCE COMPANY AND LLP REGISTRATION</li>
                                <li>ROC EVENT COMPLIANCE</li>
                                <li>ROC ANNUAL COMPLIANCE</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul>
                                <li>PAN AND TAN CARD</li>
                                <li>PROJECT REPORT AND CMA REPORT</li>
                                <li>GST (GOODS AND SERVICES TAX)</li>
                                <li>PARTNERSHIP SERVICES</li>
                                <li>OTHER SERVICES</li>
                                <li>PF, ESI AND PAYROLL SERVICES</li>
                                <li>GEM PORTAL SERVICE</li>
                                <li>NET WORTH STATEMENT</li>
                                <li>STARTUP INDIA SERVICES</li>
                                <li>IRCTC</li>
                                <li>WEBSITE DEVELOPMENT AND HOSTING SERVICES</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection