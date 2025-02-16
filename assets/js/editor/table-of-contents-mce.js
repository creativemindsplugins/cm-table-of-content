(function () {
    tinymce.create("tinymce.plugins.TableOfContents", {
        init: function (ed, url) {

            ed.addButton('cmtoc_exclude', {
                title: 'Exclude from CM Table of Contents',
                image: url + '/icon.png',
                onclick: function () {
                    ed.focus();
                    ed.selection.setContent('[table_of_content_exclude]' + ed.selection.getContent() + '[/table_of_content_exclude]');
                }
            });

            ed.addButton('cmtoc_parse', {
                title: 'Parse with CM Table of Contents',
                image: url + '/icon.png',
                onclick: function () {
                    ed.focus();
                    ed.selection.setContent('[cm_table_of_content_parse]' + ed.selection.getContent() + '[/cm_table_of_content_parse]');
                }
            });

        },
        getInfo: function () {
            return{
                longname: "CM Table of Contents",
                author: "CreativeMinds",
                authorurl: "https://www.cminds.com/",
                infourl: "https://www.cminds.com/",
                version: "2.0"
            };
        },
        createControl: function (n, cm) {
            return null;
        }
    });

    tinymce.PluginManager.add("cmtoc_table_of_content", tinymce.plugins.TableOfContents);
}());