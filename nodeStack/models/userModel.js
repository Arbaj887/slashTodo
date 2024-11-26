const mongodb = require("mongoose");
const bcrypt = require('bcrypt');

const userSchema = new mongodb.Schema(
  {
    name: {
      type: String,
      required: true,
      
      trim:true
    },
    email: {
      type: String,
      required: true,
      unique: true,
      lowercase:true,
      trim:true
    },
    password: {
      type: String,
      required: true,
      trim:true
    },
    // uuid:{
    //   type: String,
    //   required: true,
    // }
  },

  { timestamps: true }
);

userSchema.pre('save',async function(next){
    if(!this.isModified("password")) return next();

    this.password = await bcrypt.hash(this.password, 10)
    next();

})
userSchema.methods.isPasswordCorrect = async function(password){
    return await bcrypt.compare(password, this.password)
}

const user = new mongodb.model('user', userSchema);
module.exports = user;
