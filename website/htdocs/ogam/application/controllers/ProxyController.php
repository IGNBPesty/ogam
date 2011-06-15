<?php
/**
 * © French National Forest Inventory
 * Licensed under EUPL v1.1 (see http://ec.europa.eu/idabc/eupl).
 */
require_once 'AbstractOGAMController.php';

/**
 * Proxy used to safely route the request to the mapserver.
 * @package controllers
 */
class ProxyController extends AbstractOGAMController {

	/**
	 * Initialise the controler
	 */
	public function init() {
		parent::init();

		$this->classDefinitionModel = new Application_Model_DbTable_Mapping_ClassDefinition();
	}

	/**
	 * No authorization check.
	 */
	function preDispatch() {
		parent::preDispatch();

		$userSession = new Zend_Session_Namespace('user');
		$permissions = $userSession->permissions;
		if (empty($permissions)) { // user not logged
			$this->_redirector->gotoUrl('/');
		}
	}

	/**
	 * Extract the content of a String located after a substring.
	 *
	 * @param String $string the source string
	 * @param String $substring the substring to locate
	 * @return String the part of the string located after the substring
	 */
	private function _extractAfter($string, $substring) {
		return substr($string, strpos($string, $substring) + strlen($substring));
	}

	/**
	 * Extract the value of a parameter from an URL.
	 *
	 * @param String $url the url string
	 * @param String $param the parameter name
	 * @return String the value of the parameter
	 */
	private function _extractParam($url, $param) {
		$end = $this->_extractAfter($url, $param."=");
		$endpos = strpos($end, "&");
		if ($endpos === false) {
			$endpos = strlen($end);
		}
		$value = substr($end, 0, $endpos);
		return $value;
	}

	/**
	 * Checks that a string ends with a given substring.
	 *
	 * @param String $str
	 * @param String $sub
	 * @return a boolean
	 */
	private function _endsWith($str, $sub) {
		return (substr($str, strlen($str) - strlen($sub)) == $sub);
	}

	/**
	 * Get a Tile from Mapserver
	 */
	function gettileAction() {

		$uri = $_SERVER["REQUEST_URI"];

		$configuration = Zend_Registry::get("configuration");
		$mapserverURL = $configuration->mapserver_url;
		$mapserverURL = $mapserverURL."&";

		$uri = $mapserverURL.$this->_extractAfter($uri, "proxy/gettile?");

		// Check the image type
		$imagetype = $this->_extractParam($uri, "FORMAT");
		if ($this->_endsWith($imagetype, "JPG") || $this->_endsWith($imagetype, "JPEG")) {
			header("Content-Type: image/jpg");
		} else {
			header("Content-Type: image/png");
		}

		// If the layer needs activation, we suppose it needs a SLD.
		$hassld = $this->_extractParam($uri, "HASSLD");
		if (strtolower($hassld) == "true") {

			// Get the layer name
			$layerName = $this->_extractParam($uri, "LAYERS");

			if (strpos($layerName, "interpolation") !== FALSE) {
				$sld = $this->_generateRasterSLD($layerName);
			} else {
				$sld = $this->_generateSLD($layerName, 'average_value');
			}
			$uri .= "&SLD_BODY=".urlencode($sld);
		}

		$this->logger->debug('redirect gettile : '.$uri);

		// Send the request to Mapserver and forward the response data
		$handle = fopen($uri, "rb");
		if ($handle) {
			while (!feof($handle)) {
				echo fread($handle, 8192);
			}
			fclose($handle);
		}

		// No View, we send directly the output
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
	}

