<?php

namespace Web\Core;


class Logger
{
    private static $filename = '~/logger.log';

    public static function setLoggerMessage($file, $methode, $line, $messages)
    {
        $text = date("Y-m-d_H:i:s") . '##' . $file . '->' . $methode . '->' . $line . '->' . $messages . "\n";
        file_put_contents(self::$filename, $text, FILE_APPEND);
    }
}