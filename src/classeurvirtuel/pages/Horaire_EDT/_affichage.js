//-----------------------------------------------------
//  Composition pour affichage des publications EDT
//                      ~o~
//  
// Ce fichier javascript est inclu dans les fichiers 
// fichiers html index, professeurs, classes, eleves,
// salles et conseilsdeclasse.
//-----------------------------------------------------

// Compose le bandeau titre de page
function composerBandeauTitre () {
	var strHtml = '';
	
	strHtml += '<table class="marges" width="100%" cellspacing="0" cellpadding="0">';
	strHtml += '  <tr>';
  strHtml += '    <td class="titre emploi">' + titrePage + '</td>';	  		
	strHtml += '    <td class="titre date">' + dateDerniereMaj + '</td>';	
	strHtml += '  </tr>';
	strHtml += '</table>';
  
  document.getElementById ('bandeauPage').innerHTML = strHtml;
}; 

//-----------------------------------------------------
// Compose en colonne les genres de ressources publiées
function composerBandeauGenre () {
	var strHtml = '';
	
	strHtml += '<table cellspacing="0" cellpadding="5">';
	strHtml += '  <tr>';
	strHtml += '    <td class="titre recherche">' + libChoixGenre + '</td>';
	for (var i=0; i < listeGenres.length; i++)
	  strHtml += '    <td class="titre genre espaceGauche" onclick="composerBandeauRessource (\'' + listeGenres[i].genre + '\')">' + listeGenres [i].libelle + '</td>'; 
	strHtml += '  </tr>';
	strHtml += '</table>';
	  
  document.getElementById ('bandeauGenre').innerHTML = strHtml;
};

//-----------------------------------------------------
// Compose en colonne les ressources publiées
function composerBandeauRessource (aGenre) {
  var j = null; // mémorise indice du premier élément du select
	var strHtml = '';
   
  strHtml += '<table cellspacing="0" cellpadding="5">';	
  strHtml += '  <tr >';	
	strHtml += '    <td class="choix">' + listeChoixRessources[aGenre] + '</td>';
	strHtml += '    <td>';
	strHtml += '      <select onchange="composerBandeauPeriode(this.options[selectedIndex].value)">'; 
	for (var i=0; i < listeRessources.length; i++) 	  
	  if (listeRessources[i].genre == aGenre){
      if (j == null)
        j = i;	  	
      strHtml += '<option value="' + listeRessources[i].codage + '">' + listeRessources[i].libelle + '</option>';  
    }  
  strHtml += '      </select>';
	strHtml += '    </td>';
	strHtml += '  </tr>';
  strHtml += '</table>';
 
  document.getElementById ('bandeauRessource').innerHTML = strHtml;
  
  if (j != null) 
    composerBandeauPeriode(listeRessources[j].codage); 
};

//-----------------------------------------------------
// Compose en colonne les ressources publiées
function composerBandeauPeriode (aCle) {
	var j = null; // mémorise indice du premier élément du select
	var strHtml = ''; 
  
  if (aCle != 'vide') { 
    strHtml += '<table cellspacing="0" cellpadding="5">';	
    strHtml += '  <tr>';
    strHtml += '    <td class="choix espaceGauche">' + libChoixPeriodes + '</td>';
    strHtml += '    <td>';
	  strHtml += '        <select onchange="composerGrille(this.options[selectedIndex].value)">';
	  for (var i=0; i < listePeriodes.length; i++) 	   
	    if (listePeriodes[i].cleRess == aCle) {
        if (j == null) 
          j = i;
        strHtml += '<option value="' + listePeriodes[i].codage + '">' + listePeriodes[i].libelle + '</option>'; 
      } // if   
    strHtml += '        </select>';
    strHtml += '    </td>';
    strHtml += '  </tr>';
    strHtml += '</table>';
	}
	  
	// Attention !
	// pas d'affichage d'un choix de période
	// pour les sessions de conseils de classe
	if (j != null)
	  for (var i=0; i < listeRessources.length; i++) 	  
	    if (listeRessources[i].codage == aCle)
	      if (listeRessources[i].genre == 'grSession')
    	    strHtml = '';	
	
	document.getElementById ('bandeauPeriode').innerHTML = strHtml;
  
  if (j != null)
    composerGrille(listePeriodes[j].codage);
	else if (aCle == 'vide')
		composerGrille(aCle);    
};

//-----------------------------------------------------
// Compose en colonne les grilles et renvois associés
function composerGrille (aCle) {
	var genrePub = '';
	var strHtml = '';  
  
  if (aCle != 'vide') { 
    for (var i=0; i < listeGrilles.length; i++) 	  
	    if (listeGrilles[i].cleGrille == aCle) {
        genrePub = listeGrilles[i].genre; 
               
        strHtml += '<table class="marges" cellspacing="0" cellpadding="0">';	
        strHtml += '  <tr>';
        strHtml += '    <td><img src="' + listeGrilles[i].nomFichier + '"></img></td>';
        strHtml += '  </tr>';
        if (listeGrilles[i].renvois.length > 0) 
          for (var j=0; j < listeGrilles[i].renvois.length; j++) {	          
            strHtml += '  <tr>';
            strHtml += '    <td>';     
            strHtml += '      <table style="font-family:' + listeGrilles[i].renvois[j].police.nom + '; font-size:' + listeGrilles[i].renvois[j].police.taille + 'pt" cellspacing="0" cellpadding="5">';
            strHtml += '        <tr>';
	          strHtml += '          <td class="titreRenvoi" colspan="2">' + listeGrilles[i].renvois[j].titre + '</td>'; 
	          strHtml += '        </tr>';
	          
	          if (listeGrilles[i].renvois[j].lignes.length > 0) 
	            for (var k=0; k < listeGrilles[i].renvois[j].lignes.length; k++) {
	          	  strHtml += '<tr>';       
	          	  strHtml += '  <td class="centrer">(' + listeGrilles[i].renvois[j].lignes[k].numero + ')</td>';      
	          	  strHtml += '  <td>' + listeGrilles[i].renvois[j].lignes[k].texte + '</td>';         
	          	  strHtml += '</tr>';
	            } // for k
            
            strHtml += '      </table>';               
            strHtml += '    </td>';       
  	        strHtml += '  </tr>';
  	      } // for j 
        
        strHtml += '</table>';        
        if (listeGrilles[i].genre != 'grSession') // à supprimer à terme
          break;                                  // à supprimer à terme
      } // if->for 
	}      
 
  document.getElementById ('grille').innerHTML = strHtml;
  
  composerSignature (aCle, genrePub);
};

//-----------------------------------------------------
// Compose en colonne les grilles et renvois associés
function composerSignature (aCle, aGenre) {
	var strHtml = ''; 

  if (aCle != 'vide') { 
    strHtml += '<P>&nbsp;</P>';	
    strHtml += '<p class="signature">' + listeSignature [aGenre]; 
    strHtml += ' - &copy; <a href="' + SigUrlSite + '" title="' + SigUrlTitre + '">';
    strHtml += 'INDEX-EDUCATION</a>&nbsp;' + SigIdxEditeur + '</p>';  
    strHtml += '<br>';         
	}      
 
  document.getElementById ('signature').innerHTML = strHtml;
};


//-----------------------------------------------------
// main !
function AfficherPage () {
	composerBandeauTitre ();
	composerBandeauGenre ();
};
