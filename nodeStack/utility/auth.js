const jwt =require('jsonwebtoken');

const auth = async (req,res,next)=>{
    // console.log(req.header)
    const token = req.cookies?.token || req.header("Authorization")?.replace("Bearer ", "");
    
    try{
    if(!token){
        console.log("Unauthorized Access")
        return res.status(401).json({message:"Unauthorized Access"});

    }
    
    jwt.verify(token,process.env.JWT_SECRET,(err,decoded)=>{
        if(err){
            console.log("error in Auth",err)
        }
        
        req.userId=decoded?.id
       
    });
 
    next();
    }catch(err){
        console.log("Auth error",err)
    }    

}

module.exports = auth;