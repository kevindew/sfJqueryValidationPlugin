<?php
/**
 * sfJqueryValidationValidatorParserFactory
 *
 * Takes a field and a validator and pops out a parser for the validator
 *
 * @package     sfJqueryValidationPlugin
 * @subpackage  Parser Factory
 * @author      Kevin Dew  <kev@dewsolutions.co.uk>
 */
class sfJqueryValidationValidatorParserFactory
{
  /**
   * Form field
   *
   * @var sfFormField
   */
  protected $_field;

  /**
   * Validator
   *
   * @var sfValidatorBase
   */
  protected $_validator;

  /**
   * An array of prefixs that will be used to find the parser class
   *
   * @var array
   */
  protected $_parserClassPrefixes = array(
      'sfJqueryValidationParser'
  );

  /**
   * @param   sfFormField     $field
   * @param   sfValidatorBase $validator
   *
   * @return  void
   */
  public function __construct(sfFormField $field, sfValidatorBase $validator)
  {
    $this
      ->setField($field)
      ->setValidator($validator)
    ;
  }

  /**
   * @param   sfFormField   $field
   * @return  self
   */
  public function setField(sfFormField $field)
  {
    $this->_field = $field;
    return $this;
  }

  /**
   * @return  sfFormField
   */
  public function getField()
  {
    return $this->_field;
  }

  /**
   * @param   sfValidatorBase   $validator
   * @return  self
   */
  public function setValidator(sfValidatorBase $validator)
  {
    $this->_validator = $validator;
    return $this;
  }

  /**
   * @return  sfValidatorBase
   */
  public function getValidator()
  {
    return $this->_validator;
  }

  /**
   * @see     self::$_parserClassPrefixes
   * @param   array $parserClassPrefixes
   * @return  self
   */
  public function setParserClassPrefixes(array $parserClassPrefixes)
  {
    $this->_parserClassPrefixes = $parserClassPrefixes;
    return $this;
  }  

  /**
   * @see     self::$_parserClassPrefixes
   * @return  array
   */
  public function getParserClassPrefixes()
  {
    return $this->_parserClassPrefixes();
  }

  /**
   * Get the parser class for the validator
   *
   * @return  mixed
   */
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


  /**
   * Check if a parser class exists
   *
   * @param   string    $validatorClass
   * @return  string|false
   */
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
