@extends('layouts.app')

@section('title', 'Polling Unit Results')
@section('page-title', 'Polling Unit Results')
@section('page-subtitle', 'View election results for any individual polling unit')

@section('content')
<div class="row g-4">

    {{-- Filter Card --}}
    <div class="col-lg-4">
        <div class="card h-auto">
            <div class="card-header">
                <i class="bi bi-funnel-fill me-2"></i>Select Polling Unit
            </div>
            <div class="card-body p-4">

                {{-- State --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">State</label>
                    <select id="stateSelect" class="form-select">
                        <option value="">-- Select State --</option>
                        @foreach($states as $state)
                            <option value="{{ $state->state_id }}"
                                {{ $state->state_id == $defaultStateId ? 'selected' : '' }}>
                                {{ $state->state_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- LGA --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Local Government Area</label>
                    <select id="lgaSelect" class="form-select" disabled>
                        <option value="">-- Select LGA --</option>
                    </select>
                </div>

                {{-- Ward --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Ward</label>
                    <select id="wardSelect" class="form-select" disabled>
                        <option value="">-- Select Ward --</option>
                    </select>
                </div>

                {{-- Polling Unit --}}
                <div class="mb-4">
                    <label class="form-label fw-semibold">Polling Unit</label>
                    <select id="pollingUnitSelect" class="form-select" disabled>
                        <option value="">-- Select Polling Unit --</option>
                    </select>
                </div>

                <button id="viewResultsBtn" class="btn btn-primary w-100" disabled>
                    <i class="bi bi-eye-fill me-2"></i>View Results
                </button>
            </div>
        </div>
    </div>

    {{-- Results Card --}}
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-bar-chart-fill me-2"></i>Election Results</span>
                <span id="pollingUnitLabel" class="badge bg-light text-dark"></span>
            </div>
            <div class="card-body p-4">

                {{-- Placeholder --}}
                <div id="resultsPlaceholder" class="text-center py-5 text-muted">
                    <i class="bi bi-map" style="font-size:3rem; opacity:0.3;"></i>
                    <p class="mt-3">Select a polling unit on the left to view its results.</p>
                </div>

                {{-- Spinner --}}
                <div class="spinner-overlay" id="resultsSpinner">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2">Loading results...</p>
                </div>

                {{-- Results Table --}}
                <div class="results-section" id="resultsSection">
                    <table class="table table-hover result-table mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Party</th>
                                <th class="text-end">Votes</th>
                                <th class="text-end">Share (%)</th>
                            </tr>
                        </thead>
                        <tbody id="resultsBody"></tbody>
                        <tfoot>
                            <tr class="total-row">
                                <td colspan="2"><strong>Total Votes</strong></td>
                                <td class="text-end" colspan="2"><strong id="totalVotes">0</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    const stateSelect       = document.getElementById('stateSelect');
    const lgaSelect         = document.getElementById('lgaSelect');
    const wardSelect        = document.getElementById('wardSelect');
    const pollingUnitSelect = document.getElementById('pollingUnitSelect');
    const viewResultsBtn    = document.getElementById('viewResultsBtn');
    const resultsPlaceholder = document.getElementById('resultsPlaceholder');
    const resultsSpinner    = document.getElementById('resultsSpinner');
    const resultsSection    = document.getElementById('resultsSection');
    const resultsBody       = document.getElementById('resultsBody');
    const totalVotes        = document.getElementById('totalVotes');
    const pollingUnitLabel  = document.getElementById('pollingUnitLabel');

    function resetSelect(el, placeholder) {
        el.innerHTML = `<option value="">${placeholder}</option>`;
        el.disabled = true;
    }

    function showSpinner() {
        resultsPlaceholder.style.display = 'none';
        resultsSection.style.display = 'none';
        resultsSpinner.style.display = 'block';
    }

    function hideSpinner() {
        resultsSpinner.style.display = 'none';
    }

    // State → load LGAs
    stateSelect.addEventListener('change', function () {
        resetSelect(lgaSelect, '-- Select LGA --');
        resetSelect(wardSelect, '-- Select Ward --');
        resetSelect(pollingUnitSelect, '-- Select Polling Unit --');
        viewResultsBtn.disabled = true;

        if (!this.value) return;

        fetch(`/ajax/lgas/${this.value}`)
            .then(r => r.json())
            .then(data => {
                data.forEach(lga => {
                    lgaSelect.innerHTML += `<option value="${lga.lga_id}">${lga.lga_name}</option>`;
                });
                lgaSelect.disabled = false;
            });
    });

    // LGA → load Wards
    lgaSelect.addEventListener('change', function () {
        resetSelect(wardSelect, '-- Select Ward --');
        resetSelect(pollingUnitSelect, '-- Select Polling Unit --');
        viewResultsBtn.disabled = true;

        if (!this.value) return;

        fetch(`/ajax/wards/${this.value}`)
            .then(r => r.json())
            .then(data => {
                data.forEach(ward => {
                    wardSelect.innerHTML += `<option value="${ward.ward_id}">${ward.ward_name}</option>`;
                });
                wardSelect.disabled = false;
            });
    });

    // Ward → load Polling Units
    wardSelect.addEventListener('change', function () {
        resetSelect(pollingUnitSelect, '-- Select Polling Unit --');
        viewResultsBtn.disabled = true;

        if (!this.value) return;

        fetch(`/ajax/polling-units/${this.value}`)
            .then(r => r.json())
            .then(data => {
                data.forEach(pu => {
                    const label = pu.polling_unit_number
                        ? `${pu.polling_unit_number} - ${pu.polling_unit_name}`
                        : pu.polling_unit_name;
                    pollingUnitSelect.innerHTML += `<option value="${pu.uniqueid}">${label}</option>`;
                });
                pollingUnitSelect.disabled = false;
            });
    });

    // Polling Unit → enable button
    pollingUnitSelect.addEventListener('change', function () {
        viewResultsBtn.disabled = !this.value;
    });

    // Load Results
    viewResultsBtn.addEventListener('click', function () {
        const puId = pollingUnitSelect.value;
        if (!puId) return;

        showSpinner();

        fetch(`/ajax/pu-results/${puId}`)
            .then(r => r.json())
            .then(data => {
                hideSpinner();

                if (data.error) {
                    alert(data.error);
                    resultsPlaceholder.style.display = 'block';
                    return;
                }

                pollingUnitLabel.textContent = data.polling_unit;
                resultsBody.innerHTML = '';

                data.results.forEach((row, i) => {
                    const share = data.total > 0
                        ? ((row.party_score / data.total) * 100).toFixed(1)
                        : '0.0';
                    resultsBody.innerHTML += `
                        <tr>
                            <td>${i + 1}</td>
                            <td><span class="badge-party">${row.party_abbreviation}</span></td>
                            <td class="text-end fw-semibold">${Number(row.party_score).toLocaleString()}</td>
                            <td class="text-end text-muted">${share}%</td>
                        </tr>`;
                });

                totalVotes.textContent = Number(data.total).toLocaleString();
                resultsSection.style.display = 'block';
            })
            .catch(() => {
                hideSpinner();
                alert('Failed to load results. Please try again.');
                resultsPlaceholder.style.display = 'block';
            });
    });

    // Auto-trigger state load on page ready (Delta is preselected)
    if (stateSelect.value) {
        stateSelect.dispatchEvent(new Event('change'));
    }
</script>
@endpush
