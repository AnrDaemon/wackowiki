<?php
/*

Typografica library: typografica class.

https://wackowiki.org/doc/Dev/Projects/Typografica

*/

class Typografica
{
	public $wacko;
	public $skip_tags	= true;
	public $p_prefix	= '<p class="typo">';
	public $p_postfix	= '</p>';
	public $asoft		= true;
	public $indent1	= 'image/spacer.png" width=25 height=1 border=0 alt="">'; // <->
	public $indent2	= 'image/spacer.png" width=50 height=1 border=0 alt="">'; // <-->
	public $fixed_size	= 80; // maximum width
	public $ignore		= '/(<!--notypo-->.*?<!--\/notypo-->)/usi'; // regex to be ignored
	public $de_nobr	= true;

	public $phonemasks	= [
							[
								"/(\d{4})\-(\d{2})\-(\d{2}) (\d{2}):(\d{2}):(\d{2})/",
								"/(\d{4})\-(\d{2})\-(\d{2})/",
								"/(\([\d\+\-]+\)) ?(\d{3})\-(\d{2})\-(\d{2})/",
								"/(\([\d\+\-]+\)) ?(\d{2})\-(\d{2})\-(\d{2})/",
								"/(\([\d\+\-]+\)) ?(\d{3})\-(\d{2})/",
								"/(\([\d\+\-]+\)) ?(\d{2})\-(\d{3})/",
								"/(\d{3})\-(\d{2})\-(\d{2})/",
								"/(\d{2})\-(\d{2})\-(\d{2})/",
								"/(\d{1})\-(\d{2})\-(\d{2})/",
								"/(\d{2})\-(\d{3})/",
								"/(\d+)\-(\d+)/",
							],
							[
								"<nobr>\\1–\\2–\\3\u{00A0}\\4:\\5:\\6</nobr>",
								"<nobr>\\1–\\2–\\3</nobr>",
								"<nobr>\\1\u{00A0}\\2–\\3–\\4</nobr>",
								"<nobr>\\1\u{00A0}\\2–\\3–\\4</nobr>",
								"<nobr>\\1\u{00A0}\\2–\\3</nobr>",
								"<nobr>\\1\u{00A0}\\2–\\3</nobr>",
								"<nobr>\\1–\\2–\\3</nobr>",
								"<nobr>\\1–\\2–\\3</nobr>",
								"<nobr>\\1–\\2–\\3</nobr>",
								"<nobr>\\1–\\2</nobr>",
								"<nobr>\\1–\\2</nobr>",
							]
						];

	public $glueleft	= ["рис\.", "табл\.", "см\.", "им\.", "ул\.", "пер\.", "кв\.", "офис", "оф\.", "г\."]; // contains some Russian abberviations, also see below
	public $glueright	= ["руб\.", "коп\.", "у\.е\.", "мин\."];

	public $settings	= [
							'inches'	=> 1, // convert inches into &quot;
							'apostroph'	=> 1, // apostroph converter
							'laquo'		=> 0, // angle quotes
							'farlaquo'	=> 0, // angle quotes for FAR (greater&less characters)
							'quotes'	=> 0, // English quotes
							'dash'		=> 1, // (150) - middle dash
							'emdash'	=> 1, // (151) - long dash by two minus
							'(c)'		=> 1, // special characters, as you know
							'(r)'		=> 1,
							'(tm)'		=> 1,
							'(p)'		=> 1,
							'+-'		=> 1,
							'degrees'	=> 1, // degree character
							'[--]'		=> 1, // indents like $Indent*
							'dashglue'	=> 1, // dash glue
							'wordglue'	=> 1, // word glue
							'spacing'	=> 1, // comma and spacing, exchange
							'phones'	=> 0, // phone number processing
							'fixed'		=> 0, // fit to fixed width
							'html'		=> 0  // HTML tags ban
	];

	function __construct(&$wacko)
	{
		$this->wacko	= &$wacko;
		$this->indent1	= '<img src="' . $wacko->db->base_path . $this->indent1;
		$this->indent2	= '<img src="' . $wacko->db->base_path . $this->indent2;
	}

