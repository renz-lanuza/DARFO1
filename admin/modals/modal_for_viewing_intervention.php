<!-- Modal for Viewing Interventions -->
<div class="modal fade" id="viewInterventionsModal" tabindex="-1" role="dialog" aria-labelledby="viewInterventionsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content shadow-lg rounded">
            <div class="modal-header text-white" style="background-color: #0D7C66;">
                <h5 class="modal-title" id="viewInterventionsModalLabel">Interventions Received</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <div id="modalContent" class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p class="mt-2">Fetching interventions, please wait...</p>
                </div>
            </div>
            <div class="modal-footer border-top">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Optional Custom Styles -->
<style>
    .modal-content {
        border-radius: 10px;
        overflow: hidden;
    }
    .modal-footer {
        border-top: 1px solid #ddd;
    }
</style>
