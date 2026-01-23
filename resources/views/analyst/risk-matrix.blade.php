<!DOCTYPE html>
<html>
<head>
    <title>Risk Matrix</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .risk-matrix-cell { 
            height: 100px; 
            display: flex; 
            align-items: center; 
            justify-content: center;
            font-weight: bold;
            cursor: pointer;
            border-radius: 8px;
            margin-bottom: 15px;
            transition: all 0.3s;
        }
        .risk-matrix-cell:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .risk-high { 
            background: linear-gradient(135deg, #ff6b6b, #ff4757); 
            color: white; 
        }
        .risk-medium { 
            background: linear-gradient(135deg, #ffd166, #ffb142); 
        }
        .risk-low { 
            background: linear-gradient(135deg, #06d6a0, #1dd1a1); 
            color: white;
        }
        .select2-container--default .select2-selection--single {
            height: 38px;
            border: 1px solid #ced4da;
        }
        .risk-flag-badge {
            font-size: 0.8em;
            margin: 2px;
        }
        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-danger">
        <div class="container">
            <a class="navbar-brand" href="/">Customs System</a>
            <div class="navbar-text text-white">
                ⚠️ Risk Matrix
                <a href="/analyst/reports" class="btn btn-sm btn-outline-light ms-2">Back to Reports</a>
            </div>
        </div>
    </nav>
    
    <div class="container mt-4">
        <h1>Risk Assessment Matrix</h1>
        <p class="text-muted">Evaluate and manage case risk levels</p>
        
        <!-- Quick Stats -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="text-danger">High Risk</h5>
                        <p class="display-6">{{ $stats['high_risk'] ?? 0 }}</p>
                        <small>cases requiring attention</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="text-warning">Medium Risk</h5>
                        <p class="display-6">{{ $stats['medium_risk'] ?? 0 }}</p>
                        <small>cases for review</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="text-success">Low Risk</h5>
                        <p class="display-6">{{ $stats['low_risk'] ?? 0 }}</p>
                        <small>routine cases</small>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Risk Flag Management -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Manage Risk Flags</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="/analyst/update-risk-flags">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Search and Select Case</label>
                                <select name="case_id" class="form-control select2-case-search" required>
                                    <option value="">Type to search cases...</option>
                                </select>
                                <small class="text-muted">Start typing case ID or vehicle plate</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Risk Level</label>
                                <select name="risk_level" class="form-control">
                                    <option value="">Auto-assess</option>
                                    <option value="high">High Risk</option>
                                    <option value="medium">Medium Risk</option>
                                    <option value="low">Low Risk</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Risk Flags</label>
                        <div class="border rounded p-3">
                            <div class="row">
                                @php
                                    $riskFlags = [
                                        'high_financial' => ['High Financial Risk', 'danger'],
                                        'high_security' => ['High Security Risk', 'danger'],
                                        'suspicious_docs' => ['Suspicious Documents', 'warning'],
                                        'unusual_routing' => ['Unusual Routing', 'warning'],
                                        'new_declarant' => ['New Declarant', 'warning'],
                                        'high_value' => ['High Value Shipment', 'danger'],
                                        'restricted_goods' => ['Restricted Goods', 'danger'],
                                        'VALUE_ANOMALY' => ['Value Anomaly', 'warning'],
                                        'compliance' => ['Compliance Check', 'info'],
                                        'routine' => ['Routine Inspection', 'success']
                                    ];
                                @endphp
                                @foreach($riskFlags as $key => [$label, $color])
                                <div class="col-md-4 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" 
                                               name="risk_flags[]" value="{{ $key }}" id="flag_{{ $key }}">
                                        <label class="form-check-label d-flex align-items-center" for="flag_{{ $key }}">
                                            <span class="badge bg-{{ $color }} me-2">{{ $loop->iteration }}</span>
                                            {{ $label }}
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-shield-check"></i> Update Risk Assessment
                        </button>
                        <button type="reset" class="btn btn-outline-secondary">Clear Form</button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Cases with Risk Flags -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Cases with Risk Flags</h5>
                <div class="text-muted">
                    Showing {{ $cases->count() }} of {{ $totalCases }} cases
                </div>
            </div>
            <div class="card-body">
                @if($cases->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Case ID</th>
                                    <th>Vehicle</th>
                                    <th>Declarant</th>
                                    <th>Risk Flags</th>
                                    <th>Risk Level</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cases as $case)
                                <tr>
                                    <td>
                                        <strong>{{ $case->id }}</strong><br>
                                        <small class="text-muted">{{ $case->status }}</small>
                                    </td>
                                    <td>
                                        {{ $case->vehicle->plate_no ?? 'N/A' }}<br>
                                        <small class="text-muted">{{ $case->origin_country }} → {{ $case->destination_country }}</small>
                                    </td>
                                    <td>{{ $case->declarant->name ?? 'N/A' }}</td>
                                    <td>
                                        @if($case->risk_flags && count($case->risk_flags) > 0)
                                            <div class="d-flex flex-wrap">
                                                @foreach($case->risk_flags as $flag)
                                                <span class="badge risk-flag-badge bg-{{ 
                                                    str_contains($flag, 'high') ? 'danger' : 
                                                    (in_array($flag, ['suspicious_docs', 'unusual_routing', 'new_declarant', 'VALUE_ANOMALY']) ? 'warning' : 
                                                    (in_array($flag, ['routine', 'compliance']) ? 'success' : 'info')) 
                                                }}">
                                                    {{ $flag }}
                                                </span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-muted">No flags</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $riskLevel = 'low';
                                            if ($case->risk_flags) {
                                                if (array_intersect(['high_financial', 'high_security', 'high_value', 'restricted_goods'], $case->risk_flags)) {
                                                    $riskLevel = 'high';
                                                } elseif (array_intersect(['suspicious_docs', 'unusual_routing', 'new_declarant', 'VALUE_ANOMALY'], $case->risk_flags)) {
                                                    $riskLevel = 'medium';
                                                }
                                            }
                                        @endphp
                                        <span class="badge bg-{{ 
                                            $riskLevel === 'high' ? 'danger' : 
                                            ($riskLevel === 'medium' ? 'warning' : 'success') 
                                        }}">
                                            {{ ucfirst($riskLevel) }} Risk
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="/analyst/case/{{ $case->id }}/risk" class="btn btn-outline-primary">
                                                Edit
                                            </a>

                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    @if($cases->hasPages())
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center mt-3">
                            {{-- Previous Page Link --}}
                            @if($cases->onFirstPage())
                                <li class="page-item disabled">
                                    <span class="page-link">Previous</span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $cases->previousPageUrl() }}">Previous</a>
                                </li>
                            @endif
                            
                            {{-- Page Numbers --}}
                            @for ($i = 1; $i <= min(5, $cases->lastPage()); $i++)
                                <li class="page-item {{ $cases->currentPage() == $i ? 'active' : '' }}">
                                    <a class="page-link" href="{{ $cases->url($i) }}">{{ $i }}</a>
                                </li>
                            @endfor
                            
                            {{-- Next Page Link --}}
                            @if($cases->hasMorePages())
                                <li class="page-item">
                                    <a class="page-link" href="{{ $cases->nextPageUrl() }}">Next</a>
                                </li>
                            @else
                                <li class="page-item disabled">
                                    <span class="page-link">Next</span>
                                </li>
                            @endif
                        </ul>
                    </nav>
                    @endif
                @else
                    <div class="text-center py-5">
                        <h5 class="text-muted">No cases with risk flags found</h5>
                        <p>Start by adding risk flags to cases using the form above.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Add Select2 JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <script>
        // Initialize Select2 for case search
        $(document).ready(function() {
            $('.select2-case-search').select2({
                placeholder: 'Type to search cases...',
                allowClear: true,
                ajax: {
                    url: '/analyst/search-cases',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term, // search term
                            page: params.page
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data.items,
                            pagination: {
                                more: data.more
                            }
                        };
                    },
                    cache: true
                },
                minimumInputLength: 2,
                templateResult: formatCase,
                templateSelection: formatCaseSelection
            });
            
            function formatCase(caseData) {
                if (caseData.loading) return caseData.text;
                
                var $container = $(
                    '<div class="case-option">' +
                        '<strong>' + caseData.id + '</strong>' +
                        '<div class="text-muted small">' +
                            'Vehicle: ' + (caseData.vehicle_plate || 'N/A') +
                            ' • Declarant: ' + (caseData.declarant_name || 'N/A') +
                        '</div>' +
                    '</div>'
                );
                
                return $container;
            }
            
            function formatCaseSelection(caseData) {
                return caseData.id || caseData.text;
            }
        });
        
        // Simple risk flag toggling
        document.querySelectorAll('.risk-matrix-cell').forEach(cell => {
            cell.addEventListener('click', function() {
                const riskType = this.getAttribute('data-type');
                const riskLevel = this.getAttribute('data-risk');
                
                // Could load cases for this risk type via AJAX
                alert(`Showing cases with ${riskLevel} ${riskType} risk`);
            });
        });
    </script>
</body>
</html>