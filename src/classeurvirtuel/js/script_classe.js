// autocomplet : this function will be executed every time we change the text
function autocomplet() {
	var min_length = 0; // min caracters to display the autocomplete
	var keyword = $('#classe_id').val();
	if (keyword.length >= min_length) {
		$.ajax({
			url: '../pages/ajax_refresh_classe.php',
			type: 'POST',
			data: {keyword:keyword},
			success:function(data){
				$('#classe_list_id').show();
				$('#classe_list_id').html(data);



			}
		});
	} else {
		$('#classe_list_id').hide();
	}

}

// set_item : this function will be executed when we select an item
function set_item(item) {
	// change input value
	$('#classe_id').val(item);
	// hide proposition list
	$('#classe_list_id').hide();


}