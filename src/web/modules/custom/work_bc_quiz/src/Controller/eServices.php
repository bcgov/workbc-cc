<?php
namespace Drupal\work_bc_quiz\Controller;

use Drupal\Core\Controller\ControllerBase;
require_once(DRUPAL_ROOT . '/modules/custom/work_bc_quiz/config/common.inc');

class eServices extends ControllerBase{
  function __construct() {
    
  }
  function fnGetService($service, $action, $parameters){
    
    if(is_file(DRUPAL_ROOT . '/modules/custom/work_bc_quiz/config/config.ini')){  
      $host = (!isset($_COOKIE['Drupal_visitor_environment']) && !defined('Drupal_visitor_environment')) ? fnGetEnvironment() : ((isset($_COOKIE['Drupal_visitor_environment'])) ? $_COOKIE['Drupal_visitor_environment'] : Drupal_visitor_environment);
      
      $config = parse_ini_file(DRUPAL_ROOT . '/modules/custom/work_bc_quiz/config/config.ini', true);
      if(isset($action) && !empty($action)) {
				if( isset($parameters) ) {
					return (isset($config[$service])) ? $config[$service][$host] .'/'. $config['SERVICES'][$action] . '?' .$parameters : $service .'/'. $config['SERVICES'][$action];
				} else {
					
				  return (isset($config[$service])) ? $config[$service][$host] .'/'. $config['SERVICES'][$action] : $service .'/'. $config['SERVICES'][$action];
				}
			}
			else {
				return $config[$service][$host];
			}
    }
    else{
			return false;
		}
  }
  function fnGetCurlRequest($action, $parameters = null, $get_vars = false, $cid = NULL, $cacheExpire = 7200, $cookie_vals = '', $ret_cookies = false){
    $host = (!isset($_COOKIE['Drupal_visitor_environment']) && !defined('Drupal_visitor_environment')) ? fnGetEnvironment() : ((isset($_COOKIE['Drupal_visitor_environment'])) ? $_COOKIE['Drupal_visitor_environment'] : Drupal_visitor_environment);
    
    $site_settings = \Drupal::service('site_settings.loader');
    $settings = $site_settings->loadAll();
    
    $user = (isset($settings['onet_credentials']['onet']['field_username'])) ? $settings['onet_credentials']['onet']['field_username'] : '';
    $password = (isset($settings['onet_credentials']['onet']['field_secret'])) ? $settings['onet_credentials']['onet']['field_secret'] : '';
    $url = $this->fnGetService('WS-HOSTS', $action, $parameters);
    if(!empty($user) && !empty($password)){
      $url = str_replace(['@user', '@pass'], [$user, $password], $url);
    }
    $r = array();
		if(!empty($cookie_vals) && is_array($cookie_vals)){
		  $cookie = implode('&', $cookie_vals);
		}
		else
		{
		  if(!empty($cookie_vals)){
			$cookie = $cookie_vals;
		  }
		  else
		  {
			$cookie = NULL;
		  }
		}
    
    if($url){
		  // create a new cURL resource
		  $ch = curl_init();
      
		  // TODO: The services are hosted by xxx and currently need authentication.
		  //$username = 'cgi';
      //$password = '4852wsy';
		  // set URL and other appropriate options
		  #curl_setopt($ch, CURLOPT_USERPWD, $username.':'.$password);
      #curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_NTLM);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array("Accept: application/json"));
      curl_setopt($ch, CURLOPT_HEADER, false);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_URL, $url);
		 

		  //try & catch for curl request to get url
		  try {
        
        // grab URL and pass it to the browser
        
         $ret = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if($httpcode == 200 ){
          if($get_vars == true){
            $info = curl_getinfo($ch);
            if(isset($info['url'])){
              $url = parse_url($info['url']);

              parse_str($url['query'], $r);

              $r['query_string'] = $r;
            }
          }

          if($ret_cookies == true){
            // get cookie
            preg_match('/^Set-Cookie:\s*([^;]*)/mi', $ret, $m);
            $r['cookies'] = $m;
          }
          $r['response'] = $ret;
          
          return $r;
        }
        return false;
		  }
		  catch (Exception $e) {
        return false;
		  }
		} else{
		  //$r = fnGetCache($cid);
		}
		return $r;
  }
  function fnGetCodes($parameters='') {
    return $this->fnGetCurlRequest('GET_CODES', $parameters , false);
  }
}