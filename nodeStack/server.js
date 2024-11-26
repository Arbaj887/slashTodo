const express= require('express');
const cors = require('cors');
const cookie=require('cookie-parser');
require('dotenv').config();

const connectDB= require('./dbConfig/db.js')
const userRouter = require('./router/userRoute.js')

const app = express();

app.use(express.json());
app.use(express.urlencoded({extended:true}));
app.use(cors());
app.use(cookie());
app.use('/',userRouter);

connectDB();

app.get('/',async(req,res)=>{
    res.send('Hello World');
})

app.listen(process.env.PORT,()=>{
    console.log(`server is running on port ${process.env.PORT}`);
});