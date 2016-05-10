/* Activation de l'accordéon */
$(function() {
    $( "#accordion" ).accordion({
        header: '.panel-default',
        active : 0
    });
});

function readDate(idInputDate) {
    //console.log(idInputDate);
    //oc_commandebundle_CommandeGlobale_dateReservation_day
        var select_jour = $(idInputDate+'_day')[0];
        var choice_jour = select_jour.selectedIndex;
        var jour = select_jour.options[choice_jour].value;
    //oc_commandebundle_CommandeGlobale_dateReservation_month
        var select_mois = $(idInputDate+'_month')[0];
        var choice_mois = select_mois.selectedIndex;
        var mois = select_mois.options[choice_mois].text;
    //oc_commandebundle_CommandeGlobale_dateReservation_year
        var select_annees = $(idInputDate+'_year')[0];
        var choice_annee = select_annees.selectedIndex;
        var annee = select_annees.options[choice_annee].value;
    // Reconstruction Date Visite
    var select_demi = $('#oc_commandebundle_CommandeGlobale_demiJournee')[0];
    var date_str=''+mois+' '+jour+', '+annee+' 00:00:00';
    var date = new Date(date_str);
    return date;
}


/* Définition de l'objet de validation des champs via JS/JQUERY */
function Validate_Step2() {
    this.headers = new Array();
    this.articles= new Array();
    this.tarifs= new Array();
    this.loops=new Array();
    this.visiteurs= new Array();
};
/*
*   Stocke dans articles la collections des éléments de l'acordéon 
*/
Validate_Step2.prototype.getArticles = function() {
    this.articles = $('#accordion > article').get();
};

/*
*   Stocke dans headers la collections des titre de l'acordéon 
*/
Validate_Step2.prototype.getHeaders = function() {
    this.headers = $('#accordion div.panel-heading').get();
};

/*
*   Stocke dans tarifs la collections des tarifs présents dans l'accordeon
*   tarifs[0] contient le tarif du 1er élément de l'accordéon
*/
Validate_Step2.prototype.getTarifs = function() {
    for (var i=0; i<this.articles.length;i++) {
        this.tarifs[i]=$(this.articles[i]).attr('tarif');
        this.loops[i]=Number($(this.articles[i]).attr('loops'));
        //console.log($(this.articles[i]).attr('loops'));
        //console.log(this.loops[i]);
    }
};

/*
*   Stocke dans visiteurs la collections des visiteurs présents dans l'accordeon
*   visiteurs[0] contient les visiteurs associés au 1er élément/tarif de l'accordéon
*/
Validate_Step2.prototype.getVisiteurs = function() {
    for (var i=0; i<this.articles.length;i++) {
        this.visiteurs[i]=this.articles[i].getElementsByClassName('visitor');
    }
};

/*
*   Valide si un champ texte a plus de x caractères
*/
Validate_Step2.prototype.validateStr = function(champ, x) {
    if (champ.length<x) {
        return [false, 'Ce champ doit contenir plus de '+x+' caractère(s)'];
    } else {
        return [true,''];
    }
};

/*
*   Valide si l'age du visiteur respecte les conditions (min & max) définis
*/
Validate_Step2.prototype.validateAge = function(birthday, age_min, age_max) {
    var today = new Date();
    var age = Math.floor((today.getTime() - birthday.getTime())/(365.24*24*3600*1000));
    if (age<age_max && !(age<age_min)) {
        return [true,'',age];
    } else {
        if (age<age_min) {
            return [false,'l\'age de ce visiteur ('+age+' ans) doit être plus grand que '+age_min+' an(s)',age];
        } else {
            return [false,'l\'age de ce visiteur ('+age+' ans) doit être plus petit que '+age_max+' an(s)', age];
        }
    }
};

/* Mise à jour des champs Input après validation */
Validate_Step2.prototype.updateInput = function(td, valStr) {
    if (!valStr[0]) {
        $(td).attr('class',"form-group has-error has-feedback");
        $(td.getElementsByTagName('span')[0]).attr('class','glyphicon glyphicon-remove form-control-feedback');
    } else {
        $(td).attr('class',"has-success has-feedback");
        $(td.getElementsByTagName('span')[0]).attr('class','glyphicon glyphicon-ok form-control-feedback');
    }
};

