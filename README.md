task/archive
============

Example
=======

```php
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
