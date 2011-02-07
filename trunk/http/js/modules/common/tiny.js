tinyMCE.init({
    theme: "advanced",
    mode: "textareas",
    language: "ru",
    plugins: "bbcode,paste,autoresize,fullscreen", 
    theme_advanced_buttons1: "bold,italic,removeformat,|,undo,redo,|,fullscreen,|,cleanup,pasteword",
    theme_advanced_buttons2: "",
    theme_advanced_buttons3: "",
    theme_advanced_toolbar_location: "top",
    theme_advanced_toolbar_align: "center",
    entity_encoding: "raw",
    
    add_unload_trigger : false,
    remove_linebreaks: true,
    inline_styles: false,
    convert_fonts_to_spans: false,
    convert_newlines_to_brs : true,

    remove_redundant_brs: false,
    valid_elements: "br,strong,em",
    force_br_newlines: true,
	force_p_newlines: false,
    forced_root_block: false,
    preformatted: true,

    paste_auto_cleanup_on_paste: true,
    paste_convert_middot_lists: 0,
    paste_block_drop: true,
    paste_retain_style_properties: "",
    paste_strip_class_attributesall: "all",
    paste_remove_spanstrue: true,
    paste_remove_stylestrue: true,
    paste_remove_styles_if_webkittrue: true,
    paste_dialog_widthtrue: true,
    paste_dialog_heighttrue: true,
    paste_text_stickytrue: true,
    paste_text_use_dialogtrue: true,

    fullscreen_new_window: false,
    fullscreen_settings: {
        theme_advanced_path_location: "top"
    }

});