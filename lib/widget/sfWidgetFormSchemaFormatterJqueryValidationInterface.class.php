<?php
/**
 * A Interface to integrate a widget schema form formatter with jQuery Validation
 *
 * @package     sfJqueryValidationPlugin
 * @subpackage  form
 * @author      Kevin Dew <kev@dewsolutions.co.uk>
 */

interface sfWidgetFormSchemaFormatterJqueryValidationInterface
{
  /**
   * Get the error class for a field
   *
   * @return  string
   */
  public function getFieldErrorClass();

  /**
   * Set the error class for a field
   *
   * @param   string  $fieldErrorClass
   * @return  this
   */
  public function setFieldErrorClass($fieldErrorClass);

  /**
   * Get the valid class for a field
   *
   * @return  string
   */
  public function getFieldValidClass();

  /**
   * Set the valid class for a field
   *
   * @param   string  $fieldValidClass
   * @return  this
   */
  public function setFieldValidClass($fieldValidClass);

  /**
   * Get the formatting for the global error (error at top of form)
   *
   * @return  string
   */
  public function getGlobalErrorFormat();

  /**
   * Set the formatting for the global error (error at top of form)
   *
   * @param   string  $globalErrorFormat
   * @return  self
   */
  public function setGlobalErrorFormat($globalErrorFormat);

  /**
   * Get error element string for outputting in jQuery validation
   *
   * @return  string
   */
  public function getJqueryValidationErrorElement();

  /**
   * Set error element string for outputting in jQuery validation
   *
   * @param   string  $jqueryValidationErrorElement
   * @return  self
   */
  public function setJqueryValidationErrorElement($jqueryValidationErrorElement);

  /**
   * Get wrapper string for outputting in jQuery validation
   *
   * @return  string
   */
  public function getJqueryValidationWrapper();

  /**
   * Set wrapper string for outputting in jQuery validation
   *
   * @param   string  $jqueryValidationWrapper
   * @return  self
   */
  public function setJqueryValidationWrapper($jqueryValidationWrapper);


  /**
   * Get the body for the callback method
   *
   * note function header and footer is in sfJqueryValidationGenerator
   *
   * @return  string
   */
  public function getJqueryValidationSubmitHandlerCallback();

  /**
   * Set the body for the callback method
   *
   * note function header and footer is in sfJqueryValidationGenerator
   *
   * @param   string
   * @return  self
   */
  public function setJqueryValidationSubmitHandlerCallback(
    $jqueryValidationSubmitHandlerCallback
  );

  /**
   * @see     self::getJqueryValidationSubmitHandlerCallback
   * @return  string
   */
  public function getJqueryValidationInvalidHandlerCallback();

  /**
   * @see     self::getJqueryValidationSubmitHandlerCallback
   * @param   string
   * @return  self
   */
  public function setJqueryValidationInvalidHandlerCallback(
    $jqueryValidationInvalidHandlerCallback
  );

  /**
   * @see     self::getJqueryValidationSubmitHandlerCallback
   * @return  string
   */
  public function getJqueryValidationShowErrorsCallback();

  /**
   * @see     self::getJqueryValidationSubmitHandlerCallback
   * @param   string
   * @return  self
   */
  public function setJqueryValidationShowErrorsCallback(
    $jqueryValidationShowErrorsCallback
  );

  /**
   * @see     self::getJqueryValidationSubmitHandlerCallback
   * @return  string
   */
  public function getJqueryValidationErrorPlacementCallback();

  /**
   * @see     self::getJqueryValidationSubmitHandlerCallback
   * @param   string
   * @return  self
   */
  public function setJqueryValidationErrorPlacementCallback(
    $jqueryValidationErrorPlacementCallback
  );

  /**
   * @see     self::getJqueryValidationSubmitHandlerCallback
   * @return  string
   */
  public function getJqueryValidationHighlightCallback();

  /**
   * @see     self::getJqueryValidationSubmitHandlerCallback
   * @param   string
   * @return  self
   */
  public function setJqueryValidationHighlightCallback(
    $jqueryValidationHighlightCallback
  );

  /**
   * @see     self::getJqueryValidationSubmitHandlerCallback
   * @return  string
   */
  public function getJqueryValidationUnhighlightCallback();

  /**
   * @see     self::getJqueryValidationSubmitHandlerCallback
   * @param   string
   * @return  self
   */
  public function setJqueryValidationUnhighlightCallback(
    $jqueryValidationUnhighlightCallback
  );
}
