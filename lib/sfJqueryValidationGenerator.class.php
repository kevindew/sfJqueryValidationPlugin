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
   * A collection arrays of groups in form of:
   *  array($groupName => array($fieldName1, $fieldName2, etc)
   *
   * @var array
   */
  protected $_groups = array();

  /**
   * Whether rules have been generated yet
   *
   * @var boolean
   */
  protected $_rulesGenerated = false;

  /**
   * Whether formatting has been generated yet
   *
   * @var boolean
   */
  protected $_formFormatterOptionsGenerated = false;

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
   * Callback methods
   *
   * @var string
   */
  protected 
    $_submitHandlerCallback,
    $_invalidHandlerCallback,
    $_showErrorsCallback,
    $_errorPlacementCallback,
    $_highlightCallback,
    $_unhighlightCallback
  ;

  /**
   * Callback method templates
   *
   * @var string
   */
  protected 
    $_submitHandlerCallbackTemplate = 'function(form) {%callback%}',
    $_invalidHandlerCallbackTemplate = 'function(form, validator) {%callback%}',
    $_showErrorsCallbackTemplate = 'function(errorMap, errorList) {%callback%}',
    $_errorPlacementCallbackTemplate = 'function(error, element) {%callback%}',
    $_highlightCallbackTemplate = 'function(element, errorClass, validClass) {%callback%}',
    $_unhighlightCallbackTemplate = 'function(element, errorClass, validClass) {%callback%}'
  ;

  /**
   *
   * @param sfFormJqueryValidationInterface $form
   */
  public function  __construct(
    sfFormJqueryValidationInterface $form
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
   * Method to be overloaded for adding custom logic to the javascript
   *
   * @return void
   */
  public function doGenerateJavascript()
  {}

  /**
   * Generates the javascript rules for the validation
   *
   * @return  string
   */
  public function generateJavascript()
  {

    if (!$this->getFormFormattingOptionsGenerated())
    {
      $this->generateFormFormatterOptions();
    }

    if (!$this->getRulesGenerated())
    {
      $this->generateRules();
    }

    $this->doGenerateJavascript();
    $this->getForm()->doGenerateJqueryValidation();

    // no rules
    if (!count($this->getRules()))
    {
      return '';
    }

    $rules = array(
      'rules' => $this->getRulesString(),
      'messages' => $this->getMessagesString()
    );

    if ($this->getGroupsString())
    {
      $rules['groups'] = $this->getGroupsString();
    }

    $callbacks = array();

    if ($this->getSubmitHandlerCallback())
    {
      $callbacks['submitHandler'] = $this->getSubmitHandlerCallbackParsed();
    }

    if ($this->getInvalidHandlerCallback())
    {
      $callbacks['invalidHandler'] = $this->getInvalidHandlerCallbackParsed();
    }

    if ($this->getShowErrorsCallback())
    {
      $callbacks['showErrors'] = $this->getShowErrorsCallbackParsed();
    }

    if ($this->getErrorPlacementCallback())
    {
      $callbacks['errorPlacement'] = $this->getErrorPlacementCallbackParsed();
    }
    
    if ($this->getHighlightCallback())
    {
      $callbacks['highlight'] = $this->getHighlightCallbackParsed();
    }

    if ($this->getUnhighlightCallback())
    {
      $callbacks['unhighlight'] = $this->getUnhighlightCallbackParsed();
    }


    $options = array_merge($this->getOptions(), $rules, $callbacks);


    $script = strtr(
      $this->getScriptTemplate(),
      array(
        '%form_accessor_id%' => addcslashes($this->_getFormAccessorId(), "'"),
        '%user_script%' => $this->getUserJavascript(),
        '%options%' => $this->generateJavascriptObject($options)
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

    // we need to set the id format otherwise it might be wrong
    $widget = clone $field->getWidget();
    $widget->setIdFormat($this->getForm()->getWidgetSchema()->getIdFormat());

    return $widget->generateId($field->renderName());
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
   * @param   boolean             $reset
   * @param   boolean             $overwrite
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

        $this->_recursiveGenerateFieldsAndValidator(
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
        $field->renderName(),
        $field,
        $validatorSchema[$name]
      );
      $parser = $factory->getParser();

      foreach ($parser->getRules() as $fieldName => $rules)
      {          
        $this->setFieldRules(
          $fieldName, $rules
        );
      }

      foreach ($parser->getGroups() as $groupName => $fields)
      {
        $this->setGroup($groupName, $fields);
      }

      $this->setJavascripts(
        array_merge($this->getJavascripts(), $parser->getJavascripts())
      );

      $this->setStylesheets(
        array_merge($this->getStylesheets(), $parser->getStylesheets())
      );
    } // end foreach
  }

  /**
   * Get the rules array
   *
   * in form of array($field => array($rule => $ruleObj))
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
   * Get the groups array
   *
   * in form of array($groupName => array($fieldName1, $fieldName2, etc))
   *
   * @return  array
   */
  public function getGroups()
  {
    return $this->_groups;
  }


  /**
   * Set the groups array
   *
   * @param   array   $groups
   * @return  self
   */
  public function setGroups(array $groups)
  {
    $this->_groups = $groups;
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
   * Whether or not form formatting options have been generated or not
   *
   * @return  boolean
   */
  public function getFormFormattingOptionsGenerated()
  {
    return $this->_formFormatterOptionsGenerated;
  }

  /**
   * Set whether form formatting options have been generated or not
   * (setting to false when generated will mean they get regenerated)
   *
   * @param   boolean   $formFormattingOptionsGenerated
   * @return  self
   */
  protected function setFormFormattingOptionsGenerated(
    $formFormattingOptionsGenerated
  )
  {
    $this->_formFormattingOptionsGenerated 
      = (bool) $formFormattingOptionsGenerated
    ;
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
   * @param   boolean $overwrite
   *
   * @return  self
   */
  public function mergeFieldRules($fieldName, array $fieldRules, $overwrite = true)
  {
    $toMerge = $this->getFieldRules($fieldName)
      ? $this->getFieldRules($fieldName)
      : array()
    ;
    $this->setFieldRules(
      $fieldName,
      $overwrite 
        ? array_merge($toMerge, $fieldRules) 
        : array_merge($fieldRules, $toMerge)
    );

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
   * Get the array of fields for a group
   *
   * @param   string $name
   * @return  array
   */
  public function getGroup($name)
  {
    $groups = $this->getGroups();
    
    return isset($groups[$name]) ? $groups[$name] : array();
  }
  
  /**
   * Set the array of fields for a group
   *
   * @param   string  $name
   * @param   array   $fields
   * @return  self
   */
  public function setGroup($name, array $fields)
  {
    $groups = $this->getGroups();
    
    $groups[$name] = $fields;
    
    $this->setGroups($groups);
    
    return $this;
  }
  
  /**
   * Remove a group
   *
   * @param   string  $name
   * @return  self
   */
  public function removeGroup($name)
  {
    $groups = $this->getGroups();
    
    if (!isset($groups[$name]))
    {
      unset($groups[$name]);
      $this->setGroups($groups);
    }
    
    return this;
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
  public static function generateJavascriptObject(array $array, $linePrefix = '')
  {
    $return = array();

    foreach ($array as $key => $value)
    {
      if (is_array($value))
      {
        $string = self::generateJavascriptObject($value, $linePrefix . '  ');
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

    return $this->generateJavascriptObject($return, '  ');
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

    return $this->generateJavascriptObject($return, '  ');
  }

  /**
   * Get all the groups as a string of a javascript object for outputting
   *
   * @return  string
   */
  public function getGroupsString()
  {
    $return = array();

    // convert groups into an array
    foreach ($this->getGroups() as $groupName => $fields)
    {
      if (!$fields)
      {
        continue;
      }

      if (is_string($fields))
      {
        $return[$groupName] = '"' . $fields . '"';
        continue;
      }

      if (is_array($fields))
      {
        $return[$groupName] = '"' . implode(' ', $fields) . '"';
      }
    }

    return $this->generateJavascriptObject($return, '  ');
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
   * @param   array   $options
   * @param   boolean $overwrite  (Optional) default true
   * @return  self
   */
  public function mergeOptions(array $options, $overwrite = true)
  {
    $this->setOptions(
      $overwrite 
        ? array_merge($this->getOptions(), $options)
        : array_merge($options, $this->getOptions())
    );
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

  /**
   * Generate options from the form formatter
   *
   * @param   boolean $overwrite  (Optional) Default true
   * @return  self
   */
  public function generateFormFormatterOptions($overwrite = true)
  {
    $options = array();

    $options['errorClass'] = '"'
      . addcslashes(
          $this->getForm()->getWidgetSchema()->getFormFormatter()
            ->getFieldErrorClass(),
          '"'
        )
      . '"'
    ;

    $options['validClass'] = '"'
      . addcslashes(
          $this->getForm()->getWidgetSchema()->getFormFormatter()
            ->getFieldValidClass(),
          '"'
        )
      . '"'
    ;

    $options['errorElement'] = '"'
      . addcslashes(
          $this->getForm()->getWidgetSchema()->getFormFormatter()
            ->getJqueryValidationErrorElement(),
          '"'
        )
      . '"'
    ;

    $options['wrapper'] = '"'
      . addcslashes(
          $this->getForm()->getWidgetSchema()->getFormFormatter()
            ->getJqueryValidationWrapper(),
          '"'
        )
      . '"'
    ;

    $options['errorContainer'] = '"'
      . addcslashes(
          $this->getForm()->getWidgetSchema()->getFormFormatter()
            ->getJqueryValidationErrorContainer(),
          '"'
        )
      . '"'
    ;

    $this->mergeOptions($options, $overwrite);

    // set callbacks
    if ($overwrite || !$this->getSubmitHandlerCallback())
    {
      $this->setSubmitHandlerCallback(
        $this->getForm()->getWidgetSchema()->getFormFormatter()
          ->getJqueryValidationSubmitHandlerCallback()
      );
    }

    if ($overwrite || !$this->getInvalidHandlerCallback())
    {
      $this->setInvalidHandlerCallback(
        $this->getForm()->getWidgetSchema()->getFormFormatter()
          ->getJqueryValidationInvalidHandlerCallback()
      );
    }

    if ($overwrite || !$this->getErrorPlacementCallback())
    {
      $this->setErrorPlacementCallback(
        $this->getForm()->getWidgetSchema()->getFormFormatter()
          ->getJqueryValidationErrorPlacementCallback()
      );
    }

    if ($overwrite || !$this->getShowErrorsCallback())
    {
      $this->setShowErrorsCallback(
        $this->getForm()->getWidgetSchema()->getFormFormatter()
          ->getJqueryValidationShowErrorsCallback()
      );
    }

    if ($overwrite || !$this->getHighlightCallback())
    {
      $this->setHighlightCallback(
        $this->getForm()->getWidgetSchema()->getFormFormatter()
          ->getJqueryValidationHighlightCallback()
      );
    }

    if ($overwrite || !$this->getUnhighlightCallback())
    {
      $this->setUnhighlightCallback(
        $this->getForm()->getWidgetSchema()->getFormFormatter()
          ->getJqueryValidationUnhighlightCallback()
      );
    }

    return $this;
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

  /**
   * Parse a JS callback into it's corresponding function, returns blank if the
   * callback is empty
   *
   * @param   string $callback
   * @param   string $template
   * @return  string
   */
  protected function _parseCallback($callback, $template)
  {
    if ($callback)
    {
      return strtr($template, array(
        '%callback%' => $callback
      ));
    }
    return '';
  }

  /**
   * @return  string
   */
  public function getSubmitHandlerCallback()
  {
    return $this->_submitHandlerCallback;
  }

  /**
   * @param   string  $submitHandlerCallback
   * @return  self
   */
  public function setSubmitHandlerCallback($submitHandlerCallback)
  {
    $this->_submitHandlerCallback = $submitHandlerCallback;
    return $this;
  }

  /**
   * @return  string
   */
  public function getSubmitHandlerCallbackTemplate()
  {
    return $this->_submitHandlerCallbackTemplate;
  }

  /**
   * @param   string  $submitHandlerCallbackTemplate
   * @return  self
   */
  public function setSubmitHandlerCallbackTemplate($submitHandlerCallbackTemplate)
  {
    $this->_submitHandlerCallbackTemplate = $submitHandlerCallbackTemplate;
    return $this;
  }

  /**
   * @return  string
   */
  public function getSubmitHandlerCallbackParsed()
  {
    return $this->_parseCallback(
      $this->getSubmitHandlerCallback(),
      $this->getSubmitHandlerCallbackTemplate()
    );
  }

  /**
   * @return  string
   */
  public function getInvalidHandlerCallback()
  {
    return $this->_invalidHandlerCallback;
  }

  /**
   * @param   string  $invalidHandlerCallback
   * @return  self
   */
  public function setInvalidHandlerCallback($invalidHandlerCallback)
  {
    $this->_invalidHandlerCallback = $invalidHandlerCallback;
    return $this;
  }

  /**
   * @return  string
   */
  public function getInvalidHandlerCallbackTemplate()
  {
    return $this->_invalidHandlerCallbackTemplate;
  }

  /**
   * @param   string  $invalidHandlerCallbackTemplate
   * @return  self
   */
  public function setInvalidHandlerCallbackTemplate($invalidHandlerCallbackTemplate)
  {
    $this->_invalidHandlerCallbackTemplate = $invalidHandlerCallbackTemplate;
    return $this;
  }

  /**
   * @return  string
   */
  public function getInvalidHandlerCallbackParsed()
  {
    return $this->_parseCallback(
      $this->getInvalidHandlerCallback(),
      $this->getInvalidHandlerCallbackTemplate()
    );
  }

  /**
   * @return  string
   */
  public function getShowErrorsCallback()
  {
    return $this->_showErrorsCallback;
  }

  /**
   * @param   string  $showErrorsCallback
   * @return  self
   */
  public function setShowErrorsCallback($showErrorsCallback)
  {
    $this->_showErrorsCallback = $showErrorsCallback;
    return $this;
  }

  /**
   * @return  string
   */
  public function getShowErrorsCallbackTemplate()
  {
    return $this->_showErrorsCallbackTemplate;
  }

  /**
   * @param   string  $showErrorsCallbackTemplate
   * @return  self
   */
  public function setShowErrorsCallbackTemplate($showErrorsCallbackTemplate)
  {
    $this->_showErrorsCallbackTemplate = $showErrorsCallbackTemplate;
    return $this;
  }

  /**
   * @return  string
   */
  public function getShowErrorsCallbackParsed()
  {
    return $this->_parseCallback(
      $this->getShowErrorsCallback(),
      $this->getShowErrorsCallbackTemplate()
    );
  }

  /**
   * @return  string
   */
  public function getErrorPlacementCallback()
  {
    return $this->_errorPlacementCallback;
  }

  /**
   * @param   string  $errorPlacementCallback
   * @return  self
   */
  public function setErrorPlacementCallback($errorPlacementCallback)
  {
    $this->_errorPlacementCallback = $errorPlacementCallback;
    return $this;
  }

  /**
   * @return  string
   */
  public function getErrorPlacementCallbackTemplate()
  {
    return $this->_errorPlacementCallbackTemplate;
  }

  /**
   * @param   string  $errorPlacementCallbackTemplate
   * @return  self
   */
  public function setErrorPlacementCallbackTemplate($errorPlacementCallbackTemplate)
  {
    $this->_errorPlacementCallbackTemplate = $errorPlacementCallbackTemplate;
    return $this;
  }

  /**
   * @return  string
   */
  public function getErrorPlacementCallbackParsed()
  {
    return $this->_parseCallback(
      $this->getErrorPlacementCallback(),
      $this->getErrorPlacementCallbackTemplate()
    );
  }

  /**
   * @return  string
   */
  public function getHighlightCallback()
  {
    return $this->_highlightCallback;
  }

  /**
   * @param   string  $highlightCallback
   * @return  self
   */
  public function setHighlightCallback($highlightCallback)
  {
    $this->_highlightCallback = $highlightCallback;
    return $this;
  }

  /**
   * @return  string
   */
  public function getHighlightCallbackTemplate()
  {
    return $this->_highlightCallbackTemplate;
  }

  /**
   * @param   string  $highlightCallbackTemplate
   * @return  self
   */
  public function setHighlightCallbackTemplate($highlightCallbackTemplate)
  {
    $this->_highlightCallbackTemplate = $highlightCallbackTemplate;
    return $this;
  }

  /**
   * @return  string
   */
  public function getHighlightCallbackParsed()
  {
    return $this->_parseCallback(
      $this->getHighlightCallback(),
      $this->getHighlightCallbackTemplate()
    );
  }

  /**
   * @return  string
   */
  public function getUnhighlightCallback()
  {
    return $this->_unhighlightCallback;
  }

  /**
   * @param   string  $unhighlightCallback
   * @return  self
   */
  public function setUnhighlightCallback($unhighlightCallback)
  {
    $this->_unhighlightCallback = $unhighlightCallback;
    return $this;
  }

  /**
   * @return  string
   */
  public function getUnhighlightCallbackTemplate()
  {
    return $this->_unhighlightCallbackTemplate;
  }

  /**
   * @param   string  $unhighlightCallbackTemplate
   * @return  self
   */
  public function setUnhighlightCallbackTemplate($unhighlightCallbackTemplate)
  {
    $this->_unhighlightCallbackTemplate = $unhighlightCallbackTemplate;
    return $this;
  }

  /**
   * @return  string
   */
  public function getUnhighlightCallbackParsed()
  {
    return $this->_parseCallback(
      $this->getUnhighlightCallback(),
      $this->getUnhighlightCallbackTemplate()
    );
  }  
}