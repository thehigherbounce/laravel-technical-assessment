<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use App\Models\Dataset;
use App\CsvParser;

class UsersController extends Controller
{
    /**
     * Get users based on provided filters
     *
     * @param Request $request Request containing filter parameters
     * @return \Illuminate\Http\JsonResponse Returns JSON response containing filtered result set
     */
    public function getUsers(Request $request)
    {
        $params = $request->only(['category', 'gender', 'age', 'birthday', 'age_range']);
        $query = Dataset::filter($params);

        $filtered = $query->count();
        $perPage = $request->input('length', 10);
        $start = $request->input('start', 0);
        $query->skip($start)->take($perPage);
        $users = $query->get()->toArray();
        $total =  Dataset::count();

        $response = [
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $users,
        ];

        return response()->json($response);
    }
    /**
     * Import a CSV file into the database
     *
     * @param Request $request Request object containing a CSV file
     * @return \Illuminate\Http\JsonResponse Returns JSON response of result
     */
    public function importCsv(Request $request)
    {
        $parser = new CsvParser($request->file('csv_file'));
        $chunkSize = 1000;
        $dataChunks = $parser->getDataChunks($chunkSize);
        try {
            foreach ($dataChunks as $chunk) {
                Dataset::insertOrIgnore($chunk);
            }
            return response()->json(['result' => 'success']);
        } catch (\Throwable $err) {
            return response()->json(['result' => 'error', 'data' => $err->getMessage()]);
        }
    }
    /**
     * Get users based on provided filters
     *
     * @param Request $request Request containing filter parameters
     * @return \Illuminate\Http\JsonResponse Returns JSON response containing filtered result
     */
    public function exportCSV(Request $request)
    {
        $params = $request->only(['category', 'gender', 'age', 'birthday', 'age_range']);
        $query = Dataset::filter($params);
        $data = $query->get();

        // Set the CSV file headers
        $headers = [
            'category',
            'firstname',
            'lastname',
            'email',
            'gender',
            'birthDate',
        ];

        // Initialize the StreamedResponse object
        $response = new StreamedResponse(function () use ($data, $headers) {
            $stream = fopen('php://output', 'w');
            // Write the CSV header to the output stream
            fputcsv($stream, $headers);

            // Write the CSV data row by row to the output stream
            foreach ($data as $row) {
                $csvRow = [
                    $row->category,
                    $row->firstname,
                    $row->lastname,
                    $row->email,
                    $row->gender,
                    $row->birthDate,
                ];

                fputcsv($stream, $csvRow);
            }

            fclose($stream);
        });

        $filename = 'export.csv';

        // Add a Content-Disposition header to force a download
        $disposition = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $filename
        );
        $response->headers->set('Content-Disposition', $disposition);

        // Set the response content type to CSV
        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');

        return $response;
    }
}
