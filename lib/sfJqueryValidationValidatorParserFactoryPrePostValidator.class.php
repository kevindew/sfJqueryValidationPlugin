<?php
/**
 * sfJqueryValidationValidatorParserFactorPrePostValidator
 *
 * Takes a field and a validator and pops out a parser for the validator
 *
 * @package     sfJqueryValidationPlugin
 * @subpackage  Pre/Post Validator Parser Factory
 * @author      Kevin Dew  <kev@dewsolutions.co.uk>
 */
abstract class sfJqueryValidationValidatorParserFactoryPrePostValidator
extends sfJqueryValidationValidatorParserFactory
{
  /**
   * @param   sfFormField     $field
   * @param   sfValidatorBase $validator
   *
   * @return  void
   */
  public function __construct(
    sfFormFieldSchema $field, sfValidatorBase $validator
  )
  {
    parent::__construct('', $field, $validator);
  }

  /**
   * Get the parser class for the validator
   *
   * @return  mixed
   */
  public function getParser()
  {
    $validatorClass = get_class($this->getValidator());

    $parserClass = false;

    while ($validatorClass && ($validatorClass != 'sfValidatorBase'))
    {
      $parserClass = $this->_checkParserClass($validatorClass);
      if ($parserClass)
      {
        break;
      }
      $validatorClass = get_parent_class($validatorClass);
    }

    if (!$parserClass)
    {
      return false;
    }

    $parser = new $parserClass(
      $this->getField(), $this->getValidator()
    );

    return $parser;
  }
}
