<?php

namespace App\Http\Controllers;

use App\Models\Test;
use App\Models\Worksheet;
use Illuminate\Http\Request;

class PDFViewerController extends Controller
{
    public function index(Request $request, $id) {
        if ( ! $request->input('type') ) {
            abort(404);
        }

        $type = $request->input('type');

        if ( $type == 'worksheet' ) {
            $model = Worksheet::findOrFail($id);
            $default_url = route('user.pdf.viewer.read', ['worksheet', $model->id]);
        } else {
            $model = Test::findOrFail($id);
            $default_url = route('user.pdf.viewer.read', ['test', $model->id]);
        }


        return view('pdf.viewer', compact('default_url'));
    }

    public function read($type, $id) {

        if ( $type == 'worksheet' ) {
            $model = Worksheet::findOrFail($id);
        } else {
            $model = Test::findOrFail($id);
        }

        $mime = 'application/pdf';
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        header('Pragma: public');
        header('Cache-Control: private');
        header('Expires: '.gmdate("D, d M Y H:i:s", strtotime("+2 DAYS", time())). " GMT");
        header('Last-Modified: '. gmdate("D, d M Y H:i:s", time()). " GMT");
        // header('Content-Length: '.filesize($filename)); // Get the file size manually
        header('Content-type: '. $mime);

        set_time_limit(0);
        $this->readfile_chunked($model->url());
    }

    public function readfile_chunked($filename) {
        $chunksize = 1 * (1024 * 1024); // how many bytes per chunk
        $buffer = '';
        $handle = fopen($filename, 'rb');
        if ($handle === false) {
            return false;
        }
        sleep(1);
        while (!feof($handle)) {
            $buffer = fread($handle, $chunksize);
            //if (strlen($buffer) < $chunksize)
            //   $buffer = str_pad($buffer, $chunksize);
            print $buffer;
            // 2006-01-26: Added
            flush();
            @ob_flush();
        }
        return fclose($handle);
    }
}