	/**
	 * Get a Tile from Tilecache
	 */
	function getcachedtileAction() {

		$configuration = Zend_Registry::get("configuration");
		$tilecacheURL = $configuration->tilecache_url;
		$ur = new HttpQueryString(false, $_SERVER["QUERY_STRING"]); //recupere la requete envoyé partie ?...

		$queriesArg = array();
		$queriesArg['request'] = 'GetMap';
		$queriesArg['service'] = 'WMS';

		$query = $ur->mod($queriesArg); //force la valeur de certains parametres

		$uri = $tilecacheURL.$query->toString();

		$this->logger->debug('redirect getcachedtile : '.$uri);

		// Send the request to Mapserver and forward the response data
		header("Content-Type: image/png");
		$handle = fopen($uri, "rb");
		if ($handle) {
			while (!feof($handle)) {
				echo fread($handle, 8192);
			}
			fclose($handle);
		}

		// No View, we send directly the output
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender();

	}

	/**
	 * Get a Legend Image
	 */
	function getlegendimageAction() {

		$uri = $_SERVER["REQUEST_URI"];

		$configuration = Zend_Registry::get("configuration");
		$mapserverURL = $configuration->mapserver_url;
		$mapserverURL = $mapserverURL."&";

		$uri = $mapserverURL.$this->_extractAfter($uri, "proxy/getlegendimage?");

		// Check the image type
		$imagetype = $this->_extractParam($uri, "FORMAT");
		if ($this->_endsWith($imagetype, "JPG") || $this->_endsWith($imagetype, "JPEG")) {
			header("Content-Type: image/jpg");
		} else {
			header("Content-Type: image/png");
		}

		// If the layer needs activation, we suppose it needs a SLD.
		$activation = $this->_extractParam($uri, "HASSLD");
		$this->logger->debug('uri : '.$uri);
		$this->logger->debug('activation : '.$activation);
		if (strtolower($activation) == "true") {

			// Get the layer name
			$layerName = $this->_extractParam($uri, "LAYER");

			// generate a SLD_BODY
			if (strpos($layerName, "interpolation") !== FALSE) {
				$sld = $this->_generateRasterSLD($layerName);
			} else {
				$sld = $this->_generateSLD($layerName, 'average_value');
			}
			$uri .= "&SLD_BODY=".urlencode($sld);
		}

		$this->logger->debug('redirect getlegendimage : '.$uri);

		// Send the request to Mapserver and forward the response data
		$handle = fopen($uri, "rb");
		if ($handle) {
			while (!feof($handle)) {
				echo fread($handle, 8192);
			}
			fclose($handle);
		}

		// No View, we send directly the output
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender();

	}

