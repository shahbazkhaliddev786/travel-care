@extends('layouts.mainLayout')

@section('title', 'Medical Analysis - TravelCare')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/analysis.css') }}">
@endsection

@section('content')
<div class="analysis-container">
    <div class="analysis-content">
        <!-- Left Panel - Analysis List -->
        <div class="analysis-left-panel">
            <div class="analysis-header">
                <h1 class="analysis-title">Analyses</h1>
                <button class="btn-primary" id="uploadAnalysisBtn">
                    <i class="fas fa-plus"></i>
                    Upload New Analysis
                </button>
            </div>
            
            <div class="analysis-list">
                <!-- August 2023 -->
                <div class="analysis-date-group">
                    <h3 class="analysis-date">10 August 2023</h3>
                    
                    <div class="analysis-item active">
                        <div class="analysis-info">
                            <h4 class="analysis-name">Justin Aminoff</h4>
                            <p class="analysis-type">Estimated (E2)</p>
                        </div>
                        <div class="analysis-actions">
                            <button class="download-btn">
                                <i class="fas fa-download"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="analysis-item">
                        <div class="analysis-info">
                            <h4 class="analysis-name">Wilson Davis</h4>
                            <p class="analysis-type">Estimated (E2)</p>
                        </div>
                        <div class="analysis-actions">
                            <button class="download-btn">
                                <i class="fas fa-download"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="analysis-item">
                        <div class="analysis-info">
                            <h4 class="analysis-name">Roger Rosser</h4>
                            <p class="analysis-type">Estimated (E2) - Estimated (E2) - Estimated (E2)</p>
                        </div>
                        <div class="analysis-actions">
                            <button class="download-btn">
                                <i class="fas fa-download"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="analysis-item">
                        <div class="analysis-info">
                            <h4 class="analysis-name">Wilson Samson</h4>
                            <p class="analysis-type">Estimated (E2)</p>
                        </div>
                        <div class="analysis-actions">
                            <button class="download-btn">
                                <i class="fas fa-download"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- July 2023 -->
                <div class="analysis-date-group">
                    <h3 class="analysis-date">7 July 2023</h3>
                    
                    <div class="analysis-item">
                        <div class="analysis-info">
                            <h4 class="analysis-name">Kadie Dokidis</h4>
                            <p class="analysis-type">Estimated (E2)</p>
                        </div>
                        <div class="analysis-actions">
                            <button class="download-btn">
                                <i class="fas fa-download"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="analysis-item">
                        <div class="analysis-info">
                            <h4 class="analysis-name">Terry Dorwer</h4>
                            <p class="analysis-type">Estimated (E2)</p>
                        </div>
                        <div class="analysis-actions">
                            <button class="download-btn">
                                <i class="fas fa-download"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="analysis-item">
                        <div class="analysis-info">
                            <h4 class="analysis-name">Charlie Aminoff</h4>
                            <p class="analysis-type">Estimated (E2) - Estimated (E2) - Estimated (E2)</p>
                        </div>
                        <div class="analysis-actions">
                            <button class="download-btn">
                                <i class="fas fa-download"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Pagination -->
            <div class="analysis-pagination">
                <nav aria-label="Analysis pagination">
                    <ul class="pagination">
                        <li class="page-item disabled">
                            <span class="page-link">&lt;</span>
                        </li>
                        <li class="page-item active">
                            <span class="page-link">1</span>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="#">2</a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="#">3</a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="#">4</a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="#">5</a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="#">6</a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="#">7</a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="#">8</a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="#">9</a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="#">10</a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="#">11</a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="#">12</a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="#">13</a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="#">14</a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="#">&gt;</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
        
        <!-- Right Panel - Analysis Details -->
        <div class="analysis-right-panel">
            <div class="analysis-details-card">
                <h2 class="details-title">Estimated (E2)</h2>
                
                <div class="details-content">
                    <p class="details-description">
                        This test measures the amount of estradiol (E2), the form of estrogen made mainly by the ovaries. E2 plays a key role in the development of the female reproductive system, including the uterus, fallopian tubes, vagina, and breasts. E2 is also important for bone and cardiovascular health in both men and women.
                    </p>
                    
                    <div class="details-info">
                        <div class="info-row">
                            <span class="info-label">Patient:</span>
                            <span class="info-value">Wilson Davis</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Date:</span>
                            <span class="info-value">10 August 2023</span>
                        </div>
                    </div>
                    
                    <div class="download-section">
                        <a href="#" class="download-report-btn">
                            <i class="fas fa-download"></i>
                            Key/Engagement generated report.pdf
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Upload Analysis Modal -->
<div id="uploadAnalysisModal" class="modal-overlay">
    <div class="modal-container">
        <div class="modal-header">
            <h2 class="modal-title">Upload Analysis</h2>
            <button class="modal-close" id="closeModal">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <form id="uploadAnalysisForm">
                <div class="form-group">
                    <label for="analysisName" class="form-label">Name</label>
                    <input type="text" id="analysisName" class="form-input" placeholder="Analysis Name">
                </div>
                
                <div class="form-group">
                    <label for="analysisDescription" class="form-label">Add Description</label>
                    <textarea id="analysisDescription" class="form-textarea" placeholder="Write Short Overall Description About Analysis"></textarea>
                </div>
                
                <div class="form-group">
                    <div class="file-upload-area">
                        <div class="file-upload-content">
                            <i class="fas fa-cloud-upload-alt upload-icon"></i>
                            <span class="upload-text">Upload the file</span>
                        </div>
                        <input type="file" id="analysisFile" class="file-input" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="patientName" class="form-label">Patient Name</label>
                    <input type="text" id="patientName" class="form-input" placeholder="@Patient">
                </div>
                
                <button type="submit" class="submit-btn">
                    Send to a patient
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Analysis Details Modal -->
<div id="analysisDetailsModal" class="modal-overlay analysis-details-modal">
    <div class="modal-container analysis-details-container">
        <div class="modal-header">
            <h2 class="modal-title" id="modalAnalysisTitle">Estimated (E2)</h2>
            <button class="modal-close" id="closeDetailsModal">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body analysis-modal-body">
            <div class="modal-details-content">
                <p class="modal-details-description" id="modalAnalysisDescription">
                    This test measures the amount of estradiol (E2), the form of estrogen made mainly by the ovaries. E2 plays a key role in the development of the female reproductive system, including the uterus, fallopian tubes, vagina, and breasts. E2 is also important for bone and cardiovascular health in both men and women.
                </p>
                
                <div class="modal-details-info">
                    <div class="modal-info-row">
                        <span class="modal-info-label">Patient:</span>
                        <span class="modal-info-value" id="modalPatientName">Wilson Davis</span>
                    </div>
                    <div class="modal-info-row">
                        <span class="modal-info-label">Date:</span>
                        <span class="modal-info-value" id="modalAnalysisDate">10 August 2023</span>
                    </div>
                </div>
                
                <div class="modal-download-section">
                    <a href="#" class="modal-download-btn" id="modalDownloadBtn">
                        <i class="fas fa-download"></i>
                        <span id="modalDownloadText">Key/Engagement generated report.pdf</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
    <script src="{{ asset('js/analysis.js') }}"></script>
@endsection