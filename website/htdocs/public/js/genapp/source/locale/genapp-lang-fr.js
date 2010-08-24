if(Genapp.form.DateRangeField){
    Ext.apply(Genapp.form.DateRangeField.prototype, {
        minText : "Les dates contenues dans ce champ doivent être égales ou postérieures au {0}",
        maxText : "Les dates contenues dans ce champ doivent être égales ou antérieures au {0}",
        reverseText : "La date de fin doit être postérieure à la date de début",
        notEqualText : "Les dates de début et de fin ne peuvent être égales"
    });
}
if(Genapp.form.GeometryField){
    Ext.apply(Genapp.form.GeometryField.prototype, {
        fieldLabel: "Localisation",
        mapWindowTitle: "Dessinez la zone recherchée sur la carte :",
        mapWindowValidateButtonText: "Valider",
        mapWindowValidateAndSearchButtonText: "Valider et rechercher",
        mapWindowCancelButtonText: "Annuler"
    });
}
if(Genapp.form.NumberRangeField){
    Ext.apply(Genapp.form.NumberRangeField.prototype, {
        minText : "La valeur minimum pour ce champ est {0}",
        maxText : "La valeur maximum pour ce champ est {0}",
        reverseText : "Le maximum doit être supérieur au minimum",
        formatText : "Le format correct est '{0}'",
        nanText : "'{0}' n'est pas un nombre valide"
    });
}
if(Genapp.form.TwinNumberField){
    Ext.apply(Genapp.form.TwinNumberField.prototype, {
        minText : "La valeur minimum pour ce champ est {0}",
        maxText : "La valeur maximum pour ce champ est {0}",
        nanText : "'{0}' n'est pas un nombre valide"
    });
}
if(Genapp.ConsultationPanel){
    Ext.apply(Genapp.ConsultationPanel.prototype, {
        localeCls:'fr',
        userManualLinkText : 'Manuel utilisateur',
        datasetComboBoxEmptyText :"Sélectionnez un type de données",
        datasetPanelTitle :"Type de données",
        formsPanelTitle :"Formulaires :",
        csvExportButtonText: "Export CSV",
        aggregationButtonText: "Agrégation",
        interpolationButtonText: "Interpolation",
        gridViewEmptyText : "Pas de résultat...",
        gridPanelTitle :"Résultats",
        gridPanelTabTip:"Les résultats de la requête",
        centerPanelTitle:"Onglet des résultats",
        queryPanelTitle: "Requêteur",
        queryPanelPinToolQtip: "Annuler la fermeture automatique de l'onglet",
        queryPanelUnpinToolQtip:"Activer la fermeture automatique de l'onglet",
        queryPanelCancelButtonText: "Annuler",
        queryPanelResetButtonText:"Réinitialiser",
        queryPanelSearchButtonText:"Rechercher",
        queryPanelCancelButtonTooltip:"Annuler la requête",
        queryPanelResetButtonTooltip:"Réinitialiser la requête",
        queryPanelSearchButtonTooltip:"Lancer la requête",
        detailsPanelCtTitle:"Détails",
        detailsPanelCtPinToolQtip: "Annuler la fermeture automatique de l'onglet",
        detailsPanelCtUnpinToolQtip:"Activer la fermeture automatique de l'onglet",
        mapMaskMsg:"Chargement...",
        alertErrorTitle:"Erreur :",
        alertRequestFailedMsg:"Désolé, la requête a échoué...",
        csvExportAlertTitle:"Exportation d'un fichier CSV avec Internet Explorer",
        csvExportAlertMsg:"<div><H2>Pour votre confort sur Internet Explorer vous pouvez: </H2> \
            <H3>Désactiver la confirmation pour les téléchargements de fichiers.</H3> \
            <ul> \
            <li>Dans IE, dérouler le menu 'Outils'</li> \
            <li>Cliquer sur 'Options Internet'</li> \
            <li>Cliquer sur l'onglet 'Sécurité'</li> \
            <li>Cliquer sur le bouton 'Personnaliser le niveau'</li> \
            <li>Descendre jusqu'à la partie 'Téléchargements'</li> \
            <li>Activé la demande de confirmation pour les téléchargements de fichiers</li> \
            </ul> \
            <H3>Désactiver l'ouverture du fichier dans la fenêtre courante.</H3> \
            <ul> \
            <li>Ouvrir le poste de travail</li> \
            <li>Dérouler le menu 'Outils'</li> \
            <li>Cliquer sur 'Options des dossiers...'</li> \
            <li>Cliquer sur l'onglet 'Types de fichiers'</li> \
            <li>Sélectionner l'extension XLS</li> \
            <li>Cliquer sur le bouton 'Avancé'</li> \
            <li>Décocher 'Parcourir dans une même fenêtre'</li> \
            </ul></div>"
    });
}
if(Genapp.DateRangePicker){
    Ext.apply(Genapp.DateRangePicker.prototype, {
        tbarStartDateButtonText:"Date de début ...",
        tbarRangeDateButtonText:"Intervalle",
        tbarEndDateButtonText:"... Date de fin",
        fbarOkButtonText:"ok"
    });
}
if(Genapp.DetailsPanel){
    Ext.apply(Genapp.DetailsPanel.prototype, {
        loadingMsg:"Cgmt..."
    });
}
if(Genapp.FieldForm){
    Ext.apply(Genapp.FieldForm.prototype, {
        criteriaPanelTbarLabel:"Critères",
        criteriaPanelTbarComboEmptyText:"Sélectionner...",
        criteriaPanelTbarComboLoadingText:"Recherche en cours...",
        columnsPanelTbarLabel:"Colonnes",
        columnsPanelTbarComboEmptyText:"Sélectionner...",
        columnsPanelTbarComboLoadingText:"Recherche en cours...",
        columnsPanelTbarAddAllButtonTooltip:"Ajouter toutes les colonnes",
        columnsPanelTbarRemoveAllButtonTooltip:"Supprimer toutes les colonnes"
    });
}
if(Genapp.MapPanel){
    Ext.apply(Genapp.MapPanel.prototype, {
        title :"Carte",
        tabTip: "La carte avec les localisations des résultats de la requête",
        layerPanelTitle:"Couches",
        layerPanelTabTip:"L'arbre de sélection des couches",
        legendPanelTitle:"Légendes",
        legendPanelTabTip:"Les legendes des couches",
        panZoomBarControlTitle:"Zoom",
        navigationControlTitle:"Déplacer la carte",
        selectFeatureControlTitle:"Sélectionner la géométrie",
        invalidWKTMsg:"La géométrie ne peut être affichée",
        zoomToFeaturesControlTitle:"Zoomer sur la sélection",
        drawFeatureControlTitle:"Dessiner un polygone",
        modifyFeatureControlTitle:"Modifier la géométrie",
        tbarDeleteFeatureButtonTooltip:"Effacer la géométrie",
        tbarPreviousButtonTooltip:"Position précédente",
        tbarNextButtonTooltip:"Position suivante",
        zoomBoxInControlTitle:"Zoom en avant",
        zoomBoxOutControlTitle:"Zoom en arrière",
        zoomToMaxExtentControlTitle:"Zoom arrière maximum",
        featureInfoControlTitle:"Voir les informations sur le point"
    });
}

if(Genapp.NumberRangePicker){
    Ext.apply(Genapp.NumberRangePicker.prototype, {
        minFieldLabel:"Min",
        maxFieldLabel:"Max",
        okButtonText:"ok"
    });
}

if(OpenLayers.Handler.FeatureInfo.prototype){
    Ext.apply(OpenLayers.Handler.FeatureInfo.prototype, {
        alertErrorTitle:"Erreur :",
        alertRequestFailedMsg:"Désolé, la demande d'informations sur la géométrie a échoué..."
    });
}

if(Genapp.PredefinedRequestPanel){
	Ext.apply(Genapp.PredefinedRequestPanel.prototype, {
		title:"Requêtes prédéfinies",
		consultationButtonTooltip:"Consultation"
    });
}