	/**
	 * Get the country code and the plot code for a given plot location.
	 */
	function getinfoAction() {

		$uri = $_SERVER["REQUEST_URI"];

		$configuration = Zend_Registry::get("configuration");
		$mapserverURL = $configuration->mapserver_url;
		$mapserverURL = $mapserverURL."&";
		$sessionId = session_id();

		$websiteSession = new Zend_Session_Namespace('website');
		$locationFormat = $websiteSession->locationFormat; // The format carrying the location info
		$schema = $websiteSession->schema; // The format carrying the location info

		$uri = $this->_extractAfter($uri, "proxy/getInfo?");

		$metadataModel = new Genapp_Model_DbTable_Metadata_Metadata();

		// On effecture une requête mapserver "GetFeature" pour chaque layer
		$uri = $mapserverURL.$uri."&SESSION_ID=".$sessionId;
		$this->logger->debug('redirect getinfo : '.$uri);
		$gml = "";
		$handle = fopen($uri, "rb");
		if ($handle) {
			while (!feof($handle)) {
				$gml .= fread($handle, 8192);
			}
			fclose($handle);
		}
		//$this->logger->debug('$gml : '.$gml);

		// On parse le résultat (à l'ancienne) et on affiche les données

		// Découpe du bloc display
		$this->logger->debug('Découpe du bloc display');
		if (strpos($gml, ":display>")) {

			$dom = new DomDocument();
			$dom->loadXML($gml);

			// Parcours les infos à afficher
			$displayNodes = $dom->getElementsByTagName("display");

			// The id is used to avoid to display two time the same result (it's a id for the result dataset)
			$id = array($schema.$locationFormat);
			// The columns config to setup the grid columnModel
			$columns = array();
			// The columns max length to setup the column width
			$columnsMaxLength = array();
			// The fields config to setup the store reader
			$locationFields = array('id');// The id must stay the first field
			// The data to full the store
			$locationsData = array();
			foreach ($displayNodes as $displayIndex => $displayNode) {
				$locationData = array();
				// Get the locations ids
				$params = $this->_getParams($displayNode);
				$nextNode = $displayNode->nextSibling;
				// TODO : remove the hard coded keys
				array_push($id, $params['provider_id'].$params['plot_code']);
				array_push($locationData, 'SCHEMA/'.$schema.'/FORMAT/'.$locationFormat.'/PROVIDER_ID/'.$params['provider_id'].'/PLOT_CODE/'.$params['plot_code']);
				// Get the other fields
				while ($nextNode != null) {
					if ($nextNode->nodeType == XML_ELEMENT_NODE && stripos($nextNode->nodeName, 'display_') !== false) {
						// Get the params
						$tableParams = $this->_getParams($nextNode);
						// Setup the location data and the column max length
						foreach ($tableParams as $columnName => $value) {
							array_push($locationData, $value);
							if (empty($columnsMaxLength[$columnName])) {
								$columnsMaxLength[$columnName] = array();
							}
							array_push($columnsMaxLength[$columnName], strlen($value));
						}
						// Setup the fields and columns config
						if ($displayIndex == ($displayNodes->length - 1)) {
							// Get the table name
							$table = substr($nextNode->nodeName, 11);
							$this->logger->debug('Table name: '.$table);
							// Get the table format
							$tableFormat = $metadataModel->getTableFormatFromTableName($schema, $table);
							$format = $tableFormat->format;
							// Get the table fields
							$tableFields = $metadataModel->getTableFields(null, $schema, $format);
							$tFOrdered = array();
							foreach ($tableFields as $tableField) {
								$tFOrdered[$tableField->columnName] = $tableField;
							}
							foreach ($tableParams as $columnName => $value) {
								$tableField = $tFOrdered[strtoupper($columnName)];
								// Set the column model and the location fields
								$dataIndex = $tableField->format.'__'.$tableField->data;
								// Adds the column header to prevent it from being truncated too and 2 for the header margins
								array_push($columnsMaxLength[$columnName], strlen($tableField->label) + 2);
								$column = array(
									'header' => $tableField->label,
									'dataIndex' => $dataIndex,
									'editable' => false,
									'tooltip' => $tableField->definition,
									'width' => max($columnsMaxLength[$columnName]) * 7
								);
								array_push($columns, $column);
								array_push($locationFields, $dataIndex);
							}
						}
					}
					$nextNode = $nextNode->nextSibling;
				}
				array_push($locationsData, $locationData);
			}
			// We must sort the array here because it can't be done
			// into the mapfile sql request to avoid a lower performance
			sort($id);

			// Check if the location table has a child table
			$hasChild = false;
			$locationTableFormat = $metadataModel->getTableFormat($schema, $locationFormat);
			$children = $metadataModel->getChildrenTableLabels($locationTableFormat);
			if (!empty($children)) {
				$hasChild = true;
			}

			//$this->logger->debug('$locationsData: ' . print_r($locationsData,true));

			echo '{'.'success:true'.', id:'.json_encode(implode('', $id)).', title:'.json_encode($locationTableFormat->label.' ('.count($locationsData).')').', hasChild:'.json_encode($hasChild).', columns:'.json_encode($columns).', fields:'.json_encode($locationFields).', data:'.json_encode($locationsData).'}';
		} else {
			echo '{success:true, id:null, title:null, hasChild:false, columns:[], fields:[], data:[]}';
		}

		// No View, we send directly the output
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
	}

	/**
	 * Extract some parameters from a WFS XLM response.
	 *
	 * @param String $domNode the XML node
	 * @return Array[String => String] The params
	 */
	private function _getParams($domNode) {
		$child = array();
		foreach ($domNode->childNodes as $childNode) {
			if ($childNode->nodeType == XML_ELEMENT_NODE) {
				$name = str_replace('ms:', '', $childNode->nodeName);
				$name = str_replace('myns:', '', $name);
				$child[$name] = $childNode->nodeValue;
			}
		}
		return $child;
	}

