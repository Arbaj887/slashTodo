const router= require('express').Router();
const {login,register,dashboard,logout,editUser,deleteUser} = require('../controllers/userController.js');
const auth = require('../utility/auth.js');

router.route('/login').post(login);
router.route('/register').post(register);
router.route('/logout').post(auth,logout)
router.route('/dashboard').get(auth,dashboard);
router.route('/edit').post(auth,editUser);
router.route('/delete').post(auth,deleteUser);

module.exports=router