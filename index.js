const express = require('express')
const app = express()
const port = 3000

var morgan = require('morgan')
var path = require('path')
var rfs = require('rotating-file-stream')

var accessLogStream = rfs.createStream('access.log', {
    interval: '1d', // rotate daily
    path: path.join(__dirname, 'log')
  })
  
// setup the logger
app.use(morgan('combined', { stream: accessLogStream }))

app.get('/', (req, res) => {
  res.json({message: "hello"});
})

app.listen(port, () => {
  console.log(`Example app listening at http://localhost:${port}`)
})