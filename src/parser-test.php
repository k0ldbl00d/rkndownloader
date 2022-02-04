<?php

require_once("parser.php");
require_once("util.php");

$p = new Parser();
$p->readZip("./data/soc-latest.zip");