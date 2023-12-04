
$( document ).ready(function() {
    var caf = false;
    var lignesJson = [];
    var lignesIds = $( document ).find('.ligneInsc');
    for (var i = 0; i < lignesIds.length; ++i) {
        lignesJson.push({'id': lignesIds.eq(i).data('id'), 'prix': parseInt (lignesIds.eq(i).data('prixligne')), transp : parseInt (lignesIds.eq(i).data('transport'))});
    }

    $('select.select-transp').on('change', function() {
        updatePriceTransp($(this));
    });

    $('select.select-dates').on('change', function() {
        updatePriceDates($(this));
    });

    $('.updateSoustotal').on('click', function() {
        if($('#mul').val().length>10){
            $('.block-multiple').addClass('hasMultiple ami');
        }
        else {
            $('.block-multiple').addClass('error');
            $('.block-multiple').removeClass('hasMultiple ami');
        }
        updatePriceTotal();
    });
    $('.verifcaf').on('click', function() {
        checkQuotientCaf($(this));
    });
    $('.infoEnfant').on('click', function() {
        var bulle = $(this).next('.infoBulle');
        if(bulle.hasClass('open')){
            bulle.removeClass('open');
        }
        else {
            bulle.addClass('open');
        }
    });

    function updatePriceTransp(that){
		var parent = that.closest('.ligneInsc');
        var ligneID =parent.data('id');
        var totalTransp = 0;
        transpPrix = that.find(':selected').data('price');
        parent.find('.prix-transp').find('.price').html( transpPrix);

        lignePrix = 0;
        //console.log(parseInt (lignePrix));
        // parent.find('.ligne-price').html(transpPrix + parseInt (lignePrix));


        for (var i = 0; i < lignesJson.length; ++i) {
            if (lignesJson[i]['id'] === ligneID) {
                lignesJson[i]['transp'] = transpPrix;
                lignePrix = lignesJson[i]['prix'];
            }
            totalTransp +=lignesJson[i]['transp'];
        }
        parent.find('.ligne-price').html(transpPrix + parseInt (lignePrix));
        $( document ).find('#totaltransp').html(totalTransp);
		updatePriceTotal();
}


function updatePriceTotal(){
    var total = 0; totalTransp =0 ;  totalPrix =0;
     for (var i = 0; i < lignesJson.length; ++i) {
         total += parseInt (lignesJson[i]['transp']) + lignesJson[i]['prix'];
         totalTransp += parseInt (lignesJson[i]['transp']);
         totalPrix += lignesJson[i]['prix'];        
     }
     if($('.block-multiple').hasClass('hasMultiple')){
            var remise= (totalPrix*5)/100;
            $( document ).find('#remise').html(remise);
            $( document ).find('#prixavantReduc').html(totalPrix);
            totalPrix-= remise;
            total = totalPrix + totalTransp;
            $( document ).find('#soustotal').html(totalPrix);
            $( document ).find('#totaltransp').html(totalTransp);
    
     }

    $( document ).find('#totaltotal').html(total);
}


function updatePriceDates(that){
    var parent = that.closest('.ligneInsc');
    if(parent.hasClass('sejour')){
        var ligneID =parent.data('id');
        var lignePrix = 'error';
        if(that.find(':selected').hasClass("duree2")) {
            parent.find('.prix-duree2').addClass("open");
            parent.find('.prix-duree1').removeClass("open");
            lignePrix =parent.data('priced2');
        }
        else {
            parent.find('.prix-duree1').addClass("open");
            parent.find('.prix-duree2').removeClass("open");
            lignePrix =parent.data('price');
        }
        for (var i = 0; i < lignesJson.length; ++i) {
                if (lignesJson[i]['id'] === ligneID) {
                    lignesJson[i]['prix'] = parseInt (lignePrix);
                    parent.find('.ligne-price').html(lignesJson[i]['transp'] + parseInt (lignePrix));
                }
        }
        updatePriceTotal();
    }
  }


    //console.log($('form').find(':input:not([type="submit"], button):enabled:visible'));
	function checkQuotientCaf(el){
        var val =$('#quotientCaf').val();
        if( val.length > 0 ){
            var block = el.closest('#block-caf').find('.block-to-open');
            block.removeClass('open');
            var limiteCaf = $('#block-caf').data('limitecaf');
            if( val < limiteCaf ){
                block.eq(0).addClass('open');
                $('#block-Caf-sanspdf').show();
                $('.no-justificatifcaf').show();
            }
            else {
                block.eq(1).addClass('open');
                $('#block-Caf-sanspdf').hide();
            }
        }
	}
});