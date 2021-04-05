<?php

declare(strict_types=1);

namespace axios\tools;

class Files
{
    /**
     * show files list in dir.
     */
    public static function ls(string $dir, bool $realpath = false, bool $asc = true, int $sorting_type = SORT_FLAG_CASE): array
    {
        $list = [];
        if (\is_dir($dir)) {
            $dirHandle = opendir($dir);
            while (!\is_bool($dirHandle) && false !== ($file_name = \readdir($dirHandle))) {
                if ('..' === $file_name || '.' === $file_name) {
                    continue;
                }
                if ($realpath) {
                    $file_name = Path::join($dir, $file_name);
                } else {
                    $real = Path::join($dir, $file_name);
                    if (\is_dir($real)) {
                        $file_name .= \DIRECTORY_SEPARATOR;
                    }
                }
                $list[] = $file_name;
            }
        }
        $asc ? \sort($list, $sorting_type) : \rsort($list, $sorting_type);

        return \array_values($list);
    }

    /**
     * search files.
     */
    public static function search(string $dir, ?array $extInclude = null, bool $asc = true, int $sorting_type = SORT_FLAG_CASE): array
    {
        $list = [];
        if (is_dir($dir)) {
            $dirHandle = opendir($dir);
            while (!\is_bool($dirHandle) && false !== ($file_name = readdir($dirHandle))) {
                $tmp = str_replace('.', '', $file_name);
                if ('' != $tmp) {
                    $subFile = Path::join($dir, $file_name);
                    $ext     = pathinfo($file_name, PATHINFO_EXTENSION);
                    if (is_dir($subFile)) {
                        $list = array_merge($list, self::search($subFile, $extInclude, $asc, $sorting_type));
                    } elseif (null === $extInclude || (\is_array($extInclude) && \in_array($ext, $extInclude))) {
                        $list[] = $subFile;
                    }
                }
            }
            closedir($dirHandle);
        }
        $asc ? sort($list, $sorting_type) : rsort($list, $sorting_type);

        return array_values($list);
    }

    /**
     * @param string $source copy from path
     * @param string $target copy to path
     * @param bool   $force  force copy if file exist when $force is true
     */
    public static function copy(string $source, string $target, bool $force = false, ?array $extInclude = null): void
    {
        if (!file_exists($source) || (file_exists($target) && !$force)) {
            return;
        }
        if (is_file($source)) {
            copy($source, $target);

            return;
        }
        $copy_files = self::search($source, $extInclude);
        foreach ($copy_files as $file) {
            $target_path = str_replace($source, $target, $file);
            $target_dir  = \dirname($target_path);
            if (!file_exists($target_dir)) {
                @mkdir($target_dir, 0755, true);
            }
            if ($force || !file_exists($target_path)) {
                copy($file, $target_path);
            }
        }
    }

    public static function write(string $filename, string $text, string $mode = 'w', int $blank = 0): void
    {
        if (!file_exists(\dirname($filename))) {
            @mkdir(\dirname($filename), 0755, true);
        }
        $fp = fopen($filename, $mode);
        if (flock($fp, LOCK_EX)) {
            while ($blank > 0) {
                fwrite($fp, PHP_EOL);
                $blank = $blank - 1;
            }
            fwrite($fp, $text . PHP_EOL);
            flock($fp, LOCK_UN);
        }
        fclose($fp);
    }

    public static function remove(string $path, bool $recurse = false): void
    {
        if (is_dir($path) && !$recurse) {
            return;
        }
        if (is_file($path)) {
            @unlink($path);

            return;
        }

        $handle = opendir($path);
        while (false !== ($fileName = readdir($handle))) {
            $subFile = Path::join($path, $fileName);
            $tmp     = str_replace('.', '', $fileName);
            if ('' != $tmp && is_dir($subFile)) {
                self::remove($subFile, $recurse);
            } elseif ('' != $tmp && !is_dir($subFile)) {
                @unlink($subFile);
            }
        }
        closedir($handle);
        @rmdir($path);
    }
}
