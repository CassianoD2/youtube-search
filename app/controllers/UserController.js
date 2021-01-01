var express = require('express');
var router = express.Router();

const { body, validationResult } = require('express-validator');
const { Users } = require('../models/index');
const Bcrypt = require('bcrypt');

const saltBcrypt = 10;

router.get('/',(req, res) => {
    Users.findAll()
        .then(
            (users) => {
                res.json(users);
            }
        );
});


const UsersPostValidation = [
    body('email').isEmail().withMessage("Preencha com um e-mail válido."),
    body('password').isLength({min: 6}).withMessage("Deve ter no mínimo 6 caracteres."),
    body('name').exists().withMessage("Preencha o nome.")
];

router.post('/', UsersPostValidation, async (req, res) => {
    var errors = validationResult(req);

    if(!errors.isEmpty()) {
        res.status(400).json({errors: errors.array()});
        return;
    } else {
        var userBody = req.body;
        userBody.password = Bcrypt.hashSync(req.body.password, saltBcrypt);
    }

    //Check if user exist
    let checkUser = await Users.findAll({
        where: {
            email: req.body.email
        }
    });

    if (checkUser.length > 0) {
        res.status(400).json({error: true, msg: "This e-mail is already used."});
        return;
    }

    try {
        const user = await Users.create(userBody);
        res.json({msg: "User created!"});
    } catch (error) {
        res.status(400).json({error: true, msg: "Something wrong occurred, please try again later", realMsg: error.errors[0].message});
    }
});

module.exports = router;