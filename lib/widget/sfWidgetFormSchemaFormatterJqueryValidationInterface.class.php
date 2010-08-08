<?php

interface sfWidgetFormSchemaFormatterJqueryValidationInterface
{
  public function getFieldErrorClass();

  public function setFieldErrorClass($fieldErrorClass);

  public function getFieldValidClass();

  public function setFieldValidClass($fieldValidClass);

  public function getGlobalErrorFormat();

  public function setGlobalErrorFormat($globalErrorFormat);

  public function getJqueryValidationSubmitHandlerCallback();

  public function setJqueryValidationSubmitHandlerCallback(
    $jqueryValidationSubmitHandlerCallback
  );

  public function getJqueryValidationInvalidHandlerCallback();

  public function setJqueryValidationInvalidHandlerCallback(
    $jqueryValidationInvalidHandlerCallback
  );

  public function getJqueryValidationShowErrorsCallback();

  public function setJqueryValidationShowErrorsCallback(
    $jqueryValidationShowErrorsCallback
  );

  public function getJqueryValidationErrorPlacementCallback();

  public function setJqueryValidationErrorPlacementCallback(
    $jqueryValidationErrorPlacementCallback
  );

  public function getJqueryValidationHighlightCallback();

  public function setJqueryValidationHighlightCallback(
    $jqueryValidationHighlightCallback
  );

  public function getJqueryValidationUnhighlightCallback();

  public function setJqueryValidationUnhighlightCallback(
    $jqueryValidationUnhighlightCallback
  );

  public function setJqueryValidationErrorElement($jqueryValidationErrorElement);

  public function getJqueryValidationErrorElement();

  public function setJqueryValidationWrapper($jqueryValidationWrapper);

  public function getJqueryValidationWrapper();


}
