<?php

namespace Task\Plugin;

use Task\Plugin\Archive\Archive;

class ArchivePluginTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $plugin = new ArchivePlugin(new FilesystemPlugin);
        $this->assertInstanceOf('Task\Plugin\Archive\Archive', $plugin->create(Archive::TAR));
    }
}
