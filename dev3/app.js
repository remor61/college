const express=require('express');
const bodyParser=require('body-parser');
const mysql =require('mysql');
const { engine } =require('express-handlebars');
const app=express();

app.engine("handlebars", engine());
app.set('view engine','handlebars');
app.set('views','./views');

//Start server
app.listen(3000,function(req,res){
  console.log('Servidor está rodando!');
});

app.get("/",function(req,res){
  res.render('index');
});

app.use('/css',express.static('css'));
app.use('/js',express.static('js'));
app.use('/img',express.static('img'));