<?php
session_start();
//*********************************************************************************
// Societe: CEPM
// Auteur : Florent Crisinel
// Date dernière modification   : 25.04.2017
// But    : Page de gestion des notes de suivi
//*********************************************************************************
// Modifications:
// Date   :
// Auteur :
// Raison :
//*********************************************************************************


//inclusion de la classe d'interaction avec la base de données
include_once('../include/mysql.inc.php');

//Check si connecté
if(!empty($_SESSION['user_id']) )
{
	if(!empty($_GET['idclasse']))
	{
		$_SESSION['class']=$_GET['idclasse'];
	}
}
else
{
	header("Location:login.php");
}

// Récupération de la date du jour
$dateToDisplay = time();
if (isset($_GET['timestamp']))
	$dateToDisplay = $_GET['timestamp'];

//Création du tableau contenant l'horaire de la journée
$bd = new dbIfc();
$classInfo = $bd->GetClassInfo($_SESSION['class']);
$dbDate = strftime("%Y-%m-%d", $dateToDisplay);

unset($bd);
?>
<!DOCTYPE html>
<html lang="en">

<head>

	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="">
	<meta name="author" content="">

	<title>CEPM Scan System V2.0</title>

	<!-- Bootstrap Core CSS -->
	<link rel="stylesheet" href="assets/css/ace.min.css" />
	<link href="../bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

	<!-- MetisMenu CSS -->
	<link href="../bower_components/metisMenu/dist/metisMenu.min.css" rel="stylesheet">

	<!-- Custom CSS -->
	<link href="../dist/css/sb-admin-2.css" rel="stylesheet">

	<!-- Morris Charts CSS -->
	<link href="../bower_components/morrisjs/morris.css" rel="stylesheet">

	<!-- Custom Fonts -->
	<link href="../bower_components/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

	<link rel="stylesheet" href="//fonts.googleapis.com/css?family=Open+Sans:400,300" />

	<!-- DatePicker -->
	<link href="../bower_components/bootstrap-datepicker/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css">

	<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
	<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
	<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
	<![endif]-->

</head>

<body>

<div id="wrapper">

	<?php include("../include/menu.php"); ?>

	<div id="page-wrapper">

		<div class="row">
			<div class="col-lg-12">
				<h1 class="page-header">
                    <i class="fa fa-angle-left back-button" onclick="window.history.back()"></i>
					<span>Suivi de l'enseignement</span>
					<small style="cursor:pointer;" data-toggle="tooltip" data-placement="bottom" data-html='true'
					       title="Niveau: <?= $classInfo['cla_niveau'] ?><br>Année: <?= $classInfo['cla_type'] ?><br>
                               Jour(s) de cours: <?= $classInfo['cla_jourdecours'] ?><br>Type: <?= $classInfo['typ_nom'] ?><br>
                               Département: <?= $classInfo['dep_nom'] ?>">
						<?= $classInfo['cla_nom'] ?>
						<i class="fa fa-info-circle"></i>
					</small>
				</h1>
			</div>
		</div>

        <div class="row">
            <div class="col-lg-6" id="left-table">

                <!-- Le sélecteur de date, chargé par VueJs -->
                <date-browser v-bind:current-date="currentDate"></date-browser>

                <!-- Ce tableau va être initialisé par VueJs -->
                <suivi-table v-bind:current-date="currentDate"></suivi-table>

                <!-- Un lien normal vers la page d'impression -->
                <a class="btn btn-primary pull-right" target="_blank" v-bind:href="'print_suivi_table.php?idclasse=<?= $classInfo['id_classe'] ?>&timestamp=' + currentDate">Imprimer</a>

            </div>
            <div class="col-lg-6" id="right-table">

                <!-- Le sélecteur de date, chargé par VueJs -->
                <date-browser v-bind:current-date="currentDate"></date-browser>

                <!-- Ce tableau va être initialisé par VueJs -->
                <suivi-table v-bind:current-date="currentDate"></suivi-table>

                <!-- Un lien normal vers la page d'impression -->
                <a class="btn btn-primary pull-right" target="_blank" v-bind:href="'print_suivi_table.php?idclasse=<?= $classInfo['id_classe'] ?>&timestamp=' + currentDate">Imprimer</a>

            </div>
        </div>

        <div class="row">
            <div class="col-xs-12">
                <div class="pull-right" id="export-buttons">
                    <a class="btn btn-primary" target="_blank" v-bind:href="'print_suivi_table.php?idclasse='+ getClassId() +'&timestamp=CURRENT_YEAR'">Imprimer Tout</a>
                    <a class="btn btn-primary" target="_blank" v-bind:href="'extraction_suivi_table.php?idclasse='+ getClassId() + '&timestamp=CURRENT_YEAR'">Exporter</a>
                </div>
            </div>
        </div>

	</div>
	<!-- /#page-wrapper -->