/*
*   Valide les visiteurs pour chaque tarif
*/
Validate_Step2.prototype.validateVisiteurs = function() {
    var has_error=false;
    for (var i=0; i<this.visiteurs.length; i++) {
        console.log('#visiteurs '+i+'/'+this.visiteurs.length);
        var visiteurs_tarif=this.visiteurs[i];
        var tarif = this.tarifs[i];
        console.log("Tarif => "+tarif);
        var age_min=0;
        var age_max=200;
        switch (tarif) {
            case "Normal": // Pour les plus de 12 ans et moins de 60 ans
                    // Pas de contrôle en fait
                break;
            case "Senior": // Pour les 60 ans et plus
                age_min=60;
                break;
            case "Enfant": // Pour les plus de 4 ans et moins de 12 ans
                age_min=4;
                age_max=12;
                break;
            case "Gratuit": // Gratuit pour les moins de 4 ans
                age_max=4;
                break;
            case "Réduit": // Sur présentation des justificatifs
                    // Sans objet
                break;
            case "Famille": // Pour 2 adultes et 2 enfants d'une même famille
                    // Analyse spécialisée : 2 plus de 18 ans + 2 Enfants
                    age_min=18;
                break;
        }
        console.log('['+age_min+"; "+age_max+']');
        var nom_err=false;
        var prenom_err=false;
        var birthday_err=false;
        
        var birthday_famille= new Array();
        
        for (var j=0; j<visiteurs_tarif.length; j++) {
            //récupération des noms et prénoms
            var tds = visiteurs_tarif[j].getElementsByTagName('td');
            console.log("   "+visiteurs_tarif.length+" visiteurs");
            var nom = tds[0].getElementsByTagName('input')[0].value;
            var valNom= this.validateStr(nom, 3);
            this.updateInput(tds[0],valNom);
            if (!valNom[0]) {nom_err=true;}
            
            var prenom = tds[1].getElementsByTagName('input')[0].value;
            var valPrenom= this.validateStr(prenom, 3);
            this.updateInput(tds[1],valPrenom);
            if (!valPrenom[0]) {prenom_err=true;}
            console.log('   visiteurs_tarif '+j);
            console.log('   nom :'+nom);
            console.log('   prénom :'+prenom);
            // checkNomPrenom(visiteurs_tarif[j]); // Dans tous les cas on test que les noms et prénom contiennent des caractères.
            if (tarif!='Normal' && tarif!='Réduit' && tarif!='Famille') {
                idDate = "#oc_commandebundle_CommandeGlobale_commandes_"+this.loops[i]+"_visiteurs_"+j+"_birthday";
                console.log('readDate 171');
                console.log('   idDate = '+idDate);
                var birthday_date = readDate(idDate);
                var validate_Age = this.validateAge(birthday_date, age_min, age_max)
                if (!validate_Age[0]) {
                    birthday_err=true;
                    $(tds[2]).css('border','1px red solid');
                    $(tds[3]).css('border','1px red solid');
                    $(tds[4]).css('border','1px red solid');
                } else {
                    $(tds[2]).css('border','1px green solid');
                    $(tds[3]).css('border','1px green solid');
                    $(tds[4]).css('border','1px green solid');
                }
            }
            if (tarif=="Famille") {
                // Traitement particulier du tarif famille.
                // Il faut que la moitié des visiteurs ait plus de 18 ans et l'autre ait moins de 18 ans
                // On ne controlera pas l'adéquation des noms car il y a tellement de famille recomposée que cela n'a pas grand sens...
                // Ici on mémorise l'ensemble des dates de naissance et on les pousse dans une catégorie 'adulte' ou 'enfant'
                idDate = "#oc_commandebundle_CommandeGlobale_commandes_"+this.loops[i]+"_visiteurs_"+j+"_birthday";
                console.log('readDate 191');
                var birthday_date = readDate(idDate);
                var validate_Age = this.validateAge(birthday_date, age_min, age_max)
                //console.log(validate_Age);
                if (validate_Age[0]) {
                    // c'est donc un adulte
                    birthday_famille[j]=[birthday_date,'adulte',validate_Age[2],tds[2],tds[3],tds[4]];
                } else {
                    // c'est donc un enfant
                    birthday_famille[j]=[birthday_date,'enfant',validate_Age[2],tds[2],tds[3],tds[4]];
                }
                
            }
        }
        
        if (tarif=="Famille") {
            console.log('TEST Famille');
            console.log(birthday_famille);
            // Ici on compte le nombre d'adulte et le nombre d'enfant, si les 2 nombres sont égaux pas d'erreur sinon on remonte une erreur sur les dates de naissance.
            var nbEnfants=0;
            var nbAdultes=0;
            for (var k=0; k<birthday_famille.length; k++) {
                if (birthday_famille[k][1]=='adulte') { // vrai si adulte
                    nbAdultes++;
                } else { nbEnfants++;}
            }
            console.log('   Nombre d\'Adultes = '+nbAdultes);
            console.log('   Nombre d\'Enfants = '+nbEnfants);
            if (nbAdultes==nbEnfants) {
                birthday_err=false;
                for (var k=0; k<birthday_famille.length; k++) {
                    $(birthday_famille[k][3]).css('border','1px green solid');
                    $(birthday_famille[k][4]).css('border','1px green solid');
                    $(birthday_famille[k][5]).css('border','1px green solid');
                }
            } else {
                birthday_err=true;
                // On surligne en rouge les éléments date correspondant.
                for (var k=0; k<birthday_famille.length; k++) {
                    $(birthday_famille[k][3]).css('border','1px red solid');
                    $(birthday_famille[k][4]).css('border','1px red solid');
                    $(birthday_famille[k][5]).css('border','1px red solid');
                }
            }
            //console.log(birthday_famille);
        }
        
        if (prenom_err || nom_err || birthday_err) {// Affichage des erreurs au niveau des titres
            if (prenom_err) { 
                has_error=true;
                $(this.articles[i].getElementsByTagName("th")[1].getElementsByTagName('span')[0]).show();
            } else { 
                $(this.articles[i].getElementsByTagName("th")[1].getElementsByTagName('span')[0]).hide();
            }
            if (nom_err) {
                has_error=true;
                $(this.articles[i].getElementsByTagName("th")[0].getElementsByTagName('span')[0]).show();
            } else {
                $(this.articles[i].getElementsByTagName("th")[0].getElementsByTagName('span')[0]).hide();
            }
            if (birthday_err) {
                has_error=true;
                $(this.articles[i].getElementsByTagName("th")[2].getElementsByTagName('myErr')[0]).show();
            } else {
                if (tarif!='Normal' && tarif!='Réduit') {
                    $(this.articles[i].getElementsByTagName("th")[2].getElementsByTagName('myErr')[0]).hide();
                }
            }
            $(this.headers[i].getElementsByClassName("myhead")[0]).show();
        } else {
            $(this.articles[i].getElementsByTagName("th")[0].getElementsByTagName('span')[0]).hide();
            $(this.articles[i].getElementsByTagName("th")[1].getElementsByTagName('span')[0]).hide();
            if (tarif!='Normal' && tarif!='Réduit') {
                $(this.articles[i].getElementsByTagName("th")[2].getElementsByTagName('myErr')[0]).hide();
            }
            $(this.headers[i].getElementsByClassName("myhead")[0]).hide();
        }
    }
    return !has_error;
}
function validateEmail(email) {
    var re = new RegExp('^[0-9a-z._-]+@{1}[0-9a-z.-]{2,}[.]{1}[a-z]{2,5}$','i');
    return re.test(email);
}

