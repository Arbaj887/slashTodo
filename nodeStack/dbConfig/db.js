const mongodb = require("mongoose");

const uri = process.env.MONGODB_URI;
const connectDB = async (req, res) => {
  try {
    await mongodb.connect(uri).then(()=>{
        console.log("Connected to MongoDB");
    })
   
    // return res.status(200).json({ msg: "MongoDB Connected..." });
  } catch (error) {
    console.error(error);
    res.status(500).json({ msg: "Error connecting to MongoDB" });
  }
};
module.exports = connectDB;