	///////////////////////////////////////////////////////////////////////////////////////////////////////////
	///////////////////////////////////////////////////////////////////////////////////////////////////////////
	function correct($data)
	{
		// -2. ignoring a (or next?) regexp
		$ignored = [];
		{
			$total	= preg_match_all($this->ignore, $data, $matches);
			$data	= preg_replace($this->ignore, '{:typo:markup:2:}', $data);

			for ($i = 0; $i < $total; $i++)
			{
				$ignored[] = $matches[0][$i];
			}
		}

		// -1. HTML tags ban
		if ($this->settings['html'])
		{
			$data = str_replace('&', '&amp;', $data);
		}

		// 0. Stripping tags
		// actulally, tag similarity is a problem.
		//   case 1, simple (ending tag) </abcz>
		//   case 2, simple (just a tag) <abcz>
		//   case 3, a bit difficult     <abcz href="abcz">
		//   case 4, simple (just a tag) <abcz />
		//   case 5, wiki               <!--link:begin-->...==
		//   most difficult case - tag parameter contains ">" character
		//   it's here: <abcz href="abcz>">
		//  how does stripping work? let's assume a special character. Yes-yes, special character
		//    it would be stick (?like bee or mosquito?) within us =)
		//  will change all tags with special character, simultaneously store them into an array.
		//  and then believe, there are no special characters in the wild world (unexplored wilderness?).
		$tags = [];

		if ($this->skip_tags)
		{
			$re		= '/<\/?[a-z\d]+(' .			// tag name
									'\s+(' .		// repeatable statement: if only one delimiter and little body
									'[a-z]+(' .		// alpha-composed attribute, could be followed by equals character
											'=((\'[^\']*\')|(\"[^\"]*\")|([\d@\-_a-z:\/?&=\.]+))' .
											')?' .
										')?' .
									')*\/?>|' .
						'<\!--link:begin-->[^\n]*?==|' .
						'<\!--imglink:begin-->[^\n]*?==' .
					'/ui';
			$total	= preg_match_all($re, $data, $matches);
			$data	= preg_replace($re, '{:typo:markup:1:}', $data);

			for ($i = 0; $i < $total; $i++)
			{
				if ($this->settings['html'])
				{
					$matches[0][$i] = '&lt;' . mb_substr($matches[0][$i], 1);
				}

				$tags[] = $matches[0][$i];
			}
		}

		// 1. Commas and spaces
		if ($this->settings['spacing'])
		{
			$data = preg_replace('/(\s*)([,]*)/ui', "\\2\\1", $data);
			$data = preg_replace('/(\s*)([\.?!]*)(\s*[¨À-ßA-Z])/u', "\\2\\1\\3", $data);
		}

		// 2. Splitting to strings with length no more than XX characters
		// --- not ported to wacko ---
		// --- not ported to wacko ---

		// 3. Special characters
		$data = $this->replace_specials($data);

		// 4. Short words and &nbsp;
		if ($this->settings['wordglue'])
		{
			$data	= ' ' . $data . ' ';
			$_data	= ' ' . $data . ' ';

			while ($_data != $data)
			{
				$_data	= $data;
				$data	= preg_replace('/(\s+)([a-zÀ-ÿ]{1,2})(\s+)([^\\s$])/ui', "\\1\\2\u{00A0}\\4", $data);	// \u{00A0} No-Break Space (NBSP)
				$data	= preg_replace('/(\s+)([a-zÀ-ÿ]{3})(\s+)([^\\s$])/ui',   "\\1\\2\u{00A0}\\4", $data);
			}

			foreach ($this->glueleft as $i)
			{
				$data = preg_replace('/(\s+)(' . $i . ')(\s+)/ui', "\\1\\2\u{00A0}", $data);
			}

			foreach ($this->glueright as $i)
			{
				$data = preg_replace('/(\s+)(' . $i . ')(\s+)/ui', "\u{00A0}\\2\\3", $data);
			}
		}

		// 5. Sticking flippers together. Psaw! Concatenation of hyphens
		if ($this->settings['dashglue'])
		{
			$data = preg_replace('/([a-zÀ-ÿ\d]+(\-[a-zÀ-ÿ\d]+)+)/ui', "<nobr>\\1</nobr>", $data);
		}

		// 6. Macros
		$data = $this->replace_macros($data);

		// 7. Line feeds
		// --- not ported to wacko ---
		// --- not ported to wacko ---

		// INFINITY. Inserting tags back.
		if ($this->skip_tags)
		{
			$data .= ' ';
			$a = explode('{:typo:markup:1:}', $data);

			if ($a)
			{
				$data = $a[0];
				$size = count($a);

				for ($i = 1; $i < $size; $i++)
				{
					$data = $data . $tags[$i - 1] . $a[$i];
				}
			}
		}

		// INFINITY-2. inserting a (next?) ignored regexp
		{
			$data .= ' ';
			$a = explode('{:typo:markup:2:}', $data);

			if ($a)
			{
				$data = $a[0];
				$size = count($a);

				for ($i = 1; $i < $size; $i++)
				{
					$data = $data . $ignored[$i - 1] . $a[$i];
				}
			}
		}

		// BONUS: link scrolling via A(...)
		// --- not ported to wacko ---
		// --- not ported to wacko ---

		// ooh, finished
		if ($this->de_nobr)
		{
			$data = str_replace('<nobr>', '<span class="nobr">', str_replace('</nobr>', '</span>', $data));
		}

		return preg_replace('/^(\s)+/u', '',  preg_replace('/(\s)+$/u', '', $data));
	}
	///////////////////////////////////////////////////////////////////////////////////////////////////////////

