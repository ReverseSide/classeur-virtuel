var libChoixGenre = "Recherche par :";
var listeGenres = new Array ()
listeGenres [0] = new GenreRessource ("grProf","PROFESSEURS");
listeGenres [1] = new GenreRessource ("grClasse","CLASSES");

function GenreRessource (aGenre, aLibelle) {
  this.genre   = aGenre;
  this.libelle = aLibelle;
}
