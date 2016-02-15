<?php

if (!defined('IN_WACKO'))
{
	exit;
}

?>
<div id="page">
<?php

/*
	the source text will be shown with exception of those fragments which are hidden by formatters %%(comment)..%%

	TODO: add config option to set an treshhold or to disable the source handler
*/

// redirect to show method if page don't exists
if (!$this->page)
{
	$this->redirect($this->href());
}

// deny for comment
if ($this->page['comment_on_id'])
{
	$this->redirect($this->href('', $this->page['tag']));
}

if ($this->has_access('read'))
{
	/* obsolete code - or do we need an ability to print old revisions?
	if ($this->page['latest'] == 0)
	{
		echo "<div class=\"revisioninfo\">".
		str_replace('%1', $this->href(),
		str_replace('%2', $this->tag,
		str_replace('%3', $this->page['modified'],
		$this->get_translation('Revision')))).".</div>";
	}*/

	// build html body
	$data = $this->page['body'];

	// display page
	$data = $this->format($data, 'source', array('bad' => 'good'));
	echo $data;
}
else
{
	$message = $this->get_translation('ReadAccessDenied');
	$this->show_message($message, 'info');
}

?>
</div>