/**
 * Extensions to jquery.validation
 *
 *
 * @package     sfJqueryValidationPlugin
 * @subpackage  jquery.validation
 * @author      Kevin Dew <kev@dewsolutions.co.uk>
 */

(function($) {
  /**
   * Based on the reg-exp extension by James Beattie for
   * sfJqueryFormValidationPlugin
   */
  $.validator.addMethod(
    'regex',
    function(value, element, regex) {
      return this.optional(element) || regex.test(value);
    },
    'Invalid.'
  );
    
  /**
   * Based on the reg-exp extension by James Beattie for
   * sfJqueryFormValidationPlugin
   */
  $.validator.addMethod(
    'regexFail',
    function(value, element, regex) {
      return this.optional(element) || !regex.test(value);
    },
    'Invalid.'
  );


  /**
   * Method to validate an or validation type
   */
  $.validator.addMethod(
    'or',
    function(value, element, orArray) {

      // return true if length is 0
      if (orArray.length == 0) {
        return this.optional(element) || true;
      }

      var firstError = '';

      // assume we've failed we'll change this if we pass a test
      var pass = false;

      var methodThis = this;

      // use element data to store rules for error message
      if (typeof arguments[3] == 'undefined') {
        $(element).data('jqValError', []);
      }

      // loop through each validation object
      var count = 0;
      $.each(orArray, function(index, validatorObject) {

        var objectPass = true;

        // loop through each rule
        
        $.each(validatorObject, function(rule, arg) {

          if (!$.validator.methods[rule].call(
            methodThis, value, element, arg, count
          )) {
            
            objectPass = false;

            // add or error data to the object so we can try work out error message
            if (!firstError) {
              $(element).data('jqValError').push(rule)
              $(element).data('jqValError').push(count);
              firstError = rule;
            }
            return false;
          }          

        });

        if (objectPass && !pass) {
          pass = true;
          return false;
        }
        count++;
      });

      return this.optional(element) || pass;
    },
    'Invalid.'
  );

  /**
   * Method to validate an and validation type
   */
  $.validator.addMethod(
    'and',
    function(value, element, andArray) {

      // return true if length is 0
      if (andArray.length == 0) {
        return this.optional(element) || true;
      }

      var allPassed = true;

      var firstError = '';

      var methodThis = this;

      // use element data to store rules for error message
      if (typeof arguments[3] == 'undefined') {
        $(element).data('jqValError', []);
      }

      // loop through each validation object
      var count = 0;
      $.each(andArray, function(index, validatorObject) {

        // loop through each rule

        $.each(validatorObject, function(rule, arg) {

          if (!$.validator.methods[rule].call(
            methodThis, value, element, arg, count
          )) {

            allPassed = false;

            // add and error data to the object so we can try work out error message
            if (!firstError) {
              $(element).data('jqValError').push(rule)
              $(element).data('jqValError').push(count);
              firstError = rule;
            }
            return false;
          }

        });

        if (!allPassed) {
          return false;
        }
        count++;
      });

      return this.optional(element) || allPassed;
    },
    'Invalid.'
  );

  /**
   * Methods for the validator object itself
   */
  if (typeof $.validator.sfJqueryValidationPlugin == 'undefined') {
    $.validator.sfJqueryValidationPlugin = {};
  }

  /**
   * Method to return a message that is stored in an object based on data stored
   * in the element
   *
   * @param   mixed       rulesParams   The rule parameters for the validation
   *                                    rule
   * @param   DomElement  element
   * @param   Object      messages      An object of messages
   * @param   string      backupMessage A string backup message if parsing the
   *                                    messages object fails
   * @param   string      dataString    Where to check an element for the error
   *
   * @return  string
   */
  $.validator.sfJqueryValidationPlugin.parseMessagesFromElementData = function (
    ruleParams, element, messages, backupMessage, dataString
  ) {

    if (typeof $(element).data(dataString) !== 'object') {
      return backupMessage;
    }

    var jqValErrorArr = $(element).data(dataString);
    var first;

    while (typeof (first = jqValErrorArr.pop()) != 'undefined') {
      if (typeof messages[first] == 'undefined') {
        break;
      }

      messages = messages[first];

      if (typeof messages == 'function') {
        return messages.call(this, ruleParams, element);
      } else if (typeof messages == 'string') {
        var theregex = /\$?\{(\d+)\}/g;
        if (theregex.test(messages)) {
          messages = jQuery.format(messages.replace(theregex, '{$1}'), ruleParams);
        }
        return messages;
      }

    }

    return backupMessage;
  }
})(jQuery);