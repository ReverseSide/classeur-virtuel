<?php


class dbLogin
{
    private $objConnexion=null;
    //Déclaration des constantes   
    const STR_HOST='nlhj.myd.infomaniak.com';    
	const STR_DBNAME='nlhj_cepm';
	const STR_USER='nlhj_databuser';
	const STR_PASS='classeur123';
    public function __construct()
    {     $this->dbConnect();
    }

    private function dbConnect()
    {        
            $this->objConnexion = new PDO(
'mysql:host='.dbLogin::STR_HOST.';dbname='.dbLogin::STR_DBNAME.'', ''.dbLogin::STR_USER.'', ''.dbLogin::STR_PASS.'',array(PDO::MYSQL_ATTR_INIT_COMMAND=>"SET NAMES utf8"));
    }
    // *******************************************************************
    // Nom :	Request
    // But :	Permet d'exécuter une requête SQL
    // Retour:	Tableau
    // Param.:  (Requête SQL)
    // *******************************************************************
    private function Request($Request)
    {
        $Reponse = $this->objConnexion->query($Request);
        $tabAss=$Reponse->fetchAll(PDO::FETCH_ASSOC);
        $Reponse->closeCursor();

        return $tabAss;
    }//Request

    // *******************************************************************
    // Nom :	GetMyClass
    // But :	Recherche les élèves d'une classe
    // Retour:	Tableau
    // Param.: 	(id de la classe)
    // *******************************************************************
	/*public function GetMyClass($class)
    {
	}*/
    public function listProf( $nome )
    {
        $pdo = &$this->objConnexion;
        $strSelect="SELECT * FROM users WHERE  fonction NOT LIKE '%EL%'";
        $tabReponse=$this->Request($strSelect);
        
		return $tabReponse ;
		
                $_SESSION['user_id']=$entry['uid'];
                $_SESSION['login'] = $entry['log_name'];
                $_SESSION['user_name']=$nom;
        

        }
}
?>
