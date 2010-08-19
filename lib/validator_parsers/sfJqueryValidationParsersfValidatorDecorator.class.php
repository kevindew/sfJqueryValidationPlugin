<?php
/**
 * sfJqueryValidationParsersfValidationDecorator
 *
 * @package     sfJqueryValidationPlugin
 * @subpackage  Parser
 * @author      Kevin Dew  <kev@dewsolutions.co.uk>
 */
class sfJqueryValidationParsersfValidatorDecorator
  extends sfJqueryValidationParsersfValidatorBase
{
  protected $_parser;

  /**
   * @see   parent
   */
  public function __construct($name, sfFormField $field, sfValidatorBase $validator)
  {
    $factory = new sfJqueryValidationValidatorParserFactory(
      $name, $field, $validator->getValidator()
    );
    
    $this->setParser($factory->getParser());
    parent::__construct($name, $field, $validator);
  }

  /**
   * Set the parser
   *
   * @param   sfJqueryValidationParsersfValidatorBase   $parser
   * @return  self
   */
  public function setParser(sfJqueryValidationParsersfValidatorBase $parser)
  {
    $this->_parser = $parser;
    return $this;
  }

  /**
   * Get the parser
   *
   * @return  sfJqueryValidationParsersfValidatorBase
   */
  public function getParser()
  {
    return $this->_parser;
  }

  protected function _generateRules() {}

  /**
   * @return  array
   */
  public function getRules()
  {
    return $this->getParser()->getRules();
  }

  /**
   * @param   array $rules
   * @return  self
   */
  public function setRules(array $rules)
  {
    $this->getParser()->setRules($rules);
    return $this;
  }
}
