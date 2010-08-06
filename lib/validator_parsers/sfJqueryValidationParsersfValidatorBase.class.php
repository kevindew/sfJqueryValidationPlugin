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
   * An array of javascripts
   *
   * @var   array
   */
  protected $_javascripts = array();

  /**
   * An array of stylesheets
   *
   * @var   array
   */
  protected $_stylesheets = array();

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

  /**
   *
   */
  public static function generateMessageJsFunctionReplace(
    $message,
    array $replace
  )
  {
    $message = '"' . addcslashes($message, '"') . '"';

    // see if theres actually any values to replace otherwise no need for a
    // function
    $match = false;
    foreach (array_keys($replace) as $check)
    {
      if (strpos($message, $check) !== false)
      {
        $match = true;
        break;
      }
    }

    if (!$match)
    {
      return $message;
    }

    foreach($replace as $key => $value)
    {
      // add string concatentation
      $replace[$key] = '" + ' . $value . ' + "';
    }

    $message = str_replace(array_keys($replace), $replace,$message);
    
    return 'function(ruleParams, element) {return ' . $message . ';}';
  }

  /**
   * Get an array of javascript paths
   *
   * @return  array
   */
  public function getJavascripts()
  {
    return $this->_javascripts;
  }

  /**
   * Set an array of javascript paths
   *
   * @param   array   $javascripts
   *
   * @return  self
   */
  public function setJavascripts($javascripts)
  {
    $this->_javascripts = $javascripts;
    return $this;
  }

  /**
   * Get an array of stylesheet paths
   *
   * @return  array
   */
  public function getStylesheets()
  {
    return $this->_stylesheets;
  }

  /**
   * Get an array of stylesheet paths
   *
   * @return  array
   */
  public function setStylesheets($stylesheets)
  {
    $this->_stylesheets = $stylesheets;
    return $this;
  }
  
}
