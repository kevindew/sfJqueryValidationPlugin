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
   * An array of javascripts
   *
   * @var array
   */
  protected $_javascripts = array();

  /**
   * An array of stylesheets
   *
   * @var array
   */
  protected $_stylesheets = array();

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

    // set validator script
    if (sfConfig::get('app_sfJqueryValidationPlugin_jquery_validation_script'))
    {
      $this->setJavascripts(array_merge(
        $this->getJavascripts(),
        array(
          sfConfig::get('app_sfJqueryValidationPlugin_jquery_validation_script')
        )
      ));
    }

    $this->setScriptTemplate($this->getDefaultScriptTemplate());
    $this->setOptions($this->getDefaultOptions());
    $this->setOptionsFromFormFormatter();
    $this->mergeOptions($options);
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
  public function generateRules($reset = false)
  {
    if (!$reset)
    {
      $this->_rules = array();
    }


    $this->_recursiveGenerateFieldsAndValidator(
      $this->getForm()->getFormFieldSchema(),
      $this->getForm()->getValidatorSchema(),
      $reset
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
    sfValidatorSchema $validatorSchema,
    $reset = false
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
          $validatorSchema[$name],
          $reset
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

      if ($reset)
      {
        $this->setFieldRules(
          $field->renderName(), $parser->getRules()
        );
      }
      else
      {
        $this->mergeFieldRules(
          $field->renderName(), $parser->getRules()
        );
      }

      $this->setJavascripts(
        array_merge($this->getJavascripts(), $parser->getJavascripts())
      );

      $this->setStylesheets(
        array_merge($this->getStylesheets(), $parser->getStylesheets())
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
  
  /**
   * Add a rule for a field
   *
   * @param   string                          $fieldName
   * @param   string                          $ruleName
   * @param   sfJqueryValidationValidatorRule $rule
   *
   * @return  self
   */
  public function addFieldRule(
    $fieldName, $ruleName, sfJqueryValidationValidatorRule $rule)
  {
    $this->mergeFieldRules($fieldName, array($ruleName => $rule));

    return $this;
  }

  /**
   * Get a rule for a field
   *
   * @param   string                          $fieldName
   * @param   string                          $ruleName
   *
   * @return  sfJqueryValidationValidatorRule
   */
  public function getFieldRule($fieldName, $ruleName)
  {
    $fieldRules = $this->getFieldRules($fieldName);

    if ($fieldRules === null)
    {
      throw new Exception('Field does not exist');
    }

    if (!isset($fieldRules[$ruleName]))
    {
      throw new Exception('Rule does not exist');
    }

    return $fieldRules[$ruleName];
  }

  /**
   * Remove a rule for a field
   *
   * @param   string  $fieldName
   * @param   string  $ruleName
   *
   * @return  self
   */
  public function removeFieldRule(
    $fieldName, $ruleName)
  {
    $rules = $this->getFieldRules($fieldName);

    if ($rules)
    {
      unset($rules[$ruleName]);
    }

    return $this;
  }

  /**
   * Generate a javascript object as a string
   *
   * Reason for using this rather than json_encode is that json_encode doesn't
   * allow raw javascript which we need
   *
   * @param   array   $array  an array of arrays or strings
   *
   * @return  string
   */
  protected function _generateJavascriptObject(array $array, $linePrefix = '')
  {
    $return = array();

    foreach ($array as $key => $value)
    {
      if (is_array($value))
      {
        $string = $this->_generateJavascriptObject($value, $linePrefix . '  ');
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

    return "{\n" . $linePrefix . '  '
      . implode(", \n" . $linePrefix . '  ', $return)
      . "\n" . $linePrefix . "}";
  }

  /**
   * Get all the rules as a string of a javascript object for outputting
   *
   * @throws  Exception
   *
   * @return  string
   */
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
        if ($ruleObj === null)
        {
          continue;
        }

        if (!$ruleObj instanceof sfJqueryValidationValidatorRule)
        {
          throw new Exception(
            'Rule must be an instance of sfJqueryValidationValidatorRule'
          );
        }

        $rulesArr[$name] = $ruleObj->getRule();
      }

      $return[$field] = $rulesArr;
    }

    return $this->_generateJavascriptObject($return);
  }

  /**
   * Get all the messages as a string of a javascript object for outputting
   *
   * @throws  Exception
   *
   * @return  string
   */
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
        if ($ruleObj === null)
        {
          continue;
        }

        if (!$ruleObj instanceof sfJqueryValidationValidatorRule)
        {
          throw new Exception(
            'Rule must be an instance of sfJqueryValidationValidatorRule'
          );
        }

        $messagesArr[$name] = $ruleObj->getMessage();
      }

      $return[$field] = $messagesArr;
    }

    return $this->_generateJavascriptObject($return);
  }

  /**
   * Get an array of options
   *
   * Options are the ones jquery accept:
   * http://docs.jquery.com/Plugins/Validation/validate#toptions
   *
   * @return  array
   */
  public function getOptions()
  {
    return $this->_options;
  }


  /**
   * Set an array of options
   *
   * Options are the ones jquery accept:
   * http://docs.jquery.com/Plugins/Validation/validate#toptions
   *
   * @param   array $options
   *
   * @return  self
   */
  public function setOptions(array $options)
  {
    $this->_options = $options;
    return $this;
  }

  /**
   * @see     self::setOptions()
   * @param   array $options
   * @return  self
   */
  public function mergeOptions(array $options)
  {
    $this->setOptions(array_merge($this->getOptions(), $options));
    return $this;
  }

  /**
   * Get a particular option
   *
   * @param   string  $name
   * @return  mixed
   */
  public function getOption($name)
  {
    $options = $this->getOptions();
    return isset($options[$name]) ? $options[$name] : null;
  }

  /**
   * Set an option:
   *
   * Options are the ones jquery accept:
   * http://docs.jquery.com/Plugins/Validation/validate#toptions
   *
   * @param   string  $name
   * @param   mixed   $value
   * @return  self
   */
  public function addOption($name, $value)
  {
    $options = $this->getOptions();
    $options[$name] = $value;
    $this->setOptions($options);
    return $this;
  }

  /**
   * Kill an option
   *
   * @param   string  $name
   * @return  self
   */
  public function removeOption($name)
  {
    $options = $this->getOptions();
    unset($options[$name]);
    $this->setOptions($options);
    return $this;
  }

  /**
   * Gets the default options for validation
   *
   * @return  string
   */
  public function getDefaultOptions()
  {
    $defaultOptions = array();

    return $defaultOptions;
  }

  public function setOptionsFromFormFormatter()
  {
    $options = array();

    $options['errorClass'] = '"'
      . $this->getForm()->getWidgetSchema()->getFormFormatter()
          ->getFieldErrorClass()
      . '"'
    ;

    $options['validClass'] = '"'
      . $this->getForm()->getWidgetSchema()->getFormFormatter()
          ->getFieldValidClass()
      . '"'
    ;

    $options['errorElement'] = '"'
      . $this->getForm()->getWidgetSchema()->getFormFormatter()
          ->getJqueryValidationErrorElement()
      . '"'
    ;

    $options['wrapper'] = '"'
      . $this->getForm()->getWidgetSchema()->getFormFormatter()
          ->getJqueryValidationWrapper()
      . '"'
    ;

    return $this->mergeOptions($options);
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