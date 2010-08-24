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

      // loop through each validation object
      $.each(orArray, function(index, validatorObject) {
        console.log(this);

        var objectPass = true;

        // loop through each rule
        $.each(validatorObject, function(rule, arg) {

          if (!$.validator.methods[rule].call(methodThis, value, element, arg)) {
            objectPass = false;
            return false;
            if (!firstError) {
              firstError = rule;
            }
          }

        });

        if (objectPass && !pass) {
          pass = true;
          return false;
        }

      });

      return this.optional(element) || pass;
    },
    'Invalid.'
  );
})(jQuery);