</div>
<!-- /#wrapper -->

<!-- Modal d'édition chargée par VueJs -->
<div id="edit-modal" v-if="modalData" class="modal open" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" v-on:click="closeModal()"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Note de suivi</h4>
            </div>
            <div class="modal-body">
                <form>
                    <input type="hidden" v-model="modalData.id" readonly />
                    <input type="hidden" v-model="modalData.type" readonly />

                    <div class="form-group">
                        <label for="suiviDate">Date</label>
                        <input type="text" id="suiviDate" class="form-control" v-bind:value="modalData.date | toDate" readonly />
                    </div>

                    <div class="form-group">
                        <label for="suiviMatCode">MatCode</label>
                        <input type="text" id="suiviMatCode" class="form-control" v-model="modalData.matCode" readonly />
                    </div>

                    <div class="form-group">
                        <label for="suiviComment">Commentaire</label>
                        <textarea id="suiviComment" class="form-control" v-model="modalData.comment" rows="4"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" v-on:click="save()">Enregistrer</button>
                <button type="button" class="btn btn-default" v-on:click="closeModal()">Fermer</button>
            </div>
        </div>
    </div>
</div>

<!-- jQuery -->
<script src="../bower_components/jquery/dist/jquery.min.js"></script>

<!-- Bootstrap Core JavaScript -->
<script src="../bower_components/bootstrap/dist/js/bootstrap.min.js"></script>

<!-- Metis Menu Plugin JavaScript -->
<script src="../bower_components/metisMenu/dist/metisMenu.min.js"></script>

<!-- Morris Charts JavaScript -->
<script src="../bower_components/raphael/raphael-min.js"></script>
<script src="../bower_components/morrisjs/morris.min.js"></script>
<script src="../js/morris-data.js"></script>

<!-- Custom Theme JavaScript -->
<script src="../dist/js/sb-admin-2.js"></script>

<!-- DatePicker + locale -->
<script src="../bower_components/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
<script src="../bower_components/bootstrap-datepicker/js/bootstrap-datepicker.fr-CH.min.js"></script>
<script src="../bower_components/moment/min/moment.min.js"></script>
<script src="../bower_components/moment/locale/fr-ch.js"></script>

<!-- Core Javascript -->
<script src="../js/classeurvirtuel.js"></script>




<!-- Templates VueJs pour afficher les tableaux et commentaires -->
<script type="text/x-template" id="date-browser-template">
    <div class="input-group date-browser">
        <span class="input-group-addon" id="basic-addon1">S{{ currentDate | weekNumber }}</span>
        <input type="text" class="form-control" title="Date actuelle" v-bind:value="currentDate | toDate" disabled readonly />
        <span class="input-group-btn">
            <?php
            $queryString = "?idclasse=". $_SESSION['class'] ."&timestamp=". $dateToDisplay;
            ?>
            <button class="btn btn-default" v-on:click="previousDay">&lt;</button>
            <button class="btn btn-default day-changer">Changer de jour</button>
            <button class="btn btn-default" v-on:click="nextDay">&gt;</button>
        </span>
    </div>
</script>
<script type="text/x-template" id="suivi-table-template">
    <table class="table table-bordered comments-table">
        <thead>
        <tr class="remarque-row" v-for="course in courses" v-if="course.name == 'Remarques'">
            <td>Remarques</td>
            <td colspan="2" class="comments-container">
                <suivi-comment
                        v-for="comment in course.trackingNotes"
                        :key="comment.id"
                        v-bind:note-data="comment">
                </suivi-comment>
                <div class="add-new" v-on:click="openCreateModal(course.class, course.date, course.matCode, 'trackingNotes')"><i class="fa fa-plus"></i></div>
            </td>
        </tr>
        <tr>
            <th>Matière</th>
            <th>Matière enseignée</th>
            <th>Devoir à faire</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="course in courses" v-if="course.name != 'Remarques'">
            <td>{{ course.name }}</td>
            <td class="comments-container">
                <suivi-comment
                        v-for="comment in course.trackingNotes"
                        :key="comment.id"
                        v-bind:note-data="comment">
                </suivi-comment>
                <div class="add-new" v-on:click="openCreateModal(course.class, course.date, course.matCode, 'trackingNotes')"><i class="fa fa-plus"></i></div>
            </td>
            <td class="comments-container">
                <suivi-comment
                        v-for="comment in course.homeWorks"
                        :key="comment.id"
                        v-bind:note-data="comment">
                </suivi-comment>
                <div class="add-new" v-on:click="openCreateModal(course.class, course.date, course.matCode, 'homeWorks')"><i class="fa fa-plus"></i></div>
            </td>
        </tr>
        </tbody>
    </table>
