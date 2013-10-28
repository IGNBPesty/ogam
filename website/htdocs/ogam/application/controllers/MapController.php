<?php
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
require_once 'AbstractOGAMController.php';

/**
 * MapController is the controller that manages the web-mapping interface.
 * @package controllers
 */
class MapController extends AbstractOGAMController {

	/**
	 * Initialise the controler
	 */
	public function init() {
		parent::init();

		// Initialise the models
		$this->layersModel = new Application_Model_Mapping_Layers();
		$this->boundingBoxModel = new Application_Model_Mapping_BoundingBox();

        $this->lang = strtoupper($this->translator->getAdapter()->getLocale());
	}

	/**
	 * Check if the authorization is valid this controler.
	 *
	 * @throws an Exception if the user doesn't have the rights
	 */
	function preDispatch() {

		parent::preDispatch();

		$userSession = new Zend_Session_Namespace('user');
		$permissions = $userSession->permissions;
		if (empty($permissions) || !(array_key_exists('DATA_QUERY', $permissions) || array_key_exists('DATA_QUERY_HARMONIZED', $permissions))) {
			throw new Zend_Auth_Exception('Permission denied for right : DATA_QUERY');
		}
	}

	/**
	 * Get the parameters used to initialise a map.
	 */
	public function getMapParametersAction() {
		$this->logger->debug('getMapParametersAction');
		// Get back the provider id for the current user
		$userSession = new Zend_Session_Namespace('user');
		$providerId = $userSession->user->providerId;

		// Get the parameters from configuration file
		$configuration = Zend_Registry::get("configuration");

		$this->view->bbox = $configuration->bbox; // Bounding box
		$this->view->tilesize = $configuration->tilesize; // Tile size
		$this->view->projection = "EPSG:".$configuration->srs_visualisation; // Projection
		
		$this->view->useMapProxy = $configuration->useMapProxy;

		// Get the available scales
		$scales = $this->layersModel->getScales();
		
		// Transform the available scales into resolutions
		$resolutions = $this->_getResolutions($scales);
		$resolString = implode(",", $resolutions);
		$this->view->resolutions = $resolString;
		$this->view->numZoomLevels = count($resolutions);

		$this->logger->debug('$configuration->usePerProviderCenter : '.$configuration->usePerProviderCenter);

		if ($configuration->usePerProviderCenter == 1) {
			// Center the map on the provider location
			$center = $this->boundingBoxModel->getCenter($providerId);
			$this->view->defaultzoom = $center->defaultzoom;
			$this->view->x_center = $center->x_center;
			$this->view->y_center = $center->y_center;
		} else {
			// Use default settings
			$this->view->defaultzoom = $configuration->zoom_level;
			$this->view->x_center = ($configuration->bbox->x_min + $configuration->bbox->x_max) / 2;
			$this->view->y_center = ($configuration->bbox->y_min + $configuration->bbox->y_max) / 2;
		}

		// Feature parameters
		if (empty($configuration->featureinfo->margin)) {
			$configuration->featureinfo->margin = "5000";
		}
		$this->view->featureinfo_margin = $configuration->featureinfo->margin;
		if (empty($configuration->featureinfo->typename)) {
			$configuration->featureinfo->typename = "result_locations";
		}
		$this->view->featureinfo_typename = $configuration->featureinfo->typename;
		if (empty($configuration->featureinfo->maxfeatures)) {
			$configuration->featureinfo->maxfeatures = 0;
		}
		$this->view->featureinfo_maxfeatures = $configuration->featureinfo->maxfeatures;

		$this->_helper->layout()->disableLayout();
		$this->render('map-parameters');
	}

	/**
	 * Calculate the resolution array corresponding to the available scales.
	 *
	 * @param Array[Integer] $scales The list of scales
	 */
	private function _getResolutions($scales) {

		// Get the parameters from configuration file
		$configuration = Zend_Registry::get("configuration");
		$tilesize = $configuration->tilesize; // Tile size in pixels
		$dpi = $configuration->mapserver_dpi; // Default number of dots per inch in mapserv
		$factor = $configuration->mapserver_inch_per_kilometer; // Inch to meter conversion factor

		// WARNING : Bounding box must match the tilecache configuration and tile size

		$resolutions = array();
		foreach ($scales as $scale) {
			$res = $scale * (2 * $tilesize) / ($dpi * $factor);
			$resolutions[$scale] = $res;
		}
		return $resolutions;
	}

