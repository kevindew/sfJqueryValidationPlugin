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
   * @param sfFormJqueryValidationInterface $form
   */
  public function  __construct(sfFormJqueryValidationInterface $form)
  {
    $this->setScriptTemplate($this->getDefaultScriptTemplate());
    $this->setForm($form);
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
    var validator = $('#%form_accessor_id%').parents('form').first().validate({});

%user_script%
  });
})(jQuery);
EOF;
  }

  /**
   * Generates the javascript rules for the validation
   *
   * @return  string
   */
  public function generateJavascript()
  {

    $script = strtr(
      $this->getScriptTemplate(),
      array(
        '%form_accessor_id%' => addcslashes($this->_getFormAccessorId(), "'"),
        '%user_script%' => $this->getUserJavascript()
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

}