<?php namespace WSACurlSoapClient;

use CurlSoapClient\CurlSoapClient;
use Monolog\Logger;
use WS\WSASoap;

/**
* A wrapper around \SoapClient that uses cURL to make the requests
*/
class WSECurlSoapClient extends CurlSoapClient
{

  const ADD_ACTION    = "addaction";
  const ADD_TO        = "addto";
  const ADD_MESSAGE_ID = "addmessageid";
  const ADD_REPLY_TO   = "addreplyto";

  
  protected $wseOptions = [
    self::ADD_ACTION => true,
    self::ADD_TO => true,
    self::ADD_MESSAGE_ID => true,
    self::ADD_REPLY_TO => true
  ];
  
  function __construct($wsdl, array $options, array $curlOptions, Logger $logger, array $wseOptions = []) {
    $this->wseOptions += $wseOptions;
    
    parent::__construct($wsdl, $options, $curlOptions, $logger);
  }
  
  /**
   * We override this function from parent to use WS-Security.
   *
   * @param string $request 
   * @param string $location 
   * @param string $action 
   * @param string $version 
   * @param string $one_way 
   * @return string
   * @author Peter Haza
   */
  public function __doRequest($request, $location, $action, $version, $one_way = 0) {
    
    $dom = new \DOMDocument();
    $dom->loadXML($request);
    
    $wsa = new WSASoap($dom);
    
    if($this->wseOptions[self::ADD_ACTION]) {
      $wsa->addAction($action);
    }
    if($this->wseOptions[self::ADD_TO]) {
      $wsa->addTo($location);
    }
    if($this->wseOptions[self::ADD_MESSAGE_ID]) {
      $wsa->addMessageID();
    }
    if($this->wseOptions[self::ADD_REPLY_TO]) {
      $wsa->addReplyTo();
    }

    $request = $wsa->saveXML();
    
    return parent::__doRequest($request, $location, $action, $version, $one_way);    
  }
}