	/**
	 * Return the list of vector layers as a JSON.
	 */
	public function ajaxgetvectorlayersAction() {

		$this->logger->debug('getvectorlayersAction');

		// Get the available layers
		$layerNames = $this->layersModel->getVectorLayersList();

		$json = '{"success":true';
		$json .= ', layerNames : [';
		$json .= '{"code":null,"label":"'.$this->translator->translate('empty_layer').'"},';
		foreach ($layerNames as $layerName => $layerLabel) {
			$json .= '{"code":'.json_encode($layerName).',';
			$json .= '"label":'. json_encode($layerLabel).'},';
		}
		$json = substr($json, 0, -1); // remove last comma
		$json .= ']';
		$json .= '}';

		echo $json;

		// No View, we send directly the javascript
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		$this->getResponse()->setHeader('Content-type', 'application/json');
	}

	/**
	 * Return the list of available layers as a JSON.
	 */
	public function ajaxgetlayersAction() {

		$this->logger->debug('ajaxgetlayersAction');

		// Get back the provider id of the user
		$userSession = new Zend_Session_Namespace('user');
		$providerId = $userSession->user->providerId;

		// Get some configutation parameters
		$configuration = Zend_Registry::get("configuration");
		$proxyPath = $configuration->useMapProxy ? '/mapProxy.php' : '/proxy/gettile';
		
		// Get the available services base urls and parameters
		$services = $this->layersModel->getServices();

		// Get the available layers
		$layers = $this->layersModel->getLayersList($providerId);
		
		// Get the available scales
		$scales = $this->layersModel->getScales();
		// Transform the available scales into resolutions
		$resolutions = $this->_getResolutions($scales);

				
		// Build the base URL for tiles
		$sessionId = session_id();
		$out = '{';
		foreach ($services as $service) {
			 $out .='"'.$service->serviceName.'":'.$service->serviceConfig.',';
			 if ($service->serviceName == "mapserver") {
			     $tileBaseUrl = json_decode($service->serviceConfig)->{'urls'}[0];
			 }
		}
		
		echo $out;
		
		// Build the wfs base URL for mapserver tiles
		echo '"url_wfs":"'.$tileBaseUrl.'/proxy/getwfs?SESSION_ID='.$sessionId.'",';

		// For each available layer, build the corresponding URL and definition
		$out = '"layers":[';
		foreach ($layers as $layer) {

			$out .= "{";

			// OpenLayer object (tiled or not)
			if ($layer->isUntiled == 1) {
				$out .= '"singleTile":true';
			} else {
				$out .= '"singleTile":false';
			}

			// Logical layer name
			$out .= ', "name":"'.$layer->layerName.'"';

			// Service name
			$out .= ', "servicename":"'.$layer->serviceName.'"';
			
			// Has a legend ?
			if ($layer->hasLegend == 1) {
				$out .= ', "hasLegend": true';
			} else {
				$out .= ', "hasLegend": false';
			}

			$out .= ', "params":{';

			// Server Layer name (or list of names)
			$layerNames = $layer->serviceLayerName;
			$out .= '"layers" : ["'.$layerNames.'"]';

			// Transparency
			if ($layer->isTransparent == 1) {
				$out .= ', "transparent": true';
			} else {
				$out .= ', "transparent": false';
			}

			//  Image Format
			$out .= ', "format": "image/'.$layer->imageFormat.'"';

			// Hidden ?
			if ($layer->isHidden == 1) {
				$out .= ', "isHidden": true';
			} else {
				$out .= ', "isHidden": false';
			}

			// Disabled ?
			if ($layer->isDisabled == 1) {
				$out .= ', "isDisabled": true';
			} else {
				$out .= ', "isDisabled": false';
			}

			// Checked ?
			if ($layer->isChecked == 1) {
				$out .= ', "isChecked": true';
			} else {
				$out .= ', "isChecked": false';
			}

			$out .= ', "activateType": "'.$layer->activateType.'"';

			// We will test this flag to know if we need to generate a SLD
			if ($layer->hasSLD == 1) {
				$out .= ', "hasSLD": true';
			} else {
				$out .= ', "hasSLD": false';
			}

			// Add the sessionid
			$out .= ', "session_id": "'.$sessionId.'"';

			// Add the country code
			$out .= ', "provider_id": "'.$providerId.'"';

			$out .= '}';

			// Options
			$out .= ', "options":{"buffer": 0';

			// Node Group
			if (!empty($layer->parentId)) {
				$out .= ', "nodeGroup": "'.$layer->parentId.'"';
			}

			// Checked Group
			if (!empty($layer->checkedGroup)) {
				$out .= ', "checkedGroup": "'.$layer->checkedGroup.'"';
			}

			// Transition effect
			if (!empty($layer->transitionEffect)) {
				$out .= ', "transitionEffect": "'.$layer->transitionEffect.'"';
			}

			// Layer visibility by default
			if ($layer->isDefault == 1) {
				$out .= ', "visibility": true';
			} else {
				$out .= ', "visibility": false';
			}

			// Is a Base Layer ?
			if ($layer->isBaseLayer == 1) {
				$out .= ', "isBaseLayer": true';
			} else {
				$out .= ', "isBaseLayer": false';
			}

			// Label
			$out .= ',"label":"'.addslashes($layer->layerLabel).'"';

			// Scale min/max management
			if ($layer->maxscale != "" || $layer->minscale != "") {
				$out .= ', "resolutions": [';

				$restable = "";
				foreach ($scales as $scale) {
					if (($layer->minscale == "" || $scale >= $layer->minscale) && ($layer->maxscale == "" || $scale <= $layer->maxscale)) {
						$restable .= $resolutions[$scale].", ";
					}
				}
				$restable = substr($restable, 0, -2);
				$out .= $restable;

				$out .= "]";
			}

			$out .= "}},";

		}

		// Remove the last comma
		if (!empty($layers)) {
			$out = substr($out, 0, -1);
		}

		echo $out.']}';

		// No View, we send directly the javascript
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		$this->getResponse()->setHeader('Content-type', 'application/json');

	}

