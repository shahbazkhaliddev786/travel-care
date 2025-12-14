<!-- Reusable Bulk Actions Component -->
<div id="bulkActionsCard" class="card mb-3" style="display: none;">
    <div class="card-body">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
            <div>
                <h6 class="mb-2">
                    <i class="bi bi-box-seam me-2"></i>
                    Bulk Actions
                </h6>
                <small class="text-muted">
                    <span id="selectedCount">0</span> selected
                </small>
            </div>
            <div class="d-flex flex-wrap gap-2">
                @if(($showApprovalButtons ?? false) === true)
                <button type="button" class="btn btn-outline-success" onclick="bulkAction('activate')">
                    <i class="bi bi-check-lg me-1"></i>
                    Approve
                </button>
                <button type="button" class="btn btn-outline-danger" onclick="bulkAction('deactivate')">
                    <i class="bi bi-x-lg me-1"></i>
                    Reject
                </button>
                @else
                <button type="button" class="btn btn-outline-success" onclick="bulkAction('enable_video')">
                    <i class="bi bi-camera-video me-1"></i>
                    Enable Video
                </button>
                <button type="button" class="btn btn-outline-warning" onclick="bulkAction('disable_video')">
                    <i class="bi bi-camera-video-off me-1"></i>
                    Disable Video
                </button>
                @endif
                <button type="button" class="btn btn-outline-danger" onclick="bulkAction('delete')">
                    <i class="bi bi-trash me-1"></i>
                    Delete Selected
                </button>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    const BULK_ACTION_URL = "{{ $bulkActionUrl }}";
    const LABEL_PLURAL = "{{ $labelPlural ?? 'items' }}";

    function getSelectedIds() {
        return Array.from(document.querySelectorAll('.bulk-checkbox:checked')).map(cb => parseInt(cb.value, 10));
    }

    function updateBulkCard() {
        const bulkCard = document.getElementById('bulkActionsCard');
        const selectedCountEl = document.getElementById('selectedCount');
        const selectedCount = getSelectedIds().length;
        if (!bulkCard || !selectedCountEl) return;
        bulkCard.style.display = selectedCount > 0 ? 'block' : 'none';
        selectedCountEl.textContent = selectedCount;
    }

    function syncSelectAllStates() {
        const checkboxes = Array.from(document.querySelectorAll('.bulk-checkbox'));
        const allChecked = checkboxes.length > 0 && checkboxes.every(cb => cb.checked);
        const selectAll = document.getElementById('selectAll');
        const selectAllTable = document.getElementById('selectAllTable');
        if (selectAll) selectAll.checked = allChecked;
        if (selectAllTable) selectAllTable.checked = allChecked;
    }

    function toggleAllCheckboxes(checked) {
        document.querySelectorAll('.bulk-checkbox').forEach(cb => { cb.checked = checked; });
        updateBulkCard();
        syncSelectAllStates();
    }

    function handleCheckboxes() {
        document.querySelectorAll('.bulk-checkbox').forEach(cb => {
            cb.addEventListener('change', function() {
                updateBulkCard();
                syncSelectAllStates();
            });
        });
    }

    window.bulkAction = function(action) {
        const selectedIds = getSelectedIds();
        if (selectedIds.length === 0) {
            if (typeof showAlert === 'function') {
                showAlert('warning', `Please select ${LABEL_PLURAL} first`);
            } else {
                alert(`Please select ${LABEL_PLURAL} first`);
            }
            return;
        }

        let actionText = '';
        switch(action) {
            case 'approve':
                actionText = 'approve';
                break;
            case 'reject':
                actionText = 'reject';
                break;
            case 'activate':
                actionText = 'approve';
                break;
            case 'deactivate':
                actionText = 'reject';
                break;
            case 'enable_video':
                actionText = 'enable video consultation for';
                break;
            case 'disable_video':
                actionText = 'disable video consultation for';
                break;
            case 'delete':
                actionText = 'delete';
                break;
            default:
                actionText = 'perform the selected action on';
        }

        if (!confirm(`Are you sure you want to ${actionText} ${selectedIds.length} ${LABEL_PLURAL}?`)) {
            return;
        }

        // Perform bulk action via POST
        fetch(BULK_ACTION_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ action: action, ids: selectedIds })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (typeof showAlert === 'function') {
                    showAlert('success', data.message || 'Bulk action completed successfully');
                }
                setTimeout(() => { location.reload(); }, 1200);
            } else {
                if (typeof showAlert === 'function') {
                    showAlert('error', data.message || 'Failed to perform bulk action');
                } else {
                    alert(data.message || 'Failed to perform bulk action');
                }
            }
        })
        .catch(error => {
            console.error('Bulk action error:', error);
            if (typeof showAlert === 'function') {
                showAlert('error', 'An error occurred while performing bulk action');
            } else {
                alert('An error occurred while performing bulk action');
            }
        });
    };

    document.addEventListener('DOMContentLoaded', function() {
        // Handle select-all in header area
        const selectAll = document.getElementById('selectAll');
        if (selectAll) {
            selectAll.addEventListener('change', function() {
                toggleAllCheckboxes(this.checked);
                const selectAllTable = document.getElementById('selectAllTable');
                if (selectAllTable) selectAllTable.checked = this.checked;
            });
        }

        // Handle select-all in table header
        const selectAllTable = document.getElementById('selectAllTable');
        if (selectAllTable) {
            selectAllTable.addEventListener('change', function() {
                toggleAllCheckboxes(this.checked);
                const selectAllHeader = document.getElementById('selectAll');
                if (selectAllHeader) selectAllHeader.checked = this.checked;
            });
        }

        // Individual checkbox handlers
        handleCheckboxes();

        // Initialize card state
        updateBulkCard();
    });
})();
</script>