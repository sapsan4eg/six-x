<?php
class Bundles
{
	private static $bundles = array();
	{
		self::setValue( 'js', self::TakeJs('jquery-1.11.1.min') . self::TakeJs('bootstrap.min') );
		self::setValue( 'jsvalidate', self::TakeJs('jquery.validate.min'));
		self::setValue( 'awesome', self::TakeCss('font-awesome/css/font-awesome.min'));
		self::setValue( 'mirror', self::TakeCss('codemirror/codemirror') . self::TakeCss('codemirror/blackboard.min') . 
						self::TakeCss('codemirror/monokai.min') . self::TakeJs('codemirror/codemirror') . 
						self::TakeJs('codemirror/mode/xml/xml'));
		self::setValue( 'summer', self::TakeCss('summernote/summernote') . self::TakeJs('summernote/summernote'));
		self::setValue( 'payment', self::TakeJs('jquery.payment') . self::TakeCss('payment'));
	private static function setValue($key, $value)
	{
	public static function getValue($key)
	{
    private static function TakeCss($link ='')
	{
	private static function TakeJs($link ='')
	{
	   return "<script src=\"" . HTTP_SERVER . DIR_SCRIPTS . $link .".js\"></script>". PHP_EOL;
	}
?>