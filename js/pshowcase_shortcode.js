(function() {    
    tinymce.PluginManager.add('wp_posts_showcase_button', function(ed, url) {
            ed.addButton('wp_posts_showcase_button', {
                title : 'WP Posts Showcase',
                image : url+'/../images/shortcode-icon.png',
                onclick : function() {
                    ed.execCommand('mceInsertContent', false, '[wp_posts_showcase]');
               }
            });        
    });
})();