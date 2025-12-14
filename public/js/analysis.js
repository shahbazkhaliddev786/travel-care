// Analysis Page JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Analysis items functionality
    const analysisItems = document.querySelectorAll('.analysis-item');
    
    // Right panel elements
    const detailsTitle = document.querySelector('.details-title');
    const detailsDescription = document.querySelector('.details-description');
    const patientValue = document.querySelector('.info-value');
    const dateValue = document.querySelectorAll('.info-value')[1];
    const downloadLink = document.querySelector('.download-report-btn');
    
    // Sample analysis data
    const analysisData = {
        'Justin Aminoff': {
            type: 'Estimated (E2)',
            date: '10 August 2023',
            description: 'This test measures the amount of estradiol (E2), the form of estrogen made mainly by the ovaries. E2 plays a key role in the development of the female reproductive system, including the uterus, fallopian tubes, vagina, and breasts. E2 is also important for bone and cardiovascular health in both men and women.',
            downloadFile: 'Justin_Aminoff_E2_Report.pdf'
        },
        'Wilson Davis': {
            type: 'Estimated (E2)',
            date: '10 August 2023',
            description: 'This test measures the amount of estradiol (E2), the form of estrogen made mainly by the ovaries. E2 plays a key role in the development of the female reproductive system, including the uterus, fallopian tubes, vagina, and breasts. E2 is also important for bone and cardiovascular health in both men and women.',
            downloadFile: 'Wilson_Davis_E2_Report.pdf'
        },
        'Roger Rosser': {
            type: 'Estimated (E2)',
            date: '10 August 2023',
            description: 'This comprehensive test measures multiple estradiol (E2) levels over time, providing detailed insights into hormonal patterns. E2 plays a key role in the development of the female reproductive system and is crucial for bone and cardiovascular health.',
            downloadFile: 'Roger_Rosser_E2_Comprehensive_Report.pdf'
        },
        'Wilson Samson': {
            type: 'Estimated (E2)',
            date: '10 August 2023',
            description: 'This test measures the amount of estradiol (E2), the form of estrogen made mainly by the ovaries. E2 plays a key role in the development of the female reproductive system, including the uterus, fallopian tubes, vagina, and breasts. E2 is also important for bone and cardiovascular health in both men and women.',
            downloadFile: 'Wilson_Samson_E2_Report.pdf'
        },
        'Kadie Dokidis': {
            type: 'Estimated (E2)',
            date: '7 July 2023',
            description: 'This test measures the amount of estradiol (E2), the form of estrogen made mainly by the ovaries. E2 plays a key role in the development of the female reproductive system, including the uterus, fallopian tubes, vagina, and breasts. E2 is also important for bone and cardiovascular health in both men and women.',
            downloadFile: 'Kadie_Dokidis_E2_Report.pdf'
        },
        'Terry Dorwer': {
            type: 'Estimated (E2)',
            date: '7 July 2023',
            description: 'This test measures the amount of estradiol (E2), the form of estrogen made mainly by the ovaries. E2 plays a key role in the development of the female reproductive system, including the uterus, fallopian tubes, vagina, and breasts. E2 is also important for bone and cardiovascular health in both men and women.',
            downloadFile: 'Terry_Dorwer_E2_Report.pdf'
        },
        'Charlie Aminoff': {
            type: 'Estimated (E2)',
            date: '7 July 2023',
            description: 'This comprehensive test measures multiple estradiol (E2) levels over time, providing detailed insights into hormonal patterns. E2 plays a key role in the development of the female reproductive system and is crucial for bone and cardiovascular health.',
            downloadFile: 'Charlie_Aminoff_E2_Comprehensive_Report.pdf'
        }
    };
    
    // Initialize with first item selected
    if (analysisItems.length > 0) {
        const firstItem = analysisItems[0];
        const firstPatientName = firstItem.querySelector('.analysis-name').textContent.trim();
        const firstData = analysisData[firstPatientName];
        
        if (firstData) {
            updateAnalysisDetails(firstData, firstPatientName);
        }
    }
    
    // Add click event to all analysis items
    analysisItems.forEach(item => {
        item.addEventListener('click', function() {
            // Remove previous active state
            document.querySelectorAll('.analysis-item.active').forEach(el => {
                el.classList.remove('active');
            });
            
            // Add active state to clicked item
            this.classList.add('active');
            
            // Get patient name from the clicked item
            const patientName = this.querySelector('.analysis-name').textContent.trim();
            
            // Get analysis data
            const data = analysisData[patientName];
            
            if (data) {
                // Check if we're on mobile/tablet (768px or below)
                if (window.innerWidth <= 768) {
                    // Show analysis details modal
                    showAnalysisDetailsModal(data, patientName);
                } else {
                    // Update analysis details in right panel
                    updateAnalysisDetails(data, patientName);
                }
            }
        });
    });
    
    function updateAnalysisDetails(data, patientName) {
        // Update analysis details with animation
        const rightPanel = document.querySelector('.analysis-right-panel');
        
        if (rightPanel) {
            rightPanel.style.opacity = '0.7';
            rightPanel.style.transform = 'translateY(5px)';
            
            setTimeout(() => {
                // Update title
                if (detailsTitle) {
                    detailsTitle.textContent = data.type;
                }
                
                // Update description
                if (detailsDescription) {
                    detailsDescription.textContent = data.description;
                }
                
                // Update patient name
                if (patientValue) {
                    patientValue.textContent = patientName;
                }
                
                // Update date
                if (dateValue) {
                    dateValue.textContent = data.date;
                }
                
                // Update download link
                if (downloadLink) {
                    const icon = downloadLink.querySelector('i');
                    downloadLink.innerHTML = '';
                    if (icon) {
                        downloadLink.appendChild(icon);
                    } else {
                        downloadLink.innerHTML = '<i class="fas fa-download"></i>';
                    }
                    downloadLink.innerHTML += ' ' + data.downloadFile;
                }
                
                // Restore animation
                rightPanel.style.opacity = '1';
                rightPanel.style.transform = 'translateY(0)';
                rightPanel.style.transition = 'all 0.3s ease';
            }, 150);
        }
    }
    
    // Analysis Details Modal functionality
    function showAnalysisDetailsModal(data, patientName) {
        const detailsModal = document.getElementById('analysisDetailsModal');
        const modalTitle = document.getElementById('modalAnalysisTitle');
        const modalDescription = document.getElementById('modalAnalysisDescription');
        const modalPatientName = document.getElementById('modalPatientName');
        const modalDate = document.getElementById('modalAnalysisDate');
        const modalDownloadBtn = document.getElementById('modalDownloadBtn');
        const modalDownloadText = document.getElementById('modalDownloadText');
        
        if (detailsModal) {
            // Update modal content
            if (modalTitle) {
                modalTitle.textContent = data.type;
            }
            
            if (modalDescription) {
                modalDescription.textContent = data.description;
            }
            
            if (modalPatientName) {
                modalPatientName.textContent = patientName;
            }
            
            if (modalDate) {
                modalDate.textContent = data.date;
            }
            
            if (modalDownloadText) {
                modalDownloadText.textContent = data.downloadFile;
            }
            
            // Show modal
            detailsModal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
    }
    
    // Analysis Details Modal close functionality
    const detailsModal = document.getElementById('analysisDetailsModal');
    const closeDetailsBtn = document.getElementById('closeDetailsModal');
    
    function closeDetailsModal() {
        if (detailsModal) {
            detailsModal.classList.remove('active');
            document.body.style.overflow = 'auto';
        }
    }
    
    if (closeDetailsBtn) {
        closeDetailsBtn.addEventListener('click', closeDetailsModal);
    }
    
    // Close details modal when clicking outside
    if (detailsModal) {
        detailsModal.addEventListener('click', function(e) {
            if (e.target === detailsModal) {
                closeDetailsModal();
            }
        });
    }
    

    
    // Handle modal download button
    const modalDownloadBtn = document.getElementById('modalDownloadBtn');
    if (modalDownloadBtn) {
        modalDownloadBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const downloadText = document.getElementById('modalDownloadText');
            if (downloadText) {
                const fileName = downloadText.textContent;
                console.log('Downloading:', fileName);
                alert('Download started: ' + fileName);
            }
        });
    }
    
    // Add smooth scrolling for pagination
    const paginationLinks = document.querySelectorAll('.page-link');
    paginationLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Remove active state from all pagination items
            document.querySelectorAll('.page-item.active').forEach(item => {
                item.classList.remove('active');
            });
            
            // Add active state to clicked item
            this.parentElement.classList.add('active');
            
            // Scroll to top of analysis list
            const analysisList = document.querySelector('.analysis-list');
            if (analysisList) {
                analysisList.scrollTop = 0;
            }
        });
    });
    
    // Download button functionality
    const downloadBtns = document.querySelectorAll('.download-btn');
    downloadBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation(); // Prevent triggering the analysis item click
            
            // Get the analysis item
            const analysisItem = this.closest('.analysis-item');
            const patientName = analysisItem.querySelector('.analysis-name').textContent.trim();
            const data = analysisData[patientName];
            
            if (data) {
                // Simulate download
                console.log('Downloading:', data.downloadFile);
                // In a real application, you would trigger the actual download here
                alert('Download started: ' + data.downloadFile);
            }
        });
    });
    
    // Upload Analysis Modal Functionality
    const uploadBtn = document.getElementById('uploadAnalysisBtn');
    const modal = document.getElementById('uploadAnalysisModal');
    const closeBtn = document.getElementById('closeModal');
    const form = document.getElementById('uploadAnalysisForm');
    const fileInput = document.getElementById('analysisFile');
    const fileUploadArea = document.querySelector('.file-upload-area');
    const uploadText = document.querySelector('.upload-text');
    
    // Open modal
    if (uploadBtn) {
        uploadBtn.addEventListener('click', function() {
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        });
    }
    
    // Close modal
    function closeModal() {
        modal.classList.remove('active');
        document.body.style.overflow = 'auto';
        // Reset form
        if (form) {
            form.reset();
            uploadText.textContent = 'Upload the file';
        }
    }
    
    if (closeBtn) {
        closeBtn.addEventListener('click', closeModal);
    }
    
    // Close modal when clicking outside
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeModal();
            }
        });
    }
    
    // Close modal with Escape key (updated to handle both modals)
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            if (detailsModal && detailsModal.classList.contains('active')) {
                closeDetailsModal();
            } else if (modal && modal.classList.contains('active')) {
                closeModal();
            }
        }
    });
    
    // File upload area click handler
    if (fileUploadArea) {
        fileUploadArea.addEventListener('click', function() {
            fileInput.click();
        });
    }
    
    // File input change handler
    if (fileInput) {
        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                uploadText.textContent = file.name;
                fileUploadArea.style.borderColor = '#009999';
                fileUploadArea.style.background = '#e8fffe';
            } else {
                uploadText.textContent = 'Upload the file';
                fileUploadArea.style.borderColor = '#00b2b2';
                fileUploadArea.style.background = '#f0fffe';
            }
        });
    }
    
    // Drag and drop functionality
    if (fileUploadArea) {
        fileUploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.style.borderColor = '#009999';
            this.style.background = '#e8fffe';
        });
        
        fileUploadArea.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.style.borderColor = '#00b2b2';
            this.style.background = '#f0fffe';
        });
        
        fileUploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                const file = files[0];
                uploadText.textContent = file.name;
                this.style.borderColor = '#009999';
                this.style.background = '#e8fffe';
            }
        });
    }
    
    // Form submission
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get form data
            const formData = new FormData();
            const analysisName = document.getElementById('analysisName').value;
            const analysisDescription = document.getElementById('analysisDescription').value;
            const patientName = document.getElementById('patientName').value;
            const file = fileInput.files[0];
            
            // Basic validation
            if (!analysisName.trim()) {
                alert('Please enter an analysis name.');
                return;
            }
            
            if (!analysisDescription.trim()) {
                alert('Please enter a description.');
                return;
            }
            
            if (!file) {
                alert('Please select a file to upload.');
                return;
            }
            
            if (!patientName.trim()) {
                alert('Please enter a patient name.');
                return;
            }
            
            // Prepare form data
            formData.append('name', analysisName);
            formData.append('description', analysisDescription);
            formData.append('file', file);
            formData.append('patient_name', patientName);
            
            // Simulate form submission
            console.log('Form Data:', {
                name: analysisName,
                description: analysisDescription,
                fileName: file.name,
                patientName: patientName
            });
            
            // Show success message
            alert('Analysis uploaded successfully and sent to ' + patientName + '!');
            
            // Close modal
            closeModal();
            
            // In a real application, you would send this data to the server
            // fetch('/upload-analysis', {
            //     method: 'POST',
            //     body: formData
            // }).then(response => {
            //     // Handle response
            // });
        });
    }
});