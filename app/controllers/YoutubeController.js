var express = require('express');
var router = express.Router(); 

const { body, validationResult } = require('express-validator');

router.get('/',(req, res) => {
    res.json({title: "youtube"});
});

module.exports = router;