 $(document).ready(function() {
	$('.index_h3').corner('10px'),
    $('.header').corner('10px'),
    $('.advert_content').corner('5px').parent().css('padding', '1px').corner("round 5px")
    $('.notification_normal').corner('10px'),
    $('.notification_alert').corner('10px'),
    $('.notification_warning').corner('10px'),
    $('.help').corner('3px'),
    $('.navigation_list').corner('10px'),
    $('.advert_price').corner('6px'),
    $('.button_switch_advert_type').corner('6px')
})

var imitationLink = function(_this)
{
	this.location.href = _this.getAttribute('tooltip');
}
 
 /**
  * ѕоказывает email по клику в объ€влении. 
  * 
  * @param HTMLElement _this ссылка на элемент, по клику на котором произошло событие
  * @param int ID объ€влени€ 
  * @return bool 
  */
var viewAdvertEmail = function(_this, id_advert)
{
	var ajax = new Ajax();

	var img = document.createElement('IMG');
	img.src = '/http/image/desing/icon/ajax-loader-small.gif';
	
	var parent = _this.parentNode;
	parent.replaceChild(img, _this.parentNode.firstChild);
	
    ajax.setObserverState(function(ajx, xhr)
    {
    	if (ajx.getHttpRequest().readyState == 4) {
            if (ajx.getHttpRequest().status == 200) {
            	var response = ajx.getJson2HashByKey();
            	var a = document.createElement('A');
            	a.setAttribute('href', 'mailto:' + response.email);
            	a.appendChild(document.createTextNode(response.email));
            	setTimeout(function(){parent.replaceChild(a, img)}, 800);
            }
        }	
    });
    
    ajax.get('/ajax/advert-email/id/' + id_advert, true);
    
    return false;
}

/**
 * ѕоказывает телефон по клику в объ€влении. 
 * 
 * @param HTMLElement _this ссылка на элемент, по клику на котором произошло событие
 * @param int ID объ€влени€ 
 * @return bool 
 */
var viewAdvertPhone = function(_this, id_advert)
{
	var img = document.createElement('IMG');
	img.src = '/http/image/desing/icon/ajax-loader-small.gif';
	
	var parent = _this.parentNode;
	parent.replaceChild(img, _this.parentNode.firstChild);

	var ajax = new Ajax();
	
    ajax.setObserverState(function(ajx, xhr)
    {
    	
    	if (ajx.getHttpRequest().readyState == 4) {
            if (ajx.getHttpRequest().status == 200) {
            	var response = ajx.getJson2HashByKey();
            	var span = document.createElement('SPAN');
            	span.appendChild(document.createTextNode(response.phone));
            	setTimeout(function(){parent.replaceChild(span, img)}, 800);
            }
        }	
    });
    
    ajax.get('/ajax/advert-phone/id/' + id_advert, true);
    
    return false;
}

var _gaq = _gaq || [];
_gaq.push(['_setAccount', 'UA-19949843-1']);
_gaq.push(['_trackPageview']);
(function() {
  var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
  ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
  var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
})();