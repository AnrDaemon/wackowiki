<?php

/**
 * Console renderer
 *
 * LICENSE: This source file is subject to version 3.0 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_0.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category   Text
 * @package    Text_Highlighter
 * @author     Andrey Demenev <demenev@gmail.com>
 * @copyright  2004-2006 Andrey Demenev
 * @license    http://www.php.net/license/3_0.txt  PHP License
 * @version    Release: 0.8.0
 * @link       http://pear.php.net/package/Text_Highlighter
 */

/**
 * @ignore
 */

const HL_CONSOLE_DEFCOLOR = "\033[0m";

/**
 * Console renderer
 *
 * Suitable for displaying text on color-capable terminals, directly
 * or trough less -r
 *
 * Elements of $options argument of constructor (each being optional):
 *
 * - 'numbers' - whether to add line numbers
 * - 'tabsize' - Tab size
 * - 'colors'  - additional colors
 */

class Text_Highlighter_Renderer_Console extends Text_Highlighter_Renderer
{

	/**#@+
	 * @access private
	 */

	/**
	 * class of last outputted text chunk
	 *
	 * @var string
	 */
	public $_lastClass;

	/**
	 * Line numbering
	 *
	 * @var boolean
	 */
	public $_numbers = false;

	/**
	 * Tab size
	 *
	 * @var integer
	 */
	public $_tabsize = 4;

	/**
	 * Highlighted code
	 *
	 * @var string
	 */
	public $_output = '';

	/**#@-*/

	public $_colors = [];

	public $_defColors = [
		'default'		=> "\033[0m",
		'inlinetags'	=> "\033[31m",
		'brackets'		=> "\033[36m",
		'quotes'		=> "\033[34m",
		'inlinedoc'		=> "\033[34m",
		'var'			=> "\033[1m",
		'types'			=> "\033[32m",
		'number'		=> "\033[32m",
		'string'		=> "\033[31m",
		'reserved'		=> "\033[35m",
		'comment'		=> "\033[33m",
		'mlcomment'		=> "\033[33m",
	];

	function preprocess($str)
	{
		// normalize whitespace and tabs
		$str = str_replace("\r\n","\n", $str);
		$str = str_replace("\t",str_repeat(' ', $this->_tabsize), $str);

		return rtrim($str);
	}


	/**
	 * Resets renderer state
	 *
	 * @access protected
	 *
	 *
	 * Descendents of Text_Highlighter call this method from the constructor,
	 * passing $options they get as parameter.
	 */
	function reset()
	{
		$this->_lastClass = '';

		if (isset($this->_options['numbers']))
		{
			$this->_numbers = (bool)$this->_options['numbers'];
		}
		else
		{
			$this->_numbers = false;
		}

		if (isset($this->_options['tabsize']))
		{
			$this->_tabsize = $this->_options['tabsize'];
		}
		else
		{
			$this->_tabsize = 4;
		}

		if (isset($this->_options['colors']))
		{
			$this->_colors = array_merge($this->_defColors, $this->_options['colors']);
		}
		else
		{
			$this->_colors = $this->_defColors;
		}

		$this->_output = null;
	}



	/**
	 * Accepts next token
	 *
	 * @access public
	 *
	 * @param  string $class   Token class
	 * @param  string $content Token content
	 */
	function acceptToken($class, $content)
	{
		$color = $this->_colors[$class] ?? $this->_colors['default'];

		if ($this->_lastClass != $class)
		{
			$this->_output .= $color;
		}

		$content = str_replace("\n", $this->_colors['default'] . "\n" . $color, $content);
		$content .= $this->_colors['default'];
		$this->_output .= $content;
	}

	/**
	* Signals that no more tokens are available
	*
	* @access public
	*
	*/
	function finalize()
	{
		if ($this->_numbers)
		{
			$nlines		= substr_count($this->_output, "\n") + 1;
			$len		= strlen($nlines);
			$i			= 1;

			$this->_output = preg_replace_callback('~^~m', function () use (&$i, $len) {
				return ' ' . str_pad($i++, $len, ' ', STR_PAD_LEFT) . ': ';
			}, $this->_output);
		}

		$this->_output .= HL_CONSOLE_DEFCOLOR . "\n";
	}

	/**
	 * Get generated output
	 *
	 * @return string Highlighted code
	 * @access public
	 *
	 */
	function getOutput()
	{
		return $this->_output;
	}
}
