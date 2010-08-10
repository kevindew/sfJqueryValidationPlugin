<?php
/**
 * A Interface to integrate a form with jQuery Validation
 *
 * @package     sfJqueryValidationPlugin
 * @subpackage  form
 * @author      Kevin Dew <kev@dewsolutions.co.uk>
 */
interface sfFormJqueryValidationInterface
{
  /**
   * Get whether to use the error class on a field server side
   *
   * @return  bool
   */
  public function getUseFieldErrorClassServerSide();

  /**
   * Set whether to use the error class on a field server side
   *
   * @param   $useFieldErrorClassServerSide   bool
   *
   * @return  self
   */
  public function setUseFieldErrorClassServerSide($useFieldErrorClassServerSide);

  /**
   * Get whether to use the valid class on a field server side
   *
   * @return  bool
   */
  public function getUseFieldValidClassServerSide();

  /**
   * Set whether to use the valid class on a field server side
   *
   * @param   $useFieldValidClassServerSide   bool
   *
   * @return  self
   */
  public function setUseFieldValidClassServerSide($useFieldValidClassServerSide);

  /**
   * Get whether to use the valid class on empty fields
   *
   * requires getUseFieldValidClassServerSide() to return true
   *
   * @return  bool
   */
  public function getUseValidClassOnEmptyFields();

  /**
   * Set whether to use the valid class on empty fields
   *
   * requires getUseFieldValidClassServerSide() to return true
   *
   * @param   $useValidClassOnEmptyFields   bool
   *
   * @return  self
   */
  public function setUseValidClassOnEmptyFields($useValidClassOnEmptyFields);

  /**
   * Return the path to the validation script
   *
   * @return  string
   */
  public function getValidationScriptPath();

  /**
   * Get whether to use jQuery validation on the form or not
   *
   * @return  bool
   */
  public function getUseJqueryValidation();

  /**
   * Set whether to use jQuery validation on the form or not
   *
   * @param   $useJqueryValidation
   *
   * @return  bool
   */
  public function setUseJqueryValidation($useJqueryValidation);

  /**
   * Get the instance of sfJqueryValidationGenerator
   *
   * @return  sfJqueryValidationGenerator
   */
  public function getJqueryValidationGenerator();

  /**
   * Set the instance of jqueryValidationGenerator
   *
   * @param   sfJqueryValidationGenerator  $jqueryValidationGenerator
   *
   * @return  self
   */
  public function setJqueryValidationGenerator(
    sfJqueryValidationGenerator $jqueryValidationGenerator
  );

  /**
   * Method for processing the jQuery validation
   *
   * @return  void
   */
  public function doGenerateJqueryValidation();

  /**
   * Get the global error message for the form
   *
   * @return  string
   */
  public function getGlobalErrorMessage();

  /**
   * Set the global error message for the form
   *
   * @param   string  $globalErrorMessage
   * @return  self
   */
  public function setGlobalErrorMessage($globalErrorMessage);
}
