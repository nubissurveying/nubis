$(document).ready(function(){
	//Dom is ready lets get the fun started.

	var Calculator = {
		runningTotal : '',	
		currentVal : '',
		setCurrentVal: false,
		executeAction: '',
		display: '',
		adjustTotals: function(val){
			if (!this.setCurrentVal) {
				//If this is the first number user has entered then it becomes runningTotal
				//Otherwise it becomes currentVal which will then be used to update runningTotal based on the action picked
				this.runningTotal += val;
			} else {
				//val is a string so we can append to currentVal for multiple digits
				this.currentVal += val;
			};
		},
		add: function(){
			this.runningTotal = parseInt(this.runningTotal) + parseInt(this.currentVal);
		},
		subtract: function() {
			this.runningTotal = parseInt(this.runningTotal) - parseInt(this.currentVal);
		},	
		multiply: function(){
			this.runningTotal = parseInt(this.runningTotal) * parseInt(this.currentVal);
		},
		divide: function(){
			this.runningTotal = parseInt(this.runningTotal) / parseInt(this.currentVal);
		},
		clear: function(){
			this.runningTotal = '';
			this.currentVal = '';
			this.executeAction = '';
			this.setCurrentVal = false;
			this.display = '';
		},
		resetCurrentVal: function (){
			this.currentVal = '';
		},
		calculate: function(){
			this.executeAction = '';
			this.currentVal = '';
			return this.runningTotal;
		},
		getAction: function(val){
			 var method = '';
			switch (val) {
				case '+': 
					method = Calculator.add;
					break;
				case '-':
					method = Calculator.subtract;
					break;
				case 'x':
					method = Calculator.multiply;
					break;
				case '/':
					method = Calculator.divide;
					break;
			}

			return method;
		},
		setDisplay: function(){
			return this.display = this.currentVal == '' ? this.runningTotal : this.currentVal;
		}
	};

	
	var onButtonPress = function (){
		var that = $(this),
			action = that.hasClass('action'),
			instant = that.hasClass('instant'),
			val = that.text();
		if (!action) {
			//No action means the button pressed is a number not an "action"
			Calculator.adjustTotals(val);
		} else if(!instant) { 
			//A action button was pressed. Store the action so it can be executed lator
			if (Calculator.executeAction != ''){
				Calculator.executeAction();
			};

			Calculator.executeAction = Calculator.getAction(val);
			Calculator.setCurrentVal = true;
			Calculator.resetCurrentVal();
		} else {
			//Either = or Clr is clicked. this needs immediate action.
			if (Calculator.executeAction != ''){
				Calculator.executeAction();
			};

			switch (val){
				case 'cl': 
					method = Calculator.clear();
					break;
				case '=':
					method = Calculator.calculate();
                                        moveToSurvey();

					break;
			}
		}

		Calculator.setDisplay();
	}

	var refreshVal = function(){
		$('.calculator input[type=text]').val(Calculator.display);
	}

	$('div.key').click(function(){
		//We want this to stay as div.keyin the onButtonPress function
		onButtonPress.call(this);
		refreshVal();
	});
});
