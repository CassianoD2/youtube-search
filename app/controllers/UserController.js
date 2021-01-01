var express = require('express');
var router = express.Router();

const { body, validationResult } = require('express-validator');
const { Users } = require('../models/index');
const Bcrypt = require('bcrypt');

const saltBcrypt = 10;

router.get('/',(req, res) => {
    //TODO: implement with a flag isAdmin to have access to this information.
    res.status(400).json({error: true, msg: "You don't have permission!"});

    // Users.findAll({
    //     attributes: ['name', 'email']
    // })
    //     .then(
    //         (users) => {
    //             res.json(users);
    //         }
    //     );
    //
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
    }

    try {
        const user = await Users.create(req.body);
        res.json({msg: "User created!"});
    } catch (error) {
        res.status(400).json({error: true, msg: error});
    }
});


const UserLoginValidation = [
    body('email').exists().notEmpty().withMessage("Campo e-mail obrigatório."),
    body('password').exists().notEmpty().withMessage("Campo senha obrigatório")
];

router.post('/token', UserLoginValidation, async (req, res) => {
    var errors = validationResult(req);

    if(!errors.isEmpty()) {
        res.status(400).json({errors: errors.array()});
        return;
    }

    const user = await Users.findOne({
        where: {
            email: req.body.email
        }
    }).then((userResult) => {
        console.log(userResult.checkPassword(req.body.password));
    });

    res.json();
});

module.exports = router;