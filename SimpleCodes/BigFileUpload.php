<?php
/**
 * This is a cod example for uloading big file , using Laravel Bus and Job
 */
namespace App\Http\Controllers;

use App\Jobs\CsvProcess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;

class BigFileUploadContorller extends Controller
{
    /**
     * A description of the entire PHP function.
     *
     * @return Some_Return_Value
     */
    function index()
    {
        return view('upload-file');
    }

    /**
     * Uploads a file and processes its contents.
     *
     * @param Request $request The request object containing the uploaded file.
     * @return mixed The result of the batch process.
     */
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

                $batch->add(new CsvProcess($data, $header));
            }

            return $batch;
        }

        return "Please upload a file";
    }

    /**
     * Retrieves a batch using the given request.
     *
     * @param Request $request The request object containing the batch ID.
     * @return mixed The retrieved batch.
     */
    public function batch(Request $request)
    {
        $batchId = $request->id;
        return Bus::findBatch($batchId);
    }
}
