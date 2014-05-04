task/archive
============

[![Build Status](https://travis-ci.org/taskphp/archive.svg?branch=master)](https://travis-ci.org/taskphp/archive)
[![Coverage Status](https://coveralls.io/repos/taskphp/archive/badge.png?branch=master)](https://coveralls.io/r/taskphp/archive?branch=master)

Example
=======

```php
use Task\Plugin\FilesystemPlugin;
use Task\Plugin\ArchivePlugin;
use Task\Plugin\Archive\Archive;

$project->inject(function ($container) {
    $container['fs'] = new FilesystemPlugin;
    $container['archive'] = new ArchivePlugin;
});

$project->addTask('archive', ['fs', 'archive', function ($fs, $archive) {
    $source = 'path/to/directory';
    $target = 'path/to/archive.tar.gz';

    $fs->ls($source)
        ->pipe($archive->create(Archive::TAR, Archive::GZ))
        ->pipe($fs->touch($target));
}]);
```

Installation
============

Add to your `composer.json`:
```json
...
"require-dev": {
    "task/archive": "~0.1"
}
...
```