	/**
	 * Return the model corresponding to the legend.
	 */
	public function ajaxgettreelayersAction() {

		$this->logger->debug('ajaxgettreelayersAction');

		// Get back the country code
		$userSession = new Zend_Session_Namespace('user');
		$providerId = $userSession->user->providerId;
		$this->logger->debug('providerId : '.$providerId);

		$item = $this->_getLegendItems(-1, $providerId);

		echo "[".$item."]";

		// No View, we send directly the javascript
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		$this->getResponse()->setHeader('Content-type', 'application/json');

	}

	/**
	 * Return the part of the legend model corresponding to the selected node.
	 *
	 * @param String $parentId The identifier of the parent node
	 * @param String $providerId The identifier of the provider
	 * @return String
	 */
	private function _getLegendItems($parentId, $providerId) {

		$this->logger->debug('_getLegendItems : '.$parentId." ".$providerId);

		// Get the list of items corresponding to the asked level
		$legendItems = $this->layersModel->getLegend($parentId, $providerId);

		// Get the list of active layers
		$mappingSession = new Zend_Session_Namespace('mapping');
		$activatedLayers = $mappingSession->activatedLayers;
		if ($activatedLayers == null) {
			$activatedLayers = array();
		}

		$json = "";

		// Iterate over each legend item
		foreach ($legendItems as $legendItem) {

			$json .= '{';
			$json .= '"text": "'.$legendItem->label.'", ';

			$json .= '"expanded": ';
			if ($legendItem->isExpended == 1) {
				$json .= 'true, ';
			} else {
				$json .= 'false, ';
			}

			$json .= '"checked": ';
			if ($legendItem->isChecked == 1) {
				$json .= 'true, ';
			} else {
				$json .= 'false, ';
			}

			$json .= '"hidden": ';
			if ($legendItem->isHidden == 1 && !in_array($legendItem->layerName, $activatedLayers)) {
				$json .= 'true, ';
			} else {
				$json .= 'false, ';
			}

			$json .= '"disabled": ';
			if ($legendItem->isDisabled == 1) {
				$json .= 'true, ';
			} else {
				$json .= 'false, ';
			}

			// The item is a leaf
			if ($legendItem->isLayer == 1) {
				$json .= '"leaf": true, ';
				$json .= '"nodeType" : "gx_layer", '; // TODO : Do this on the js side
				$json .= '"layer": "'.$legendItem->layerName.'" ';
			} else {
				// The item is a node
				$json .= '"leaf": false, ';
				$json .= '"nodeType" : "gx_layercontainer", '; // TODO : Do this on the js side
				$json .= '"nodeGroup": "'.$legendItem->itemId.'" ';

			}
			$json .= '}, ';
		}

		$json = substr($json, 0, -2);

		return $json;
	}

	/**
	 * Calculate bounding box corresponding to a position and zoom level.
	 *
	 * @param Integer $centerX the X position of the center of the map
	 * @param Integer $centerY the Y position of the center of the map
	 * @param Integer $zoomLevel the zoom level
	 * @param Integer $size the size of the image (in pixels)
	 * @return Array[Integer] the bounding box
	 */
	private function _getBbox($centerX, $centerY, $zoomLevel, $size) {

		// Get the scales
		$scales = $this->layersModel->getScales();

		// Get the resolutions (in meters per pixel)
		$resolutions = $this->_getResolutions($scales);

		// Get the resolution at the current zoom level
		$resolutions = array_values($resolutions);
		$currentRes = $resolutions[$zoomLevel];

		// Calculate the BBOX around the center for an image of a given size
		$xMin = $centerX - ($currentRes * $size / 2);
		$xMax = $centerX + ($currentRes * $size / 2);
		$yMin = $centerY - ($currentRes * $size / 2);
		$yMax = $centerY + ($currentRes * $size / 2);

		return $xMin.",".$yMin.",".$xMax.",".$yMax;
	}

