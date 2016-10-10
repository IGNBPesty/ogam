/**
 * This class defines the model for the layers.
 */
Ext.define('OgamDesktop.model.map.Layer',{
	extend: 'OgamDesktop.model.map.base',
	requires:[
	    'OgamDesktop.model.map.ZoomLevel',
	    'OgamDesktop.model.map.LayerService'
	],
	fields: [
	    {name: 'id', type: 'string'},
	    {name: 'name', type: 'string'},
	    {name: 'label', type: 'string'},
	    {name: 'serviceLayerName', type: 'string'},
	    {name: 'isTransparent', type: 'boolean'},
	    {name: 'defaultOpacity', type: 'integer'},
	    {name: 'isBaseLayer', type: 'boolean'},
	    {name: 'isUntiled', type: 'boolean'},
	    {name: 'maxZoomLevel', reference:'ZoomLevel'},
	    {name: 'minZoomLevel', reference:'ZoomLevel'},
	    {name: 'hasLegend', type: 'boolean'},
	    {name: 'providerId', type: 'string'},
	    {name: 'activateType', type: 'string'},
	    {name: 'viewService', reference:'LayerService'},
	    {name: 'legendService', reference:'LayerService'},
	    {name: 'detailService', reference:'LayerService'},
	    {name: 'featureService', reference:'LayerService'}
	],
	
	/**
	 * Return true if the current resolution is out of the layer resolutions
	 * @param {Number} currentResolution The current resolution
	 * @return {Boolean}
	 */
    isOutOfResolution: function(currentResolution){
        var minResolution = null, maxResolution = null;
        if (!Ext.isEmpty(this.getMinZoomLevel())) {
            minResolution = this.getMinZoomLevel().get('resolution');
        }
        if (!Ext.isEmpty(this.getMaxZoomLevel())) {
            maxResolution = this.getMaxZoomLevel().get('resolution');
        }
        if ((minResolution != null && currentResolution < minResolution)
                || (maxResolution != null && currentResolution >= maxResolution)) {
            return true;
        } else {
            return false;
        }
	}
});