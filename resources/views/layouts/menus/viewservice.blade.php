@extends('layouts.app')

@section('content')
<div class="">
  <h2 class="mb-4">Taxation Services</h2>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
  @endif

  <!-- Search and Filter Section -->
  <div class="card mb-4">
    <div class="card-body">
      <div class="row">
        <div class="col-md-6">
          <div class="input-group">
            <span class="input-group-text">
              <i class="fas fa-search"></i>
            </span>
            <input type="text" id="searchInput" class="form-control" placeholder="Search services by name, code, or ID...">
          </div>
        </div>
        <div class="col-md-2">
          <select id="priceFilter" class="form-select p-3 border rounded">
            <option value="">All Prices</option>
            <option value="0-1000">₹0 - ₹1,000</option>
            <option value="1000-5000">₹1,000 - ₹5,000</option>
            <option value="5000-10000">₹5,000 - ₹10,000</option>
            <option value="10000+">₹10,000+</option>
          </select>
        </div>
        <div class="col-md-2">
          <button id="clearFilters" class="btn btn-outline-secondary w-100">
            <i class="fas fa-times me-1"></i>   Clear Filters
          </button>
        </div>
      </div>
    </div>
  </div>

  <div class="row" id="servicesContainer">
    @forelse($groupedServices as $mainServiceId => $services)
      @php
        // Prefer main from provided map so mains without services also render
        $main = isset($mainServicesById) ? ($mainServicesById[$mainServiceId] ?? null) : optional($services->first())->mainService;
      @endphp

              <div class="col-12 mb-4 service-card"
                   data-service-name="{{ strtolower($main?->name ?? '') }}"
                   data-service-code="{{ $main?->service_code ?? '' }}"
                   data-main-price="{{ $main?->amount ?? 0 }}"
                   data-service-ids="{{ $services->pluck('unique_id')->implode(',') }}">
        <div class="card h-100 shadow-sm border-0">
          <div class="card-header  text-black" style="background-color: #d8b4fe; color:black">
            <div class="d-flex justify-content-between align-items-center">
              <h6 class="mb-0">
                <i class="fas fa-tag me-2"></i>
                {{ $main?->service_code }} - {{ $main?->name }}
              </h6>
             {{-- {{ route('addservices.edit', $batchId) }}  --}}
             {{-- <a href="#" class="btn btn-sm btn-light disabled">
             <i class="fas fa-edit"></i> Edit
              </a> --}}
            </div>
          </div>

          <div class="card-body">
            <div class="row">
              <div class="col-md-12 text-end">
                <div class="price-section">
                  <h4 class="text-primary mb-0">₹{{ number_format($main?->amount ?? 0, 2) }}</h4>
                  <small class="text-muted">Main Service Price</small>
                </div>
              </div>
            </div>

            @if($services->isNotEmpty())
              <hr>
              <div class="additional-services">
                <h6 class="text-muted mb-3">
                  <i class="fas fa-plus-circle me-1"></i>
                  Services ({{ $services->count() }})
                </h6>

                <div class="row">
                  @foreach($services as $svc)
                    <div class="col-md-6 mb-3">
                      <div class="p-3 bg-light rounded border">
                        <div class="d-flex justify-content-between align-items-start">
                          <div class="flex-grow-1">
                            <h6 class="mb-1">{{ $svc->service_name }}</h6>
                            <small class="text-muted">
                              <code>{{ $svc->unique_id }}</code>
                            </small>
                            {{-- <div class="mt-2">
                              <small class="text-muted d-block">Obligation: {{ $svc->obligation }}</small>
                              <small class="text-muted d-block">Frequency: {{ $svc->frequency }}</small>
                              <small class="text-muted d-block">Max TAT: {{ $svc->maximum_tat }}</small>
                            </div> --}}
                          </div>
                          <div class="text-end ms-3">
                            <h6 class="text-success mb-0">₹{{ number_format($svc->fee, 2) }}</h6>
                            <small class="text-muted">Service Fee</small>
                          </div>
                        </div>
                      </div>
                    </div>
                  @endforeach
                </div>
              </div>
            @endif
          </div>

        </div>
      </div>
    @empty
      <div class="col-12">
        <div class="card text-center py-5">
          <div class="card-body">
            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">No services added yet</h5>
            <p class="text-muted">Start by adding your first service</p>
            <a href="{{ route('addservices.create') }}" class="btn btn-primary">
              <i class="fas fa-plus me-2"></i>Add Service
            </a>
          </div>
        </div>
      </div>
    @endforelse
  </div>

  <!-- No Results Message -->
  <div id="noResults" class="col-12" style="display: none;">
    <div class="card text-center py-5">
      <div class="card-body">
        <i class="fas fa-search fa-3x text-muted mb-3"></i>
        <h5 class="text-muted">No services found</h5>
        <p class="text-muted">Try adjusting your search criteria</p>
        <button id="clearFiltersFromNoResults" class="btn btn-primary">
          <i class="fas fa-times me-2"></i>Clear Filters
        </button>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const priceFilter = document.getElementById('priceFilter');
    const clearFilters = document.getElementById('clearFilters');
    const clearFiltersFromNoResults = document.getElementById('clearFiltersFromNoResults');
    const serviceCards = document.querySelectorAll('.service-card');
    const noResults = document.getElementById('noResults');

    function filterServices() {
        const searchTerm = searchInput.value.toLowerCase().trim();
        const priceRange = priceFilter.value;
        let visibleCount = 0;

        serviceCards.forEach(card => {
            const serviceName = card.dataset.serviceName || '';
            const serviceCode = card.dataset.serviceCode || '';
            const serviceIds = card.dataset.serviceIds || '';
            const mainPrice = parseFloat(card.dataset.mainPrice) || 0;

            // Search filter
            const matchesSearch = !searchTerm ||
                serviceName.includes(searchTerm) ||
                serviceCode.includes(searchTerm) ||
                serviceIds.toLowerCase().includes(searchTerm);

            // Price filter
            let matchesPrice = true;
            if (priceRange) {
                switch(priceRange) {
                    case '0-1000':
                        matchesPrice = mainPrice >= 0 && mainPrice <= 1000;
                        break;
                    case '1000-5000':
                        matchesPrice = mainPrice > 1000 && mainPrice <= 5000;
                        break;
                    case '5000-10000':
                        matchesPrice = mainPrice > 5000 && mainPrice <= 10000;
                        break;
                    case '10000+':
                        matchesPrice = mainPrice > 10000;
                        break;
                }
            }

            if (matchesSearch && matchesPrice) {
                card.style.display = 'block';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        // Show/hide no results message
        if (visibleCount === 0) {
            noResults.style.display = 'block';
        } else {
            noResults.style.display = 'none';
        }
    }

    function clearAllFilters() {
        searchInput.value = '';
        priceFilter.value = '';
        filterServices();
    }

    // Event listeners
    searchInput.addEventListener('input', filterServices);
    priceFilter.addEventListener('change', filterServices);
    clearFilters.addEventListener('click', clearAllFilters);
    clearFiltersFromNoResults.addEventListener('click', clearAllFilters);

    // Initial filter
    filterServices();
});
</script>
@endsection
