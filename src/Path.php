<?php

declare(strict_types=1);

namespace axios\tools;

class Path
{
    const DS = \DIRECTORY_SEPARATOR;

    /**
     * path join.
     *
     * @param string ...$paths
     */
    public static function join(string ...$paths): string
    {
        $is_win = \PHP_SHLIB_SUFFIX === 'dll';
        if (0 === count($paths)) {
            throw new \InvalidArgumentException('At least one parameter needs to be passed in.');
        }
        $base = array_shift($paths);
        if ($is_win && str_contains($base, \DIRECTORY_SEPARATOR)) {
            $pathResult = explode(\DIRECTORY_SEPARATOR, $base);
        } else {
            $pathResult = explode('/', $base);
        }

        $pathResultLen = count($pathResult);
        if ('' === $pathResult[$pathResultLen - 1]) {
            unset($pathResult[$pathResultLen - 1]);
        }
        foreach ($paths as $path) {
            $tmp = explode('/', $path);
            foreach ($tmp as $str) {
                if ('..' === $str) {
                    array_pop($pathResult);
                } elseif ('.' === $str || '' === $str) {
                    continue;
                } else {
                    $pathResult[] = $str;
                }
            }
        }

        return implode(\DIRECTORY_SEPARATOR, $pathResult);
    }

    /**
     * search files.
     *
     * @param array|string $extInclude
     */
    public static function search(string $dir, $extInclude = '*', bool $asc = false, int $sorting_type = \SORT_FLAG_CASE): array
    {
        $list = [];
        if (is_dir($dir)) {
            $dirHandle = opendir($dir);
            while (!\is_bool($dirHandle) && false !== ($file_name = readdir($dirHandle))) {
                $tmp = str_replace('.', '', $file_name);
                if ('' != $tmp) {
                    $subFile = self::join($dir, $file_name);
                    $ext     = pathinfo($file_name, \PATHINFO_EXTENSION);
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
