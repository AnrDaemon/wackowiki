<div id="page" class="page">
<?php

if (!isset ($this->config["comments_count"])) $this->config["comments_count"] = 15;

if ($this->config['hide_rating'] != 1 && ($this->config["hide_rating"] != 2 || $this->GetUser()))
{
	// registering local functions
	// determine if user has rated a given page
	function HandlerShowPageIsRated(&$engine, $id)
	{
		$cookie	= $engine->GetCookie('rating');
		$ids	= explode(';', $cookie);
		if ($id = array_search($id, $ids))
			return true;
		else return false;
	}
}

// redirect from comment page to the commented one
if ($this->page["comment_on_id"])
{
	// count previous comments
	$count = $this->LoadSingle(
		"SELECT COUNT(tag) AS n ".
		"FROM {$this->config['table_prefix']}page ".
		"WHERE comment_on_id = '".quote($this->dblink, $this->page['comment_on_id'])."' ".
			"AND created <= '".quote($this->dblink, $this->page['created'])."' ".
		"GROUP BY comment_on_id ".
		"LIMIT 1", 1);

	// determine comments page number where this comment is located
	$p = ceil($count["n"] / $this->config["comments_count"]);

	// forcibly open page
	$this->Redirect($this->href("", $this->GetCommentOnTag($this->page["comment_on_id"]), 'show_comments=1&p='.$p).'#'.$this->page['tag']);
}

