/*
Copyright (c) 2003-2012, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.editorConfig = function( config )
{
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
	config.toolbar =
    [
	{ name: 'document', items : [ 'Source' ] },
	{ name: 'clipboard', items : [ 'Undo','Redo' ] },
	{ name: 'editing', items : [ 'Find','Replace','-','SpellChecker'] },
	{ name: 'basicstyles', items : [ 'Bold','Italic','Underline','Strike','Subscript','Superscript'] },
	{ name: 'paragraph', items : [ 'NumberedList','BulletedList'] },
	{ name: 'links', items : [ 'Link','Unlink','Anchor' ] },
	{ name: 'insert', items : [ 'SpecialChar'] },
	{ name: 'styles', items : [ 'Styles' ] },
	{ name: 'colors', items : [ 'TextColor','BGColor' ] }
    ];
};
