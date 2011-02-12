<?php
/**
 * ESI view helper that generates ESI tags based on a controller and an action.
 * If the predefined request header isn't set, the view helper doesn't render ESI tags,
 * but dispatches the controller and action and returns the dispatched data as a string.
 * 
 * @author Thijs Feryn <thijs@feryn.eu>
 */

/** @see Zend_View_Helper_Abstract */
require_once 'Zend/View/Helper/Abstract.php';

/**
 * ESI view helper
 */
class My_View_Helper_Esi extends Zend_View_Helper_Abstract
{
    /**
     * The header the ESI proxy sends
     *
     * @var string
     */
    protected $_requestHeader = null;
    /**
     * The header we send back to the ESI proxy
     *
     * @var string
     */
    protected $_responseHeader = null;
    /**
     * The front controller instance
     *
     * @var Zend_Controller_Front
     */
    protected $_frontController = null;
    /**
     * Generate an ESI tag if we are behind a proxy or just dispatch and return the data if not
     *
     * @param string $controller
     * @param string $action
     * @param string $requestHeader
     * @param string $responseHeader
     * @return string
     */
    public function esi($controller = 'index', $action='index', $requestHeader = 'X_VARNISH', $responseHeader = 'esi-enabled: 1')
    {
        $this->_requestHeader = $requestHeader;
        $this->_responseHeader = $responseHeader;
        $this->_frontController = Zend_Controller_Front::getInstance();
        
        if($this->_getRequestHeader()){
            $this->_getResponseHeader();
            return "<esi:include src=\"{$this->_buildUrl($controller, $action)}\"/>";
        } else {
            return $this->_dispatch($controller, $action);
        }
    }
    /**
     * Check if we are behind a proxy
     *
     * @return bool
     */
    protected function _getRequestHeader()
    {
        if($this->_requestHeader === null || $this->_requestHeader === false){
            return false;
        }        
        $request = $this->_frontController->getRequest();
        if($request->getHeader($this->_requestHeader) === false){
            return false;
        } else {
            return true;
        }
    }
    /**
     * Indicate that we are ESI aware
     */
    protected function _getResponseHeader()
    {
        if($this->_responseHeader !== null && $this->_responseHeader !== false){
            header($this->_responseHeader);
        }
    }
    /**
     * Dispatch the controller and action if we aren't behind an ESI proxy
     *
     * @param string $controller
     * @param string $action
     * @return string
     */
    protected function _dispatch($controller,$action)
    {
        $request = new Zend_Controller_Request_Simple($action, $controller);
        $response = new Zend_Controller_Response_Http();
        $dispatcher = $this->_frontController->getDispatcher();
        $dispatcher->dispatch($request, $response);
        return $response->getBody();
    }
    /**
     * Generate the ESI source URL
     *
     * @param string $controller
     * @param string $action
     * @return string
     */
    protected function _buildUrl($controller,$action)
    {
        $router = $this->_frontController->getRouter();
        return $router->assemble(array('controller'=>$controller,'action'=>$action));
    }
}