</script>
<script type="text/x-template" id="suivi-comment-template">
    <div class="comment-wrapper">
        <div class="comment" v-bind:class="{'own': noteData.canEdit}">
            <span>{{ noteData.comment }}</span>

            <!-- Boutons pour éditer un commentaire -->
            <span class="controls" v-if="noteData.canEdit">
                <i class="fa fa-pencil" v-on:click="editNote()"></i>
                <i class="fa fa-times" v-on:click="deleteNote()"></i>
            </span>

            <!-- Nom du prof en légende si c'est quelqu'un d'autre -->
            <span class="teacher-name" v-if="!noteData.canEdit">{{ noteData.profName }}</span>
        </div>
    </div>
</script>

<!-- Vue.js et script de gestion de la page -->
<script src="https://unpkg.com/vue"></script>
<script>
    // Permet de formatter une date ou un timestamp unix
    Vue.filter('toDate', function(value) {
        var date = 0;
        if (isNaN(value))
            date = window.moment(value);
        else
            date = window.moment.unix(value);

        return date.format("dddd D MMMM YYYY");
    });

    // Permet d'extraire le n° de semaine d'une date ou d'un timestamp
    Vue.filter('weekNumber', function(value) {
        var date = 0;
        if (isNaN(value))
            date = window.moment(value);
        else
            date = window.moment.unix(value);

        return date.format("W");
    });

    var Bus = new Vue({});

    Vue.component('suivi-table', {
        props: ['currentDate'],
        template: '#suivi-table-template',
        methods: {
            refreshData: function() {
                var self = this;
                var url = '../api/get-suivi-table.php?idclasse='+ this.$parent.classId +'&timestamp='+ this.currentDate;
                ApiQuery(url, {}, function(response) {
                    self.courses = JSON.parse(response);
                });
            },
            openCreateModal: function(classId, date, matCode, type) {
                window.editModal.openCreateModal(classId, date, matCode, type);
            }
        },
        // Crée initialement un tableau vide qui sera remplacé par refreshData()
        data: function() {
            return {
                courses: []
            }
        },
        // Lorsque le composant est chargé, aller chercher les données et les afficher
        created: function() {
            this.refreshData();
            Bus.$on('needRefresh', this.refreshData);
        },
        // Lorsque la date change, rafraichir les données
        watch: {
            currentDate: "refreshData"
        }
    });

    Vue.component('suivi-comment', {
        props: ['noteData'],
        template: '#suivi-comment-template',
        methods: {
            editNote: function() {
                window.editModal.openEditModal(
                    this.noteData.id,
                    this.noteData.class,
                    this.noteData.date,
                    this.noteData.matCode,
                    this.noteData.type,
                    this.noteData.comment
                );
            },
            deleteNote: function() {
                if (!confirm("Voulez-vous vraiment supprimer cette note?")) {
                    return;
                }

                ApiQuery('../api/delete-suivi-note.php', {
                    'id': this.noteData.id
                }, function() {
                    Bus.$emit("needRefresh");
                });
            }
        }
    });

    Vue.component('date-browser', {
        props: ['currentDate'],
        template: '#date-browser-template',
        methods: {
            nextDay: function() {
                var self = this;
                var url = '../api/get-next-active-day.php?idclasse='+ this.$parent.classId +'&timestamp='+ this.currentDate +'&raw=true';
                ApiQuery(url, {}, function(response) {
                    self.$parent.currentDate = response;
                });
            },
            previousDay: function() {
                var self = this;
                var url = '../api/get-prev-active-day.php?idclasse='+ this.$parent.classId +'&timestamp='+ this.currentDate +'&raw=true';
                ApiQuery(url, {}, function(response) {
                    self.$parent.currentDate = response;
                });
            }
        },
        // Lorsque l'élément est attaché au DOM, initialise le datepicker
        mounted: function () {
            var self = this;
            var datePicker = jQuery(this.$el).find(".day-changer");
            datePicker.datepicker({
                format: "yyyy-mm-dd",
                language: "fr",
                calendarWeeks: true,
                todayBtn: "linked"
            }).on("changeDate", function(evt) {
                var timestamp = new Date(evt.format()).getTime() / 1000;
                self.$parent.currentDate = timestamp;
            });
            datePicker.datepicker("update", "<?= $dbDate ?>");
        }
    });

    var leftTable = new Vue({
        el: '#left-table',
        data: {
            currentDate: '<?= $dateToDisplay ?>',
            classId: <?= $classInfo['id_classe'] ?>,
            modalData: null
        }
    });

    var rightTable = new Vue({
        el: '#right-table',
        data: {
            currentDate: '<?= $dateToDisplay ?>',
            classId: <?= $classInfo['id_classe'] ?>,
            modalData: null
        },
        // Défile au prochain jour lorsque l'élément est chargé
        created: function() {
            var self = this;
            var url = '../api/get-next-active-day.php?idclasse='+ this.classId +'&timestamp='+ this.currentDate +'&raw=true';
            ApiQuery(url, {}, function(response) {
                self.currentDate = response;
            });
        }
    });

    var exportButtons = new Vue({
        el: '#export-buttons',
        methods: {
            getLeftDate: function() {
                return leftTable.currentDate;
            },
            getClassId: function() {
                return leftTable.classId;
            },
            getRightDate: function() {
                return rightTable.currentDate;
            }
        }
    });

    var editModal = new Vue({
        el: '#edit-modal',
        data: {
            modalData: null
        },
        methods: {
            closeModal: function() {
                this.modalData = null;
            },
            openCreateModal: function(classId, date, matCode, type) {
                this.openEditModal(null, classId, date, matCode, type, "");
            },
            openEditModal: function(id, classId, date, matCode, type, comment) {
                this.modalData = {
                    id: id,
                    date: date,
                    matCode: matCode,
                    type: type,
                    comment: comment,
                    classId: classId
                }
            },
            save: function() {
                var self = this;
                var url = "../api/create-suivi-note.php";
                var postData = {
                    id: this.modalData.id,
                    date: this.modalData.date,
                    matcode: this.modalData.matCode,
                    type: this.modalData.type,
                    commentaire: this.modalData.comment,
                    classe: this.modalData.classId
                };

                // Choix entre le mode création ou edition
                if (this.modalData.id > 0)
                    url = "../api/update-suivi-note.php";

                ApiQuery(url, postData, function() {
                    Bus.$emit("needRefresh");
                    self.closeModal();
                })
            }
        }
    });
