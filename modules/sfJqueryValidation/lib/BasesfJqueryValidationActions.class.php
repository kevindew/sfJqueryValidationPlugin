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
  public function executeIndex(sfWebRequest $request)
  {
    $reflection = new ReflectionClass($request->getParameter('form'));

    if (!$reflection->implementsInterface('sfFormJqueryValidationInterface'))
    {
      throw new Exception('Requested class does not implement interface');
    }

    $form = $reflection->newInstance();

    if (!$form instanceof sfForm)
    {
      throw new Exception('Requested class does not inherit from sfForm');
    }

    $form->getWidgetSchema()->setNameFormat($request->getParameter('name_format'));
    $form->getWidgetSchema()->setIdFormat($request->getParameter('id_format'));

    $javascript = $form->getJqueryValidationGenerator()->generateJavascript();

    return $this->renderText($javascript);
  }
}