<?php
session_start();
session_destroy(); // 銷毀所有登入紀錄
header("Location: login.php"); // 轉跳回登入頁
?>