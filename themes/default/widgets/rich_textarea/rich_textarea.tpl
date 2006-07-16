{content}

{open_javascript:h}

var doOnLoad = true;
var instance = tinyMCE.getInstanceById('{id}input');
if (instance) {
	for (var i in tinyMCE.instances) {
		if (tinyMCE.instances[i] == instance) {
			doOnLoad = false;
			tinyMCE.execCommand('mceAddControl', true, '{id}input');
		}
	}
}

tinyMCE.init({
	mode: "exact",
	elements : "{id}input",
	theme: "{theme}",
	language: "en",
	plugins: "table,advhr,advimage,advlink,emotions,preview,flash,searchreplace,print,paste,directionality,fullscreen,noneditable,contextmenu",
	theme_advanced_buttons1: "{toolbar1}",
	theme_advanced_buttons2: "{toolbar2}",
	theme_advanced_buttons3: "{toolbar3}",
	theme_advanced_toolbar_location: "top",
	theme_advanced_toolbar_align: "left",
	theme_advanced_statusbar_location: "bottom",
	{if:upload}file_browser_callback: "{id}fileBrowserCallBack",{end:}
	auto_cleanup_word: true,
	relative_urls: false,
	apply_source_formatting: true,
	add_form_submit_trigger: false,
	add_unload_trigger: false,
	strict_loading_mode: true
});

{if:upload}
{id}fileBrowserCallBack = function(field_name, url, type, win) {
	tinyfck_field = field_name;
	tinyfck = win;
	window.open("../filemanager/browser.html?Connector=connectors/php/connector.php?ServerPath={upload_path}", "{id}textarea", "modal,width=600,height=400");
}
{end:}

if (doOnLoad) {
	tinyMCE.onLoad();
}

{close_javascript:h}