/* Définition des fonctionnalités et tests réalisés lors de la soumission du formulaire */
$(".next").click(function() {
    //Avant soumission faire les tests de validité de renseignement des champs
    var f0 = new Validate_Step2();
    f0.getArticles();
    f0.getHeaders();
    f0.getTarifs();
    f0.getVisiteurs();
    
    //Validation finale des données concernant l'acheteur
    //oc_commandebundle_CommandeGlobale_client_nom
    var valid_NomClient=f0.validateStr($('#oc_commandebundle_CommandeGlobale_client_nom').val(), 3);
    if (valid_NomClient[0]) {
        $('#nom_client>div').attr('class','form-group has-success has-feedback');
        $('#nom_client span.form-control-feedback').attr('class',"glyphicon glyphicon-ok form-control-feedback");
    } else {
        $('#nom_client>div').attr('class','form-group has-error has-feedback');
        $('#nom_client span.form-control-feedback').attr('class',"glyphicon glyphicon-remove form-control-feedback");
    }
    //oc_commandebundle_CommandeGlobale_client_prenom
    var valid_PrenomClient=f0.validateStr($('#oc_commandebundle_CommandeGlobale_client_prenom').val(), 3);
    if (valid_PrenomClient[0]) {
        $('#prenom_client>div').attr('class','form-group has-success has-feedback');
        $('#prenom_client span.form-control-feedback').attr('class',"glyphicon glyphicon-ok form-control-feedback");
    } else {
        $('#prenom_client>div').attr('class','form-group has-error has-feedback');
        $('#prenom_client span.form-control-feedback').attr('class',"glyphicon glyphicon-remove form-control-feedback");
    }
    //oc_commandebundle_CommandeGlobale_client_email
    var email = $('#oc_commandebundle_CommandeGlobale_client_email').val();
    var valid_Email = validateEmail(email);
    if (valid_Email) {
        $('#email_client>div').attr('class','form-group has-success has-feedback');
        $('#email_client span.form-control-feedback').attr('class',"glyphicon glyphicon-ok form-control-feedback");
    } else {
        $('#email_clientt>div').attr('class','form-group has-error has-feedback');
        $('#email_client span.form-control-feedback').attr('class',"glyphicon glyphicon-remove form-control-feedback");
    }
    
    var valid_data =f0.validateVisiteurs();
    console.log('Data : '+valid_data);
    var test_client = valid_NomClient[0] && valid_PrenomClient[0] && valid_Email;
    console.log('Client : '+test_client);
    if ( test_client && valid_data) {
        alert('Vous avez tout bien renseigné !\nBravo !');
        document.forms['oc_commandebundle_CommandeGlobale'].submit(); 
    } else {
        
    }
});