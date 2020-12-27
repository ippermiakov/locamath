<?php /* Smarty version 2.6.18, created on 2013-06-20 09:31:34
         compiled from login.html */ ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title><?php echo $this->_tpl_vars['application_name']; ?>
</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="title" content="<?php echo $this->_tpl_vars['meta_title']; ?>
">
<meta name="keywords" content="<?php echo $this->_tpl_vars['meta_keywords']; ?>
">
<meta name="description" content="<?php echo $this->_tpl_vars['meta_description']; ?>
">
<link href="<?php echo $this->_tpl_vars['shared_resources']; ?>
auth.css" rel="stylesheet" type="text/css"></link>
<link rel="shortcut icon" href="<?php echo $this->_tpl_vars['resources']; ?>
favicon.ico" />
</head>
<body bgcolor="#FFFFFF" leftmargin="0" topmargin="0">
<table class="default_text" width="100%" height="100%" border="0" cellpadding="5" cellspacing="0">
<form action="<?php echo $this->_tpl_vars['action']; ?>
" method="post" name="login">
  <tr height="100%">
    <td align="middle" valign="top" width="50%">
      <table class="auth-table">
        <tr> 
          <td colspan=4 align="center" class="title">
            <?php echo $this->_tpl_vars['application_name']; ?>
<br>
          </td>
        </tr>
        <tr> 
          <td width="25%"></td>
          <td width="25%" align="right">Login:</td>
          <td width="25%" align="left"><INPUT class="default_input" type="text" Size="30" id="edt_user_login" name="edt_user_login" value="<?php echo $this->_tpl_vars['edt_user_login']; ?>
">
          <td width="25%"></td>
          </td>
        </tr>
        <tr> 
          <td width="25%"></td>
          <td width="25%" align="right">Password:</td>
          <td width="25%" align="left"><INPUT class="default_input" type="password" size="30" id="edt_user_password" name="edt_user_password"></td>
          <td width="25%"></td>
        </tr>
        <tr> 
          <td width="25%"></td>
          <td width="25%" align="right"><label for="edt_remember_me">Remember me</label></td>
          <td width="25%" align="left"><INPUT class="default_check_box" type="checkbox" id="edt_remember_me" name="edt_remember_me" <?php echo $this->_tpl_vars['edt_remember_me']; ?>
></td>
          <td width="25%"></td>
        </tr>
        <tr> 
          <td width="25%"></td>
          <td colspan=2 align="center">
            <span class="error"><?php echo $this->_tpl_vars['error']; ?>
</span>
          </td>
          <td width="25%"></td>
        </tr>
        <tr> 
          <td width="25%"></td>
          <td width="50%" align="center" colspan=2><INPUT class="default_button" Name="submit" Type="submit" Value="  Login  "></td>
          <td width="25%"></td>
        </tr>
        <tr height="100%">
          <td colspan="4"></td>
        </tr>
      </table>
    </td>
  </tr>
</form>
</table>
<script language="JavaScript"><?php echo '
  var edit = document.getElementById(\'edt_user_login\');
  if (edit){ 
    edit.focus();
  }
'; ?>
</script>
</body>
</html>