<?php
/*
 *  WFS-GetCapabilities
 */
namespace Drupal\drupal8ogc_wfs\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\user\Entity\User;

class GetCapabilities extends ControllerBase 
{
    //setHeader könnte in die Main-Funktion des WFS
    
    
    public function getCapabilities()
    {
        $responseXML = self::setHeader();
        $responseXML = $responseXML.self::getServiceIdentification();
        $responseXML = $responseXML.self::getServiceProvider();
        $responseXML = $responseXML.self::getOperationsMetadata();
        $responseXML = $responseXML.self::getEnd();
        return $responseXML;
    }
    private function setHeader()
    {
        $config = $this->config('drupal8ogc_wfs.settings');
        $header = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>".$config->get('xmlheader');
        return $header;
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
        $responseXML = $responseXML.self::getKeywords();
        $responseXML = $responseXML.'</Keywords>
                <ServiceType>WFS</ServiceType>
                <ServiceTypeVersion>1.1.0</ServiceTypeVersion>
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