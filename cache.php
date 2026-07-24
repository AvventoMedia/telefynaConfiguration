<?php
    if (!is_dir('exports')) {
        mkdir('exports', 0777, true);
    }
    if (!empty($_POST)) {
        $now = date('Y-m-d_H-i-s');
        file_put_contents("exports/".$now.".txt", json_encode($_POST, JSON_PRETTY_PRINT)."\r\n______________________________________________________\r\n");
    }
?>