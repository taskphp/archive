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
     * @dataProvider makeIteratorProvider
     */
    public function testMakeIterator($data, $throws, $iterator = null)
    {
        $archive = new Archive(Archive::TAR);

        $ex = null;
        try {
            $archive->makeIterator($data);
        } catch (\InvalidArgumentException $__ex__) {
            $ex = $__ex__;
        }

        if ($throws) {
            $this->assertNotNull($ex);
        } else {
            $this->assertNull($ex);
        }
    }
    public function makeIteratorProvider()
    {
        $file = new \SplFileInfo('/tmp');

        return [
            ['foo', true],
            [['foo'], false, new \ArrayIterator(['foo'])],
            [$file, false, new \ArrayIterator([$file])]
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
