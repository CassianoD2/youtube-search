const axios = require('axios');
const env = process.env.NODE_ENV || 'development';
const config = require(__dirname + '/../../config/config.json')[env];

const YOUTUBEAPIKEY = config['youtubeapi'] ? process.env.YOUTUBEAPIKEY : config['youtubeapi'];

var youtubeService = {
    search: (res, text) => {
        var params = new URLSearchParams({
            key: YOUTUBEAPIKEY,
            part: 'snippet',
            maxResults: 50,
            q: text,
            type: 'video',
        });

        var videoPush = [];

        var getInfoVideo = async (videoId) => {
            var params = new URLSearchParams({
                part: 'snippet, contentDetails',
                id: videoId,
                key: YOUTUBEAPIKEY
            });

            return await axios.get("https://www.googleapis.com/youtube/v3/videos?" + params.toString())
                .then((result) => {
                    return {
                        error: false,
                        data:result.data
                    };
                })
                .catch((error) => {
                    return {
                        error: true,
                        data: error
                    }
                })
        };

        var funcYoutube = async (text, params) => {
            var retorno = await axios.get("https://www.googleapis.com/youtube/v3/search?" + params.toString())
                .then(
                    (result) => {
                        try {
                            if (result.data.nextPageToken) {
                                params.append('pageToken', result.data.nextPageToken);
                            }

                            result.data.items.forEach( async element => {
                                element.videoInfo = await getInfoVideo(element.id.videoId);
                                videoPush.push(element);
                            });

                            if (videoPush.length < 50) {
                                funcYoutube(text, params);
                            } else {
                                res.json(videoPush);
                            }
                        } catch (err) {
                            console.log(err);
                        }
                    }
                ).catch(
                    (error) => {
                        console.log(error.toJSON());
                        res.status(400).json(error);
                    }
                );

        }

        funcYoutube(text, params);
    }
};


module.exports = youtubeService;