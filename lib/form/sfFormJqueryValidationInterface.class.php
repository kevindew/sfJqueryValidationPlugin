<?php

interface sfFormJqueryValidationInterface
{
  public function getGlobalErrorMessage();

  public function setGlobalErrorMessage($globalErrorMessage);

  public function getUseFieldErrorClassServerSide();

  public function setUseFieldErrorClassServerSide($useFieldErrorClassServerSide);

  public function getUseFieldValidClassServerSide();

  public function setUseFieldValidClassServerSide($useFieldValidClassServerSide);

  public function getUseValidClassOnEmptyFields();

  public function setUseValidClassOnEmptyFields($useValidClassOnEmptyFields);

  public function getJqueryValidationScriptPath();

  public function setJqueryValidationScriptPath($jqueryValidationScriptPath);

  public function getValidationScriptPath();

  public function getUseJqueryValidation();

  public function setUseJqueryValidation($useJqueryValidation);

  public function getJqueryValidationGenerator();

  public function setJqueryValidationGenerator(
    sfJqueryValidationGenerator $jqueryValidationGenerator
  );
}
