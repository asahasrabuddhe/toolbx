<?php

return array(


    'pdf' => array(
        'enabled' => true,
        'binary' => '/usr/bin/xvfb-run /usr/bin/wkhtmltopdf',
        'timeout' => false,
        'options' => array(),
        'env'     => array(),
    ),
    'image' => array(
        'enabled' => true,
        'binary' => '"C:\Program Files\wkhtmltopdf\bin\wkhtmltoimage.exe"',
        'timeout' => false,
        'options' => array(),
        'env'     => array(),
    ),
);
