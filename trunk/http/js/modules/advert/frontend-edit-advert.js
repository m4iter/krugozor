function set_contact_input_state(elementId)
{
	var lnk = document.getElementById(elementId);
	
	//lnk.disabled = (lnk.disabled ? false : true);
	
	var op = eval("lnk.style." + getOpacityProperty());
	
	setElementOpacity(lnk, (op == 1 || !op ? 0.5 : 1));
}

function view_login_password(_this, login, password)
{
    var span = document.createElement('SPAN');
    var b = document.createElement('B');
    b.appendChild(document.createTextNode(login));
    span.appendChild(b);
    span.appendChild(document.createTextNode(' '));
    var b = document.createElement('B');
    b.appendChild(document.createTextNode(password));
    span.appendChild(b);
    _this.parentNode.replaceChild(span, _this);
    return false;
}

window.onload = function()
{
	var oFCKeditor = new FCKeditor('advert[text]');
	var FCKeditor_path = "/http/fckeditor/";
	oFCKeditor.BasePath = FCKeditor_path;
	oFCKeditor.Config["CustomConfigurationsPath"] = FCKeditor_path + "myconfig.js?" + ( new Date() * 1 ) ;
	oFCKeditor.Width = '510px';
	oFCKeditor.Height = 300;
	oFCKeditor.ToolbarSet = 'BBcode';
	oFCKeditor.ReplaceTextarea();

	document.getElementById('close_rules').onclick = function(){
		this.parentNode.style.display = 'none';
		return false;
	}
}