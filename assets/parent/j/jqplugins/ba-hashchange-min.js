/* This file has */
/* been minimized. */
/* credits of reused code can be found by removing '-min' from the url. */
/*
 * jQuery hashchange event - v1.2 - 2/11/2010
 * http://benalman.com/projects/jquery-hashchange-plugin/
 * 
 * Copyright (c) 2010 "Cowboy" Ben Alman
 * Dual licensed under the MIT and GPL licenses.
 * http://benalman.com/about/license/
 */
(function($,window,undefined){var fake_onhashchange,jq_event_special=$.event.special,str_location="location",str_hashchange="hashchange",str_href="href",browser=$.browser,mode=document.documentMode,is_old_ie=browser.msie&&(mode===undefined||mode<8),supports_onhashchange="on"+str_hashchange in window&&!is_old_ie;function get_fragment(url){url=url||window[str_location][str_href];return url.replace(/^[^#]*#?(.*)$/,"$1")}$[str_hashchange+"Delay"]=100;jq_event_special[str_hashchange]=$.extend(jq_event_special[str_hashchange],{setup:function(){if(supports_onhashchange){return false}$(fake_onhashchange.start)},teardown:function(){if(supports_onhashchange){return false}$(fake_onhashchange.stop)}});fake_onhashchange=(function(){var self={},timeout_id,iframe,set_history,get_history;function init(){set_history=get_history=function(val){return val};if(is_old_ie){iframe=$('<iframe src="javascript:0"/>').hide().insertAfter("body")[0].contentWindow;get_history=function(){return get_fragment(iframe.document[str_location][str_href])};set_history=function(hash,history_hash){if(hash!==history_hash){var doc=iframe.document;doc.open().close();doc[str_location].hash="#"+hash}};set_history(get_fragment())}}self.start=function(){if(timeout_id){return}var last_hash=get_fragment();set_history||init();(function loopy(){var hash=get_fragment(),history_hash=get_history(last_hash);if(hash!==last_hash){set_history(last_hash=hash,history_hash);$(window).trigger(str_hashchange)}else{if(history_hash!==last_hash){window[str_location][str_href]=window[str_location][str_href].replace(/#.*/,"")+"#"+history_hash}}timeout_id=setTimeout(loopy,$[str_hashchange+"Delay"])})()};self.stop=function(){if(!iframe){timeout_id&&clearTimeout(timeout_id);timeout_id=0}};return self})()})(jQuery,this);