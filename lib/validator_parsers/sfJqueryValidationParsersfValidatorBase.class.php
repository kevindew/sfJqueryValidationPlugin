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
  
  const PRINT_JQUERY_VALUE
    = 'jQuery(\'<div />\').text(jQuery(element).val()).html()';

  /**
   * Input name field
   *
   * @var string
   */
  protected $_name;
  
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
   * Stored in format of:
   * array(
   *  fieldName => array(
   *    ruleName => ruleObject
   *  )
   * )
   *
   * @var   array
   */
  protected $_rules = array();

  /**
   * The groups for this field
   *
   * Stored in format of: 
   * array(
   *  groupName => array(
   *    fieldName1,
   *    fieldName2,
   *    etc
   *  )
   * )
   *
   * @var   array
   */
  protected $_groups = array();

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
   * @param   string          $name
   * @param   sfFormField     $field
   * @param   sfValidatorBase $validator
   * @return  void
   */
  public function __construct($name, sfFormField $field, sfValidatorBase $validator)
  {
    $this
      ->setName($name)
      ->setField($field)
      ->setValidator($validator)
      ->configure()
    ;
    $this->_generateRules();
  }

  /**
   * Method to hook into 
   *
   * @return void
   */
  public function configure()
  {
  }

  /**
   * @param   string  $name
   * @return  self
   */
  public function setName($name)
  {
    $this->_name = (string) $name;
    return $this;
  }

  /**
   * @return  string
   */
  public function getName()
  {
    return $this->_name;
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
   * Get the rules for a particular field name
   *
   * @param   string  $name
   * @return  array
   */
  public function getRulesByName($name = null)
  {
    if ($name === null)
    {
      $name = $this->getName();
    }

    $existingRules = $this->getRules();

    return isset($existingRules[$name]) ? $existingRules[$name] : array();
  }

  /**
   * Set the rules for a particular field name
   *
   * @param   array   $rules
   * @param   string  $name
   * @return  self
   */
  public function setRulesByName(array $rules, $name = null)
  {
    if ($name === null)
    {
      $name = $this->getName();
    }

    $existingRules = $this->getRules();

    $existingRules[$name] = $rules;
    
    $this->setRules($existingRules);

    return $this;
  }

  /**
   * Add a rule
   *
   * @param   string                          $ruleName
   * @param   sfJqueryValidationValidatorRule $rule
   * @return  self
   */
  public function addRule(
    $ruleName,
    sfJqueryValidationValidatorRule $rule,
    $name = null
  )
  {
    $rules = $this->getRulesByName($name);
    $rules[$ruleName] = $rule;
    $this->setRulesByName($rules, $name);

    return $this;
  }

  /**
   * Get a rule
   *
   * @param   string  $name
   * @return  sfJqueryValidationValidatorRule
   * @throws  Exception
   */
  public function getRule($ruleName, $name = null)
  {
    $rules = $this->getRulesByName($name);
    if (!isset($rules[$ruleName]))
    {
      throw new Exception('Rule ' . $ruleName . ' does not exist');
    }

    return $rules[$ruleName];
  }

  /**
   * Removes a rule
   *
   * @param   string  $name
   * @return  self
   */
  public function removeRule($ruleName, $name = null)
  {
    $rules = $this->getRulesByName($name);
    unset($rules[$ruleName]);
    $this->setRulesByName($rules);

    return $this;
  }

  /**
   * @return  array
   */
  public function getGroups()
  {
    return $this->_groups;
  }

  /**
   * @param   array $groups
   * @return  self
   */
  public function setGroups(array $groups)
  {
    $this->_groups = $groups;
    return $this;
  }

  /**
   * Get a group
   *
   * @param   string  $name
   * @return  array
   */
  public function getGroup($name)
  {
    $existingGroups = $this->getGroups();

    return isset($existingGroups[$name]) ? $existingGroups[$name] : array();
  }

  /**
   * Set a group
   *
   * @param   string  $name
   * @param   array   $groups
   * @return  self
   */
  public function setGroup($name, array $groups)
  {
    $existingGroups = $this->getGroups();

    $existingGroups[$name] = $groups;

    $this->setGroups($existingGroups);

    return $this;
  }

  /**
   * Generate the rules for this field
   *
   * @return  void
   */
  protected function _generateRules()
  {
    if (
      $this->getValidator()->hasOption('required')
      &&
      $this->getValidator()->getOption('required')
    )
    {
      $this->addRule(
        'required',
        new sfJqueryValidationValidatorRule(
          'true',
           $this->getValidator()->getMessage('required')
        )
      );
    }
  }

  /**
   * Used to generate a javascript function to return a message.
   *
   * Typical input
   * - $message: "%value% is invalid
   * - $replace: array('%value%' => 'jQuery(element).val()')
   * Output
   * - function(ruleParams, element) { return jQuery(element).val() + " is invalid" }
   *
   * @param   string  $message    The raw message
   * @param   array   $replace
   * @return  string
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
    
    return 'function(ruleParams, element) { return ' . $message . ';}';
  }

  /**
   * Convert an array of values to a javascript array 
   *
   * @param   array   $choices  An array of values to become choices
   * @return  string
   */
  public static function buildChoices(array $choices)
  {
    // turn array into associative array
    return json_encode(array_unique(array_values($choices)));
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
  public function setJavascripts(array $javascripts)
  {
    $this->_javascripts = array_unique($javascripts);
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
   * Set an array of stylesheet paths
   *
   * @param   array   $stylesheets
   *
   * @return  self
   */
  public function setStylesheets(array $stylesheets)
  {
    $this->_stylesheets = array_unique($stylesheets);
    return $this;
  }
  
}
