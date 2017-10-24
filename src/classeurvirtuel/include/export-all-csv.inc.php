<?php

// Check si l'utilisateur est connecté
if(empty($_SESSION['user_id']))
{
    header("Location:login.php");
    die("Forbidden");
}

isset($startDate) or die('Must not be called directly');
isset($endDate) or die("Must not be called directly");

// Prépare le zip
$zipName = "../tmp/export_". time() .".zip";
$zip = new ZipArchive();
$zip->open($zipName, ZipArchive::CREATE);


// Définition des types d'exports à réaliser
$types = [1, 2, 3, 4, 5, 6, 7];

// Lance tous les exports
foreach ($types as $exportType)
{
    // Intercepte la sortie
    $type = $exportType;
    ob_start();
    include("csv.inc.php");
    $result = ob_get_clean();

    // Réinitialise les headers
    header_remove();

    // Ajoute le fichier dans le zip
    $zip->addFromString($filename, $result);
}

// Sauve le zip et le supprime
$zip->close();
$zipContent = file_get_contents($zipName);
unlink($zipName);

// Retour de résultat
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Content-Length: " . strlen($zipContent));
header("Content-type: application/zip");
header("Content-Disposition: attachment; filename=export_full_".$startDate."_".$endDate.".zip");
die($zipContent);
