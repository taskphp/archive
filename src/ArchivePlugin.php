<?php

namespace Task\Plugin;

use Task\Plugin\PluginInterface;
use Task\Plugin\FilesystemPlugin;
use Task\Plugin\Archive\Archive;

class ArchivePlugin implements PluginInterface
{
    public function create($type, $compression = null)
    {
        return new Archive($type, $compression);
    }
}
