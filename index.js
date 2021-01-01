require('dotenv').config();
const express = require('express');
const app = express();
const port = 3000;

var morgan = require('morgan');
var path = require('path');
var rfs = require('rotating-file-stream');

// var UserController = require('./app/controllers/UserController');
// var YoutubeController = require('./app/controllers/YoutubeController');

var accessLogStream = rfs.createStream('access.log', {
    interval: '1d',
    path: path.join(__dirname, 'log')
});
  
app.use(express.urlencoded({ extended: false }));
app.use(express.json());

app.use(morgan('combined', { stream: accessLogStream }));

app.get('/', (req, res) => {
  res.json({message: "hello"});
});

// app.use('/users', UserController);
// app.use('/youtube', YoutubeController);

app.listen(port, () => {
  console.log(`Example app listening at http://localhost:${port}`);
});