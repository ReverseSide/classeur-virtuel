<?php
// PDO connect *********
session_start();
function connect() {
    return new PDO('mysql:host=nlhj.myd.infomaniak.com;dbname=nlhj_cepm', 'nlhj_databuser', 'classeur123', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
}








$pdo = connect();
$keyword = '%'.$_POST['keyword'].'%';

$sql = "select t_eleve.id_eleve ,t_eleve.ele_nom ,t_eleve.ele_prenom ,t_eleve.id_codebarre from t_eleve  where t_eleve.ele_nom like  (:keyword) or t_eleve.ele_prenom like  (:keyword) ORDER BY t_eleve.ele_nom ASC LIMIT 0, 15 ";
$query = $pdo->prepare($sql);
$query->bindParam(':keyword', $keyword, PDO::PARAM_STR);
$query->execute();
$list = $query->fetchAll();
$requete3=$pdo->prepare('SELECT distinct t_sdh.cou_matcode FROM t_sdh ORDER BY cou_matcode ASC');
$requete3->execute();
$Cours=$requete3->fetchAll();
foreach ($list as $rs) {

    $Eleve_name = str_replace($_POST['keyword'], '<b>'.$_POST['keyword'].'</b>', $rs['ele_nom'] .' ' .$rs['ele_prenom']);
    // add new option
 //  $id=$entry['ele_nom'];

    $_SESSION['id_codebarre'] =  $rs['id_codebarre'];


    echo "<div class='panel box box-default'> <div class='box-header with-border'>
		<h4 class='box-title'>
		  <a href='student_dtl.php?stu=". $rs['id_codebarre']. "'   aria-expanded='true' style='color:#3c8dbc'>".$Eleve_name ."</a>		
		<div class='pull-right box-tools'>


		    <a class='btn-sm btn btn-default' href='student_dtl.php?stu=". $rs['id_codebarre']. "' title='Voir'><i class='fa fa-eye'></i></a>		    <a class='btn-sm btn btn-default' href='student_edt.php?stu=". $rs['id_codebarre']. "' title='Editer'><i class='fa fa-pencil-square-o'></i></a>		                    </div>
	      </div>
</h4>
	      <div class='panel-collapse collapse' id='collapse0' aria-expanded='true' style=''>
		<div class='box-body'>
				  <ol style=' font-size: 15px; line-height: 35px;'>
		    			<li>MCA-Batch-01			    <a class='btn-xs btn btn-default' href='/index.php?r=course%2Fbatches%2Fview&amp;id=1' title='Visualiser les détails du lot'><i class='fa fa-eye'></i></a>			    <a class='btn-xs btn btn-default' href='/EduSec/index.php?r=course%2Fbatches%2Fupdate&amp;id=1' title='Editer détails des collections'><i class='fa fa-pencil-square-o'></i></a>			    <a class='btn-xs btn btn-default' data-confirm='Supprimer cet outil ?' data-method='post'><i class='fa fa-trash-o'></i></a>				<div class='pull-right hidden-xs'>
					<span class='label label-default'>
						<i class='fa fa-users'></i> Elèves&nbsp;
						<span class='badge' style='background:#fff;color: #777;'>
						5						</span>
					</span> &nbsp;
											<span class='label label-success'>opérationnel</span>
									</div>
							<ol>
									<li>
					    MCA-Section-01					    <a class='btn-xs btn btn-default'  title='Visualiser les détails du section'><i class='fa fa-eye'></i></a>			    		    <a class='btn-xs btn btn-default'  title='Editer les détails de la section'><i class='fa fa-pencil-square-o'></i></a>			    		    <a class='btn-xs btn btn-default'  title='Supprimer' data-confirm='Supprimer cet outil ?' data-method='post'><i class='fa fa-trash-o'></i></a>					    <div class='pull-right hidden-xs'>
						<span class='label label-default'>
							<i class='fa fa-users'></i> Elèves&nbsp;
							<span class='badge' style='background:#fff;color: #777;'>
							5							</span>
						</span> &nbsp;
												<span class='label label-success'>opérationnel</span>
											    </div>
					</li>
								</ol>

			</li>
		    		  </ol>
		</div>
	      </div><!-- /.panel box -->
	    </div>
	
	
	
	
	
	
	
";

}

?>


<script>

    function RefreshDatePickers()
    {
        var datePickers = jQuery(".date-picker");
        datePickers.datepicker({
            format: "yyyy-mm-dd",
            language: "fr",
            calendarWeeks: true,
            todayHighlight: true
        });
    }
    document.addEventListener("DOMContentLoaded", function() {
        RefreshDatePickers();
    });

</script>
<!-- DatePicker + locale -->
<script src="../bower_components/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
<script src="../bower_components/bootstrap-datepicker/js/bootstrap-datepicker.fr-CH.min.js"></script>
<link href="../bower_components/bootstrap-datepicker/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css">
