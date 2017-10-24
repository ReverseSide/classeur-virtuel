<?php
session_start();
//*********************************************************************************
// Societe: CEPM
// Auteur : Florent Crisinel
// Date dernière modification   : 09.09.2016
// But    : Route pour obtenir les statistiques de présence d'une classe ou d'un élève
//*********************************************************************************
// Modifications:
// Date   :
// Auteur :
// Raison :
//*********************************************************************************

// Vérifie que l'utilisateur soit connecté. Si non, renvoyer une erreur 403 (Forbidden)
if(empty($_SESSION['user_id']))
{
    http_response_code(403);
    die("Forbidden");
}

// S'il manque des champs dans la requête, retourner une erreur 400 (Bad request)
if (!isset($_POST['type']) || !isset($_POST['id']) && ($_POST['type'] !== 'e' && $_POST['type'] !== 'c'))
{
    http_response_code(400);
    die("Required parameters are: type ('e'/'c'), id");
}

// Inclusion de la classe d'interaction avec la base de données
include_once('../include/mysql.inc.php');
$db = new dbIfc();

// Obtient la liste de tous les élèves à prendre en compte dans le calcul
$tabStudents = array();
$classId = 0;
if (strtolower($_POST['type']) === 'e')
{
    $tabStudents = $db->GetStudent($_POST['id']);
    if (isset($tabStudents['0'])) $classId = $tabStudents['0']['idx_classe'];
}
else if (strtolower($_POST['type']) === 'c')
{
    $classId = $_POST['id'];
    $class = $db->GetMyClass($classId);
    foreach ($class as $student)
    {
        $tabStudents = array_merge($tabStudents, $db->GetStudent($student['id_codebarre']));
    }
}

// Pour savoir à partir de quand commencer à chercher, trouver l'élève le plus ancien (redoublements p.ex)
$startDate = time() + 5;    // Petite marge de sécurité pour la condition de la boucle suivante
$maxBackTime = time() - (60*60*24*365.25*10);
foreach ($tabStudents as &$student)
{
    $studentStart = strtotime($student['ele_debutdeformation']);
	
	// On ne compte pas les absences sur plus de 10 ans (probablement des bugs d'import)
	if ($studentStart < $maxBackTime)
	{
		$studentStart = $maxBackTime;
	}
    $student['ele_debutdeformation'] = $studentStart;
    if ($studentStart < $startDate)
    {
        $startDate = $studentStart;
    }

    // On en proffite pour remplacer les dispenses par des booléens si ce n'est pas déjà le cas
    if (!is_bool($student['ele_dispenseecg']))
        $student['ele_dispenseecg'] = ($student['ele_dispenseecg'] !== "");
    if (!is_bool($student['ele_dispensesport']))
        $student['ele_dispensesport'] = ($student['ele_dispensesport'] !== "");
    if (!is_bool($student['ele_dispensebt']))
        $student['ele_dispensebt'] = ($student['ele_dispensebt'] !== "");
}

// Parcours tous les jours jusqu'à aujourd'hui pour compter le nombre de périodes
$totalPeriods = 0;
while ($startDate < time())
{
    // Cherche si la classe a cours
    $classSchedule = $db->GetSchedule($classId, $startDate);

    // On fait le tour des cours trouvés
    foreach ($classSchedule as $period)
    {
        // Et maintenant le tour des élèves
        foreach ($tabStudents as $student)
        {
            // On ne compte pas les présences si l'élève n'avait pas encore commencé sa formation
            if ($startDate < $student['ele_debutdeformation'])
                continue;

            // [IMO] Les cours pour lesquels un élève est dispensé ne doivent pas compter dans le total
            if ($student['ele_dispenseecg'] && $period['cou_matcode'] === "ECG")
                continue;
            if ($student['ele_dispensesport'] && $period['cou_matcode'] === "SPORT")
                continue;
            if ($student['ele_dispensebt'] && $period['cou_matcode'] !== "ECG" && $period['cou_matcode'] !== "SPORT")
                continue;

            // Si la boucle arrive ici, on peut compter la période
            $totalPeriods++;
        }
    }

    $startDate += 60*60*24; // +1 jour
}

// Maintenant, le nombre d'absences pour tous les élèves sélectionnés (ça c'est facile)
$nbAbsences = 0;
foreach ($tabStudents as $student)
{
    $nbAbsences += count($db->GetStudentMissing($student['id_codebarre']));
}

// Finalement, calcul et retour du ratio de présence
if ($totalPeriods === 0)
    $ratio = 0;
else
    $ratio = round(100 - ($nbAbsences * 100 / $totalPeriods), 2);

print("<span class='presence-ratio'>$ratio%</span>");
