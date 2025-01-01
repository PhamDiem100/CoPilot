<?php
	include("includes/header.php");
?>	
    <div class="container col-md-12">
		<div class="row">
           <div class="col-md-10">
			<div class="row">
				<div class="col-7 d-flex align-items-center">
					<h1 class="me-3">Domain Listings</h1>
					</div>
					<div class="col-5 d-flex align-items-center">
					<div id="message"></div>
				</div>
		
			</div>
			</div>
		</div>
		
        <!-- Form to Add Domains -->
        <!--<form id="add-domain-form" class="mb-4">
            <div class="row">
                <div class="col-md-5">
                    <input type="text" id="domain_name" class="form-control" placeholder="Domain Name" required>
                </div>
                <div class="col-md-3">
                    <input type="number" id="price" class="form-control" placeholder="Price" required>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary w-100">Add Domain</button>
                </div>
            </div>
        </form>-->

        <!-- Domain Listings Table -->
		<div class="col-12 d-flex">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Domain Name</th>
                    <th>Buy Now Option</th>
					<th>Price</th>
					<th>Make Offer Option</th>
					<th>Offer Price</th>
					<th>Minimum Price Option</th>
					<th>Minimum Price</th>
                    <th>Choices</th>
                </tr>
            </thead>
            <tbody id="domain-list">
                <!-- Dynamic Rows -->
            </tbody>
        </table>
		</div>
    </div>

    <!-- Action Modal -->
    <div class="modal fade" id="actionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Actions</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <button id="view-details" class="btn btn-info w-100 mb-2">View Details</button>
                    <button id="edit-listing" class="btn btn-primary w-100 mb-2">Edit Domain</button>
                    <button id="delete-listing" class="btn btn-danger w-100 mb-2">Deactivate Domain</button>
                    <button id="create-checkout-link" class="btn btn-success w-100">Create Checkout Link</button>
                </div>
            </div>
        </div>
    </div>
<style>
.form-switch {
    position: relative;
    display: inline-block;
    width: 34px;
    height: 20px;
}

.form-switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    transition: 0.4s;
    border-radius: 20px;
}

.slider:before {
    position: absolute;
    content: "";
    height: 14px;
    width: 14px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: 0.4s;
    border-radius: 50%;
}

input:checked + .slider {
    background-color: #2196F3;
}

input:checked + .slider:before {
    transform: translateX(14px);
}

  /* Content editable cells */
    .currency {
      display: inline-block;
      position: relative;
      min-width: 80px;
      padding-left: 20px !important; /* Space for the dollar sign */
	  border: none; 
    }

    .currency::before {
      content: "$";
      position: absolute;
      left: 5px;
      top: 50%;
      transform: translateY(-50%);
      color: #333;
	  
	  
    }

    /* Make contenteditable look like an input field */
    .currency[contenteditable="true"] {
      outline: none;
      white-space: nowrap;
    }

    .currency[contenteditable="true"]:empty::before {
      content: "$";
    }
	
.invalid {
    border: 2px solid red !important;
	/*background-color: #ffe6e6 !important;*/
}

.error-message {
    font-size: 16px;
    color: red;
    margin-left: 5px;
}
	
