var updateTotal = function() {
    //On récupère toutes les lignes visibles du tableau
    var lignes_visibles=$("tbody tr:visible");
    //On parcourt chacune des lignes et on calcule le montant total de la transaction
    var montant_total=0;
    var nb_place=0;
    //console.log('lignes visibles');
    //console.log(lignes_visibles);
    for(var i=0; i<lignes_visibles.length; i++) {
        var amount = Number($(lignes_visibles[i]).attr('amount'));
        var place = Number($(lignes_visibles[i]).attr('place'));
        montant_total+=amount;
        nb_place+=place;
    }
    $('#total').html(montant_total + "  <span class=\"glyphicon glyphicon-euro\"></span>");
    $('#oc_commandebundle_CommandeGlobale_nbBillets').attr('value',nb_place);
}

$("plus, moins").click(function() { // Clic sur un bouton d'ajout pour un tarif
    /* Récupération des informations liées au bouton cliqué 
        (classe tarif)
        -key
        -prix
        -nBillet
    */
    var mon_type=$(this).attr('name');
    var key=$(this).attr('key');
    var prix=Number($(this).attr("prix"));
    var nBillet=Number($(this).attr("nBillets"));
    /* Récupération des informations liées à la commande en cours
        -nombre de billet du tarif en cours déjà souhaité
    */
    var nbBillet=Number($("people[key=\""+key+"\"]").html());
    
    /* Identification du type d'opération demandé 'Ajout' ou 'Retrait' */
    if (mon_type=="plus") {
        nbBillet+=nBillet;
        //console.log("ajout de "+nBillet);
        
    } else if (mon_type=="moins" && nbBillet>=nBillet) {
        nbBillet-=nBillet;
        //console.log("suppression de "+nBillet);
    }
    
    var amount=Number($("#"+key).attr("amount"));
    
    // Nouveau calcul du montant total
    amount=nbBillet/nBillet*prix;
    
    // On met à jour l'affichage
    $("people[key=\""+key+"\"]").html(nbBillet);
    $("input."+key).val(nbBillet/nBillet);
    console.log($("input."+key));
    $("#"+key+"_pl").html(nbBillet);
    $("#"+key+"_st").html(amount+"  <span class=\"glyphicon glyphicon-euro\">");
    $("#"+key).attr('amount', amount+"");
    $("#"+key).attr('place', nbBillet+"");
    if (nbBillet==0) {
        $("#"+key).hide();
    } else {
        $("#"+key).show();
    }
    
    updateTotal();
})
updateTotal();

/* Fonction récupérant sous forme de Date la date sélectionnée par l'utilisateur
 ATTENTION Cette fonction ne marche que si dans le fichier config.sys on a:
 parameters:
     locale: en
*/
function getSelectedDate() {
    //oc_commandebundle_CommandeGlobale_dateReservation_day
        var select_jour = $('#oc_commandebundle_CommandeGlobale_dateReservation_day')[0];
        var choice_jour = select_jour.selectedIndex;
        var jour_visite = select_jour.options[choice_jour].value;
    //oc_commandebundle_CommandeGlobale_dateReservation_month
        var select_mois = $('#oc_commandebundle_CommandeGlobale_dateReservation_month')[0];
        var choice_mois = select_mois.selectedIndex;
        var mois_visite = select_mois.options[choice_mois].text;
    //oc_commandebundle_CommandeGlobale_dateReservation_year
        var select_annees = $('#oc_commandebundle_CommandeGlobale_dateReservation_year')[0];
        var choice_annee = select_annees.selectedIndex;
        var annee_visite = select_annees.options[choice_annee].value;
    // Reconstruction Date Visite
    var select_demi = $('#oc_commandebundle_CommandeGlobale_demiJournee')[0];
    var date_str='';
    if (select_demi.checked == true ){
        date_str=''+mois_visite+' '+jour_visite+', '+annee_visite+' 14:00:00';
    } else {
        date_str=''+mois_visite+' '+jour_visite+', '+annee_visite+' 00:00:00';
    }
    var theDate=new Date(date_str);
    return theDate;
}

/* Définition des fonctionnalités et tests réalisés lors de la soumission du formulaire */
$(".next").click(function() {
    var err= new Array();
     // Au moins une place/tarif doit être sélectionnée
    var nbPlace = Number($('#oc_commandebundle_CommandeGlobale_nbBillets').attr('value'));
    if (nbPlace ==0) {err.push('Il faut sélectionner au moins un tarif. Merci d\'en sélectionner un.\n');}
    // La date doit être supérieure ou égale à la date du jour si <14h sinon doit être strictement supérieur.
    var date_jour = new Date();
    date_visite = getSelectedDate();
    if (date_visite.getDay()==0 || date_visite.getDay() == 6) {err.push('Le Louvre est fermé les samedi et dimanche. Merci de changer la date de la visite.\n');}
    if (date_jour > date_visite) { err.push('Impossible de réserver une date dans le passé. Merci de sélectionner un autre jour.\n');}
    if (err.length==0) {
        $("select").prop('disabled',false);
        document.forms['oc_commandebundle_CommandeGlobale'].submit(); 
    } else {
        var str_err='';
        for (var i=0; i<err.length; i++) {
            str_err+=err[i];
        }
        alert(str_err);
    }
});

