var express = require('express');
var router = express.Router();

const { body, query, validationResult } = require('express-validator');
const env = process.env.NODE_ENV || 'development';
const config = require(__dirname + '/../../config/config.json')[env];
const jwt = require('jsonwebtoken');
const jwtSecret = config['jwtsessionkey'] ? process.env.JWTSESSIONKEY : config['jwtsessionkey'];

const youtubeService = require('../Services/YoutubeService');

const searchRouteCheck = [
    query('text').exists().withMessage("Um texto para pesquisa é necessário."),
];

router.get('/search', searchRouteCheck, (req, res) => {
    var errors = validationResult(req);

    if(!errors.isEmpty()) {
        res.status(400).json({errors: errors.array()});
    }

    const tokenHeader = req.headers['x-access-token'];

    var userInfoToken = null;
    jwt.verify(tokenHeader, jwtSecret, function (err, decoded) {
        console.log(err);
        if (err) {
            return res.status(500).json({ auth: false, message: 'Failed to authenticate token.' });
        }

        // se tudo estiver ok, salva no request para uso posterior
        userInfoToken = {
            id: decoded.id,
            email: decoded.email,
            apiKey: decoded.apiKey
        };
    });

    return youtubeService.search(res, req.query.text);
});

module.exports = router;