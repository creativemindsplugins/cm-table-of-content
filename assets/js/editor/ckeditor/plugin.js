/*
 Copyright (c) 2003-2010, CKSource - Frederico Knabben. All rights reserved.
 For licensing, see LICENSE.html or http://ckeditor.com/license
 */

/**
 * @file Sample plugin for CKEditor.
 */
(function () {
    CKEDITOR.plugins.add('cmtoc_table_of_content',
            {
                init: function (editor)
                {
                    var a = {
                        exec: function (editor) {
                            var selection = editor.getSelection();
                            var text = selection.getSelectedText();
                            var nodeHtml = selection.getStartElement();
                            console.log(nodeHtml);
                            console.log(nodeHtml.innerHTML);
                            nodeHtml.setText(nodeHtml.getText().replace(text, '[table_of_content_exclude]' + text + '[/table_of_content_exclude]'));
                        }
                    };

                    editor.addCommand('cmtoc_exclude_cmd', a);

                    editor.ui.addButton('cmtoc_exclude',
                            {
                                label: 'Exclude from CM Table of Contents',
                                command: 'cmtoc_exclude_cmd',
                                toolbar: 'links',
                                icon: this.path + '../icon.png'
                            });

                    var b = {
                        exec: function (editor) {
                            var selection = editor.getSelection();
                            var text = selection.getSelectedText();
                            var nodeHtml = selection.getStartElement();
                            console.log(nodeHtml);
                            console.log(nodeHtml.innerHTML);
                            nodeHtml.setText(nodeHtml.getText().replace(text, '[cm_table_of_content_parse]' + text + '[/cm_table_of_content_parse]'));
                        }
                    };

                    editor.addCommand('cmtoc_parse_cmd', b);

                    editor.ui.addButton('cmtoc_parse',
                            {
                                label: 'Parse with CM Table of Contents',
                                command: 'cmtoc_parse_cmd',
                                toolbar: 'links',
                                icon: this.path + '../icon.png'
                            });
                }
            });
})();
