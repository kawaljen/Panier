$( document ).ready(function() {

  $('.toogle-hide').click(function(){
		$(this).closest('form').addClass('editable');
	});


	//les quantités sur la premiere page du panier
	$("input[name='qte']").change(function(){
		$qte = $(this).val();
		$id= $(this).data('artid');
		
		//update price
		$parent = $(this).closest('tr');
		$parent.find('.prix1').html($qte * $(this).data('artprix'));
		$prix2Ligne = $(this).data('artprix2');
		if($prix2Ligne>0)	
			$parent.find('.prix2').html($qte * $prix2Ligne);

		//add input panier
		$inputs = $("form#panier").find('input[value='+$id+']');
		if($inputs.length< $qte){
			for (var i = 0; i < $qte-$inputs.length; ++i) {
				$("form#panier").append('<input type="hidden" name="article[]" value="'+$id+'" />');
			}
		}
		else if($inputs.length > $qte){
			for (var i = 0; i < $inputs.length -$qte; ++i) {
				$inputs[i].value = 0;
			}
		}
	});


});
