/**
 * Copyright (c) 2008, CreaLabs SNC (http://www.crealabs.it)
 * Code licensed under AGPL3 license:
 * http://www.gnu.org/licenses/agpl.html
 */

p4a_menu_activate = function ()
{
	$('#p4a_menu li').each(function () {
		$(this).mouseover(function () {
			$(this).children().show();
		}).mouseout(function () {
			$(this).find('ul').hide();
		});
	});
}

p4a_png_fix = function ()
{
	$.ifixpng(p4a_theme_path + '/jquery/pixel.gif');
	
	$("img[@src$=.png]").each(function () {
		var parents = jQuery.makeArray($(this).parents());
		var found = false;
		for (var i=0; i<parents.length; i++) {
			if (!$(parents[i]).is(':visible')) {
				found = true;
				break;
			}
		}
		if (!found) {
			$(this).ifixpng();
		}
	});
}

$(function () {
	p4a_png_fix();
	p4a_menu_activate();
});