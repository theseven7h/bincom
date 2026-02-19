@extends('layouts.app')

@section('title', 'LGA Summed Results')
@section('page-title', 'LGA Summed Results')
@section('page-subtitle', 'View the total votes across all polling units in a Local Government Area')

@section('content')
<div class="row g-4">

    {{-- Filter Card --}}
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-funnel-fill me-2"></i>Select LGA
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
                <div class="mb-4">
                    <label class="form-label fw-semibold">Local Government Area</label>
                    <select id="lgaSelect" class="form-select" disabled>
                        <option value="">-- Select LGA --</option>
                    </select>
                </div>

                <button id="viewResultsBtn" class="btn btn-primary w-100" disabled>
                    <i class="bi bi-table me-2"></i>View Summed Results
                </button>

                <div class="mt-3 p-3 bg-light rounded-3 small text-muted">
                    <i class="bi bi-info-circle me-1"></i>
                    Results are computed by <strong>summing</strong> all individual polling unit scores within the LGA.
                </div>
            </div>
        </div>
    </div>

    {{-- Results Card --}}
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-bar-chart-steps me-2"></i>Summed LGA Results</span>
                <span id="lgaLabel" class="badge bg-light text-dark"></span>
            </div>
            <div class="card-body p-4">

                {{-- Placeholder --}}
                <div id="resultsPlaceholder" class="text-center py-5 text-muted">
                    <i class="bi bi-building" style="font-size:3rem; opacity:0.3;"></i>
                    <p class="mt-3">Select a state and LGA to view the summed results.</p>
                </div>

                {{-- Spinner --}}
                <div class="spinner-overlay" id="resultsSpinner">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2">Loading results...</p>
                </div>

                {{-- Results --}}
                <div class="results-section" id="resultsSection">
                    <table class="table table-hover result-table mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Party</th>
                                <th class="text-end">Total Votes</th>
                                <th class="text-end">Share (%)</th>
                            </tr>
                        </thead>
                        <tbody id="resultsBody"></tbody>
                        <tfoot>
                            <tr class="total-row">
                                <td colspan="2"><strong>Grand Total</strong></td>
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
    const viewResultsBtn    = document.getElementById('viewResultsBtn');
    const resultsPlaceholder = document.getElementById('resultsPlaceholder');
    const resultsSpinner    = document.getElementById('resultsSpinner');
    const resultsSection    = document.getElementById('resultsSection');
    const resultsBody       = document.getElementById('resultsBody');
    const totalVotes        = document.getElementById('totalVotes');
    const lgaLabel          = document.getElementById('lgaLabel');

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

    // State → LGAs
    stateSelect.addEventListener('change', function () {
        resetSelect(lgaSelect, '-- Select LGA --');
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

    // LGA → enable button
    lgaSelect.addEventListener('change', function () {
        viewResultsBtn.disabled = !this.value;
    });

    // View Results
    viewResultsBtn.addEventListener('click', function () {
        const lgaId = lgaSelect.value;
        if (!lgaId) return;

        showSpinner();

        fetch(`/ajax/lga-results/${lgaId}`)
            .then(r => r.json())
            .then(data => {
                hideSpinner();

                if (data.error) {
                    alert(data.error);
                    resultsPlaceholder.style.display = 'block';
                    return;
                }

                lgaLabel.textContent = data.lga_name;
                resultsBody.innerHTML = '';

                if (data.results.length === 0) {
                    resultsBody.innerHTML = `<tr><td colspan="4" class="text-center text-muted py-4">No results found for this LGA.</td></tr>`;
                    totalVotes.textContent = '0';
                } else {
                    data.results.forEach((row, i) => {
                        const share = data.total > 0
                            ? ((row.total_score / data.total) * 100).toFixed(1)
                            : '0.0';
                        resultsBody.innerHTML += `
                            <tr>
                                <td>${i + 1}</td>
                                <td><span class="badge-party">${row.party_abbreviation}</span></td>
                                <td class="text-end fw-semibold">${Number(row.total_score).toLocaleString()}</td>
                                <td class="text-end text-muted">${share}%</td>
                            </tr>`;
                    });
                    totalVotes.textContent = Number(data.total).toLocaleString();
                }

                resultsSection.style.display = 'block';
            })
            .catch(() => {
                hideSpinner();
                alert('Failed to load results. Please try again.');
                resultsPlaceholder.style.display = 'block';
            });
    });

    // Auto-trigger state load
    if (stateSelect.value) {
        stateSelect.dispatchEvent(new Event('change'));
    }
</script>
@endpush
