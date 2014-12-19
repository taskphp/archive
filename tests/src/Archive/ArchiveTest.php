<?php

namespace Task\Plugin\Archive;

use Task\Plugin\FilesystemPlugin;
use Task\Plugin\Filesystem\File;

class ArchiveTest extends \PHPUnit_Framework_TestCase
{
    public function testTarball()
    {
        $fs = new FilesystemPlugin;
        $archive = new Archive(Archive::TAR, Archive::GZ);

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

    public function testZipball()
    {
        $fs = new FilesystemPlugin;
        $archive = new Archive(Archive::ZIP);

        $dir = sys_get_temp_dir().'/'.time().'archive';
        mkdir($dir);

        touch("$dir/foo");
        touch("$dir/bar");
        touch("$dir/baz");

        $tmp = tempnam(sys_get_temp_dir(), 'archive');
        $fs->ls($dir)
            ->pipe($archive)
            ->pipe($fs->open($tmp));

        $zip = new \ZipArchive;
        $zip->open($tmp);

        $files = [];
        foreach (range(0, $zip->numFiles-1) as $i) {
            $stat = $zip->statIndex($i);
            $files[] = $stat['name'];
        }

        $this->assertEquals(['bar', 'baz', 'foo'], $files);

        `rm -rf $dir $tmp`;
    }

    /**
     * @expectedException InvalidArgumentException
     * @dataProvider writeThrowsOnBadDataProvider
     */
    public function testWriteThrowsOnBadData($data)
    {
        $archive = new Archive(Archive::TAR, Archive::GZ);
        $archive->write($data);
    }
    public function writeThrowsOnBadDataProvider()
    {
        return [
            ['foo']
        ];
    }

    /**
     * @expectedException InvalidArgumentException
     * @dataProvider isSupportedThrowsProvider
     */
    public function testIsSupportedThrows($type, $compression = null)
    {
        new Archive($type, $compression);
    }
    public function isSupportedThrowsProvider()
    {
        return [
            ['foo'],
            [Archive::TAR, 'bar'],
            [Archive::ZIP, Archive::GZ]
        ];
    }

    public function tearDown()
    {
        `rm -f test`;
    }
}
