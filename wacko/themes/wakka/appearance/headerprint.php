<?php
header('Content-Type: text/html; charset='.$this->get_charset());
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->page['lang'] ?>" lang="<?php echo $this->page['lang'] ?>">
<head>
<title><?php echo htmlspecialchars($this->config['site_name'])." : ".(isset($this->page['title']) ? $this->page['title'] : $this->tag); ?></title>
<?php // do not index alternative print pages
echo "<meta name=\"robots\" content=\"noindex, nofollow\" />\n";?>
<meta http-equiv="content-type" content="text/html; charset=<?php echo $this->get_charset(); ?>" />
<link rel="stylesheet" type="text/css" href="<?php echo $this->config['theme_url'] ?>css/print.css" />
</head>
<body>
<div class="header">
  <?php // Print wackoname and wackopath ?>
  <?php echo $this->config['site_name']; ?>: <?php echo (isset($this->page['title']) ? $this->page['title'] : $this->get_page_path()); ?> </div>
<!-- End of header //-->