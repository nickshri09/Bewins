@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Taxation Services</h2>
        <button class="btn btn-primary" onclick="downloadPDF()">Download as PDF</button>
    </div>

    {{-- Page Break Styling --}}
    <style>
        .page-break {
            page-break-after: always;
        }
    </style>

    <div id="service-list">
        @php $entryCount = 0; @endphp

        @forelse($groupedServices as $batchId => $services)
            @php
                $main     = $services->first();
                $children = $services->slice(1);
                $entryCount++;
            @endphp

            <div class="border border-primary rounded mb-4 shadow-sm">
                <div class="d-flex justify-content-between align-items-center p-3" style="background:#e9f1ff;">
                    <div><strong>{{ $main->unique_id }}</strong> – {{ $main->service_name }}</div>
                    <div class="fw-bold">₹{{ number_format($main->fee,2) }}</div>
                </div>

                @if($children->isNotEmpty())
                    <div class="px-3 pt-2 pb-3">
                        @foreach($children as $child)
                            <div class="d-flex justify-content-between align-items-center mb-2"
                                 style="margin-left:20px; padding:8px; border:1px solid #ccc; background:#f0f0f0; border-radius:4px;">
                                <div>{{ $child->unique_id }} &nbsp; {{ $child->service_name }}</div>
                                <div>+ ₹{{ number_format($child->fee,2) }}</div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Page break after every 20 entries --}}
            @if($entryCount % 20 === 0)
                <div class="page-break"></div>
            @endif

        @empty
            <p class="text-muted">No services found.</p>
        @endforelse
    </div>
</div>

{{-- PDF Library and Script --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
    function downloadPDF() {
        const element = document.getElementById('service-list');
        html2pdf().from(element)
            .set({
                margin: 0.5,
                filename: 'taxation-services.pdf',
                html2canvas: { scale: 2 },
                jsPDF: { unit: 'in', format: 'a4', orientation: 'portrait' }
            })
            .save();
    }
</script>
@endsection
