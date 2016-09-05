<?php

namespace OGAMBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * 
 * @Route("/query")
 *
 */
class QueryController extends Controller
{
    /**
     * @Route("/", name = "query_home")
     */
    public function indexAction()
    {
        return $this->render('OGAMBundle:Query:show_query_form.html.twig');
    }

    /**
     * @Route("/show-query-form")
     */
    public function showQueryFormAction()
    {
        return $this->render('OGAMBundle:Query:show_query_form.html.twig', array(
            // ...
        ));
    }

    /**
     * @Route("/ajaxgetpredefinedrequestlist")
     */
    public function ajaxgetpredefinedrequestlistAction()
    {
        return $this->render('OGAMBundle:Query:ajaxgetpredefinedrequestlist.html.twig', array(
            // ...
        ));
    }

    /**
     * @Route("/ajaxgetpredefinedrequestcriteria")
     */
    public function ajaxgetpredefinedrequestcriteriaAction()
    {
        return $this->render('OGAMBundle:Query:ajaxgetpredefinedrequestcriteria.html.twig', array(
            // ...
        ));
    }

    /**
     * @Route("/ajaxsavepredefinedrequest")
     */
    public function ajaxsavepredefinedrequestAction()
    {
        return $this->render('OGAMBundle:Query:ajaxsavepredefinedrequest.html.twig', array(
            // ...
        ));
    }

    /**
     * @Route("/ajaxgetqueryform")
     */
    public function ajaxgetqueryformAction()
    {
        return $this->render('OGAMBundle:Query:ajaxgetqueryform.html.twig', array(
            // ...
        ));
    }

    /**
     * @Route("/ajaxgetqueryformfields")
     */
    public function ajaxgetqueryformfieldsAction()
    {
        return $this->render('OGAMBundle:Query:ajaxgetqueryformfields.html.twig', array(
            // ...
        ));
    }

    /**
     * @Route("/ajaxgetdatasets")
     */
    public function ajaxgetdatasetsAction()
    {
        return $this->render('OGAMBundle:Query:ajaxgetdatasets.html.twig', array(
            // ...
        ));
    }

    /**
     * @Route("/ajaxbuildrequest")
     */
    public function ajaxbuildrequestAction()
    {
        return $this->render('OGAMBundle:Query:ajaxbuildrequest.html.twig', array(
            // ...
        ));
    }

    /**
     * @Route("/ajaxgetresultsbbox")
     */
    public function ajaxgetresultsbboxAction()
    {
        return $this->render('OGAMBundle:Query:ajaxgetresultsbbox.html.twig', array(
            // ...
        ));
    }

    /**
     * @Route("/ajaxgetresultcolumns")
     */
    public function ajaxgetresultcolumnsAction()
    {
        return $this->render('OGAMBundle:Query:ajaxgetresultcolumns.html.twig', array(
            // ...
        ));
    }

    /**
     * @Route("/ajaxgetresultrows")
     */
    public function ajaxgetresultrowsAction()
    {
        return $this->render('OGAMBundle:Query:ajaxgetresultrows.html.twig', array(
            // ...
        ));
    }

    /**
     * @Route("/getgridparameters")
     */
    public function getgridparametersAction()
    {
        return $this->render('OGAMBundle:Query:getgridparameters.html.twig', array(
            // ...
        ));
    }

    /**
     * @Route("/ajaxgetdetails")
     */
    public function ajaxgetdetailsAction()
    {
        return $this->render('OGAMBundle:Query:ajaxgetdetails.html.twig', array(
            // ...
        ));
    }

    /**
     * @Route("/ajaxgetchildren")
     */
    public function ajaxgetchildrenAction()
    {
        return $this->render('OGAMBundle:Query:ajaxgetchildren.html.twig', array(
            // ...
        ));
    }

    /**
     * @Route("/csv-export")
     */
    public function csvExportAction()
    {
        return $this->render('OGAMBundle:Query:csv_export.html.twig', array(
            // ...
        ));
    }

    /**
     * @Route("/kml-export")
     */
    public function kmlExportAction()
    {
        return $this->render('OGAMBundle:Query:kml_export.html.twig', array(
            // ...
        ));
    }

    /**
     * @Route("/json-export")
     */
    public function geojsonExportAction()
    {
        return $this->render('OGAMBundle:Query:geojson_export.html.twig', array(
            // ...
        ));
    }

    /**
     * @Route("/ajaxgettreenodes")
     */
    public function ajaxgettreenodesAction()
    {
        return $this->render('OGAMBundle:Query:ajaxgettreenodes.html.twig', array(
            // ...
        ));
    }

    /**
     * @Route("/ajaxgettaxrefnodes")
     */
    public function ajaxgettaxrefnodesAction()
    {
        return $this->render('OGAMBundle:Query:ajaxgettaxrefnodes.html.twig', array(
            // ...
        ));
    }

    /**
     * @Route("/ajaxgetdynamiccodes")
     */
    public function ajaxgetdynamiccodesAction()
    {
        return $this->render('OGAMBundle:Query:ajaxgetdynamiccodes.html.twig', array(
            // ...
        ));
    }

    /**
     * @Route("/ajaxgetcodes")
     */
    public function ajaxgetcodesAction()
    {
        return $this->render('OGAMBundle:Query:ajaxgetcodes.html.twig', array(
            // ...
        ));
    }

    /**
     * @Route("/ajaxgettreecodes")
     */
    public function ajaxgettreecodesAction()
    {
        return $this->render('OGAMBundle:Query:ajaxgettreecodes.html.twig', array(
            // ...
        ));
    }

    /**
     * @Route("/ajaxgettaxrefcodes")
     */
    public function ajaxgettaxrefcodesAction()
    {
        return $this->render('OGAMBundle:Query:ajaxgettaxrefcodes.html.twig', array(
            // ...
        ));
    }

    /**
     * @Route("/ajaxgetlocationinfo")
     */
    public function ajaxgetlocationinfoAction()
    {
        return $this->render('OGAMBundle:Query:ajaxgetlocationinfo.html.twig', array(
            // ...
        ));
    }

    /**
     * @Route("/ajaxresetresultlocation")
     */
    public function ajaxresetresultlocationAction()
    {
        return $this->render('OGAMBundle:Query:ajaxresetresultlocation.html.twig', array(
            // ...
        ));
    }

}
