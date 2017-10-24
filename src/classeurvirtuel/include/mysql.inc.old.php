﻿<?php
//*********************************************************
// Societe: CEPM
// Auteur : Gregory Krieger
// Date : 23.05.2016
// But : Classe - Interactions avec la base de données
//*********************************************************
// Modifications:
// Date :
// Auteur :
// Raison :
//*********************************************************
// Date :
// Auteur :
// Raison :
//*********************************************************

class dbIfc
{
    private $objConnexion=null;
    //Déclaration des constantes

    const STR_HOST='nlhj.myd.infomaniak.com';

	const STR_DBNAME='nlhj_cepm';
	const STR_USER='nlhj_databuser';

	const STR_PASS='classeur123';

    public function __construct()
    {
        $this->dbConnect();
    }

    private function dbConnect()
    {
        try {
            $this->objConnexion = new PDO('mysql:host='.dbIfc::STR_HOST.';dbname='.dbIfc::STR_DBNAME.'', ''.dbIfc::STR_USER.'', ''.dbIfc::STR_PASS.'',array(PDO::MYSQL_ATTR_INIT_COMMAND=>"SET NAMES utf8"));
        } catch (Exception $e) {
            die('Erreur' . $e->getMessage());
        }
    }


    // *******************************************************************
    // Nom :	GetMyClass
    // But :	Recherche les élèves d'une classe
    // Retour:	Tableau
    // Param.: 	(id de la classe)
    // *******************************************************************
	public function GetMyClass($class)
    {
        $strSelect="SELECT id_eleve, id_codebarre, ele_nom, ele_prenom, cla_nom, ele_dispenseecg, ele_datedenaissance, ele_dispensebt, ele_dispensesport, ele_derogation, ele_desavantage, ele_statut FROM t_eleve, t_classe WHERE   t_eleve.id_codebarre not in (select idx_codebarre from t_rupture where rup_suit ='Non') and  idx_classe='$class' AND id_classe='$class' ORDER BY ele_nom ASC";
        $tabReponse=$this->Request($strSelect);


        // Post traitement: stocke les dispenses sous forme de booléens
        foreach ($tabReponse as &$student)
        {
            if (strpos(strtoupper($student['ele_dispenseecg']), "DISP") === false)
                $student['ele_dispenseecg'] = false;
            else
                $student['ele_dispenseecg'] = true;
            if (strpos(strtoupper($student['ele_dispensebt']), "DISP") === false)
                $student['ele_dispensebt'] = false;
            else
                $student['ele_dispensebt'] = true;
            if (strpos(strtoupper($student['ele_dispensesport']), "DISP") === false)
                $student['ele_dispensesport'] = false;
            else
                $student['ele_dispensesport'] = true;
        }

        return $tabReponse;
    }//GetMyClass
// *******************************************************************
    // Nom :	AddInternShip
    // But :	Ajout un stage à un élève
    // Retour:	Rien
    // *******************************************************************
    public function AddInternShip($stDateDeb, $stDateFin, $stEntNom, $stEntRue, $stEntNPA, $stEntLocalite, $stEntCant, $stEntContNom, $stEntContPrenom, $stEntContTel, $stEntContMob, $stEntContEmail, $stuBarCode)
    {
        $msg = "";
        try
        {
            $req = $this->
                objConnexion->prepare("INSERT INTO t_stage (sta_dateDeb, sta_dateFin, sta_entNom, sta_entRue, sta_entNpa, sta_entLocalite, sta_entCanton, sta_entConNom, sta_entConPrenom, sta_entConTel, sta_entConMob, sta_entConEmail, idx_eleve)
              VALUES (:dateDeb, :dateFin, :entNom, :entRue, :entNpa, :entLocalite, :entCanton, :entConNom, :entConPrenom, :entConTel, :entConMob, :entConEmail, :eleve)");
            $req->execute(array(
                'dateDeb' => $stDateDeb,
                'dateFin' => $stDateFin,
                'entNom' => $stEntNom,
                'entRue' => $stEntRue,
                'entNpa' => $stEntNPA,
                'entLocalite' => $stEntLocalite,
                'entCanton' => $stEntCant,
                'entConNom' => $stEntContNom,
                'entConPrenom' => $stEntContPrenom,
                'entConTel' => $stEntContTel,
                'entConMob' => $stEntContMob,
                'entConEmail' => $stEntContEmail,
                'eleve' => $stuBarCode
            ));
            $msg = "Le stage a été correctement enregistré";
        }
        catch(PDOException $e)
        {
            $msg = "une erreur s'est produite lors de l'enregistrement du stage:<br>" . $e->getMessage();
        }
        return $msg;
    }//AddStage

    // *******************************************************************
    // Nom :	GetStudent
    // But :	Recherche des détails d'un élève
    // Retour:	Tableau
    // Param.: 	(codebarre de l'élève)
    // *******************************************************************
    // *******************************************************************
    // Nom :	GetStudent
    // But :	Recherche des détails d'un élève
    // Retour:	Tableau
    // Param.: 	(codebarre de l'élève)
    // *******************************************************************
    public function GetStudent($id)
    {

        $strSelect="SELECT * FROM t_eleve
LEFT JOIN t_maitredapprentissage ON t_eleve.idx_maitredapprentissage = id_maitredapprentissage
LEFT JOIN t_representantlegal ON t_eleve.idx_representantlegal = id_representantlegal
LEFT JOIN t_entreprise ON t_eleve.idx_entreprise = id_entreprise
LEFT JOIN t_classe ON t_eleve.idx_classe = id_classe
WHERE t_eleve.id_codebarre LIKE '$id'
LIMIT 1";
        $tabReponse=$this->Request($strSelect);
        return $tabReponse;
    }//GetMyClass

	private function SetAlternances($date, &$alternance, &$semestrePaire, &$semestre)
	{
		// Get Numéro de semaine
		$textDate  = date("j F Y", $date);
		$weeknumber=$this->datediff('ww', '24 August 2015', $textDate, false);

		// Semaine paire ou impaire
		$alternance = "";

		if ($weeknumber % 2 == 1)
		{
			$alternance = "S.I";
		}
		elseif ($weeknumber % 2 == 0)
		{
			$alternance = "S.P";
		}
		//semestre 1 ou 2
		$dateSemestre = date("Y-m-d");
		$semestrePaire = "";
		$semestre = "";
		if($dateSemestre > "2016-01-22")
		{
			$semestre = "S2";
			if($alternance == "S.I")
			{
				$semestrePaire = "S2i";
			}
			else
			{
				$semestrePaire = "S2p";
			}
		}
		else
		{
			$semestre = "S1";
			if($alternance == "S.I")
			{
				$semestrePaire = "S1i";
			}
			else
			{
				$semestrePaire = "S1p";
			}
		}
	}//SetAlternances

    // *******************************************************************
    // Nom :	GetSchedule
    // But :	Va chercher les horaires de la classe
    // Retour:	Tableau
    // Param.: 	(id de la classe)
    // *******************************************************************
    public function GetSchedule($class, $date = null)
    {
        setlocale(LC_TIME, 'fr', 'fr_CH', 'fr_CH.utf8');

        // Si la date est omise, utiliser la date actuelle
        if (is_null($date))
        {

            $date = time();
        }

		//	echo(12);
       $jour=strftime("%A", $date);

        $this->SetAlternances($date, $alterancepaire, $semestrepaire, $semestre);

//Relâches  vacances
if ((strtotime(date("Y-m-d", $date))>= strtotime('2017-02-18')) &&  (strtotime(date("Y-m-d", $date))<= strtotime('2017-02-26'))  )
{
	  $strSelect="SELECT id_sdh, idx_classe, cou_duree, cou_heuredebut, cla_nom, cou_alternance, cou_matcode, cou_matlibelle
FROM t_sdh, t_classe
WHERE 0=1

";
        $tabReponse=$this->Request($strSelect);
        return $tabReponse;
}
//Vacances d'hiver  vacances
if ((strtotime(date("Y-m-d", $date))>= strtotime('2016-12-23')) &&  (strtotime(date("Y-m-d", $date))<= strtotime('2017-01-08'))  )
{
	  $strSelect="SELECT id_sdh, idx_classe, cou_duree, cou_heuredebut, cla_nom, cou_alternance, cou_matcode, cou_matlibelle
FROM t_sdh, t_classe
WHERE 0=1

";
        $tabReponse=$this->Request($strSelect);
        return $tabReponse;
}
//Vacances de Pâques
if ((strtotime(date("Y-m-d", $date))>= strtotime('2017-04-08')) &&  (strtotime(date("Y-m-d", $date))<= strtotime('2017-04-23'))  )
{
	  $strSelect="SELECT id_sdh, idx_classe, cou_duree, cou_heuredebut, cla_nom, cou_alternance, cou_matcode, cou_matlibelle
FROM t_sdh, t_classe
WHERE 0=1

";
        $tabReponse=$this->Request($strSelect);
        return $tabReponse;
}
//Pont de l'Ascension
if ((strtotime(date("Y-m-d", $date))>= strtotime('2017-05-25')) &&  (strtotime(date("Y-m-d", $date))<= strtotime('2017-05-28'))  )
{
	  $strSelect="SELECT id_sdh, idx_classe, cou_duree, cou_heuredebut, cla_nom, cou_alternance, cou_matcode, cou_matlibelle
FROM t_sdh, t_classe
WHERE 0=1

";
        $tabReponse=$this->Request($strSelect);
        return $tabReponse;
}
//Lundi de Pentecôte

if (strtotime(date("Y-m-d", $date))== strtotime('2017-06-05'))
{
	  $strSelect="SELECT id_sdh, idx_classe, cou_duree, cou_heuredebut, cla_nom, cou_alternance, cou_matcode, cou_matlibelle
FROM t_sdh, t_classe
WHERE 0=1

";
        $tabReponse=$this->Request($strSelect);
        return $tabReponse;
}
//Vacances d'été


if ((strtotime(date("Y-m-d", $date))>= strtotime('2017-07-01')) &&  (strtotime(date("Y-m-d", $date))<= strtotime('2017-08-20'))  )
{
	  $strSelect="SELECT id_sdh, idx_classe, cou_duree, cou_heuredebut, cla_nom, cou_alternance, cou_matcode, cou_matlibelle
FROM t_sdh, t_classe
WHERE 0=1

";
        $tabReponse=$this->Request($strSelect);
        return $tabReponse;
}

//Jeûne fédéral

if (strtotime(date("Y-m-d", $date))== strtotime('2017-09-18'))
{
	  $strSelect="SELECT id_sdh, idx_classe, cou_duree, cou_heuredebut, cla_nom, cou_alternance, cou_matcode, cou_matlibelle
FROM t_sdh, t_classe
WHERE 0=1

";
        $tabReponse=$this->Request($strSelect);
        return $tabReponse;
}


//Vacances d'automne


if ((strtotime(date("Y-m-d", $date))>= strtotime('2017-10-07')) &&  (strtotime(date("Y-m-d", $date))<= strtotime('2017-10-22'))  )
{
	  $strSelect="SELECT id_sdh, idx_classe, cou_duree, cou_heuredebut, cla_nom, cou_alternance, cou_matcode, cou_matlibelle
FROM t_sdh, t_classe
WHERE 0=1

";
        $tabReponse=$this->Request($strSelect);
        return $tabReponse;
}

else
{
        $strSelect="SELECT id_sdh, idx_classe, cou_duree, cou_heuredebut, cla_nom, cou_alternance, cou_matcode, cou_matlibelle,t_professeur.pro_nom,t_professeur.pro_prenom
FROM t_sdh, t_classe,t_professeur
WHERE t_sdh.idx_professeur=t_professeur.id_professeur and idx_classe=$class
AND id_classe=$class
AND cou_jour='$jour'
AND (cou_alternance='H' OR cou_alternance='$semestre' OR cou_alternance='$semestrepaire' OR cou_alternance='$alterancepaire')
GROUP BY id_cours
ORDER BY cou_heuredebut;";
        $tabReponse=$this->Request($strSelect);
        return $tabReponse;
		}
    }//GetSchedule

    // *******************************************************************
    // Nom :	GetClassLate
    // But :	Va chercher les arrivées tardives de la journnée
    // Retour:	Tableau
    // Param.: 	(id de la classe)
    // *******************************************************************
    public function GetClassLate($class, $date = null)
    {
        if (is_null($date))
            $date = time();
        $today=date("Y-m-d", $date);

        $strSelect="SELECT id_tardive, tar_periode, idx_eleve, idx_cours, idx_classe FROM t_tardive WHERE idx_classe='$class' AND tar_date='$today'";
        $tabReponse=$this->Request($strSelect);
        return $tabReponse;
    }//GetLate

    // *******************************************************************
    // Nom :	GetClassDoor
    // But :	Va chercher les mises à la porte de la journnée
    // Retour:	Tableau
    // Param.: 	(id de la classe)
    // *******************************************************************
    public function GetClassDoor($class, $date = null)
    {
        if (is_null($date))
            $date = time();
        $today=date("Y-m-d", $date);

        $strSelect="SELECT id_porte, por_periode, idx_eleve, idx_cours, idx_classe FROM t_porte WHERE idx_classe='$class' AND por_date='$today'";
        $tabReponse=$this->Request($strSelect);
        return $tabReponse;
    }//GetLate

    // *******************************************************************
    // Nom :	GetClassMissing
    // But :	Va chercher les absences de la journnée d'une classe
    // Retour:	Tableau
    // Param.: 	(id de la classe)
    // *******************************************************************
    public function GetClassMissing($class, $date = null)
    {
        if (is_null($date))
            $date = time();
        $today=date("Y-m-d", $date);

        $strSelect="SELECT id_absence, abs_periode, abs_excuse, idx_eleve, idx_cours, idx_classe FROM t_absence WHERE idx_classe='$class' AND abs_date='$today'";
        $tabReponse=$this->Request($strSelect);
        return $tabReponse;
    }//GetLate

    // *******************************************************************
    // Nom :	GetClassOubliGym
    // But :	Va chercher les oublis d'affaires de gym de la journnée d'une classe
    // Retour:	Tableau
    // Param.: 	(id de la classe)
    // *******************************************************************
    public function GetClassOubliGym($class, $date = null)
    {
        if (is_null($date))
            $date = time();
        $today=date("Y-m-d", $date);

        $strSelect="SELECT id_oubligym, oub_periode, idx_eleve, idx_cours, idx_classe FROM t_oubligym WHERE idx_classe='$class' AND oub_date='$today'";
        $tabReponse=$this->Request($strSelect);
        return $tabReponse;
    }//GetClassOubliGym

    // *******************************************************************
    // Nom :	GetClassSante
    // But :	Va chercher les absences pour santé de la journnée d'une classe
    // Retour:	Tableau
    // Param.: 	(id de la classe)
    // *******************************************************************
    public function GetClassSante($class, $date = null)
    {
        if (is_null($date))
            $date = time();
        $today=date("Y-m-d", $date);

        $strSelect="SELECT id_sante, san_periode, idx_eleve, idx_cours, idx_classe FROM t_sante WHERE idx_classe='$class' AND san_date='$today'";
        $tabReponse=$this->Request($strSelect);
        return $tabReponse;
    }//GetClassSante

    // *******************************************************************
    // Nom :	GetClassNotices
    // But :	Va chercher les remarques pour la journnée d'une classe
    // Retour:	Tableau
    // Param.: 	(id de la classe)
    // *******************************************************************
    public function GetClassNotices($class, $date = null)
    {
        if (is_null($date))
            $date = time();
        $today=date("Y-m-d", $date);

        $strSelect="
          SELECT id_remarqueeleve, rem_message, rem_datedebut, rem_datefin, idx_codebarre
          FROM t_remarqueeleve
          INNER JOIN t_eleve ON idx_codebarre = id_codebarre
          WHERE idx_classe=$class AND (rem_datedebut IS NULL OR rem_datedebut <= '$today') AND (rem_datefin IS NULL OR rem_datefin >= '$today')";
        $tabReponse=$this->Request($strSelect);
        return $tabReponse;
    }//GetClassNotices

    // *******************************************************************
    // Nom :	InsertParticuliar
    // But :	Fait persister les cas particuliers dans la bdd
    // Retour:	aucun
    // Param.: 	(id eleve, période concernée, status (a - mp - t - p),
    // id classe, id cours, id codebarre de l'élève)
    // *******************************************************************
    public function InsertParticuliar($id_eleve, $id_professeur, $period, $status, $id_classe, $idcours, $idcodebarre, $date = null)
    {
        if (is_null($date))
            $date = time();
        $today = date("Y-m-d", $date);
        $classInfo = $this->GetClassInfo($id_classe);

        // Fonctions pour faire les modifications
        $addAbs = function() use ($today, $period, $id_eleve, $id_professeur, $idcours, $id_classe, $idcodebarre)
        {
            $req = $this->objConnexion->prepare('INSERT INTO t_absence(abs_date, abs_periode, idx_eleve, idx_professeur, idx_cours, idx_classe, idx_codebarre) VALUES(:date, :periode, :idx_eleve, :idx_professeur, :cours, :classe, :codebarre)');
            $req->execute(array(
                'date' => $today,
                'periode' => $period,
                'idx_eleve' => $id_eleve,
                'idx_professeur' => $id_professeur,
                'cours' => $idcours,
                'classe' => $id_classe,
                'codebarre' => $idcodebarre
            ));
        };
        $remAbs = function() use ($today, $id_eleve, $period)
        {
            $req = $this->objConnexion->prepare("DELETE FROM t_absence WHERE abs_date= :today AND idx_eleve= :ideleve AND abs_periode= :period");
            $req->execute(array(
                'today' => $today,
                'ideleve' => $id_eleve,
                'period' => $period
            ));
        };
        $addTar = function() use ($today, $period, $id_eleve, $id_professeur, $idcours, $id_classe, $idcodebarre)
        {
            $req = $this->objConnexion->prepare('INSERT INTO t_tardive(tar_periode, tar_date, idx_eleve, idx_professeur, idx_cours, idx_classe, idx_codebarre) VALUES(:period, :date, :eleve, :professeur, :cours, :classe, :codebarre)');
            $req->execute(array(
                'period' => $period,
                'date' => $today,
                'eleve' => $id_eleve,
                'professeur' => $id_professeur,
                'cours' => $idcours,
                'classe' => $id_classe,
                'codebarre' => $idcodebarre
            ));
        };
        $remTar = function() use ($today, $id_eleve, $period)
        {
            $req = $this->objConnexion->prepare("DELETE FROM t_tardive WHERE tar_date= :today AND idx_eleve= :ideleve AND tar_periode= :period");
            $req->execute(array(
                'today' => $today,
                'ideleve' => $id_eleve,
                'period' => $period
            ));
        };
        $addPor = function() use ($today, $period, $id_eleve, $id_professeur, $idcours, $id_classe, $idcodebarre)
        {
            $req = $this->objConnexion->prepare('INSERT INTO t_porte(por_date, por_periode, idx_eleve, idx_professeur, idx_cours, idx_classe, idx_codebarre) VALUES(:date, :periode, :idx_eleve, :idx_professeur, :cours, :classe, :codebarre)');
            $req->execute(array(
                'date' => $today,
                'periode' => $period,
                'idx_eleve' => $id_eleve,
                'idx_professeur' => $id_professeur,
                'cours' => $idcours,
                'classe' => $id_classe,
                'codebarre' => $idcodebarre
            ));
        };
        $remPor = function() use ($today, $id_eleve, $period)
        {
            $req = $this->objConnexion->prepare("DELETE FROM t_porte WHERE por_date= :today AND idx_eleve= :ideleve AND por_periode= :period");
            $req->execute(array(
                'today' => $today,
                'ideleve' => $id_eleve,
                'period' => $period
            ));
        };
        $addSan = function() use ($today, $period, $id_eleve, $id_professeur, $idcours, $id_classe, $idcodebarre)
        {
            $req = $this->objConnexion->prepare('INSERT INTO t_sante(san_date, san_periode, idx_eleve, idx_professeur, idx_cours, idx_classe, idx_codebarre) VALUES(:date, :periode, :idx_eleve, :idx_professeur, :cours, :classe, :codebarre)');
            $req->execute(array(
                'date' => $today,
                'periode' => $period,
                'idx_eleve' => $id_eleve,
                'idx_professeur' => $id_professeur,
                'cours' => $idcours,
                'classe' => $id_classe,
                'codebarre' => $idcodebarre
            ));
        };
        $remSan = function() use ($today, $id_eleve, $period)
        {
            $req = $this->objConnexion->prepare("DELETE FROM t_sante WHERE san_date= :today AND idx_eleve= :ideleve AND san_periode= :period");
            $req->execute(array(
                'today' => $today,
                'ideleve' => $id_eleve,
                'period' => $period
            ));
        };
        $addGym = function() use ($today, $period, $id_eleve, $id_professeur, $idcours, $id_classe, $idcodebarre)
        {
            $req = $this->objConnexion->prepare('INSERT INTO t_oubligym(oub_date, oub_periode, idx_eleve, idx_professeur, idx_cours, idx_classe, idx_codebarre) VALUES(:date, :periode, :idx_eleve, :idx_professeur, :cours, :classe, :codebarre)');
            $req->execute(array(
                'date' => $today,
                'periode' => $period,
                'idx_eleve' => $id_eleve,
                'idx_professeur' => $id_professeur,
                'cours' => $idcours,
                'classe' => $id_classe,
                'codebarre' => $idcodebarre
            ));
        };
        $remGym = function() use ($today, $id_eleve, $period)
        {
            $req = $this->objConnexion->prepare("DELETE FROM t_oubligym WHERE oub_date= :today AND idx_eleve= :ideleve AND oub_periode= :period");
            $req->execute(array(
                'today' => $today,
                'ideleve' => $id_eleve,
                'period' => $period
            ));
        };
        $addExc = function() use ($today, $period, $id_eleve, $id_professeur, $idcours, $id_classe, $idcodebarre)
        {
            $req = $this->objConnexion->prepare('INSERT INTO t_absence(abs_date, abs_periode, abs_excuse, idx_eleve, idx_professeur, idx_cours, idx_classe, idx_codebarre) VALUES(:date, :periode, \'Oui\', :idx_eleve, :idx_professeur, :cours, :classe, :codebarre)');
            $req->execute(array(
                'date' => $today,
                'periode' => $period,
                'idx_eleve' => $id_eleve,
                'idx_professeur' => $id_professeur,
                'cours' => $idcours,
                'classe' => $id_classe,
                'codebarre' => $idcodebarre
            ));
        };
        $remExc = function() use ($today, $id_eleve, $period)
        {
            $req = $this->objConnexion->prepare("DELETE FROM t_absence WHERE abs_date= :today AND idx_eleve= :ideleve AND abs_periode= :period");
            $req->execute(array(
                'today' => $today,
                'ideleve' => $id_eleve,
                'period' => $period
            ));
        };


        // Fin des fonctions de modifications

        // Choix des cases à faire tourner en fonction de si la classe est une classe ES ou non
        if ($classInfo['id_departement'] == 6)
        {
            if ($status=="p")
            {
                $addAbs();
            }
            if ($status=="a")
            {
                $remAbs();
                $addExc();
            }
            if ($status=="e")
            {
                $remExc();
            }

        }
        else
        {
            if($status=="p")
            {
                $addAbs();
            }
            if($status=="a")
            {
                $remAbs();
                $addTar();
            }
            if($status=="t")
            {
                $remTar();
                $addPor();
            }
            if($status=="mp")
            {
                $remPor();
                $addSan();
            }
            if ($status=="s")
            {
                $remSan();
                $addGym();
            }
            if ($status=="g")
            {
                $remGym();
				if($_SESSION['administration']==true)
				{
					$addExc();
				}


            }
            if ($status=="e")
            {
                $remExc();
            }
        }
    }//InsertParticuliar


    // *******************************************************************
    // Nom :	IsStudentExempted
    // But :	Cherche si un élève est dispensé du cours donné
    // Retour:	Tableau
    // Param.: 	(codebarre de l'élève)
    // *******************************************************************
    public function IsStudentExempted($idcodebarre)
    {
        $today=date("Y-m-d");
        $strSelect="SELECT id_absence, abs_periode, idx_eleve, idx_cours, idx_classe, cou_matlibelle, abs_date, abs_periode FROM t_absence, t_sdh WHERE idx_codebarre=$idcodebarre AND id_sdh=idx_cours;";
        $tabReponse=$this->Request($strSelect);
        return $tabReponse;
    }//IsStudentExempted

    // *******************************************************************
    // Nom :	GetStudentMissing
    // But :	Va chercher les absences de l'élève
    // Retour:	Tableau
    // Param.: 	(codebarre de l'élève)
    // *******************************************************************
    public function GetStudentMissing($idcodebarre)
    {

        $today=date("Y-m-d");
        $strSelect="SELECT id_absence, abs_periode, idx_eleve, idx_cours, t_sdh.idx_classe, cou_matlibelle, abs_date, abs_periode, abs_excuse, abs_commentaire FROM t_absence, t_sdh WHERE idx_codebarre=$idcodebarre AND id_sdh=idx_cours;";
        $tabReponse=$this->Request($strSelect);
        return $tabReponse;
    }//GetStudentMissing

    // *******************************************************************
    // Nom :	GetStudentLate
    // But :	Va chercher les arrivées tardives
    // Retour:	Tableau
    // Param.: 	(codebarre de l'élève)
    // *******************************************************************
    public function GetStudentLate($idcodebarre)
    {

        $today=date("Y-m-d");
        $strSelect="SELECT id_tardive, tar_periode, idx_eleve, idx_cours, t_sdh.idx_classe, cou_matlibelle, tar_date, tar_periode, cou_heuredebut, tar_commentaire FROM t_tardive, t_sdh WHERE idx_codebarre=$idcodebarre AND id_sdh=idx_cours;";
        $tabReponse=$this->Request($strSelect);
        return $tabReponse;
    }//GetStudentLate

    // *******************************************************************
    // Nom :	GetStudentDoor
    // But :	Va chercher les mises à la porte d'un élève
    // Retour:	Tableau
    // Param.: 	(codebarre de l'élève)
    // *******************************************************************
    public function GetStudentDoor($idcodebarre)
    {

        $today=date("Y-m-d");
        $strSelect="SELECT id_porte, por_periode, idx_eleve, idx_cours, t_sdh.idx_classe, cou_matlibelle, por_date, por_periode, cou_heuredebut, por_commentaire, CONCAT(pro_nom, ' ', pro_prenom) AS pro_nomprenom FROM t_porte, t_sdh, t_professeur WHERE idx_codebarre=$idcodebarre AND id_sdh=idx_cours AND t_porte.idx_professeur = id_professeur;";
        $tabReponse=$this->Request($strSelect);
        return $tabReponse;
    }//GetStudentDoor


    // *******************************************************************
    // Nom :	GetStudentMed
    // But :	Va chercher les visites au service médical d'un élève
    // Retour:	Tableau
    // Param.: 	(codebarre de l'élève)
    // *******************************************************************
    public function GetStudentMed($idcodebarre)
    {
        $strSelect="SELECT id_sante, san_periode, idx_eleve, idx_cours, idx_classe, cou_matlibelle, san_date, san_periode, cou_heuredebut FROM t_sante, t_sdh WHERE idx_codebarre=$idcodebarre AND id_sdh=idx_cours;";
        $tabReponse=$this->Request($strSelect);
        return $tabReponse;
    }//GetStudentMed

    // *******************************************************************
    // Nom :	GetStudentGym
    // But :	Va chercher les oublis d'affaires de gym d'un élève
    // Retour:	Tableau
    // Param.: 	(codebarre de l'élève)
    // *******************************************************************
    public function GetStudentGym($idcodebarre)
    {
        $strSelect="SELECT id_oubligym, oub_periode, idx_eleve, idx_cours, t_sdh.idx_classe, cou_matlibelle, oub_date, oub_periode, cou_heuredebut FROM t_oubligym, t_sdh WHERE idx_codebarre=$idcodebarre AND id_sdh=idx_cours;";
        $tabReponse=$this->Request($strSelect);
        return $tabReponse;
    }//GetStudentGym

    // *******************************************************************
    // Nom :	GetStudentNotices
    // But :	Va chercher les remarques d'un élève
    // Retour:	Tableau
    // Param.: 	(codebarre de l'élève)
    // *******************************************************************
    public function GetStudentNotices($idcodebarre)
    {
        $today=date("Y-m-d");
        $strSelect="SELECT id_remarqueeleve, rem_datedebut, rem_datefin, rem_message, idx_codebarre FROM t_remarqueeleve WHERE idx_codebarre=$idcodebarre;";
        $tabReponse=$this->Request($strSelect);
        return $tabReponse;
    }//GetStudentNotices

    // *******************************************************************
    // Nom :	UpdateStudentNotices
    // But :	Ecrase les remarques pour un élève par celles fournies
    // Retour:	RIEN
    // Param.: 	(code barre de l'élève, notices)
    // *******************************************************************
    public function UpdateStudentNotices($barCode, $notices)
    {
        // Supprime les remarques actuelles
        $req = $this->objConnexion->prepare("DELETE FROM t_remarqueeleve WHERE idx_codebarre = :id");
        $req->execute(array(
            'id' => $barCode,
        ));

        // Ajout des nouvelles valeurs
        $sql = "INSERT INTO t_remarqueeleve (rem_message, rem_datedebut, rem_datefin, idx_codebarre) VALUES :REPLACEVALUESHERE";
        $values = "";
        $pdo = &$this->objConnexion;
        foreach ($notices as $notice)
        {
            if ($notice->startDate === "")
                $notice->startDate = "NULL";
            else
                $notice->startDate = $pdo->quote($notice->startDate);
            if ($notice->endDate === "")
                $notice->endDate = "NULL";
            else
                $notice->endDate = $pdo->quote($notice->endDate);
            $values .= "(". $pdo->quote($notice->message) .", $notice->startDate, $notice->endDate, ". $pdo->quote($barCode) ."),";
        }
        $values = substr($values, 0, strlen($values) - 1);
        $sql = str_replace(":REPLACEVALUESHERE", $values, $sql);
        $req = $this->objConnexion->prepare($sql);
        $req->execute();

    }//UpdateStudentNotices

    // *******************************************************************
    // Nom :	UpdateMissings
    // But :	Met à jour les absences données
    // Retour:	RIEN
    // Param.: 	(absences)
    // *******************************************************************
    public function UpdateMissings($missings)
    {
        $req = $this->objConnexion->prepare("UPDATE t_absence SET abs_excuse = :excuse, abs_commentaire = :commentaire WHERE id_absence = :id");
        foreach ($missings as $miss)
        {
            $req->execute([
                'id' => $miss->id,
                'excuse' => $miss->checked,
                'commentaire' => $miss->comment
            ]);
        }
        $req->closeCursor();
    }//UpdateMissings

    // *******************************************************************
    // Nom :	UpdateLates
    // But :	Met à jour les absences données
    // Retour:	RIEN
    // Param.: 	(absences)
    // *******************************************************************
    public function UpdateLates($lages)
    {
        $req = $this->objConnexion->prepare("UPDATE t_tardive SET tar_commentaire = :commentaire WHERE id_tardive = :id");
        foreach ($lages as $late)
        {
            $req->execute([
                'id' => $late->id,
                'commentaire' => $late->comment
            ]);
        }
        $req->closeCursor();
    }//UpdateLates

    // *******************************************************************
    // Nom :	UpdateDoors
    // But :	Met à jour les absences données
    // Retour:	RIEN
    // Param.: 	(absences)
    // *******************************************************************
    public function UpdateDoors($doors)
    {
        $req = $this->objConnexion->prepare("UPDATE t_porte SET por_commentaire = :commentaire WHERE id_porte = :id");
        foreach ($doors as $door)
        {
            $req->execute([
                'id' => $door->id,
                'commentaire' => $door->comment
            ]);
        }
        $req->closeCursor();
    }//UpdateDoors

    // *******************************************************************
    // Nom :	AddProfPresence
    // But :	Insert la présence d'un prof à un cours dans la base de données
    // Retour:	RIEN
    // Param.: 	(id prof, id cours, date)
    // *******************************************************************
    public function AddProfPresence($idProf, $idCours, $date)
    {
        $req = $this->objConnexion->prepare("INSERT INTO t_presprof (pre_date, idx_professeur, idx_cours) VALUES (:date, :prof, :cours)");
        $req->execute([
            'prof' => $idProf,
            'cours' => $idCours,
            'date' => $date
        ]);
        $req->closeCursor();
    }//AddProfPresence

    // *******************************************************************
    // Nom :	RemoveProfPresence
    // But :	Supprime la présence d'un prof à un cours dans la base de données
    // Retour:	RIEN
    // Param.: 	(id prof, id cours, date)
    // *******************************************************************
    public function RemoveProfPresence($idProf, $idCours, $date)
    {
        $req = $this->objConnexion->prepare("DELETE FROM t_presprof WHERE idx_professeur = :prof AND pre_date = :date AND idx_cours = :cours");
        $req->execute([
            'prof' => $idProf,
            'cours' => $idCours,
            'date' => $date
        ]);
        $req->closeCursor();
    }//RemoveProfPresence

    // *******************************************************************
    // Nom :	IsProfPresent
    // But :	Cherche si un prof est présent à un cours
    // Retour:	BOOLEAN - Si le prof est présent
    // Param.: 	(id prof, id cours, date)
    // *******************************************************************
    public function IsProfPresent($idProf, $idCours, $date)
    {
        $pdo = &$this->objConnexion;
        return $this->RequestBln("SELECT id_presprof FROM t_presprof WHERE idx_professeur = ". $pdo->quote($idProf) ." AND pre_date = ". $pdo->quote($date) ." AND idx_cours = ". $pdo->quote($idCours));
    }//IsProfPresent

    // *******************************************************************
    // Nom :	WichProfPresent
    // But :	Cherche quel prof est présent
    // Retour:	BOOLEAN - Si le prof est présent
    // Param.: 	(id prof, id cours, date)
    // *******************************************************************
    public function WichProfPresent($idProf, $idCours, $date)
    {
        $pdo = &$this->objConnexion;
        $strSelect="SELECT idx_professeur FROM t_presprof WHERE pre_date = ". $pdo->quote($date) ." AND idx_cours = ". $pdo->quote($idCours);
        $tabReponse=$this->Request($strSelect);
        return $tabReponse;
    }//IsProfPresent

    // *******************************************************************
    // Nom :	Login
    // But :	Permet le login des enseigneants.
    // Retour:	BOOLEAN - Création des variables de session
    // Param.: 	(nom, mot de pass ou code barre enseignant)
    // *******************************************************************
    public function Login($nom, $password)
    {
        $pdo = &$this->objConnexion;
        $strSelect="SELECT * FROM t_professeur WHERE pro_activedirectory=". $pdo->quote($nom) ." AND (pro_codebarre=". $pdo->quote($password) ." OR pro_motdepasse=". $pdo->quote($password) .")";
        $tabReponse=$this->Request($strSelect);
        $counter=0;
        foreach ($tabReponse as $entry)
        {
            if($entry['pro_activedirectory']==$nom)
            {
                $nom=$entry['pro_nom'];
                $prenom=$entry['pro_prenom'];
                $nom="$nom $prenom";

                $_SESSION['user_id']=$entry['id_professeur'];
                $_SESSION['login'] = $entry['pro_activedirectory'];
                $_SESSION['user_name']=$nom;
                /*echo $_SESSION['user_name'];
                echo $_SESSION['user_id'];*/

                $counter++;
            }

        }

        if($counter==0)
        {

            return false;

        }
        else
        {

            return true;
        }


    }//login

    // *******************************************************************
    // Nom :	UpdatePassword
    // But :	Permet le changer un mot de passe
    // Retour:	RIEN
    // Param.: 	(id de l'enseignant, nouveau mot de passe)
    // *******************************************************************
    public function UpdatePassword($userId, $newPassword)
    {
        $req = $this->objConnexion->prepare("UPDATE t_professeur SET pro_motdepasse = :newPass WHERE id_professeur = :id");
        $req->execute(array(
            'id' => $userId,
            'newPass' => $newPassword
        ));
    }//UpdatePassword

    // *******************************************************************
    // Nom :	GetDepartment
    // But :	Va chercher les les départements
    // Retour:	Tableau
    // Param.: 	-
    // *******************************************************************
    public function GetDepartment()
    {

        $strSelect="SELECT * FROM t_departement";
        $tabReponse=$this->Request($strSelect);
        return $tabReponse;
    }//GetDepartment

    // *******************************************************************
    // Nom :	GetClassDepartment
    // But :	Va chercher  les départements
    // Retour:	Tableau
    // Param.: 	(id du departement)
    // *******************************************************************
    public function GetClassDepartment($iddepartmeent)
    {

        $strSelect="SELECT * FROM t_classe WHERE idx_departement=$iddepartmeent";
        $tabReponse=$this->Request($strSelect);
        return $tabReponse;
    }//GetClassDepartment


    // *******************************************************************
    // Nom :	InsertNewClass
    // But :	Insère une nouvelle classe dans le profil d'un enseignant
    // Retour:	Tableau
    // Param.: 	(id de la classe)
    // *******************************************************************

    public function InsertNewClass($id)
    {


        $req = $this->objConnexion->prepare('INSERT INTO t_maclasse(mac_nom, idx_professeur, idx_classe1) VALUES(:nom, :prof, :classe1)');
        $req->execute(array(
            'nom' => 'test',
            'prof' => $_SESSION['user_id'],
            'classe1' => $id
        ));
    }//GetClassDepartment



// *******************************************************************
    // Nom :	GetStudent
    // But :	Recherche les etudiants
    // Retour:	Tableau
    // Param.: 	(nom de la classe)
    // *******************************************************************
    public function getstudents()
    {

        $strSelect="select * from t_eleve";
        $tabReponse=$this->Request($strSelect);
        return $tabReponse;
    }//GetMyClass

	// *******************************************************************
    // Nom :	GetCurrentStage
    // But :	Trouver le stage actuel
    // Retour:	array
    // Param.: 	(id élève)
    // *******************************************************************
    public function GetCurrentStage($id_eleve, $date = "")
    {
        if($date == ""){
            $date = date("Y-m-d");
        }

        $req = $this->objConnexion->prepare("
            SELECT sta_dateDeb, DATE_FORMAT(sta_dateFin, '%Y-%m-%d') as sta_dateFin
            FROM t_stage
            WHERE DATE(sta_dateFin) >= DATE(:staDate)
            AND DATE(sta_dateDeb) <= DATE(:staDate)
            AND idx_eleve = :eleveId
            ORDER BY DATE(sta_dateDeb) ASC
            LIMIT 1
        ");
        $req->execute(array(
            'eleveId' => $id_eleve,
            'staDate' => $date
        ));

        $response = $req->fetch(PDO::FETCH_ASSOC);
        $req->closeCursor();

        return $response;
    }//GetCurrentStage



 // *******************************************************************
    // Nom :	GetStage
    // But :	Récupère les stages lié à un élève
    // Param.: 	(id élève)
    // *******************************************************************
    public function GetStage($id_eleve)
    {
        $req = $this->objConnexion->prepare("
          SELECT *
          FROM t_stage
          WHERE idx_eleve = :eleveId
        ");
        $req->execute(array(
            'eleveId' => $id_eleve
        ));
        $response = $req->fetchAll(PDO::FETCH_ASSOC);
        $req->closeCursor();

        return $response;
    }//GetStage
    // *******************************************************************
    // Nom :	GetMyClasses
    // But :	Va chercher les classes du profil d'un enseignant
    // Retour:	Tableau
    // Param.: 	(id de la classe)
    // *******************************************************************
    public function GetMyClasses()
    {
        $id=$_SESSION['user_id'];
        $strSelect="SELECT * FROM t_maclasse, t_classe WHERE idx_professeur=$id AND idx_classe1=id_classe";
        $tabReponse=$this->Request($strSelect);
        return $tabReponse;
    }//GetClassDepartment

    // *******************************************************************
    // Nom :	DeleteMyClass
    // But :	Supprime une classe du profil d'un enseignant
    // Retour:	RIEN
    // Param.: 	(id de la classe)
    // *******************************************************************
    public function DeleteMyClass($id)
    {

        $req = $this->objConnexion->prepare("DELETE FROM t_maclasse WHERE id_maclasse= :id");
        $req->execute(array(
            'id' => $id,
        ));
    }//DeleteMyClass

    // *******************************************************************
    // Nom :	GetComments
    // But :	Obtient tous les commentaires assignés à une classe pour un cours
    // Retour:	Tableau
    // Param.: 	(id de la classe, matcode de la branche)
    // *******************************************************************
    public function GetComments($id_class, $mat_code)
    {
        $req = $this->objConnexion->prepare("
          SELECT id_commentaire, c.com_contenu AS com_contenu, c.com_date AS com_date, p.id_professeur as id_professeur, p.pro_nom AS pro_nom, p.pro_prenom AS pro_prenom
          FROM t_commentaire AS c LEFT JOIN t_professeur AS p ON c.idx_professeur = p.id_professeur
          WHERE c.idx_classe = :class AND c.idx_matcode = :matcode
          ORDER BY c.com_date DESC");
        $req->execute(array(
            'class' => $id_class,
            'matcode' => $mat_code
        ));
        $response = $req->fetchAll(PDO::FETCH_ASSOC);
        $req->closeCursor();

        return $response;
    }//GetComments

    // *******************************************************************
    // Nom :	InsertComment
    // But :	Insert un nouveau commentaire pour une classe à un cours
    // Retour:	RIEN
    // Param.: 	(commentaire, code de la matière, id enseignant, id classe)
    // *******************************************************************
    public function InsertComment($comment, $mat_code, $id_teacher, $id_class)
    {
        $req = $this->objConnexion->prepare("
          INSERT INTO t_commentaire
            (com_contenu, com_date, idx_matcode, idx_professeur, idx_classe)
            VALUES (:comment, NOW(), :matcode, :teacher, :class);
        ");
        $req->execute(array(
            'comment' => $comment,
            'matcode' => $mat_code,
            'teacher' => $id_teacher,
            'class' => $id_class
        ));
    }//InsertComment

    // *******************************************************************
    // Nom :	UpdateComment
    // But :	Met à jour un commentaire existant, ou le supprime si
    //          aucune nouvelle valeur n'est fournie
    // Retour:	RIEN
    // Param.: 	(id commentaire, commentaire)
    // *******************************************************************
    public function UpdateComment($commentId, $newComment)
    {
        if (strlen($newComment) > 0)
        {
            $req = $this->objConnexion->prepare("UPDATE t_commentaire SET com_contenu = :content WHERE id_commentaire = :id");
            $req->execute(array(
                'id' => $commentId,
                'content' => $newComment
            ));
        }
        else
        {
            $req = $this->objConnexion->prepare("DELETE FROM t_commentaire WHERE id_commentaire = :id");
            $req->execute(array(
                'id' => $commentId
            ));
        }
    }//UpdateComment

    // *******************************************************************
    // Nom :	GetClassInfo
    // But :	Récupère les données d'une classe à partir de son id
    // Retour:	array - les données de la classe
    // Param.: 	(id classe)
    // *******************************************************************
    public function GetClassInfo($id_class)
    {
        $req = $this->objConnexion->prepare("
          SELECT id_classe, cla_nom,cla_niveau, cla_type, cla_jourdecours, typ_nom, dep_nom, id_departement
          FROM t_classe
          LEFT JOIN t_typeformation ON id_typeformation = idx_typeformation
          INNER JOIN t_departement ON id_departement = t_classe.idx_departement
          WHERE id_classe = :classId
        ");
        $req->execute(array(
            'classId' => $id_class
        ));

        $response = $req->fetch(PDO::FETCH_ASSOC);
        $req->closeCursor();
        return $response;
    }//GetClassName

    // *******************************************************************
    // Nom :	GetMatName
    // But :	Récupère le nom d'un cours à partir de son MatCode
    // Retour:	string - le nom du cours
    // Param.: 	(mat code)
    // *******************************************************************
    public function GetMatName($matCode)
    {
        $req = $this->objConnexion->prepare("
          SELECT cou_matlibelle FROM t_sdh WHERE cou_matcode = :matCode LIMIT 1
        ");
        $req->execute(array(
            'matCode' => $matCode
        ));

        $column = $req->fetchColumn(0);
        $req->closeCursor();
        return $column;
    }//GetMatName

    // *******************************************************************
    // Nom :	GetCoches
    // But :	Récupère la liste des coches possibles
    // Retour:	Tableau
    // Param.: 	Rien
    // *******************************************************************
    public function GetCoches()
    {
        return $this->Request("SELECT * FROM t_typecoche");
    }//GetCoches

    // *******************************************************************
    // Nom :	GetClassCoches
    // But :	Récupère la liste des coches pour une classe
    // Retour:	Tableau
    // Param.: 	(id classe)
    // *******************************************************************
    public function GetClassCoches($classId)
    {
        $req = $this->objConnexion->prepare("
          SELECT id_typecoche, id_eleve, typ_nom, ele_prenom, ele_nom, COUNT(id_eleve) AS ammount
          FROM t_coche AS c
          INNER JOIN t_eleve AS e ON c.idx_eleve = e.id_eleve
          INNER JOIN t_typecoche AS t ON t.id_typecoche = c.idx_typecoche
          LEFT JOIN t_classe AS cla ON cla.id_classe = e.idx_classe
          WHERE id_classe = :class
          GROUP BY id_eleve, typ_nom");
        $req->execute(array(
            'class' => $classId
        ));
        $response = $req->fetchAll(PDO::FETCH_ASSOC);
        $req->closeCursor();

        // Post traitement php: regroupe les coches par élève
        $students = array();
        foreach ($response as $item)
        {
            $fullStudentName = $item['ele_prenom'] ." ". $item['ele_nom'];
            $item['fullName'] = $fullStudentName;
            if (!isset($students[$item['id_eleve']]))
                $students[$item['id_eleve']] = array();
            $students[$item['id_eleve']][$item['id_typecoche']] = $item;
        }

        return $students;
    }//GetClassCoches

    // *******************************************************************
    // Nom :	RemoveCoche
    // But :	Supprime une coche pour l'élève donné
    // Retour:	Rien
    // Param.: 	(id élève, id type de coche)
    // *******************************************************************
    public function RemoveCoche($studentId, $cocheTypeId)
    {
        $req = $this->objConnexion->prepare("DELETE FROM t_coche WHERE idx_eleve= :student AND idx_typecoche = :cocheType ORDER BY coc_creation DESC LIMIT 1");
        $req->execute(array(
            'student' => $studentId,
            'cocheType' => $cocheTypeId
        ));
    }//RemoveCoche

    // *******************************************************************
    // Nom :	AddCoche
    // But :	Ajoute une coche pour l'élève donné
    // Retour:	Rien
    // Param.: 	(id élève, id type de coche, commentaire)
    // *******************************************************************
    public function AddCoche($studentId, $cocheTypeId, $comment = "")
    {
        $req = $this->objConnexion->prepare("INSERT INTO t_coche (idx_eleve, idx_typecoche, coc_creation, coc_commentaire) VALUES (:student, :cocheType, NOW(), :comment)");
        $req->execute(array(
            'student' => $studentId,
            'cocheType' => $cocheTypeId,
            'comment' => $comment
        ));
    }//AddCoche

    // *******************************************************************
    // Nom :	AddLastCocheComment
    // But :	Ajoute un commentaire à la dernière coche pour l'élève donné
    // Retour:	Rien
    // Param.: 	(id élève, commentaire)
    // *******************************************************************
    public function AddLastCocheComment($studentId, $comment)
    {
        $req = $this->objConnexion->prepare("UPDATE t_coche SET coc_commentaire = :comment WHERE idx_eleve = :student ORDER BY coc_creation DESC LIMIT 1");
        $req->execute(array(
            'student' => $studentId,
            'comment' => $comment
        ));
    }//AddLastCocheComment

    // *******************************************************************
    // Nom :	GetNumCoches
    // But :	Obtient le nombre de coches d'un certain type pour un certain élève
    // Retour:	Entier, Le nombre de coches
    // Param.: 	(id élève, id type de coche)
    // *******************************************************************
    public function GetNumCoches($studentId, $cocheTypeId)
    {
        $req = $this->objConnexion->prepare("SELECT COUNT(idx_eleve) FROM t_coche WHERE idx_eleve = :student AND idx_typecoche = :cocheType GROUP BY idx_eleve");
        $req->execute(array(
            'student' => $studentId,
            'cocheType' => $cocheTypeId
        ));

        $column = $req->fetchColumn(0);
        $req->closeCursor();
        if ($column === false) $column = 0;
        return $column;
    }//GetNumCoches






    // *******************************************************************
    // Nom :	datediff
    // But :	Permet de savoir si une date est paire ou impaire
    // Retour:	-
    // Param.:
    // *******************************************************************
    public function datediff($interval, $datefrom, $dateto, $using_timestamps = false) {
        /*
        $interval can be:
        yyyy - Number of full years
        q - Number of full quarters
        m - Number of full months
        y - Difference between day numbers
            (eg 1st Jan 2004 is "1", the first day. 2nd Feb 2003 is "33". The datediff is "-32".)
        d - Number of full days
        w - Number of full weekdays
        ww - Number of full weeks
        h - Number of full hours
        n - Number of full minutes
        s - Number of full seconds (default)
        */

        if (!$using_timestamps) {
            $datefrom = strtotime($datefrom, 0);
            $dateto = strtotime($dateto, 0);
        }
        $difference = $dateto - $datefrom; // Difference in seconds

        switch($interval) {

            case 'yyyy': // Number of full years

                $years_difference = floor($difference / 31536000);
                if (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom), date("j", $datefrom), date("Y", $datefrom)+$years_difference) > $dateto) {
                    $years_difference--;
                }
                if (mktime(date("H", $dateto), date("i", $dateto), date("s", $dateto), date("n", $dateto), date("j", $dateto), date("Y", $dateto)-($years_difference+1)) > $datefrom) {
                    $years_difference++;
                }
                $datediff = $years_difference;
                break;

            case "q": // Number of full quarters

                $quarters_difference = floor($difference / 8035200);
                while (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom)+($quarters_difference*3), date("j", $dateto), date("Y", $datefrom)) < $dateto) {
                    $quarters_difference++;
                }
                $quarters_difference--;
                $datediff = $quarters_difference;
                break;

            case "m": // Number of full months

                $months_difference = floor($difference / 2678400);
                while (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom)+($months_difference), date("j", $dateto), date("Y", $datefrom)) < $dateto) {
                    $months_difference++;
                }
                $months_difference--;
                $datediff = $months_difference;
                break;

            case 'y': // Difference between day numbers

                $datediff = date("z", $dateto) - date("z", $datefrom);
                break;

            case "d": // Number of full days

                $datediff = floor($difference / 86400);
                break;

            case "w": // Number of full weekdays

                $days_difference = floor($difference / 86400);
                $weeks_difference = floor($days_difference / 7); // Complete weeks
                $first_day = date("w", $datefrom);
                $days_remainder = floor($days_difference % 7);
                $odd_days = $first_day + $days_remainder; // Do we have a Saturday or Sunday in the remainder?
                if ($odd_days > 7) { // Sunday
                    $days_remainder--;
                }
                if ($odd_days > 6) { // Saturday
                    $days_remainder--;
                }
                $datediff = ($weeks_difference * 5) + $days_remainder;
                break;

            case "ww": // Number of full weeks

                $datediff = floor($difference / 604800);
                break;

            case "h": // Number of full hours

                $datediff = floor($difference / 3600);
                break;

            case "n": // Number of full minutes

                $datediff = floor($difference / 60);
                break;

            default: // Number of full seconds (default)

                $datediff = $difference;
                break;
        }

        return $datediff;

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
	// Nom :	RequestPrepared
	// But :	Permet d'exécuter une requête SQL préparée.
	//          Devrait être préféré par rapport à Request() simple.
	// Retour:	Tableau
	// Param.:  (Requête SQL)
	//          (Paramètres de la requête)
	// *******************************************************************
	private function RequestPrepared($Request, $Params = array())
	{
		$statement = $this->objConnexion->prepare($Request);
		$statement->execute($Params);

		$response = $statement->fetchAll(PDO::FETCH_ASSOC);
		$statement->closeCursor();

		return $response;
	}//Request

    // *******************************************************************
    // Nom :	RequestBln
    // But :	Permet d'exécuter une requête SQL afin de savoir si elle
    //          retourne quelque chose.
    // Retour:	BOOLEAN
    // Param.: (Requête SQL)
    // *******************************************************************
	private function RequestBln($Request)
    {
        $Reponse = $this->objConnexion->query($Request);

        if(!empty($Reponse->fetchAll(PDO::FETCH_ASSOC)))
		{
			return true;
		}
		else
		{
			$Reponse->closeCursor();
			return false;
		}
    }//RequestBln




// *******************************************************************
    // Nom :	GetStudent
    // But :	Recherche des détails d'un élève
    // Retour:	Tableau
    // Param.: 	(id de la classe)
    // *******************************************************************
    public function GetStudentbyClasse($id)
    {

        $strSelect="select * from t_eleve where  t_eleve.id_codebarre not in (select idx_codebarre from t_rupture where rup_suit ='Non') and idx_classe = '$id' ";
        $tabReponse=$this->Request($strSelect);
        return $tabReponse;
    }//GetMyClass


	// *******************************************************************
	// Nom :	GetSuiviCours
	// But :	Recherche les commentaires de suivi pour une classe un jour donné
	// Retour:	Tableau
	// Param.: 	(id de la classe)
	//          (date à laquelle rechercher)
	// *******************************************************************
	public function GetSuiviCours($classId, $timestamp)
	{
		setlocale(LC_TIME, 'fr', 'fr_CH', 'fr_CH.utf8');
		$dayOfWeek = strtolower(strftime("%A", $timestamp));
		$date = strftime("%Y-%m-%d", $timestamp);

		$this->SetAlternances($timestamp, $alternance, $semestrePair, $semestre);

		$request = "SELECT
				id_suivicours,
				sui_type,
				sui_commentaire,
				sui_date,
				idx_matcode,
				sui.idx_classe,
				sui.idx_professeur,
				pro_nom,
				pro_prenom,
				id_sdh,
				cou_matlibelle
			FROM t_suivicours AS sui
			LEFT JOIN t_sdh AS sdh ON sui.idx_classe = sdh.idx_classe AND idx_matcode = cou_matcode AND cou_jour = :dayOfWeek
			LEFT JOIN t_professeur AS pro ON id_professeur = sui.idx_professeur
			WHERE sui.idx_classe = :classId
				AND sui_date = :date
				AND (
					(cou_alternance = 'H' OR cou_alternance = :semestre OR cou_alternance = :semestrePair OR cou_alternance = :alternancePaire)
					OR idx_matcode = ''
				)
			GROUP BY id_suivicours";

		return $this->RequestPrepared(
			$request,
			array(
				'classId' => $classId,
				'date' => $date,
				'dayOfWeek' => $dayOfWeek,
				'semestre' => $semestre,
				'semestrePair' => $semestrePair,
				'alternancePaire' => $alternance
			)
		);
	}

	// *******************************************************************
	// Nom :	InsertSuiviCours
	// But :	Insère un commentaire de suivi
	// Retour:	aucun
	// Param.:  (type, comment, date, matCode, class, teacher)
	// *******************************************************************
	public function InsertSuiviCours($type, $comment, $date, $matCode, $class, $teacher)
	{
		$this->RequestPrepared("INSERT INTO t_suivicours (sui_type, sui_commentaire, sui_date, idx_matcode, idx_classe, idx_professeur) VALUES (?, ?, ?, ?, ?, ?)", array(
			$type,
			$comment,
			$date,
			$matCode,
			$class,
			$teacher
		));
	}

	// *******************************************************************
	// Nom :	UpdateSuiviCours
	// But :	Modifie un commentaire de suivi. Attention à vérifier les clés du
	//			tableau $newData car elles sont vulnérables à l'injection SQL
	// Retour:	aucun
	// Param.:  (id, newData)
	// *******************************************************************
	public function UpdateSuiviCours($id, $newData)
	{
		if (empty($newData))
			return;

		$sql = "UPDATE t_suivicours SET ";

		$values = array();
		foreach ($newData as $field => $value)
		{
			$sql .= "`$field` = ?,";
			$values[] = $value;
		}

		$sql = rtrim($sql, ",");
		$sql .= " WHERE id_suivicours = ?";
		$values[] = $id;

		$this->RequestPrepared($sql, $values);
	}

	// *******************************************************************
	// Nom :	InsertSuiviCours
	// But :	Insère un commentaire de suivi
	// Retour:	aucun
	// Param.:  (type, comment, date, matCode, class, teacher)
	// *******************************************************************
	public function DeleteSuiviCours($id)
	{
		$this->RequestPrepared("DELETE FROM t_suivicours WHERE id_suivicours = :id", array(
			'id' => $id
		));
	}












}//Class