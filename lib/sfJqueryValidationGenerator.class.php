<?php
/**
 * sfJqueryValidationGenerator
 *
 * Takes a form and generates validation for it
 *
 * @package     sfJqueryValidationPlugin
 * @subpackage  Generator
 * @author      Kevin Dew  <kev@dewsolutions.co.uk>
 */
class sfJqueryValidationGenerator
{
 
  /**
   * A string of javascript that is used as a template for what is generated
   *
   * @var string
   */
  protected $_scriptTemplate;

  /**
   * The form object this class will use
   *
   * @var sfFormJqueryValidationInterface
   */
  protected $_form;

  /**
   * The field on the form to use as the id for jQuery Validation
   *
   * @var string|null
   */
  protected $_fieldForId;

  /**
   * Extra javascript set for this instance
   *
   * @var string
   */
  protected $_userJavascript = '';

  /**
   * A collection of rules
   *
   * @var array
   */
  protected $_rules = array();

  /**
   * Whether rules have been generated yet
   *
   * @var boolean
   */
  protected $_rulesGenerated = false;

  /**
   * A collection of options for the jQuery validation
   *
   * @var array
   */
  protected $_options = array();

  /**
   *
   * @param sfFormJqueryValidationInterface $form
   * @param array                           $options options for jQuery Validation
   */
  public function  __construct(
    sfFormJqueryValidationInterface $form,
    array $options = array()
  )
  {
    $this->setForm($form);
    $this->setScriptTemplate($this->getDefaultScriptTemplate());
    $this->setOptions(array_merge($this->getDefaultOptions(), $options));
  }

  /**
   * @see     self::_scriptTemplate
   * @return  string
   */
  public function getScriptTemplate()
  {
    return $this->_scriptTemplate;
  }

  /**
   * @see     self::_scriptTemplate
   * @param   string  $scriptTemplate
   * @return  self
   */
  public function setScriptTemplate($scriptTemplate)
  {
    $this->_scriptTemplate = $scriptTemplate;
    return $this;
  }

  /**
   * @see     self::_form
   * @return  sfFormJqueryValidationInterface
   */
  public function getForm()
  {
    return $this->_form;
  }

  /**
   * @see     self::_form
   * @param   form  sfFormJqueryValidationInterface
   * @return  self
   */
  public function setForm(sfFormJqueryValidationInterface $form)
  {
    $this->_form = $form;
    return $this;
  }

  /**
   * The default script template
   *
   * @return string
   */
  public static function getDefaultScriptTemplate()
  {
    return <<<EOF
(function($) {
  $(document).ready(function() {
    var validator = $('#%form_accessor_id%')
      .parents('form').first()
      .validate(%options%)
    ;

%user_script%
  });
})(jQuery);
EOF;
  }

  /**
   * Generates the javascript rules for the validation
   *
   * @param   array   $extraOptions
   *
   * @return  string
   */
  public function generateJavascript(array $extraOptions = array())
  {

    if (!$this->getRulesGenerated())
    {
      $this->generateRules();
    }

    // no rules
    if (!count($this->getRules()))
    {
      return '';
    }

    $rules = array(
      'rules' => $this->getRulesString(),
      'messages' => $this->getMessagesString()
    );


    $options = array_merge($this->getOptions(), $rules, $extraOptions);


    $script = strtr(
      $this->getScriptTemplate(),
      array(
        '%form_accessor_id%' => addcslashes($this->_getFormAccessorId(), "'"),
        '%user_script%' => $this->getUserJavascript(),
        '%options%' => $this->_generateJavascriptObject($options)
      )
    );

    return $script;
  }

  /**
   * @see     self::_fieldForId
   * @return  string|null
   */
  public function getFieldForId()
  {
    return $this->_fieldForId;
  }

  /**
   * @see     self::_fieldForId
   * @param   $fieldForId string|null
   * @return  self
   */
  public function setFieldForId($fieldForId)
  {
    $this->_fieldForId = $fieldForId;

    return $this;
  }

  /**
   * @see     self::_userJavascript
   * @return  string
   */
  public function getUserjavascript()
  {
    return $this->_userJavascript;
  }

