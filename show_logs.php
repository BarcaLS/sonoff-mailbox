<?php

echo "
<html>
<head>
    <meta http-equiv=\"Content-Type\" content=\"text/html;charset=utf-8\">
    <meta name=\"author\" content=\"Marcin Sarna\">
    <meta http-equiv=reply-to content=\"marcin@sarna.info\">
    <meta name=\"Description\" content=\"Logi skrzynki na listy\">
    <meta name=\"keywords\" content=\"sławków sarna logi skrzynka listy\">
    <link rel=\"stylesheet\" href=\"../default.css\" type=\"text/css\">
    <link rel=\"stylesheet\" href=\"../fonts/Sansation/stylesheet.css\" type=\"text/css\">
    <title>Logi skrzynki na listy</title>
</head>
<body>
<br><center><b>LOGI SKRZYNKI NA LISTY</b><br><br>
<a href=show_logs.php><img src=\"images/refresh.png\" width=50pt></a><br>
<pre>";
$logs = fopen('logs/mailbox.log', 'r');
echo fread($logs, filesize('logs/mailbox.log'));
fclose($logs);
echo "</body></html>";

?>