</style>
    <script>
        $(document).ready(function () {
            loadDomains();

            // Load domains from database
            function loadDomains() {
                $.get('fetch_domains.php', function (data) {
                    $('#domain-list').html('');
                    data.forEach(domain => {
                        $('#domain-list').append(`
                            <tr data-id="${domain.ID}">
                                <td contenteditable="true" class="editable-domain-name">${domain.DOMAIN }</td>
                                <!--<td contenteditable="true" class="editable-bopt">${domain.BUYNOW_OPTION }</td>-->
								<td>
									<label class="form-switch">
										<input type="checkbox" class="buynow-switch" data-id="${domain.ID}" ${domain.BUYNOW_OPTION == 1 ? 'checked' : ''}>
										<span class="slider"></span>
									</label>
								</td>
								<td ${domain.BUYNOW_OPTION == 0 ? 'contenteditable="false"' : 'contenteditable="true"'} class="editable-buy-now-price currency"> ${domain.BUYNOW_PRICE }</td>
								<!--<td contenteditable="true" class="editable-price">${domain.MAKE_OFFER_OPTION }</td>-->
								<td>
									<label class="form-switch">
										<input type="checkbox" class="makeoffer-switch" data-id="${domain.ID}" ${domain.MAKE_OFFER_OPTION == 1 ? 'checked' : ''}>
										<span class="slider"></span>
									</label>
								</td>
								<td ${domain.MAKE_OFFER_OPTION == 0 ? 'contenteditable="false"' : 'contenteditable="true"'} class="editable-moprice currency">${domain.MAKE_OFFER_PRICE }</td>
								
								<td>
									<label class="form-switch">
										<input type="checkbox" class="minprice-switch" data-id="${domain.ID}" ${domain.MINIMUM_PRICE_OPTION == 1 ? 'checked' : ''}>
										<span class="slider"></span>
									</label>
								</td>
								<td contenteditable="true" class="editable-miniprice currency">${domain.MINIMUM_PRICE }</td>
                                <td>
                                    <button class="btn btn-secondary btn-sm action-btn" data-id="${domain.ID}">Actions</button>
                                </td>
                            </tr>
                        `);
                    });

                    // Open Actions Modal
                    $('.action-btn').on('click', function () {
                        const domainId = $(this).data('id');
                        $('#actionModal').modal('show');

                        // View Details
                        $('#view-details').off().on('click', function () {
                            window.location.href = `view_details.php?id=${domainId}`;
                        });

                        // Edit Listing
                        $('#edit-listing').off().on('click', function () {
                            const row = $(`tr[data-id="${domainId}"]`);
                            const domainName = row.find('.editable-domain-name').text().trim();

                            const buyNowPrice = row.find('.editable-buy-now-price').text().trim();
							const offerPrice = row.find('.editable-moprice').text().trim();
							const minPrice = row.find('.editable-miniprice').text().trim();
							
							

                            $.post('update_domain.php', { id: domainId, domain_name: domainName, buy_now_price: buyNowPrice, make_offer_price:offerPrice, min_price: minPrice}, function (response) {
                                //alert(response.message);
								$("#message").show();
								$("#message").html(response.message);
                                $('#actionModal').modal('hide');
                                loadDomains();
                            }, 'json');
                        });

                        // Delete Listing
                        $('#delete-listing').off().on('click', function () {
							const firstConfirm = confirm("Are you sure you want to deactivate this Domain?");
							if (firstConfirm) {
							const secondConfirm = confirm("This action is irreversible. Do you really want to proceed?");
								if (secondConfirm) {
									$.post('delete_domain.php', { id: domainId }, function (response) {
										$("#message").show();	
										$("#message").html(response.message);
										$('#actionModal').modal('hide');
										loadDomains();
										setTimeout(function () {
					   						$("#message").hide();
					 					}, 2500);
									}, 'json');
								}
							}
						
                        });

                        // Create Checkout Link
                        $('#create-checkout-link').off().on('click', function () {
                            $.post('create_checkout_link.php', { id: domainId }, function (response) {
                                //alert(`Checkout Link: ${response.link}`);
								$("#message").html("Checkout Link: ${response.link}");
                                $('#actionModal').modal('hide');
                            }, 'json');
                        });
                    });
                }, 'json');
            }

            // Add Domain
            $('#add-domain-form').on('submit', function (e) {
                e.preventDefault();
                const domainName = $('#domain_name').val();
                const price = $('#price').val();

                $.post('add_domain.php', { domain_name: domainName, price: price }, function (response) {
                    //alert(response.message);
					$("#message").html(response.message);
                    if (response.success) {
                        loadDomains();
                        $('#add-domain-form')[0].reset();
                    }
                }, 'json');
            });

            // Inline Edit Save on Blur
            $(document).on('blur', '.editable-domain-name, .editable-buy-now-price,  .editable-moprice, .editable-miniprice ', function () {
                const row = $(this).closest('tr');
                const domainId = row.data('id');
                const domainName = row.find('.editable-domain-name').text().trim();
                const buyNowPrice = row.find('.editable-buy-now-price').text().trim();
				const makeOfferPrice = row.find('.editable-moprice').text().trim();
				const minPrice = row.find('.editable-miniprice').text().trim();

				// Only validate domain name if the blur event is triggered from the .editable-domain-name field
				if ($(this).hasClass('editable-domain-name')) {
					if (!validateDomainName(domainName)) {
							showError($(this), 'Invalid domain name');
							return;
						} else { 
							removeError($(this)); // Remove error if valid
						}
				}else{
				 if (!validatePrice($(this).text().trim())) {
						showError($(this), 'Invalid Price');
						return; // Prevent the AJAX call if validation fails
					} else {
						removeError($(this));  // If valid, remove the error
					}				
				}
                $.post('update_domain.php', { id: domainId, domain_name: domainName, buy_now_price: buyNowPrice, make_offer_price: makeOfferPrice, min_price : minPrice}, function (response) {
                    			$("#message").show();	
					$("#message").html(response.message);
					setTimeout(function () {
					   $("#message").hide();
					 }, 2500);
                }, 'json');
            });
			
			$(document).on('change', '.buynow-switch', function () {
				const row = $(this).closest('tr');
				const domainId = $(this).data('id');
				const domainName = row.find('.editable-domain-name').text().trim();
				const buynowOption = $(this).is(':checked') ? 1 : 0;

				// Send the updated value to the server
				$.post('update_domain.php', { id: domainId, domain_name: domainName, buy_option: buynowOption }, function (response) {
					if(response.success){
						loadDomains();
					}
                    
					$("#message").show();
					setTimeout(function () {
					   $("#message").hide();
					 }, 2500);
					$("#message").html(response.message);
                }, 'json');
			});
			
			$(document).on('change', '.makeoffer-switch', function () {
				const row = $(this).closest('tr');
				const domainId = $(this).data('id');
				const domainName = row.find('.editable-domain-name').text().trim();
				const makeofferOption = $(this).is(':checked') ? 1 : 0;

				// Send the updated value to the server
				$.post('update_domain.php', { id: domainId, domain_name: domainName, makeoffer_option: makeofferOption }, function (response) {
                    if(response.success){
						loadDomains();
					}
					$("#message").show();
					$("#message").html(response.message);
					setTimeout(function () {
					   $("#message").hide();
					 }, 2500);
					
                }, 'json');
			});
			
			$(document).on('change', '.minprice-switch', function () {
				const row = $(this).closest('tr');
				const domainId = $(this).data('id');
				const domainName = row.find('.editable-domain-name').text().trim();
				const minOfferOption = $(this).is(':checked') ? 1 : 0;

				// Send the updated value to the server
				$.post('update_domain.php', { id: domainId, domain_name: domainName, minoffer_option: minOfferOption }, function (response) {
                    			$("#message").show();
					$("#message").html(response.message);
				setTimeout(function () {
					   $("#message").hide();
					 }, 2500);
                      		}, 'json');
			});
        });
		


		// Function to validate individual price (could be a simple check or more complex validation)
		function validatePrice(price) {
			// Example validation: price should be a valid positive number (can extend as per requirement)
			const pricePattern = /^[0-9]*\.?[0-9]+$/;  // Regex to allow valid decimals
			return pricePattern.test(price) && parseFloat(price) > 0;  // Ensure price is positive
		}
		
		// Function to show error message and add 'invalid' class to the field
		function showError(element, message) { 
			console.log("EL# "+ element + " Msg# "+ message);
			const $element = $(element);
			$element.addClass('invalid');
			$("#message").show();
			$("#message").html('<span class="error-message" style="color: red;">' + message + '</span>');
			  setTimeout(function () {
			   $("#message").hide();
			  }, 2500);
			
		}

		// Function to remove error message and 'invalid' class
		function removeError(element) { console.log("@ RM");
			const $element = $(element);
			$element.removeClass('invalid');
			$("#message").html("");
		}	

		// Function to validate domain name
		function validateDomainName(domain) {
			// Regular expression for valid domain name
			const domainPattern = /^(?!\-)(?!.*\-\.)(?!.*\-\-)(?!\.\.)(?!.*\-$)[A-Za-z0-9-]+(\.[A-Za-z]{2,})+$/;
			
			return domainPattern.test(domain);  // Test domain name against the regex
		}		
    </script>
<?php
include("includes/footer.php");
