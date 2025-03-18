<!-- Bootstrap Modal -->
<div class="modal fade" id="interventionModal" tabindex="-1" aria-labelledby="modalTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Intervention Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalBody">
                <!-- Intervention data will be loaded here -->
            </div>
            <div class="modal-footer bg-light justify-content-center">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function closeModal() {
        var modalElement = document.getElementById('interventionModal');
        var modalInstance = bootstrap.Modal.getInstance(modalElement); 

        if (modalInstance) {
            modalInstance.hide(); // Hide the modal properly
        } else {
            new bootstrap.Modal(modalElement).hide();
        }

        // Ensure modal is fully removed from the DOM after closing
        setTimeout(() => {
            modalElement.classList.remove('show');
            modalElement.setAttribute('aria-hidden', 'true');
            modalElement.style.display = 'none';
            document.body.classList.remove('modal-open');
            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
        }, 300); // Allow Bootstrap's transition effect
    }
</script>
