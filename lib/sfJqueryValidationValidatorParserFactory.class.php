<?php

class sfJqueryValidationValidatorParserFactory
{
  protected $_field;

  protected $_validator;

  protected $_parserClassPrefixes = array(
      'sfJqueryValidationParser'
  );

  public function __construct(sfFormField $field, sfValidatorBase $validator)
  {
    $this
      ->setField($field)
      ->setValidator($validator)
    ;
  }

  public function setField(sfFormField $field)
  {
    $this->_field = $field;
    return $this;
  }

  public function getField()
  {
    return $this->_field;
  }

  public function setValidator(sfValidatorBase $validator)
  {
    $this->_validator = $validator;
    return $this;
  }

  public function getValidator()
  {
    return $this->_validator;
  }

  public function setParserClassPrefixes(array $parserClassPrefixes)
  {
    $this->_parserClassPrefixes = $parserClassPrefixes;
    return $this;
  }  
  
  public function getParserClassPrefixes()
  {
    return $this->_parserClassPrefixes();
  }

  public function getParser()
  {
    $validatorClass = get_class($this->getValidator());

    $parserClass = false;

    while($validatorClass)
    {
      $parserClass = $this->_checkParserClass($validatorClass);
      if ($parserClass)
      {
        break;
      }
      $validatorClass = get_parent_class($validatorClass);
    }

    if (!$parserClass)
    {
      throw new Exception('Parser class for validator could not be found');
    }

    $parser = new $parserClass($this->getField(), $this->getValidator());

    return $parser;
  }

  protected function _checkParserClass($validatorClass)
  {
    foreach ($this->_parserClassPrefixes as $prefix)
    {

      // should probably use a interface too
      if (class_exists($prefix . $validatorClass))
      {
        return $prefix . $validatorClass;
      }
    }
    
    return false;
  }
}
