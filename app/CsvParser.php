<?php

namespace App;

class CsvParser
{
    /**
     * Path to the CSV file
     *
     * @var array
     */
    private $data;

    /**
     * CsvParser constructor.
     *
     * @param file $filePath
     * @throws Exception
     */
    public function __construct($filePath)
    {
        try{            
            $csvData = file_get_contents($filePath->getRealPath());
        } catch (\Throwable $err) {
            throw new \Exception("Unable to read CSV file: " . $filePath);
        }
        
        $this->data = [];

        $rows = explode("\n", $csvData);
        // Remove any empty rows
        $rows = array_filter($rows);

        foreach($rows as $row) {
            $data = str_getcsv($row);

            if ($data[0] === 'category') {
                continue;
            }

            $this->data[] = [
                'category' => $data[0],
                'firstname' => $data[1],
                'lastname' => $data[2],
                'email' => $data[3],
                'gender' => $data[4],
                'birthdate' => $data[5],
            ];
        }
    }

    /**
     * Read and parse the CSV data
     *
     * @param int $chunkSize
     * @return Generator
     */
    public function getDataChunks($chunkSize = 1000)
    {
        return array_chunk($this->data, $chunkSize);
    }
}
