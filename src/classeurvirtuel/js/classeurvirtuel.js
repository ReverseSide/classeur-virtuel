//*********************************************************************************
// Societe: CEPM
// Auteur : Florent Crisinel
// Date dernière modification   : 08.08.2016
// But    : Fichier de fonctions utilitaires JavaScript
//*********************************************************************************
// Modifications:
// Date   :
// Auteur :
// Raison :
//*********************************************************************************


// *******************************************************************
// Nom :	ApiQuery
// But :	Fonction générique pour effectuer un appel POST en AJAX
// Retour:	aucun
// Param.: 	route       (l'url à appeler)
//          params      (un objet JS contenant les paramètres à passer en POST
//          callback    (une fonction à exécuter une fois la réponse reçue,
//                      prenant elle-même en paramètre le contenu de la page cible)
// *******************************************************************
function ApiQuery(route, params, callback)
{
    // Instancie un objet pour la requête AJAX
    var xhttp = new XMLHttpRequest();

    // Encode les paramètres
    var strParams = "";
    for (var paramName in params)
    {
        // Eh oui, le JS est bizzare. Cette ligne sert à éviter les propriétés "cachées"
        if (!params.hasOwnProperty(paramName)) continue;

        if (strParams.length > 1)
            strParams += "&";
        strParams += encodeURIComponent(paramName) + "=" + encodeURIComponent(params[paramName]);
    }

    // Enregistre un "callback" à exécuter lorsque une réponse est retournée
    xhttp.onreadystatechange = function()
    {
        // Si l'opération est un succès, exécuter le callback fourni en paramètre en lui passant la réponse
        if (xhttp.readyState == 4 && xhttp.status >= 200 && xhttp.status < 300)
            callback(xhttp.responseText);
    };

    // Envoie la requête (avec les paramètres en POST pour faire plus propre et robuste)
    xhttp.open("POST", route, true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send(strParams);
}

// *******************************************************************
// Nom :	AjaxReplace
// But :	Remplacer le contenu d'un élément donné avec du contenu chargé dynamiquement
// Retour:	aucun
// Param.: 	element     (l'élément DOM duquel remplacer le contenu)
//          route       (l'url à appeler)
//          params      (un objet JS contenant les paramètres à passer en POST)
//          replace     (si false, remplace l'intérieur de l'élément. Autrement, tout l'élément)
//          callback    (si une fonction est fournie, elle sera appellée une fois le résultat reçu)
// *******************************************************************
function AjaxReplace(element, route, params, replace, callback)
{
    ApiQuery(route, params, function(response)
    {
        if (typeof replace === "boolean" && replace === false)
            element.innerHTML = response;
        else
        {
            element.outerHTML = response;
        }

        if (typeof callback === "function")
            callback();
    });
}

// Cette fonction est un raccourci vers AjaxReplace, mais qui crée un objet à partir des paramètres
function UpdateParticuliar(element, idEleve, period, status, codebarre, idCours, timestamp)
{
    if (typeof timestamp === "undefined")
        timestamp = (new Date()).getTime() / 1000;

    AjaxReplace(element, '../api/update-particuliar.php', {
        id: idEleve,
        period: period,
        status: status,
        codebarre: codebarre,
        cours: idCours,
        timestamp: timestamp
    }, true, function() {
        jQuery('.dont-click').click(function(evt) {
            evt.stopPropagation();
            evt.preventDefault();
        });
    });
}

// Permet d'obtenir le numéro de colonne en partant de la droite d'une cellule dans un tableau
function GetColumnId(element)
{
    var row = jQuery(element).parents('tr');
    var cells = row.children(jQuery(element).prop("tagName"));
    for (var i = 0; i < cells.length; i++)
    {
        if (cells[i] === element)
        {
            return i;
        }
    }
    return 0;
}

// Cette fonction dégrise les cases bloquées dans une colonne de tableau
function ToggleColumn(checkbox, coursId, date, table)
{
    var columnId = GetColumnId(checkbox.parentNode);
    var checked = checkbox.checked;

    ApiQuery("../api/set-prof-presence.php", {
        cours: coursId,
        date: date,
        present: checked
    }, function() {
        var rows = jQuery(table).find('tr');
        rows.each(function(id, row)
        {
            var cells = jQuery(row).children('td');
            if (cells.length > columnId)
            {
                jQuery(cells[columnId]).children('.dont-click').toggleClass('locked');
            }
        });
    });
}

// Fonctions liées à la mise à jour des tableaux de la page student.php
function UpdateStudentNotices(barCode, notices)
{
    ApiQuery('../api/update-student-notices.php', {
        eleve: barCode,
        remarques: JSON.stringify(notices)
    }, function() {
        // Rechargement de la page pour afficher les modifications
        window.location.reload();
    });
}
function UpdateStudentMissings(missings)
{
    ApiQuery('../api/update-student-missings.php', {
        absences: JSON.stringify(missings)
    }, function() { window.location.reload(); });
}
function UpdateStudentLates(lates)
{
    ApiQuery('../api/update-student-lates.php', {
        tardives: JSON.stringify(lates)
    }, function() { window.location.reload(); });
}
function UpdateStudentDoors(doors)
{
    ApiQuery('../api/update-student-doors.php', {
        portes: JSON.stringify(doors)
    }, function() { window.location.reload(); });
}

// Cette fonctions va chercher et remplace la liste des commentaires sur une page
function UpdateComments(matCode, classId)
{
    AjaxReplace(document.getElementById("comments-container"), "../api/get-comments.php", {
        matcode: matCode,
        classe: classId
    }, false);
}

// Enregistre un nouveau commentaire
function SaveComment(matCode, teacherId, classId)
{
    var comment = document.getElementById("comment-text-area").value;
    if (comment.length == 0)
    {
        alert("Vous devez entrer un texte pour votre commentaire");
        return;
    }
    AjaxReplace(document.getElementById("comments-container"), "../api/create-comment.php", {
        commentaire: comment,
        matcode: matCode,
        professeur: teacherId,
        classe: classId
    }, false);
    document.getElementById("comment-text-area").value = "";
}

// Écrase un commentaire existant
function EditComment(commentId, newComment, classId, matCode)
{
    ApiQuery('../api/update-comment.php', {
        id: commentId,
        commentaire: newComment
    }, function() {
        jQuery("#edit-modal-textarea").val("");
        AjaxReplace(document.getElementById('comments-container'), '../api/get-comments.php', {
            classe: classId,
            matcode: matCode
        }, false, function() {
            jQuery("#edit-modal").css('visibility', 'hidden');
        });
    });
}

// Ajoute une coche à l'élève donné
function AddCoche(element, studentId, typeId)
{
    // Montre le champ de commentaire avant, car après AjaxReplace, la variable element ne sera plus liée au DOM
    jQuery(element).parents(".widget-main").find(".comment-container").css("visibility", "visible");

    // Effectue le remplacement
    AjaxReplace(element, '../api/add-coche.php', {
        eleve: studentId,
        type: typeId
    }, true, RefreshCochesRow);
}

// Supprime une coche à l'élève donné
function RemoveCoche(element, studentId, typeId)
{
    // Cache le champ de commentaire avant, car après AjaxReplace, la variable element ne sera plus liée au DOM
    jQuery(element).parents(".widget-main").find(".comment-container").css("visibility", "hidden");

    // Trouve l'élément td parent le plus proche
    var tagName = element.tagName.toLowerCase();
    while (tagName !== "td")
    {
        if (tagName === "body") return;
        element = element.parentNode;
        tagName = element.tagName.toLowerCase();
    }
    AjaxReplace(element, '../api/delete-coche.php', {
        eleve: studentId,
        type: typeId
    }, true, RefreshCochesRow);
}

// Recalcule le total des coches pour chaque ligne de coches
function RefreshCochesRow()
{
    // Trouve l'élément tableau
    var table = jQuery(".table-coches");

    // Effectue une addition de toutes les valeurs numériques dans des <div>
    function parseElement(element)
    {
        var sum = 0;
        for (var node in element.childNodes)
        {
            if (!element.childNodes.hasOwnProperty(node)) continue;
            if (typeof element.childNodes[node].tagName === "undefined")
            {
                var tryInt = parseInt(element.childNodes[node].nodeValue);
                if (!isNaN(tryInt))
                    sum += tryInt;
                continue;
            }
            if (element.childNodes[node].className.indexOf("total") >= 0) continue;

            if (element.childNodes[node].tagName.toLowerCase() === "div" || element.childNodes[node].tagName.toLowerCase() === "td")
            {
                sum += parseElement(element.childNodes[node]);
            }
        }
        return sum;
    }

    var rows = table.find("tr");
    for (var i = 0; i < rows.length; i++)
    {
        var totalCell = rows[i].querySelector("td.total");
        if (totalCell !== null)
            totalCell.innerHTML = parseElement(rows[i]);
    }
}

// Met à jour le commentaire de la dernière coche créée pour l'élève donné
function UpdateLastCocheComment(element, studentId)
{
    var container = jQuery(element).parents(".input-group");
    var comment = container.find("input");
    ApiQuery('../api/update-last-coche-comment.php', {
        eleve: studentId,
        commentaire: comment.val()
    }, function() {
        comment.val("");
        container.css("visibility", "hidden");
    });
}

// Lance le calcul du ratio de présence d'une classe ou d'un élève et affiche le résultat
function ComputePresenceRatio(type, id, targetElement)
{
    if (typeof targetElement === "undefined")
        return;

    targetElement.disabled = true;
    targetElement.innerHTML = "<i class='fa fa-cog' style='animation:spin 1.3s infinite;'></i> Calcul en cours...";
    AjaxReplace(targetElement, '../api/get-presence-ratio.php', {
        type: type,
        id: id
    }, true);
}

// Décode une url et retourne ses paramètres sous forme de tableau
function DecodeUrlParams(url)
{
    var params = url.split("?")[1].split("&");
    var tabParams = {};

    for(var i = 0; i < params.length; i++){
        var param = params[i].split("=");
        tabParams[param[0]] = param[1];
    }

    return {
        url: url.substr(0, url.indexOf("?")),
        params: tabParams
    };
}

function EncodeUrlParams(decoded)
{
    var url = decoded.url;
    var params = decoded.params;
    var strParams = "?";
    for (var paramName in params)
    {
        if (!params.hasOwnProperty(paramName)) continue;
        if (strParams.length > 1)
            strParams += "&";
        strParams += encodeURIComponent(paramName) + "=" + encodeURIComponent(params[paramName]);
    }
    if (strParams.length > 1)
        url += strParams;
    return url;
}
