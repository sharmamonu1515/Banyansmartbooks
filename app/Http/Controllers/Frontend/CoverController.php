<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Cover;
use App\Models\Topic;
use App\Models\Video;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class CoverController extends Controller
{

    public function home() {
        return '<style>* { padding: 0; margin: 0; }</style><img src="' . url('/images/coming-soon.jpeg') . '" style="width: 100vw; height: 100vh">';
    }

    public function index($cover_code) {

        $cover = Cover::where('code', $cover_code)->first();

        if ( ! $cover ) {
            abort(404);
        }

        if ( ! $cover->status || ! $cover->host->status ) {
            abort(401);
        }

        return view('cover.topics', compact('cover'));
    }

    public function get_file_by_topic($action, Topic $topic) {
        if ($action == 'video') {
            return response()->json([
                'data' => $topic->videos()->orderBy('sequence', 'ASC')->get()
            ]);
        } else if ($action == 'audio') {
            return response()->json([
                'data' => $topic->audios()->orderBy('sequence', 'ASC')->get(),
                'url' => $topic->cover->host->bucket . '/' . $topic->cover->host->name . '/' . $topic->cover->fld,
            ]);
        } else if ($action == 'test') {
            return response()->json([
                'data' => $topic->tests()->orderBy('sequence', 'ASC')->get(),
                'url' => $topic->cover->host->bucket . '/' . $topic->cover->host->name . '/' . $topic->cover->fld,
            ]);
        } else {
            return response()->json([
                'data' => $topic->worksheets()->orderBy('sequence', 'ASC')->get(),
                'url' => $topic->cover->host->bucket . '/' . $topic->cover->host->name . '/' . $topic->cover->fld,
            ]);
        }
    }

    public function get_video(Video $video) {

        try {
            preg_match('/ip(?:hone|[ao]d) os \K[\d_]+/i', $_SERVER['HTTP_USER_AGENT'], $matches);

            if ( ! empty($matches) ) {
                $versions = explode('_', $matches[0]);
                if ( (float) ($versions[0] . '.' . $versions[1]) < 11.2 ) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Minimum IOS 11.2 version is required for playback of our Videos',
                    ]);
                }
            }

            $client = new Client();

            $response = $client->request('POST', "https://dev.vdocipher.com/api/videos/{$video->ref_id}/otp", [
                'headers' => [
                    'Authorization' => 'Apisecret 0DtjB6OXfcndPhZGosz6RzalOM0vUWcrBm259vf1l4F0ZmojWZzp7c9YjLJktA64',
                    'Content-Type' => 'application/json',
                ]
            ]);

            $content = json_decode($response->getBody());

            if ( isset($content->otp) ) {
                return response()->json([
                    'success' => true,
                    'url' => "https://player.vdocipher.com/v2/?otp={$content->otp}&playbackInfo={$content->playbackInfo}"
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Some error occurred.'
            ]);

        } catch(Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Some error occurred.'
            ]);
        }
    }
}
