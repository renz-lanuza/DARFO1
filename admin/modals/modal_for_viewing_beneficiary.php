<!-- View Beneficiary Modal -->
<div class="modal fade" id="viewBeneficiaryModal" tabindex="-1" aria-labelledby="viewBeneficiaryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content shadow-lg rounded-3">
            <div class="modal-header" style="background-color: #0D7C66;">
                <h5 class="modal-title text-white" id="viewBeneficiaryModalLabel">
                    <i class="fas fa-user"></i> Beneficiary Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div id="beneficiaryDetails" class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <tbody>
                            <!-- Details will be inserted dynamically -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer bg-light justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function formatDateToWords(dateString) {
        if (!dateString) return 'N/A'; // Handle empty values

        const months = [
            "January", "February", "March", "April", "May", "June",
            "July", "August", "September", "October", "November", "December"
        ];

        const dateObj = new Date(dateString);
        if (isNaN(dateObj)) return 'Invalid Date';

        const day = dateObj.getDate();
        const month = months[dateObj.getMonth()];
        const year = dateObj.getFullYear();

        return `${month} ${day}, ${year}`;
    }

    function viewBeneficiary(beneficiaryId) {
        console.log("Fetching details for beneficiary ID:", beneficiaryId);

        // Fetch beneficiary details via AJAX
        $.ajax({
            url: '8beneficiaryManagement/get_beneficiary_details.php',
            type: 'GET',
            data: {
                id: beneficiaryId
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === "success") {
                    const formattedBirthdate = formatDateToWords(response.data.birthdate);

                    // Check the beneficiary category (normalize to lower case)
                    const beneficiaryCategory = response.data.beneficiary_category.trim().toLowerCase();
                    let cooperativeName = '';

                    // Only add Cooperative Name if beneficiary category is not "individual"
                    if (beneficiaryCategory !== 'individual') {
                        cooperativeName = `
                            <tr><td><i class="fas fa-handshake"></i> <strong>Cooperative Name:</strong></td><td>${response.data.cooperative_name || 'N/A'}</td></tr>
                        `;
                    }

                    // Populate table rows dynamically
                    const details = `
                        <tr><td><i class="fas fa-user"></i> <strong>First Name:</strong></td><td>${response.data.fname}</td></tr>
                        <tr><td><i class="fas fa-user"></i> <strong>Middle Name:</strong></td><td>${response.data.mname || 'N/A'}</td></tr>
                        <tr><td><i class="fas fa-user"></i> <strong>Last Name:</strong></td><td>${response.data.lname}</td></tr>
                        <tr><td><i class="fas fa-map-marker-alt"></i> <strong>Province:</strong></td><td>${response.data.province_name}</td></tr>
                        <tr><td><i class="fas fa-city"></i> <strong>Municipality:</strong></td><td>${response.data.municipality_name}</td></tr>
                        <tr><td><i class="fas fa-home"></i> <strong>Barangay:</strong></td><td>${response.data.barangay_name}</td></tr>
                        <tr><td><i class="fas fa-id-card"></i> <strong>RSBSA No.:</strong></td><td>${response.data.rsbsa_no || 'N/A'}</td></tr>
                        <tr><td><i class="fas fa-phone"></i> <strong>Contact Number:</strong></td><td>${response.data.contact_no || 'N/A'}</td></tr>
                        <tr><td><i class="fas fa-venus-mars"></i> < strong>Sex:</strong></td><td>${response.data.sex}</td></tr>
                        <tr><td><i class="fas fa-birthday-cake"></i> <strong>Birthdate:</strong></td><td>${formattedBirthdate}</td></tr>
                        <tr><td><i class="fas fa-users"></i> <strong>Beneficiary Type:</strong></td><td>${response.data.beneficiary_type}</td></tr>
                        <tr><td><i class="fas fa-check-circle"></i> <strong>Applicable:</strong></td><td>${response.data.if_applicable || 'N/A'}</td></tr>
                        ${cooperativeName} <!-- Conditional Cooperative Name -->
                    `;

                    $('#beneficiaryDetails tbody').html(details);
                    $('#viewBeneficiaryModal').modal('show');
                } else {
                    alert('Error fetching beneficiary details: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", xhr.responseText);
                alert('An error occurred while fetching beneficiary details.');
            }
        });
    }
</script>

<!-- FontAwesome Icons -->
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
