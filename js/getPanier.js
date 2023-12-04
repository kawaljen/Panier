var mainsite = (function () {
    var my = {},
        s = {  
			panier: $('#panier'),
        };

    my.init = function () {

       // bindUIactions();
        AddToPanier();

    };


	function AddToPanier(that, e){
		var data = [];
		if (sessionStorage.getItem('panier')){
			data.push(sessionStorage.getItem('panier'));
		}
        $.ajax({
            url: "/panier-dev/panierApi.php",
            beforeSend: function( xhr ) {
            xhr.overrideMimeType( "text/plain; charset=x-user-defined" );
            }
        })
            .done(function( data ) {
                sessionStorage.setItem('stat', data);
                printAnalyticsStat(data);
                console.log('newstat');
                        
            });	
	}
	
    
    return my;
}());

$(document).ready(function(){
	mainsite.init();
});

