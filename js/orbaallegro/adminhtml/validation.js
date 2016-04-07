var AllegorValidation = Class.create({
    initialize: function(formId, formName, errorMsg, errorCssClass, maxInputValue) {
        this.formId         = formId || '';
        this.formName       = formName || '';
		this.errorMsg		= errorMsg || '<span style="color: red">Osiągnięto limit znaków!</span>';
        this.errorCssClass  = errorCssClass || 'validation-failed';
		this.maxInput		= maxInputValue || '50';
    },

    validateCounter: function(fieldId, extraDiv, charactersLeftId) {
		if (!($$('#'+fieldId)[0])) {
			return false;
		}
		
		var startLength = this.getFieldCountValue($$('#'+fieldId)[0].value);
		var charactersLeft = this.maxInput - startLength;
		
        $$('#'+fieldId).each(function(subElement) {
			subElement.insert({after: new Element('div').addClassName(extraDiv)}).insert({after: "<span id='"+extraDiv+"'> Pozostało: <span id='"+charactersLeftId+"'>"+charactersLeft+"</span></span>"});
            subElement.observe('keyup', function(event) {
				var box			= $(subElement);
				var boxCount	= this.getFieldCountValue(box.value);
				var countLeft	= this.maxInput - boxCount;
				var errorLimit	= this.errorMsg;

				if (boxCount <= this.maxInput) {
					this.enableForm();
					$$('#'+charactersLeftId).each(function(evt) {
						evt.update(countLeft);
					});
				} else {
					this.disableForm();
					$$('#'+charactersLeftId).each(function(evt) {
						evt.update(errorLimit);
					});
				}

				return false;
            }.bind(this));
        }.bind(this));
    },
	
	getFieldCountValue: function (fieldValue) {
		//Key wage values were defined by Allegro Service
		var counter = 0;
		for (var i = 0, len = fieldValue.length; i < len; i++) {
			switch(fieldValue[i]) {
				case '<':
				case '>':
					counter = counter + 4;
					break;				
				case '&':
					counter = counter + 5;
					break;
				case '"':
					counter = counter + 6;
					break;					
				default:
					counter++;
			}			
		}
		return counter;
	},
	
	disableForm: function() {
		$$('.content-buttons.form-buttons .scalable.save').each(function(subElement) {
			subElement.setAttribute('disabled', 'disabled');
		});
	},

	enableForm: function() {
		$$('.content-buttons.form-buttons .scalable.save').each(function(subElement) {
			if (subElement.hasAttribute('disabled')) {
				subElement.removeAttribute('disabled');
			}
		});		
	}
});

document.observe('dom:loaded', function() {
    /** Allegro: Edit Form - Start **/
    var allegroEditForm = new AllegorValidation('auction_edit_form');
    allegroEditForm.validateCounter('auction1', 'auction_name_counter', 'charactersLeft');
    /** Allegro: Edit Form - End **/	
});