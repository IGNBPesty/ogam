/**
 * Licensed under EUPL v1.1 (see http://ec.europa.eu/idabc/eupl).
 *
 * © European Union, 2008-2012
 *
 * Reuse is authorised, provided the source is acknowledged. The reuse policy of the European Commission is implemented by a Decision of 12 December 2011.
 *
 * The general principle of reuse can be subject to conditions which may be specified in individual copyright notices.
 * Therefore users are advised to refer to the copyright notices of the individual websites maintained under Europa and of the individual documents.
 * Reuse is not applicable to documents subject to intellectual property rights of third parties.
 */

/**
 * @requires OpenLayers/Control.js
 */

/**
 * Class: OpenLayers.Control.GetFeatureControl. Implements a very simple control
 * that generates a WKT request to get a feature geometry and add it to the map.
 */
OpenLayers.Control.GetFeatureControl = OpenLayers.Class(OpenLayers.Control, {

	/**
	 * @cfg {OpenLayers.Handler} handler Reference to the handler for this
	 *      control
	 */
	handler : null,

	/**
	 * @cfg {String} layerName The layer name
	 */
	layerName : null,
	
	/**
	 * @cfg {OpenLayers.map} map The map
	 */
	map : null,

	/**
	 * Property: type {String} The type of <OpenLayers.Control> -- When added to
	 * a <Control.Panel>, 'type' is used by the panel to determine how to handle
	 * our events.
	 */
	type : OpenLayers.Control.TYPE_TOGGLE,

	/**
	 * Constructor: OpenLayers.Control.GetFeatureControl
	 * 
	 * Parameters: options - {Object}
	 */
	initialize : function(map, options) {
		OpenLayers.Control.prototype.initialize.apply(this, [ options ]);
		
		// Register events
		//Ogam.eventManager.addEvents('getFeature');

		this.handler = new OpenLayers.Handler.GetFeature(this, {
			'click' : this.click,
			'control' : this
		});
	},

	/**
	 * This function is called when a feature is received. Fire a event with the
	 * received feature.
	 */
	getFeature : function(feature) {
		this.fireEvent('getFeature', feature, this.map.id);
	},

	/**
	 * Method: activate Activates the control.
	 * 
	 * Returns: {Boolean} The control was effectively activated.
	 */
	activate : function() {
		if (!this.active) {
			this.handler.activate();
		}
		return OpenLayers.Control.prototype.activate.apply(this, arguments);
	},

	/**
	 * Method: deactivate Deactivates the control.
	 * 
	 * Returns: {Boolean} The control was effectively deactivated.
	 */
	deactivate : function() {
		return OpenLayers.Control.prototype.deactivate.apply(this, arguments);
	},

	/**
	 * Destroy the control.
	 */
	destroy : function() {
		if (this.handler !== null) {
			this.handler.destroy();
			this.handler = null;
		}
		return OpenLayers.Control.prototype.destroy.apply(this, arguments);
	},

	CLASS_NAME : "OpenLayers.Control.GetFeatureControl"
});

/**
 * The handler for the control
 */
OpenLayers.Handler.GetFeature = OpenLayers.Class(OpenLayers.Handler, {
	/**
	 * @cfg {String} alertErrorTitle The alert Error Title (defaults to
	 *      <tt>'Error :'</tt>)
	 */
	alertErrorTitle : 'Error :',
	/**
	 * @cfg {String} alertRequestFailedMsg The alert Request Failed Msg
	 *      (defaults to <tt>'Sorry, the request failed...'</tt>)
	 */
	alertRequestFailedMsg : 'Sorry, the feature info request failed...',

	/**
	 * @cfg {OpenLayers.Control.FeatureInfoControl} control The control
	 */
	control : null,

	/**
	 * The gml format used to read the response.
	 * 
	 * @type {OpenLayers.Format.GML}
	 * @property wktFormat
	 */
	gmlFormat : new OpenLayers.Format.GML(),
	
	/**
	 * Handle the response from the server.
	 * 
	 * @param response
	 */
	handleResponse: function (response) {
		 if(response.status == 500) {
			 Ext.Msg.alert(this.alertErrorTitle, this.alertRequestFailedMsg);
		 }
		 if(!response.responseText) {
			 Ext.Msg.alert(this.alertErrorTitle, this.alertRequestFailedMsg);
		 }
		 
		 // Decode the response
		 try {
			var feature = this.gmlFormat.read(response.responseText);
			this.control.getFeature(feature);

		} catch (e) {
			Ext.Msg.alert(this.alertErrorTitle, this.alertRequestFailedMsg);
		}
	},

	/**
	 * Handle the click event.
	 */
	click : function(evt) {
		// Calcul de la coordonnée correspondant au point cliqué par
		// l'utilisateur
		var px = new OpenLayers.Pixel(evt.xy.x, evt.xy.y);
		var ll = this.map.getLonLatFromPixel(px);

		// Construction d'une URL pour faire une requête WFS sur le point
		var url = Ogam.base_url + "proxy/getwfs?SERVICE=WFS&VERSION=1.0.0&REQUEST=GetFeature&typename=" + this.control.layerName + "&BBOX=" + ll.lon + ","
				+ ll.lat + "," + ll.lon + "," + ll.lat;
		url = url + "&MAXFEATURES=1";

		// Send a request
		OpenLayers.Request.GET({
				url : url, 
				scope : this,
				callback: this.handleResponse});
	}

});
