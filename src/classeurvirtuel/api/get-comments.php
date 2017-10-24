<?php
if(!isset($_SESSION)){session_start();}
//*********************************************************************************
// Societe: CEPM
// Auteur : Florent Crisinel
// Date dernière modification   : 13.08.2016
// But    : Route d'affichage de la liste des commentaires d'un cours et d'une classe
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
if(!empty($_POST['classe']))
{
    $_SESSION['class'] = $_POST['classe'];
}

// S'il manque des champs dans la requête, retourner une erreur 400 (Bad request)
if (empty($_POST['matcode']) || !isset($_POST['classe']))
{
    http_response_code(400);
    die("Required parameters are: matcode, classe");
}

// Inclusion de la classe d'interaction avec la base de données
include_once('../include/mysql.inc.php');
$bd = new dbIfc();

// Récupération de la liste des commentaires
$comments = $bd->GetComments($_POST['classe'], $_POST['matcode']);
setlocale(LC_TIME, 'fr', 'fr_FR', 'fr_FR.UTF8');

// Affichage des commentaires
foreach ($comments as $comment):
    $isMine = $comment['id_professeur'] == $_SESSION['user_id'];
    $createdTime = strtotime($comment['com_date']);
    ?>

<div class="comment<?php if ($isMine) { print(" mine"); } ?>">
    <div class="heading">
        <?php if ($isMine): ?>
        <span class="author">
            <span><i class="fa fa-pencil" onclick="OpenEditModal(<?= $comment['id_commentaire'] ?>, jQuery(this).parents('.comment').find('.content'))"></i></span>
            <span><i class="fa fa-times" onclick="EditComment(<?= $comment['id_commentaire'] ?>, '', <?= $_POST['classe'] ?>, '<?= $_POST['matcode'] ?>')"></i></span>
            <span>Moi</span>
        </span>
        <?php else: ?>
        <span class="author"><?= $comment['pro_prenom'] ?> <?= $comment['pro_nom'] ?></span>
        <?php endif; ?>
        <span class="date"><?= utf8_encode(strftime("%A %d %B %Y", $createdTime)) ?> à <?= strftime("%H:%M", $createdTime) ?></span>
    </div>
    <div class="content"><?= htmlentities($comment['com_contenu']) ?></div>
</div>

<?php endforeach;

if (count($comments) === 0)
{
    echo("<span class='no-result'>Aucun commentaire</span>");
}
