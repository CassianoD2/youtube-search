const axios = require('axios');

var youtubeService = {
    search: async (res, text) => {
        var params = new URLSearchParams({
            key: process.env.YOUTUBEAPI,
            part: 'snippet',
            maxResults: 200,
            q: text,
            type: 'video'
        });

        await axios.get("https://www.googleapis.com/youtube/v3/search?" + params.toString()).then(
            (result) => {
                console.log(result);
                res.json(result.data);
            }
        ).catch(
            (error) => {
                console.log(error);
                res.status(400).json(error.data);
            }
        );
    }
};


module.exports = youtubeService; 