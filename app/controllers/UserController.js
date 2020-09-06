var express = require('express');
var router = express.Router(); 

const { body, validationResult } = require('express-validator');
const { Users } = require('../models/index');

router.get('/',(req, res) => {
    Users.findAll()
        .then(
            (users) => {
                res.json(users);
            }
        );
});


const UsersPostValidation = [
    body('email').isEmail().withMessage("Utilize um e-mail válido."),
    body('password').isLength({min: 6}).withMessage("Deve ter no mínimo 6 caracteres."),
    body('name').exists().withMessage("Preencha o nome.")
];

router.post('/', UsersPostValidation, (req, res) => {
    var errors = validationResult(req);
    if(!errors.isEmpty()) {
        res.status(400).json({errors: errors.array()});
    }

    res.json(req.body);
});

module.exports = router;