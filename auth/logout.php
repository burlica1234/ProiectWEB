<?php
session_start();
session_unset(); // sterge variabilele din $_SESSION
session_destroy(); // distruge sesiunea complet

header("Location: ../index.php");
exit;