</script>

<!-- CSS de la page -->
<style>
    .modal.open {
        display: block;
        background-color: rgba(0, 0, 0, 0.5);
    }

    .back-button {
        color: #A0A0A0;
        cursor: pointer;
    }

    .date-browser {
        max-width: 460px;
        margin-bottom: 20px;
    }
    .date-browser input {
        padding-left: 10px;
        padding-right: 10px;
    }

    table.comments-table {
        table-layout: fixed;
    }
    table td.comments-container {
        position: relative;
        padding-right: 18px !important;
    }
    table td.comments-container .add-new {
        position: absolute;
        right: 0;
        bottom: 0;
        padding: 1px 5px;
        cursor: pointer;
        color: #bcbcbc;
        opacity: 1;
    }
    table td.comments-container .add-new:hover {
        color: #1E1E1E;
    }
    table thead > tr.remarque-row {
        background: none;
        background-color: #FFFFCD;
    }
    table thead > tr.remarque-row .comments-container {
        color: #333;
    }

    .comment {
        display: inline-block;
        background-color: #EFEFEF;
        padding: 1px 5px;
        margin: 2px 2px 2px 0;
        border-radius: 3px;
        border: 1px solid #DEDEDE;
        white-space: pre-wrap;
		word-break: break-all;
    }
    .comment.own {
        background-color: #E5E7EA;
        border-color: #D4D6D9;
    }
    .comment .controls {
        float: right;
        margin-left: 8px;
        color: #858585;
    }
    .comment .controls .fa {
        cursor: pointer;
        transition: color 0.3s;
    }
    .comment .controls .fa-pencil:hover { color: #DBA834; }
    .comment .controls .fa-times:hover { color: #D33B23; }
    .comment .teacher-name {
        font-size: 10px;
        color: #626262;
        white-space: nowrap;
    }

    #left-table,
    #right-table {
        margin-bottom: 60px;
    }
</style>

</body>

</html>