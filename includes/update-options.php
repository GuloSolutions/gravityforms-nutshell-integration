<?php

if (isset($_POST['checkbox'])) {
    foreach ($_POST['checkbox'] as $k=>$v) {
        update_option($k, 1);
    }
}
