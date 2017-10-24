<?php

// Check si l'utilisateur est connecté
if(empty($_SESSION['user_id']))
{
    header("Location:login.php");
    die("Forbidden");
}

//Déclaration des constantes


$user='nlhj_databuser';

$pass='classeur123';
$filename="swagg.csv";





$pdo = new PDO('mysql:host=nlhj.myd.infomaniak.com;dbname=nlhj_cepm', $user, $pass);



//requete
if($type==1 || $type==2 || $type==3)
{
    $filename="absences_".$startDate."_".$endDate.".csv";
    $tableName = "t_absence";
    $colPrefix = "abs";
    if ($type == 2) $filename = "absences-excusees_".$startDate."_".$endDate.".csv";
    if ($type == 3) $filename = "absences-non-excusees_".$startDate."_".$endDate.".csv";
}
if($type==4)
{
    $filename="tardives_".$startDate."_".$endDate.".csv";
    $tableName = "t_tardive";
    $colPrefix = "tar";
}
if($type==5)
{
    $filename="porte_".$startDate."_".$endDate.".csv";
    $tableName = "t_porte";
    $colPrefix = "por";
}
if($type==6)
{
    $filename="oubli-gym_".$startDate."_".$endDate.".csv";
    $tableName = "t_oubligym";
    $colPrefix = "oub";
}
if($type==7)
{
    $filename="sante_".$startDate."_".$endDate.".csv";
    $tableName = "t_sante";
    $colPrefix = "san";
}

$arguments = [
    'startDate' => $startDate,
    'endDate' => $endDate
];
$sql = "SELECT ";
$sql .= "cla_nom, ".$colPrefix."_periode, id_eleve, id_sdh, id_classe, cou_matlibelle, ".$colPrefix."_date, WEEKOFYEAR(".$colPrefix."_date) AS semaine, ele_nom, ele_prenom, ent_nom, ent_rue, ent_npa, ent_localite, CONCAT(pro_prenom, ' ', pro_nom) AS `saisi par`";
if ($type == 1 || $type == 2 || $type == 3)
    $sql .= ", ".$colPrefix."_excuse";
$sql .= " FROM $tableName";
$sql .= " LEFT JOIN t_sdh ON id_sdh = $tableName.idx_cours";
$sql .= " LEFT JOIN t_eleve ON id_eleve = $tableName.idx_eleve";
$sql .= " LEFT JOIN t_entreprise ON id_entreprise = t_eleve.idx_entreprise";
$sql .= " LEFT JOIN t_classe ON id_classe = t_eleve.idx_classe";
$sql .= " LEFT JOIN t_departement ON id_departement = t_classe.idx_departement";
$sql .= " LEFT JOIN t_professeur ON id_professeur = $tableName.idx_professeur";
$sql .= " WHERE ".$colPrefix."_date >= :startDate AND ".$colPrefix."_date <= :endDate";
if ($department !== "*")
{
    $sql .= " AND id_departement = :department";
    $arguments['department'] = $department;
}
if ($type == 2)
    $sql .= " AND ".$colPrefix."_excuse = 'Oui'";
if ($type == 3)
    $sql .= " AND ".$colPrefix."_excuse = 'Non'";
$sql .= " ORDER BY idx_eleve;";
$req = $pdo->prepare($sql);
$req->execute($arguments);


// Il faut vérifier que la fonction ne soit pas déjà déclarée par exemple en cas d'export multiples
if (!function_exists("makeCSV"))
{
    function makeCSV($req){
        $csv_terminated = "\n";
        $csv_separator = ";";
        $csv_enclosed = '';
        $csv_escaped = "\\";

        //recupération nom de colonne
        foreach(range(0, $req->columnCount() - 1) as $column_index){
            $nameCol[] = $req->getColumnMeta($column_index)['name'];
        }

        //création ligne d'en tete
        array_walk($nameCol, 'format', $csv_enclosed);
        $out = implode(';', $nameCol).$csv_terminated;

        //création ligne d'enregistrement
        while ($row = $req->fetch(PDO::FETCH_ASSOC)) {
            array_walk($row, 'format', $csv_enclosed);
            $out .= implode(';', $row).$csv_terminated;
        }
        return $out;
    }

    //fonction de formatage des cellules
    function format(&$item,$key, $escaped){
        $item = $escaped.addcslashes($item,$escaped).$escaped;
    }
}


//creation du csv
$out = makeCSV($req);

header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Content-Length: " . strlen($out));
header("Content-type: text/x-csv;charset=WINDOWS-1252");
header("Content-Disposition: attachment; filename=$filename");
$out = iconv("UTF-8", "WINDOWS-1252", $out);
echo $out;
