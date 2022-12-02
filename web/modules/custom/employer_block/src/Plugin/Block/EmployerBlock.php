<?php

namespace Drupal\employer_block\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Block\BlockPluginInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Provides a 'Employer Block' Block.
 *
 * @Block(
 *   id = "employer_block",
 *   admin_label = @Translation("Employer block"),
 *   category = @Translation("Employer"),
 * )
 */
class EmployerBlock extends BlockBase implements BlockPluginInterface
{
    /**
     * {@inheritdoc}
     */
    public function build()
    {
        $config = $this->getConfiguration();
        
        $users = array(
           array(
               'employerid' => $config['employer1_id']
           ),
      
           array(
               'employerid' => $config['employer2_id']
           ),
      
           array(
               'employerid' => $config['employer3_id']
           ),
           
           array(
               'employerid' => $config['employer4_id']
           ),
           
           array(
               'employerid' => $config['employer5_id']
           ),
      
           array(
               'employerid' => $config['employer6_id']
           ),
      
           array(
               'employerid' => $config['employer7_id']
           ),
           
           array(
               'employerid' => $config['employer8_id']
           ),
           
           array(
               'employerid' => $config['employer9_id']
           ),
           
           array(
               'employerid' => $config['employer10_id']
           ),
        );
        
        $responses = [];
    
        foreach ($users as $user){
         
         $url = "https://umd-csm.symplicity.com/api/public/v1/employers/" . $user['employerid'];
        
          // Use the API key to authorize
          $context = stream_context_create(array(
            'http' => array(
                'header'  => "authorization: Token AncVIv72dasr5BA2TAr45xnvPedLW3ud6WqK7lMewZXh8YsxylN0zmAeIyMBybAlYuYAAArBvkMf6DjGteMaSpijLysz8VGXN/p1tQcAePDIS+aeYsbhW20WUdv0ifJgdnsxI3D6iJY="
            )
          ));
          
          // Get the feed
          $data = file_get_contents($url, false, $context);
        
          // JSON DECODE
          $json = json_decode($data);
          
          //var_dump($json);
          
          // Add this object to the final array
          $responses[] = $json;
          
        }
        
        $return_string = '<div id="employercarousel" class="carousel slide" data-ride="carousel"><div class="carousel-inner">';
        
        $i = 0;
        foreach ($responses as $response){
          
          $industries = '';
          
          foreach ( $response->industries as $key => $industry ) {
            
            $after = ( $key !== count( $response->industries ) -1 ) ? ", " : "";
            
            $industries .= $industry->label . $after;
            
          }
          
          $i++;
          
          if($i == 1) { 
            $return_string .= '<div class="p-card item active">'; 
          } else {
            $return_string .= '<div class="p-card item">';
          }
          
          $return_string .= '<p class="employername">' . $response->name . '</p>';
          
          $return_string .= '<div class="row">';
          
          $return_string .= '<div class="col col-xs-12 col-sm-8 col-md-8 col-lg-8"><p class="overline">Company Overview:</p><p class="overview">' . $response->overview . '</p></div>';
          
          $return_string .= '<div class="col col-xs-12 col-sm-4 col-md-4 col-lg-4"><p class="overline">Location:</p><p class="location">' . $response->address->label . '</p><p class="overline">Industries:</p><p class="overview">'.$industries.'</p>';
          
          $return_string .= '<p><a href="' . $response->website . '" target="_blank" class="view-more-button button">Visit Website</a></p>';
          
          $return_string .= '</div></div>';
          
          $return_string .= '</div>';
          
        }
        
        $return_string .= '</div><a class="left carousel-control" href="#employercarousel" role="button" data-slide="prev">
    <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
    <span class="sr-only">Previous</span>
  </a>
  <a class="right carousel-control" href="#employercarousel" role="button" data-slide="next">
    <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
    <span class="sr-only">Next</span>
  </a></div>';

   //return $return_string;

        return [
            '#markup' => "<div id='symplicityblock'>$return_string</div>",
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function blockForm($form, FormStateInterface $form_state)
    {
        $form = parent::blockForm($form, $form_state);

        $config = $this->getConfiguration();

/*
        $current_year = date("Y");
        $codimth_copyright = $this->t('©@year codimTh. All Rights Reserved.', [
            '@year' => $current_year
        ]);

        $form['codimth_copyright'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Copyright'),
            '#description' => $this->t('Write your copyright text.'),
            '#default_value' => isset($config['codimth_copyright']) ? $config['codimth_copyright'] : $codimth_copyright,
        ];
*/
        
        $form['employer1_id'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Employer 1 ID'),
            '#description' => $this->t('This is the ID of this employer.'),
            '#default_value' => isset($config['employer1_id']) ? $config['employer1_id'] : '',
        ];
        
        
        $form['employer2_id'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Employer 2 ID'),
            '#description' => $this->t('This is the ID of this employer'),
            '#default_value' => isset($config['employer2_id']) ? $config['employer2_id'] : '',
        ];
        
        
        $form['employer3_id'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Employer 3 ID'),
            '#description' => $this->t('This is the ID of this employer'),
            '#default_value' => isset($config['employer3_id']) ? $config['employer3_id'] : '',
        ];
        
        $form['employer4_id'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Employer 4 ID'),
            '#description' => $this->t('This is the ID of this employer'),
            '#default_value' => isset($config['employer4_id']) ? $config['employer4_id'] : '',
        ];
        
        $form['employer5_id'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Employer 5 ID'),
            '#description' => $this->t('This is the ID of this employer'),
            '#default_value' => isset($config['employer5_id']) ? $config['employer5_id'] : '',
        ];
        
        $form['employer6_id'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Employer 6 ID'),
            '#description' => $this->t('This is the ID of this employer'),
            '#default_value' => isset($config['employer6_id']) ? $config['employer6_id'] : '',
        ];
        
        $form['employer7_id'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Employer 7 ID'),
            '#description' => $this->t('This is the ID of this employer'),
            '#default_value' => isset($config['employer7_id']) ? $config['employer7_id'] : '',
        ];
        
        $form['employer8_id'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Employer 8 ID'),
            '#description' => $this->t('This is the ID of this employer'),
            '#default_value' => isset($config['employer8_id']) ? $config['employer8_id'] : '',
        ];
        
        $form['employer9_id'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Employer 9 ID'),
            '#description' => $this->t('This is the ID of this employer'),
            '#default_value' => isset($config['employer9_id']) ? $config['employer9_id'] : '',
        ];
        
        $form['employer10_id'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Employer 10 ID'),
            '#description' => $this->t('This is the ID of this employer'),
            '#default_value' => isset($config['employer10_id']) ? $config['employer10_id'] : '',
        ];
        

        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function blockSubmit($form, FormStateInterface $form_state)
    {
        parent::blockSubmit($form, $form_state);

        $this->setConfigurationValue('employer1_id', $form_state->getValue('employer1_id'));
        $this->setConfigurationValue('employer2_id', $form_state->getValue('employer2_id'));
        $this->setConfigurationValue('employer3_id', $form_state->getValue('employer3_id'));
        $this->setConfigurationValue('employer4_id', $form_state->getValue('employer4_id'));
        $this->setConfigurationValue('employer5_id', $form_state->getValue('employer5_id'));
        $this->setConfigurationValue('employer6_id', $form_state->getValue('employer6_id'));
        $this->setConfigurationValue('employer7_id', $form_state->getValue('employer7_id'));
        $this->setConfigurationValue('employer8_id', $form_state->getValue('employer8_id'));
        $this->setConfigurationValue('employer9_id', $form_state->getValue('employer9_id'));
        $this->setConfigurationValue('employer10_id', $form_state->getValue('employer10_id'));
    }


    /**
     * {@inheritdoc}
     */
    public function blockValidate($form, FormStateInterface $form_state)
    {

        if (empty($form_state->getValue('employer1_id'))) {
            $form_state->setErrorByName('employer1_id', t('This field is required'));
        }
        
        if (empty($form_state->getValue('employer2_id'))) {
            $form_state->setErrorByName('employer2_id', t('This field is required'));
        }
        
        if (empty($form_state->getValue('employer3_id'))) {
            $form_state->setErrorByName('employer3_id', t('This field is required'));
        }
        
        if (empty($form_state->getValue('employer4_id'))) {
            $form_state->setErrorByName('employer4_id', t('This field is required'));
        }
        
        if (empty($form_state->getValue('employer5_id'))) {
            $form_state->setErrorByName('employer5_id', t('This field is required'));
        }
        
        if (empty($form_state->getValue('employer6_id'))) {
            $form_state->setErrorByName('employer6_id', t('This field is required'));
        }
        
        if (empty($form_state->getValue('employer7_id'))) {
            $form_state->setErrorByName('employer7_id', t('This field is required'));
        }
        
        if (empty($form_state->getValue('employer8_id'))) {
            $form_state->setErrorByName('employer8_id', t('This field is required'));
        }
        
        if (empty($form_state->getValue('employer9_id'))) {
            $form_state->setErrorByName('employer9_id', t('This field is required'));
        }
        
        if (empty($form_state->getValue('employer10_id'))) {
            $form_state->setErrorByName('employer10_id', t('This field is required'));
        }
        
    }

    /**
     * {@inheritdoc}
     * return 0 If you want to disable caching for this block.
     */
    public function getCacheMaxAge()
    {

        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function access(AccountInterface $account, $return_as_object = FALSE)
    {
        return AccessResult::allowedIfHasPermission($account, 'access content');
    }

}