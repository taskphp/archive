<?php

namespace Task\Plugin\Archive;

use Task\Plugin\FilesystemPlugin;

class ArchiveTest extends \PHPUnit_Framework_TestCase
{
    public function testStream()
    {
        $fs = new FilesystemPlugin;
        $archive = new Archive($fs, Archive::TAR, Archive::GZ);

        $dir = sys_get_temp_dir().'/'.time().'archive';
        mkdir($dir);

        touch("$dir/foo");
        touch("$dir/bar");
        touch("$dir/baz");

        $tmp = tempnam(sys_get_temp_dir(), 'archive');
        $fs->ls($dir)
            ->pipe($archive)
            ->pipe($fs->open($tmp));

        exec("tar -tf $tmp", $output);
        sort($output);
        $this->assertEquals(['bar', 'baz', 'foo'], $output);

        `rm -rf $dir $tmp`;
    }
}
