<?php
/**
 * sfJqueryValidationValidatorRuleSchema
 *
 * Stores a collection of sfJqueryValidationValidatorRule
 *
 * @package     sfJqueryValidationPlugin
 * @subpackage  ValidatorRuleSchema
 * @author      Kevin Dew  <kev@dewsolutions.co.uk>
 */
class sfJqueryValidationValidatorRuleSchema 
  extends sfJqueryValidationValidatorRule
{
//  protected $_rules = array();

  protected $_name;
  protected $_field;
  protected $_validators = array();

  public function __construct(
    $name, sfFormField $field, array $validators)
  {
    $this
      ->setName($name)
      ->setField($field)
      ->setValidators($validators)
    ;
  }

  public function getName()
  {
    return $this->_name;
  }

  public function setName($name)
  {
    $this->_name = $name;
    return $this;
  }

  public function getField()
  {
    return $this->_field;
  }

  public function setField($field)
  {
    $this->_field = $field;
    return $this;
  }

  public function getValidators()
  {
    return $this->_validators;
  }

  public function setValidators($validators)
  {
    $this->_validators = $validators;
    return $this;
  }



//  /**
//   * @return  array
//   */
//  public function getRules()
//  {
//    return $this->_rules;
//  }
//
//  /**
//   * @param   array $rules
//   * @return  self
//   */
//  public function setRules(array $rules)
//  {
//    $this->_rules = $rules;
//    return $this;
//  }

  public function getRule($indent = '      ')
  {
    $parsers = array();

    foreach ($this->getValidators() as $validator) {
      $parserFactory = new sfJqueryValidationValidatorParserFactory(
        $this->getName(),
        $this->getField(),
        $validator
      );

      $parsers[] = $parserFactory->getParser();
    }

    $ruleCollection = array();

    foreach ($parsers as $parser)
    {
      $rules = array();

      foreach ($parser->getRulesByName($this->getName()) as $name => $rule)
      {
        $rules[$name] = $rule->getRule();
      }

      if ($rules)
      {
        $ruleCollection[] =
          sfJqueryValidationGenerator::generateJavascriptObject($rules, $indent)
        ;
      }
    }

    return '[' . implode(', ', $ruleCollection) . ']';
  }

  public function  getMessage()
  {
    return '"invalid"';
  }
}
