var express = require('express');
var router = express.Router();

const env = process.env.NODE_ENV || 'development';
const config = require(__dirname + '/../../config/config.json')[env];

const { body, validationResult } = require('express-validator');
const { Users } = require('../models/index');
const jwt = require('jsonwebtoken');

const UsersPostValidation = [
    body('email').isEmail().withMessage("Preencha com um e-mail válido."),
    body('password').isLength({min: 6}).withMessage("Deve ter no mínimo 6 caracteres."),
    body('name').exists().withMessage("Preencha o nome.")
];

const UserLoginValidation = [
    body('email').exists().notEmpty().withMessage("Campo e-mail obrigatório."),
    body('password').exists().notEmpty().withMessage("Campo senha obrigatório")
];

function verifyJWT(req, res, next){
    const token = req.headers['x-access-token'];
    if (!token) {
        return res.status(401).json({ auth: false, message: 'No token provided.' });
    }

    jwt.verify(token, process.env.SECRET, function(err, decoded) {
        if (err) {
            return res.status(500).json({ auth: false, message: 'Failed to authenticate token.' });
        }
        // se tudo estiver ok, salva no request para uso posterior
        req.userId = decoded.id;
        next();
    });
}

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
        if (userResult) {
            if (userResult.checkPassword(req.body.password)) {
                const tokenLogin = jwt.sign(
                    {
                        id: userResult.id,
                        email: userResult.email
                    },
                    config['jwtsessionkey'],
                    {
                        expiresIn: 3600
                    });
                return res.json({auth: true, token: tokenLogin});
            } else {
                return res.status(412).json({
                    'error': true,
                    'msg': "E-mail ou senha inválidos!"
                });
            }
        }

        return res.status(412).json({
            'error': true,
            'msg': "E-mail ou senha inválidos!"
        });
    });

    res.json();
});

module.exports = router;