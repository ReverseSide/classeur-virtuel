// autocomplet : this function will be executed every time we change the text
function autocomplet() {
	var min_length = 0; // min caracters to display the autocomplete
	var keyword = $('#Eleve_id').val();

	if (keyword.length >= min_length) {
		$.ajax({
			url: '../pages/ajax_refresh_eleve_edt.php',
			type: 'POST',
			data: {keyword:keyword},
			success:function(data){
				$('#Eleve_list_id').show();
				$('#Eleve_list_id').html(data);



			}
		});
	} else {
		$('#Eleve_list_id').hide();
	}

}

// set_item : this function will be executed when we select an item
function set_item(item) {
	// change input value
	$('#Eleve_id').val(item);
	// hide proposition list
	$('#Eleve_list_id').hide();


}