	/**
	 * Show a PDF containing the map selected by the user.
	 */
	function printmapAction() {

		$this->logger->debug('generatemapAction');

		// Get the map parameters
		$center = $this->_getParam('center');
		$zoom = $this->_getParam('zoom');
		$layers = $this->_getParam('layers');
		$centerX = substr($center, stripos($center, "lon=") + 4, stripos($center, ",") - (stripos($center, "lon=") + 4));
		$centerY = substr($center, stripos($center, "lat=") + 4);
		
		//Get the base urls for the services
		$services = $this->layersModel->getServices();
		
		// Get the server name for the layers
		$layerNames = explode(",", $layers);
		$serviceLayerNames = "";
		$baseUrls="";
		foreach ($layerNames as $layerName) {
			$layer = $this->layersModel->getLayer($layerName);
			$serviceLayerNames .= $layer->serviceLayerName.",";
			
			//Get the base Url for service print
			$serviceName = $layer->serviceName;
			foreach ($services as $service) {
			    if ($service->serviceName == $serviceName){
			        $baseUrls .=$service->servicePrintUrl.",";
			    }
			}
		}
		$baseUrls = substr($baseUrls, 0, -1); // remove last comma		
		$serviceLayerNames = substr($serviceLayerNames, 0, -1); // remove last comma
		
		
		// Get the configuration values
		$configuration = Zend_Registry::get("configuration");
		$mapReportServiceURL = $configuration->mapReportGenerationService_url;
	    
		    
		// Get the scales
		$scales = $this->layersModel->getScales();
		    
		// Get the current scale
		$scalesArray = array_values($scales);
		$currentScale = $scales[$zoom];
		    
		//Construction of the json specification, parameter of mapfish-print servlet
		$spec="{
    
		    layout: 'A4 portrait',
		    title: 'A simple example',
		    srs: '".$configuration->srs_visualisation."',
		    units: 'm',
		    layers: [";
		    $layersArray = explode(",",$layers);
		    $baseUrlsArray = explode(",",$baseUrls);
		    $serviceLayerNamesArray = explode(",",$serviceLayerNames);
		    $i=0;
		    foreach ($layersArray as $layer) {
		        $spec .="{
		        type: 'WMS',
		        format: 'image/png',
		        layers: ['$serviceLayerNamesArray[$i]'],
		        baseURL: '$baseUrlsArray[$i]',
		        customParams: {TRANSPARENT:true}
		        },";
		        $i++;
		    }
		    $spec.=" ],
		    pages: [
		        {
		            center: [$centerX,$centerY],
		            scale: $currentScale,
		            dpi: 190,
		            mapTitle: '$layers',
		            comment: 'L93, center: $centerX,$centerY, scale: 1 : $currentScale',
		            data: [
		                {id:1, name: 'blah', icon: 'icon_pan'},
		                {id:2, name: 'blip', icon: 'icon_zoomin'}
		            ]
                }
            ]
	    }";
   
       $this->logger->debug('json : '.$spec);
		    
		    
		// Calculate the report URL
		$mapReportUrl = $mapReportServiceURL."/print.pdf?spec=".urlencode($spec);
		$this->logger->debug('generatemap URL : '.$mapReportUrl);
		
		// Set the timeout and user agent
		ini_set ('user_agent', $_SERVER['HTTP_USER_AGENT']);
		ini_set("max_execution_time", $configuration->max_execution_time);
		$maxReportGenerationTime = $configuration->max_report_generation_time;
		$defaultTimeout = ini_get('default_socket_timeout');
		if ($maxReportGenerationTime != null) {			
			ini_set('default_socket_timeout', $maxReportGenerationTime);
		}

		// Set the header for a PDF output
		header("Cache-control: private\n");
		header("Content-Type: application/pdf\n");
		header("Content-transfer-encoding: binary\n");
		header("Content-disposition: attachment; filename=Map_".date('dmy_Hi').".pdf");

		// Launch the PDF generation
		$handle = fopen($mapReportUrl, "rb");
		if ($handle) {
			while (!feof($handle)) {
				echo fread($handle, 8192);
			}
			fclose($handle);
			
			// No View, we send directly the output
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender();
		} else {
			$this->logger->debug("Error reading data");
			echo "Error reading data";
		}

		// Restore default timeout
		ini_set('default_socket_timeout', $defaultTimeout);
		
		// No View, we send directly the javascript
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender();

	}

}
