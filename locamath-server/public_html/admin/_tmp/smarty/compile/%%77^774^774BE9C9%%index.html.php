<?php /* Smarty version 2.6.18, created on 2013-06-20 09:31:37
         compiled from index.html */ ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title><?php echo $this->_tpl_vars['application_name']; ?>
</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="title"       content="<?php echo $this->_tpl_vars['meta_title']; ?>
">
<meta name="keywords"    content="<?php echo $this->_tpl_vars['meta_keywords']; ?>
">
<meta name="description" content="<?php echo $this->_tpl_vars['meta_description']; ?>
">
<link href="<?php echo $this->_tpl_vars['shared_resources']; ?>
main.css" rel="stylesheet" type="text/css"></link>
<link href="<?php echo $this->_tpl_vars['shared_resources']; ?>
ui.css" rel="stylesheet" type="text/css"></link>
<link rel="shortcut icon" href="<?php echo $this->_tpl_vars['resources']; ?>
favicon.ico" />
</head>

<body>
    <div class="main-container">
        <div class="block" id="header"> 
            <!-- Logo --> 
            <h1 id="logo"><a href="<?php echo $this->_tpl_vars['relative_url']; ?>
"><?php echo $this->_tpl_vars['application_title']; ?>
</a></h1> 
            <div id="buttons">
                You are logged in as <?php echo $this->_tpl_vars['user_name']; ?>
&nbsp;
                <a href="javascript:__Popup('<?php echo $this->_tpl_vars['relative_url']; ?>
index.php?_en=my_account&_pw=1');">My account</a>&nbsp;|
                <a href="<?php echo $this->_tpl_vars['relative_url']; ?>
index.php?_en=logout">Logout</a>
            </div> 
            <?php echo $this->_tpl_vars['menu']; ?>

        </div>
        
        <div style="clear:both;width:100%;height:90%;">
        <?php echo $this->_tpl_vars['content']; ?>

        </div>        
    </div>

</body>
</html>