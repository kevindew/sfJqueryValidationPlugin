<?php
class sfJqueryValidationParsersfValidatorBase
{

  protected $_field;
  protected $_validator;
  protected $_rules = array();

  public function __construct(sfFormField $field, sfValidatorBase $validator)
  {
    $this->setField($field);
    $this->setValidator($validator);
    $this->_generateRules();
  }

  public function getField()
  {
    return $this->_field;
  }

  public function setField(sfFormField $field)
  {
    $this->_field = $field;
    return $this;
  }

  public function getValidator()
  {
    return $this->_validator;
  }

  public function setValidator(sfValidatorBase $validator)
  {
    $this->_validator = $validator;
    return $this;
  }

  public function getRules()
  {
    return $this->_rules;
  }

  public function setRules(array $rules)
  {
    $this->_rules = $rules;
    return $this;
  }

  public function addRule($name, sfJqueryValidationValidatorRule $rule)
  {
    $rules = $this->getRules();
    $rules[$name] = $rule;
    $this->setRules($rules);

    return $this;
  }

  public function removeRule($name)
  {
    $rules = $this->getRules();
    unset($rules[$name]);
    $this->setRules($rules);

    return $this;
  }

  protected function _generateRules()
  {
    if ($this->getValidator()->getOption('required'))
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
