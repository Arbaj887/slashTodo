const user = require('../models/userModel.js');
const bycrpt = require('bcrypt');
const cookieParser = require('cookie-parser');
const jwt = require('jsonwebtoken');


// --------------------------------------------------Login---------------------------------------------------------------------
const login=async(req,res)=>{
    try{
        const {email,password}=req.body;
        const result=await user.findOne({email});
        if(!result){
            return res.status(400).json({message:'Invalid email or password'});
            }
            
            const isMatch=await result.isPasswordCorrect(password);
            if(!isMatch){
                return res.status(400).json({message:'Invalid email or password'});
                }
                const token=  jwt.sign({
                    id:result._id,
                    name:result.name,
                    email:result.email,
                },
                    process.env.JWT_SECRET
                )
                res.cookie("token", token)
              return res.status(200).json({message:"Logging",token});  
                
}
catch(err){
    console.log(err);
    res.status(500).json({message:'Internal server error'});
    
}
}

//----------------------------------------------------------Register--------------------------------------------------------
const register=async(req,res)=>{
    try{
        const {name,email,password}=req.body;
        
        const userExist=await user.findOne({email});
        if(userExist){
            return res.status(400).json({message:'User Already Exist'});
            }
           const userCreate = await user.create({name,email,password});
                await userCreate.save();
                const token=  jwt.sign({
                    id:userCreate ._id,
                    name:userCreate .name,
                    email:userCreate.email,
                },
                    process.env.JWT_SECRET
                )
                res.cookie("token", token);
              return res.status(200).json({message:"user Created Successfully",token });  
                
}
catch(err){
    console.log(err);
    res.status(500).json({message:'Internal server error'});
    
}
}

//--------------------------------------------------Logout------------------------------------------------------
const logout = async(req,res)=>{
    try{
    res.clearCookie('token');
    return res.status(200).json({message:"user Logout successfully"})
    }catch(err){
        console.log(err);
        res.status(500).json({message:'Internal server error'});
    }
}

//-----------------------------------------------Dashboard-------------------------------------------------------------------------
const dashboard = async (req,res)=>{
    // console.log(req.userId);
    try{
        const allUser=await user.find({});
        
        return res.status(200).json(allUser);
    }catch(err){
        console.log(err)
       return  res.status(500).json({message:'Internal server error'});
    }
}

//-----------------------------------------------------------Edit-User-----------------------------------------------
const editUser=async(req,res)=>{
    const {id,name,email}=req.body;

    try {
        const findUser= await user.findOne({_id:id});
        
        findUser.name=name;
        findUser.email=email;
        // console.log(findUser)
        await findUser.save();

        const allUser = await user.find() ;
        return res.status(200).json({message:"user Updated successfully",allUser})
    } catch (err) {
        console.log(err)
        return res.status(500).json({message:'Internal server error'});
    }

}
//------------------------------------------------------------delete--User--------------------------------------------
const deleteUser=async(req,res)=>{
    const {id}=req.body;
          try {
         await user.findByIdAndDelete({_id:id});
         const allUser = await user.find() ;
         return res.status(200).json({message:"user deleted successfully",allUser})
          } catch (err)
           {
            console.log(err)
            return res.status(500).json({message:'Internal server error'});
          }
}

module.exports={
    login,
    register,
    logout,
    dashboard,
    editUser,deleteUser
}