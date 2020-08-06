<?php

declare(strict_types=1);

namespace axios\tools;

class Path
{
    const DS = \DIRECTORY_SEPARATOR;

    /**
     * path join.
     *
     * @param mixed ...$paths
     *
     * @return string
     */
    public static function join(...$paths)
    {
        if (0 === \count($paths)) {
            throw new \InvalidArgumentException('At least one parameter needs to be passed in.');
        }
        $pathResult = explode(self::DS, $paths[0]);
        unset($paths[0]);
        $pathResultLen = \count($pathResult);
        if ('' === $pathResult[$pathResultLen - 1]) {
            unset($pathResult[$pathResultLen - 1]);
        }
        foreach ($paths as $path) {
            $tmp = explode(self::DS, $path);
            foreach ($tmp as $str) {
                if ('..' === $str) {
                    array_pop($pathResult);
                } elseif ('.' === $str || '' === $str) {
                    continue;
                } else {
                    array_push($pathResult, $str);
                }
            }
        }

        return implode(self::DS, $pathResult);
    }

    /**
     * search files.
     *
     * @param string $dir
     * @param string $extInclude
     * @param false  $asc
     * @param int    $sorting_type
     *
     * @return array
     */
    public static function search($dir, $extInclude = '*', $asc = false, $sorting_type = SORT_FLAG_CASE)
    {
        $list = [];
        if (is_dir($dir)) {
            $dirHandle = opendir($dir);
            while (!\is_bool($dirHandle) && false !== ($file_name = readdir($dirHandle))) {
                $tmp = str_replace('.', '', $file_name);
                if ('' != $tmp) {
                    $subFile = self::join($dir, $file_name);
                    $ext     = pathinfo($file_name, PATHINFO_EXTENSION);
                    if (is_dir($subFile)) {
                        $list = array_merge($list, self::search($subFile, $extInclude, $asc, $sorting_type));
                    } elseif (\is_string($extInclude)) {
                        if ('*' == $extInclude || preg_match($extInclude, $file_name)) {
                            $list[\count($list)] = $subFile;
                        }
                    } elseif (\is_array($extInclude) && \in_array($ext, $extInclude)) {
                        $list[\count($list)] = $subFile;
                    }
                }
            }
            closedir($dirHandle);
        }
        $asc ? ksort($list, $sorting_type) : krsort($list, $sorting_type);

        return $list;
    }
}
