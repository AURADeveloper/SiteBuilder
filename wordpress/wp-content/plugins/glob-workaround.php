<?php
/** TODO workaround */
function get_php_files($dir, $ext = null) {
    if ($handle = opendir($dir)) {

        $found = Array();

        while (false !== ($file = readdir($handle))) {
            if ($ext == null) {
                $found[] = $dir . $file;
                continue;
            }
            if (strtolower(substr($file, strrpos($file, '.') + 1)) == $ext)  {
                $found[] = $dir . $file;
            }
        }
        closedir($handle);
        return $found;
    } else {
        return false;
    }
}

function get_folders($dir) {
    if ($handle = opendir($dir)) {

        $found = Array();

        while (false !== ($file = readdir($handle))) {
            if (is_dir($file)) {
                $found[] = $dir . $file;
            }
        }
        closedir($handle);
        return $found;
    } else {
        return false;
    }
}