	/**
	 * Show a PDF report for the data submission.
	 */
	function showreportAction() {

		$configuration = Zend_Registry::get("configuration");
		$reportServiceURL = $configuration->reportGenerationService_url;
		$errorReport = $configuration->errorReport;
		$submissionId = $this->_getParam("submissionId");

		$reportURL = $reportServiceURL."/run?__format=pdf&__report=report/".$errorReport."&submissionid=".$submissionId;

		$this->logger->debug('redirect showreport : '.$reportURL);

		set_time_limit(0);
		header("Cache-control: private\n");
		header("Content-Type: application/pdf\n");
		header("Content-transfer-encoding: binary\n");
		header('Content-disposition: attachment; filename=Error_Report_'.$submissionId.".pdf");

		$handle = fopen($reportURL, "rb");
		if ($handle) {
			while (!feof($handle)) {
				echo fread($handle, 8192);
			}
			fclose($handle);
		}

		// No View, we send directly the output
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender();

	}

	/**
	 * Show a simplified PDF report for the data submission.
	 */
	function showsimplifiedreportAction() {

		$configuration = Zend_Registry::get("configuration");
		$reportServiceURL = $configuration->reportGenerationService_url;
		$errorReport = $configuration->simplifiedReport;
		$submissionId = $this->_getParam("submissionId");

		$reportURL = $reportServiceURL."/run?__format=pdf&__report=report/".$errorReport."&submissionid=".$submissionId;

		$this->logger->debug('redirect showreport : '.$reportURL);

		set_time_limit(0);
		header("Cache-control: private\n");
		header("Content-Type: application/pdf\n");
		header("Content-transfer-encoding: binary\n");
		header('Content-disposition: attachment; filename=Error_Report_'.$submissionId.".pdf");

		$handle = fopen($reportURL, "rb");
		if ($handle) {
			while (!feof($handle)) {
				echo fread($handle, 8192);
			}
			fclose($handle);
		}

		// No View, we send directly the output
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender();

	}

	/**
	 * Show a PDF report for the plot location submission.
	 */
	function showplotreportAction() {

		$configuration = Zend_Registry::get("configuration");
		$reportServiceURL = $configuration->reportGenerationService_url;
		$plotErrorReport = $configuration->plotErrorReport;
		$submissionId = $this->_getParam("submissionId");

		$reportURL = $reportServiceURL."/run?__format=pdf&__report=report/".$plotErrorReport."&submissionid=".$submissionId;

		$this->logger->debug('redirect showreport : '.$reportURL);

		set_time_limit(0);
		header("Cache-control: private\n");
		header("Content-Type: application/pdf\n");
		header("Content-transfer-encoding: binary\n");
		header('Content-disposition: attachment; filename=Error_Report_'.$submissionId.".pdf");

		$handle = fopen($reportURL, "rb");
		if ($handle) {
			while (!feof($handle)) {
				echo fread($handle, 8192);
			}
			fclose($handle);
		}

		// No View, we send directly the output
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender();

	}

