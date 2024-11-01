(function( $ ) {

    $(function() {
         
        // Add Color Picker to all inputs that have 'color-field' class
        $( '.wpps-color-picker' ).wpColorPicker();
         
    });	
	
	$('.layout_blocks').on('change', function (){
		
		var counter = this.value; 
		var ptypes = $('.dropdown_posttypes').val();
		nonce = myAjax.ajax_nonce;
		$.ajax({
         type : "post",
         dataType : "html",
         url : myAjax.ajaxurl,
         data : {action: "wp_posts_showcase_adddropdown", countr : counter, ptypes: ptypes, nonce: nonce},
         success: function(response) {
			// alert(response);	
            if(response != 0) {
               $("#selection_posts").find("tr").remove();
			   $('#selection_posts').append(response);
			   //by default first option has o be selected
			   $(".showcase_posts option:first").attr('selected','selected');
            }
            else {
               $("#selection_posts").find("tr").remove(); 
               alert("No data Available. Please select another post type!")
            }
         }
      });        
    });
    
     $('.dropdown_posttypes').on('change', function (){
		
		var counter = $('.layout_blocks').val(); 
		var ptypes = this.value;
		nonce = myAjax.ajax_nonce;
		$.ajax({
         type : "post",
         dataType : "html",
         url : myAjax.ajaxurl,
         data : {action: "wp_posts_showcase_adddropdown", countr : counter, ptypes: ptypes, nonce: nonce},
         success: function(response) {
            if(response != 0) {
               $("#selection_posts").find("tr").remove();
			   $('#selection_posts').append(response);
			   //by default first option has o be selected
			   $(".showcase_posts option:first").attr('selected','selected');
            }
            else {
               $("#selection_posts").find("tr").remove(); 
               alert("No data Available. Please select another post type!")
            }
         }
      });        
    });
    
})( jQuery );