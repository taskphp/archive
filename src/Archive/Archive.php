<?php

namespace Task\Plugin\Archive;

use Task\Plugin\Stream\WritableInterface;
use Task\Plugin\FilesystemPlugin;
use Task\Plugin\Filesystem\FilesystemIterator;

class Archive implements WritableInterface
{
    const TAR = 'tar';
    const ZIP = 'zip';

    const GZ = \Phar::GZ;
    const BZ2 = \Phar::BZ2;

    protected $extensions = [
        self::GZ => 'gz',
        self::BZ2 => 'bz2'
    ];

    protected $fs;
    protected $type;
    protected $compression;
    protected $tmp;

    public function __construct($type, $compression = null, FilesystemPlugin $fs = null)
    {
        # Throws
        if ($this->isSupported($type, $compression)) {
            $this->type = $type;
            $this->compression = $compression;
        }

        $this->fs = $fs ?: new FilesystemPlugin;
    }

    public function write($data)
    {
        $tempnam = sys_get_temp_dir().'/'.'archive'.time().'.'.$this->type;

        if ($data instanceof FilesystemIterator) {
            $phar = new \PharData($tempnam);
            $this->tmp = $this->fs->open($tempnam);

            $phar->buildFromIterator($data, $data->getPath());

            if ($this->compression) {
                $phar = $phar->compress($this->compression);
                $this->fs->remove($tempnam);
                $this->tmp = $this->fs->open($tempnam.'.'.$this->extensions[$this->compression]);
            }

            return $this->tmp;
        }

        throw new \InvalidArgumentException("Unknown data type");
    }

    public function __destruct()
    {
        if ($this->tmp) {
            $this->fs->remove($this->tmp->getPathname());
        }
    }

    public static function isSupported($type, $compression = null)
    {
        if (!in_array($type, [static::TAR, static::ZIP])) {
            throw new \InvalidArgumentException("Unsupported type [$type]");
        }

        if (!is_null($compression) && !in_array($compression, [static::GZ, static::BZ2])) {
            throw new \InvalidArgumentException("Unsupported compression stream [$compression]");
        }

        if ($type === static::ZIP && !is_null($compression)) {
            throw new \InvalidArgumentException("Can't compress a ZIP archive");
        }

        return true;
    }
}
