<?php
/**
 * sfJqueryValidationParserPostValidatorBase
 *
 * @package     sfJqueryValidationPlugin
 * @subpackage  PostValidatorParser
 * @author      Kevin Dew  <kev@dewsolutions.co.uk>
 */
abstract class sfJqueryValidationParserPostValidatorBase
extends sfJqueryValidationParsersfValidatorBase
{
  /**
   * Builds a parser
   *
   * @param   sfFormField     $field
   * @param   sfValidatorBase $validator
   * @return  void
   */
  public function __construct(sfFormField $field, sfValidatorBase $validator)
  {
    $this
      ->setName('')
      ->setField($field)
      ->setValidator($validator)
      ->configure()
    ;
    $this->_generateRules();
  }

  /**
   * Generate the rules for this field
   *
   * @return  void
   */
  protected function _generateRules()
  {
  }

}
