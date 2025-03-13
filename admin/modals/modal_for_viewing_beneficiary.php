<!-- View Beneficiary Modal -->
<div class="modal fade" id="viewBeneficiaryModal" tabindex="-1" aria-labelledby="viewBeneficiaryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewBeneficiaryModalLabel">Beneficiary Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Details will be dynamically populated here -->
                <div id="beneficiaryDetails"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


<script>
    function viewBeneficiary(beneficiaryId) {
        console.log("Fetching details for beneficiary ID:", beneficiaryId); // Log the ID
        // Fetch beneficiary details via AJAX
        $.ajax({
            url: '8beneficiaryManagement/get_beneficiary_details.php', // PHP script to fetch details
            type: 'GET',
            data: {
                id: beneficiaryId
            }, // Pass the beneficiary ID
            dataType: 'json', // Expect JSON response
            success: function(response) {
                if (response.status === "success") {
                    // Populate the modal with the fetched data
                    const details = `
                        <p><strong>First Name:</strong> ${response.data.fname}</p>
                        <p><strong>Middle Name:</strong> ${response.data.mname || 'N/A'}</p>
                        <p><strong>Last Name:</strong> ${response.data.lname}</p>
                        <p><strong>Province:</strong> ${response.data.province_name}</p>
                        <p><strong>Municipality:</strong> ${response.data.municipality_name}</p>
                        <p><strong>Barangay:</strong> ${response.data.barangay_name}</p>
                        <p><strong>RSBSA No.:</strong> ${response.data.rsbsa_no || 'N/A'}</p>
                        <p><strong>Contact Number:</strong> ${response.data.contact_no || 'N/A'}</p> 
                        <p><strong>Sex:</strong> ${response.data.sex}</p>
                        <p><strong>Birthdate:</strong> ${response.data.birthdate}</p>
                        <p><strong>Beneficiary Type:</strong> ${response.data.beneficiary_type}</p>
                        <p><strong>Applicable:</strong> ${response.data.if_applicable || 'N/A'}</p>
                        <p><strong>Cooperative Name:</strong> ${response.data.cooperative_name || 'N/A'}</p> <!-- Added Cooperative Name -->
                    `;
                    $('#beneficiaryDetails').html(details); // Insert details into the modal
                    $('#viewBeneficiaryModal').modal('show'); // Show the modal
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
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>