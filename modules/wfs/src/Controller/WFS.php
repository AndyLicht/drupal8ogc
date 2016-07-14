<?php

namespace Drupal\drupal8ogc_wfs\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Response;
use Drupal\user\Entity\User;

class WFS extends ControllerBase 
{
    public function buildContent()
    {
        $possiblerequestoptions = ['getcapabilities'];
        $responseXML = null;
        $request = strtolower(filter_input(INPUT_GET,'request'));
        //$request = strtolower($_GET['request']);
        switch ($request)
        {
            case NULL:
                $responseXML = $this->setException(0,null);
                break;
            case 'getcapabilities':
                $responseXML = $this->getCapabilities();
                break;
            case !in_array($request,$possiblerequestoptions):
                $responseXML = $this->setException(1,$request);
                break;
        }
        
        //create Response
        $response = new Response();
        $response->setContent($responseXML);
        $response->setStatusCode(Response::HTTP_OK);
        $response->headers->set('Content-Type', 'application/xml');
        //$response->headers->set('Content-Type', 'text/html');
        // prints the HTTP headers followed by the content
        return $response;
    }
   
    private function setHeader()
    {
        $config = $this->config('drupal8ogc_wfs.settings');
        $header = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>".$config->get('xmlheader');
        return $header;
    }
    
    public function getCapabilities()
    {
        $responseXML = $this->setHeader();
        $responseXML = $responseXML.$this->getServiceIdentification();
        $responseXML = $responseXML.$this->getServiceProvider();
        $responseXML = $responseXML.$this->getOperationsMetadata();
        $responseXML = $responseXML.$this->getEnd();
        return $responseXML;
    }
   
    private function setException($i,$request)
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
    
    private function getKeywords()
    {
        $responseXML = '';
        $config = $this->config('drupal8ogc_wfs.settings');
        $keywords = split(";",$config->get('wfskeywords'));
        foreach ($keywords as $keyword)
        {
            $responseXML = $responseXML.'<Keyword>'.$keyword.'</Keyword>';
        }
        return $responseXML;
    }
    private function getServiceProvider()
    {
        $config = $this->config('drupal8ogc_wfs.settings');
        if(!$config->get('provider')|| $config->get('provider') === '' || $config->get('provider') === NULL)
        {
            $user = User::load(1);
        }
        else
        {
            $user = User::load($config->get('provider'));
        }
        //var_dump($user);
        $responseXML = '
            <ServiceProvider>
                <ProviderName>'.$user->get('field_ogc_providername')->value.'</ProviderName>
                <ServiceContact>
                    <IndividualName>'.$user->get('name')->value.'</IndividualName>
                    <PositionName></PositionName>
                    <ContactInfo>
                        <Address>
                            <PostalCode>'.$user->get('field_ogc_postal_code')->value.'</PostalCode>
                            <City>'.$user->get('field_ogc_city')->value.'</City>
                            <AdministrativeArea>'.$user->get('field_ogc_administrative_area')->value.'</AdministrativeArea>
                            <Country>'.$user->get('field_ogc_country')->value.'</Country>
                            <ElectronicMailAddress>'.$user->get('field_ogc_mail')->value.'</ElectronicMailAddress>
                        </Address>
                        <Phone>
                            <Voice>'.$user->get('field_ogc_phone')->value.'</Voice>
                            <Facsimile>'.$user->get('field_ogc_facsimile_number')->value.'</Facsimile>   
                        </Phone>
                    </ContactInfo>
                </ServiceContact>
            </ServiceProvider>
            ';
        return $responseXML; 
    }
    
    private function getEnd()
    {
        //</wfsb:WFS_Simple_Capabilities>
        $config = $this->config('drupal8ogc_wfs.settings');
        return $config->get('xmlend');
    }
    
    private function getServiceIdentification()
    {
        $config = $this->config('drupal8ogc_wfs.settings');
        $responseXML = 
            '<ServiceIdentification>
                <Title>'.$config->get('wfstitle').'</Title>
                <Abstract>'.$config->get('wfsabstract').'</Abstract>
                <Keywords>';
        $responseXML = $responseXML.$this->getKeywords();
        $responseXML = $responseXML.'</Keywords>
                <ServiceType>WFS</ServiceType>
                <ServiceTypeVersion>2.0.0</ServiceTypeVersion>
                <Fees>'.$config->get('wfsfees').'</Fees>
                <AccessConstraints>'.$config->get('wfsaccess').'</AccessConstraints>
            </ServiceIdentification>'            
            ;
        return $responseXML;
    }
    
    private function getOperationsMetadata()
    {
        $host = \Drupal::request()->getHost();
        $responseXML = 
        "<OperationsMetadata>
            <Operation name=\"GetCapabilities\">
                <DCP><HTTP><Get xlink:href=\"http://".$host."/drupal8ogc/wfs\"></Get></HTTP></DCP>
            </Operation>
            <Operation name=\"GetFeature\">
                <DCP><HTTP><Get xlink:href=\"http://".$host."/drupal8ogc/wfs\"></Get></HTTP></DCP>
                <Parameter name=\"outputFormat\">
                <Value>text/xml</Value>
                </Parameter>
            </Operation>
            <Operation name=\"DescribeFeatureType\">
                <DCP><HTTP><Get xlink:href=\"http://".$host."/drupal8ogc/wfs\"></Get></HTTP></DCP>
                <Parameter name=\"outputFormat\">
                <Value>text/xml</Value>
                </Parameter>
            </Operation>
        </OperationsMetadata>
        ";
        return $responseXML;
    }
}