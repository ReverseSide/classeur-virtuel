<?php
	
	//if(strpos($_SERVER["HTTP_USER_AGENT"], 'MSIE') || strpos($_SERVER["HTTP_USER_AGENT"], 'Trident'))header('Location: /ie');
	try{
		$pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
		$bdd = new PDO('mysql:host=nlhj.myd.infomaniak.com;dbname=nlhj_cepm;charset=utf8','nlhj_databuser','classeur123',$pdo_options);
	}
	catch (Exception $e){
		die('Erreur : ' . $e->getMessage());
	}
	



/*const STR_HOST='nlhj.myd.infomaniak.com';
    
	const STR_DBNAME='nlhj_cepm';
	const STR_USER='nlhj_databuser';

	const STR_PASS='classeur123';

    public function __construct()
    {
        $bdd->dbConnect();
    }

    private function dbConnect()
    {
         
            $bdd->objConnexion = new PDO('mysql:host='.dbIfc::STR_HOST.';dbname='.dbIfc::STR_DBNAME.'', ''.dbIfc::STR_USER.'', ''.dbIfc::STR_PASS.'',array(PDO::MYSQL_ATTR_INIT_COMMAND=>"SET NAMES utf8"));

    }*/

?>