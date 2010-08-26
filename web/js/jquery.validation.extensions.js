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

      // use element data to store rules for error message
      if (typeof arguments[3] == 'undefined') {
        $(element).data('jqValError', []);
      } else {
        $(element).data('jqValError').push(arguments[3]);
        $(element).data('jqValError').push('or');
      }


      // loop through each validation object
      var count = 0;
      $.each(orArray, function(index, validatorObject) {

        var objectPass = true;

        // loop through each rule
        
        $.each(validatorObject, function(rule, arg) {

          if (!$.validator.methods[rule].call(methodThis, value, element, arg, count)) {
            
            objectPass = false;

            // add or error data to the object so we can try work out error message
            if (!firstError) {
              if (rule != 'or') {
                $(element).data('jqValError').push(count);
                $(element).data('jqValError').push(rule)
              }
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

  $.validator.addMethod(
    'and',
    function(value, element, orArray) {

      // return true if length is 0
      if (orArray.length == 0) {
        return this.optional(element) || true;
      }

      var allPassed = true;

      var firstError = '';

      var methodThis = this;

      // use element data to store rules for error message
      if (typeof arguments[3] == 'undefined') {
        $(element).data('jqValError', []);
      } else {
        $(element).data('jqValError').push(arguments[3]);
        $(element).data('jqValError').push('and');
      }


      // loop through each validation object
      var count = 0;
      $.each(orArray, function(index, validatorObject) {

        // loop through each rule

        $.each(validatorObject, function(rule, arg) {

          if (!$.validator.methods[rule].call(methodThis, value, element, arg, count)) {

            allPassed = false;

            // add or error data to the object so we can try work out error message
            if (!firstError) {
              if (rule != 'and') {
                $(element).data('jqValError').push(count);
                $(element).data('jqValError').push(rule)
              }
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

      console.log(this.optional(element) || allPassed);

      return this.optional(element) || allPassed;
    },
    'Invalid.'
  );
})(jQuery);