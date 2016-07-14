<?php
/*
 *  WFS Service
 */ 
namespace Drupal\drupal8ogc_wfs\Controller;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class AdminForm extends ConfigFormBase 
{
    
    public function getFormId() 
    {
        return 'drupal8ogc_wfs_settings_form';
    }
    
    protected function getEditableConfigNames() 
    {
        return ['drupal8ogc_wfs.settings'];  
    }
    
    public function buildForm(array $form, FormStateInterface $form_state) 
    {
        $config = $this->config('drupal8ogc_wfs.settings');
        $form['description'] = array(
            '#markup' => t('Settings for the ogc services.'),
        );
        $form['advanced'] = array(
            '#type' => 'vertical_tabs',
            '#title' => t('Settings'),
        );
       
        $form['base'] = array(
            '#type' => 'details',
            '#title' => t('WFS'),
            '#group' => 'advanced',
        );
        $form['base']['xmlheader'] = array(
            '#type' => 'textarea',
            '#title' => $this->t('actual XML-Header'),
            '#default_value' => $config->get('xmlheader')
        );
        $form['base']['xmlend'] = array(
            '#type' => 'textarea',
            '#title' => $this->t('End of File'),
            '#default_value' => $config->get('xmlend')
        );
        
        return parent::buildForm($form, $form_state);
    }
    
    public function submitForm(array &$form, FormStateInterface $form_state)
    {   
        $this->config('drupal8ogc_wfs.settings')
            ->set('xmlheader',$form_state->getValue('xmlheader'))    
            ->set('xmlend',$form_state->getValue('xmlend'))    
            ->save();
        parent::submitForm($form, $form_state);
    }
    
    public function validateForm(array &$form, FormStateInterface $form_state) 
    {
       
    }
}