	// -----------------------------------------------------------------------------------
	// Method is only for internal use. Checks only special characters
	function replace_specials($data)
	{
		// print "(($data))";
		// 0. inches with digits
		if ($this->settings['inches'])
		{
			$data = preg_replace('/(?<=\s)((\d{1,2}([\.,]\d{1,2})?))\"/ui', '\\1"', $data);	// \u{0022}
		}

		// 0a. apostroph
		if ($this->settings['apostroph'])
		{
			$data = preg_replace("/([\s\"][~\d¸¨´¥ºª³²¿¯’'A-Za-zÀ-ßà-ÿ\-:\/\.]+)'([~ºª³²¿¯àÀåÅèÈîÎóÓþÞÿß][~\d¸¨´¥ºª³²¿¯’'A-Za-zÀ-ßà-ÿ\-:\/\.]+[\s\.,:;\)<=\"])/ui", "\\1’\\2", $data );
		}

		// 1. English quotes
		if ($this->settings['quotes'])
		{
			$data	= preg_replace('/\"\"/u', '&quot;&quot;', $data);
			$data	= preg_replace('/\"\.\"/u', '&quot;.&quot;', $data);
			$_data	= "\"\"";

			while ($_data != $data)
			{
				$_data	= $data;
				$data	= preg_replace('/(^|\s|\{:typo:markup:2:}|{:typo:markup:1:}|>)\"([A-Za-z\d\'\!\s\.\?\,\-\&\;\:\_{:typo:markup:1:}{:typo:markup:2:}]+(\"|\u{0094}))/ui', "\\1\u{0093}\\2", $data);			// \u{0093} <Set Transmit State>
				$data	= preg_replace('/(\u{0093}([A-Za-z\d\'\!\s\.\?\,\-\&\;\:{:typo:markup:1:}{:typo:markup:2:}\_]*).*[A-Za-z\d][{:typo:markup:1:}{:typo:markup:2:}\?\.\!\,]*)\"/ui', "\\1\u{0094}", $data);	// \u{0094} <Cancel Character>
			}
		}

		// 2. angle quotes
		if ($this->settings['laquo'])
		{
			$data	= preg_replace('/\"\"/u', '&quot;&quot;', $data);
			$data	= preg_replace("/(^|\s|{:typo:markup:2:}|{:typo:markup:1:}|>|\()\"(({:typo:markup:2:}|{:typo:markup:1:})*[~\d¸¨´¥ºª³²¿¯’'A-Za-zÀ-ßà-ÿ\-:\/\.])/ui", "\\1«\\2", $data);
			// nb: wacko only regexp follows:
			$data	= preg_replace("/(^|\s|\{:typo:markup:2:}|{:typo:markup:1:}|>|\()\"(({:typo:markup:2:}|{:typo:markup:1:}|\/\u{00A0}|\/|\!)*[~\d¸¨´¥ºª³²’'A-Za-zÀ-ßà-ÿ\-:\/\.])/ui", "\\1«\\2", $data);
			$_data	= "\"\"";

			while ($_data != $data)
			{
				$_data	= $data;
				$data	= preg_replace("/(\&laquo\;([^\"]*)[¸¨´¥ºª³²¿¯’'A-Za-zÀ-ßà-ÿ\d\.\-:\/](\{:typo:markup:2:}|{:typo:markup:1:})*)\"/usi", "\\1»", $data);
				// nb: wacko only regexps follows:
				$data	= preg_replace("/(\&laquo\;([^\"]*)[¸¨´¥ºª³²¿¯’'A-Za-zÀ-ßà-ÿ\d\.\-:\/](\{:typo:markup:2:}|{:typo:markup:1:})*\?({:typo:markup:2:}|{:typo:markup:1:})*)\"/usi", "\\1»", $data);
				$data	= preg_replace("/(\&laquo\;([^\"]*)[¸¨´¥ºª³²¿¯’'A-Za-zÀ-ßà-ÿ\d\.\-:\/](\{:typo:markup:2:}|{:typo:markup:1:}|\/|\!)*)\"/usi", "\\1»", $data);
			}
		}

		// 2a. angle quotes for FAR manager
		// --- not ported to wacko ---
		// --- not ported to wacko ---

		// 2b. angle and English quotes together
		if (($this->settings['quotes']) && (($this->settings['laquo']) || ($this->settings['farlaquo'])))
		{
			$data = preg_replace("/(\u{0093}(([A-Za-z\d'!\.?,\-&;:]|\s|{:typo:markup:1:}|{:typo:markup:2:})*)«(.*)»)»/ui", "\\1\u{0094}", $data);		// \u{0094} <Cancel Character>
		}

		// 3. dash
		if ($this->settings['dash'])
		{
			$data = preg_replace('/(\s|;)\-(\s)/ui', "\\1–\\2", $data);			// \u{2013}
		}

		// 3a. long dash
		if ($this->settings['emdash'])
		{
			$data = preg_replace('/(\s|;)\-\-(\s)/ui', "\\1—\\2", $data);		// \u{2014}
		}

		// 4. (c)
		if ($this->settings['(c)'])
		{
			$data = preg_replace('/\([cCñÑ]\)/ui', '©', $data);			// \u{00A9}
		}

		// 4a. (r)
		if ($this->settings['(r)'])
		{
			$data = preg_replace('/\(r\)/ui', '<sup>®</sup>', $data);	// \u{00AE}
		}

		// 4b. (tm)
		if ($this->settings['(tm)'])
		{
			$data = preg_replace('/\(tm\)|\(тм\)/ui', '™', $data);		// \u{2122}
		}

		// 4c. (p)
		if ($this->settings['(p)'])
		{
			$data = preg_replace('/\(p\)/ui', '§', $data);				// \u{00A7}
		}

		// 5. +/-
		if ($this->settings['+-'])
		{
			$data = preg_replace('/\+\-/u', '±', $data);				// \u{00B1}
		}

		// 5a. 12°C
		if ($this->settings['degrees'])
		{
			$data = preg_replace('/-(\d)+\^([FCÑK])/u', "-\\1°\\2", $data);	// \u{00B0} Degree Sign
			$data = preg_replace('/\+(\d)+\^([FCÑK])/u', "+\\1°\\2", $data);	// \u{00B0}
			$data = preg_replace('/\^([FCÑK])/u', "°\\1", $data);				// \u{00B0}
		}

		// 6. phones
		if ($this->settings['phones'])
		{
			foreach ($this->phonemasks[0] as $i => $v)
			{
				$data = preg_replace($v, $this->phonemasks[1][$i], $data);
			}
		}

		return $data;
	}

	// -----------------------------------------------------------------------------------
	// Method is only for internal use. Checks only macros
	function replace_macros($data)
	{
		// 1. Paragraphs
		// --- not ported to wacko ---

		// 2. Paragpaph indent (indented line)
		if ($this->settings['[--]'])
		{
			$data = preg_replace('/\[--\]/u', $this->indent1, $data);
			$data = preg_replace('/\[---\]/u', $this->indent2, $data);
		}

		// 3. mailto:
		// --- not ported to wacko ---

		// 4. http://
		// --- not ported to wacko ---

		return $data;
	}

}
