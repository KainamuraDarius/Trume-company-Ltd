<?php
// Copy this file to config.php (config.php is never committed to Git).
// The public site only READS settings here; the database itself is created by Trume Admin.
return [
    // Absolute path to the shared database, OUTSIDE public_html so nobody can download it.
    // cPanel: '/home4/rmsugnxf/trume-data/trume.sqlite'
    // Local:  '/home/kainamura/StudioProjects/trume-data/trume.sqlite'
    'database_path' => '/home4/rmsugnxf/trume-data/trume.sqlite',
    // Public address of this website, used for share previews and the sitemap.
    'site_url' => 'https://trumecompanyltd.truehome.com.ug',
];
