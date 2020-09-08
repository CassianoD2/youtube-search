var express = require('express');
var router = express.Router(); 

const { body, query, validationResult } = require('express-validator');

const youtubeService = require('../Services/YoutubeService');

const searchRouteCheck = [
    query('text').exists().withMessage("Um texto para pesquisa é necessário."),
];

router.get('/search', searchRouteCheck, (req, res) => {
    var errors = validationResult(req);
    if(!errors.isEmpty()) {
        res.status(400).json({errors: errors.array()});
    }


    youtubeService.search(res, req.query.text);
});

module.exports = router;