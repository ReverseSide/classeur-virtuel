// autocomplet : this function will be executed every time we change the text
function autocomplet() {
	var min_length = 0; // min caracters to display the autocomplete
	var keyword = $('#Entreprise_id').val();

	if (keyword.length >= min_length) {
		$.ajax({
			url: '../pages/ajax_refresh_entreprise.php',
			type: 'POST',
			data: {keyword:keyword},
			success:function(data){
				$('#Entreprise_list_id').show();
				$('#Entreprise_list_id').html(data);



			}
		});
	} else {
		$('#Entreprise_list_id').hide();
	}

}

// set_item : this function will be executed when we select an item
function set_item(item) {
	// change input value
	$('#Entreprise_id').val(item);
	// hide proposition list
	$('#Entreprise_list_id').hide();


}