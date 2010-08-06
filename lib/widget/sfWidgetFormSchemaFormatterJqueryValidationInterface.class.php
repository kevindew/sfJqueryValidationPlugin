<?php

interface sfWidgetFormSchemaFormatterJqueryValidationInterface
{
  public function getFieldErrorClass();

  public function setFieldErrorClass($fieldErrorClass);

  public function getFieldValidClass();

  public function setFieldValidClass($fieldValidClass);

  public function getGlobalErrorFormat();

  public function setGlobalErrorFormat($globalErrorFormat);
//
//  public function getErrorPlacement();
//
//  public function setErrorPlacement($errorPlacement);
//
//  public function getShowTopError();
//
//  public function setShowTopError($showTopError);
//

  public function setJqueryValidationErrorElement($jqueryValidationErrorElement);

  public function getJqueryValidationErrorElement();

  public function setJqueryValidationWrapper($jqueryValidationWrapper);

  public function getJqueryValidationWrapper();


}
