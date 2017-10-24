<?php


 
 
 


$target_dir = "/images/utilisateurs/";
//$target_file = $target_dir . basename($_FILES["filetoupload"]["name"]);
$target_file = $target_dir . $_POST['codeBarre'].'.jpg';
$uploadOk = 1;
$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
// Check if image file is a actual image or fake image

if(isset($_POST["submit"])) {
	 $file = $_FILES['filetoupload']['name'];
	
    $check = getimagesize($_FILES["filetoupload"]["tmp_name"]);
    if($check !== false) {
        echo "Le fichier est une image - " . $check["mime"] . ".";
        $uploadOk = 1;
    } else {
        echo "le fichier n'est pas une image.";
        $uploadOk = 0;
    }
}
// Check if file already exists
if (file_exists($target_file)) {
    echo "Le fichier existe déjà.";
    $target_file = "copy-" . $target_file; // Prepending "copy-" to avoid breaking extensions
    // Moving the file to users catalogue
    move_uploaded_file($_FILES['filetoupload']['tmp_name'],$target_file);
	}

//If user don't have his own catalogue 
else {
	
	
	

// Check file size

	if ($_FILES["filetoupload"]["size"] > 500000) {
		echo "Le fichier est volumineux.";
		$uploadOk = 0;
	}
// Allow certain file formats
if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" ) {
    echo "Seuls les formats JPG, JPEG, PNG sont autorisés.";
    $uploadOk = 0;
	
}
// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
    echo "Une erreur est survenue lors du transfert";
header("Location:student_edt.php?stu=".$_POST["codeBarre"]."&success=0");
} else {
    if (move_uploaded_file($_FILES["filetoupload"]["tmp_name"], $target_file)) {
        echo "Le fichier ". basename( $_FILES["filetoupload"]["name"]). " a été importé avec succès.";
		header("Location:student_edt.php?stu=".$_POST["codeBarre"]."&success=1");
    } else {
        echo "Une erreur est survenue lors du transfert.";
		header("Location:student_edt.php?stu=".$_POST["codeBarre"]."&success=0");
		
    }
	
}
}
?>