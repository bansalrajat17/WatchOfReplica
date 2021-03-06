;(function($)
{
	'use strict';

	var conditions = window.conditions;
	var appliedConditions = [];

	/**
	 * Compare two variables
	 * 
	 * @param Mixed needle Variable 1
	 * @param Mixed haystack Variable 2
	 * @param String Compare Operator
	 *
	 * @return boolean
	 * @return boolean
	 */
	function checkCompareStatement(needle, haystack, compare)
	{
		if ( needle === null || typeof needle === 'undefined' )
			needle = '';

		switch( compare )
		{
			case '=':
				if ( $.isArray(needle) && $.isArray(haystack) )
					return ( $(needle).not(haystack).length === 0 && $(haystack).not(needle).length === 0 );
				else
					return needle == haystack;

			case '>=':
				return needle >= haystack;

			case '>':
				return needle > haystack;

			case '<=':
				return needle <= haystack;

			case '<':
				return needle < haystack;

			case 'contains':
				if ( $.isArray(needle) )
					return $.inArray( haystack, needle ) > -1;  
				else if ( $.type(needle) == 'string')
					return needle.indexOf( haystack ) > -1;

			case 'in':
				if ( $.isArray(needle) ) {
					var found = false;
		            $.each(needle, function(index, value) {
		            	if ( $.isNumeric(value ) )
		            		value = parseInt(value);

                    	if (haystack.indexOf(value) > -1)
                            found = true;
                    });

                    return found;
		       } else {
		            return haystack.indexOf(needle) > -1;
		       }

			case 'start_with':
				return needle.indexOf(haystack) === 0;
			
			case 'starts with':
				return needle.indexOf(haystack) === 0;
				
			case 'end_with':
				haystack = new RegExp(haystack + '$');
				return haystack.test(needle);

		    case 'ends with':
		        haystack = new RegExp(haystack + '$');
		        return haystack.test(needle);

			case 'match':
				haystack = new RegExp(haystack);
				return haystack.test(needle);

			case 'between':
				if ($.isArray(haystack) && typeof haystack[0] != 'undefined' && typeof haystack[1] != 'undefined')
					return checkCompareStatement(needle, haystack[0], '>=') && checkCompareStatement(needle, haystack[1], '<=');
		}
	}
	
	/**
	 * Put a selector, then retrieve values
	 * 
	 * @param  Element selector Element Selector
	 * 
	 * @return Mixed Selector values
	 */
	function getFieldValue(fieldName, $scope, target)
	{
		// Allows user define conditional logic by callback
		if ( checkCompareStatement( fieldName, '(', 'contains' ) )
			return eval(fieldName);

		if ($(target).find('#' + fieldName).length && $(target).find( '#' + fieldName).attr('type') !== 'checkbox' 
			&& typeof $(target).find('#' + fieldName).val() != 'undefined' && $(target).find('#' + fieldName).val() != null && $scope == '')
			fieldName = '#' + fieldName;

		// If fieldName start with #. Then it's an ID, just return it values
		if (checkCompareStatement( fieldName, '#', 'start_with') && $(target).find(fieldName).attr('type') !== 'checkbox' 
			&& typeof $(target).find(fieldName).val() != 'undefined' && $(target).find(fieldName).val() != null && $scope == '' )
		{
			return $(fieldName).val();	
		}

		var selector = guessSelector(fieldName, target);


		if ( null !== selector )
		{
			var selectorType 	= $(selector).attr('type');

			if ( $.inArray( selectorType, ['text', 'file'] ) > -1 ) {
				if ( $scope == '' )
					return $(selector).val();
				else
					return $scope.find(selector).val();
			}

			// If user selected a checkbox or radio. Return array of selected fields,
			// or value of singular field.
			if ( $.inArray( selectorType, ['checkbox', 'radio', 'hidden'] ) > -1 ) {
				var arr 		= [],
					elements 	= [];

				if ( selectorType === 'hidden' && fieldName !== 'post_category' && 
					! checkCompareStatement(selector, 'tax_input', 'contains') )
					elements = ($scope != '') ? $scope.find(selector) : $(selector);
				else
					elements = ($scope != '') ? $scope.find(selector + ':checked') : $(selector + ':checked');
				
				// Multiple field. Selected multiple items
				if (elements.length > 1 && selectorType != 'radio'){
					elements.each(function()
					{
						arr.push($(this).val());
					});
				}
				// Singular field, selected
				else if ( elements.length === 1 ) {
					arr = elements.val();
				}
				// Singular field, not selected
				else {
					arr = 0;
				}

				return arr;
			}

			if ( $scope == '' )
				return $(selector).val();
			else
				return $scope.find(selector).val();
		}

		return 0;
	}
	
	/**
	 * Check if logics attached to fields is correct or not
	 * 
	 * If a field is hidden by Conditional Logic, then all dependent fields are hidden also
	 * 
	 * @param  Array  logics All logics applied to field
	 * 
	 * @return {Boolean}
	 */
	function isLogicCorrect(logics, $scope, target)
	{
		var relation 	= ( typeof logics.relation != 'undefined' ) ? logics.relation : 'and',
			success 	= relation == 'and';

		$.each( logics.when, function( index, logic ) 
		{
			var isDependentFieldVisible = $(guessSelector(logic[0], target)).closest('.sm-rwmb-field').attr('data-visible');
			
			if ( 'hidden' == isDependentFieldVisible ) {
				success = 'hidden';
			} else {
				
				// Check if $scope contains logic[0] selector, if not, unset $scope
				if ( $scope != '' ) {
					var logicSelector = guessSelector(logic[0], target);
					
					if ( ! $scope.find(logicSelector).length )
						$scope = '';
				}

				var item 	= getFieldValue(logic[0], $scope, target),
				 	compare = logic[1],
				 	value	= logic[2],
				 	check   = false,
				 	negative = false;

				// Cast to string if array has 1 element and its a string
				if ( $.isArray(item) && item.length === 1 )
					item = item[0];

				// Allows user using NOT statement.
				if (checkCompareStatement(compare, 'not', 'contains') || checkCompareStatement(compare, '!', 'contains')) {
					negative = true;
					compare  = compare.replace( 'not', '' ); 
					compare  = compare.replace( '!', '' ); 
				}

				compare = compare.trim();

				if ($.isNumeric(item))
					item = parseInt( item );

				check = checkCompareStatement( item, value, compare );

				if ( negative )
					check =! check;

				success = ( relation === 'and' ) ? success && check : success || check;
			}
		} );

		return success;
	}

	/**
	 * Run all conditional logic statements then show / hide fields or meta boxes
	 * 
	 * @param  Array conditions All defined conditional
	 * 
	 * @return void
	 */
	function runConditionalLogic(target)
	{
		
		var processConditions = function(el, skipEvents) {
			
			var field 			= $(el),
			 	fieldConditions = field.data('conditions'),
			 	action 			= typeof fieldConditions['hidden'] != 'undefined' ? 'hidden' : 'visible',
			 	logic 			= fieldConditions[action];

			var $scope = '';

			if (field.parents().hasClass('sm-rwmb-clone'))
				$scope 	= field.parents('.sm-rwmb-clone');

			var logicApply 		= isLogicCorrect(logic, $scope, target);

			var $selector 		= field.parent().hasClass('sm-rwmb-field') ? field.parent() : field.parents('.postbox');

			if (logicApply === true)
				action == 'visible' ? applyVisible($selector) : applyHidden($selector);
			else if (logicApply === false)
				action == 'visible' ? applyHidden($selector) : applyVisible($selector);
			else if (logicApply === 'hidden')
				applyHidden($selector);
			
			if(!skipEvents) {
					
				var singleLogicSelector;
				$.each(logic.when, function(i, single_logic) {
					if ( ! checkCompareStatement( single_logic[0], '(', 'contains' ) )
					{
						singleLogicSelector = guessSelector(single_logic[0], target);
					
						$(singleLogicSelector).on('change keyup click', function() {
							processConditions(el, true);
						});
					}
				});
			}
		};
		
		$(target).find('.sm-rwmb-conditions').each(function() {
			
			processConditions(this, false);
		});

	}

	function runOutsideConditionalLogic(target) {
		
		var processConditions = function(field, logics) {
			
			$.each(logics, function(action, logic) {
				
				if (typeof logic.when == 'undefined') return;

				var selector 		= guessSelector(field, target),
					logicApply 		= isLogicCorrect(logic, '', target);

				if (logicApply === true)
					action == 'visible' ? applyVisible($(selector)) : applyHidden($(selector));
				else if (logicApply === false)
					action == 'visible' ? applyHidden($(selector)) : applyVisible($(selector));
				else if (logicApply === 'hidden')
					applyHidden($(selector));
					
				if(!skipEvents) {

					var singleLogicSelector;
					$.each(logic.when, function(i, single_logic) {
						if ( ! checkCompareStatement( single_logic[0], '(', 'contains' ) )
						{
							singleLogicSelector = guessSelector(single_logic[0], target);
							
							$(singleLogicSelector).on('change keyup click', function() {
								processConditions(field, logics, true);
							});
						}
					});	
				}
			});
		};
		
		// Outside Conditions
		$.each(window.conditions, function(field, logics) {	

			processConditions(field, logics, false);

		});
	}

	function guessSelector(fieldName, target)
	{
		if ( checkCompareStatement(fieldName, '(', 'contains'))
			return null;


		var targetID = $(target).attr('id');
		if(targetID) {
			targetID = '#'+targetID+' ';
		}else{
			targetID = '';
		}

		if ($(target).find(fieldName).length || isUserDefinedSelector(fieldName))
			return targetID+fieldName;
		
		// If field id exists. Then return it values
		try {
			if ( $(target).find('#' + fieldName).length 
				&& $(target).find('#' + fieldName).attr('type') !== 'checkbox' 
				&& ! $(target).find('#' + fieldName).attr('name')
			)	
				return targetID+fieldName;

			if ($(target).find('[name="' + fieldName + '"]').length)
				return targetID+'[name="' + fieldName + '"]';

			if ($(target).find('[name^="' + fieldName + '"]').length)
				return targetID+'[name^="' + fieldName + '"]';

			if ($(target).find('[name*="' + fieldName + '"]').length)
				return targetID+'[name*="' + fieldName + '"]';
		} catch(e){
			console.log(e);
		}
	}
	
	
	function isUserDefinedSelector(fieldName)
	{
		return checkCompareStatement(fieldName, '.', 'starts with') || 
			checkCompareStatement(fieldName, '#', 'starts with') ||
			checkCompareStatement(fieldName, '[name', 'contains') ||
			checkCompareStatement(fieldName, '>', 'contains') ||
			checkCompareStatement(fieldName, '*', 'contains') ||
			checkCompareStatement(fieldName, '~', 'contains');
	}

	/**
	 * Visible field or entire meta box
	 * 
	 * @param  Element element Element Selector
	 * 
	 * @return void
	 */
	function applyVisible($element)
	{	
		// Element is a Field. Find the field wrapper and show.
		if ($element.closest('.sm-rwmb-field').length)
			$element = $element.closest('.sm-rwmb-field');

		if ($element.hasClass('sm-rwmb-column'))
			$element.css('visibility', 'visible');
		else
			$element.show();

		$element.attr('data-visible', 'visible');
	}

	/**
	 * Hide field or entire meta box

	 * @param  Element element Element Selector
	 * 
	 * @return void
	 */
	function applyHidden($element)
	{
		// Element is a Field. Find the field wrapper and show.
		if ($element.closest('.sm-rwmb-field').length)
			$element = $element.closest('.sm-rwmb-field');

		if ($element.hasClass('sm-rwmb-column'))
			$element.css('visibility', 'hidden');
		else
			$element.hide();

		$element.attr('data-visible', 'hidden');
	}

	// Load conditional logic by default
	
	var target = 'body';
	
	runConditionalLogic(target);
	runOutsideConditionalLogic(target);

	$(document).on('sm_mb_init_events', function(e, data) {
	
		var target = data.target;

		runConditionalLogic(target);
		
	});

})(jQuery);