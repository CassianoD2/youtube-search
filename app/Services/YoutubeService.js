const axios = require('axios');

var youtubeService = {
    search: (res, text) => {
        var params = new URLSearchParams({
            key: process.env.YOUTUBEAPI,
            part: 'snippet',
            maxResults: 2,
            q: text,
            type: 'video',
        });

        var videoPush = [];
        var funcYoutube = async (text, params) => {
            var retorno = await axios.get("https://www.googleapis.com/youtube/v3/search?" + params.toString())
            .then(
                (result) => {
                    console.log(result.data);
                    try {
                        if (result.data.nextPageToken) {
                            params.append('pageToken', result.data.nextPageToken);
                        }
                        
                        result.data.items.forEach(element => {
                            videoPush.push(element);
                        });

                        console.log(videoPush.length);
                        
                        if (videoPush.length < 200) {
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
    },

    getInfoVideo: async (videoId) => {
        var params = new URLSearchParams({
            part: 'snippet, contentDetails',
            id: videoId,
            key: process.env.YOUTUBEAPI
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
    }
};


module.exports = youtubeService; 