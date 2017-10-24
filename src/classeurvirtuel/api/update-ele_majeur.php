<?php
session_start();
//*********************************************************************************
// Societe: CEPM
// Auteur : Vincent Montet
// Date dernière modification   : 30.08.2017
// But    : Update ele_majeur tous les jours par cron
//*********************************************************************************


// Inclusion de la classe d'interaction avec la base de données
include_once('../include/mysql.inc.php');
$bd = new dbIfc();

// Appel la classe qui met à jour le champs majeur
$bd->UpdateMajeur();
unset($bd);

//SELECT *, DATE_FORMAT(FROM_DAYS(DATEDIFF(NOW(), `ele_datedenaissance`)), "%Y") as age FROM `t_eleve` WHERE `ele_datedenaissance` BETWEEN date('1998-06-01') and date('1999-09-01') ORDER BY `t_eleve`.`ele_datedenaissance` DESC
//SELECT *, trim(round(DATE_FORMAT(FROM_DAYS(DATEDIFF(NOW(), `ele_datedenaissance`)), "%Y"),2))+0 as agecal FROM `t_eleve` WHERE `ele_datedenaissance` BETWEEN date('1998-06-01') and date('1999-09-01') ORDER BY `t_eleve`.`ele_datedenaissance` DESC
//SELECT *, round(DATE_FORMAT(FROM_DAYS(DATEDIFF(NOW(), `ele_datedenaissance`)), "%Y"),0) as agecal FROM `t_eleve` WHERE `ele_datedenaissance` BETWEEN date('1998-06-01') and date('1999-09-01') ORDER BY `t_eleve`.`ele_datedenaissance` DESC