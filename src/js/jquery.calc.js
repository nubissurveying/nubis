/*
 * jQuery Growl Calc
 * Version 2.0.0
 * Last Updated 2014-02-08
 * @requires jQuery v1.11.0 or later (untested on previous version)
 *
 * Examples at: http://projects.zoulcreations.com/jquery/jquery-calc/
 * Copyright (c) 2008-2014 David Higgins
 * 
 */


jQuery(function($) {
  var digits = $('#calculator .digits .digit');
  var clear = $('#calculator .clear');
  var calculator = { left: false, right: false, result: 0, operator: '+' };
  function calculate() {
    calculator.result = eval(calculator.left + calculator.operator + calculator.right);
    $('#calculator .result').text(calculator.result);
    calculator.left = null;
    calculator.right = null;
    leftBuffer = calculator.result + '';
    rightBuffer = '';
  }
  function isDigit(key) {
    var digits = "0123456789";
    var digit = false;
    if(digits.indexOf(key) != -1)
      digit = true;
    return digit;
  }
  var leftBuffer = '';
  var rightBuffer = '';

  clear.click(function() {
    leftBuffer = '';
    rightBuffer = '';
    calculator = { left: false, right: false, result: 0, operator: '+' };
    $('#calculator .result').text(calculator.result);
  });

  digits.click(function() {
    var key = $(this).text();
    console.log(leftBuffer, rightBuffer);
    if(isDigit(key)) {
      if(calculator.left) {
        rightBuffer += key.toString();
        $('#calculator .result').text(rightBuffer);
      } else {
        leftBuffer += key.toString();
        $('#calculator .result').text(leftBuffer);
      }
    } else if(key != '=' && key != '.') {
      switch(key) {
        case '÷': key = '/'; break;
        case '×': key = '*'; break;
      }
      calculator.operator = key;
      calculator.left = leftBuffer;
    } else if(key == '.') {
      if(calculator.left) {
        rightBuffer += '.';
        $('#calculator .result').text(rightBuffer);
      } else {
        leftBuffer += '.';
        $('#calculator .result').text(leftBuffer);
      }
    } else {
      calculator.right = rightBuffer;
      if(calculator.left.substring(calculator.left.length-1, 1) == '.')
        calculator.left += '0';
      if(calculator.right.substring(calculator.right.length-1, 1) == '.')
        calculator.right += '0';
        
      calculate();
    }
  });
});
