<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class YoutubeController extends Controller
{
    /** @var string URL API Youtube */
    const URL = "https://www.googleapis.com/youtube/v3/";

    public function index(Request $request)
    {
        $error = false;
        $errorMessage = '';

        $textToSearch = $request->get('text');
        if (empty($textToSearch)) {
            $error = true;
            $errorMessage = 'Por favor informe um texto para pesquisa.';
        }

        $videos = $this->getVideos($textToSearch);

        $informations = [];
        if (isset($videos['error']) && isset($videos['errorCode'])) {
            $error = true;
            $errorMessage = "Limite de consulta diário excedido.";
        } else {
            $informations = array_slice($this->processInformationForVideo($videos), 0, 5);
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
        $params = [
            "key" => env("YOUTUBEAPI"),
            "part" => 'snippet',
            "maxResults" => 2,
            "q" => $textToSearch,
            "type" => 'video',
        ];

        $itemVideo = [];
        $returnVideo = Http::get(self::URL . "search?" . http_build_query($params));

        if ($returnVideo->successful()) {
            foreach ($returnVideo->json()['items'] as $value) {
                $itemVideo[$value['id']['videoId']] = $this->getInfoVideos($value['id']['videoId']);
            }
        } else {
            try {
                $returnVideo->throw();
            } catch (\Exception $e) {
                $itemVideo['error'] = $e->getMessage();
                $itemVideo['errorCode'] = $e->getCode();
            }
        }

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

        return $returnVideo->json();
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
}
