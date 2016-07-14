<?php
/*
 *  WFS Service
 */ 
namespace Drupal\drupal8ogc_wfs\Controller;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\user\Entity\User;

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
        if(!$config->get('provider'))
        {
            $providerId = 1;
        }
        else
        {
            $providerId = $config->get('provider');
        };
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
         $form['base']['getprovider'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('actual ServiceProvider'),
            '#value' => $this->getServiceProviderName($providerId),
        );
        $form['base']['setprovider'] = array(
            '#type' => 'select',
            '#title' => $this->t('choose Provider'),
            '#options' => $this->createSelectUsers(),
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
        $form['base']['wfstitle'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('Service Title'),
            '#default_value' => $config->get('wfstitle')
        );
        $form['base']['wfsabstract'] = array(
            '#type' => 'textarea',
            '#title' => $this->t('Service Abstract'),
            '#default_value' => $config->get('wfsabstract')
        );
        $form['base']['wfskeywords'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('Service Keywords'),
            '#default_value' => $config->get('wfskeywords'),
            '#description' => $this->t('spilt your keywords by ;'),
        );
        $form['base']['wfsfees'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('Service Fees'),
            '#default_value' => $config->get('wfsfees')
        );
        $form['base']['wfsaccess'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('Service Access Constraints'),
            '#default_value' => $config->get('wfsaccess')
        );
        return parent::buildForm($form, $form_state);
    }
    
    public function submitForm(array &$form, FormStateInterface $form_state)
    {   
        $this->config('drupal8ogc_wfs.settings')
            ->set('xmlheader',$form_state->getValue('xmlheader'))    
            ->set('xmlend',$form_state->getValue('xmlend'))
            ->set('provider',$form_state->getValue('setprovider'))
            ->set('wfsabstract',$form_state->getValue('wfsabstract'))
            ->set('wfstitle',$form_state->getValue('wfsabstract'))
            ->set('wfsfees',$form_state->getValue('wfsfees'))
            ->set('wfsaccess',$form_state->getValue('wfsaccess'))                
            ->set('wfskeywords',$form_state->getValue('wfskeywords'))                
            ->save();
        parent::submitForm($form, $form_state);
    }
    
    public function validateForm(array &$form, FormStateInterface $form_state) 
    {
       
    }
    
    private function getServiceProviderName($id)
    {
        $user = User::load($id);
        return $user->get('name')->value.', '.$user->get('field_ogc_providername')->value;
    }
    
    private function createSelectUsers()
    {
        $ids = \Drupal::entityQuery('user')
            ->condition('status', 1)
            ->execute();
        $users = User::loadMultiple($ids);
        $options = array();
        foreach($users as $user)
        {
            $options[$user->id()] = $user->get('name')->value.', '.$user->get('field_ogc_providername')->value;
        }
        return $options;
    }
}