  /**
   * @see     self::_userJavascript
   * @param   $userJavascript string
   * @return  self
   */
  public function setUserJavascript($userJavascript)
  {
    $this->_userJavascript = $userJavascript;

    return $this;
  }

  /**
   * Get the id to use to access the form in javascript
   *
   * @return  string
   */
  protected function _getFormAccessorId()
  {
    if ($this->getFieldForId() === null)
    {
      // guess at form field id
      $this->getForm()->rewind();

      $field = $this->getForm()->current();
    }
    else
    {
      $field = $this->getForm()->offSetGet($this->getFieldForId());
    }

    // field might be field holding more fields
    $field = $this->_getFirstFormField($field);

    return $field->renderId();
  }

  /**
   * Recursive method to loop through sfFormFieldSchema objects to get a
   * sfFormField
   *
   * @param   sfFormField $field
   * @return  sfFormField
   */
  protected function _getFirstFormField(sfFormField $field)
  {
    if ($field instanceof sfFormFieldSchema)
    {
      foreach ($field as $f)
      {
        return $this->_getFirstFormField($f);
      }
    }

    return $field;
  }

  /**
   * Build up validation rules for this form
   *
   * @return  void
   */
  public function generateRules()
  {
    $this->_rules = array();

    $this->_recursiveGenerateFieldsAndValidator(
      $this->getForm()->getFormFieldSchema(),
      $this->getForm()->getValidatorSchema()
    );

    $this->setRulesGenerated(true);
  }

  /**
   * Recursively loop through the form field and validation schema objects
   * until not schema objects and then get validation rules from them
   *
   * @param   sfFormFieldSchema   $formFieldSchema
   * @param   sfValidatorSchema   $validatorSchema
   *
   * @throws  Exception
   *
   * @return  void
   */
  protected function _recursiveGenerateFieldsAndValidator(
    sfFormFieldSchema $formFieldSchema,
    sfValidatorSchema $validatorSchema
  )
  {
    foreach($formFieldSchema as $name => $field)
    {
      if ($field instanceof sfFormFieldSchema)
      {
        // check validator schema
        if (
          !isset($validatorSchema[$name])
          ||
          !$validatorSchema[$name] instanceof sfValidatorSchema
        )
        {
          throw new Exception('Validator isn\'t an instance of sfValidatorSchema');
        }

        return $this->_recursiveGenerateFieldsAndValidator(
          $field,
          $validatorSchema[$name]
        );
      }

      // check validator
      if (
        !isset($validatorSchema[$name])
        ||
        !$validatorSchema[$name] instanceof sfValidatorBase
      )
      {
        throw new Exception('Validation isn\'t an instance of sfValidatorBase');
      }

      $factory = new sfJqueryValidationValidatorParserFactory(
        $field,
        $validatorSchema[$name]
      );
      $parser = $factory->getParser();

      $this->setFieldRules(
        $field->renderName(), $parser->getRules()
      );
    }
  }

  /**
   * Get the rules array
   *
   * in form of array('field' => array('rule' => $ruleObj))
   *
   * @return  array
   */
  public function getRules()
  {
    return $this->_rules;
  }


  /**
   * Set the rules array
   *
   * @param   array   $rules
   * @return  self
   */
  public function setRules(array $rules)
  {
    $this->_rules = $rules;
    return $this;
  }

  /**
   * Whether or not rules have been generated or not
   *
   * @return  boolean
   */
  public function getRulesGenerated()
  {
    return $this->_rulesGenerated;
  }

  /**
   * Set whether rules have been generated or not
   * (setting to false when generated will mean they get regenerated)
   *
   * @param   boolean   $rulesGenerated
   * @return  self
   */
  protected function setRulesGenerated($rulesGenerated)
  {
    $this->_rulesGenerated = (bool) $rulesGenerated;
    return $this;
  }


