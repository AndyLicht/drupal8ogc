<?php
/*
 *  WFS-Exception
 */
namespace Drupal\drupal8ogc_wfs\Controller;

use Drupal\Core\Controller\ControllerBase;

class WFSException extends ControllerBase 
{
    public function setException($i,$request)
    {
        switch($i)
        {
            case 0:
                $responseXML = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
                    <ExceptionReport version=\"1.0\">
                        <Exception exceptionCode=\"MissingParameterValue\" locator=\"request\"/>
                    </ExceptionReport>";
                break;
            case 1:
                $responseXML = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
                    <ExceptionReport version=\"1.0\">
                        <Exception exceptionCode=\"InvalidParameterValue\" locator=\"".$request."\"/>
                    </ExceptionReport>";
                break;
        }
        return $responseXML;
    }
}