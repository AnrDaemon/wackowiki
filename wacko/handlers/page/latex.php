<div id="page">
<?php

// redirect to show method if page don't exists
if (!$this->page) $this->Redirect($this->href("show"));

if ($this->HasAccess("read"))
{
	if (!$this->page)
	{
		print(str_replace("%1",$this->href("edit"),$this->GetTranslation("DoesNotExists")));
	}
	else
	{
		// comment header?
		if ($this->page["comment_on_id"])
		{
			print("<div class=\"commentinfo\">".$this->GetTranslation("ThisIsCommentOn")." ".$this->ComposeLinkToPage($this->GetCommentOnTag($this->page["comment_on_id"]), "", "", 0).", ".$this->GetTranslation("PostedBy")." ".($this->IsWikiName($this->page["user_name"])?$this->Link($this->page["user_name"]):$this->page["user_name"])." ".$this->GetTranslation("At")." ".$this->page["modified"]."</div>");
		}

		if ($this->page["latest"] == "0")
		{
			print("<div class=\"revisioninfo\">".
			str_replace("%1",$this->href(),
			str_replace("%2",$this->tag,
			str_replace("%3",$this->page["modified"],
			$this->GetTranslation("Revision")))).".</div>");
		}

		// display page
		$this->context[++$this->current_context] = $this->tag;
		$text = preg_replace("/{{(tableofcontents|toc).*?}}/i", "", $this->page["body"]);
		$data = $this->Format($text, "wiki");

		// Convert everything that doesn't need regexps
		$data = str_replace(
		array(
		"<br />\n",						// Strip newlines
		'&nbsp;',						// Blanks to blanks for easier handling
		'<strong>',						// Bold
		'</strong>',
		'<em>',							// Emphasized
		'</em>',
		'<small>',						// Small
		'</small>',
		'<tt>',							// Monospaced
		'</tt>',
		'<li>',							// List item
		'</li>',
		'</ul>',						// End of unnumbered list
		'</ol>',						// End of numbered list
		),
		array(
		"\n",
		' ',
		'\textbf{',
		'}',
		'\emph{',
		'}',
		'\textsmaller{',
		'}',
		'\texttt{',
		'}',
		'&nbsp;&nbsp;&nbsp;&nbsp;\item ',
		"\n",
		"\\end{itemize}\n\n",
		"\\end{enumerate}\n\n",
		),
		$data
		);

		// Convert the cool stuff
		$data = preg_replace(
		array(
		'|%%\(math\)(.*)%%|Us',			// Math formula
		'|%%\(math outline\)(.*)%%|Us',	// Math outline
		'|<h1>\s*?(.*)\s*</h1>|U',		// Headings
		'|<h2>\s*?(.*)\s*</h2>|U',
		'|<h3>\s*?(.*)\s*</h3>|U',
		'|<ul[^>]*>|U',					// Unnumbered list
		'|<ol[^>]*>|U',					// Numbered list
		'|<p.*>(.*)</p>|Us',			// Separate paragraphs by blank line
		'|</?.*>|U',					// Strip all other HTML tags
		'|( *\n){3,}|m',				// Cut all \n\n\n... to \n\n
		),
		array(
		'$\\1$',
		'$$\1$$',
		"\n\\section{\\1}\n",
		"\n\\subsection{\\1}\n",
		"\n\\subsubsection{\\1}\n",
		"\n\\begin{itemize}\n",
		"\n\\begin{enumerate}\n",
		"\\1\n\n",
		'',
		"\n\n",
		),
		$data
		);

		print '<code>'.nl2br(trim($data)).'</code>';

		$this->current_context--;

	}
}
else
{
	print($this->GetTranslation("ReadAccessDenied"));
}
?>
</div>
