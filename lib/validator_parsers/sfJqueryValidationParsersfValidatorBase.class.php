<?php
/**
 * sfJqueryValidationParsersfValidationBase
 *
 * jQuery validation parser for sfValidatorBase
 *
 * This is the base rule that other validators should subclass
 *
 * @package     sfJqueryValidationPlugin
 * @subpackage  Parser
 * @author      Kevin Dew  <kev@dewsolutions.co.uk>
 */
class sfJqueryValidationParsersfValidatorBase
{
  /**
   * @var   sfFormField
   */
  protected $_field;

  /**
   * @var   sfValidatorBase
   */
  protected $_validator;

  /**
   * The rules for this field
   *
   * @var   array
   */
  protected $_rules = array();

  /**
   * Builds a parser
   *
   * @param sfFormField $field
   * @param sfValidatorBase $validator
   */
  public function __construct(sfFormField $field, sfValidatorBase $validator)
  {
    $this->setField($field);
    $this->setValidator($validator);
    $this->_generateRules();
  }

  /**
   * @return  sfFormField
   */
  public function getField()
  {
    return $this->_field;
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
   * @return  sfValidatorBase
   */
  public function getValidator()
  {
    return $this->_validator;
  }

  /**
   * @param   sfValidatorBase $validator
   * @return  self
   */
  public function setValidator(sfValidatorBase $validator)
  {
    $this->_validator = $validator;
    return $this;
  }

  /**
   * @return  array
   */
  public function getRules()
  {
    return $this->_rules;
  }

  /**
   * @param   array $rules
   * @return  self
   */
  public function setRules(array $rules)
  {
    $this->_rules = $rules;
    return $this;
  }

  /**
   * Add a rule
   *
   * @param   string                          $name
   * @param   sfJqueryValidationValidatorRule $rule
   * @return  self
   */
  public function addRule($name, sfJqueryValidationValidatorRule $rule)
  {
    $rules = $this->getRules();
    $rules[$name] = $rule;
    $this->setRules($rules);

    return $this;
  }

  /**
   * Removes a rule
   *
   * @param   string  $name
   * @return  self
   */
  public function removeRule($name)
  {
    $rules = $this->getRules();
    unset($rules[$name]);
    $this->setRules($rules);

    return $this;
  }

  /**
   * Generate the rules for this field
   *
   * @return  void
   */
  protected function _generateRules()
  {
    if ($this->getValidator()->hasOption('required'))
    {
      $this->addRule(
        'required',
        new sfJqueryValidationValidatorRule(
          'true',
           $this->getValidator()->getMessage('required'),
           sfJqueryValidationValidatorRule::STR_RAW
        )
      );
    }
  }
}