	/**
	 * Generate a SLD.
	 * TODO : Move this method to the "Map" controler and make a call between controlers.
	 *
	 * @param String $layerName The name of the layer
	 * @param String $variableName The name of the variable
	 */
	private function _generateSLD($layerName, $variableName) {

		$this->logger->debug('_generateSLD : '.$layerName);

		// Define the classes
		// TODO : Remove the hardcoded reference to BASAL_AREA
		$classes = $this->classDefinitionModel->getClassDefinition('BASAL_AREA');

		// Generate the SLD string
		$sld = '<StyledLayerDescriptor version="1.0.0"';
		$sld .= ' xmlns="http://www.opengis.net/sld"';
		$sld .= '  xmlns="http://www.opengis.net/ogc"';
		$sld .= '  xsi:schemaLocation="http://www.opengis.net/sld http://schemas.opengis.net/sld/1.0.0/StyledLayerDescriptor.xsd">';
		$sld .= '<NamedLayer>';
		$sld .= '<Name>'.$layerName.'</Name>';
		$sld .= '<UserStyle>';
		$sld .= '<Name>'.$layerName.'</Name>';
		$sld .= '<Title>'.$layerName.'</Title>';
		$sld .= '<FeatureTypeStyle>';

		// Generate the SLD code corresponding to one class
		foreach ($classes as $classe) {

			$sld .= '<Rule>';
			$sld .= '<Name>'.$classe->label.'</Name>';
			$sld .= '<Filter>';
			$sld .= '<And>';
			$sld .= '<PropertyIsGreaterThan>';
			$sld .= '<PropertyName>'.$variableName.'</PropertyName>';
			$sld .= '<Literal>'.$classe->minValue.'</Literal>';
			$sld .= '</PropertyIsGreaterThan>';
			$sld .= '<PropertyIsLessThan>';
			$sld .= '<PropertyName>'.$variableName.'</PropertyName>';
			$sld .= '<Literal>'.$classe->maxValue.'</Literal>';
			$sld .= '</PropertyIsLessThan>';
			$sld .= '</And>';
			$sld .= '</Filter>';
			$sld .= '<PolygonSymbolizer>';
			$sld .= '<Fill>';
			$sld .= '<CssParameter name="fill">#'.$classe->color.'</CssParameter>';
			$sld .= '</Fill>';
			$sld .= '</PolygonSymbolizer>';
			$sld .= '</Rule>';
		}

		$sld .= '</FeatureTypeStyle>';
		$sld .= '</UserStyle>';
		$sld .= '</NamedLayer>';
		$sld .= '</StyledLayerDescriptor>';

		$this->logger->debug('_generated SLD : '.$sld);

		return $sld;

	}

	/**
	 * Generate a SLD for a Raster layer.
	 * TODO : Move this method to the "Map" controler and make a call between controlers.
	 *
	 * @param String $layerName The name of the layer
	 * @param String $variableName The name of the variable
	 */
	private function _generateRasterSLD($layerName) {

		$this->logger->debug('_generateRasterSLD : '.$layerName);

		// Define the classes
		// TODO : Remove the hardcoded reference to BASAL_AREA
		$classes = $this->classDefinitionModel->getRasterClassDefinition('BASAL_AREA');

		// Generate the SLD string
		$sld = '<StyledLayerDescriptor version="1.0.0"';
		$sld .= ' xmlns="http://www.opengis.net/sld"';
		$sld .= '  xmlns="http://www.opengis.net/ogc"';
		$sld .= '  xsi:schemaLocation="http://www.opengis.net/sld http://schemas.opengis.net/sld/1.0.0/StyledLayerDescriptor.xsd">';
		$sld .= '<NamedLayer>';
		$sld .= '<Name>'.$layerName.'</Name>';
		$sld .= '<UserStyle>';
		$sld .= '<Name>'.$layerName.'</Name>';
		$sld .= '<Title>'.$layerName.'</Title>';
		$sld .= '<FeatureTypeStyle>';
		$sld .= '<Rule>';
		$sld .= '<RasterSymbolizer>';
		$sld .= '<ColorMap>';

		// Generate the SLD code corresponding to one class
		foreach ($classes as $classe) {
			$sld .= '<ColorMapEntry color="#'.$classe->color.'" quantity="'.$classe->maxValue.'" label="'.$classe->label.'" />';
		}

		$sld .= '</ColorMap>';
		$sld .= '</RasterSymbolizer>';
		$sld .= '</Rule>';
		$sld .= '</FeatureTypeStyle>';
		$sld .= '</UserStyle>';
		$sld .= '</NamedLayer>';
		$sld .= '</StyledLayerDescriptor>';

		$this->logger->debug('_generated SLD : '.$sld);

		return $sld;

	}

}