<?php
/**
 * @package moviePlugin
 */

class movieDeactivate
{

   public static function deactivate() {
        flush_rewrite_rules();
    }
}