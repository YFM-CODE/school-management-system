<?php

$interfaces = glob('d:/project_percontohan/school_management_system/app/Interfaces/*RepositoryInterface.php');
foreach ($interfaces as $file) {
    rename($file, str_replace('RepositoryInterface', 'Interface', $file));
}

$files = array_merge(
    glob('d:/project_percontohan/school_management_system/app/Interfaces/*.php'),
    glob('d:/project_percontohan/school_management_system/app/Repositories/*.php'),
    ['d:/project_percontohan/school_management_system/app/Providers/AppServiceProvider.php']
);

foreach ($files as $file) {
    file_put_contents($file, str_replace('RepositoryInterface', 'Interface', file_get_contents($file)));
}

echo "Done\n";
