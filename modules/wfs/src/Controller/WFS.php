<?php

namespace Drupal\drupal8ogc_wfs\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Response;
use Drupal\user\Entity\User;

class WFS extends ControllerBase 
{
    public function buildContent()
    {
        $responseXML = null;
        $request = strtolower(filter_input(INPUT_GET,$_GET['request']));
        $request = strtolower($_GET['request']);
        switch ($request)
        {
            case NULL:
                $responseXML = $this->setException(0);
                break;
            case 'getcapabilities':
                $responseXML = $this->getCapabilities();
                break;
        }
        
        //load Header
        $xmlContent = $this->setHeader();
        $xmlContent = $xmlContent.$responseXML;
        
        //create Response
        $response = new Response();
        $response->setContent($xmlContent);
        $response->setStatusCode(Response::HTTP_OK);
        $response->headers->set('Content-Type', 'application/xml');
        //$response->headers->set('Content-Type', 'text/html');
        // prints the HTTP headers followed by the content
        return $response;
        
        //Basic WFS
        //Operations: GetCapabilities, DescribeFeatureType, GetFeatureType
    }
   
    private function setHeader()
    {
        $config = $this->config('drupal8ogc_wfs.settings');
        $header = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>".$config->get('xmlheader');
        return $header;
    }
    
    public function getCapabilities()
    {
        $responseXML = $this->getServiceProvider();
        $responseXML = $responseXML.$this->getEnd();
        return $responseXML;
    }
   
    public function setException($i)
    {
        var_dump("setException");
        switch($i)
        {
            case 0:
                $responseXML = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
                    <ExceptionReport version=\"1.0\">
                        <Exception exceptionCode=\"MissingParameterValue\" locator=\"request\"/>
                    </ExceptionReport>";
                break;
        }
        
        return $responseXML;
    }
    
    public function getServiceProvider()
    {
        $config = $this->config('drupal8ogc.settings');
        $user = User::load($config->get('provider'));
        $responseXML = '
            <ServiceProvider>
                <ProviderName>'.$user->get('field_ogc_providername')->value.'</ProviderName>
                <ServiceContact>
                    <IndividualName>'.$user->get('name')->value.'</IndividualName>
                    <PositionName></PositionName>
                    <ContactInfo>
                        <Phone>
                            <Voice>'.$user->get('field_ogc_phone')->value.'</Voice>
                        </Phone>
                        <Address>
                            <ElectronicMailAddress>'.$user->get('field_ogc_mail')->value.'</ElectronicMailAddress>
                        </Address>
                    </ContactInfo>
                </ServiceContact>
            </ServiceProvider>';
        return $responseXML; 
    }
    
    private function getEnd()
    {
        //</wfsb:WFS_Simple_Capabilities>
        $config = $this->config('drupal8ogc_wfs.settings');
        return $config->get('xmlend');
    }
}

/*
 * <ows:ServiceProvider>
<ows:ProviderName>Behörde für Stadtentwicklung und Umwelt</ows:ProviderName>
<ows:ServiceContact>
<ows:IndividualName>Ronald Lehmann</ows:IndividualName>
<ows:PositionName/>
<ows:ContactInfo>
<ows:Phone>
<ows:Voice/>
<ows:Facsimile/>    FEHLT NOCH (Vorwahl?Ländercode?)
</ows:Phone>
<ows:Address>
<ows:City>Hamburg</ows:City> Fehlt noch
<ows:AdministrativeArea/> Fehlt noch
<ows:PostalCode>21109</ows:PostalCode>Fehlt noch
<ows:Country>Germany</ows:Country>Fehlt noch
</ows:Address>
</ows:ContactInfo>
</ows:ServiceContact>
</ows:ServiceProvider>
 */