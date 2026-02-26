/**
 * @license Copyright (c) 2003-2020, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see https://ckeditor.com/legal/ckeditor-oss-license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
config.filebrowserBrowseUrl = 'ckeditor4/ckfinder/ckfinder.html';
 
config.filebrowserImageBrowseUrl = 'ckeditor4/ckfinder/ckfinder.html?type=Images';
 
config.filebrowserFlashBrowseUrl = 'ckeditor4/ckfinder/ckfinder.html?type=Flash';
 
config.extraPlugins = 'youtube';
config.youtube_responsive = true;
config.youtube_controls = true;

};
