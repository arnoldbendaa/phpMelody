/* iOS touch fix for BootStrap */
$('body').on('touchstart.dropdown', '.dropdown-menu', function (e) { e.stopPropagation(); });
/*
$(document).ready(function(){
        $('#secondary') .css({'height': (($('#wrapper').height()))+'px'});
});*/

$(function() {
	var cc = $.cookie('list_grid');
	if (cc == 'g') {
		$('#pm-grid').addClass('grid');
	} else {
		$('#pm-grid').removeClass('grid');
	}
});
$(document).ready(function() {
	$('#grid').click(function() {
		$('#pm-grid').fadeOut(200, function() {
			$(this).addClass('grid').fadeIn(200);
			$.cookie('list_grid', 'g');
		});
		return false;
	});
	
	$('#list').click(function() {
		$('#pm-grid').fadeOut(200, function() {
			$(this).removeClass('grid').fadeIn(200);
			$.cookie('list_grid', null);
		});
		return false;
	});
});


$(document).ready(function()
{
  $("li.subcat").stop().hide();
  $("li.topcat").hover(
  function()
   {
     $("li.subcat").slideDown(100);
        },
      function()
        {
          $("li.subcat").hide();
               });
});


$('.ajax-modal').click(function(e) {
    e.preventDefault();
    var href = $(e.target).attr('href');
    if (href.indexOf('#') == 0) {
        $(href).modal('open');
    } else {
        $.get(href, function(data) {
            $('<div class="modal" id="uploadForm">' + data + '</div>').modal({keyboard: true});
        });
    }
});
$(document).ready(function() {
$("#to_modal").live('click', function() {
    var url = $(this).attr('url');
    var modal_id = $(this).attr('data-controls-modal');
    $("#" + modal_id).load(url);
  });
});
/*
$('#tags_suggest').tagsInput({
	'removeWithBackspace' : true,
	'height':'auto',
	'width':'auto',
	'defaultText':'',
	'minChars' : 3,
	'maxChars' : 30
});
$('#tags_upload').tagsInput({
	'removeWithBackspace' : true,
	'height':'auto',
	'width':'auto',
	'defaultText':'',
	'minChars' : 3,
	'maxChars' : 30
});
*/