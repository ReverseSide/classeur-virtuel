<?php
//*********************************************************************************
// Societe: CEPM
// Auteur : Florent Crisinel
// Création   : 28.04.2017
// But    : Génération d'une page pour impression d'un tableau de notes de suivi
//*********************************************************************************
// Modifications:
// Date   :
// Auteur :
// Raison :
//*********************************************************************************
session_start();
setlocale(LC_TIME, 'fr', 'fr_CH', 'fr_CH.utf8');

// Check si l'utilisateur est connecté
if(empty($_SESSION['user_id']))
{
	header("Location:login.php");
}

$timestamps = array();

// Si le timestamp est "CURRENT_YEAR", récupérer tous les timestamps des jours où il y a eur cours dans l'année scolaire
if (isset($_GET['timestamp']) && $_GET['timestamp'] === "CURRENT_YEAR")
{
    // Trouve l'année scolaire en cours
    $startYear = (int)strftime("%Y", time());
    if (strftime("%m", time()) <= 7)
        $startYear--;

    // Le début d'une année scolaire se situe aparement un Lundi entre le 21 et le 27 août...
    // Du coup, on prend une date au 27 août et on calcule l'écart qu'il y a en trop pour tomber sur un Lundi
    $startRange = strtotime("27.08.$startYear");
    $dayOfWeek = (int)strftime("%w", $startRange);
    $startRange -= $dayOfWeek * 86400;

    // Encore de la pure supposition, mais il semble que la meilleure façon de trouver la fin de l'année scolaire
    // est de chercher un vendredi compris entre le 30 juin et le 6 juillet. Les années scolaires n'ont pas toutes
    // la même longueur! (p.ex l'année 2017-2018 fait une semaine de plus que d'habitude).
    $endRange = strtotime("30.06." . ($startYear + 1));
    $dayOfWeek = (int)strftime("%w", $endRange);
    $endRange += ((11 - $dayOfWeek) % 7) * 86400;
    $endRange += 86399; // Ajout d'un jour moins une seconde pour avoir la fin du vendredi

    // On commence par reculer légèrement la date de début, comme ça on tombe sur le dimanche avant la rentrée
    // Cela permet à la fonction get-next-active-day() de trouver le premier lundi si cette classe a cours le lundi
    $_GET['raw'] = true;
    $_GET['timestamp'] = $startRange - 1;

    // Il y a sans doute un façon plus optimisée de faire ça, mais pour l'instant ça suffira:
    // On récupère tous les jours où cette classe à cours en appellant la route get-next-active-day()
    do
    {
        ob_start();
        $nextTimestamp = include("../api/get-next-active-day.php");
        ob_end_clean();

        if ($nextTimestamp > $endRange)
            break;

        $timestamps[] = $nextTimestamp;
        $_GET['timestamp'] = $nextTimestamp;
    }
    while(true);
}
// Récupère les tables à afficher. Il peut y avoir plusieurs tables en passant plusieurs timestamps séparés par des virgules
else if (isset($_GET['timestamp']))
{
	$timestamps = $_GET['timestamp'];
	$timestamps = explode(",", $timestamps);
}

// Récupère les données
$tables = array();
foreach ($timestamps as $timestamp)
{
	// Charge les données au format JSON depuis l'api
	ob_start();
	$_GET['timestamp'] = trim($timestamp);
	$tables[] = include("../api/get-suivi-table.php");
	ob_end_clean();
}

?>
<!DOCTYPE html>
<html lang="fr">
	<head>
        <meta charset="UTF-8" />
        <style>
            body {
                font-family: Arial, sans-serif;
                -webkit-print-color-adjust: exact;
            }
            table {
                width: 100%;
                margin-bottom: 40px;
                border: 1px solid black;
                border-collapse: collapse;
            }
            table thead {
                background-color: #e1e1e1 !important;
            }
            table th,
            table td {
                padding: 10px;
                border: 1px solid black;
            }

            .comment {
                margin-bottom: 8px;
                white-space: pre-wrap;
            }
            .comment:last-child {
                margin-bottom: 0;
            }

            @media screen {
                body {
                    max-width: 1140px;
                    margin: auto;
                }
            }
        </style>
	</head>
	<body>
		<?php foreach ($tables as $i => $table): ?>
            <?php
            $dateForWeekD =  utf8_encode(strftime("%F", $timestamps[$i]));
            $dateForWeek = new DateTime($dateForWeekD);
            $weekNumber = $dateForWeek->format("W");
            ?>
            <h2><?= utf8_encode(strftime("%A %e %B %Y", $timestamps[$i])) ?> - Semaine n° <?php echo $weekNumber; ?></h2>
			<table>
				<thead>
					<tr>
						<th>Matière</th>
						<th>Matière enseignée</th>
						<th>Devoir à faire</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($table as $branch): ?>
						<tr>
							<td><?= $branch['name'] ?></td>
                            <td>
                                <?php foreach ($branch['trackingNotes'] as $note): ?>
                                    <div class="comment"><?= $note['comment'] ?></div>
                                <?php endforeach; ?>
                            </td>
                            <td>
                                <?php foreach ($branch['homeWorks'] as $note): ?>
                                    <div class="comment"><?= $note['comment'] ?></div>
                                <?php endforeach; ?>
                            </td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>

		<?php endforeach; ?>

        <script>
            // Impression de la page via le navigateur
            window.print();
        </script>
	</body>
</html>
