<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit939b5f7b9d030eb2e260c7ceb66002f7
{
    public static $files = array (
        '253c157292f75eb38082b5acb06f3f01' => __DIR__ . '/..' . '/nikic/fast-route/src/functions.php',
    );

    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Slim\\HttpCache\\' => 15,
            'Slim\\' => 5,
        ),
        'P' => 
        array (
            'Psr\\Http\\Message\\' => 17,
        ),
        'I' => 
        array (
            'Interop\\Container\\' => 18,
        ),
        'F' => 
        array (
            'FastRoute\\' => 10,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Slim\\HttpCache\\' => 
        array (
            0 => __DIR__ . '/..' . '/slim/http-cache/src',
        ),
        'Slim\\' => 
        array (
            0 => __DIR__ . '/..' . '/slim/slim/Slim',
        ),
        'Psr\\Http\\Message\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/http-message/src',
        ),
        'Interop\\Container\\' => 
        array (
            0 => __DIR__ . '/..' . '/container-interop/container-interop/src/Interop/Container',
        ),
        'FastRoute\\' => 
        array (
            0 => __DIR__ . '/..' . '/nikic/fast-route/src',
        ),
    );

    public static $prefixesPsr0 = array (
        'P' => 
        array (
            'Pimple' => 
            array (
                0 => __DIR__ . '/..' . '/pimple/pimple/src',
            ),
        ),
    );

    public static $classMap = array (
        'ezSQL_codeigniter' => __DIR__ . '/..' . '/jv2222/ezsql/codeigniter/Ezsql_codeigniter.php',
        'ezSQL_cubrid' => __DIR__ . '/..' . '/jv2222/ezsql/cubrid/ez_sql_cubrid.php',
        'ezSQL_mssql' => __DIR__ . '/..' . '/jv2222/ezsql/mssql/ez_sql_mssql.php',
        'ezSQL_mysql' => __DIR__ . '/..' . '/jv2222/ezsql/mysql/ez_sql_mysql.php',
        'ezSQL_mysqli' => __DIR__ . '/..' . '/jv2222/ezsql/mysqli/ez_sql_mysqli.php',
        'ezSQL_oracle8_9' => __DIR__ . '/..' . '/jv2222/ezsql/oracle8_9/ez_sql_oracle8_9.php',
        'ezSQL_pdo' => __DIR__ . '/..' . '/jv2222/ezsql/pdo/ez_sql_pdo.php',
        'ezSQL_postgresql' => __DIR__ . '/..' . '/jv2222/ezsql/postgresql/ez_sql_postgresql.php',
        'ezSQL_sqlite' => __DIR__ . '/..' . '/jv2222/ezsql/sqlite/ez_sql_sqlite.php',
        'ezSQL_sqlsrv' => __DIR__ . '/..' . '/jv2222/ezsql/sqlsrv/ez_sql_sqlsrv.php',
        'ezSQL_sybase' => __DIR__ . '/..' . '/jv2222/ezsql/sybase/ez_sql_sybase.php',
        'ezSQLcore' => __DIR__ . '/..' . '/jv2222/ezsql/shared/ez_sql_core.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit939b5f7b9d030eb2e260c7ceb66002f7::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit939b5f7b9d030eb2e260c7ceb66002f7::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInit939b5f7b9d030eb2e260c7ceb66002f7::$prefixesPsr0;
            $loader->classMap = ComposerStaticInit939b5f7b9d030eb2e260c7ceb66002f7::$classMap;

        }, null, ClassLoader::class);
    }
}
