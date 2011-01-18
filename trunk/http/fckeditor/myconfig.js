FCKConfig.AutoDetectLanguage = false;
FCKConfig.DefaultLanguage = "ru";

// This option allows you to enable/disable "Upload" tab in the "Link" window
FCKConfig.LinkUpload = false;

FCKConfig.SkinPath = FCKConfig.BasePath + 'skins/my/';

FCKConfig.ToolbarCanCollapse = false;

FCKConfig.SkinEditorCSS = '' ;	// FCKConfig.SkinPath + "|<minified css>" ;
FCKConfig.SkinDialogCSS = '' ;	// FCKConfig.SkinPath + "|<minified css>" ;

FCKConfig.Plugins.Add('bbcode') ;

FCKConfig.ToolbarSets['BBcode'] = [
['Bold','Italic', 'RemoveFormat', '-', 'Undo', 'Redo', '-', 'FitWindow']
];

FCKConfig.ForcePasteAsPlainText	= true;
FCKConfig.ShowDropDialog = true;