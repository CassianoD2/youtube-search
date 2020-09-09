<?php

namespace App\Http\Controllers;

use App\Model\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class YoutubeController extends Controller
{
    /** @var string URL API Youtube */
    const URL = "https://www.googleapis.com/youtube/v3/";

    public function index(Request $request)
    {
        $error = false;
        $errorMessage = '';

        $textToSearch = $request->get('text');
        $timeToView = $request->get('timeView');

        $videos = [];
        $informations = [];

        if (empty($textToSearch)|| empty($timeToView)) {
            $error = true;
            $errorMessage = 'Por favor informe um texto para pesquisa.';
        } else {

            $videos = $this->getVideos($textToSearch);

            $informations = [];

            if (isset($videos['error']) && isset($videos['errorCode'])) {
                $error = true;
                $errorMessage = "Limite de consulta diário excedido.
                \n Para utilizar nosso serviço registre uma chave em sua conta ao se registrar.";
            } else {
                $informations = array_slice($this->processInformationForVideo($videos), 0, 5);
            }
        }


        return view(
            'youtube/search',
            [
                'videos' => $videos,
                'statistics' => $informations,
                'error' => $error,
                'errorMessage' => $errorMessage
            ]
        );
    }

    /**
     * Retorna a lista de vídeos.
     *
     * @param string $textToSearch
     */
    private function getVideos($textToSearch)
    {
        $apiKey = env("YOUTUBEAPI");
        if ($user = Auth::user()) {
            $apiKey = $user->youtube_key;
        }

        $params = [
            "key" => $apiKey,
            "part" => 'snippet',
            "maxResults" => 50,
            "q" => $textToSearch,
            "type" => 'video',
        ];

        $itemVideo = [];

        $consultVideo = function (&$params, &$itemVideo, &$consultVideoReq) {
            $returnVideo = Http::get(self::URL . "search?" . http_build_query($params));

            try {
                if ($returnVideo->successful()) {
                    foreach ($returnVideo->json()['items'] as $value) {
                        $itemVideo[$value['id']['videoId']] = $this->getInfoVideos($value['id']['videoId']);
                    }

                    if (isset($returnVideo->json()['nextPageToken'])) {
                        $params['pageToken'] = $returnVideo->json()['nextPageToken'];
                    }

                    if (count($itemVideo) < 200) {
                        $consultVideoReq($params, $itemVideo, $consultVideoReq);
                    }
                } else {
                    try {
                        $returnVideo->throw();
                    } catch (\Exception $e) {

                        if (!empty($itemVideo)) {
                            $itemVideo = [];
                        }

                        $itemVideo['error'] = $e->getMessage();
                        $itemVideo['errorCode'] = $e->getCode();
                        return false;
                    }
                }
            } catch (\Exception $e) {
                $itemVideo['error'] = $e->getMessage();
                $itemVideo['errorCode'] = $e->getCode();
                return false;
            }
        };

        $consultVideo($params, $itemVideo, $consultVideo);

        return $itemVideo;
    }

    /**
     * Retorna as informações especificas de um vídeo.
     *
     * @param string $videoId
     *
     * @return array|mixed
     */
    private function getInfoVideos($videoId)
    {
        $params = [
            "key" => env("YOUTUBEAPI"),
            "part" => 'snippet,contentDetails',
            "id" => $videoId
        ];

        $returnVideo = Http::get(self::URL . "videos?" . http_build_query($params));

        if ($returnVideo->successful()) {
            return $returnVideo->json();
        }else{
            $returnVideo->throw();
        }
    }

    /**
     * Retorna as estatisticas dos vídeos pesquisados.
     *
     * @param array $videosInfos
     */
    private function processInformationForVideo($videosInfos)
    {
        $clean = function ($text) {
            $text = strtolower($text);
            $text = preg_replace('/[\#\@\.\-\,\/\:\*\+\(\)]/i'," ", $text);
            $text = preg_replace('/(de|do|da|e|se|seu|sua|nossa|sou|pra)/', "", $text);

            return $text;
        };

        $statistics = [];
        foreach ($videosInfos as $item) {

            $title = $clean($item['items'][0]['snippet']['title']);

            $description = $clean($item['items'][0]['snippet']['description']);

            $title = preg_split('/\s/', $title);
            $description = preg_split('/\s/', $description);

            $countWord = function (&$wordArray, $word) {
                if (!empty($word) && !is_numeric($word)) {
                    if (!isset($wordArray[$word])) {
                        $wordArray[$word] = 1;
                    } else {
                        $wordArray[$word] += 1;
                    }
                }
            };

            foreach ($title as $word) {
               $countWord($statistics, $word);
            }

            foreach ($description as $word) {
                $countWord($statistics, $word);
            }

            arsort($statistics);

            return $statistics;
        }
    }

    /**
     * Registra a API Key
     */
    public function registerApiKey()
    {
        if ($user = Auth::user()) {
            return view('youtube.regkey',[
                "apiKey" => $user->youtube_key ? $user->youtube_key : ''
            ]);
        } else {
            return redirect('/login');
        }
    }

    public function saveApiKey(Request $request)
    {
        if ($user = Auth::user()) {

            $userModel = User::findOrFail($user->id);
            $userModel->youtube_key = $request->post('api_key');
            if ($userModel->save()) {
                return redirect('/youtube/search')->with('saveKey', "Chave salva com sucesso!");
            } else {
                return redirect('/youtube/search')->with('errorSaveKey', "Não foi possível salvar a chave!");
            }
        } else {
            return redirect('/login');
        }
    }
}
