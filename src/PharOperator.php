<?php

declare(strict_types=1);

namespace axios\tools;

class PharOperator
{
    private \Phar $phar;

    private string $zip_dir;

    /**
     * @var array exclude files or folders
     */
    private array $exclude;

    /**
     * @var mixed|string
     *
     * @example '/\.(json|php|ini)$/'
     */
    private $include;

    private string $index = 'autoload.php';

    public function __construct($zip_dir = null, $include = '/\.*$/', $exclude = [])
    {
        if (!file_exists($zip_dir)) {
            throw new \InvalidArgumentException("{$zip_dir} is not exist.");
        }
        $this->zip_dir = Path::join($zip_dir);
        $this->exclude = $exclude;
        $this->include = $include;
    }

    /**
     * @param $index
     *
     * @return $this
     */
    public function setIndex(string $index): self
    {
        $this->index = $index;

        return $this;
    }

    public function addExclude(string $exclude): self
    {
        $this->exclude[] = Path::join($exclude);

        return $this;
    }

    public function zip(string $output_path): void
    {
        $save_path = $output_path;
        if (is_dir($save_path)) {
            throw new \InvalidArgumentException("{$output_path} must be file path.");
        }
        if (!file_exists(\dirname($save_path))) {
            @mkdir(\dirname($save_path), 0755, true);
        }

        if (file_exists($save_path)) {
            @unlink($save_path);
        }

        $this->phar = new \Phar($save_path);
        $this->phar->buildFromDirectory($this->zip_dir, $this->include);
        if (!empty($this->exclude)) {
            $this->remove($this->exclude);
        }
        $this->phar->setStub($this->phar->createDefaultStub($this->index));
        $this->phar->stopBuffering();
    }

    private function remove(array $exclude): void
    {
        foreach ($exclude as $file) {
            $path = Path::join($this->zip_dir, $file);
            if (is_dir($path)) {
                $files = Path::search($path, $this->include);
                foreach ($files as $filepath) {
                    $file = str_replace($this->zip_dir . \DIRECTORY_SEPARATOR, '', $filepath);
                    if ($this->phar->offsetExists($file)) {
                        $this->phar->delete($file);
                    }
                }
            } elseif ($this->phar->offsetExists($file)) {
                $this->phar->delete($file);
            }
        }
    }
}