// display page body
if ($this->HasAccess("read"))
{
	if (!$this->page)
	{
		// Not sure what the point of wrapping it in the conditional was
		// if (function_exists("virtual")) header("HTTP/1.0 404 Not Found");
		header("HTTP/1.0 404 Not Found");

		print($this->GetTranslation("DoesNotExists") ." ".( $this->HasAccess("write") ?  str_replace("%1", $this->href("edit", "", "", 1), $this->GetTranslation("PromptCreate")) : ""));
	}
	else
	{
		// comment header?
		if ($this->page["comment_on_id"])
		{
			print("<div class=\"commentinfo\">".$this->GetTranslation("ThisIsCommentOn")." ".$this->ComposeLinkToPage($this->GetCommentOnTag($this->page["comment_on_id"]), "", "", 0).", ".$this->GetTranslation("PostedBy")." ".($this->IsWikiName($this->page["user_name"])?$this->Link($this->page["user_name"]):$this->page["user_name"])." ".$this->GetTranslation("At")." ".$this->GetTimeStringFormatted($this->page["modified"])."</div>");
		}

		// revision header
		if ($this->page["latest"] == "0")
		{
			print("<div class=\"revisioninfo\">".
			str_replace("%1",$this->href(),
			str_replace("%2",$this->tag,
			str_replace("%3",$this->GetPageTimeFormatted(),
			$this->GetTranslation("Revision")))));

			// if this is an old revision, display ReEdit button
			if ($this->HasAccess("write"))
			{
				$latest = $this->LoadPage($this->tag);
				?>
				<br />
				<?php echo $this->FormOpen("edit") ?>
				<input type="hidden" name="previous" value="<?php echo $latest["modified"] ?>" />
				<input type="hidden" name="id" value="<?php echo htmlspecialchars($this->page["page_id"]) ?>" />
				<input type="hidden" name="body" value="<?php echo htmlspecialchars($this->page["body"]) ?>" />
				<input type="submit" value="<?php echo $this->GetTranslation("ReEditOldRevision") ?>" />
				<input name="cancel" id="button" type="button" value="<?php echo $this->GetTranslation("EditCancelButton") ?>" onclick="document.location='<?php echo addslashes($this->href()) ?>';" />
				<?php echo $this->FormClose(); ?>
				<?php
			}

			echo "</div>";
		}

		// count page hit (we don't count for page owner)
		if ($this->GetUserId() != $this->page["owner_id"])
		{
			$this->Query(
				"UPDATE ".$this->config["table_prefix"]."page ".
				"SET hits = hits + 1 ".
				"WHERE page_id = '".quote($this->dblink, $this->page["page_id"])."'");
		}

		$this->SetLanguage($this->pagelang);

		// recompile if necessary
		if (($this->page["body_r"] == "") ||
		(($this->page["body_toc"] == "") && $this->config["paragrafica"]))
		{
			// build html body
			$this->page["body_r"] = $this->Format($this->page["body"], "wacko");

			// build toc
			if ($this->config["paragrafica"])
			{
				$this->page["body_r"]   = $this->Format($this->page["body_r"], "paragrafica");
				$this->page["body_toc"] = $this->body_toc;
			}

			// store to DB
			if ($this->page["latest"] != "0")
				$this->Query(
					"UPDATE ".$this->config["table_prefix"]."page SET ".
						"body_r = '".quote($this->dblink, $this->page["body_r"])."', ".
						"body_toc = '".quote($this->dblink, $this->page["body_toc"])."' ".
					"WHERE page_id = '".quote($this->dblink, $this->page["page_id"])."' ".
					"LIMIT 1");
		}

		// display page
		$data = $this->Format($this->page["body_r"], "post_wacko", array("bad" => "good"));
		$data = $this->NumerateToc( $data ); //  numerate toc if needed
		echo $data;

		$this->SetLanguage($this->userlang);
		?>
		<script type="text/javascript">
			var dbclick = "page";
		</script>
		<?php

	}
}
else
{
	// Not sure what the point of wrapping it in the conditional was
	// if (function_exists("virtual")) header("HTTP/1.0 403 Forbidden");
	header("HTTP/1.0 403 Forbidden");

	print($this->GetTranslation("ReadAccessDenied"));
}
?>
<br style="clear: both" />&nbsp;</div>
<?php
// If this page exists
if ($this->page)
{
	// files code starts
	if ($this->config["footer_files"])
	{

		if ($this->HasAccess("read") && $this->config["hide_files"] != 1 && ($this->config["hide_files"] != 2 || $this->GetUser()))
		{

			// store files display in session
			$tag = $this->tag;
			if (!isset($_SESSION[$this->config["session_prefix"].'_'."show_files"][$tag]))
			$_SESSION[$this->config["session_prefix"].'_'."show_files"][$tag] = ($this->UserWantsFiles() ? "1" : "0");

			if(isset($_GET["show_files"]))
			{
				switch($_GET["show_files"])
				{
					case "0":
						$_SESSION[$this->config["session_prefix"].'_'."show_files"][$tag] = 0;
						break;
					case "1":
						$_SESSION[$this->config["session_prefix"].'_'."show_files"][$tag] = 1;
						break;
				}
			}

			// display files!
			if ($this->page && $_SESSION[$this->config["session_prefix"].'_'."show_files"][$tag])
			{
				// display files header
				?>
	<a name="files" id="files"></a>
	<div id="filesheader"><?php echo $this->GetTranslation("Files_all") ?>
	<?php echo "[<a href=\"".$this->href("", "", "show_files=0")."\">".$this->GetTranslation("HideFiles")."</a>]"; ?>
	</div>

			<?php
			echo "<div class=\"files\">";
			echo $this->Action("files",array("nomark" => 1));
			echo "</div>";
			// display form
			if ($user = $this->GetUser())
			{
				$user = strtolower($this->GetUserName());
				$registered = true;
			}
			else
				$user = GUEST;

			if (isset($registered)
				&&
					(
						($this->config["upload"] === true) || ($this->config["upload"] == "1") ||
						($this->CheckACL($user,$this->config["upload"]))
					)
				)
			{
				echo "<div class=\"filesform\">\n";
				echo $this->Action("upload",array("nomark" => 1));
				echo "</div>\n";
			}
		}
		else
		{
			echo "<div id=\"filesheader\">";

			if ($this->page["page_id"])
			{
				// load files for this page
				$files = $this->LoadAll(
					"SELECT upload_id ".
					"FROM ".$this->config["table_prefix"]."upload ".
					"WHERE page_id = '". quote($this->dblink, $this->page["page_id"]) ."'");
			}
			else
			{
				$files = array();
			}

			switch ($c = count($files))
			{
				case 0:
					print($this->GetTranslation("Files_0"));
					break;
				case 1:
					print($this->GetTranslation("Files_1"));
					break;
				default:
					print(str_replace("%1", $c, $this->GetTranslation("Files_n")));
			}
			echo "[<a href=\"".$this->href("", "", "show_files=1#files")."\">".$this->GetTranslation("ShowFiles")."</a>]";
			echo "</div>\n";
		}
	}
	}
	// files form output ends
	?>
	<?php
	if ($this->config["footer_comments"])
	{
	// pagination
	$pagination = $this->Pagination($this->GetCommentsCount(), $this->config["comments_count"], 'p', 'show_comments=1#comments');

	// comments form output begins

		if ($this->HasAccess("read") && $this->config["hide_comments"] != 1 && ($this->config["hide_comments"] != 2 || $this->GetUser()))
		{
			// load comments for this page
			$comments = $this->LoadComments($this->page["page_id"], $pagination['offset'], $this->config["comments_count"]);

			// store comments display in session
			$tag = $this->tag;
			if (!isset($_SESSION[$this->config["session_prefix"].'_'."show_comments"][$tag]))
			$_SESSION[$this->config["session_prefix"].'_'."show_comments"][$tag] = ($this->UserWantsComments() ? "1" : "0");

			if(isset($_GET["show_comments"]))
			{
				switch($_GET["show_comments"])
				{
					case "0":
						$_SESSION[$this->config["session_prefix"].'_'."show_comments"][$tag] = 0;
						break;
					case "1":
						$_SESSION[$this->config["session_prefix"].'_'."show_comments"][$tag] = 1;
						break;
				}
			}

			// display comments!
			if ($this->page && $_SESSION[$this->config["session_prefix"].'_'."show_comments"][$tag])
			{
				// display comments header
				?>
			<a name="comments"></a>
		<div id="commentsheader">
		<?php if (isset($pagination['text']))
				echo '<div style="float:right; letter-spacing:normal;"><small><small>'.$pagination['text'].'</small></small></div>'; ?>
		<?php echo $this->GetTranslation("Comments_all")." [<a href=\"".$this->href("", "", "show_comments=0")."\">".$this->GetTranslation("HideComments")."</a>]"; ?>
			</div>
			<?php

			// display comments themselves
			if ($comments)
			{
				echo "<ol id=\"comments\">\n";

				foreach ($comments as $comment)
				{
					echo "<li id=\"".$comment["tag"]."\" class=\"comment\">\n";
					$del = "";
					if ($this->IsAdmin() || $this->UserIsOwner($comment["page_id"]) || ($this->config["owners_can_remove_comments"] && $this->UserIsOwner($this->page["page_id"])))
					{
						print("<a href=\"".$this->href("remove", $comment["tag"])."\"><img src=\"".$this->config["theme_url"]."icons/delete_comment.gif\" title=\"".$this->GetTranslation("DeleteCommentTip")."\" alt=\"".$this->GetTranslation("DeleteText")."\" align=\"right\" border=\"0\" /></a>");
						print("<a href=\"".$this->href("edit", $comment["tag"])."\"><img src=\"".$this->config["theme_url"]."icons/edit.gif\" title=\"".$this->GetTranslation("EditCommentTip")."\" alt=\"".$this->GetTranslation("EditComment")."\" align=\"right\" border=\"0\" /></a>");
					}
					if ($comment["body_r"]) $strings = $comment["body_r"];

					else $strings = $this->Format($comment["body"], "wacko");
					echo "<div class=\"commenttext\">\n";
					print("<div class=\"commenttitle\">\n<a href=\"".$this->href("", "", "show_comments=1")."#".$comment["tag"]."\">".$comment["title"]."</a>\n</div>\n");
					print($this->Format($strings,"post_wacko")."\n");
					echo "</div>\n";
					print("<ul class=\"commentinfo\">\n".
								"<li>".($comment["user"]
										? ($this->IsWikiName($comment["user"])
											? $this->Link("/".$comment["user"],"",$comment["user"])
											: $comment["user"])."</li>\n"
										: $this->GetTranslation("Guest")).
								"<li>".$this->GetTimeStringFormatted($comment["created"])."</li>\n".
								($comment["modified"] != $comment["created"]
									? "<li>".$this->GetTimeStringFormatted($comment["modified"])." ".$this->GetTranslation("CommentEdited")."</li>\n"
									: "").
							"</ul>\n");
					echo "</li>";
				}

				echo "</ol>";
			}

			if (isset($pagination['text']))
				echo '<div style="text-align:right;padding-right:10px;border-top:solid 1px #BABFC7;"><small><small>'.$pagination['text'].'</small></small></div>';

			// display comment form
			if ($this->HasAccess("comment"))
			{
				print("<div class=\"commentform\">\n");

				echo $this->FormOpen("addcomment"); ?>
					<label for="addcomment"><?php echo $this->GetTranslation("AddComment");?></label><br />
					<textarea id="addcomment" name="body" rows="6" cols="7" style="width: 100%"><?php if (isset($_SESSION['freecap_old_comment'])) echo $_SESSION['freecap_old_comment']; ?></textarea>

					<label for="addcomment_title"><?php echo $this->GetTranslation("AddCommentTitle");?></label><br />
					<input id="addcomment_title" name="title" size="60" maxlength="100"></input><br />
		<?php
					// captcha code starts

					// Only show captcha if the admin enabled it in the config file
					if($this->config["captcha_new_comment"])
					{
						// Don't load the captcha at all if the GD extension isn't enabled
						if(extension_loaded('gd'))
						{
							// check whether anonymous user
							// anonymous user has no name
							// if false, we assume it's anonymous
							if($this->GetUserName() == false)
							{
		?>
		<br />
		<br />
		<label for="captcha"><?php echo $this->GetTranslation("Captcha");?>:</label>
		<br />
		<img src="<?php echo $this->config["base_url"];?>lib/captcha/freecap.php" id="freecap" alt="<?php echo $this->GetTranslation("Captcha");?>" /> <a href="" onclick="this.blur(); new_freecap(); return false;" title="<?php echo $this->GetTranslation("CaptchaReload"); ?>"><img src="<?php echo $this->config["base_url"];?>images/reload.png" width="18" height="17" alt="<?php echo $this->GetTranslation("CaptchaReload"); ?>" /></a>
		<br />
		<input id="captcha" type="text" name="word" maxlength="6" style="width: 273px;" />
		<br />
		<br />
		<?php
							}
						}
					}
					// end captcha
		?>
		<input type="submit" value="<?php echo $this->GetTranslation("AddCommentButton"); ?>" accesskey="s" />
		<?php echo $this->FormClose(); ?>
		<?php
				print("</div>\n");
				}
			// end comment form
			}
			else
			{
		?>
		<div id="commentsheader">
		<?php
				$c = (int)$this->page["comments"];

				if		($c  <  1)	echo $this->GetTranslation('Comments_0');
				else if	($c === 1)	echo $this->GetTranslation('Comments_1');
				else if	($c  >  1)	echo str_replace('%1', $c, $this->GetTranslation('Comments_n'));

			//TODO: show link to show comment only if there is one or/and user has the right to add a new one
		?>
			[<a href="<?php echo $this->href("", "", "show_comments=1#comments")?>"><?php echo $this->GetTranslation("ShowComments"); ?></a>]</div>
		<?php
			}
		}
	}

	// rating form output begins
	if ($this->HasAccess('read') && $this->page && $this->config['hide_rating'] != 1 && ($this->config["hide_rating"] != 2 || $this->GetUser()))
	{
		// determine if user has rated this page
		if (HandlerShowPageIsRated($this, $this->page['page_id']) === false && $_GET['show_rating'] != 1)
		{
			// display rating header
			echo '<a name="rating"></a>';
			echo '<div id="rateheader">';
			echo $this->GetTranslation('RatingHeader').' [<a href="'.$this->href('', '', 'show_rating=1').'#rating">'.$this->GetTranslation('RatingResults').'</a>]';
			echo "</div>\n";

			// display rating form
			echo '<div class="rating">'.$this->FormOpen('rate').'';
			echo '<input id="minus3" name="value" type="radio" value="-3" /><label for="minus3">-3</label>'.
				 '<input id="minus2" name="value" type="radio" value="-2" /><label for="minus2">-2</label>'.
				 '<input id="minus1" name="value" type="radio" value="-1" /><label for="minus1">-1</label>'.
				 '<input id="plus0" name="value" type="radio" value="0" /><label for="plus0"> 0</label>'.
				 '<input id="plus1" name="value" type="radio" value="1" /><label for="plus1">+1</label>'.
				 '<input id="plus2" name="value" type="radio" value="2" /><label for="plus2">+2</label>'.
				 '<input id="plus3" name="value" type="radio" value="3" /><label for="plus3">+3</label>'.
				 '<input name="rate" id="submit" type="submit" value="'.$this->GetTranslation('RatingSubmit').'" />';
			echo ''.$this->FormClose().'</div>';
		}
		else
		{
			$results = $this->LoadSingle(
				"SELECT page_id, value, voters ".
				"FROM {$this->config['table_prefix']}rating ".
				"WHERE page_id = {$this->page['page_id']} ".
				"LIMIT 1");

			if ($results['voters'] > 0)			$results['ratio'] = $results['value'] / $results['voters'];
			if (is_float($results['ratio']))	$results['ratio'] = round($results['ratio'], 2);
			if ($results['ratio'] > 0)			$results['ratio'] = '+'.$results['ratio'];

			// display rating header
			echo '<a name="rating"></a>';
			echo '<div id="rateheader">';
			echo $this->GetTranslation('RatingHeaderResults').
				(HandlerShowPageIsRated($this, $this->page['page_id']) === false
					? ' [<a href="'.$this->href('', '', 'show_rating=0').'#rating">'.$this->GetTranslation('RatingForm').'</a>]'
					: '');
			echo "</div>\n";

			// display rating results
			if (isset($results['ratio']))
			{
				echo '<div class="rating">';
				echo ''.$this->GetTranslation('RatingTotal').': <strong>'.$results['ratio'].'</strong>'.
					 ' '.
					 ''.$this->GetTranslation('RatingVoters').': <strong>'.$results['voters'].'</strong>';
				echo '</div>';
			}
			else
			{
				echo '<div class="rating">';
				echo '<em>'.$this->GetTranslation('RatingNotRated').'</em>';
				echo '</div>';
			}
		}
	}
}

?>