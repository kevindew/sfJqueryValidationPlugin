<?php
/**
 * BasesfJqueryValidation actions
 *
 * @package     sfJqueryValidationPlugin
 * @subpackage  Controller
 * @author      Kevin Dew  <kev@dewsolutions.co.uk>
 */
class BasesfJqueryValidationActions extends sfActions
{
  /**
   * @return  void
   *
   * @see     sfAction
   */
  public function preExecute()
  {
    sfConfig::set('sf_web_debug', false);
    $this->setTemplate('asset');
    $this->getResponse()->setContentType('application/x-javascript');

    // cache
    //sfJqueryValidationUtility::setCacheHeaders($this->getResponse());

    // gzip
    //sfJqueryValidationUtility::setGzip();
  }


  /**
   * @see sfActions::execute
   */
  public function executeIndex()
  {

    $this->assets = "Console.log('test')";
  }
}