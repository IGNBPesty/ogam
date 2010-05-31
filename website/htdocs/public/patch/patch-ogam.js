// Default parameters
if(Genapp.ConsultationPanel){
    Ext.apply(Genapp.ConsultationPanel.prototype, {
        hideDetails : true, 
        hideMapDetails : true,  
        hideCsvExportButton : true, 
        hideAggregationCsvExportMenuItem : true,
        hideAggregationButton : true,
        hideInterpolationButton : true,
        widthToSubstract:120,
        heightToSubstract:210
    });
}