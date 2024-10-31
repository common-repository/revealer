/* tinyMCE plugin JS
 * Written by Nimrod Tsabari
 * Since Version 0.1b
 * Version 2.0
 */
(function() {
    tinymce.create('tinymce.plugins.revealerplugin', {
        init : function(ed, url) {
		   ed.addCommand('revealerAct', function() {
                ed.windowManager.open({
                        file : url + '/revealer.htm',
                        width : 465,
                        height : 450,
                        inline : 1
                }, {
                        plugin_url : url, // Plugin absolute URL
                        some_custom_arg : 'custom arg' // Custom argument
                });
	        });          
	        ed.addButton('revealerplugin', {
                title : 'Add A Revealer',
                image : url + '/img/revealer.png',
                cmd : 'revealerAct',
            });
        },
        createControl : function(n, cm) {
            return null;
        },
        getInfo : function() {
            return {
                longname : "Revealer",
                author : 'Nimrod Tsabari / omniWP',
                authorurl : 'http://omniwp.com/',
                version : "2.0"
            };
        }
    });
    tinymce.PluginManager.add('revealerplugin', tinymce.plugins.revealerplugin);
})();