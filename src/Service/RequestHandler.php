<?php

namespace Drupal\protect_before_launch\Service;

use Drupal\Core\Render\HtmlResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Class RequestHandler.
 *
 * @package Drupal\protect_before_launch
 */
class RequestHandler implements HttpKernelInterface {


  /**
   * @var \Drupal\protect_before_launch\Service\Configuration
   */
  protected $config = null;

  /**
   * @var HttpKernelInterface
   */
  protected $httpKernel = null;

  public function __construct(HttpKernelInterface $httpKernel, Configuration $config) {
    $this->httpKernel = $httpKernel;
    $this->config = $config;
  }

  protected function shieldPage(){
    return $this->config->getProtect() ? true : false;
  }

  protected function excludedPath(Request $request){
    $currentPath = urldecode($request->getRequestUri());
    foreach ($this->config->getExcludePaths() as $path){
      if(preg_match('/' . str_replace('/', '\/', $path) . '/i', $currentPath)){
        return true;
      }
    }
    return false;
  }

  protected function isAllowed(Request $request, HtmlResponse $response){
    if($this->shieldPage() && !$this->excludedPath($request) && !$this->config->validate($request->getUser(), $request->getPassword())){
      $response->headers->add(['WWW-Authenticate' => 'Basic realm="' . $this->config->getRealm()  . '"']);
      $response->setStatusCode(401, 'Unauthorized');
      $response->setContent($this->config->getContent());
    }
    return $response;
  }

  public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = TRUE) {
    /** @var \Drupal\Core\Render\HtmlResponse $response */
    $response = $this->httpKernel->handle($request, $type, $catch);
    if('cli' != php_sapi_name() && get_class($response) == 'Drupal\Core\Render\HtmlResponse'){
      $response = $this->isAllowed($request, $response);
    }
    return $response;
  }
}