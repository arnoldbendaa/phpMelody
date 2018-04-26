/*
*	TypeWatch 2.2
*
*	Examples/Docs: github.com/dennyferra/TypeWatch
*	
*  Copyright(c) 2013 
*	Denny Ferrassoli - dennyferra.com
*   Charles Christolini
*  
*  Dual licensed under the MIT and GPL licenses:
*  http://www.opensource.org/licenses/mit-license.php
*  http://www.gnu.org/licenses/gpl.html
*/
(function(jQuery){jQuery.fn.typeWatch=function(o){var _supportedInputTypes=["TEXT","TEXTAREA","PASSWORD","TEL","SEARCH","URL","EMAIL","DATETIME","DATE","MONTH","WEEK","TIME","DATETIME-LOCAL","NUMBER","RANGE"];var options=jQuery.extend({wait:750,callback:function(){},highlight:true,captureLength:2,inputTypes:_supportedInputTypes},o);function checkElement(timer,override){var value=jQuery(timer.el).val();if((value.length>=options.captureLength&&value.toUpperCase()!=timer.text)||(override&&value.length>=options.captureLength)){timer.text=value.toUpperCase();timer.cb.call(timer.el,value);}}function watchElement(elem){var elementType=elem.type.toUpperCase();if(jQuery.inArray(elementType,options.inputTypes)>=0){var timer={timer:null,text:jQuery(elem).val().toUpperCase(),cb:options.callback,el:elem,wait:options.wait};if(options.highlight){jQuery(elem).focus(function(){this.select();});}var startWatch=function(evt){var timerWait=timer.wait;var overrideBool=false;var evtElementType=this.type.toUpperCase();if(typeof evt.keyCode!="undefined"&&evt.keyCode==13&&evtElementType!="TEXTAREA"&&jQuery.inArray(evtElementType,options.inputTypes)>=0){timerWait=1;overrideBool=true;}var timerCallbackFx=function(){checkElement(timer,overrideBool);};clearTimeout(timer.timer);timer.timer=setTimeout(timerCallbackFx,timerWait);};jQuery(elem).on("keydown paste cut input",startWatch);}}return this.each(function(){watchElement(this);});};})(jQuery);