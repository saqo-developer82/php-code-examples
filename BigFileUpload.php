<?php
namespace App\Http\Controllers;

use App\Jobs\CsvProcess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;

class BigFileUploadContorller extends Controller
{
    function index()
    {
        return view('upload-file');
    }

    function upload(Request $request)
    {
        if ($request->has('mycsv')) {
            $data = file($request->mycsv);
            //Chunking file
            $chunks = array_chunk($data, 1000);

            $header = [];
            $batch = Bus::batch([])->dispatch();

            foreach ($chunks as $key => $chunk) {
                $data = array_map('str_getcsv', $chunk);

                if ($key == 0) {
                    $header = array_map('strtolower', str_replace(" ","_", $data[0]));
                    unset($data[0]);
                }

                $batch->add(new SalesCsvProcess($data, $header));
            }

            return $batch;
        }

        return "Please upload a file";
    }

    public function batch(Request $request)
    {
        $batchId = $request->id;
        return Bus::findBatch($batchId);
    }
}