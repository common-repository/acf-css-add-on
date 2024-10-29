(function( $ ) {

	'use strict';

	//get the model
	var modal 		= document.getElementById('acfca_modal');

	// Get the button that opens the modal. When the user clicks on the button, open the modal 
	$(".m-opener").on("click" , function(e){
	  	modal.style.display = "block";

	  	var term 		= 	$(this).attr('data-id');
	  	$('input[name="acfca_field_id"]').val(term);

	  	var fieldClass 	= 	$(this).find('input').val();
	  	$('input[name="acfca_field_class"]').val(fieldClass);
	});

	// When the user clicks anywhere outside of the modal, close it and go to the form action
	window.onclick 	= function(event) {
	  	if (event.target == modal) {
	   		modal.style.display = "none";
	  	}
	}	

	$('#acfca_modal .close').on('click', function() {
	    $('#acfca_modal').hide();
	});

	//ajax submit classes
	$('#add_class_form').on("submit",function(event) {
	    event.preventDefault();
	    var fieldId 		= 	"#" + $('#acfca_field_id').val();
	    var classes 		= 	$('#acfca_field_class').val();
	    modal.style.display = 	"none";
	    var formdata 		= 	$("#add_class_form").serialize();//alert(formdata);
	    $.ajax({
	        url: 	ajaxurl,
	        type: 	'POST',
	        data: 	formdata,
	        success: function(result) {
	        	var selector = 	fieldId + " > input";
	        	$(selector).val(classes);
	        },
	        error: function(error) {
	            alert("error");
	        }
	    }); 
	});
})( jQuery );
