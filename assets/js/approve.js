$(document).ready(function() {
	$('.approve').click(function(e) { 
		e.preventDefault()
	    var id = $(this).attr('id');
		$('#updated_comment_id').val(id); 
		$('#submit_value').val('approve');
		$("#user_comments").submit();
	});
	$('.reject').click(function(e) { 
		e.preventDefault()
	    var id = $(this).attr('id');
		$('#updated_comment_id').val(id); 
		$('#submit_value').val('reject');
		$("#user_comments").submit();
	});
 });