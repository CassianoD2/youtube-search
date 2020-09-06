var express = require('express');
var router = express.Router(); 

const { Users } = require('../models/index');

router.get('/',(req, res) => {
    Users.findAll()
        .then(
            (users) => {
                res.json(users);
            }
        );
});

router.post('/', (req, res) => {


    res.json(req.body);
});

module.exports = router;