  /**
   * Set the rules for a field
   *
   * @param   string  $fieldName
   * @param   array   $fieldRules array of rules in form of
   *                              array(name => sfJqueryValidationValidatorRule)
   * @return  self
   */
  public function setFieldRules($fieldName, array $fieldRules)
  {
    $rules = $this->getRules();
    $rules[$fieldName] = $fieldRules;
    $this->setRules($rules);

    return $this;
  }

  /**
   * Merge the rules for a field
   *
   * @param   string  $fieldName
   * @param   array   $fieldRules array of rules in form of
   *                              array(name => sfJqueryValidationValidatorRule)
   * @return  self
   */
  public function mergeFieldRules($fieldName, array $fieldRules)
  {
    $toMerge = $this->getFieldRules($fieldName)
      ? $this->getFieldRules($fieldName)
      : array()
    ;
    $this->setFieldRules($fieldName, array_merge($toMerge, $fieldRules));

    return $this;
  }

  /**
   * Get the rules for a field
   *
   * @param   string      $fieldName
   *
   * @return  array|null  array of rules in form of
   *                      array(name => sfJqueryValidationValidatorRule)
   */
  public function getFieldRules($fieldName)
  {
    $rules = $this->getRules();

    return isset($rules[$fieldName]) ? $rules[$fieldName] : null;
  }

  

  public function addFieldRule(
    $fieldName, $ruleName, sfJqueryValidationValidatorRule $rule)
  {
    $rules = $this->getRules();
    $toMerge = isset($rules[$fieldName]) ? $rules[$fieldName] : array();
    $rules[$fieldName] = array_merge($toMerge, $fieldRules);
    $this->setRules($rules);

    return $this;
  }

  public function removeFieldRule($fieldName, $rule, $message)
  {
    // @todo
  }

  /**
   *
   * @param array $array an array of arrays or strings
   */
  protected function _generateJavascriptObject(array $array)
  {
    $return = array();

    foreach ($array as $key => $value)
    {
      if (is_array($value))
      {
        $string = $this->_generateJavascriptObject($value);
      }
      else
      {
        $string = is_bool($value)
          ? ($value ? 'true' : 'false')
          : (string) $value
        ;
      }

      $return[] = '"' . $key . '": ' . $string;
    }

    return '{' . implode(', ', $return) . '}';
  }

  public function getRulesString()
  {
    $return = array();

    // convert rules into an array
    foreach ($this->getRules() as $field => $rules)
    {
      $rulesArr = array();

      if (!count($rules))
      {
        continue;
      }

      foreach ($rules as $name => $ruleObj)
      {
        $rulesArr[$name] = $ruleObj->getRule();
      }

      $return[$field] = $rulesArr;
    }

    return $this->_generateJavascriptObject($return);
  }

  public function getMessagesString()
  {
    $return = array();

    // convert rules into an array
    foreach ($this->getRules() as $field => $rules)
    {
      $messagesArr = array();

      if (!count($rules))
      {
        continue;
      }

      foreach ($rules as $name => $ruleObj)
      {
        $messagesArr[$name] = $ruleObj->getMessage();
      }

      $return[$field] = $messagesArr;
    }

    return $this->_generateJavascriptObject($return);
  }

  public function getOptions()
  {
    return $this->_options;
  }

  public function setOptions(array $options)
  {
    $this->_options = $options;
    return $this;
  }

  public function getOption($name)
  {
    $options = $this->getOptions();
    return isset($options[$name]) ? $options[$name] : null;
  }

  public function addOption($name, $value)
  {
    $options = $this->getOptions();
    $options[$name] = $value;
    $this->setOptions($options);
    return $this;
  }

  public function removeOption($name)
  {
    $options = $this->getOptions();
    unset($options[$name]);
    $this->setOptions($options);
    return $this;
  }

  public function getDefaultOptions()
  {
    $defaultOptions = array();

    $defaultOptions['errorClass'] = '"'
      . $this->getForm()->getWidgetSchema()->getFormFormatter()
          ->getFieldErrorClass()
      . '"'
    ;

    $defaultOptions['validClass'] = '"'
      . $this->getForm()->getWidgetSchema()->getFormFormatter()
          ->getFieldValidClass()
      . '"'
    ;

    return $defaultOptions;
  }

}