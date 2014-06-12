<?php

class InlineManual_ApiRequest {

  protected $path;
  protected $params;
  protected $method;
  protected $response;
  protected $response_code;

  public function __construct($path, $params = array(), $method = 'GET') {
    $this->path = $path;
    $this->params = $params;
    $this->method = $method;
  }

  public function send() {
    $this->curlRequest();
    return $this->getResponse();
  }

  public function getResponse() {
    return $this->response;
  }

  public function getResponseCode() {
    return $this->response_code;
  }

  public function getTargetUrl() {
    $url = InlineManual::$api_base . '/' . $this->path;
    if ($this->method == 'GET' && !empty($this->params)) {
      $url .= '?' . http_build_query($this->params);
    }
    return $url;
  }

  protected function curlRequest() {
    $curl = curl_init();
    curl_setopt_array($curl, $this->getCurlOptions());
    $response = curl_exec($curl);

    if ($response === FALSE) {
      $error_code = curl_errno($curl);
      $error_message = curl_error($curl);
      curl_close($curl);
      $message = $this->getCurlErrorMessage($error_code, $error_message);
      throw new InlineManual_ApiConnectionError($message);
    }

    $this->response = json_decode($response);
    $this->response_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    if (isset($this->response->errors) || in_array($this->response_code, array(401, 402, 403, 404))) {
      $message = isset($this->response->errors[0]) ? $this->response->errors[0] : 'Invalid response from API (' . InlineManual::$api_base . ').';
      throw new InlineManual_ApiError($message);
    }

    curl_close($curl);
  }

  protected function getHeaders() {
    $client_info = array(
      'lib_version'  => InlineManual::VERSION,
      'lang'         => 'php',
      'lang_version' => phpversion(),
      'publisher'    => 'InlineManual',
      'uname'        => php_uname()
    );

    $headers = array(
      'X-InlineManual-Client-User-Agent: ' . json_encode($client_info),
      'User-Agent: InlineManual/v1 PhpLib/' . InlineManual::VERSION,
    );

    if (InlineManual::$api_version) {
      $headers[] = 'Api-Version: ' . InlineManual::$api_version;
    }

    return $headers;
  }

  protected function getCurlOptions() {
    $options = array();
    $options[CURLOPT_URL]            = $this->getTargetUrl();
    $options[CURLOPT_HTTPGET]        = 1;
    $options[CURLOPT_RETURNTRANSFER] = TRUE;
    $options[CURLOPT_CONNECTTIMEOUT] = 30;
    $options[CURLOPT_TIMEOUT]        = 80;
    $options[CURLOPT_RETURNTRANSFER] = TRUE;
    $options[CURLOPT_HTTPHEADER]     = $this->getHeaders();

    // See http://unitstep.net/blog/2009/05/05/using-curl-in-php-to-access-https-ssltls-protected-sites/
    if (InlineManual::$verify_ssl_certs) {
      $options[CURLOPT_SSL_VERIFYPEER] = TRUE;
      $options[CURLOPT_SSL_VERIFYHOST] = 2;
      $options[CURLOPT_CAINFO]         = dirname(__FILE__) . '/../data/ca-bundle.crt';
    }
    else {
      $options[CURLOPT_SSL_VERIFYPEER] = FALSE;
    }

    return $options;
  }

  protected function getCurlErrorMessage($error_code, $error_message) {
    $api_base = InlineManual::$api_base;

    switch ($error_code) {
      case CURLE_COULDNT_CONNECT:
      case CURLE_COULDNT_RESOLVE_HOST:
      case CURLE_OPERATION_TIMEOUTED:
        $message = "Could not connect to InlineManual API ($api_base). Please check your internet connection and try again. If this problem persists let us know at support@inlinemanual.com.";
        break;
      case CURLE_SSL_CACERT:
      case CURLE_SSL_PEER_CERTIFICATE:
        $message = "Could not verify InlineManual's SSL certificate. Please make sure that your network is not intercepting certificates. (Try going to $api_base in your browser.) If this problem persists, let us know at support@inlinemanual.com.";
        break;
      default:
        $message = "Unexpected error communicating with InlineManual. If this problem persists, let us know at support@inlinemanual.com.";
    }
    $message .= "\n\n(Network error [errno $error_code]: $error_message)";

    return $message;
  }

}
