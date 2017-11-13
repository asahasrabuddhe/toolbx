<?php

namespace App;

class Helper
{
    /**
     * Convert a multi-dimensional, associative array to CSV data
     * @param  array $data the array of data
     * @return string       CSV text
     */
    public static function str_putcsv($data) {
    		$data = (array)json_decode(json_encode($data), true);
            # Generate CSV data from array
            $fh = fopen('php://temp', 'rw'); # don't create a file, attempt
                                             # to use memory instead
            fputs($fh, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));
            fputcsv($fh, array_keys(current($data)));
            foreach($data as $row) {
                fputcsv($fh, array_values($row));
            }
            rewind($fh);
            $csv = stream_get_contents($fh);
            fclose($fh);

            return $csv;
    }
}