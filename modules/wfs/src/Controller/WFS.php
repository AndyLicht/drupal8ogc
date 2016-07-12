<?php

namespace Drupal\drupal8ogc_wfs\Controller;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Response;

class WFS extends ControllerBase 
{
    public function buildContent()
    {
        $responseXML = null;
        $request = strtolower($_GET['request']);
        switch ($request)
        {
            case NULL:
                $responseXML = $this->setException(0);
                break;
            case 'getcapabilites':
                $responseXML = $this->getCapabilities($_GET['request']);
                break;
        }
        
        
        
        
        
        $response = new Response();
        $response->setContent($responseXML);
        $response->setStatusCode(Response::HTTP_OK);
        //$response->headers->set('Content-Type', 'application/xml');
        $response->headers->set('Content-Type', 'text/html');

        



        // prints the HTTP headers followed by the content
        return $response;
        
        //Basic WFS
        //Operations: GetCapabilities, DescribeFeatureType, GetFeatureType
    }
   
    public function getCapabilities($request)
    {
        $responseXML = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
                        <note>
                            <to>".$request."</to>
                            <from>Jani</from>
                            <heading>Reminder</heading>
                            <body>Don't forget me this weekend!</body>
                        </note>";
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
<ows:Facsimile/>
</ows:Phone>
<ows:Address>
<ows:City>Hamburg</ows:City>
<ows:AdministrativeArea/>
<ows:PostalCode>21109</ows:PostalCode>
<ows:Country>Germany</ows:Country>
</ows:Address>
</ows:ContactInfo>
</ows:ServiceContact>
</ows:ServiceProvider>
 */