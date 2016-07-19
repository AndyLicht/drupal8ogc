<?php

namespace Drupal\drupal8ogc_wfs\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Response;

class WFS extends ControllerBase 
{
    public function buildContent()
    {
        $possiblerequestoptions = ['getcapabilities','descripefeaturetype'];
        $responseXML = null;
        $request = strtolower(filter_input(INPUT_GET,'request'));
        $version = filter_input(INPUT_GET,'version');
        if(!$version || $version != '1.1.0')
        {
            $responseXML = WFSException::setException(3,$request);
        }
        else
        {    
            switch ($request)
            {
                case NULL:
                    $responseXML = self::setException(0,null);
                    break;
                case 'getcapabilities':
                    $responseXML = GetCapabilities::getCapabilities();
                    break;
                case 'descripefeaturetype':
                    $responseXML = DescripeFeatureType::descripefeaturetype($_GET);
                    break;
                case !in_array($request,$possiblerequestoptions):
                    $responseXML = WFSException::setException(1,$request);
                    break;
            }
        }
        
        //create Response
        $response = new Response();
        $response->setContent($responseXML);
        $response->setStatusCode(Response::HTTP_OK);
        //$response->headers->set('Content-Type', 'application/xml');
        $response->headers->set('Content-Type', 'text/html');
        return $response;
    }
}