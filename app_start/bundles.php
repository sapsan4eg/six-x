<?php
class Bundles
{
	private static $bundles = array();	public static function create()
	{		self::setValue( 'css', self::TakeCss('bootstrap') . self::TakeCss('main'));
		self::setValue( 'js', self::TakeJs('jquery-1.11.1.min') . self::TakeJs('bootstrap.min') );
		self::setValue( 'jsvalidate', self::TakeJs('jquery.validate.min'));
		self::setValue( 'awesome', self::TakeCss('font-awesome/css/font-awesome.min'));
		self::setValue( 'mirror', self::TakeCss('codemirror/codemirror') . self::TakeCss('codemirror/blackboard.min') . 
						self::TakeCss('codemirror/monokai.min') . self::TakeJs('codemirror/codemirror') . 
						self::TakeJs('codemirror/mode/xml/xml'));
		self::setValue( 'summer', self::TakeCss('summernote/summernote') . self::TakeJs('summernote/summernote'));
		self::setValue( 'payment', self::TakeJs('jquery.payment') . self::TakeCss('payment'));
		self::setValue( 'highcharts', self::TakeJs('highcharts/highcharts'));
		self::setValue( 'highcharts3d', self::TakeJs('highcharts/highcharts-3d'));	}
	private static function setValue($key, $value)
	{		isset(self::$bundles[$key]) ? self::$bundles[$key] .= $value : self::$bundles[$key] = $value;	}
	public static function getValue($key)
	{		return isset(self::$bundles[$key]) ? self::$bundles[$key] : null ;	}
    private static function TakeCss($link ='')
	{	   return "<link href=\"" . HTTP_SERVER . DIR_CONTENT . $link . ".css\" rel=\"stylesheet\">" . PHP_EOL;	}
	private static function TakeJs($link ='')
	{
	   return "<script src=\"" . HTTP_SERVER . DIR_SCRIPTS . $link .".js\"></script>". PHP_EOL;
	}}
?>